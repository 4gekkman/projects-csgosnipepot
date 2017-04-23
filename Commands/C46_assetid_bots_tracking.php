<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Tracking of asset_bots of bets
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
class C46_assetid_bots_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить все ставки, assetid_bots для которых ещё есть смысл определять
     *  2. Обработать ставки для каждой комнаты отдельно
     *    2.1. Получить время выплаты выигрыша в этой комнате, в минутах
     *    2.2. Получить дату и время в прошлом, на $payout_limit_min минут раньше текущего
     *    2.3. Получить все ставки комнаты $room, не старше $payout_limit_min
     *    2.4. По очереди обработать каждую ставку
     *  3. Обработать выигрыши для каждой комнаты отдельно
     *    3.1. Получить время выплаты выигрыша в этой комнате, в минутах
     *    3.2. Получить дату и время в прошлом, на $payout_limit_min минут раньше текущего
     *    3.3. Получить все выигрыши комнаты $room, не старше $payout_limit_min
     *    3.4. По очереди обработать каждый выигрыш
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------//
    // Tracking of asset_bots of bets //
    //--------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить из кэша текущее состояние игры
      $rooms = json_decode(Cache::get('processing:rooms'), true);
      if(empty($rooms))
        throw new \Exception('Не найдены комнаты в кэше.');

      // 2. Обработать ставки для каждой комнаты отдельно
      foreach($rooms as $room) {

        // 2.1. Получить время выплаты выигрыша в этой комнате, в минутах
        $payout_limit_min = !empty($room['payout_limit_min']) ? $room['payout_limit_min'] : 60;

        // 2.2. Получить дату и время в прошлом, на $payout_limit_min минут раньше текущего
        $max_age = \Carbon\Carbon::now()->subMinutes($payout_limit_min);

        // 2.3. Получить все ставки комнаты $room, не старше $payout_limit_min
        // - И статус раундов которых pending или выше.
        $bets = \M9\Models\MD3_bets::whereHas('rounds', function($query) USE ($room) {
          $query->whereHas('rooms', function($query) USE ($room) {
            $query->where('id', $room['id']);
          })->whereHas('rounds_statuses', function($query){
            $query->where('id', '>=', 4);
          });
        })->where('created_at', '>', $max_age->toDateTimeString())
          ->get();

        // 2.4. По очереди обработать каждую ставку
        foreach($bets as $bet) {

          // 1] Выяснить, есть ли связанные с $bet вещи с пустым assetid_bots
          $is_empty_assetid_bots_in_bet = call_user_func(function() USE ($bet) {

            $result = false;
            foreach($bet['m8_items'] as $item) {
              if(empty($item['pivot']['assetid_bots'])) {
                $result = true;
                break;
              }
            }
            return $result;

          });

          // 2] Если $is_empty_assetid_bots_in_bet == false, перейти к следующей итерации
          if($is_empty_assetid_bots_in_bet === false) continue;

          // 3] Начать транзакцию
          DB::beginTransaction();

          // 4] Получить все ставки комнаты $room, не старше $payout_limit_min
          $bets_fresh = \M9\Models\MD3_bets::whereHas('rounds', function($query) USE ($room) {
            $query->whereHas('rooms', function($query) USE ($room) {
              $query->where('id', $room['id']);
            });
          })->where('created_at', '>', $max_age->toDateTimeString())
            ->get();

          // 5] Получить массив assetid уже занятых в других ставках из $bets
          // - Ставка $bet связана с конкретным раундом $round.
          // - С этим или предыдущими раундами могут быть связаны от 0 и более других ставок.
          // - Каждая из этих ставок связана с 1-й или более вещью.
          // - В связи с каждой вещью определен и assetid этой вещи у бота.
          // - Необходимо получить массив этих самых занятых assetid других ставок.
          $busy_assetids = call_user_func(function() USE ($bets_fresh) {

            // 1] Получить все связанные с $round ставки
            $bets = $bets_fresh;

            // 2] Собрать в массив (без повторений) все assetid всех вещей ставок $bets
            $busy_assetids = [];
            foreach($bets as $bet) {
              foreach($bet['m8_items'] as $item) {
                if(!in_array($item['pivot']['assetid_bots'], $busy_assetids) && !empty($item['pivot']['assetid_bots']))
                  array_push($busy_assetids, $item['pivot']['assetid_bots']);
              }
            }

            // n] Вернуть результат
            return $busy_assetids;

          });

          // 6] Записать assetid_bots в md2001
          // - Это assetid принятых ботом в виде ставки скинов.
          // - Единственный технический способ это сделать таков:
          //   1) Сначала составить массив с данными для всех связей.
          //   2) Сделать detach всех связей между $bet и m8_items.
          //   3) По массиву, сделать заново attach связей.
          // - Действовать через pivot->assetid_bots, или через updateExistingPivot
          //   не получается, т.к. система находит все связи с одинаковыми id_item
          //   и id_bet, и записывает значение во все связи, а не в одну.
          call_user_func(function() USE (&$bet, $busy_assetids) {

            // 1] Получить список всех связанных с $bet скинов
            $bet_items = $bet->m8_items;

            // 2] Получить Steam ID бота, связанного с $bet
            $bet_bot_steamid = $bet->m8_bots[0]['steamid'];

            // 3] Получить инвентарь бота, связанного с $bet
            $bet_bot_inventory = runcommand('\M8\Commands\C4_getinventory', [
              "steamid" => $bet_bot_steamid,
              "force"   => true
            ]);
            if($bet_bot_inventory['status'] != 0)
              throw new \Exception($bet_bot_inventory['data']['errormsg']);

            // 4] Для каждого скина в $bet_items заполнить поле assetid_bots
            call_user_func(function() USE (&$bet, &$bet_items, $bet_bot_inventory, $busy_assetids){

              // 4.1] Составить итоговый массив всех связей с m8_items для $bet
              // - В формате:
              //
              //  [
              //    id_bet
              //    id_item
              //    item_price_at_bet_time
              //    assetid_users
              //    assetid_bots
              //  ]
              //
              $rels = call_user_func(function() USE (&$bet, &$bet_items, &$bet_bot_inventory, $busy_assetids) {

                // 4.1.1] Подготовить массив для результатов
                $results = [];

                // 4.1.2] Подготовить массив для assetid_users и assetid_bots
                // - Уже найденных в $bet_bot_inventory скинов.
                $assetids_found = [];
                $assetid_users_arr = [];

                // 4.1.3] Наполнить $results
                foreach($bet_items as &$item) {

                  // 1) Если assetid_users item'а уже в $assetid_users_arr
                  // - Перейти к следующей итерации.
                  if(in_array($item->pivot->assetid_users, $assetid_users_arr))
                    continue;

                  // 2) Добавить assetid_users item'а в $assetid_users_arr
                  array_push($assetid_users_arr, $item->pivot->assetid_users);

                  // 3) Найти в $bet_bot_inventory соотв.вещь, и добавить значение в $results
                  call_user_func(function() USE (&$results, &$bet_items, &$bet, &$item, &$assetids_found, &$assetid_users_arr, &$bet_bot_inventory, $busy_assetids) {
                    foreach($bet_bot_inventory['data']['rgDescriptions'] as $item_in_inventory) {
                      if($item_in_inventory['market_hash_name'] == $item['name']) {

                        if(!in_array($item_in_inventory["assetid"], $assetids_found) && !in_array($item_in_inventory["assetid"], $busy_assetids)) {

                          // 3.1) Добавить assetid в $assetids_found
                          array_push($assetids_found, $item_in_inventory["assetid"]);

                          // 3.2) Добавить значение в $results
                          array_push($results, [
                            "id_bet"                  => $bet->id,
                            "id_item"                 => $item['id'],
                            "item_price_at_bet_time"  => $item['pivot']['item_price_at_bet_time'],
                            "assetid_users"           => $item['pivot']['assetid_users'],
                            "assetid_bots"            => $item_in_inventory["assetid"]
                          ]);

                          // 3.3) Завершить цикл
                          break;

                        }

                      }
                    }
                  });

                }

                // 4.1.4] Если размер $bet_items не равен размеру $results, вернуть пустую строку
                if(count($bet_items) != count($results))
                  return [];

                // 4.1.n] Вернуть результаты
                return $results;

              });

              // 4.2] Если $rels пуст, завершить с ошибкой
              if(empty($rels) || count($rels) == 0)
                return;
                //throw new \Exception('Не удалось получить связи между ставкой и её вещами, которые нужно пересоздавать для добавления assetid_bots. По всей видимости, инвентарь, получаемый через Steam API ещё не обновился, и принятые вещи ещё там не появились.');

              // 4.3] Сделать detach для всех связей между $bet и m8_items
              $bet->m8_items()->detach();

              // 4.4] Сделать attach всех связей $rels между $bet и m8_items
              call_user_func(function() USE (&$rels, &$bet, &$bet_items) {
                foreach($rels as $rel) {
                  $bet->m8_items()->attach($rel['id_item'], [
                    "item_price_at_bet_time" => $rel['item_price_at_bet_time'],
                    "assetid_users"          => $rel['assetid_users'],
                    "assetid_bots"           => $rel['assetid_bots']
                  ]);
                }
              });

            });

          });

          // n] Подтвердить транзакцию
          DB::commit();

          // m] Обновить весь кэш
          $result = runcommand('\M9\Commands\C25_update_wins_cache', [
            "all" => true
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

        }

      }


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C46_assetid_bots_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C46_assetid_bots_tracking']);
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

