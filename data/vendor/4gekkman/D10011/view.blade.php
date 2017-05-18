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

  <title>CSGOHAP dashboard botnet</title>

@stop



<?php /*----------------->
<!-- 2. CSS документа  -->
<!-------------------*/ ?>
@section('css')

  <!-- document css: start -->
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-animsition/animsition/dist/css/animsition.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/toastr/toastr.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-cssgrids/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/tooltipster/dist/css/tooltipster.bundle.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/beemuse/dist/beemuse.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/mdi/css/materialdesignicons.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/css/perfect-scrollbar.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/bootstrap/dist/css/bootstrap.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/loaders.css/loaders.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/ionicons/css/ionicons.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/Font-Awesome/css/font-awesome.min.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10004/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10011/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  1. Контейнер ЕСУБ
    1.1. Шапка
    1.2. Контент ЕСУБ

      1.2.1. Главный интерфейс документа


-------------------------*/ ?>
@section('content')
<?php /*-------------------------->
<!-- Контентный столбец (860px) -->
<!----------------------------*/ ?> <div class="content-column">
<div class="botnet">

  <?php /*----------------->
  <!-- 1. Контейнер ЕСУБ -->
  <!-------------------*/ ?>
  <div class="botnet-container">

    <?php /*---------->
    <!-- 1.1. Шапка -->
    <!------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-robot"></i>
        <span>Единая система управления ботами</span>
      </div>

    </div>

    <?php /*------------------------------>
    <!-- A. Главный интерфейс документа -->
    <!--------------------------------*/ ?>
    <div class="botnet-content"><table class="botnet-table"><tbody><tr>

      <?php /*----------------->
      <!-- A1. Боты и задачи -->
      <!-------------------*/ ?>
      <td class="left-part">

        <?php /*------------------>
        <!-- A1.1. Список ботов -->
        <!--------------------*/ ?>
        <div style="display: none" class="bots-list" data-bind="visible: !m.s2.choosen_bot()"><div data-bind="foreach: m.s2.bots_filtered">

          <?php /*--->
          <!-- Бот -->
          <!-----*/ ?>
          <div class="bots-bot" data-bind="click: $root.f.s1.choose_bot">

            <?php /*------------->
            <!-- Описание бота -->
            <!---------------*/ ?>
            <span data-bind="text: description"></span>

            <?php /*--------------------------------->
            <!-- Login и steam ID бота (если есть) -->
            <!-----------------------------------*/ ?>
            <div class="bot-login-steamid" style="display: none" data-bind="visible: (login() && steamid())">
              <span data-bind="text: login() + ' (' + steamid() + ')'"></span>
            </div>

          </div>

        </div></div>

        <?php /*------------------------------>
        <!-- A1.2. Свойства выбранного бота -->
        <!--------------------------------*/ ?>
        <div style="display: none" class="bots-props" data-bind="visible: m.s2.choosen_bot">

          <?php /*----------------------->
          <!-- А1] Панель инструментов -->
          <!-------------------------*/ ?>
          <div class="tools">

            <?php /*----------------------------------------->
            <!-- A1.1. Кнопка "Назад" в левом верхнем углу -->
            <!-------------------------------------------*/ ?>
            <div class="btn-common back-button" data-bind="click: f.s1.unchoose_bot">
              <span>← Back</span>
            </div>

          </div>

          <?php /*----------------------------------------------->
          <!-- А2] Редактирование safe-свойств выбранного бота -->
          <!-------------------------------------------------*/ ?>
          <div class="botedit form-horizontal" style="padding-bottom: 30px;" data-bind="if: m.s2.choosen_bot">

            <?php /* Заголовок и кнопка save -->
            <!-----------------------------*/ ?>
            <div class="header-note">

              <?php /*------------->
              <!-- Кнопка "Save" -->
              <!---------------*/ ?>
              <span>Safe bot properties</span>

              <?php /*------------->
              <!-- Кнопка "Save" -->
              <!---------------*/ ?>
              <div class="btn-common save-button" data-bind="click: f.s1.edit_bot_safe.bind($data, function(){})">
                <span>Update safe bot properties</span>
              </div>

            </div>

            <?php /* 1] Login -->
            <!--------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Login</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.login">
              </div>
            </div>

            <?php /* 2] Steamid -->
            <!----------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Steam ID</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.steamid">
              </div>
            </div>

            <?php /* 3] API domain -->
            <!-------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">API domain</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.apikey_domain">
              </div>
            </div>

            <?php /* 4] API key -->
            <!----------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">API key</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.apikey">
              </div>
            </div>

            <?php /* 5] Trade URL -->
            <!------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Trade URL</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.trade_url">
              </div>
            </div>

            <?php /* 6] Description -->
            <!--------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Description</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_safe.description">
              </div>
            </div>

            <?php /* Заголовок -->
            <!---------------*/ ?>
            <div class="header-note">

              <?php /*------------->
              <!-- Кнопка "Save" -->
              <!---------------*/ ?>
              <span>Unsafe bot properties</span>

              <?php /*------------->
              <!-- Кнопка "Save" -->
              <!---------------*/ ?>
              <div class="btn-common save-button-unsafe" data-bind="click: f.s1.edit_bot_unsafe.bind($data, function(){})">
                <span>Update unsafe bot properties</span>
              </div>

            </div>

            <?php /* 1] Password -->
            <!-----------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Password</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.password">
              </div>
            </div>

            <?php /* 2] Session ID -->
            <!--------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Session ID</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.sessionid">
              </div>
            </div>

            <?php /* 3] Shared secret -->
            <!----------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Shared secret</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.shared_secret">
              </div>
            </div>

            <?php /* 4] Serial number -->
            <!----------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Serial number</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.serial_number">
              </div>
            </div>

            <?php /* 5] Revocation code -->
            <!------------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Revocation code</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.revocation_code">
              </div>
            </div>

            <?php /* 6] URI -->
            <!------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">URI</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.uri">
              </div>
            </div>

            <?php /* 7] Server time -->
            <!--------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Server time</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.server_time">
              </div>
            </div>

            <?php /* 8] Account name -->
            <!---------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Account name</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.account_name">
              </div>
            </div>

            <?php /* 9] Token gid -->
            <!------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Token gid</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.token_gid">
              </div>
            </div>

            <?php /* 10] identity_secret -->
            <!-------------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Identity secret</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.identity_secret">
              </div>
            </div>

            <?php /* 11] Secret 1 -->
            <!------------------*/ ?>
            <div style="display: none" class="form-group">
              <div class="col-sm-3 control-label">Secret 1</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.secret_1">
              </div>
            </div>

            <?php /* 12] Device ID -->
            <!-------------------*/ ?>
            <div class="form-group">
              <div class="col-sm-3 control-label">Device ID</div>
              <div class="col-sm-9">
                <input class="form-control input-sm" data-bind="textInput: m.s2.edit_unsafe.device_id">
              </div>
            </div>

          </div>

        </div>

      </td>

      <?php /*-------------------->
      <!-- A2. Группы и фильтры -->
      <!----------------------*/ ?>
      <td class="right-part">

        <?php /*------------------------------------->
        <!-- A2.1. Интерфейс создания новой группы -->
        <!---------------------------------------*/ ?>
        <div style="display: none" class="newgroup-cont">

          <?php /*--------------------------------------------------->
          <!-- A2.1.1. Кнопка "Новая группа" в правом верхнем углу -->
          <!-----------------------------------------------------*/ ?>
          <div style="display: none" class="new-group-button-cont" data-bind="visible: !$root.m.s1.groups.new.ison()">
            <div class="new-group-button" data-bind="click: f.s1.create_new_group">
              <span>Новая группа</span>
            </div>
          </div>

          <?php /*--------------------------------------->
          <!-- A2.1.2. Интерфейс создания новой группы -->
          <!-----------------------------------------*/ ?>
          <div style="display: none" data-bind="visible: $root.m.s1.groups.new.ison">

            Интерфейс создания новой группы

          </div>

        </div>

        <?php /*------------------------->
        <!-- A2.2. Перманентные группы -->
        <!---------------------------*/ ?>
        <div class="pg-cont">

          <?php /*--------------------------------->
          <!-- A2.2.1. Список перманентных групп -->
          <!-----------------------------------*/ ?>
          <div class="pg-list" data-bind="foreach: m.s1.groups.permanent.list">

            <?php /*------------------->
            <!-- Перманентная группа -->
            <!---------------------*/ ?>
            <div class="permanent-group" data-bind="click: $root.f.s1.choose_group, css: {choosen: $root.m.s1.groups.choosen().permanent && $root.m.s1.groups.choosen().permanent() && $data.id() == $root.m.s1.groups.choosen().id()}">
              <span data-bind="text: name"></span>
            </div>

          </div>

        </div>

        <?php /*--------------------------->
        <!-- A2.3. Группы из базы данных -->
        <!-----------------------------*/ ?>
        <div class="bd-list" data-bind="foreach: m.s1.groups.variable.list">

          <?php /*--------------------->
          <!-- Группа из базы данных -->
          <!-----------------------*/ ?>
          <div class="bd-group" data-bind="click: $root.f.s1.choose_group, css: {choosen: !$root.m.s1.groups.choosen().permanent && $data.id() == $root.m.s1.groups.choosen().id()}">

            <?php /*------------------------------>
            <!-- 1] Содержимое в обычном режиме -->
            <!--------------------------------*/ ?>
            <div style="display: none" data-bind="visible: !$root.m.s1.groups.rename.group() || $root.m.s1.groups.rename.group().id() != id()">

              <?php /*---------->
              <!-- Имя группы -->
              <!------------*/ ?>
              <div class="group-name">
                <span data-bind="text: name"></span>
              </div>

              <?php /*--------------->
              <!-- Блок управления -->
              <!-----------------*/ ?>
              <div class="controls">
                <i class="mdi mdi-rename-box rename" title="Переименовать" data-bind="click: $root.f.s1.turnon_rename_group_mode, clickBubble: false"></i>
                <i class="mdi mdi-delete-empty delete" title="Удалить" data-bind="click: $root.f.s1.delete_group, clickBubble: false"></i>
              </div>

            </div>

            <?php /*------------------------------------->
            <!-- 2] Содержимое в режиме переименования -->
            <!---------------------------------------*/ ?>
            <div style="display: none" data-bind="visible: $root.m.s1.groups.rename.group() && $root.m.s1.groups.rename.group().id() == id()">

              <?php /*------------------------------->
              <!-- Input для переименования группы -->
              <!---------------------------------*/ ?>
              <input type="text" data-bind="textInput: $root.m.s1.groups.rename.input, attr: {id: 'rename_input_of_db_group_'+id()}">

              <?php /*--------------->
              <!-- Блок управления -->
              <!-----------------*/ ?>
              <div class="controls renamemode">
                <i class="mdi mdi-check apply" title="Подтвердить переименование" data-bind="click: $root.f.s1.apply_group_rename"></i>
                <i class="mdi mdi-close cancel" title="Отменить переименование" data-bind="click: $root.f.s1.cancel_group_rename"></i>
              </div>

            </div>

          </div>

        </div>

        <?php /*------------->
        <!-- A2.4. Фильтры -->
        <!---------------*/ ?>
        <div style="display: none" class="filters">

          <?php /*------------------------->
          <!-- A2.4.1. Фильтры для групп -->
          <!---------------------------*/ ?>
          <div class="bot-filters" data-bind="foreach: m.s1.filters.list">
            <label>
              <input type="checkbox" data-bind="checked: value">
              <span data-bind="text: name"></span>
            </label>
          </div>

        </div>


      </td>

    </tr></tbody></table></div>

  </div>

</div>
</div>@stop



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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/jquery/jquery.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-jslib1/library.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-animsition/animsition/dist/js/animsition.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/toastr/toastr.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/smooth-scroll.js/dist/js/smooth-scroll.min.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/tooltipster/dist/js/tooltipster.bundle.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/momentjs/moment.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/perfect-scrollbar/js/perfect-scrollbar.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/d3/d3.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/bootstrap/dist/js/bootstrap.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/socket.io-client/dist/socket.io.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/history.js/scripts/bundled/html4+html5/native.history.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/knockoutjs/dist/knockout.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/moment/moment.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/knockout-mapping/knockout.mapping.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10004/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10011/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




