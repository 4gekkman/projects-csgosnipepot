<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace M4;


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
        $packid = "M4";

        // 2] Пары 'событие-обработчик' документов M-пакета //
        //--------------------------------------------------//
        $pairs2register = [
          'R2\Event' => 'M4\EventHandlers\H1_update',
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

        // 4] Подключить routes.php //
        //--------------------------//
        require __DIR__.'/routes.php';

    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {

      //-----------------------------------------------------//
      // 1. Добавление консольных команд в планировщик задач //
      //-----------------------------------------------------//

        // Список подготовленных для добавление в планировщик строк
        // Пример: $schedule->command("m1:parseapp")->withoutOverlapping()->hourly();
        $add2schedule = [
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M4\Console\T2_list',
          '\M4\Console\T3_check',
          '\M4\Console\T4_new',
          '\M4\Console\T5_del',
          '\M4\Console\T1_call4update',
          '\M4\Console\T6_switch'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }