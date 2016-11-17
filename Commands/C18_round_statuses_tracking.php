<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Provides tracking and updating the status of all rounds
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
class C18_round_statuses_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить все игровые данные из кэша
     *  2. Подготовить маячёк (изменился ли статус любого раунда)
     *  3. Пробежаться по $rooms, если надо поменять статусы последних раундов
     *  4. Обновить весь кэш, если статус любого раунда был изменён
     *  5. Сделать commit
     *  6. Получить свежие игровые данные
     *  7. Транслировать свежие игровые данные через публичный канал
     *
     *  m. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------------------------------------------------//
    // Отслеживать и обновлять статус всех не-finished-раундов, транслировать свежие игровые данные, если надо //
    //---------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить все игровые данные из кэша
      $rooms = json_decode(Cache::get('processing:rooms'), true);

      // 2. Подготовить маячёк (изменился ли статус любого раунда)
      $is_any_round_status_was_changed = false;

      // 3. Пробежаться по $rooms, если надо поменять статусы последних раундов
      foreach($rooms as $room) {

        // 3.1. Получить набор параметров, необходимых для трекинга статуса
        $params = call_user_func(function() USE ($room) {

          // 1] Подготовить массив для результата
          $result = [];

          // 2] Получить из $room последний раунд
          $result['lastround'] = $room['rounds'][count($room['rounds']) - 1];

          // 3] Получить массив-текущий-статус для lastround
          $result['lastround_status'] = $result['lastround']['rounds_statuses'][count($result['lastround']['rounds_statuses']) - 1];

          // 4] Получить pivot-таблицу lastround актуального статуса
          $result['pivot'] = $result['lastround_status']['pivot'];

          // 5] Получить статус последнего раунда (цифру и имя)
          $result['status']       = $result['pivot']['id_status'];
          $result['status_name']  = $result['lastround_status']['status'];

          // 6] Получить дату/время установки этого статуса в формате Carbon
          $result['started_at'] = \Carbon\Carbon::parse($result['pivot']['started_at']);

          // 7] Получить продолжительность в секундах статусов Started, Pending, Lottery, Winner
          $result['duration'] = [];
          $result['duration']['started']  = $room['room_round_duration_sec'];
          $result['duration']['pending']  = (int)$room['pending_duration_s'];
          $result['duration']['lottery']  = round($room['lottery_duration_ms']/1000);
          $result['duration']['winner']   = (int)$room['winner_duration_s'];

          // 8] Получить ограничение на кол-во вещей в $room
          $result['max_items_per_round'] = $room['max_items_per_round'];

          // 9] Получить кол-во поставленных вещей
          $result['items_count'] = call_user_func(function() USE ($result) {

            // 10.1] Подготовить переменную для результата
            $count = 0;

            // 10.2] Пробежатсья по ставкам последнего раунда
            foreach($result['lastround']['bets'] as $bet) {
              $count = +$count + +count($bet['m8_items']);
            }

            // 10.n] Вернуть результат
            return $count;

          });

          // 11] Получить кол-во accepted-ставок, связанных с lastround
          $result['bets_accepted_count'] = count($result['lastround']['bets']);

          // 12] Текущее серверное время
          $result['current_server_time'] = \Carbon\Carbon::now();

          // 13] Не истекло ли ещё время раунда
          $result['is_round_time_is_not_expired'] = call_user_func(function() USE ($result) {

            // 13.1] Если текущий статус 1 или 2, вернуть true
            if($result['status'] < 3) return true;

            // 13.2] Если текущий статус > 3 вернуть false
            else if($result['status'] > 3) return false;

            // 13.3] Если текущий статус == 3
            else {

              // 13.3.1] Если текущее серверное время достигло времени истечения состояния started
              // - Вернуть false (утверждение, что время раунда не истекло, не верное)
              if($result['current_server_time']->gte($result['started_at']->addSeconds($result['duration']['started']))) return false;

              // 13.3.1] Иначе, вернуть true
              else return true;

            }

          });

          // 14] Не достигнут ли лимит по вещам
          $result['is_skins_amount_limit_is_not_reached'] = call_user_func(function() USE ($result) {

            return +$result['items_count'] < +$result['max_items_per_round'];

          });

          // 15] Количество сделавших accepted-ставки пользователей
          $result['users_with_accepted_bets_count'] = call_user_func(function() USE ($result) {

            // 15.1] Подготовить переменную для результата и массив для найденных id пользователей, сделавших ставку
            $count = 0;
            $users_with_accepted_bets_ids = [];

            // 15.2] Подсчитать
            foreach($result['lastround']['bets'] as $bet) {
              if(!in_array($bet['m5_users'][0]['id'], $users_with_accepted_bets_ids)) {
                array_push($users_with_accepted_bets_ids, $bet['m5_users'][0]['id']);
                $count = +$count + 1;
              }
            }

            // 15.n] Вернуть результат
            return $count;

          });

          // n] Вернуть $result
          return $result;

        });

        // 3.2. Вычислить подходящий статус для $room в текущем состоянии
        $suitable_room_status = call_user_func(function() USE ($params) {

          // 1] Created
          $is_created = call_user_func(function() USE ($params) {

            // 1.1] Если нет ни 1-й accepted-ставки, вернуть true
            if($params['bets_accepted_count'] == 0) return true;

            // 1.2] Иначе, вернуть false
            return false;

          });
          if($is_created == true) return [
            "id"    => "1",
            "name"  => "Created"
          ];

          // 2] First bet
          $is_first_bet = call_user_func(function() USE ($params) {

            // 2.1] Если есть ровно 1-на accepted-ставка, вернуть true
            if($params['bets_accepted_count'] == 1) return true;

            // 2.2] Иначе, вернуть false
            return false;

          });
          if($is_first_bet == true) return [
            "id"    => "2",
            "name"  => "First bet"
          ];

          // 3] Started
          $is_started = call_user_func(function() USE ($params) {

            // 3.1] Есть ли 2 или более accepted-ставок, и ставки сделали >= 2 разных пользователей
            $is2more_accepted_bets = call_user_func(function() USE ($params) {
              if($params['bets_accepted_count'] >= 2 && $params['users_with_accepted_bets_count'] >= 2) return true;
              return false;
            });

            // 3.2] Вернуть результат
            if(

              // Если есть более 2-х accepted-ставок, и текущий статус Created или First bet
              ($is2more_accepted_bets == true && ($params['status'] == 1 || $params['status'] == 2)) ||

              // Если есть более 2-х accepted-ставок, и текущий статус Started, и лимиты по времени/вещам не достигнуты
              ($is2more_accepted_bets == true && $params['status'] == 3 && $params['is_round_time_is_not_expired'] == true && $params['is_skins_amount_limit_is_not_reached'] == true)

            ) return true;
            else return false;

          });
          if($is_started == true) return [
            "id"    => "3",
            "name"  => "Started"
          ];

          // 4] Pending
          $is_pending = call_user_func(function() USE ($params) {

            if(

              // Если текущий статус Started, но лимит по времени или вещам вышел
              ( $params['status'] == 3 && ($params['is_round_time_is_not_expired'] == false || $params['is_skins_amount_limit_is_not_reached'] == false) ) ||

              // Если текущий статус Pending, но ещё не прошло его время
              ( $params['status'] == 4 && !($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['pending']))) )

            ) return true;
            else return false;

          });
          if($is_pending == true) return [
            "id"    => "4",
            "name"  => "Pending"
          ];

          // 5] Lottery
          $is_lottery = call_user_func(function() USE ($params) {

            if(

              // Если текущий статус Pending, и его время вышло
              ($params['status'] == 4 && ($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['pending'])))) ||

              // Если текущий статус Lottery, но его время ещё не вышло
              ($params['status'] == 5 && !($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['lottery']))))

            ) return true;
            else return false;

          });
          if($is_lottery == true) return [
            "id"    => "5",
            "name"  => "Lottery"
          ];

          // 6] Winner
          $is_winner = call_user_func(function() USE ($params) {

            if(

              // Если текущий статус Lottery, и его время вышло
              ($params['status'] == 5 && ($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['lottery'])))) ||

              // Если текущий статус Winner, но его время ещё не вышло
              ($params['status'] == 6 && !($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['winner']))))

            ) return true;
            else return false;

          });
          if($is_winner == true) return [
            "id"    => "6",
            "name"  => "Winner"
          ];

          // 7] Finished
          $is_finished = call_user_func(function() USE ($params) {

            if(

              // Если текущий статус Winner, и его время вышло
              ($params['status'] == 6 && ($params['current_server_time']->gte($params['started_at']->addSeconds($params['duration']['winner'])))) ||

              // Если текущий статус Finished
              ($params['status'] == 7)

            ) return true;
            else return false;

          });
          if($is_finished == true) return [
            "id"    => "7",
            "name"  => "Finished"
          ];

          // n] Вернуть значение, означающее, что статус вычислить не удалось
          return [
            "id"    => "0",
            "name"  => "Undefined"
          ];

        });

        // 3.3. Если вычисленный статус отличается от старого в бОльшую сторону
        // - Провести операцию по смене статуса для $params['lastround'].
        // - Не забыть указать ended_at и comment для старого статуса.
        // - Не забыть указать started_at для нового статуса.
        // - Отметить, что статус был изменён в $is_any_round_status_was_changed.
        //   В зависимости от значения этой переменной:
        //
        //    • В конце вернуть параметр is_cache_was_updated.
        //    • Вне foreach обновить весь кэш.
        //    • Вне foreach отправить данные через публ.канал
        //      - Но только, если новый статус не <= 3.
        //
        if(
          $suitable_room_status['name'] != $params['status_name'] &&
          $suitable_room_status['id'] > $params['status']
        ) {

          // 1] Отметить, что статус был изменён
          $is_any_round_status_was_changed = true;

          // 2] Получить модель последнего раунда lastround
          $lastround = \M9\Models\MD2_rounds::where('id', $params['lastround']['id'])
              ->first();
          if(empty($lastround))
            throw new \Exception('Не удалось получить последний раунд с ID = '.$params['lastround']['id'].' комнаты с ID = '.$room['id']);

          // 3] Добавить значение ended_at и comment для старого статуса
          $lastround->rounds_statuses()->updateExistingPivot($params['status'], [
            "ended_at" => $params['current_server_time']->toDateTimeString(),
            "comment"  => "Автоматическое изменение статуса раунда командой m9.C18"
          ]);

          // 4] Отвязать $lastround от старого статуса
          $lastround->rounds_statuses()->detach($params['status']);

          // 5] Привязать $lastround к новому статусу
          $lastround->rounds_statuses()->attach($suitable_room_status['id']);

          // 6] Добавить значение started_at для нового статуса $lastround
          $lastround->rounds_statuses()->updateExistingPivot($suitable_room_status['id'], [
            "started_at" => $params['current_server_time']->toDateTimeString(),
            "comment"    => "Автоматическое изменение статуса раунда командой m9.C18"
          ]);

          // 7] Если новый статус - Lottery - то вычислить победителя
          if($suitable_room_status['name'] == "Lottery") {
            $result = runcommand('\M9\Commands\C23_who_are_you_mr_winner', [
              "id_round" => $lastround['id'],
              "id_room"  => $room['id']
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);
          }

        }

      }

      throw new \Exception("Stop!");

      // 4. Обновить весь кэш, если статус любого раунда был изменён
      if($is_any_round_status_was_changed == true) {
        $result = runcommand('\M9\Commands\C13_update_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);
      }

      // 5. Сделать commit
      DB::commit();

      // 6. Получить свежие игровые данные
      $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1]);
      if($allgamedata['status'] != 0)
        throw new \Exception($allgamedata['data']['errormsg']);

      // 7. Транслировать свежие игровые данные через публичный канал
      // - Если статус любого раунда был изменён
      if($is_any_round_status_was_changed == true) {
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

      // m. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "is_cache_was_updated" => $is_any_round_status_was_changed
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C18_round_statuses_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C18_round_statuses_tracking']);
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

