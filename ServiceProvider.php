<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//
  // - Например: M1

    namespace M1;


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
        $packid = "M1";

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
          '//$schedule->command("m1:run_light")->cron("*/10*****");'
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        $commands = [
          '\M1\Console\T1_parseapp',
          '\M1\Console\T2_sp_regs_update',
          '\M1\Console\T3_allrespublish',
          '\M1\Console\T4_m_dbs_update',
          '\M1\Console\T6_afterupdate',
          '\M1\Console\T7_new',
          '\M1\Console\T9_del',
          '\M1\Console\T10_release',
          '\M1\Console\T11_mdlw_cfgs_update',
          '\M1\Console\T12_m_schedules_update',
          '\M1\Console\T13_workbench_sync',
          '\M1\Console\T14_workbench_sync_all',
          '\M1\Console\T15_clear_interpack_rels',
          '\M1\Console\T16_suf',
          '\M1\Console\T17_suf_collect_deps',
          '\M1\Console\T18_suf_install_deps',
          '\M1\Console\T19_suf_check_deps',
          '\M1\Console\T20_suf_bower_process',
          '\M1\Console\T21_suf_dlw_process',
          '\M1\Console\T22_suf_get_deptrees',
          '\M1\Console\T23_suf_bower_automain',
          '\M1\Console\T24_suf_blade_integrate',
          '\M1\Console\T25_suf_watch_setting',
          '\M1\Console\T26_queue',
          '\M1\Console\T27_queue_clear',
          '\M1\Console\T28_run',
          '\M1\Console\T29_run_light',
          '\M1\Console\T30_github_check',
          '\M1\Console\T31_github_new',
          '\M1\Console\T32_github_del',
          '\M1\Console\T33_clone'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }