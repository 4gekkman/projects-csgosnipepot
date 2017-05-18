<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get steam name and steam ID of a trade partner by their trade url
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
class C30_get_steamname_and_steamid_by_tradeurl extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти модель бота с id_bot
     *  3. Осуществить GET-запрос к steam по trade url и получить HTML-документ в ответ
     *  4. Извлечь из $html все необходимые данные
     *  5. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------------//
    // Получить Steam name и Steam ID торгового партнёра по его торговому URL //
    //------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "partner"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "token"           => ["required", "string"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти модель бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception('Не удалось найти бота с ID = '.$this->data['id_bot']);

      // 3. Осуществить GET-запрос к steam по trade url и получить HTML-документ в ответ

        // 3.1. Осуществить запрос
        $response = call_user_func(function() USE ($bot) {

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $bot->id,
            "method"          => "GET",
            "url"             => "https://steamcommunity.com/tradeoffer/new",
            "cookies_domain"  => 'steamcommunity.com',
            "data"            => [
              "partner" => $this->data['partner'],
              "token"   => $this->data['token'],
            ],
            "ref"             => ""
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });

        // 3.2. Если код ответа не 200, сообщить и завершить
        if($response->getStatusCode() != 200)
          throw new \Exception('Unexpected response from Steam: code '.$response->getStatusCode());

        // 3.3. Получить из $response строку с HTML из ответа
        $html = (string) $response->getBody();

      // 4. Извлечь из $html все необходимые данные
      // - Результирующий массив должен выглядеть так:
      //
      //    [
      //      steamid                 // Steam ID партнёра
      //      steam_name              // Steam name партнёра
      //      escrow_days_my          // Escrow hold мой
      //      escrow_days_partner     // Escrow hold партнёра
      //    ]
      //
      Log::info($html);
      $needed_data = call_user_func(function() USE ($html) {

        // 4.1. Подготовить массив для результатов
        $results = [];

        // 4.2. Steam ID партнёра (g_ulTradePartnerSteamID)
        $pattern = '/g_ulTradePartnerSteamID = (.*);/';
        preg_match($pattern, $html, $matches);
        if(!isset($matches[1]))
          throw new \Exception('Unexpected response from Steam.');
        $steamid = str_replace("'", '', $matches[1]);
        if($steamid == 'false') {
          $steamid = 0;
        }
        $results['steamid'] = $steamid;

        // 4.3. Steam name партнёра (g_strTradePartnerPersonaName)
        $pattern = '/g_strTradePartnerPersonaName = (.*);/';
        preg_match($pattern, $html, $matches);
        if(!isset($matches[1]))
          throw new \Exception('Unexpected response from Steam.');
        $steam_name = str_replace('"', '', $matches[1]);
        $results['steam_name'] = $steam_name;

        // 4.4. Escrow hold мой (g_daysMyEscrow)
        $pattern = '/g_daysMyEscrow = (.*);/';
        preg_match($pattern, $html, $matches);
        if(!isset($matches[1]))
          throw new \Exception('Не удалось найти g_daysMyEscrow в ответном HTML.');
        $g_daysMyEscrow = str_replace('"', '', $matches[1]);
        $results['escrow_days_my'] = $g_daysMyEscrow;

        // 4.5. Escrow hold партнёра (g_daysTheirEscrow)
        $pattern = '/g_daysTheirEscrow = (.*);/';
        preg_match($pattern, $html, $matches);
        if(!isset($matches[1]))
          throw new \Exception('Не удалось найти g_daysTheirEscrow в ответном HTML.');
        $g_daysTheirEscrow = str_replace('"', '', $matches[1]);
        $results['escrow_days_partner'] = $g_daysTheirEscrow;

        // 4.6. Аватар партнёра
        $avatar = call_user_func(function() USE ($html) {

          // 1] Создать новые объекты DOMDocument и DOMXpath, загрузить в них $html
          libxml_use_internal_errors(true);
          $doc = new \DOMDocument();
          $doc->loadHTML($html);
          $xpath = new \DOMXPath($doc);
          libxml_use_internal_errors(false);

          // 2] Получить URL аватара партнёра
          $avatar = call_user_func(function() USE ($xpath) {

            // 2.1] Найти аватар в $html
            $ava = $xpath->query('//div[@class="avatarIcon"]/descendant::a/img/@src');

            // 2.2] Если $ava пуст, вернуть пустую строку
            if($ava->length == 0) return '';

            // 3.3] Иначе, вернуть URL аватара
            return $ava[0]->nodeValue;

          });

          // 3] Добавить "_full" в конце к имени аватара
          $full_avatar = call_user_func(function() USE ($avatar) {

            // 3.1] Если $avatar пуст, ничего не делать
            if(empty($avatar)) return "";

            // 3.2] Извлечь расширение
            preg_match("#\.[^\/]+$#ui", $avatar, $matches);
            $ext = $matches[0];

            // 3.3] Удалить из $avatar расширение
            $avatar = preg_replace("#\.[^\/]+$#ui", '', $avatar);

            // 3.4] Добавить в конец $avatar строку '_full', и затем $ext
            $avatar = $avatar . '_full' . $ext;

            // 3.5] Вернуть результат
            return $avatar;

          });

          // 4] Вернуть URL аватара
          return $full_avatar;

        });
        $results['avatar'] = $avatar;

        // 4.n. Провести валидацию полученных результатов
        $validator = r4_validate($results, [
          "steamid"               => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "steam_name"            => ["required", "string"],
          "escrow_days_my"        => ["required", "regex:/^[0-9]+$/ui"],
          "escrow_days_partner"   => ["required", "regex:/^[0-9]+$/ui"],
          "avatar"                => ["required", "url"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 4.m. Вернуть результаты
        return $results;

      });

      // 5. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "partner"               => $this->data['partner'],
          "token"                 => $this->data['token'],
          "trade_url"             => "https://steamcommunity.com/tradeoffer/new?partner=".$this->data['partner']."&token=".$this->data['token'],
          "steamid_partner"       => $needed_data['steamid'],
          "steam_name_partner"    => $needed_data['steam_name'],
          "steamescrow_days_my"   => $needed_data['escrow_days_my'],
          "escrow_days_partner"   => $needed_data['escrow_days_partner'],
          "avatar"                => $needed_data['avatar']
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C30_get_steamname_and_steamid_by_tradeurl from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C30_get_steamname_and_steamid_by_tradeurl']);
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

