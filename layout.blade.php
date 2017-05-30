<?php /* =================================================////
////																										  ////
////                     Шаблон L-пакета				          ////
////																										  ////
////======================================================////
//// 			        		 		   ////
//// 			  Оглавление			 ////
//// 			         				   ////
////=========================////


    А. Содержимое HEAD html-разметки шаблона
    ----------------------------------------
      А1. Подключение тэга title с названием документа
      А2. Подключение локального CSS документа

    Б. Содержимое BODY html-разметки шаблона
    ----------------------------------------
      Б1. ...

    В. Подключение ресурсов шаблона
    -------------------------------
      В1. Принять данные для шаблона с сервера
      В2. Подключение JS-скрипта наследника шаблона


////==================================================== */ ?>

<!doctype html>
<html lang="ru">


<?php /*----------------------------------------->
<!-- А. Содержимое HEAD html-разметки шаблона  -->
<!-------------------------------------------*/ ?>
<head>
  <meta charset="UTF-8">

  <?php /*-- А1. Подключение тэга title с названием документа -->
  <!--------------------------------------------------------*/ ?>
  @yield('title')

  <?php /*-- А2. Подключение локального CSS документа -->
  <!------------------------------------------------*/ ?>
  @yield('css')

  <?php /*-- А3. Подключение favicon -->
  <!-------------------------------*/ ?>
  <link rel="icon" type="image/png" href="{!! asset('public/L10003/assets/icons/favicon-48x48.png') !!}" sizes="16x16">
  <link rel="icon" type="image/png" href="{!! asset('public/L10003/assets/icons/favicon-48x48.png') !!}" sizes="32x32">
  <link rel="icon" type="image/png" href="{!! asset('public/L10003/assets/icons/favicon-48x48.png') !!}" sizes="48x48">

</head>

<?php /*----------------------------------------->
<!-- Б. Содержимое BODY html-разметки шаблона  -->
<!----------------------------------------------->

  1. Фиксированная шапка сайта
    1.1. Область шапки над чатом и гл.меню (для логотипа)
    1.2. Область шапки с элементами управления
  2. Фиксированное главное меню слева
    2.1. Переключатель главного меню
    2.2. Главное меню
  3. Фиксированная панель чата справа
    3.1. Панель управления чатом в верхней части
  4. Фиксированная кнопка раскрытия правого чата справа
  5. Интерфейс "Пополнение баланса"

  n. Экран загрузки документа
  x. Контентная область

---------------------------------------------*/ ?>
<body>

  <?php /*---------------------------->
  <!-- 1. Фиксированная шапка сайта -->
  <!------------------------------*/ ?>
  <div class="site-header">

    <?php /*----------------------------------------------------->
    <!-- 1.1. Область шапки над чатом и гл.меню (для логотипа) -->
    <!-------------------------------------------------------*/ ?>
    <div class="area1">

      <div class="logo">
        <img src="{!! asset('public/L10003/assets/images/csgohap_logo_white.png') !!}">
      </div>

    </div>

    <?php /*------------------------------------------>
    <!-- 1.2. Область шапки с элементами управления -->
    <!--------------------------------------------*/ ?>
    <div class="area2">

      <?php /*--------------------->
      <!-- Интерфейсы для гостей -->
      <!-----------------------*/ ?>

        <?php /*---------------------->
        <!-- 1] Интернационализация -->
        <!------------------------*/ ?>
        <div style="display: none" class="i18n" data-bind="visible: !m.s0.is_logged_in()">

          <?php /*------------------->
          <!-- 1.1] Выбранный язык -->
          <!---------------------*/ ?>
          <div class="i18n_choosen">
            <i class="mdi mdi-menu-up"></i>
          </div>

          <?php /*------------------------->
          <!-- 1.2] Панель выбора языков -->
          <!---------------------------*/ ?>
          <div class="i18n_panel">

          </div>

        </div>

        <?php /*-------------------->
        <!-- 2] Управление звуком -->
        <!----------------------*/ ?>
        <div style="display: none" class="soundcontrol_guest" data-bind="visible: !m.s0.is_logged_in()">
          <img data-bind="visible: m.s4.is_global_volume_on, click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_on.svg') !!}">
          <img data-bind="visible: !m.s4.is_global_volume_on(), click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_off.svg') !!}">
        </div>

        <?php /*------------------>
        <!-- 3] Кнопка "Log in" -->
        <!--------------------*/ ?>
        <div style="display: none" class="login_button" data-bind="visible: !m.s0.is_logged_in()" onclick="window.location = '{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam&authmode=redirect&url_redirect='+window.location.href"> <!--  onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); else popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")> -->
          <i class="fa fa-fw fa-steam"></i>
          <span>Войти через Steam</span>
        </div>

      <?php /*------------------------------------------------>
      <!-- Интерфейсы для аутентифицированных пользователей -->
      <!--------------------------------------------------*/ ?>



        <?php /*-------------------->
        <!-- 2] Управление звуком -->
        <!----------------------*/ ?>
        <div style="display: none" class="soundcontrol_authuser" data-bind="visible: m.s0.is_logged_in">
          <img data-bind="visible: m.s4.is_global_volume_on, click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_on.svg') !!}" title="Выключить звук">
          <img data-bind="visible: !m.s4.is_global_volume_on(), click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_off.svg') !!}" title="Включить звук">
        </div>

        <?php /*--------->
        <!-- 3] Баланс -->
        <!-----------*/ ?>
        <div style="display: none" class="balance" data-bind="visible: m.s0.is_logged_in, click: function(){ layoutmodel.m.s7.ison(true); }" title="Внести депозит">

          <?php /*------------------------>
          <!-- 1] Надпись "Ваши монеты" -->
          <!--------------------------*/ ?>
          <span class="youcoins_note">Баланс:</span>

          <?php /*------------------------------------------->
          <!-- 2] Баланс аутентифицированного пользователя -->
          <!---------------------------------------------*/ ?>
          <span class="yourcoins_balance" data-bind="text: m.s0.balance"></span>

          <?php /*-------------------->
          <!-- 3] Иконка с монетами -->
          <!----------------------*/ ?>
          <i class="mdi mdi-plus-circle-outline"></i>

        </div>

        <?php /*----------------------------------------------------------->
        <!-- 4] Информация об аккаунте аутентифицированного пользователя -->
        <!-------------------------------------------------------------*/ ?>
        <div class="account" data-bind="visible: m.s0.is_logged_in">

          <?php /*----------->
          <!-- 1] Аватар -->
          <!-------------*/ ?>
          <div class="avatar">
            <img data-bind="attr: {src: m.s0.auth.user().avatar_steam}" src="http://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/87/8781d4671c68dbbeba1910d7989664dad391c2fc_full.jpg">
          </div>

          <?php /*------------------------------->
          <!-- 2] Никнэйм и кнопка "Log out" -->
          <!---------------------------------*/ ?>
          <div class="nickname_logout">
            <div class="nickname">
              <span data-bind="text: m.s0.auth.user().nickname, attr: {title: m.s0.auth.user().nickname}" title="gtmmm2011"></span>
            </div>
            <div class="logout">
              <span data-bind="click: f.s0.logout">Выйти</span>
            </div>
          </div>

        </div>

    </div>

  </div>

  <?php /*----------------------------------->
  <!-- 2. Фиксированное главное меню слева -->
  <!-------------------------------------*/ ?>
  <div class="menu" data-bind="css: {'menu-hidden': !m.s2.expanded()}, style: {top: (m.s2.top() + 'px')}">

    <?php /*-------------------------------->
    <!-- 2.1. Переключатель главного меню -->
    <!----------------------------------*/ ?>
    <div class="toggle" data-bind="click: f.s2.switch, visible: !m.s2.hidden()">
      <i class="mdi mdi-chevron-double-left"></i>
    </div>

    <?php /*----------------->
    <!-- 2.2. Главное меню -->
    <!-------------------*/ ?>
    <div class="items" data-bind="foreach: m.s1.subdocs">

      <?php /*---------->
      <!-- Пункт меню -->
      <!------------*/ ?>
      <div class="item" data-bind="style: {backgroundColor: bg_color, borderColor: brd_color}, css: {choosen: $data.uri() == $root.m.s1.selected_subdoc().uri()}, visible: (visible() && (layoutmodel.m.s0.is_logged_in() ? true : vis4anon()))">

        <?php /*---------------------->
        <!-- 1] Контент пункта меню -->
        <!------------------------*/ ?>
        <div class="item-content">

          <?php /*------------------------------------------->
          <!-- 1.1] Контент пункта меню для "Classic game" -->
          <!---------------------------------------------*/ ?>
          <div style="display: none" data-bind="visible: uri() == '/', click: $root.f.s1.choose_subdoc.bind($data, {uri: uri(), redirect: ext_redir()})">

            <?php /*------------------------->
            <!-- 1.1.1] Иконка пункта меню -->
            <!---------------------------*/ ?>
            <div class="icon">
              <i class="mdi" data-bind="css: icon_mdi"></i>
            </div>

            <?php /*--------------------------------------->
            <!-- 1.1.2] Заголовок и всплывающие панельки -->
            <!-----------------------------------------*/ ?>
            <div class="uppart">

              <?php /*--------->
              <!-- Заголовок -->
              <!-----------*/ ?>
              <span data-bind="text: title"></span>

              <?php /*-------------------->
              <!-- Всплывающая панелька -->
              <!----------------------*/ ?>
              <div class="bubblepanel" data-bind="css: {lifted: $root.m.s6.notify.is_hidden}, style: {transitionDuration: $root.m.s6.notify.traisitionDuration}">
                <span data-bind="text: $root.m.s6.notify.text"></span>
              </div>

            </div>

            <?php /*-------------------------->
            <!-- 1.1.3] Состояние / На кону -->
            <!----------------------------*/ ?>
            <div class="downpart">

              <?php /*------------------>
              <!-- Название состояния -->
              <!--------------------*/ ?>
              <span class="game-state" data-bind="text: $root.m.s6.status.title"></span>

              <?php /*------->
              <!-- На кону -->
              <!---------*/ ?>
              <span class="game-prize" data-bind="text: Math.round(($root.m.s6.curjackpot()/100)*server.data.usdrub_rate) + ' руб.'"></span>

            </div>

          </div>

          <?php /*----------------------------------------->
          <!-- 1.2] Контент пункта меню "Тех.поддержка"" -->
          <!-------------------------------------------*/ ?>
          <div style="display: none" data-bind="visible: uri() == '/support'" onclick="window.open('https://vk.com/id271729956', '_blank');">

            <?php /*------------------------->
            <!-- 1.2.1] Иконка пункта меню -->
            <!---------------------------*/ ?>
            <div class="icon" data-bind="css: {'image-instead': ((icon_url() || icon_svg()) && !icon_mdi())}">
              <i style="display: none" class="mdi" data-bind="visible: icon_mdi, css: icon_mdi"></i>
              <img style="display: none" data-bind="visible: icon_url, attr: {src: icon_url}">
              <img class="svg" style="display: none" data-bind="visible: icon_svg, attr: {src: icon_svg}">
            </div>

            <?php /*-------------------------->
            <!-- 1.2.2] Контент пункта меню -->
            <!----------------------------*/ ?>
            <span data-bind="text: title"></span>

          </div>

          <?php /*------------------------------------>
          <!-- 1.n] Стандартный контент пункта меню -->
          <!--------------------------------------*/ ?>
          <div style="display: none" data-bind="visible: ['/', '/support'].indexOf(uri()) == -1, click: $root.f.s1.choose_subdoc.bind($data, {uri: uri(), redirect: ext_redir(), not_clckbl: not_clckbl()})">

            <?php /*------------------------->
            <!-- 1.n.1] Иконка пункта меню -->
            <!---------------------------*/ ?>
            <div class="icon" data-bind="css: {'image-instead': ((icon_url() || icon_svg()) && !icon_mdi())}">
              <i style="display: none" class="mdi" data-bind="visible: icon_mdi, css: icon_mdi"></i>
              <img style="display: none" data-bind="visible: icon_url, attr: {src: icon_url}">
              <img class="svg" style="display: none" data-bind="visible: icon_svg, attr: {src: icon_svg}">
            </div>

            <?php /*-------------------------->
            <!-- 1.n.2] Контент пункта меню -->
            <!----------------------------*/ ?>
            <span data-bind="text: title, css: {soon: soon}"></span>

          </div>

        </div>

      </div>

    </div>

    <?php /*-------------------------->
    <!-- 2.3. Счётчик пользователей -->
    <!----------------------------*/ ?>
    <div class="users-counter">

      <?php /*------------------------->
      <!-- Контент для скрытого меню -->
      <!---------------------------*/ ?>
      <div style="display: none" class="content-menu-hidden" data-bind="visible: !m.s2.expanded()">
        <span data-bind="text: m.s0.logged_in_steam_users"></span>
      </div>

      <?php /*--------------------------->
      <!-- Контент для раскрытого меню -->
      <!-----------------------------*/ ?>
      <div style="display: none" class="content-menu-not-hidden" data-bind="visible: m.s2.expanded">
        <span data-bind="text: 'На сайте '+m.s0.logged_in_steam_users()+' '+m.s0.logged_in_steam_users_declension()"></span>
      </div>

    </div>

    <?php /*--------------------->
    <!-- 2.4. Иконки соц.сетей -->
    <!-----------------------*/ ?>
    <div class="social-icons">

      <?php /*--------------------------->
      <!-- Контент для раскрытого меню -->
      <!-----------------------------*/ ?>
      <div style="display: none" class="content-menu-hidden" data-bind="visible: !m.s2.expanded()">

        <?php /*----- VK -----*/ ?>
        <a target="_blank" href="https://vk.com/csgohap">
          <img src="{!! asset('public/L10003/assets/social/vk_icon.svg') !!}">
        </a>

        <?php /*----- Twitter -----*/ ?>
        <a target="_blank" href="https://twitter.com/csgohap ">
          <img src="{!! asset('public/L10003/assets/social/twitter_icon.svg') !!}">
        </a>

        <?php /*----- Steam -----*/ ?>
        <a target="_blank" href="http://steamcommunity.com/groups/CSGOHAP">
          <img src="{!! asset('public/L10003/assets/social/steam_icon.svg') !!}">
        </a>

      </div>

      <?php /*------------------------->
      <!-- Контент для скрытого меню -->
      <!---------------------------*/ ?>
      <div style="display: none" class="content-menu-not-hidden" data-bind="visible: m.s2.expanded">

        <?php /*----- VK -----*/ ?>
        <a target="_blank" href="https://vk.com/csgohap">
          <img src="{!! asset('public/L10003/assets/social/vk_icon.svg') !!}">
        </a>

        <?php /*----- Twitter -----*/ ?>
        <a target="_blank" href="https://twitter.com/csgohap ">
          <img src="{!! asset('public/L10003/assets/social/twitter_icon.svg') !!}">
        </a>

        <?php /*----- Steam -----*/ ?>
        <a target="_blank" href="http://steamcommunity.com/groups/CSGOHAP">
          <img src="{!! asset('public/L10003/assets/social/steam_icon.svg') !!}">
        </a>

      </div>

    </div>

  </div>

  <?php /*----------------------------------->
  <!-- 3. Фиксированная панель чата справа -->
  <!-------------------------------------*/ ?>
  <div class="chat-right-container">

    <?php /*---------->
    <!-- Правый чат -->
    <!------------*/ ?>
    <div class="chat" data-bind="css: {'hidden-chat': !m.s3.expanded(), minimized: m.s3.hidden, 'min-chat-not-expanded': !m.s3.expanded()}"><noindex>

      <?php /*-------------------------------------------->
      <!-- 3.1. Панель управления чатом в верхней части -->
      <!----------------------------------------------*/ ?>
      <div class="controls" data-bind="click: f.s3.switch">

        <?php /*-------------------->
        <!-- Кнопка сокрытия чата -->
        <!----------------------*/ ?>
        <div class="hide_chat_button">

          <?php /*--------------------------------------------->
          <!-- 1] Для большой версии чата на правом сайдбаре -->
          <!-----------------------------------------------*/ ?>
          <i class="mdi mdi-chevron-double-right" data-bind="visible: !m.s3.hidden()"></i>

          <?php /*------------------------->
          <!-- 2] Для мелкой версии чата -->
          <!---------------------------*/ ?>
          <i class="mdi mdi-chevron-down" data-bind="visible: m.s3.hidden()"></i>

        </div>

        <?php /*---------------------->
        <!-- Надпись "Чат" и иконка -->
        <!------------------------*/ ?>
        <div class="chatheader">
          <i class="mdi mdi-wechat"></i>
          <span>Чат</span>
        </div>

      </div>

      <?php /*--------------------->
      <!-- 3.2. Сообщения в чате -->
      <!-----------------------*/ ?>
      <div class="chat-messages" data-bind="foreach: m.s5.messages">

        <?php /*---------------->
        <!-- Сообщение в чате -->
        <!------------------*/ ?>
        <div class="chat-message row">

          <?php /*-------->
          <!-- Аватарка -->
          <!----------*/ ?>
          <div class="avatarka span20" data-bind="attr: {title: steamname}">

            <div style="display: none" data-bind="if: !system(), visible: !system()">
              <img style="display: none" data-bind="visible: !system(), attr: {src: layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+id_user()+'.jpg' + '?as=' + avatar().slice(-20) + '&ua=' + user_updated_at().replace(/[ :-]/g,'')}">
            </div>

            <div style="display: none" data-bind="if: system, visible: system">
              <img style="display: none" data-bind="visible: system, attr: {src: avatar}">
            </div>

          </div>

          <?php /*--------->
          <!-- Сообщение -->
          <!-----------*/ ?>
          <div class="message span77">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div data-bind="attr: {title: steamname()}, css: {system: system}">
              <span data-bind="text: steamname()"></span>
            </div>

            <?php /*-------------->
            <!-- Само сообщение -->
            <!----------------*/ ?>
            <div data-bind="css: {system: system}">
              <span data-bind="text: message"></span>
            </div>

          </div>

          <?php /*------------>
          <!-- Кнопка "Бан" -->
          <!--------------*/ ?>
          <div style="display: none" class="ban-button" data-bind="visible: $root.m.s5.is_curuser_moderator() && !system(), click: $root.f.s5.ban_open_interface">
            <span>Бан</span>
          </div>

        </div>

      </div>

      <?php /*------------------------------------------------------------>
      <!-- 3.3. Панелька с градиентом прозрачности в верхней части чата -->
      <!--------------------------------------------------------------*/ ?>
      <!--<div class="gradient-panel"></div>-->

      <?php /*----------------------------------->
      <!-- 3.4. Поле для ввода сообщений в чат -->
      <!-------------------------------------*/ ?>
      <div class="chat-input">

        <?php /*------------->
        <!-- Кнопка "Send" -->
        <!---------------*/ ?>
        <div style="display: none" class="send" data-bind="click: f.s5.post_to_the_chat_main, visible: m.s0.is_logged_in">
          <i class="mdi mdi-send"></i>
        </div>

        <?php /*------------------------>
        <!-- Поле для ввода сообщений -->
        <!--------------------------*/ ?>
        <div style="display: none" class="input-field" data-bind="visible: m.s0.is_logged_in">
          <input type="text" data-bind="textInput: m.s5.new_message, event: {keypress: f.s5.post_to_the_chat_main}" placeholder="Введите сообщение...">
        </div>

        <?php /*-------------------------------------------------->
        <!-- Сообщения для не аутентифицированных пользователей -->
        <!----------------------------------------------------*/ ?>
        <div style="display: none" class="msg-for-not-auth" data-bind="visible: !m.s0.is_logged_in()">
          <span>Чтобы писать, </span>
          <a onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); else popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")>войдите через Steam</a>
        </div>

      </div>

      <?php /*------------------------>
      <!-- 3.5. Интерфейс бана чата -->
      <!--------------------------*/ ?>
      <div style="display: none" class="ban-interface" data-bind="visible: (m.s5.ban.visible() && m.s5.is_curuser_moderator())">

        <?php /*--------->
        <!-- Заголовок -->
        <!-----------*/ ?>
        <div class="ban-header">
          <span>Бан пользователя в чате</span>
          <i class="mdi mdi-close" data-bind="click: function(){ m.s5.ban.visible(0); }"></i>
        </div>

        <?php /*---------->
        <!-- Содержимое -->
        <!------------*/ ?>
        <div class="ban-content">

          <?php /*--------------------->
          <!-- ID и ник пользователя -->
          <!-----------------------*/ ?>
          <div class="ban-id-nick">
            <span data-bind="html: m.s5.ban.steamname() + ' (id: ' + m.s5.ban.id_user() + ')', attr: {title: m.s5.ban.steamname() + ' (id: ' + m.s5.ban.id_user() + ')'}"></span>
          </div>

          <?php /*------------------->
          <!-- Срок бана в минутах -->
          <!---------------------*/ ?>
          <div class="ban-time">
            <input type="text" data-bind="textInput: m.s5.ban.ban_time_min" placeholder="Время бана в минутах">
            - время в мин.
          </div>

          <?php /*------------>
          <!-- Причина бана -->
          <!--------------*/ ?>
          <div class="ban-time">
            <textarea data-bind="value: m.s5.ban.reason"></textarea>
          </div>

        </div>

        <?php /*----------------->
        <!-- Кнопка "Забанить" -->
        <!-------------------*/ ?>
        <div class="ban-button" data-bind="click: f.s5.ban">
          <span>Забанить</span>
          <div style="display: none" class="loader" data-bind="visible: m.s5.ban.is_spinner_vis">
            <div class="loader-inner ball-clip-rotate">
              <div></div>
            </div>
          </div>
        </div>

      </div>



    </noindex></div>

  </div>

  <?php /*----------------------------------------------------->
  <!-- 4. Фиксированная кнопка раскрытия правого чата справа -->
  <!-------------------------------------------------------*/ ?>
  <div class="chat_right_open" data-bind="click: f.s3.switch, css: {'hidden-chat-button': m.s3.expanded}, visible: !m.s3.hidden()">

    <i class="mdi mdi-wechat"></i>
    <span>Чат</span>

  </div>

  <?php /*--------------------------------->
  <!-- 5. Интерфейс "Пополнение баланса" -->
  <!-----------------------------------*/ ?>

    <?php /*-------------------------------->
    <!-- 5.1. Модальный щит на весь экран -->
    <!----------------------------------*/ ?>
    <div style="display: none; z-index: 5" class="fade-shield" data-bind="visible: m.s7.ison"></div>

    <?php /*----------------------------------------->
    <!-- 5.2. Модальное окно с вариантами депозита -->
    <!-------------------------------------------*/ ?>
    <div style="display: none; z-index: 6" class="deposit-window-cont" data-bind="click: f.s7.close_popup, visible: m.s7.ison">

      <?php /*-------------->
      <!-- Модальное окно -->
      <!----------------*/ ?>
      <div class="deposit-window" data-bind="click: function(){}, clickBubble: false">

        <?php /*---------------------------->
        <!-- 1] Заголовок модального окна -->
        <!------------------------------*/ ?>
        <div class="header">

          <?php /*------------>
          <!-- 1] Заголовок -->
          <!--------------*/ ?>
          <span class="header-text">Пополнение баланса</span>

          <?php /*------------------->
          <!-- 2] Кнопка "закрыть" -->
          <!---------------------*/ ?>
          <i class="mdi mdi-close" data-bind="click: f.s7.close_popup"></i>

          <?php /*--------------------------------------------------->
          <!-- 3] Размер баланса аутентифицированного пользователя -->
          <!-----------------------------------------------------*/ ?>
          <span class="header-balance" data-bind="text: m.s0.balance"></span>

          <?php /*------------------->
          <!-- 4] Надпись "Баланс" -->
          <!---------------------*/ ?>
          <span class="header-balance-text">Баланс: </span>

        </div>

        <?php /*----------------------------->
        <!-- 2] Содержимое модального окна -->
        <!-------------------------------*/ ?>
        <table class="mw-content"><tbody><tr>

          <?php /*---------------------->
          <!-- 2.1] Пополнить скинами -->
          <!------------------------*/ ?>
          <td class="byskins">

            <?php /*--------------------------->
            <!-- 2.1.1] Картинка и заголовок -->
            <!-----------------------------*/ ?>
            <div class="img_and_header">
              <div class="img">
                <img src="{!! asset('public/L10003/assets/images/drop.png') !!}">
              </div>
              <div class="h-part">
                <span>Пополнить скинами</span>
              </div>
            </div>

            <?php /*--------------------->
            <!-- 2.1.2] Основной текст -->
            <!-----------------------*/ ?>
            <div class="main-text">
              <span data-bind="text: 'Обменяйте свои скины стоимостью от ~'+m.s7.min_price_rub()+' '+m.s7.declension()+' на монеты. За '+f.s7.kopeyky()['every']+' ~'+Math.round(layout_data.data.usdrub_rate)+' '+f.s7.kopeyky()['kop']+' вы получите 1 монету.'"></span>
            </div>

            <?php /*---------------------->
            <!-- 2.1.3] Текст-пояснение -->
            <!------------------------*/ ?>
            <div class="desc-text">
              <span>Можно отправлять до 30 предметов за раз. Перед отправкой вещей вы сможете увидеть нашу оценку их стоимости.</span>
            </div>

            <?php /*------------------------->
            <!-- 2.1.4] Кнопка "Пополнить" -->
            <!---------------------------*/ ?>
            <div class="deposit-button-cont">
              <button class="deposit-button" data-bind="click: function(){ f.s7.close_popup(); f.s1.choose_subdoc({uri: '/deposit'}); }">Пополнить</button>
            </div>

          </td>

          <?php /*-------------------------->
          <!-- 2.2] Реферальная программа -->
          <!----------------------------*/ ?>
          <td class="byrefs">

            <?php /*--------------------------->
            <!-- 2.2.1] Картинка и заголовок -->
            <!-----------------------------*/ ?>
            <div class="img_and_header">
              <div class="img">
                <img src="{!! asset('public/L10003/assets/images/partner.png') !!}">
              </div>
              <div class="h-part">
                <span>Партнёрская программа</span>
              </div>
            </div>

            <?php /*--------------------->
            <!-- 2.2.2] Основной текст -->
            <!-----------------------*/ ?>
            <div class="main-text">
              <span>Получайте монеты за приглашение новых игроков на проект с помощью реферальной ссылки или кода.</span>
            </div>

            <?php /*---------------------->
            <!-- 2.2.3] Текст-пояснение -->
            <!------------------------*/ ?>
            <div class="desc-text">
              <span>За каждого приглашенного реферала вы получаете 10 монет, а так же 30% от суммы пополнения его баланса.</span>
            </div>

            <?php /*------------------------->
            <!-- 2.2.4] Кнопка "Пополнить" -->
            <!---------------------------*/ ?>
            <div class="deposit-button-cont">
              <button class="deposit-button disabled">Пополнить</button>
            </div>

            <?php /*------------------------------------>
            <!-- 2.2.5] Модальный щит "Coming soon.." -->
            <!--------------------------------------*/ ?>
            <!--<div class="modal-coming-soon">-->
            <!--  <img src="{!! asset('public/L10003/assets/images/comingsoon2.png') !!}">-->
            <!--</div>-->

          </td>

          <?php /*----------------------->
          <!-- 2.3] Пополнить деньгами -->
          <!------------------------*/ ?>
          <td class="bymoney">

            <?php /*--------------------------->
            <!-- 2.3.1] Картинка и заголовок -->
            <!-----------------------------*/ ?>
            <div class="img_and_header">
              <div class="img">
                <img src="{!! asset('public/L10003/assets/images/money.png') !!}">
              </div>
              <div class="h-part">
                <span>С помощью платёжной системы</span>
              </div>
            </div>

            <?php /*--------------------->
            <!-- 2.3.2] Основной текст -->
            <!-----------------------*/ ?>
            <div class="main-text">
              <span>Выберите удобный способ оплаты и получите необходимое количество монет на свой аккаунт.</span>
            </div>

            <?php /*---------------------->
            <!-- 2.3.3] Текст-пояснение -->
            <!------------------------*/ ?>
            <div class="desc-text">
              <span>Пополняя через платежные системы вы можете получить выгоду до 20%. Чем больше сумма пополнения, тем больше процент монет.</span>
            </div>

            <?php /*------------------------->
            <!-- 2.3.4] Кнопка "Пополнить" -->
            <!---------------------------*/ ?>
            <div class="deposit-button-cont">
              <button class="deposit-button disabled">Пополнить</button>
            </div>

            <?php /*------------------------------------>
            <!-- 2.3.5] Модальный щит "Coming soon.." -->
            <!--------------------------------------*/ ?>
            <!--<div class="modal-coming-soon">-->
            <!--  <img src="{!! asset('public/L10003/assets/images/comingsoon2.png') !!}">-->
            <!--</div>-->

          </td>

        </tr></tbody></table>

      </div>

    </div>

  <?php /*--------------------------->
  <!-- n. Экран загрузки документа -->
  <!-----------------------------*/ ?>
  <div class="start-loading-screen" style="z-index: 99999999;">

    <img src="{!! asset('public/L10003/assets/images/csgohap_logo_white.png') !!}">

  </div>

  <?php /*--------------------->
  <!-- x. Контентная область -->
  <!-----------------------*/ ?>
  <div data-bind="stopBindings: true">
    <div id="content" class="content" data-bind="css: {'left-sidebar-expanded': layoutmodel.m.s2.expanded, 'right-sidebar-expanded': (layoutmodel.m.s3.expanded() && !layoutmodel.m.s3.hidden())}">
      @yield('content')
    </div>
  </div>

</body>
</html>


<?php /*-------------------------------->
<!-- В. Подключение ресурсов шаблона  -->
<!----------------------------------*/ ?>

  <?php /*----------------------------------------->
  <!-- В1. Принять данные для шаблона с сервера  -->
  <!-------------------------------------------*/ ?>
  <script>

    // 1. Подготовить объект для JS-кода шаблона
    var layout_data = {};

    // 2. Принять данные для шаблона

      // 2.1. Принять csrf_token
      layout_data.csrf_token  = "{{ csrf_token() }}";

      // 2.2. Принять переданные из контроллера данные
      layout_data.data        =  {!! $data !!};

  </script>

  <?php /*---------------------------------------------->
  <!-- В2. Подключение JS-скрипта наследника шаблона  -->
  <!------------------------------------------------*/ ?>

    <?php /* JS наследника шаблона */ ?>
    @yield('js')








