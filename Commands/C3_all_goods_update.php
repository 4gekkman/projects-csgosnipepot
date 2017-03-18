<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update goods unified cache via bot goods cache and cast cast add and subtract arrays via websocket public channel
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
class C3_all_goods_update extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из конфига название группы ботов магазина
     *  2. Получить всех ботов из этой группы
     *  3. Сформировать новый Актуальный Единый Кэш (АЕК) / Unified Actual Cache (UAC)
     *  4. Получить предыдущий UAC
     *  5. Получить массив вещей add, которые есть в $uac_new, но нет в $uac_prev
     *  6. Получить массив вещей subtract, которых нет в $uac_new, но есть в $uac_prev
     *  7. Записать $uac_new в кэш
     *
     *  n. Транслировать изменения товарных остатков всем клиентам через публичный канал websocket
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------------------------------------------------------//
    // Update goods unified cache via bot goods cache and cast cast add and subtract arrays via websocket public channel //
    //-------------------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из конфига название группы ботов магазина
      $group = config("M14.bot_group_name_to_accept_deposits");

      // 2. Получить всех ботов из этой группы
      $bots = \M8\Models\MD1_bots::whereHas('groups', function($queue) USE ($group) {
        $queue->where('name', $group);
      })->get();

      // 3. Сформировать новый Актуальный Единый Кэш (АЕК) / Unified Actual Cache (UAC)
      $uac_new = call_user_func(function() USE ($bots) {

        // 1] Подготовить массив для результата
        $results = [];

        // 2] Наполнить $results
        foreach($bots as $bot) {

          // 2.1] Получить товары бота $bot
          $goods = json_decode(Cache::get("M14:goods:".$bot['steamid']), true);

          // 2.2] Если $goods существует, и это массив, добавить его содержимое в $results
          if(!empty($goods) && is_array($goods)) {
            foreach($goods as $good) {
              array_push($results, $good);
            }
          }

        }

        // n] Вернуть результат
        return $results;

      });

      // 4. Получить предыдущий UAC
      $uac_prev = json_decode(Cache::get("M14:goods"), true);
      if(empty($uac_prev))
        $uac_prev = [];

      // 5. Получить массив вещей add, которые есть в $uac_new, но нет в $uac_prev
      $add = call_user_func(function() USE ($uac_new, $uac_prev) {

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Наполнить $results
        foreach($uac_new as $new) {
          $is_new_not_in_uac_prev = true;
          foreach($uac_prev as $prev) {
            if($new['assetid'] == $prev['assetid'])
              $is_new_not_in_uac_prev = false;
          }
          if($is_new_not_in_uac_prev == true)
            array_push($results, $new);
        }

        // 3] Вернуть $results
        return $results;

      });

      // 6. Получить массив вещей subtract, которых нет в $uac_new, но есть в $uac_prev
      $subtract = call_user_func(function() USE ($uac_new, $uac_prev) {

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Наполнить $results
        foreach($uac_prev as $prev) {
          $is_prev_not_in_uac_new = true;
          foreach($uac_new as $new) {
            if($new['assetid'] == $prev['assetid'])
              $is_prev_not_in_uac_new = false;
          }
          if($is_prev_not_in_uac_new == true)
            array_push($results, $prev);
        }

        // 3] Вернуть $results
        return $results;

      });

      // 7. Записать $uac_new в кэш
      Cache::put("M14:goods", json_encode($uac_new, JSON_UNESCAPED_UNICODE), 300);

      // n. Транслировать изменения товарных остатков всем клиентам через публичный канал websocket
      Event::fire(new \R2\Broadcast([
        'channels' => ['m9:public'],
        'queue'    => 'm14_goods_update_queue',
        'data'     => [
          'task' => 'm14:update:goods',
          'data' => [
            'add'       => $add,
            'subtract'  => $subtract
          ]
        ]
      ]));


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C3_all_goods_update from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C3_all_goods_update']);
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

