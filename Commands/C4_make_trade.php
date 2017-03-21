<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make trade offer to get deposit skins from user
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        players_steamid   | Steam ID игрока
 *        items2bet         | Список вещей, которые хочет поставить игрок
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

  namespace M13\Commands;

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
class C4_make_trade extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить конфигурацию для системы депозита
     *  3. Получить из БД все вещи, указанные в items2bet
     *  4. Если в $items отсутствуют какие-то вещи из items2bet, выписать их market_name
     *  5. Отфильтровать из $items вещи, цена которых менее min_skin2accept_price_cents.
     *  6. Отфильтровать из $items вещи типов, запрещённых в item_type_filters
     *  7. Сгенерировать случайный код безопасности
     *  8. Определить, какой бот будет принимать вещи
     *  9. Получить игрока, который хочет сделать ставку, и проверить валидность его Trade URL
     *  10. Подготовить массив assetid вещей, которые бот должен запросить
     *  11. Подсчитать суммарную стоимость передаваемых вещей в центах
     *  12. Подсчитать суммарное кол-во монет, которые будут выданы (с учётом skin_price2accept_spread_in_perc)
     *  13. Отправить игроку торговое предложение
     *  14. Записать необходимую информацию о ставке в БД
     *  15. Сделать коммит
     *  16. Обновить весь кэш
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------//
    // Make trade offer to get deposit skins from user //
    //-------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "players_steamid" => ["required", "regex:/^[0-9]+$/ui"],
        "items2bet"       => ["required", "array"],
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Получить конфигурацию для системы депозита
      $deposit_configs = call_user_func(function(){

        return [
          'min_skin2accept_price_cents'       => config("M13.min_skin2accept_price_cents") ?: '10',
          'item_type_filters'                 => config("M13.item_type_filters"),
          'skin_price2accept_spread_in_perc'  => config("M13.skin_price2accept_spread_in_perc"),
        ];

      });

      // 3. Получить из БД все вещи, указанные в items2bet
      $items = call_user_func(function(){

        // 1] Получить массив market_name из items2bet
        $items2bet_market_names = call_user_func(function(){
          $results = [];
          for($i=0; $i<count($this->data['items2bet']); $i++) {
            array_push($results, $this->data['items2bet'][$i]['market_name']);
          }
          return $results;
        });

        // 2] Получить из БД коллекцию всех вещей с $items2bet_market_names
        $items = \M8\Models\MD2_items::whereIn('name', $items2bet_market_names)->get();

        // n] Вернуть результаты
        return $items;

      });

      // 4. Если в $items отсутствуют какие-то вещи из items2bet, выписать их market_name
      // - Чтобы потом сообщить об этом пользователю
      $absent_item_names = call_user_func(function() USE ($items) {

        // 1] Получить массив market_name из items2bet
        $items2bet_market_names = collect($this->data['items2bet'])->pluck('market_name')->toArray();

        // 2] Получить массив market_name из $items
        $items_market_names = $items->pluck('name')->toArray();

        // n] Вернуть результаты
        return collect($items2bet_market_names)->diff($items_market_names);

      });

      // 5. Отфильтровать из $items вещи, цена которых менее min_skin2accept_price_cents.
      // - И записать market_name отфильтрованных вещей (чтобы потом сообщить об этом пользователю).
      $items = $items->filter(function($value, $key) USE($deposit_configs, &$absent_item_names) {

        // 1] Прошла ли $value фильтры?
        $has_passed = $value['price']*100 >= $deposit_configs['min_skin2accept_price_cents'];

        // 2] Если не прошла, записать name $value в $absent_item_names, если его там ещё нет
        if(!$has_passed && !in_array($value['name'], $absent_item_names->toArray()))
          $absent_item_names->push($value['name']);

        // 3] Вернуть результат
        return $has_passed;

      });

      // 6. Отфильтровать из $items вещи типов, запрещённых в item_type_filters
      // - И записать market_name отфильтрованных вещей (чтобы потом сообщить об этом пользователю).
      $items = $items->filter(function($value, $key) USE($deposit_configs, &$absent_item_names) {

        // 1] Получить массив фильтров по типам вещей и их значений
        $item_type_filters = $deposit_configs['item_type_filters'];

        // 2] Выяснить, является ли $value запрещённым для депозита
        $is_forbidden = call_user_func(function() USE ($item_type_filters, $value) {

          // 2.1] Получить $value в виде массива
          $val = $value->toArray();

          // 2.2] Выяснить, является ли $value undefined-типом
          $is_undefined = call_user_func(function() USE ($val) {
            if(
              $val['is_case'] != '1' &&
              $val['is_key'] != '1' &&
              $val['is_startrak'] != '1' &&
              $val['is_souvenir'] != '1' &&
              $val['is_souvenir_package'] != '1' &&
              $val['is_knife'] != '1' &&
              $val['is_weapon'] != '1') return 1;
            else
              return 0;
          });

          // 2.3] Если тип $value запрещён, вернуть true

            // undefined
            if($is_undefined == 1 && $item_type_filters['undefined'] == false)
              return true;

            // weapon
            if($val['is_weapon'] == 1 && $item_type_filters['weapon'] == false)
              return true;

            // weapon
            if($val['is_knife'] == 1 && $item_type_filters['knife'] == false)
              return true;

            // weapon
            if($val['is_case'] == 1 && $item_type_filters['case'] == false)
              return true;

            // weapon
            if($val['is_key'] == 1 && $item_type_filters['key'] == false)
              return true;

            // weapon
            if($val['is_startrak'] == 1 && $item_type_filters['startrak'] == false)
              return true;

            // weapon
            if($val['is_souvenir_package'] == 1 && $item_type_filters['souvenir packages'] == false)
              return true;

            // weapon
            if($val['is_souvenir'] == 1 && $item_type_filters['souvenir'] == false)
              return true;

          // 2.3] Иначе, вернуть false
          return false;

        });

        // 3] Если не прошла, записать name $value в $absent_item_names, если его там ещё нет
        if($is_forbidden && !in_array($value['name'], $absent_item_names->toArray()))
          $absent_item_names->push($value['name']);

        // n] Если является, вернуть false, иначе true
        return !$is_forbidden;

      });

      // 7. Сгенерировать случайный код безопасности
      // - Он представляет из себя число из 6 цифр.
      // - У каждого кода безопасности есть свой срок годности.
      $safecode = call_user_func(function() {
        $result = '';
        for($i = 0; $i < 6; $i++) {
          $result .= mt_rand(0, 9);
        }
        return $result;
      });

      // 8. Определить, какой бот будет принимать вещи
      // - Пока что её должен принимать первый бот из bot_group_name_to_accept_deposits.

        // 8.1. Определить бота
        $bot = call_user_func(function(){

          // 1] Получить название группы ботов, которые должны принимать скины в кач-ве депозита
          $group = config("M13.bot_group_name_to_accept_deposits");

          // 2] Получить 1-го бота из группы $group
          $bot = \M8\Models\MD1_bots::whereHas('groups', function($queue) USE ($group){
            $queue->where('name', $group);
          })->first();

          // n] Вернуть $bot
          return $bot;

        });

        // 8.2. Если $bot не найден, завершить с ошибкой
        if(empty($bot))
          throw new \Exception('Не удалось найти бота, который мог бы принять вещи.');

      // 9. Получить игрока, который хочет сделать ставку, и проверить валидность его Trade URL

        // 9.1. Получить игрока
        $user = \M5\Models\MD1_users::where('ha_provider_uid', $this->data['players_steamid'])->first();
        if(empty($user))
          throw new \Exception("Не удалось найти в базе данных запись о твоём профиле.");

        // 9.2. Проверить валидность его Trade URL
        $is_users_tradeurl_valid = call_user_func(function() USE ($user, $bot) {

          // 1] Получить steam_tradeurl пользователя $user
          $steam_tradeurl = $user['steam_tradeurl'];

          // 2] Получить "Partner ID" и "Token" из торгового URL
          $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
            "trade_url" => $steam_tradeurl
          ]);
          if($partner_and_token['status'] != 0)
            return false;

          // 3] Получить steamname и steamid по торговому URL
          $result = runcommand('\M8\Commands\C30_get_steamname_and_steamid_by_tradeurl', [
            "id_bot"  => $bot['id'],
            "partner" => $partner_and_token['data']['partner'],
            "token"   => $partner_and_token['data']['token']
          ]);
          if($result['status'] != 0)
            return false;

          // n] Вернуть true
          return true;

        });

        // 9.3. Если Trade URL не валиден, создать ошибку и сообщить об этом
        if($is_users_tradeurl_valid == false)
          throw new \Exception('Вероятно, указанный Вами в профиле торговый URL не валиден. Пожалуйста, перейдите в профиль и укажите валидный торговый URL.');

      // 10. Подготовить массив assetid вещей, которые бот должен запросить
      // - В формате <assetid> => <market_name>
      $assets2recieve = call_user_func(function() USE ($absent_item_names) {

        $results = [];
        for($i=0; $i<count($this->data['items2bet']); $i++) {
          if(!in_array($this->data['items2bet'][$i]['market_name'], $absent_item_names->toArray()))
            $results[$this->data['items2bet'][$i]['assetid']] = $this->data['items2bet'][$i]['market_name'];
        }
        return $results;

      });

      // 11. Подсчитать суммарную стоимость передаваемых вещей в центах
      $sum_cents = call_user_func(function() USE ($assets2recieve, $items) {

        $result = 0;
        foreach($assets2recieve as $asset => $name) {
          foreach($items as $item) {
            if($item['name'] == $name)
              $result = +$result + +$item['price']*100;
          }
        }
        return $result;

      });

      // 12. Подсчитать суммарное кол-во монет, которые будут выданы (с учётом skin_price2accept_spread_in_perc)
      $sum_coins = call_user_func(function() USE ($sum_cents, $deposit_configs) {

        // 1] Получить спред в % (от 0 до 100).
        $spread = $deposit_configs['skin_price2accept_spread_in_perc'];

        // 2] Подсчитать кол-во монет
        return (int) round($sum_cents*((100 - $spread)/100));

      });

      // 13. Отправить игроку торговое предложение
      $tradeofferid = call_user_func(function() USE ($bot, $safecode, $user, $assets2recieve){

        // 1] Получить steam_tradeurl пользователя $user
        $steam_tradeurl = $user->steam_tradeurl;
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

        // 3] Сформировать сообщение для торгового предложения
        $tradeoffermessage = call_user_func(function() USE ($safecode) {
          return "Safecode: ".$safecode;
        });

        // 4] Отправить пользователю торговое предложение

          // 4.1] Отправить
          $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
            "id_bot"                => $bot->id,
            "steamid_partner"  			=> $this->data['players_steamid'],
            "id_partner"            => $partner,
            "token_partner"         => $token,
            "dont_trade_with_gays"  => "1",
            "assets2send"           => [],
            "assets2recieve"        => array_map(function($item){ return (string)$item; }, array_keys($assets2recieve)),
            "tradeoffermessage"     => $tradeoffermessage
          ]);

          // 4.2] Если возникла ошибка
          if($tradeoffer['status'] != 0)
            throw new \Exception("Не удалось отправить торговое предложение. Возможные причины: ты указал неправильный Steam Trade URL; Steam тормозит; проблемы с ботом.");

          // 4.3] Если с этим пользователем нельзя торговать из-за escrow
          if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
            throw new \Exception("Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней.");

        // 5] Подтвердить все исходящие торговые предложения бота $bot
        $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
          "id_bot"                => $bot->id,
          "need_to_ids"           => "0",
          "just_fetch_info"       => "0"
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // n] Вернуть ID торгового предложения
        return $tradeoffer['data']['tradeofferid'];

      });

      // 14. Записать необходимую информацию о ставке в БД
      call_user_func(function() USE ($bot, $items, $sum_cents, $sum_coins, $user, $assets2recieve, $safecode, $tradeofferid, $deposit_configs) {

        // 1] Создать новый трейд в md4_trades
        $new_trade = new \M13\Models\MD4_trades();
        $new_trade->id_status              = 2;
        $new_trade->tradeofferid           = $tradeofferid;
        $new_trade->sum_cents              = $sum_cents;
        $new_trade->sum_coins              = $sum_cents;
        $new_trade->sum_coins_minus_spread = $sum_coins;
        $new_trade->spread_at_cre_time     = $deposit_configs['skin_price2accept_spread_in_perc'];
        $new_trade->save();

        // 2] Связать его с пользователем $user через md2000
        // - Но номера билетов пока не указывать.
        if(!$new_trade->m5_users->contains($user->id))
          $new_trade->m5_users()->attach($user->id);

        // 3] Связать $new_trade с вещами $items в нашей базе
        // - Не забыть указать item_price_at_bet_time и assetid_users.
        // - А вот assetid_bots пока не указывать.
        foreach($assets2recieve as $assetid => $market_name) {

          // 3.1] Находим в $items вещь по $market_name
          $item = call_user_func(function() USE ($items, $market_name) {
            for($i=0; $i<count($items); $i++) {
              if($items[$i]['name'] == $market_name)
                return $items[$i];
            }
            return "";
          });
          if(empty($item))
            throw new \Exception("Вещь '".$market_name."' неизвестна системе, поэтому её нельзя принять.");

          // 3.2] Связать $new_trade с $item
          if(!$new_trade->m8_items->contains($item['id']))
            $new_trade->m8_items()->attach($item['id'], ['item_price_at_bet_time' => round($item['price'] * 100), 'assetid_users' => $assetid]);

        }

        // 4] Связать $new_trade с ботом $bot
        if(!$new_trade->m8_bots->contains($bot['id'])) $new_trade->m8_bots()->attach($bot['id']);

        // 5] Записать код безопасности $safecode в md6_safecodes
        $newsafecode = new \M13\Models\MD6_safecodes();
        $newsafecode->code = $safecode;
        $newsafecode->save();

        // 7] Связать $safecode и $newbet через md1007
        if(!$new_trade->safecodes->contains($newsafecode->id)) $new_trade->safecodes()->attach($newsafecode->id);

      });

      // 15. Сделать коммит
      DB::commit();

      // 16. Обновить весь кэш
      $result = runcommand('\M13\Commands\C6_update_cache', [
        "all" => true
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "safecode"        => $safecode,
          "tradeofferid"    => $tradeofferid
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C4_make_trade from M-package M13 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M13', 'C4_make_trade']);
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

