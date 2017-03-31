<?php /*---------------------------->
<!-- 11. Пополнить баланс скинами -->
<!---------------------------------->
Подоглавление:

  11.1. Контейнер интерфейса

    11.1.1. Шапка
    11.1.2. Контент интерфейса

-------------------*/ ?>
<div class="deposit" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/deposit'">

  <?php /*-------------------------->
  <!-- 11.1. Контейнер интерфейса -->
  <!----------------------------*/ ?>
  <div class="deposit-container">

    <?php /*------------>
    <!-- 11.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="logo_and_name">
        <i class="mdi mdi-plus-circle-outline"></i>
        <span>Пополнить баланс скинами</span>
        <span style="display: none" class="balance" data-bind="text: 'Баланс: '+layoutmodel.m.s0.balance()"></span>
      </div>

    </div>

    <?php /*-------------------------->
    <!-- 11.1.2. Контент интерфейса -->
    <!----------------------------*/ ?>
    <div class="deposit-content"><table class="depo-table"><tbody><tr>

      <?php /*----------------------->
      <!-- 11.1.2.1. Левая колонка -->
      <!-------------------------*/ ?>
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
            <ul class="sort-interface" data-bind="foreach: m.s6.sort.criterias.list">

              <?php /*--------------------------------->
              <!-- Критерий и направление сортировки -->
              <!-----------------------------------*/ ?>
              <li data-bind="css: {'choosen': $root.m.s6.sort.criterias.choosen().name() == name()}, click: $root.f.s6.choose_sort.bind($data)">
                <span data-bind="text: text"></span>
                <div class="sortdir">
                  <i style="display: none" class="mdi mdi-menu-up" data-bind="visible: $root.m.s6.sort.criterias.choosen().name() == name() && $root.m.s6.sort.directions.choosen().name() == 'asc'"></i>
                  <i style="display: none" class="mdi mdi-menu-down" data-bind="visible: $root.m.s6.sort.criterias.choosen().name() == name() && $root.m.s6.sort.directions.choosen().name() == 'desc'"></i>
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
            <div class="pagi-button" data-bind="click: f.s6.pagi_prev, css: {disabled: m.s6.deposit.pagi.pagenum() == 1}">
              <i class="mdi mdi-chevron-left"></i>
            </div>

            <?php /*---------------------------->
            <!-- 2.2] Кнопка пагинации вправо -->
            <!------------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s6.pagi_next, css: {disabled: m.s6.deposit.pagi.pagenum() == m.s6.deposit.pagi.count()}">
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
            <input type="text" placeholder="" data-bind="textInput: m.s6.deposit.left.searchinput">
            <i class="mdi mdi-magnify"></i>

          </div>

        </div>

        <?php /*------------>
        <!-- 2] Инвентарь -->
        <!--------------*/ ?>
        <div class="inventory">

          <?php /*-------------------------------->
          <!-- 2.1] Остающиеся в инвентаре вещи -->
          <!----------------------------------*/ ?>
          <div style="display: none" class="deposit-left-items-cont" data-bind="foreach: m.s6.deposit.left.items_at_curpage, visible: m.s6.deposit.left.items_filtered_sorted_paginated().length">

            <?php /*---->
            <!-- Вещь -->
            <!------*/ ?>
            <div class="item" data-bind="click: $root.f.s6.move_to_mybet">

              <?php /*----------------------------------->
              <!-- 1) Цветовая индикация качества вещи -->
              <!-------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s6.get_cat_quality_item_color($data)}" style="background: transparent;"></div>

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
                <span data-bind="text: Math.round(price()*100*((100 - server.data.deposit_configs.skin_price2accept_spread_in_perc)/100))"></span>
              </div>

              <?php /*----------->
              <!-- 4) Название -->
              <!-------------*/ ?>
              <div class="marketname">
                <span data-bind="text: market_name"></span>
              </div>

            </div>

          </div>

          <?php /*---------------------------------------------------------------->
          <!-- 2.2] Если в инвентаре нет вещей, которыми можно пополнить баланс -->
          <!------------------------------------------------------------------*/ ?>
          <div style="display: none" class="there_is_no_items" data-bind="visible: !m.s6.deposit.left.items_filtered_sorted_paginated().length">
            <span>Подходящих вещей нет</span><br>
          </div>

          <?php /*--------------------------------------->
          <!-- 2.n] Модальный щит загрузки со спинером -->
          <!-----------------------------------------*/ ?>
          <div class="loader">
            <div style="display: none" class="modal_shield loader-inner ball-clip-rotate modal-inv" data-bind="visible: m.s6.is_inv_loading_shield_visible">
              <div></div>
            </div>
          </div>

        </div>

        <?php /*------------------------------------------------------>
        <!-- 3] Доп.элементы управления в левом столбце (в подвале) -->
        <!--------------------------------------------------------*/ ?>
        <div class="deposit-footer">

          <?php /*-------------->
          <!-- 3.1] Пагинация -->
          <!----------------*/ ?>
          <div class="main-pagi">

            <?php /*------------------------->
            <!-- 1) Кнопка пагинации влево -->
            <!---------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s6.pagi_prev, css: {disabled: m.s6.deposit.pagi.pagenum() == 1}">
              <i class="mdi mdi-chevron-left"></i>
            </div>

            <?php /*-------------------------->
            <!-- 2) Кнопка пагинации вправо -->
            <!----------------------------*/ ?>
            <div class="pagi-button" data-bind="click: f.s6.pagi_next, css: {disabled: m.s6.deposit.pagi.pagenum() == m.s6.deposit.pagi.count()}">
              <i class="mdi mdi-chevron-right"></i>
            </div>

          </div>

          <?php /*-------------------------------->
          <!-- 3.2] Кнопка "Обновить инвентарь" -->
          <!----------------------------------*/ ?>
          <div class="upd-inv-button" data-bind="click: f.s6.update_inventory.bind($data, true, true)">
            <span>Обновить инвентарь</span>
          </div>

        </div>

      </td>

      <?php /*------------------------>
      <!-- 11.1.2.2. Правая колонка -->
      <!--------------------------*/ ?>
      <td class="right-col">

        <?php /*------------------------------------------------------------------>
        <!-- 1] Итоговое кол-во монет, которые получит игрок за выбранные скины -->
        <!--------------------------------------------------------------------*/ ?>
        <div class="total-coins">
          <img src="{!! asset('public/D10009/assets/icons/coins/coins_v5.svg') !!}">
          <span data-bind="text: m.s6.deposit.choosen.items_value_in_coins"></span>
        </div>

        <?php /*------------------------------->
        <!-- 2] Выбранные для депозита скины -->
        <!---------------------------------*/ ?>
        <div class="choosen-skins">

          <?php /*------------------------------------------>
          <!-- 2.1] Выбранные для пополнения баланса вещи -->
          <!--------------------------------------------*/ ?>
          <div class="deposit-choosen-items-cont" data-bind="foreach: m.s6.deposit.choosen.items">

            <?php /*---->
            <!-- Вещь -->
            <!------*/ ?>
            <div class="item" data-bind="click: $root.f.s6.move_to_myinventory">

              <?php /*----------------------------------->
              <!-- 1) Цветовая индикация качества вещи -->
              <!-------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s6.get_cat_quality_item_color($data)}" style="background: transparent;"></div>

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
                <span data-bind="text: Math.round(price()*100*((100 - server.data.deposit_configs.skin_price2accept_spread_in_perc)/100))"></span>
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
        <div class="deposit-footer">

          <?php /*---------------->
          <!-- Кнопка "Депозит" -->
          <!------------------*/ ?>
          <div class="depo-button" data-bind="click: f.s6.deposit">
            <span>Пополнить</span>
          </div>

        </div>

      </td>

    </tr></tbody></table></div>

  </div>

</div>