<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Tracking of assetid of wins
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *
 *      ]
 *    ]
 *
 *  Формат возвращаемого значения
 *  -----------------------------
 *
 *    [
 *      status          // 0 - всё ОК, -1 - нет доступа, -2 - ошибка
 *      data            // результат выполнения команды
 *    ]
 *
 *  Значение data в зависимости от статуса
 *  --------------------------------------
 *
 *    status == 0
 *    -----------
 *      - ""
 *
 *    status == -1
 *    ------------
 *      - Не контролируется в командах. Отслеживается в хелпере runcommand.
 *
 *    status = -2
 *    -----------
 *      - Текст ошибки. Может заменяться на "" в контроллерах (чтобы скрыть от клиента).
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M9\Commands;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Базовые классы, необходимые для работы команд вообще
  use App\Jobs\Job,
      Illuminate\Queue\SerializesModels,
      Illuminate\Queue\InteractsWithQueue,
      Illuminate\Contracts\Queue\ShouldQueue;   // добавление в очередь задач

  // Классы, поставляемые Laravel
  use Illuminate\Routing\Controller as BaseController,
      Illuminate\Support\Facades\App,
      Illuminate\Support\Facades\Artisan,
      Illuminate\Support\Facades\Auth,
      Illuminate\Support\Facades\Blade,
      Illuminate\Support\Facades\Bus,
      Illuminate\Support\Facades\Cache,
      Illuminate\Support\Facades\Config,
      Illuminate\Support\Facades\Cookie,
      Illuminate\Support\Facades\Crypt,
      Illuminate\Support\Facades\DB,
      Illuminate\Database\Eloquent\Model,
      Illuminate\Support\Facades\Event,
      Illuminate\Support\Facades\File,
      Illuminate\Support\Facades\Hash,
      Illuminate\Support\Facades\Input,
      Illuminate\Foundation\Inspiring,
      Illuminate\Support\Facades\Lang,
      Illuminate\Support\Facades\Log,
      Illuminate\Support\Facades\Mail,
      Illuminate\Support\Facades\Password,
      Illuminate\Support\Facades\Queue,
      Illuminate\Support\Facades\Redirect,
      Illuminate\Support\Facades\Redis,
      Illuminate\Support\Facades\Request,
      Illuminate\Support\Facades\Response,
      Illuminate\Support\Facades\Route,
      Illuminate\Support\Facades\Schema,
      Illuminate\Support\Facades\Session,
      Illuminate\Support\Facades\Storage,
      Illuminate\Support\Facades\URL,
      Illuminate\Support\Facades\Validator,
      Illuminate\Support\Facades\View;

  // Доп.классы, которые использует эта команда


//---------//
// Команда //
//---------//
class C47_assetid_wins_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Принять данные
    public $data;

  //------------------------------------------------------//
  // В. Принять аргументы, переданные при запуске команды //
  //------------------------------------------------------//
  public function __construct($data)  // TODO: указать аргументы
  {

    $this->data = $data;

  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Получить из кэша текущее состояние игры
     *  2. Обработать выигрыши для каждой комнаты отдельно
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------//
    // Tracking of assetid of wins //
    //-----------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить из кэша текущее состояние игры
      $rooms = json_decode(Cache::get('processing:rooms'), true);
      if(empty($rooms))
        throw new \Exception('Не найдены комнаты в кэше.');

      // 2. Обработать выигрыши для каждой комнаты отдельно
      foreach($rooms as $room) {

        // 2.1. Получить время выплаты выигрыша в этой комнате, в минутах
        $payout_limit_min = !empty($room['payout_limit_min']) ? $room['payout_limit_min'] : 60;

        // 2.2. Получить дату и время в прошлом, на $payout_limit_min минут раньше текущего
        $max_age = \Carbon\Carbon::now()->subMinutes($payout_limit_min);

        // 2.3. Получить все выигрыши комнаты $room, не старше $payout_limit_min
        // - И статус раундов которых pending или выше.
        $wins = \M9\Models\MD4_wins::whereHas('rounds', function($query) USE ($room) {
          $query->whereHas('rooms', function($query) USE ($room) {
            $query->where('id', $room['id']);
          })->whereHas('rounds_statuses', function($query){
            $query->where('id', '>=', 4);
          });
        })->where('created_at', '>', $max_age->toDateTimeString())
          ->get();

        // 2.4. По очереди обработать каждый выигрыш
        foreach($wins as $win) {

          // 1] Получить связанный с $win раунд
          $round = \M9\Models\MD2_rounds::whereHas('wins', function($query) USE ($win) {
            $query->where('id', $win['id']);
          })->first();

          // 2] Составить список всех поставленных вещей, отсортированный по цене
          // - Отсортированный по цене по убыванию.
          // - Плюс, каждой вещи добавить св-во percentage (цена вещи, делёная на банк).
          $items = call_user_func(function() USE ($round, $win) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Наполнить $results
            // - И добавить каждой вещи св-во percentage
            foreach($round['bets'] as $bet) {
              foreach($bet['m8_items'] as $item) {
                $item['percentage'] = (($item['price']*100)/$win['jackpot_total_sum_cents'])*100;
                array_push($results, $item->toArray());
              }
            }

            // 3] Отсортировать все вещи по цене, по возрастанию
            usort($results, function($a, $b){
              if((int)($a['price']*100) > (int)($b['price']*100)) return 1;
              if((int)($a['price']*100) < (int)($b['price']*100)) return -1;
              return 0;
            });

            // n] Вернуть результаты
            return $results;

          });

          // 3] Выяснить, есть ли связанные с $win вещи с пустым assetid
          $is_empty_assetid_in_win = call_user_func(function() USE ($win) {

            $result = false;
            foreach($win['m8_items'] as $item) {
              if(empty($item['pivot']['assetid'])) {
                $result = true;
                break;
              }
            }
            return $result;

          });

          // 4] Перейти к след.итерации, если выполнены все условия:
          // - Нет связанных с $win вещей с пустыми assetid.
          // - Вообще в принципе есть вещи, связанные с $win
          // - [отмена] Количество связанных с $win вещей совпадает с кол-вом $items
          if($is_empty_assetid_in_win === false && count($win['m8_items']) != 0) // && count($items) == count($win['m8_items'])) continue;

          // 5] Выяснить, есть ли среди $items вещи с пустыми assetid_bots
          $is_empty_assetid_bots_in_bet = false;
          $is_empty_assetid_bots_in_bet = false;
          foreach($items as $item) {
            if(empty($item['pivot']['assetid_bots'])) {
              $is_empty_assetid_bots_in_bet = true;
              break;
            }
          }

          // 6] Если $is_empty_assetid_bots_in_bet == true, перейти к следующей итерации
          if($is_empty_assetid_bots_in_bet === true) continue;

          // 7] Определить вещи на комиссию
          $items2take = call_user_func(function() USE ($win, $items) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Наполнить $results
            $cents_already = 0;
            for($i=0; $i<count($items); $i++) {
              $cents_already = +$cents_already + +$items[$i]['price']*100;
              if($cents_already <= $win['howmuch_finally_cents'])
                array_push($results, $items[$i]);
              else
                $cents_already = +$cents_already - +$items[$i]['price']*100;
            }

            // n] Вернуть результаты
            return [
              "fee_cents_taken_fact" => $cents_already,
              "items2take" =>           $results
            ];

          });

          // 8] Определить вещи на отдачу
          $items2give = call_user_func(function() USE ($items, $items2take) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Получить массив assetid всех вещей из $items2take
            $items2take_ids = collect($items2take['items2take'])->pluck('pivot.assetid_bots')->toArray();

            // 3] Добавить в $results все вещи из $items, кроме $items2take
            foreach($items as $item) {
              if(!in_array($item['pivot']['assetid_bots'], $items2take_ids))
                array_push($results, $item);
            }

            // n] Вернуть результаты
            return $results;

          });

          // 9] Связать новый выигрыш с вещами $items2give
          DB::beginTransaction();
          foreach($items2give as $item) {
            if(!$win->m8_items->contains($item['id'])) {
              $win->m8_items()->attach($item['id'], ["assetid" => $item['pivot']['assetid_bots'], "price" => $item['price']]);
            }
          }
          DB::commit();

          // 10] Обновить весь кэш выигрышей
          $result = runcommand('\M9\Commands\C25_update_wins_cache', [
            "all" => true
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 11] Обновить весь кэш истории
          $result = runcommand('\M9\Commands\C51_update_history_cache', [
            "all"   => true,
            "force" => true
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 12]
          // - Получить свежую единицу истории выигрыша $win через C52
          // - Транслировать её через публичный канал

        }

      }


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C47_assetid_wins_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C47_assetid_wins_tracking']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $errortext,
            "errormsg" => $e->getMessage()
          ]
        ];
    }}); if(!empty($res)) return $res;


    //---------------------//
    // N. Вернуть статус 0 //
    //---------------------//
    return [
      "status"  => 0,
      "data"    => ""
    ];

  }

  //--------------//
  // Д. Заготовки //
  //--------------//
  /**
   *
   * Д1. Провести валидацию
   * Д2. Начать транзакцию
   * Д3. Подтвердить транзакцию
   * Д4. Сохранить данные в БД
   * Д5. Создать элемент
   * Д6. Удалить элемент
   *
   *
   */


    // Д1. Провести валидацию //
    //------------------------//

      //// x. Провести валидацию
      //
      //  // Создать объект-валидатор
      //  $validator = Validator::make($this->data, [
      //    'prop1'               => 'sometimes|rule1',
      //    'prop2'               => 'sometimes|rule2',
      //    'prop3'               => 'sometimes|rule3',
      //  ]);
      //
      //  // Провести валидацию, и если она провалилась...
      //  if ($validator->fails()) {
      //
      //    // Вернуть статус -2 и ошибку
      //    return [
      //      "status"  => -2,
      //      "data"    => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
      //    ];
      //
      //  }


    // Д2. Начать транзакцию //
    //-----------------------//

      //// x. Начать транзакцию
      //DB::beginTransaction();


    // Д3. Подтвердить транзакцию //
    //----------------------------//

      //// x. Подтвердить транзакцию
      //DB::commit();


    // Д4. Сохранить данные в БД //
    //---------------------------//

      //// x. Сохранить данные в БД
      //try {
      //
      //  // 4.1. Получить eloquent-объект
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 4.2. Сохранить в v присланные данные
      //  foreach($this->data as $key => $value) {
      //
      //    // Если $key == 'id', перейти к следующей итерации
      //    if($key == 'id') continue;
      //
      //    // Если в таблице есть столбец $key
      //    if(lib_hasColumn('m1', 'MD1_somemodel', $key)) {
      //
      //      // Изменить значение столбца $key на $value
      //      $item[$key] = $value;
      //
      //      // Сохранить изменения
      //      $item->save();
      //
      //    }
      //
      //  }
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д5. Создать элемент //
    //---------------------//

      //// x. Создать новый элемент
      //try {
      //
      //  // 1] Попробовать найти удалённый элемент
      //  $item = \M7\Models\MD1_somemodel::onlyTrashed()->where('name','=',$this->data['name'])->first();
      //
      //  // 2] Если удалённый элемент с таким именем не найдн
      //  if(empty($item)) {
      //
      //    // 2.1] Создать новый элемент
      //    $item = new \M1\Models\MD1_somemodel();
      //
      //    // 2.2] Наполнить $new данными
      //    $item->name                        = $this->data['name'];
      //    $item->description                 = $this->data['description'];
      //
      //    // 2.3] Сохранить $new в БД
      //    $item->save();
      //
      //  }
      //
      //  // 3] Если удалённый элемент найден
      //  if(!empty($item)) {
      //
      //    // 3.1] Восстановить его
      //    $item->restore();
      //
      //    // 3.2] Обновить некоторые свойства права
      //    $item->description                 = $data['description'];
      //
      //    // 3.3] Сохранить изменения
      //    $item->save();
      //
      //  }
      //
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д6. Удалить элемент //
    //---------------------//

      //// x. Удалить элемент
      //try {
      //
      //  // 1] Получить eloquent-модель элемента, который требуется удалить
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 2] Удалить элемент
      //  $item->delete();
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}




}

?>

