<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Cancel the active trade offer that was sent by the bot in steam, and also in DB
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        betid,
 *        tradeofferid
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
class C12_cancel_the_active_bet extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Отменить торговое предложение с tradeofferid
     *  3. Если успешно удалось отменить ТП в Steam
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------------------------//
    // Отменить торговое предложение, которое было отправлено ботом игроку, в Steam, а также в базе данных //
    //-----------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [

        "betid"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "tradeofferid"  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_bot"        => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_user"       => ["required", "regex:/^[1-9]+[0-9]*$/ui"]

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Проверить текущий статус торгового предложения с tradeofferid
      $current_status = runcommand('\M8\Commands\C22_get_tradeoffer_via_api', [
        "id_bot"        => $this->data['id_bot'],
        "id_tradeoffer" => $this->data['tradeofferid']
      ]);
      if($current_status['status'] != 0)
        throw new \Exception($current_status['data']['errormsg']);

      // 3. Отменить торговое предложение с tradeofferid
      // - Если такое торговое предложение вообще существует.
      if(count($current_status['data']['tradeoffer']['response']) != 0) {
        $result = runcommand('\M8\Commands\C27_cancel_trade_offer', [
          "id_bot"        => $this->data['id_bot'],
          "id_tradeoffer" => $this->data['tradeofferid']
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);
      }

      // 4. Если успешно удалось отменить ТП в Steam
      // - Или если такого ТП уже не существует в Steam.
      if((!empty($result) && $result['status'] == 0) || count($current_status['data']['tradeoffer']['response']) == 0) {

        // 1] Получить ставку с betid и tradeofferid
        $bet = \M9\Models\MD3_bets::with(['bets_statuses'])
            ->where('id', $this->data['betid'])
            ->where('tradeofferid', $this->data['tradeofferid'])
            ->first();
        if(empty($bet))
          throw new \Exception('Не удалось найти ставку в m9.md3_bets по id = '.$this->data['betid']);

        // 2] Получить статусы Active и Canceled
        $status_active = \M9\Models\MD8_bets_statuses::where('status', 'Active')->first();
        $status_canceled = \M9\Models\MD8_bets_statuses::where('status', 'Canceled')->first();
        if(empty($status_active) || empty($status_canceled))
          throw new \Exception('Не удалось найти статусы Active или Canceled в m9.md8_bets_statuses');

        // 3] Отвязать ставку от статуса Active
        $bet->bets_statuses()->detach($status_active->id);

        // 4] Привязать ставку к статусу Canceled
        $bet->bets_statuses()->attach($status_canceled->id);

        // 5] Обновить кэш

          // 5.1] processing:bets:active
          $result = runcommand('\M9\Commands\C13_update_cache', [
            "cache2update" => ["processing:bets:active"]
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 5.2] processing:rooms
          $result = runcommand('\M9\Commands\C13_update_cache', [
            "cache2update" => ["processing:bets:accepted"]
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

        // 6] Сообщить игроку $this->data['id_user'], что его ставка истекла
        // - Через websocket, по частном каналу
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:private:'.$this->data['id_user']],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'tradeoffer_cancel'
          ]
        ]));

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C12_cancel_the_active_bet from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C12_cancel_the_active_bet']);
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

