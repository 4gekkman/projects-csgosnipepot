<?php /*-------------->
<!-- 7. ТОП игроков -->
<!-------------------->
Подоглавление:

  7.1. Контейнер ТОПа игроков

    7.1.1. Шапка
    7.1.2. Контент ТОПа игроков


-------------------*/ ?>
<div class="top" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/top'">

  <?php /*--------------------------->
  <!-- 7.1. Контейнер ТОПа игроков -->
  <!-----------------------------*/ ?>
  <div class="top-container">

    <?php /*------------>
    <!-- 7.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-star-outline"></i>
        <span>ТОП игроков</span>
      </div>

    </div>

    <?php /*--------------------------->
    <!-- 7.1.2. Контент ТОПа игроков -->
    <!-----------------------------*/ ?>
    <div class="top-content">

      <?php /*--------------------------------->
      <!-- 1] Список заголовков ТОПа игроков -->
      <!-----------------------------------*/ ?>
      <div class="top-headers">

        <div class="top-header" style="width: 6%; padding-left: 20px; overflow: visible; z-index: 1;"><span>Место</span></div>
        <div class="top-header" style="width: 6%;"><span></span></div>
        <div class="top-header" style="width: 52%; padding-left: 20px;"><span>Ник в Steam</span></div>
        <div class="top-header" style="width: 12%; text-align: left;"><span>Всего игр</span></div>
        <div class="top-header" style="width: 12%; text-align: left;"><span>Победы</span></div>
        <div class="top-header" style="width: 12%; text-align: left;"><span>Выиграл</span></div>

      </div>


      <?php /*------------------------------>
      <!-- 2] Список позиций ТОПа игроков -->
      <!--------------------------------*/ ?>
      <div class="top-items" data-bind="foreach: m.s4.top">

        <?php /*---------------------->
        <!-- Позиция в ТОПе игроков -->
        <!------------------------*/ ?>
        <div class="top-item" data-bind="css: {odd: !(($index()+1)%2)}">

          <?php /*---------->
          <!-- 2.1] Номер -->
          <!------------*/ ?>
          <div class="num">
            <span data-bind="text: ($index()+1)"></span>
          </div>

          <?php /*----------->
          <!-- 2.2] Аватар -->
          <!-------------*/ ?>
          <div class="ava">
            <img data-bind="attr: {src: avatar_steam}">
          </div>

          <?php /*---------------->
          <!-- 2.3] Ник в Steam -->
          <!------------------*/ ?>
          <div class="nick">
            <a target="_blank" data-bind="text: nickname, attr: {href: 'http://steamcommunity.com/profiles/'+steamid()}"></a>
          </div>

          <?php /*-------------->
          <!-- 2.4] Всего игр -->
          <!----------------*/ ?>
          <div class="games">
            <span data-bind="text: rounds_num"></span>
          </div>

          <?php /*----------->
          <!-- 2.5] Победы -->
          <!-------------*/ ?>
          <div class="wins">
            <span data-bind="text: wins_num"></span>
          </div>

          <?php /*--------------------->
          <!-- 2.6] Выигранная сумма -->
          <!-----------------------*/ ?>
          <div class="totalsum">
            <span data-bind="text: Math.ceil((totalsum()/100)*server.data.usdrub_rate) + ' руб.'"></span>
          </div>

        </div>

      </div>


      <?php /*------------------------------------->
      <!-- n] Модальный щит загрузки со спинером -->
      <!---------------------------------------*/ ?>
      <div class="loader">
        <div style="display: none" class="modal_shield loader-inner ball-clip-rotate" data-bind="visible: m.s4.is_shield_visible">
          <div></div>
        </div>
      </div>

    </div>

  </div>

</div>