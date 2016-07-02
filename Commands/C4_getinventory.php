<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get inventory by passed steamid
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        "steamid"     // id steam-пользователя
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
class C4_getinventory extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Выполнить HTTP-запрос и получить инвентарь пользователя со steamid
     *  3. Провести валидацию полученных результатов
     *  4. Извлечь json из body в виде массива
     *  5. Провести обработку объектов в rgDescriptions
     *  6. Преобразовать rgDescriptions в массив массивов
     *  7. Добавить в $rgDescriptions цены вещей
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------//
    // Получить инвентарь steam-пользователя по его ID //
    //-------------------------------------------------//
    $res = call_user_func(function() { try {
$t1 = time();
      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "steamid"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Выполнить HTTP-запрос и получить инвентарь пользователя со steamid
      $inventory = call_user_func(function() {

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Создать экземпляр guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 3] Сформировать URL для запроса
        $url = "http://steamcommunity.com/profiles/" .
            $this->data['steamid'] .
            "/inventory/json/730/2";

        // 4] Выполнить запрос
        $request_result = $guzzle->request('GET', $url, []);

        // 5] Наполнить $result
        $result['result'] = $request_result;
        $result['status'] = $request_result->getStatusCode();
        $result['body'] = $request_result->getBody();

        // n] Вернуть результат
        return $result;

      });

      // 3. Провести валидацию полученных результатов
      $validator = r4_validate($inventory, [
        "status"          => ["required", "in:200"],
        "body"            => ["required", "json"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 4. Извлечь json из body в виде массива и провести валидацию

        // 4.1. Извлечь
        $json_decoded = json_decode($inventory['body'], true);

        // 4.2. Убедиться, что success == true
        if(!array_key_exists('success', $json_decoded) || !is_bool($json_decoded['success']) || $json_decoded['success'] !== true)
          throw new \Exception('The inventory is empty, or error (details in log).');

        // 4.3. Провести валидацию
        $validator = r4_validate($json_decoded, [
          "success"         => ["r4_defined", "boolean"],
          "rgInventory"     => ["r4_defined", "array"],
          "rgDescriptions"  => ["r4_defined", "array"],
          "more"            => ["r4_defined", "boolean"],
          "more_start"      => ["r4_defined", "boolean"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

      // 5. Провести обработку объектов в rgDescriptions
      foreach($json_decoded['rgDescriptions'] as $key => &$description) {

        // 5.1. В каждый из объектов в rgDescriptions добавить доп.свойства
        // - Взять их из rgInventory.
        // - Искать соотв.объекты в оном по classid и instanceid.
        // - Интересуют следующие доп.св-ва: assetid, amount
        call_user_func(function() USE (&$description, $json_decoded) {

          // 1] Извлечь classid и instanceid для $description
          $classid    = $description['classid'];
          $instanceid = $description['instanceid'];

          // 2] Получить assetid и amount из соотв.объекта в rgInventory
          foreach($json_decoded['rgInventory'] as $key => &$inv) {

            if($inv['classid'] == $classid && $inv['instanceid'] == $instanceid) {
              $assetid = $inv['id'];
              $amount = $inv['amount'];
            }
            break;

          }

          // 3] Добавить $assetid и $amount в $description
          $description['assetid'] = isset($assetid) ? $assetid : "";
          $description['amount'] = isset($amount) ? $amount : "";

        });

        // 5.2. Вынести важную информацию из тегов на 1-й уровень массива
        foreach($description['tags'] as $tag) {

          // 1] Type
          if($tag['category_name'] == "Type") {
            $description['type'] = $tag['name'];
          }

          // 2] Weapon
          if($tag['category_name'] == "Weapon") {
            $description['weapon'] = $tag['name'];
          }

          // 3] Collection
          if($tag['category_name'] == "Collection") {
            $description['collection'] = $tag['name'];
          }

          // 4] Category
          if($tag['category_name'] == "Category") {
            $description['category'] = $tag['name'];
          }

          // 5] Quality
          if($tag['category_name'] == "Quality") {
            $description['quality'] = $tag['name'];
            $description['quality_color'] = $tag['color'];
          }

          // 6] Exterior
          if($tag['category_name'] == "Exterior") {
            $description['exterior'] = $tag['name'];
          }

        }

        // 5.3. Сформировать полные URL для картинок

          // 1] Получить сервер изображений steam
          $steam_image_server = config('M8.steam_image_server') ?: 'https://steamcommunity-a.akamaihd.net/economy/image/';

          // 2] Сформировать полный URL для icon_url
          $description['icon_url'] = !empty($description['icon_url']) ? $steam_image_server . '/' . $description['icon_url'] : "";

          // 3] Сформировать полный URL для icon_url_large
          $description['icon_url_large'] = !empty($description['icon_url_large']) ? $steam_image_server . '/' . $description['icon_url_large'] : "";

          // 4] Сформировать полный URL для icon_drag_url
          $description['icon_drag_url'] = !empty($description['icon_drag_url']) ? $steam_image_server . '/' . $description['icon_drag_url'] : "";

      }

      // 6. Преобразовать rgDescriptions в массив массивов
      $rgDescriptions = call_user_func(function() USE ($json_decoded) {

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Наполнить $results
        foreach($json_decoded['rgDescriptions'] as $description) {

          array_push($results, $description);

        }

        // n] Вернуть результаты
        return $results;

      });

      // 7. Добавить в $rgDescriptions цены вещей
      call_user_func(function() USE (&$rgDescriptions) {

        // 7.1. Пробежаться по $rgDescriptions и получить массив полей market_hash_name
        $market_hash_names = call_user_func(function() USE ($rgDescriptions) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Наполнить $results
          foreach($rgDescriptions as $item) {
            array_push($results, $item['market_hash_name']);
          }

          // 3] Вернуть результаты
          return $results;

        });

        // 7.2. Для каждого $market_hash_names получить цену
        $prices = call_user_func(function() USE ($market_hash_names) {

          // 1] Выполнить запрос цен
          $result = runcommand('\M8\Commands\C17_get_final_items_prices', ['items' => $market_hash_names]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результат
          return $result['data']['prices'];

        });

        // 7.3. Добавить в $rgDescriptions доп.поля
        foreach($rgDescriptions as &$item) {
          $item['price']          = $prices[$item['market_hash_name']]['price'];
          $item['price_success']  = $prices[$item['market_hash_name']]['success'];
        }

      });

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "rgDescriptions"  => $rgDescriptions,
          "inventory_count" => count($rgDescriptions)
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C4_getinventory from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C4_getinventory']);
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

