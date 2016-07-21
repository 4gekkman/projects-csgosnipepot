@extends($layoutid)
<?php /*=====================================================////
////																											   ////
////                    Документ D-пакета      				       ////
////																												 ////
////========================================================*/ ?>


<?php /*------------------->
<!-- 1. Title документа  -->
<!---------------------*/ ?>
@section('title')

  <title>D10005 - Interface for the M8 package (bots and automation)</title>

@stop



<?php /*----------------->
<!-- 2. CSS документа  -->
<!-------------------*/ ?>
@section('css')

  <!-- document css: start -->
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/font-awesome/css/font-awesome.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/ionicons/css/ionicons.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/bootstrap/css/bootstrap.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/css/AdminLTE.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/css/skins/_all-skins.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-slider/slider.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/daterangepicker/daterangepicker.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datepicker/datepicker3.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/iCheck/all.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/select2/select2.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/skins/moono/editor.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/dataTables.bootstrap.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/loaders.css/loaders.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/css/perfect-scrollbar.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-animsition/animsition/dist/css/animsition.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-cssgrids/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10005/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  1. Панель загрузки
  2. Боты
    2.0. Различные предупреждения
    2.1. Левый столбец (контент)
      2.1.1. Панель с заголовком и кол-вом ботов
      2.1.2. Список ботов
    2.2. Правый столбец (панель управления)
      2.2.1. Панель инструментов
  3. Интерфейс кликнутого бота
    3.1. Левый столбец: кнопка "Назад" и левое меню
      3.1.1. Кнопка "назад"
      3.1.2. Левое меню интерфейса бота
    3.2. Правый столбец: имя бота и контент интерфейса
      3.2.1. Хлебные крошки
      3.2.2. Контент-бокс интерфейса бота

  n. Получение данных с сервера и подключение JS этого документа
    n.1. Получение данных с сервера
    n.2. Подключение JS этого документа

-------------------------*/ ?>
@section('content')

<?php /*------------------>
<!-- 1. Панель загрузки -->
<!--------------------*/ ?>
<div class="loader">
  <div style="display: none" class="loading_state_panel loader-inner ball-scale" data-bind="visible: m.s0.is_loadshield_on">
    <div></div>
  </div>
</div>

<?php /*-------->
<!-- 2. Боты  -->
<!----------*/ ?>
<div class="bots-table-new row" style="display: none" data-bind="visible: m.s1.selected_group().id() == 1">

  <?php /*------------------------------>
  <!-- 2.0. Различные предупреждения  -->
  <!--------------------------------*/ ?>
  <div>

    <?php /*----------------------------------------->
    <!-- 1] Ошибка при обновлении цен с csgofast -->
    <!-------------------------------------------*/ ?>
    <div style="display: none" class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.csgofast_last_bug">
      <h4>There is csgofast price update error!</h4>
      <p data-bind="text: m.s2.price_update_errors.csgofast_last_bug"></p>
    </div>

    <?php /*--------------------------------------------->
    <!-- 2] Ошибка при обновлении цен с Steam Market -->
    <!-----------------------------------------------*/ ?>
    <div style="display: none" class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.steammarket_last_bug">
      <h4>There is Steam Market price update error!</h4>
      <p data-bind="text: m.s2.price_update_errors.steammarket_last_bug"></p>
    </div>

  </div>

  <?php /*---------------------------->
  <!-- 2.1. Левый столбец (контент) -->
  <!------------------------------*/ ?>
  <div class="bots-list-new col-md-7 col-sm-7 col-xs-7">

    <?php /*------------------------------------------>
    <!-- 2.1.1. Панель с заголовком и кол-вом ботов -->
    <!--------------------------------------------*/ ?>
    <div class="box box-common">
      <div class="box-body subdoc_title">
        <span>Bots</span>
        <span data-bind="text: '- ' + m.s2.bots_filtered().length + ' / ' + m.s2.bots().length" style="font-size: 13px; color: rgb(204, 204, 204);"></span>
      </div>
    </div>

    <?php /*------------------->
    <!-- 2.1.2. Список ботов -->
    <!---------------------*/ ?>
    <div class="row new-bot-list" data-bind="foreach: m.s2.bots_filtered" style="margin: 0;">

      <?php /*--->
      <!-- Бот -->
      <!-----*/ ?>
      <div class="new-bot-container col-lg-6 col-md-6 col-sm-12 col-xs-12" data-bind="click: $root.f.s2.show_bots_interface">
        <div class="box box-common new-bot rowfix">

          <?php /*------>
          <!-- Аватар -->
          <!--------*/ ?>
          <div class="spanfix_left0 spanfix_width62 bots_avatar">

            <?php /*-------->
            <!-- Картинка -->
            <!----------*/ ?>
            <img data-bind="attr: {src: avatar_steam}">

            <?php /*------->
            <!-- ID бота -->
            <!---------*/ ?>
            <div class="bots_id">
              <span data-bind="text: id"></span>
            </div>

          </div>

          <?php /*---------->
          <!-- Информация -->
          <!------------*/ ?>
          <div class="bots_info">

            <?php /*------------>
            <!-- Никнэйм бота -->
            <!--------------*/ ?>
            <div class="bots-nickname" data-bind="css: {green_background_soft: authorization, red_background_soft: !authorization()}">
              <span data-bind="text: steam_name, attr: {title: steam_name}"></span>
            </div>

            <?php /*----------------->
            <!-- Информация о боте -->
            <!-------------------*/ ?>
            <div class="bots-additionals">

              <?php /*---------------------------->
              <!-- Количество вещей в инвентаре -->
              <!------------------------------*/ ?>
              <div title="How many items the bot has in it's inventory">
                <i class="fa fa-fw fa-cube"></i>
                <span data-bind="text: inventory_count"></span>
              </div>

              <?php /*------------------------------->
              <!-- В каком кол-ве игр задействован -->
              <!---------------------------------*/ ?>
              <div title="How many games the bot is involved in">
                <i class="fa fa-fw fa-gamepad"></i>
                <span data-bind="text: '1'"></span>
              </div>

              <?php /*--------------------------------->
              <!-- Какие торговые операции разрешены -->
              <!-----------------------------------*/ ?>
              <div title="What trade opeartions is allowed">

                <?php /*---------------------------------------------->
                <!-- Если разрешены входящие, и запрещены исходящие -->
                <!------------------------------------------------*/ ?>
                <i class="fa fa-fw fa-long-arrow-down" data-bind="visible: ison_incoming() && !ison_outcoming()"></i>

                <?php /*---------------------------------------------->
                <!-- Если разрешены исходящие, и запрещены входящие -->
                <!------------------------------------------------*/ ?>
                <i class="fa fa-fw fa-long-arrow-up" data-bind="visible: !ison_incoming() && ison_outcoming()"></i>

                <?php /*------------------>
                <!-- Если все разрешены -->
                <!--------------------*/ ?>
                <i class="fa fa-fw fa-arrows-v" data-bind="visible: ison_incoming() && ison_outcoming()"></i>

                <?php /*------------------------>
                <!-- Если ничего не разрешено -->
                <!--------------------------*/ ?>
                <i class="fa fa-fw fa-ban" data-bind="visible: !ison_incoming() && !ison_outcoming()"></i>

              </div>

            </div>

          </div>

        </div>
      </div>

    </div>

  </div>

  <?php /*--------------------------------------->
  <!-- 2.2. Правый столбец (панель управления) -->
  <!-----------------------------------------*/ ?>
  <div class="col-md-5 col-sm-5 col-xs-5" style="padding-left: 0;">
    
    <?php /*-------------------------->
    <!-- 2.2.1. Панель инструментов -->
    <!----------------------------*/ ?>
    <div class="box box-common">
      <div class="box-body">

        <?php /*---------->
        <!-- 1] Actions -->
        <!------------*/ ?>
        <div>

          <?php /*--------->
          <!-- Заголовок -->
          <!-----------*/ ?>
          <div class="header-note">
            <span>New bot</span>
          </div>

          <?php /*---------------------->
          <!-- Добавление нового бота -->
          <!------------------------*/ ?>
          <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-8">
              <input class="form-control input-sm" data-bind="textInput: m.s2.newbot.steamid" placeholder="Enter bot's steam id...">
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left: 0;">
              <button type="button" class="btn btn-block btn-success btn-sm" data-bind="click: f.s2.add_new_bot">Add new bot</button>
            </div>
          </div>

        </div>

        <?php /*---------->
        <!-- 2] Sort by -->
        <!------------*/ ?>
        <div>

          <?php /*--------->
          <!-- Заголовок -->
          <!-----------*/ ?>
          <div class="header-note">
            <span>Sort by</span>
          </div>

          <?php /*---------->
          <!-- Сортировка -->
          <!------------*/ ?>
          <select class="form-control" data-bind="options: m.s2.sortbots.options, optionsText: function(item) { return item().text; }, value: m.s2.sortbots.choosen, event: {change: f.s2.sortfunc}"></select>

        </div>

        <?php /*---------->
        <!-- 3] Filters -->
        <!------------*/ ?>
        <div style="margin-top: 10px;">

          <?php /*--------->
          <!-- Заголовок -->
          <!-----------*/ ?>
          <div class="header-note">
            <span>Filters</span>
          </div>

          <?php /*------->
          <!-- Фильтры -->
          <!---------*/ ?>
          <div class="filters-box">

            <?php /*------------->
            <!-- 3.1] По имени -->
            <!---------------*/ ?>
            <div>

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By name</span>
              </div>

              <?php /*------>
              <!-- Фильтр -->
              <!--------*/ ?>
              <div>
                <input class="form-control input-sm" data-bind="textInput: m.s2.filterbots.name" placeholder="Enter bot's steam name...">
              </div>

            </div>

            <?php /*---------------->
            <!-- 3.2] По Steam ID -->
            <!------------------*/ ?>
            <div>

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By Steam ID</span>
              </div>

              <?php /*------>
              <!-- Фильтр -->
              <!--------*/ ?>
              <div>
                <input class="form-control input-sm" data-bind="textInput: m.s2.filterbots.steamid" placeholder="Enter bot's steam id...">
              </div>

            </div>

            <?php /*---------------------------->
            <!-- 3.3] По торговым разрешениям -->
            <!------------------------------*/ ?>
            <div class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By trade permissions</span>
              </div>

              <?php /*------>
              <!-- Фильтр -->
              <!--------*/ ?>
              <div class="form-group">

                <?php /*----------------->
                <!-- Create and cancel -->
                <!-------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterbots.tradepermissions.create_and_cancel">
                    Create and cancel
                  </label>
                </div>

                <?php /*------------------>
                <!-- Accept and decline -->
                <!--------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterbots.tradepermissions.accept_and_decline">
                    Accept and decline
                  </label>
                </div>

              </div>

            </div>

            <?php /*------------->
            <!-- 3.4] По играм -->
            <!---------------*/ ?>
            <div style="display: none" class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By games</span>
              </div>

              <?php /*------>
              <!-- Фильтр -->
              <!--------*/ ?>
              <div class="form-group">

                <?php /*----------------->
                <!-- Create and cancel -->
                <!-------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterbots.games.lottery">
                    Lottery
                  </label>
                </div>

              </div>

            </div>

          </div>

          <?php /*---------------------->
          <!-- Кнопка "Reset filters" -->
          <!------------------------*/ ?>
          <div>
            <div style="padding-left: 0;">
              <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.reset_filters">Reset filters</button>
            </div>
          </div>

        </div>


      </div>
    </div>

  </div>

</div>


<?php /*----------------------------->
<!-- 3. Интерфейс кликнутого бота  -->
<!-------------------------------*/ ?>
<div style="display: none" class="ibot_container" data-bind="visible: m.s1.selected_group().id() == 2">

  <?php /*----------------------------------------------->
  <!-- 3.1. Левый столбец: кнопка "Назад" и левое меню -->
  <!-------------------------------------------------*/ ?>
  <div class="spanfix_left0 spanfix_width120 ibot_left_column">

    <?php /*--------------------->
    <!-- 3.1.1. Кнопка "назад" -->
    <!-----------------------*/ ?>
    <div class="box box_back_style">
      <div class="box-body back_link" style="padding-top: 0; padding-bottom: 0;" data-bind="click: f.s1.choose_subdoc.bind($data, {group: 'bots', subdoc: 'bots'})">
        <span>
          <i class="fa fa-long-arrow-left" style="font-size: 32px;"></i>&nbsp;&nbsp;
        </span>
      </div>
    </div>

    <?php /*--------------------------------->
    <!-- 3.1.2. Левое меню интерфейса бота -->
    <!-----------------------------------*/ ?>
    <div class="box_leftmenu_style" >
      <ul>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Properties'}, click: f.s1.choose_subdoc.bind($data, {group: 'bot', subdoc: 'properties', without_reload: '1'})"><span>Properties</span></li>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Authcode'}, click: f.s1.choose_subdoc.bind($data, {group: 'bot', subdoc: 'authcode', without_reload: '1'})"><span>Auth code</span></li>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Authorization'}, click: f.s1.choose_subdoc.bind($data, {group: 'bot', subdoc: 'authorization', without_reload: '1'})"><span>Authorization</span></li>
      </ul>
      <ul>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Newtrade'}, click: f.s1.choose_subdoc.bind($data, {group: 'bot', subdoc: 'newtrade', without_reload: '1'})"><span>New trade</span></li>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Tradeoffers'}, click: f.s1.choose_subdoc.bind($data, {group: 'bot', subdoc: 'tradeoffers', without_reload: '1'})"><span>Trade offers</span></li>
      </ul>
    </div>

  </div>

  <?php /*-------------------------------------------------->
  <!-- 3.2. Правый столбец: имя бота и контент интерфейса -->
  <!----------------------------------------------------*/ ?>
  <div class="ibot_right_column">

    <?php /*--------------------->
    <!-- 3.2.1. Хлебные крошки -->
    <!-----------------------*/ ?>
    <div class="box box_back_style">
      <div class="box-body subdoc_title">
        <span>Bots &nbsp; → &nbsp; </span>
        <span data-bind="text: m.s2.edit.steam_name"></span>
        <span data-bind="text: '(' + m.s2.edit.steamid() + ')', visible: m.s2.edit.steamid" style="font-size: 13px; color: #ccc;"></span>
      </div>
    </div>

    <?php /*----------------------------------->
    <!-- 3.2.2. Контент-бокс интерфейса бота -->
    <!-------------------------------------*/ ?>
    <div class="content_box_wrapper">
      <div class="content_box">

        <?php /*------------->
        <!-- 1] Properties -->
        <!---------------*/ ?>
        <div style="display: none" class="content_in_content_box" data-bind="visible: m.s1.selected_subdoc().name() == 'Properties'">

          <?php /*------------------>
          <!-- 1.1] Аватарка бота -->
          <!--------------------*/ ?>
          <div class="botava">
            <img src="http://placehold.it/100x100/fafafa?text=avatar" data-bind="attr: {src: m.s2.edit.avatar_steam}">
            <button type="button" class="btn btn-block btn-danger" data-bind="click: f.s2.delete_bot">Delete bot</button>
          </div>

          <?php /*---------------------------------------->
          <!-- 1.2] Форма для редактирования полей бота -->
          <!------------------------------------------*/ ?>
          <div class="botedit" style="padding-bottom: 30px;">

            <?php /*---------------->
            <!-- Базовые свойства -->
            <!------------------*/ ?>
            <div class="form-horizontal">

              <?php /* Заголовок -->
              <!---------------*/ ?>
              <div class="header-note">
                <span>Basic properties</span>
              </div>

              <?php /* 1] id -->
              <!-----------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">ID</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.id" disabled="">
                </div>
              </div>

              <?php /* 2] apikey -->
              <!---------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">apikey</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.apikey" disabled="">
                </div>
              </div>

              <?php /* 3] apikey_domain -->
              <!----------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">apikey_domain</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.apikey_domain">
                </div>
              </div>

              <?php /* 4] trade_url -->
              <!------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">trade_url</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.trade_url">
                </div>
              </div>

              <?php /* 5] login -->
              <!--------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">login</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.login">
                </div>
              </div>

              <?php /* 6] password -->
              <!-----------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">password</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.password">
                </div>
              </div>

              <?php /* 7] steamid -->
              <!----------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">steamid</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.steamid">
                </div>
              </div>

            </div>

            <?php /*--------------------------------->
            <!-- Свойства мобильной аутентификации -->
            <!-----------------------------------*/ ?>
            <div class="form-horizontal">

              <?php /* Заголовок -->
              <!---------------*/ ?>
              <div class="header-note">
                <span>Mobile authentication properties</span>
              </div>

              <?php /* 1] shared_secret -->
              <!----------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">shared_secret</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.shared_secret">
                </div>
              </div>

              <?php /* 2] serial_number -->
              <!----------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">serial_number</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.serial_number">
                </div>
              </div>

              <?php /* 3] revocation_code -->
              <!------------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">revocation_code</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.revocation_code">
                </div>
              </div>

              <?php /* 4] uri -->
              <!------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">uri</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.uri">
                </div>
              </div>

              <?php /* 5] server_time -->
              <!--------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">server_time</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.server_time">
                </div>
              </div>

              <?php /* 6] account_name -->
              <!---------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">account_name</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.account_name">
                </div>
              </div>

              <?php /* 7] token_gid -->
              <!-------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">token_gid</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.token_gid">
                </div>
              </div>

              <?php /* 8] identity_secret -->
              <!-------------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">identity_secret</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.identity_secret">
                </div>
              </div>

              <?php /* 9] secret_1 -->
              <!------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">secret_1</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.secret_1">
                </div>
              </div>

              <?php /* 10] device_id -->
              <!-------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">device_id</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="textInput: m.s2.edit.device_id">
                </div>
              </div>

            </div>

            <?php /*-------------->
            <!-- Торговые права -->
            <!----------------*/ ?>
            <div class="form-horizontal">

              <?php /* Заголовок -->
              <!---------------*/ ?>
              <div class="header-note">
                <span>Trade permissions</span>
              </div>

              <?php /* 1] ison_incoming -->
              <!----------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">Accept and decline</div>
                <div class="col-sm-8">
                  <select class="form-control input-sm" data-bind="options: m.s2.options_true_false, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().value(); }, value: m.s2.edit.ison_incoming"></select>
                </div>
              </div>

              <?php /* 2] ison_outcoming -->
              <!-----------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">Create and cancel</div>
                <div class="col-sm-8">
                  <select class="form-control input-sm" data-bind="options: m.s2.options_true_false, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().value(); }, value: m.s2.edit.ison_outcoming"></select>
                </div>
              </div>

            </div>

            <?php /*------------------------>
            <!-- Кнопка "отредактировать" -->
            <!--------------------------*/ ?>
            <div>
              <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
            </div>

          </div>


        </div>

        <?php /*------------>
        <!-- 2] Auth code -->
        <!--------------*/ ?>
        <div style="display: none" class="content_in_content_box no_padding auth_code_styles" data-bind="visible: m.s1.selected_subdoc().name() == 'Authcode'">

          <?php /*--->
          <!-- Код -->
          <!-----*/ ?>
          <div class="auth_code_itself">
            <span data-bind="text: m.s4.code, visible: m.s4.is_current_code_valid"></span>
          </div>

          <?php /*----------------------------->
          <!-- Индикатор срока действия кода -->
          <!-------------------------------*/ ?>
          <div class="auth_code_styles_expire" data-bind="visible: m.s4.is_current_code_valid, style: {width: m.s4.expire_percents}"></div>

          <?php /*--------------->
          <!-- Панель загрузки -->
          <!-----------------*/ ?>
          <div class="loader">
            <div style="display: none" class="auth_code_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: !m.s4.is_current_code_valid()">
              <div></div>
            </div>
          </div>

        </div>

        <?php /*---------------->
        <!-- 3] Authorization -->
        <!------------------*/ ?>
        <div style="display: none" class="content_in_content_box row" data-bind="visible: m.s1.selected_subdoc().name() == 'Authorization'">

          <?php /*----------------------->
          <!-- Левый столбец (контент) -->
          <!-------------------------*/ ?>
          <div class="col-md-7 col-sm-7 col-xs-7 authorization-props">

            <?php /*-------------------->
            <!-- Свойства авторизации -->
            <!----------------------*/ ?>
            <div>

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Authorization properties</span>
              </div>

              <?php /*-------->
              <!-- Свойства -->
              <!----------*/ ?>
              <div class="form-horizontal">

                <?php /* 1] Authorized -->
                <!-------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Authorized</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="css: {green_background_soft: m.s2.edit.authorization, red_background_soft: !m.s2.edit.authorization()}, textInput: m.s2.edit.authorization() ? 'Yes' : 'No'" disabled="">
                  </div>
                </div>

                <?php /* 2] Session ID -->
                <!-------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">SessionID</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s2.indexes.bots[m.s2.edit.id()] ? m.s2.indexes.bots[m.s2.edit.id()].sessionid : ''" disabled="">
                  </div>
                </div>

              </div>

            </div>

            <?php /*------------------------------------------------------------->
            <!-- Инструкции на случай ошибки, свои для каждого из кодов ошибки -->
            <!---------------------------------------------------------------*/ ?>
            <div style="display: none" class="authorization_styles" data-bind="visible: m.s2.edit.authorization_last_bug_code">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Authorization errors</span>
              </div>

              <?php /*---------->
              <!-- Инструкции -->
              <!------------*/ ?>
              <div class="auth_error_descriptions">

                <?php /*------------------------------------------------->
                <!-- Код ошибки "1": recieved from Steam json is empty -->
                <!---------------------------------------------------*/ ?>
                <div data-bind="visible: m.s2.edit.authorization_last_bug_code() == 1">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, code 1</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug"></div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Try again.</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff"></div>

                </div>

                <?php /*------------------------------>
                <!-- Код ошибки "2": captcha needed -->
                <!--------------------------------*/ ?>
                <div data-bind="visible: m.s2.edit.authorization_last_bug_code() == 2">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, code 2</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug">Some error text</div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Click the button "Show the captcha..." below.</li>
                        <li>Enter text from the captcha to the field "Captcha text" below.</li>
                        <li>Push the button "Authorize..." above again, and wait.</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff form-horizontal">

                    <?php /*-------------------------->
                    <!-- 1] Кнопка "Показать капчу" -->
                    <!----------------------------*/ ?>
                    <div style="margin-bottom: 15px;">
                      <button type="button" class="btn btn-xs btn-block btn-default" data-bind="click: function(){ window.open('https://steamcommunity.com/login/rendercaptcha/?gid='+m.s2.edit.captchagid()); }">Show the captcha in separate window</button>
                    </div>

                    <?php /*----------->
                    <!-- 2] ID капчи -->
                    <!-------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">Captcha ID</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.captchagid" disabled="">
                      </div>
                    </div>

                    <?php /*-------------->
                    <!-- 3] Текст капчи -->
                    <!----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">Captcha text</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.captcha_text">
                      </div>
                    </div>

                  </div>

                </div>

                <?php /*--------------------------------->
                <!-- Код ошибки "3": 2FA code not fits -->
                <!-----------------------------------*/ ?>
                <div data-bind="visible: m.s2.edit.authorization_last_bug_code() == 3">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, code 3</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug">Some error text</div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Try again, use fresh and valid code (see the panel above).</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff"></div>

                </div>

                <?php /*--------------------------------------->
                <!-- Код ошибки "4": wrong login or password -->
                <!-----------------------------------------*/ ?>
                <div data-bind="visible: m.s2.edit.authorization_last_bug_code() == 4">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, code 4</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug">Some error text</div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Try again, double check login and password correctness.</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff"></div>

                </div>

                <?php /*--------------------------------------------------->
                <!-- Код ошибки "5": somehow in response success = false -->
                <!-----------------------------------------------------*/ ?>
                <div data-bind="visible: m.s2.edit.authorization_last_bug_code() == 5">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, code 5</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug">Some error text</div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Try again.</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff"></div>

                </div>

                <?php /*------------------------------------->
                <!-- Неизвестный код ошибки: general error -->
                <!---------------------------------------*/ ?>
                <div data-bind="visible: !m.s2.edit.authorization_last_bug_code() && m.s2.edit.authorization_last_bug()">

                  <?php /*--------------->
                  <!-- Описание ошибки -->
                  <!-----------------*/ ?>
                  <div class="error_description">
                    <div class="error_header">Authorization error, general error code</div>
                    <div class="error_text" data-bind="text: m.s2.edit.authorization_last_bug">Some error text</div>
                  </div>

                  <?php /*--------------------------->
                  <!-- Инструкции для пользователя -->
                  <!-----------------------------*/ ?>
                  <div class="user_instructions">
                    <div class="instructions_header">Instructions</div>
                    <div class="instructions">
                      <ol>
                        <li>Try again.</li>
                        <li>If it doesn't work, contact administrator of the service.</li>
                      </ol>
                    </div>
                  </div>

                  <?php /*------------->
                  <!-- Всё остальное -->
                  <!---------------*/ ?>
                  <div class="the_rest_stuff"></div>

                </div>

              </div>


            </div>

          </div>

          <?php /*---------------------------------->
          <!-- Правый столбец (панель управления) -->
          <!------------------------------------*/ ?>
          <div class="col-md-5 col-sm-5 col-xs-5">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="header-note">
              <span>Actions</span>
            </div>

            <?php /*-------------------------->
            <!-- Кнопка "Авторизовать бота" -->
            <!----------------------------*/ ?>
            <div>
              <button type="button" class="btn btn-block btn-default" data-bind="click: f.s2.authorize_bot">Authorize</button>
            </div>

          </div>

        </div>

        <?php /*----------->
        <!-- 4] Newtrade -->
        <!-------------*/ ?>
        <div style="display: none" class="content_in_content_box" data-bind="visible: m.s1.selected_subdoc().name() == 'Newtrade'">

          <?php /*----------------------------->
          <!-- Ввод и проверка торгового URL -->
          <!-------------------------------*/ ?>
          <div>

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="header-note">
              <span>Enter trade url to choose a trade partner</span>
            </div>

            <?php /*-------->
            <!-- Свойства -->
            <!----------*/ ?>
            <div class="form-horizontal">

              <?php /* 1] Authorized -->
              <!-------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label">Them trade URL</div>
                <div class="col-sm-8">
                  <input class="form-control input-sm" data-bind="css: {green_background_soft: m.s5.steam_name_partner, red_background_soft: !m.s5.steam_name_partner()}, textInput: m.s5.trade_url">
                </div>
              </div>

              <?php /* 2] Session ID -->
              <!-------------------*/ ?>
              <div class="form-group">
                <div class="col-sm-4 control-label"></div>
                <div class="col-sm-8">
                  <button type="button" class="btn btn-block btn-default btn-sm" data-bind="click: f.s5.update_tp">Check trade url and choose trade partner</button>
                </div>
              </div>

            </div>

          </div>

          <?php /*-------------------------->
          <!-- Выбранный торговый партнёр -->
          <!----------------------------*/ ?>
          <div style="display: none" data-bind="visible: m.s5.steam_name_partner">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="header-note">
              <span>Choosen trade partner</span>
            </div>

            <?php /*-------------------------------------->
            <!-- Свойства выбранного торгового партнёра -->
            <!----------------------------------------*/ ?>
            <div>

              <?php /*------------------------------>
              <!-- 1] Аватарка торгового партнёра -->
              <!--------------------------------*/ ?>
              <div class="partnerava">
                <img src="http://placehold.it/100x100/fafafa?text=avatar" data-bind="attr: {src: m.s5.avatar}">
              </div>

              <?php /*----------->
              <!-- 2] Свойства -->
              <!-------------*/ ?>
              <div class="partneredit form-horizontal" style="padding-bottom: 30px;">

                <?php /* 1] Steam name -->
                <!-------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Steam name</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s5.steam_name_partner" disabled="">
                  </div>
                </div>

                <?php /* 2] Steam ID -->
                <!-----------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Steam ID</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s5.steamid_partner" disabled="">
                  </div>
                </div>

                <?php /* 3] Partner ID -->
                <!--------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Partner ID</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s5.partner" disabled="">
                  </div>
                </div>

                <?php /* 4] Partner token -->
                <!----------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Partner token</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s5.token" disabled="">
                  </div>
                </div>

                <?php /* 5] Escrow hold, days -->
                <!--------------------------*/ ?>
                <div class="form-group">
                  <div class="col-sm-4 control-label">Escrow hold, days</div>
                  <div class="col-sm-8">
                    <input class="form-control input-sm" data-bind="textInput: m.s5.escrow_days_partner" disabled="">
                  </div>
                </div>

              </div>

            </div>

          </div>

          <?php /*--------------------------------------------------------->
          <!-- Создание и отправка нового торгового предложения партнёру -->
          <!-----------------------------------------------------------*/ ?>
          <div style="display: none" data-bind="visible: m.s5.steam_name_partner">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="header-note">
              <span>Trade</span>
            </div>

            <?php /*--------->
            <!-- Интерфейс -->
            <!-----------*/ ?>
            <div class="row">

              <?php /*----------------------------------------->
              <!-- Левый столбец (инвентари бота и партнёра) -->
              <!-------------------------------------------*/ ?>
              <div class="new-trade-column left-column col-md-7 col-sm-7 col-xs-7">

                <?php /*--------------------->
                <!-- Панель инвентаря бота -->
                <!-----------------------*/ ?>
                <div class="box">

                  <?php /*---------------------------------------->
                  <!-- Заголовочная часть и панель инструментов -->
                  <!------------------------------------------*/ ?>
                  <div class="box-header with-border">

                    <?php /*-------------------------------->
                    <!-- Заголовок, переключатель, кол-во -->
                    <!----------------------------------*/ ?>
                    <div>

                      <?php /*--------->
                      <!-- Заголовок -->
                      <!-----------*/ ?>
                      <span>The bot's inventory</span>

                      <?php /*----------------------------------->
                      <!-- Переключатель "развернуть/свернуть" -->
                      <!-------------------------------------*/ ?>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                      </button>

                      <?php /*---------------------------->
                      <!-- Количество вещей в инвентаре -->
                      <!------------------------------*/ ?>
                      <small class="small_notes"><span title="Number of selected inventory items" data-bind="text: m.s3.inventory_selected"></span> / <span title="Inventory items have passed thru the filters" data-bind="text: m.s3.found_inventory_items().length"></span> / <span title="Total number of inventory items" data-bind="text: m.s3.inventory_total"></span></small>

                    </div>

                  </div>

                  <?php /*--------->
                  <!-- Инвентарь -->
                  <!-----------*/ ?>
                  <div class="box-body no-padding" style="display: block;">

                    <?php /*------------------------------>
                    <!-- Строка поиска, кнопка "Update" -->
                    <!--------------------------------*/ ?>
                    <div class="row">

                      <?php /*------------------------------>
                      <!-- Строка поиска, кнопка "Update" -->
                      <!--------------------------------*/ ?>
                      <div class="col-md-7 col-sm-7 col-xs-7">
                        <input type="text" class="search-str" data-bind="textInput: m.s3.search_string">
                        <i class="fa fa-search" title="Search"></i>
                      </div>

                      <?php /*--------------->
                      <!-- Кнопка "Update" -->
                      <!-----------------*/ ?>
                      <div class="col-md-5 col-sm-5 col-xs-5">
                        <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.update.bind($data, {silent: false})">Update</button>
                      </div>

                    </div>

                    <?php /*-------------------->
                    <!-- Содержимое инвентаря -->
                    <!----------------------*/ ?>
                    <div class="inventory-container">

                      <?php /*-------------------------------------->
                      <!-- Надпись на случай, если инвентарь пуст -->
                      <!----------------------------------------*/ ?>
                      <div class="empty-inventory" data-bind="visible: !m.s3.inventory().length && !m.s3.is_ajax_invoking()">
                        <span>Inventory is empty. Try to update.</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s3.inventory, visible: !m.s3.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s3.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }, visible: $root.m.s3.found_inventory_items.indexOf(assetid()) != -1 && $root.m.s3.inventory_items2trade.indexOf(assetid()) == -1">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*--------------->
                      <!-- Панель загрузки -->
                      <!-----------------*/ ?>
                      <div class="loader">
                        <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s3.is_ajax_invoking">

                        </div>
                      </div>

                    </div>

                  </div>

                </div>

                <?php /*------------------------->
                <!-- Панель инвентаря партнёра -->
                <!---------------------------*/ ?>
                <div class="box">

                  <?php /*---------------------------------------->
                  <!-- Заголовочная часть и панель инструментов -->
                  <!------------------------------------------*/ ?>
                  <div class="box-header with-border">

                    <?php /*-------------------------------->
                    <!-- Заголовок, переключатель, кол-во -->
                    <!----------------------------------*/ ?>
                    <div>

                      <?php /*--------->
                      <!-- Заголовок -->
                      <!-----------*/ ?>
                      <span>The partners's inventory</span>

                      <?php /*----------------------------------->
                      <!-- Переключатель "развернуть/свернуть" -->
                      <!-------------------------------------*/ ?>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                      </button>

                      <?php /*---------------------------->
                      <!-- Количество вещей в инвентаре -->
                      <!------------------------------*/ ?>
                      <small class="small_notes"><span title="Number of selected inventory items" data-bind="text: m.s6.inventory_selected"></span> / <span title="Inventory items have passed thru the filters" data-bind="text: m.s6.found_inventory_items().length"></span> / <span title="Total number of inventory items" data-bind="text: m.s6.inventory_total"></span></small>

                    </div>

                  </div>

                  <?php /*--------->
                  <!-- Инвентарь -->
                  <!-----------*/ ?>
                  <div class="box-body no-padding" style="display: block;">

                    <?php /*------------------------------>
                    <!-- Строка поиска, кнопка "Update" -->
                    <!--------------------------------*/ ?>
                    <div class="row">

                      <?php /*------------------------------>
                      <!-- Строка поиска, кнопка "Update" -->
                      <!--------------------------------*/ ?>
                      <div class="col-md-7 col-sm-7 col-xs-7">
                        <input type="text" class="search-str" data-bind="textInput: m.s6.search_string">
                        <i class="fa fa-search" title="Search"></i>
                      </div>

                      <?php /*--------------->
                      <!-- Кнопка "Update" -->
                      <!-----------------*/ ?>
                      <div class="col-md-5 col-sm-5 col-xs-5">
                        <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s6.update.bind($data, {silent: false})">Update</button>
                      </div>

                    </div>

                    <?php /*-------------------->
                    <!-- Содержимое инвентаря -->
                    <!----------------------*/ ?>
                    <div class="inventory-container">

                      <?php /*-------------------------------------->
                      <!-- Надпись на случай, если инвентарь пуст -->
                      <!----------------------------------------*/ ?>
                      <div class="empty-inventory" data-bind="visible: !m.s6.inventory().length && !m.s6.is_ajax_invoking()">
                        <span>Inventory is empty. Try to update.</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s6.inventory, visible: !m.s6.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s6.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }, visible: $root.m.s6.found_inventory_items.indexOf(assetid()) != -1 && $root.m.s6.inventory_items2trade.indexOf(assetid()) == -1">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*--------------->
                      <!-- Панель загрузки -->
                      <!-----------------*/ ?>
                      <div class="loader">
                        <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s6.is_ajax_invoking">

                        </div>
                      </div>

                    </div>

                  </div>

                </div>


              </div>

              <?php /*---------------------------------------------------->
              <!-- Правый столбец (вещи для обмена и панель управления) -->
              <!------------------------------------------------------*/ ?>
              <div class="new-trade-column right-column col-md-5 col-sm-5 col-xs-5">

                <?php /*------------------------>
                <!-- Вещи, которые отдаёт бот -->
                <!--------------------------*/ ?>
                <div class="box">

                  <?php /*---------------------------------------->
                  <!-- Заголовочная часть и панель инструментов -->
                  <!------------------------------------------*/ ?>
                  <div class="box-header with-border">

                    <?php /*-------------------------------->
                    <!-- Заголовок, переключатель, кол-во -->
                    <!----------------------------------*/ ?>
                    <div>

                      <?php /*--------->
                      <!-- Заголовок -->
                      <!-----------*/ ?>
                      <span>Items to give</span>

                      <?php /*----------------------------------->
                      <!-- Переключатель "развернуть/свернуть" -->
                      <!-------------------------------------*/ ?>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                      </button>

                      <?php /*-------------------------------------->
                      <!-- Количество вещей, которые будут отданы -->
                      <!----------------------------------------*/ ?>
                      <small class="small_notes"><span title="Number of selected inventory items" data-bind="text: m.s3.inventory_selected"></span></small>

                    </div>

                  </div>

                  <?php /*------------------------------------>
                  <!-- Вещи инвентаря, которые будут отданы -->
                  <!--------------------------------------*/ ?>
                  <div class="box-body no-padding" style="display: block;">

                    <?php /*-------------------->
                    <!-- Содержимое инвентаря -->
                    <!----------------------*/ ?>
                    <div class="inventory-container">

                      <?php /*----------------------------------------------->
                      <!-- Надпись на случай, если никакие вещи не выбраны -->
                      <!-------------------------------------------------*/ ?>
                      <div class="empty-inventory" data-bind="visible: !m.s3.inventory_items2trade().length">
                        <span>No items to give.</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s3.inventory, visible: !m.s3.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s3.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }, visible: $root.m.s3.inventory_items2trade.indexOf(assetid()) != -1">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*--------------->
                      <!-- Панель загрузки -->
                      <!-----------------*/ ?>
                      <div class="loader">
                        <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s3.is_ajax_invoking">

                        </div>
                      </div>

                    </div>

                  </div>

                </div>

                <?php /*-------------------------->
                <!-- Вещи, которые получает бот -->
                <!----------------------------*/ ?>
                <div class="box">

                  <?php /*---------------------------------------->
                  <!-- Заголовочная часть и панель инструментов -->
                  <!------------------------------------------*/ ?>
                  <div class="box-header with-border">

                    <?php /*-------------------------------->
                    <!-- Заголовок, переключатель, кол-во -->
                    <!----------------------------------*/ ?>
                    <div>

                      <?php /*--------->
                      <!-- Заголовок -->
                      <!-----------*/ ?>
                      <span>Items to get</span>

                      <?php /*----------------------------------->
                      <!-- Переключатель "развернуть/свернуть" -->
                      <!-------------------------------------*/ ?>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                      </button>

                      <?php /*---------------------------------------->
                      <!-- Количество вещей, которые будут получены -->
                      <!------------------------------------------*/ ?>
                      <small class="small_notes"><span title="Number of selected inventory items" data-bind="text: m.s6.inventory_selected"></span></small>

                    </div>

                  </div>

                  <?php /*-------------------------------------->
                  <!-- Вещи инвентаря, которые будут получены -->
                  <!----------------------------------------*/ ?>
                  <div class="box-body no-padding" style="display: block;">

                    <?php /*-------------------->
                    <!-- Содержимое инвентаря -->
                    <!----------------------*/ ?>
                    <div class="inventory-container">

                      <?php /*----------------------------------------------->
                      <!-- Надпись на случай, если никакие вещи не выбраны -->
                      <!-------------------------------------------------*/ ?>
                      <div class="empty-inventory" data-bind="visible: !m.s6.inventory_items2trade().length">
                        <span>No items to get.</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s6.inventory, visible: !m.s6.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s6.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }, visible: $root.m.s6.inventory_items2trade.indexOf(assetid()) != -1">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*--------------->
                      <!-- Панель загрузки -->
                      <!-----------------*/ ?>
                      <div class="loader">
                        <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s6.is_ajax_invoking">

                        </div>
                      </div>

                    </div>

                  </div>

                </div>

                <?php /*-------------------------------------------->
                <!-- Экономика планируемого торгового предложения -->
                <!----------------------------------------------*/ ?>
                <div class="box economy-container">

                  <?php /*--------->
                  <!-- Заголовок -->
                  <!-----------*/ ?>
                  <div>
                    <span>Economy</span>
                  </div>

                  <?php /*---------->
                  <!-- Содержимое -->
                  <!------------*/ ?>
                  <div class="economy-content">

                    <?php /*-------------->
                    <!-- Дебет и кредит -->
                    <!----------------*/ ?>
                    <div class="debit-and-credit">

                      <?php /*----->
                      <!-- Дебет -->
                      <!-------*/ ?>
                      <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <span>Debit</span>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <span data-bind="text: m.s6.items2trade_sumprice() + '$'"></span>
                        </div>
                      </div>

                      <?php /*------>
                      <!-- Кредит -->
                      <!--------*/ ?>
                      <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <span>Credit</span>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <span data-bind="text: m.s3.items2trade_sumprice() + '$'"></span>
                        </div>
                      </div>

                    </div>

                    <?php /*------>
                    <!-- Баланс -->
                    <!--------*/ ?>
                    <div class="balance">

                      <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <span>Balance</span>
                        </div>
                        <div class="balance-class col-md-6 col-sm-6 col-xs-6">
                          <span data-bind="text: +m.s6.items2trade_sumprice() - +m.s3.items2trade_sumprice() + '$', css: {greentext: (+m.s6.items2trade_sumprice() - +m.s3.items2trade_sumprice()) > 0, redtext: (+m.s6.items2trade_sumprice() - +m.s3.items2trade_sumprice()) < 0}"></span>
                        </div>
                      </div>

                    </div>

                  </div>

                </div>

                <?php /*-------->
                <!-- Действия -->
                <!----------*/ ?>
                <div class="box">

                  <?php /*------------------------------>
                  <!-- Отправить торговое предложение -->
                  <!--------------------------------*/ ?>
                  <button type="button" class="btn btn-block btn-success btn-sm" data-bind="click: f.s0.send_trade_offer">Send trade offer</button>

                </div>

              </div>

            </div>

          </div>

        </div>

        <?php /*-------------->
        <!-- 5] Tradeoffers -->
        <!----------------*/ ?>
        <div style="display: none" class="content_in_content_box trade-offers row" data-bind="visible: m.s1.selected_subdoc().name() == 'Tradeoffers'">

          <?php /*----------------------->
          <!-- Левый столбец (контент) -->
          <!-------------------------*/ ?>
          <div class="col-md-7 col-sm-7 col-xs-7">

            <?php /*----------->
            <!-- 1] Incoming -->
            <!-------------*/ ?>
            <div style="display: none" data-bind="visible: m.s7.types.choosen() == 1">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Incoming</span>
                <small class="small_notes" style="cursor: default">- <span title="Number of incoming trade offers" data-bind="text: m.s7.tradeoffers_incoming().length"></span></small>
              </div>

              <?php /*-------------------->
              <!-- Торговые предложения -->
              <!----------------------*/ ?>
              <div class="trade-offers-list" data-bind="foreach: m.s7.tradeoffers_incoming">

                <?php /*-------------------->
                <!-- Торговое предложения -->
                <!----------------------*/ ?>
                <div class="trade-offer">

                  <?php /*----->
                  <!-- Шапка -->
                  <!-------*/ ?>
                  <div class="head-part row">

                    <div class="col-md-8 col-sm-12 col-xs-12" style="text-align: left; padding-left: 0;">

                      <?php /*--------------->
                      <!-- Аватар партнёра -->
                      <!-----------------*/ ?>
                      <div class="avatar">
                        <img data-bind="attr: {src: avatar}">
                      </div>

                      <?php /*------------>
                      <!-- Имя партнёра -->
                      <!--------------*/ ?>
                      <div>
                        <span data-bind="text: name_of_the_partner"></span>
                      </div>

                      <?php /*----------------->
                      <!-- Steam ID партнёра -->
                      <!-------------------*/ ?>
                      <div class="partner-steam-id">
                        <span data-bind="text: '(' + partner_steamid() + ')'"></span>
                      </div>

                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12" style="text-align: right; padding-right: 5px;">

                      <?php /*------------------------>
                      <!-- ID торгового предложения -->
                      <!--------------------------*/ ?>
                      <div class="trade-offer-id">
                        <span data-bind="text: '#' + tradeofferid()"></span>
                      </div>

                    </div>

                  </div>

                  <?php /*---->
                  <!-- Тело -->
                  <!------*/ ?>
                  <div class="body-part row">

                    <?php /*------------>
                    <!-- Бот получает -->
                    <!--------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gets</span>
                        <span class="price" data-bind="text: '($' + total_sum_receive() + ')', visible: total_sum_receive() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*---------------------------------->
                      <!-- Список вещей, которые бот получает -->
                      <!------------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_receive, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_receive().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                    <?php /*---------->
                    <!-- Бот отдаёт -->
                    <!------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gives</span>
                        <span class="price" data-bind="text: '($' + total_sum_give() + ')', visible: total_sum_give() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*-------------------------------->
                      <!-- Список вещей, которые бот отдаёт -->
                      <!----------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_give, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_give().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                  </div>

                  <?php /*------>
                  <!-- Подвал -->
                  <!--------*/ ?>
                  <div class="footer-part row">

                    <?php /*---------------------------------------------->
                    <!-- Дата и время последнего обновления / истечения -->
                    <!------------------------------------------------*/ ?>
                    <div class="time_updated_created col-md-7 col-sm-12 col-xs-12">

                      <div style="display: none" data-bind="visible: time_updated() && expiration_time()">
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, time_updated())()" title="Last update"></span>
                        <span>, </span>
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, expiration_time())()" title="Expiration date/time"></span>
                      </div>

                    </div>

                    <?php /*----------------->
                    <!-- Кнопка "Действия" -->
                    <!-------------------*/ ?>
                    <div class="trade-offer-actions col-md-5 col-sm-12 col-xs-12">

                      <div class="btn-group" data-bind="visible: mode() == 1 || mode() == 3">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">

                            <?php /*------>
                            <!-- Accept -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.accept.bind($data, {silent: false})">Accept</a></li>

                            <?php /*------->
                            <!-- Decline -->
                            <!---------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.decline.bind($data, {silent: false})">Decline</a></li>

                            <?php /*---------->
                            <!-- Get prices -->
                            <!------------*/ ?>
                            <li data-bind="visible: mode() == 1 || mode() == 3"><a href="#" data-bind="click: $root.f.s7.get_prices.bind($data, {mode: mode()})">Get prices</a></li>

                            <?php /*------>
                            <!-- Cancel -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 3"><a href="#" data-bind="click: $root.f.s7.cancel.bind($data, {silent: false})">Cancel</a></li>

                          </ul>
                        </div>
                      </div>

                    </div>


                  </div>

                </div>

              </div>

              <?php /*------------------------------------->
              <!-- Если торговые предложения отсутствуют -->
              <!---------------------------------------*/ ?>
              <div class="empty-note" style="display: none" data-bind="visible: !m.s7.tradeoffers_incoming().length">
                <span>Empty</span>
              </div>

            </div>

            <?php /*------------------->
            <!-- 2] Incoming history -->
            <!---------------------*/ ?>
            <div style="display: none" data-bind="visible: m.s7.types.choosen() == 2">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Incoming offers</span>
                <small class="small_notes" style="cursor: default">- <span title="Number of incoming history trade offers" data-bind="text: m.s7.tradeoffers_incoming_history().length"></span></small>
              </div>

              <?php /*-------------------->
              <!-- Торговые предложения -->
              <!----------------------*/ ?>
              <div class="trade-offers-list" data-bind="foreach: m.s7.tradeoffers_incoming_history">

                <?php /*-------------------->
                <!-- Торговое предложения -->
                <!----------------------*/ ?>
                <div class="trade-offer">

                  <?php /*----->
                  <!-- Шапка -->
                  <!-------*/ ?>
                  <div class="head-part row">

                    <div class="col-md-8 col-sm-12 col-xs-12" style="text-align: left; padding-left: 0;">

                      <?php /*--------------->
                      <!-- Аватар партнёра -->
                      <!-----------------*/ ?>
                      <div class="avatar">
                        <img data-bind="attr: {src: avatar}">
                      </div>

                      <?php /*------------>
                      <!-- Имя партнёра -->
                      <!--------------*/ ?>
                      <div>
                        <span data-bind="text: name_of_the_partner"></span>
                      </div>

                      <?php /*----------------->
                      <!-- Steam ID партнёра -->
                      <!-------------------*/ ?>
                      <div class="partner-steam-id">
                        <span data-bind="text: '(' + partner_steamid() + ')'"></span>
                      </div>

                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12" style="text-align: right; padding-right: 5px;">

                      <?php /*------------------------>
                      <!-- ID торгового предложения -->
                      <!--------------------------*/ ?>
                      <div class="trade-offer-id">
                        <span data-bind="text: '#' + tradeofferid()"></span>
                      </div>

                    </div>

                  </div>

                  <?php /*---->
                  <!-- Тело -->
                  <!------*/ ?>
                  <div class="body-part row">

                    <?php /*------------>
                    <!-- Бот получает -->
                    <!--------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gets</span>
                        <span class="price" data-bind="text: '($' + total_sum_receive() + ')', visible: total_sum_receive() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*---------------------------------->
                      <!-- Список вещей, которые бот получает -->
                      <!------------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_receive, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_receive().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                    <?php /*---------->
                    <!-- Бот отдаёт -->
                    <!------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gives</span>
                        <span class="price" data-bind="text: '($' + total_sum_give() + ')', visible: total_sum_give() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*-------------------------------->
                      <!-- Список вещей, которые бот отдаёт -->
                      <!----------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_give, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_give().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                  </div>

                  <?php /*------>
                  <!-- Подвал -->
                  <!--------*/ ?>
                  <div class="footer-part row">

                    <?php /*---------------------------------------------->
                    <!-- Дата и время последнего обновления / истечения -->
                    <!------------------------------------------------*/ ?>
                    <div class="time_updated_created col-md-7 col-sm-12 col-xs-12">

                      <div style="display: none" data-bind="visible: time_updated() && expiration_time()">
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, time_updated())()" title="Last update"></span>
                        <span>, </span>
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, expiration_time())()" title="Expiration date/time"></span>
                      </div>

                    </div>

                    <?php /*----------------->
                    <!-- Кнопка "Действия" -->
                    <!-------------------*/ ?>
                    <div class="trade-offer-actions col-md-5 col-sm-12 col-xs-12">

                      <div class="btn-group" data-bind="visible: mode() == 1 || mode() == 3">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">

                            <?php /*------>
                            <!-- Accept -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.accept.bind($data, {silent: false})">Accept</a></li>

                            <?php /*------->
                            <!-- Decline -->
                            <!---------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.decline.bind($data, {silent: false})">Decline</a></li>

                            <?php /*---------->
                            <!-- Get prices -->
                            <!------------*/ ?>
                            <li data-bind="visible: mode() == 1 || mode() == 3"><a href="#" data-bind="click: $root.f.s7.get_prices.bind($data, {mode: mode()})">Get prices</a></li>

                            <?php /*------>
                            <!-- Cancel -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 3"><a href="#" data-bind="click: $root.f.s7.cancel.bind($data, {silent: false})">Cancel</a></li>

                          </ul>
                        </div>
                      </div>

                    </div>


                  </div>

                </div>

              </div>

              <?php /*------------------------------------->
              <!-- Если торговые предложения отсутствуют -->
              <!---------------------------------------*/ ?>
              <div class="empty-note" style="display: none" data-bind="visible: !m.s7.tradeoffers_incoming_history().length">
                <span>Empty</span>
              </div>

            </div>

            <?php /*------->
            <!-- 3] Sent -->
            <!---------*/ ?>
            <div style="display: none" data-bind="visible: m.s7.types.choosen() == 3">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Sent offets</span>
                <small class="small_notes" style="cursor: default">- <span title="Number of sent trade offers" data-bind="text: m.s7.tradeoffers_sent().length"></span></small>
              </div>

              <?php /*-------------------->
              <!-- Торговые предложения -->
              <!----------------------*/ ?>
              <div class="trade-offers-list" data-bind="foreach: m.s7.tradeoffers_sent">

                <?php /*-------------------->
                <!-- Торговое предложения -->
                <!----------------------*/ ?>
                <div class="trade-offer">

                  <?php /*----->
                  <!-- Шапка -->
                  <!-------*/ ?>
                  <div class="head-part row">

                    <div class="col-md-8 col-sm-12 col-xs-12" style="text-align: left; padding-left: 0;">

                      <?php /*--------------->
                      <!-- Аватар партнёра -->
                      <!-----------------*/ ?>
                      <div class="avatar">
                        <img data-bind="attr: {src: avatar}">
                      </div>

                      <?php /*------------>
                      <!-- Имя партнёра -->
                      <!--------------*/ ?>
                      <div>
                        <span data-bind="text: name_of_the_partner"></span>
                      </div>

                      <?php /*----------------->
                      <!-- Steam ID партнёра -->
                      <!-------------------*/ ?>
                      <div class="partner-steam-id">
                        <span data-bind="text: '(' + partner_steamid() + ')'"></span>
                      </div>

                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12" style="text-align: right; padding-right: 5px;">

                      <?php /*------------------------>
                      <!-- ID торгового предложения -->
                      <!--------------------------*/ ?>
                      <div class="trade-offer-id">
                        <span data-bind="text: '#' + tradeofferid()"></span>
                      </div>

                    </div>

                  </div>

                  <?php /*---->
                  <!-- Тело -->
                  <!------*/ ?>
                  <div class="body-part row">

                    <?php /*------------>
                    <!-- Бот получает -->
                    <!--------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gets</span>
                        <span class="price" data-bind="text: '($' + total_sum_receive() + ')', visible: total_sum_receive() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*---------------------------------->
                      <!-- Список вещей, которые бот получает -->
                      <!------------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_receive, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_receive().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                    <?php /*---------->
                    <!-- Бот отдаёт -->
                    <!------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gives</span>
                        <span class="price" data-bind="text: '($' + total_sum_give() + ')', visible: total_sum_give() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*-------------------------------->
                      <!-- Список вещей, которые бот отдаёт -->
                      <!----------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_give, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_give().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                  </div>

                  <?php /*------>
                  <!-- Подвал -->
                  <!--------*/ ?>
                  <div class="footer-part row">

                    <?php /*---------------------------------------------->
                    <!-- Дата и время последнего обновления / истечения -->
                    <!------------------------------------------------*/ ?>
                    <div class="time_updated_created col-md-7 col-sm-12 col-xs-12">

                      <div style="display: none" data-bind="visible: time_updated() && expiration_time()">
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, time_updated())()" title="Last update"></span>
                        <span>, </span>
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, expiration_time())()" title="Expiration date/time"></span>
                      </div>

                    </div>

                    <?php /*----------------->
                    <!-- Кнопка "Действия" -->
                    <!-------------------*/ ?>
                    <div class="trade-offer-actions col-md-5 col-sm-12 col-xs-12">

                      <div class="btn-group" data-bind="visible: mode() == 1 || mode() == 3">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">

                            <?php /*------>
                            <!-- Accept -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.accept.bind($data, {silent: false})">Accept</a></li>

                            <?php /*------->
                            <!-- Decline -->
                            <!---------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.decline.bind($data, {silent: false})">Decline</a></li>

                            <?php /*---------->
                            <!-- Get prices -->
                            <!------------*/ ?>
                            <li data-bind="visible: mode() == 1 || mode() == 3"><a href="#" data-bind="click: $root.f.s7.get_prices.bind($data, {mode: mode()})">Get prices</a></li>

                            <?php /*------>
                            <!-- Cancel -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 3"><a href="#" data-bind="click: $root.f.s7.cancel.bind($data, {silent: false})">Cancel</a></li>

                          </ul>
                        </div>
                      </div>

                    </div>


                  </div>

                </div>

              </div>

              <?php /*------------------------------------->
              <!-- Если торговые предложения отсутствуют -->
              <!---------------------------------------*/ ?>
              <div class="empty-note" style="display: none" data-bind="visible: !m.s7.tradeoffers_sent().length">
                <span>Empty</span>
              </div>

            </div>

            <?php /*--------------->
            <!-- 4] Sent history -->
            <!-----------------*/ ?>
            <div style="display: none" data-bind="visible: m.s7.types.choosen() == 4">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note">
                <span>Sent history</span>
                <small class="small_notes" style="cursor: default">- <span title="Number of sent history trade offers" data-bind="text: m.s7.tradeoffers_sent_history().length"></span></small>
              </div>

              <?php /*-------------------->
              <!-- Торговые предложения -->
              <!----------------------*/ ?>
              <div class="trade-offers-list" data-bind="foreach: m.s7.tradeoffers_sent_history">

                <?php /*-------------------->
                <!-- Торговое предложения -->
                <!----------------------*/ ?>
                <div class="trade-offer">

                  <?php /*----->
                  <!-- Шапка -->
                  <!-------*/ ?>
                  <div class="head-part row">

                    <div class="col-md-8 col-sm-12 col-xs-12" style="text-align: left; padding-left: 0;">

                      <?php /*--------------->
                      <!-- Аватар партнёра -->
                      <!-----------------*/ ?>
                      <div class="avatar">
                        <img data-bind="attr: {src: avatar}">
                      </div>

                      <?php /*------------>
                      <!-- Имя партнёра -->
                      <!--------------*/ ?>
                      <div>
                        <span data-bind="text: name_of_the_partner"></span>
                      </div>

                      <?php /*----------------->
                      <!-- Steam ID партнёра -->
                      <!-------------------*/ ?>
                      <div class="partner-steam-id">
                        <span data-bind="text: '(' + partner_steamid() + ')'"></span>
                      </div>

                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12" style="text-align: right; padding-right: 5px;">

                      <?php /*------------------------>
                      <!-- ID торгового предложения -->
                      <!--------------------------*/ ?>
                      <div class="trade-offer-id">
                        <span data-bind="text: '#' + tradeofferid()"></span>
                      </div>

                    </div>

                  </div>

                  <?php /*---->
                  <!-- Тело -->
                  <!------*/ ?>
                  <div class="body-part row">

                    <?php /*------------>
                    <!-- Бот получает -->
                    <!--------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gets</span>
                        <span class="price" data-bind="text: '($' + total_sum_receive() + ')', visible: total_sum_receive() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*---------------------------------->
                      <!-- Список вещей, которые бот получает -->
                      <!------------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_receive, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_receive().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                    <?php /*---------->
                    <!-- Бот отдаёт -->
                    <!------------*/ ?>
                    <div class="bot-gives-gets col-md-6 col-sm-6 col-xs-12">

                      <?php /*----------------->
                      <!-- Заголовок и дебет -->
                      <!-------------------*/ ?>
                      <div class="header-bot-gives-gets">
                        <span class="title-bot-gives-gets">Bot gives</span>
                        <span class="price" data-bind="text: '($' + total_sum_give() + ')', visible: total_sum_give() && !total_sum_is_some_absent()"></span>
                      </div>

                      <?php /*-------------------------------->
                      <!-- Список вещей, которые бот отдаёт -->
                      <!----------------------------------*/ ?>
                      <div class="items-list" data-bind="foreach: items_to_give, visible: !$root.m.s7.is_ajax_invoking()">

                        <?php /*------->
                        <!-- Предмет -->
                        <!---------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + image() + '\')'}">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label" data-bind="visible: price">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                      <?php /*------------------------------------------->
                      <!-- Сообщение о том, что бот ничего не получает -->
                      <!---------------------------------------------*/ ?>
                      <div style="display: none" class="nothing" data-bind="visible: !items_to_give().length">
                        <span>Nothing</span>
                      </div>

                    </div>

                  </div>

                  <?php /*------>
                  <!-- Подвал -->
                  <!--------*/ ?>
                  <div class="footer-part row">

                    <?php /*---------------------------------------------->
                    <!-- Дата и время последнего обновления / истечения -->
                    <!------------------------------------------------*/ ?>
                    <div class="time_updated_created col-md-7 col-sm-12 col-xs-12">

                      <div style="display: none" data-bind="visible: time_updated() && expiration_time()">
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, time_updated())()" title="Last update"></span>
                        <span>, </span>
                        <span data-bind="text: $root.f.s0.unix_timestamp_tojstime.bind($data, expiration_time())()" title="Expiration date/time"></span>
                      </div>

                    </div>

                    <?php /*----------------->
                    <!-- Кнопка "Действия" -->
                    <!-------------------*/ ?>
                    <div class="trade-offer-actions col-md-5 col-sm-12 col-xs-12">

                      <div class="btn-group" data-bind="visible: mode() == 1 || mode() == 3">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">

                            <?php /*------>
                            <!-- Accept -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.accept.bind($data, {silent: false})">Accept</a></li>

                            <?php /*------->
                            <!-- Decline -->
                            <!---------*/ ?>
                            <li data-bind="visible: mode() == 1"><a href="#" data-bind="click: $root.f.s7.decline.bind($data, {silent: false})">Decline</a></li>

                            <?php /*---------->
                            <!-- Get prices -->
                            <!------------*/ ?>
                            <li data-bind="visible: mode() == 1 || mode() == 3"><a href="#" data-bind="click: $root.f.s7.get_prices.bind($data, {mode: mode()})">Get prices</a></li>

                            <?php /*------>
                            <!-- Cancel -->
                            <!--------*/ ?>
                            <li data-bind="visible: mode() == 3"><a href="#" data-bind="click: $root.f.s7.cancel.bind($data, {silent: false})">Cancel</a></li>

                          </ul>
                        </div>
                      </div>

                    </div>


                  </div>

                </div>

              </div>

              <?php /*------------------------------------->
              <!-- Если торговые предложения отсутствуют -->
              <!---------------------------------------*/ ?>
              <div class="empty-note" style="display: none" data-bind="visible: !m.s7.tradeoffers_sent_history().length">
                <span>Empty</span>
              </div>

            </div>

          </div>

          <?php /*---------------------------------->
          <!-- Правый столбец (панель управления) -->
          <!------------------------------------*/ ?>
          <div class="col-md-5 col-sm-5 col-xs-5 control-panel">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="header-note">
              <span>Control panel</span>
            </div>

            <?php /*----------------->
            <!-- Панель управления -->
            <!-------------------*/ ?>
            <div>

              <?php /*--------------->
              <!-- Кнопка "Update" -->
              <!-----------------*/ ?>
              <div>
                <button type="button" class="btn btn-block btn-default btn-sm" data-bind="click: f.s7.update.bind($data, {silent: false})">Update</button>
              </div>

              <?php /*------------------------->
              <!-- Автоматическое обновление -->
              <!---------------------------*/ ?>
              <div class="form-group auto-update">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s7.auto_update">
                    Auto update every 1 min.
                  </label>
                </div>
              </div>

              <?php /*----------------------------------------->
              <!-- Какой тип торговых предложений показывать -->
              <!-------------------------------------------*/ ?>
              <div>

                <?php /*----------------->
                <!-- Блок radio-кнопок -->
                <!-------------------*/ ?>
                <div style="border-top: 1px solid #ddd; padding-top: 4px;" class="radio-block">

                  <div class="form-group" data-bind="foreach: m.s7.types.options">
                    <div class="radio">
                      <label>
                        <input type="radio" data-bind="attr: {id: id, name: name, value: value}, checked: $root.m.s7.types.choosen">
                        <span data-bind="text: text"></span>
                      </label>
                    </div>
                  </div>

                </div>

              </div>

            </div>

          </div>
          
          <?php /*--------------->
          <!-- Панель загрузки -->
          <!-----------------*/ ?>
          <div class="loader">
            <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s7.is_ajax_invoking">
              <div></div>
            </div>
          </div>

        </div>

      </div>
    </div>

  </div>

</div>




@stop



<?php /*--------------------------------------------------------------->
<!-- n. Получение данных с сервера и подключение JS этого документа  -->
<!-----------------------------------------------------------------*/ ?>
@section('js')

  <?php /*-------------------------------->
  <!-- n.1. Получение данных с сервера  -->
  <!----------------------------------*/ ?>
  <script>

    // 1. Подготовить объект, в который будут записаны данные
    var server = {};

    // 2. Принять данные

      // 2.1. Принять csrf_token
      server.csrf_token             = "{{ csrf_token() }}";

      // 2.2. Принять переданные из контроллера данные
      server.data                   =  {!! $data !!};

  </script>


  <?php /*------------------------------------>
  <!-- n.2. Подключение JS этого документа  -->
  <!--------------------------------------*/ ?>

  <!-- document js: start -->
  <script attr1="\Request::getHost();" attr2="\Request::getHost();"></script>
  <script attr1="\Request::getHost();" attr2="\Request::getHost();"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-jslib1/library.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/jquery/jquery.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/moment/moment.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/bootstrap/js/bootstrap.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/fastclick/fastclick.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/js/app.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/sparkline/jquery.sparkline.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/chartjs/Chart.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.resize.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.pie.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.categories.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/knob/jquery.knob.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-slider/bootstrap-slider.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/select2/select2.full.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.extensions.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/moment/min/moment.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/daterangepicker/daterangepicker.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datepicker/bootstrap-datepicker.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/iCheck/icheck.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/ckeditor.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/config.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/lang/ru.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/styles.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/jquery.dataTables.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/knockoutjs/dist/knockout.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/history.js/scripts/bundled/html4+html5/native.history.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/socket.io-client/socket.io.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/d3/d3.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-animsition/animsition/dist/js/animsition.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/momentjs/moment.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10005/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




