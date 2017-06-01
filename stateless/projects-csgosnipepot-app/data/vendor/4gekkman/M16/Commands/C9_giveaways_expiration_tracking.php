<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Tracking of giveaways expiration
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

  namespace M16\Commands;

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
class C9_giveaways_expiration_tracking extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить активные Ready и Active выдачи из кэша
     *  2. Отменить те активные выдачи, срок годности которых уже вышел
     *  3. Получить ожидающие подтверждения трейды из кэша
     *  4. Отменить те ожидающие подтверждения выдачи, срок годности которых уже вышел
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------//
    // Tracking of giveaways expiration //
    //----------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить активные Ready и Active выдачи из кэша
      $trades_ready = json_decode(Cache::get('m16:cache:ready'), true);
      $trades_active = json_decode(Cache::get('m16:cache:active'), true);
      $trades_active = array_merge($trades_active, $trades_ready);
      if(empty($trades_active)) $trades_active = [];

      // 2. Отменить те активные выдачи, срок годности которых уже вышел
      foreach($trades_active as $trade) {

        // 2.1. Получить дату и время истечения трейда
        $expired_at = call_user_func(function() USE ($trade) {

          // 1] Получить giveaways_limit_secs
          $giveaways_limit_secs = config("M16.giveaways_limit_secs");
          if(empty($giveaways_limit_secs))
            $giveaways_limit_secs = 1800;

          // n] Вернуть результат
          return \Carbon\Carbon::parse($trade['created_at'])->addSeconds((int)$giveaways_limit_secs)->toDateTimeString();

        });

        // 2.2. Определить, истёк ли срок годности
        $is_expired = call_user_func(function() USE ($expired_at) {

          return \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($expired_at));

        });

        // 2.3. Если истёк, отменить выдачу
        if($is_expired == true) {

          // 1] Начать транзакцию
          DB::beginTransaction();

          // 2] Получить выдачу с ID $trade['tradeofferid']
          $giveaway = \M16\Models\MD2_giveaways::with(['m5_users'])
            ->where('id', $trade['id'])
            ->first();

          // 3] Если выдача не найдена, перети к следующей итерации
          if(empty($giveaway))
            continue;

          // 4] Изменить статус выдачи на 4
          $giveaway->giveaway_status = 4;
          $giveaway->save();

          // 6] Обновить кэш m16:cache:active и m16:cache:ready
          $result = runcommand('\M16\Commands\C6_update_cache', [
            "all"   => true,
            "force" => true,
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 5] Сделать commit
          DB::commit();

          // 6] Обнулить счётчик онлайна пользователя $id_user
          Redis::set('m16:online:counter:'.$giveaway['m5_users'][0]['id'], 0);

          // 7] Транслировать игроку через частный канал сигнал удалить выдачу
          Event::fire(new \R2\Broadcast([
            'channels' => ['m16:private:'.$giveaway['m5_users'][0]['id']],
            'queue'    => 'm16_broadcast',
            'data'     => [
              'task'   => 'm16_del_giveaway',
              'status' => 0,
              'data'   => [

              ]
            ]
          ]));

        }

      }

      // 3. Получить ожидающие подтверждения трейды из кэша
      $trades_conf = json_decode(Cache::get('m16:processor:trades:status:9'), true);
      if(empty($trades_conf)) $trades_conf = [];

      // 4. Отменить те ожидающие подтверждения выдачи, срок годности которых уже вышел
      foreach($trades_conf as $trade) {

        // 4.1. Получить дату и время истечения трейда
        $expired_at = call_user_func(function() USE ($trade) {

          // 1] Получить giveaways_limit_secs
          $giveaways_limit_secs = config("M16.giveaways_limit_secs");
          if(empty($giveaways_limit_secs))
            $giveaways_limit_secs = 1800;

          // n] Вернуть результат
          return \Carbon\Carbon::parse($trade['created_at'])->addSeconds((int)$giveaways_limit_secs)->toDateTimeString();

        });

        // 4.2. Определить, истёк ли срок годности
        $is_expired = call_user_func(function() USE ($expired_at) {

          return \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($expired_at));

        });

        // 4.3. Если истёк, отменить выдачу
        if($is_expired == true) {

          // 1] Начать транзакцию
          DB::beginTransaction();

          // 2] Получить выдачу с ID $trade['tradeofferid']
          $giveaway = \M16\Models\MD2_giveaways::with(['m5_users'])
            ->where('id', $trade['id'])
            ->first();

          // 3] Если выдача не найдена, перети к следующей итерации
          if(empty($giveaway))
            continue;

          // 4] Изменить статус выдачи на 4
          $giveaway->giveaway_status = 4;
          $giveaway->save();

          // 6] Обновить кэш m16:cache:active и m16:cache:ready
          $result = runcommand('\M16\Commands\C6_update_cache', [
            "all"   => true,
            "force" => true,
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 5] Сделать commit
          DB::commit();

          // 6] Обнулить счётчик онлайна пользователя $id_user
          Redis::set('m16:online:counter:'.$giveaway['m5_users'][0]['id'], 0);

          // 7] Транслировать игроку через частный канал сигнал удалить выдачу
          Event::fire(new \R2\Broadcast([
            'channels' => ['m16:private:'.$giveaway['m5_users'][0]['id']],
            'queue'    => 'm16_broadcast',
            'data'     => [
              'task'   => 'm16_del_giveaway',
              'status' => 0,
              'data'   => [

              ]
            ]
          ]));

        }

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C9_giveaways_expiration_tracking from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M16', 'C9_giveaways_expiration_tracking']);
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

