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
        Боты и задачи
      </td>

      <?php /*-------------------->
      <!-- A2. Группы и фильтры -->
      <!----------------------*/ ?>
      <td class="right-part">

        <?php /*------------------------------------->
        <!-- A2.1. Интерфейс создания новой группы -->
        <!---------------------------------------*/ ?>
        <div class="newgroup-cont">

          <?php /*--------------------------------------------------->
          <!-- A2.1.1. Кнопка "Новая группа" в правом верхнем углу -->
          <!-----------------------------------------------------*/ ?>
          <div style="display: none" class="new-group-button-cont" data-bind="visible: !$root.m.s1.groups.new.ison()">
            <div class="new-group-button">
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
                <i class="mdi mdi-rename-box rename" title="Переименовать" data-bind="click: $root.f.s1.turnon_rename_group_mode"></i>
                <i class="mdi mdi-delete-empty delete" title="Удалить"></i>
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
        <div class="filters">

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




