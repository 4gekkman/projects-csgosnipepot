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
     *  2. Пробежаться по $rooms
     *    2.1.
     *
     *  n. Сделать commit
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

      // 2. Пробежаться по $rooms
      foreach($rooms as $room) {

        // 2.1. Получить набор параметров, необходимых для трекинга статуса
        $params = call_user_func(function() USE ($room) {

          // 1] Подготовить массив для результата
          $result = [];

          // 2] Получить из $room последний раунд
          $result['lastround'] = $room['rounds'][count($room['rounds']) - 1];

          // 3] Получить pivot-таблицу lastround актуального статуса
          $result['pivot'] = $result['lastround']['rounds_statuses'][count($result['lastround']['rounds_statuses']) - 1]['pivot'];

          // 4] Получить статус последнего раунда (цифру)
          $result['status'] = $result['pivot']['id_status'];

          // 5] Получить дату/время установки этого статуса в формате Carbon
          $result['started_at'] = \Carbon\Carbon::parse($result['pivot']['started_at']);

          // 6] Получить продолжительность в секундах статусов Started, Pending, Lottery, Winner
          $result['duration'] = [];
          $result['duration']['started']  = $room['room_round_duration_sec'];
          $result['duration']['pending']  = "5";
          $result['duration']['lottery']  = round($room['lottery_duration_ms']/1000);
          $result['duration']['winner']   = "5";

          // 7] Получить ограничение на кол-во вещей в $room
          $result['max_items_per_round'] = $room['max_items_per_round'];

          // 8] Получить кол-во поставленных вещей
          $result['items_count'] = call_user_func(function() USE ($result) {

            // 8.1] Подготовить переменную для результата
            $count = 0;

            // 8.2] Пробежатсья по ставкам последнего раунда
            foreach($result['lastround']['bets'] as $bet) {
              $count = +$count + +count($bet['m8_items']);
            }

            // 8.n] Вернуть результат
            return $count;

          });

          // 9] Получить кол-во accepted-ставок, связанных с lastround
          $result['bets_accepted_count'] = count($result['lastround']['bets']);

          // 10] Текущее серверное время
          $result['current_server_time'] = \Carbon\Carbon::now();

          // n] Вернуть $result
          return $result;

        });

        // 2.2. Вычислить подходящий статус для $room в текущем состоянии
        $suitable_room_status = call_user_func(function() USE ($params) {

          // 1] Created
          $is_created = call_user_func(function() USE ($params) {

            // 1.1] Если нет ни 1-й accepted-ставки, вернуть true
            if($params['bets_accepted_count'] == 0) return true;

            // 1.2] Иначе, вернуть false
            return false;

          });
          if($is_created == true) return "Created";

          // 2] First bet
          $is_first_bet = call_user_func(function() USE ($params) {

            // 2.1] Если есть ровно 1-на accepted-ставка, вернуть true
            if($params['bets_accepted_count'] == 1) return true;

            // 2.2] Иначе, вернуть false
            return false;

          });
          if($is_first_bet == true) return "First bet";

          // 3] Started
          $is_started = call_user_func(function() USE ($params) {

            // 3.1] Есть ли 2 или более accepted-ставок
            $is2more_accepted_bets = call_user_func(function() USE ($params) {
              if($params['bets_accepted_count'] >= 2) return true;
              return false;
            });

            // 3.2] Является ли текущий статус раунда равным 3
            $is_current_status_started = $params['status'] == 3 ? true : false;

            // 3.3] Не истекло ли ещё время раунда
            $is_round_time_is_not_expired = call_user_func(function() USE ($params) {


              // $params['duration']['started']->gt($params['current_server_time'])
            });

            // 3.4] Не достигнут ли лимит по вещам
            $is_skins_amount_limit_reached = call_user_func(function() USE ($params) {



            });

            // 3.n] Вернуть результат
            if($is2more_accepted_bets == true && ())

          });
          if($is_started == true) return "Started";

          // 4] Pending
          $is_pending = call_user_func(function() USE ($params) {



          });
          if($is_pending == true) return "Pending";

          // 5] Lottery
          $is_lottery = call_user_func(function() USE ($params) {



          });
          if($is_lottery == true) return "Lottery";

          // 6] Winner
          $is_winner = call_user_func(function() USE ($params) {



          });
          if($is_winner == true) return "Winner";

          // 7] Finished
          $is_finished = call_user_func(function() USE ($params) {



          });
          if($is_finished == true) return "Finished";

          // n] Вернуть значение, означающее, что статус вычислить не удалось
          return "Undefined";

        });

        write2log($suitable_room_status, []);



      }







      //write2log($rooms, []);



      // Не отправлять данные через публичный канал, если итоговый статус раунда <= 3.

      // Обновлять кэш только в случае необходимости

      // В результатах (is_cache_was_updated) возвращать, был ли обновлён кэш

      // Переключение статуса возможно только "вперёд", и невозможно "назад"


      // n. Сделать commit
      DB::commit();

      // m. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "is_cache_was_updated" => false
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

