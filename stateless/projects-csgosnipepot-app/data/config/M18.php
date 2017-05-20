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
        'name'        => "Steam Group Promo",
        'description' => "Steam Group Promo system",
      ],
      'EN' => [
        'name'        => "Steam Group Promo",
        'description' => "Steam Group Promo system",
      ]
    ],

    //----------------------//
    // 5] Настройки M16     //
    //    M16 configuration //
    //----------------------//

      // 1] Сколько монет будет выдано за вступление в группу | How much coins will be given for join to the group
      'coins' => '15',

      // 2] За вступление в какие группы будут выданы монеты (gid групп) | For joining to what groups the coins will be given
      'groups2join' => [
        ''
      ]

];
