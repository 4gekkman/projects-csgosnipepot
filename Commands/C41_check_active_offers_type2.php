<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Check if new incoming offers appeared
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
use Mockery\CountValidator\Exception;

// Доп.классы, которые использует эта команда


//---------//
// Команда //
//---------//
class C41_check_active_offers_type2 extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить массив ID ботов, обслуживающих текущий раунд во всех включенных комнатах
     *  2. Для каждой комнаты получить ID бота, обслуживающего предыдущий раунд
     *  3. Получить массив ID ботов, обслуживающих текущий раунд во всех включенных комнатах
     *  4. Каждым ботом из (3) запросить через API и обработать свежие данные по активным входящим офферам
     *    4.1. Получить все активные входящие офферы для бота $id_bot
     *    4.2. Если активных входящих офферов нет, перейти к следующей итерации
     *    4.3. Получить массив активных входящих офферов
     *    4.4. Получить все активные офферы типа 2
     *    4.5. Отфильтровать из $offers те офферы, которые уже есть в $active_offers_type2
     *    4.6. Добавить все офферы из $offers в БД
     *      4.6.1. Начать транзакцию
     *      4.6.2. Создать новый экземпляр ставки
     *      4.6.3. Назначить ставке $newbet статус Active
     *      4.6.4. Связать $newbet с $room
     *      4.6.5. Связать $newbet с пользователем
     *      4.6.6. Связать $newbet с ботом
     *      4.6.7. Связать $newbet с вещами из ставки
     *      4.6.8. Подсчитать и записать общую сумму ставки в центах
     *      4.6.9. Сохранить $newbet
     *      4.6.10. Подтвердить транзакцию
     *      4.6.11. Обновить весь кэш
     *      4.6.12. Уведомить пользователя $user по частному каналу, что его оффер в обработке
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------//
    // Check if new incoming offers appeared //
    //---------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить актуальную информацию по всем вкл.комнатам и последним раундам
      $rooms = json_decode(Cache::get('processing:rooms'), true);
      if(empty($rooms))
        throw new Exception('Отсутствует кэш processing:rooms');

      // 2. Для каждой комнаты получить ID бота, обслуживающего предыдущий раунд
      // - По ID комнаты можно будет узнать ID бота, обслуживающего предыдущий раунд.
      // - Если ID бота найти не удалось, там будет пустая строка.
      $rooms_penultimate_round_bot_ids = call_user_func(function() USE ($rooms) {

        // 1] Подготовить массив для результата
        $results = [];

        // 2] Наполнить $results
        foreach($rooms as $room) {
          if(count($room['rounds']) >= 2) {
            $results[$room['id']] = $room['rounds'][1]['bets'][0]['m8_bots'][0]['id'];
          }
          else {
            $results[$room['id']] = '';
          }
        }

        // n] Вернуть результат
        return $results;

      });

      // 3. Получить массив ID ботов, обслуживающих текущий раунд во всех включенных комнатах
      // - По ID комнаты можно будет узнать ID бота, обслуживающего текущий раунд.
      // - Если ID бота найти не удалось, там будет пустая строка.
      $bot_ids = call_user_func(function() USE ($rooms, $rooms_penultimate_round_bot_ids) {

        // 1] Подготовить массив для результатов
        $ids = [];

        // 2] Наполнить $bot_ids
        foreach($rooms as $room) {

          // 2.1] Получить массив ID всех ботов, обслуживающих комнату $room
          $room_bot_ids = collect($room['m8_bots'])->pluck('id')->toArray();

          // 2.2] Если $room_bot_ids пуст, записать пустую строку в $ids
          if(empty($room_bot_ids) || count($room_bot_ids) == 0)
            $ids[$room['id']] = '';

          // 2.3] В противном случае
          else {

            // 2.3.1] Получить ID бота, обслуживающего $room в предыдущем раунде
            $penult_bot_id = $rooms_penultimate_round_bot_ids[$room['id']];

            // 2.3.2] Если $penult_bot_id пуст, добавить в $ids первого из $room_bot_ids
            if(empty($penult_bot_id))
              $ids[$room['id']] = $room_bot_ids[0];

            // 2.3.3] В противном случае
            else {

              // 2.3.3.1] Найти позицию вхождения $penult_bot_id в $room_bot_ids
              $pos = array_search($penult_bot_id, $room_bot_ids);

              // 2.3.3.2] Если $pos не найдена, добавить в $ids первого из $room_bot_ids
              if(empty($pos))
                $ids[$room['id']] = $room_bot_ids[0];

              // 2.3.3.3] В противном случае
              else {

                // 2.3.3.3.1] Если $pos последняя, добавить в $ids первого из $room_bot_ids
                if((count($room_bot_ids))-1 == $pos)
                  $ids[$room['id']] = $room_bot_ids[0];

                // 2.3.3.3.1] Если же не последняя, добавить следующую
                else
                  $ids[$room['id']] = $room_bot_ids[+$pos+1];

              }

            }

          }

        }

        // n] Вернуть результаты
        return $ids;

      });

      // 4. Каждым ботом из (3) запросить через API и обработать свежие данные по активным входящим офферам
      foreach($bot_ids as $id_room => $id_bot) {

        // 4.1. Получить все активные входящие офферы для бота $id_bot
        $active_incoming_offers = runcommand('\M8\Commands\C19_get_tradeoffers_via_api', [
          'id_bot' => $id_bot,
          'active_only' => 1,
          'get_received_offers' => 1,
          'get_descriptions' => 1
        ]);

        if($active_incoming_offers['status'] != 0) {

          // 1] Получить домен из конфига
          $domain = config('M8.api_keys_default_domain') ?: 'csgogames.com';

          // 2] Произвести замену API-ключа для бота $id_bot
          $result = runcommand('\M8\Commands\C11_bot_set_apikey', [
            "id_bot"  => $id_bot,
            "force"   => 1,
            "domain"  => $domain
          ]);
          if($result['status'] != 0) {

            // Сообщить
            $errortext = 'Invoking of command C11_bot_set_apikey from M-package M8 have ended on line with error: '.$result['data']['errormsg'];
            Log::info($errortext);
            write2log($errortext, ['M8', 'C11_bot_set_apikey']);

          }

          // 3] Сообщить
          $errortext = 'Invoking of command C41_check_active_offers_type2 from M-package M9 have ended on line with error: '.$active_incoming_offers['data']['errormsg'];
          Log::info($errortext);
          write2log($errortext, ['M9', 'C41_check_active_offers_type2']);

          // 4] Перейти к следующей итерации
          continue;

        }

        // 4.2. Если активных входящих офферов нет, перейти к следующей итерации
        if(
            empty($active_incoming_offers) ||
            empty($active_incoming_offers['data']) ||
            empty($active_incoming_offers['data']['tradeoffers']) ||
            count($active_incoming_offers['data']['tradeoffers']) == 0
        ) continue;

        // 4.3. Получить массив активных входящих офферов
        // - Дополненный market name для всех вещей в items_to_receive.
        $offers = call_user_func(function() USE ($active_incoming_offers) {

          // 1] Подготовить массив для результатов
          $offers = [];

          // 2] Добавить в $offers все офферы из $active_incoming_offers
          foreach($active_incoming_offers['data']['tradeoffers']['trade_offers_received'] as $offer) {
            array_push($offers, $offer);
          }

          // 3] Каждой вещи в $offers добавить market name
          foreach($offers as &$offer) {

            // 3.1] Если нет items_to_receive, перейти к следующей итерации
            if(!array_key_exists('items_to_receive', $offer)) continue;

            // 3.2] Каждой вещи в items_to_receive добавить market name
            foreach($offer['items_to_receive'] as &$item) {

              // 3.2.1] Получить appid, contextid и classid вещи $item
              $appid        = $item['appid'];
              $instanceid   = $item['instanceid'];
              $classid      = $item['classid'];

              // 3.2.2] Найти соответствующий descriptions
              $description = call_user_func(function() USE ($active_incoming_offers, $appid, $instanceid, $classid) {

                // Если descriptions не передан, вернуть пустую строку
                if(!array_key_exists('descriptions', $active_incoming_offers['data']['tradeoffers']))
                  return "";

                // А если передан, найти
                foreach($active_incoming_offers['data']['tradeoffers']['descriptions'] as $description) {
                  if($appid == $description['appid'] && $instanceid == $description['instanceid'] && $classid == $description['classid'])
                    return $description;
                }

                // Если найти не удалось, вернуть пустую строку
                return "";

              });

              // 3.2.3] Если $description не найден, записать пустой market name
              if(empty($description) || !array_key_exists('market_name', $description))
                $item['market_name'] = "";

              // 3.2.4] В противном случае, записать market name
              else
                $item['market_name'] = $description['market_name'];

            }

          }

          // 4] Отфильтровать из $offers офферы, у которых trade_offer_state != 2
          $offers = array_values(array_filter($offers, function($value){
            return $value['trade_offer_state'] == 2;
          }));

          // n] Вернуть результаты
          return $offers;

        });

        // 4.4. Получить все активные офферы типа 2
        $active_offers_type2 = json_decode(Cache::get('processing:bets_type2:active'), true);

        // 4.5. Отфильтровать из $offers те офферы, которые уже есть в $active_offers_type2
        $offers = array_values(array_filter($offers, function($value) USE ($active_offers_type2) {
          return !in_array($value['tradeofferid'], collect($active_offers_type2)->pluck('tradeofferid')->toArray());
        }));

        // 4.6. Добавить все офферы из $offers в БД
        foreach($offers as $offer) {

          // 4.6.1. Начать транзакцию
          DB::beginTransaction();

          // 4.6.2. Создать новый экземпляр ставки
          $newbet = new \M9\Models\MD3_bets();
          $newbet->type = 2;
          $newbet->tradeofferid = $offer['tradeofferid'];
          $newbet->sum_cents_at_bet_moment = "";
          $newbet->escrow_end_date = call_user_func(function() USE ($offer) {

            if(array_key_exists('escrow_end_date', $offer) && $offer['escrow_end_date'] != 0)
              return 1;

            else
              return 0;

          });
          $newbet->items_to_give = call_user_func(function() USE ($offer) {

            if(array_key_exists('items_to_give', $offer) && count($offer['items_to_give']) != 0)
              return 1;

            else
              return 0;

          });
          $newbet->save();

          // 4.6.3. Назначить ставке $newbet статус Active

            // 1] Получить статус "Active" из md8_bets_statuses
            $status = \M9\Models\MD8_bets_statuses::find(2);

            // 2] Связать $newbet с $status
            // - Не забыв указать expired_at
            if(!$newbet->bets_statuses->contains($status->id)) $newbet->bets_statuses()->attach($status->id);

          // 4.6.4. Связать $newbet с $room
          if(!$newbet->rooms->contains($id_room)) $newbet->rooms()->attach($id_room);

          // 4.6.5. Связать $newbet с пользователем
          // - Но номера билетов пока не указывать.

            // 1] Получить steamid приславшего оффер пользователя
            $steamid = 76561197960265728 + $offer['accountid_other'];

            // 2] Получить пользователя, приславшего ставку
            $user = \M5\Models\MD1_users::where('ha_provider_uid', $steamid)->first();

            // 3] Если $user найден, связать его с $newbet
            if(!empty($user)) {
              if(!$newbet->m5_users->contains($user->id)) $newbet->m5_users()->attach($user->id);
            }

          // 4.6.6. Связать $newbet с ботом
          if(!$newbet->m8_bots->contains($id_bot)) $newbet->m8_bots()->attach($id_bot);

          // 4.6.7. Связать $newbet с вещами из ставки

            // 1] Получить массив market names вещей, с которыми надо связать
            $items2bet_market_names = call_user_func(function() USE ($offer) {

              // 1.1] Подготовить массив для результатов
              $results = [];

              // 1.2] Наполнить $results
              if(array_key_exists('items_to_receive', $offer)) {
                foreach($offer['items_to_receive'] as $item) {
                  array_push($results, $item['market_name']);
                }
              }

              // 1.n] Вернуть результаты
              return $results;

            });

            // 2] Получить коллекцию вещей $items2bet_market_names из m8.md2_items
            $items = \M8\Models\MD2_items::whereIn('name', $items2bet_market_names)->get();

            // 3] Проверить, каких вещей из $items2bet_market_names не хватает в $items
            // - Результат вернуть в виде массив market names не хватающих вещей
            $items_missed = call_user_func(function() USE ($items2bet_market_names, $items) {

              // 3.1] Подготовить массив для результатов
              $results = [];

              // 3.2] Наполнить $results
              foreach($items2bet_market_names as $item1) {
                foreach($items as $item2) {
                  if($item1 == $item2->name) continue 2;
                }
                array_push($results, $item1);
              }

              // 3.n] Вернуть результаты
              return $results;

            });

            // 4] Получить индекс assetid_users вещей, с которыми надо связать $newbet
            // - По market name вещи можно получить assetid
            $assetid_users_index = call_user_func(function() USE ($offer) {

              // 1.1] Подготовить массив для результатов
              $results = [];

              // 1.2] Наполнить $results
              if(array_key_exists('items_to_receive', $offer)) {
                foreach($offer['items_to_receive'] as $item) {
                  $results[$item['market_name']] = $item['assetid'];
                }
              }

              // 1.n] Вернуть результаты
              return $results;

            });

            // 5] Если $items_missed не пуст
            if(!empty($items_missed)) {

              // Записать в ставку, что есть не найденные вещи, сделать коммит, и перейти к след.итерации
              $newbet->unknown_items = json_encode($items_missed, JSON_UNESCAPED_UNICODE);
              $newbet->save();
              DB::commit();
              continue;

            }

            // 6] Если $items_missed пуст, связать с $newbet все вещи из $items
            else {
              foreach($items as $item) {

                if(!$newbet->m8_items->contains($item->id)) $newbet->m8_items()->attach($item->id, ['item_price_at_bet_time' => round($item['price'] * 100), 'assetid_users' => $assetid_users_index[$item->name]]);

              }
            }

          // 4.6.8. Подсчитать и записать общую сумму ставки в центах

            // 1] Подсчитать
            $sum_cents_at_bet_moment = call_user_func(function() USE ($items) {

              $result = 0;
              foreach($items as $item) {
                $result = +$result + +$item->price;
              }
              return round($result*100);

            });

            // 2] Записать
            $newbet->sum_cents_at_bet_moment = $sum_cents_at_bet_moment;

          // 4.6.9. Сохранить $newbet
          $newbet->save();

          // 4.6.10. Подтвердить транзакцию
          DB::commit();

          // 4.6.11. Обновить весь кэш
          $result = runcommand('\M9\Commands\C13_update_cache', [
            "cache2update" => ["processing:bets_type2:active"]
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 4.6.12. Уведомить пользователя $user по частному каналу, что его оффер в обработке
          if(!empty($user)) {

            Event::fire(new \R2\Broadcast([
              'channels' => ['m9:private:'.$user->id],
              'queue'    => 'm9_lottery_broadcasting',
              'data'     => [
                'task' => 'tradeoffer_processing',
                'data' => [
                  'tradeofferid' => $offer['tradeofferid']
                ]
              ]
            ]));

          }

        }

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C41_check_active_offers_type2 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C41_check_active_offers_type2']);
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

