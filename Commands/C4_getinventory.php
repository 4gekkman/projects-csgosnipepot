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
 *        "steamid"               // id steam-пользователя
 *        "force"                 // [не обязателен] принудительное обновление кэша (==false по умолчанию)
 *        "filter_by_room_id"     // [не обязателен] фильтровать по типам предметов указанной комнаты
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
     *  1. Провести валидацию входящих параметров, назначить значения по умолчанию
     *  2. Получить обработанный и подготовленный инвентарь
     *    2.1. Если force == false и кэш существует, взять его из кэш
     *    2.2. В противном случае, запросить инвентарь из Steam
     *      2.2.1. Выполнить HTTP-запрос и получить инвентарь пользователя со steamid
     *      2.2.2. Провести валидацию полученных результатов
     *      2.2.3. Извлечь json из body в виде массива
     *      2.2.4. Подготовить и наполнить массив items
     *      2.2.5. Вернуть $items
     *  3. Закэшировать все вещи
     *  4. Если filter_by_room_id не пуста, провести фильтрацию $items
     *  5. Отфильтровать из $items все вещи без цены
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------//
    // Получить инвентарь steam-пользователя по его ID //
    //-------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров, назначить значения по умолчанию

        // Провести валидацию
        $validator = r4_validate($this->data, [
          "steamid"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "force"                => ["boolean"],
          "filter_by_room_id"    => ["regex:/^[1-9]+[0-9]*$/ui"]
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // Назначить значения по умолчанию

          // force
          if(!array_key_exists("force", $this->data))
            $this->data['force'] = false;

          // filter_by_room_id
          if(!array_key_exists("filter_by_room_id", $this->data))
            $this->data['filter_by_room_id'] = "";

      // 2. Получить обработанный и подготовленный инвентарь
      $items = call_user_func(function(){

        // 2.1. Если force == false и кэш существует, взять его из кэш
        $cache = json_decode(Cache::get("inventory:".$this->data['steamid']), true);
        if($this->data['force'] == false && !empty($cache)) {
          return $cache['rgDescriptions'];
        }

        // 2.2. В противном случае, запросить инвентарь из Steam
        else {

          // 2.2.1. Выполнить HTTP-запрос и получить инвентарь пользователя со steamid
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
            $request_result = $guzzle->request('GET', $url);

            // 5] Наполнить $result
            $result['result'] = $request_result;
            $result['status'] = $request_result->getStatusCode();
            $result['body'] = $request_result->getBody();

            // n] Вернуть результат
            return $result;

          });

          // 2.2.2. Провести валидацию полученных результатов
          $validator = r4_validate($inventory, [
            "status"          => ["required", "in:200"],
            "body"            => ["required", "json"],
          ]); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 2.2.3. Извлечь json из body в виде массива и провести валидацию

            // 1] Извлечь
            $json_decoded = json_decode($inventory['body'], true);

            // 2] Убедиться, что success == true
            if(!array_key_exists('success', $json_decoded) || !is_bool($json_decoded['success']) || $json_decoded['success'] !== true)
              throw new \Exception('The inventory is empty, or error (details in log).');

            // 3] Провести валидацию
            $validator = r4_validate($json_decoded, [
              "success"         => ["r4_defined", "boolean"],
              "rgInventory"     => ["r4_defined", "array"],
              "rgDescriptions"  => ["r4_defined", "array"],
              "more"            => ["r4_defined", "boolean"],
              "more_start"      => ["r4_defined", "boolean"],
            ]); if($validator['status'] == -1) {
              throw new \Exception($validator['data']);
            }

          // 2.2.4. Подготовить и наполнить массив items
          $items = call_user_func(function() USE ($json_decoded) {

            // 1] Подготовить массив для результатов
            $items = [];

            // 2] Наполнить items данными из rgInventory
            foreach($json_decoded['rgInventory'] as $item) {
              array_push($items, [
                "assetid" => isset($item['id']) ? $item['id'] : "",
                "classid" => isset($item['classid']) ? $item['classid'] : "",
                "instanceid" => isset($item['instanceid']) ? $item['instanceid'] : ""
              ]);
            }

            // 3] Наполнить items данными из rgDescriptions
            foreach($items as &$item) {

              // 3.1] Получить ключ по шаблону "<classid>_<instanceid>" для $item
              $key = $item['classid'] . '_' . $item['instanceid'];

              // 3.2] По этому ключу получить из rgDescriptions описание для $item
              $desc = $json_decoded['rgDescriptions'][$key];

              // 3.3] Записать все св-ва из $desc в $item
              foreach($desc as $key => $value) {
                $item[$key] = $value;
              }


            }

            // 4] Провести обработку каждой вещи в $items
            foreach($items as $key => &$description) {

              // 4.1] Вынести важную информацию из тегов на 1-й уровень массива
              // - Если, конечно, индекс 'tags' присутствует
              if(array_key_exists('tags', $description)) {
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
              } else {
                $description['type'] = "";
                $description['weapon'] = "";
                $description['collection'] = "";
                $description['category'] = "";
                $description['quality'] = "";
                $description['quality_color'] = "";
                $description['exterior'] = "";
              }

              // 4.2] Сформировать полные URL для картинок

                // 1] Получить сервер изображений steam
                $steam_image_server = config('M8.steam_image_server') ?: 'http://steamcommunity-a.akamaihd.net/economy/image/';

                // 2] Сформировать полный URL для icon_url
                $description['icon_url'] = !empty($description['icon_url']) ? $steam_image_server . $description['icon_url'] . '/360fx360f' : "https://placeholdit.imgix.net/~text?txtsize=50&txt=steam%20problems&w=300&h=300&txttrack=0";

                // 3] Сформировать полный URL для icon_url_large
                $description['icon_url_large'] = !empty($description['icon_url_large']) ? $steam_image_server . '/' . $description['icon_url_large'] : "https://placeholdit.imgix.net/~text?txtsize=50&txt=steam%20problems&w=300&h=300&txttrack=0";

                // 4] Сформировать полный URL для icon_drag_url
                $description['icon_drag_url'] = !empty($description['icon_drag_url']) ? $steam_image_server . '/' . $description['icon_drag_url'] : "https://placeholdit.imgix.net/~text?txtsize=50&txt=steam%20problems&w=300&h=300&txttrack=0";

              // 4.3] На случай глюков steam, добавить необходимые значения полям
              if(!array_key_exists('tags', $description)) {
                $description['background_color'] = "#fff";
                $description['price'] = "0";
              }

            }

            // 5] Добавить в $items дополнительные поля
            call_user_func(function() USE (&$items) {

              // 5.1] Пробежаться по $items и получить массив полей market_hash_name
              $market_hash_names = call_user_func(function() USE ($items) {

                // 1] Подготовить массив для результатов
                $results = [];

                // 2] Наполнить $results
                foreach($items as $item) {
                  if(array_key_exists('market_hash_name', $item))
                    array_push($results, $item['market_hash_name']);
                }

                // 3] Вернуть результаты
                return $results;

              });

              // 5.2] Добавить в каждый из $item его цену

                // 5.2.1] Для каждого $market_hash_names получить цену
                $prices = call_user_func(function() USE ($market_hash_names) {

                  // 1] Если $market_hash_names пуст, вернуть []
                  if(empty($market_hash_names)) return [];

                  // 2] Выполнить запрос цен
                  $result = runcommand('\M8\Commands\C17_get_final_items_prices', ['items' => $market_hash_names]);
                  if($result['status'] != 0)
                    throw new \Exception($result['data']['errormsg']);

                  // 3] Вернуть результат
                  return $result['data']['prices'];

                });

                // 5.2.2] Добавить в $items доп.поля
                // - Если $prices не пуст
                foreach($items as &$item) {
                  if(array_key_exists('market_hash_name', $item) && !empty($prices)) {
                    $item['price']          = $prices[$item['market_hash_name']]['price'];
                    $item['price_success']  = $prices[$item['market_hash_name']]['success'];
                  }
                }

              // 5.3] Добавить в каждый из $item его тип и стабильность
              // - Каждая вещь может быть одновременно нескольких типов.
              // - Поэтому, добавить св-во itemtypes в следующем формате:
              //
              //    [
              //      "undefined"         => 0 или 1,    // тип не определён
              //      "case"              => 0 или 1,
              //      "key"               => 0 или 1,
              //      "startrak"          => 0 или 1,
              //      "souvenir"          => 0 или 1,
              //      "souvenir packages" => 0 или 1,
              //      "knife"             => 0 или 1,
              //      "weapon"            => 0 или 1,
              //    ]
              //
              foreach($items as &$item) {

                // 1] Получить вещь с name == $item
                $item_db = call_user_func(function() USE ($item) {

                  // 1.1] Если ключ market_hash_name отсутствует в $item
                  if(!array_key_exists('market_hash_name', $item))
                    return null;

                  // 1.2] Получить вещь с name == $item
                  $item_db = \M8\Models\MD2_items::where('name', $item['market_hash_name'])->first();

                  // 1.3] Если $item_db пуст, вернуть null
                  if(empty($item_db)) return null;

                  // 1.4] В противном случае, вернуть $item_db
                  else return $item_db;

                });

                // 2] Если $item_db пуст
                if(empty($item_db)) {

                  // 2.1] Записать в $item is_price_unstable
                  $item['is_price_unstable'] = 0;

                  // 2.2] Записать в $item тип вещи
                  $item['itemtypes'] = [
                    "undefined"         => 1,
                    "case"              => 0,
                    "key"               => 0,
                    "startrak"          => 0,
                    "souvenir"          => 0,
                    "souvenir packages" => 0,
                    "knife"             => 0,
                    "weapon"            => 0,
                  ];

                  // 2.3] Перейти к следующей итерации
                  continue;

                }

                // 3] Если $item_db не пуст
                else {

                  // 3.1] Добавить в каждый из $item показатель стабильности его цены
                  $item['is_price_unstable'] = $item_db->is_price_unstable;

                  // 3.2] Добавить в $item его тип
                  $item['itemtypes'] = [
                    "undefined"         => 0,
                    "case"              => $item_db->is_case == '1' ? 1 : 0,
                    "key"               => $item_db->is_key == '1' ? 1 : 0,
                    "startrak"          => $item_db->is_startrak == '1' ? 1 : 0,
                    "souvenir"          => $item_db->is_souvenir == '1' ? 1 : 0,
                    "souvenir packages" => $item_db->is_souvenir_package == '1' ? 1 : 0,
                    "knife"             => $item_db->is_knife == '1' ? 1 : 0,
                    "weapon"            => $item_db->is_weapon == '1' ? 1 : 0,
                  ];

                  // 3.3] Перейти к следующей итерации
                  continue;

                }

              }

            });

            // n] Вернуть результаты
            return $items;

          });

          // 2.2.5. Вернуть $items
          return $items;

        }

      });

      // 3. Закэшировать все вещи
      Cache::put("inventory:".$this->data['steamid'], json_encode([
        "rgDescriptions"  => $items,
        "inventory_count" => count($items)
      ], JSON_UNESCAPED_UNICODE), 30);

      // 4. Если filter_by_room_id не пуста, провести фильтрацию $items
      call_user_func(function() USE (&$items) {

        // 1] Если filter_by_room_id нет, завершить
        if(empty($this->data['filter_by_room_id'])) return;

        // 2] Получить кэш комнат
        // - Если его нет, завершить.
        $rooms = json_decode(Cache::get("processing:rooms"), true);
        if(empty($rooms) || count($rooms) == 0)
          return;

        // 3] Найти комнату с ID == data['filter_by_room_id']
        $room = call_user_func(function() USE ($rooms) {
          foreach($rooms as $r)
            if($r['id'] == $this->data['filter_by_room_id'])
              return $r;
        });
        if(empty($room))
          return;

        // 4] Получить список допустимых в $room типов вещей
        $allow_only_types = json_decode($room['allow_only_types'], JSON_UNESCAPED_UNICODE);

        // 5] Получить список запрещённых в $room типов вещей
        $forbidden_only_types =

        // 5] Отфильтровать $items по типам вещей
        $items = array_values(array_filter($items, function($item, $key) USE ($allow_only_types) {

          // 5.1] Все ли itemtypes вещи $item есть в $allow_only_types
          $is = call_user_func(function() USE ($item, $allow_only_types) {

            foreach($item['itemtypes'] as $type => $is) {
              if($is == 1) {
                if(!in_array($type, $allow_only_types))
                  return false;
              }
            }
            return true;

          });

          // 5.2] Если все, то пропустить
          return $is;

        }, ARRAY_FILTER_USE_BOTH));

      });

      // 5. Отфильтровать из $items все вещи без цены
      call_user_func(function() USE (&$items) {

        $items = array_values(array_filter($items, function($item, $key) {

          return !empty($item['price']);

        }, ARRAY_FILTER_USE_BOTH));

      });

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "rgDescriptions"  => $items,
          "inventory_count" => count($items)
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

