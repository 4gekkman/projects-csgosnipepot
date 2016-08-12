<?php

return [

    //---------------------------------------------------------------------------//
    // 1] История обновлений базы данных пакета. Не редактировать вручную.       //
    //    History of updates of database of the package. Don't edit it manually. //
    //---------------------------------------------------------------------------//
    'updateshistory'    => [],

    //-------------------------------------------------------------------------//
    // 2] История обновлений конфига пакета. Не редактировать вручную.         //
    //    History of updates of config of the package. Don't edit it manually. //
    //-------------------------------------------------------------------------//
    'cnfupdshistory'    => [],

    //-----------------//
    // 3] Локализация  //
    //    localization //
    //-----------------//

      // 3.1] Поддерживаемые пакетом локали | The locales that are supported by the package //
      //------------------------------------------------------------------------------------//
      'locales' => ['RU', 'EN'],

      // 3.2] Выбранная локаль | Chosen locale //
      //---------------------------------------//
      // - If the value is empty, uses config('app.locale')
      // - Else if config('app.locale') not in 'locales', uses 1-st of 'locales'
      // - Else if 1-st of 'locales' is empty, uses 'RU'.
      'locale' => '',

    //----------------------------------------//
    // 4] Имя и описание пакета               //
    //    Name and description of the package //
    //----------------------------------------//
    'aboutpack' => [
      'RU' => [
        'name'        => "Ticks",
        'description' => "This M-package make ticks every N seconds and translate it via event system",
      ],
      'EN' => [
        'name'        => "Ticks",
        'description' => "This M-package make ticks every N seconds and translate it via event system",
      ]
    ],

    //--------------//
    // 5] Настройки //
    //    Settings  //
    //--------------//

      // 5.1] Период тиков в миллисекундах | Ticks perios in milliseconds //
      //------------------------------------------------------------------//
      // - По умолчанию 1000 миллисекунд (1 секунда).
      "ticks_period_ms" => 1000,

      // 5.2] Время мониторинга наличия тиков стартером в секундах | Starter's ticks existence monitoring time in seconds //
      //------------------------------------------------------------------------------------------------------------------//
      // - По умолчанию 10 секунд.
      "ticks_monitoring_s" => 10,

      // 5.3] Включена ли система | Is system turned on //
      //------------------------------------------------//
      // - 'on' or 'off'.
      "is_system_on" => 'on',



];
