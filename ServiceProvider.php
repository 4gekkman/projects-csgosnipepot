<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace M5;


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
        $packid = "M5";

        // 2] Пары 'событие-обработчик' документов M-пакета //
        //--------------------------------------------------//
        $pairs2register = [
          'M5\EventHandlers\H1_update' => 'R2\Event',
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
          '$schedule->command("m1:email_cleartable")->withoutOverlapping()->hourly();',
          '$schedule->command("m1:phone_cleartable")->withoutOverlapping()->hourly();',
          '$schedule->command("m1:delnotverifiedemail")->withoutOverlapping()->hourly();',
          '$schedule->command("m1:delnotverifiedphone")->withoutOverlapping()->hourly();',
          '$schedule->command("m1:auth_clear_expired")->withoutOverlapping()->daily();',
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M5\Console\T1_update',
          '\M5\Console\T2_switch',
          '\M5\Console\T3_checkanon',
          '\M5\Console\T4_checkadmins',
          '\M5\Console\T5_users',
          '\M5\Console\T6_groups',
          '\M5\Console\T7_privileges',
          '\M5\Console\T8_tags',
          '\M5\Console\T9_new',
          '\M5\Console\T10_del',
          '\M5\Console\T11_change',
          '\M5\Console\T12_attach',
          '\M5\Console\T13_detach',
          '\M5\Console\T14_restore',
          '\M5\Console\T15_getuserprivs',
          '\M5\Console\T16_getgroupprivs',
          '\M5\Console\T17_email_cleartable',
          '\M5\Console\T18_phone_cleartable',
          '\M5\Console\T19_delnotverifiedemail',
          '\M5\Console\T20_delnotverifiedphone',
          '\M5\Console\T21_get_auth_limit',
          '\M5\Console\T22_logout',
          '\M5\Console\T23_auth_clear_expired'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }