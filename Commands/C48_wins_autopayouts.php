<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make wins autopayouts
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *
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
class C48_wins_autopayouts extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить все выигрыши со статусами кроме paid/expired
     *  2. Обработать каждый выигрыш индивидуально
     *    2.1. Получить ID и STEAMID победителя из $win
     *    2.2. Получить связанных с $win ботов
     *    2.3. Составить список всех вещей на выплату по выигрышу $win
     *    2.4. Если не у всех вещей из $items есть assetid, перейти к следующей итерации
     *    2.5. Обработать каждого бота из $bots индивидуально
     *      2.5.1. Если is_free == true или tradeofferid не пуст, перейти к следующей итерации
     *      2.5.2. Получить инвентарь бота $bot
     *      2.5.3. Получить массив вещей из $items, которые есть в $bet_bot_inventory
     *      2.5.4. Получить steam_tradeurl пользователя $user
     *      2.5.5. Получить partner и token пользователя из его trade url
     *      2.5.6. Проверить валидность $steam_tradeurl
     *      2.5.7. Отправить пользователю торговое предложение
     *      2.5.8. Получить модель $win
     *      2.5.9. Получить статус выигрышей Active
     *      2.5.10. Начать транзакцию
     *      2.5.11. Изменить статус $win на Active
     *      2.5.12. Обновить pivot-таблицу между $win и $bot
     *      2.5.n. Сделать commit
     *      2.5.m. Обновить весь кэш
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Make wins autopayouts //
    //-----------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить все выигрыши со статусами кроме paid/expired
      $wins_not_paid_expired = json_decode(Cache::get('processing:wins:not_paid_expired'), true);

      // 2. Обработать каждый выигрыш индивидуально
      foreach($wins_not_paid_expired as $win) {

        // 2.1. Получить ID и STEAMID победителя из $win

          // Получить
          $steamid_and_id = call_user_func(function() USE ($win) {

            // 1] Получить информацию о пользователе
            if(array_key_exists('m5_users', $win) && is_array($win['m5_users']) && count($win['m5_users']) > 0)
              $user = $win['m5_users'][0];
            else
              $user = '';

            // 2] Вернуть результат
            if(empty($user))
              return [
                "steamid" => "",
                "id"      => "",
                "user"    => ""
              ];
            else
              return [
                "steamid" => $user['ha_provider_uid'],
                "id"      => $user['id'],
                "user"    => $user
              ];

          });

          // Если получить не удалось, перейти к следующей итерации
          if(empty($steamid_and_id) || count($steamid_and_id) == 0 || empty($steamid_and_id['steamid']) || empty($steamid_and_id['id']))
            continue;

        // 2.2. Получить связанных с $win ботов

          // Получить
          $bots = $win['m8_bots'];

          // Если боты отсутствуют, перейти к следующей итерации
          if(empty($bots) || count($bots) == 0)
            continue;

        // 2.3. Составить список всех вещей на выплату по выигрышу $win
        $items = call_user_func(function() USE ($win) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Наполнить $results
          // - И добавить каждой вещи св-во percentage
          foreach($win['m8_items'] as $item)
            array_push($results, $item);

          // n] Вернуть результаты
          return $results;

        });

        // 2.4. Если не у всех вещей из $items есть assetid, перейти к следующей итерации

          // Есть ли вещи в $items, у которых пустой assetid?
          $is_empty_assetid_in_items = call_user_func(function() USE ($items) {

            $result = false;
            foreach($items as $item) {
              if(empty($item['pivot']['assetid'])) {
                $result = true;
                break;
              }
            }
            return $result;

          });

          // Если есть, перейти к следующей итерации
          if($is_empty_assetid_in_items)
            continue;

        // 2.5. Обработать каждого бота из $bots индивидуально
        foreach($bots as $bot) {

          // 2.5.1. Если is_free == true или tradeofferid не пуст, перейти к следующей итерации
          // - Это значит, что либо бот уже расплатился по $win (is_free == true).
          // - Либо это значит, что бот уже отправил оффер по $win (tradeofferid не пуст)
          if($bot['pivot']['is_free'] != 0 || !empty($bot['pivot']['tradeofferid']))
            continue;

          // 2.5.2. Получить инвентарь бота $bot

            // Получить
            $bet_bot_inventory = runcommand('\M8\Commands\C4_getinventory', [
              "steamid" => $bot['steamid'],
              "force"   => true
            ]);

            // Если инвентарь не найден, перейти к следующей итерации
            if($bet_bot_inventory['status'] != 0)
              continue;

          // 2.5.3. Получить массив вещей из $items, которые есть в $bet_bot_inventory

            // Получить
            $bot_items2payout = call_user_func(function() USE ($bet_bot_inventory, $items) {

              // 1] Подготовить массив для результатов
              $results = [
                'assetids' => [],
                'items'    => []
              ];

              // 2] Наполнить $results
              foreach($items as $item) {
                foreach($bet_bot_inventory['data']['rgDescriptions'] as $item_in_inventory) {
                  if($item_in_inventory['assetid'] == $item['pivot']['assetid']) {
                    array_push($results['items'], $item);
                    array_push($results['assetids'], $item['pivot']['assetid']);
                  }
                }
              }

              // n] Вернуть результаты
              return $results;

            });

            // Если $bot_items2payout пуст, перейти к следующей итерации
            if(count($bot_items2payout['items']) == 0 || count($bot_items2payout['assetids']) == 0)
              continue;

          // 2.5.4. Получить steam_tradeurl пользователя $user
          $steam_tradeurl = $win['m5_users'][0]['steam_tradeurl'];
          if(empty($steam_tradeurl))
            continue;

          // 2.5.5. Получить partner и token пользователя из его trade url
          $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
            "trade_url" => $steam_tradeurl
          ]);
          if($partner_and_token['status'] != 0)
            continue;

          // 2.5.6. Проверить валидность $steam_tradeurl
          $result = runcommand('\M8\Commands\C30_get_steamname_and_steamid_by_tradeurl', [
            "id_bot"  => $bot['id'],
            "partner" => $partner_and_token['data']['partner'],
            "token"   => $partner_and_token['data']['token']
          ]);
          if($result['status'] != 0)
            continue;

          // 2.5.7. Отправить пользователю торговое предложение

            // 1] Отправить
            $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
              "id_bot"                => $bot['id'],
              "steamid_partner"  			=> $win['m5_users'][0]['ha_provider_uid'],
              "id_partner"            => $partner_and_token['data']['partner'],
              "token_partner"         => $partner_and_token['data']['token'],
              "dont_trade_with_gays"  => "1",
              "assets2send"           => $bot_items2payout['assetids'],
              "assets2recieve"        => [],
              "tradeoffermessage"     => ''
            ]);

            // 2] Если возникла ошибка
            if($tradeoffer['status'] != 0 || !array_key_exists('tradeofferid', $tradeoffer['data']) || empty($tradeoffer['data']['tradeofferid']))
              continue;

            // 3] Если с этим пользователем нельзя торговать из-за escrow
            if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
              continue;

            // 4] Подтвердить все исходящие торговые предложения бота $bot2acceptbet
            $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
              "id_bot"                => $bot['id'],
              "need_to_ids"           => "0",
              "just_fetch_info"       => "0"
            ]);
            if($result['status'] != 0)
              continue;

          // 2.5.8. Получить модель $win
          $win2pay_model = \M9\Models\MD4_wins::find($win['id']);
          if(empty($win2pay_model))
            continue;

          // 2.5.9. Получить статус выигрышей Active
          $status_active = \M9\Models\MD9_wins_statuses::where('status', 'Active')->first();
          if(empty($status_active))
            continue;

          // 2.5.10. Начать транзакцию
          DB::beginTransaction();

          // 2.5.11. Изменить статус $win на Active
          // - Удалить старые связи с wins_statuses, и создать новую.
          $win2pay_model->wins_statuses()->detach();
          if(!$win2pay_model->wins_statuses->contains($status_active['id'])) $win2pay_model->wins_statuses()->attach($status_active['id'], ['started_at' => \Carbon\Carbon::now()->toDateTimeString(), 'comment' => 'Создание активного оффера (ов) для выплаты этого выигрыша победителю.']);

          // 2.5.12. Обновить pivot-таблицу между $win и $bot
          $win2pay_model->m8_bots()->updateExistingPivot($bot['id'], [
            "is_free"           => 0,
            "tradeofferid"      => $tradeoffer['data']['tradeofferid'],
            "offer_expired_at"  => \Carbon\Carbon::now()->addSeconds((int)round($win2pay_model['rounds'][0]['rooms']['offers_timeout_sec']))->toDateTimeString()
          ]);

          // 2.5.n. Сделать commit
          DB::commit();

          // 2.5.m. Обновить весь кэш
          $result = runcommand('\M9\Commands\C25_update_wins_cache', [
            "all"   => true
          ]);

        }

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C48_wins_autopayouts from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C48_wins_autopayouts']);
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

