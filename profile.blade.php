<?php /*----------------------->
<!-- 5. Профиль пользователя -->
<!----------------------------->
Подоглавление:

  5.1. Контейнер профиля

    5.1.1. Шапка
    5.1.2. Контент профиля


-------------------*/ ?>
<div class="profile" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/profile'">

  <?php /*---------------------->
  <!-- 5.1. Контейнер профиля -->
  <!------------------------*/ ?>
  <div class="pf-container">

    <?php /*------------>
    <!-- 5.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name" data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'game'}, click: f.s1.choose_tab.bind($data, 'game')">
        <i class="mdi mdi-account"></i>
        <span>Профиль</span>
      </div>

    </div>

    <?php /*---------------------->
    <!-- 5.1.2. Контент профиля -->
    <!------------------------*/ ?>
    <div class="profile-content">

      <?php /*-------------------------------------------------------------->
      <!-- Информация об аккаунте аутентифицированного Steam-пользователя -->
      <!----------------------------------------------------------------*/ ?>
      <div style="display: none" class="user-acc-info" data-bind="visible: m.s0.is_logged_in">

        <?php /*---------------------->
        <!-- Информация об аккаунте -->
        <!------------------------*/ ?>
        <div>

          <?php /*-------->
          <!-- Аватарка -->
          <!----------*/ ?>
          <div class="ava">
            <img src="http://placehold.it/100x100/fafafa?text=avatar" data-bind="attr: {src: m.s0.auth.user().avatar_steam}">
          </div>

          <?php /*------------------------->
          <!-- Дополнительная информация -->
          <!---------------------------*/ ?>
          <div class="userinfo">

            <?php /*---------->
            <!-- Steam Name -->
            <!------------*/ ?>
            <div>
              <span class="mini-header">Steam Name</span>
              <input type="text" placeholder="Steam Name" data-bind="textInput: m.s0.auth.user().nickname" disabled="">
            </div>

            <?php /*-------->
            <!-- Steam ID -->
            <!----------*/ ?>
            <div>
              <span class="mini-header">Steam ID</span>
              <input type="text" placeholder="Steam ID" data-bind="textInput: m.s0.auth.user().ha_provider_uid" disabled="">
            </div>

          </div>

        </div>

      </div>

      <?php /*---------------------------------->
      <!-- Кнопки "Login via Steam" и "Logout"-->
      <!------------------------------------*/ ?>
      <div class="log-in-out">

        <?php /*--------->
        <!-- Заголовок -->
        <!-----------*/ ?>
        <div>
          <span style="display: none" data-bind="visible: !m.s0.is_logged_in()">Войти, используя свой аккаунт в Steam</span>
          <span style="display: none" data-bind="visible: m.s0.is_logged_in">Выйти из своего аккаунта</span>
        </div>

        <?php /*------>
        <!-- Кнопки -->
        <!--------*/ ?>
        <div class="button-styles">

          <?php /*----->
          <!-- Login -->
          <!-------*/ ?>
          <button style="display: none" data-bind="visible: !m.s0.is_logged_in()" onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); else popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")>
            <i class="fa fa-fw fa-steam"></i>
            <span>Войти через Steam</span>
          </button>

          <?php /*------>
          <!-- Logout -->
          <!--------*/ ?>
          <button style="display: none" data-bind="visible: m.s0.is_logged_in, click: f.s5.logout">
            <i class="fa fa-fw fa-sign-out"></i>
            <span>Выйти</span>
          </button>

        </div>
      </div>

      <?php /*------------>
      <!-- Торговый URL -->
      <!--------------*/ ?>
      <div class="trade-url" style="display: none" data-bind="visible: m.s0.is_logged_in">

        <?php /*--------->
        <!-- Заголовок -->
        <!-----------*/ ?>
        <div>
          <span>Указать свой торговый URL в Steam (</span><a style="border-bottom: 1px solid #555;" target="_blank" href="http://steamcommunity.com/my/tradeoffers/privacy#trade_offer_access_url">посмотреть его можно здесь, в самом низу</a><span>)</span>
        </div>

        <?php /*------------------------>
        <!-- Поле для ввода Trade URL -->
        <!--------------------------*/ ?>
        <div>
          <input type="text" placeholder="Твой торговый URL в Steam" data-bind="textInput: m.s5.steam_tradeurl">
        </div>

        <?php /*------>
        <!-- Кнопка -->
        <!--------*/ ?>
        <div class="button-styles">
          <button data-bind="click: f.s5.save_steam_tradeurl">
            <span>Изменить</span>
          </button>
        </div>

      </div>



    </div>

  </div>

</div>