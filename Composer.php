<?php
////======================================================////
////																										  ////
////            Композер представления шаблона					  ////
////																											////
////======================================================////


  //-------------------------------------------//
  // Пространство имён композера представления //
  //-------------------------------------------//

    namespace L10000;

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
View::composer('L10000::layout', function($view) {

  // 1. Получить из конфига данные о пунктах меню
  $menu = config("L10000.menuitems");

  // 2. Получить URL и Base URL запроса
  $url = Request::url();
  $root_url = (\Request::secure() ? "https://" : "http://") . (\Request::getHost());

  // 3. Узнать номер запрашиваемого пункта меню
  $menu_item_number = call_user_func(function() USE ($menu, $url) {

    // 3.1. Пробежаться по $menu и искать asset в $utl
    // - Вернуть ID соотв.пункта $menu, если совпадение найдено.
    foreach($menu as $item) {
      if(preg_match("!".$item['asset']."!ui", $url) > 0)
        return $item['id'];
    }

    // 3.2. Если ничего не найдено, вернуть 1
    return 1;

  });

  // n. Передать необходимые шаблону данные
  $view->with('data', json_encode([
    "menu"              => $menu,
    "menu_item_number"  => $menu_item_number,
    "root_url"          => $root_url,
    "port"              => \Request::getPort()
  ], JSON_UNESCAPED_UNICODE));


});