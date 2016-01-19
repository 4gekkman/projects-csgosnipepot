<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace PARAMpackfullnamePARAM;


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

      //--------------//
      // 1. Параметры //
      //--------------//

        // 1] ID M-пакета //
        //----------------//
        $packid = "PARAMpackfullnamePARAM";

        // 2] Пары 'событие-обработчик' документов M-пакета //
        //--------------------------------------------------//
        $pairs2register = [

        ];

      //----------------------------------//
      // 2. Код (руками не редактировать) //
      //----------------------------------//

        // 1] Публикация настроек M-пакета в config проекта //
        //--------------------------------------------------//
        if(!file_exists(config_path($packid.'.php'))) {
          $this->publishes([
              __DIR__.'/settings.php' =>
              config_path($packid.'.php'),
          ], 'M');
        }

        // 2] Регистрация Before- и After-middleware M-пакета //
        //----------------------------------------------------//
        $kernel->pushMiddleware($packid.'\Middlewares\AfterMiddleware');
        $kernel->pushMiddleware($packid.'\Middlewares\BeforeMiddleware');

        // 3] Регистрация пар 'событие-обработчик' документом M-пакета документа "" //
        //--------------------------------------------------------------------------//
        foreach($pairs2register as $event => $handler) {
          $events->listen($event, $handler);
        }

    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {

      //-------------------------------------------------//
      // Регистрация консольных команд документов модуля //
      //-------------------------------------------------//

        // Список команд для регистрации
        // //'\M1\Documents\Main\Console\T1_parse',
        $commands = [

        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }