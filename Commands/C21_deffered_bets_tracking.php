<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Track all deffered bets
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
class C21_deffered_bets_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из кэша все включенные комнаты
     *  2. Подготовить маячёк
     *  3. Пробежаться по всем $rooms, проверяя статус последнего раунда
     *  4. Сделать commit
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------//
    // Отслеживать судьбу всех перенесённых на следующий раунд ставок //
    //----------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из кэша все включенные комнаты
      $rooms = json_decode(Cache::get('processing:rooms'), true);
      if(empty($rooms)) $rooms = [];

      // 2. Подготовить маячёк
      // - Если он true, надо обновить кэш и отправить свежие данные в публ.канал
      $should_cache_update_and_translate = false;

      // 3. Пробежаться по всем $rooms, проверяя статус последнего раунда
      foreach($rooms as $room) {

        // 3.1. Получить статус последнего раунда

          // 1] Получить последний раунд комнаты $room
          $lastround = count($room['rounds']) ? $room['rounds'][0] : "";

          // 2] Если $lastround не найден, перейти к следующей итерации
          if(empty($lastround)) continue;

          // 3] Получить статус $lastround
          $lastround_status_id = $lastround['rounds_statuses'][count($lastround['rounds_statuses']) - 1]['pivot']['id_status'];

          // 4] Если $lastround_status_id > 3, перейти к следующей итерации
          if($lastround_status_id > 3) continue;

        // 3.2. Получить все accepted-ставки
        $bets_accepted = \M9\Models\MD3_bets::with(["m8_bots", "m8_items", "m5_users", "safecodes", "rooms", "rounds", "bets_statuses"])
          ->whereHas('bets_statuses', function($query){
            $query->where('status', 'Accepted');
          })
          ->get()->toArray();

        // 3.3. Отфильтровать из $bets_accepted неподходящие ставки
        // - Которые уже связаны с любым другим раундом
        // - Которые не связаны с комнатой $room
        if(!empty($bets_accepted) && is_array($bets_accepted))
          $bets_accepted_filtered = array_values(array_filter($bets_accepted, function($value, $key) USE ($room) {

            // 1) Если ставка связана с любым другим раундом, вернуть false
            if(count($value['rounds']) > 0) return false;

            // 2) Если ставка не связана с комнатой $room, вернуть false
            if(count($value['rooms']) == 0 || $value['rooms'][0]['id'] != $room['id']) return false;

            // n) Иначе, вернуть true
            return true;

          }, ARRAY_FILTER_USE_BOTH));
        else
          $bets_accepted_filtered = [];

        // 3.4. Вычислить диапазоны билетов для каждой ставки в $bets_accepted_filtered
        for($i=0; $i<count($bets_accepted_filtered); $i++) {

          // 1] Вычислить сумму $i-й ставки в центах
          $bet_sum_cents = call_user_func(function() USE ($bets_accepted_filtered, $i) {

            $result = 0;
            foreach($bets_accepted_filtered[$i]['m8_items'] as $item) {
              $result = +$result + +$item['price'];
            }
            return round($result*100);

          });

          // 2] Если $i = 0, то:
          // - tickets_from == 0
          // - tickets_to == +$bet_sum_cents - 1
          if($i == 0) {
            $bets_accepted_filtered[$i]['tickets_from'] = 0;
            $bets_accepted_filtered[$i]['tickets_to'] = (int)(+$bet_sum_cents - 1);
          }

          // 3] Если $i > 0, то
          if($i > 0) {
            $bets_accepted_filtered[$i]['tickets_from'] = (int)(+$bets_accepted_filtered[+$i-1]['tickets_to'] + 1);
            $bets_accepted_filtered[$i]['tickets_to'] = (int)(+$bets_accepted_filtered[$i]['tickets_from'] + +$bet_sum_cents - 1);
          }

        }

        // 3.5. Если это возможно, добавить в раунд отложенную ставку одного из пользователей
        if(count($bets_accepted_filtered) > 0) {
          foreach($bets_accepted_filtered as $bet2attach) {

            // 1] Проверить, может ли этот пользователь добавить ставку в $lastround
            $canwe_makeabet = call_user_func(function() USE ($room, $bet2attach) {

              $result = runcommand('\M9\Commands\C22_canwe_makeabet_intheroom_now', [
                "id_room" => $room['id'],
                "id_user" => $bet2attach['m5_users'][0]['id']
              ]);
              if($result['status'] != 0)
                throw new \Exception($result['data']['errormsg']);
              return $result['data'];

            });

            // 2] Если может
            if($canwe_makeabet['verdict'] == true) {

              // 2.1] Сделать метку
              $should_cache_update_and_translate = true;

              // 2.2] Получить ставку $bet2attach из БД
              $bet = \M9\Models\MD3_bets::find($bet2attach['id']);
              if(empty($bet))
                throw new \Error('Не удалось найти ставку с ID = '.$bet2attach['id'].' в БД.');

              // 2.3] Связать $bet с $lastround
              if(!$bet->rounds->contains($lastround['id']))
                $bet->rounds()->attach($lastround['id']);

              // 2.4] Отвязать $bet от $room
              if($bet->rooms->contains($room['id']))
                $bet->rooms()->detach($room['id']);

              // 2.5] Добавить tickets_from / tickets_to в md2000
              $bet->m5_users()->updateExistingPivot($bet2attach['m5_users'][0]['id'], ["tickets_from" => $bet2attach["tickets_from"], "tickets_to" => $bet2attach["tickets_to"]]);

              // 2.6] Завершить цикл (т.к. мы работаем только с 1-й ставкой)
              break;

            }

          }
        }

      }

      // 4. Сделать commit
      DB::commit();

      // 5.  Если маячёк true, обновить кэш и транслировать свежие данные
      if($should_cache_update_and_translate == true ) {

        // 5.1. Обновить весь кэш
        $result = runcommand('\M9\Commands\C13_update_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 5.2. Получить свежие игровые данные
        $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1, 'safe' => true]);
        if($allgamedata['status'] != 0)
          throw new \Exception($allgamedata['data']['errormsg']);

        // 5.3. Сообщить всем игрокам через публичный канал websockets свежие игровые данные
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:public'],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'fresh_game_data',
            'data' => [
              'rooms' => $allgamedata['data']['rooms']
            ]
          ]
        ]));

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C21_deffered_bets_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C21_deffered_bets_tracking']);
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

