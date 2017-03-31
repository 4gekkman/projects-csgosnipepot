<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер M-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace M8;


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
        $packid = "M8";

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
          //'$schedule->command("m8:update_bots_inventory_count")->cron("*/10 * * * * *");',
          //'$schedule->command("m8:update_bots_authorization_statuses")->cron("*/10 * * * * *");',
          //'$schedule->command("m8:update_bots_apikeys")->hourly();',
          //'$schedule->command("m8:update_prices_all")->dailyAt("04:00");'
        ];

      //----------------------------------------------------//
      // 2. Регистрация консольных команд документов модуля //
      //----------------------------------------------------//

        // Список команд для регистрации
        // Пример: '\M1\Console\T1_parseapp',
        $commands = [
          '\M8\Console\T1_sync',
          '\M8\Console\T2_getinventory',
          '\M8\Console\T3_bot_get_mobile_code',
          '\M8\Console\T4_bot_get_sessid_steamid',
          '\M8\Console\T5_bot_login',
          '\M8\Console\T6_update_bots_inventory_count',
          '\M8\Console\T7_update_bots_authorization_statuses',
          '\M8\Console\T8_bot_set_apikey',
          '\M8\Console\T9_update_bots_apikeys',
          '\M8\Console\T10_update_db_lists',
          '\M8\Console\T11_update_prices_csgofast',
          '\M8\Console\T12_update_prices_steammarket',
          '\M8\Console\T13_get_price_steammarket',
          '\M8\Console\T14_get_final_items_prices',
          '\M8\Console\T15_update_prices_all',
          '\M8\Console\T16_gettradeoffersviaapi',
          '\M8\Console\T17_getsteamtime',
          '\M8\Console\T18_fetch_confirmations',
          '\M8\Console\T19_get_tradeoffer_via_api',
          '\M8\Console\T20_check_escrow_hold_days',
          '\M8\Console\T21_get_trade_offers_via_html',
          '\M8\Console\T22_new_trade_offer',
          '\M8\Console\T23_get_partner_and_token_from_trade_url',
          '\M8\Console\T24_cancel_trade_offer',
          '\M8\Console\T25_accept_trade_offer',
          '\M8\Console\T26_decline_trade_offer',
          '\M8\Console\T27_get_steamname_and_steamid_by_tradeurl',
          '\M8\Console\T28_add_new_bot',
          '\M8\Console\T29_delete_bot',
          '\M8\Console\T30_rename_group',
          '\M8\Console\T31_update_items_quality_indb'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }