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
        'name'        => "Free Coins",
        'description' => "Free Coins",
      ],
      'EN' => [
        'name'        => "Free Coins",
        'description' => "Free Coins",
      ]
    ],

    //----------------------//
    // 5] Настройки M15     //
    //    M15 configuration //
    //----------------------//

      //-----------------------------//
      // 5.1] Ежедневные выплаты     //
      //      Daily payments         //
      //-----------------------------//

        // 5.1.1] Кол-во выплачиваесях монет | Number of coins to payout
        'daily_coins_payout_num_everyuser' => 10,








];
