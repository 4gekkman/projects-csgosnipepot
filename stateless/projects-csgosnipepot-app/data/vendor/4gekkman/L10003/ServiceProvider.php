<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер L-пакета					      ////
////																												////
////========================================================////


  //-------------------------------------//
  // Пространство имён сервис-провайдера //
  //-------------------------------------//

    namespace L10003;


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

        // 1] ID пакета //
        //--------------//
        $packid = "L10003";

      //----------------------------------//
      // 2. Код (руками не редактировать) //
      //----------------------------------//

        // 1] Публикация публичных ресурсов документа в Public проекта //
        //-------------------------------------------------------------//
        // Пример: L10003/Public ---> Public/L10003
        // $this->publishes([
        //     __DIR__.'/Public' => base_path('Public/'.basename(__DIR__)),
        // ], 'L');

        // 2] Регистрация представления документа в приложении //
        //-----------------------------------------------------//
        // Шаблон ключа:          [id пакета]
        // Пример ключа:          L10003
        // Пример использования:  L10003::layout
        $this->loadViewsFrom(__DIR__, basename(__DIR__));

        // 3] Публикация настроек пакета в config проекта //
        //------------------------------------------------//
        if(!file_exists(config_path($packid.'.php'))) {
          $this->publishes([
              __DIR__.'/settings.php' =>
              config_path($packid.'.php'),
          ], 'L');
        }

        // 4] Регистрация файлов локализации L-пакета //
        //--------------------------------------------//
        // - Доступ из php  : trans('l10003.welcome')
        // - Доступ из blade: {{ trans('l10003.welcome') }}

          // RU //
          //----//
          $this->publishes([
              __DIR__.'/Localization/ru/localization.php' => resource_path('lang/ru/'.$packid.'.php'),
          ], 'L');

          // EN //
          //----//
          $this->publishes([
              __DIR__.'/Localization/en/localization.php' => resource_path('lang/en/'.$packid.'.php'),
          ], 'L');

    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {

      //---------------------------------------------//
      // Регистрация композера представления шаблона //
      //---------------------------------------------//
      require __DIR__.'/Composer.php';

    }

  }