<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Invoke OAuth bot login in Steam for specified bot.
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot        // ID бота в локальной системе, от имени которого надо авторизоваться в Steam
 *        mobile        // 0/1, имитировать ли мобильное устройство при обращении к Steam, или нет
 *        relogin       // 0/1, Нужно ли осуществить переавторизацию бота, даже если он авторизован?
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

  namespace M8\Commands;

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
class C8_bot_login extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих параметров
     *  2. Попробовать найти бота с id_bot
     *  3. Проверить, вошёл ли уже $bot в Steam, или нет
     *  4. Если $bot уже вошёл в Steam, завершить
     *  5. Запросить публичный RSA-ключ для бота
     *  6. Зашифровать пароль $bot'а с помощью $rsa по определённому алгоритму
     *  7. Получить для $bot код мобильной аутентификации
     *  8. Запросить в steam OAuth-авторизацию для $bot
     *  9. Обработать ошибки авторизации для $authorization
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------//
    // Осуществить OAuth авторизацию указанного бота в Steam //
    //-------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "relogin"         => ["required", "regex:/^[01]{1}$/ui"]
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception("Can't find bot with ID = ".$this->data['id_bot']);
      if(empty($bot->login))
        throw new \Exception("Login of the bot with ID = ".$this->data['id_bot']." is empty, but required.");
      if(empty($bot->steamid))
        throw new \Exception("Steam ID of the bot with ID = ".$this->data['id_bot']." is empty, but required.");

      // 3. Проверить, вошёл ли уже $bot в Steam, или нет
      $is_bot_authorized = call_user_func(function() USE ($bot) {

        // 3.1. Выполнить запрос
        $result = runcommand('\M8\Commands\C7_bot_get_sessid_steamid', [
          'id_bot'          => $this->data['id_bot'],
          'method'          => 'GET',
          'cookies_domain'  => 'steamcommunity.com'
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 3.2. Вернуть результат
        return $result['data']['is_bot_authenticated'];

      });

      // 4. Если $bot уже вошёл в Steam, и relogin = false, завершить
      if($is_bot_authorized && $this->data['relogin'] === '0') {

        return [
          "status"  => 0,
          "data"    => [
            'was_bot_authorized'  => $is_bot_authorized,
            'id_bot'              => $this->data['id_bot'],
            'relogin'             => $this->data['relogin'],
          ]
        ];

      }

      // 5. Запросить публичный RSA-ключ для бота

        // 5.1. Запросить
        $rsa_response = call_user_func(function() USE ($bot) {

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $this->data['id_bot'],
            "method"          => "GET",
            "url"             => "https://steamcommunity.com/login/getrsakey",
            "cookies_domain"  => $this->data['cookies_domain'],
            "data"            => [
              'username'      => $bot->login
            ],
            "ref"             => ""
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });
        $rsa = json_decode($rsa_response->getBody(), true);

        // 5.2. Если неудача, завершить
        if(!$rsa['success'])
          throw new \Exception("Can't get public RSA from Steam for bot with ID = ".$this->data['id_bot']);

      // 6. Зашифровать пароль $bot'а с помощью $rsa по определённому алгоритму
      $password_encrypted = call_user_func(function() USE ($bot, $rsa) {

        // 6.1. Создать новый экземпляр RSA
        $rsa_instance = new \phpseclib\Crypt\RSA();

        // 6.2. Установить режим шифрования
        $rsa_instance->setEncryptionMode(\phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);

        // 6.3. Подготовить ключ
        $key = [
            'modulus' => new \phpseclib\Math\BigInteger($rsa['publickey_mod'], 16),
            'publicExponent' => new \phpseclib\Math\BigInteger($rsa['publickey_exp'], 16)
        ];

        // 6.4. Добавить $key
        $rsa_instance->loadKey($key, \phpseclib\Crypt\RSA::PUBLIC_FORMAT_RAW);

        // 6.5. Получить зашифрованный пароль
        $password = base64_encode($rsa_instance->encrypt($bot->password));

        // 6.6. Вернуть результат
        return $password;

      });

      // 7. Получить для $bot код мобильной аутентификации
      $code = call_user_func(function() USE ($bot) {

        // 7.1. Получить текущий код аутентификации
        $result = runcommand('\M8\Commands\C5_bot_get_mobile_code', [
          'id_bot'  => $this->data['id_bot'],
          'time'    => time()
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 2] Вернуть результаты (guzzle response)
        return $result['data']['code'];

      });

      // 8. Запросить в steam OAuth-авторизацию для $bot
      $authorization = call_user_func(function() USE ($bot, $password_encrypted, $rsa, $code) {

        // 8.1. Подготовить массив для параметров запроса
        // - И добавить туда общие параметры
        $params = [
          'username'          => $bot->login,
          'password'          => $password_encrypted,
          'twofactorcode'     => $code,
          'captchagid'        => $this->data['captchagid'],
          'captcha_text'      => $this->data['captcha_text'],
          'emailsteamid'      => $bot->steamid.'',
          'emailauth'         => '',
          'rsatimestamp'      => $rsa['timestamp'],
          'remember_login'    => 'false',
          'oauth_client_id'   => 'DE45CD61',
          'oauth_scope'       => 'read_profile write_profile read_client write_client',
          'loginfriendlyname' => '#login_emailauth_friendlyname_mobile'
        ];

        // 8.2. Осуществить POST-запрос к steam и получить OAuth-авторизацию для бота
        $response = call_user_func(function() USE($params) {

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $this->data['id_bot'],
            "url"             => "https://steamcommunity.com/login/dologin",
            "method"          => "POST",
            "cookies_domain"  => $this->data['cookies_domain'],
            "data"            => $params,
            "ref"             => ""
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });

        // 8.4. Расшифровать json-строку с ответом
        $json = json_decode($response->getBody(), true);

        // 8.5. Получить код ошибки
        $error_code = call_user_func(function() USE ($json) {

          // 1] Если $json пуста
          if(empty($json))
            return 1;

          // 2] Если требуется капча
          if(isset($json['captcha_needed']) && $json['captcha_needed'])
            return 5;

          // 3] Если код мобильной аутентификации не подходит
          if(isset($json['requires_twofactor']) && $json['requires_twofactor'] && !$json['success'])
            return 3;

          // 4] Если неправильные логин/пароль
          if(isset($json['login_complete']) && !$json['login_complete'])
            return 4;

          // 5] Если success == false
          if(!array_key_exists('success', $json) || $json['success'] == false)
            return 2;

          // n] Ошибок нет, вернуть 0
          return 0;

        });

        // 8.6. Вернуть результат
        return [
          'error_code'    => $error_code,
          'authorization' => $json
        ];

      });

      // 9. Обработать ошибки авторизации для $authorization
      switch($authorization['error_code']) {
        case 0: break;
        case 1: throw new \Exception("OAuth authorization failed: recieved from Steam json is empty."); break;
        case 2: throw new \Exception("OAuth authorization failed: captcha needed."); break;
        case 3: throw new \Exception("OAuth authorization failed: 2FA code not fits."); break;
        case 4: throw new \Exception("OAuth authorization failed: wrong login or password."); break;
        case 5: throw new \Exception("OAuth authorization failed: somehow in response success = false."); break;
      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C8_bot_login from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C8_bot_login']);
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

