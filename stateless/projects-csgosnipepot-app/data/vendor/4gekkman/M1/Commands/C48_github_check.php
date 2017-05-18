<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Checks if 4gekkman acc is accessible for the app
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
 *        errormsg      // если статус не 0, тут будет текст ошибки
 *        password      // пароль от github
 *        token         // токен от github
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

  namespace M1\Commands;

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
class C48_github_check extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Подготовить storage
     *  2. Проверить валидность пароля
     *  3. Проверить валидность токена
     *  4. Проверить существование ps-скрипта
     *  5. Вернуть результат
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------//
    // Проверить валидность пароля, токена и существование ps-скрипта //
    //----------------------------------------------------------------//
    // - Которые находятся в файлах.
    // - Адреса к которым указаны вконфиге M1.
    $res = call_user_func(function() { try {

      // 1. Подготовить storage
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => "/"]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

      // 2. Проверить валидность пароля

        // Проверить
        $is_pass_valid = call_user_func(function() {

          // 2.1. Получить путь к файлу с паролем из конфига M1
          $path = config("M1.github_password");
          if(empty($path))
            return [
              "password"  => "",
              "error_msg" => "Проверка пароля: в конфиге M1 (поле 'github_password') не указан путь к файлу с паролем"
            ];

          // 2.2. Проверить существование файла по адресу $path/password
          if(!$this->storage->exists($path))
            return [
              "password"  => "",
              "error_msg" => "Проверка пароля: не найден файл с паролем по адресу: '".$path."'"
            ];

          // 2.3. Получить содержимое файла $path
          $password = $this->storage->get($path);

          // 2.4. Сформировать запрос
          $request = "curl -u 4gekkman:".$password." https://api.github.com/authorizations";

          // 2.5. Отправить запрос и получить ответ, преобразовав его в массив
          $responce = json_decode(shell_exec($request), true);

          // 2.6. Если в $responce есть поле "message" с сообщением "Bad credentials", завершить
          if(array_key_exists('message', $responce) && $responce['message'] == "Bad credentials")
            return [
              "password"  => $password,
              "error_msg" => "Проверка пароля: неверный пароль"
            ];

          // 2.8. Вернуть результат
          return [
            "password"        => $password,
            "authorizations"  => $responce,
            "error_msg"       => ""
          ];

        });

        // Если пароль не валиден, сообщить
        if(!empty($is_pass_valid['error_msg']))
          throw new \Exception($is_pass_valid['error_msg']);

      // 3. Проверить валидность токена

        // Проверить
        $is_token_valid = call_user_func(function() USE ($is_pass_valid){

          // 3.1. Получить путь к файлу с токеном из конфига M1
          $path = config("M1.github_oauth2");
          if(empty($path))
            return [
              "token"     => "",
              "error_msg" => "Валидация токена: в конфиге M1 (поле 'github_oauth2') не указан путь к файлу с токеном"
            ];

          // 3.2. Проверить существование файла по адресу $path/password
          if(!$this->storage->exists($path))
            return [
              "token"  => "",
              "error_msg" => "Валидация токена: не найден файл с токеном по адресу: '".$path."'"
            ];

          // 3.3. Получить содержимое файла $path
          $token = $this->storage->get($path);

          // 3.4. Получить последние 8 символов токена
          $last_eight = mb_substr($token, -8);

          // 3.5. Пробежаться по авторизациям из $is_pass_valid, и попробовать найти токен
          foreach($is_pass_valid['authorizations'] as $auth) {

            if($last_eight == $auth["token_last_eight"])
              return [
                "token"           => $token,
                "error_msg"       => ""
              ];
          }

          // 3.5. Если курсор дошёл сюда, значет токен не найден
          return [
            "token"           => $token,
            "error_msg"       => "Валидация токена: среди доступных авторизаций токен из файла не найден"
          ];

        });

        // Если токен не валиден, сообщить
        if(!empty($is_token_valid['error_msg']))
          throw new \Exception($is_token_valid['error_msg']);

      // 4. Проверить существование ps-скрипта

        // Проверить
        $is_psscript_exists = call_user_func(function(){

          // 4.1. Получить путь к powershell-скрипту
          $path = config("M1.github_powershell");
          if(empty($path))
            return [
              "psscript"  => "",
              "error_msg" => "Проверка ps-скрипта: в конфиге M1 (поле 'github_powershell') не указан путь к файлу со скриптом"
            ];

          // 4.2. Проверить существование файла по адресу $path/password
          if(!$this->storage->exists($path))
            return [
              "psscript"  => "",
              "error_msg" => "Проверка ps-скрипта: не найден файл с ps-скриптом по адресу: '".$path."'"
            ];

          // 4.3. Получить содержимое файла $path
          $psscript = $this->storage->get($path);

          // 4.4. Вернуть результат
          return [
            "psscript"        => $psscript,
            "error_msg"       => ""
          ];

        });

        // Если ps-скрипт не существует
        if(!empty($is_psscript_exists['error_msg']))
          throw new \Exception($is_psscript_exists['error_msg']);


      // 5. Вернуть результат
      return [
        "status"  => 0,
        "data"    => [
          "errormsg" => "",
          "password" => $is_pass_valid['password'],
          "token"    => $is_token_valid['token']
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C48_github_check from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C48_github_check']);
      return [
        "status"  => -2,
        "data"    => [
          "errormsg" => $errortext,
          "password" => "",
          "token"    => ""
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
