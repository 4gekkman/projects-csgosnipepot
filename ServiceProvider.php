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
          '$schedule->command("m8:update_bots_inventory_count")->withoutOverlapping()->everyTenMinutes();',
          '$schedule->command("m8:update_bots_authorization_statuses")->withoutOverlapping()->everyTenMinutes();',
          '$schedule->command("m8:update_bots_apikeys")->withoutOverlapping()->everyTenMinutes();',
          '$schedule->command("m8:update_prices_all")->withoutOverlapping()->twiceDaily(1, 13);',
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
          '\M8\Console\T18_fetch_confirmations'
        ];

        // Регистрация команд в методе register
        $this->commands($commands);

    }

  }