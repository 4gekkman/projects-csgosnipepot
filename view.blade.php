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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/ionicons/css/ionicons.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-slider/slider.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/daterangepicker/daterangepicker-bs3.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/iCheck/all.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/select2/select2.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/skins/moono/editor.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/loaders.css/loaders.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/css/perfect-scrollbar.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/css/c.css">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10005/css/c.css">
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
          2.2.2.1. Инвентарь и торговые предложения
          2.2.2.2. Свойства бота

-------------------------*/ ?>
@section('content')

<?php /*--------------->
<!-- Панель загрузки -->
<!-----------------*/ ?>
<div class="loader">
  <div style="display: none" class="loading_state_panel loader-inner square-spin" data-bind="visible: m.s0.is_loadshield_on">
    <div></div>
  </div>
</div>

<?php /*--------------------------->
<!-- Заголовок и хлебные крошки  -->
<!-----------------------------*/ ?>
<section class="content-header content-header-my">
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
<div class="row">

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
                        <th rowspan="1" colspan="1" style="width: 4%;">№</th>
                        <th rowspan="1" colspan="1" style="width: 7%;">User_ID</th>
                        <th rowspan="1" colspan="1" style="width: 7%;">Bot_ID</th>
                        <th rowspan="1" colspan="1" style="width: 15%;">Steam_ID</th>
                        <th rowspan="1" colspan="1" style="width: 15%;">Steam name</th>
                        <th rowspan="1" colspan="1" style="width: 16%;">Games</th>
                        <th rowspan="1" colspan="1" style="width: 12%;">Inventory</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Trade ↓</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Trade ↑</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s2.bots">

                        <tr class="odd" data-bind="click: $root.f.s2.show_bots_interface, css: {tradeban_incoming: !ison_incoming(), tradeban_outcoming: !ison_outcoming(), tradeban_all: !ison_incoming() && !ison_outcoming()}">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s2.select_all_bots(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id_user"></td>
                          <td data-bind="text: id"></td>
                          <td data-bind="text: id_steam"></td>
                          <td data-bind="text: steam_name"></td>
                          <td data-bind="text: ''"></td>
                          <td data-bind="text: ''"></td>
                          <td data-bind="text: ison_incoming"></td>
                          <td data-bind="text: ison_outcoming"></td>
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
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 2}">

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

            <?php /*----------------------------------------->
            <!-- 2.2.2.1. Инвентарь и торговые предложения -->
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
                      <div data-bind="visible: !m.s3.inventory().length">
                        <span>Inventory is empty...</span>
                      </div>

                      <?php /*-------------------->
                      <!-- Содержимое инвентаря -->
                      <!----------------------*/ ?>
                      <div class="inventory" data-bind="foreach: m.s3.inventory">

                        <?php /*------------------->
                        <!-- Предмет в инвентаре -->
                        <!---------------------*/ ?>
                        <div class="item" data-bind="style: {backgroundImage: 'url(\'' + icon_url() + '\')', backgroundColor: background_color}, attr: {title: $root.f.s3.get_item_title($data)}, css: {selected: selected}, click: function(data, event){ data.selected(!data.selected()); }">

                        </div>

                      </div>

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
            <!-- 2.2.2.2. Свойства бота -->
            <!------------------------*/ ?>
            <div class="row">

              <?php /*------------------------------------>
              <!-- Свойства автоматизации торговли бота -->
              <!--------------------------------------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="box">
                  <div class="box-header with-border subdoc_title">
                    Automation properties
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

                    <?php /* 13] apikey -->
                    <!----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-4 control-label">apikey</div>
                      <div class="col-sm-8">
                        <input class="form-control input-sm" data-bind="textInput: m.s2.edit.apikey">
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

              <?php /*------------------->
              <!-- Общие свойства бота -->
              <!---------------------*/ ?>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="box">
                  <div class="box-header with-border subdoc_title">
                    Common properties
                  </div>
                  <div class="box-body form-horizontal" data-bind="style: {}">

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

                    <?php /* n] Кнопка "Редактировать" -->
                    <!-------------------------------*/ ?>
                    <div>
                      <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-jslib1/library.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/jquery/jquery.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/moment/moment.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/fastclick/fastclick.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/dist/js/app.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/sparkline/jquery.sparkline.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/chartjs/Chart.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.resize.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.pie.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/flot/jquery.flot.categories.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/knob/jquery.knob.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-slider/bootstrap-slider.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/select2/select2.full.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/input-mask/jquery.inputmask.extensions.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/moment/min/moment.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datepicker/bootstrap-datepicker.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/iCheck/icheck.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/ckeditor.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/config.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/lang/ru.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/ckeditor/styles.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/knockoutjs/dist/knockout.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/history.js/scripts/bundled/html4+html5/native.history.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/socket.io-client/socket.io.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/js/j.js"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10005/js/j.js"></script>
  <!-- document js: stop -->


@stop




