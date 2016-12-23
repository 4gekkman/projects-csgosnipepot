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

</head>

<?php /*----------------------------------------->
<!-- Б. Содержимое BODY html-разметки шаблона  -->
<!----------------------------------------------->

  1. Фиксированная шапка сайта
    1.1. Область шапки над чатом и гл.меню (для логотипа)
    1.2. Область шапки с элементами управления
  2. Фиксированное главное меню слева
    2.1. Переключатель главного меню
  3. Фиксированная панель чата справа
    3.1. Панель управления чатом в верхней части
  4. Фиксированная кнопка раскрытия правого чата справа


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
        <img src="{!! asset('public/L10003/assets/images/main_logo.png') !!}">
      </div>

    </div>

    <?php /*------------------------------------------>
    <!-- 1.2. Область шапки с элементами управления -->
    <!--------------------------------------------*/ ?>
    <div class="area2">

      <?php /*--------------------->
      <!-- Интерфейсы для гостей -->
      <!-----------------------*/ ?>

        <?php /*-------------------->
        <!-- 1] Управление звуком -->
        <!----------------------*/ ?>
        <div style="display: none" class="soundcontrol_guest" data-bind="visible: !m.s0.is_logged_in()">
          <img data-bind="visible: m.s4.is_global_volume_on, click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_on.svg') !!}">
          <img data-bind="visible: !m.s4.is_global_volume_on(), click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_off.svg') !!}">
        </div>

        <?php /*------------------>
        <!-- 2] Кнопка "Log in" -->
        <!--------------------*/ ?>
        <div style="display: none" class="login_button" data-bind="visible: !m.s0.is_logged_in()" onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); else popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")>
          <i class="fa fa-fw fa-steam"></i>
          <span>Войти через Steam</span>
        </div>

      <?php /*------------------------------------------------>
      <!-- Интерфейсы для аутентифицированных пользователей -->
      <!--------------------------------------------------*/ ?>

        <?php /*-------------------->
        <!-- 1] Управление звуком -->
        <!----------------------*/ ?>
        <div style="display: none" class="soundcontrol_guest" data-bind="visible: m.s0.is_logged_in">
          <img data-bind="visible: m.s4.is_global_volume_on, click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_on.svg') !!}">
          <img data-bind="visible: !m.s4.is_global_volume_on(), click: f.s4.switch" style="display: none" src="{!! asset('public/L10003/assets/icons/volume_off.svg') !!}">
        </div>

        <?php /*----------------------------------------------------------->
        <!-- 2] Информация об аккаунте аутентифицированного пользователя -->
        <!-------------------------------------------------------------*/ ?>
        <div class="account" data-bind="visible: m.s0.is_logged_in">

          <?php /*----------->
          <!-- 2.1] Аватар -->
          <!-------------*/ ?>
          <div class="avatar">
            <img data-bind="attr: {src: m.s0.auth.user().avatar_steam}" src="http://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/87/8781d4671c68dbbeba1910d7989664dad391c2fc_full.jpg">
          </div>

          <?php /*------------------------------->
          <!-- 2.2] Никнэйм и кнопка "Log out" -->
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
  <div class="menu-container">

    <?php /*---->
    <!-- Меню -->
    <!------*/ ?>
    <div class="menu" data-bind="css: {'menu-hidden': !m.s2.expanded()}">

      <?php /*-------------------------------->
      <!-- 2.1. Переключатель главного меню -->
      <!----------------------------------*/ ?>
      <div class="toggle" data-bind="click: f.s2.switch, visible: !m.s2.hidden()">
        <i class="mdi mdi-chevron-double-left"></i>
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
    <div class="chat" data-bind="css: {'hidden-chat': !m.s3.expanded(), minimized: m.s3.hidden, 'min-chat-not-expanded': !m.s3.expanded()}">

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


    </div>


  </div>

  <?php /*----------------------------------------------------->
  <!-- 4. Фиксированная кнопка раскрытия правого чата справа -->
  <!-------------------------------------------------------*/ ?>
  <div class="chat_right_open" data-bind="click: f.s3.switch, css: {'hidden-chat-button': m.s3.expanded}, visible: !m.s3.hidden()">

    <i class="mdi mdi-wechat"></i>
    <span>Чат</span>

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








