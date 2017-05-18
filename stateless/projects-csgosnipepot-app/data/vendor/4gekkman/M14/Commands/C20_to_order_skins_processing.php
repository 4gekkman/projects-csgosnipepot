<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Processing of to order goods from shop
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_user
 *        items2order
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

  namespace M14\Commands;

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
class C20_to_order_skins_processing extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Проверить, не зарезервированы ли вещи из items2order
     *  3. Подсчитать общую стоимость заказанных скинов в монетах
     *  4. Попробовать снять со счёта пользователя id_user монеты $order_sum_coins
     *  5. Если возникла любая ошибка, транслировать и завершить
     *  6. Если операция прошла гладко, выполнить необходимые действия
     *  7. Сделать commit
     *  8. Выполнить C18_update_tos_goods
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------//
    // Processing of to order goods from shop //
    //----------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_user"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "steam_tradeurl"  => ["required", "string"],
        "id_purchase"     => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "items2order"     => ["required", "array"],
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Проверить, не зарезервированы ли вещи из items2order

        // 2.1. Получить все интересующие нас вещи
        $all_reserved_items = \M14\Models\MD2_skins2order::with(['m8_items'])
          ->where('id_status', '>', 1)->get();

        // 2.2. Проверить, есть ли хоть 1-на из items2order среди вещей $all_reserved_items
        $reserved_items = call_user_func(function() USE ($all_reserved_items) {

          // 1] Подготовить массив для результата
          $results = [];

          // 2] Произвести проверку
          foreach($this->data['items2order'] as $item2order) {
            foreach($all_reserved_items as $item) {
              if($item['assetid'] == $item2order['assetid']) {
                if(!in_array($item['assetid'], collect($results)->pluck('assetid')->toArray()))
                  array_push($results, $item2order);
              }
            }
          }

          // 3] Вернуть результат
          return $results;

        });

        // 2.3. Если некоторые из items2order уже зарезервированы
        // - Транслировать ошибку, и завершить
        if(count($reserved_items) != 0) {

          // Транслировать ошибку
          Event::fire(new \R2\Broadcast([
            'channels' => ['m9:private:'.$this->data['id_user']],
            'queue'    => 'm14_processor',
            'data'     => [
              'task' => 'm14:buy:order:reserved',
              'data' => [
                'reserved_items'  => collect($reserved_items)->pluck('market_name')->toArray(),
              ]
            ]
          ]));

          // Завершить
          return;

        }

      // 3. Подсчитать общую стоимость заказанных скинов в монетах
      $order_sum_coins = call_user_func(function() {

        $result = 0;
        foreach($this->data['items2order'] as $item) {
          $result = +$result + round($item['price']*100);
        }
        return $result;

      });

      // 4. Попробовать снять со счёта пользователя id_user монеты $order_sum_coins
      $result = runcommand('\M13\Commands\C14_subtract_coins', [
        "id_user"     => $this->data['id_user'],
        "coins"       => $order_sum_coins,
        "description" => "Оплата части заказа №".$this->data['id_purchase'].", связанной со скинами на заказ"
      ]);

      // 5. Если возникла любая ошибка, транслировать и завершить
      if($result['status'] != 0) {

        // Транслировать ошибку
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$this->data['id_user']],
          'queue'    => 'm14_processor',
          'data'     => [
            'task' => 'm14:buy:order:reserved',
            'data' => [
              'reserved_items'  => collect($this->data['items2order'])->pluck('market_name')->toArray(),
            ]
          ]
        ]));

        // Завершить
        return;

      }

      // 6. Если операция прошла гладко, выполнить необходимые действия
      else if($result['status'] == 0) {

        // 6.1. Получить все заказанные, и доступные для заказа скины
        $items2order = \M14\Models\MD2_skins2order::with(['m8_items'])
          ->whereIn('assetid', collect($this->data['items2order'])->pluck('assetid')->toArray())->get();

        // 6.2. Установить им всем id_status равный 2
        foreach($items2order as $item2order) {
          $item2order->id_status = 2;
          $item2order->save();
        }

        // 6.3. Отправить на указанный в конфиге email инфу по этому заказу
        // - market name, ссылка на обмен пользователя и steamid

          // 1] Получить из конфига массив всех email, на которые надо отправить письмо
          $emails = config("M14.emails_skin_orders");

          // 2] Отправить
          foreach($emails as $email) {

            // 2.1] Подготовить текст
            $text = "Оплаченный заказ на скины. " .
                "Steamid: " . $this->data['steamid'] . ". " .
                "Trade url: " . $this->data['steam_tradeurl'] . ". " .
                "Skins: " . implode(", ", collect($this->data['items2order'])->pluck('market_name')->toArray());

            // 2.2] Отправить email
            Mail::raw($text, function($message) USE ($email) {
              $message->from("csgohap@gmail.com")->to($email)->subject("Оплаченный заказ на скины с csgohap №".$this->data['id_purchase']);
            });

          }

        // 6.4. Уведомить пользователя, что данные скины он получит в течение указанных в конфиге часов
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$this->data['id_user']],
          'queue'    => 'm14_processor',
          'data'     => [
            'task' => 'm14:buy:order:success',
            'data' => [
              'reserved_items'  => collect($this->data['items2order'])->pluck('market_name')->toArray(),
            ]
          ]
        ]));

      }

      // 7. Сделать commit
      DB::commit();

      // 8. Выполнить C18_update_tos_goods
      $result = runcommand('\M14\Commands\C18_update_tos_goods', []);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C20_to_order_skins_processing from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C20_to_order_skins_processing']);
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

