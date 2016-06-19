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
     *
     *
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
        "url"             => ["required", "url"],
        "mobile"          => ["required", "boolean"],
        "postdata"        => ["sometimes", "array"],
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
      $cookie_file_path = call_user_func(function() USE ($bot) {

        // 1] Получить путь к каталогу, куда надо сохранять куки ботов, относительно корня laravel
        $root4cookies = config('M8.root4cookies') ?: 'storage/m8_bots_cookies';

        // 2] Получить имя файла с куками для бота $bot
        $name = $this->data['mobile'] == false ? $bot->login : $bot->login . "_auth";

        // 3] Сформировать результат
        $file = $root4cookies . '/' . $name . '.cookiefile';

        // 4] Получить новый экземпляр $fs
        $fs = r1_fs('');

        // 5] Если $root4cookies не существует, создать такой каталог
        if(!$fs->exists($root4cookies))
          $fs->makeDirectory($root4cookies);

        // 6] Если $file не существует, создать такой файл
        if(!$fs->exists($file))
          $fs->put($file, '');

        // 7] Вернуть результат
        return base_path($file);

      });

      // 4. Отправить запрос от имени бота с id_bot

        // 4.1. Подготовить экземпляр клиента Guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 4.2. Подготовить массив с опциями для curl
        $curl_options = call_user_func(function() USE ($cookie_file_path) {

          // 1] Подготовить массив для результата
          $result = [];

          // 2] Наполнить $result

            // 2.1] URL для запроса
            $result[CURLOPT_URL] = $this->data['url'];

            // 2.2] Пусть cURL вернёт данные в виде строки, а не выведет их в браузер
            $result[CURLOPT_RETURNTRANSFER] = true;

            // 2.3] Пусть cURL не проверяет сертификат узла сети
            $result[CURLOPT_SSL_VERIFYPEER] = false;

            // 2.4] Пусть cURL не проверяет имя хоста по сертификату
            $result[CURLOPT_SSL_VERIFYHOST] = 0;

            // 2.5] Пусть cURL сохраняет файлы-куки для этого бота по указанному адресу

              // Имя файла, содержащего cookies. Данный файл должен быть в формате Netscape или просто заголовками HTTP, записанными в файл. Если в качестве имени файла передана пустая строка, то cookies сохраняться не будут, но их обработка все еще будет включена
              $result[CURLOPT_COOKIEFILE] = $cookie_file_path;

              // Имя файла, в котором будут сохранены все внутренние cookies текущей передачи после закрытия дескриптора, например, после вызова curl_close
              $result[CURLOPT_COOKIEJAR] = $cookie_file_path;

            // 2.6] Пусть cURL не следует заголовкам "Location: ", которые посылает сервер
            $result[CURLOPT_FOLLOWLOCATION] = 1;

            // 2.7] Установить timeout ожидания соединения, в секундах
            $result[CURLOPT_CONNECTTIMEOUT] = 50;

            // 2.8] Задать, от мобильного или нет устройства происходит запрос

              // Если передана mobile, и она true, значит от мобильного
              // - Задать соотв.поля
              if(array_key_exists('mobile', $this->data) && $this->data['mobile'] == true) {

                // Массив устанавливаемых HTTP-заголовков, в формате array('Content-type: text/plain', 'Content-length: 100')
                $result[CURLOPT_HTTPHEADER] = [
                  "X-Requested-With: com.valvesoftware.android.steam.community"
                ];

                // Содержимое заголовка "User-Agent: ", посылаемого в HTTP-запросе
                $result[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; Google Nexus 4 - 4.1.1 - API 16 - 768x1280 Build/JRO03S) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';

                // Содержимое заголовка "Cookie: ", используемого в HTTP-запросе. Обратите внимание, что несколько cookies разделяются точкой с запятой с последующим пробелом (например, "fruit=apple; colour=red")
                $result[CURLOPT_COOKIE] = call_user_func(function(){

                  // Мобильные куки по умолчанию
                  $cookie = ['mobileClientVersion' => '0 (2.1.3)', 'mobileClient' => 'android', 'Steam_Language' => 'english', 'dob' => ''];

                  // Сформировать результат
                  $out = "";
                  foreach ($cookie as $k => $c) {
                      $out .= "{$k}={$c}; ";
                  }

                  // Вернуть результат
                  return $out;

                });
              }

              // В противном случае не от мобильного
              // - Задать соотв.поля
              else {

                $result[CURLOPT_HTTPHEADER] = [
                  "X-Requested-With: com.valvesoftware.android.steam.community"
                ];

                // Содержимое заголовка "User-Agent: ", посылаемого в HTTP-запросе
                $result[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0';

              }

            // 2.9] Если передан ref url, задать его
            if(array_key_exists('ref', $this->data))
              $result[CURLOPT_REFERER] = $this->data['ref'];

            // 2.10] Если передана postdata, задать соотв.поля
            if(array_key_exists('postdata', $this->data)) {

              // Указать, что методом запроса будет POST
              $result[CURLOPT_POST] = true;

              // Сформировать строку с post-данными
              $poststring = call_user_func(function(){
                $result = "";
                foreach($this->data['postdata'] as $key => $value) {
                  if($result)
                    $result .= "&";
                  $result .= $key . "=" . $value;
                }
                return $result;
              });

              // Добавить $poststring в параметры cURL-запроса
              $result[CURLOPT_POSTFIELDS] = $poststring;

            }

          // n] Вернуть результат
          return $result;

        });

        // 4.3. Определить метод запроса (GET / POST)
        $method = call_user_func(function(){
          if(array_key_exists('postdata', $this->data)) return "POST";
          return "GET";
        });

        // 4.4. Отправить запрос, указав его параметры
        $response = $guzzle->request($method, '/', [
          'curl' => $curl_options
        ]);

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

