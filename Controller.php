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
 *                  POST-API2   D10009:2
 *
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



    //-----------------------//
    // Обработать GET-запрос //
    //-----------------------//

      // 1. Провести авто-инициацию некоторых таблиц-справочников в БД
      $result = runcommand('\M9\Commands\C39_init_db_references', []);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 2. Получить steam_tradeurl, если он есть
      $steam_tradeurl = call_user_func(function(){

        // 1] Извлечь auth_cache
        $auth = json_decode(session('auth_cache'), true);

        // 2] Получить ID запрашивающего пользователя
        if(!array_key_exists('user', $auth) || !array_key_exists('id', $auth['user']))
          return "";
        $id = $auth['user']['id'];

        // 3] Попробовать найти пользователя с $id
        $user = \M5\Models\MD1_users::find($id);
        if(empty($user))
          return "";

        // 4] Вернуть steam_tradeurl
        return $user->steam_tradeurl;

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
      $classicgame_statistics = runcommand('\M9\Commands\C40_get_statistics', [
        "force" => false
      ]);
      if($classicgame_statistics['status'] != 0)
        throw new \Exception($classicgame_statistics['data']['errormsg']);

      // 7. Получить значение USD/RUB
      $rate = runcommand('\M9\Commands\C50_get_rate', [
        'pair' => 'USD/RUB'
      ]);
      if($result['status'] != 0)
        $rate = 60;
      else
        $rate = $rate['data']['rate'];

      // N. Вернуть клиенту представление и данные $data
      return View::make($this->packid.'::view', ['data' => json_encode([

        'document_locale'         => r1_get_doc_locale($this->packid),
        'auth'                    => session('auth_cache') ?: '',
        'packid'                  => $this->packid,
        'layoutid'                => $this->layoutid,
        'websocket_server'        => (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ':6001',
        'websockets_channel'      => Session::getId(),
        'steam_tradeurl'          => $steam_tradeurl,
        'rooms'                   => $allgamedata['data']['rooms'],
        'choosen_room_id'         => $allgamedata['data']['choosen_room_id'],
        'lottery_game_statuses'   => $lottery_game_statuses_db,
        'palette'                 => $palette,
        "servertime_s"            => \Carbon\Carbon::now()->timestamp,
        "classicgame_statistics"  => $classicgame_statistics,
        "usdrub_rate"             => $rate,

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


      }






  } // конец postIndex()


}?>