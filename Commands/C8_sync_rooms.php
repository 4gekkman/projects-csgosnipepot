<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Sync data about rooms in DB with data from the M-package config
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
class C8_sync_rooms extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из конфига информацию о комнатах, провести валидацию
     *  2. Мягко удалить все комнаты из БД
     *  3. Добавить/Восстановить-обновить в БД те комнаты, которые есть в $rooms
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------------------//
    // Синхронизировать данные о комнатах в БД с данными о них в конфиге M-пакета //
    //----------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из конфига информацию о комнатах, провести валидацию
      $rooms = call_user_func(function(){

        // 1] Получить из конфига информацию о комнатах
        $rooms = config("M9.rooms");

        // 2] Если $rooms это не массив, вернуть пустой массив
        if(!is_array($rooms)) return [];

        // 3] Провести валидацию $rooms
        $validator = r4_validate($rooms, [

          // Основные
          "*"                           => ["sometimes", "array"],
          "*.bet_accepting_mode"        => ["required", "in:availability"],
          "*.name"                      => ["required", "string"],
          "*.description"               => ["required", "string"],
          "*.description_full"          => ["required", "string"],
          "*.is_on"                     => ["required", "regex:/^[01]{1}$/ui"],
          "*.room_round_duration_sec"   => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "*.max_items_per_round"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "*.min_bet"                   => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.allow_unstable_prices"     => ["required", "regex:/^[01]{1}$/ui"],
          "*.allow_only_types"          => ["required", "json"],
          "*.allow_only_types.*"        => ["required", "in:case,key,startrak,souvenir packages,knife,weapon,souvenir"],
          "*.change"                    => ["required", "regex:/^[01]{1}$/ui"],
          "*.one_bot_payout"            => ["required", "regex:/^[01]{1}$/ui"],

          // Дополнительные
          "*.max_items_per_bet"         => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.min_items_per_bet"         => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.min_items_per_round"       => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.max_bet"                   => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.min_bet_round"             => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],
          "*.max_bet_round"             => ["r4_defined", "regex:/^(|[1-9]+[0-9]*)$/ui"],

        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 4] Вернуть $rooms
        return $rooms;

      });

      // 2. Мягко удалить все комнаты из БД
      $rooms_db = \M9\Models\MD1_rooms::get();
      foreach($rooms_db as $room_db) {
        $room_db->delete();
      }

      // 3. Добавить/Восстановить-обновить в БД те комнаты, которые есть в $rooms
      foreach($rooms as $name => $room) {

        // 1] Попробовать найти в БД комнату с именем $name
        $room2save = \M9\Models\MD1_rooms::withTrashed()->where('name', $name)->first();

        // 2] Если $room2save не найдена
        if(empty($room2save)) {

          // 2.1] Создать новую комнату
          $room2save = new \M9\Models\MD1_rooms();

          // 2.2] Наполнить $room2save
          $room2save->bet_accepting_mode      = $room['bet_accepting_mode'];
          $room2save->name                    = $room['name'];
          $room2save->description             = $room['description'];
          $room2save->description_full        = $room['description_full'];
          $room2save->is_on                   = $room['is_on'];
          $room2save->room_round_duration_sec = $room['room_round_duration_sec'];
          $room2save->max_items_per_round     = $room['max_items_per_round'];
          $room2save->min_bet                 = $room['min_bet'];
          $room2save->allow_unstable_prices   = $room['allow_unstable_prices'];
          $room2save->allow_only_types        = $room['allow_only_types'];
          $room2save->change                  = $room['change'];
          $room2save->one_bot_payout          = $room['one_bot_payout'];
          $room2save->max_items_per_bet       = $room['max_items_per_bet'];
          $room2save->min_items_per_bet       = $room['min_items_per_bet'];
          $room2save->min_items_per_round     = $room['min_items_per_round'];
          $room2save->max_bet                 = $room['max_bet'];
          $room2save->min_bet_round           = $room['min_bet_round'];
          $room2save->max_bet_round           = $room['max_bet_round'];

          // 2.3] Сохранить $room2save
          $room2save->save();

        }

        // 3] Если $room2save найдена
        else {

          // 3.1] Если $room2save мягко удалена, восстановить
          $room2save->restore();

          // 3.2] Сохранить $room2save
          $room2save->save();

        }

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C8_sync_rooms from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C8_sync_rooms']);
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

