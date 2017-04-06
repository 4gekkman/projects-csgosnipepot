<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - It is just a datbase part of the C31_cancel_the_active_win_offer command
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        winid               | ID выигрыша в базе данных
 *        tradeofferid        | ID оффера ставки
 *        id_user             | ID пользователя, владельца ставки
 *        id_room             | ID комнаты, связанной со ставкой
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
class C32_cancel_the_active_win_offer_dbpart extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить выигрыш с winid и tradeofferid
     *  3. Получить статусы Active и статус для another_status_id
     *  4. Отвязать ставку от статуса $status_active
     *  5. Привязать ставку к другому статусу
     *  6. Обновить весь кэш
     *  7. Сделать commit
     *  8. Сообщить игроку $this->data['id_user'], что его ставка истекла
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------//
    // It is just a datbase part of the C31_cancel_the_active_win_offer command //
    //--------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить и проверить входящие данные

        // 1.1. Принять
        $validator = r4_validate($this->data, [

          "winid"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "tradeofferid"      => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "id_user"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "id_room"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "is_expired"        => ["regex:/^[01]{1}$/ui"]

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }

        // 1.2. Назначить значения по умолчанию для некоторых параметров

          // 1] is_expired
          if(!array_key_exists('is_expired', $this->data))
            $this->data['is_expired'] = 0;

      // 2. Получить выигрыш с winid и tradeofferid
      // - Заодно удалить значение tradeofferid из pivot-таблицы.
      $win = call_user_func(function(){

        // 1] Получить выигрыш с winid
        $win = \M9\Models\MD4_wins::with(['wins_statuses', 'm8_bots'])
            ->where('id', $this->data['winid'])
            ->first();

        // 2] Если у $win нет tradeofferid, вернуть ""
        $has_tradeofferid = call_user_func(function() USE (&$win) {
          foreach($win['m8_bots'] as &$bot) {
            if($bot['pivot']['tradeofferid'] == $this->data['tradeofferid']) {

              // Удалить значение tradeofferid из pivot-таблицы
              $bot['pivot']['tradeofferid'] = "";
              $bot['pivot']->save();

              // Вернуть true
              return true;

            }
          }
          return false;
        });

        // 3] Если $has_tradeofferid == false, вернуть ""
        if($has_tradeofferid == false) return "";

        // 4] Иначе вернуть $win
        return $win;

      });
      if(empty($win))
        throw new \Exception('Не удалось найти выигрыш в m9.md4_wins по id = '.$this->data['winid']);

      // 3. Получить статусы Active, Ready и Expired
      $status_active = \M9\Models\MD9_wins_statuses::where('status', 'Active')->first();
      $status_ready = \M9\Models\MD9_wins_statuses::where('status', 'Ready')->first();
      $status_expired = \M9\Models\MD9_wins_statuses::where('status', 'Expired')->first();
      if(empty($status_active) || empty($status_ready) || empty($status_expired))
        throw new \Exception('Не удалось найти статусы Active, Ready или Expired в m9.md9_wins_statuses');

      // 4. Отвязать выигрыш от статуса $status_active
      $win->wins_statuses()->detach($status_active->id);

      // 5. Привязать ставку к другому статусу

        // Привязать ставку к статусу $status_expired, если is_expired == 1
        if($this->data['is_expired'] == 1)
          $win->wins_statuses()->attach($status_expired->id);

        // В ином случае, привязать ставку к статусу $status_ready
        else
          $win->wins_statuses()->attach($status_ready->id);

      // 6. Обновить весь кэш
      $result = runcommand('\M9\Commands\C25_update_wins_cache', [
        "all" => true
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 7. Сделать commit
      DB::commit();

      // 8. Сообщить игроку $this->data['id_user'], что его ставка истекла
      // - Через websocket, по частном каналу
      Event::fire(new \R2\Broadcast([
        'channels' => ['m9:private:'.$this->data['id_user']],
        'queue'    => 'm9_lottery_broadcasting',
        'data'     => [
          'task' => 'tradeoffer_wins_cancel',
          'data' => [
            'id_room'     => $this->data['id_room'],
            'wins'        => [
              "active"            => json_decode(Cache::tags(['processing:wins:active:personal:safe'])->get('processing:wins:active:safe:'.$this->data['id_user']), true) ?: "",
              "not_paid_expired"  => json_decode(Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->get('processing:wins:not_paid_expired:safe:'.$this->data['id_user']), true) ?: [],
              //"paid"              => json_decode(Cache::tags(['processing:wins:paid:personal:safe'])->get('processing:wins:paid:safe:'.$this->data['id_user']), true) ?: [],
              //"expired"           => json_decode(Cache::tags(['processing:wins:expired:personal:safe'])->get('processing:wins:expired:safe:'.$this->data['id_user']), true) ?: []
            ]
          ]
        ]
      ]));


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C32_cancel_the_active_win_offer_dbpart from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C32_cancel_the_active_win_offer_dbpart']);
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

