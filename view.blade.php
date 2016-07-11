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

  Панель загрузки
  Заголовок и хлебные крошки

    1. Меню главной таб-панели
    2. Контент главной таб-панели
      2.1. Боты
        2.1.1. Боты
      2.2. Интерфейс кликнутого бота
        2.2.1. Кнопка "Назад" и название поддокумента
        2.2.2. Содержимое интерфейса бота
          2.2.2.1. Различного рода предупреждения
          2.2.2.2. Инвентарь и торговые предложения
          2.2.2.3. Свойства бота

-------------------------*/ ?>
@section('content')

<?php /*------------------>
<!-- 1. Панель загрузки -->
<!-----------------*/ ?>
<div class="loader">
  <div style="display: none" class="loading_state_panel loader-inner ball-scale" data-bind="visible: m.s0.is_loadshield_on">
    <div></div>
  </div>
</div>

<?php /*-------->
<!-- 2. Боты  -->
<!----------*/ ?>
<div style="display: none" data-bind="visible: m.s1.selected_group().id() == 1">

  <?php /*---------->
  <!-- 2.1. Боты  -->
  <!------------*/ ?>

    <?php /*---------------------------->
    <!-- 0] Различные предупреждения  -->
    <!------------------------------*/ ?>
    <div>

      <?php /*----------------------------------------->
      <!-- 0.1] Ошибка при обновлении цен с csgofast -->
      <!-------------------------------------------*/ ?>
      <div style="display: none" class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.csgofast_last_bug">
        <h4>There is csgofast price update error!</h4>
        <p data-bind="text: m.s2.price_update_errors.csgofast_last_bug"></p>
      </div>

      <?php /*--------------------------------------------->
      <!-- 0.2] Ошибка при обновлении цен с Steam Market -->
      <!-----------------------------------------------*/ ?>
      <div style="display: none" class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.steammarket_last_bug">
        <h4>There is Steam Market price update error!</h4>
        <p data-bind="text: m.s2.price_update_errors.steammarket_last_bug"></p>
      </div>

    </div>

    <?php /*------------------------>
    <!-- 1] Название поддокумента -->
    <!---------------------------*/ ?>
    <h4>
      <b>Bots</b>
      <small class="small_notes" style="cursor: default">- <span title="Number of selected items" data-bind="text: m.s2.num_of_selected_bots"></span> / <span title="Total number of items" data-bind="text: m.s2.bots_total"></span></small>
    </h4>

    <?php /*-------------->
    <!-- 2] Содержание  -->
    <!----------------*/ ?>
    <div class="row">

      <div class="col-md-12">

        <div class="box box-info">

          <?php /* Заголовок  -->
          <!----------------*/ ?>
          <div class="box-header with-border row">

            <?php /* Чекбокс "Select all" -->
            <!--------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">
              <label class="selectall_cb checkbox">
                <input type="checkbox" data-bind="checked: m.s2.select_all_bots, event: {change: f.s2.select_all_change}"> - Select all
              </label>
            </div>

            <?php /* Блок кнопок "Actions" -->
            <!---------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">

              <div class="btn-group">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                    <i class="fa fa-gear"></i>
                    <span> Actions with selected</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#" data-bind="click: function(){}">No actions for now</a></li>
                  </ul>
                </div>
              </div>

            </div>

          </div>

          <?php /* Контент  -->
          <!--------------*/ ?>
          <div class="box-body no-padding">

            <?php /* Таблица с данными -->
            <!-----------------------*/ ?>
            <table class="table table-condensed table-hover all_table_styles">
              <thead>
              <tr role="row">
                <th rowspan="1" colspan="1" style="width: 4%;"></th>
                <th rowspan="1" colspan="1" style="width: 4%;" title="Number of a bot in the list.">№</th>
                <th rowspan="1" colspan="1" style="width: 7%;" title="ID of a user, affilated with a bot.">User_ID</th>
                <th rowspan="1" colspan="1" style="width: 7%;" title="ID of a bot.">Bot_ID</th>
                <th rowspan="1" colspan="1" style="width: 19%;" title="Steam ID of a bot.">Steam_ID</th>
                <th rowspan="1" colspan="1" style="width: 15%;" title="Steam name of a bot (but not Steam login).">Steam name</th>
                <th rowspan="1" colspan="1" style="width: 19%;" title="The games where a bot is involved.">Games</th>
                <th rowspan="1" colspan="1" style="width: 8%;" title="Inventory count of a bot, updates every 10 minutes.">Inventory</th>
                <th rowspan="1" colspan="1" style="width: 5%;" title="Authorization status of a bot, updates every hour.">Auth</th>
                <th rowspan="1" colspan="1" style="width: 10%;" title="Trade status of a bot.">Trade_↓↑</th>
              </tr>
              </thead>
              <tbody data-bind="foreach: m.s2.bots" >

                <tr class="odd" data-bind="click: $root.f.s2.show_bots_interface, css: {tradeban_incoming: !ison_incoming(), tradeban_outcoming: !ison_outcoming(), tradeban_all: !ison_incoming() && !ison_outcoming()}">
                  <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s2.select_all_bots(false); return true; }"></td>
                  <td data-bind="text: number"></td>
                  <td data-bind="text: id_user"></td>
                  <td data-bind="text: id"></td>
                  <td data-bind="text: id_steam"></td>
                  <td data-bind="text: steam_name"></td>
                  <td data-bind="text: ''"></td>
                  <td data-bind="text: inventory_count, css: {yellow_text: inventory_count() >= 500 && inventory_count() < 750, red_text: inventory_count() >= 750, red_background_soft: inventory_count_last_bug()}, attr: {title: 'Inventory status\n- Last update (server time): ' + inventory_count_last_update() + '\n- Problems: ' + inventory_count_last_bug()}"></td>
                  <td data-bind="css: {green_background_soft: authorization, red_background_soft: !authorization()}, attr: {title: 'Authorization status in Steam\n- Last update (server time): ' + authorization_last_update() + '\n- Authorization status check problems: ' + authorization_status_last_bug() + '\n- Authorization problems: ' + authorization_last_bug() + '\n- Authorization error code: ' + authorization_last_bug_code()}"></td>
                  <td style="font-size: 16px; text-align: center">
                    <span data-bind="text: ison_incoming() ? '↓' : ''"></span>
                    <span data-bind="text: ison_outcoming() ? '↑' : ''"></span>
                    <span data-bind="text: !ison_incoming() && !ison_outcoming() ? 'off' : ''" style="color: #9c0033"></span>
                  </td>
                </tr>

              </tbody>

            </table>

            <?php /* Если боты отсутствуют -->
            <!---------------------------*/ ?>
            <div data-bind="visible: m.s2.bots().length === 0">
              <p style="font-size: 16px; padding: 10px 0 0 10px">Bots are absent...</p>
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
          <div>

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

          <?php /*-------------------------------------------------------->
          <!-- Создани и отправка нового торгового предложения партнёру -->
          <!----------------------------------------------------------*/ ?>
          <div> <!-- style="display: none" data-bind="visible: m.s5.steam_name_partner"> -->

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
              <div class="new-trade-left-column col-md-7 col-sm-7 col-xs-7">

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
                      <small class="small_notes"><span title="Number of selected inventory items" data-bind="text: m.s3.inventory_selected"></span> / <span title="Total number of inventory items" data-bind="text: m.s3.inventory_total"></span></small>

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
                        <span>Inventory is empty...</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s3.inventory, visible: !m.s3.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s3.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>
              </div>

              <?php /*---------------------------------------------------->
              <!-- Правый столбец (вещи для обмена и панель управления) -->
              <!------------------------------------------------------*/ ?>
              <div class="col-md-5 col-sm-5 col-xs-5">
                123
              </div>

            </div>

          </div>

        </div>

        <?php /*-------------->
        <!-- 5] Tradeoffers -->
        <!----------------*/ ?>
        <div style="display: none" class="content_in_content_box" data-bind="visible: m.s1.selected_subdoc().name() == 'Tradeoffers'">

          Tradeoffers

        </div>

      </div>
    </div>

  </div>

</div>






<?php /*--------------------------->
<!-- Заголовок и хлебные крошки  -->
<!-----------------------------*/ ?>
<section style="display: none" class="content-header content-header-my">
  <h1>
    Bots and trade automation
  </h1>
  <ol class="breadcrumb breadcrumb-my">
    <li><a href="#"><i class="fa fa-flash"></i>Bots and trade automation</a></li>
    <!--<li class="active">Here</li>-->
  </ol>
</section>

<?php /*------------------->
<!-- Главная таб-панель  -->
<!---------------------*/ ?>
<div class="row" style="display: none">

  <?php /* Содержание -->
  <!----------------*/ ?>
  <div class="col-md-12">
    <div class="nav-tabs-custom">

      <?php /*--------------------------->
      <!-- 1. Меню главной таб-панели  -->
      <!-----------------------------*/ ?>
      <ul class="nav nav-tabs" data-bind="foreach: m.s1.subdocs">
        <li data-bind="visible: $root.f.s1.is_tab_visible($data), css: {active: $root.f.s1.is_tab_active($data, $root)}, click: $root.f.s1.choose_subdoc.bind($data, '')"><a href="#" data-toggle="tab" data-bind="text: name"></a></li>
      </ul>

      <?php /*------------------------------>
      <!-- 2. Контент главной таб-панели  -->
      <!--------------------------------*/ ?>
      <div class="tab-content">

        <?php /*---------->
        <!-- 2.1. Боты  -->
        <!------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 1}">

          <?php /*------------>
          <!-- 2.1.1. Боты  -->
          <!--------------*/ ?>

            <?php /*---------------------------->
            <!-- 0] Различные предупреждения  -->
            <!------------------------------*/ ?>
            <div>

              <?php /*----------------------------------------->
              <!-- 0.1] Ошибка при обновлении цен с csgofast -->
              <!-------------------------------------------*/ ?>
              <div class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.csgofast_last_bug">
                <h4>There is csgofast price update error!</h4>
                <p data-bind="text: m.s2.price_update_errors.csgofast_last_bug"></p>
              </div>

              <?php /*--------------------------------------------->
              <!-- 0.2] Ошибка при обновлении цен с Steam Market -->
              <!-----------------------------------------------*/ ?>
              <div class="callout callout-danger" data-bind="visible: m.s2.price_update_errors.steammarket_last_bug">
                <h4>There is Steam Market price update error!</h4>
                <p data-bind="text: m.s2.price_update_errors.steammarket_last_bug"></p>
              </div>

            </div>

            <?php /*------------------------>
            <!-- 1] Название поддокумента -->
            <!---------------------------*/ ?>
            <h4>
              <b>Bots</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of selected items" data-bind="text: m.s2.num_of_selected_bots"></span> / <span title="Total number of items" data-bind="text: m.s2.bots_total"></span></small>
            </h4>

            <?php /*-------------->
            <!-- 2] Содержание  -->
            <!----------------*/ ?>
            <div class="row">

              <div class="col-md-12">

                <div class="box box-info">

                  <?php /* Заголовок  -->
                  <!----------------*/ ?>
                  <div class="box-header with-border row">

                    <?php /* Чекбокс "Select all" -->
                    <!--------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <label class="selectall_cb checkbox">
                        <input type="checkbox" data-bind="checked: m.s2.select_all_bots, event: {change: f.s2.select_all_change}"> - Select all
                      </label>
                    </div>

                    <?php /* Блок кнопок "Actions" -->
                    <!---------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="btn-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions with selected</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a href="#" data-bind="click: function(){}">No actions for now</a></li>
                          </ul>
                        </div>
                      </div>

                    </div>

                  </div>

                  <?php /* Контент  -->
                  <!--------------*/ ?>
                  <div class="box-body no-padding">

                    <?php /* Таблица с данными -->
                    <!-----------------------*/ ?>
                    <table class="table table-condensed table-hover all_table_styles">
                      <thead>
                      <tr role="row">
                        <th rowspan="1" colspan="1" style="width: 4%;"></th>
                        <th rowspan="1" colspan="1" style="width: 4%;" title="Number of a bot in the list.">№</th>
                        <th rowspan="1" colspan="1" style="width: 7%;" title="ID of a user, affilated with a bot.">User_ID</th>
                        <th rowspan="1" colspan="1" style="width: 7%;" title="ID of a bot.">Bot_ID</th>
                        <th rowspan="1" colspan="1" style="width: 19%;" title="Steam ID of a bot.">Steam_ID</th>
                        <th rowspan="1" colspan="1" style="width: 15%;" title="Steam name of a bot (but not Steam login).">Steam name</th>
                        <th rowspan="1" colspan="1" style="width: 19%;" title="The games where a bot is involved.">Games</th>
                        <th rowspan="1" colspan="1" style="width: 8%;" title="Inventory count of a bot, updates every 10 minutes.">Inventory</th>
                        <th rowspan="1" colspan="1" style="width: 5%;" title="Authorization status of a bot, updates every hour.">Auth</th>
                        <th rowspan="1" colspan="1" style="width: 10%;" title="Trade status of a bot.">Trade_↓↑</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s2.bots" >

                        <tr class="odd" data-bind="click: $root.f.s2.show_bots_interface, css: {tradeban_incoming: !ison_incoming(), tradeban_outcoming: !ison_outcoming(), tradeban_all: !ison_incoming() && !ison_outcoming()}">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s2.select_all_bots(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id_user"></td>
                          <td data-bind="text: id"></td>
                          <td data-bind="text: id_steam"></td>
                          <td data-bind="text: steam_name"></td>
                          <td data-bind="text: ''"></td>
                          <td data-bind="text: inventory_count, css: {yellow_text: inventory_count() >= 500 && inventory_count() < 750, red_text: inventory_count() >= 750, red_background_soft: inventory_count_last_bug()}, attr: {title: 'Inventory status\n- Last update (server time): ' + inventory_count_last_update() + '\n- Problems: ' + inventory_count_last_bug()}"></td>
                          <td data-bind="css: {green_background_soft: authorization, red_background_soft: !authorization()}, attr: {title: 'Authorization status in Steam\n- Last update (server time): ' + authorization_last_update() + '\n- Authorization status check problems: ' + authorization_status_last_bug() + '\n- Authorization problems: ' + authorization_last_bug() + '\n- Authorization error code: ' + authorization_last_bug_code()}"></td>
                          <td style="font-size: 16px; text-align: center">
                            <span data-bind="text: ison_incoming() ? '↓' : ''"></span>
                            <span data-bind="text: ison_outcoming() ? '↑' : ''"></span>
                            <span data-bind="text: !ison_incoming() && !ison_outcoming() ? 'off' : ''" style="color: #9c0033"></span>
                          </td>
                        </tr>

                      </tbody>

                    </table>

                    <?php /* Если боты отсутствуют -->
                    <!---------------------------*/ ?>
                    <div data-bind="visible: m.s2.bots().length === 0">
                      <p style="font-size: 16px; padding: 10px 0 0 10px">Bots are absent...</p>
                    </div>

                  </div>

                </div>

              </div>

            </div>

        </div>

        <?php /*------------------------------->
        <!-- 2.2. Интерфейс кликнутого бота  -->
        <!---------------------------------*/ ?>
        <div class="tab-pane ibot_container rowfix" data-bind="css: {active: m.s1.selected_subdoc().id() == 2}">

          <?php /*------------------------------------------------->
          <!-- 2.2.1. Левый столбец: кнопка "Назад" и левое меню -->
          <!---------------------------------------------------*/ ?>
          <div class="spanfix_left0 spanfix_width120">

            123

          </div>

          <?php /*---------------------------------------------------->
          <!-- 2.2.2. Правый столбец: имя бота и контент интерфейса -->
          <!------------------------------------------------------*/ ?>
          <div class="spanfix_left120">

            123

          </div>


          <div style="display: none">

          <?php /*------------------------------------------------->
          <!-- 2.2.1. Левый столбец: кнопка "Назад" и левое меню -->
          <!---------------------------------------------------*/ ?>
          <div class="ibot_left_column">

            <?php /*----------------------->
            <!-- 2.2.1.1. Кнопка "назад" -->
            <!-------------------------*/ ?>
            <div class="box box_back_style">
              <div class="box-body back_link" style="padding-top: 0; padding-bottom: 0;" data-bind="click: f.s1.choose_subdoc.bind($data, 1)">
                <span>
                  <i class="fa fa-long-arrow-left" style="font-size: 32px;"></i>&nbsp;&nbsp;
                </span>
              </div>
            </div>

            <?php /*----------------------------------->
            <!-- 2.2.1.2. Левое меню интерфейса бота -->
            <!-------------------------------------*/ ?>
            <div class="box_leftmenu_style">
              <ul>
                <li>Trade</li>
                <li>Properties</li>
                <li>Auth code</li>
                <li>Authorization</li>
              </ul>
            </div>

          </div>

          <?php /*---------------------------------------------------->
          <!-- 2.2.2. Правый столбец: имя бота и контент интерфейса -->
          <!------------------------------------------------------*/ ?>
          <div class="ibot_right_column">

            123

          </div>





              <?php /*--------------------------------------------->
              <!-- 2.2.1. Кнопка "Назад" и название поддокумента -->
              <!-----------------------------------------------*/ ?>
              <div class="row">
                <div class="col-md-2 col-sm-2 col-xs-2">
                  <div class="box box_back_style">
                    <div class="box-body back_link" style="padding-top: 0; padding-bottom: 0;" data-bind="click: f.s1.choose_subdoc.bind($data, 1)">
                      <span>
                        <i class="fa fa-long-arrow-left" style="font-size: 32px;"></i>&nbsp;&nbsp;
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-10 col-sm-10 col-xs-10">
                  <div class="box box_breadcrumbs_style">
                    <div class="box-body subdoc_title">
                      Bots &nbsp; → &nbsp; <span data-bind="text: m.s2.edit.steam_name"></span>
                    </div>
                  </div>
                </div>
              </div>

              <?php /*--------------------------->
              <!-- 2.2.2. Левое меню и контент -->
              <!-----------------------------*/ ?>
              <div class="row">

                <?php /*------------------->
                <!-- 2.2.2.1. Левое меню -->
                <!---------------------*/ ?>
                <div class="col-md-2 col-sm-2 col-xs-2">
                  <div class="box_leftmenu_style">
                    <ul>
                      <li>Trade</li>
                      <li>Properties</li>
                      <li>Auth code</li>
                      <li>Authorization</li>
                    </ul>
                  </div>
                </div>

                <?php /*-------------------------------->
                <!-- 2.2.2.2. Контент интерфейса бота -->
                <!----------------------------------*/ ?>
                <div class="col-md-10 col-sm-10 col-xs-10">

                </div>
              </div>

          </div>


        </div>









        <!----------------------------------->
        <!--                               -->
        <!--    НАЧАЛО СТАРОЙ ВЕРСИИ       -->
        <!--                               -->
        <!----------------------------------->

        <?php /*------------------------------->
        <!-- 2.2. Интерфейс кликнутого бота  -->
        <!---------------------------------*/ ?>
        <div style="display: none" class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 2}">

          <?php /*--------------------------------------------->
          <!-- 2.2.1. Кнопка "Назад" и название поддокумента -->
          <!-----------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 1)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Bots &nbsp; → &nbsp; Bot [<span data-bind="text: m.s2.edit.steam_name"></span>]
                </div>
              </div>
            </div>
          </div>

          <?php /*--------------------------------->
          <!-- 2.2.2. Содержимое интерфейса бота -->
          <!------------------------------------*/ ?>
          <div>

            <?php /*--------------------------------------->
            <!-- 2.2.2.1. Различного рода предупреждения -->
            <!-----------------------------------------*/ ?>
            <div>

              <?php /*-------------------------->
              <!-- Бот не авторизован в Steam -->
              <!----------------------------*/ ?>
              <div class="callout callout-danger" data-bind="visible: !m.s2.edit.authorization()">
                <h4>This bot is not authorized in Steam!</h4>
                <p>
                  You have to authorize it manually, see the panel below. <br>
                  Authorization status last bug: <span data-bind="text: m.s2.edit.authorization_status_last_bug"></span><br>
                  Authorization last bug: <span data-bind="text: m.s2.edit.authorization_last_bug"></span>
                  Authorization error code: <span data-bind="text: m.s2.edit.authorization_last_bug_code"></span>
                </p>
              </div>

            </div>

            <?php /*----------------------------------------->
            <!-- 2.2.2.2. Инвентарь и торговые предложения -->
            <!-------------------------------------------*/ ?>
            <div class="row">

              <?php /*--------->
              <!-- Инвентарь -->
              <!-----------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="box">

                  <?php /*--------->
                  <!-- Заголовок -->
                  <!-----------*/ ?>
                  <div class="box-header with-border subdoc_title">
                    Inventory
                    <small class="small_notes" style="cursor: default">- <span title="Number of selected inventory items" data-bind="text: m.s3.inventory_selected"></span> / <span title="Total number of inventory items" data-bind="text: m.s3.inventory_total"></span></small>
                  </div>

                  <?php /*------------------->
                  <!-- Панель инструментов -->
                  <!---------------------*/ ?>
                  <div class="tools_panel box-header">

                    <?php /*---------->
                    <!-- Обновление -->
                    <!------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6 tools" style="padding: 0">

                      <?php /*----------------->
                      <!-- Кнопка "обновить" -->
                      <!-------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline;">
                        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Update bots inventory" style="vertical-align: baseline" data-bind="click: f.s3.update">
                          <i class="fa fa-refresh"></i>
                          <span>Update</span>
                        </button>
                      </div>

                      <?php /*------------------------>
                      <!-- Чекбокс "Авто обновление -->
                      <!--------------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline; padding-left: 15px;" data-bind="visible: false">
                        <label class="selectall_cb checkbox">
                          <input type="checkbox" data-bind="checked: false, event: {change: function(){}}" style="vertical-align: sub; display: inline; position: relative;"> - auto
                        </label>
                      </div>

                    </div>

                    <?php /*------------------------------------->
                    <!-- Развыделение всех элементов инвентаря -->
                    <!---------------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6 tools" style="padding: 0">

                      <?php /*------------------------------------->
                      <!-- Развыделение всех элементов инвентаря -->
                      <!---------------------------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline; float: right;">
                        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Deselect all elements in inventory" style="vertical-align: baseline" data-bind="click: f.s3.deselect_all">
                          <span>Deselect all</span>
                        </button>
                      </div>

                    </div>

                  </div>

                  <?php /*------->
                  <!-- Контент -->
                  <!---------*/ ?>
                  <div class="box-body form-horizontal" style="max-height: 400px; min-height: 400px; overflow: hidden;">
                    <div class="inventory-container" style="height: 400px; position: relative; overflow: hidden;">

                      <?php /*-------------------------------------->
                      <!-- Надпись на случай, если инвентарь пуст -->
                      <!----------------------------------------*/ ?>
                      <div data-bind="visible: !m.s3.inventory().length && !m.s3.is_ajax_invoking()">
                        <span>Inventory is empty...</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s3.inventory, visible: !m.s3.is_ajax_invoking()">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s3.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }">

                          <?php /*------------->
                          <!-- Лэйбл с ценой -->
                          <!---------------*/ ?>
                          <div class="price_label">
                            <span data-bind="text: '$'+price()"></span>
                          </div>

                        </div>

                      </div>

                    </div>
                  </div>

                  <?php /*--------------->
                  <!-- Панель загрузки -->
                  <!-----------------*/ ?>
                  <div class="loader">
                    <div style="display: none" class="inventory_loading_state_panel loader-inner ball-clip-rotate" data-bind="visible: m.s3.is_ajax_invoking">
                      <div></div>
                    </div>
                  </div>

                </div>
              </div>

              <?php /*-------------------->
              <!-- Торговые предложения -->
              <!----------------------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="box">

                  <?php /*----------------------------->
                  <!-- Заголовок, фильтры и действия -->
                  <!-------------------------------*/ ?>
                  <div class="box-header with-border subdoc_title">

                    <?php /*------------------------------------->
                    <!-- Заголовок и количественные показатели -->
                    <!---------------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
                      Trade offers
                      <small class="small_notes" style="cursor: default">- <span title="Number of selected trade offers" data-bind="text: '1'"></span> / <span title="Number of passed filters trade offers" data-bind="text: '2'"></span> / <span title="Total number of trade offers" data-bind="text: '4'"></span></small>
                    </div>

                    <?php /*------------------>
                    <!-- Фильтры и действия -->
                    <!--------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">

                      <?php /*---------------------------------->
                      <!-- Действия с торговыми предложениями -->
                      <!------------------------------------*/ ?>
                      <div style="display: inline-block;">
                        <div class="btn-group">
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected trade offers">
                              <i class="fa fa-gear"></i>
                              <span> Actions</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                              <li><a href="#" data-bind="click: function(){}">No actions for now</a></li>
                            </ul>
                          </div>
                        </div>
                      </div>

                      <?php /*-------------------------->
                      <!-- Показывать ли исходящие ТП -->
                      <!----------------------------*/ ?>
                      <div style="display: inline-block; float: right;">
                        <label class="selectall_cb checkbox">
                          <input type="checkbox" data-bind="checked: true, event: {change: function(){}}" style="vertical-align: sub; display: inline; position: relative;">↑
                        </label>
                      </div>

                      <?php /*------------------------->
                      <!-- Показывать ли входящие ТП -->
                      <!---------------------------*/ ?>
                      <div style="display: inline-block; float: right;">
                        <label class="selectall_cb checkbox">
                          <input type="checkbox" data-bind="checked: true, event: {change: function(){}}" style="vertical-align: sub; display: inline; position: relative;">↓
                        </label>
                      </div>

                    </div>

                  </div>

                  <?php /*------------------->
                  <!-- Панель инструментов -->
                  <!---------------------*/ ?>
                  <div class="tools_panel box-header">

                    <?php /*---------->
                    <!-- Обновление -->
                    <!------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6 tools" style="padding: 0">

                      <?php /*----------------->
                      <!-- Кнопка "обновить" -->
                      <!-------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline;">
                        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items" style="vertical-align: baseline">
                          <i class="fa fa-refresh"></i>
                          <span>Update</span>
                        </button>
                      </div>

                      <?php /*------------------------>
                      <!-- Чекбокс "Авто обновление -->
                      <!--------------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline; padding-left: 15px;" data-bind="visible: false">
                        <label class="selectall_cb checkbox">
                          <input type="checkbox" data-bind="checked: false, event: {change: function(){}}" style="vertical-align: sub; display: inline; position: relative;"> - auto
                        </label>
                      </div>

                    </div>

                    <?php /*------------------------------------->
                    <!-- Развыделение всех элементов инвентаря -->
                    <!---------------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6 tools" style="padding: 0">

                      <?php /*------------------------------------->
                      <!-- Развыделение всех элементов инвентаря -->
                      <!---------------------------------------*/ ?>
                      <div style="display: inline-block; vertical-align: baseline; float: right;">
                        <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items" style="vertical-align: baseline">
                          <span>Deselect all</span>
                        </button>
                      </div>

                    </div>

                  </div>

                  <?php /*------------------------------------->
                  <!-- Панель отправки торгового предложения -->
                  <!---------------------------------------*/ ?>
                  <div class="tools_panel box-header">

                    <?php /*--------------------------------------->
                    <!-- Поле для ввода торгового URL получателя -->
                    <!-----------------------------------------*/ ?>
                    <div class="col-md-8 col-sm-8 col-xs-8 form-horizontal" style="padding: 0">
                      <div class="form-group" style="margin: 0">
                        <div class="col-sm-12" style="padding: 0">
                          <input class="form-control input-sm" placeholder="Enter trade url..." data-bind="textInput: ''" style="border: 0; border-right: 1px solid #ddd; padding-left: 0;">
                        </div>
                      </div>
                    </div>

                    <?php /*-------------------------------------->
                    <!-- Кнопка "Отправить торговое предложение -->
                    <!----------------------------------------*/ ?>
                    <div class="col-md-4 col-sm-4 col-xs-4 tools" style="padding: 0; text-align: center;">
                      <div style="display: inline-block; vertical-align: baseline;">
                        <button type="button" class="btn btn-success dropdown-toggle btn-xs" data-toggle="dropdown" title="Send trade offer" style="vertical-align: baseline">
                          <span>Send trade offer</span>
                        </button>
                      </div>
                    </div>

                  </div>

                  <?php /*------->
                  <!-- Контент -->
                  <!---------*/ ?>
                  <div class="box-body form-horizontal tradeoffers-container" style="max-height: 358px; min-height: 358px;">

                    Контент

                  </div>
                </div>
              </div>

            </div>

            <?php /*---------------------->
            <!-- 2.2.2.3. Свойства бота -->
            <!------------------------*/ ?>
            <div class="row">

              <?php /*------------------------------------>
              <!-- Свойства автоматизации торговли бота -->
              <!--------------------------------------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="box collapsed-box">
                  <div class="box-header with-border subdoc_title">
                    <span>Automation properties</span>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </div>
                  </div>
                  <div class="box-body form-horizontal" data-bind="style: {}">

                    <?php /* 1] login -->
                    <!--------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">login</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.login">
                      </div>
                    </div>

                    <?php /* 2] password -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">password</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.password">
                      </div>
                    </div>

                    <?php /* 3] steamid -->
                    <!----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">steamid</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.steamid">
                      </div>
                    </div>

                    <?php /* 4] shared_secret -->
                    <!----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">shared_secret</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.shared_secret">
                      </div>
                    </div>

                    <?php /* 5] serial_number -->
                    <!----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">serial_number</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.serial_number">
                      </div>
                    </div>

                    <?php /* 6] revocation_code -->
                    <!------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">revocation_code</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.revocation_code">
                      </div>
                    </div>

                    <?php /* 7] uri -->
                    <!------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">uri</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.uri">
                      </div>
                    </div>

                    <?php /* 8] server_time -->
                    <!--------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">server_time</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.server_time">
                      </div>
                    </div>

                    <?php /* 9] account_name -->
                    <!---------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">account_name</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.account_name">
                      </div>
                    </div>

                    <?php /* 10] token_gid -->
                    <!-------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">token_gid</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.token_gid">
                      </div>
                    </div>

                    <?php /* 11] identity_secret -->
                    <!-------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">identity_secret</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.identity_secret">
                      </div>
                    </div>

                    <?php /* 12] secret_1 -->
                    <!------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">secret_1</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.secret_1">
                      </div>
                    </div>

                    <?php /* 13] device_id -->
                    <!-------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">device_id</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.device_id">
                      </div>
                    </div>

                    <?php /* n] Кнопка "Редактировать" -->
                    <!-------------------------------*/ ?>
                    <div>
                      <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
                    </div>

                  </div>
                </div>
              </div>

              <?php /*----------------------------------------------------------------->
              <!-- Общие свойства бота + мобильная аутентификация + авторизация бота -->
              <!-------------------------------------------------------------------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">

                <?php /*------------------->
                <!-- Общие свойства бота -->
                <!---------------------*/ ?>
                <div class="box collapsed-box">
                  <div class="box-header with-border subdoc_title">
                    <span>Common properties</span>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </div>
                  </div>
                  <div class="box-body form-horizontal" data-bind="style: {}">

                    <?php /* Ошибки -->
                    <!------------*/ ?>

                      <?php /* Невозможно извлечь API-ключ -->
                      <!---------------------------------*/ ?>
                      <div class="callout callout-danger" data-bind="visible: m.s2.edit.apikey_last_bug">
                        <p data-bind="text: 'Can not retrieve API-key for this bot: '+m.s2.edit.apikey_last_bug()"></p>
                      </div>

                    <?php /* 1] id -->
                    <!-----------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">ID</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.id" disabled="">
                      </div>
                    </div>

                    <?php /* 2] ison_incoming -->
                    <!----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">ison_incoming</div>
                      <div class="col-sm-8">
                        <select class="form-control input-sm" data-bind="options: m.s2.options_true_false, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().value(); }, value: m.s2.edit.ison_incoming"></select>
                      </div>
                    </div>

                    <?php /* 3] ison_outcoming -->
                    <!-----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">ison_outcoming</div>
                      <div class="col-sm-8">
                        <select class="form-control input-sm" data-bind="options: m.s2.options_true_false, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().value(); }, value: m.s2.edit.ison_outcoming"></select>
                      </div>
                    </div>

                    <?php /* 4] apikey -->
                    <!---------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">apikey</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.apikey" disabled="">
                      </div>
                    </div>

                    <?php /* 5] apikey_domain -->
                    <!----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">apikey_domain</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.apikey_domain">
                      </div>
                    </div>

                    <?php /* 6] trade_url -->
                    <!------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">trade_url</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.trade_url">
                      </div>
                    </div>

                    <?php /* n] Кнопка "Редактировать" -->
                    <!-------------------------------*/ ?>
                    <div>
                      <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
                    </div>

                  </div>
                </div>

                <?php /*--------------------------------->
                <!-- Панель мобильного аутентификатора -->
                <!-----------------------------------*/ ?>
                <div class="box">
                  <div class="box-header with-border subdoc_title">

                    <div class="col-md-8 col-sm-8 col-xs-8" style="padding-left: 0">Current mobile auth code</div>
                    <div class="col-md-4 col-sm-4 col-xs-4">
                      <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Copy current code" data-bind="click: f.s4.copy">
                        <i class="fa fa-clone"></i>
                        <span>Copy</span>
                      </button>
                    </div>

                  </div>
                  <div class="box-body auth_code_styles">

                    <?php /*--->
                    <!-- Код -->
                    <!-----*/ ?>
                    <span data-bind="text: m.s4.code, visible: m.s4.is_current_code_valid"></span>

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
                </div>

                <?php /*------------------------------>
                <!-- Панель ручной авторизации бота -->
                <!--------------------------------*/ ?>
                <div class="box" data-bind="visible: !m.s2.edit.authorization()">
                  <div class="box-header with-border subdoc_title">

                    <span>Manual authorization of the bot in Steam</span>

                  </div>
                  <div class="box-body authorization_styles">

                    <?php /*-------------------------->
                    <!-- Кнопка "Авторизовать бота" -->
                    <!----------------------------*/ ?>
                    <div>
                      <button type="button" class="btn btn-block btn-default" data-bind="click: function(){}">Authorize the bot in Steam</button>
                    </div>

                    <?php /*------------------------------------------------------------->
                    <!-- Инструкции на случай ошибки, свои для каждого из кодов ошибки -->
                    <!---------------------------------------------------------------*/ ?>
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

              </div>

            </div>


          </div>

        </div>

        <!----------------------------------->
        <!--                               -->
        <!--     КОНЕЦ СТАРОЙ ВЕРСИИ       -->
        <!--                               -->
        <!----------------------------------->








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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10005/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




