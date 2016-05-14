<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Change a user
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

  namespace M5\Commands;

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
class C17_changeuser extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять входящие параметры
     *  2. Провести валидацию входящих параметров
     *  3. Попробовать найти пользователя с таким email или phone
     *  4. Если $params['isanonymous'] == 'yes' проверить, нет ли уже в системе анонимного пользователя
     *  5. Если требуется изменить пол, получить ID для нового пола
     *  6. Попробовать найти пользователя с указанным ID
     *  7. Внести изменения в $user
     *  8. Сделать commit
     *  9. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Изменить пользователя //
    //-----------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять входящие параметры

        // 1.1. Принять
        $params = $this->data;

//        // 1.2. Обработать
//        foreach($params as $key => $value)
//          if($value == "0" || $value == "0") $params[$key] = null;
//
//        // 1.3. Отфильтровать из $params пустые значения
//        $params = array_filter($params, function($item){
//          if(empty($item)) return false;
//          return true;
//        });

      // 2. Провести валидацию входящих параметров
      $validator = r4_validate($params, [

        "id"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

        "name"            => ["sometimes", "regex:/^[a-zа-яё]+$/ui"],
        "surname"         => ["sometimes", "regex:/^[a-zа-яё]+$/ui"],
        "patronymic"      => ["sometimes", "regex:/^[a-zа-яё]+$/ui"],

        "email"           => ["required", "email"],
        "phone"           => ["sometimes", "regex:/^[0-9]+$/ui"],

        "gender"          => ["required", "in:m,f,u"],
        "birthday"        => ["sometimes", "date"],

        "isanonymous"     => ["required", "in:yes,no"],
        "is_blocked"      => ["required", "in:yes,no"],
        "adminnote"       => ["sometimes", "string"]

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 3. Попробовать найти пользователя с таким email или phone

        // email
        if(!empty($params['email'])) {

          // 1] Искать
          $user2edit = \M5\Models\MD1_users::where('email', $params['email'])->first();

          // 2] Если найден другой пользователь, завершить
          if(!empty($user2edit) && $user2edit->id != $params['id'])
            throw new \Exception("Пользователь с email '$user2edit->email' уже есть в системе, его ID = ".$user2edit->id);

        }

        // phone
        if(!empty($params['phone'])) {

          // 1] Искать
          $user2edit = \M5\Models\MD1_users::where('phone', $params['phone'])->first();

          // 2] Если найден другой пользователь, завершить
          if(!empty($user2edit) && $user2edit->id != $params['id'])
            throw new \Exception("Пользователь с phone '$user2edit->phone' уже есть в системе, его ID = ".$user2edit->id);

        }

      // 4. Если $params['isanonymous'] == 'yes' проверить, нет ли уже в системе анонимного пользователя
      if(array_key_exists('isanonymous', $params) && $params['isanonymous'] == 'yes') {
        $isanonymous = \M5\Models\MD1_users::withTrashed()->where('isanonymous', 1)->first();
        if(!empty($isanonymous) && $isanonymous->id != $params['id']) throw new \Exception('В системе можеть быть лишь 1 анонимный пользователь, и таковой уже имеется с ID = '.$isanonymous->id);
      }

      // 5. Если требуется изменить пол, получить ID для нового пола
      if(array_key_exists('gender', $params)) {
        $gender = \M5\Models\MD11_genders::where('name',$params['gender'])->first();
        if(empty($gender)) throw new \Exception('В таблице полов не удалось найти пол '.$params['gender']);
      }

      // 6. Попробовать найти пользователя с указанным ID
      $user = \M5\Models\MD1_users::find($params['id']);
      if(empty($user))
        throw new \Exception("Пользователь с id '".$params['id']."' не найден в системе среди активных (не мягко удалённых) аккаунтов");

      // 7. Внести изменения в $user

        // 7.1. Отменить "подтверждённость" email, если он изменился
        if($user->id == $params['id'] && $user->email != $params['email']) {
          $user->is_email_approved = 0;
        }

        // 7.2. Отменить "подтверждённость" phone, если он изменился
        if($user->id == $params['id'] && $user->phone != $params['phone']) {
          $user->is_phone_approved = 0;
        }

        // 7.3. Внести основные изменения
        foreach($params as $key => $value) {

          // Если $key == 'timestamp', продолжить
          if($key == 'timestamp') continue;

          // Если $key == 'id', продолжить
          if($key == 'id') continue;

          // Если $key == 'is_email_approved', продолжить
          if($key == 'is_email_approved') continue;

          // Если $key == 'is_phone_approved', продолжить
          if($key == 'is_phone_approved') continue;

          // Если $key == 'password'
          if($key == 'password') {
            $user["password_hash"] = Hash::make($value);
            continue;
          }

          // Если $key == 'isanonymous'
          if($key == 'isanonymous') {
            $user[$key] = $value == 'yes' ? 1 : 0;
            continue;
          }

          // Если $key == 'is_blocked'
          if($key == 'is_blocked') {
            $user[$key] = $value == 'yes' ? 1 : 0;
            continue;
          }

          // Если $key == 'gender'
          if($key == 'gender') {
            $user[$key] = $gender->id;
            continue;
          }

          // В общем случае
          $user[$key] = $value;

        }

        // 7.n. Сохранить
        $user->save();

      // 8. Сделать commit
      DB::commit();

      // 9. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "id"      => $user->id
        ]
      ];


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C17_changeuser from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C17_changeuser']);
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

