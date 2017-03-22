<?php /*--------------->
<!-- 1. Classic game -->
<!--------------------->
Подоглавление:

  1.1. Контейнер классической игры

    1.1.1. Шапка
    1.1.2. Контент классической игры

      1.1.2.1. Номер игры и банк
      1.1.2.2. Панель интерфейса игрового процесса

        1.1.2.2.1. Счётчики предметов и времени текущего раунда
        1.1.2.2.2. Органы управления игрой для игрока
        1.1.2.2.3. Бегущая полоса с аватарками
        1.1.2.2.4. Инфо-панель для состояний "Lottery" и "Winner"

      1.1.2.3. Распределение шансов на выигрыш в текущем раунде (цвета и полоски)
      1.1.2.4. Распределение шансов на выигрыш в текущем раунде (аватарки и текст)
      1.1.2.5. Ставки раунда
      1.1.2.6. Панель с информацией о текущем раунде

    1.1.3. Контент истории игр

  1.2. Условия использования, предупреждение о не аффилированности с Valve
  1.3. Статистические данные

    1.3.1. Последний победитель
    1.3.2. Счастливчик дня
    1.3.3. Наибольшая ставка

-------------------*/ ?>
<div class="classic-game" data-bind="visible: layoutmodel.m.s1.selected_subdoc().uri() == '/'">

  <?php /*-------------------------------->
  <!-- 1.1. Контейнер классической игры -->
  <!----------------------------------*/ ?>
  <div class="cg-container">

    <?php /*------------>
    <!-- 1.1.1. Шапка -->
    <!--------------*/ ?>
    <div class="header">

      <?php /*-------------------------------------------->
      <!-- 1] Логотип классической игры, и её заголовок -->
      <!----------------------------------------------*/ ?>
      <div class="logo_and_name" data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'game'}, click: f.s1.choose_tab.bind($data, 'game')">
        <i class="mdi mdi-crown"></i>
        <span>Classic Game</span>
      </div>

      <?php /*--------------------------->
      <!-- 2] Интерфейс выбора комнаты -->
      <!-----------------------------*/ ?>
      <div class="choose-room" data-bind="if: m.s1.game.choosen_room()">

        <?php /*------------------------------->
        <!-- 2.1] Название выбранной комнаты -->
        <!---------------------------------*/ ?>
        <div class="choosen-name">
          <span data-bind="text: m.s1.game.choosen_room().name() + ' ROOM'"></span>
          <i class="mdi mdi-chevron-down"></i>
        </div>

        <?php /*----------------------------------------->
        <!-- 2.2] Список комнат, которые можно выбрать -->
        <!-------------------------------------------*/ ?>
        <div class="rooms-list" data-bind="foreach: m.s1.game.rooms">

          <?php /*------->
          <!-- Комната -->
          <!---------*/ ?>
          <div class="room" data-bind="click: $root.f.s1.choose_room">

            <?php /*-------->
            <!-- Название -->
            <!----------*/ ?>
            <div class="title">
              <span data-bind="text: name() + ' ROOM'"></span>
            </div>

            <?php /*-------->
            <!-- Описание -->
            <!----------*/ ?>
            <div class="description">
              <span data-bind="html: description"></span>
            </div>

          </div>

        </div>

      </div>

      <?php /*------------------->
      <!-- 3] Кнопка "История" -->
      <!---------------------*/ ?>
      <div class="history-button" data-bind="css: {choosen: m.s1.maintabs.choosen().name() == 'history'}, click: f.s1.choose_tab.bind($data, 'history')">
        <span>История</span>
      </div>

    </div>

    <?php /*-------------------------------->
    <!-- 1.1.2. Контент классической игры -->
    <!----------------------------------*/ ?>
    <div style="display: none" data-bind="if: m.s1.game.curprev().current, visible: m.s1.maintabs.choosen().name() == 'game'">

      <?php /*-------------------------->
      <!-- 1.1.2.1. Номер игры и банк -->
      <!----------------------------*/ ?>
      <div class="gamenum_and_bank" data-bind="if: m.s1.game.curprev().current">

        <?php /*------------->
        <!-- 1] Номер игры -->
        <!---------------*/ ?>
        <div class="gamenum">
          <span>ИГРА</span>
          <span data-bind="text: '#' + m.s1.game.curprev().current().id()"></span>
        </div>

        <?php /*------->
        <!-- 2] Банк -->
        <!---------*/ ?>
        <div class="bank">
          <span>БАНК:</span>
          <span data-bind="text: Math.ceil((m.s1.game.curjackpot()/100)*server.data.usdrub_rate)"></span>
          <span>руб</span>
        </div>

      </div>

      <?php /*-------------------------------------------->
      <!-- 1.1.2.2. Панель интерфейса игрового процесса -->
      <!----------------------------------------------*/ ?>
      <div class="gameprocess" data-bind="css: {notauth: (m.s0.auth.is_anon() && ['Lottery', 'Winner', 'Finished'].indexOf(m.s1.game.choosen_status()) == -1)}">

        <?php /*------------------------------------------------------->
        <!-- 1.1.2.2.1. Счётчики предметов и времени текущего раунда -->
        <!---------------------------------------------------------*/ ?>
        <div class="counters" data-bind="css: {lottery: (['Lottery'].indexOf(m.s1.game.choosen_status()) != -1), winner: (['Winner'].indexOf(m.s1.game.choosen_status()) != -1)}">

          <?php /*----------------------->
          <!-- 1] Счётчик вещей раунда -->
          <!-------------------------*/ ?>
          <div class="counter-items">

            <?php /*------------------->
            <!-- Индикаторная полоса -->
            <!---------------------*/ ?>
            <div class="indicator" data-bind="if: $root.m.s1.game.choosen_room, style: {width: m.s1.bank.indicator_percents()+'%'}">

              <?php /*--------------------------------------->
              <!-- Ножи в правой части индикаторной полосы -->
              <!-----------------------------------------*/ ?>
              <img class="knifes" src="{!! asset('public/D10009/assets/images/knife-gradient.png') !!}">

              <?php /*------------------------------------------>
              <!-- Информация о кол-ве поставленных предметов -->
              <!--------------------------------------------*/ ?>
              <div class="items-inbank-max">
                <span data-bind="text: m.s1.bank.items_sorted().length + ($root.m.s1.game.choosen_room().max_items_per_round() != '0' ? ('/' + $root.m.s1.game.choosen_room().max_items_per_round()) : '')"></span>
              </div>

            </div>

          </div>

          <?php /*------------------------->
          <!-- 2] Текст между счётчиками -->
          <!---------------------------*/ ?>
          <div class="text-between-counters">
            <span>ИЛИ ЧЕРЕЗ</span>
          </div>

          <?php /*------------------------->
          <!-- 3] Счётчик времени раунда -->
          <!---------------------------*/ ?>
          <div class="counter-time">
            <div>
              <span data-bind="text: m.s1.game.counters.lottery.minutes"></span>
            </div>
            <div>:</div>
            <div>
              <span data-bind="text: m.s1.game.counters.lottery.seconds"></span>
            </div>
          </div>

        </div>

        <?php /*--------------------------------------------->
        <!-- 1.1.2.2.2. Органы управления игрой для игрока -->
        <!-----------------------------------------------*/ ?>
        <div class="controls" data-bind="css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) != -1), notauth: !m.s0.is_logged_in()}">

          <?php /*------------------------------------------------------------>
          <!-- 1] Кол-во внесённых предметов, шанс, кнопка "Внести депозит" -->
          <!--------------------------------------------------------------*/ ?>
          <div class="status_and_button" data-bind="visible: m.s0.is_logged_in">

            <?php /*------------------------------->
            <!-- 1.1] Кол-во внесённых предметов -->
            <!---------------------------------*/ ?>
            <div class="items_info">
              <span data-bind="text: m.s1.bank.itemsnum_human"></span>
            </div>

            <?php /*--------------------------------->
            <!-- 1.2] Шанс игрока в текущем раунде -->
            <!-----------------------------------*/ ?>
            <div class="chance">
              <span class="arrows-left"></span>
              <span data-bind="text: 'Ваш шанс: '+m.s1.bank.bets()+'%'"></span>
              <span class="arrows-right"></span>
            </div>

            <?php /*---------------------------->
            <!-- 1.3] Кнопка "Внести депозит" -->
            <!------------------------------*/ ?>
            <div class="make-a-bet">
              <div class="button" data-bind="click: f.s1.onclick_handler">
                <span>Внести депозит</span>
              </div>
            </div>

          </div>

          <?php /*--------------------------------------------------->
          <!-- 2] Панель с информацией о лимитах выбранной комнаты -->
          <!-----------------------------------------------------*/ ?>
          <div class="info" data-bind="if: m.s1.game.choosen_room, visible: m.s0.is_logged_in">

            <?php /*------------------------->
            <!-- 2.1] Информация о лимитах -->
            <!---------------------------*/ ?>
            <div class="limits">
              <span data-bind="text: 'Минимальная сумма депозита '+(m.s1.game.choosen_room().min_bet() != 0 ? (Math.ceil((Math.round(m.s1.game.choosen_room().min_bet())/100)*server.data.usdrub_rate)  + ' руб.') : ' не ограничена.')"></span>
              <span data-bind="text: 'Максимальный депозит '+(m.s1.game.choosen_room().max_items_per_bet() != 0 ? (m.s1.game.choosen_room().max_items_per_bet() + ' предметов.') : ' не ограничен.')"></span>
            </div>

          </div>

          <?php /*------------------------------------------------------------------------------>
          <!-- 3] Панель с кнопкой "Принять участие" для не аутентифицированных пользователей -->
          <!--------------------------------------------------------------------------------*/ ?>
          <div class="not_auth_user_panel" data-bind="visible: !m.s0.is_logged_in()">

            <?php /*----------------------------------->
            <!-- 1.1] Замечание про дорогие предметы -->
            <!-------------------------------------*/ ?>
            <div class="expensive_items">
              <div>
                <span>ЧЕМ ДОРОЖЕ ПРЕДМЕТЫ ВЫ СТАВИТЕ, ТЕМ ВЫШЕ ШАНС НА ПОБЕДУ</span>
              </div>
            </div>

            <?php /*--------------------------------------->
            <!-- 1.2] Информация об ограничениях комнаты -->
            <!-----------------------------------------*/ ?>
            <div class="roominfo">
              <span class="arrows-left"></span>
              <div>
                <span data-bind="text: 'МИНИМАЛЬНАЯ СУММА ДЕПОЗИТА '+(m.s1.game.choosen_room().min_bet() != 0 ? (Math.ceil((Math.round(m.s1.game.choosen_room().min_bet())/100)*server.data.usdrub_rate)  + ' РУБ.') : ' не ограничена.')"></span>
                <span data-bind="text: 'МАКСИМАЛЬНЫЙ ДЕПОЗИТ '+(m.s1.game.choosen_room().max_items_per_bet() != 0 ? (m.s1.game.choosen_room().max_items_per_bet() + ' ПРЕДМЕТОВ.') : ' НЕ ОГРАНИЧЕН.')"></span>
              </div>
              <span class="arrows-right"></span>
            </div>

            <?php /*---------------------------->
            <!-- 1.3] Кнопка "Внести депозит" -->
            <!------------------------------*/ ?>
            <div class="make-a-bet">
              <div class="button" onclick="if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) window.open('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam'); else popupCenter('{!! (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ":" . (\Request::getPort()); !!}/authwith?provider=steam','steam','1024','768');")>
                <i class="fa fa-fw fa-steam"></i>
                <span>Войти, чтобы играть</span>
              </div>
            </div>

          </div>

        </div>

        <?php /*-------------------------------------->
        <!-- 1.1.2.2.3. Бегущая полоса с аватарками -->
        <!----------------------------------------*/ ?>
        <div class="strip-avatars" data-bind="if: m.s1.game.choosen_room, css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) == -1), downpanel: (['Started', 'Pending'].indexOf(m.s1.game.choosen_status()) != -1)}">

          <?php /*------------------------------------------------------------->
          <!-- Непосредственно контейнер (кот.движется) для полоски аватарок -->
          <!---------------------------------------------------------------*/ ?>
          <div class="moving_cont" data-bind="foreach: m.s1.game.strip.avatars, style: {width: m.s1.game.strip.width()+'px', transform: 'translate3d('+m.s1.game.strip.currentpos()+'px, 0px, 0px)'}"> <!--, transform: 'translate3d('+(-m.s1.game.strip.currentpos())+'px, 0px, 0px)', transitionDuration: m.s1.game.strip.duration, transitionTimingFunction: m.s1.game.bezier.cssvalue}">-->

            <?php /*--------------------->
            <!-- Аватарка в контейнере -->
            <!-----------------------*/ ?>
            <img data-bind="attr: {src: $data}">

          </div>

        </div>

        <?php /*--------------------------------------------------------->
        <!-- 1.1.2.2.4. Инфо-панель для состояний "Lottery" и "Winner" -->
        <!-----------------------------------------------------------*/ ?>
        <div class="lw-panel" data-bind="css: {hiddenpanel: (['Lottery', 'Winner'].indexOf(m.s1.game.choosen_status()) == -1)}">

          <?php /*--------------------------------------->
          <!-- 1] Стрелочка, указывающая на победителя -->
          <!-----------------------------------------*/ ?>
          <img class="arrow" src="{!! asset('public/D10009/assets/images/roulette-arrow.png') !!}">

          <?php /*---------------------------------------------->
          <!-- 2] Победный билет, победил игрок, число раунда -->
          <!------------------------------------------------*/ ?>
          <div class="textinfo">

            <?php /*------------------->
            <!-- 2.1] Победный билет -->
            <!---------------------*/ ?>
            <div class="ticket">
              <span>Победный билет: </span>
              <span data-bind="text: m.s1.game.lwpanel.ticket()"></span>
            </div>

            <?php /*------------------->
            <!-- 2.2] Победил игрок -->
            <!---------------------*/ ?>
            <div class="winner">
              <span>Победил игрок: </span>
              <span data-bind="text: m.s1.game.lwpanel.winner"></span>
            </div>

            <?php /*----------------->
            <!-- 2.3] Число раунда -->
            <!-------------------*/ ?>
            <div class="number">
              <span>Число раунда: </span>
              <span data-bind="text: m.s1.game.lwpanel.number"></span>
            </div>

          </div>

          <?php /*-------------------->
          <!-- 3] Текущий банк игры -->
          <!----------------------*/ ?>
          <div class="bank">
            <span data-bind="text: Math.ceil((m.s1.game.curjackpot()/100)*server.data.usdrub_rate) + ' руб.'"></span>
          </div>

          <?php /*---------------------------------------------->
          <!-- 4] Счётчик времени до начала следующего раунда -->
          <!------------------------------------------------*/ ?>
          <div class="counter-time">

            <?php /*-------------------------->
            <!-- Надпись "новая игра через" -->
            <!----------------------------*/ ?>
            <div class="counterdesc">
              <span>Новая игра через: </span>
            </div>

            <?php /*----------->
            <!-- Сам счётчик -->
            <!-------------*/ ?>
            <div class="counter">

              <!--<div>-->
              <!--  <span data-bind="text: m.s1.game.counters.newgame.minutes"></span>-->
              <!--</div>-->
              <!--<div>:</div>-->
              <div>
                <span data-bind="text: m.s1.game.counters.newgame.seconds"></span>
              </div>

            </div>

          </div>

          <?php /*--------------------------------->
          <!-- 5] Кнопка "Внести депозит первым" -->
          <!-----------------------------------*/ ?>
          <div class="make-a-bet-first">
            <div class="button" data-bind="click: f.s1.onclick_handler">
              <span>Внести депозит первым</span>
            </div>
          </div>

        </div>

      </div>

      <?php /*--------------------------------------------------------------------------->
      <!-- 1.1.2.3. Распределение шансов на выигрыш в текущем раунде (цвета и полоски) -->
      <!-----------------------------------------------------------------------------*/ ?>
      <div class="odds-graphic" style="display: none" data-bind="foreach: m.s1.game.wheel.data, visible: (m.s1.game.curprev().current() && m.s1.game.curprev().current().bets().length)">

        <?php /*-------------------------------------->
        <!-- Полоска, графически отображающая шансы -->
        <!----------------------------------------*/ ?>
        <div class="strip" data-bind="style: {background: color, width: Math.round(odds()*100*100)/100+'%'}"></div>

      </div>

      <?php /*---------------------------------------------------------------------------->
      <!-- 1.1.2.4. Распределение шансов на выигрыш в текущем раунде (аватарки и текст) -->
      <!------------------------------------------------------------------------------*/ ?>
      <div class="odds-avatars" style="display: none" data-bind="foreach: m.s1.game.wheel.data, visible: (m.s1.game.curprev().current() && m.s1.game.curprev().current().bets().length)">

        <?php /*---------------------------->
        <!-- Аватарка, цвет игрока, шансы -->
        <!------------------------------*/ ?>
        <div class="ava-color-odds">

          <?php /*----------->
          <!-- 1] Аватарка -->
          <!-------------*/ ?>
          <div class="avatar">
            <img data-bind="attr: {src: avatar}">
          </div>

          <?php /*-------------->
          <!-- 2] Цвет игрока -->
          <!----------------*/ ?>
          <div class="strip" data-bind="style: {background: color}"></div>

          <?php /*-------->
          <!-- 3] Шансы -->
          <!----------*/ ?>
          <div class="odds">
            <span data-bind="text: Math.round(odds()*100*10)/10 + '%'"></span>
          </div>

        </div>

      </div>

      <?php /*---------------------->
      <!-- 1.1.2.5. Ставки раунда -->
      <!------------------------*/ ?>
      <div class="bets" data-bind="foreach: m.s1.smoothbets.bets"> <!-- m.s1.game.curprev().current().bets.slice(0).reverse()"> -->

        <?php /*------>
        <!-- Ставка -->
        <!--------*/ ?>
        <div class="bet-container" data-bind="css: {expanded: (is_expanded() == true)}, style: {zIndex: $index}">

          <?php /*------------------->
          <!-- Информация о ставке -->
          <!---------------------*/ ?>
          <div class="bet-info" data-bind="style: {borderColor: bet_color_hex}">

            <?php /*------>
            <!-- Аватар -->
            <!--------*/ ?>
            <div class="avatar">
              <img data-bind="attr: {src: m5_users()[0].avatar_steam}" />
            </div>

            <?php /*--------------------->
            <!-- Информационный раздел -->
            <!-----------------------*/ ?>
            <div class="info-section">

              <?php /*---------------------------------->
              <!-- Имя игрока и номера билетов ставки -->
              <!------------------------------------*/ ?>
              <div>

                <!-- Номер ставки в раунде -->
                <div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">
                  <span>Билеты от </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>
                  <span> до </span>
                  <span class="ticketnumber" data-bind="text: '#' + m5_users()[0].pivot.tickets_to()"></span>
                </div>
                <!--<span class="bet-number" data-bind="text: 'Ставка №' + (($root.m.s1.game.curprev().current().bets().length) - ($index() + 1) + 1)" title="Номер ставки в рамках раунда"></span>-->

                <!-- Nickname игрока, сделавшего ставку-->
                <a class="nickname" target="_blank" data-bind="text: m5_users()[0].nickname"></a> <!--, attr: {href: 'http://steamcommunity.com/profiles/' + m5_users()[0].ha_provider_uid()}" title="Перейти в профиль игрока в Steam"></a>-->

              </div>

              <?php /*------------>
              <!-- Сумма, шансы -->
              <!--------------*/ ?>
              <div class="sum-odds-tickets">

                <!-- Сумма ставки и шансы выигрыша -->
                <span class="odds" data-bind="text: (Math.round(((+total_bet_amount() / +$root.m.s1.game.curjackpot())) * 100 * 10) / 10) + '%'" title="Шансы на победу ставки"></span>
                <span>/</span>
                <span class="sum" data-bind="text: Math.ceil((Math.round(total_bet_amount())/100)*server.data.usdrub_rate) + ' руб.'" title="Сумма ставки"></span>

                <!-- Номера билетов -->
                <!--<div class="tickets" style="display: inline-block; padding-left: 15px;" title="Диапазон билетов ставки">-->
                <!--  <span data-bind="text: '#' + m5_users()[0].pivot.tickets_from()"></span>-->
                <!--  <span>-</span>-->
                <!--  <span data-bind="text: m5_users()[0].pivot.tickets_to()"></span>-->
                <!--</div>-->

              </div>

            </div>

          </div>

          <?php /*----------------->
          <!-- Поставленные вещи -->
          <!-------------------*/ ?>
          <div class="bets-items-cont" data-bind="foreach: m8_items()">

            <?php /*----------------->
            <!-- Поставленная вещь -->
            <!-------------------*/ ?>
            <div class="bets-item tooltipstered" data-bind="attr: {title: name}">

              <?php /*------------------------------------------->
              <!-- Цветная полоска с цветом качества/категории -->
              <!---------------------------------------------*/ ?>
              <div class="strip" data-bind="style: {background: $root.f.s1.get_cat_quality_item_color($data)}"></div>

              <?php /*----------->
              <!-- Изображение -->
              <!-------------*/ ?>
              <img data-bind="attr: {src: steammarket_image}"> <!--{src: $root.f.s1.get_steam_img_with_size(steammarket_image(), '80x55')}"> -->

              <?php /*---->
              <!-- Цена -->
              <!------*/ ?>
              <div class="price">
                <span data-bind="text: Math.ceil(price()*server.data.usdrub_rate)"></span>
                <span class="rub" data-bind="text: 'руб'"></span>
              </div>

            </div>

          </div>

        </div>

      </div>

      <?php /*---------------------------------------------->
      <!-- 1.1.2.6. Панель с информацией о текущем раунде -->
      <!------------------------------------------------*/ ?>
      <div class="infopanel">

        <?php /*-------------------------->
        <!-- 1] Призыв вносить депозиты -->
        <!----------------------------*/ ?>
        <div class="call4deposits">
          <span>ИГРА НАЧАЛАСЬ! ВНОСИТЕ ДЕПОЗИТЫ!</span>
        </div>

        <?php /*------------------------------------->
        <!-- 2] Кнопка "Честная игра" и хэш раунда -->
        <!---------------------------------------*/ ?>
        <div class="fair-button-and-hash">

          <?php /*-------------------------->
          <!-- 2.1] Кнопка "Честная игра" -->
          <!----------------------------*/ ?>
          <div class="fair-button">
            <i></i>
            <span>ЧЕСТНАЯ ИГРА</span>
          </div>

          <?php /*-------------------------->
          <!-- 2.2] Кнопка "Честная игра" -->
          <!----------------------------*/ ?>
          <div class="hash">
            <span data-bind="text: 'Хэш раунда: '"></span>
            <span data-bind="text: m.s1.game.curprev().current().key_hash"></span>
          </div>

        </div>

      </div>

    </div>

    <?php /*-------------------------->
    <!-- 1.1.3. Контент истории игр -->
    <!----------------------------*/ ?>
    <div class="cg_history" style="display: none" data-bind="visible: m.s1.maintabs.choosen().name() == 'history'">

      <?php /*----------------------------------------------------------------------------------------->
      <!-- 1] Содержимое истории игр (показывать, только если история загружена в выбранной комнате) -->
      <!-------------------------------------------------------------------------------------------*/ ?>
      <div style="display: none" class="history" data-bind="if: m.s1.history.is_in_choosen_room, visible: m.s1.history.is_in_choosen_room">

        <?php /*---------------------------->
        <!-- Список позиций в истории игр -->
        <!------------------------------*/ ?>
        <div data-bind="foreach: m.s1.history.all()[m.s1.game.choosen_room().id()]">

          <?php /*--------------------->
          <!-- Позиция в истории игр -->
          <!-----------------------*/ ?>
          <div class="history-item">

            <?php /*----------------------->
            <!-- 1.1] Заголовочная часть -->
            <!-------------------------*/ ?>
            <div class="header-part">

              <?php /*--------->
              <!-- 1) Аватар -->
              <!-----------*/ ?>
              <img class="avatar" data-bind="attr: {src: avatar_steam}">

              <?php /*--------------------->
              <!-- 2) Ник, шанс, выигрыш -->
              <!-----------------------*/ ?>
              <div class="nick_odds_jackpot">

                <?php /*--->
                <!-- Ник -->
                <!-----*/ ?>
                <div class="nick">
                  <a target="_blank" data-bind="text: nickname, attr: {href: 'http://steamcommunity.com/profiles/'+steamid()}"></a>
                </div>

                <?php /*---->
                <!-- Шанс -->
                <!------*/ ?>
                <div class="odds">
                  <span data-bind="text: 'Шанс: ' + (Math.round(((+winner_bets_items_cents() / +jackpot_total_sum_cents())) * 100 * 10) / 10) + '%'"></span>
                </div>

                <?php /*------->
                <!-- Выигрыш -->
                <!---------*/ ?>
                <div class="jackpot">
                  <span data-bind="text: 'Выигрыш: ' + Math.ceil((win_fact_cents()/100)*server.data.usdrub_rate) + ' руб.'"></span>
                </div>

              </div>

              <?php /*-------------------------->
              <!-- 3) Игра, хэш, число раунда -->
              <!----------------------------*/ ?>
              <div class="game_hash">

                <?php /*---->
                <!-- Игра -->
                <!------*/ ?>
                <div class="game">
                  <!--<span data-bind="text: 'Комната: ' + room_name()"></span>-->
                  <span class="num" data-bind="text: 'ИГРА #' + id()"></span>
                </div>

                <?php /*--->
                <!-- Хэш -->
                <!-----*/ ?>
                <div class="key">
                  <span data-bind="text: 'Хэш раунда: ' + key_hash()"></span>
                </div>

                <?php /*------------>
                <!-- Число раунда -->
                <!--------------*/ ?>
                <div class="key_hash">
                  <span data-bind="text: 'Число раунда: ' + key()"></span>
                </div>

              </div>

            </div>

            <?php /*---------------------------->
            <!-- 1.2] Список выигранных вещей -->
            <!------------------------------*/ ?>
            <div class="items" data-bind="foreach: items">

              <?php /*---->
              <!-- Вещь -->
              <!------*/ ?>
              <div class="item" data-bind="attr: {title: name}">

                <?php /*----------->
                <!-- Изображение -->
                <!-------------*/ ?>
                <div class="img_cont">
                  <img data-bind="attr: {src: steammarket_image}">
                </div>

                <?php /*---->
                <!-- Цена -->
                <!------*/ ?>
                <div class="price">
                  <span data-bind="text: Math.ceil((price()/100)*server.data.usdrub_rate) + ' руб.'"></span>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

      <?php /*--------------------------------------------------------------------------------------------------->
      <!-- 2] Надпись об отсутствии истории (показывать, только если история не загружена в выбранной комнате) -->
      <!-----------------------------------------------------------------------------------------------------*/ ?>
      <div style="display: none" class="history_absent" data-bind="if: !m.s1.history.is_in_choosen_room(), visible: !m.s1.history.is_in_choosen_room()">

        <span>История отсутствует</span>

      </div>

      <?php /*--------------------------->
      <!-- 3] Кнопка "Показать ещё 10" -->
      <!-----------------------------*/ ?>
      <div style="display: none" class="button_more" data-bind="if: m.s1.history.is_in_choosen_room, visible: (m.s1.history.is_in_choosen_room() && m.s1.history.all()[m.s1.game.choosen_room().id()]().length < 50 && m.s1.history.totalcount()[m.s1.game.choosen_room().id()]() >= m.s1.history.all()[m.s1.game.choosen_room().id()]().length && m.s1.history.pagenums()[m.s1.game.choosen_room().id()]() < 5)">

        <?php /*------>
        <!-- Кнопка -->
        <!--------*/ ?>
        <div class="button_more_itself">
          <button type="button" class="btn btn-block btn-default btn-flat" data-bind="click: f.s1.get_more_history" style="position: relative">
            <span>Показать ещё</span>
            <div class="loader">
              <div style="display: none; position: absolute; top: 3px; right: 3px;" class="loader-inner ball-clip-rotate" data-bind="visible: m.s1.history.is_more_history_spinner_vis">
                <div></div>
              </div>
            </div>
          </button>
        </div>

      </div>

      <?php /*------------------------------------->
      <!-- 4] Модальный щит загрузки со спинером -->
      <!---------------------------------------*/ ?>
      <div class="loader">
        <div style="display: none" class="modal_shield loader-inner ball-clip-rotate" data-bind="visible: m.s0.is_load_shield_on">
          <div></div>
        </div>
      </div>

    </div>

  </div>

  <?php /*------------------------------------------------------------------------>
  <!-- 1.2. Условия использования, предупреждение о не аффилированности с Valve -->
  <!--------------------------------------------------------------------------*/ ?>
  <div class="termsofuse">

    <?php /*---------------------------------->
    <!-- 1] Заявление о не аффилированности -->
    <!------------------------------------*/ ?>
    <div class="not-affilated-statement">
      <span>CSGOHAP is NOT affiliated with VALVE corp</span>
    </div>

    <?php /*------------------------>
    <!-- 2] Правила использования -->
    <!--------------------------*/ ?>
    <div class="termslink">
      <a>Terms of service</a>
    </div>

  </div>

  <?php /*-------------------------->
  <!-- 1.3. Статистические данные -->
  <!----------------------------*/ ?>
  <div class="statistics" data-bind="if: m.s1.game.choosen_room">

    <?php /*--------------------------->
    <!-- 1.3.1. Последний победитель -->
    <!-----------------------------*/ ?>
    <div class="lastwinner" data-bind="visible: (m.s1.game.statistics['m9:statistics:lastwinners'] && m.s1.game.choosen_room() && m.s1.game.choosen_room().id() && m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Последний победитель</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*------------>
        <!-- 3.1] Выигрыш -->
        <!--------------*/ ?>
        <div class="row">
          <div class="span50">Выигрыш:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

        <?php /*--------->
        <!-- 3.2] Шанс -->
        <!-----------*/ ?>
        <div class="row">
          <div class="span50">Шанс:</div>
          <div class="span50 odds" data-bind="text: Math.round(m.s1.game.statistics['m9:statistics:lastwinners'][m.s1.game.choosen_room().id()].odds()*10)/10 + '%'"></div>
        </div>

      </div>

    </div>

    <?php /*---------------------->
    <!-- 1.3.2. Счастливчик дня -->
    <!------------------------*/ ?>
    <div class="luckyoftheday" data-bind="visible: (m.s1.game.statistics['m9:statistics:luckyoftheday'] && m.s1.game.statistics['m9:statistics:luckyoftheday'].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Счастливчик дня</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:luckyoftheday'].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:luckyoftheday'].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*------------>
        <!-- 3.1] Выигрыш -->
        <!--------------*/ ?>
        <div class="row">
          <div class="span50">Выигрыш:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:luckyoftheday'].jackpot_total_sum_cents()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

        <?php /*--------->
        <!-- 3.2] Шанс -->
        <!-----------*/ ?>
        <div class="row odds">
          <div class="span50">Шанс:</div>
          <div class="span50 odds" data-bind="text: m.s1.game.statistics['m9:statistics:luckyoftheday'].odds() + '%'"></div>
        </div>

      </div>

    </div>

    <?php /*------------------------>
    <!-- 1.3.3. Наибольшая ставка -->
    <!--------------------------*/ ?>
    <div class="thebiggetsbet" data-bind="visible: (m.s1.game.statistics['m9:statistics:thebiggetsbet'] && m.s1.game.statistics['m9:statistics:thebiggetsbet'].id())">

      <?php /*------------>
      <!-- 1] Заголовок -->
      <!--------------*/ ?>
      <div class="header">
        <span>Наибольшая ставка</span>
      </div>

      <?php /*---------->
      <!-- 2] Контент -->
      <!------------*/ ?>
      <div class="contentpart">

        <?php /*----------->
        <!-- 2.1] Аватар -->
        <!-------------*/ ?>
        <img data-bind="attr: {src: m.s1.game.statistics['m9:statistics:thebiggetsbet'].avatar_steam}">

        <?php /*------------>
        <!-- 2.2] Никнэйм -->
        <!--------------*/ ?>
        <div class="nickname">
          <span data-bind="text: m.s1.game.statistics['m9:statistics:thebiggetsbet'].nickname"></span>
        </div>

      </div>

      <?php /*--------->
      <!-- 3] Подвал -->
      <!-----------*/ ?>
      <div class="footer">

        <?php /*---------------------->
        <!-- 3.1] Наибольшая ставка -->
        <!------------------------*/ ?>
        <div class="row">
          <div class="span50">Ставка:</div>
          <div class="span50" data-bind="text: Math.ceil((m.s1.game.statistics['m9:statistics:thebiggetsbet'].sum_cents_at_bet_moment()/100)*server.data.usdrub_rate) + ' руб.'"></div>
        </div>

      </div>

    </div>

  </div>

</div>