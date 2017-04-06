<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Tracking of all active offers within processing of wins
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
class C26_active_offers_wins_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить активные выигрыши из кэша
     *  2. Получить из $wins_active список всех активных офферов, и ID отправивших их ботов
     *  3. Получить из $offers_ids неповторяющийся список ID и номеров офферов всех ботов, имеющих активные офферы
     *  4. Для каждого из $bots_ids получить список НЕ активных send offers через HTTP
     *  5. Попробовать найти бывшие активные ($offers_ids) офферы
     *  6. Сделать commit
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------------------------------//
    // Отслеживание изменения статусов всех активных офферов в процессе процессинга выигрышей //
    //----------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить активные выигрыши из кэша
      $wins_active = json_decode(Cache::get('processing:wins:active'), true);

      // 2. Получить из $wins_active список всех активных офферов, и ID отправивших их ботов
      // - В формате:
      //
      //    [
      //      <номер оффера> => [
      //        id_bot      => <id бота>,
      //        win         => <ссылка на выигрыш из $wins_active>,
      //      ],
      //      ...
      //    ]
      //
      $offers_ids = call_user_func(function() USE ($wins_active) {

        $offers_ids = [];
        for($i=0; $i<count($wins_active); $i++) {
          foreach($wins_active[$i]['m8_bots'] as $bot) {

            // 1] Получить pivot-таблицу
            $pivot = $bot['pivot'];

            // 2] Если is_free == 1, или отсутствует tradeofferid
            // - Перейти к следующей итерации.
            if($pivot['is_free'] == 1 || empty($pivot['tradeofferid']))
              continue;

            // 3] Получить ID и tradeofferid бота
            $id           = $bot['id'];
            $tradeofferid = $pivot['tradeofferid'];

            // 4] Если $id нет в $offers_ids, добавить
            if(!in_array($id, $offers_ids))
              $offers_ids[$tradeofferid] = [
                "id_bot"        => $id,
                "win"           => $wins_active[$i],
                "tradeofferid"  => $tradeofferid
              ];

          }

        }
        return $offers_ids;

      });

      // 3. Получить из $offers_ids неповторяющийся список ID и номеров офферов всех ботов, имеющих активные офферы
      $bots_ids = call_user_func(function() USE ($offers_ids) {

        $bots_ids = [];
        foreach($offers_ids as $tradeofferid => $id_bot_win) {
          if(!in_array($id_bot_win['id_bot'], $bots_ids))
            array_push($bots_ids, $id_bot_win['id_bot']);
        }
        return $bots_ids;

      });

      // 4. Для каждого из $bots_ids получить список НЕ активных send offers через HTTP
      // - В формате:
      //
      //    [
      //      <id бота> => [ ...не активные офферы... ]
      //    ]
      //
      $bots_not_active_offers_by_id = call_user_func(function() USE ($bots_ids) {

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Наполнить $result
        for($i=0; $i<count($bots_ids); $i++) {

          // 2.1] Получить ID i-го бота
          $id_bot = $bots_ids[$i];

          // 2.2] Попробовать получить НЕ активные исходящие офферы $id_bot через HTTP
          // - Что означают коды:
          //
          //    -3    // Не удалось получить ответ от Steam
          //    -2    // Информация об отправленных офферах отсутствует в ответе в принципе
          //    0     // Успех, найденный оффер доступен по ключу offer
          //
          $offers_http = call_user_func(function() USE ($id_bot) {

            // 2.4.1] Получить все активные офферы бота $id_bot через HTTP
            $offers = runcommand('\M8\Commands\C24_get_trade_offers_via_html', ["id_bot"=>$id_bot,"mode"=>3]);

            // 2.4.2] Отфильтровать из $offers офферы со статусом 2 (Active)
            $offers['data']['tradeoffers']['trade_offers_sent'] = array_values(array_filter($offers['data']['tradeoffers']['trade_offers_sent'], function($item){
              if($item['trade_offer_state'] != 2) return true;
              else return false;
            }));

            // 2.4.3] Если получить ответ от Steam не удалось
            if($offers['status'] != 0)
              return [
                "code"   => -3,
                "offers"  => ""
              ];

            // 2.4.4] Если trade_offers_sent отсутствуют в ответе
            if(!array_key_exists('trade_offers_sent', $offers['data']['tradeoffers']))
              return [
                "code"   => -2,
                "offers"  => ""
              ];

            // 2.2.5] Вернуть offers
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

      // 5. Попробовать найти бывшие активные ($offers_ids) офферы
      // - Среди ныне не активных ($bots_not_active_offers_by_id) офферов.
      $ex_active_offers = call_user_func(function() USE ($offers_ids, $bots_not_active_offers_by_id) {

        // 1] Подготовить массив для результатов
        $result = [];

        // 2] Наполнить $result
        foreach($offers_ids as $tradeofferid => $id_bot_win) {

          // 2.1] Получить все НЕ активные офферы, принадлежащие боту $id_bot_win['id_bot']
          $id_bot_offers = array_key_exists($id_bot_win['id_bot'], $bots_not_active_offers_by_id) ? $bots_not_active_offers_by_id[$id_bot_win['id_bot']] : [];

          // 2.2] Попробовать найти оффер с $tradeofferid в $id_bot_offers
          $id_bot_offer = call_user_func(function() USE ($id_bot_offers, $tradeofferid) {

            // 2.2.1] Если $id_bot_offers пуст, вернуть пустую строку
            if(empty($id_bot_offers) || count($id_bot_offers) == 0) return '';

            // 2.2.2] Иначе, искать
            for($j=0; $j<count($id_bot_offers['data']['tradeoffers']['trade_offers_sent']); $j++) {

              if($id_bot_offers['data']['tradeoffers']['trade_offers_sent'][$j]['tradeofferid'] == $tradeofferid)
                return $id_bot_offers['data']['tradeoffers']['trade_offers_sent'][$j];

            }

            // 2.2.3] Вернуть пустую строку
            return '';

          });

          // 2.3] Получить новый статус оффера
          $id_bot_offer_status = !empty($id_bot_offer) ? $id_bot_offer['trade_offer_state'] : '';

          // 2.4] Добавить новую запись в $results, если $id_bot_offer найден
          if(!empty($id_bot_offer))
            array_push($result, [
              "win"           => $id_bot_win['win'],
              "id_bot"        => $id_bot_win['id_bot'],
              "tradeofferid"  => $tradeofferid,
              "id_status_new" => $id_bot_offer_status,
            ]);

        }

        // 3] Вернуть $result
        return $result;

      });

      // 6. Сделать commit
      DB::commit();

      // 7. Пробежаться по каждому офферу в $ex_active_offers
      // - И в зависимости от того "Accepted" он, или отличается, предпринять ряд действий.
      call_user_func(function() USE ($ex_active_offers) {
        for($i=0; $i<count($ex_active_offers); $i++) {

          // 1] Получить нужные данные в короткие переменные
          $win            = $ex_active_offers[$i]['win'];
          $winid          = $win['id'];
          $tradeofferid   = $ex_active_offers[$i]['tradeofferid'];
          $id_status_new  = $ex_active_offers[$i]['id_status_new'];
          $id_user        = $win['m5_users'][0]['id'];
          $id_room        = $win['rounds'][0]['rooms']['id'];
          $id_bot         = $ex_active_offers[$i]['id_bot'];

          // 2] Если статус оффера изменился на Accepted
          if($id_status_new == 3) {

            $result = runcommand('\M9\Commands\C33_win_active_to_accepted', [
              "winid"             => $winid,
              "tradeofferid"      => $tradeofferid,
              "id_user"           => $id_user,
              "id_room"           => $id_room,
              "id_bot"            => $id_bot,
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

          // 3] Если статус оффера изменился НЕ на Accepted
          else {

            $result = runcommand('\M9\Commands\C32_cancel_the_active_win_offer_dbpart', [
              "winid"             => $winid,
              "tradeofferid"      => $tradeofferid,
              "id_user"           => $id_user,
              "id_room"           => $id_room,
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

            //$result = runcommand('\M9\Commands\C31_cancel_the_active_win_offer', [
            //  "winid"             => $winid,
            //  "tradeofferid"      => $tradeofferid,
            //  "id_user"           => $id_user,
            //  "id_room"           => $id_room,
            //  "id_bot"            => $id_bot,
            //]);
            //if($result['status'] != 0)
            //  throw new \Exception($result['data']['errormsg']);

          }

        }
      });


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C26_active_offers_wins_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C26_active_offers_wins_tracking']);
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

