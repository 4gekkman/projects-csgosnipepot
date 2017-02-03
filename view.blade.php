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
<!--------------------->
Подоглавление:

  1.1. Контейнер классической игры

    1.1.1. Шапка
    1.1.2. Контент классической игры

      1.1.2.1. Номер игры и банк
      1.1.2.2. Панель интерфейса игрового процесса

        1.1.2.2.1. Счётчики предметов и времени текущего раунда
        1.1.2.2.2. Органы управления игрой для игрока
        1.1.2.2.3. Бегущая полоса с аватарками
        1.1.2.2.4. Инфо-панель для состояний "Lottery" и "Winner"

      1.1.2.3. Распределение шансов на выигрыш в текущем раунде (цвета и полоски)
      1.1.2.4. Распределение шансов на выигрыш в текущем раунде (аватарки и текст)
      1.1.2.5. Ставки раунда
      1.1.2.6. Панель с информацией о текущем раунде

    1.1.3. Контент истории игр

  1.2. Условия использования, предупреждение о не аффилированности с Valve
  1.3. Статистические данные

    1.3.1. Последний победитель
    1.3.2. Счастливчик дня
    1.3.3. Наибольшая ставка

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
    <div style="display: none" data-bind="if: m.s1.game.curprev().current, visible: m.s1.maintabs.choosen().name() == 'game'">

      <?php /*-------------------------->
      <!-- 1.1.2.1. Номер игры и банк -->
      <!----------------------------*/ ?>
      <div class="gamenum_and_bank" data-bind="if: m.s1.game.curprev().current">

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
          <span data-bind="text: Math.ceil((m.s1.game.curjackpot()/100)*server.data.usdrub_rate)"></span>
          <span>руб</span>
        </div>

      </div>

      <?php /*-------------------------------------------->
      <!-- 1.1.2.2. Панель интерфейса игрового процесса -->
      <!----------------------------------------------*/ ?>
      <div class="gameprocess">

        <?php /*------------------------------------------------------->
        <!-- 1.1.2.2.1. Счётчики предметов и времени текущего раунда -->
        <!---------------------------------------------------------*/ ?>
        <div class="counters" data-bind="css: {lottery: (['Lottery'].indexOf(m.s1.game.choosen_status()) != -1), winner: (['Winner'].indexOf(m.s1.game.choosen_status()) != -1)}">

          <?php /*----------------------->
          <!-- 1] Счётчик вещей раунда -->
          <!-------------------------*/ ?>
          <div class="counter-items">

            <?php /*------------------->
            <!-- Индикаторная полоса -->
            <!---------------------*/ ?>
            <div class="indicator" data-bind="if: $root.m.s1.game.choosen_room, style: {width: m.s1.bank.indicator_percents()+'%'}">

              <?php /*--------------------------------------->
              <!-- Ножи в правой части индикаторной полосы -->
              <!-----------------------------------------*/ ?>
              <img class="knifes" src="{!! asset('public/D10009/assets/images/knife-gradient.png') !!}">

              <?php /*------------------------------------------>
              <!-- Информация о кол-ве поставленных предметов -->
              <!--------------------------------------------*/ ?>
              <div class="items-inbank-max">
                <span data-bind="text: m.s1.bank.items_sorted().length + ($root.m.s1.game.choosen_room().max_items_per_round() != '0' ? ('/' + $root.m.s1.game.choosen_room().max_items_per_round()) : '')"></span>
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
              <span data-bind="text: m.s1.game.counters.lottery.minutes"></span>
            </div>
            <div>:</div>
            <div>
              <span data-bind="text: m.s1.game.counters.lottery.seconds"></span>
            </div>
          </div>

        </div>

        <?php /*--------------------------------------------->
        <!-- 1.1.2.2.2. Органы управления игрой для игрока -->
        <!-----------------------------------------------*/ ?>
        <div class="controls" data-bind="css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) != -1)}">

          <?php /*------------------------------------------------------------>
          <!-- 1] Кол-во внесённых предметов, шанс, кнопка "Внести депозит" -->
          <!--------------------------------------------------------------*/ ?>
          <div class="status_and_button">

            <?php /*------------------------------->
            <!-- 1.1] Кол-во внесённых предметов -->
            <!---------------------------------*/ ?>
            <div class="items_info">
              <span data-bind="text: m.s1.bank.itemsnum_human"></span>
            </div>

            <?php /*--------------------------------->
            <!-- 1.2] Шанс игрока в текущем раунде -->
            <!-----------------------------------*/ ?>
            <div class="chance">
              <span class="arrows-left"></span>
              <span data-bind="text: 'Ваш шанс: '+m.s1.bank.bets()+'%'"></span>
              <span class="arrows-right"></span>
            </div>

            <?php /*---------------------------->
            <!-- 1.3] Кнопка "Внести депозит" -->
            <!------------------------------*/ ?>
            <div class="make-a-bet">
              <div class="button" data-bind="click: f.s1.onclick_handler">
                <span>Внести депозит</span>
              </div>
            </div>

          </div>

          <?php /*--------------------------------------------------->
          <!-- 2] Панель с информацией о лимитах выбранной комнаты -->
          <!-----------------------------------------------------*/ ?>
          <div class="info" data-bind="if: m.s1.game.choosen_room">

            <?php /*------------------------->
            <!-- 2.1] Информация о лимитах -->
            <!---------------------------*/ ?>
            <div class="limits">
              <span data-bind="text: 'Минимальная сумма депозита '+(m.s1.game.choosen_room().min_bet() != 0 ? (Math.ceil((Math.round(m.s1.game.choosen_room().min_bet())/100)*server.data.usdrub_rate)  + ' руб.') : ' не ограничена.')"></span>
              <span data-bind="text: 'Максимальный депозит '+(m.s1.game.choosen_room().max_items_per_bet() != 0 ? (m.s1.game.choosen_room().max_items_per_bet() + ' предметов.') : ' не ограничен.')"></span>
            </div>

          </div>

        </div>

        <?php /*-------------------------------------->
        <!-- 1.1.2.2.3. Бегущая полоса с аватарками -->
        <!----------------------------------------*/ ?>
        <div class="strip-avatars" data-bind="if: m.s1.game.choosen_room, css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) == -1), downpanel: (['Started', 'Pending'].indexOf(m.s1.game.choosen_status()) != -1)}">

          <?php /*------------------------------------------------------------->
          <!-- Непосредственно контейнер (кот.движется) для полоски аватарок -->
          <!---------------------------------------------------------------*/ ?>
          <div class="moving_cont" data-bind="foreach: m.s1.game.strip.avatars, style: {width: m.s1.game.strip.width()+'px', transform: 'translate3d('+m.s1.game.strip.currentpos()+'px, 0px, 0px)'}"> <!--, transform: 'translate3d('+(-m.s1.game.strip.currentpos())+'px, 0px, 0px)', transitionDuration: m.s1.game.strip.duration, transitionTimingFunction: m.s1.game.bezier.cssvalue}">-->

            <?php /*--------------------->
            <!-- Аватарка в контейнере -->
            <!-----------------------*/ ?>
            <img data-bind="attr: {src: $data}">

          </div>

        </div>

        <?php /*--------------------------------------------------------->
        <!-- 1.1.2.2.4. Инфо-панель для состояний "Lottery" и "Winner" -->
        <!-----------------------------------------------------------*/ ?>
        <div class="lw-panel" data-bind="css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) == -1)}">

          <?php /*--------------------------------------->
          <!-- 1] Стрелочка, указывающая на победителя -->
          <!-----------------------------------------*/ ?>
          <img class="arrow" src="{!! asset('public/D10009/assets/images/roulette-arrow.png') !!}">

          <?php /*---------------------------------------------->
          <!-- 2] Победный билет, победил игрок, число раунда -->
          <!------------------------------------------------*/ ?>
          <div class="textinfo">

            <?php /*------------------->
            <!-- 2.1] Победный билет -->
            <!---------------------*/ ?>
            <div class="ticket">
              <span>Победный билет: </span>
              <span data-bind="text: m.s1.game.lwpanel.ticket()"></span>
            </div>

            <?php /*------------------->
            <!-- 2.2] Победил игрок -->
            <!---------------------*/ ?>
            <div class="winner">
              <span>Победил игрок: </span>
              <span data-bind="text: m.s1.game.lwpanel.winner"></span>
            </div>

            <?php /*----------------->
            <!-- 2.3] Число раунда -->
            <!-------------------*/ ?>
            <div class="number">
              <span>Число раунда: </span>
              <span data-bind="text: m.s1.game.lwpanel.number"></span>
            </div>

          </div>

          <?php /*-------------------->
          <!-- 3] Текущий банк игры -->
          <!----------------------*/ ?>
          <div class="bank">
            <span data-bind="text: Math.ceil((m.s1.game.curjackpot()/100)*server.data.usdrub_rate) + ' руб.'"></span>
          </div>

          <?php /*---------------------------------------------->
          <!-- 4] Счётчик времени до начала следующего раунда -->
          <!------------------------------------------------*/ ?>
          <div class="counter-time">

            <?php /*-------------------------->
            <!-- Надпись "новая игра через" -->
            <!----------------------------*/ ?>
            <div class="counterdesc">
              <span>Новая игра через: </span>
            </div>

            <?php /*----------->
            <!-- Сам счётчик -->
            <!-------------*/ ?>
            <div class="counter">

              <!--<div>-->
              <!--  <span data-bind="text: m.s1.game.counters.newgame.minutes"></span>-->
              <!--</div>-->
              <!--<div>:</div>-->
              <div>
                <span data-bind="text: m.s1.game.counters.newgame.seconds"></span>
              </div>

            </div>

          </div>

          <?php /*--------------------------------->
          <!-- 5] Кнопка "Внести депозит первым" -->
          <!-----------------------------------*/ ?>
          <div class="make-a-bet-first">
            <div class="button" data-bind="click: f.s1.onclick_handler">
              <span>Внести депозит первым</span>
            </div>
          </div>

        </div>

      </div>

      <?php /*--------------------------------------------------------------------------->
      <!-- 1.1.2.3. Распределение шансов на выигрыш в текущем раунде (цвета и полоски) -->
      <!-----------------------------------------------------------------------------*/ ?>
      <div class="odds-graphic" style="display: none" data-bind="foreach: m.s1.game.wheel.data, visible: (m.s1.game.curprev().current() && m.s1.game.curprev().current().bets().length)">

        <?php /*-------------------------------------->
        <!-- Полоска, графически отображающая шансы -->
        <!----------------------------------------*/ ?>
        <div class="strip" data-bind="style: {background: color, width: Math.round(odds()*100*100)/100+'%'}"></div>

      </div>

      <?php /*---------------------------------------------------------------------------->
      <!-- 1.1.2.4. Распределение шансов на выигрыш в текущем раунде (аватарки и текст) -->
      <!------------------------------------------------------------------------------*/ ?>
      <div class="odds-avatars" style="display: none" data-bind="foreach: m.s1.game.wheel.data, visible: (m.s1.game.curprev().current() && m.s1.game.curprev().current().bets().length)">

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
            <span data-bind="text: Math.round(odds()*100*10)/10 + '%'"></span>
          </div>

        </div>

      </div>

      <?php /*---------------------->
      <!-- 1.1.2.5. Ставки раунда -->
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

              <?php /*---------------------------------->
              <!-- Имя игрока и номера билетов ставки -->
              <!------------------------------------*/ ?>
              <div>

                <!-- Номер ставки в раунде -->
                <div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">
                  <span>Билеты от </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>
                  <span> до </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_to()"></span>
                </div>
                <!--<span class="bet-number" data-bind="text: 'Ставка №' + (($root.m.s1.game.curprev().current().bets().length) - ($index() + 1) + 1)" title="Номер ставки в рамках раунда"></span>-->

                <!-- Nickname игрока, сделавшего ставку-->
                <a class="nickname" target="_blank" data-bind="text: m5_users()[0].nickname"></a> <!--, attr: {href: 'http://steamcommunity.com/profiles/' + m5_users()[0].ha_provider_uid()}" title="Перейти в профиль игрока в Steam"></a>-->

              </div>

              <?php /*------------>
              <!-- Сумма, шансы -->
              <!--------------*/ ?>
              <div class="sum-odds-tickets">

                <!-- Сумма ставки и шансы выигрыша -->
                <span class="odds" data-bind="text: (Math.round(((+total_bet_amount() / +$root.m.s1.game.curjackpot())) * 100 * 10) / 10) + '%'" title="Шансы на победу ставки"></span>
                <span>/</span>
                <span class="sum" data-bind="text: Math.ceil((Math.round(total_bet_amount())/100)*server.data.usdrub_rate) + ' руб.'" title="Сумма ставки"></span>

                <!-- Номера билетов -->
                <!--<div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">-->
                <!--  <span data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>-->
                <!--  <span>-</span>-->
                <!--  <span data-bind="text: m5_users()[0].pivot.tickets_to()"></span>-->
                <!--</div>-->

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
            <div class="bets-item tooltipstered" data-bind="attr: {title: name}">

              <?php /*------------------------------------------->
              <!-- Цветная полоска с цветом качества/категории -->
              <!---------------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s1.get_cat_quality_item_color($data)}"></div>

              <?php /*----------->
              <!-- Изображение -->
              <!-------------*/ ?>
              <img data-bind="attr: {src: steammarket_image}"> <!--{src: $root.f.s1.get_steam_img_with_size(steammarket_image(), '80x55')}"> -->

              <?php /*---->
              <!-- Цена -->
              <!------*/ ?>
              <div class="price">
                <span data-bind="text: Math.ceil(price()*server.data.usdrub_rate)"></span>
                <span class="rub" data-bind="text: 'руб'"></span>
              </div>

            </div>

          </div>

        </div>

      </div>

      <?php /*---------------------------------------------->
      <!-- 1.1.2.6. Панель с информацией о текущем раунде -->
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
  <div class="statistics" data-bind="if: m.s1.game.choosen_room">

    <?php /*--------------------------->
    <!-- 1.3.1. Последний победитель -->
    <!-----------------------------*/ ?>
    <div class="lastwinner" data-bind="visible: (m.s1.game.statistics['m9:statistics:lastwinners'] && m.s1.game.choosen_room() && m.s1.game.choosen_room().id() && m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Последний победитель</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*------------>
        <!-- 3.1] Выигрыш -->
        <!--------------*/ ?>
        <div class="row">
          <div class="span50">Выигрыш:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

        <?php /*--------->
        <!-- 3.2] Шанс -->
        <!-----------*/ ?>
        <div class="row">
          <div class="span50">Шанс:</div>
          <div class="span50 odds" data-bind="text: m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].odds() + '%'"></div>
        </div>

      </div>

    </div>

    <?php /*---------------------->
    <!-- 1.3.2. Счастливчик дня -->
    <!------------------------*/ ?>
    <div class="luckyoftheday" data-bind="visible: (m.s1.game.statistics['m9:statistics:luckyoftheday'] && m.s1.game.statistics['m9:statistics:luckyoftheday'].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Счастливчик дня</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:luckyoftheday'].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:luckyoftheday'].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*------------>
        <!-- 3.1] Выигрыш -->
        <!--------------*/ ?>
        <div class="row">
          <div class="span50">Выигрыш:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:luckyoftheday'].jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

        <?php /*--------->
        <!-- 3.2] Шанс -->
        <!-----------*/ ?>
        <div class="row odds">
          <div class="span50">Шанс:</div>
          <div class="span50 odds" data-bind="text: m.s1.game.statistics['m9:statistics:luckyoftheday'].odds() + '%'"></div>
        </div>

      </div>

    </div>

    <?php /*------------------------>
    <!-- 1.3.3. Наибольшая ставка -->
    <!--------------------------*/ ?>
    <div class="thebiggetsbet" data-bind="visible: (m.s1.game.statistics['m9:statistics:thebiggetsbet'] && m.s1.game.statistics['m9:statistics:thebiggetsbet'].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Наибольшая ставка</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:thebiggetsbet'].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:thebiggetsbet'].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*---------------------->
        <!-- 3.1] Наибольшая ставка -->
        <!------------------------*/ ?>
        <div class="row">
          <div class="span50">Ставка:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:thebiggetsbet'].sum_cents_at_bet_moment()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

      </div>

    </div>

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




