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
          <div style="display: none" class="avatar_and_header" data-bind="if: m.s5.choosen_group(), visible: m.s5.choosen_group()">

            <?php /*------>
            <!-- Аватар -->
            <!--------*/ ?>
            <img data-bind="attr: {src: layoutmodel.m.s0.full_host()+'/'+server.data.public_faq_folder+'/'+m.s5.choosen_group().uri_group_relative()+'/'+m.s5.choosen_group().avatar()}">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <div class="article-header">
              <span data-bind="text: m.s5.choosen_group().name.ru()"></span>
            </div>

          </div>

          <?php /*----------->
          <!-- Сами статьи -->
          <!-------------*/ ?>
          <div style="display: none" data-bind="if: m.s5.choosen_group(), visible: m.s5.choosen_group()">
            <div data-bind="foreach: m.s5.articles()[m.s5.choosen_group().name_folder()]">

              <?php /*------>
              <!-- Статья -->
              <!--------*/ ?>
              <div class="article" data-bind="css: {expanded: is_expanded}">

                <?php /*--------->
                <!-- Заголовок -->
                <!-----------*/ ?>
                <div class="article-header" data-bind="click: $root.f.s5.switch_article">
                  <i class="mdi mdi-chevron-right" data-bind="visible: !is_expanded()"></i>
                  <span data-bind="text: name.ru"></span>
                </div>

                <?php /*-------------->
                <!-- Контент статьи -->
                <!----------------*/ ?>
                <div class="article-content" data-bind="html: html.ru, css: {expanded: is_expanded}"></div>

              </div>

            </div>
          </div>

          <?php /*---------------------------------------------------->
          <!-- Если запрошенной через query string группы нет в FAQ -->
          <!------------------------------------------------------*/ ?>
          <div class="wrong_group" style="display: none" data-bind="visible: !m.s5.choosen_group()">
            <span>Запрошенная группа статей отсутствует в FAQ. Выберите одну из доступных групп справа, кликнув по ней.</span>
          </div>

        </td>

        <?php /*----------->
        <!-- 1.2] Группы -->
        <!-------------*/ ?>
        <td class="faq-groups" data-bind="foreach: m.s5.groups">

          <?php /*------>
          <!-- Группа -->
          <!--------*/ ?>
          <div class="group" data-bind="css: {choosen: $data == $root.m.s5.choosen_group()}, click: $root.f.s5.get_faq.bind($data, false, name_folder())">

            <?php /*--------->
            <!-- Заголовок -->
            <!-----------*/ ?>
            <span data-bind="text: name.ru()"></span>

            <?php /*---------------->
            <!-- Спиннер загрузки -->
            <!------------------*/ ?>
            <div class="loader">
              <div style="display: none" class="group_articles_load_spinner loader-inner ball-clip-rotate" data-bind="visible: is_spinner_visible">
                <div></div>
              </div>
            </div>

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