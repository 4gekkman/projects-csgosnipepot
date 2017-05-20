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
    // routing start
    'routing' => [
      'localhost' => [
        'http' => [
          '' => [
            '/layouts/l10004'
          ]
        ]
      ],
      '10.10.10.10' => [
        'http' => [
          '' => [
            '/layouts/l10004'
          ]
        ]
      ],
      '188.244.34.28' => [
        'http' => [
          '' => [
            '/layouts/l10004'
          ]
        ]
      ],
      'csgosnipepot.com' => [
        'http' => [
          '' => [
            '/layouts/l10004'
          ]
        ]
      ],
      '207.154.229.41' => [
        'http' => [
          '' => [
            '/layouts/l10004'
          ]
        ]
      ]
    ],
    // routing end

    //-----------------//
    // 2] Локализация  //
    //    localization //
    //-----------------//

      // 2.1] Поддерживаемые пакетом локали | The locales that are supported by the package //
      //------------------------------------------------------------------------------------//
      'locales' => ['RU', 'EN'],

      // 2.2] Выбранная локаль | Chosen locale //
      //---------------------------------------//
      // - If the value is empty, uses config('app.locale')
      // - Else if config('app.locale') not in 'locales', uses 1-st of 'locales'
      // - Else if 1-st of 'locales' is empty, uses 'RU'.
      'locale' => '',

    //------------------------------//
    // 3] Имя и описание пакета     //
    //    Locale of package         //
    //------------------------------//
    'aboutpack' => [
      'RU' => [
        'name'        => 'CSGOHAP dashboard layout',
        'description' => 'CSGOHAP dashboard layout',
      ],
      'EN' => [
        'name'        => 'CSGOHAP dashboard layout',
        'description' => 'CSGOHAP dashboard layout',
      ]
    ],

    //-------------------------------------------------------------------------//
    // 4] История обновлений конфига пакета. Не редактировать вручную.         //
    //    History of updates of config of the package. Don't edit it manually. //
    //-------------------------------------------------------------------------//
    'cnfupdshistory' => [],
  
    //--------------//
    // 5] Настройки //
    //    Settings  //
    //--------------//
  
      // 5.1] Пункты главного меню | Main menu items
      'mainmenu' => [
        [
          'uri'       =>  '/lk/faq',
          'icon_mdi'  =>  'mdi-information-outline',
          'icon_url'  =>  '',
          'title'     =>  'FAQ',
          'bg_color'  =>  '#223340',
          'brd_color' =>  'transparent',
          'visible'   =>  true
        ],
        [
          'uri'       =>  '/lk/botnet',
          'icon_mdi'  =>  'mdi-robot',
          'icon_url'  =>  '',
          'title'     =>  'Управление ботами',
          'bg_color'  =>  '#223340',
          'brd_color' =>  'transparent',
          'visible'   =>  true
        ],
        [
          'uri'       =>  '/lk/shop',
          'icon_mdi'  =>  'mdi-shopping',
          'icon_url'  =>  '',
          'title'     =>  'Магазин скинов',
          'bg_color'  =>  '#223340',
          'brd_color' =>  'transparent',
          'visible'   =>  true
        ]
      ],


];
