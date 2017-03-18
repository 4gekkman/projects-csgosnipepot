<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace M14;


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
        $packid = "M14";

        // 2] Пары 'событие-обработчик' документов M-пакета //
        //--------------------------------------------------//
        $pairs2register = [
          'M14\EventHandlers\H1_goods_update' => 'R2\Event',
          'M14\EventHandlers\H2_ticks' => 'R2\Event'
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
          '$schedule->command("m14:update_all_bots_goods")->cron("*/15 * * * * *");',
          '$schedule->command("m14:update_tos_goods")->cron("*/15 * * * * *");',
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M14\Console\T1_update_bot_goods',
          '\M14\Console\T2_update_all_bots_goods',
          '\M14\Console\T3_all_goods_update',
          '\M14\Console\T4_get_goods',
          '\M14\Console\T5_buy',
          '\M14\Console\T6_processor',
          '\M14\Console\T7_update_cache',
          '\M14\Console\T8_clearing',
          '\M14\Console\T9_make_paid_trades',
          '\M14\Console\T10_active_offers_expiration_tracking',
          '\M14\Console\T11_cancel_the_active_trade',
          '\M14\Console\T12_cancel_the_active_trade_dbpart',
          '\M14\Console\T13_active_offers_tracking',
          '\M14\Console\T14_active_to_accepted',
          '\M14\Console\T15_offers_toothcomb',
          '\M14\Console\T16_subtract_goods',
          '\M14\Console\T17_add_goods',
          '\M14\Console\T18_update_tos_goods',
          '\M14\Console\T19_get_tos_goods',
          '\M14\Console\T20_to_order_skins_processing'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }