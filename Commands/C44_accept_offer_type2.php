<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Accept offer type 2
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
class C44_accept_offer_type2 extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Принять оффер tradeofferid
     *  3. В случае успешного принятия, внести изменения в БД
     *  4. В случае неудачного принятия, принять меры
     *    4.1. Подготовить массив $bots_ids
     *    4.2. Получить список не активных incoming offers через HTTP для бота botid
     *    4.3. Проверить, есть ли tradeofferid среди $bots_not_active_offers_by_id
     *    4.4. Если оффер не активен, и принят, применяем C16
     *    4.5. Если оффер не активен, и статус отличается от 3, применяем C15
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------//
    // Accept offer type 2 //
    //---------------------//
    $res = call_user_func(function() { try {

      // 1. Получить и проверить входящие данные
      $validator = r4_validate($this->data, [

        "betid"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "botid"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "partnerid"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "tradeofferid"      => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_user"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_room"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Принять оффер tradeofferid
      $accept_result = runcommand('\M8\Commands\C28_accept_trade_offer', [
        "id_bot"        => $this->data['botid'],
        "id_tradeoffer" => $this->data['tradeofferid'],
        "id_partner"    => $this->data['partnerid'],
      ]);

      // 3. В случае успешного принятия, внести изменения в БД
      // - Успешное, это когда: нет ошибки и вернулся не пустой $result['data']['tradeofferid'].
      // - Команда также обновит весь кэш, и пошлёт по частному каналу уведомление игроку.
      if (
        $accept_result['status'] == 0 &&
        !empty($accept_result) &&
        array_key_exists('data', $accept_result) &&
        array_key_exists('tradeid', $accept_result['data']) &&
        !empty($accept_result['data']['tradeid'])
      ) {

        // Внести изменения
        $result = runcommand('\M9\Commands\C16_active_to_accepted', [
          "betid"             => $this->data['betid'],
          "tradeofferid"      => $this->data['tradeofferid'],
          "id_user"           => $this->data['id_user'],
          "id_room"           => $this->data['id_room'],
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

      }

      // 4. В случае неудачного принятия, принять меры
      // - Проверить статус оффера через HTTP.
      // - Если он не активен, внести соотв.изменения в БД
      else {

        // 4.1. Подготовить массив $bots_ids
        $bots_ids = [$this->data['botid']];

        // 4.2. Получить список не активных incoming offers через HTTP для бота botid

          // 4.2.1. Получить
          $bots_not_active_offers_by_id = call_user_func(function() USE ($bots_ids) {

            // 1] Подготовить массив для результата
            $result = [];

            // 2] Наполнить $result
            for($i=0; $i<count($bots_ids); $i++) {

              // 2.1] Получить ID i-го бота
              $id_bot = $bots_ids[$i];

              // 2.2] Попробовать получить НЕ активные входящие офферы $id_bot через HTTP
              // - Что означают коды:
              //
              //    -3    // Не удалось получить ответ от Steam
              //    -2    // Информация об отправленных офферах отсутствует в ответе в принципе
              //    0     // Успех, найденный оффер доступен по ключу offer
              //
              $offers_http = call_user_func(function() USE ($id_bot) {

                // 2.2.1] Получить все активные офферы бота $id_bot через HTTP
                $offers = runcommand('\M8\Commands\C24_get_trade_offers_via_html', ["id_bot"=>$id_bot,"mode"=>1]);

                // 2.2.2] Если tradeoffers отсутствуют в ответе
                if(!array_key_exists('tradeoffers', $offers['data']))
                  return [
                    "code"   => -2,
                    "offers"  => ""
                  ];

                // 2.2.3] Отфильтровать из $offers офферы со статусом 2 (Active)
                $offers['data']['tradeoffers']['trade_offers_received'] = array_values(array_filter($offers['data']['tradeoffers']['trade_offers_received'], function($item){
                  if($item['trade_offer_state'] != 2) return true;
                  else return false;
                }));

                // 2.2.4] Если получить ответ от Steam не удалось
                if($offers['status'] != 0)
                  return [
                    "code"   => -3,
                    "offers"  => ""
                  ];

                // 2.2.5] Если trade_offers_received отсутствуют в ответе
                if(!array_key_exists('trade_offers_received', $offers['data']['tradeoffers']))
                  return [
                    "code"   => -2,
                    "offers"  => ""
                  ];

                // 2.2.6] Вернуть offers
                return [
                  "code"    => 0,
                  "offers"  => $offers
                ];

              });

              // 2.3] Если $offers_http['code'] == 0
              // - Записать данные в $result и перейти к следующей итерации
              if($offers_http['code'] == 0) {
                $result[$id_bot] = $offers_http['offers'];
                continue;
              }

            }

            // 3] Вернуть результат
            return $result;

          });

          // 4.2.2. Добавить $bots_not_active_offers_by_id в кэш, если trade_offers_received не пуст
          // - С ключем: m9:bots_not_active_offers:<bot_id>
          if(!empty($bots_not_active_offers_by_id[$this->data['botid']]['data']['tradeoffers']['trade_offers_received']))
            Cache::put('m9:bots_not_active_offers:'.$this->data['botid'], json_encode($bots_not_active_offers_by_id, JSON_UNESCAPED_UNICODE), 30);

          // 4.2.3. А если trade_offers_received пуст, попробовать достать $bots_not_active_offers_by_id из кэша
          else {

            // 1] Получить кэш
            $cache = json_decode(Cache::get('m9:bots_not_active_offers:'.$this->data['botid']), true);

            // 2] Если trade_offers_received не пуст, записать содержимое кэша в $bots_not_active_offers_by_id
            if(!empty($cache[$this->data['botid']]['data']['tradeoffers']['trade_offers_received']))
              $bots_not_active_offers_by_id = $cache;

          }

          // 4.2.4. Получить из кэша информацию о состоянии активных офферов из m9.C45
          $c45_executing = json_decode(Cache::get('m9:processing:bets_ex_active_type2'), true);
          if(empty($c45_executing)) $c45_executing = [];

          // 4.2.5. Получить все доступные активные ставки типа 2
          $bets_active = json_decode(Cache::get('processing:bets_type2:active'), true);

          // 4.2.6. Пополнить $bots_not_active_offers_by_id информацией из $c45_executing
          // - Добавлять только информацию об офферах со статусом НЕ Active (не 2).
          // - Добавлять только в том случае, если $bots_not_active_offers_by_id ещё нет информации по этому офферу.
          // - Добавлять только в том случае, если оффер есть в $bets_active.
          foreach($c45_executing as $to) {

            // 1] Если $to нет в $bets_active, перейти к следующей итерации
            $is_in_bets_active = false;
            foreach($bets_active as $bet) {
              if($to['tradeofferid'] == $bet['tradeofferid'])
                $is_in_bets_active = true;
            }
            if($is_in_bets_active == false) continue;

            // 2] Если информация о $to уже есть в $bots_not_active_offers_by_id, перейти к следующей итерации
            $is_already_in_bets_ex_acitive = false;
            foreach($bots_not_active_offers_by_id[$this->data['botid']]['data']['tradeoffers']['trade_offers_received'] as $bet) {
              if($to['tradeofferid'] == $bet['tradeofferid'])
                $is_already_in_bets_ex_acitive = true;
            }
            if($is_already_in_bets_ex_acitive == true) continue;

            // 3] Если статус $to "Active", перейти к следующей итерации
            if($to['id_status_new'] == 2) continue;

            // 4] Добавить $to в $bots_not_active_offers_by_id
            array_push($bots_not_active_offers_by_id[$this->data['botid']]['data']['tradeoffers']['trade_offers_received'], $to['tradeoffer_steam_format']);

          }

        // 4.3. Проверить, есть ли tradeofferid среди $bots_not_active_offers_by_id
        // - И какой у него статус.
        $is_offer_not_active = call_user_func(function() USE ($bots_not_active_offers_by_id) {

          // 1] Получить все недавно ставшие не активными входящие офферы
          $trade_offers_received = array_key_exists($this->data['botid'], $bots_not_active_offers_by_id) ?
              $bots_not_active_offers_by_id[$this->data['botid']]['data']['tradeoffers']['trade_offers_received'] : "";

          // 2] Получить массив tradeofferid недавно ставших неактивными офферов
          if(!empty($trade_offers_received) && count($trade_offers_received) > 0)
            $tradeofferids = collect($bots_not_active_offers_by_id[$this->data['botid']]['data']['tradeoffers']['trade_offers_received'])
                ->pluck('tradeofferid')->toArray();
          else
            $tradeofferids = [];

          // 3] Попробовать найти tradeofferid в $tradeofferids

            // 3.1] Вычислить позицию
            $pos = array_search($this->data['tradeofferid'], $tradeofferids);

            // 3.2] Если $pos не false, получить оффер
            if($pos !== false)
              $offer = $trade_offers_received[$pos];
            else
              $offer = '';

          // n] Вернуть результаты
          if(!empty($offer))
            return [
              'verdict' => true,
              'trade_offer_state' => $offer['trade_offer_state']
            ];
          else
            return [
              'verdict' => false,
              'trade_offer_state' => ""
            ];

        });

        // 4.4. Если оффер не активен, и принят, применяем C16
        if($is_offer_not_active['verdict'] == true && $is_offer_not_active['trade_offer_state'] == 3) {

          // Внести изменения
          $result = runcommand('\M9\Commands\C16_active_to_accepted', [
            "betid"             => $this->data['betid'],
            "tradeofferid"      => $this->data['tradeofferid'],
            "id_user"           => $this->data['id_user'],
            "id_room"           => $this->data['id_room'],
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

        }

        // 4.5. Если оффер не активен, и статус отличается от 3, применяем C15
        if($is_offer_not_active['verdict'] == true && $is_offer_not_active['trade_offer_state'] != 3) {

          $result = runcommand('\M9\Commands\C15_cancel_the_active_bet_dbpart', [
            "betid"             => $this->data['betid'],
            "tradeofferid"      => $this->data['tradeofferid'],
            "another_status_id" => 7,
            "id_user"           => $this->data['id_user'],
            "id_room"           => $this->data['id_room'],
            "codes_and_errors"  => "",
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

        }

      }


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C44_accept_offer_type2 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C44_accept_offer_type2']);
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

