<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Convert active win trade offer to accepted
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        winid
 *        tradeofferid
 *        id_user
 *        id_room
 *        id_bot
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
class C33_win_active_to_accepted extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить выигрыш с winid
     *  3. Получить статусы Active и Paid
     *  4. Получить активный оффер с tradeofferid
     *  5. Поменять в pivot is_free на 1, и удалить значение tradeofferid
     *  6. Выплатили ли все боты по выигрышу $win всё, что должны?
     *  7. Если $is_all_free == true, поменять статус $win на Paid
     *  8. Сделать commit
     *  9. Обновить весь кэш
     *  10. Сообщить игроку $this->data['id_user'], что его выигрыш выплачен
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------//
    // Обозначить оффер по выплате указанного выигрыша, как выплаченный //
    //------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить и проверить входящие данные
      $validator = r4_validate($this->data, [

        "winid"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "tradeofferid"      => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_user"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_room"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_bot"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Получить выигрыш с winid
      $win = \M9\Models\MD4_wins::with(['wins_statuses', 'm8_items', 'm8_bots'])
          ->where('id', $this->data['winid'])
          ->first();
      if(empty($win))
        throw new \Exception('Не удалось найти выигрыш в m9.md4_wins по id = '.$this->data['winid']);

      // 3. Получить статусы Active и Paid
      $status_active = \M9\Models\MD9_wins_statuses::where('status', 'Active')->first();
      $status_paid = \M9\Models\MD9_wins_statuses::where('status', 'Paid')->first();
      if(empty($status_active) || empty($status_paid))
        throw new \Exception('Не удалось найти статусы Active или Paid в m9.md9_wins_statuses');

      // 4. Получить активный оффер с tradeofferid
      // - В формате:
      //
      //    [
      //      id_bot        // id бота, чей трейдоффер
      //      win           // массив данных о выигрыше
      //      tradeofferid  // id трейдоффера
      //    ]
      //
      $bot_win_tradeofferid = call_user_func(function(){

        // 1] Получить активные выигрыши из кэша
        $wins_active = json_decode(Cache::get('processing:wins:active'), true);

        // 2] Получить из $wins_active список всех активных офферов, и ID отправивших их ботов
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

        // 3] Если tradeofferid есть в $offers_ids, вернуть результат
        if(array_key_exists($this->data['tradeofferid'], $offers_ids))
          return $offers_ids[$this->data['tradeofferid']];

        // 4] Иначе вернуть пустую строку
        else return '';

      });
      if(empty($bot_win_tradeofferid))
        throw new \Exception('Не удалось найти в БД активный оффер #'.$this->data['tradeofferid'].' для выигрыша #'.$this->data['winid']);

      // 5. Поменять в pivot is_free на 1, и удалить значение tradeofferid
      call_user_func(function () USE ($win, $bot_win_tradeofferid) {
        foreach($win['m8_bots'] as &$bot) {
          if($bot['pivot']['tradeofferid'] == $this->data['tradeofferid']) {
            $bot['pivot']['is_free'] = 1;
            $bot['pivot']['tradeofferid'] = "";
            $bot['pivot']->save();
            break;
          }
        }
      });

      // 6. Выплатили ли все боты по выигрышу $win всё, что должны?
      $is_all_free = call_user_func(function() USE ($win) {
        $is_all_free = true;
        foreach($win['m8_bots'] as $bot) {
          if($bot['pivot']['is_free'] == 0)
            $is_all_free = false;
        }
        return $is_all_free;
      });

      // 7. Если $is_all_free == true, поменять статус $win на Paid
      // - И добавить started_at в pivot-таблицу.
      if($is_all_free == true) {
        if($win->wins_statuses->contains($status_active->id)) $win->wins_statuses()->detach($status_active->id);
        if(!$win->wins_statuses->contains($status_paid->id)) $win->wins_statuses()->attach($status_paid->id);
        $win->wins_statuses()->updateExistingPivot($status_paid->id, ["started_at" => \Carbon\Carbon::now()->toDateTimeString()]);
      }

      // 8. Сделать commit
      DB::commit();

      // 9. Обновить весь кэш
      // - Но только, если он не был обновлён в C18.
      // - А там он обновляется только лишь при изменении статуса
      //   любого из раундов, любой из комнат.
      $result = runcommand('\M9\Commands\C25_update_wins_cache', [
        "all" => true
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 10. Сообщить игроку $this->data['id_user'], что его выигрыш выплачен
      // - Через websocket, по частном каналу.
      // - Сообщить те же данные, что при истечения выигрыша, с тем же task.
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
            ],
            'update_inventory'    => true
          ]
        ]
      ]));





    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C33_win_active_to_accepted from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C33_win_active_to_accepted']);
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

