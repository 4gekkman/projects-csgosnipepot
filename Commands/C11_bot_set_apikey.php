<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Gets and sets apikey for the specified authorized in Steam bot.
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        "id_bot",
 *        "force",
 *        "domain"
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
class C11_bot_set_apikey extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  4. Если domain бота пуст, назначить ему domain из параметров
     *  5. Если поле apikey у $bot пустое, или если force = 1, обновить API-ключ $bot'а
     *  6. Вернуть результат
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------------//
    // Получить и добавить API-ключ указанному боту, который д.б. авторизован в Steam //
    //--------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "force"               => ["required", "regex:/^[01]{1}$/ui"],
        "domain"              => ["required", "string"]
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception("Can't find bot with ID = ".$this->data['id_bot']);

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
        return $result['data'];

      });
      if(!$is_bot_authorized['is_bot_authenticated'])
        throw new \Exception("The bot with ID = ".$this->data['id_bot']." not logged in Steam.");

      // 4. Если domain бота пуст, назначить ему domain из параметров
      if(empty($bot->apikey_domain)) {
        $bot_got_domain = 1;
        $bot->apikey_domain = $this->data['domain'];
        $bot->save();
      }

      // 5. Если поле apikey у $bot пустое, или если force = 1, обновить API-ключ $bot'а
      if(empty($bot->apikey) || $this->data['force'] == 1) {

        // 5.1. Подготовить функцию для извлечения API-ключа пользователя
        // - Если надо, она также регистрирует пользователю новый ключ.
        $bot_get_apikey = function($recursionLevel = 1) USE ($bot, $is_bot_authorized, &$bot_get_apikey) {

          // 5.1.1. Запросить от имени бота HTML страницы с API-ключём
          $apikey_html_response = call_user_func(function() USE ($bot) {

            // 1] Осуществить запрос
            $result = runcommand('\M8\Commands\C6_bot_request_steam', [
              "id_bot"          => $bot->id,
              "method"          => "GET",
              "url"             => "https://steamcommunity.com/dev/apikey",
              "cookies_domain"  => 'steamcommunity.com',
              "data"            => [],
              "ref"             => ""
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

            // 2] Вернуть результаты (guzzle response)
            return $result['data']['response'];

          });

          // 5.1.2. Извлечь строку с HTML из запроса, key и domain
          $apikey_html = (string) $apikey_html_response->getBody();
          $key = call_user_func(function() USE ($apikey_html) {
            $result = preg_match('/<p>Key: (.*)<\/p>/', $apikey_html, $matches);
            if($result) return $matches[1];
            return '';
          });
          $domain = call_user_func(function() USE ($apikey_html) {
            $result = preg_match('/<p>Domain Name: (.*)<\/p>/', $apikey_html, $matches);
            if($result) return $matches[1];
            return '';
          });

          // 5.1.3. Если по какой-то причине доступ боту ограничен, apikey = ''
          if(preg_match('/<h2>Access Denied<\/h2>/', $apikey_html)) {

            // 1] Сохранить apikey в модель $bot
            $bot->apiKey = '';
            $bot->save();

            // 2] Вернуть результат
            return [
              'apikey' => '',
              'error'  => 1
            ];

          }

          // 5.1.4. Если доступ есть, API-ключ получен, и домен совпадает с доменом бота
          else if(!empty($key) && !empty($domain) && $domain == $bot->apikey_domain) {

            // 1] Сохранить apikey в модель $bot
            $bot->apiKey = $key;
            $bot->save();

            // 2] Вернуть результат
            return [
              'apikey' => $key,
              'error'  => 0
            ];

          }

          // 5.1.5. Если доступ есть, но API-ключ не получен, или домен не совпадает с доменом бота
          else if ($recursionLevel < 3 || empty($key) || empty($domain) || $domain != $bot->apikey_domain) {

            // 1] Зарегистрировать для $bot новый API-ключ
            $result = runcommand('\M8\Commands\C6_bot_request_steam', [
              "id_bot"          => $bot->id,
              "method"          => "POST",
              "url"             => "https://steamcommunity.com/dev/registerkey",
              "cookies_domain"  => 'steamcommunity.com',
              "data"            => [
                'domain'        => $bot->apikey_domain,
                'agreeToTerms'  => 'agreed',
                'sessionid'     => $is_bot_authorized['sessionid'],
                'Submit'        => 'Register'
              ],
              "ref"             => ""
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

            // 2] Прибавить 1 к $recursionLevel
            $recursionLevel++;

            // 3] Рекурсивно выполнить функцию $bot_get_apikey
            // - И вернуть результат.
            return [
              'apikey' => $bot_get_apikey($recursionLevel)['apikey'],
              'error'  => 2
            ];

          }

        };

        // 5.2. Вызвать функцию $bot_get_apikey
        $bot_get_apikey_result = $bot_get_apikey();

      }

      // 6. Вернуть результат
      DB::commit();
      return [
        "status"  => 0,
        "data"    => [
          "id_bot"          => $this->data['id_bot'],
          "bot_got_domain"  => !empty($bot_got_domain) ? 1 : 0,
          "authorized"      => $is_bot_authorized['is_bot_authenticated'] ? 1 : 0,
          "domain"          => $bot->apikey_domain,
          "apikey"          => !empty($bot_get_apikey_result) ? $bot_get_apikey_result['apikey'] : $bot->apikey,
          "access_denied"   => !empty($bot_get_apikey_result) && $bot_get_apikey_result['error'] == 1 ? 1 : 0,
          "isnew"           => !empty($bot_get_apikey_result) && $bot_get_apikey_result['error'] == 2 ? 1 : 0,
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C11_bot_set_apikey from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C11_bot_set_apikey']);
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

