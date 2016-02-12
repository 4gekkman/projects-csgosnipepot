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
        'name'        => "Users and Privileges",
        'description' => "Package to manage users and privileges",
      ],
      'EN' => [
        'name'        => "Users and Privileges",
        'description' => "Package to manage users and privileges",
      ]
    ],

    //--------------------//
    // 5] Общие настройки //
    //    Common settings //
    //--------------------//

      // 5.1] Вкл / Выкл систему | Turn on / Turn off the system //
      //---------------------------------------------------------//
      'common_ison' => true,

      // 5.2] Время жизни кода восстановления пароля в минутах | Pass recovery code lifetime in minutes //
      //------------------------------------------------------------------------------------------------//
      'common_pass_recovery_code_lifetime' => 15,

      // 5.3] Минимальное количество символов в пароле | Min num of chars in a password //
      //--------------------------------------------------------------------------------//
      'common_min_chars_in_pass' => 8,

      // 5.4] URL сервера изображений | Image server url //
      //-------------------------------------------------//
      'common_url_img_server' => "",

      // 5.5] URL документа для аутентификации | Auth doc url //
      //------------------------------------------------------//
      'common_url_auth_doc' => "",

    //--------------------------//
    // 5] Защита от брутфорса   //
    //    Bruteforce protection //
    //--------------------------//
    // - After "threshold" failed auth attempts during "counter_lifetime" the protection starts operating.
    // - Each subsequent failed attempt X over "threshold" freezes acc in "delay" * X minutes.

      // 5.1] Вкл / Выкл защиту от брутфорса | Turn on / Turn off the bruteforce protection //
      //------------------------------------------------------------------------------------//
      'bruteforce_protection_ison' => true,

      // 5.2] Кол-во неудачных попыток аутентификации, после которого начинает применяться защита        //
      //      The number of failed authentication attempts, after which the protection begins to operate //
      //-------------------------------------------------------------------------------------------------//
      'bruteforce_protection_threshold' => 5,

      // 5.3] Время жизни счётчика неудачных попыток в минутах | Lifetime of failed attempts counter in minutes //
      //--------------------------------------------------------------------------------------------------------//
      'bruteforce_protection_counter_lifetime' => 60,

      // 5.4] Величина прогрессивной задержки в минутах | Progressive delay in minutes //
      //-------------------------------------------------------------------------------//
      'bruteforce_protection_delay' => 1,

    //----------------------------------------//
    // 6] Время жизни аутентификационной куки //
    //    Auth cookie lifetime                //
    //----------------------------------------//

      // 6.1] Глобальное значение в минутах | Global value in minutes //
      //--------------------------------------------------------------//
      'auth_cookie_lifetime_global' => 525600,

      // 6.2] Локальные значения для указанных групп | Local values for groups //
      //-----------------------------------------------------------------------//
      // - If user is in several groups, system uses the smallest value.
      // - Sample:
      //
      //    [
      //      'admins'    => 1000,
      //      'customers' => 100,
      //      'couriers'  => 10,
      //    ]
      //
      'auth_cookie_lifetime_locals' => [

      ],

    //----------------------------------------//
    // 7] Настройка переадресации после входа //
    //    Setting up forwarding after log in  //
    //----------------------------------------//

      // 7.1] Глобальный URL для переадресации | Global url for redirect //
      //-----------------------------------------------------------------//
      // - Sample: "http://site.ru/uri"
      'redirect_global_url' => "",

      // 7.2] Локальные URL для переадресации пользователей указанных групп //
      //      Local urls for redirect of users of specified groups          //
      //--------------------------------------------------------------------//
      // - If user is in several groups, it takes the first occurrence in the list below.
      // - Sample:
      //
      //    [
      //      'admins'    => "http://site.ru/uri1",
      //      'customers' => "http://site.ru/uri2",
      //      'couriers'  => "http://site.ru/uri3",
      //    ]
      //
      'redirect_local_urls' => [

      ],

      // 7.3] Вкл / Выкл глобально переадресацию на реферальный URL //
      //      Turn on / Turn off redirect to ref URL globally       //
      //------------------------------------------------------------//
      // - If it is turned on, "global_url" setting does not work
      'redirect_global_ref_ison' => false,

      // 7.4] Вкл / Выкл локально переадресацию на реферальный URL, в зависимости от наличия базового URL в URL запроса //
      //      Turn on / Turn off redirect to ref URL locally, depending on presence of base URL in a request URL        //
      //----------------------------------------------------------------------------------------------------------------//
      // - Sample:
      //
      //    [
      //      'http://site.ru/uri1' => true,  // it will work also for: 'http://site.ru/uri1/url2', 'http://site.ru/uri1/url2/url3', etc.
      //      'http://site.ru/uri2' => false,
      //    ]
      //
      'redirect_local_ref_ison' => [

      ],

    //------------------------------------------------------------------------//
    // 8] Список столбцов с unique-индексами, которые используются, как логин //
    //    List of columns with unique index, which can be used as login       //
    //------------------------------------------------------------------------//
    'logins' => ['id', 'email', 'phone'],

    //--------------------------------------------------//
    // 9] Настройка факторов и сценариев аутентификации //
    //    Auth factors and scenarios settings           //
    //--------------------------------------------------//

      // 9.1] Список поддерживаемых факторов аутентификации | List of supported auth factors //
      //-------------------------------------------------------------------------------------//
      'authfactors' => ['password', 'sms', 'phrase'],

      // 9.2] Глобальный сценарий | Global scenario //
      //--------------------------------------------//
      // - Every even value must be one of: 'and' / 'or'.
      // - Every odd value must be one of authfactors.
      // - Scenario arr must contain odd number of elements.
      // - Examples:
      //
      //    ['password','and','sms']                        // Password and sms are required
      //    ['password','or','sms']                         // Can use or password, or sms.
      //    ['password','and','sms','or','phrase']          // Can use or (password + sms) or (phrase)
      //
      'authfactors_scenario_global' => ['password'],

      // 9.2] Локальные сценарии | Local scenarios //
      //-------------------------------------------//
      // - Local scenarios have priority over global.
      // - If user is in several groups, it takes the safest (considering the global).
      // - Example:
      //
      //    [
      //      'admins'    => ['password','and','sms']
      //      'couriers'  => ['sms']
      //    ]
      //
      'authfactors_scenarios_local' => [

      ],

    //-------------------------------------//
    // 10] Настройка SMS-аутентификации    //
    //     Authentication via SMS settings //
    //-------------------------------------//

      // 10.1] Длина кода для двухфакторной аутентификации | The length of the code for two-factor authentication //
      //----------------------------------------------------------------------------------------------------------//
      'smsauth_length' => 4,

      // 10.2] Набор символов для кода для двухфакторной аутентификации | The set of characters for the code for two-factor authentication //
      //-----------------------------------------------------------------------------------------------------------------------------------//
      'smsauth_charset' => "1234567890",

      // 10.3] MAX кол-во SMS за время жизни счётчика неудачных попыток | Max sms'es during failed attempts counter lifetime //
      //---------------------------------------------------------------------------------------------------------------------//
      'smsauth_smslimit' => 10,

      // 10.4] Время жизни счётчика SMS в кэше | Failed attempts counter lifetime in cache //
      //-----------------------------------------------------------------------------------//
      'smsauth_counter_lifetime' => 60,

    //----------------------------------------------------//
    // 11] Настройка аутентификации через секретную фразу //
    //     Authentication via secret phrase               //
    //----------------------------------------------------//

      // 11.1] Длина кода для двухфакторной аутентификации | The length of the code for two-factor authentication //
      //----------------------------------------------------------------------------------------------------------//
      'phraseauth_length' => 10,








];
