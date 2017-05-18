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

  <title>D10000 - Users and Groups and Privs Control System</title>

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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10000/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10000/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  Панель загрузки
  Заголовок и хлебные крошки
  Главная таб-панель

    1. Меню главной таб-панели
    2. Контент главной таб-панели
      2.1. Пользователи
        2.1.1. Пользователи
        2.1.2. Фильтры
      2.2. Свойства пользователя
        2.2.1. Кнопка "Назад" и название поддокумента
        2.2.2. Редактирование пользователя и прочий функционал
          2.2.2.1. Редактирование пользователя
          2.2.2.2. Аватар, смена пароля, проверка email/phone
      2.3. Новый пользователь
        2.3.1. Кнопка "Назад" и название поддокумента
        2.3.2. Регистрация нового пользователя
      2.4. Группы
        2.4.1. Группы
        2.4.2. Фильтры
      2.5. Свойства группы
        2.5.1. Кнопка "Назад" и название поддокумента
        2.5.2. Редактирование группы
      2.6. Новая группа
        2.6.1. Кнопка "Назад" и название поддокумента
        2.6.2. Регистрация новой группы
      2.7. Права
        2.7.1. Права
        2.7.2. Фильтры
      2.8. Свойства права
        2.8.1. Кнопка "Назад" и название поддокумента
        2.8.2. Редактирование права
      2.9. Новое право
        2.9.1. Кнопка "Назад" и название поддокумента
        2.9.2. Регистрация нового права
      2.10. Теги
        2.10.1. Теги
        2.10.2. Фильтры
      2.11. Свойства тега
        2.11.1. Кнопка "Назад" и название поддокумента
        2.11.2. Редактирование тега
      2.12. Новый тег
        2.12.1. Кнопка "Назад" и название поддокумента
        2.12.2. Регистрация нового тега
      2.13. Настройки
      2.14. Аутентификация
        2.14.1. Статус аутентификации, восстановление пароля
        2.14.2. Различные интерфейсы для аутентификации

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

        <?php /*------------------>
        <!-- 2.1. Пользователи  -->
        <!--------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 1}">

          <?php /*-------------------->
          <!-- 2.1.1. Пользователи  -->
          <!----------------------*/ ?>

            <?php /*------------------------>
            <!-- 1] Название поддокумента -->
            <!---------------------------*/ ?>
            <h4>
              <b>Users</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of items have selected" data-bind="text: m.s5.pagi.num_of_selected_users"></span> / <span title="Number of items have passed filters" data-bind="text: m.s5.pagi.users_filtered"></span> / <span title="Total number of items" data-bind="text: m.s5.pagi.users_total"></span></small>
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
                        <input type="checkbox" data-bind="checked: m.s5.select_all_users, event: {change: f.s5.select_all_change}"> - Select all (passed through filters)
                      </label>
                    </div>

                    <?php /* Блок кнопок "Actions" и "New" -->
                    <!-----------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="btn-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions with selected</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a data-bind="click: f.s5.del">Delete</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s5.attach2users.bind($data, ['groups'])">Attach sel.users to sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s5.attach2users.bind($data, ['privs'])">Attach sel.users to sel.privs</a></li>
                            <li><a href="#" data-bind="click: f.s5.attach2users.bind($data, ['tags'])">Attach sel.users to sel.tags</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s5.detach2users.bind($data, ['groups'])">Detach sel.users from sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s5.detach2users.bind($data, ['privs'])">Detach sel.users from sel.privs</a></li>
                            <li><a href="#" data-bind="click: f.s5.detach2users.bind($data, ['tags'])">Detach sel.users from sel.tags</a></li>
                          </ul>
                        </div>
                        <button type="button" class="btn btn-success btn-xs" title="Create a new item" data-bind="click: f.s1.choose_subdoc.bind($data, 3)">New user</button>
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
                        <th rowspan="1" colspan="1" style="width: 10%;">ID</th>
                        <th rowspan="1" colspan="1" style="width: 20%;">Email</th>
                        <th rowspan="1" colspan="1" style="width: 20%;">Phone</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Nickname</th>
                        <th rowspan="1" colspan="1" style="width: 11%;">Surname</th>
                        <th rowspan="1" colspan="1" style="width: 11%;">Name</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Patronymic</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s5.users">

                        <tr class="odd" data-bind="click: $root.f.s5.showedit, css: {blocked_user: is_blocked}">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s5.select_all_users(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id"></td>
                          <td>
                            <i class="fa fa-fw fa-exclamation" data-bind="visible: !is_email_approved() && email()" style="color: RGBA(255,0,0,.5)" title="This email is not approved"></i>
                            <span data-bind="text: email"></span>
                          </td>
                          <td>
                            <i class="fa fa-fw fa-exclamation" data-bind="visible: !is_phone_approved() && phone()" style="color: RGBA(255,0,0,.5)" title="This phone is not approved"></i>
                            <span data-bind="text: phone"></span>
                          </td>
                          <td data-bind="text: nickname"></td>
                          <td data-bind="text: surname"></td>
                          <td data-bind="text: name"></td>
                          <td data-bind="text: patronymic"></td>
                        </tr>

                      </tbody>

                    </table>

                    <?php /* Пагинация -->
                    <!---------------*/ ?>
                    <div class="box-footer">
                      <ul class="pagination pagi_margin pagination_cursor" style="float: right">

                        <?php /* Кнопка "Назад" -->
                        <!--------------------*/ ?>
                        <li class="paginate_button previous" data-bind="css: {disabled: $root.m.s5.pagi.pages_current() == 1}">
                          <a data-bind="click: f.s5.pagi.back">«</a>
                        </li>

                        <?php /* Кнопки "1,2,3,..." -->
                        <!------------------------*/ ?>
                        <!-- ko foreach: m.s5.pagi.pages -->
                        <li class="paginate_button" data-bind="css: {disabled: pagenum() == $root.m.s5.pagi.pages_current()}">
                          <a data-bind="text: pagenum, click: $root.f.s5.pagi.goto_data"></a>
                        </li>
                        <!-- /ko -->

                        <?php /* Кнопка "Вперед" -->
                        <!---------------------*/ ?>
                        <li class="paginate_button next" data-bind="css: {disabled: $root.m.s5.pagi.pages_current() == $root.m.s5.pagi.pages_total()}">
                          <a  data-bind="click: f.s5.pagi.forward">»</a>
                        </li>

                      </ul>
                    </div>

                  </div>

                </div>
              </div>

            </div>

          <?php /*-------------->
          <!-- 2.1.2. Фильтры -->
          <!----------------*/ ?>

            <?php /*------------->
            <!-- 1] Заголовок  -->
            <!---------------*/ ?>
            <h4>
              <b>Filters</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of turned on filters" data-bind="text: m.s5.filters_on_count"></span> / <span title="Total number of filters" data-bind="text: m.s5.filters_total"></span></small>
            </h4>

            <?php /*-------------->
            <!-- 2] Содержание  -->
            <!----------------*/ ?>
            <div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><div class="box box-warning"><div class="box-body"><div class="row">

              <?php /*----------------------->
              <!-- 2.1] Панель управления  -->
              <!-------------------------*/ ?>
              <div class="col-md-4 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* Заголовок -->
                <!---------------*/ ?>
                <div>
                  <b>Control panel</b>
                </div>

                <?php /* Кнопка "Применить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-success btn-sm filters_button" data-bind="click: f.s0.update_all.bind($data, ['users'])">Apply all filters</button>
                </div>

                <?php /* Кнопка "Включить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s5.filters_all_on">Turn on all filters</button>
                </div>

                <?php /* Кнопка "Выключить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s5.filters_all_off">Turn off all filters</button>
                </div>

                <?php /* Кнопка "Очистить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-danger btn-sm filters_button" data-bind="click: f.s5.filters_clear.bind($data, [])">Clear all filters</button>
                </div>

              </div>

              <?php /*------------>
              <!-- 2.2] Фильтры -->
              <!--------------*/ ?>
              <div class="col-md-8 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* 1) IDs of users  -->
                <!----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['ids of users'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['ids of users'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['ids of users'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['ids of users'].value, attr: {placeholder: m.s5.indexes.filters['ids of users'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 2) Email  -->
                <!---------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['email'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['email'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['email'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['email'].value, attr: {placeholder: m.s5.indexes.filters['email'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 3) Phone  -->
                <!---------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['phone'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['phone'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['phone'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['phone'].value, attr: {placeholder: m.s5.indexes.filters['phone'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 4) Name  -->
                <!--------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['name'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['name'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['name'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['name'].value, attr: {placeholder: m.s5.indexes.filters['name'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 5) Surname  -->
                <!-----------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['surname'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['surname'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['surname'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['surname'].value, attr: {placeholder: m.s5.indexes.filters['surname'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 6) Patronymic  -->
                <!--------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['patronymic'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['patronymic'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['patronymic'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['patronymic'].value, attr: {placeholder: m.s5.indexes.filters['patronymic'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 7) Gender  -->
                <!----------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['gender'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['gender'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['gender'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['gender'].value().male"><span> - Male</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['gender'].value().female"><span> - Female</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['gender'].value().undefined"><span> - Undefined</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 8) Anonymity  -->
                <!-------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['anonymity'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['anonymity'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['anonymity'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['anonymity'].value().anonymous"><span> - Anonymous</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['anonymity'].value().not_anonymous"><span> - Not anonymous</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 9) Block  -->
                <!---------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['block'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['block'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['block'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['block'].value().blocked"><span> - Blocked</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['block'].value().not_blocked"><span> - Not blocked</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 10) Email approvement  -->
                <!----------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['email approvement'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['email approvement'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['email approvement'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['email approvement'].value().approved"><span> - Approved</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['email approvement'].value().not_approved"><span> - Not approved</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 11) Phone approvment  -->
                <!---------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['phone approvement'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['phone approvement'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['phone approvement'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['phone approvement'].value().approved"><span> - Approved</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['phone approvement'].value().not_approved"><span> - Not approved</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 12) IDs of groups  -->
                <!------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['ids of groups'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['ids of groups'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['ids of groups'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['ids of groups'].value, attr: {placeholder: m.s5.indexes.filters['ids of groups'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s5.indexes.filters['ids of groups'].applybutton, click: f.s5.filters_apply_groups"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 13) IDs of privileges  -->
                <!----------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['ids of privileges'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['ids of privileges'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['ids of privileges'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['ids of privileges'].value, attr: {placeholder: m.s5.indexes.filters['ids of privileges'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s5.indexes.filters['ids of privileges'].applybutton, click: f.s5.filters_apply_privs"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 14) IDs of tags  -->
                <!----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['ids of tags'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['ids of tags'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['ids of tags'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s5.indexes.filters['ids of tags'].value, attr: {placeholder: m.s5.indexes.filters['ids of tags'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s5.indexes.filters['ids of tags'].applybutton, click: f.s5.filters_apply_tags"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 15) Admin  -->
                <!----------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s5.indexes.filters['admin'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s5.filters_clear.bind($data, ['admin'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s5.indexes.filters['admin'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['admin'].value().admin"><span> - Admin</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s5.indexes.filters['admin'].value().not_admin"><span> - Not admin</span></label></div>
                    </div>
                  </div>

                </div>

              </div>

            </div></div></div></div></div>

        </div>

        <?php /*--------------------------->
        <!-- 2.2. Свойства пользователя  -->
        <!-----------------------------*/ ?>
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
                  Users &nbsp; → &nbsp; Edit user
                </div>
              </div>
            </div>
          </div>

          <?php /*------------------------------------------------------>
          <!-- 2.2.2. Редактирование пользователя и прочий функционал -->
          <!--------------------------------------------------------*/ ?>
          <div class="row"><!-- ko if: m.s5.indexes.users[m.s5.edit.id()] -->

            <?php /*------------------------------------>
            <!-- 2.2.2.1. Редактирование пользователя -->
            <!--------------------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Properties
                </div>
                <div class="box-body form-horizontal" data-bind="style: {}">

                  <?php /* 0] Сообщение о том, что пользователь заблокирован -->
                  <!-------------------------------------------------------*/ ?>
                  <div class="callout callout-danger" data-bind="visible: m.s5.edit.isblocked">
                    <p>This user is blocked!</p>
                  </div>

                  <?php /* 1] ID -->
                  <!-----------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">ID</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s5.edit.id" disabled>
                    </div>
                  </div>

                  <?php /* 2] Email -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Email</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s5.edit.email">
                    </div>
                  </div>
                  <div class="callout callout-danger" data-bind="visible: m.s5.emailapprove.isapproved">
                    <p>Email of this user is not verified!</p>
                  </div>

                  <?php /* 3] Surname -->
                  <!----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Surname</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="ivanov" data-bind="textInput: m.s5.edit.surname">
                    </div>
                  </div>

                  <?php /* 4] Name -->
                  <!-------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Name</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="ivan" data-bind="textInput: m.s5.edit.name">
                    </div>
                  </div>

                  <?php /* 5] Patronymic -->
                  <!-------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Patronymic</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="ivanovich" data-bind="textInput: m.s5.edit.patronymic">
                    </div>
                  </div>

                  <?php /* 6] Gender -->
                  <!---------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Gender</div>
                    <div class="col-sm-10">
                      <select class="form-control" data-bind="options: m.s5.genders, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().name(); }, value: m.s5.edit.gender"></select>
                    </div>
                  </div>

                  <?php /* 7] Birthday -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Birthday</div>
                    <div class="col-sm-10">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="m-s5-edit-birthday" data-bind="textInput: m.s5.edit.birthday">
                      </div>
                    </div>
                  </div>

                  <?php /* 8] Phone -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Phone</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="79161112233" data-bind="textInput: m.s5.edit.phone">
                    </div>
                  </div>
                  <div class="callout callout-danger" data-bind="visible: m.s5.phoneapprove.isapproved">
                    <p>Phone of this user is not verified!</p>
                  </div>

                  <?php /* 9] Анонимный ли пользователь -->
                  <!----------------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Anon</div>
                    <div class="col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" data-bind="checked: m.s5.edit.isanonymous">
                        </label>
                      </div>
                    </div>
                  </div>

                  <?php /* 10] Заблокирован ли пользователь -->
                  <!--------------------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Block</div>
                    <div class="col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" data-bind="checked: m.s5.edit.isblocked">
                        </label>
                      </div>
                    </div>
                  </div>

                  <?php /* 11] Заметка админа -->
                  <!------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Admin note</div>
                    <div class="col-sm-10">
                      <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s5.edit.adminnote"></textarea>
                    </div>
                  </div>

                  <?php /* 12] ha_provider_name -->
                  <!--------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Provider name</div>
                    <div class="col-sm-9">
                      <input class="form-control" data-bind="textInput: m.s5.edit.ha_provider_name" disabled>
                    </div>
                  </div>

                  <?php /* 13] ha_provider_uid -->
                  <!-------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Provider uid</div>
                    <div class="col-sm-9">
                      <input class="form-control" data-bind="textInput: m.s5.edit.ha_provider_uid" disabled>
                    </div>
                  </div>

                  <?php /* 14] Nickname -->
                  <!------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Nickname</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="superivan" data-bind="textInput: m.s5.edit.nickname">
                    </div>
                  </div>

                  <?php /* n] Кнопка "Редактировать" -->
                  <!-------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s5.edit">Edit</button>
                  </div>

                </div>
              </div>
            </div>

            <?php /*--------------------------------------------------->
            <!-- 2.2.2.2. Аватар, смена пароля, проверка email/phone -->
            <!-----------------------------------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">

              <?php /*-------------->
              <!-- Проверка email -->
              <!----------------*/ ?>
              <div class="box box-danger" data-bind="visible: m.s5.emailapprove.isapproved">
                <div class="box-header with-border subdoc_title">
                  Email verification
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Кнопки -->
                  <!---------------*/ ?>
                  <div style="margin-bottom: 15px">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm" data-bind="click: f.s5.send_email_ver_code">
                        <i class="fa fa-rotate-right"></i>
                        <span> &nbsp; Get code</span>
                      </button>
                      <button type="button" class="btn btn-success btn-sm" data-bind="click: f.s5.email_verify_code">
                        <i class="fa fa-send"></i>
                        <span> &nbsp; Verify</span>
                      </button>
                    </div>
                  </div>

                  <?php /* 2] Информационное сообщение -->
                  <!---------------------------------*/ ?>
                  <div>
                    <p>Once you click "Get code", you'll get the new code to the specified email box. Enter this code to the field below and click "Verify".</p>
                  </div>

                  <?php /* 3] Поле для ввода кода подтверждения -->
                  <!------------------------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Code</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="Enter code " data-bind="textInput: m.s5.emailapprove.code">
                    </div>
                  </div>

                </div>
              </div>

              <?php /*-------------->
              <!-- Проверка phone -->
              <!----------------*/ ?>
              <div class="box box-danger" data-bind="visible: !m.s5.indexes.users[m.s5.edit.id()].is_phone_approved()">
                <div class="box-header with-border subdoc_title">
                  Phone verification
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Кнопки -->
                  <!---------------*/ ?>
                  <div style="margin-bottom: 15px">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm">
                        <i class="fa fa-rotate-right"></i>
                        <span> &nbsp; Get code</span>
                      </button>
                      <button type="button" class="btn btn-success btn-sm">
                        <i class="fa fa-send"></i>
                        <span> &nbsp; Verify</span>
                      </button>
                    </div>
                  </div>

                  <?php /* 2] Информационное сообщение -->
                  <!---------------------------------*/ ?>
                  <div>
                    <p>Once you click "Get code", you'll get the new code by SMS to the specified phone number. Enter this code to the field below and click "Verify".</p>
                  </div>

                  <?php /* 3] Поле для ввода кода подтверждения -->
                  <!------------------------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Code</div>
                    <div class="col-sm-10">
                      <input class="form-control" placeholder="Enter code " data-bind="textInput: m.s5.emailapprove.code">
                    </div>
                  </div>

                </div>
              </div>

              <?php /*------>
              <!-- Аватар -->
              <!--------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Avatar
                </div>
                <div class="box-body">

                  <?php /* 1] Выбор файла с аватаром -->
                  <!-------------------------------*/ ?>
                  <div class="form-group">
                    <label for="exampleInputFile">Choose image</label>
                    <input type="file" id="avatar-image-input">
                  </div>

                  <?php /* 2] Превью выбранного изображения -->
                  <!--------------------------------------*/ ?>
                  <div style="margin-bottom: 15px">
                    <img src="http://placehold.it/100x100">
                  </div>

                  <?php /* n] Кнопка "Изменить" -->
                  <!--------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s5.change_password">Change</button>
                  </div>

                </div>
              </div>

              <?php /*------------>
              <!-- Смена пароля -->
              <!--------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Change password of this user
                </div>
                <div class="box-body form">

                  <?php /* 1] Новый пароль -->
                  <!---------------------*/ ?>
                  <div class="form-group">
                    <label>New password</label>
                    <div>
                      <input class="form-control input-sm" data-bind="textInput: m.s5.changepass.new1">
                    </div>
                  </div>

                  <?php /* 2] Новый пароль -->
                  <!---------------------*/ ?>
                  <div class="form-group">
                    <label>New password again</label>
                    <div>
                      <input class="form-control input-sm" data-bind="textInput: m.s5.changepass.new2">
                    </div>
                  </div>

                  <?php /* n] Кнопка "Изменить" -->
                  <!--------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s5.change_password">Change</button>
                  </div>

                </div>
              </div>


            </div>

          <!-- /ko --></div>

        </div>

        <?php /*------------------------>
        <!-- 2.3. Новый пользователь  -->
        <!--------------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 3}">

          <?php /*--------------------------------------------->
          <!-- 2.3.1. Кнопка "Назад" и название поддокумента -->
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
                  Users &nbsp; → &nbsp; New user
                </div>
              </div>
            </div>
          </div>

          <?php /*-------------------------------------->
          <!-- 2.3.2. Регистрация нового пользователя -->
          <!----------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  New user
                </div>
                <div class="box-body">

                  <?php /* Поля формы -->
                  <!----------------*/ ?>
                  <div class="form-horizontal">

                    <?php /* 1] Email -->
                    <!--------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Email</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="ivan@gmail.com" data-bind="textInput: m.s5.new.email">
                      </div>
                    </div>

                    <?php /* 2] Surname -->
                    <!----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Surname</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="ivanov" data-bind="textInput: m.s5.new.surname">
                      </div>
                    </div>

                    <?php /* 3] Name -->
                    <!-------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Name</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="ivan" data-bind="textInput: m.s5.new.name">
                      </div>
                    </div>

                    <?php /* 4] Patronymic -->
                    <!-------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Patronymic</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="ivanovich" data-bind="textInput: m.s5.new.patronymic">
                      </div>
                    </div>

                    <?php /* 5] Gender -->
                    <!---------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Gender</div>
                      <div class="col-sm-10">
                        <select class="form-control" data-bind="options: m.s5.genders, optionsText: function(item){ return item().name(); }, optionsValue: function(item){ return item().name(); }, value: m.s5.new.gender"></select>
                      </div>
                    </div>

                    <?php /* 6] Birthday -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Birthday</div>
                      <div class="col-sm-10">
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control pull-right" id="m-s5-new-birthday" data-bind="textInput: m.s5.new.birthday">
                        </div>
                      </div>
                    </div>

                    <?php /* 7] Phone -->
                    <!--------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Phone</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="79161112233" data-bind="textInput: m.s5.new.phone">
                      </div>
                    </div>

                    <?php /* 8] Анонимный ли пользователь -->
                    <!----------------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Anon</div>
                      <div class="col-sm-10">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" data-bind="checked: m.s5.new.isanonymous">
                          </label>
                        </div>
                      </div>
                    </div>

                    <?php /* 9] Password -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Password</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="ivanovich" data-bind="textInput: m.s5.new.password">
                      </div>
                    </div>

                    <?php /* 10] Nickname -->
                    <!------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Nickname</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="superivan" data-bind="textInput: m.s5.new.nickname">
                      </div>
                    </div>

                    <?php /* n] Заметка админа -->
                    <!-----------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Admin note</div>
                      <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s5.new.adminnote"></textarea>
                      </div>
                    </div>

                  </div>

                  <?php /* Кнопка "Создать" -->
                  <!----------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s5.create">Create</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*------------>
        <!-- 2.4. Группы  -->
        <!--------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 4}">

          <?php /*--------------->
          <!-- 2.4.1. Группы  -->
          <!-----------------*/ ?>

            <?php /*------------------------>
            <!-- 1] Название поддокумента -->
            <!---------------------------*/ ?>
            <h4>
              <b>Groups</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of items have selected" data-bind="text: m.s2.pagi.num_of_selected_groups"></span> / <span title="Nuber of items have passed filters" data-bind="text: m.s2.pagi.groups_filtered"></span> / <span title="Total number of items" data-bind="text: m.s2.pagi.groups_total"></span></small>
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
                        <input type="checkbox" data-bind="checked: m.s2.select_all_groups, event: {change: f.s2.select_all_change}"> - Select all (passed through filters)
                      </label>
                    </div>

                    <?php /* Блок кнопок "Actions" и "New" -->
                    <!-----------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="btn-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions with selected</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a data-bind="click: f.s2.del">Delete</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s2.attach2groups.bind($data, ['users'])">Attach sel.groups to sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s2.attach2groups.bind($data, ['privs'])">Attach sel.groups to sel.privs</a></li>
                            <li><a href="#" data-bind="click: f.s2.attach2groups.bind($data, ['tags'])">Attach sel.groups to sel.tags</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s2.detach2groups.bind($data, ['users'])">Detach sel.groups from sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s2.detach2groups.bind($data, ['privs'])">Detach sel.groups from sel.privs</a></li>
                            <li><a href="#" data-bind="click: f.s2.detach2groups.bind($data, ['tags'])">Detach sel.groups from sel.tags</a></li>
                          </ul>
                        </div>
                        <button type="button" class="btn btn-success btn-xs" title="Create a new item" data-bind="click: f.s1.choose_subdoc.bind($data, 6)">New group</button>
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
                        <th rowspan="1" colspan="1" style="width: 10%;">ID</th>
                        <th rowspan="1" colspan="1" style="width: 5%;">Admin</th>
                        <th rowspan="1" colspan="1" style="width: 77%;">Title</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s2.groups">

                        <tr class="odd" data-bind="click: $root.f.s2.showedit">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s2.select_all_groups(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id"></td>
                          <td data-bind="text: isadmin"></td>
                          <td data-bind="text: name"></td>
                        </tr>

                      </tbody>

                    </table>

                    <?php /* Пагинация -->
                    <!---------------*/ ?>
                    <div class="box-footer">
                      <ul class="pagination pagi_margin pagination_cursor" style="float: right">

                        <?php /* Кнопка "Назад" -->
                        <!--------------------*/ ?>
                        <li class="paginate_button previous" data-bind="css: {disabled: $root.m.s2.pagi.pages_current() == 1}">
                          <a data-bind="click: f.s2.pagi.back">«</a>
                        </li>

                        <?php /* Кнопки "1,2,3,..." -->
                        <!------------------------*/ ?>
                        <!-- ko foreach: m.s2.pagi.pages -->
                        <li class="paginate_button" data-bind="css: {disabled: pagenum() == $root.m.s2.pagi.pages_current()}">
                          <a data-bind="text: pagenum, click: $root.f.s2.pagi.goto_data"></a>
                        </li>
                        <!-- /ko -->

                        <?php /* Кнопка "Вперед" -->
                        <!---------------------*/ ?>
                        <li class="paginate_button next" data-bind="css: {disabled: $root.m.s2.pagi.pages_current() == $root.m.s2.pagi.pages_total()}">
                          <a  data-bind="click: f.s2.pagi.forward">»</a>
                        </li>

                      </ul>
                    </div>

                  </div>

                </div>
              </div>

            </div>

          <?php /*--------------->
          <!-- 2.4.2. Фильтры  -->
          <!-----------------*/ ?>

            <?php /*------------->
            <!-- 1] Заголовок  -->
            <!---------------*/ ?>
            <h4>
              <b>Filters</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of turned on filters" data-bind="text: m.s2.filters_on_count"></span> / <span title="Total number of filters" data-bind="text: m.s2.filters_total"></span></small>
            </h4>

            <?php /*-------------->
            <!-- 2] Содержание  -->
            <!----------------*/ ?>
            <div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><div class="box box-warning"><div class="box-body"><div class="row">

              <?php /*----------------------->
              <!-- 2.1] Панель управления  -->
              <!-------------------------*/ ?>
              <div class="col-md-4 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* Заголовок -->
                <!---------------*/ ?>
                <div>
                  <b>Control panel</b>
                </div>

                <?php /* Кнопка "Применить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-success btn-sm filters_button" data-bind="click: f.s0.update_all.bind($data, ['groups'])">Apply all filters</button>
                </div>

                <?php /* Кнопка "Включить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s2.filters_all_on">Turn on all filters</button>
                </div>

                <?php /* Кнопка "Выключить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s2.filters_all_off">Turn off all filters</button>
                </div>

                <?php /* Кнопка "Очистить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-danger btn-sm filters_button" data-bind="click: f.s2.filters_clear.bind($data, [])">Clear all filters</button>
                </div>

              </div>

              <?php /*------------>
              <!-- 2.2] Фильтры -->
              <!--------------*/ ?>
              <div class="col-md-8 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* 1) IDs of groups  -->
                <!-----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['ids of groups'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['ids of groups'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['ids of groups'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s2.indexes.filters['ids of groups'].value, attr: {placeholder: m.s2.indexes.filters['ids of groups'].placeholder}"></div>
                    </div>
                  </div>

                </div>

                <?php /* 2) Title  -->
                <!---------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['title'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['title'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['title'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s2.indexes.filters['title'].value, attr: {placeholder: m.s2.indexes.filters['title'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 3) Admin  -->
                <!---------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['admin'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['admin'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['admin'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s2.indexes.filters['admin'].value().admin"><span> - Admin</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s2.indexes.filters['admin'].value().not_admin"><span> - Not admin</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 4) IDs of users  -->
                <!-----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['ids of users'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['ids of users'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['ids of users'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s2.indexes.filters['ids of users'].value, attr: {placeholder: m.s2.indexes.filters['ids of users'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s2.indexes.filters['ids of users'].applybutton, click: f.s2.filters_apply_users"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 5) IDs of privileges  -->
                <!---------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['ids of privileges'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['ids of privileges'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['ids of privileges'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s2.indexes.filters['ids of privileges'].value, attr: {placeholder: m.s2.indexes.filters['ids of privileges'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s2.indexes.filters['ids of privileges'].applybutton, click: f.s2.filters_apply_privs"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 6) IDs of tags  -->
                <!---------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s2.indexes.filters['ids of tags'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s2.filters_clear.bind($data, ['ids of tags'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s2.indexes.filters['ids of tags'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s2.indexes.filters['ids of tags'].value, attr: {placeholder: m.s2.indexes.filters['ids of tags'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s2.indexes.filters['ids of tags'].applybutton, click: f.s2.filters_apply_tags"></button></div>
                    </div>
                  </div>

                </div>


              </div>

            </div></div></div></div></div>


        </div>

        <?php /*--------------------->
        <!-- 2.5. Свойства группы  -->
        <!-----------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 5}">

          <?php /*--------------------------------------------->
          <!-- 2.5.1. Кнопка "Назад" и название поддокумента -->
          <!-----------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 4)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Groups &nbsp; → &nbsp; Edit group
                </div>
              </div>
            </div>
          </div>

          <?php /*---------------------------->
          <!-- 2.5.2. Редактирование группы -->
          <!------------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Edit group
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] ID -->
                  <!-----------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">ID</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s2.edit.id" disabled>
                    </div>
                  </div>

                  <?php /* 2] Название -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Name</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s2.edit.title">
                    </div>
                  </div>

                  <?php /* 3] Административная ли группа -->
                  <!-----------------------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Admin</div>
                    <div class="col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" data-bind="checked: m.s2.edit.admin">
                        </label>
                      </div>
                    </div>
                  </div>

                  <?php /* 4] Описание -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Desc</div>
                    <div class="col-sm-10">
                      <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s2.edit.description"></textarea>
                    </div>
                  </div>

                  <?php /* 5] Кнопка "Редактировать" -->
                  <!-------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.edit">Edit</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*------------------>
        <!-- 2.6. Новая группа  -->
        <!--------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 6}">

          <?php /*--------------------------------------------->
          <!-- 2.6.1. Кнопка "Назад" и название поддокумента -->
          <!-----------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 4)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Groups &nbsp; → &nbsp; New group
                </div>
              </div>
            </div>
          </div>

          <?php /*------------------------------->
          <!-- 2.6.2. Регистрация новой группы -->
          <!---------------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  New group
                </div>
                <div class="box-body">

                  <?php /* Обязательные поля -->
                  <!-----------------------*/ ?>
                  <div class="form-horizontal">
                    <div><b>Mandatory fields</b></div><br>

                    <?php /* 1] Название -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Name</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="group_title" data-bind="textInput: m.s2.new.title">
                      </div>
                    </div>

                    <?php /* 2] Административная ли группа -->
                    <!-----------------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Admin</div>
                      <div class="col-sm-10">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" data-bind="checked: m.s2.new.admin">
                          </label>
                        </div>
                      </div>
                    </div>

                  </div>

                  <?php /* Не обязательные поля -->
                  <!--------------------------*/ ?>
                  <div class="form-horizontal">
                    <div><b>Not mandatory fields</b></div><br>

                    <?php /* 1] Описание -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Desc</div>
                      <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s2.new.description"></textarea>
                      </div>
                    </div>

                  </div>

                  <?php /* Кнопка "Создать" -->
                  <!----------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s2.create">Create</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*----------->
        <!-- 2.7. Права  -->
        <!-------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 7}">

          <?php /*------------->
          <!-- 2.7.1. Права  -->
          <!---------------*/ ?>

            <?php /*------------------------>
            <!-- 1] Название поддокумента -->
            <!---------------------------*/ ?>
            <h4>
              <b>Privileges</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of items have selected" data-bind="text: m.s3.pagi.num_of_selected_privs"></span> / <span title="Nuber of items have passed filters" data-bind="text: m.s3.pagi.privs_filtered"></span> / <span title="Total number of items" data-bind="text: m.s3.pagi.privs_total"></span></small>
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
                        <input type="checkbox" data-bind="checked: m.s3.select_all_privs, event: {change: f.s3.select_all_change}"> - Select all (passed through filters)
                      </label>
                    </div>

                    <?php /* Блок кнопок "Actions" и "New" -->
                    <!-----------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="btn-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions with selected</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a data-bind="click: f.s3.del">Delete</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s3.attach2privs.bind($data, ['users'])">Attach sel.privs to sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s3.attach2privs.bind($data, ['groups'])">Attach sel.privs to sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s3.attach2privs.bind($data, ['tags'])">Attach sel.privs to sel.tags</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s3.detach2privs.bind($data, ['users'])">Detach sel.privs from sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s3.detach2privs.bind($data, ['groups'])">Detach sel.privs from sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s3.detach2privs.bind($data, ['tags'])">Detach sel.privs from sel.tags</a></li>
                          </ul>
                        </div>
                        <button type="button" class="btn btn-success btn-xs" title="Create a new item" data-bind="click: f.s1.choose_subdoc.bind($data, 9)">New privilege</button>
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
                        <th rowspan="1" colspan="1" style="width: 10%;">ID</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Type</th>
                        <th rowspan="1" colspan="1" style="width: 10%;">Name</th>
                        <th rowspan="1" colspan="1" style="width: 62%;">Description</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s3.privs">

                        <tr class="odd" data-bind="click: $root.f.s3.showedit">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s3.select_all_privs(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id"></td>
                          <td data-bind="text: type"></td>
                          <td data-bind="text: name"></td>
                          <td data-bind="text: description"></td>
                        </tr>

                      </tbody>

                    </table>

                    <?php /* Пагинация -->
                    <!---------------*/ ?>
                    <div class="box-footer">
                      <ul class="pagination pagi_margin pagination_cursor" style="float: right">

                        <?php /* Кнопка "Назад" -->
                        <!--------------------*/ ?>
                        <li class="paginate_button previous" data-bind="css: {disabled: $root.m.s3.pagi.pages_current() == 1}">
                          <a data-bind="click: f.s3.pagi.back">«</a>
                        </li>

                        <?php /* Кнопки "1,2,3,..." -->
                        <!------------------------*/ ?>
                        <!-- ko foreach: m.s3.pagi.pages -->
                        <li class="paginate_button" data-bind="css: {disabled: pagenum() == $root.m.s3.pagi.pages_current()}">
                          <a data-bind="text: pagenum, click: $root.f.s3.pagi.goto_data"></a>
                        </li>
                        <!-- /ko -->

                        <?php /* Кнопка "Вперед" -->
                        <!---------------------*/ ?>
                        <li class="paginate_button next" data-bind="css: {disabled: $root.m.s3.pagi.pages_current() == $root.m.s3.pagi.pages_total()}">
                          <a  data-bind="click: f.s3.pagi.forward">»</a>
                        </li>

                      </ul>
                    </div>

                  </div>

                </div>
              </div>

            </div>

          <?php /*--------------->
          <!-- 2.7.2. Фильтры  -->
          <!-----------------*/ ?>

            <?php /*------------->
            <!-- 1] Заголовок  -->
            <!---------------*/ ?>
            <h4>
              <b>Filters</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of turned on filters" data-bind="text: m.s3.filters_on_count"></span> / <span title="Total number of filters" data-bind="text: m.s3.filters_total"></span></small>
            </h4>

            <?php /*-------------->
            <!-- 2] Содержание  -->
            <!----------------*/ ?>
            <div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><div class="box box-warning"><div class="box-body"><div class="row">

              <?php /*----------------------->
              <!-- 2.1] Панель управления  -->
              <!-------------------------*/ ?>
              <div class="col-md-4 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* Заголовок -->
                <!---------------*/ ?>
                <div>
                  <b>Control panel</b>
                </div>

                <?php /* Кнопка "Применить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-success btn-sm filters_button" data-bind="click: f.s0.update_all.bind($data, ['privs'])">Apply all filters</button>
                </div>

                <?php /* Кнопка "Включить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s3.filters_all_on">Turn on all filters</button>
                </div>

                <?php /* Кнопка "Выключить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s3.filters_all_off">Turn off all filters</button>
                </div>

                <?php /* Кнопка "Очистить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-danger btn-sm filters_button" data-bind="click: f.s3.filters_clear.bind($data, [])">Clear all filters</button>
                </div>

              </div>

              <?php /*------------>
              <!-- 2.2] Фильтры -->
              <!--------------*/ ?>
              <div class="col-md-8 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* 1) IDs of privileges  -->
                <!---------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['ids of privileges'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['ids of privileges'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['ids of privileges'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s3.indexes.filters['ids of privileges'].value, attr: {placeholder: m.s3.indexes.filters['ids of privileges'].placeholder}"></div>
                    </div>
                  </div>

                </div>

                <?php /* 2) Name  -->
                <!--------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['name'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['name'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['name'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s3.indexes.filters['name'].value, attr: {placeholder: m.s3.indexes.filters['name'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 3) Privilege type  -->
                <!------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['privilege type'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['privilege type'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['privilege type'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group my-cb-group">
                      <div><label><input type="checkbox" data-bind="checked: m.s3.indexes.filters['privilege type'].value().access"><span> - Access</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s3.indexes.filters['privilege type'].value().exec"><span> - Exec</span></label></div>
                      <div><label><input type="checkbox" data-bind="checked: m.s3.indexes.filters['privilege type'].value().custom"><span> - Custom</span></label></div>
                    </div>
                  </div>

                </div>

                <?php /* 4) IDs of users  -->
                <!-----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['ids of users'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['ids of users'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['ids of users'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s3.indexes.filters['ids of users'].value, attr: {placeholder: m.s3.indexes.filters['ids of users'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s3.indexes.filters['ids of users'].applybutton, click: f.s3.filters_apply_users"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 5) IDs of groups -->
                <!----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['ids of groups'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['ids of groups'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['ids of groups'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s3.indexes.filters['ids of groups'].value, attr: {placeholder: m.s3.indexes.filters['ids of groups'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s3.indexes.filters['ids of groups'].applybutton, click: f.s3.filters_apply_groups"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 6) IDs of tags  -->
                <!---------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s3.indexes.filters['ids of tags'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s3.filters_clear.bind($data, ['ids of tags'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s3.indexes.filters['ids of tags'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s3.indexes.filters['ids of tags'].value, attr: {placeholder: m.s3.indexes.filters['ids of tags'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s3.indexes.filters['ids of tags'].applybutton, click: f.s3.filters_apply_tags"></button></div>
                    </div>
                  </div>

                </div>


              </div>

            </div></div></div></div></div>


        </div>

        <?php /*-------------------->
        <!-- 2.8. Свойства права  -->
        <!----------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 8}">

          <?php /*--------------------------------------------->
          <!-- 2.8.1. Кнопка "Назад" и название поддокумента -->
          <!-----------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 7)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Privileges &nbsp; → &nbsp; Edit privilege
                </div>
              </div>
            </div>
          </div>

          <?php /*--------------------------->
          <!-- 2.8.2. Редактирование права -->
          <!-----------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Edit privilege
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] ID -->
                  <!-----------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">ID</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s3.edit.id" disabled>
                    </div>
                  </div>

                  <?php /* 2] Название -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Name</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s3.edit.title, attr: {disabled: m.s3.edit.type() != 'custom'}">
                    </div>
                  </div>

                  <?php /* 3] Тип -->
                  <!------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Type</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s3.edit.type" disabled>
                    </div>
                  </div>

                  <?php /* 4] Описание -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Desc</div>
                    <div class="col-sm-10">
                      <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s3.edit.description, attr: {disabled: m.s3.edit.type() != 'custom'}"></textarea>
                    </div>
                  </div>

                  <?php /* 5] Кнопка "Редактировать" -->
                  <!-------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s3.edit, visible: m.s3.edit.type() == 'custom'">Edit</button>
                  </div>

                  <?php /* n] Сообщение о невозможности редактировать не custom права -->
                  <!----------------------------------------------------------------*/ ?>
                  <div class="callout callout-danger" data-bind="visible: m.s3.edit.type() != 'custom'">
                    <h4>You can't edit this privilege!</h4>

                    <p>You can edit only privileges of type 'custom'.</p>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*----------------->
        <!-- 2.9. Новое право  -->
        <!-------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 9}">

          <?php /*--------------------------------------------->
          <!-- 2.9.1. Кнопка "Назад" и название поддокумента -->
          <!-----------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 7)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Privileges &nbsp; → &nbsp; New custom privilege
                </div>
              </div>
            </div>
          </div>

          <?php /*------------------------------->
          <!-- 2.9.2. Регистрация нового права -->
          <!---------------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  New custom privilege
                </div>
                <div class="box-body">

                  <?php /* Обязательные поля -->
                  <!-----------------------*/ ?>
                  <div class="form-horizontal">
                    <div><b>Mandatory fields</b></div><br>

                    <?php /* 1] Название -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Name</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="some_privilege" data-bind="textInput: m.s3.new.title">
                      </div>
                    </div>

                  </div>

                  <?php /* Не обязательные поля -->
                  <!---------------------------*/ ?>
                  <div class="form-horizontal">
                    <div><b>Not mandatory fields</b></div><br>

                    <?php /* 1] Описание -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Desc</div>
                      <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="Enter ..." data-bind="textInput: m.s3.new.description"></textarea>
                      </div>
                    </div>

                  </div>

                  <?php /* Кнопка "Создать" -->
                  <!----------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s3.create">Create</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*----------->
        <!-- 2.10. Теги  -->
        <!-------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 10}">

          <?php /*------------->
          <!-- 2.10.1. Теги  -->
          <!---------------*/ ?>

            <?php /*------------------------>
            <!-- 1] Название поддокумента -->
            <!---------------------------*/ ?>
            <h4>
              <b>Tags</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of items have selected" data-bind="text: m.s4.pagi.num_of_selected_tags"></span> / <span title="Nuber of items have passed filters" data-bind="text: m.s4.pagi.tags_filtered"></span> / <span title="Total number of items" data-bind="text: m.s4.pagi.tags_total"></span></small>
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
                        <input type="checkbox" data-bind="checked: m.s4.select_all_tags, event: {change: f.s4.select_all_change}"> - Select all (passed through filters)
                      </label>
                    </div>

                    <?php /* Блок кнопок "Actions" и "New" -->
                    <!-----------------------------------*/ ?>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="btn-group">
                        <div class="btn-group">
                          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" title="Actions with the selected items">
                            <i class="fa fa-gear"></i>
                            <span> Actions with selected</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a data-bind="click: f.s4.del">Delete</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s4.attach2tags.bind($data, ['users'])">Attach sel.tags to sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s4.attach2tags.bind($data, ['groups'])">Attach sel.tags to sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s4.attach2tags.bind($data, ['privs'])">Attach sel.tags to sel.privs</a></li>
                            <li class="divider"></li>
                            <li><a href="#" data-bind="click: f.s4.detach2tags.bind($data, ['users'])">Detach sel.tags from sel.users</a></li>
                            <li><a href="#" data-bind="click: f.s4.detach2tags.bind($data, ['groups'])">Detach sel.tags from sel.groups</a></li>
                            <li><a href="#" data-bind="click: f.s4.detach2tags.bind($data, ['privs'])">Detach sel.tags from sel.privs</a></li>
                          </ul>
                        </div>
                        <button type="button" class="btn btn-success btn-xs" title="Create a new item" data-bind="click: f.s1.choose_subdoc.bind($data, 12)">New tag</button>
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
                        <th rowspan="1" colspan="1" style="width: 10%;">ID</th>
                        <th rowspan="1" colspan="1" style="width: 82%;">Tag</th>
                      </tr>
                      </thead>
                      <tbody data-bind="foreach: m.s4.tags">

                        <tr class="odd" data-bind="click: $root.f.s4.showedit">
                          <td data-bind="click: function(){ return true; }, clickBubble: false"><input type="checkbox" data-bind="checked: selected, click: function(data, event){ if(!event.target.checked) $root.m.s4.select_all_tags(false); return true; }"></td>
                          <td data-bind="text: number"></td>
                          <td data-bind="text: id"></td>
                          <td data-bind="text: name"></td>
                        </tr>

                      </tbody>

                    </table>

                    <?php /* Пагинация -->
                    <!---------------*/ ?>
                    <div class="box-footer">
                      <ul class="pagination pagi_margin pagination_cursor" style="float: right">

                        <?php /* Кнопка "Назад" -->
                        <!--------------------*/ ?>
                        <li class="paginate_button previous" data-bind="css: {disabled: $root.m.s4.pagi.pages_current() == 1}">
                          <a data-bind="click: f.s4.pagi.back">«</a>
                        </li>

                        <?php /* Кнопки "1,2,3,..." -->
                        <!------------------------*/ ?>
                        <!-- ko foreach: m.s4.pagi.pages -->
                        <li class="paginate_button" data-bind="css: {disabled: pagenum() == $root.m.s4.pagi.pages_current()}">
                          <a data-bind="text: pagenum, click: $root.f.s4.pagi.goto_data"></a>
                        </li>
                        <!-- /ko -->

                        <?php /* Кнопка "Вперед" -->
                        <!---------------------*/ ?>
                        <li class="paginate_button next" data-bind="css: {disabled: $root.m.s4.pagi.pages_current() == $root.m.s4.pagi.pages_total()}">
                          <a  data-bind="click: f.s4.pagi.forward">»</a>
                        </li>

                      </ul>
                    </div>

                  </div>

                </div>
              </div>

            </div>

          <?php /*--------------->
          <!-- 2.10.2. Фильтры -->
          <!-----------------*/ ?>

            <?php /*------------->
            <!-- 1] Заголовок  -->
            <!---------------*/ ?>
            <h4>
              <b>Filters</b>
              <small class="small_notes" style="cursor: default">- <span title="Number of turned on filters" data-bind="text: m.s4.filters_on_count"></span> / <span title="Total number of filters" data-bind="text: m.s4.filters_total"></span></small>
            </h4>

            <?php /*-------------->
            <!-- 2] Содержание  -->
            <!----------------*/ ?>
            <div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><div class="box box-warning"><div class="box-body"><div class="row">

              <?php /*----------------------->
              <!-- 2.1] Панель управления  -->
              <!-------------------------*/ ?>
              <div class="col-md-4 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* Заголовок -->
                <!---------------*/ ?>
                <div>
                  <b>Control panel</b>
                </div>

                <?php /* Кнопка "Применить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-success btn-sm filters_button" data-bind="click: f.s0.update_all.bind($data, ['tags'])">Apply all filters</button>
                </div>

                <?php /* Кнопка "Включить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s4.filters_all_on">Turn on all filters</button>
                </div>

                <?php /* Кнопка "Выключить все фильтры"  -->
                <!-------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-default btn-sm filters_button" data-bind="click: f.s4.filters_all_off">Turn off all filters</button>
                </div>

                <?php /* Кнопка "Очистить все фильтры"  -->
                <!------------------------------------*/ ?>
                <div>
                  <button type="button" class="btn btn-block btn-danger btn-sm filters_button" data-bind="click: f.s4.filters_clear.bind($data, [])">Clear all filters</button>
                </div>

              </div>

              <?php /*------------>
              <!-- 2.2] Фильтры -->
              <!--------------*/ ?>
              <div class="col-md-8 col-sm-6 col-xs-12" style="margin-top: 10px">

                <?php /* 1) IDs of tags  -->
                <!---------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s4.indexes.filters['ids of tags'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s4.filters_clear.bind($data, ['ids of tags'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s4.indexes.filters['ids of tags'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s4.indexes.filters['ids of tags'].value, attr: {placeholder: m.s4.indexes.filters['ids of tags'].placeholder}"></div>
                    </div>
                  </div>

                </div>

                <?php /* 2) Tag  -->
                <!-------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s4.indexes.filters['tag'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s4.filters_clear.bind($data, ['tag'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s4.indexes.filters['tag'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <input type="text" class="form-control input-sm" data-bind="value: m.s4.indexes.filters['tag'].value, attr: {placeholder: m.s4.indexes.filters['tag'].placeholder}">
                    </div>
                  </div>

                </div>

                <?php /* 3) IDs of users  -->
                <!-----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s4.indexes.filters['ids of users'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s4.filters_clear.bind($data, ['ids of users'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s4.indexes.filters['ids of users'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s4.indexes.filters['ids of users'].value, attr: {placeholder: m.s4.indexes.filters['ids of users'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s4.indexes.filters['ids of users'].applybutton, click: f.s4.filters_apply_users"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 4) IDs of groups -->
                <!----------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s4.indexes.filters['ids of groups'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s4.filters_clear.bind($data, ['ids of groups'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s4.indexes.filters['ids of groups'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s4.indexes.filters['ids of groups'].value, attr: {placeholder: m.s4.indexes.filters['ids of groups'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s4.indexes.filters['ids of groups'].applybutton, click: f.s4.filters_apply_groups"></button></div>
                    </div>
                  </div>

                </div>

                <?php /* 5) IDs of privileges  -->
                <!---------------------------*/ ?>
                <div>

                  <?php /* Заголовок фильтра -->
                  <!-----------------------*/ ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6"><b><span data-bind="text: m.s4.indexes.filters['ids of privileges'].name"></span></b></div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <button type="button" class="btn btn-block btn-default btn-xs" data-bind="click: f.s4.filters_clear.bind($data, ['ids of privileges'])">Clear</button>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label class="turnon_filter_label checkbox" title="Turn on/off that filter">
                        <input type="checkbox" data-bind="checked: m.s4.indexes.filters['ids of privileges'].on"> - on
                      </label>
                    </div>
                  </div>

                  <?php /* Тело фильтра -->
                  <!------------------*/ ?>
                  <div style="margin: 5px 0 10px 0">
                    <div class="form-group">
                      <div><input type="text" class="form-control input-sm" data-bind="value: m.s4.indexes.filters['ids of privileges'].value, attr: {placeholder: m.s4.indexes.filters['ids of privileges'].placeholder}"></div>
                      <div><button type="button" class="btn btn-block btn-default btn-xs my-filters-apply-ids-button" data-bind="text: m.s4.indexes.filters['ids of privileges'].applybutton, click: f.s4.filters_apply_privs"></button></div>
                    </div>
                  </div>

                </div>

              </div>

            </div></div></div></div></div>

        </div>

        <?php /*-------------------->
        <!-- 2.11. Свойства тега  -->
        <!----------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 11}">

          <?php /*---------------------------------------------->
          <!-- 2.11.1. Кнопка "Назад" и название поддокумента -->
          <!------------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 10)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Tags &nbsp; → &nbsp; Edit tag
                </div>
              </div>
            </div>
          </div>

          <?php /*--------------------------->
          <!-- 2.11.2. Редактирование тега -->
          <!-----------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Edit group
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] ID -->
                  <!-----------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">ID</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s4.edit.id" disabled>
                    </div>
                  </div>

                  <?php /* 2] Название -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-2 control-label">Tag</div>
                    <div class="col-sm-10">
                      <input class="form-control" data-bind="textInput: m.s4.edit.title">
                    </div>
                  </div>

                  <?php /* n] Кнопка "Редактировать" -->
                  <!-------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s4.edit">Edit</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*---------------->
        <!-- 2.12. Новый тег  -->
        <!------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 12}">

          <?php /*---------------------------------------------->
          <!-- 2.12.1. Кнопка "Назад" и название поддокумента -->
          <!------------------------------------------------*/ ?>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2">
              <div class="box">
                <div class="box-body back_link" data-bind="click: f.s1.choose_subdoc.bind($data, 10)">
                  <span>
                    <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-10 col-sm-10 col-xs-10">
              <div class="box">
                <div class="box-body subdoc_title">
                  Tags &nbsp; → &nbsp; New tag
                </div>
              </div>
            </div>
          </div>

          <?php /*------------------------------->
          <!-- 2.12.2. Регистрация нового тега -->
          <!---------------------------------*/ ?>
          <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  New tag
                </div>
                <div class="box-body">

                  <?php /* Обязательные поля -->
                  <!-----------------------*/ ?>
                  <div class="form-horizontal">
                    <div><b>Mandatory fields</b></div><br>

                    <?php /* 1] Название -->
                    <!-----------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-2 control-label">Tag</div>
                      <div class="col-sm-10">
                        <input class="form-control" placeholder="tagname" data-bind="textInput: m.s4.new.title">
                      </div>
                    </div>

                  </div>

                  <?php /* Кнопка "Создать" -->
                  <!----------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-success" data-bind="click: f.s4.create">Create</button>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <?php /*---------------->
        <!-- 2.13. Настройки  -->
        <!------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 13}">

          Look for settings in config of package M5.

        </div>

        <?php /*--------------------->
        <!-- 2.14. Аутентификация  -->
        <!-----------------------*/ ?>
        <div class="tab-pane" data-bind="css: {active: m.s1.selected_subdoc().id() == 14}">
          <div class="row">

            <?php /*---------------------------------------------------->
            <!-- 2.14.1. Статус аутентификации, восстановление пароля -->
            <!------------------------------------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">

              <?php /*------------------------------->
              <!-- 2.14.1.1. Статус аутентификации -->
              <!---------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Auth status
                </div>
                <div class="box-body form-horizontal">

                  <?php /*------------------------------------------>
                  <!-- 2.14.1.1. Статус "Вы не аутентифицированы" -->
                  <!--------------------------------------------*/ ?>
                  <div data-bind="visible: m.s0.auth.is_anon() == -1">
                    <p>You are not authenticated.</p>
                  </div>

                  <?php /*------------------------------------------------------------------->
                  <!-- 2.14.1.2. Статус "Вы аутентифицированы, как анонимный пользователь" -->
                  <!---------------------------------------------------------------------*/ ?>
                  <div data-bind="visible: m.s0.auth.is_anon() == 1">
                    <p>You are authenticated as an anonymous user (guest).</p>
                  </div>

                  <?php /*--------------------------------------->
                  <!-- 2.14.1.3. Статус "Вы аутентифицированы" -->
                  <!-----------------------------------------*/ ?>
                  <div data-bind="visible: m.s0.auth.is_anon() == 0">

                    <?php /* 1] Сообщение о том, что вы аутентифицированы -->
                    <!--------------------------------------------------*/ ?>
                    <p>You are authenticated.</p>

                    <?php /* 2] Email -->
                    <!--------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-3 control-label">Email</div>
                      <div class="col-sm-9">
                        <input class="form-control input-sm" data-bind="textInput: m.s0.auth.user().email" disabled>
                      </div>
                    </div>

                    <?php /* 3] Phone -->
                    <!--------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-3 control-label">Phone</div>
                      <div class="col-sm-9">
                        <input class="form-control input-sm" data-bind="textInput: m.s0.auth.user().phone" disabled>
                      </div>
                    </div>

                    <?php /* 4] Full name -->
                    <!------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-3 control-label">Full name</div>
                      <div class="col-sm-9">
                        <input class="form-control input-sm" data-bind="textInput: m.s6.fio" disabled>
                      </div>
                    </div>

                    <?php /* 5] ha_provider_name -->
                    <!-------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-3 control-label">Provider</div>
                      <div class="col-sm-9">
                        <input class="form-control input-sm" data-bind="textInput: m.s0.auth.user().ha_provider_name" disabled>
                      </div>
                    </div>

                    <?php /* 6] ha_provider_uid -->
                    <!------------------------*/ ?>
                    <div class="form-group">
                      <div class="col-sm-3 control-label">Provider uid</div>
                      <div class="col-sm-9">
                        <input class="form-control input-sm" data-bind="textInput: m.s0.auth.user().ha_provider_uid" disabled>
                      </div>
                    </div>

                    <?php /* n] Кнопка "Выйти" -->
                    <!-----------------------*/ ?>
                    <div>
                      <button type="button" class="btn btn-block btn-danger" data-bind="click: f.s6.logout">Log out</button>
                    </div>

                  </div>
                </div>
              </div>

              <?php /*------------------------------------------->
              <!-- 2.14.1.2. Восстановление пароля через email -->
              <!---------------------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Password recovery by email
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Email -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-4 control-label">Email</div>
                    <div class="col-sm-8">
                      <input class="form-control input-sm" placeholder="Enter email ..." data-bind="textInput: m.s6.pass_recover_email.email">
                    </div>
                  </div>

                  <?php /* 2] Code -->
                  <!-------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-4 control-label">Code</div>
                    <div class="col-sm-8">
                      <input class="form-control input-sm" placeholder="Enter code ..." data-bind="textInput: m.s6.pass_recover_email.code">
                    </div>
                  </div>

                  <?php /* 3] Password -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-5 control-label">New password</div>
                    <div class="col-sm-7">
                      <input class="form-control input-sm" placeholder="Enter password ..." data-bind="textInput: m.s6.pass_recover_email.password1">
                    </div>
                  </div>

                  <?php /* 4] Password again -->
                  <!-----------------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-5 control-label">New password again</div>
                    <div class="col-sm-7">
                      <input class="form-control input-sm" placeholder="Enter password ..." data-bind="textInput: m.s6.pass_recover_email.password2">
                    </div>
                  </div>

                  <?php /* 5] Текстовая аннотация -->
                  <!----------------------------*/ ?>
                  <div>
                    <p>Enter email and push the button "Get code". Then enter code, password twice, and push "Recover".</p>
                  </div>

                  <?php /* n] Кнопки -->
                  <!---------------*/ ?>
                  <div style="margin-bottom: 15px">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm" data-bind="click: f.s5.pswd_recovery_email_code">
                        <i class="fa fa-rotate-right"></i>
                        <span> &nbsp; Get code</span>
                      </button>
                      <button type="button" class="btn btn-success btn-sm" data-bind="click: f.s5.recover_pswd_email_code">
                        <i class="fa fa-send"></i>
                        <span> &nbsp; Recover</span>
                      </button>
                    </div>
                  </div>

                </div>
              </div>


            </div>

            <?php /*----------------------------------------------->
            <!-- 2.14.2. Различные интерфейсы для аутентификации -->
            <!-------------------------------------------------*/ ?>
            <div class="col-md-6 col-sm-6 col-xs-6">

              <?php /*--------------------------------------------->
              <!-- 2.14.2.1. Аутентификация через email и пароль -->
              <!-----------------------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Auth by email and password
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Email -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Email</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter email ..." data-bind="textInput: m.s6.email_password.email">
                    </div>
                  </div>

                  <?php /* 2] Password -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Password</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter password ..." data-bind="textInput: m.s6.email_password.password">
                    </div>
                  </div>

                  <?php /* n] Кнопка "Аутентифицироваться" -->
                  <!-------------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-default" data-bind="click: f.s6.auth_email_password">Auth</button>
                  </div>

                </div>
              </div>

              <?php /*--------------------------------------------->
              <!-- 2.14.2.2. Аутентификация через phone и пароль -->
              <!-----------------------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Auth by phone and password
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Phone -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Phone</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter phone ..." data-bind="textInput: m.s6.phone_password.phone">
                    </div>
                  </div>

                  <?php /* 2] Password -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Password</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter password ..." data-bind="textInput: m.s6.phone_password.password">
                    </div>
                  </div>

                  <?php /* n] Кнопка "Аутентифицироваться" -->
                  <!-------------------------------------*/ ?>
                  <div>
                    <button type="button" class="btn btn-block btn-default" data-bind="click: f.s6.auth_phone_password">Auth</button>
                  </div>

                </div>
              </div>

              <?php /*------------------------------------>
              <!-- 2.14.2.3. Двухэтапная аутентификация -->
              <!--------------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Auth by email+pass and phone+code
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Email -->
                  <!--------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Email</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter email ..." data-bind="textInput: 'ivan@gmail.com'">
                    </div>
                  </div>

                  <?php /* 2] Password -->
                  <!-----------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Password</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter password ..." data-bind="textInput: 'sdfdfgsd'">
                    </div>
                  </div>

                  <?php /* 3] Code -->
                  <!-------------*/ ?>
                  <div class="form-group">
                    <div class="col-sm-3 control-label">Code</div>
                    <div class="col-sm-9">
                      <input class="form-control input-sm" placeholder="Enter code ..." data-bind="textInput: '1234'">
                    </div>
                  </div>

                  <?php /* 4] Текстовая аннотация -->
                  <!----------------------------*/ ?>
                  <div>
                    <p>Enter email and password, and push the button "Get code", and get a digital code by SMS. Then enter the code to the input "Code", and push the button "Auth".</p>
                  </div>

                  <?php /* n] Кнопка "Аутентифицироваться" -->
                  <!-------------------------------------*/ ?>
                  <div style="margin-bottom: 15px">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm">
                        <i class="fa fa-rotate-right"></i>
                        <span> &nbsp; Get code</span>
                      </button>
                      <button type="button" class="btn btn-success btn-sm">
                        <i class="fa fa-send"></i>
                        <span> &nbsp; Auth</span>
                      </button>
                    </div>
                  </div>

                </div>
              </div>

              <?php /*------------------------------------>
              <!-- 2.14.2.4. Аутентификация через Steam -->
              <!--------------------------------------*/ ?>
              <div class="box">
                <div class="box-header with-border subdoc_title">
                  Sign in throught Steam
                </div>
                <div class="box-body form-horizontal">

                  <?php /* 1] Кнопка "Войти через steam" -->
                  <!-----------------------------------*/ ?>
                  <div>
                    <a class="btn btn-block btn-social btn-github" onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")>
                      <i class="fa fa-steam-square"></i> Sign in with Steam
                    </a>
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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10000/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




