<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make steam offers to give bought goods to buyers
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
class C9_make_paid_trades extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить оплаченные трейды, но для которых ранее ещё не были созданы офферы в Steam
     *  2. По каждому из $trades отправить покупателю торговое предложение
     *    2.1. Начать транзакцию
     *    2.2. Получить необходимые для операции данные
     *    2.3. Получить из конфига лимит на кол-во попыток создать оффер
     *    2.4. Получить $trade из БД
     *    2.5. Если количество попыток создать оффер исчерпано
     *    2.6. Отправить игроку торговое предложение
     *    2.7. Если $tradeofferid отправить не удалось
     *    2.8. Если $tradeofferid удалось успешно отправить
     *  3.
     *  n. Обновить кэш трейдов, связанный с сабжом, а также с активными и ожидающими подтверждения офферами
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------//
    // Make steam offers to give bought goods to buyers //
    //--------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить оплаченные трейды, но для которых ранее ещё не были созданы офферы в Steam
      $trades = json_decode(Cache::get('m14:processor:trades:payment_status:2'), true);
      if(empty($trades))
        $trades = [];

      // 2. По каждому из $trades отправить покупателю торговое предложение
      foreach($trades as $trade) {

        // 2.1. Начать транзакцию
        DB::beginTransaction();

        // 2.2. Получить необходимые для операции данные
        $tries_create_offer   = $trade['tries_create_offer'];
        $user                 = $trade['m5_users'][0];
        $id_bot               = $trade['m8_bots'][0]['id'];
        $safecode             = $trade['safecodes'][0]['code'];
        $id_purchase          = $trade['purchases'][0]['id'];
        $assets2send          = call_user_func(function() USE ($trade) {

          $results = [];
          foreach($trade['m8_items'] as $item) {
            if(!in_array($item['pivot']['assetid_bots'], $results))
              array_push($results, $item['pivot']['assetid_bots']);
          }
          return $results;

        });

        // 2.3. Получить из конфига лимит на кол-во попыток создать оффер
        $tries = config("M14.tries_create_offer_until_failed");
        if(empty($tries))
          $tries = 50;

        // 2.4. Получить $trade из БД
        $trade_db = \M14\Models\MD4_trades::where('id', $trade['id'])->first();
        if(empty($trade_db)) {

          // 1] Сообщить
          $errortext = 'Invoking of command C9_make_paid_trades from M-package M14 have ended with error: не удалось найти в БД трейд с ID = '.$trade['id'];
          Log::info($errortext);

          // n] Отменить транзакцию
          DB::rollback();

          // m] Перейти к след.итерации
          continue;

        }

        // 2.5. Если количество попыток создать оффер исчерпано
        if((int)$tries_create_offer >= (int)$tries) {

          // 1] Установить id_status, равный -1 (не удавшаяся сделка, ожидает возврата монет пользователю)
          $trade_db->id_status = -1;
          $trade_db->save();

          // 2] Вернуть в магазин ранее заблокированные предметы отменяемого трейда
          $result = runcommand('\M14\Commands\C17_add_goods', [
            "id_trade"    => $trade['id']
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 3] Сделать коммит
          DB::commit();

          // 3] Перейти к следующей итерации
          continue;

        }

        // 2.6. Отправить игроку торговое предложение
        $tradeofferid = call_user_func(function() USE ($id_bot, $safecode, $user, $assets2send){

          // 1] Получить steam_tradeurl пользователя $user
          $steam_tradeurl = $user['steam_tradeurl'];
          if(empty($steam_tradeurl))
            return [
              "tradeofferid" => "",
              "error"        => "Чтобы сделать ставку, сначала введи свой Steam Trade URL в настройках."
            ];

          // 2] Получить partner и token пользователя из его trade url
          $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
            "trade_url" => $steam_tradeurl
          ]);
          if($partner_and_token['status'] != 0)
            return [
              "tradeofferid" => "",
              "error"        => "Похоже, что ты ввёл неправильный Steam Trade URL в настройках. Перепроверь его."
            ];
          $partner = $partner_and_token['data']['partner'];
          $token = $partner_and_token['data']['token'];

          // 3] Сформировать сообщение для торгового предложения
          $tradeoffermessage = call_user_func(function() USE ($safecode) {
            return "Safecode: ".$safecode;
          });

          // 4] Отправить пользователю торговое предложение

            // 4.1] Отправить
            $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
              "id_bot"                => $id_bot,
              "steamid_partner"  			=> $user['ha_provider_uid'],
              "id_partner"            => $partner,
              "token_partner"         => $token,
              "dont_trade_with_gays"  => "0",
              "assets2send"           => $assets2send,
              "assets2recieve"        => [],
              "tradeoffermessage"     => $tradeoffermessage
            ]);

            // 4.2] Если возникла ошибка
            if($tradeoffer['status'] != 0)
              return [
                "tradeofferid" => "",
                "error"        => "Не удалось отправить торговое предложение. Возможные причины: ты указал неправильный Steam Trade URL; Steam тормозит; проблемы с ботом."
              ];

            // 4.3] Если с этим пользователем нельзя торговать из-за escrow
            if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
              return [
                "tradeofferid" => "",
                "error"        => "Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней."
              ];

          // n] Вернуть ID торгового предложения
          return [
            "tradeofferid" => $tradeoffer['data']['tradeofferid'],
            "error"        => ""
          ];

        });

        // 2.7. Если $tradeofferid отправить не удалось
        if(empty($tradeofferid['tradeofferid'])) {

          // 1] Сообщить
          $errortext = 'Invoking of command C9_make_paid_trades from M-package M14 have ended with error: '.$tradeofferid['error'];
          Log::info($errortext);

          // 2] Прибавить единицу к tries_create_offer
          $trade_db->tries_create_offer = +$trade_db->tries_create_offer + 1;
          $trade_db->save();

          // 3] Сделать коммит
          DB::commit();

        }

        // 2.8. Если $tradeofferid удалось успешно отправить
        else if(!empty($tradeofferid['tradeofferid'])) {

          // 1] Записать необходимые данные в $trade_db
          $trade_db->tradeofferid = $tradeofferid['tradeofferid'];
          $trade_db->id_status    = 9;
          $trade_db->save();

          // 2] Получить кол-во трейдов, связанных с этой покупкой
          $trades_num = \M14\Models\MD4_trades::whereHas('purchases', function($queue) USE ($id_purchase) {
            $queue->where('id', $id_purchase);
          })->count();

          // 3] Прибавить единицу к tries_create_offer
          $trade_db->tries_create_offer = +$trade_db->tries_create_offer + 1;
          $trade_db->save();

          // 4] Сделать коммит
          DB::commit();

        }

      }

      // 3. Получить оплаченные трейды, для которых ранее были созданы офферы, но ожидающие подтверждения
      $trades_need_conf = json_decode(Cache::get('m14:processor:trades:status:9'), true);
      if(empty($trades_need_conf))
        $trades_need_conf = [];

      // 4. Подтвердить каждый из ранее отправленных трейдов, ожидающий подтверждения
      foreach($trades_need_conf as $trade) {

        // 4.1. Начать транзакцию
        DB::beginTransaction();

        // 4.2. Получить необходимые для операции данные
        $id_bot               = $trade['m8_bots'][0]['id'];
        $safecode             = $trade['safecodes'][0]['code'];
        $id_purchase          = $trade['purchases'][0]['id'];
        $tradeofferid         = $trade['tradeofferid'];
        $user                 = $trade['m5_users'][0];

        // 4.3. Подтвердить исходящий оффер $tradeofferid бота $id_bot
        $confirmation = runcommand('\M8\Commands\C21_fetch_confirmations', [
          "id_bot"                => $id_bot,
          "need_to_ids"           => "1",
          "just_fetch_info"       => "0",
          "tradeoffer_ids"        => [
            $tradeofferid
          ]
        ]);

        // 4.4. Если подтвердить оффер удалось
        if($confirmation['status'] == 0) {

          // 4.4.1. Получить $trade из БД
          $trade_db = \M14\Models\MD4_trades::where('id', $trade['id'])->first();
          if(empty($trade_db)) {

            // 1] Сообщить
            $errortext = 'Invoking of command C9_make_paid_trades from M-package M14 have ended with error: не удалось найти в БД трейд с ID = '.$trade['id'];
            Log::info($errortext);

            // n] Отменить транзакцию
            DB::rollback();

            // m] Перейти к след.итерации
            continue;

          }

          // 4.4.2. Записать новый статус оффера
          $trade_db->id_status = 2;

          // 4.4.3. Сохранить изменения, сделать коммит
          $trade_db->save();
          DB::commit();

          // 4.4.4. Получить кол-во трейдов, связанных с этой покупкой
          $trades_num = \M14\Models\MD4_trades::whereHas('purchases', function($queue) USE ($id_purchase) {
            $queue->where('id', $id_purchase);
          })->count();

          // 4.4.5. Через частный канал уведомить пользователя $user о новом оффере
          Event::fire(new \R2\Broadcast([
            'channels' => ['m9:private:'.$user['id']],
            'queue'    => 'm13_processor',
            'data'     => [
              'task' => 'm14:trade:created',
              'data' => [
                'tradeofferid'          => $tradeofferid,
                'id_trade'              => $trade['id'],
                'purchase_trades_num'   => 1,
                'safecode'              => $safecode,
                'id_purchase'           => $id_purchase,
              ]
            ]
          ]));

        }

        // 4.5. Если подтвердить оффер не удалось
        else {

          // 1] Откатить изменения
          DB::rollback();

        }

      }

      // n. Обновить кэш трейдов, связанный с сабжом, а также с активными и ожидающими подтверждения офферами
      // - m14:processor:trades:payment_status:2
      // - m14:processor:trades:status:2
      // - m14:processor:trades:status:9
      runcommand('\M14\Commands\C7_update_cache', [
        "all"   => true,
        "force" => true
      ]);


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C9_make_paid_trades from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C9_make_paid_trades']);
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

