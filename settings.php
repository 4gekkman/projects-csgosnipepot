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

      // 5.1] Режим приёма ставок комнаты по умолчанию | Default room bet accepting mode
      // - Доступные значения:
      //
      //    roll          | Все подключенные к комнате боты принимают ставки по очереди
      //    availability  | Ставку принимает первый способный на это бот, в спискке подключенных к комнате
      //
      'bet_accepting_mode' => 'availability',

      // 5.2] Длительность раундов в комнате в секундах | Duration of the room rouds in seconds
      'room_round_duration_sec' => '35',

      // 5.3] MAX кол-во вещей в 1-й ставке в раундах комнады | MAX items per bet in room rounds
      // - Значение 0 означает отсутствие ограничений.
      'max_items_per_bet' => '0',

      // 5.4] MIN кол-во вещей в 1-й ставке в раундах комнады | MIN items per bet in room rounds
      // - Значение 0 означает отсутствие ограничений.
      'min_items_per_bet' => '0',

      // 5.5] MAX кол-во вещей в раунде | MAX items per round
      // - Значение 0 означает отсутствие ограничений.
      'max_items_per_round' => '0',

      // 5.6] MIN кол-во вещей в раунде | MIN items per round
      // - Значение 0 означает отсутствие ограничений.
      'min_items_per_round' => '0',

      // 5.7] MAX размер ставки в $ | MAX bet value in $
      // - Значение 0 означает отсутствие ограничений.
      'max_bet' => '0',

      // 5.8] MIN размер ставки в $ | MIN bet value in $
      // - Значение 0 означает отсутствие ограничений.
      'min_bet' => '0',

      // 5.9] MAX общий размер ставок в $ в раунде | MAX total bet value in $ in round
      // - Значение 0 означает отсутствие ограничений.
      'max_bet_round' => '0',

      // 5.10] MIN общий размер ставок в $ в раунде | MIN total bet value in $ in round
      // - Значение 0 означает отсутствие ограничений.
      'min_bet_round' => '0',

      // 5.11] Разрешить ли делать ставки вещами с нестабильными ценами | Allow unstable prices
      'allow_unstable_prices' => 0,

      // 5.12] Разрешить принимать в виде ставок только эти типы вещей | Allow to accept bets by only those types of items

        // 5.12.1] Доступные типы вещей | Available types of items
        'available_types_of_items' => [
          'case',
          'key',
          'startrak',
          'souvenir packages',
          'souvenir',
          'knife',
          'weapon'
        ],

        // 5.12.2] Разрешить принимать в виде ставок только эти типы вещей | Allow to accept bets by only those types of items
        'allow_only_types' => [
          'case',
          'key',
          'startrak',
          'souvenir packages',
          'knife',
          'weapon'
        ],

      // 5.13] Размер комиссии сервиса в комнате в % от банка | Service fee in the room in % from bank
      'fee_percents' => '10',

      // 5.14] Включить ли механизм размена при выплате выигрышей | Should we turn on the change mechanism
      'change' => 1,

      // 5.15] Включить ли механизм выплат выигрышей от имени 1-го бота | Should we turn on the mechanism of payout from 1 bot
      'one_bot_payout' => 0,

      // 5.16] Лимит в минутах на то, чтобы победитель забрал выигрыш | Payout limit in minutes
      'payout_limit_min' => '60',

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
    //
    // - Значения полей:
    //
    //    ▪ bet_accepting_mode      / Режим приёма ставок ботами. На данный момент, реализован лишь "availability" (наиболее свободный бот комнаты, т.е. у кого очередь необработанных ставок меньше).
    //    ▪ name                    / Имя комнаты
    //    ▪ description             / Краткое описание комнаты (будет отображаться под именем при выборе комнаты игроком)
    //    ▪ description_full        / Полное описание комнаты (будет всплывать при наведении на иконку "i" рядом с именем комнаты)
    //    ▪ is_on                   / Включена ли комната (0 / 1)
    //    ▪ room_round_duration_sec / Сколько длится 1 раунд в комнате в секундах
    //    ▪ max_items_per_bet       / Максимальное кол-во вещей, которое можно поставить пользователю за весь раунд. Пустое значение = отсутствие ограничения.
    //    ▪ max_items_per_round     / Максимальное кол-во вещей, которое можно поставить всем за весь раунд. Пустое значение = отсутствие ограничения.
    //    ▪ min_items_per_bet       / Минимальное кол-во вещей, которое можно поставить пользователю за весь раунд. Пустое значение = отсутствие ограничения.
    //    ▪ min_items_per_round     / Минимальное кол-во вещей, которое можно поставить всем за весь раунд. Пустое значение = отсутствие ограничения.
    //    ▪ min_bet                 / Минимальный размер ставок 1-го игрока за раунд в центах доллара США. Пустое значение = отсутствие ограничения.
    //    ▪ max_bet                 / Максимальный размер ставок 1-го игрока за раунд в центах доллара США. Пустое значение = отсутствие ограничения.
    //    ▪ min_bet_round           / Минимальный размер ставок всех игроков за раунд в центах доллара США. Пустое значение = отсутствие ограничения.
    //    ▪ max_bet_round           / Максимальный размер ставок всех игроков за раунд в центах доллара США. Пустое значение = отсутствие ограничения.
    //    ▪ allow_unstable_prices   / Разрешить ли цены, помеченные, как нестабильные (0 / 1)
    //    ▪ allow_only_types        / Какие типы вещей разрешено принимать в виде ставок (json с массивом имён этих типов).
    //    ▪ fee_percents            / Размер комиссии сервиса от банка каждого раунда, в процентах - целое число от 0 до 100.
    //    ▪ change                  / Использовать ли сдачу (0 - нет, 1 - да)
    //    ▪ one_bot_payout          / Выплачивать ли выигрыш одним ботом, или можно разными? (0 - разными, 1 - одним).
    //
    "rooms" => [

      "Light" => [

        // Основные
        "bet_accepting_mode"      => "availability",
        "name"                    => "Light",
        "description"             => "Bets from 0.03¢",
        "description_full"        => "It's the \"light\" room with the lowest minimum bet limit - from 0.03¢.",
        "is_on"                   => 1,
        "room_round_duration_sec" => "120",
        "max_items_per_round"     => "200",
        "min_bet"                 => "3",
        "allow_unstable_prices"   => 0,
        "allow_only_types"        => "[\"case\",\"key\",\"startrak\",\"souvenir packages\",\"knife\",\"weapon\"]",
        "fee_percents"            => "10",
        "change"                  => 0,
        "one_bot_payout"          => 0,

        // Дополнительные
        "max_items_per_bet"       => "",
        "min_items_per_bet"       => "",
        "min_items_per_round"     => "",
        "max_bet"                 => "",
        "min_bet_round"           => "",
        "max_bet_round"           => "",
      ],

      "Main" => [

        // Основные
        "bet_accepting_mode"      => "availability",
        "name"                    => "Main",
        "description"             => "Bets from 1$",
        "description_full"        => "It's the \"main\" room with minimum bet limit - from 1$.",
        "is_on"                   => 1,
        "room_round_duration_sec" => "120",
        "max_items_per_round"     => "200",
        "min_bet"                 => "100",
        "allow_unstable_prices"   => 0,
        "allow_only_types"        => "[\"case\",\"key\",\"startrak\",\"souvenir packages\",\"knife\",\"weapon\"]",
        "fee_percents"            => "7",
        "change"                  => 0,
        "one_bot_payout"          => 0,

        // Дополнительные
        "max_items_per_bet"       => "",
        "min_items_per_bet"       => "",
        "min_items_per_round"     => "",
        "max_bet"                 => "",
        "min_bet_round"           => "",
        "max_bet_round"           => "",
      ],

      "Premium" => [

        // Основные
        "bet_accepting_mode"      => "availability",
        "name"                    => "Premium",
        "description"             => "Bets from 10$",
        "description_full"        => "This is the \"premium\" room with minimum bet limit - from 10$.",
        "is_on"                   => 1,
        "room_round_duration_sec" => "120",
        "max_items_per_round"     => "200",
        "min_bet"                 => "1000",
        "allow_unstable_prices"   => 0,
        "allow_only_types"        => "[\"case\",\"key\",\"startrak\",\"souvenir packages\",\"knife\",\"weapon\"]",
        "fee_percents"            => "5",
        "change"                  => 0,
        "one_bot_payout"          => 0,

        // Дополнительные
        "max_items_per_bet"       => "",
        "min_items_per_bet"       => "",
        "min_items_per_round"     => "",
        "max_bet"                 => "",
        "min_bet_round"           => "",
        "max_bet_round"           => "",
      ],

    ],




];
