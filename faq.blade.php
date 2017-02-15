<?php /*------>
<!-- 8. FAQ -->
<!------------>
Подоглавление:

  8.1. Контейнер FAQ

    8.1.1. Шапка
    8.1.2. Контент FAQ


-------------------*/ ?>
<div class="faq" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/faq'">

  <?php /*---------------------->
  <!-- 8.1. Контейнер профиля -->
  <!------------------------*/ ?>
  <div class="faq-container">

    <?php /*------------>
    <!-- 8.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-information-outline"></i>
        <span>FAQ</span>
      </div>

    </div>

    <?php /*--------------------------->
    <!-- 8.1.2. Контент ТОПа игроков -->
    <!-----------------------------*/ ?>
    <div class="faq-content">

      FAQ

      <?php /*------------------------------------->
      <!-- n] Модальный щит загрузки со спинером -->
      <!---------------------------------------*/ ?>
      <div class="loader">
        <div style="display: none" class="modal_shield loader-inner ball-clip-rotate" data-bind="visible: m.s5.is_initial_shield_visible">
          <div></div>
        </div>
      </div>

    </div>

  </div>

</div>