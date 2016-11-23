<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Tracking of all active offers within processing process
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
class C14_active_offers_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить активные ставки из кэша
     *  2. Получить из $bets_active неповторяющийся список всех ботов, имеющих активные офферы
     *  3. Для каждого из $bots_ids получить список активных офферов
     *  4. Попробовать найти бывшие активные ($bets_active) офферы
     *  5. Сделать commit
     *  6. Пробежаться по каждому офферу в $bets_ex_active
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------//
    // Отслеживание изменения статусов всех активных офферов в процессе процессинга игры //
    //-----------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить активные ставки из кэша
      $bets_active = json_decode(Cache::get('processing:bets:active'), true);

      // 2. Получить из $bets_active неповторяющийся список ID всех ботов, имеющих активные офферы
      $bots_ids = call_user_func(function() USE ($bets_active) {

        $bots_ids = [];
        for($i=0; $i<count($bets_active); $i++) {
          $id = $bets_active[$i]['m8_bots'][0]['id'];
          if(!in_array($id, $bots_ids))
            array_push($bots_ids, $id);
        }
        return $bots_ids;

      });

      // 3. Для каждого из $bots_ids получить список НЕ активных send offers через HTTP
      $bots_not_active_offers_by_id = call_user_func(function() USE ($bots_ids) {

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Наполнить $result
        for($i=0; $i<count($bots_ids); $i++) {

          // 2.1] Получить ID i-го бота
          $id_bot = $bots_ids[$i];

          // 2.2] Попробовать получить активные исходящие офферы $id_bot через API
          // - Что означают коды:
          //
          //    -3    // Не удалось получить ответ от Steam
          //    -2    // Информация об отправленных офферах отсутствует в ответе в принципе
          //    0     // Успех, найденный оффер доступен по ключу offer
          //
          //$offers_api = call_user_func(function() USE ($id_bot) {
          //
          //  // 2.2.1] Получить все исходящие (в т.ч. не активные) офферы бота $id_bot через API
          //  $offers = runcommand('\M8\Commands\C19_get_tradeoffers_via_api', ["id_bot"=>$id_bot,"activeonly"=>1]);
          //
          //  // 2.2.2] Если получить ответ от Steam не удалось
          //  if($offers['status'] != 0)
          //    return [
          //      "code"   => -3,
          //      "offers"  => ""
          //    ];
          //
          //  // 2.2.3] Если trade_offers_sent отсутствуют в ответе
          //  if(!array_key_exists('trade_offers_sent', $offers['data']['tradeoffers']))
          //    return [
          //      "code"   => -2,
          //      "offers"  => ""
          //    ];
          //
          //  // 2.2.4] Вернуть offers
          //  return [
          //    "code"    => 0,
          //    "offers"  => $offers
          //  ];
          //
          //});

          // 2.3] Если $offers_api['code'] == 0
          // - Записать данные в $result и перейти к следующей итерации
          //if($offers_api['code'] == 0) {
          //  $result[$id_bot] = $offers_api['offers'];
          //  continue;
          //}

          // 2.4] Попробовать получить НЕ активные исходящие офферы $id_bot через HTTP
          // - Что означают коды:
          //
          //    -3    // Не удалось получить ответ от Steam
          //    -2    // Информация об отправленных офферах отсутствует в ответе в принципе
          //    0     // Успех, найденный оффер доступен по ключу offer
          //
          $offers_http = call_user_func(function() USE ($id_bot) {

            // 2.4.1] Получить все активные офферы бота $id_bot через HTTP
            $offers = runcommand('\M8\Commands\C24_get_trade_offers_via_html', ["id_bot"=>$id_bot,"mode"=>4]);

            // 2.4.2] Если получить ответ от Steam не удалось
            if($offers['status'] != 0)
              return [
                "code"   => -3,
                "offers"  => ""
              ];

            // 2.4.3] Если trade_offers_sent отсутствуют в ответе
            if(!array_key_exists('trade_offers_sent', $offers['data']['tradeoffers']))
              return [
                "code"   => -2,
                "offers"  => ""
              ];

            // 2.2.4] Вернуть offers
            return [
              "code"    => 0,
              "offers"  => $offers
            ];

          });

          // 2.5] Если $offers_http['code'] == 0
          // - Записать данные в $result и перейти к следующей итерации
          if($offers_http['code'] == 0) {
            $result[$id_bot] = $offers_http['offers'];
            continue;
          }

        }

        // 3] Вернуть результат
        return $result;

      });

      // 4. Попробовать найти бывшие активные ($bets_active) офферы
      // - Среди ныне не активных ($bots_not_active_offers_by_id) офферов.
      $bets_ex_active = call_user_func(function() USE ($bets_active, $bots_not_active_offers_by_id) {

        // 1] Подготовить массив для результатов
        $result = [];

        // 2] Наполнить $result
        for($i=0; $i<count($bets_active); $i++) {

          // 2.1] Получить ID $i-го оффера
          $id_offer_i = $bets_active[$i]['tradeofferid'];

          // 2.2] Получить ID бота, которому принадлежит $i-ая ставка
          $id_bot = $bets_active[$i]['m8_bots'][0]['id'];

          // 2.3] Получить все НЕ активные офферы, принадлежащие $id_bot
          $id_bot_offers = array_key_exists($id_bot, $bots_not_active_offers_by_id) ? $bots_not_active_offers_by_id[$id_bot] : [];

          // 2.4] Попробовать найти оффер с $id_offer_i в $id_bot_offers
          $id_bot_offer = call_user_func(function() USE ($id_bot_offers, $id_offer_i) {

            // 2.4.1] Если $id_bot_offers пуст, вернуть пустую строку
            if(empty($id_bot_offers) || count($id_bot_offers) == 0) return '';

            // 2.4.2] Иначе, искать
            for($j=0; $j<count($id_bot_offers['data']['tradeoffers']['trade_offers_sent']); $j++) {

              if($id_bot_offers['data']['tradeoffers']['trade_offers_sent'][$j]['tradeofferid'] == $id_offer_i)
                return $id_bot_offers['data']['tradeoffers']['trade_offers_sent'][$j];

            }

            // 2.4.3] Вернуть пустую строку
            return '';

          });

          // 2.5] Получить статусы этих 2-х офферов
          $id_offer_i_status = $bets_active[$i]['bets_statuses'][0]['pivot']['id_status'];
          $id_bot_offer_status = !empty($id_bot_offer) ? $id_bot_offer['trade_offer_state'] : '';

          // 2.6] Добавить новую запись в $results, если $id_bot_offer найден
          if(!empty($id_bot_offer))
            array_push($result, [
              "tradeoffer"    => $bets_active[$i],
              "tradeofferid"  => $id_offer_i,
              "id_status_old" => $id_offer_i_status,
              "id_status_new" => $id_bot_offer_status
            ]);

        }

        // 3] Вернуть $result
        return $result;

      });

      // 5. Сделать commit
      DB::commit();

      // 6. Пробежаться по каждому офферу в $bets_ex_active
      // - И в зависимости от того "Accepted" он, или отличается, предпринять ряд действий.
      call_user_func(function() USE ($bets_ex_active) {
        for($i=0; $i<count($bets_ex_active); $i++) {

          // 1] Получить нужные данные в короткие переменные
          $tradeoffer     = $bets_ex_active[$i]['tradeoffer'];
            $betid          = $tradeoffer['id'];
            $tradeofferid   = $bets_ex_active[$i]['tradeofferid'];
            $id_status_new  = $bets_ex_active[$i]['id_status_new'];
            $id_user        = $tradeoffer['m5_users'][0]['id'];
            $id_room        = $tradeoffer['rooms'][0]['id'];

          // 2] Если статус оффера изменился на Accepted
          if($id_status_new == 3) {

            $result = runcommand('\M9\Commands\C16_active_to_accepted', [
              "betid"             => $betid,
              "tradeofferid"      => $tradeofferid,
              "id_user"           => $id_user,
              "id_room"           => $id_room,
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

          // 3] Если статус оффера изменился НЕ на Accepted
          else {

            $result = runcommand('\M9\Commands\C15_cancel_the_active_bet_dbpart', [
              "betid"             => $betid,
              "tradeofferid"      => $tradeofferid,
              "another_status_id" => $id_status_new,
              "id_user"           => $id_user,
              "id_room"           => $id_room,
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

        }
      });


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C14_active_offers_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C14_active_offers_tracking']);
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

