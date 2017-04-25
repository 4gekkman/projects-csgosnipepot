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

  <title>CSGOHAP dashboard skins shop</title>

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
  <link rel="stylesheet" type="text/css" href="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10012/css/c.css?rand={!! mt_rand(1000,9999); !!}">
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
        <span>Управление магазином скинов</span>
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

          <?php /*----------------->
          <!-- 1] Скины на заказ -->
          <!-------------------*/ ?>
          <div class="skins2order" style="display: none" data-bind="visible: m.s1.selected_subdoc().name() == 'skins2order'">

            <?php /*----------------------->
            <!-- А1] Панель инструментов -->
            <!-------------------------*/ ?>
            <div class="tools">

              <?php /*--------------------------------------------->
              <!-- A1.1. Кнопка "Добавить" в правом верхнем углу -->
              <!-----------------------------------------------*/ ?>
              <div style="display: none" class="add-button" data-bind="visible: m.s2.selected_subdoc().name() == 'list', click: f.s2.select_skins2order_subdoc.bind($data, 'add')">
                <span>Добавить скины в магазин</span>
              </div>

              <?php /*----------------------------------------->
              <!-- A1.2. Кнопка "Назад" в левом верхнем углу -->
              <!-------------------------------------------*/ ?>
              <div style="display: none" class="back-button" data-bind="visible: m.s2.selected_subdoc().name() == 'add', click: f.s2.select_skins2order_subdoc.bind($data, 'list')">
                <span>← Назад</span>
              </div>

            </div>

            <?php /*---------------------------------------------->
            <!-- А2] Поддокументы поддокумента "Скины на заказ" -->
            <!------------------------------------------------*/ ?>
            <div class="subdocs">

              <?php /*--------------------------------------->
              <!-- А2.1] Список скинов на заказ в магазине -->
              <!-----------------------------------------*/ ?>
              <div style="display: none" class="list" data-bind="visible: m.s2.selected_subdoc().name() == 'list'">

                <?php /*----------->
                <!-- 2.1] Товары -->
                <!-------------*/ ?>
                <div class="goods">

                  <?php /*--------------------------------->
                  <!-- 2.1.1] Остающиеся "на полке" товары -->
                  <!-----------------------------------*/ ?>
                  <div style="display: none" class="goods-cont" data-bind="foreach: m.s3.items, visible: m.s3.items().length">

                    <?php /*---->
                    <!-- Вещь -->
                    <!------*/ ?>
                    <div class="item" data-bind="click: $root.f.s3.remove_item_from_items2order">

                      <?php /*----------------------------------->
                      <!-- 1) Цветовая индикация качества вещи -->
                      <!-------------------------------------*/ ?>
                      <div class="strip" data-bind="style: {background: $root.f.s0.get_cat_quality_item_color($data)}" style="background: transparent;"></div>

                      <?php /*-------------->
                      <!-- 2) Изображение -->
                      <!----------------*/ ?>
                      <div class="img_cont">
                        <img data-bind="attr: {src: icon_url() + ' 2x'}">
                      </div>

                      <?php /*---------------------->
                      <!-- 3) Стоимость в монетах -->
                      <!------------------------*/ ?>
                      <div class="value_in_coins">
                        <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
                        <span data-bind="text: Math.round(price()*100)"></span>
                      </div>

                      <?php /*----------->
                      <!-- 4) Название -->
                      <!-------------*/ ?>
                      <div class="marketname">
                        <span data-bind="text: market_name"></span>
                      </div>

                      <?php /*-------------------->
                      <!-- 5) Надпись "Удалить" -->
                      <!----------------------*/ ?>
                      <div class="sign delete">
                        <span>Удалить</span>
                      </div>

                    </div>

                  </div>

                  <?php /*--------------------->
                  <!-- 2.2] Если товаров нет -->
                  <!-----------------------*/ ?>
                  <div style="display: none" class="there_is_no_items" data-bind="visible: !m.s3.items().length">
                    <span>Товаров на заказ нет в магазине</span><br>
                  </div>

                </div>

              </div>

              <?php /*-------------------------------------------------------->
              <!-- А2.2] Интерфейс для добавления скинов на заказ в магазин -->
              <!----------------------------------------------------------*/ ?>
              <div style="display: none" class="add" data-bind="visible: m.s2.selected_subdoc().name() == 'add'">

                <?php /*----------->
                <!-- 2.1] Товары -->
                <!-------------*/ ?>
                <div class="goods">

                  <?php /*--------------------------------->
                  <!-- 2.1.1] Остающиеся "на полке" товары -->
                  <!-----------------------------------*/ ?>
                  <div style="display: none" class="goods-cont" data-bind="foreach: m.s4.items, visible: m.s4.items().length">

                    <?php /*---->
                    <!-- Вещь -->
                    <!------*/ ?>
                    <div class="item" data-bind="click: $root.f.s4.add_item_to_items2order">

                      <?php /*-------------->
                      <!-- 1) Изображение -->
                      <!----------------*/ ?>
                      <div class="img_cont">
                        <img data-bind="attr: {src: steammarket_image() + ' 2x'}">
                      </div>

                      <?php /*---------------------->
                      <!-- 2) Стоимость в монетах -->
                      <!------------------------*/ ?>
                      <div class="value_in_coins">
                        <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
                        <span data-bind="text: Math.round(price()*100)"></span>
                      </div>

                      <?php /*----------->
                      <!-- 3) Название -->
                      <!-------------*/ ?>
                      <div class="marketname">
                        <span data-bind="text: name"></span>
                      </div>

                      <?php /*--------------------->
                      <!-- 4) Надпись "Добавить" -->
                      <!-----------------------*/ ?>
                      <div class="sign delete">
                        <span>Добавить</span>
                      </div>

                    </div>

                  </div>

                  <?php /*--------------------->
                  <!-- 2.2] Если товаров нет -->
                  <!-----------------------*/ ?>
                  <div style="display: none" class="there_is_no_items" data-bind="visible: !m.s4.items().length">
                    <span>Товары для добавления в товары на заказ отсутствуют</span><br>
                  </div>

                </div>

              </div>

            </div>

          </div>

          <?php /*------------>
          <!-- 2] Настройки -->
          <!--------------*/ ?>
          <div style="display: none" data-bind="visible: m.s1.selected_subdoc().name() == 'settings'">
            Настройки
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
  <script src="{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/public/D10012/js/j.js?rand={!! mt_rand(1000,9999); !!}"></script>
  <!-- document js: stop -->


@stop




