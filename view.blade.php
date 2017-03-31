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

  <title>D10006 - Interface for the M9 package (CSGO Lottery)</title>

@stop



<?php /*----------------->
<!-- 2. CSS документа  -->
<!-------------------*/ ?>
@section('css')

  <!-- document css: start -->
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/Font-Awesome/css/font-awesome.min.css?rand={!! mt_rand(1000,9999); !!}">
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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10006/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  1. Панель загрузки
  2. Игровые комнаты
    2.1. Левый столбец (контент)
      2.1.1. Панель с заголовком и кол-вом комнат
      2.1.2. Список комнат
    2.2. Правый столбец (панель управления)
      2.2.1. Панель инструментов
  3. Интерфейс кликнутой комнаты
    3.1. Левый столбец: кнопка "Назад" и левое меню
      3.1.1. Кнопка "назад"
      3.1.2. Левое меню интерфейса комнаты
    3.2. Правый столбец: имя бота и контент интерфейса
      3.2.1. Хлебные крошки
      3.2.2. Контент-бокс интерфейса комнаты

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

<?php /*------------------->
<!-- 2. Игровые комнаты  -->
<!---------------------*/ ?>
<div class="rooms-styles row" style="display: none" data-bind="visible: m.s1.selected_group().id() == 1">

  <?php /*---------------------------->
  <!-- 2.1. Левый столбец (контент) -->
  <!------------------------------*/ ?>
  <div class="room-list-cont col-md-7 col-sm-7 col-xs-7">

    <?php /*------------------------------------------->
    <!-- 2.1.1. Панель с заголовком и кол-вом комнат -->
    <!---------------------------------------------*/ ?>
    <div class="box box-common">
      <div class="box-body subdoc_title">
        <span>Rooms</span>
        <span data-bind="text: '- ' + m.s2.rooms_filtered().length + ' / ' + m.s2.rooms().length" style="font-size: 13px; color: rgb(204, 204, 204);"></span>
      </div>
    </div>

    <?php /*-------------------->
    <!-- 2.1.2. Список комнат -->
    <!----------------------*/ ?>
    <div class="row room-list" data-bind="foreach: m.s2.rooms_filtered" style="margin: 0;">

      <?php /*------->
      <!-- Комната -->
      <!---------*/ ?>
      <div class="new-room-container col-lg-6 col-md-6 col-sm-12 col-xs-12" data-bind="click: $root.f.s2.show_rooms_interface">
        <div class="box box-common new-room rowfix">

          <?php /*---------->
          <!-- ID комнаты -->
          <!------------*/ ?>
          <div class="spanfix_left0 spanfix_width62 rooms_avatar">

            <?php /*------->
            <!-- ID бота -->
            <!---------*/ ?>
            <div class="rooms_id" data-bind="css: {green_background_soft: is_on, red_background_soft: !is_on()}">
              <span data-bind="text: id"></span>
            </div>

          </div>

          <?php /*---------->
          <!-- Информация -->
          <!------------*/ ?>
          <div class="rooms_info">

            <?php /*------------>
            <!-- Никнэйм бота -->
            <!--------------*/ ?>
            <div class="rooms-nickname">
              <span data-bind="text: name, attr: {title: name}"></span>
            </div>

            <?php /*----------------->
            <!-- Информация о боте -->
            <!-------------------*/ ?>
            <div class="rooms-additionals">

              <?php /*-------------------------->
              <!-- Количество ботов в комнате -->
              <!----------------------------*/ ?>
              <div title="How many bots have been attached to that room">
                <i class="fa fa-fw fa-child"></i>
                <span data-bind="text: bot_count"></span>
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

        <?php /*----------->
        <!-- 1] New room -->
        <!-------------*/ ?>
        <div>

          <?php /*--------->
          <!-- Заголовок -->
          <!-----------*/ ?>
          <div class="header-note">
            <span>New room</span>
          </div>

          <?php /*------------------------>
          <!-- Добавление новой комнаты -->
          <!--------------------------*/ ?>
          <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-8">
              <input class="form-control input-sm" data-bind="textInput: m.s2.newroom.name" placeholder="Enter new room's name...">
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left: 0;">
              <button type="button" class="btn btn-block btn-success btn-sm" data-bind="click: f.s2.create_new_room">New room</button>
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
          <select class="form-control" data-bind="options: m.s2.sortrooms.options, optionsText: function(item) { return item().text; }, value: m.s2.sortrooms.choosen, event: {change: f.s2.sortfunc}"></select>

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

            <?php /*---------------------------->
            <!-- 3.1] По режиму приёма ставок -->
            <!------------------------------*/ ?>
            <div style="display: none" class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By bet's acception mode</span>
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
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.mode.roll">
                    Roll
                  </label>
                </div>

                <?php /*------------------>
                <!-- Accept and decline -->
                <!--------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.mode.availability">
                    Availability
                  </label>
                </div>

              </div>

            </div>

            <?php /*--------------->
            <!-- 3.2] По статусу -->
            <!-----------------*/ ?>
            <div class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By status</span>
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
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.status.enabled">
                    Enabled
                  </label>
                </div>

                <?php /*------------------>
                <!-- Accept and decline -->
                <!--------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.status.disabled">
                    Disabled
                  </label>
                </div>

              </div>

            </div>

            <?php /*--------------------------->
            <!-- 3.3] По режиму выдачи сдачи -->
            <!-----------------------------*/ ?>
            <div class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By change</span>
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
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.change.enabled">
                    Enabled
                  </label>
                </div>

                <?php /*------------------>
                <!-- Accept and decline -->
                <!--------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.change.disabled">
                    Disabled
                  </label>
                </div>

              </div>

            </div>

            <?php /*------------------------------------------->
            <!-- 3.4] По режиму выдачи выигрыша от 1-го бота -->
            <!---------------------------------------------*/ ?>
            <div class="filter-noselect">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <div class="header-note filter-header">
                <span>By one bot payout mode</span>
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
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.onebotpayout.enabled">
                    Enabled
                  </label>
                </div>

                <?php /*------------------>
                <!-- Accept and decline -->
                <!--------------------*/ ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" data-bind="checked: m.s2.filterrooms.onebotpayout.disabled">
                    Disabled
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
<div style="display: none" class="choosen-room-cont" data-bind="visible: m.s1.selected_group().id() == 2">

  <?php /*----------------------------------------------->
  <!-- 3.1. Левый столбец: кнопка "Назад" и левое меню -->
  <!-------------------------------------------------*/ ?>
  <div class="spanfix_left0 spanfix_width120 room-submenu">

    <?php /*--------------------->
    <!-- 3.1.1. Кнопка "назад" -->
    <!-----------------------*/ ?>
    <div class="box box_back_style">
      <div class="box-body back_link" style="padding-top: 0; padding-bottom: 0;" data-bind="click: f.s1.choose_subdoc.bind($data, {group: 'rooms', subdoc: 'rooms'})">
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
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Properties'}, click: f.s1.choose_subdoc.bind($data, {group: 'room', subdoc: 'properties', without_reload: '1'})"><span>Properties</span></li>
        <li data-bind="css: {active: m.s1.selected_subdoc().name() == 'Bots'}, click: f.s1.choose_subdoc.bind($data, {group: 'room', subdoc: 'bots', without_reload: '1'})"><span>Bots</span></li>
      </ul>
    </div>

  </div>

  <?php /*-------------------------------------------------->
  <!-- 3.2. Правый столбец: имя бота и контент интерфейса -->
  <!----------------------------------------------------*/ ?>
  <div class="room-content">

    <?php /*--------------------->
    <!-- 3.2.1. Хлебные крошки -->
    <!-----------------------*/ ?>
    <div class="box box_back_style">
      <div class="box-body subdoc_title">
        <span>Rooms &nbsp; → &nbsp; </span>
        <span data-bind="text: m.s2.edit.name"></span>
      </div>
    </div>

    <?php /*-------------------------------------->
    <!-- 3.2.2. Контент-бокс интерфейса комнаты -->
    <!----------------------------------------*/ ?>
    <div class="content_box_wrapper">
      <div class="content_box">

        <?php /*------------->
        <!-- 1] Properties -->
        <!---------------*/ ?>
        <div style="display: none" class="content_in_content_box" data-bind="visible: m.s1.selected_subdoc().name() == 'Properties'">

          <?php /*---------------->
          <!-- Базовые свойства -->
          <!------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Базовые параметры</span>
            </div>

            <?php /* 1] name -->
            <!-------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Название</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.name">
              </div>
            </div>

            <?php /* 2] is_on -->
            <!--------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Включена ли</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.is_on">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 3] allow_unstable_prices -->
            <!------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Разрешить ли нестабильные цены</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_unstable_prices">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 4] description -->
            <!--------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Описание</div>
              <div class="col-sm-7">
                <textarea class="form-control" rows="2" placeholder="Description of the room..." data-bind="textInput: m.s2.edit.description"></textarea>
              </div>
            </div>

            <?php /* 5] revolutions_per_lottery -->
            <!--------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Кол-во целых оборотов колеса при розыгрыше</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.revolutions_per_lottery">
              </div>
            </div>

            <?php /* 6] offers_timeout_sec -->
            <!---------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Таймаут офферов ботов игрокам, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.offers_timeout_sec">
              </div>
            </div>

          </div>

          <?php /*------------------->
          <!-- Настройки таймингов -->
          <!---------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Настройки таймингов</span>
            </div>
            <hr>

            <?php /* 1] room_round_duration_sec -->
            <!--------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Время жизни состояния Started, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.room_round_duration_sec">
              </div>
            </div>

            <?php /* 2] started_client_delta_s -->
            <!--------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Клиентская дельта состояния Started, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.started_client_delta_s">
              </div>
            </div>
            <hr>

            <?php /* 3] pending_duration_s -->
            <!---------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Время жизни состояния Pending, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.pending_duration_s">
              </div>
            </div>

            <?php /* 4] pending_client_delta_s -->
            <!-------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Клиентская дельта состояния Pending, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.pending_client_delta_s">
              </div>
            </div>

            <?php /* 5] lottery_client_delta_items_limit_s -->
            <!-------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Клиентская дельта состояния Pending (лимит вещей), с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.lottery_client_delta_items_limit_s">
              </div>
            </div>
            <hr>

            <?php /* 6] lottery_duration_ms -->
            <!----------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Время жизни состояния Lottery, мс</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.lottery_duration_ms">
              </div>
            </div>

            <?php /* 7] lottery_client_delta_ms -->
            <!--------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Клиентская дельта состояния Lottery, мс</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.lottery_client_delta_ms">
              </div>
            </div>
            <hr>

            <?php /* 8] winner_duration_s -->
            <!--------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Время жизни состояния Winner, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.winner_duration_s">
              </div>
            </div>

            <?php /* 9] winner_client_delta_s -->
            <!-------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Клиентская дельта состояния Winner, с</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.winner_client_delta_s">
              </div>
            </div>
            <hr>

          </div>

          <?php /*------------------------------------------------->
          <!-- Настройки версии интерфейса с бегущими аватарками -->
          <!---------------------------------------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Настройки версии интерфейса с бегущими аватарками</span>
            </div>

            <?php /* 1] avatars_num_in_strip -->
            <!-----------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Кол-во аватарок в ленте</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.avatars_num_in_strip">
              </div>
            </div>

          </div>

          <?php /*------>
          <!-- Бонусы -->
          <!--------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Бонусы</span>
            </div>

            <?php /* 1] bonus_domain -->
            <!---------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Бонус за строку в нике</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.bonus_domain">
              </div>
            </div>

            <?php /* 2] bonus_domain_name -->
            <!--------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Какая строка должна быть в нике</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.bonus_domain_name">
              </div>
            </div>

            <?php /* 3] bonus_firstbet -->
            <!-----------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Бонус за 1-ю в раунде ставку</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.bonus_firstbet">
              </div>
            </div>

            <?php /* 4] bonus_secondbet -->
            <!------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Бонус за 2-ю в раунде ставку</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.bonus_secondbet">
              </div>
            </div>

          </div>

          <?php /*---------------------->
          <!-- Экономические свойства -->
          <!------------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Экономика</span>
            </div>

            <?php /* 1] one_bot_payout -->
            <!-----------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Выплата выигрыша 1-им ботом</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.one_bot_payout">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 2] payout_limit_min -->
            <!-------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Время на забор выигрыша, мин</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.payout_limit_min">
              </div>
            </div>

            <?php /* 3] change -->
            <!---------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Вкл/Выкл сдачу</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.change">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 4] fee_percents -->
            <!---------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Комиссия сервиса, %</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.fee_percents">
              </div>
            </div>

            <?php /* 5] debts_collect_per_win_max_percent -->
            <!------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Лимит на взымание долгов, %</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.debts_collect_per_win_max_percent">
              </div>
            </div>

          </div>

          <?php /*-------------------->
          <!-- Лимиты и ограничения -->
          <!----------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Лимиты и ограничения</span>
            </div>

            <?php /* 1] min_bet | MIN ставка игрока, ¢ -->
            <!---------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MIN ставка игрока, ¢</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.min_bet">
              </div>
            </div>

            <?php /* 2] max_bet | MAX ставка игрока, ¢ -->
            <!---------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX ставка игрока, ¢</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_bet">
              </div>
            </div>

            <?php /* 3] max_bets_per_round | MAX кол-во ставок игроком за раунд -->
            <!----------------------------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX кол-во ставок игроком за раунд</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_bets_per_round">
              </div>
            </div>

            <?php /* 4] max_round_jackpot | MAX банк раунда, ¢ -->
            <!-----------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX банк раунда, ¢</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_round_jackpot">
              </div>
            </div>

            <?php /* 5] min_items_per_bet | MIN кол-во предметов в ставке -->
            <!----------------------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MIN кол-во предметов в ставке</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.min_items_per_bet">
              </div>
            </div>

            <?php /* 6] max_items_per_bet | MAX кол-во предметов в ставке -->
            <!----------------------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX кол-во предметов в ставке</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_items_per_bet">
              </div>
            </div>

            <?php /* 7] max_items_per_round | MAX кол-во предметов в раунде -->
            <!------------------------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX кол-во предметов в раунде</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_items_per_round">
              </div>
            </div>

            <?php /* 8] max_items_peruser_perround | MAX вещей в ставках 1-го игрока за раунд  -->
            <!-------------------------------------------------------------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">MAX вещей в ставках 1-го игрока за раунд</div>
              <div class="col-sm-7">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit.max_items_peruser_perround">
              </div>
            </div>

          </div>

          <?php /*------------------------->
          <!-- Ограничения на типы вещей -->
          <!---------------------------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Ограничения на типы вещей</span>
            </div>

            <?php /* 1] Case -->
            <!-------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Case</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.case">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 2] Key -->
            <!------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Key</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.key">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 3] Startrak -->
            <!-----------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Startrak</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.startrak">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 4] Souvenir pack -->
            <!----------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Souvenir packages</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types['souvenir packages']">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 5] Souvenir -->
            <!-----------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Souvenir</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.souvenir">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 6] Knife -->
            <!--------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Knife</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.knife">
                  </label>
                </div>
              </div>
            </div>

            <?php /* 7] Weapon -->
            <!---------------*/ ?>
            <div class="form-group">
              <div class="col-sm-5 control-label">Weapon</div>
              <div class="col-sm-7">
                <div class="checkbox">
                  <label>
                    <input class="no_outline" type="checkbox" data-bind="checked: m.s2.edit.allow_only_types.weapon">
                  </label>
                </div>
              </div>
            </div>

          </div>

          <?php /*-------->
          <!-- Действия -->
          <!----------*/ ?>
          <div class="form-horizontal">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">
              <span>Actions</span>
            </div>

            <?php /* Сохранить изменения -->
            <!-------------------------*/ ?>
            <div>
              <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
            </div>

            <?php /* Удалить комнату -->
            <!---------------------*/ ?>
            <div style="margin-top: 10px;">
              <button type="button" class="btn btn-block btn-danger" data-bind="click: f.s2.delete_room">Delete room</button>
            </div>

          </div>

        </div>

        <?php /*------->
        <!-- 2] Bots -->
        <!---------*/ ?>
        <div style="display: none" class="content_in_content_box room-bots-cont row" data-bind="visible: m.s1.selected_subdoc().name() == 'Bots'">

          <?php /*--------------------------------------------->
          <!-- Левый столбец (боты, прикреплённые к комнате) -->
          <!-----------------------------------------------*/ ?>
          <div class="col-md-6 col-sm-6 col-xs-6 left-column-cont">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="row">

              <?php /* Текст заголовка -->
              <!---------------------*/ ?>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="header-note">Choosen bots</span>
                <span data-bind="text: '- ' + m.s3.attached2selectedroom_bot_ids().length" style="font-size: 13px; color: rgb(204, 204, 204);"></span>
              </div>

            </div>

            <?php /* Прикреплённые к комнате боты -->
            <!----------------------------------*/ ?>
            <div class="attached-bots-list" data-bind="foreach: m.s3.bots">

              <?php /*--->
              <!-- Бот -->
              <!-----*/ ?>
              <div class="attached-bot" data-bind="click: $root.f.s2.edit_attached_bot_list.bind($data, {action: 'detach'}), visible: $root.m.s3.attached2selectedroom_bot_ids.indexOf(id()) != -1">

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

          <?php /*------------------------------------------------->
          <!-- Правый столбец (боты, не прикреплённые к комнате) -->
          <!---------------------------------------------------*/ ?>
          <div class="col-md-6 col-sm-6 col-xs-6 right-column-cont">

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div>
              <span class="header-note">Not choosen bots</span>
              <span data-bind="text: '- ' + m.s3.notattached2selectedroom_bot_ids().length" style="font-size: 13px; color: rgb(204, 204, 204);"></span>
            </div>

            <?php /* Не прикреплённые к комнате боты -->
            <!-------------------------------------*/ ?>
            <div class="attached-bots-list" data-bind="foreach: m.s3.bots">

              <?php /*--->
              <!-- Бот -->
              <!-----*/ ?>
              <div class="attached-bot" data-bind="click: $root.f.s2.edit_attached_bot_list.bind($data, {action: 'attach'}), visible: $root.m.s3.notattached2selectedroom_bot_ids.indexOf(id()) != -1">

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

      </div>
    </div>

  </div>

</div>

@stop



<?php /*--------------------------------------------------------------->
<!-- 4. Получение данных с сервера и подключение JS этого документа  -->
<!-----------------------------------------------------------------*/ ?>
@section('js')

  <?php /*-------------------------------->
  <!-- 4.1. Получение данных с сервера  -->
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
  <!-- 4.2. Подключение JS этого документа  -->
  <!--------------------------------------*/ ?>

  <!-- document js: start -->
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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/socket.io-client/dist/socket.io.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/d3/d3.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-animsition/animsition/dist/js/animsition.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/momentjs/moment.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10006/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




