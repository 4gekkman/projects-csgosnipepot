<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Cache updating for game processing
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        all           | True/False, если true, то обновить весь кэш
 *        cache2update  | Массив ключей кэша, который надо обновить (нужен, только если all не указано)
 *        force         | (по умолчанию, == true) Обновлять кэш, даже если он присутствует
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
class C13_update_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Обновить кэш, который указан в cache2update
     *    3.1. processing:bets:active +
     *         processing:bets:active:<id пользователя> +
     *         processing:bets:active:<id пользователя>:<id комнаты>
     *    3.2. processing:bets:accepted
     *    3.3. processing:rooms
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------//
    // Обновляет указанный кэш в рамках процессинга игры //
    //---------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "all"             => ["boolean"],
        "cache2update"    => ["required_without:all", "array"],
        "force"           => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если all не передано, задать ей значение по умолчанию false
        if(!array_key_exists('all', $this->data))
          $this->data['all'] = false;

        // 2.2. Если cache2update не передан, назначить пустой массив
        if(!array_key_exists('cache2update', $this->data))
          $this->data['cache2update'] = [];

        // 2.3. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;


      // 3. Обновить кэш, который указан в cache2update

        // 3.1. processing:bets:active + processing:bets:active:<id пользователя> + processing:bets:active:<id пользователя>

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('processing:bets:active'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('processing:bets:active') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:bets:active", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все ставки со статусом Active
              // - Включая все их связи.
              $active_bets = \M9\Models\MD3_bets::with(["m8_bots", "m8_items", "m5_users", "safecodes", "rooms", "rounds", "bets_statuses"])
                ->whereHas('bets_statuses', function($query){
                  $query->where('status', 'Active');
                })
                ->get();

              // 2] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$active_bets) {

                // 2.1] Записать JSON с $active_bets в кэш
                Cache::put('processing:bets:active', json_encode($active_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 2.2] Пробежаться по $cache, и записать индивидуальный кэш активных ставок (пользователь)

                  // 2.2.1] Получить не повторяющийся список ID пользователей, которые есть в $active_bets
                  $users_ids = call_user_func(function() USE ($active_bets) {
                    $result = [];
                    foreach($active_bets as $bet) {
                      $id_user = $bet['m5_users'][0]['id'];
                      if(!in_array($id_user, $result))
                        array_push($result, $id_user);
                    }
                    return $result;
                  });

                  // 2.2.2] Получить для каждого $users_ids свой массив с активными ставками
                  $users_active_bets = call_user_func(function() USE ($users_ids, $active_bets) {
                    $result = [];
                    foreach($users_ids as $id) {
                      foreach($active_bets as $bet) {
                        $id_user = $bet['m5_users'][0]['id'];
                        if($id_user == $id) {
                          if(!array_key_exists($id_user, $result)) $result[$id_user] = [];
                          array_push($result[$id_user], $bet);
                        }
                      }
                    }
                    return $result;
                  });

                  // 2.2.3] Записать $users_active_bets в кэш
                  foreach($users_active_bets as $id_user => $bets) {
                    Cache::tags(['processing:bets:active:personal'])->put('processing:bets:active:'.$id_user, json_encode($bets, JSON_UNESCAPED_UNICODE), 30);
                  }

                // 2.3] Пробежаться по $cache, и записать индивидуальный кэш активных ставок (пользователь + комната)
                foreach($active_bets as $bet) {
                  $id_user = $bet['m5_users'][0]['id'];
                  $id_room = $bet['rooms'][0]['id'];
                  Cache::tags(['processing:bets:active:personal'])->put('processing:bets:active:'.$id_user.':'.$id_room, json_encode($bet, JSON_UNESCAPED_UNICODE), 30);
                }

                // 2.4] Если $active_bets пуст, сбросить весь персонализированный кэш
                if(count($active_bets) == 0) {
                  Cache::tags(['processing:bets:active:personal'])->flush();
                }

              });

              // 3] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$active_bets) {

                // 3.1] Получить версию коллекции $active_bets с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • Полностью m8_bots.
                //
                $active_bets->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [];

                  // 2) Вернуть $value_arr
                  return $value_arr;

                });

                // 3.2] Записать JSON с $active_bets в кэш
                Cache::put('processing:bets:active:safe', json_encode($active_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3.3] Пробежаться по $cache, и записать индивидуальный кэш активных ставок (пользователь)

                  // 3.3.1] Получить не повторяющийся список ID пользователей, которые есть в $active_bets
                  $users_ids = call_user_func(function() USE ($active_bets) {
                    $result = [];
                    foreach($active_bets as $bet) {
                      $id_user = $bet['m5_users'][0]['id'];
                      if(!in_array($id_user, $result))
                        array_push($result, $id_user);
                    }
                    return $result;
                  });

                  // 3.3.2] Получить для каждого $users_ids свой массив с активными ставками
                  $users_active_bets = call_user_func(function() USE ($users_ids, $active_bets) {
                    $result = [];
                    foreach($users_ids as $id) {
                      foreach($active_bets as $bet) {
                        $id_user = $bet['m5_users'][0]['id'];
                        if($id_user == $id) {
                          if(!array_key_exists($id_user, $result)) $result[$id_user] = [];
                          array_push($result[$id_user], $bet);
                        }
                      }
                    }
                    return $result;
                  });

                  // 3.3.3] Записать $users_active_bets в кэш
                  foreach($users_active_bets as $id_user => $bets) {
                    Cache::tags(['processing:bets:active:personal:safe'])->put('processing:bets:active:safe:'.$id_user, json_encode($bets, JSON_UNESCAPED_UNICODE), 30);
                  }

                // 3.4] Пробежаться по $cache, и записать индивидуальный кэш активных ставок (пользователь + комната)
                foreach($active_bets as $bet) {
                  $id_user = $bet['m5_users'][0]['id'];
                  $id_room = $bet['rooms'][0]['id'];
                  Cache::tags(['processing:bets:active:personal:safe'])->put('processing:bets:active:safe:'.$id_user.':'.$id_room, json_encode($bet, JSON_UNESCAPED_UNICODE), 30);
                }

                // 3.5] Если $active_bets пуст, сбросить весь персонализированный кэш
                if(count($active_bets) == 0) {
                  Cache::tags(['processing:bets:active:personal:safe'])->flush();
                }

              });


            }

          }

        // 3.2. processing:bets:accepted

          // 3.2.1. Получить кэш
          $cache = json_decode(Cache::get('processing:bets:accepted'), true);

          // 3.2.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:bets:accepted') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:bets:accepted", $this->data['cache2update']) == true || $this->data['all'] == true) {
              call_user_func(function(){

                // 1] Получить все ставки со статусом Accepted
                // - Включая все их связи.
                $accepted_bets = \M9\Models\MD3_bets::with(["m8_bots", "m8_items", "m5_users", "safecodes", "rooms", "rounds", "bets_statuses"])
                  ->whereHas('bets_statuses', function($query){
                    $query->where('status', 'Accepted');
                  })
                  ->get();

                // 2] Обновить полную (не safe) версию кэша
                Cache::put('processing:bets:accepted', json_encode($accepted_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3] Обновить безопасную (safe) версию кэша
                call_user_func(function() USE (&$accepted_bets) {

                  // 3.1] Получить версию коллекции $accepted_bets с отфильтрованной секретной информацией
                  // - Что должно быть вырезано цензурой:
                  //
                  //    • Полностью m8_bots.
                  //
                  $accepted_bets->transform(function($value, $key){

                    // 1) Удалить m8_bots из $value
                    $value_arr = $value->toArray();
                    $value_arr['m8_bots'] = [];

                    // 2) Вернуть $value_arr
                    return $value_arr;

                  });

                  // 3.2] Записать JSON с $accepted_bets в кэш
                  Cache::put('processing:bets:accepted:safe', json_encode($accepted_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

                });

              });
            }

          }

        // 3.3. processing:rooms

          // 3.3.1. Получить кэш
          $cache = json_decode(Cache::get('processing:rooms'), true);

          // 3.3.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:rooms') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:rooms", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить массив ID последних раундов каждой комнаты
              $all_rooms_last_round_ids = call_user_func(function(){

                // 1.1] Подготовить массив для результата
                $result = [];

                // 1.2] Получить коллекцию всех комнат
                $rooms = \M9\Models\MD1_rooms::get();

                // 1.3] Наполнить $result
                foreach($rooms as $room) {
                  $lastround = \M9\Models\MD2_rounds::whereHas('rooms', function($queue) USE ($room) {
                    $queue->where('id', $room['id']);
                  })->orderBy('id', 'desc')->first();
                  array_push($result, $lastround['id']);
                }

                // 1.n] Вернуть $result
                return $result;

              });

              // 2] Получить все включенные комнаты
              // - Включая все их связи.
              // - Но не со всеми раундами, а лишь с текущим.
              // - И вместе со всеми связанными данными текущего раунда.
              $rooms = \M9\Models\MD1_rooms::with(["m8_bots", "bet_accepting_modes",
                  "rounds" => function($query) USE ($all_rooms_last_round_ids) {
                    $query->whereIn('id', $all_rooms_last_round_ids);
                  },
                  //"bets",
                  "rounds.rounds_statuses",
                  "rounds.bets",
                  "rounds.bets.m8_bots",
                  "rounds.bets.m8_items",
                  "rounds.bets.m5_users",
                  "rounds.bets.safecodes",
                  "rounds.bets.rooms",
                  //"rounds.bets.rounds",
                  "rounds.bets.bets_statuses"
                ])
                ->where('is_on', 1)
                ->get();

              // 3] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$rooms) {

                // 3.1] Добавить для каждой комнаты в $rooms текущий размер очереди ставок
                call_user_func(function() USE (&$rooms) {
                  foreach($rooms as &$room) {

                    // 3.1.1] Подсчитать кол-во офферов в очереди
                    $count = \M9\Models\MD3_bets::whereHas('rooms', function($query) USE ($room) {
                      $query->where('id',$room->id);
                    })->whereHas('bets_statuses', function($query){
                      $query->where('status', 'Accepted');
                    })->doesntHave('rounds')
                      ->count();

                    // 3.1.2] Записать в $room
                    $room->queue_offers_count = $count;

                  }
                });

                // 3.2] Записать JSON с $rooms в кэш
                Cache::put('processing:rooms', json_encode($rooms->toArray(), JSON_UNESCAPED_UNICODE), 30);

              });

              // 4] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$rooms) {

                // 4.1] Получить версию $rooms с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • m8_bots
                //    • rounds.bets.m8_bots
                //    • rounds.key
                //    • rounds.bets.m5_users.adminnote
                //
                $rooms->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [];

                  // 2) Удалить лишние свойства
                  foreach($value_arr['rounds'] as &$round) {

                    // key

                      // 1] Если статус раунда lottery или winner, не затирать key
                      if(in_array($round['rounds_statuses'][0]['status'], ['Lottery', 'Winner'])) {

                      }

                      // 2] В противном случае, затирать
                      else
                        $round['key'] = "";

                    // прочие
                    foreach($round['bets'] as &$bet) {
                      $bet['m8_bots'] = [];
                      foreach($bet['m5_users'] as &$user) {
                        $user['adminnote'] = "";
                      }
                    }
                  }

                  // 3) Вернуть $value_arr
                  return $value_arr;

                });

                // 4.2] Записать JSON с $rooms в кэш
                Cache::put('processing:rooms:safe', json_encode($rooms->toArray(), JSON_UNESCAPED_UNICODE), 30);

              });

            }

          }



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C13_update_cache from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C13_update_cache']);
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

