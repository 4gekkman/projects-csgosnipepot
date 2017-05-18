<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер R-пакета 					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//
  // - По большому счёту, надо указать id модуля.

    namespace R1;


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


      //----------------------------------------------------//
      // 1. Регистрация библиотеки хелперов php_helpers.php //
      //----------------------------------------------------//
      require __DIR__ . '/php_helpers.php';


    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {}

  }