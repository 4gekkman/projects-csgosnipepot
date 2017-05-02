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
  <meta name="description" content="CSGOHAP - Рулетка на скины Counter Strike Global Offensive от 1 рубля! Вещи для CS GO" />

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
  11. Пополнить баланс скинами

  Б. Зона блоков сбоку от контентного столбца
    Б1. Последний победитель (Classic Game)
    Б2. Счастливчик дня (Classic Game)
    Б3. Наибольшая ставка (Classic Game)

  В. Зона для SEO-текста
  F. Футер

  X. Получение данных с сервера и подключение JS этого документа
    X1. Получение данных с сервера
    X2. Подключение JS этого документа
    X3. Yandex.Metrika counter
    X4. Google Analitics counter

-------------------------*/ ?>
@section('content')
<?php /*-------------------------->
<!-- Контентный столбец (860px) -->
<!----------------------------*/ ?> <div class="content-column" data-bind="css: {aside: layoutmodel.m.s8.is_aside}">


<?php /*------------------->
<!-- A. Зона уведомлений -->
<!------------------------->
Подоглавление:

  A1. Уведомление для тех, кто не ввёл свой Steam Trade URL
  A2. Баннер "Будь Онлайн" №1

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

  <?php /*--------------------------->
  <!-- A2. Баннер "Будь Онлайн" №1 -->
  <!-----------------------------*/ ?>
  <!--<div style="display: none" class="notification beonline_banner_1" data-bind="click: layoutmodel.f.s1.choose_subdoc.bind(null, {uri: '/free'}), visible: layoutmodel.m.s1.selected_subdoc().uri() != '/free'">
    <img src="{!! asset('public/D10009/assets/banners/beonline1.jpg') !!}">
  </div>-->


</div>


<?php /*--------------->
<!-- 1. Classic game -->
<!-----------------*/ ?>
@include('D10009::Subviews.classicgame')

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
@include('D10009::Subviews.shop')

<?php /*----------------------->
<!-- 5. Профиль пользователя -->
<!-------------------------*/ ?>
@include('D10009::Subviews.profile')

<?php /*------------>
<!-- 6. Партнёрка -->
<!--------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/ref'">
  <span>Партнёрка</span>
</div>

<?php /*-------------->
<!-- 7. ТОП игроков -->
<!----------------*/ ?>
@include('D10009::Subviews.top')

<?php /*--------->
<!-- 8. F.A.Q. -->
<!-----------*/ ?>
@include('D10009::Subviews.faq')

<?php /*----------------->
<!-- 9. Тех. поддержка -->
<!-------------------*/ ?>
<div data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/support'">
  <span>Тех. поддержка</span>
</div>

<?php /*-------------->
<!-- 10. Free coins -->
<!----------------*/ ?>
@include('D10009::Subviews.freecoins')

<?php /*---------------------------->
<!-- 11. Пополнить баланс скинами -->
<!------------------------------*/ ?>
@include('D10009::Subviews.deposit')


<?php /*------------------------------------------->
<!-- Б. Зона блоков сбоку от контентного столбца -->
<!------------------------------------------------->
Подоглавление:

  Б1. Последний победитель (Classic Game)
  Б2. Счастливчик дня (Classic Game)
  Б3. Наибольшая ставка (Classic Game)

-------------------*/ ?>
<div class="aside-blocks" data-bind="if: m.s1.game.choosen_room, css: {aside: layoutmodel.m.s8.is_aside}">

  <?php /*--------------------------------------->
  <!-- Б1. Последний победитель (Classic Game) -->
  <!-----------------------------------------*/ ?>
  <div style="display: none" class="aside-block lastwinner" data-bind="visible: (m.s1.game.choosen_room() && m.s1.game.choosen_room().id() && m.s1.game.stats.thelastwinner.front.nickname() && layoutmodel.m.s1.selected_subdoc().uri() == '/')">

    <?php /*--------------->
    <!-- Контейнер карты -->
    <!-----------------*/ ?>
    <div class="card" data-bind="css: {flipped: m.s1.game.stats.thelastwinner.is_card_flipped}">

      <?php /*------------------->
      <!-- Фронтальная сторона -->
      <!---------------------*/ ?>
      <div class="front">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.thelastwinner.front.id()+'.jpg' + '?as=' + m.s1.game.stats.thelastwinner.front.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.thelastwinner.front.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.thelastwinner.front.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.thelastwinner.front.jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

          <?php /*--------->
          <!-- 3.2] Шанс -->
          <!-----------*/ ?>
          <div class="row">
            <div class="span50">Шанс:</div>
            <div class="span50 odds" data-bind="text: Math.round(m.s1.game.stats.thelastwinner.front.odds()*100)/100 + '%'"></div>
          </div>

        </div>

      </div>

      <?php /*---------------->
      <!-- Обратная сторона -->
      <!------------------*/ ?>
      <div class="back">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.thelastwinner.back.id()+'.jpg' + '?as=' + m.s1.game.stats.thelastwinner.back.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.thelastwinner.back.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.thelastwinner.back.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.thelastwinner.back.jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

          <?php /*--------->
          <!-- 3.2] Шанс -->
          <!-----------*/ ?>
          <div class="row">
            <div class="span50">Шанс:</div>
            <div class="span50 odds" data-bind="text: Math.round(m.s1.game.stats.thelastwinner.back.odds()*100)/100 + '%'"></div>
          </div>

        </div>

      </div>

    </div>

  </div>

  <?php /*---------------------------------->
  <!-- Б2. Счастливчик дня (Classic Game) -->
  <!------------------------------------*/ ?>
  <div style="display: none" class="aside-block luckyoftheday" data-bind="visible: (m.s1.game.stats.luckyoftheday.data.nickname() && layoutmodel.m.s1.selected_subdoc().uri() == '/')">

    <?php /*--------------->
    <!-- Контейнер карты -->
    <!-----------------*/ ?>
    <div class="card" data-bind="css: {flipped: m.s1.game.stats.luckyoftheday.is_card_flipped}">

      <?php /*------------------->
      <!-- Фронтальная сторона -->
      <!---------------------*/ ?>
      <div class="front">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.luckyoftheday.front.id()+'.jpg' + '?as=' + m.s1.game.stats.luckyoftheday.front.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.luckyoftheday.front.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.luckyoftheday.front.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.luckyoftheday.front.jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

          <?php /*--------->
          <!-- 3.2] Шанс -->
          <!-----------*/ ?>
          <div class="row odds">
            <div class="span50">Шанс:</div>
            <div class="span50 odds" data-bind="text: m.s1.game.stats.luckyoftheday.front.odds() + '%'"></div>
          </div>

        </div>

      </div>

      <?php /*---------------->
      <!-- Обратная сторона -->
      <!------------------*/ ?>
      <div class="back">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.luckyoftheday.back.id()+'.jpg' + '?as=' + m.s1.game.stats.luckyoftheday.back.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.luckyoftheday.back.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.luckyoftheday.back.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.luckyoftheday.back.jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

          <?php /*--------->
          <!-- 3.2] Шанс -->
          <!-----------*/ ?>
          <div class="row odds">
            <div class="span50">Шанс:</div>
            <div class="span50 odds" data-bind="text: m.s1.game.stats.luckyoftheday.back.odds() + '%'"></div>
          </div>

        </div>

      </div>

    </div>

  </div>

  <?php /*------------------------------------>
  <!-- Б3. Наибольшая ставка (Classic Game) -->
  <!--------------------------------------*/ ?>
  <div style="display: none" class="aside-block thebiggestbet" data-bind="visible: (m.s1.game.stats.thebiggestbet.data.nickname() && layoutmodel.m.s1.selected_subdoc().uri() == '/')">

    <?php /*--------------->
    <!-- Контейнер карты -->
    <!-----------------*/ ?>
    <div class="card" data-bind="css: {flipped: m.s1.game.stats.thebiggestbet.is_card_flipped}">

      <?php /*------------------->
      <!-- Фронтальная сторона -->
      <!---------------------*/ ?>
      <div class="front">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.thebiggestbet.front.id()+'.jpg' + '?as=' + m.s1.game.stats.thebiggestbet.front.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.thebiggestbet.front.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.thebiggestbet.front.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.thebiggestbet.front.sum_cents_at_bet_moment()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

        </div>

      </div>

      <?php /*---------------->
      <!-- Обратная сторона -->
      <!------------------*/ ?>
      <div class="back">

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
          <img data-bind="attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+m.s1.game.stats.thebiggestbet.back.id()+'.jpg' + '?as=' + m.s1.game.stats.thebiggestbet.back.avatar_steam().slice(-20) + '&ua=' + m.s1.game.stats.thebiggestbet.back.updated_at().replace(/[ :-]/g,'')}">

          <?php /*------------>
          <!-- 2.2] Никнэйм -->
          <!--------------*/ ?>
          <div class="nickname">
            <span data-bind="text: m.s1.game.stats.thebiggestbet.back.nickname"></span>
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
            <div class="span50" data-bind="text: Math.round((m.s1.game.stats.thebiggestbet.back.sum_cents_at_bet_moment()/100)*server.data.usdrub_rate) + ' руб.'"></div>
          </div>

        </div>

      </div>

    </div>

  </div>

</div>


<?php /*---------------------->
<!-- В. Зона для SEO-текста -->
<!------------------------*/ ?>

  <?php /*--------------------------->
  <!-- В1. Статья для Classic Game -->
  <!-----------------------------*/ ?>
  <div class="seo" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/' && m.s0.auth.is_anon()">
    <h1>Рулетка CS GO со ставками от 1 рубля</h1>
    <p>Представляем вашему вниманию популярную рулетку CSGOHAP.RU &ndash; удобный сервис для любителей всемирно известного сетевого шутера Counter Strike: Global Offensive, где игроки могут выгодно покупать и продавать скины (вещи и оружие) за живые деньги или ставки. Наша CS GO- рулетка с минимальной ставкой 1 рубль позволяет значительно улучшить снаряжение и огневую мощь вашего стрелка быстро и практически бесплатно!</p>
    <h2>Что такое рулетка скинов КС ГО?</h2>
    <p>Каждый геймер заинтересован в том, чтобы его виртуальный стрелок был хорошо экипирован и вооружен &ndash; это значительно увеличивает шансы на победу. Вы можете долго наращивать мощь бойца, играя и проходя миссии или принять участие в случайных (рандомных) раздачах оружия и амуниции. Мы предлагаем третий путь &ndash; быстрый и практичный: купить или продать скины на CS GO (рулетка от 1 рубля)!</p>
    <p>При этом рулетка вещей КС ГО не требует больших затрат &ndash; вещи и оружие можно выиграть в лотерею, внеся минимальный вклад от 1-го рубля или выставив на кон ненужное вам рандомное оружие и предметы! Все игроки просто делают свои взносы в общую копилку, после чего происходит розыгрыш, и система случайным образом выбирает победителя, который и забирает весь шмот.</p>
    <p>Способ прозрачен, предлагает участникам абсолютно равные шансы и позволяет быстро и дешево укомплектовать своего бота &ndash; отличная возможность повысить свою &laquo;выживаемость&raquo; в игре, в первую очередь для новичков и &laquo;бомжей&raquo; (игроков без денег). При минимальном взносе, они могут выиграть максимум возможного! Для опытных игроков ставки и размеры взноса скинами не ограничены (чем выше взнос, тем больше шансов на выигрыш).</p>
    <h2>Как это работает?</h2>
    <p>CS GO рулетка предлагает геймерам Counter Strike выиграть скины (амуниция и оружие), кейсы (рандомные комплекты), капсулы, наклейки и т.д. Для этого вам нужно:</p>
    <ol>
      <li>Внести депозит за своего игрового бота (от рубля, но не более 20 предметов одновременно).</li>
      <li>Получить ценовой билет (10 рублей = 100 билетов).</li>
      <li>Принять участие в розыгрыше (начинается при сборе 100 скинов в лотерее).</li>
      <li>Стать победителем и забрать весь шмот!</li>
    </ol>
    <p>При внесении депозита проверьте правильность указываемой ссылки на обмен, иначе выигрыш будет аннулирован. Сувениры, текстуры и предметы из других игр не принимаются к лотерее.</p>
    <p>Сервис имеет право на 10% комиссии от общей суммы розыгрыша и замену предметов их эквивалентами по своему усмотрению. После того, как выигрыш автоматически отправляется вам (2-3 минуты), вам необходимо изъять его в течение 10 минут!</p>
    <p>Информация для участников игры на скины CS GO: мы работаем прозрачно (выигрыш присуждается автоматически случайным образом), защищенно, оперативно (розыгрыш каждые 1.5 минуты). Разобраться в интерфейсе рулетки сможет любой новичок, прошедший регистрацию на сервисе. По любым вопросам оказывается круглосуточная поддержка.</p>
  </div>


<?php /*-------->
<!-- F. Футер -->
<!----------*/ ?>
<div class="footer">

  <?php /*----------------------------------------------------------------------->
  <!-- F1. Условия использования, предупреждение о не аффилированности с Valve -->
  <!-------------------------------------------------------------------------*/ ?>
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





</div>





</div> @stop



<?php /*--------------------------------------------------------------->
<!-- X. Получение данных с сервера и подключение JS этого документа  -->
<!-----------------------------------------------------------------*/ ?>
@section('js')

  <?php /*------------------------------>
  <!-- X1. Получение данных с сервера -->
  <!--------------------------------*/ ?>
  <script>

    // 1. Подготовить объект, в который будут записаны данные
    var server = {};

    // 2. Принять данные

      // 2.1. Принять csrf_token
      server.csrf_token             = "{{ csrf_token() }}";

      // 2.2. Принять переданные из контроллера данные
      server.data                   =  {!! $data !!};

  </script>


  <?php /*---------------------------------->
  <!-- X2. Подключение JS этого документа -->
  <!------------------------------------*/ ?>

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


  <?php /*-------------------------->
  <!-- X3. Yandex.Metrika counter -->
  <!----------------------------*/ ?>
  <script type="text/javascript">
      (function (d, w, c) {
          (w[c] = w[c] || []).push(function() {
              try {
                  w.yaCounter31732306 = new Ya.Metrika({
                      id:31732306,
                      clickmap:true,
                      trackLinks:true,
                      accurateTrackBounce:true,
                      webvisor:true
                  });
              } catch(e) { }
          });

          var n = d.getElementsByTagName("script")[0],
              s = d.createElement("script"),
              f = function () { n.parentNode.insertBefore(s, n); };
          s.type = "text/javascript";
          s.async = true;
          s.src = "https://mc.yandex.ru/metrika/watch.js";

          if (w.opera == "[object Opera]") {
              d.addEventListener("DOMContentLoaded", f, false);
          } else { f(); }
      })(document, window, "yandex_metrika_callbacks");
  </script>
  <noscript><div><img src="https://mc.yandex.ru/watch/31732306" style="position:absolute; left:-9999px;" alt="" /></div></noscript>


  <?php /*---------------------------->
  <!-- X4. Google Analitics counter -->
  <!------------------------------*/ ?>
  <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-58933893-3', 'auto');
  ga('send', 'pageview');

  </script>


@stop




