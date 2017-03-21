<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Add notes about future trade offers of this purchase to database
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        players_steamid
 *        items2buy
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

  namespace M14\Commands;

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
class C5_buy extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить текущий Unified Actual Cache (UAC) и актуальный кэш скинов на заказ
     *  3. Проверить, все ли items2buy есть в UAC, и items2order в $ordercache
     *  4. Проверить, не зарезервированы ли вещи из items2buy
     *  5. Получить из БД все вещи, указанные в items2buy, которые получится найти
     *  6. Проверить, не отсутствуют ли в $items какие-либо вещи из items2buy
     *  7. Сгенерировать случайный код безопасности
     *  8. Получить из $uac все items2buy, и из $ordercache все items2order
     *  9. Получить индекс вещей из $items2buy_from_uac, доступных по ID бота
     *  10. Получить игрока, который хочет совершить покупку, и проверить валидность его Trade URL
     *  11. Подсчитать общую стоимость покупки в монетах
     *  12. Проверить, есть ли у $user в кошельке достаточно монет для покупки
     *  13. Записать в БД информацию о покупке
     *  14. Записать необходимую информацию о трейдах в БД
     *  15. Сделать коммит
     *  16. Обновить весь кэш
     *  17. Добавить команду C20_to_order_skins_processing в очередь m14_processor
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------//
    // Add notes about future trade offers of this purchase to database //
    //-------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "players_steamid" => ["required", "regex:/^[0-9]+$/ui"],
        "items2buy"       => ["required_without:items2order", "array"],
        "items2order"     => ["required_without:items2buy", "array"],
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Получить текущий Unified Actual Cache (UAC) и актуальный кэш скинов на заказ

        // Unified Actual Cache (UAC)
        $uac = json_decode(Cache::get("M14:goods"), true);
        if(empty($uac))
          $uac = [];

        // Актуальный кэш скинов на заказ
        $ordercache = json_decode(Cache::get("M14:goods:order"), true);
        if(empty($ordercache))
          $ordercache = [];

      // 3. Проверить, все ли items2buy есть в UAC, и items2order в $ordercache

        // 3.1. Проверить, есть ли все items2buy в UAC
        $is_all_items2buy_in_uac = call_user_func(function() USE ($uac) {

          // 1] Подготовить переменную для результата
          $result = true;

          // 2] Произвести проверку
          foreach($this->data['items2buy'] as $item2buy) {
            $semi = false;
            foreach($uac as $item) {
              if($item['assetid'] == $item2buy['assetid'] && $item['classid'] == $item2buy['classid'] && $item['instanceid'] == $item2buy['instanceid']) {
                $semi = true;
                break;
              }
            }
            if($semi == false)
              $result = false;
          }

          // 3] Вернуть результат
          return $result;

        });

        // 3.2. Проверить, есть ли все items2order в $ordercache
        $is_all_items2order_in_uac = call_user_func(function() USE ($ordercache) {

          // 1] Подготовить переменную для результата
          $result = true;

          // 2] Произвести проверку
          foreach($this->data['items2order'] as $items2order) {
            $semi = false;
            foreach($ordercache as $item) {
              if($item['assetid'] == $items2order['assetid']) {
                $semi = true;
                break;
              }
            }
            if($semi == false)
              $result = false;
          }

          // 3] Вернуть результат
          return $result;

        });

        // 3.3. Если не все, завершить с ошибкой
        if($is_all_items2buy_in_uac == false || $is_all_items2order_in_uac == false)
          throw new \Exception("1");

      // 4. Проверить, не зарезервированы ли вещи из items2buy
      // - Зарезервированными считаются оплаченные вещи,
      //   связанные с активными (2) или принятыми (3) трейдами.

        // 4.1. Получить все интересующие нас трейды
        $trades_with_reserved_items = \M14\Models\MD4_trades::with(['m8_items'])
          ->where(function($query) {
            $query->where('payment_status_id', 2)
              ->where(function($query) {
                  $query->where('id_status', 0)
                      ->orWhere('id_status', 2)
                      ->orWhere('id_status', 3);
                });
          })->get();

        // 4.2. Проверить, есть ли хоть 1-на из items2buy среди вещей трейдов $trades_with_reserved_items
        $is_any_items2buy_already_reserved = call_user_func(function() USE ($trades_with_reserved_items) {

          // 1] Подготовить переменную для результата
          $result = false;

          // 2] Произвести проверку
          foreach($this->data['items2buy'] as $item2buy) {
            $semi = false;
            foreach($trades_with_reserved_items as $trade) {
              foreach($trade['m8_items'] as $item) {
                if($item['pivot']['assetid_bots'] == $item2buy['assetid']) {
                  $semi = true;
                  break 2;
                }
              }
            }
            if($semi == true)
              $result = true;
          }

          // 3] Вернуть результат
          return $result;

        });

        // 4.3. Если некоторые из items2buy уже зарезервированы, вернуть ошибку
        if($is_any_items2buy_already_reserved == true)
          throw new \Exception("2");

      // 5. Получить из БД все вещи, указанные в items2buy, которые получится найти
      $items = call_user_func(function(){

        // 1] Получить массив market_name из items2buy
        $items2buy_market_names = call_user_func(function(){
          $results = [];
          for($i=0; $i<count($this->data['items2buy']); $i++) {
            array_push($results, $this->data['items2buy'][$i]['market_name']);
          }
          return $results;
        });

        // 2] Получить из БД коллекцию всех вещей с $items2buy_market_names
        $items = \M8\Models\MD2_items::whereIn('name', $items2buy_market_names)->get();

        // n] Вернуть результаты
        return $items;

      });

      // 6. Проверить, не отсутствуют ли в $items какие-либо вещи из items2buy

        // 6.1. Проверить
        $absent_item_names = call_user_func(function() USE ($items) {

          // 1] Получить массив market_name из items2buy
          $items2buy_market_names = collect($this->data['items2buy'])->pluck('market_name')->toArray();

          // 2] Получить массив market_name из $items
          $items_market_names = $items->pluck('name')->toArray();

          // n] Вернуть результаты
          return collect($items2buy_market_names)->diff($items_market_names)->toArray();

        });

        // 6.2. Если отсутствующие есть, вернуть ошибку
        if(!is_array($absent_item_names) || !empty($absent_item_names) || count($absent_item_names) > 0)
          throw new \Exception("3");

      // 7. Сгенерировать случайный код безопасности
      // - Он представляет из себя число из 6 цифр.
      // - У каждого кода безопасности есть свой срок годности.
      $safecode = call_user_func(function() {
        $result = '';
        for($i = 0; $i < 6; $i++) {
          $result .= mt_rand(0, 9);
        }
        return $result;
      });

      // 8. Получить из $uac все items2buy, и из $ordercache все items2order

        // 8.1. Все items2buy из $uac
        $items2buy_from_uac = call_user_func(function() USE ($uac) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Наполнить $results
          foreach($this->data['items2buy'] as $item2buy) {
            foreach($uac as $item) {
              if($item['assetid'] == $item2buy['assetid'] && $item['classid'] == $item2buy['classid'] && $item['instanceid'] == $item2buy['instanceid']) {
                array_push($results, $item);
                break;
              }
            }
          }

          // 3] Вернуть результаты
          return $results;

        });

        // 8.2. Все $ordercache из items2order
        $items2order_from_ordercache = call_user_func(function() USE ($ordercache) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Наполнить $results
          foreach($this->data['items2order'] as $item2order) {
            foreach($ordercache as $item) {
              if($item['assetid'] == $item2order['assetid']) {
                array_push($results, $item);
                break;
              }
            }
          }

          // 3] Вернуть результаты
          return $results;

        });

      // 9. Получить индекс вещей из $items2buy_from_uac, доступных по ID бота
      // - Вида:
      //
      //  [
      //    <id bot> => [<вещи>],
      //    ...
      //  ]
      //
      $bot_items_index = call_user_func(function() USE ($items2buy_from_uac) {

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Получить из конфига название группы ботов магазина
        $group = config("M14.bot_group_name_to_accept_deposits");

        // 3] Получить всех ботов из этой группы
        $bots = \M8\Models\MD1_bots::whereHas('groups', function($queue) USE ($group) {
          $queue->where('name', $group);
        })->get();

        // 4] Добавить в $results всех $bots, у которых есть $items2buy_from_uac
        foreach($bots as $bot) {

          // 4.1] Подготовить массив для связанных с этим ботом вещей из $items2buy_from_uac
          $items = [];

          // 4.2] Получить товары, которые есть у бота
          $bot_goods = json_decode(Cache::get("M14:goods:".$bot['steamid']), true);
          if(empty($bot_goods))
            $bot_goods = [];

          // 4.3] Если среди $bot_goods есть хоть 1 из $items2buy_from_uac, добавить $bot в $results
          foreach($items2buy_from_uac as $item2buy) {
            foreach($bot_goods as $bot_good) {
              if($item2buy['assetid'] == $bot_good['assetid'] && $item2buy['classid'] == $bot_good['classid'] && $item2buy['instanceid'] == $bot_good['instanceid']) {
                if(!in_array($bot['id'], $items))
                  array_push($items, $item2buy);
              }
            }
          }

          // 4.4] Добавить $items в $results, если $items не пуст
          if(count($items) > 0)
            $results[$bot['id']] = $items;

        }

        // n] Вернуть результаты
        return $results;

      });

      // 10. Получить игрока, который хочет совершить покупку, и проверить валидность его Trade URL

        // 10.1. Получить игрока
        $user = \M5\Models\MD1_users::where('ha_provider_uid', $this->data['players_steamid'])->first();
        if(empty($user))
          throw new \Exception("Не удалось найти в базе данных запись о твоём профиле.");

        // 10.2. Получить первого бота из $bot_items_index
        $bot = \M8\Models\MD1_bots::where('id', array_keys($bot_items_index)[0])->first();

        // 10.3. Проверить валидность его Trade URL
        $is_users_tradeurl_valid = call_user_func(function() USE ($user, $bot) {

          // 1] Получить steam_tradeurl пользователя $user
          $steam_tradeurl = $user['steam_tradeurl'];

          // 2] Получить "Partner ID" и "Token" из торгового URL
          $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
            "trade_url" => $steam_tradeurl
          ]);
          if($partner_and_token['status'] != 0)
            return false;

          // 3] Получить steamname и steamid по торговому URL
          $result = runcommand('\M8\Commands\C30_get_steamname_and_steamid_by_tradeurl', [
            "id_bot"  => $bot['id'],
            "partner" => $partner_and_token['data']['partner'],
            "token"   => $partner_and_token['data']['token']
          ]);
          if($result['status'] != 0)
            return false;

          // n] Вернуть true
          return true;

        });

        // 10.4. Если Trade URL не валиден, создать ошибку и сообщить об этом
        if($is_users_tradeurl_valid == false)
          throw new \Exception('Вероятно, указанный Вами в профиле торговый URL не валиден. Пожалуйста, перейдите в профиль и укажите валидный торговый URL.');

      // 11. Подсчитать общую стоимость покупки в монетах
      $purchase_sum_coins = call_user_func(function() USE ($items2buy_from_uac, $items2order_from_ordercache) {

        $result = 0;
        foreach($items2buy_from_uac as $item) {
          $result = +$result + round($item['price']*100);
        }
        foreach($items2order_from_ordercache as $item) {
          $result = +$result + round($item['price']*100);
        }
        return $result;

      });

      // 12. Проверить, есть ли у $user в кошельке достаточно монет для покупки

        // 12.1. Получить баланс пользователя в центах
        $balance = call_user_func(function() USE ($user) {

          // 1] Получить
          $balance  = runcommand('\M13\Commands\C2_get_balance', [
            "id_user" => $user['id']
          ]);
          if($balance['status'] != 0)
            throw new \Exception($balance['data']['errormsg']);

          // 2] Вернуть результат
          return $balance['data']['balance'];

        });

        // 12.2. Если на балансе недостаточно средств, завершить с ошибкой
        if((int)$purchase_sum_coins > (int)$balance)
          throw new \Exception("4");

      // 13. Записать в БД информацию о покупке

        // 13.1. Создать новую покупку
        $new_purchase = new \M14\Models\MD1_purchases();
        $new_purchase->purchase_sum_coins = $purchase_sum_coins;
        $new_purchase->save();

        // 13.2. Связать новую покупку с пользователем $user
        if(!$new_purchase->m5_users->contains($user['id']))
          $new_purchase->m5_users()->attach($user['id']);

      // 14. Записать необходимую информацию о трейдах в БД
      foreach($bot_items_index as $id_bot => $bot_items) {

        // 1] Подсчитать суммарную стоимость $bot_items в центах/монетах
        $trade_sum_coins = call_user_func(function() USE ($bot_items) {

          $result = 0;
          foreach($bot_items as $item) {
            $result = +$result + round($item['price']*100);
          }
          return $result;

        });

        // 2] Создать новый трейд в md4_trades
        $new_trade = new \M14\Models\MD4_trades();
        $new_trade->trade_sum_coins = $trade_sum_coins;
        $new_trade->save();

        // 3] Связать его с пользователем $user
        if(!$new_trade->m5_users->contains($user->id))
          $new_trade->m5_users()->attach($user->id);

        // 4] Связать его с ботом $id_bot
        if(!$new_trade->m8_bots->contains($id_bot))
          $new_trade->m8_bots()->attach($id_bot);

        // 5] Связать $new_trade с вещами $bot_items в нашей базе
        // - Не забыть указать price_coins_atbettime, price_cents_atbettime и assetid_bots.
        foreach($bot_items as $item) {

          // 5.1] Найти в $items вещь по $market_name
          $item_from_db = call_user_func(function() USE ($items, $item) {
            for($i=0; $i<count($items); $i++) {
              if($items[$i]['name'] == $item['market_name'])
                return $items[$i];
            }
            return "";
          });
          if(empty($item))
            throw new \Exception("Вещь '".$item['market_name']."' неизвестна системе.");

          // 5.2] Связать $new_trade с $item
          if(!$new_trade->m8_items->contains($item_from_db['id']))
            $new_trade->m8_items()->attach($item_from_db['id'], ['price_coins_atbettime' => round($item['price'] * 100), 'price_cents_atbettime' => round($item['price'] * 100), 'assetid_bots' => $item['assetid']]);

        }

        // 6] Записать код безопасности $safecode в md6_safecodes
        $newsafecode = new \M14\Models\MD6_safecodes();
        $newsafecode->code = $safecode;
        $newsafecode->save();

        // 7] Связать $safecode и $newbet через md1007
        if(!$new_trade->safecodes->contains($newsafecode->id)) $new_trade->safecodes()->attach($newsafecode->id);

        // 8] Связать $new_trade с $new_purchase, если они ещё не связаны
        if(!$new_purchase->trades->contains($new_trade['id']))
          $new_purchase->trades()->attach($new_trade['id']);

      }

      // 15. Сделать коммит
      DB::commit();

      // 16. Обновить весь кэш
      runcommand('\M14\Commands\C7_update_cache', [
        "all"   => true,
      ]);

      // 17. Добавить команду C20_to_order_skins_processing в очередь m14_processor
      runcommand('\M14\Commands\C20_to_order_skins_processing', [
        "id_user"         => $user['id'],
        "steamid"         => $this->data['players_steamid'],
        "steam_tradeurl"  => $user['steam_tradeurl'],
        "id_purchase"     => $new_purchase['id'],
        "items2order"     => $items2order_from_ordercache
      ], 0, ['on'=>false, 'delaysecs'=>'5', 'name' => 'm14_processor']);

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => ""
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C5_buy from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C5_buy']);
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

