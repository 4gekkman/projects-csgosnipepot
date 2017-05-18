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
    'cnfupdshistory' => [],

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
        'name'        => "CSGO skins shop",
        'description' => "CSGO skins shop",
      ],
      'EN' => [
        'name'        => "CSGO skins shop",
        'description' => "CSGO skins shop",
      ]
    ],

    //----------------------//
    // 5] Настройки M14     //
    //    M14 configuration //
    //----------------------//

      // 1] Название группы ботов из M8 для магазина | M8 bot group name for shop
      'bot_group_name_to_accept_deposits' => 'Shop',

      // 2] Через сколько секунд отменять исходящие трейды | Limit in secs for sent offers
      'sent_offers_limit_secs' => 300,

      // 3] Кол-во попыток создать трейд до признания сделки по нему не состоявшейся | Num of tries to create offer until consider trade failed
      'tries_create_offer_until_failed' => 50,

      // 4] Email's, на которые отправлять заказы на скины | Emails to send skin orders
      'emails_skin_orders' => [
        'gtmmm2011@gmail.com',
        'adena-zone@yandex.ru'
      ],


];
