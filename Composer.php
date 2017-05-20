<?php
////======================================================////
////																										  ////
////            Композер представления шаблона					  ////
////																											////
////======================================================////


  //-------------------------------------------//
  // Пространство имён композера представления //
  //-------------------------------------------//

    namespace L10004;

  //---------------------------------//
  // Подключение необходимых классов //
  //---------------------------------//

    // Ресурсы фреймворка
    use Illuminate\Support\Facades\App,
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

    // Собственные классы



////==========================================================//*/
View::composer('L10004::layout', function($view) {

  // a] Сделать запись в лог
  file_put_contents(env('LOG_ACCESS_L10004'), json_encode([
    'ip'        => \Request::ip(),
    'id_user'   => lib_current_user_id(),
    'datetime'  => \Carbon\Carbon::now()->toDateTimeString()
  ], JSON_UNESCAPED_UNICODE).PHP_EOL , FILE_APPEND | LOCK_EX);

  // 1. Получить все сегменты-параметры URI запроса в виде массива значений
  $parameters = array_values(Route::current()->parameters());

  // 2. Получить базовый URI за вычетом параметров
  $baseuri = call_user_func(function() USE ($parameters) {

    // 1] Получить все сегменты
    $segments = \Request::segments();

    // 2] Подготовить переменную для базового URI
    $result = '/';

    // 3] Наполнить $result
    // - Кроме count($parameters) последних значений
    for($i=0; $i<(count($segments) - count($parameters)); $i++) {
      $result = $result . $segments[$i] . '/';
    }

    // 4] Удалить / в конце, но только если $result != '/'
    if($result != '/')
      $result = preg_replace("#/$#ui", "", $result);

    // n] Вернуть результат
    return $result;

  });

  // 3. Получить последние N сообщений чата

    // 3.1. Извлечь из конфига информацию о комнате чата с именем 'main'
    $chat_main = config("M10.rooms.main");

    // 3.2. Получить последние N сообщений
    $messages = runcommand('\M10\Commands\C4_get_messages', [
      "room_name"   => "dashboard_common",
      "number"      => $chat_main['max_messages'],
      "active_only" => "1"
    ]);
    if($messages['status'] != 0)
      throw new \Exception($messages['data']['errormsg']);

  // 4. Получить query string, и спарсить в массив
  $querystring = \Request::getQueryString();
  parse_str($querystring, $querystring_arr);

  // 5. Получить содержимое главного меню
  $mainmenu = config("L10004.mainmenu");

  // n. Передать необходимые шаблону данные
  $view->with('data', json_encode([
    'auth'                  => session('auth_cache') ?: '',
    'request'               => [
      "secure"      => \Request::secure() ? "https://" : "http://",
      "host"        => \Request::getHost(),
      "port"        => \Request::getPort(),
      "baseuri"     => $baseuri,
      "querystring" => $querystring,
      "qs_array"    => $querystring_arr
    ],
    'parameters' =>         $parameters,
    'websocket_server'      => (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ':6001',
    'websockets_channel'    => Session::getId(),
    'logged_in_steam_users' => Redis::get('active_connections_number'),
    'messages'              => $messages,
    'chat_main'             => $chat_main,
    'servertime_s'          => \Carbon\Carbon::now()->timestamp,
    'asset_url'             => asset(''),
    'mainmenu'              => $mainmenu
  ], JSON_UNESCAPED_UNICODE));

});