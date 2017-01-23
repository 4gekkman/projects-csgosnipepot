<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Process active offers of type 2
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
class C42_process_active_offers_type2 extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить список активных офферов 2-го типа
     *  2. Если $active_offers_type2 не пуст, обработать их
     *    2.1. Составить чеклист причин отменить $offer
     *    2.2. Вычислить кол-во предметов в ставке, и её сумму в центах
     *    2.3. Получить все необходимые лимиты комнаты, связанной с $offer
     *    2.4. Пройтись по $checklist и проверить каждый пункт
     *
     *      • Проверяем №1 в $checklist: запрос вещей бота
     *      • Проверяем №2 в $checklist: не нулевой escrow
     *      • Проверяем №3 в $checklist: вещи не опознаны
     *      • Проверяем №4 в $checklist: оффер от анонима
     *      • Проверяем №5 в $checklist: недостаточно предметов для ставки
     *      • Проверяем №6 в $checklist: слишком много предметов для ставки
     *      • Проверяем №7 в $checklist: сумма ставки слишком мала
     *      • Проверяем №8 в $checklist: сумма ставки слишком велика
     *
     *    2.5. На основании $checklist составить итоговый вердикт об $offer
     *    2.6. Если вердикт положительный, принять оффер
     *    2.7. Если вердикт отрицательный, отклонить оффер
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------//
    // Process active offers of type 2 //
    //---------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить список активных офферов 2-го типа
      // - Если таковых нет, завершить.
      $active_offers_type2 = json_decode(Cache::get('processing:bets_type2:active'), true);

      // 2. Если $active_offers_type2 не пуст, обработать их
      if(!empty($active_offers_type2) || count($active_offers_type2) != 0) {
        foreach($active_offers_type2 as $offer) {

          // 2.1. Составить чеклист причин отменить $offer
          // - Если хоть 1-на из них true, отменяем.
          $checklist = [

            // 1] Запрос вещей бота
            'items_to_give' => [
              'code'    => 1,
              'verdict' => false,
              'desc'    => 'Вы запросили вещи нашего бота в своём торговом предложении, что неприемлимо.'
            ],

            // 2] Не нулевой escrow
            'escrow_end_date' => [
              'code'    => 2,
              'verdict' => false,
              'desc'    =>  'Если наш бот примет ваше торговое предложение, оно будет "заморожено" в escrow, что неприемлимо. '.
                            'Начиная с 09.12.15 все обмены в системе Steam необходимо подтверждать с помощью мобильного аутентификатора Steam, который привязан к аккаунту не менее 7 дней.'.
                            'В противном случае обмены «замораживаются» на 72 часа для безопасности пользователей. Наш бот принимает только те обмены, которые подтверждены с помощью мобильного аутентификатора Steam. Подробнее вы можете почитать на https://csgo.com/news/568-srochnoe-ob-yavlenie-dlya-vseh-pol-zovateley-marketa/'
            ],

            // 3] Вещи не опознаны
            'unknown_items' => [
              'code'    => 3,
              'verdict' => false,
              'desc'    =>  'Наш бот не смог опознать некоторые вещи из вашего торгового предложения. А именно: '
            ],

            // 4] Оффер от анонима
            'unknown_user' => [
              'code'    => 4,
              'verdict' => false,
              'desc'    =>  'Торговое предложение прислал не аутентифицированный пользователь'
            ],

            // 5] Недостаточно предметов для ставки
            'min_items_per_bet' => [
              'code'    => 5,
              'verdict' => false,
              'desc'    => 'Недостаточно предметов для ставки в выбранной комнате. Минимум предметов: '
            ],

            // 6] Слишком много предметов для ставки
            'max_items_per_bet' => [
              'code'    => 6,
              'verdict' => false,
              'desc'    => 'Вы пытаетесь поставить слишком много предметов в одной ставке в выбранной комнате. Максимум предметов: '
            ],

            // 7] Сумма ставки слишком мала
            'min_bet' => [
              'code'    => 7,
              'verdict' => false,
              'desc'    => 'Сумма ставки слишком мала. Минимум: '
            ],

            // 8] Сумма ставки слишком велика
            'max_bet' => [
              'code'    => 8,
              'verdict' => false,
              'desc'    => 'Сумма ставки слишком велика. Максимум: '
            ],

          ];

          // 2.2. Вычислить кол-во предметов в ставке, и её сумму в центах
          $count_and_sum = call_user_func(function() USE ($offer) {

            // 1] Сумма в центах
            $sum = call_user_func(function() USE ($offer) {
              $result = 0;
              foreach($offer['m8_items'] as $item) {
                $result = +$result + +$item['pivot']['item_price_at_bet_time'];
              }
              return $result;
            });

            // 2] Количество вещей
            $count = call_user_func(function() USE ($offer) {
              $result = 0;
              foreach($offer['m8_items'] as $item) {
                $result = +$result + 1;
              }
              return $result;
            });

            // 3] Вернуть результаты
            return [
              'sum'   => $sum,
              'count' => $count
            ];

          });

          // 2.3. Получить все необходимые лимиты комнаты, связанной с $offer
          $room_limits = call_user_func(function() USE ($offer) {

            // 1] Получить связанную с $offer комнату
            $room = $offer['rooms']['0'];

            // 2] Вернуть результаты
            return [
              "min_items_per_bet" => $room['min_items_per_bet'],  // MIN кол-во предметов в ставке
              "max_items_per_bet" => $room['max_items_per_bet'],  // MAX кол-во предметов в ставке
              "min_bet"           => $room['min_bet'],            // MIN ставка игрока
              "max_bet"           => $room['max_bet'],            // MAX ставка игрока
            ];

          });

          // 2.4. Пройтись по $checklist и проверить каждый пункт
          call_user_func(function() USE (&$checklist, $offer, $count_and_sum, $room_limits) {

            // Проверяем №1 в $checklist: запрос вещей бота
            call_user_func(function() USE (&$checklist, $offer) {

              // 1] Если в оффере есть запрос вещей бота
              if(!empty($offer['items_to_give']))
                $checklist['items_to_give']['verdict'] = true;

            });

            // Проверяем №2 в $checklist: не нулевой escrow
            call_user_func(function() USE (&$checklist, $offer) {

              // 1] Если в оффере есть запрос вещей бота
              if(!empty($offer['escrow_end_date']))
                $checklist['escrow_end_date']['verdict'] = true;

            });

            // Проверяем №3 в $checklist: вещи не опознаны
            call_user_func(function() USE (&$checklist, $offer) {

              // 1] Извлечь информацию unknown_items в виде массива
              $unknown_items = json_decode($offer['unknown_items'], true);

              // 2] Если в оффере есть неопознанные вещи
              if(!empty($unknown_items) && is_array($unknown_items)) {

                // 1.1] Установить verdict равным true
                $checklist['unknown_items']['verdict'] = true;

                // 1.2] Добавить список этих вещей в описание
                $checklist['unknown_items']['desc'] = $checklist['unknown_items']['desc'] .
                    implode(', ', $unknown_items);

              }

            });

            // Проверяем №4 в $checklist: оффер от анонима
            call_user_func(function() USE (&$checklist, $offer) {

              // 1] Если оффер не связан ни с одним известным системе пользователем
              if(!array_key_exists('m5_users', $offer) || !is_array($offer['m5_users']) || count($offer['m5_users']) == 0)
                $checklist['unknown_user']['verdict'] = true;

            });

            // Проверяем №5 в $checklist: недостаточно предметов для ставки
            call_user_func(function() USE (&$checklist, $offer, $count_and_sum, $room_limits) {

              // 1] Если недостаточно предметов для ставки
              if($count_and_sum['count'] < $room_limits['min_items_per_bet']) {

                // 1.1] Установить verdict равным true
                $checklist['min_items_per_bet']['verdict'] = true;

                // 1.2] Добавить лимит в описание
                $checklist['min_items_per_bet']['desc'] = $checklist['min_items_per_bet']['desc'] . $room_limits['min_items_per_bet'];

              }

            });

            // Проверяем №6 в $checklist: слишком много предметов для ставки
            call_user_func(function() USE (&$checklist, $offer, $count_and_sum, $room_limits) {

              // 1] Если слишком много предметов для ставки
              if($count_and_sum['count'] > $room_limits['max_items_per_bet']) {

                // 1.1] Установить verdict равным true
                $checklist['max_items_per_bet']['verdict'] = true;

                // 1.2] Добавить лимит в описание
                $checklist['max_items_per_bet']['desc'] = $checklist['max_items_per_bet']['desc'] . $room_limits['max_items_per_bet'];

              }

            });

            // Проверяем №7 в $checklist: сумма ставки слишком мала
            call_user_func(function() USE (&$checklist, $offer, $count_and_sum, $room_limits) {

              // 1] Если сумма ставки слишком мала
              if($count_and_sum['sum'] < $room_limits['min_bet']) {

                // 1.1] Установить verdict равным true
                $checklist['min_bet']['verdict'] = true;

                // 1.2] Добавить лимит в описание
                $checklist['min_bet']['desc'] = $checklist['min_bet']['desc'] . $room_limits['min_bet'] . ' центов.';

              }

            });

            // Проверяем №8 в $checklist: сумма ставки слишком велика
            call_user_func(function() USE (&$checklist, $offer, $count_and_sum, $room_limits) {

              // 1] Если сумма ставки слишком велика
              if($count_and_sum['sum'] > $room_limits['max_bet']) {

                // 1.1] Установить verdict равным true
                $checklist['max_bet']['verdict'] = true;

                // 1.2] Добавить лимит в описание
                $checklist['max_bet']['desc'] = $checklist['max_bet']['desc'] . $room_limits['max_bet'] . ' центов.';

              }

            });

          });

          // 2.5. На основании $checklist составить итоговый вердикт об $offer
          // - Во-первых, принимать ли этот оффер, или нет.
          // - Во-вторых, если нет, то каковы коды и описания ошибок.
          $offer_final_verdict = call_user_func(function() USE ($checklist) {

            // 1] Принимать ли этот оффер, или нет
            $accept = call_user_func(function() USE ($checklist) {

              $result = true;
              foreach($checklist as $point) {
                if($point['verdict'] == true) {
                  $result = false;
                  break;
                }
              }
              return $result;

            });

            // 2] Коды ошибок и их описания
            $codes_and_errors = call_user_func(function() USE ($checklist) {
              $result = [];
              foreach($checklist as $key => $point) {
                if($point['verdict'] == true) {
                  array_push($result, [
                    'code' => $point['code'],
                    'desc' => $point['desc']
                  ]);
                }
              }
              return $result;
            });

            // n] Вернуть результаты
            return [
              'verdict'           => $accept,
              'codes_and_errors'  => $codes_and_errors
            ];

          });

          // 2.6. Если вердикт положительный, принять оффер
          if($offer_final_verdict['verdict'] == true) {

            $result = runcommand('\M9\Commands\C44_accept_offer_type2', [
              "betid"             => $offer['id'],
              "tradeofferid"      => $offer['tradeofferid'],
              "id_user"           => $offer['m5_users'][0]['id'],
              "id_room"           => $offer['rooms'][0]['id'],
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

          // 2.7. Если вердикт отрицательный, отклонить оффер
          if($offer_final_verdict['verdict'] == false) {

            $result = runcommand('\M9\Commands\C43_decline_offer_type2', [
              "betid"             => $offer['id'],
              "botid"             => $offer['m8_bots'][0]['id'],
              "tradeofferid"      => $offer['tradeofferid'],
              "id_user"           => $offer['m5_users'][0]['id'],
              "id_room"           => $offer['rooms'][0]['id'],
              "codes_and_errors"  => json_encode($offer_final_verdict['codes_and_errors'], JSON_UNESCAPED_UNICODE)
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

        }
      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C42_process_active_offers_type2 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C42_process_active_offers_type2']);
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

