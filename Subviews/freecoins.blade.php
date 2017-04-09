<?php /*-------------->
<!-- 10. Free Coins -->
<!-------------------->
Подоглавление:

  10.1. Контейнер Free Coins

    10.1.1. Шапка
    10.1.2. Контент Free Coins


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
        <span class="timer" data-bind="text: m.s8.beonline.left4giveaway.human"></span>




      </div>

    </div>

  </div>

</div>