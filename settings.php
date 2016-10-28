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
      'steam_image_server' => 'http://steamcommunity-a.akamaihd.net/economy/image/',

      // 5.4] Путь к каталогу относительно корня laravel, куда сохранять файлы с куками ботов | Path to catalogue relative to the laravel root, where should we save files with bots cookies
      'root4cookies' => 'storage/m8_bots_cookies',

      // 5.5] MAX кол-во попыток авто.авторизации | The MAX number of auto.authorization attempts
      'max_num_of_auto_authorization_attempts' => 2,

      // 5.6] API-ключ от backpack.tf | Backpack.tf API-key
      'apikey_backpack' => '576c203786674711306558fc',

      // 5.7] Цена по умолчанию для вещей, цена которых неизвестна, $ | Default price for items with unknown price, $
      'price_default4unknown_items' => '0.03',

      // 5.8] Если кол-во предложений вещи на Steam Market < этого числа, брать lowest_price
      // - If quantity of item in Steam Market is lower, than this number, take lowest_price
      'anti_manipulating_quantity_limit' => '40',

      // 5.9] На сколько % должна измениться цена, чтобы система посчитала её нестабильной?
      // - On which % must the price change for the system mark it as unstable?
      'unstable_price_threshold' => '50',

      // 5.10] Включить ли извлечение lowest price из истории маркета в C17
      // - Turn on/off extracting of lowest price from Steam Market history
      'check_lowest_price_on_market' => true,


    //--------------------------------//
    // 6] Настройка списков в БД      //
    //    Settings of the lists in DB //
    //--------------------------------//
    'exteriors' => [
      "Not Painted",
      "Battle-Scarred",
      "Well-Worn",
      "Field-Tested",
      "Minimal Wear",
      "Factory New",
    ],
    'knife_types' => [
      "Bayonet",
      "Bowie Knife",
      "Butterfly Knifeife",
      "Falchion Knifefe",
      "Flip Knife",
      "Gut Knife",
      "Huntsman Knifefe",
      "Karambit",
      "M9 Bayonet",
      "Shadow Daggersrs"
    ],
    'weapon_models' => [
      "AK-47",
      "AUG",
      "AWP",
      "CZ75-Auto",
      "Desert Eagle",
      "Dual Berettas",
      "FAMAS",
      "Five-SeveN",
      "G3SG1",
      "Galil AR",
      "Glock-18",
      "M249",
      "M4A1-S",
      "M4A4",
      "MAC-10",
      "MAG-7",
      "MP7",
      "MP9",
      "Negev",
      "Nova",
      "P2000",
      "P250",
      "P90",
      "PP-Bizon",
      "R8 Revolver",
      "Sawed-Off",
      "SCAR-20",
      "SG 553",
      "SSG 08",
      "Tec-9",
      "UMP-45",
      "USP-S",
      "XM1014"
    ],


];
