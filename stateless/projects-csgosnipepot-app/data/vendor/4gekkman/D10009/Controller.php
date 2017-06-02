<?php
////==============================================////
////																				      ////
////             Контроллер D-пакета		  	      ////
////																							////
////==============================================////


/**
 *
 *
 *     HTTP-метод   Имя API     Ключ              Защита   Описание
 * ------------------------------------------------------------------------------------------------------------
 * Стандартные операции
 *
 *     GET          GET-API     любой get-запрос           Обработка всех GET-запросов
 *     POST         POST-API    любой post-запрос          Обработка всех POST-запросов
 *
 * ------------------------------------------------------------------------------------------------------------
 * Нестандартные POST-операции
 *
 *                  POST-API1   D10009:1                   Безопасная обёртка для команды изменения trade url
 *                  POST-API2   D10009:2                   Сохранение в куки нового значения для выключателя звука
 *                  POST-API3   D10009:3                   Подгрузить указанную страницу истории для classic game, для указанной комнаты
 *                  POST-API4   D10009:4                   Получить ТОП игроков
 *                  POST-API5   D10009:5                   Получить FAQ
 *                  POST-API6   D10009:6                   Обновить инвентарь аутентифицированного пользователя
 *                  POST-API7   D10009:7                   Создать новый трейд с запросом вещей пользователя
 *                  POST-API8   D10009:8                   Обновить товарные остатки при первом входе в магазин
 *                  POST-API9   D10009:9                   Осуществить покупку, создать новый трейд для доставки вещей покупателю
 *                  POST-API10  D10009:10                  Пользователь запрашивает бесплатные монеты (ежедневная награда)
 *                  POST-API11  D10009:11                  Пользователь запрашивает создание оффера по своей выдаче
 *                  POST-API12  D10009:12                  Попытка применить nick promo
 *                  POST-API13  D10009:13                  Попытка применить steam group promo
 *
 *
 */


//-------------------------------//
// Пространство имён контроллера //
//-------------------------------//

  namespace D10009;


//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

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

  // Модели и прочие классы



//------------//
// Контроллер //
//------------//
class Controller extends BaseController {

  //-------------------------------------------------//
  // ID пакета, которому принадлежит этот контроллер //
  //-------------------------------------------------//
  public $packid = "D10009";
  public $layoutid = "L10003";

  //--------------------------------------//
  // GET-API. Обработка всех GET-запросов //
  //--------------------------------------//
  public function getIndex() {

    //------------------------------------------//
    // Не обрабатывать AJAX-запросы методом GET //
    //------------------------------------------//
    if(\Request::ajax())
      return;

    //----------------------------------------------------------------------------------//
    // Провести авторизацию прав доступа запрашивающего пользователя к этому интерфейсу //
    //----------------------------------------------------------------------------------//
    // - Если команда для проведения авторизации доступна, и если авторизация включена.
    if(class_exists('\M5\Commands\C66_authorize_access') && config("M5.authorize_access_ison") == true) {

      // Провести авторизацию
      $authorize_results = runcommand('\M5\Commands\C66_authorize_access', ['packid' => $this->packid, 'userid' => lib_current_user_id()]);

      // Если доступ запрещён, вернуть документ с кодом 403
      if($authorize_results['status'] == -1)
        return Response::make("Unfortunately, access to this document is forbidden for you.", 403);

    }

    //-----------------------//
    // Проверка наличия кэша //
    //-----------------------//

      // Обновить весь кэш процессинга игры, но для каждого, только если он отсутствует
      $result = runcommand('\M9\Commands\C13_update_cache', [
        "all"   => true,
        "force" => false
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // Обновить весь кэш процессинга выигрышей, но для каждого, только если он отсутствует
      $result = runcommand('\M9\Commands\C25_update_wins_cache', [
        "all"   => true,
        "force" => false
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // Обновить весь кэш истории, но для каждого, только если он отсутствует
      $result = runcommand('\M9\Commands\C51_update_history_cache', [
        "all"   => true,
        "force" => false
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

    //-----------------------//
    // Обработать GET-запрос //
    //-----------------------//

      // 1. Провести авто-инициацию некоторых таблиц-справочников в БД
      $result = runcommand('\M9\Commands\C39_init_db_references', []);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 2. Получить steam_tradeurl и $user, если он есть
      $steam_tradeurl_and_user = call_user_func(function(){

        // 1] Извлечь auth_cache
        $auth = json_decode(session('auth_cache'), true);

        // 2] Получить ID запрашивающего пользователя
        if(empty($auth) || !array_key_exists('user', $auth) || empty($auth['user']) || !array_key_exists('id', $auth['user']))
          return [
            "steam_tradeurl" => "",
            "user"           => ""
          ];
        $id = $auth['user']['id'];

        // 3] Попробовать найти пользователя с $id
        $user = \M5\Models\MD1_users::find($id);
        if(empty($user))
          return [
            "steam_tradeurl" => "",
            "user"           => ""
          ];

        // 4] Вернуть steam_tradeurl
        return [
          "steam_tradeurl" => $user->steam_tradeurl,
          "user"           => $user
        ];

      });

      // 3. Получить все текущие игровые данные и id выбранной комнаты
      $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1, 'safe' => true]);
      if($allgamedata['status'] != 0)
        throw new \Exception($allgamedata['data']['errormsg']);

      // 4. Получить/Подготовить данные, связанные с возможными состояниями комнат
      $states = runcommand('\M9\Commands\C6_get_available_game_statuses', []);
      if($states['status'] != 0)
        throw new \Exception($states['data']['errormsg']);
      else
        $lottery_game_statuses_db = $states['data']['lottery_game_statuses_db'];

      // 5. Получить палитру цветов для игроков из конфига
      $palette = config('M9.palette');

      // 6. Получить статистическую информацию по классической игре

        // 6.1. Статистика "Наибольшая ставка"
        $classicgame_stats_thebiggestbet = runcommand('\M9\Commands\C55_get_stats_thebiggestbet', [
          "force"     => false,
          "broadcast" => false
        ]);
        if($classicgame_stats_thebiggestbet['status'] != 0)
          throw new \Exception($classicgame_stats_thebiggestbet['data']['errormsg']);

        // 6.2. Статистика "Счастливчик дня"
        $classicgame_stats_luckyoftheday = runcommand('\M9\Commands\C56_get_stats_luckyoftheday', [
          "force"     => false,
          "broadcast" => false
        ]);
        if($classicgame_stats_luckyoftheday['status'] != 0)
          throw new \Exception($classicgame_stats_luckyoftheday['data']['errormsg']);


      // 7. Получить значение USD/RUB
      $rate = runcommand('\M9\Commands\C50_get_rate', [
        'pair' => 'USD/RUB'
      ]);
      if($rate['status'] != 0)
        $rate = 60;
      else if(array_key_exists('data', $rate) && array_key_exists('rate', $rate['data']))
        $rate = $rate['data']['rate'];
      else
        $rate = 60;

      // 8. Получить конфигурацию для системы депозита
      $deposit_configs = call_user_func(function(){

        return [
          'min_skin2accept_price_cents'       => config("M13.min_skin2accept_price_cents") ?: '10',
          'skin_price2accept_spread_in_perc'  => config("M13.skin_price2accept_spread_in_perc") ?: '30',
          'item_type_filters'                 => config("M13.item_type_filters")
        ];

      });

      // 9. Получить данные для модели ежедневной награды аутентиф.пользователя
      $reward = call_user_func(function(){

        // 9.1. Размер ежедневной награды
        $coins = config("M15.daily_coins_payout_num_everyuser");
        if(empty($coins))
          $coins = 10;

        // 9.2. Сколько времени осталось до наступления следующих суток
        $time_until_next_day = call_user_func(function(){

          // 1] Выполнить команду
          $result = runcommand('\M15\Commands\C5_get_time_until_next_day', []);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результат
          return $result['data']['end'];

        });


        // 9.3. Получил ли уже сегодня текущий аутентиф.пользователь свою награду

          // 1] Получить ID текущего пользователя
          $id_user = lib_current_user_id();

          // 2] Если $id_user == -1, вернуть пустую строку
          if($id_user == -1)
            $is_got_reword = "";

          // 3] Иначе, выполнить команду
          else
            $is_got_reword = call_user_func(function(){

              // Выполнить команду
              $result = runcommand('\M15\Commands\C1_get_status', [
                "id_user" => lib_current_user_id()
              ]);
              if($result['status'] != 0)
                throw new \Exception($result['data']['errormsg']);

              // Вернуть результат
              return $result['data']['status']['id_status'];

            });

          // 9.n. Вернуть результат
          return [
            "coins"               => $coins,
            "time_until_next_day" => $time_until_next_day,
            "is_got_reword"       => $is_got_reword
          ];

      });

      // 10. Получить информацию о счётчиках запрашивающего пользователя
      // - Но только, если пользователья на анонимный.
      $counters = runcommand('\M16\Commands\C4_get_counters_data', [
        'id_user' => lib_current_user_id()
      ]);
      if($counters['status'] != 0)
        throw new \Exception($counters['data']['errormsg']);

      // 11. Получить информацию о Ready-выдаче пользователя lib_current_user_id
      $giveaway = call_user_func(function(){

        // 1] Получить информацию о выдаче пользователя
        $giveaway = json_decode(Cache::tags(['m16:cache:ready:personal'])->get('m16:cache:ready:'.lib_current_user_id()), true);

        // 2] Если она пуста, вернуть пустой объект
        if(empty($giveaway))
          return new \stdClass();

        // 3] Иначе, вернуть $giveaway
        return $giveaway;

      });

      // 12. Получить информацию для Nick Promo
      $nickpromo = runcommand('\M17\Commands\C2_get_nick_promo_info', [
        'id_user' => lib_current_user_id()
      ]);
      if($nickpromo['status'] != 0)
        throw new \Exception($nickpromo['data']['errormsg']);

      // 13. Получить информацию для Steam Group Promo
      $steamgrouppromo = runcommand('\M18\Commands\C2_get_steamgroup_promo_info', [
        'id_user' => lib_current_user_id()
      ]);
      if($steamgrouppromo['status'] != 0)
        throw new \Exception($steamgrouppromo['data']['errormsg']);

      // 14. Состоит ли текущий пользователь в группе winners
      $is_in_winners = call_user_func(function() {

        // Состоит ли $user в группе winners
        $is_in_winners = \M5\Models\MD1_users::where('id', lib_current_user_id())
          ->whereHas('groups', function($queue){
            $queue->where('name', 'Winners');
          })->first();

        // Если $user состоит в группе Winners
        if(!empty($is_in_winners)) {
          return 4;
        }

        // Если не состоит
        else {
          return 0;
        }

      });

      // 15. Если пользователь не анонимный, обновить его аватар и ник, если время пришло
      if(!empty($steam_tradeurl_and_user['user'])) {

        // 15.1. Получить last_ava_nick_update_at пользователя
        $last = $steam_tradeurl_and_user['user']->last_ava_nick_update_at;

        // 15.2. Получить из конфига значение how_often2update_users_ava_and_nick
        $how_often = config("M5.how_often2update_users_ava_and_nick") ?: 1440;

        // 15.3. Если с момента $last уже прошло $how_often минут, или $last пуст
        // - Обновить ник и аватар пользователя.
        if(empty($last) || +(\Carbon\Carbon::parse($last)->diffInMinutes(\Carbon\Carbon::now())) >= $how_often) {

          // 1] Обновить ник и аватар пользователя.
          $result = runcommand('\M5\Commands\C75_update_steam_nick_ava_by_id', [
            "id"              => $steam_tradeurl_and_user['user']->id,
            "besides_groups"  => ['']
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Обновить значение $last
          $steam_tradeurl_and_user['user']->last_ava_nick_update_at = \Carbon\Carbon::now();

        }

      }

      // m. Если пользователь не анонимный, записать ему значение в last_visit_at
      if(!empty($steam_tradeurl_and_user['user'])) {
        $steam_tradeurl_and_user['user']->last_visit_at = \Carbon\Carbon::now();
        $steam_tradeurl_and_user['user']->save();
      }

      // N. Вернуть клиенту представление и данные $data
      return View::make($this->packid.'::view', ['data' => json_encode([

        'document_locale'         => r1_get_doc_locale($this->packid),
        'auth'                    => session('auth_cache') ?: '',
        'packid'                  => $this->packid,
        'layoutid'                => $this->layoutid,
        'websocket_server'        => (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ':6001',
        'websockets_channel'      => Session::getId(),
        'steam_tradeurl'          => $steam_tradeurl_and_user['steam_tradeurl'],
        'rooms'                   => $allgamedata['data']['rooms'],
        'choosen_room_id'         => $allgamedata['data']['choosen_room_id'],
        'lottery_game_statuses'   => $lottery_game_statuses_db,
        'palette'                 => $palette,
        "servertime_s"            => \Carbon\Carbon::now()->timestamp,
        "usdrub_rate"             => $rate,
        "public_faq_folder"       => config('M12.public_faq_folder'),
        "deposit_configs"         => $deposit_configs,
        "reward"                  => $reward,
        "classicgame_stats"       => [
          "classicgame_stats_thebiggestbet" => $classicgame_stats_thebiggestbet,
          "classicgame_stats_luckyoftheday" => $classicgame_stats_luckyoftheday,
        ],
        "counters"                => $counters['data'],
        "giveaway"                => $giveaway,
        "nickpromo"               => [
          'coins'   => $nickpromo['data']['coins'],
          'is_paid' => $nickpromo['data']['is_paid']
        ],
        "steamgrouppromo"         => [
          'coins'   => $steamgrouppromo['data']['coins'],
          'is_paid' => $steamgrouppromo['data']['is_paid']
        ],
        "is_in_group" => $is_in_winners

      ]), 'layoutid' => $this->layoutid.'::layout']);


  } // конец getIndex()


  //----------------------------------------//
  // POST-API. Обработка всех POST-запросов //
  //----------------------------------------//
  public function postIndex() {

    //----------------------------------------------------------------------------------//
    // Провести авторизацию прав доступа запрашивающего пользователя к этому интерфейсу //
    //----------------------------------------------------------------------------------//
    // - Если команда для проведения авторизации доступна, и если авторизация включена.
    if(class_exists('\M5\Commands\C66_authorize_access') && config("M5.authorize_access_ison") == true) {

      // Провести авторизацию
      $authorize_results = runcommand('\M5\Commands\C66_authorize_access', ['packid' => $this->packid, 'userid' => lib_current_user_id()]);

      // Если доступ запрещён, вернуть документ с кодом 403
      if($authorize_results['status'] == -1)
        return Response::make("Unfortunately, access to this document is forbidden for you.", 403);

    }

    //------------------------//
    // Обработать POST-запрос //
    //------------------------//

      //------------------------------------------//
      // 1] Получить значение опций key и command //
      //------------------------------------------//
      // - $key       - ключ операции (напр.: D10009:1)
      // - $command   - полный путь команды, которую требуется выполнить
      $key        = Input::get('key');
      $command    = Input::get('command');


      //----------------------------------------//
      // 2] Обработка стандартных POST-запросов //
      //----------------------------------------//
      // - Это около 99% всех POST-запросов.
      if(empty($key) && !empty($command)) {

        // 1. Получить присланные данные

          // Получить данные data
          $data = Input::get('data');   // массив


        // 2. Выполнить команду и получить результаты
        $response = runcommand(

            $command,                   // Какую команду выполнить
            $data,                      // Какие данные передать команде
            lib_current_user_id()       // ID пользователя, от чьего имени выполнить команду

        );


        // 3. Добавить к $results значение timestamp поступления запроса
        $response['timestamp'] = $data['timestamp'];


        // 4. Сформировать ответ и вернуть клиенту
        return Response::make(json_encode($response, JSON_UNESCAPED_UNICODE));

      }


      //------------------------------------------//
      // 3] Обработка нестандартных POST-запросов //
      //------------------------------------------//
      // - Очень редко алгоритм из 2] не подходит.
      // - Например, если надо принять файл.
      // - Тогда $command надо оставить пустой.
      // - А в $key прислать ключ-код номер операции.
      if(!empty($key) && empty($command)) {

        //---------------------------------//
        // Нестандартная операция D10009:1 //
        //---------------------------------//
        // - Безопасная обёртка для команды изменения trade url
        if($key == 'D10009:1') { try {

          //// 1. Получить "Partner ID" и "Token" из торгового URL
          //$partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
          //  "trade_url" => Input::get('data')['steam_tradeurl']
          //]);
          //if($partner_and_token['status'] != 0)
          //  throw new \Exception($partner_and_token['data']['errormsg']);
          //
          //// 2. Получить steamname и steamid по торговому URL
          //$result = runcommand('\M8\Commands\C30_get_steamname_and_steamid_by_tradeurl', [
          //  "id_bot" => \M8\Models\MD1_bots::query()->first()->id,
          //  "partner" => $partner_and_token['data']['partner'],
          //  "token" => $partner_and_token['data']['token']
          //]);
          //if($result['status'] != 0)
          //  throw new \Exception($result['data']['errormsg']);

          // 3. Выполнить команду
          $result = runcommand('\M5\Commands\C70_save_steam_tradeurl', [
            "steam_tradeurl" => Input::get('data')['steam_tradeurl'],
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 4. Вернуть результаты
          return $result;

        } catch(\Exception $e) {
          $errortext = 'Invoking of command D10009:D10009:1 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
          Log::info($errortext);
          write2log($errortext, ['M9', 'D10009:D10009:1']);
          return [
            "status"  => -2,
            "data"    => [
              "errortext" => $errortext,
              "errormsg" => $e->getMessage()
            ]
          ];
        }}

        //---------------------------------//
        // Нестандартная операция D10009:2 //
        //---------------------------------//
        // - Сохранение в куки нового значения для выключателя звука.
        if($key == 'D10009:2') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Сформировать $response
          $response = [
            "status"      => 0,
            "data"        => "",
            "timestamp"   => $data['timestamp']
          ];

          // 3. Провести валидацию входящих параметров
          $validator = r4_validate($data, [
            "is_global_volume_on"          => ["required", "boolean"]
          ]); if($validator['status'] == -1) {
            return [
              "status"  => -2,
              "data"    => [
                "errortext" => $validator['data'],
                "errormsg" => $validator['data']
              ],
              "timestamp"   => $data['timestamp']
            ];
          }

          // 4. Установить новые значения кук
          $cookie_m9_sound_global_ison = cookie()->forever('m9:sound:global:ison', $data['is_global_volume_on'] === false ? "0" : "1");

          // 5. Сформировать ответ и вернуть клиенту
          return Response::make(json_encode($response, JSON_UNESCAPED_UNICODE))
              ->withCookie($cookie_m9_sound_global_ison);

        }

        //---------------------------------//
        // Нестандартная операция D10009:3 //
        //---------------------------------//
        // - Подгрузить указанную страницу истории для classic game, для указанной комнаты
        if($key == 'D10009:3') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Выполнить команду
          $result = runcommand('\M9\Commands\C52_get_history_by_room_and_page', [
            "id_room"   => Input::get('data')['id_room'],
            "page_num"  => Input::get('data')['page_num'],
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:4 //
        //---------------------------------//
        // - Получить ТОП игроков
        if($key == 'D10009:4') {

          // 1. Выполнить команду
          $result = runcommand('\M9\Commands\C54_get_top', []);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:5 //
        //---------------------------------//
        // - Получить FAQ
        if($key == 'D10009:5') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Выполнить команду
          $result = runcommand('\M12\Commands\C4_get_faq', [
            'faq'         => 'csgohap',
            'group_mode'  => 2,                    // Брать в качестве стартовой группу указанную в конфиге
            'group'       => $data['group'],
            'what2return' => $data['what2return']
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:6 //
        //---------------------------------//
        // - Обновить инвентарь аутентифицированного пользователя
        if($key == 'D10009:6') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Получить значения параметров

            // 2.1. force
            $force = call_user_func(function(){

              // 1] Получить входящие параметры
              $data = Input::get('data');

              // 2] Если $data пуста, вернуть false
              if(empty($data)) return false;

              // 3] Если в дата нет force, вернуть false
              if(!array_key_exists('force', $data)) return false;

              // 4] Если force не булево, вернуть false
              $validator = r4_validate($data, [
                "force"              => ["boolean"],
              ]); if($validator['status'] == -1) {
                return false;
              }

              // 5] Вернуть значение force
              return $data['force'];

            });

            // 2.2. steamid
            $steamid = call_user_func(function(){

              // 1] Получить auth_cache
              $auth = json_decode(session('auth_cache'), true);
              if(empty($auth)) return "";

              // 2] Получить пользователя
              if(!array_key_exists('user', $auth)) return "";
              $user = $auth['user'];
              if(empty($user)) return "";

              // 3] Получить steamid пользователя
              if(!array_key_exists('ha_provider_uid', $user)) return "";
              $steamid = $user['ha_provider_uid'];
              if(empty($steamid)) return "";

              // 4] Вернуть результат
              return $steamid;

            });

          // 3. Выполнить команду
          $result = runcommand('\M13\Commands\C3_get_inventory', [
            "force"   => $force,
            "steamid" => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:7 //
        //---------------------------------//
        // - Создать новый трейд с запросом вещей пользователя
        if($key == 'D10009:7') {

          // 1. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 2. Выполнить команду
          $result = runcommand('\M13\Commands\C4_make_trade', [
            'items2bet'       => Input::get('data')['items2bet'],
            'players_steamid' => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:8 //
        //---------------------------------//
        // - Обновить товарные остатки при первом входе в магазин
        if($key == 'D10009:8') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Выполнить команду
          $result = runcommand('\M14\Commands\C4_get_goods', [

          ]);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10009:9 //
        //---------------------------------//
        // - Осуществить покупку, создать новый трейд для доставки вещей покупателю
        if($key == 'D10009:9') {

          // 1. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 2. Выполнить команду
          $result = runcommand('\M14\Commands\C5_buy', [
            'items2buy'       => Input::get('data')['items2buy'],
            'items2order'     => Input::get('data')['items2order'],
            'players_steamid' => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }

        //----------------------------------//
        // Нестандартная операция D10009:10 //
        //----------------------------------//
        // - Пользователь запрашивает бесплатные монеты (ежедневная награда)
        if($key == 'D10009:10') {

          // 1. Получить ID текущего пользователя
          $id_user = lib_current_user_id();

          // 2. Если $id_user == -1, вернуть ошибку "2"
          if($id_user == -1)
            return [
              "status"  => -2,
              "data"    => [
                "errortext" => "",
                "errormsg"  => "2"
              ]
            ];

          // 3. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 4. Выполнить команду
          $result = runcommand('\M15\Commands\C3_get_freecoins', [
            "id_user" => $id_user,
            "steamid" => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }

        //----------------------------------//
        // Нестандартная операция D10009:11 //
        //----------------------------------//
        // - Пользователь запрашивает создание оффера по своей выдаче
        if($key == 'D10009:11') {

          // 1. Получить ID текущего пользователя
          $id_user = lib_current_user_id();

          // 2. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 3. Если $id_user == -1, или нет steamid, вернуть ошибку "2"
          if($id_user == -1 || empty($steamid))
            return [
              "status"  => -2,
              "data"    => [
                "errortext" => "",
                "errormsg"  => "2"
              ]
            ];

          // 4. Выполнить команду
          runcommand('\M16\Commands\C11_create_giveaway_offer', [
            "id_user" => lib_current_user_id(),
            "steamid" => $steamid
          ], 0, ['on'=>true, 'name'=>'m16_processor_hard']);

          // n. Вернуть результаты
          return [
            "status"  => 0,
            "data"    => ""
          ];

        }

        //----------------------------------//
        // Нестандартная операция D10009:12 //
        //----------------------------------//
        // - Попытка применить nick promo.
        if($key == 'D10009:12') {

          // 1. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 2. Выполнить команду
          $result = runcommand('\M17\Commands\C1_apply_nick_promo', [
            "id_user" => lib_current_user_id(),
            "steamid" => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }

        //----------------------------------//
        // Нестандартная операция D10009:13 //
        //----------------------------------//
        // - Попытка применить steam group promo.
        if($key == 'D10009:13') {

          // 1. Получить steamid пользователя
          $steamid = call_user_func(function(){

            // 1] Получить auth_cache
            $auth = json_decode(session('auth_cache'), true);
            if(empty($auth)) return "";

            // 2] Получить пользователя
            if(!array_key_exists('user', $auth)) return "";
            $user = $auth['user'];
            if(empty($user)) return "";

            // 3] Получить steamid пользователя
            if(!array_key_exists('ha_provider_uid', $user)) return "";
            $steamid = $user['ha_provider_uid'];
            if(empty($steamid)) return "";

            // 4] Вернуть результат
            return $steamid;

          });

          // 2. Выполнить команду
          $result = runcommand('\M18\Commands\C1_apply_steamgroup_promo', [
            "id_user" => lib_current_user_id(),
            "steamid" => $steamid
          ]);

          // n. Вернуть результаты
          return $result;

        }





      }






  } // конец postIndex()


}?>