<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make trade offer to payout the prize to the winner
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_win
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
class C30_make_payout_tradeoffer extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить steamid пользователя, который делает запрос
     *  3. Получить из кэша все not_paid_expired-выигрыши этого игрока
     *  4. Найти среди них выигрыш с id_win
     *  5. Проверить, нет ли уже у пользователя активных выигрышей
     *  6. От каждого бота отправить игроку торовое предложение
     *  7. Обновить данные о выигрыше в БД, если $tradeoffer_ids не пуст
     *  8. Сделать коммит, если $tradeoffer_ids не пуст
     *
     *  n. Обновить весь кэш, если $tradeoffer_ids не пуст
     *  m. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------//
    // Make trade offer to payout the prize to the winner //
    //----------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_win"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"]
      ]); if($validator['status'] == -1) {
        throw new \Exception("Неверные входящие данные.");
      }

      // 2. Получить steamid пользователя, который делает запрос
      $steamid_and_id = call_user_func(function(){

        // 1] Получить аутентификационный экш из сессии
        $auth = json_decode(session('auth_cache'), true);
        if(empty($auth))
          throw new \Exception('Не удалось найти аутентификационный кэш в сессии.');

        // 2] Получить информацию о пользователе
        $user = array_key_exists('user', $auth) ? $auth['user'] : "";
        if(empty($user))
          throw new \Exception('Не удалось найти аутентификационный кэш в сессии.');

        // 3] Получить steamid пользователя $user
        if(array_key_exists('ha_provider_uid', $user))
          return [
            "steamid" => $user['ha_provider_uid'],
            "id"      => $user['id'],
            "user"    => $user
          ];
        else
          return [
            "steamid" => "",
            "id"      => "",
            "user"    => ""
          ];

      });
      if(empty($steamid_and_id) || count($steamid_and_id) == 0 || empty($steamid_and_id['steamid']) || empty($steamid_and_id['id']))
        throw new \Exception('Не удалось найти steamid пользователя в аутентификационном кэше сессии.');

      // 3. Получить из кэша все not_paid_expired-выигрыши этого игрока
      $not_paid_expired = json_decode(Cache::tags(['processing:wins:not_paid_expired:personal'])->get('processing:wins:not_paid_expired:'.$steamid_and_id['id']), true);
      if(empty($not_paid_expired))
        $not_paid_expired = [];

      // 4. Найти среди них выигрыш с id_win
      $win2pay = call_user_func(function() USE ($not_paid_expired) {
        foreach($not_paid_expired as $win) {
          if($win['id'] == $this->data['id_win'])
            return $win;
        }
      });
      if(empty($win2pay))
        throw new \Exception("Не удалось обнаружить в системе тот выигрыш, который ты хочешь забрать.");

      // 5. Проверить, нет ли уже у пользователя активных выигрышей
      // - А также, совпадают ли steamid запрашивающего выигрыш, и получателя
      call_user_func(function() USE ($steamid_and_id, $win2pay) {

        // 5.1. Получить все активные выигрыши пользователя из кэша
        $wins_active = json_decode(Cache::tags(['processing:wins:active:personal'])->get('processing:wins:active:'.$steamid_and_id['id']), true);

        // 5.2. Если $wins_active не пуста
        // - Завершить с ошибкой
        if(count($wins_active) != 0)
          throw new \Exception("Нельзя одновременно запросить торговые предложения от ботов по 2-м и более выигрышам.");

        // 5.3. Если steamid запрашивающего и получателя не одинаковы, завершить
        if($win2pay['m5_users'][0]['ha_provider_uid'] != $steamid_and_id['steamid'])
          throw new \Exception("Выигрыш может получить только его владелец, бро. Только его владелец.");

      });

      // 6. От каждого бота отправить игроку торовое предложение
      // - И получить traderofferid каждого из этих предложений.
      // - В случае неудачи при отправке, процесс не прерывается.
      // - Потом для успешно расплатившихся ботов надо будет пометить
      //   в поле is_free == 1, в pivot-таблице выигрыш-бот.
      // - Результат получить в формате:
      //
      //    $tradeoffer_ids[<id бота>] = [
      //      "success"       => true/false,
      //      "tradeofferid"  => <номер оффера> или "" (в случае неудачи),
      //      "error"         => текст ошибки
      //    ]
      // $tradeoffer_ids[$bot['id']][tradeofferid]
      $tradeoffer_ids = call_user_func(function() USE ($steamid_and_id, $win2pay) {

        // 6.1. Подготовить массив для результатов
        $results = [];

        // 6.2. Наполнить $results
        foreach($win2pay['m8_bots'] as $bot) {

          // 1] Попробовать отправить оффер
          $tradeoffer_result = call_user_func(function() USE ($steamid_and_id, $win2pay, $bot){

            // 1.1] Получить steam_tradeurl пользователя $user
            $steam_tradeurl = $win2pay['m5_users'][0]['steam_tradeurl'];
            if(empty($steam_tradeurl))
              return [
                "success"       => false,
                "tradeofferid"  => "",
                "error"         => "Твой торговый URL в Steam не введён."
              ];

            // 1.2] Получить partner и token пользователя из его trade url
            $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
              "trade_url" => $steam_tradeurl
            ]);
            if($partner_and_token['status'] != 0)
              return [
                "success"       => false,
                "tradeofferid"  => "",
                "error"         => "Твой торговый URL в Steam неверен."
              ];
            $partner = $partner_and_token['data']['partner'];
            $token = $partner_and_token['data']['token'];

            // 1.3] Подготовить массив assetid вещей, которые бот должен отправить
            $assets2send = call_user_func(function() USE ($win2pay) {

              $results = [];
              for($i=0; $i<count($win2pay['m8_items']); $i++) {
                array_push($results, $win2pay['m8_items'][$i]['pivot']['assetid']);
              }
              return $results;

            });

            // 1.4] Сформировать сообщение для торгового предложения
            $tradeoffermessage = call_user_func(function() USE ($win2pay) {
              return "Safecode: ".$win2pay['safecodes'][0]['code'];
            });

            // 1.5] Отправить пользователю торговое предложение

              // 1.5.1] Отправить
              $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
                "id_bot"                => $bot['id'],
                "steamid_partner"  			=> $win2pay['m5_users'][0]['ha_provider_uid'],
                "id_partner"            => $partner,
                "token_partner"         => $token,
                "dont_trade_with_gays"  => "1",
                "assets2send"           => $assets2send,
                "assets2recieve"        => [],
                "tradeoffermessage"     => $tradeoffermessage
              ]);

              // 1.5.2] Если возникла ошибка
              if($tradeoffer['status'] != 0)
                return [
                  "success"       => false,
                  "tradeofferid"  => "",
                  "error"         => "Не удалось отправить торговое предложение. Возможно, проблемы с ботом, или Steam тормозит."
                ];

              // 1.5.3] Если с этим пользователем нельзя торговать из-за escrow
              if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
                return [
                  "success"       => false,
                  "tradeofferid"  => "",
                  "error"         => "Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней."
                ];

              // 1.5.4] Подтвердить все исходящие торговые предложения бота $bot2acceptbet
              $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
                "id_bot"                => $bot['id'],
                "need_to_ids"           => "0",
                "just_fetch_info"       => "0"
              ]);
              if($result['status'] != 0)
                return [
                  "success"       => false,
                  "tradeofferid"  => "",
                  "error"         => "Не удалось подтвердить отправленное торговое предложение."
                ];

            // 1.n] Вернуть результат
            return [
              "success"       => true,
              "tradeofferid"  => $tradeoffer['data']['tradeofferid'],
              "error"         => ""
            ];

          });

          // 2] Записать результат в $results
          // - Но только, если success == true
          if($tradeoffer_result['success'] == true)
            $results[$bot['id']] = $tradeoffer_result;

        }

        // 6.3. Вернуть $results
        return $results;

      });

      // 7. Обновить данные о выигрыше в БД, если $tradeoffer_ids не пуст
      if(count($tradeoffer_ids) > 0) {

        // 1] Получить модель $win2pay
        $win2pay_model = \M9\Models\MD4_wins::find($win2pay['id']);
        if(empty($win2pay_model))
          throw new \Exception('Не удалось найти в БД выигрыш с ID = '.$win2pay['id']);

        // 2] Получить статус выигрышей Active
        $status_active = \M9\Models\MD9_wins_statuses::where('status', 'Active')->first();
        if(empty($status_active))
          throw new \Exception('Не удалось найти статус Active в таблице md9_wins_statuses');

        // 3] Изменить статус $win2pay на Active
        // - Удалить старые связи с wins_statuses, и создать новую.
        $win2pay_model->wins_statuses()->detach();
        if(!$win2pay_model->wins_statuses->contains($status_active['id'])) $win2pay_model->wins_statuses()->attach($status_active['id'], ['started_at' => \Carbon\Carbon::now()->toDateTimeString(), 'comment' => 'Создание активного оффера (ов) для выплаты этого выигрыша победителю.']);

        // 4] Для каждого из $win2pay['m8_bots'] записать is_free, tradeofferid и offer_expired_at
        foreach($win2pay['m8_bots'] as $bot) {
          $win2pay_model->m8_bots()->updateExistingPivot($bot['id'], [
            "is_free"           => 0,
            "tradeofferid"      => $tradeoffer_ids[$bot['id']]['tradeofferid'],
            "offer_expired_at"  => \Carbon\Carbon::now()->addSeconds((int)round($win2pay_model['rounds'][0]['rooms']['offers_timeout_sec']))->toDateTimeString()
          ]);
        }

      }

      // 8. Сделать коммит, если $tradeoffer_ids не пуст
      if(count($tradeoffer_ids) > 0) {
        DB::commit();
      }

      // n. Обновить весь кэш, если $tradeoffer_ids не пуст
      if(count($tradeoffer_ids) > 0) {

        $result = runcommand('\M9\Commands\C25_update_wins_cache', [
          "all"   => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

      }

      // m. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "safecode"        => $win2pay['safecodes'][0]['code'],
          "tradeoffer_ids"  => $tradeoffer_ids,
          "expire_in_secs"  => $win2pay['rounds'][0]['rooms']['offers_timeout_sec'],
          "wins"                  => [
            "active"            => json_decode(Cache::tags(['processing:wins:active:personal'])->get('processing:wins:active:'.json_decode(session('auth_cache'), true)['user']['id']), true) ?: "",
            "not_paid_expired"  => json_decode(Cache::tags(['processing:wins:not_paid_expired:personal'])->get('processing:wins:not_paid_expired:'.json_decode(session('auth_cache'), true)['user']['id']), true) ?: [],
            "paid"              => json_decode(Cache::tags(['processing:wins:paid:personal'])->get('processing:wins:paid:'.json_decode(session('auth_cache'), true)['user']['id']), true) ?: [],
            "expired"           => json_decode(Cache::tags(['processing:wins:expired:personal'])->get('processing:wins:expired:'.json_decode(session('auth_cache'), true)['user']['id']), true) ?: []
          ],
        ]
      ];


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C30_make_payout_tradeoffer from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C30_make_payout_tradeoffer']);
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

