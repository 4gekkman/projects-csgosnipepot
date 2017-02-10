<?php /*-------------->
<!-- 7. ТОП игроков -->
<!-------------------->
Подоглавление:

  7.1. Контейнер ТОПа игроков

    7.1.1. Шапка
    7.1.2. Контент ТОПа игроков


-------------------*/ ?>
<div class="profile" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/top'">

  <?php /*---------------------->
  <!-- 5.1. Контейнер профиля -->
  <!------------------------*/ ?>
  <div class="top-container">

    <?php /*------------>
    <!-- 5.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name" data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'game'}, click: f.s1.choose_tab.bind($data, 'game')">
        <i class="mdi mdi-star-outline"></i>
        <span>ТОП игроков</span>
      </div>

    </div>

    <?php /*--------------------------->
    <!-- 5.1.2. Контент ТОПа игроков -->
    <!-----------------------------*/ ?>
    <div class="top-content">

      123

    </div>

  </div>

</div>