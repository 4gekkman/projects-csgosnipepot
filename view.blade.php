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
      1.1.2.4. Распределение шансов на выигрыш в текущем раунде (цвета и полоски)
      1.1.2.5. Распределение шансов на выигрыш в текущем раунде (аватарки и текст)
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
      <div class="choose-room" data-bind="if: m.s1.game.choosen_room()">

        <?php /*------------------------------->
        <!-- 2.1] Название выбранной комнаты -->
        <!---------------------------------*/ ?>
        <div class="choosen-name">
          <span data-bind="text: m.s1.game.choosen_room().name() + ' ROOM'"></span>
          <i class="mdi mdi-chevron-down"></i>
        </div>

        <?php /*----------------------------------------->
        <!-- 2.2] Список комнат, которые можно выбрать -->
        <!-------------------------------------------*/ ?>
        <div class="rooms-list" data-bind="foreach: m.s1.game.rooms">

          <?php /*------->
          <!-- Комната -->
          <!---------*/ ?>
          <div class="room" data-bind="click: $root.f.s1.choose_room">

            <?php /*-------->
            <!-- Название -->
            <!----------*/ ?>
            <div class="title">
              <span data-bind="text: name() + ' ROOM'"></span>
            </div>

            <?php /*-------->
            <!-- Описание -->
            <!----------*/ ?>
            <div class="description">
              <span data-bind="html: description"></span>
            </div>

          </div>

        </div>

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
      <div class="gamenum_and_bank">

        <?php /*------------->
        <!-- 1] Номер игры -->
        <!---------------*/ ?>
        <div class="gamenum">
          <span>ИГРА</span>
          <span data-bind="text: '#' + m.s1.game.curprev().current().id()"></span>
        </div>

        <?php /*------->
        <!-- 2] Банк -->
        <!---------*/ ?>
        <div class="bank">
          <span>БАНК:</span>
          <span data-bind="text: m.s1.game.curjackpot()/100"></span>
          <span>руб</span>
        </div>

      </div>

      <?php /*----------------------------------------------------->
      <!-- 1.1.2.2. Счётчики предметов и времени текущего раунда -->
      <!-------------------------------------------------------*/ ?>
      <div class="counters">

        <?php /*----------------------->
        <!-- 1] Счётчик вещей раунда -->
        <!-------------------------*/ ?>
        <div class="counter-items">

          <?php /*------------------->
          <!-- Индикаторная полоса -->
          <!---------------------*/ ?>
          <div class="indicator" data-bind="style: {width: m.s1.bank.indicator_percents()+'%'}">

            <?php /*--------------------------------------->
            <!-- Ножи в правой части индикаторной полосы -->
            <!-----------------------------------------*/ ?>
            <img class="knifes" src="{!! asset('public/D10009/assets/images/knife-gradient.png') !!}">

            <?php /*------------------------------------------>
            <!-- Информация о кол-ве поставленных предметов -->
            <!--------------------------------------------*/ ?>
            <div class="items-inbank-max">
              <span data-bind="text: m.s1.bank.items_sorted().length + ($root.m.s1.game.choosen_room().max_items_per_round() != '0' ? (' / ' + $root.m.s1.game.choosen_room().max_items_per_round()) : '')"></span>
            </div>

          </div>

        </div>

        <?php /*------------------------->
        <!-- 2] Текст между счётчиками -->
        <!---------------------------*/ ?>
        <div class="text-between-counters">
          <span>ИЛИ ЧЕРЕЗ</span>
        </div>

        <?php /*------------------------->
        <!-- 3] Счётчик времени раунда -->
        <!---------------------------*/ ?>
        <div class="counter-time">
          <div>
            <span data-bind="text: m.s1.game.timeleft.minutes"></span>
          </div>
          <div>:</div>
          <div>
            <span data-bind="text: m.s1.game.timeleft.seconds"></span>
          </div>
        </div>

      </div>

      <?php /*------------------------------------------->
      <!-- 1.1.2.3. Органы управления игрой для игрока -->
      <!---------------------------------------------*/ ?>
      <div class="controls">

        <?php /*------------------------------------------------------------>
        <!-- 1] Кол-во внесённых предметов, шанс, кнопка "Внести депозит" -->
        <!--------------------------------------------------------------*/ ?>
        <div class="status_and_button">

          <?php /*------------------------------->
          <!-- 1.1] Кол-во внесённых предметов -->
          <!---------------------------------*/ ?>
          <div class="items_info">
            <span data-bind="text: 'Вы внесли предметов: '+(m.s1.game.wheel.currentuser() ? m.s1.game.wheel.currentuser().itemscount() : '0')"></span>
          </div>

          <?php /*--------------------------------->
          <!-- 1.2] Шанс игрока в текущем раунде -->
          <!-----------------------------------*/ ?>
          <div class="chance">
            <span class="arrows-left"></span>
            <span data-bind="text: 'Ваш шанс: '+(m.s1.game.wheel.currentuser() ? Math.round(m.s1.game.wheel.currentuser().odds()*100*100)/100 : '0')+'%'"></span>
            <span class="arrows-right"></span>
          </div>

          <?php /*---------------------------->
          <!-- 1.3] Кнопка "Внести депозит" -->
          <!------------------------------*/ ?>
          <div class="make-a-bet">
            <div class="button">
              <span>Внести депозит</span>
            </div>
          </div>

        </div>

        <?php /*--------------------------------------------------->
        <!-- 2] Панель с информацией о лимитах выбранной комнаты -->
        <!-----------------------------------------------------*/ ?>
        <div class="info">

          <?php /*------------------------->
          <!-- 2.1] Информация о лимитах -->
          <!---------------------------*/ ?>
          <div class="limits">
            <span data-bind="text: 'Минимальная сумма депозита '+(m.s1.game.choosen_room().min_bet() != 0 ? (m.s1.game.choosen_room().min_bet() + ' руб.') : ' не ограничена.')"></span>
            <span data-bind="text: 'Максимальный депозит '+(m.s1.game.choosen_room().max_items_per_bet() != 0 ? (m.s1.game.choosen_room().max_items_per_bet() + ' предметов.') : ' не ограничен.')"></span>
          </div>

        </div>

      </div>

      <?php /*--------------------------------------------------------------------------->
      <!-- 1.1.2.4. Распределение шансов на выигрыш в текущем раунде (цвета и полоски) -->
      <!-----------------------------------------------------------------------------*/ ?>
      <div class="odds-graphic" data-bind="foreach: m.s1.game.wheel.data">

        <?php /*-------------------------------------->
        <!-- Полоска, графически отображающая шансы -->
        <!----------------------------------------*/ ?>
        <div class="strip" data-bind="style: {background: color, width: Math.round(odds()*100*100)/100+'%'}"></div>

      </div>

      <?php /*---------------------------------------------------------------------------->
      <!-- 1.1.2.5. Распределение шансов на выигрыш в текущем раунде (аватарки и текст) -->
      <!------------------------------------------------------------------------------*/ ?>
      <div class="odds-avatars"  data-bind="foreach: m.s1.game.wheel.data">

        <?php /*---------------------------->
        <!-- Аватарка, цвет игрока, шансы -->
        <!------------------------------*/ ?>
        <div class="ava-color-odds">

          <?php /*----------->
          <!-- 1] Аватарка -->
          <!-------------*/ ?>
          <div class="avatar">
            <img data-bind="attr: {src: avatar}">
          </div>

          <?php /*-------------->
          <!-- 2] Цвет игрока -->
          <!----------------*/ ?>
          <div class="strip" data-bind="style: {background: color}"></div>

          <?php /*-------->
          <!-- 3] Шансы -->
          <!----------*/ ?>
          <div class="odds">
            <span data-bind="text: Math.round(odds()*100*100)/100 + '%'"></span>
          </div>

        </div>

      </div>

      <?php /*---------------------->
      <!-- 1.1.2.6. Ставки раунда -->
      <!------------------------*/ ?>
      <div class="bets" data-bind="foreach: m.s1.game.curprev().current().bets.slice(0).reverse()">

        <?php /*------>
        <!-- Ставка -->
        <!--------*/ ?>
        <div class="bet-container"">

          <?php /*------------------->
          <!-- Информация о ставке -->
          <!---------------------*/ ?>
          <div class="bet-info" data-bind="style: {borderColor: bet_color_hex}">

            <?php /*------>
            <!-- Аватар -->
            <!--------*/ ?>
            <div class="avatar">
              <img data-bind="attr: {src: m5_users()[0].avatar_steam}" />
            </div>

            <?php /*--------------------->
            <!-- Информационный раздел -->
            <!-----------------------*/ ?>
            <div class="info-section">

              <?php /*------------------------->
              <!-- Номер ставки и имя игрока -->
              <!---------------------------*/ ?>
              <div>

                <!-- Номер ставки в раунде-->
                <div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">
                  <span>Билеты от </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>
                  <span> до </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_to()"></span>
                </div>

<!--                <span class="bet-number" data-bind="text: 'Ставка №' + (($root.m.s1.game.curprev().current().bets().length) - ($index() + 1) + 1)" title="Номер ставки в рамках раунда"></span>-->

                <!-- Nickname игрока, сделавшего ставку-->
                <a class="nickname" target="_blank" data-bind="text: m5_users()[0].nickname, attr: {href: 'http://steamcommunity.com/profiles/' + m5_users()[0].ha_provider_uid()}" title="Перейти в профиль игрока в Steam"></a>

              </div>

              <?php /*---------------------------->
              <!-- Сумма, шансы, номера билетов -->
              <!------------------------------*/ ?>
              <div class="sum-odds-tickets">

                <!-- Сумма ставки и шансы выигрыша -->
                <span class="odds" data-bind="text: (Math.round(((+total_bet_amount() / +$root.m.s1.game.curjackpot())) * 10000) / 100) + '%'" title="Шансы на победу ставки"></span>
                <span>/</span>
                <span class="sum" data-bind="text: (Math.round(total_bet_amount())/100) + 'руб.'" title="Сумма ставки"></span>

                <!-- Номера билетов -->
<!--                <div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">-->
<!--                  <span data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>-->
<!--                  <span>-</span>-->
<!--                  <span data-bind="text: m5_users()[0].pivot.tickets_to()"></span>-->
<!--                </div>-->

              </div>

            </div>

          </div>

          <?php /*----------------->
          <!-- Поставленные вещи -->
          <!-------------------*/ ?>
          <div class="bets-items-cont" data-bind="foreach: m8_items()">

            <?php /*----------------->
            <!-- Поставленная вещь -->
            <!-------------------*/ ?>
            <div class="flip">

              <?php /*----------------------------------->
              <!-- Лицевая сторона (изображение, цена) -->
              <!-------------------------------------*/ ?>
              <div class="flip-front">

                <?php /*----------->
                <!-- Изображение -->
                <!-------------*/ ?>
                <img data-bind="attr: {src: steammarket_image}" />

                <?php /*---->
                <!-- Цена -->
                <!------*/ ?>
                <div class="price">
                  <span data-bind="text: '$' + price()"></span>
                </div>

              </div>

              <?php /*--------------------------->
              <!-- Обратная сторона (описание) -->
              <!-----------------------------*/ ?>
              <div class="flip-back">

                <?php /*----------------->
                <!-- Название и ссылка -->
                <!-------------------*/ ?>
                <a class="name-of-item" target="_blank" data-bind="text: name, attr: {href: steammarket_link}"></a>

              </div>

            </div>

          </div>

        </div>        
        
      </div>

      <?php /*---------------------------------------------->
      <!-- 1.1.2.7. Панель с информацией о текущем раунде -->
      <!------------------------------------------------*/ ?>
      <div class="infopanel">

        <?php /*-------------------------->
        <!-- 1] Призыв вносить депозиты -->
        <!----------------------------*/ ?>
        <div class="call4deposits">
          <span>ИГРА НАЧАЛАСЬ! ВНОСИТЕ ДЕПОЗИТЫ!</span>
        </div>

        <?php /*------------------------------------->
        <!-- 2] Кнопка "Честная игра" и хэш раунда -->
        <!---------------------------------------*/ ?>
        <div class="fair-button-and-hash">

          <?php /*-------------------------->
          <!-- 2.1] Кнопка "Честная игра" -->
          <!----------------------------*/ ?>
          <div class="fair-button">
            <i></i>
            <span>ЧЕСТНАЯ ИГРА</span>
          </div>

          <?php /*-------------------------->
          <!-- 2.2] Кнопка "Честная игра" -->
          <!----------------------------*/ ?>
          <div class="hash">
            <span data-bind="text: 'Хэш раунда: '"></span>
            <span data-bind="text: m.s1.game.curprev().current().key_hash"></span>
          </div>

        </div>

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
  <div class="termsofuse">

    <?php /*---------------------------------->
    <!-- 1] Заявление о не аффилированности -->
    <!------------------------------------*/ ?>
    <div class="not-affilated-statement">
      <span>CSGOHAP is NOT affiliated with VALVE corp</span>
    </div>

    <?php /*------------------------>
    <!-- 2] Правила использования -->
    <!--------------------------*/ ?>
    <div class="termslink">
      <a>Terms of service</a>
    </div>

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




