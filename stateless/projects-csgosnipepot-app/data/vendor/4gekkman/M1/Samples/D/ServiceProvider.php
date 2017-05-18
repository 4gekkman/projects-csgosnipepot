<?php 
////========================================================////
////																											  ////
////               Сервис-провайдер D-пакета					      ////
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

        // 1] ID пакета //
        //--------------//
        $packid = "PARAMpackfullnamePARAM";

      //----------------------------------//
      // 2. Код (руками не редактировать) //
      //----------------------------------//

        // 1] Публикация публичных ресурсов документа в Public проекта //
        //-------------------------------------------------------------//
        // Пример: PARAMpackfullnamePARAM/Public ---> Public/PARAMpackfullnamePARAM
        // $this->publishes([
        //     __DIR__.'/Public' => base_path('Public/'.basename(__DIR__)),
        // ], 'D');

        // 2] Регистрация представления документа в приложении //
        //-----------------------------------------------------//
        // Шаблон ключа:          [id пакета]
        // Пример ключа:          PARAMpackfullnamePARAM
        // Пример использования:  PARAMpackfullnamePARAM::view
        $this->loadViewsFrom(__DIR__, basename(__DIR__));

        // 3] Публикация настроек D-пакета в config проекта //
        //--------------------------------------------------//
        if(!file_exists(config_path($packid.'.php'))) {
          $this->publishes([
              __DIR__.'/settings.php' =>
              config_path($packid.'.php'),
          ], 'D');
        }

        // 4] Регистрация файлов локализации D-пакета //
        //--------------------------------------------//
        // - Доступ из php  : trans('PARAMpackfullname_strtolowerPARAM.welcome')
        // - Доступ из blade: {{ trans('PARAMpackfullname_strtolowerPARAM.welcome') }}

          // RU //
          //----//
          $this->publishes([
              __DIR__.'/Localization/ru/localization.php' => resource_path('lang/ru/'.$packid.'.php'),
          ], 'D');

          // EN //
          //----//
          $this->publishes([
              __DIR__.'/Localization/en/localization.php' => resource_path('lang/en/'.$packid.'.php'),
          ], 'D');

    }

    //--------------------------------------------------//
    // Register - регистрация связей в сервис-контейнер //
    //--------------------------------------------------//
    public function register()
    {}

  }