<?php
////======================================================////
////																										  ////
////            Композер представления шаблона					  ////
////																											////
////======================================================////


  //-------------------------------------------//
  // Пространство имён композера представления //
  //-------------------------------------------//

    namespace L10001;

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
View::composer('L10001::layout', function($view) {

  // 1. Получить и приготовить данные




  // n. Передать необходимые данные шаблону

    // n.1. Данные меню
    // $view->with('menu', $menudata);


});