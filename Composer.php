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
  ], JSON_UNESCAPED_UNICODE));


});