<?php

return [

    //-------------//
    // 1] Routing  //
    //-------------//
    // - Don't touch the string "end of routing" below, otherwise smth will break.
    // - Routing system supports max 50 uri segments and 10 subdomains.
    // - Routing system supports 2 protocols: 'http' and 'https'.
    // - All subdomains should end in a dot. Example: "sub2.sub1."
    // - Subdomain '' means an absence of subdomains.
    // - All segments should start in a forward slash. Example: "/users"
    // - If you'll manually edit the array below, the changes will take effect only
    //   after the console command "m1:parseapp" will be invoked.
    // - MIN available structure example (url in example - http://domain.ru):
    /**
     *    'routing' => [
     *      'domain.ru' => [
     *        'http' => [
     *          '' => [
     *            '/'
     *          ]
     *        ]
     *      ]
     *    ]
     */
    'routing' => [
      'dev.app' => [
        'http' => [
          '' => [
            '/',
            '/test'
          ],
          's1.' => [
            '/'
          ],
          's2.s1.' => [
            '/'
          ]
        ]
      ]
    ], // end of routing

    //----------------------//
    // 2] Локаль пакета     //
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
    // 3] Имя и описание пакета     //
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
    // 4] История обновлений конфига пакета. Не редактировать вручную.         //
    //    History of updates of config of the package. Don't edit it manually. //
    //-------------------------------------------------------------------------//
    'cnfupdshistory'    => [],

];
