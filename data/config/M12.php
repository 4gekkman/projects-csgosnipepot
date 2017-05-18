<?php

return [

    //---------------------------------------------------------------------------//
    // 1] История обновлений базы данных пакета. Не редактировать вручную.       //
    //    History of updates of database of the package. Don't edit it manually. //
    //---------------------------------------------------------------------------//
    'updateshistory' => [],

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
        'name'        => "FAQ",
        'description' => "Frequently Asked Questions",
      ],
      'EN' => [
        'name'        => "FAQ",
        'description' => "Frequently Asked Questions",
      ]
    ],

    //-------------------------------------------------------------//
    // 5] Путь к папке с данными FAQ относительно корня проекта    //
    //    The path to FAQ data relative to the root of the project //
    //-------------------------------------------------------------//
    'faq_root_folder'    => "vendor/4gekkman/R6/data",

    //-------------------------------------------------------------------------------------------------------//
    // 6] Путь к папке, куда сохранять публичные ресурсы FAQ относительно публичной папки проекта            //
    //    The path to folder where to save public FAQ resources relative to the public folder of the project //
    //-------------------------------------------------------------------------------------------------------//
    'public_faq_folder'    => "public/faq",

    //-----------------------------------------//
    // 7] Имя стартовой группы для каждого FAQ //
    //    The start group name for each FAQ    //
    //-----------------------------------------//
    'start_group_names'    => [
      'csgohap'           => 'generalissues',
      'csgohapdashboard'  => 'common'
    ],


];
