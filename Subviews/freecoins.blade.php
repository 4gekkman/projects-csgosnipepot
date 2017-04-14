<?php /*-------------->
<!-- 10. Free Coins -->
<!-------------------->
Подоглавление:

  10.1. Контейнер Free Coins

    10.1.1. Шапка
    10.1.2. Контент Free Coins
      10.1.2.1. Ежедневная награда
      10.1.2.2. Будь онлайн
      10.1.2.3. Добавь CSGOHAP.RU к своему никнейму в Steam
      10.1.2.4. Вступи в нашу группу в Steam

-------------------*/ ?>
<div class="fc" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/free'">

  <?php /*-------------------------->
  <!-- 10.1. Контейнер Free Coins -->
  <!----------------------------*/ ?>
  <div class="fc-container">

    <?php /*------------->
    <!-- 10.1.1. Шапка -->
    <!---------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-coin"></i>
        <span>Free Coins</span>
      </div>

    </div>

    <?php /*-------------------------->
    <!-- 10.1.2. Контент Free Coins -->
    <!----------------------------*/ ?>
    <div class="fc-content">

      <?php /*---------------------------->
      <!-- 10.1.2.1. Ежедневная награда -->
      <!------------------------------*/ ?>
      <div class="fc-block daily-reward">

        <?php /*------------>
        <!-- 1] Заголовок -->
        <!--------------*/ ?>
        <div class="fc-header">
          <span>ЕЖЕДНЕВНАЯ НАГРАДА</span>
        </div>

        <?php /*-------------------->
        <!-- 2] Кнопка "Получить" -->
        <!----------------------*/ ?>
        <div style="display: none" class="fc-get-button" data-bind="visible: m.s8.reword.is_got_reword() == 0, click: f.s8.get_freecoins">
          <span>Получить</span>
          <div style="display: none" class="loader" data-bind="visible: m.s8.reword.is_spinner_vis">
            <div class="loader-inner ball-clip-rotate">
              <div></div>
            </div>
          </div>
        </div>

        <?php /*------------------------------------->
        <!-- 3] Оставшееся до следующего дня время -->
        <!---------------------------------------*/ ?>
        <div style="display: none" class="fc-time-until-next-day" data-bind="visible: m.s8.reword.is_got_reword() == 1">
          <span data-bind="text: m.s8.reword.time_until_next_day"></span>
        </div>

        <?php /*-------------------------->
        <!-- 4] Картинка в центре блока -->
        <!----------------------------*/ ?>
        <img class="fc-center-img" src="{!! asset('public/D10009/assets/images/money.png') !!}">

        <?php /*------------------------------>
        <!-- 5] Количество бесплатных монет -->
        <!--------------------------------*/ ?>
        <div class="fc-coins">
          <span data-bind="text: m.s8.reword.coins() + ' ' + m.s8.reword.declension()"></span>
        </div>

      </div>

      <?php /*--------------------->
      <!-- 10.1.2.2. Будь онлайн -->
      <!-----------------------*/ ?>
      <div class="fc-block beonline">

        <?php /*------------>
        <!-- 1] Заголовок -->
        <!--------------*/ ?>
        <div class="fc-header">
          <span>БУДЬ ОНЛАЙН</span>
        </div>

        <?php /*---------------->
        <!-- 2] Ссылка на FAQ -->
        <!------------------*/ ?>
        <!--<i class="help mdi mdi-help-circle" data-bind="click: f.s5.open_faq_article.bind($data, {faq_url: '/faq', group: 'freecoins', article: 'beonline'})"></i>-->

        <?php /*------------------->
        <!-- 3] Оставшееся время -->
        <!---------------------*/ ?>
        <span style="display: none" class="timer" data-bind="visible: !m.s8.beonline.giveaway().id, text: m.s8.beonline.left4giveaway.human"></span>

        <?php /*-------------------------->
        <!-- 4] Картинка в центре блока -->
        <!----------------------------*/ ?>
        <div data-bind="if: m.s8.beonline.giveaway().id">
          <img class="center-img" data-bind="attr: {src: m.s8.beonline.giveaway().m8_items()[0].steammarket_image}">
        </div>

        <?php /*-------------------->
        <!-- 2] Кнопка "Получить" -->
        <!----------------------*/ ?>
        <div style="display: none" class="fc-get-button" data-bind="visible: m.s8.beonline.giveaway().id, click: f.s8.create_giveaway_offer">
          <span>Получить</span>
          <div style="display: none" class="loader" data-bind="visible: m.s8.beonline.is_spinner_vis">
            <div class="loader-inner ball-clip-rotate">
              <div></div>
            </div>
          </div>
        </div>

      </div>

      <?php /*----------------------------------------------------->
      <!-- 10.1.2.3. Добавь CSGOHAP.RU к своему никнейму в Steam -->
      <!-------------------------------------------------------*/ ?>
      <div style="display: none" class="fc-block nick-promo" data-bind="visible: !m.s8.nickpromo.is_paid()">

        <?php /*------------>
        <!-- 1] Заголовок -->
        <!--------------*/ ?>
        <div class="fc-header">
          <span>ДОБАВЬ CSGOHAP.RU К СВОЕМУ НИКНЕЙМУ В STEAM</span>
        </div>

        <?php /*-------------------->
        <!-- 2] Кнопка "Получить" -->
        <!----------------------*/ ?>
        <div class="fc-get-button" data-bind="click: f.s8.apply_nick_promo">
          <span>Получить</span>
          <div style="display: none" class="loader" data-bind="visible: m.s8.nickpromo.is_spinner_vis">
            <div class="loader-inner ball-clip-rotate">
              <div></div>
            </div>
          </div>
        </div>

        <?php /*-------------------------->
        <!-- 3] Картинка в центре блока -->
        <!----------------------------*/ ?>
        <img class="fc-center-img" src="{!! asset('public/D10009/assets/images/gift-1.png') !!}">

        <?php /*---------------------------->
        <!-- 4] Кнопка "Изменить никнейм" -->
        <!------------------------------*/ ?>
        <div class="change-nick-href">
          <a target="_blank" data-bind="attr: {href: 'http://steamcommunity.com/profiles/'+(layoutmodel.m.s0.auth.is_anon() ? '' : layoutmodel.m.s0.auth.user().ha_provider_uid())+'/edit'}">Изменить никнейм</a>
        </div>

        <?php /*------------------------------>
        <!-- 5] Количество бесплатных монет -->
        <!--------------------------------*/ ?>
        <div class="fc-coins">
          <span data-bind="text: m.s8.nickpromo.coins() + ' ' + m.s8.nickpromo.declension()"></span>
        </div>

      </div>

      <?php /*-------------------------------------->
      <!-- 10.1.2.4. Вступи в нашу группу в Steam -->
      <!----------------------------------------*/ ?>
      <div style="display: none" class="fc-block steam-group-promo" data-bind="visible: !m.s8.steamgrouppromo.is_paid()">

        <?php /*------------>
        <!-- 1] Заголовок -->
        <!--------------*/ ?>
        <div class="fc-header">
          <span>ВСТУПИ В ГРУППУ В STEAM</span>
        </div>

        <?php /*-------------------->
        <!-- 2] Кнопка "Получить" -->
        <!----------------------*/ ?>
        <div class="fc-get-button" data-bind="click: f.s8.apply_steamgroup_promo">
          <span>Получить</span>
          <div style="display: none" class="loader" data-bind="visible: m.s8.steamgrouppromo.is_spinner_vis">
            <div class="loader-inner ball-clip-rotate">
              <div></div>
            </div>
          </div>
        </div>

        <?php /*-------------------------->
        <!-- 3] Картинка в центре блока -->
        <!----------------------------*/ ?>
        <img class="fc-center-img" src="{!! asset('public/D10009/assets/images/steam.png') !!}">

        <?php /*----------------------------->
        <!-- 4] Кнопка "Вступить в группу" -->
        <!-------------------------------*/ ?>
        <div class="change-nick-href">
          <a target="_blank" data-bind="attr: {href: 'http://steamcommunity.com/groups/CSGOHAP'}">Вступить в группу</a>
        </div>

        <?php /*------------------------------>
        <!-- 5] Количество бесплатных монет -->
        <!--------------------------------*/ ?>
        <div class="fc-coins">
          <span data-bind="text: m.s8.steamgrouppromo.coins() + ' ' + m.s8.steamgrouppromo.declension()"></span>
        </div>

      </div>

    </div>

  </div>

</div>