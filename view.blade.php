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

  <title>CS GO рулетка с минимальной ставкой от 1 рубля! Рулетка скинов игры Counter Strike Global Offencive</title>

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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10003/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10009/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  1. Classic game
  2. Double game
  3. Coinflip
  4. Магазин
  5. Профиль пользователя
  6. Партнёрка
  7. ТОП игроков
  8. F.A.Q.
  9. Тех. поддержка
  10. Free coins

-------------------------*/ ?>
@section('content')
<?php /*-------------------------->
<!-- Контентный столбец (860px) -->
<!----------------------------*/ ?> <div class="content-column">


<?php /*--------------->
<!-- 1. Classic game -->
<!--------------------->
Подоглавление:

  1.1. Контейнер классической игры

    1.1.1. Шапка
    1.1.2. Контент классической игры

      1.1.2.1. Номер игры и банк
      1.1.2.2. Счётчики предметов и времени текущего раунда
      1.1.2.3. Кол-во вложенных предметов игрока, его шанс, и кнопка "Вложить"
      1.1.2.4. Распределение шансов на выигрыш в текущем раунда (цвета и полоски)
      1.1.2.5. Распределение шансов на выигрыш в текущем раунда (аватарки и текст)
      1.1.2.6. Ставки раунда
      1.1.2.7. Панель с информацией о текущем раунде

    1.1.3. Контент истории игр

  1.2. Условия использования, предупреждение о не аффилированности с Valve
  1.3. Статистические данные

-------------------*/ ?>
<div class="classic-game" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/'">

  <?php /*-------------------------------->
  <!-- 1.1. Контейнер классической игры -->
  <!----------------------------------*/ ?>
  <div class="cg-container">

    <?php /*------------>
    <!-- 1.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*-------------------------------------------->
      <!-- 1] Логотип классической игры, и её заголовок -->
      <!----------------------------------------------*/ ?>
      <div class="logo_and_name" data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'game'}, click: f.s1.choose_tab.bind($data, 'game')">
        <i class="mdi mdi-crown"></i>
        <span>Classic Game</span>
      </div>

      <?php /*--------------------------->
      <!-- 2] Интерфейс выбора комнаты -->
      <!-----------------------------*/ ?>
      <div class="choose-room">
        <span>MAIN ROOM</span>
        <i class="mdi mdi-chevron-down"></i>
      </div>

      <?php /*------------------->
      <!-- 3] Кнопка "История" -->
      <!---------------------*/ ?>
      <div class="history-button"  data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'history'}, click: f.s1.choose_tab.bind($data, 'history')">
        <span>История</span>
      </div>

    </div>

    <?php /*-------------------------------->
    <!-- 1.1.2. Контент классической игры -->
    <!----------------------------------*/ ?>
    <div style="display: none" data-bind="visible: m.s1.maintabs.choosen().name() == 'game'">

      <?php /*-------------------------->
      <!-- 1.1.2.1. Номер игры и банк -->
      <!----------------------------*/ ?>
      <div>
        Номер игры и банк
      </div>

      <?php /*----------------------------------------------------->
      <!-- 1.1.2.2. Счётчики предметов и времени текущего раунда -->
      <!-------------------------------------------------------*/ ?>
      <div>
        Счётчики предметов и времени текущего раунда
      </div>

      <?php /*------------------------------------------------------------------------>
      <!-- 1.1.2.3. Кол-во вложенных предметов игрока, его шанс, и кнопка "Вложить" -->
      <!--------------------------------------------------------------------------*/ ?>
      <div>
        Кол-во вложенных предметов игрока, его шанс, и кнопка "Вложить"
      </div>

      <?php /*--------------------------------------------------------------------------->
      <!-- 1.1.2.4. Распределение шансов на выигрыш в текущем раунда (цвета и полоски) -->
      <!-----------------------------------------------------------------------------*/ ?>
      <div>
        Распределение шансов на выигрыш в текущем раунда (цвета и полоски)
      </div>

      <?php /*---------------------------------------------------------------------------->
      <!-- 1.1.2.5. Распределение шансов на выигрыш в текущем раунда (аватарки и текст) -->
      <!------------------------------------------------------------------------------*/ ?>
      <div>
        Распределение шансов на выигрыш в текущем раунда (аватарки и текст)
      </div>

      <?php /*---------------------->
      <!-- 1.1.2.6. Ставки раунда -->
      <!------------------------*/ ?>
      <div>
        Ставки раунда
      </div>

      <?php /*---------------------------------------------->
      <!-- 1.1.2.7. Панель с информацией о текущем раунде -->
      <!------------------------------------------------*/ ?>
      <div>
        Панель с информацией о текущем раунде
      </div>

    </div>

    <?php /*-------------------------->
    <!-- 1.1.3. Контент истории игр -->
    <!----------------------------*/ ?>
    <div style="display: none" data-bind="visible: m.s1.maintabs.choosen().name() == 'history'">
      Контент истории игр
    </div>

  </div>

  <?php /*------------------------------------------------------------------------>
  <!-- 1.2. Условия использования, предупреждение о не аффилированности с Valve -->
  <!--------------------------------------------------------------------------*/ ?>
  <div>
    Условия использования, предупреждение о не аффилированности с Valve
  </div>

  <?php /*-------------------------->
  <!-- 1.3. Статистические данные -->
  <!----------------------------*/ ?>
  <div>
    Статистические данные
  </div>

</div>

<?php /*-------------->
<!-- 2. Double game -->
<!----------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/double'">
  <span>Double game</span>
</div>

<?php /*----------->
<!-- 3. Coinflip -->
<!-------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/coinflip'">
  <span>Coinflip</span>
</div>

<?php /*---------->
<!-- 4. Магазин -->
<!------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/shop'">
  <span>Магазин</span>
</div>

<?php /*----------------------->
<!-- 5. Профиль пользователя -->
<!-------------------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/profile'">
  <span>Профиль пользователя</span>
</div>

<?php /*------------>
<!-- 6. Партнёрка -->
<!--------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/ref'">
  <span>Партнёрка</span>
</div>

<?php /*-------------->
<!-- 7. ТОП игроков -->
<!----------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/top'">
  <span>ТОП игроков</span>
</div>

<?php /*--------->
<!-- 8. F.A.Q. -->
<!-----------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/faq'">
  <span>F.A.Q.</span>
</div>

<?php /*----------------->
<!-- 9. Тех. поддержка -->
<!-------------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/support'">
  <span>Тех. поддержка</span>
</div>

<?php /*-------------->
<!-- 10. Free coins -->
<!----------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/freecoins'">
  <span>Free coins</span>
</div>



</div> @stop



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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/L10003/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10009/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




