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
        'name'        => "Deposit system",
        'description' => "Deposit system",
      ],
      'EN' => [
        'name'        => "Deposit system",
        'description' => "Deposit system",
      ]
    ],

    //----------------------//
    // 5] Настройки M13     //
    //    M13 configuration //
    //----------------------//

      // 1] Название группы ботов из M8 для приёма депозита | M8 bot group name to accept deposit
      'bot_group_name_to_accept_deposits' => 'Shop',

      // 2] Минимальная цена принимаемых скинов в центах | Min skins to accept price in cents
      // - С учётом отнятого из цены spread в процентах от первоначальной.
      'min_skin2accept_price_cents' => '10',

      // 3] Спред, вычитаемый из цены в % от первоначальной | Spread in % from original
      'skin_price2accept_spread_in_perc' => '30',

      // 4] Сколько дней хранить историю операций по кошелькам | History of operations by wallets storage limit in days
      // - 0 означает без ограничений.
      'wallet_history_limit_days' => 0,

      // 5] Типы операций с монетами | Operation types with coins
      'coin_operation_types' => [
        'deposit',                // Пополнение
        'withdrawal'              // Снятие
      ],

      // 6] Как часто пользователь может обновлять инвентарь (сек) | How often user can update inventory (sec)
      // - 0 означает без ограничений.
      'how_ofen_user_can_update_inv_sec' => 300,

      // 7] Какие типы предметов разрешены для приёма
      'item_type_filters' => [

        'undefined'         => true,
        'weapon'            => true,
        'knife'             => true,
        'case'              => true,
        'key'               => true,
        'startrak'          => true,
        'souvenir packages' => true,
        'souvenir'          => true,

      ],

      // 8] Через сколько секунд отменять исходящие трейды | Limit in secs for sent offers
      'sent_offers_limit_secs' => 300,

];
