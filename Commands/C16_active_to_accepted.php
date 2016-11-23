<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Convert the active trade offer to accepted
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        betid
 *        tradeofferid
 *        id_user
 *        id_room
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
class C16_active_to_accepted extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить и проверить входящие данные
     *  2. Получить ставку с betid и tradeofferid
     *  3. Получить статусы Active и Accepted
     *  4. Отвязать ставку от статуса $status_active, привязать к $status_accepted
     *  5. Записать assetid_bots в md2001
     *  6. Вычислить, можно ли пользователю id_user разместить ещё одну ставку в посл.раунд комнаты id_room
     *  7. Принять ставку в текущий раунд, если $canwe_makeabet['verdict'] == true
     *  8. Если принять ставку в текущий раунд нельзя, отложить до следующего
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------------------//
    // Сконвертировать оффер со статусом Active в оффер со статусом Accepted в БД //
    //----------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      $time = \Carbon\Carbon::now()->toTimeString();
      write2log("C16START: $time", []);

      DB::beginTransaction();

      // 1. Получить и проверить входящие данные
      $validator = r4_validate($this->data, [

        "betid"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "tradeofferid"      => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_user"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_room"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Получить ставку с betid и tradeofferid
      $bet = \M9\Models\MD3_bets::with(['bets_statuses', 'm8_items', 'm8_bots'])
          ->where('id', $this->data['betid'])
          ->where('tradeofferid', $this->data['tradeofferid'])
          ->first();
      if(empty($bet))
        throw new \Exception('Не удалось найти ставку в m9.md3_bets по id = '.$this->data['betid']);

      // 3. Получить статусы Active и Accepted
      $status_active = \M9\Models\MD8_bets_statuses::where('status', 'Active')->first();
      $status_accepted = \M9\Models\MD8_bets_statuses::where('status', 'Accepted')->first();
      if(empty($status_active) || empty($status_accepted))
        throw new \Exception('Не удалось найти статусы Active или Accepted в m9.md8_bets_statuses');

      // 4. Отвязать ставку от статуса $status_active, привязать к $status_accepted
      if($bet->bets_statuses->contains($status_active->id)) $bet->bets_statuses()->detach($status_active->id);
      if(!$bet->bets_statuses->contains($status_accepted->id))$bet->bets_statuses()->attach($status_accepted->id);

      // 5. Записать assetid_bots в md2001
      // - Это assetid принятых ботом в виде ставки скинов.
      call_user_func(function() USE (&$bet) {

        // 1] Получить список всех связанных с $bet скинов
        $bet_items = $bet->m8_items;

        // 2] Получить Steam ID бота, связанного с $bet
        $bet_bot_steamid = $bet->m8_bots[0]['steamid'];

        // 3] Получить инвентарь бота, связанного с $bet
        $bet_bot_inventory = runcommand('\M8\Commands\C4_getinventory', [
          "steamid" => $bet_bot_steamid,
          "force"   => true
        ]);
        if($bet_bot_inventory['status'] != 0)
          throw new \Exception($bet_bot_inventory['data']['errormsg']);

        // 4] Для каждого скина в $bet_items заполнить поле assetid_bots
        call_user_func(function() USE (&$bet, &$bet_items, $bet_bot_inventory){

          // 4.1] Подготовить массив для assetid
          // - Уже найденных в $bet_bot_inventory скинов.
          $assetids_found = [];

          // 4.2] Пробежаться по каждому скину в $bet_items
          foreach($bet_items as &$item) {

            // 4.2.1] Найти соответствие для $item в $bet_bot_inventory
            // - По "name" ($item) и "market_hash_name" ($bet_bot_inventory)
            // - И записать найденный assetid в $item;
            call_user_func(function() USE (&$bet_items, &$bet, &$item, &$assetids_found, $bet_bot_inventory) {
              foreach($bet_bot_inventory['data']['rgDescriptions'] as $item_in_inventory) {
                if($item_in_inventory['market_hash_name'] == $item['name']) {
                  if(!in_array($item_in_inventory['assetid'],$assetids_found)) {
                    array_push($assetids_found, $item_in_inventory['assetid']);
                    $bet->m8_items()->updateExistingPivot($item['id'], ["assetid_bots" => $item_in_inventory['assetid']]);
                    break;
                  }
                }
              }
            });

          }

        });


      });

      // 6. Вычислить, можно ли пользователю id_user разместить ещё одну ставку в посл.раунд комнаты id_room
      // - Можно, если выполняются следующие условия:
      //
      //    • Текущий раунд существует
      //    • Текущий статус раунда <= 3
      //    • Без учета ставки, не превышен лимит по сумме банка.
      //    • Без учета ставки, не первышен лимит по кол-ву вещей.
      //    • Без учета ставки, не первышен лимит по кол-ву ставок для этого пользователя.
      //
      $canwe_makeabet = call_user_func(function(){

        $result = runcommand('\M9\Commands\C22_canwe_makeabet_intheroom_now', [
          "id_room" => $this->data['id_room'],
          "id_user" => $this->data['id_user']
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);
        return $result['data'];

      });

      // 7. Принять ставку в текущий раунд, если $canwe_makeabet['verdict'] == true
      if($canwe_makeabet['verdict'] == true) {

        // 7.1. Получить ссылку на массив споследним раундом в комнате id_room
        $lastround = $canwe_makeabet['lastround'];

        // 7.2. Добавить tickets_from / tickets_to в md2000
        // - Добавлять билеты:
        //  • Исходя из того, что 1 цент == 1 билет.
        //  • И исходя из уже связанных с раундом ставок.
        call_user_func(function() USE ($lastround, $bet) {

          // 1] Вычислить число первого билета для новой ставки
          // - Это последний билет последней ставки раунда $lastround комнате id_room + 1
          $first_ticket_number = call_user_func(function() USE ($lastround) {

            // 1.1] Получить последнюю ставку, связанную с раундом $lastround
            $lastround_last_bet = \M9\Models\MD3_bets::with(['m5_users'])
                ->whereHas('rounds', function($query) USE ($lastround) {
                  $query->where('id', $lastround['id']);
                })
                ->orderBy('id', 'desc')
                ->first();

            // 1.2] Если $lastround_last_bet не найден, вернуть 0
            if(empty($lastround_last_bet)) return 0;

            // 1.3] Если найден, вернуть его tickets_to
            return +$lastround_last_bet['m5_users'][0]['pivot']['tickets_to']+1;

          });

          // 2] Вычислить сумму ставки $bet в центах
          $bet_sum_cents = call_user_func(function() USE ($bet) {

            $result = 0;
            foreach($bet['m8_items'] as $item) {
              $result = +$result + +$item['price'];
            }
            return round($result*100);

          });

          // 3] Вычислить tickets_from и tickets_to для $bet
          $tickets = call_user_func(function() USE ($first_ticket_number, $bet_sum_cents){

            return [
              "tickets_from"  => $first_ticket_number,
              "tickets_to"    => +$first_ticket_number + +$bet_sum_cents - 1
            ];

          });

          // 4] Добавить tickets_from / tickets_to в md2000
          $bet->m5_users()->updateExistingPivot($this->data['id_user'], ["tickets_from" => $tickets["tickets_from"], "tickets_to" => $tickets["tickets_to"]]);

        });

        // 7.3. Связать ставку с текущим раундом через md1010
        if(!$bet->rounds->contains($lastround['id'])) $bet->rounds()->attach($lastround['id']);

        // 7.4. Отвязать ставку от комнаты, убрав запись из md1009
        if($bet->rooms->contains($this->data['id_room'])) $bet->rooms()->detach($this->data['id_room']);

        // 7.5. Сделать commit
        DB::commit();

        // 7.6. Обновить весь кэш
        // - Но только, если он не был обновлён в C18.
        // - А там он обновляется только лишь при изменении статуса
        //   любого из раундов, любой из комнат.
        $result = runcommand('\M9\Commands\C13_update_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 7.7. Выполнить C18_round_statuses_tracking
        // - Что позволит в случае необходимости обновить статус раунда.
        // - Но при этом, C18 не будет отправлять данные игры через
        //   публичный канал, если итоговый статус <= 3.
        $status_tracking = runcommand('\M9\Commands\C18_round_statuses_tracking', []);
        if($status_tracking['status'] != 0)
          throw new \Exception($status_tracking['data']['errormsg']);

        // 7.8. Транслировать через публичный канал свежие игровые данные
        // - Но только, если они не были уже транслированы в C18
        if($status_tracking['data']['is_cache_was_updated'] == false) {

          // 1] Получить свежие игровые данные
          $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1]);
          if($allgamedata['status'] != 0)
            throw new \Exception($allgamedata['data']['errormsg']);

          // 2] Сообщить всем игрокам через публичный канал websockets свежие игровые данные
          Event::fire(new \R2\Broadcast([
            'channels' => ['m9:public'],
            'queue'    => 'm9_lottery_broadcasting',
            'data'     => [
              'task' => 'fresh_game_data',
              'data' => [
                'rooms' => $allgamedata['data']['rooms']
              ]
            ]
          ]));

        }

        // 7.9. Сообщить игроку через публичный канал, что его ставка принята в текущий раунд
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$this->data['id_user']],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'tradeoffer_accepted',
            'data' => [
              'id_room'           => $this->data['id_room'],
              'in_current_round'  => true,
              'bets_active'       => json_decode(Cache::tags(['processing:bets:active:personal'])->get('processing:bets:active:'.$this->data['id_user']), true) ?: [],
            ]
          ]
        ]));

      }

      // 8. Если принять ставку в текущий раунд нельзя, отложить до следующего
      else {

        // 8.1. Сделать commit
        DB::commit();

        // 8.2. Обновить весь кэш
        $result = runcommand('\M9\Commands\C13_update_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 8.3. Сообщить игроку через публичный канал, что:
        // - Его ставка принята, но в текущий раунд она не попала.
        // - Она поставлена в очередь, и появится в соответствии с ней,
        //   и с правилами комнаты, в одном из следующих раундов.
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$this->data['id_user']],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'tradeoffer_cancel',
            'data' => [
              'id_room'           => $this->data['id_room'],
              'in_current_round'  => false,
              'bets_active'       => json_decode(Cache::tags(['processing:bets:active:personal'])->get('processing:bets:active:'.$this->data['id_user']), true) ?: [],
            ]
          ]
        ]));

      }

      DB::commit();
      write2log("C16START: $time; C16END: ".\Carbon\Carbon::now()->toTimeString(), []);

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C16_active_to_accepted from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C16_active_to_accepted']);
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

