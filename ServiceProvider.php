<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер R-пакета 					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace R4;


  //---------------------------------//
  // Подключение необходимых классов //
  //---------------------------------//
  use Illuminate\Support\ServiceProvider as BaseServiceProvider,
      Illuminate\Contracts\Events\Dispatcher as DispatcherContract,
      Illuminate\Support\Facades\Validator,
      Illuminate\Support\Facades\Event;


  //------------------//
  // Сервис-провайдер //
  //------------------//
  class ServiceProvider extends BaseServiceProvider {

    //------//
    // Boot //
    //------//
    public function boot(DispatcherContract $events, \Illuminate\Contracts\Http\Kernel $kernel) {

      //-------------------------------------------------//
      // Регистрация библиотеки хелперов php_helpers.php //
      //-------------------------------------------------//
      require __DIR__ . '/php_helpers.php';

      //----------------------------------------//
      // Регистрация кастомных правил валидации //
      //----------------------------------------//
      /**
       *
       *  r4_numpos         | must be a positive integer
       *  r4_numnn          | must be not negative positive integer
       *
       *
       */

        //===========//
        // r4_numpos //
        //===========//
        Validator::extend('r4_numpos', function($attribute, $value, $parameters) {

          return preg_match("/^[1-9]+[0-9]*$/ui", $value);

        }, ":attribute must be a positive integer");


        //==========//
        // r4_numnn //
        //==========//
        Validator::extend('r4_numnn', function($attribute, $value, $parameters) {

          return preg_match("/^[0-9]+$/ui", $value);

        }, ":attribute must be not negative positive integer");


        //============//
        // r4_defined //
        //============//
        Validator::extend('r4_defined', function($attribute, $value, $parameters) {

          return isset($value);

        }, ":attribute must be not undefined");


    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {}

  }