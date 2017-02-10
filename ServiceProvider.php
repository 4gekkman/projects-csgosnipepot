<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace M9;


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
        $packid = "M9";

        // 2] Пары 'событие-обработчик' документов M-пакета //
        //--------------------------------------------------//
        $pairs2register = [
          'M9\EventHandlers\H1_ticks' => 'R2\Event'
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
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M9\Console\T1_rooms',
          '\M9\Console\T2_create_new_room',
          '\M9\Console\T3_sync_round_statuses',
          '\M9\Console\T4_sync_rooms',
          '\M9\Console\T5_processor',
          '\M9\Console\T6_cancel_the_active_bet',
          '\M9\Console\T7_processor_wins',
          '\M9\Console\T8_pause',
          '\M9\Console\T9_update_tor_ip',
          '\M9\Console\T10_check_active_offers_type2',
          '\M9\Console\T11_process_active_offers_type2',
          '\M9\Console\T12_decline_offer_type2',
          '\M9\Console\T13_accept_offer_type2',
          '\M9\Console\T14_offers_toothcomb_type2',
          '\M9\Console\T15_assetid_bots_tracking',
          '\M9\Console\T16_assetid_wins_tracking',
          '\M9\Console\T17_wins_autopayouts',
          '\M9\Console\T18_update_and_translate_stats',
          '\M9\Console\T19_get_rate',
          '\M9\Console\T20_update_history_cache',
          '\M9\Console\T21_get_history_by_room_and_page',
          '\M9\Console\T22_update_top_cache',
          '\M9\Console\T23_get_top'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }