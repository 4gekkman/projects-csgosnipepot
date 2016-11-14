<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Can we make a bet in the room now, or not
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_room
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
 *
 *  Можно, если выполняются следующие условия
 *  -----------------------------------------
 *
 *    • Текущий раунд в комнате id_room существует
 *    • Текущий статус раунда <= 3
 *    • Без учета ставки, не превышен лимит по сумме банка.
 *    • Без учета ставки, не первышен лимит по кол-ву вещей.
 *
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
class C22_canwe_makeabet_intheroom_now extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить и проверить входящие данные
     *  2. Получить комнату id_room
     *  3. Получить последний раунд, связанный с комнатой $room
     *  4. Получить значение (число) статуса последнего раунда
     *  5. Получить все лимиты комнаты $room
     *  6. Вычислить текущие параметры банка для $lastround
     *
     *  n. вернуть результат
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------//
    // Можно ли сделать ещё одну ставку в текущем раунде комнаты id_room //
    //-------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить и проверить входящие данные
      $validator = r4_validate($this->data, [

        "id_room"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Получить из кэша текущее состояние игры
      $rooms = json_decode(Cache::get('processing:rooms'), true);


      write2log($rooms, []);




//      // 2. Получить комнату id_room
//      $room = \M9\Models\MD1_rooms::find($this->data['id_room']);
//      if(empty($room))
//        throw new \Exception('Комната с ID == '.$this->data['id_room'].' не найдена.');
//
//      // 3. Получить последний раунд, связанный с комнатой $room
//      $lastround = \M9\Models\MD2_rounds::with([
//        "bets",
//        "bets.m8_bots",
//        "bets.m8_items",
//        "bets.m5_users",
//        "bets.safecodes",
//        "bets.rooms",
//        "bets.bets_statuses",
//        "rounds_statuses"
//      ])->whereHas('rooms', function($query){
//        $query->where('id', $this->data['id_room']);
//      })->orderBy('id', 'desc')->first();
//
//      // 4. Получить значение (число) статуса последнего раунда
//      $lastround_status = call_user_func(function() USE ($lastround) {
//
//        // 1] Если $lastround пуст
//        if(empty($lastround))
//          return [
//            'status'  => '',
//            'success' => false
//          ];
//
//        // 2] Получить статус
//        $status = $lastround['rounds_statuses'][count($lastround['rounds_statuses']) - 1]['pivot']['id_status'];
//
//        // 3] Если $lastround и $status не пусты, а $status - число
//        if(!empty($lastround) && !empty($status) && is_numeric($status))
//          return [
//            'status'  => $status,
//            'success' => true
//          ];
//
//        // 4] Вернуть результат по умолчанию
//        return [
//          'status'  => '',
//          'success' => false
//        ];
//
//      });
//
//      // 5. Получить все лимиты комнаты $room
//      $room_limits = call_user_func(function() USE ($room) {
//
//        return [
//          "max_items_per_round" => $room['max_items_per_round'],  // MAX кол-во предметов в раунде
//          "max_round_jackpot"   => $room['max_round_jackpot'],    // MAX банк раунда в центах
//          "max_bets_per_round"  => $room['max_bets_per_round'],   // MAX кол-во ставок игроком за раунд
//        ];
//
//      });
//
//      // 6. Вычислить текущие параметры банка для $lastround
//      $bank = call_user_func(function() USE ($lastround) {
//
//        // 1] Подготовить массив для результатов
//        $results = [];
//
//        // 2] Вычислить текущую сумму банка
//        $results['sum'] = call_user_func(function(){
//          $result = 0;
//          // TODO
//          return $result;
//        });
//
//        // 3] Вычислить текущее вол-ко вещей в банке
//        $results['count'] = call_user_func(function(){
//          $result = 0;
//          // TODO
//          return $result;
//        });
//
//        // n] Вернуть результаты
//        return $results;
//
//      });










      // n. вернуть результат
      return [
        "status"  => 0,
        "data"    => [
          "result"            => "true",
          //"room"              => $room,
          //"lastround"         => $lastround,
          //"lastround_status"  => $lastround_status,
        ]
      ];






//      // 10. Вычислить текущие параметры банка для $lastround
//      $bank = call_user_func(function() USE ($lastround) {
//
//        // 1] Подготовить массив для результатов
//        $results = [];
//
//        // 2] Вычислить текущую сумму банка
//        $results['sum'] = call_user_func(function(){
//          $result = 0;
//          // TODO
//          return $result;
//        });
//
//        // 3] Вычислить текущее вол-ко вещей в банке
//        $results['count'] = call_user_func(function(){
//          $result = 0;
//          // TODO
//          return $result;
//        });
//
//        // n] Вернуть результаты
//        return $results;
//
//      });
//
//      // 11. Определить, превышен ли уже в текущем раунде лимит по сумме/вещам
//      $is_limits_exceeded = call_user_func(function() USE ($room_limits, $bank) {
//
//        // 1] Подготовить массив для результатов
//        $results = [];
//
//        // 2] Превышен ли лимит по сумме
//        $results['by_sum'] = call_user_func(function() USE ($room_limits, $bank) {
//          $result = false;
//          // TODO
//          return $result;
//        });
//
//        // 3] Превышен ли лимит по вещам
//        $results['by_items'] = call_user_func(function() USE ($room_limits, $bank) {
//          $result = false;
//          // TODO
//          return $result;
//        });
//
//        // n] Вернуть результаты
//        return $results;
//
//      });




    } catch(\Exception $e) {
        $errortext = 'Invoking of command C22_canwe_makeabet_intheroom_now from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C22_canwe_makeabet_intheroom_now']);
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

