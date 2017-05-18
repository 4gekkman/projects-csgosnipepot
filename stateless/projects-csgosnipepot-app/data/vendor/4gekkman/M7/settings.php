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
        'name'        => "Images",
        'description' => "This package allow to save images the way you want.",
      ],
      'EN' => [
        'name'        => "Images",
        'description' => "This package allow to save images the way you want.",
      ]
    ],

    //------------------------//
    // 5] Настройки пакета M7 //
    //    M7 settings         //
    //------------------------//
    'default_parameters' => [
      'folderpath_relative_to_basepath' => 'public/public/M5/steam_avatars',
      'should_save_original'            => false,
      'should_save_not_filtered_images' => false,
      'sizes'                           => [
        [
          184,
          184
        ]
      ],
      'types'                           => [
        'image/jpeg'
      ],
      'quality'                         => 100,
      'filters'                         => [],
    ],
    'parameter_groups' => [

    ],



];
