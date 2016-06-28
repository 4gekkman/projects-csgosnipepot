<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Accepts list of market names of items and returns prices for them
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        items     // Массив рыночных имён вещей из CS:GO
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
 *  Алгоритм вычисления итоговой цены вещи
 *  --------------------------------------
 *
 * 
 *
 *
 *
 *    1. Если есть steammarket_qty
 *
 *      1.1. Если steammarket_qty >= anti_manipulating_quantity_limit
 *
 *        1.1.1. Если есть csgofast_price, брать её.
 *
 *        1.1.2. Если нет, но есть steammarket_price, брать её
 *
 *        1.1.3. Если нет, то попробовать запросить median_price
 *
 *        1.1.4. Если никаких цен нет, брать цену price_default4unknown_items + делать пометку
 *
 *      1.2. Если steammarket_qty < anti_manipulating_quantity_limit
 *
 *        1.2.1. Запросить lowest_price, и использовать её.
 *
 *        1.2.2. Если нет, использовать цену csgofast_price
 *
 *        1.2.3. Если нет, но есть steammarket_price, брать её
 *
 *        1.2.4. Если никаких цен нет, брать цену price_default4unknown_items + делать пометку
 *
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
class C17_get_final_items_prices extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить коллекцию вещей с именами items
     *  3. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------------------------------//
    // Принять массив имён вещей из CS:GO на рынке, получить для них цены, и вернуть результаты //
    //------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "items"              => ["required", "array"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Определить цену для каждого предмета в $this->data['items']
      // - Формат результатов:
      //
      //   [
      //     "имя" => [
      //       "success" => true,   // Удалось ли найти цену (true), или взяли стандартную (false)
      //       "price" => "цена",
      //     ]
      //   ]
      //
      //
      $prices = call_user_func(function() {

        // 2.1. Подготовить массив для результатов
        $results = [];

        // 2.2. Извлечь из конфига необходимые значения
        $price_default4unknown_items = config("M8.price_default4unknown_items"); $price_default4unknown_items = !empty($price_default4unknown_items) ? $price_default4unknown_items : '0.01';
        $anti_manipulating_quantity_limit = config("M8.anti_manipulating_quantity_limit"); $anti_manipulating_quantity_limit = !empty($anti_manipulating_quantity_limit) ? $anti_manipulating_quantity_limit : '30';

        // 2.3. Наполнить $results
        foreach($this->data['items'] as $item_name) {

          // 1] Попробовать найти $item_name в БД
          $item = \M8\Models\MD2_items::where('name', $item_name)->first();

          // 2] Если $item не найдено
          // - Использовать csgofast_price, если есть.
          // - А если нет, попробовать запросить для $item_name median price.
          if(empty($item) || empty($item->steammarket_qty)) {

            // 2.1] Установить $item->csgofast_price, если есть
            if(!empty($item) && !empty($item->csgofast_price)) {
              $results[$item_name] = [
                "success" => true,
                "price"   => $item->csgofast_price
              ];
              continue;
            }

            // 2.2] Запросить из истории Steam Market
            $result = runcommand('\M8\Commands\C16_get_price_steammarket', ['name' => $item_name]);

            // 2.3] Если неудачно
            if($result['status'] != 0 || empty($result['data']['median_price'])) {
              $results[$item_name] = [
                "success" => false,
                "price"   => $price_default4unknown_items
              ];
              continue;
            }

            // 2.3] Если найдено
            else {
              $results[$item_name] = [
                "success" => true,
                "price"   => $result['data']['median_price']
              ];
              continue;
            }

          }

          // 3] Если $item найдено
          else {

            // 3.1] Если steammarket_qty >= anti_manipulating_quantity_limit
            if(gmp_cmp($item->steammarket_qty, $anti_manipulating_quantity_limit) >= 0) {

              // 3.1.1] Если есть csgofast_price, брать её.
              if(!empty($item->csgofast_price)) {
                $results[$item_name] = [
                  "success" => true,
                  "price"   => $item->csgofast_price
                ];
                continue;
              }

              // 3.1.2] Если нет, но есть steammarket_price, брать её
              else if(!empty($item->steammarket_price)) {
                $results[$item_name] = [
                  "success" => true,
                  "price"   => $item->steammarket_price
                ];
                continue;
              }

              // 3.1.3] Если нет, то попробовать запросить median_price
              else {

                // Запросить
                $result = runcommand('\M8\Commands\C16_get_price_steammarket', ['name' => $item_name]);

                // Если неудачно
                if($result['status'] != 0 || empty($result['data']['median_price'])) {
                  $results[$item_name] = [
                    "success" => false,
                    "price"   => $price_default4unknown_items
                  ];
                  continue;
                }

                // Если найдено
                else {
                  $results[$item_name] = [
                    "success" => true,
                    "price"   => $result['data']['median_price']
                  ];
                  continue;
                }

              }

            }

            // 3.2] Если steammarket_qty < anti_manipulating_quantity_limit
            if(gmp_cmp($item->steammarket_qty, $anti_manipulating_quantity_limit) < 0) {

              // 3.2.1] Запросить lowest_price, и использовать её

                // Запросить
                $result = runcommand('\M8\Commands\C16_get_price_steammarket', ['name' => $item_name]);

                // Если неудачно
                if($result['status'] != 0 || empty($result['data']['lowest_price'])) {

                  // Если есть csgofast_price, брать её.
                  if(!empty($item->csgofast_price)) {
                    $results[$item_name] = [
                      "success" => true,
                      "price"   => $item->csgofast_price
                    ];
                    continue;
                  }

                  // Если нет, но есть steammarket_price, брать её
                  else if(!empty($item->steammarket_price)) {
                    $results[$item_name] = [
                      "success" => true,
                      "price"   => $item->steammarket_price
                    ];
                    continue;
                  }

                  // Если никаких цен нет, брать цену price_default4unknown_items + делать пометку
                  else {
                    $results[$item_name] = [
                      "success" => false,
                      "price"   => $price_default4unknown_items
                    ];
                    continue;
                  }

                }

                // Если найдено
                else {
                  $results[$item_name] = [
                    "success" => true,
                    "price"   => $result['data']['lowest_price']
                  ];
                  continue;
                }

            }

          }

        }

        // 2.n. Вернуть результат
        return $results;

      });

      // 3. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "prices" => $prices
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C17_get_final_items_prices from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C17_get_final_items_prices']);
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

