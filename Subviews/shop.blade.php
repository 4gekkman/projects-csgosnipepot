<?php /*----------------->
<!-- 4. Магазин скинов -->
<!----------------------->
Подоглавление:

  4.1. Контейнер интерфейса

    4.1.1. Шапка
    4.1.2. Контент интерфейса

-------------------*/ ?>
<div class="shop" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/shop'">

  <?php /*------------------------->
  <!-- 4.1. Контейнер интерфейса -->
  <!---------------------------*/ ?>
  <div class="shop-container">

    <?php /*------------>
    <!-- 4.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">

        <?php /*--------------------->
        <!-- 1] Аватар и заголовок -->
        <!-----------------------*/ ?>
        <i class="mdi mdi-shopping"></i>
        <span>Магазин</span>

        <?php /*------------------------------>
        <!-- 2] Баланс и кнопка "Пополнить" -->
        <!--------------------------------*/ ?>
        <div class="balance" data-bind="click: function(){ layoutmodel.m.s7.ison(true); }" title="Пополнить баланс">
          <i class="mdi mdi-plus-circle-outline"></i>
          <span data-bind="text: 'Баланс: '+layoutmodel.m.s0.balance()"></span>
        </div>

      </div>

    </div>

    <?php /*------------------------->
    <!-- 4.1.2. Контент интерфейса -->
    <!---------------------------*/ ?>
    <div class="shop-content"><table class="shop-table"><tbody><tr>

      <?php /*---------------------->
      <!-- 4.1.2.1. Левая колонка -->
      <!------------------------*/ ?>
      <td class="left-col">

        <?php /*------------------------->
        <!-- 1] Сортировка и пагинация -->
        <!---------------------------*/ ?>
        <div class="sort_and_pagi">

          <?php /*--------------->
          <!-- 1.1] Сортировка -->
          <!-----------------*/ ?>
          <div class="sort">

            <?php /*---------------->
            <!-- 1.1.1] Заголовок -->
            <!------------------*/ ?>
            <span>Сортировка по: </span>

            <?php /*--------------------------->
            <!-- 1.2.2] Интерфейс сортировки -->
            <!-----------------------------*/ ?>
            <ul class="sort-interface" data-bind="foreach: m.s7.sort.criterias.list">

              <?php /*--------------------------------->
              <!-- Критерий и направление сортировки -->
              <!-----------------------------------*/ ?>
              <li data-bind="css: {'choosen': $root.m.s7.sort.criterias.choosen().name() == name()}, click: $root.f.s7.choose_sort.bind($data)">
                <span data-bind="text: text"></span>
                <div class="sortdir">
                  <i style="display: none" class="mdi mdi-menu-up" data-bind="visible: $root.m.s7.sort.criterias.choosen().name() == name() && $root.m.s7.sort.directions.choosen().name() == 'asc'"></i>
                  <i style="display: none" class="mdi mdi-menu-down" data-bind="visible: $root.m.s7.sort.criterias.choosen().name() == name() && $root.m.s7.sort.directions.choosen().name() == 'desc'"></i>
                </div>
              </li>

            </ul>

          </div>

          <?php /*------------>
          <!-- 2] Пагинация -->
          <!--------------*/ ?>
          <div style="display: none" class="main-pagi">

            <?php /*--------------------------->
            <!-- 2.1] Кнопка пагинации влево -->
            <!-----------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s7.pagi_prev, css: {disabled: m.s7.shop.pagi.pagenum() == 1}">
              <i class="mdi mdi-chevron-left"></i>
            </div>

            <?php /*---------------------------->
            <!-- 2.2] Кнопка пагинации вправо -->
            <!------------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s7.pagi_next, css: {disabled: m.s7.shop.pagi.pagenum() == m.s7.shop.pagi.count()}">
              <i class="mdi mdi-chevron-right"></i>
            </div>

          </div>

          <?php /*-------->
          <!-- 3] Поиск -->
          <!----------*/ ?>
          <div class="search-input">

            <?php /*------------------>
            <!-- 3.1] Строка поиска -->
            <!--------------------*/ ?>
            <input type="text" placeholder="" data-bind="textInput: m.s7.shop.left.searchinput">
            <i class="mdi mdi-magnify"></i>

          </div>

        </div>

        <?php /*--------->
        <!-- 2] Товары -->
        <!-----------*/ ?>
        <div class="goods">

          <?php /*--------------------------------->
          <!-- 2.1] Остающиеся "на полке" товары -->
          <!-----------------------------------*/ ?>
          <div style="display: none" class="shop-left-items-cont" data-bind="foreach: m.s7.shop.left.items_at_curpage, visible: m.s7.shop.left.items_filtered_sorted_paginated().length">

            <?php /*---->
            <!-- Вещь -->
            <!------*/ ?>
            <div class="item" data-bind="click: $root.f.s7.move_to_cart">

              <?php /*----------------------------------->
              <!-- 1) Цветовая индикация качества вещи -->
              <!-------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s7.get_cat_quality_item_color($data)}" style="background: transparent;"></div>

              <?php /*-------------->
              <!-- 2) Изображение -->
              <!----------------*/ ?>
              <div class="img_cont">
                <img data-bind="attr: {src: icon_url() + ' 2x'}">
              </div>

              <?php /*---------------------->
              <!-- 3) Стоимость в монетах -->
              <!------------------------*/ ?>
              <div class="value_in_coins">
                <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
                <span data-bind="text: Math.round(price()*100)"></span>
              </div>

              <?php /*----------->
              <!-- 4) Название -->
              <!-------------*/ ?>
              <div class="marketname">
                <span data-bind="text: market_name"></span>
              </div>

            </div>

          </div>

          <?php /*--------------------->
          <!-- 2.2] Если товаров нет -->
          <!-----------------------*/ ?>
          <div style="display: none" class="there_is_no_items" data-bind="visible: !m.s7.shop.left.items_filtered_sorted_paginated().length">
            <span>Магазин пуст</span><br>
          </div>

          <?php /*--------------------------------------->
          <!-- 2.n] Модальный щит загрузки со спинером -->
          <!-----------------------------------------*/ ?>
          <div class="loader">
            <div style="display: none" class="modal_shield loader-inner ball-clip-rotate modal-inv" data-bind="visible: m.s7.is_goods_loading_shield_visible">
              <div></div>
            </div>
          </div>

        </div>

        <?php /*------------------------------------------------------>
        <!-- 3] Доп.элементы управления в левом столбце (в подвале) -->
        <!--------------------------------------------------------*/ ?>
        <div class="shop-footer">

          <?php /*-------------->
          <!-- 3.1] Пагинация -->
          <!----------------*/ ?>
          <div class="main-pagi">

            <?php /*------------------------->
            <!-- 1) Кнопка пагинации влево -->
            <!---------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s7.pagi_prev, css: {disabled: m.s7.shop.pagi.pagenum() == 1}">
              <i class="mdi mdi-chevron-left"></i>
            </div>

            <?php /*-------------------------->
            <!-- 2) Кнопка пагинации вправо -->
            <!----------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s7.pagi_next, css: {disabled: m.s7.shop.pagi.pagenum() == m.s7.shop.pagi.count()}">
              <i class="mdi mdi-chevron-right"></i>
            </div>

          </div>

        </div>

      </td>

      <?php /*----------------------->
      <!-- 4.1.2.2. Правая колонка -->
      <!-------------------------*/ ?>
      <td class="right-col">

        <?php /*------------------------------------------------------------------>
        <!-- 1] Итоговое кол-во монет, которые получит игрок за выбранные скины -->
        <!--------------------------------------------------------------------*/ ?>
        <div class="total-coins">
          <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
          <span data-bind="text: m.s7.shop.choosen.items_value_in_coins"></span>
        </div>

        <?php /*----------------->
        <!-- 2] Вещи в корзине -->
        <!-------------------*/ ?>
        <div class="choosen-skins">

          <?php /*------------------->
          <!-- 2.1] Вещи в корзине -->
          <!---------------------*/ ?>
          <div class="shop-choosen-items-cont" data-bind="foreach: m.s7.shop.choosen.items">

            <?php /*---->
            <!-- Вещь -->
            <!------*/ ?>
            <div class="item" data-bind="click: $root.f.s7.remove_from_cart">

              <?php /*----------------------------------->
              <!-- 1) Цветовая индикация качества вещи -->
              <!-------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s7.get_cat_quality_item_color($data)}" style="background: transparent;"></div>

              <?php /*-------------->
              <!-- 2) Изображение -->
              <!----------------*/ ?>
              <div class="img_cont">
                <img data-bind="attr: {src: icon_url() + ' 2x'}">
              </div>

              <?php /*---------------------->
              <!-- 3) Стоимость в монетах -->
              <!------------------------*/ ?>
              <div class="value_in_coins">
                <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
                <span data-bind="text: Math.round(price()*100)"></span>
              </div>

              <?php /*----------->
              <!-- 4) Название -->
              <!-------------*/ ?>
              <div class="marketname">
                <span data-bind="text: market_name"></span>
              </div>

            </div>

          </div>

        </div>

        <?php /*------------------------------------------------------->
        <!-- 3] Доп.элементы управления в правом столбце (в подвале) -->
        <!---------------------------------------------------------*/ ?>
        <div class="shop-footer">

          <?php /*--------------->
          <!-- Кнопка "Купить" -->
          <!-----------------*/ ?>
          <div class="buy-button" data-bind="click: f.s7.buy">
            <span>Купить</span>
          </div>

        </div>

      </td>

    </tr></tbody></table></div>

  </div>

</div>