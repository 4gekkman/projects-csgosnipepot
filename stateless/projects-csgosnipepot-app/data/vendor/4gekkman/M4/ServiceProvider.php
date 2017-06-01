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
          'M4\EventHandlers\H1_update' => 'R2\Event'
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
        $this->app['router']->pushMiddlewareToGroup('web', $packid.'\Middlewares\AfterMiddleware');
        $this->app['router']->pushMiddlewareToGroup('web', $packid.'\Middlewares\BeforeMiddleware');

        // 3] Регистрация пар 'событие-обработчик' документом M-пакета документа "" //
        //--------------------------------------------------------------------------//
        foreach($pairs2register as $handler => $event) {
          $events->listen($event, $handler);
        }

        // 4] Регистрация файлов локализации D-пакета //
        //--------------------------------------------//
        // - Доступ из php  : trans('m1.welcome')
        // - Доступ из blade: {{ trans('m1.welcome') }}

          // RU //
          //----//
          $this->publishes([
              __DIR__.'/Localization/ru/localization.php' => resource_path('lang/ru/'.$packid.'.php'),
          ], 'M');

          // EN //
          //----//
          $this->publishes([
              __DIR__.'/Localization/en/localization.php' => resource_path('lang/en/'.$packid.'.php'),
          ], 'M');

        // 5] Подключить routes.php //
        //--------------------------//
        //require __DIR__.'/routes.php';

          // 1] Получить адрес для routes.php из конфига относительно корня проекта
          $routesphp_path = config("M4.routesphp_path") ?: 'vendor/4gekkman/M4';

          // 2] Если routes.php не существует по указаному пути, создать
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path($routesphp_path)]);
          $storage = new \Illuminate\Filesystem\FilesystemManager(app());

          // 3] Если файла routes.php не существует, создать
          if(!$storage->exists('routes.php')) {

            // 3.1] Подготовить контент файла
            $contents = "<?php".PHP_EOL.PHP_EOL;
            $contents = $contents . "  // routesphp_sync: start".PHP_EOL;
            $contents = $contents . "  // routesphp_sync: stop".PHP_EOL.PHP_EOL;

            // 3.2] Разместить файл по адресу $routesphp_path
            $storage->put('routes.php', $contents);

          }

          // 4] Подключить routes.php
          require base_path($routesphp_path).'/routes.php';

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
        // - Статья про cron-формат: http://www.nncron.ru/help/EN/working/cron-format.htm
        // - Примеры:
        //
        //    $schedule->command("m1:parseapp")->withoutOverlapping()->hourly();                        // каждый час
        //    $schedule->command("m1:parseapp")->withoutOverlapping()->cron("0,15,30,45 * * * * *");    // каждые 15 минут
        //
        $add2schedule = [
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M4\Console\T1_update',
          '\M4\Console\T2_list',
          '\M4\Console\T3_check',
          '\M4\Console\T4_new',
          '\M4\Console\T5_del',
          '\M4\Console\T6_switch',
          '\M4\Console\T7_routesphp_sync'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }