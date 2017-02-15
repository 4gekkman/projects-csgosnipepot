<?php /*------>
<!-- 8. FAQ -->
<!------------>
Подоглавление:

  8.1. Контейнер FAQ

    8.1.1. Шапка
    8.1.2. Контент FAQ


-------------------*/ ?>
<div class="faq" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/faq'">

  <?php /*------------------->
  <!-- 8.1. Контейнер FAQа -->
  <!---------------------*/ ?>
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

    <?php /*------------------->
    <!-- 8.1.2. Контент FAQа -->
    <!---------------------*/ ?>
    <div class="faq-content">

      <?php /*--------------------------->
      <!-- 1] Таблица с контентом FAQа -->
      <!-----------------------------*/ ?>
      <table class="faq-table"><tbody><tr>

        <?php /*----------->
        <!-- 1.1] Статьи -->
        <!-------------*/ ?>
        <td class="faq-articles">

          <?php /*----------------------------------->
          <!-- Аватар и заголовок выбранной группы -->
          <!-------------------------------------*/ ?>
          <div class="avatar_and_header" data-bind="if: m.s5.choosen_group">

            <?php /*------>
            <!-- Аватар -->
            <!--------*/ ?>
            <img src="http://placehold.it/40x40">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="article-header">
              <span data-bind="text: m.s5.choosen_group().name.ru()"></span>
            </div>

          </div>


        </td>

        <?php /*----------->
        <!-- 1.2] Группы -->
        <!-------------*/ ?>
        <td class="faq-groups" data-bind="foreach: m.s5.groups">

          <?php /*------>
          <!-- Группа -->
          <!--------*/ ?>
          <div class="group" data-bind="css: {choosen: $data == $root.m.s5.choosen_group()}">
            <span data-bind="text: name.ru()"></span>
          </div>

        </td>

      </tr></tbody></table>

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