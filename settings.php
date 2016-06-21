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
        'name'        => "Steam bots and trade",
        'description' => "Steam bots and trade operations automate",
      ],
      'EN' => [
        'name'        => "Steam bots and trade",
        'description' => "Steam bots and trade operations automate",
      ]
    ],

    //--------------//
    // 5] Настройки //
    //    Settings  //
    //--------------//

      // 5.1] Название группы для Steam-пользователей | Steam users group name
      'group_steam_users' => 'SteamUsers',

      // 5.2] Название группы для Steam-ботов | Steam bots group name
      'group_steam_bots' => 'SteamBots',

      // 5.3] Сервер изображений Steam | Steam image server
      'steam_image_server' => 'https://steamcommunity-a.akamaihd.net/economy/image/',

      // 5.4] Путь к каталогу относительно корня laravel, куда сохранять файлы с куками ботов | Path to catalogue relative to the laravel root, where should we save files with bots cookies
      'root4cookies' => 'storage/m8_bots_cookies',

      // 5.5] MAX кол-во попыток авто.авторизации | The MAX number of auto.authorization attempts
      'max_num_of_auto_authorization_attempts' => 2

];
