<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create giveaways if needed
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
class C7_create_giveaways extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. С помощью get_counters_data извлечь все данные по счётчикам
     *  2. Сколько минут нужно быть непрерывно онлайн, чтобы получить раздачу
     *  3. Обойти все $counters
     *    3.1. Начать транзакцию
     *    3.2. Получить все необходимые значение из $counter в переменные
     *    3.3. Если время выдавать скин игроку $id_user ещё не пришло, перейти к след.итерации
     *    3.4. Попробовать найти Ready или Active выдачу для $id_user в БД
     *    3.5. Найти среди всех ботов группы Free оного с не пустым инвентарём, и выбрать вещь
     *    3.6. Создать новую выдачу
     *    3.7. Связать $giveaway_new с $id_user
     *    3.8. Если вещь для отдачи найти не удалось, перейти к следующей итерации
     *    3.9. Связать $giveaway_new с ботом
     *    3.10. Связать $giveaway_new с вещью
     *    3.11. Сгенерировать код безопасности, и записать в $giveaway_new
     *    3.n. Подтвердить транзакцию
     *    3.m. Удалить вещь из инвентаря бота, и обновить его кэш
     *    3.l. Обновить весь кэш, кроме связанного с ботами и инвентарём
     *    3.k. Выслать клиенту через частный канал пуш с новой выдачей
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------//
    // Create giveaways if needed //
    //----------------------------//
    $res = call_user_func(function() { try {

      // 1. С помощью get_counters_data извлечь все данные по счётчикам
      $counters = runcommand('\M16\Commands\C4_get_counters_data', []);
      if($counters['status'] != 0)
        throw new \Exception($counters['data']['errormsg']);

      // 2. Сколько минут нужно быть непрерывно онлайн, чтобы получить раздачу
      $giveaway_period_min = $counters['data']['config']['giveaway_period_min'];

      // 3. Обойти все $counters
      foreach($counters['data']['counters'] as $counter_data) {

        // 3.1. Получить все необходимые значение из $counter в переменные
        $id_user = $counter_data['id_user'];
        $counter = $counter_data['counter'];

        // 3.2. Если время выдавать скин игроку $id_user ещё не пришло, перейти к след.итерации
        if($counter < $giveaway_period_min*60)
          continue;

        // 3.3. Если у $id_user есть Ready- или Active-выдача, завершить

          // 1] Ready
          $ready = Cache::tags(['m16:cache:ready:personal'])->get('m16:cache:ready:'.$id_user);
          if(!empty($ready)) continue;

          // 2] Active
          $ready = Cache::tags(['m16:cache:active:personal'])->get('m16:cache:active:'.$id_user);
          if(!empty($ready)) continue;

        // 3.4. Попробовать найти Ready или Active выдачу для $id_user в БД

          // Искать
          $giveaway = \M16\Models\MD2_giveaways::whereHas('m5_users', function($query) USE ($id_user) {
            $query->where('id', $id_user);
          })->whereIn('giveaway_status', [1,2])->first();

          // Если $giveaway найден, перейти к след.итерации
          if(!empty($giveaway))
            continue;

        // 3.5. Найти среди всех ботов группы Free оного с не пустым инвентарём, и выбрать вещь
        $bot_invenrory_item = call_user_func(function(){

          // 1] Получить массив ID всех доступных ботов на раздаче
          $bots = json_decode(Cache::get('m16:cache:bots'), true);

          // 2] Пробежаться по всем $bots
          foreach($bots as $bot) {

            // 2.1] Получить инвентарь бота $bot из кэша
            $inventory = json_decode(Cache::tags(['m16:cache:inventory'])->get('m16:cache:inventory:'.$bot), true);
            if(empty($inventory))
              continue;

            // 2.2] Если инвентарь пуст, перейти к следующей итерации
            if(!array_key_exists('data', $inventory) || !array_key_exists('rgDescriptions', $inventory['data']) || count($inventory['data']['rgDescriptions']) == 0)
              continue;

            // 2.3] Попробовать найти в инвентаре вещь, ещё не связанную с Ready- или Active-выдачами
            $item = call_user_func(function() USE ($inventory) {

              // 1) Получить массив всех active и ready выдач
              $giveaways = call_user_func(function(){
                return array_merge(json_decode(Cache::get('m16:cache:ready'), true), json_decode(Cache::get('m16:cache:active'), true));
              });

              // 2) Получить все assetid этих выдач
              $assetids = collect($giveaways)->map(function($item){ return $item['m8_items'][0]['pivot']['assetid_bots']; })->unique()->toArray();

              // 3) Попробовать найти в rgDescriptions вещь, у которой assetid не в $assetids
              foreach($inventory['data']['rgDescriptions'] as $item) {

                if(!in_array($item['assetid'], $assetids))
                  return $item;

              }

            });
            if(!$item)
              continue;

            // 2.n] Вернуть результаты
            return [
              'id_bot'    => $bot,
              'inventory' => $inventory,
              'item'      => $item
            ];

          }

          // 3] Если ничего не найдено, вернуть пустую строку
          return "";

        });

        // 3.6. Начать транзакцию
        DB::beginTransaction();

        // 3.7. Создать новую выдачу

          // 1] Создать
          $giveaway_new = new \M16\Models\MD2_giveaways();
          $giveaway_new->save();

        // 3.7. Связать $giveaway_new с $id_user
        if(!$giveaway_new->m5_users->contains($id_user))
          $giveaway_new->m5_users()->attach($id_user);

        // 3.9. Если вещь для отдачи найти не удалось, перейти к следующей итерации
        if(empty($bot_invenrory_item)) {
          DB::rollback();
          continue;
        }

        // 3.10. Найти item в базе
        $item = \M8\Models\MD2_items::where('name', $bot_invenrory_item['item']['market_hash_name'])->first();
        if(empty($item)) {
          DB::rollback();
          continue;
        }

        // 3.11. Связать $giveaway_new с ботом
        if(!$giveaway_new->m8_bots->contains($bot_invenrory_item['id_bot'])) {
          $giveaway_new->m8_bots()->attach($bot_invenrory_item['id_bot']);
          $giveaway_new->save();
        }

        // 3.12. Связать $giveaway_new с вещью
        if(!$giveaway_new->m8_items->contains($item['id'])) {
          $giveaway_new->m8_items()->attach($item['id'], ['assetid_bots' => $bot_invenrory_item['item']['assetid']]);
          $giveaway_new->save();
        }

        // 3.13. Сгенерировать код безопасности, и записать в $giveaway_new

          // 1] Сгенерировать случайный код безопасности
          // - Он представляет из себя число из 6 цифр.
          // - У каждого кода безопасности есть свой срок годности.
          $safecode = call_user_func(function() {
            $result = '';
            for($i = 0; $i < 6; $i++) {
              $result .= mt_rand(0, 9);
            }
            return $result;
          });

          // 2] Записать его в $giveaway_new
          $giveaway_new->safecode = $safecode;
          $giveaway_new->save();

        // 3.n. Подтвердить транзакцию
        DB::commit();

        // 3.m. Удалить вещь из инвентаря бота, и обновить его кэш
        call_user_func(function() USE ($bot_invenrory_item) {

          // 1] Получить ID бота, assetid вещи и инвентарь
          $id_bot = $bot_invenrory_item['id_bot'];
          $item_assetid = $bot_invenrory_item['item']['assetid'];
          $inventory = $bot_invenrory_item['inventory'];

          // 2] Отфильтровать из $inventory вещь $item
          $inventory['data']['rgDeescriptions'] = collect($inventory['data']['rgDescriptions'])->filter(function($item) USE ($item_assetid) {
            return $item['assetid'] != $item_assetid;
          })->values()->toArray();

          // 3] Перезаписать инвентарь бота $id_bot в кэше
          Cache::tags(['m16:cache:inventory'])->put('m16:cache:inventory:'.$id_bot, json_encode($inventory, JSON_UNESCAPED_UNICODE), 30);

        });

        // 3.l. Обновить весь кэш, кроме связанного с ботами и инвентарём
        $cacheupdate = runcommand('\M16\Commands\C6_update_cache', [
          "all"   => false,
          "force" => true,
          "cache2update" => [
            "m16:cache:ready", "m16:cache:active"
          ]
        ]);
        if($cacheupdate['status'] != 0)
          throw new \Exception($cacheupdate['data']['errormsg']);

        // 3.k. Выслать клиенту через частный канал пуш с новой выдачей

          // 1] Извлечь из БД giveaway_new
          $giveaway = \M16\Models\MD2_giveaways::with(['m8_items'])
             ->where('id', $giveaway_new['id'])->first();
          if(empty($giveaway))
            $giveaway = $giveaway_new->toArray();

          // 2] Транслировать
          Event::fire(new \R2\Broadcast([
            'channels' => ['m16:private:'.$id_user],
            'queue'    => 'm16_broadcast',
            'data'     => [
              'task' => 'm16_new_giveaway',
              'data' => [
                'giveaway'           => $giveaway->toArray()
              ]
            ]
          ]));

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C7_create_giveaways from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M16', 'C7_create_giveaways']);
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

