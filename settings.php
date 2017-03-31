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

    //--------------//
    // 5] Настройки //
    //    Settings  //
    //--------------//

      // 5.1] Кол-во элементов в таблицах на 1-й странице | Number or items in one page in tables //
      //------------------------------------------------------------------------------------------//
      'items_at_page' => 10,

      // 5.2] Вкл / Выкл проверку exec прав | Turn on / off check of exec privileges //
      //-----------------------------------------------------------------------------//
      'authorize_exec_ison' => false,

      // 5.3] Вкл / Выкл проверку access прав | Turn on / off check of access privileges //
      //---------------------------------------------------------------------------------//
      'authorize_access_ison' => false,

      // 5.4] Минимальное количество символов в пароле | Min num of chars in a password //
      //--------------------------------------------------------------------------------//
      'min_chars_in_pass' => 8,

      // 5.5] Время жизни кода верификации email в минутах | Email verification code lifetime in minutes //
      //-------------------------------------------------------------------------------------------------//
      'email_verify_code_lifetime_min' => 15,

      // 5.6] Время жизни кода верификации phone в минутах | Phone verification code lifetime in minutes //
      //-------------------------------------------------------------------------------------------------//
      'phone_verify_code_lifetime_min' => 15,

      // 5.7] Кол-во цифр в коде верификации | Number of digits in verification code //
      //-----------------------------------------------------------------------------//
      'verification_code_length' => 4,

      // 5.8] Параметры письма при верификации email | Email verification letter parameters //
      //------------------------------------------------------------------------------------//
      'email_verify_from' => 'noreply@verify.com',
      'email_verify_subj' => 'Email verification',

      // 5.9] Глобальное время жизни аутентификации в часах | Global lifetime of auth in hours //
      //---------------------------------------------------------------------------------------//
      'auth_cookie_lifetime_global' => 525600,

      // 5.10] Локальное (для групп) время жизни аутентификации в часах | Local (for groups) lifetime of auth in hours //
      //---------------------------------------------------------------------------------------------------------------//
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

      // 5.11] Удалять ли польз-ей с не верифицированными email | Should system delete users with not verified email //
      //-------------------------------------------------------------------------------------------------------------//
      'del_users_with_not_ver_emails' => false,

      // 5.12] Через сколько часов после создания удалять польз-ей с не вериф.email | In how many hours should system delete users with not verified email //
      //---------------------------------------------------------------------------------------------------------------------------------------------------//
      'del_users_with_not_ver_emails_in_hours' => 24,

      // 5.13] Удалять ли польз-ей с не верифицированными phone | Should system delete users with not verified phone //
      //-------------------------------------------------------------------------------------------------------------//
      'del_users_with_not_ver_phones' => false,

      // 5.14] Через сколько часов после создания удалять польз-ей с не вериф.phone | In how many hours should system delete users with not verified phone //
      //---------------------------------------------------------------------------------------------------------------------------------------------------//
      'del_users_with_not_ver_phones_in_hours' => 24,

      // 5.15] Кол-во цифр в коде аутентификации | Number of digits in auth code //
      //-------------------------------------------------------------------------//
      'auth_code_length' => 4,

      // 5.16] Параметры письма при восстановлении пароля через email | Password recovery by email letter parameters //
      //-------------------------------------------------------------------------------------------------------------//
      'recover_password_email_from' => 'noreply@recovery.com',
      'recover_password_email_subj' => 'Password recovery code',

      // 5.17] Время жизни кода аутентификации по email в минутах | Email auth code lifetime in minutes //
      //------------------------------------------------------------------------------------------------//
      'email_auth_code_lifetime_min' => 15,

      // 5.18] Время жизни кода аутентификации через phone в минутах | Phone auth code lifetime in minutes //
      //---------------------------------------------------------------------------------------------------//
      'phone_auth_code_lifetime_min' => 15,

      // 5.19] Вкл / Выкл защиту от брутфорса | Turn on / Turn off the bruteforce protection //
      //-------------------------------------------------------------------------------------//
      'bruteforce_protection_ison' => true,

      // 5.20] Кол-во неудачных попыток аутентификации, после которого начинает применяться защита       //
      //      The number of failed authentication attempts, after which the protection begins to operate //
      //-------------------------------------------------------------------------------------------------//
      'bruteforce_protection_threshold' => 5,

      // 5.21] Время жизни счётчика неудачных попыток в минутах | Lifetime of failed attempts counter in minutes //
      //---------------------------------------------------------------------------------------------------------//
      'bruteforce_protection_counter_lifetime' => 60,

      // 5.22] Режим аутентификации (окно/редирект) | Auth mode (window/redirect) //
      //--------------------------------------------------------------------------//
      // - window     | Аутентификация в окне, которое потом исчезает
      // - redirect   | Редирект на аутентификацию, потом редирект обратно по полученному URL
      'authmode' => 'redirect',

    //----------------------//
    // 6] Конфиг HybridAuth //
    //    HybridAuth config //
    //----------------------//
    'hybridauth_config' => [

			"base_url" => "/authwith/hybrid-auth-endpoint",
			"providers" => [

				"Steam" => [
					"enabled" => true
				],

			],

			// If you want to enable logging, set 'debug_mode' to true.
			// You can also set it to
			// - "error" To log only error messages. Useful in production
			// - "info" To log info and error messages (ignore debug messages)
			"debug_mode" => false,

			// Path to file writable by the web server. Required if 'debug_mode' is not false
			"debug_file" => "",

    ],
    'steam_api_key' => "",

    //-------------------------------------------------------------------//
    // 7] Параметры аутентификации в Steam для HA в зависимости от среды //
    //    Auth in Steam params depends on environment                    //
    //-------------------------------------------------------------------//
    // - Хост auto означает, что надо взять тот же хост, от которого пришёл запрос.
    'hybridauth_env_params' => [
      "dev" => [
        'host'          => "auto",
        'steam_api_key' => ""
      ],
      //"test" => [
      //  'host'          => "vesnaprishla.ru",
      //  'steam_api_key' => "3541F0471AC72DA7C1C8107392F26266"
      //],
      //"prod" => [
      //  'host'          => "csgohaplogin.ru",
      //  'steam_api_key' => "3541F0471AC72DA7C1C8107392F26266"
      //],
    ]



];
