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
        'name'        => 'Новый документ',
        'description' => 'New document',
      ],
      'EN' => [
        'name'        => 'Это D-пакет',
        'description' => 'It is D-package',
      ]
    ],

    //--------------------------------------------------------------------------------------//
    // 5] Включить ли режим разработки?                                                     //
    //    - Консольные команды m1:allrespublish и m1:minify выполняются при каждом запросе. //
    //    - Удобен и необходим во время зазработки, вреден в продакшн.                      //
    //    Is development mode switched on                                                   //
    //    - Console Commands m1:allrespublish and m1:minify will invoke every request.      //
    //    - Convenient mode for development, but harmful for production.                    //
    //--------------------------------------------------------------------------------------//
    'development_mode' => true,

    //------------------------------//
    // 6] Опции для работы с github //
    //    Github options            //
    //------------------------------//

      // 6.1] Путь к файлу с паролем от github | Path to file with github password //
      //---------------------------------------------------------------------------//
      'github_password' => '',

      // 6.2] Путь к файлу с токеном от github | Path to file with github oauth2 token //
      //-------------------------------------------------------------------------------//
      'github_oauth2' => '',

      // 6.3] Путь к powershell-скрипту для авто push на github | Path to powershell-script for auto-push to github //
      //------------------------------------------------------------------------------------------------------------//
      'github_powershell' => '',

    //---------------------------------------------------//
    // 7] Параметры деплоая проекта на удалённом сервере //
    //    Deploy parameters                              //
    //---------------------------------------------------//

      // 7.1] Имя владельца github-аккаунта проекта | Name of the owner of github-account of the project //
      //-------------------------------------------------------------------------------------------------//
      'deploy_github_account_name' => '',

      // 7.2] OAuth-токен github-аккаунта проекта с правом repo | OAuth-token for github-account of the project with repo rights //
      //-------------------------------------------------------------------------------------------------------------------------//
      'deploy_github_oauth2' => '',

      // 7.3] Автоматическое обновление всех форков на деплой-аккаунте раз в час | Autoupdate of all forks on deploy-account once in hour //
      //----------------------------------------------------------------------------------------------------------------------------------//
      'deploy_github_forks_autoupdate' => false,


];
