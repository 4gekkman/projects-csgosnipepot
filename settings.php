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
        'name'        => "CSGO Lottery game",
        'description' => "CSGO Lottery game",
      ],
      'EN' => [
        'name'        => "CSGO Lottery game",
        'description' => "CSGO Lottery game",
      ]
    ],

    //--------------------------------------------//
    // 5] Параметры по умолчанию для новых комнат //
    //    Default settings for a new game rooms   //
    //--------------------------------------------//
    // - Остальные значения по умолчанию введены в соотв.таблице в БД.

      // 5.1] Разрешить принимать в виде ставок только эти типы вещей | Allow to accept bets by only those types of items
      // - Из:
      //'case',
      //'key',
      //'startrak',
      //'souvenir packages',
      //'souvenir',
      //'knife',
      //'weapon'
      'allow_only_types' => [
        'case',
        'key',
        'startrak',
        'souvenir packages',
        'knife',
        'weapon'
      ],

    //--------------------------------------//
    // 6] Доступные статусы игры "Лоттерея" //
    //    Lottery game statuses             //
    //--------------------------------------//

      'lottery_game_statuses' => [

        "0" => [
          "id"          => "1",
          "status"      => "Created",
          "description" => "Начался новый раунд, но ещё никто не сделал ставку."
        ],

        "1" => [
          "id"          => "2",
          "status"      => "First bet",
          "description" => "Сделана первая и единственная пока ставка за раунд."
        ],

        "2" => [
          "id"          => "3",
          "status"      => "Started",
          "description" => "Сделана вторая ставка за раунд, начался обратный отсчёт таймера."
        ],

        "3" => [
          "id"          => "4",
          "status"      => "Pending",
          "description" => "Время игры закончилось, или достигнут лимит вещей. Ожидание обработки сделанных ранее ставок."
        ],

        "4" => [
          "id"          => "5",
          "status"      => "Lottery",
          "description" => "Визуальное проведение розыгрыша."
        ],

        "5" => [
          "id"          => "6",
          "status"      => "Winner",
          "description" => "Демонстрация победителя розыгрыша и его выигрыша."
        ],

        "6" => [
          "id"          => "7",
          "status"      => "Finished",
          "description" => "Раунд закончен."
        ],

      ],

    //---------------------------//
    // 7] Комнаты игры "Лотерея" //
    //    Lottery game statuses  //
    //---------------------------//
    "rooms" => [

      "Light" => [
        "name"                    => "для бомжей",
        "description"             => "Bets from 0.03¢",
        "description_full"        => "It's the \"light\" room with the lowest minimum bet limit - from 0.03¢.",
        "is_on"                   => 1,
      ],

      "Main" => [
        "name"                    => "основная",
        "description"             => "Bets from 1$",
        "description_full"        => "It's the \"main\" room with minimum bet limit - from 1$.",
        "is_on"                   => 1,
      ],

      "Premium" => [
        "name"                    => "Premium",
        "description"             => "Bets from 10$",
        "description_full"        => "This is the \"premium\" room with minimum bet limit - from 10$.",
        "is_on"                   => 1,
      ],

    ],

    //-------------------------------//
    // 8] Палитра цветов для игроков //
    //    Color palette for players  //
    //-------------------------------//
    "palette" => [
			"#BD5532",	//
			"#E1B866",	//
			"#373B44",	// http://www.colourlovers.com/palette/1083480/Between_The_Clouds
			"#DEE1B6",	//
			"#73C8A9",	//

			"#805841",  //
			"#DCF7F3",  //
			"#FFD8D8",  // http://www.colourlovers.com/palette/211196/Vuittons_Breakfast
			"#FFFCDD",  //
			"#F5A2A2",  //
    ],



];
