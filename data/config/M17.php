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
        'name'        => "Nick Promo System",
        'description' => "Nick Promo System",
      ],
      'EN' => [
        'name'        => "Nick Promo System",
        'description' => "Nick Promo System",
      ]
    ],

    //----------------------//
    // 5] Настройки M16     //
    //    M16 configuration //
    //----------------------//

      // 1] Сколько монет будет выдано за добавление строки в ник | How much coins will be given for adding string to nick
      'coins' => '20',

      // 2] За добавление каких строк будут выданы монеты | For adding what strings the coins will be given
      'strings2check' => [
        'csgohap\.ru'
      ]


];
