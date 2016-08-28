<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Sync rooms with the settings from config
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

  namespace M10\Commands;

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
class C1_sync_rooms extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Добавить/Восстановить-обносить в БД те комнаты, которые есть в $rooms
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------//
    // Синхронизировать таблицу комнат в БД M10 с настройками из конфига //
    //-------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из конфига информацию о комнатах, провести валидацию
      $rooms = call_user_func(function(){

        // 1] Получить из конфига информацию о комнатах
        $rooms = config("M10.rooms");

        // 2] Если $rooms это не массив, вернуть пустой массив
        if(!is_array($rooms)) return [];

        // 3] Провести валидацию $rooms
        $validator = r4_validate($rooms, [
          "*"                 => ["sometimes", "array"],
          "*.description"     => ["required", "string"],
          "*.expire"          => ["required", "regex:/^[0-9]+$/ui"],
          "*.messages_limit"  => ["required", "regex:/^[0-9]+$/ui"],
          "*.max_msg_length"  => ["required", "regex:/^[0-9]+$/ui"],
          "*.max_messages"    => ["required", "regex:/^[0-9]+$/ui"],
          "*.allow_guests"    => ["required", "regex:/^[01]{1}$/ui"],
          "*.moderator_ids"   => ["r4_defined", "array"],
          "*.moderator_ids.*" => ["sometimes", "regex:/^[1-9]+[0-9]*$/ui"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 4] Вернуть $rooms
        return $rooms;

      });

      // 2. Мягко удалить все комнаты из БД
      $rooms_db = \M10\Models\MD1_rooms::get();
      foreach($rooms_db as $room_db) {
        $room_db->delete();
      }

      // 3. Добавить/Восстановить-обновить в БД те комнаты, которые есть в $rooms
      foreach($rooms as $name => $room) {

        // 1] Попробовать найти в БД комнату с именем $name
        $room2save = \M10\Models\MD1_rooms::withTrashed()->where('name', $name)->first();

        // 2] Если $room2save не найдена
        if(empty($room2save)) {

          // 2.1] Создать новую комнату
          $room2save = new \M10\Models\MD1_rooms();

          // 2.2] Наполнить $room2save
          $room2save->name            = $name;
          $room2save->description     = $room['description'];
          $room2save->expire          = $room['expire'];
          $room2save->messages_limit  = $room['messages_limit'];
          $room2save->max_msg_length  = $room['max_msg_length'];
          $room2save->max_messages    = $room['max_messages'];
          $room2save->allow_guests    = $room['allow_guests'];

          // 2.3] Назначить комнате модераторов
          foreach($room['moderator_ids'] as $moderator_id) {

            // 2.3.1] Удостовериться, что пользователь с таким ID имеется
            $user = \M5\Models\MD1_users::withTrashed()->where('id', $moderator_id)->first();

            // 2.3.2] Если такой есть, то назначить его модератором комнаты $room2save, если ещё не назначен
            if(!empty($user)) {
              if(!$room2save->m5_users->contains($moderator_id))
                $room2save->m5_users()->attach($moderator_id);
            }

          }

          // 2.4] Сохранить $room2save
          $room2save->save();

        }

        // 3] Если $room2save найдена
        else {

          // 3.1] Если $room2save мягко удалена, восстановить
          $room2save->restore();

          // 3.2] Обновить некоторые св-ва $room2save
          $room2save->description     = $room['description'];
          $room2save->expire          = $room['expire'];
          $room2save->messages_limit  = $room['messages_limit'];
          $room2save->max_msg_length  = $room['max_msg_length'];
          $room2save->max_messages    = $room['max_messages'];
          $room2save->allow_guests    = $room['allow_guests'];

          // 3.3] Отвязать всех модераторов от комнаты
          $room2save->m5_users()->detach();

          // 3.4] Назначить комнате модераторов
          foreach($room['moderator_ids'] as $moderator_id) {

            // 3.4.1] Удостовериться, что пользователь с таким ID имеется
            $user = \M5\Models\MD1_users::withTrashed()->where('id', $moderator_id)->first();

            // 3.4.2] Если такой есть, то назначить его модератором комнаты $room2save, если ещё не назначен
            if(!empty($user)) {
              if(!$room2save->m5_users->contains($moderator_id))
                $room2save->m5_users()->attach($moderator_id);
            }

          }

          // 3.5] Сохранить $room2save
          $room2save->save();

        }

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_sync_rooms from M-package M10 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M10', 'C1_sync_rooms']);
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

