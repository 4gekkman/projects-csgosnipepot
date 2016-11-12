<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - The game processor, fires at every game tick, every second
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
class C11_processor extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Если кэш отсутствует, наполнить.
     *  2. Отслеживание изменения статуса активных офферов
     *  3. Проверка срока годности активных ставок
     *  4. Оповещение игроков о секундах до истечения их активных офферов
     *  5. Отслеживание изменения статусов текущих раундов всех вкл.комнат
     *  6. Обеспечение наличия свежего-не-finished раунда в каждой вкл.комнате
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------//
    // The game processor, fires at every game tick, every second //
    //------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Если кэш отсутствует, наполнить.
      call_user_func(function(){

        // 1] processing:bets:active
        // - Ставки со статусом "Active"
        call_user_func(function(){

          $cache = json_decode(Cache::get('processing:bets:active'), true);
          if(!Cache::has('processing:bets:active') || empty($cache) || count($cache) == 0) {

            $result = runcommand('\M9\Commands\C13_update_cache', [
              "cache2update" => ["processing:bets:active"]
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

        });

        // 2] processing:bets:accepted
        // - Ставки со статусом "Accepted"
        call_user_func(function(){

          $cache = json_decode(Cache::get('processing:bets:accepted'), true);
          if(!Cache::has('processing:bets:accepted') || empty($cache) || count($cache) == 0) {

            $result = runcommand('\M9\Commands\C13_update_cache', [
              "cache2update" => ["processing:bets:accepted"]
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

        });

        // 3] processing:rooms
        // - Все включенные комнаты
        call_user_func(function(){

          $cache = json_decode(Cache::get('processing:rooms'), true);
          if(!Cache::has('processing:rooms') || empty($cache) || count($cache) == 0) {

            $result = runcommand('\M9\Commands\C13_update_cache', [
              "cache2update" => ["processing:rooms"]
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

          }

        });


      });

      // 2. Отслеживание изменения статуса активных офферов
      call_user_func(function(){

        // 2.1. Добавить в очередь processor_hard соотв.команду
        runcommand('\M9\Commands\C14_active_offers_tracking', [],
            0, ['on'=>true, 'name'=>'processor_hard']);

      });

      // 3. Проверка срока годности активных ставок
      call_user_func(function(){

        // 1] Получить активные ставки из кэша
        $bets_active = json_decode(Cache::get('processing:bets:active'), true);

        // 2] Отменить те активные ставки, срок годности которых уже вышел
        foreach($bets_active as $bet) {

          // 2.1] Получить статус ставки $bet
          $status = $bet['bets_statuses'][0];

          // 2.2] Получить дату и время истечения ставки
          $expired_at = $status['pivot']['expired_at'];

          // 2.3] Определить, истёк ли срок годности ставки
          $is_expired = call_user_func(function() USE ($expired_at) {

            return \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($expired_at));

          });

          // 2.4] Если ставка истекла, отменить её
          if($is_expired == true) {

            runcommand('\M9\Commands\C12_cancel_the_active_bet', [
              "betid"        => $bet['id'],
              "tradeofferid" => $bet['tradeofferid'],
              "id_bot"       => $bet['m8_bots'][0]['id'],
              "id_user"      => $bet['m5_users'][0]['id'],
              "id_room"      => $bet['rooms'][0]['id'],
            ], 0, ['on'=>true, 'name'=>'processor_hard']);

          }

        }

      });

      // 4. Оповещение игроков о секундах до истечения их активных офферов
      call_user_func(function(){

        // 1] Получить активные ставки из кэша
        $bets_active = json_decode(Cache::get('processing:bets:active'), true);

        // 2] Оповестить владельцев офферов по частным каналам
        foreach($bets_active as $bet) {

          // 2.1] Вычислить, сколько секунд осталось до истечения оффера $bet
          // - Если оффер истёк, вернуть 0.
          $secs = call_user_func(function() USE ($bet) {

            // 1) Получить expired_at
            $expired_at = \Carbon\Carbon::parse($bet['bets_statuses'][0]['pivot']['expired_at']);

            // 2) Получить текущее серверное время
            $now = \Carbon\Carbon::now();

            // 3) Вычислить, что больше, $expired_at или $now
            $is_expired_gt_than_now = $expired_at->gt($now);

            // 4) Вычесть $now из $expired_at, и получить разницу в секундах
            $sec = $expired_at->diffInSeconds($now);

            // 5) Если оффер уже истёк, вернуть 0
            if($is_expired_gt_than_now == false) return 0;

            // 6) Иначе, вернуть $sec
            else return $sec;

          });

          // 2.2] Транслировать владельцу $bet значение $secs
          Event::fire(new \R2\Broadcast([
            'channels' => ['m9:private:'.$bet['m5_users'][0]['id']],
            'queue'    => 'm9_lottery_broadcasting',
            'data'     => [
              'task' => 'tradeoffer_expire_secs',
              'data' => [
                'id_room' => $bet['rooms'][0]['id'],
                'secs'    => $secs
              ]
            ]
          ]));

        }

      });

      // 5. Отслеживание изменения статусов текущих раундов всех вкл.комнат
      call_user_func(function(){

        // 5.1. Добавить в очередь processor_hard соотв.команду
        runcommand('\M9\Commands\C18_round_statuses_tracking', [],
            0, ['on'=>true, 'name'=>'processor_hard']); // smallbroadcast

      });

      // 6. Обеспечение наличия свежего-не-finished раунда в каждой вкл.комнате
      call_user_func(function(){

        // 6.1. Добавить в очередь processor_hard соотв.команду
        runcommand('\M9\Commands\C17_new_rounds_provider', [],
            0, ['on'=>true, 'name'=>'processor_hard']); // smallbroadcast

      });


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C11_processor from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C11_processor']);
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

