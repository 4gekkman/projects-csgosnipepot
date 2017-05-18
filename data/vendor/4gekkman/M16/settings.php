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
        'name'        => "Be online",
        'description' => "Be online system",
      ],
      'EN' => [
        'name'        => "Be online",
        'description' => "Be online system",
      ]
    ],

    //----------------------//
    // 5] Настройки M16     //
    //    M16 configuration //
    //----------------------//

      // 1] Сколько минут нужно быть непрерывно онлайн, чтобы получить раздачу | How much time you have to be online to get giveaway
      'giveaway_period_min' => '180',

      // 2] Сколько секунд нужно быть оффлайн, чтобы сбросился счётчик онлайна | How much time you heve to be offline to drop online counter
      'offline2drop_online_counter_sec' => '180',

      // 3] Как часто делать резервные копии счётчиков онлайн в БД | How often to make reserve copies of online counters to DB
      'online_counters_backup_period_sec' => '300',

      // 4] ID комнаты, наличие ставок в которой надо проверять при выплате | ID of the room, where user have to have min 1 bet to get free skin
      'id_room2check' => '2',

      // 5] Срок годности активных выдач
      'giveaways_limit_secs' => '1800',


];
