<?php
////======================================================////
////																										  ////
////            Композер представления шаблона					  ////
////																											////
////======================================================////


  //-------------------------------------------//
  // Пространство имён композера представления //
  //-------------------------------------------//

    namespace L10003;

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
View::composer('L10003::layout', function($view) {

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
      $result = $result . $segments[$i];
    }

    // n] Вернуть результат
    return $result;

  });

  // 3. Получить последние N сообщений чата

    // 3.1. Извлечь из конфига информацию о комнате чата с именем 'main'
    $chat_main = config("M10.rooms.main");

    // 3.2. Получить последние N сообщений
    $messages = runcommand('\M10\Commands\C4_get_messages', [
      "room_name"   => "main",
      "number"      => $chat_main['max_messages'],
      "active_only" => "1"
    ]);
    if($messages['status'] != 0)
      throw new \Exception($messages['data']['errormsg']);

  // 4. Получить значения кук, связанных со звуком

    // 4.1. Expanded
    $m9_sound_global_ison = Cookie::get('m9:sound:global:ison');
    if(''.$m9_sound_global_ison !== "0" && ''.$m9_sound_global_ison !== "1") $m9_sound_global_ison = true;
    else {
      if($m9_sound_global_ison == 0) $m9_sound_global_ison = false;
      if($m9_sound_global_ison == 1) $m9_sound_global_ison = true;
    }

  // n. Передать необходимые шаблону данные
  $view->with('data', json_encode([
    'auth'                  => session('auth_cache') ?: '',
    'request'               => [
      "secure"  => \Request::secure() ? "https://" : "http://",
      "host"    => \Request::getHost(),
      "port"    => \Request::getPort(),
      "baseuri" => $baseuri
    ],
    'parameters' =>         $parameters,
    'websocket_server'      => (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ':6001',
    'websockets_channel'    => Session::getId(),
    'logged_in_steam_users' => Redis::get('active_connections_number'),
    'messages'              => $messages,
    'chat_main'             => $chat_main,
    'servertime_s'          => \Carbon\Carbon::now()->timestamp,
    'm9_sound_global_ison'  => $m9_sound_global_ison
  ], JSON_UNESCAPED_UNICODE));


});