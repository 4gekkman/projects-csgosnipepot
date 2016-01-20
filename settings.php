<?php

return [

    //---------------------------------------------------------------------------//
    // 1] История обновлений базы данных пакета. Не редактировать вручную.       //
    //    History of updates of database of the package. Don't edit it manually. //
    //---------------------------------------------------------------------------//
    'updateshistory'    => [],

    //------------------------------//
    // 2] Имя и описание пакета     //
    //    Locale of package         //
    //------------------------------//
    'aboutpack' => [                // {==fullreplace==}
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
    // 3] Включить ли режим разработки?                                                     //
    //    - Консольные команды m1:allrespublish и m1:minify выполняются при каждом запросе. //
    //    - Удобен и необходим во время зазработки, вреден в продакшн.                      //
    //    Is development mode switched on                                                   //
    //    - Console Commands m1:allrespublish and m1:minify will invoke every request.      //
    //    - Convenient mode for development, but harmful for production.                    //
    //--------------------------------------------------------------------------------------//
    'development_mode' => true,

    //------------------------//
    // 4] GitHub OAuth2 Token //
    //------------------------//
    'github_oauth2' => '',

];
