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
        'name'        => 'Новый документ',
        'description' => 'New document',
      ],
      'EN' => [
        'name'        => 'Это D-пакет',
        'description' => 'It is D-package',
      ]
    ],

    //--------------------------------------------------------------------------------------//
    // 5] Включить ли режим разработки?                                                     //
    //    - Консольные команды m1:allrespublish и m1:minify выполняются при каждом запросе. //
    //    - Удобен и необходим во время зазработки, вреден в продакшн.                      //
    //    Is development mode switched on                                                   //
    //    - Console Commands m1:allrespublish and m1:minify will invoke every request.      //
    //    - Convenient mode for development, but harmful for production.                    //
    //--------------------------------------------------------------------------------------//
    'development_mode' => true,

    //------------------------//
    // 6] GitHub OAuth2 Token //
    //------------------------//
    'github_oauth2' => '',

];
