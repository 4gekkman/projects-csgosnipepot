<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Wins expiration tracking
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
class C28_wins_expiration_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить все not_paid_expired выигрыши из кэша,
     *  3. Получить все истёкшие выигрыши из $wins_not_paid_expired
     *  4. Получить текущий статус, и статус Expired
     *  5. Получить активные выигрыши из кэша
     *  6. Для $expired_wins: отменить активные офферы, сменить статус на Expired
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------//
    // Отслеживание сроков годности самих выигрышей //
    //----------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить текущее серверное время в формате Carbon
      $servertime = \Carbon\Carbon::now();

      // 2. Получить все not_paid_expired выигрыши из кэша,
      $wins_not_paid_expired = json_decode(Cache::get('processing:wins:not_paid_expired'), true);

      // 3. Получить все истёкшие выигрыши из $wins_not_paid_expired
      $expired_wins = call_user_func(function() USE ($servertime, $wins_not_paid_expired) {

        $results = [];
        foreach($wins_not_paid_expired as $win) {

          // 1] Получить дату и время создания $win в формате Carbon
          $created_at = \Carbon\Carbon::parse($win['created_at']);

          // 2] Получить лимит времени на выплута в минутах (payout_limit_min)
          $payout_limit_min = $win['rounds'][0]['rooms']['payout_limit_min'];
          if(!$payout_limit_min) $payout_limit_min = 60;

          // 3] Вычислить, когда должен истечь выигрыш $win
          $expired_at = $created_at->addMinutes($payout_limit_min);

          // 4] Если $servertime >= $expired_at, добавить $win в $results
          if($servertime->gte($expired_at))
            array_push($results, $win);

        }
        return $results;

      });

      // 4. Получить статус Expired
      $status_expired = \M9\Models\MD9_wins_statuses::where('status', 'Expired')->first();
      if(empty($status_expired))
        throw new \Exception('Не удалось найти статус Expired в m9.md9_wins_statuses');

      // 5. Получить активные выигрыши из кэша
      $wins_active = json_decode(Cache::get('processing:wins:active'), true);

      // 6. Для $expired_wins: отменить активные офферы, сменить статус на Expired
      foreach($expired_wins as $expired_win) {

        // 6.1. Отменить все активные офферы для $expired_win
        call_user_func(function() USE ($expired_win, $expired_wins, $wins_active) {

          // 1] Отменить офферы истёкших выигрышей, перевести соотв.выигрыши в состояние Expired
          foreach($wins_active as $win) {

            // 1.1] Получить всех ботов, связанных с $win
            $bots = $win['m8_bots'];

            // 1.2] Проверить истёкшие офферы у каждого из ботов
            foreach($bots as $bot) {

              // 1.2.1] Получить значения is_free и tradeofferid для $bot
              $is_free      = $bot['pivot']['is_free'];
              $tradeofferid = $bot['pivot']['tradeofferid'];

              // 1.2.2] Если у $bot нет активного оффера, перейти к след.итерации
              if($is_free == 1 || !$tradeofferid) continue;

              // 1.2.3] Отменить $tradeofferid
              $result = runcommand('\M9\Commands\C31_cancel_the_active_win_offer', [
                "winid"        => $win['id'],
                "tradeofferid" => $tradeofferid,
                "id_bot"       => $bot['id'],
                "id_user"      => $win['m5_users'][0]['id'],
                "id_room"      => $expired_win['rounds'][0]['rooms']['id'],
                "is_expired"   => 1
              ]);
              if($result['status'] != 0)
                throw new \Exception($result['data']['errormsg']);

            }

          }

        });

        // 6.2. Сменить статус $expired_win на Expired
        //call_user_func(function() USE ($expired_win, $status_expired) {
        //
        //  // 1] Получить модель выигрыша $expired_win['id']
        //  $win = \M9\Models\MD4_wins::find($expired_win['id']);
        //  if(empty($win))
        //    throw new \Exception('Не удалось найти в БД истёкший выигрыш №'.$expired_win['id']);
        //
        //  // 2] Отвязать $win от всех статусов
        //  $win->wins_statuses()->detach();
        //
        //  // 3] Привязать $win к статусу $status_expired
        //  if(!$win->wins_statuses->contains($status_expired->id)) $win->wins_statuses()->attach($status_expired->id);
        //
        //  // 4] Добавить started_at в pivot-таблицу
        //  $win->wins_statuses()->updateExistingPivot($status_expired->id, ["started_at" => \Carbon\Carbon::now()->toDateTimeString()]);
        //
        //});

        // 6.3. Сделать commit
        DB::commit();

        // 6.4. Обновить весь кэш
        // - Но только, если он не был обновлён в C18.
        // - А там он обновляется только лишь при изменении статуса
        //   любого из раундов, любой из комнат.
        $result = runcommand('\M9\Commands\C25_update_wins_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 6.5. Сообщить игроку $this->data['id_user'] свежие данные по выигрышам
        // - Через websocket, по частном каналу.
        // - Сообщить те же данные, что при истечения выигрыша, с тем же task.
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$expired_win['m5_users'][0]['id']],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'tradeoffer_wins_cancel',
            'data' => [
              'id_room'     =>   $expired_win['rounds'][0]['rooms']['id'],
              'wins'        => [
                "active"            => json_decode(Cache::tags(['processing:wins:active:personal:safe'])->get('processing:wins:active:safe:'.$expired_win['m5_users'][0]['id']), true) ?: "",
                "not_paid_expired"  => json_decode(Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->get('processing:wins:not_paid_expired:safe:'.$expired_win['m5_users'][0]['id']), true) ?: [],
                //"paid"              => json_decode(Cache::tags(['processing:wins:paid:personal:safe'])->get('processing:wins:paid:safe:'.$expired_win['m5_users'][0]['id']), true) ?: [],
                //"expired"           => json_decode(Cache::tags(['processing:wins:expired:personal:safe'])->get('processing:wins:expired:safe:'.$expired_win['m5_users'][0]['id']), true) ?: []
              ]
            ]
          ]
        ]));

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C28_wins_expiration_tracking from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C28_wins_expiration_tracking']);
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

