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
     *    3.1. processing:bets:active
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
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если all не передано, задать ей значение по умолчанию false
        if(!array_key_exists('all', $this->data))
          $this->data['all'] = false;

        // 2.2. Если cache2update, назначить пустой массив
        if(!array_key_exists('cache2update', $this->data))
          $this->data['cache2update'] = [];

      // 3. Обновить кэш, который указан в cache2update

        // 3.1. processing:bets:active
        if(in_array("processing:bets:active", $this->data['cache2update']) == true || $this->data['all'] == true) {
          call_user_func(function(){

            // 1] Получить все ставки со статусом Active
            // - Включая все их связи.
            $active_bets = \M9\Models\MD3_bets::with(["m8_bots", "m8_items", "m5_users", "safecodes", "rooms", "rounds", "bets_statuses"])
              ->whereHas('bets_statuses', function($query){
                $query->where('status', 'Active');
              })
              ->get();

            // 2] Записать JSON с $active_bets в кэш
            Cache::put('processing:bets:active', json_encode($active_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

          });
        }

        // 3.2. processing:bets:accepted
        if(in_array("processing:bets:accepted", $this->data['cache2update']) == true || $this->data['all'] == true) {
          call_user_func(function(){

            // 1] Получить все ставки со статусом Accepted
            // - Включая все их связи.
            $accepted_bets = \M9\Models\MD3_bets::with(["m8_bots", "m8_items", "m5_users", "safecodes", "rooms", "rounds", "bets_statuses"])
              ->whereHas('bets_statuses', function($query){
                $query->where('status', 'Accepted');
              })
              ->get();

            // 2] Записать JSON с $accepted_bets в кэш
            Cache::put('processing:bets:accepted', json_encode($accepted_bets->toArray(), JSON_UNESCAPED_UNICODE), 30);

          });
        }

        // 3.3. processing:rooms
        if(in_array("processing:rooms", $this->data['cache2update']) == true || $this->data['all'] == true) {
          call_user_func(function(){

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
                "bets",
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

            // 3] Записать JSON с $rooms в кэш
            Cache::put('processing:rooms', json_encode($rooms->toArray(), JSON_UNESCAPED_UNICODE), 30);

          });
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

