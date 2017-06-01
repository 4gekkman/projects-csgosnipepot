<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update cache
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        all
 *        cache2update
 *        force
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
class C7_update_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Назначить значения по умолчанию
     *  3. Обновить кэш, который указан в cache2update
     *    3.1. m14:processor:trades:status:2
     *    3.2. m14:processor:trades:payment_status:1
     *    3.3. m14:processor:trades:payment_status:2
     *    3.4. m14:processor:trades:status:-1
     *    3.5. m14:processor:trades:status:9
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------//
    // Update cache //
    //--------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "all"             => ["boolean"],
        "cache2update"    => ["required_without:all", "array"],
        "force"           => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если all не передано, задать ей значение по умолчанию false
        if(!array_key_exists('all', $this->data))
          $this->data['all'] = false;

        // 2.2. Если cache2update не передан, назначить пустой массив
        if(!array_key_exists('cache2update', $this->data))
          $this->data['cache2update'] = [];

        // 2.3. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

      // 3. Обновить кэш, который указан в cache2update

        // 3.1. m14:processor:trades:status:2
        // - Уже оплаченные трейды, по которым есть активные офферы в Steam.

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m14:processor:trades:status:2'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            ((!Cache::has('m14:processor:trades:status:2') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true)
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m14:processor:trades:status:2", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все трейды со статусом 2 (Active)
              $trades_active = \M14\Models\MD4_trades::with(["m8_bots", "m8_items", "m5_users", "safecodes"])
                  ->where('id_status', 2)->get();

              // 2] Записать JSON с $active_bets в кэш
              Cache::put('m14:processor:trades:status:2', json_encode($trades_active->toArray(), JSON_UNESCAPED_UNICODE), 30);

            }

          }

        // 3.2. m14:processor:trades:payment_status:1
        // - Ожидающие оплаты монетами от покупателей трейды.

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m14:processor:trades:payment_status:1'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            ((!Cache::has('m14:processor:trades:payment_status:1') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true)
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m14:processor:trades:payment_status:1", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все трейды со статусом 0, ожидающие оплаты
              $trades_waiting4payment = \M14\Models\MD4_trades::with(["m8_bots", "m8_items", "m5_users", "purchases", "safecodes"])
                  ->where('id_status', 0)
                  ->where('payment_status_id', 1)
                  ->get();

              // 2] Записать JSON с $active_bets в кэш
              Cache::put('m14:processor:trades:payment_status:1', json_encode($trades_waiting4payment->toArray(), JSON_UNESCAPED_UNICODE), 30);

            }

          }

        // 3.3. m14:processor:trades:payment_status:2
        // - Оплаченные монетами от покупателей трейды, по которым ещё не созданы офферы в Steam.

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m14:processor:trades:payment_status:2'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            ((!Cache::has('m14:processor:trades:payment_status:2') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true)
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m14:processor:trades:payment_status:2", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все трейды со статусом 0, ожидающие оплаты
              $trades_paid = \M14\Models\MD4_trades::with(["m8_bots", "m8_items", "m5_users", "purchases", "safecodes"])
                  ->where('id_status', 0)
                  ->where('payment_status_id', 2)
                  ->get();

              // 2] Записать JSON с $active_bets в кэш
              Cache::put('m14:processor:trades:payment_status:2', json_encode($trades_paid->toArray(), JSON_UNESCAPED_UNICODE), 30);

            }

          }

        // 3.4. m14:processor:trades:status:-1
        // - Несостоявшиеся трейды, которые ждут возврата монет.

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m14:processor:trades:status:-1'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            ((!Cache::has('m14:processor:trades:status:-1') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true)
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m14:processor:trades:status:-1", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все трейды со статусом 2 (Active)
              $trades_active = \M14\Models\MD4_trades::with(["m8_bots", "m8_items", "m5_users", "safecodes"])
                  ->where('id_status', -1)
                  ->where('payment_status_id', 2)
                  ->get();

              // 2] Записать JSON с $active_bets в кэш
              Cache::put('m14:processor:trades:status:-1', json_encode($trades_active->toArray(), JSON_UNESCAPED_UNICODE), 30);

            }

          }

        // 3.5. m14:processor:trades:status:9
        // - Уже оплаченные трейды, по которым есть офферы, ожидающие мобильного подтверждения.

          // 3.5.1. Получить кэш
          $cache = json_decode(Cache::get('m14:processor:trades:status:9'), true);

          // 3.5.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            ((!Cache::has('m14:processor:trades:status:9') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true)
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m14:processor:trades:status:9", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все трейды со статусом 2 (NeedsConfirmation)
              $trades_need_conf = \M14\Models\MD4_trades::with(["m8_bots", "m8_items", "m5_users", "purchases", "safecodes"])
                  ->where('id_status', 9)->get();

              // 2] Записать JSON с $active_bets в кэш
              Cache::put('m14:processor:trades:status:9', json_encode($trades_need_conf->toArray(), JSON_UNESCAPED_UNICODE), 30);

            }

          }




    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C7_update_cache from M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M14', 'C7_update_cache']);
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

