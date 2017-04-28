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

  <title>D10013 - Activity System</title>

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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10013/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление





-------------------------*/ ?>
@section('content')
<?php /*-------------------------->
<!-- Контентный столбец (860px) -->
<!----------------------------*/ ?> <div class="content-column">
<div class="standard-admin-panel">

  <?php /*------------>
  <!-- 1. Контейнер -->
  <!--------------*/ ?>
  <div class="container-block">

    <?php /*---------->
    <!-- 1.1. Шапка -->
    <!------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-shopping"></i>
        <span>Система активности</span>
        <input type="file" id="inputurl" style="display: none; position: absolute; top: 15px; right: 350px; line-height: 10px;" data-bind="visible: $root.m.s2.chava.winner() && $root.m.s2.choosen().id(), event: { change: function() { $root.m.s2.chava.inputurl($element.files[0]); } }">
      </div>

    </div>

    <?php /*------------>
    <!-- 1.2. Контент -->
    <!--------------*/ ?>
    <div class="content-block">

      <?php /*-------------------------->
      <!-- 1.2.1. Таблица с контентом -->
      <!----------------------------*/ ?>
      <table class="content-table"><tbody><tr>

        <?php /*---------------------->
        <!-- 1.2.1.1. Левый столбец -->
        <!------------------------*/ ?>
        <td class="left">

          <?php /*------------->
          <!-- 1] Победители -->
          <!---------------*/ ?>
          <div class="winners" style="display: none" data-bind="visible: m.s1.selected_subdoc().name() == 'winners'">

            <?php /*--------->
            <!-- Контейнер -->
            <!-----------*/ ?>
            <div class="winner-cont" data-bind="foreach: m.s2.winners">

              <?php /*---------->
              <!-- Победитель -->
              <!------------*/ ?>
              <div class="winner" data-bind="click: $root.f.s2.choose_winner">

                <?php /*------>
                <!-- Аватар -->
                <!--------*/ ?>
                <img class="winner-avatar" data-bind="attr: {id: 'image_of_winner_'+id(), src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+id()+'.jpg' + '?as=' + avatar_steam().slice(-20) + '&ua=' + updated_at().replace(/[ :-]/g,'')}">

                <?php /*--->
                <!-- Ник -->
                <!-----*/ ?>
                <div class="winner-nickname">
                  <span style="display: none" data-bind="visible: !$root.m.s2.rename.winner() || id() != $root.m.s2.choosen().id(), text: nickname"></span>
                  <input style="display: none" type="text" data-bind="visible: $root.m.s2.rename.winner() && id() == $root.m.s2.choosen().id(), textInput: $root.m.s2.rename.input, attr: {id: 'rename_input_of_db_winner_'+id()}">
                </div>

                <?php /*----------->
                <!-- Блок кнопок -->
                <!-------------*/ ?>
                <div class="winner-btn-blck">

                  <?php /*----------------------------------------------->
                  <!-- Кнопки, относящиеся к переименованию победителя -->
                  <!-------------------------------------------------*/ ?>
                  <div style="display: none" data-bind="visible: !$root.m.s2.chava.winner()">
                    <i style="display: none" class="mdi mdi-check apply" title="Подтвердить переименование" data-bind="visible: $root.m.s2.rename.winner() && id() == $root.m.s2.choosen().id(), click: $root.f.s2.apply_win_rename"></i>
                    <i style="display: none" class="mdi mdi-close cancel" title="Отменить переименование" data-bind="visible: $root.m.s2.rename.winner() && id() == $root.m.s2.choosen().id(), click: $root.f.s2.cancel_win_rename"></i>
                    <i style="display: none" class="mdi mdi-rename-box rename" title="Переименовать" data-bind="visible: !$root.m.s2.rename.winner(), click: $root.f.s2.turnon_rename_win_mode"></i>
                  </div>

                  <?php /*-------------------------------------------------->
                  <!-- Кнопки, относящиеся к изменению аватара победителя -->
                  <!----------------------------------------------------*/ ?>
                  <div style="display: none" data-bind="visible: !$root.m.s2.rename.winner()">
                    <i style="display: none" class="mdi mdi-check apply" title="Подтвердить изменение аватара" data-bind="visible: $root.m.s2.chava.winner() && id() == $root.m.s2.choosen().id(), click: $root.f.s2.saveimage"></i>
                    <i style="display: none" class="mdi mdi-close cancel" title="Отменить изменение аватара" data-bind="visible: $root.m.s2.chava.winner() && id() == $root.m.s2.choosen().id(), click: $root.f.s2.saveimage_cancel"></i>
                    <i style="display: none" class="mdi mdi-file-image rename" title="Изменить аватар" data-bind="visible: !$root.m.s2.chava.winner(), click: $root.f.s2.turnon_chava_win_mode"></i>
                  </div>

                </div>

              </div>

            </div>

          </div>

          <?php /*----------->
          <!-- 2] Победить -->
          <!-------------*/ ?>
          <div class="win" style="display: none" data-bind="visible: m.s1.selected_subdoc().name() == 'win'">

            <?php /*--------->
            <!-- Контейнер -->
            <!-----------*/ ?>
            <div class="win-cont" data-bind="foreach: m.s3.rooms">

              <?php /*------->
              <!-- Комната -->
              <!---------*/ ?>
              <div class="room">

                <?php /*---------------->
                <!-- Название комнаты -->
                <!------------------*/ ?>
                <div class="win-room-name">
                  <span data-bind="text: name"></span>
                </div>

                <?php /*--------------------------------------->
                <!-- Поле для ввода номера билета победителя -->
                <!-----------------------------------------*/ ?>
                <div class="win-steamid">
                  <input type="text" data-bind="textInput: ticket2win" placeholder="Номер билета победителя">
                </div>

                <?php /*----------------->
                <!-- Кнопка "Победить" -->
                <!-------------------*/ ?>
                <div class="win-button">
                  <button data-bind="click: $root.f.s3.let_him_win">Пусть он победит</button>
                </div>

              </div>

            </div>

          </div>

        </td>

        <?php /*----------------------->
        <!-- 1.2.1.2. Правый столбец -->
        <!-------------------------*/ ?>
        <td class="right" data-bind="foreach: m.s1.subdocs">

          <?php /*----------->
          <!-- Поддокумент -->
          <!-------------*/ ?>
          <div class="menu-item" data-bind="css: {choosen: $data == $root.m.s1.selected_subdoc()}, click: $root.f.s1.select_subdoc">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <span data-bind="text: title"></span>

          </div>

        </td>

      </tr></tbody></table>

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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10013/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




