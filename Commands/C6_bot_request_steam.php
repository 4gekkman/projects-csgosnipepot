<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Send request to Steam by url from bots face, simulating a browser
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot      // ID бота, от имени которого осуществлять запрос
 *        method      // Метод запроса
 *        url         // URL, куда посылать запрос
 *        mobile      // Имитировать ли запрос с мобильного устройства
 *        postdata    // Данные для передачи в POST-запросе
 *        ref         // URL реферала
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
  use \GuzzleHttp\Exception\RequestException;

//---------//
// Команда //
//---------//
class C6_bot_request_steam extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Определить, куда сохранять файл с куками для этого бота
     *  4. Отправить запрос от имени бота с id_bot
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------//
    // Отправить запрос по указанному URL, имитируя браузер //
    //------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "method"          => ["required", "in:GET,POST"],
        "url"             => ["required", "url"],
        "cookies_domain"  => ["required", "string"],
        "data"            => ["sometimes", "array"],
        "ref"             => ["sometimes", "url"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception("Can't find bot with ID = ".$this->data['id_bot']);
      if(empty($bot->login))
        throw new \Exception("Login of the bot with ID = ".$this->data['id_bot']." is empty, but required.");

      // 3. Определить, куда сохранять файл с куками для этого бота
      // - Заодно проверить существования соотв.каталога и файла. Если их нет, создать.
      // - Если файл с куками не содержит валидный json, очистить файл.
      $cookie_file_path = call_user_func(function() USE ($bot) {

        // 1] Получить путь к каталогу, куда надо сохранять куки ботов, относительно корня laravel
        $root4cookies = config('M8.root4cookies') ?: 'storage/m8_bots_cookies';

        // 2] Получить имя файла с куками для бота $bot
        $name = $bot->login;

        // 3] Сформировать результат
        $file = $root4cookies . '/' . $name . '.cookies';

        // 4] Получить новый экземпляр $fs
        $fs = r1_fs('');

        // 5] Если $root4cookies не существует, создать такой каталог
        if(!$fs->exists($root4cookies))
          $fs->makeDirectory($root4cookies);

        // 6] Если $file не существует, создать такой файл
        if(!$fs->exists($file))
          $fs->put($file, '');

        // 7] Если в $file не валидный json, пересоздать его
        $validator = r4_validate(['data' => $fs->get($file)], [
          "data"              => ["required", "json"],
        ]); if($validator['status'] == -1) {
          $fs->delete($file);
          $fs->put($file, '');
        }

        // 8] Вернуть результат
        return [
          'fs'          => $fs,
          'fullpath'    => base_path($file),
          'relpath'     => $file,
          'rootfolder'  => $root4cookies
        ];

      });

      // 4. Отправить запрос от имени бота с id_bot

        // 4.1. Подготовить массив с куками для отправки в Steam
        $cookies2send = call_user_func(function() USE ($cookie_file_path) {

          // 1] Извлечь массив с куками бота $bot из его файла с куками
          $cookies = json_decode($cookie_file_path['fs']->get($cookie_file_path['relpath']), true);

          // 2] Если $cookies пуста или не массив, инициировать пустым массивом
          if(empty($cookies) || !is_array($cookies)) $cookies = [];

          // 3] Добавить в $cookies стандартные мобильные куки, если их там ещё нет
          if(!array_key_exists('mobileClientVersion', $cookies))
            array_push($cookies, [
              'Name'      => 'mobileClientVersion',
              'Value'     => '0 (2.1.3)',
              'Domain'    => $this->data['cookies_domain'],
              'Path'      => '/',
              'Max-Age'   => NULL,
              'Expires'   => NULL,
              'Secure'    => false,
              'Discard'   => false,
              'HttpOnly'  => false,
            ]);

          if(!array_key_exists('mobileClient', $cookies))
            array_push($cookies, [
              'Name'      => 'mobileClient',
              'Value'     => 'android',
              'Domain'    => $this->data['cookies_domain'],
              'Path'      => '/',
              'Max-Age'   => NULL,
              'Expires'   => NULL,
              'Secure'    => false,
              'Discard'   => false,
              'HttpOnly'  => false,
            ]);

          if(!array_key_exists('Steam_Language', $cookies))
            array_push($cookies, [
              'Name'      => 'Steam_Language',
              'Value'     => 'english',
              'Domain'    => $this->data['cookies_domain'],
              'Path'      => '/',
              'Max-Age'   => NULL,
              'Expires'   => NULL,
              'Secure'    => false,
              'Discard'   => false,
              'HttpOnly'  => false,
            ]);

          // n] Вернуть результаты
          return $cookies;

        });

        // 4.2. Подготовить экземпляр CookieJar, наполнить его куками из $cookies2send

          // 1] Создать, указав путь к файлу с куками бота $bot
          $cookies = new \GuzzleHttp\Cookie\FileCookieJar($cookie_file_path['fullpath'], true);

          // 2] Наполнить $cookies куками из $cookies2send
          for($i=0; $i<count($cookies2send); $i++) {
            $cookies->setCookie(new \GuzzleHttp\Cookie\SetCookie([
              'Domain'  => $this->data['cookies_domain'],
              'Name'    => $cookies2send[$i]['Name'],
              'Value'   => $cookies2send[$i]['Value'],
              'Discard' => true
            ]));
          }

        // 4.3. Подготовить запрос
        $request = new \GuzzleHttp\Psr7\Request($this->data['method'], $this->data['url']);

        // 4.4. Подготовить заголовки запроса

          // 1] Подготовить
          $request_headers = [
            'Accept'              => '*/*',
            'Content-Type'        => 'application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent'          => 'Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; Google Nexus 4 - 4.1.1 - API 16 - 768x1280 Build/JRO03S) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'X-Requested-With'    => 'com.valvesoftware.android.steam.community'
          ];

          // 2] Если ref передан, добавить заголовок Referer
          if(!empty($this->data['ref']))
            $request_headers['Referer'] = $this->data['ref'];

        // 4.3. Осуществить запрос
        try {
          $response = (new \GuzzleHttp\Client())->send($request, [
            'connect_timeout' => 50.00,
            'headers'         => $request_headers,
            'cookies'         => $cookies,
            'query'           => (array_key_exists('data', $this->data) && is_array($this->data['data']) && $this->data['method'] == "GET") ? $this->data['data'] : [],
            'form_params'     => (array_key_exists('data', $this->data) && is_array($this->data['data']) && $this->data['method'] == "POST") ? $this->data['data'] : [],
          ]);
        } catch (RequestException $e) {
          $response = $e->getResponse();
          $errortext = 'Invoking of command C6_bot_request_steam from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
          Log::info($errortext);
          write2log($errortext, ['M8', 'C6_bot_request_steam']);
          return [
            "status"  => -2,
            "data"    => [
              "errortext"   => $errortext,
              "errormsg"    => $e->getMessage(),
              "response"    => $response
            ]
          ];
        }

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "response" => $response
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C6_bot_request_steam from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C6_bot_request_steam']);
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

