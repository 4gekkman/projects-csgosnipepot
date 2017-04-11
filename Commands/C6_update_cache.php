<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update giveaways cache
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
class C6_update_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *    3.1. m16:cache:ready +
     *         m16:cache:ready:<id пользователя>
     *    3.2. m16:cache:active +
     *         m16:cache:active:<id пользователя>
     *    3.3. m16:cache:bots +
     *         m16:cache:inventory:<id бота>
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------//
    // Update giveaways cache //
    //------------------------//
    $res = call_user_func(function() { try {

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
          $this->data['all'] = true;

        // 2.2. Если cache2update не передан, назначить пустой массив
        if(!array_key_exists('cache2update', $this->data))
          $this->data['cache2update'] = [];

        // 2.3. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

      // 3. Обновить кэш, который указан в cache2update

        // 3.1. m16:cache:ready + m16:cache:ready:<id пользователя>

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m16:cache:ready'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m16:cache:ready') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m16:cache:ready", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все раздачи со статусом Ready (1)
              $giveaways_ready = \M16\Models\MD2_giveaways::with(["m8_bots", "m8_items", "m5_users"])
                ->where('giveaway_status', 1)
                ->get();

              // 2] Записать JSON с $giveaways_ready в кэш
              Cache::put('m16:cache:ready', json_encode($giveaways_ready->toArray(), JSON_UNESCAPED_UNICODE), 30);

              // 3] Записать индивидуальный для каждого игрока из $giveaways_ready кэш

                // 3.1] Получить не повторяющийся список ID пользователей, которые есть в $giveaways_ready
                $users_ids = call_user_func(function() USE ($giveaways_ready) {
                  $result = [];
                  foreach($giveaways_ready as $giveaway) {
                    $id_user = $giveaway['m5_users'][0]['id'];
                    if(!in_array($id_user, $result))
                      array_push($result, $id_user);
                  }
                  return $result;
                });

                // 3.2] Получить для каждого $users_ids свой массив данных
                $users_data = call_user_func(function() USE ($users_ids, $giveaways_ready) {
                  $result = [];
                  foreach($users_ids as $id) {
                    foreach($giveaways_ready as $giveaway) {
                      $id_user = $giveaway['m5_users'][0]['id'];
                      if($id_user == $id) {
                        if(!array_key_exists($id_user, $result)) $result[$id_user] = [];
                        array_push($result[$id_user], $giveaway);
                      }
                    }
                  }
                  return $result;
                });

                // 3.3] Сбросить весь персонализированный кэш
                Cache::tags(['m16:cache:ready:personal'])->flush();

                // 3.4] Записать $users_data в кэш
                foreach($users_data as $id_user => $giveaways) {

                  // Удалить из giveaways инфу о боте и пользователе
                  $giveaways = collect($giveaways[0]);
                  $giveaways = $giveaways->filter(function($value, $key){

                    // 1) Удалить m8_bots и m5_users из $value
                    if($key == 'm8_bots') return false;
                    if($key == 'm5_users') return false;

                    // 2) Вернуть $value
                    return true;

                  });
                  $giveaways = $giveaways->toArray();

                  // Записать кэш
                  Cache::tags(['m16:cache:ready:personal'])->put('m16:cache:ready:'.$id_user, json_encode($giveaways, JSON_UNESCAPED_UNICODE), 30);

                }

            }

          }

        // 3.2. m16:cache:active

          // 3.2.1. Получить кэш
          $cache = json_decode(Cache::get('m16:cache:active'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m16:cache:active') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m16:cache:active", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все раздачи со статусом Active (2)
              $giveaways_active = \M16\Models\MD2_giveaways::with(["m8_bots", "m8_items", "m5_users"])
                ->where('giveaway_status', 2)
                ->get();

              // 2] Записать JSON с $giveaways_active в кэш
              Cache::put('m16:cache:active', json_encode($giveaways_active->toArray(), JSON_UNESCAPED_UNICODE), 30);

              // 3] Записать индивидуальный для каждого игрока из $giveaways_active кэш

                // 3.1] Получить не повторяющийся список ID пользователей, которые есть в $giveaways_active
                $users_ids = call_user_func(function() USE ($giveaways_active) {
                  $result = [];
                  foreach($giveaways_active as $giveaway) {
                    $id_user = $giveaway['m5_users'][0]['id'];
                    if(!in_array($id_user, $result))
                      array_push($result, $id_user);
                  }
                  return $result;
                });

                // 3.2] Получить для каждого $users_ids свой массив данных
                $users_data = call_user_func(function() USE ($users_ids, $giveaways_active) {
                  $result = [];
                  foreach($users_ids as $id) {
                    foreach($giveaways_active as $giveaway) {
                      $id_user = $giveaway['m5_users'][0]['id'];
                      if($id_user == $id) {
                        if(!array_key_exists($id_user, $result)) $result[$id_user] = [];
                        array_push($result[$id_user], $giveaway);
                      }
                    }
                  }
                  return $result;
                });

                // 3.3] Сбросить весь персонализированный кэш
                Cache::tags(['m16:cache:active:personal'])->flush();

                // 3.4] Записать $users_data в кэш
                foreach($users_data as $id_user => $giveaways) {

                  // Удалить из giveaways инфу о боте и пользователе
                  $giveaways = collect($giveaways[0]);
                  $giveaways = $giveaways->filter(function($value, $key){

                    // 1) Удалить m8_bots и m5_users из $value
                    if($key == 'm8_bots') return false;
                    if($key == 'm5_users') return false;

                    // 2) Вернуть $value
                    return true;

                  });
                  $giveaways = $giveaways->toArray();

                  // Записать кэш
                  Cache::tags(['m16:cache:active:personal'])->put('m16:cache:active:'.$id_user, json_encode($giveaways, JSON_UNESCAPED_UNICODE), 30);

                }

            }

          }

        // 3.3. m16:cache:bots

          // 3.3.1. Получить кэш
          $cache = json_decode(Cache::get('m16:cache:bots'), true);

          // 3.3.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m16:cache:bots') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("m16:cache:bots", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить всех ботов, связанных с группой Free
              $bots = \M8\Models\MD1_bots::whereHas('groups', function($queue){
                $queue->where('name', 'Free');
              })->get();

              // 2] Записать JSON с массивом ID этих ботов в кэш
              Cache::put('m16:cache:bots', json_encode($bots->pluck('id')->toArray(), JSON_UNESCAPED_UNICODE), 30);

              // 3] Получить инвентарь каждого из ботов, и записать в кэш
              foreach($bots as $bot) {

                // 3.1] Получить инвентарь
                $inventory = runcommand('\M8\Commands\C4_getinventory', [
                  'force' => true,
                  'steamid' => $bot['steamid']
                ]);
                if($inventory['status'] != 0)
                  continue;

                // 3.2] Записать его в кэш
                Cache::tags(['m16:cache:inventory'])->put('m16:cache:inventory:'.$bot['id'], json_encode($inventory, JSON_UNESCAPED_UNICODE), 30);

              }

              // 4] Если $bots пуст, сбросить весь персонализированный кэш
              if(count($bots) == 0) {
                Cache::tags(['m16:cache:inventory'])->flush();
              }

            }

          }


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C6_update_cache from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M16', 'C6_update_cache']);
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

