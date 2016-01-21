<?php

return [

    //-----------//
    // 1] Домен  //
    //    Domain //
    //-----------//
    'domain' => 'http://dev.app',

    //---------//
    // 2] URI  //
    //---------//
    'uri' => '/w1',

    //----------------------//
    // 3] Локаль пакета     //
    //    Locale of package //
    //----------------------//
    // - APP        - locale of application (see config\app.php locale)
    // - RU, EN     - another locale

      // 3.1] Локали, которые поддерживает этот пакет
      // - The locales that are supported by this package
      'locales' => ['APP','RU','EN'],

      // 3.2] Выбранная для пакета локаль
      // - The selected locale
      'locale' => 'APP',

    //------------------------------//
    // 4] Имя и описание пакета     //
    //    Locale of package         //
    //------------------------------//
    'aboutpack' => [
      'RU' => [
        'name'        => 'Новый виджет',
        'description' => 'Это новый W-пакет',
      ],
      'EN' => [
        'name'        => 'New widget',
        'description' => 'It is new W-package',
      ]
    ],

    //-------------------------------------------------------------------------//
    // 5] История обновлений конфига пакета. Не редактировать вручную.         //
    //    History of updates of config of the package. Don't edit it manually. //
    //-------------------------------------------------------------------------//
    'cnfupdshistory'    => [],

];
