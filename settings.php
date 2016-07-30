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
        'name'        => "Chat for Steam projects",
        'description' => "Chat for Steam projects",
      ],
      'EN' => [
        'name'        => "Chat for Steam projects",
        'description' => "Chat for Steam projects",
      ]
    ],

    //--------------//
    // 5] Настройки //
    //    Settings  //
    //--------------//

      // 5.1] Комнаты чата | Chat rooms
      // - Sample:
      //
      //    [
      //      'room1' => [
      //        'description'     => 'The room1 description',   // Description of the room
      //        'expire'          => '1440',                    // Message expiration time in minutes (0 mean unlimited)
      //        'messages_limit'  => '10000',                   // Max number of messages to store (0 mean unlimited)
      //        'max_msg_length'  => '255',                     // Max message length (0 mean unlimited)
      //        'max_messages'    => '500',                     // Max messages in client's chat
      //        'allow_guests'    => '0',                       // Can guests post to the room, or not
      //        'moderator_ids'   => [1,2,3]                    // Moderator user ids
      //      ],
      //      'room2' => [
      //        'description'     => 'The room1 description',   // Description of the room
      //        'expire'          => '1440',                    // Message expiration time in minutes (0 mean unlimited)
      //        'messages_limit'  => '10000',                   // Max number of messages to store (0 mean unlimited)
      //        'max_msg_length'  => '255',                     // Max message length (0 mean unlimited)
      //        'max_messages'    => '500',                     // Max messages in client's chat
      //        'allow_guests'    => '0',                       // Can guests post to the room, or not
      //        'moderator_ids'   => [1,2,3]                    // Moderator user ids
      //      ]
      //    ]
      //
      'rooms' => [
        'main' => [
          'description'     => 'The main room of the chat',
          'expire'          => '1440',
          'messages_limit'  => '10000',
          'max_msg_length'  => '255',
          'max_messages'    => '100',
          'allow_guests'    => '0',
          'moderator_ids'   => []
        ]
      ],


];
