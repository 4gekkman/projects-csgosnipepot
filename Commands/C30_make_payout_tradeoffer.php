<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make trade offer to payout the prize to the winner
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_win
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
class C30_make_payout_tradeoffer extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------//
    // Make trade offer to payout the prize to the winner //
    //----------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_win"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"]
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Получить steamid пользователя, который делает запрос
      $steamid_and_id = call_user_func(function(){

        // 1] Получить аутентификационный экш из сессии
        $auth = json_decode(session('auth_cache'), true);
        if(empty($auth))
          throw new \Exception('Не удалось найти аутентификационный кэш в сессии.');

        // 2] Получить информацию о пользователе
        $user = array_key_exists('user', $auth) ? $auth['user'] : "";
        if(empty($user))
          throw new \Exception('Не удалось найти аутентификационный кэш в сессии.');

        // 3] Получить steamid пользователя $user
        if(array_key_exists('ha_provider_uid', $user))
          return [
            "steamid" => $user['ha_provider_uid'],
            "id"      => $user['id'],
            "user"    => $user
          ];
        else
          return [
            "steamid" => "",
            "id"      => "",
            "user"    => ""
          ];

      });
      if(empty($steamid_and_id) || count($steamid_and_id) == 0 || empty($steamid_and_id['steamid']) || empty($steamid_and_id['id']))
        throw new \Exception('Не удалось найти steamid пользователя в аутентификационном кэше сессии.');

      // 3. Получить из кэша все not_paid_expired-выигрыши этого игрока
      $not_paid_expired = json_decode(Cache::tags(['processing:wins:not_paid_expired:personal'])->get('processing:wins:not_paid_expired:'.$steamid_and_id['id']), true);
      if(empty($not_paid_expired))
        $not_paid_expired = [];

      // 4. Найти среди них выигрыш с id_win
      $win2pay = call_user_func(function() USE ($not_paid_expired) {
        foreach($not_paid_expired as $win) {
          if($win['id'] == $this->data['id_win'])
            return $win;
        }
      });
      if(empty($win2pay))
        throw new \Exception("Не удалось обнаружить в системе тот выигрыш, который ты хочешь забрать.");

      // 5. От каждого бота отправить игроку торовое предложение
      // - И получить traderofferid каждого из этих предложений.
      // - В случае неудачи при отправке, процесс не прерывается.
      // - Потом для успешно расплатившихся ботов надо будет пометить
      //   в поле is_free == 1, в pivot-таблице выигрыш-бот.
      // - Результат получить в формате:
      //
      //    $tradeoffer_ids[<id бота>] = [
      //      "success"       => true/false,
      //      "tradeofferid"  => <номер оффера> или "" (в случае неудачи),
      //      "error"         => текст ошибки
      //    ]
      //
      $tradeoffer_ids = call_user_func(function() USE ($steamid_and_id, $win2pay) {

        // 5.1. Подготовить массив для результатов
        $results = [];

        // 5.2. Наполнить $results
        foreach($win2pay['m8_bots'] as $bot) {

          // 1] Попробовать отправить оффер
          $tradeoffer_result = call_user_func(function() USE ($steamid_and_id, $win2pay){

            // 1.1] Получить steam_tradeurl пользователя $user
            $steam_tradeurl = $steamid_and_id['user']['steam_tradeurl'];
            if(empty($steam_tradeurl))
              return [
                "success"       => false,
                "tradeofferid"  => "",
                "error"         => "Торговый URL бота не введён."
              ];

            // 1.2] Получить partner и token пользователя из его trade url
            $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
              "trade_url" => $steam_tradeurl
            ]);
            if($partner_and_token['status'] != 0)
              return [
                "success"       => false,
                "tradeofferid"  => "",
                "error"         => "Торговый URL бота неверен."
              ];
            $partner = $partner_and_token['data']['partner'];
            $token = $partner_and_token['data']['token'];

            Log::info($steam_tradeurl);
            Log::info($partner);
            Log::info($token);


            // 1.n] Вернуть результат
            return [
              "success"       => true,
              "tradeofferid"  => "",
              "error"         => ""
            ];



    //        // 3] Подготовить массив assetid вещей, которые бот должен запросить
    //        $assets2recieve = call_user_func(function() USE ($win2pay) {
    //
    //          $results = [];
    //          for($i=0; $i<count($win2pay['m8_items']); $i++) {
    //            array_push($results, $win2pay['m8_items'][$i]['assetid']);
    //          }
    //          return $results;
    //
    //        });
    //
    //        // 4] Сформировать сообщение для торгового предложения
    //        $tradeoffermessage = call_user_func(function() USE ($safecode) {
    //          return "Safecode: ".$safecode;
    //        });
    //
    //        // 5] Отправить пользователю торговое предложение
    //
    //          // 5.1] Отправить
    //          $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
    //            "id_bot"                => $bot2acceptbet->id,
    //            "steamid_partner"  			=> $this->data['players_steamid'],
    //            "id_partner"            => $partner,
    //            "token_partner"         => $token,
    //            "dont_trade_with_gays"  => "1",
    //            "assets2send"           => [],
    //            "assets2recieve"        => $assets2recieve,
    //            "tradeoffermessage"     => $tradeoffermessage
    //          ]);
    //
    //          // 5.2] Если возникла ошибка
    //          if($tradeoffer['status'] != 0)
    //            throw new \Exception("Не удалось отправить торговое предложение. Возможно, проблемы с ботом, или Steam тормозит.");
    //
    //          // 5.3] Если с этим пользователем нельзя торговать из-за escrow
    //          if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
    //            throw new \Exception("Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней.");
    //
    //        // 6] Подтвердить все исходящие торговые предложения бота $bot2acceptbet
    //        $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
    //          "id_bot"                => $bot2acceptbet->id,
    //          "need_to_ids"           => "0",
    //          "just_fetch_info"       => "0"
    //        ]);
    //        if($result['status'] != 0)
    //          throw new \Exception($result['data']['errormsg']);
    //
    //        // n] Вернуть ID торгового предложения
    //        return $tradeoffer['data']['tradeofferid'];

          });

          // 5.2.2. Записать результат в $results
          $results[$bot['id']] = $tradeofferid;

        }

        // 5.3. Вернуть $results
        return $results;

      });




      // n. Обновить весь кэш
      // TODO


      // m. Вернуть результаты


//        // 18.1. Получить массив ботов, проводивших раунд
//        $roundbots = call_user_func(function() USE ($winner_and_ticket) {
//          $result = [];
//          $result_ids = [];
//          foreach($winner_and_ticket['round']['bets'] as $bet) {
//            if(!in_array($bet['m8_bots'][0]['id'], $result_ids)) {
//              array_push($result_ids, $bet['m8_bots'][0]['id']);
//              array_push($result, $bet['m8_bots'][0]);
//            }
//          }
//          return $result;
//        });



//        // 19.1. Получить инвентари этих ботов
//        // - Чтобы иметь доступ в формате: $roundbots_inventories[<id бота>][<id вещи>]
//        $roundbots_inventories = call_user_func(function() USE ($roundbots) {
//          $results = [];
//          foreach($roundbots as $bot) {
//
//            // 1] Получить инвентарь бота $bot
//            $inventory = runcommand('\M8\Commands\C4_getinventory', [
//              "steamid" => $bot['steamid'],
//              "force"   => true
//            ]);
//            if($inventory['status'] != 0)
//              throw new \Exception("Не получается получить инвентарь бота №".$bot['steamid']." из Steam.");
//
//            // 2] Записать $inventory в $results
//            // - Так, чтобы иметь доступ в формате: $roundbots_inventories[<id бота>][<id вещи>]
//            $results[$bot['id']] = call_user_func(function() USE ($inventory) {
//
//              $results = [];
//              foreach($inventory['data']['rgDescriptions'] as $item) {
//                $results[$item['id']] = $item;
//              }
//              return $results;
//
//            });
//
//          }
//          return $results;
//        });
//
//        Log::info($roundbots_inventories);






//      // 5. Получить инвентарь бота, который должен выплачивать выигрыш
//      $inventory = runcommand('\M8\Commands\C4_getinventory', [
//        "steamid" => $win2pay['m8_bots'][0]['steamid'],
//        "force"   => true
//      ]);
//      if($inventory['status'] != 0)
//        throw new \Exception("Не получается получить твой инвентарь. Зайди в свой аккаунт в Steam, в настройки приватности, и проверь, чтобы инвентарь был 'Public'.");
//
//      // 6. Удостовериться, что в $inventory есть все вещи, которые надо выплатить
//      call_user_func(function() USE ($inventory) {
//
//        // 1] Получить список assetid_classid_instanceid всех вещей из инвентаря
//        $inventory_ids = call_user_func(function() USE ($inventory) {
//          $results = [];
//          foreach($inventory['data']['rgDescriptions'] as $item) {
//            array_push($results, $item['assetid'] . "_" . $item['classid'] . "_" . $item['instanceid']);
//          }
//          return $results;
//        });
//
//        // 2] Получить
//
//        Log::info($inventory_ids);
//
//      });




      // 6. Отправить игроку торговое предложение
      // - С запросом тех предметов, которые он хочет поставить.
      $tradeofferid = call_user_func(function() USE ($steamid_and_id, $win2pay){

        // 1] Получить steam_tradeurl пользователя $user
        $steam_tradeurl = $steamid_and_id['user']['steam_tradeurl'];
        if(empty($steam_tradeurl))
          throw new \Exception("Чтобы сделать ставку, сначала введи свой Steam Trade URL в настройках.");

        // 2] Получить partner и token пользователя из его trade url
        $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
          "trade_url" => $steam_tradeurl
        ]);
        if($partner_and_token['status'] != 0)
          throw new \Exception("Похоже, что ты ввёл неправильный Steam Trade URL в настройках. Перепроверь его.");
        $partner = $partner_and_token['data']['partner'];
        $token = $partner_and_token['data']['token'];



//        // 3] Подготовить массив assetid вещей, которые бот должен запросить
//        $assets2recieve = call_user_func(function() USE ($win2pay) {
//
//          $results = [];
//          for($i=0; $i<count($win2pay['m8_items']); $i++) {
//            array_push($results, $win2pay['m8_items'][$i]['assetid']);
//          }
//          return $results;
//
//        });
//
//        // 4] Сформировать сообщение для торгового предложения
//        $tradeoffermessage = call_user_func(function() USE ($safecode) {
//          return "Safecode: ".$safecode;
//        });
//
//        // 5] Отправить пользователю торговое предложение
//
//          // 5.1] Отправить
//          $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
//            "id_bot"                => $bot2acceptbet->id,
//            "steamid_partner"  			=> $this->data['players_steamid'],
//            "id_partner"            => $partner,
//            "token_partner"         => $token,
//            "dont_trade_with_gays"  => "1",
//            "assets2send"           => [],
//            "assets2recieve"        => $assets2recieve,
//            "tradeoffermessage"     => $tradeoffermessage
//          ]);
//
//          // 5.2] Если возникла ошибка
//          if($tradeoffer['status'] != 0)
//            throw new \Exception("Не удалось отправить торговое предложение. Возможно, проблемы с ботом, или Steam тормозит.");
//
//          // 5.3] Если с этим пользователем нельзя торговать из-за escrow
//          if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
//            throw new \Exception("Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней.");
//
//        // 6] Подтвердить все исходящие торговые предложения бота $bot2acceptbet
//        $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
//          "id_bot"                => $bot2acceptbet->id,
//          "need_to_ids"           => "0",
//          "just_fetch_info"       => "0"
//        ]);
//        if($result['status'] != 0)
//          throw new \Exception($result['data']['errormsg']);
//
//        // n] Вернуть ID торгового предложения
//        return $tradeoffer['data']['tradeofferid'];

      });






      throw new \Exception('Stop!');



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C30_make_payout_tradeoffer from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C30_make_payout_tradeoffer']);
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

