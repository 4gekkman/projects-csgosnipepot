<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Cancel the active trade offer that was sent by the bot in steam, and also in DB
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        betid,
 *        tradeofferid
 *        id_bot
 *        id_user
 *        id_room
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

  namespace M9\Commands;

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
class C12_cancel_the_active_bet extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Попробовать получить оффер с tradeofferid из Steam через HTTP
     *  3. Получить из кэша информацию о состоянии активных офферов из m9.C35
     *  4. Попробовать определить текущее состояние оффера tradeofferid
     *  5. Если текущее состояние удалось определить, и оно не "Accepted"
     *    5.1. Попробовать отменить этот оффер, если он активен
     *    5.2. Если это был активный оффер, и отменить его успешно удалось
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------------------------//
    // Отменить торговое предложение, которое было отправлено ботом игроку, в Steam, а также в базе данных //
    //-----------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [

        "betid"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "tradeofferid"  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_bot"        => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_user"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_room"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"]

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

//      // 2. Попробовать получить оффер с tradeofferid из Steam через HTTP
//      // - Что означают коды:
//      //
//      //    -3    // Не удалось получить ответ от Steam
//      //    -2    // Информация об отправленных офферах отсутствует в ответе в принципе
//      //    -1    // Среди отправленных офферов не удалось найти оффер с tradeofferid
//      //    0     // Успех, найденный оффер доступен по ключу offer
//      //
//      // $offers = runcommand('\M8\Commands\C19_get_tradeoffers_via_api', ["id_bot"=>3,"activeonly"=>1]);
//      // $offers = runcommand('\M8\Commands\C24_get_trade_offers_via_html', ["id_bot"=>3,"mode"=>3]);
//      $offers_http = call_user_func(function(){
//
//        // 1] Получить все активные офферы бота id_bot
//        $offers = runcommand('\M8\Commands\C24_get_trade_offers_via_html', ["id_bot"=>3,"mode"=>3]);
//
//        // 2] Если получить ответ от Steam не удалось
//        if($offers['status'] != 0)
//          return [
//            "code"   => -3,
//            "offer"  => ""
//          ];
//
//        // 3] Если trade_offers_sent отсутствуют в ответе
//        if(!array_key_exists('trade_offers_sent', $offers['data']['tradeoffers']))
//          return [
//            "code"   => -2,
//            "offer"  => ""
//          ];
//
//        // 4] Найти среди $offers оффер с tradeofferid
//
//          // 4.1] Попробовать найти
//          for($i=0; $i<count($offers['data']['tradeoffers']['trade_offers_sent']); $i++) {
//            if($offers['data']['tradeoffers']['trade_offers_sent'][$i]['tradeofferid'] == $this->data['tradeofferid'])
//              return [
//                "code"  => 0,
//                "offer" => $offers['data']['tradeoffers']['trade_offers_sent'][$i]
//              ];
//          }
//
//          // 4.2] Если найти не удалось
//          return [
//            "code"  => -1,
//            "offer" => ""
//          ];
//
//      });
//
//      // 3. Получить из кэша информацию о состоянии активных офферов из m9.C35
//      // - В формате:
//      //
//      //    [
//      //      [
//      //        id_bot
//      //        tradeoffer
//      //        tradeofferid
//      //        id_status_old
//      //        id_status_new
//      //      ],
//      //      ...
//      //    ]
//      //
//      $c35_executing = json_decode(Cache::get('m9:processing:bets_ex_active'), true);
//      if(empty($c35_executing)) $c35_executing = [];
//
//      // 4. Попробовать определить текущее состояние оффера tradeofferid
//      // - Если не удалось, вернуть 0.
//      $current_tradeofferid_state = call_user_func(function() USE ($offers_http, $c35_executing) {
//
//        // 1] Если запрос $offers_http был успешен, вернуть результат
//        if($offers_http['code'] == 0) {
//          if(array_key_exists('trade_offer_state', $offers_http['offer']))
//            return $offers_http['offer']['trade_offer_state'];
//        }
//
//        // 2] В противном случае, попробовать найти tradeofferid в $c35_executing
//        foreach($c35_executing as $to) {
//          if($to['tradeofferid'] == $this->data['tradeofferid'])
//            return $to['id_status_new'];
//        }
//
//        // 3] Если до сих пор ничего не найдено, вернуть 0
//        return 0;
//
//      });
//
//      // 5. Если текущее состояние удалось определить, и оно не "Accepted"
//      if(!empty($current_tradeofferid_state) && $current_tradeofferid_state != 0 && $current_tradeofferid_state != 3) {
//
//        // 5.1. Попробовать отменить этот оффер, если он активен
//        if($current_tradeofferid_state == 2)
//          $cancel_result = runcommand('\M8\Commands\C27_cancel_trade_offer', [
//            "id_bot"        => $this->data['id_bot'],
//            "id_tradeoffer" => $this->data['tradeofferid']
//          ]);
//
//        // 5.2. Если это был активный оффер, и отменить его успешно удалось
//        // - Внести соответствующие изменения в БД.
//        if($current_tradeofferid_state == 2 && !empty($cancel_result) && $cancel_result['status'] == 0) {
//
//          $result = runcommand('\M9\Commands\C15_cancel_the_active_bet_dbpart', [
//            "betid"             => $this->data['betid'],
//            "tradeofferid"      => $this->data['tradeofferid'],
//            "another_status_id" => 6,
//            "id_user"           => $this->data['id_user'],
//            "id_room"           => $this->data['id_room'],
//          ]);
//          if($result['status'] != 0)
//            throw new \Exception($result['data']['errormsg']);
//
//        }
//
//      }

        // 2. Попробовать отменить оффер tradeofferid
        $cancel_result = runcommand('\M8\Commands\C27_cancel_trade_offer', [
          "id_bot"        => $this->data['id_bot'],
          "id_tradeoffer" => $this->data['tradeofferid']
        ]);

        // 3. Если отменить его успешно удалось
        // - Внести соответствующие изменения в БД.
        if(!empty($cancel_result) && $cancel_result['status'] == 0) {

          $result = runcommand('\M9\Commands\C15_cancel_the_active_bet_dbpart', [
            "betid"             => $this->data['betid'],
            "tradeofferid"      => $this->data['tradeofferid'],
            "another_status_id" => 6,
            "id_user"           => $this->data['id_user'],
            "id_room"           => $this->data['id_room'],
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

        }
      

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C12_cancel_the_active_bet from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C12_cancel_the_active_bet']);
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

