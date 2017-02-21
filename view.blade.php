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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-cssgrids/c.css?rand={!! mt_rand(1000,9999); !!}">
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10009/css/c.css?rand={!! mt_rand(1000,9999); !!}">
  <!-- document css: stop -->

@stop


<?php /*--------------------->
<!-- 3. Контент документа  -->
<!--------------------------->
Оглавление

  A. Зона уведомлений

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


<?php /*------------------->
<!-- A. Зона уведомлений -->
<!------------------------->
Подоглавление:

  A1. Уведомление для тех, кто не ввёл свой Steam Trade URL

-------------------*/ ?>
<div class="notifications-zone">

  <?php /*--------------------------------------------------------->
  <!-- A1. Уведомление для тех, кто не ввёл свой Steam Trade URL -->
  <!-----------------------------------------------------------*/ ?>
  <div class="notification tradeurl" data-bind="visible: (layoutmodel.m.s1.selected_subdoc().uri() == '/' && m.s0.auth.is_anon() == 0 && !m.s2.notif_tradeurl.tradeurl_server())">

    <?php /*------------>
    <!-- 1] Заголовок -->
    <!--------------*/ ?>
    <div class="head">
      <span>Ссылка на обмен в Steam:</span>
    </div>

    <?php /*----------------->
    <!-- 2] Input и кнопка -->
    <!-------------------*/ ?>
    <div class="input_and_button">

      <?php /*---------->
      <!-- 2.1] Input -->
      <!------------*/ ?>
      <input type="text" placeholder="Введите ссылку на обмен" data-bind="textInput: m.s2.notif_tradeurl.tradeurl">

      <?php /*----------->
      <!-- 2.2] Кнопка -->
      <!-------------*/ ?>
      <button data-bind="click: f.s2.save_steam_tradeurl">Сохранить</button>

    </div>

    <?php /*------------>
    <!-- 3] Подсказка -->
    <!--------------*/ ?>
    <div class="hint">
      <a target="_blank" href="http://steamcommunity.com/my/tradeoffers/privacy#trade_offer_access_url">Нажмите здесь</a>
      <span> для получения ссылки на обмен</span>
    </div>

  </div>

</div>


<?php /*--------------->
<!-- 1. Classic game -->
<!-----------------*/ ?>
@include('D10009::classicgame')

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
@include('D10009::profile')

<?php /*------------>
<!-- 6. Партнёрка -->
<!--------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/ref'">
  <span>Партнёрка</span>
</div>

<?php /*-------------->
<!-- 7. ТОП игроков -->
<!----------------*/ ?>
@include('D10009::top')

<?php /*--------->
<!-- 8. F.A.Q. -->
<!-----------*/ ?>
@include('D10009::faq')

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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/bower/4gekkman-bower-jslib1/library.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10009/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




