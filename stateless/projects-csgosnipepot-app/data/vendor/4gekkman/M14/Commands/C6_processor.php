<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - The CSGO skins shop processor. Fires every N seconds.
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

  namespace M14\Commands;

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
class C6_processor extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     * Примечания
     *
     *   ▪ Сама команда C6_processor каждые 10 секунд добавляется в очередь "m14_processor".
     *   ▪ Все команды выполняются по очереди либо в "m14_processor" (продакшн), либо в smallbroadcast (отладка)
     *   ▪ Очередь обслуживает демон queue:work --daemon, что обеспечивает высокую скорость работы.
     *
     * Оглавление
     *
     *  А. Подготовить имя очереди, которая будет обрабатывать команды
     *  Б. Если $queue пуста, провести обработку
     *
     *    Б1. Обновить весь кэш, но для каждого, только если он отсутствует
     *    Б2. Выполнить клиринговые операции (оплата заказов)
     *    Б3. Для новых оплаченных трейдов создать офферы в Steam
     *    Б4. Отслеживать изменения статусов активных офферов
     *    Б5. Отслеживать срок годности активных ставок
     *
     *  В. Отслеживать судьбу активных офферов, след которых потерялся из-за сбоев
     *  Г. Записывать в кэш дату и время последнего и предпоследнего выполнения команды processor
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------//
    // The CSGO skins shop processor. Fires every N seconds. //
    //-------------------------------------------------------//
    $res = call_user_func(function() { try {

      // А. Подготовить имя очереди, которая будет обрабатывать команды
      $queues = [
        "prod"  => "m14_processor",   // Продакшн
        "dev"   => "smallbroadcast"   // Отладка
      ];
      $queue = $queues['prod'];

      // Б. Если $queue пуста, провести обработку
      $queue_count = count(Queue::getRedis()->command('LRANGE',['queues:'.$queue, '0', '-1']));
      if($queue_count == 0) {

        // Б1. Обновить весь кэш, но для каждого, только если он отсутствует
        runcommand('\M14\Commands\C7_update_cache', [
          "all"   => true,
          "force" => false
        ], 0, ['on'=>true, 'name'=>$queue]);

        // Б2. Выполнить клиринговые операции (оплата заказов)
        runcommand('\M14\Commands\C8_clearing', [],
            0, ['on'=>true, 'name'=>$queue]);

        // Б3. Для новых оплаченных трейдов создать офферы в Steam
        runcommand('\M14\Commands\C9_make_paid_trades', [],
            0, ['on'=>true, 'name'=>$queue]);

        // Б4. Отслеживать изменения статусов активных офферов
        runcommand('\M14\Commands\C13_active_offers_tracking', [],
            0, ['on'=>true, 'name'=>$queue]);

        // Б5. Отслеживать срок годности активных ставок
        runcommand('\M14\Commands\C10_active_offers_expiration_tracking', [],
            0, ['on'=>true, 'name'=>$queue]);

      }

      // В. Отслеживать судьбу активных офферов, след которых потерялся из-за сбоев
      // - Но выполнять только в том случае, если предыдущая закончила выполняться.
      $cache = json_decode(Cache::get('m14:processing:offers_toothcomb_executing'), true);
      $trades_active = json_decode(Cache::get('m14:processor:trades:status:2'), true);
      if(empty($cache) || !is_array($cache) || count($cache) == 0) {
        if(!empty($trades_active))
          runcommand('\M14\Commands\C15_offers_toothcomb', [],
              0, ['on'=>true, 'name'=>'m14_processor_toothcomb']);
      }

      // Г. Записывать в кэш дату и время последнего и предпоследнего выполнения команды processor
      Cache::put('m14:processing:prev_datetime', Cache::get('m14:processing:last_datetime'), 300);
      Cache::put('m14:processing:last_datetime', \Carbon\Carbon::now()->toDateTimeString(), 300);


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C6_processor from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C6_processor']);
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

