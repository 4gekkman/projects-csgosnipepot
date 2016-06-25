<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Gets prices from Steam Market and updates local DB of items and prices
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
class C15_update_prices_steammarket extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Обновить данные в некоторых таблицах-списках в БД, используя данные из конфига
     *  2. Выполнить HTTP-запрос к Steam Market и получить total_count по CS:GO
     *  3. Выполнить HTTP-запрос к Steam Market и получить HTML-код со всеми ценами по CS:GO
     *  4. Распарсить полученный $html и получить массив в стиле csgofast
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------------------//
    // Извлечь цены на вещи CS:GO из Steam Market, и обновить локальную базу цен //
    //---------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Обновить данные в некоторых таблицах-списках в БД, используя данные из конфига
      $result = runcommand('\M8\Commands\C13_update_db_lists');
      if($result['status'] != 0) {
        throw new \Exception($result['data']['errormsg']);
      }

      // 2. Выполнить HTTP-запрос к Steam Market и получить total_count по CS:GO
      $total_count = call_user_func(function(){

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Создать экземпляр guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 3] Сформировать URL для запроса
        $url = "http://steamcommunity.com/market/search/render?query=appid:730";

        // 4] Выполнить запрос
        $request_result = $guzzle->request('GET', $url, []);

        // 5] Наполнить $result
        $result['result'] = $request_result;
        $result['status'] = $request_result->getStatusCode();
        $result['body'] = $request_result->getBody();

        // 6] Провести валидацию body и status
        $validator = r4_validate($result, [
          "body"              => ["required", "json"],
          "status"            => ["required", "in:200"],
        ]); if($validator['status'] == -1) {

          // 1] Записать сведения об ошибке в MD6_price_update_bugs
          $model = \M8\Models\MD6_price_update_bugs::find(1);
          $model->steammarket_last_update = (string) \Carbon\Carbon::now();
          $model->steammarket_last_bug = $validator['data'];
          $model->save();

          // 2] Возбудить исключение
          throw new \Exception($validator['data']);

        }

        // 7] Преобразовать body из json в массив
        $result['body_array'] = json_decode($result['body'], true);

        // 8] Провести валидацию body_array
        $validator = r4_validate($result['body_array'], [
          "success"              => ["required", "r4_true"],
          "total_count"          => ["required", "r4_numpos"],
        ]); if($validator['status'] == -1) {

          // 1] Записать сведения об ошибке в MD6_price_update_bugs
          $model = \M8\Models\MD6_price_update_bugs::find(1);
          $model->steammarket_last_update = (string) \Carbon\Carbon::now();
          $model->steammarket_last_bug = $validator['data'];
          $model->save();

          // 2] Возбудить исключение
          throw new \Exception($validator['data']);

        }

        // 9] Вернуть total_count
        return $result['body_array']['total_count'];

      });

      // 3. Выполнить HTTP-запросы к Steam Market и получить HTML-код со всеми ценами по CS:GO
      // - Ответ ограничен лишь 100 элементами, поэтому потребуется делать много запросов.
      // - Причём отсчёт начинается с нуля (start=0), такой запрос вернёт эл-ты от 0 до 99.
      // - Например, если total count = 5000, то потребуется сделать 5000/100 + 1 = 51 запрос.
      $steammarket_data_html = call_user_func(function() USE ($total_count) {

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Создать экземпляр guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 3] Сформировать URL для запроса
        $url = "http://steamcommunity.com/market/search/render/?query=appid:730&start=0&count=".$total_count."%20";

        // 4] Выполнить запрос
        $request_result = $guzzle->request('GET', $url, []);

        // 5] Наполнить $result
        $result['result'] = $request_result;
        $result['status'] = $request_result->getStatusCode();
        $result['body'] = $request_result->getBody();

        // 6] Провести валидацию body и status
        $validator = r4_validate($result, [
          "body"              => ["required", "json"],
          "status"            => ["required", "in:200"],
        ]); if($validator['status'] == -1) {

          // 1] Записать сведения об ошибке в MD6_price_update_bugs
          $model = \M8\Models\MD6_price_update_bugs::find(1);
          $model->steammarket_last_update = (string) \Carbon\Carbon::now();
          $model->steammarket_last_bug = $validator['data'];
          $model->save();

          // 2] Возбудить исключение
          throw new \Exception($validator['data']);

        }

        // 7] Преобразовать body из json в массив
        $result['body_array'] = json_decode($result['body'], true);

        // 8] Провести валидацию body_array
        $validator = r4_validate($result['body_array'], [
          "success"              => ["required", "r4_true"],
          "results_html"         => ["required"],
        ]); if($validator['status'] == -1) {

          // 1] Записать сведения об ошибке в MD6_price_update_bugs
          $model = \M8\Models\MD6_price_update_bugs::find(1);
          $model->steammarket_last_update = (string) \Carbon\Carbon::now();
          $model->steammarket_last_bug = $validator['data'];
          $model->save();

          // 2] Возбудить исключение
          throw new \Exception($validator['data']);

        }

        // n] Вернуть результат
        return $result['body_array']['results_html'];

      });

      // 4. Распарсить полученный $html и получить массив в стиле csgofast
      $steammarket_data = call_user_func(function() USE ($steammarket_data_html) {

        // 1] Создать новый объект класса DOMDocument
        $doc = new \DOMDocument();

        // 2] Загрузить в него $steammarket_data_html
        $doc->loadHTML($steammarket_data_html);

        // 3] Создать новый объект класса DOMXPath
        $xpath = new \DOMXpath($doc);

        // 4] Извлечь с помощью $xpath все необходимые данные
        $data = [

          // 4.1] Получить все эл-ты span с классом market_listing_item_name
          "names_nodes" => $xpath->query('//span[@class="market_listing_item_name"]'),

          // 4.2] Получить все эл-ты span с классом normal_price
          "normal_prices_nodes" => $xpath->query('//span[@class="normal_price"]'),

          // 4.3] Получить все эл-ты span с классом market_listing_row_link (ссылки на листинг вещей на Steam)
          "links_nodes" => $xpath->query('//a[@class="market_listing_row_link"]/@href'),

          // 4.4] Получить все эл-ты span с классом market_listing_num_listings_qty (сколько подобных вещей сейчас продаётся на рынке)
          "qty_nodes" => $xpath->query('//span[@class="market_listing_num_listings_qty"]'),

          // 4.5] Получить все эл-ты span с классом market_listing_row_link (это ссылки на листинг вещей на Steam)
          "images_nodes" => $xpath->query('//img/@srcset'),

        ];

        // 5] Проверить, что всё сходится

//        write2log($data['names_nodes']->length, []);
//        write2log($data['normal_prices_nodes']->length, []);
//        write2log($data['links_nodes']->length, []);
//        write2log($data['qty_nodes']->length, []);
//        write2log($data['images_nodes']->length, []);
//
//
//$x = 0;
//        foreach($data['names_nodes'] as $node) {
//          $x++;
//        }
//write2log($x, []);



//        foreach($names_span_nodes as $node) {
//          write2log($node->nodeValue, []);
//        }


      });





//      $csgofast_data = call_user_func(function() {
//
//        // 1] Подготовить массив для результата
//        $result = [];
//
//        // 2] Создать экземпляр guzzle
//        $guzzle = new \GuzzleHttp\Client();
//
//        // 3] Сформировать URL для запроса
//        $url = "https://api.csgofast.com/price/all";
//
//        // 4] Выполнить запрос
//        $request_result = $guzzle->request('GET', $url, []);
//
//        // 5] Наполнить $result
//        $result['result'] = $request_result;
//        $result['status'] = $request_result->getStatusCode();
//        $result['body'] = $request_result->getBody();
//
//        // n] Вернуть результат
//        return $result;
//
//      });




    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C15_update_prices_steammarket from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C15_update_prices_steammarket']);
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

