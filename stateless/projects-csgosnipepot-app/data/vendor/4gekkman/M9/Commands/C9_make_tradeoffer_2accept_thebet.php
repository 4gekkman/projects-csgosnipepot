<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make trade offer to accept the players bet
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        players_steamid   | Steam ID игрока
 *        items2bet         | Список вещей, которые хочет поставить игрок
 *      ]
 *    ]
 *
 *  Формат возвращаемого значения
 *  -----------------------------
 *
 *    [
 *      status          // 0 - всё ОК, -1 - нет доступа, -2 - ошибка
 *      data            // результат выполнения команды
 *    ]
 *
 *  Значение data в зависимости от статуса
 *  --------------------------------------
 *
 *    status == 0
 *    -----------
 *      - ""
 *
 *    status == -1
 *    ------------
 *      - Не контролируется в командах. Отслеживается в хелпере runcommand.
 *
 *    status = -2
 *    -----------
 *      - Текст ошибки. Может заменяться на "" в контроллерах (чтобы скрыть от клиента).
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M9\Commands;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Базовые классы, необходимые для работы команд вообще
  use App\Jobs\Job,
      Illuminate\Queue\SerializesModels,
      Illuminate\Queue\InteractsWithQueue,
      Illuminate\Contracts\Queue\ShouldQueue;   // добавление в очередь задач

  // Классы, поставляемые Laravel
  use Illuminate\Routing\Controller as BaseController,
      Illuminate\Support\Facades\App,
      Illuminate\Support\Facades\Artisan,
      Illuminate\Support\Facades\Auth,
      Illuminate\Support\Facades\Blade,
      Illuminate\Support\Facades\Bus,
      Illuminate\Support\Facades\Cache,
      Illuminate\Support\Facades\Config,
      Illuminate\Support\Facades\Cookie,
      Illuminate\Support\Facades\Crypt,
      Illuminate\Support\Facades\DB,
      Illuminate\Database\Eloquent\Model,
      Illuminate\Support\Facades\Event,
      Illuminate\Support\Facades\File,
      Illuminate\Support\Facades\Hash,
      Illuminate\Support\Facades\Input,
      Illuminate\Foundation\Inspiring,
      Illuminate\Support\Facades\Lang,
      Illuminate\Support\Facades\Log,
      Illuminate\Support\Facades\Mail,
      Illuminate\Support\Facades\Password,
      Illuminate\Support\Facades\Queue,
      Illuminate\Support\Facades\Redirect,
      Illuminate\Support\Facades\Redis,
      Illuminate\Support\Facades\Request,
      Illuminate\Support\Facades\Response,
      Illuminate\Support\Facades\Route,
      Illuminate\Support\Facades\Schema,
      Illuminate\Support\Facades\Session,
      Illuminate\Support\Facades\Storage,
      Illuminate\Support\Facades\URL,
      Illuminate\Support\Facades\Validator,
      Illuminate\Support\Facades\View;

  // Доп.классы, которые использует эта команда


//---------//
// Команда //
//---------//
class C9_make_tradeoffer_2accept_thebet extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Принять данные
    public $data;

  //------------------------------------------------------//
  // В. Принять аргументы, переданные при запуске команды //
  //------------------------------------------------------//
  public function __construct($data)  // TODO: указать аргументы
  {

    $this->data = $data;

  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Провести валидацию входящих параметров
     *  2. Получить инвентарь игрока с помощью players_steamid
     *  3. Произвести сверку вещей в inventory и items2bet
     *  4. Получить комнату, в которой игрок хочет сделать ставку, с помощью choosen_room_id
     *  5. Проверить типы вещей из $items2bet, а также наличие цены
     *  6. Сгенерировать случайный код безопасности
     *  7. Определить, какой бот будет принимать ставку
     *  8. Если не удалось определить бота, который должен принять ставку, вернуть ошибку
     *  9. Получить игрока, который хочет сделать ставку
     *  10. Проверить, нет ли уже у пользователя в этой комнате активного оффера
     *  11. Произвести проверку ставки на соответствие лимитам комнаты $room
     *  12. Отправить игроку торговое предложение
     *  13. Записать необходимую информацию о ставке в БД
     *  14. Сделать коммит
     *  15. Обновить весь кэш
     *  16. Подготовить активные офферы именно для этого пользователя
     *  m. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------//
    // Make trade offer to accept the players bet //
    //--------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "players_steamid" => ["required", "regex:/^[0-9]+$/ui"],
        "items2bet"       => ["required", "array"],
        "choosen_room_id" => ["required", "regex:/^[0-9]+$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Получить инвентарь игрока с помощью players_steamid
      $inventory = runcommand('\M8\Commands\C4_getinventory', [
        "steamid" => $this->data['players_steamid'],
        "force"   => true
      ]);
      if($inventory['status'] != 0)
        throw new \Exception("Не получается получить твой инвентарь. Зайди в свой аккаунт в Steam, в настройки приватности, и проверь, чтобы инвентарь был 'Public'.");

      // 3. Произвести сверку вещей в inventory и items2bet
      // - Все вещи из items2bet должны присутствовать в items2bet.
      // - Сверка производится по параметру market_name.
      // - При любом расхождении:
      //
      //    • Принудительно обновить инвентарь пользователя players_steamid
      //      в комнате choosen_room_id, и транслировать его по частному каналу websockets.
      //    • Завершить с ошибкой.
      //

        // 3.1. Получить массив market_name из items2bet
        $items2bet_market_names = call_user_func(function(){
          $results = [];
          for($i=0; $i<count($this->data['items2bet']); $i++) {
            array_push($results, $this->data['items2bet'][$i]['market_name']);
          }
          return $results;
        });

        // 3.2. Получить массив assetid_classid_instanceid из inventory
        $inventory_aci = call_user_func(function() USE ($inventory) {
          $results = [];
          for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
            $item = $inventory['data']['rgDescriptions'][$i];
            array_push($results, $item['assetid'] . "_" . $item['classid'] . "_" . $item['instanceid']);
          }
          return $results;
        });

        // 3.3. Получить массив assetid_classid_instanceid из items2bet
        $items2bet_aci = call_user_func(function(){
          $results = [];
          for($i=0; $i<count($this->data['items2bet']); $i++) {
            $item = $this->data['items2bet'][$i];
            array_push($results, $item['assetid'] . "_" . $item['classid'] . "_" . $item['instanceid']);
          }
          return $results;
        });

        // 3.4. Проверить, чтобы 3.2 полностью был включён в 3.3
        if(count(array_intersect($items2bet_aci, $inventory_aci)) != count($items2bet_aci)) {

          // 1] Получить ID текущего пользователя из сессии
          $id_user = json_decode(session('auth_cache'), true)['user']['id'];

          // 2] Принудительно обновить инвентарь пользователя players_steamid в комнате choosen_room_id
          // - И транслировать его по частному каналу websockets.
          call_user_func(function() USE ($inventory, $id_user) {

            Event::fire(new \R2\Broadcast([
              'channels' => ['m9:private:'.$id_user],
              'queue'    => 'm9_lottery_broadcasting',
              'data'     => [
                'task' => 'update_inventory',
                'data' => [
                  'inventory' => $inventory
                ]
              ]
            ]));

          });

          // 3] Завершить с ошибкой
          throw new \Exception("В твоём инвентаре в Steam не оказалось указанных тобой вещей. Твой инвентарь был автоматически обновлён.");

        }

      // 4. Получить комнату, в которой игрок хочет сделать ставку, с помощью choosen_room_id
      $room = \M9\Models\MD1_rooms::find($this->data['choosen_room_id']);
      if(!$room)
        throw new \Exception("Бот не может найти в базе данных комнату, в которой ты пытаешься сделать ставку.");

      // 5. Проверить типы вещей из $items2bet, а также наличие цены
      // - Если есть хотя бы 1 вещь запрещённого типа, завершить с ошибкой.

        // 5.1. Получить список допустимых в $room типов вещей
        $allow_only_types = json_decode($room->allow_only_types, JSON_UNESCAPED_UNICODE);

        // 5.2. Проверить типы вещей из $items2bet
        // - Но основываясь на данных из $inventory
        call_user_func(function() USE ($inventory, $items2bet_market_names, $allow_only_types) {
          for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
            if(in_array($inventory['data']['rgDescriptions'][$i]['market_name'], $items2bet_market_names)) {

              // 1] Получить вещь в короткую переменную
              $item = $inventory['data']['rgDescriptions'][$i];

              // 2] Все ли itemtypes вещи $item есть в $allow_only_types
              $is = call_user_func(function() USE ($item, $allow_only_types) {

                foreach($item['itemtypes'] as $type => $is) {
                  if($is == 1) {
                    if(!in_array($type, $allow_only_types))
                      return false;
                  }
                }
                return true;

              });

              // 3] Если не все, возбудить исключение
              if(!$is)
                throw new \Exception("Одна из вещей, которые ты пытаешься поставить, имеет запрещённый в этой комнате тип. Вещь: ".$inventory['data']['rgDescriptions'][$i]['market_name'].".");

            }
          }
        });

        // 5.3. Проверить наличие цены у каждой из вещей
        call_user_func(function() USE ($inventory, $items2bet_market_names, $allow_only_types) {
          for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
            if(in_array($inventory['data']['rgDescriptions'][$i]['market_name'], $items2bet_market_names)) {
              if(empty($inventory['data']['rgDescriptions'][$i]['price']))
                throw new \Exception("У нас нет цен на некоторые из вещей, которые ты пытаешься поставить. Одна из них: ".$inventory['data']['rgDescriptions'][$i]['market_name'].".");
            }
          }
        });

      // 6. Сгенерировать случайный код безопасности
      // - Он представляет из себя число определённой длины.
      // - Длина указана в настройках соответствующей комнаты, в safecode_length.
      // - У каждого кода безопасности есть свой срок годности.

        // 6.1. Получить длину кода безопасности
        $safecode_length = call_user_func(function() USE ($room) {

          // 1] Получить длину кода безопасности
          $safecode_length = $room->safecode_length;

          // 2] Если $safecode_length пуста, или не числа, использовать '6'
          $validator = r4_validate(['safecode_length' => $safecode_length], [
            "safecode_length" => ["required", "regex:/^[0-9]+$/ui"],
          ]); if($validator['status'] == -1)
            $safecode_length = 6;

          // 3] Вернуть результат
          return $safecode_length;

        });

        // 6.2. Сгенерировать случайное $safecode_length-значное число
        $safecode = call_user_func(function() USE ($safecode_length) {
          $result = '';
          for($i = 0; $i < $safecode_length; $i++) {
            $result .= mt_rand(0, 9);
          }
          return $result;
        });

      // 7. Определить, какой бот будет принимать ставку
      $bot2acceptbet = call_user_func(function() USE ($room) {

        // 7.1. Получить выбранный режим приёма ставок в этой комнате
        $bet_accepting_modes = $room->bet_accepting_modes[0];

        // 7.2. Если выбран режим onebot_oneround_inturn_circled
        if($bet_accepting_modes->mode == "onebot_oneround_inturn_circled") {

          // 1] Получить предпоследний раунд для комнаты $room
          $penultimate_round = call_user_func(function() USE ($room) {

            // 1.1] Если предыдущего раунда нет, вернуть null
            if(count($room->rounds) <= 1) return null;

            // 1.2] Если есть, вернуть предыдущий раунд
            else return $room->rounds[1];

          });

          // 2] Если $penultimate_round найден
          if($penultimate_round) {

            // 3.1] Получить всех связанных с $room ботов
            $bots = $room->m8_bots;

            // 3.2] Если $bots пуст, вернуть null
            if(count($bots) == 0) return null;

            // 3.3] В противном случае
            else {

              // 3.3.1] Получить все ставки предыдущего раунда
              $penultimate_round_bets = $penultimate_round->bets;

              // 3.3.2] Если $penultimate_round_bets пуст, вернуть первого попавшегося бота комнаты
              if(count($penultimate_round_bets) == 0) return $bots[0];

              // 3.3.3] В противном случае, получить связанного с первой из ставок бота
              $bets_bot = $penultimate_round_bets[0]->m8_bots;

              // 3.3.4] Если $bets_bot пуст, вернуть первого попавшегося бота комнаты
              if(count($bets_bot) == 0) return $bots[0];

              // 3.3.5] Если $bets_bot нет в $bots, вернуть первого попавшегося бота комнаты
              if(!in_array($bets_bot[0]['id'], $bots->pluck('id')->toArray())) return $bots[0];

              // 3.3.6] Вернуть первого бота из $bets_bot
              return $bets_bot[0];

            }

          }

          // 3] Если $penultimate_round не найден
          else {

            // 3.1] Получить всех связанных с $room ботов
            $bots = $room->m8_bots;

            // 3.2] Если $bots пуст, вернуть null
            if(count($bots) == 0) return null;

            // 3.3] В противном случае, вернуть первого попавшегося бота
            else return $bots[0];

          }

        }

        // 7.3. Если выбран режим nbots_inturn_circled
        if($bet_accepting_modes->mode == "nbots_inturn_circled") {

        }

      });

      // 8. Если не удалось определить бота, который должен принять ставку, вернуть ошибку
      if(empty($bot2acceptbet))
        throw new \Exception("Не удалось найти в этой комнате бота, который мог бы принять твою ставку.");

      // 9. Получить игрока, который хочет сделать ставку
      $user = \M5\Models\MD1_users::where('ha_provider_uid', $this->data['players_steamid'])->first();
      if(empty($user))
        throw new \Exception("Не удалось найти в базе данных запись о твоём профиле.");

      // 10. Проверить, нет ли уже у пользователя в этой комнате активного оффера
      call_user_func(function() USE ($user, $room) {

        // 1] Получить коллекцию всех ставок пользователя $user со статусом "Active"
        $active_bets = \M9\Models\MD3_bets::whereHas('m5_users', function($query){
          $query->where('ha_provider_uid', $this->data['players_steamid']);
        })->whereHas('rooms', function($query) USE ($room) {
          $query->where('id', $room->id);
        })->whereHas('bets_statuses', function($query){
          $query->where('status', 'Active');
        })->get();

        // 2] Если $active_bets не пуста
        // - Завершить с ошибкой.
        if(count($active_bets) != 0)
          throw new \Exception("Сначала прими или отмени предыдущее активное торговое предложение от бота.");

      });

      // 11. Произвести проверку ставки на соответствие лимитам комнаты $room
      $sum = call_user_func(function() USE ($room, $inventory) {

        // 1] Получить последний раунд в комнате $room
        // - И удостовериться, что он существует
        $lastround = count($room->rounds) > 0 ? $room->rounds[0] : '';
        if(empty($lastround))
          throw new \Exception("Нельзя сделать ставку, поскольку в этой комнате нет ни одного активного раунда.");

        // 2] Получить все необходимые лимиты комнаты $room
        $room_limits = call_user_func(function() USE ($room) {

          return [
            "min_items_per_bet" => $room['min_items_per_bet'],  // MIN кол-во предметов в ставке
            "max_items_per_bet" => $room['max_items_per_bet'],  // MAX кол-во предметов в ставке
            "min_bet"           => $room['min_bet'],            // MIN ставка игрока
            "max_bet"           => $room['max_bet'],            // MAX ставка игрока
          ];

        });

        // 3] Вычислить общую сумму ставки в центах, и кол-во вещей в ставке

          // Сумма в центах
          $sum = call_user_func(function() USE ($lastround, $inventory) {
            $result = 0;
            for($n=0; $n<count($this->data['items2bet']); $n++) {
              $item_inventory = call_user_func(function() USE ($inventory, $n) {
                for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
                  if($inventory['data']['rgDescriptions'][$i]['market_name'] == $this->data['items2bet'][$n]['market_name'])
                    return $inventory['data']['rgDescriptions'][$i];
                }
              });
              if(empty($item_inventory))
                throw new \Exception("Вещь '".$this->data['items2bet'][$n]['market_name']."' неизвестна системе, поэтому её нельзя поставить.");
              $result = +$result + +$item_inventory['price'];
            }
            return round($result*100);
          });

          // Кол-во вещей
          $count = call_user_func(function() USE ($lastround, $inventory) {
            $result = 0;
            for($n=0; $n<count($this->data['items2bet']); $n++) {
              $item_inventory = call_user_func(function() USE ($inventory, $n) {
                for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
                  if($inventory['data']['rgDescriptions'][$i]['market_name'] == $this->data['items2bet'][$n]['market_name'])
                    return $inventory['data']['rgDescriptions'][$i];
                }
              });
              if(empty($item_inventory))
                throw new \Exception("Вещь '".$this->data['items2bet'][$n]['market_name']."' неизвестна системе, поэтому её нельзя поставить.");
              $result = $result = +$result + 1;
            }
            return $result;
          });

        // 4] Проверить, соответствует ли $sum лимитам из $room_limits

          // min_bet
          if($room_limits['min_bet'] != 0) {
            if($sum < $room_limits['min_bet'])
              throw new \Exception('Минимальная сумма ставки в этой комнате: '.$room_limits['min_bet'].' ¢.');
          }

          // max_bet
          if($room_limits['max_bet'] != 0) {
            if($sum > $room_limits['max_bet'])
              throw new \Exception('Максимальная сумма ставки в этой комнате: '.$room_limits['max_bet'].' ¢.');
          }

        // 5] Проверить, соответствует ли $count лимитам из $room_limits

          // min_items_per_bet
          if($room_limits['min_items_per_bet'] != 0) {
            if($count < $room_limits['min_items_per_bet'])
              throw new \Exception('В этой комнате можно ставить минимум '.$room_limits['min_items_per_bet'].' вещей за 1-ну ставку.');
          }

          // max_items_per_bet
          if($room_limits['max_items_per_bet'] != 0) {
            if($count > $room_limits['max_items_per_bet'])
              throw new \Exception('В этой комнате можно ставить максимум '.$room_limits['max_items_per_bet'].' вещей за 1-ну ставку.');
          }

        // n] Вернуть сумму ставкп
        return $sum;

      });

      // 12. Отправить игроку торговое предложение
      // - С запросом тех предметов, которые он хочет поставить.
      $tradeofferid = call_user_func(function() USE ($inventory, $items2bet_market_names, $bot2acceptbet, $safecode, $user){

        // 1] Получить steam_tradeurl пользователя $user
        $steam_tradeurl = $user->steam_tradeurl;
        if(empty($steam_tradeurl))
          throw new \Exception("Чтобы сделать ставку, сначала введи свой Steam Trade URL в настройках.");

        // 2] Получить partner и token пользователя из его trade url
        $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
          "trade_url" => $steam_tradeurl
        ]);
        if($partner_and_token['status'] != 0)
          throw new \Exception("Похоже, что ты ввёл неправильный Steam Trade URL в настройках. Перепроверь его.");
        $partner = $partner_and_token['data']['partner'];
        $token = $partner_and_token['data']['token'];

        // 3] Подготовить массив assetid вещей, которые бот должен запросить
        $assets2recieve = call_user_func(function() USE ($inventory, $items2bet_market_names) {

          $results = [];
          for($i=0; $i<count($this->data['items2bet']); $i++) {
            array_push($results, $this->data['items2bet'][$i]['assetid']);
          }
          return $results;

        });

        // 4] Сформировать сообщение для торгового предложения
        $tradeoffermessage = call_user_func(function() USE ($safecode) {
          return "Safecode: ".$safecode;
        });

        // 5] Отправить пользователю торговое предложение

          // 5.1] Отправить
          $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
            "id_bot"                => $bot2acceptbet->id,
            "steamid_partner"  			=> $this->data['players_steamid'],
            "id_partner"            => $partner,
            "token_partner"         => $token,
            "dont_trade_with_gays"  => "1",
            "assets2send"           => [],
            "assets2recieve"        => $assets2recieve,
            "tradeoffermessage"     => $tradeoffermessage
          ]);

          // 5.2] Если возникла ошибка
          if($tradeoffer['status'] != 0)
            throw new \Exception("Не удалось отправить торговое предложение. Возможные причины: ты указал неправильный Steam Trade URL; Steam тормозит; проблемы с ботом.");

          // 5.3] Если с этим пользователем нельзя торговать из-за escrow
          if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
            throw new \Exception("Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней.");

        // 6] Подтвердить все исходящие торговые предложения бота $bot2acceptbet
        $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
          "id_bot"                => $bot2acceptbet->id,
          "need_to_ids"           => "1",
          "just_fetch_info"       => "0",
          "tradeoffer_ids"        => [
            $tradeoffer['data']['tradeofferid']
          ]
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // n] Вернуть ID торгового предложения
        return $tradeoffer['data']['tradeofferid'];

      });

      // 13. Записать необходимую информацию о ставке в БД
      call_user_func(function() USE ($sum, $user, $items2bet_market_names, $bot2acceptbet, $room, $inventory, $safecode, $tradeofferid) {

        // 1] Создать новую ставку md3_bets
        $newbet = new \M9\Models\MD3_bets();
        $newbet->tradeofferid = $tradeofferid;
        $newbet->sum_cents_at_bet_moment = $sum;
        $newbet->save();

        // 2] Связать её с пользователем $user через md2000
        // - Но номера билетов пока не указывать.
        if(!$newbet->m5_users->contains($user->id)) $newbet->m5_users()->attach($user->id);

        // 3] Связать $newbet с вещами $items2bet_market_names в нашей базе через md2001
        // - Не забыть указать item_price_at_bet_time и assetid_users.
        // - А вот assetid_bots пока не указывать.

          // 3.1] Получить коллекцию вещей $items2bet_market_names из m8.md2_items
          $items = \M8\Models\MD2_items::whereIn('name', $items2bet_market_names)->get();

          // 3.2] Пробежимся циклом по $this->data['items2bet']
          $assetid_users_arr = [];
          for($n=0; $n<count($this->data['items2bet']); $n++) {

            // 3.2.1] Находим в коллекции вещей из MD2_items соответствующую $n-й по market_name
            $item = call_user_func(function() USE ($items, $n) {
              for($i=0; $i<count($items); $i++) {
                if($items[$i]['name'] == $this->data['items2bet'][$n]['market_name'])
                  return $items[$i];
              }
            });
            if(empty($item))
              throw new \Exception("Вещь '".$this->data['items2bet'][$n]['market_name']."' неизвестна системе, поэтому её нельзя поставить.");

            // 3.2.2] Находим в $inventory соответствующую $n-й по market_name
            $item_inventory = call_user_func(function() USE ($inventory, $n, &$assetid_users_arr) {
              for($i=0; $i<count($inventory['data']['rgDescriptions']); $i++) {
                if($inventory['data']['rgDescriptions'][$i]['market_name'] == $this->data['items2bet'][$n]['market_name']) {
                  if(!in_array($inventory['data']['rgDescriptions'][$i]['assetid'], $assetid_users_arr)) {
                    array_push($assetid_users_arr, $inventory['data']['rgDescriptions'][$i]['assetid']);
                    return $inventory['data']['rgDescriptions'][$i];
                  }
                }
              }
            });
            if(empty($item_inventory))
              throw new \Exception("Вещь '".$this->data['items2bet'][$n]['market_name']."' неизвестна системе, поэтому её нельзя поставить.");

            // 3.2.3] Связать $newbet с $item
            if(!$newbet->m8_items->contains($item->id)) $newbet->m8_items()->attach($item->id, ['item_price_at_bet_time' => round($item['price'] * 100), 'assetid_users' => $item_inventory['assetid']]);

          }

        // 4] Связать $newbet с ботом $bot2acceptbet
        if(!$newbet->m8_bots->contains($bot2acceptbet->id)) $newbet->m8_bots()->attach($bot2acceptbet->id);

        // 5] Связать $newbet с $room
        if(!$newbet->rooms->contains($room->id)) $newbet->rooms()->attach($room->id);

        // 6] Записать код безопасности $safecode в md6_safecodes
        $newsafecode = new \M9\Models\MD6_safecodes();
        $newsafecode->code = $safecode;
        $newsafecode->save();

        // 7] Связать $safecode и $newbet через md1007
        if(!$newbet->safecodes->contains($newsafecode->id)) $newbet->safecodes()->attach($newsafecode->id);

        // 8] Связать $newbet со статусом "Active" в md8_bets_statuses

          // 8.1] Получить статус "Active" из md8_bets_statuses
          $status = \M9\Models\MD8_bets_statuses::find(2);

          // 8.2] Связать $newbet с $status
          // - Не забыв указать expired_at
          if(!$newbet->bets_statuses->contains($status->id)) $newbet->bets_statuses()->attach($status->id, ['expired_at'=>\Carbon\Carbon::now()->addSeconds((int)round($room->offers_timeout_sec))->toDateTimeString()]);

      });

      // 14. Сделать коммит
      DB::commit();

      // 15. Обновить весь кэш
      $result = runcommand('\M9\Commands\C13_update_cache', [
        "all" => true
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // m. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "safecode"        => $safecode,
          "tradeofferid"    => $tradeofferid,
          "current_or_next" => "в текущий раунд",
          "expire_in_secs"  => $room->offers_timeout_sec,
          "bets_active"     => json_decode(Cache::tags(['processing:bets:active:personal:safe'])->get('processing:bets:active:safe:'.json_decode(session('auth_cache'), true)['user']['id']), true) ?: [],
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C9_make_tradeoffer_2accept_thebet from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C9_make_tradeoffer_2accept_thebet']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $errortext,
            "errormsg" => $e->getMessage()
          ]
        ];
    }}); if(!empty($res)) return $res;


    //---------------------//
    // N. Вернуть статус 0 //
    //---------------------//
    return [
      "status"  => 0,
      "data"    => ""
    ];

  }

  //--------------//
  // Д. Заготовки //
  //--------------//
  /**
   *
   * Д1. Провести валидацию
   * Д2. Начать транзакцию
   * Д3. Подтвердить транзакцию
   * Д4. Сохранить данные в БД
   * Д5. Создать элемент
   * Д6. Удалить элемент
   *
   */


    // Д1. Провести валидацию //
    //------------------------//

      //// x. Провести валидацию
      //
      //  // Создать объект-валидатор
      //  $validator = Validator::make($this->data, [
      //    'prop1'               => 'sometimes|rule1',
      //    'prop2'               => 'sometimes|rule2',
      //    'prop3'               => 'sometimes|rule3',
      //  ]);
      //
      //  // Провести валидацию, и если она провалилась...
      //  if ($validator->fails()) {
      //
      //    // Вернуть статус -2 и ошибку
      //    return [
      //      "status"  => -2,
      //      "data"    => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
      //    ];
      //
      //  }


    // Д2. Начать транзакцию //
    //-----------------------//

      //// x. Начать транзакцию
      //DB::beginTransaction();


    // Д3. Подтвердить транзакцию //
    //----------------------------//

      //// x. Подтвердить транзакцию
      //DB::commit();


    // Д4. Сохранить данные в БД //
    //---------------------------//

      //// x. Сохранить данные в БД
      //try {
      //
      //  // 4.1. Получить eloquent-объект
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 4.2. Сохранить в v присланные данные
      //  foreach($this->data as $key => $value) {
      //
      //    // Если $key == 'id', перейти к следующей итерации
      //    if($key == 'id') continue;
      //
      //    // Если в таблице есть столбец $key
      //    if(lib_hasColumn('m1', 'MD1_somemodel', $key)) {
      //
      //      // Изменить значение столбца $key на $value
      //      $item[$key] = $value;
      //
      //      // Сохранить изменения
      //      $item->save();
      //
      //    }
      //
      //  }
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д5. Создать элемент //
    //---------------------//

      //// x. Создать новый элемент
      //try {
      //
      //  // 1] Попробовать найти удалённый элемент
      //  $item = \M7\Models\MD1_somemodel::onlyTrashed()->where('name','=',$this->data['name'])->first();
      //
      //  // 2] Если удалённый элемент с таким именем не найдн
      //  if(empty($item)) {
      //
      //    // 2.1] Создать новый элемент
      //    $item = new \M1\Models\MD1_somemodel();
      //
      //    // 2.2] Наполнить $new данными
      //    $item->name                        = $this->data['name'];
      //    $item->description                 = $this->data['description'];
      //
      //    // 2.3] Сохранить $new в БД
      //    $item->save();
      //
      //  }
      //
      //  // 3] Если удалённый элемент найден
      //  if(!empty($item)) {
      //
      //    // 3.1] Восстановить его
      //    $item->restore();
      //
      //    // 3.2] Обновить некоторые свойства права
      //    $item->description                 = $data['description'];
      //
      //    // 3.3] Сохранить изменения
      //    $item->save();
      //
      //  }
      //
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д6. Удалить элемент //
    //---------------------//

      //// x. Удалить элемент
      //try {
      //
      //  // 1] Получить eloquent-модель элемента, который требуется удалить
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 2] Удалить элемент
      //  $item->delete();
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}




}

?>
