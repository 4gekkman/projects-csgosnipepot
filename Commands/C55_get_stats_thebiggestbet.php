<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get stats the biggest bet and update cache if needed
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force       | Обновлять ли принудительно кэш (даже если он есть),
 *        broadcast   | Транслировать ли обновлённую статистику через публичный канал всем клиентам
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
class C55_get_stats_thebiggestbet extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и обработать входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Получить кэш статистики
     *  4. Если кэш пуст, или force == true, обновить кэш
     *  5. Транслировать данные всем клиентам чере публичный канал, если broadcast == true
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------//
    // Get stats the biggest bet and update cache if needed //
    //-------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Принять и обработать входящие данные
      $validator = r4_validate($this->data, [
        "force"           => ["boolean"],
        "broadcast"       => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если force отсутствует, назначить false
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = false;

        // 2.2. Если broadcast отсутствует, назначить false
        if(!array_key_exists('broadcast', $this->data))
          $this->data['broadcast'] = false;

      // 3. Получить кэш статистики
      $cache = json_decode(Cache::get('m9:stats:thebiggestbet'), true);

      // 4. Если кэш пуст, или force == true, обновить кэш
      if(empty($cache) || $this->data['force'] == true) {

        // 1] Получить данные о наибольшей ставке, и кто её сделал
        $thebiggestbet = call_user_func(function(){

          // 1.1] Подготовить переменную для результатов
          $result = "";

          // 1.2] Среди всех ставок найти наибольшую по сумме
          // - Искать только среди Accepted-ставок, связанных с любым раундом
          // - За текущий день.
          // - Среди всех найденных, брать последнюю по времени.
          $bet = \M9\Models\MD3_bets::with(['m5_users'])
              ->whereRaw('sum_cents_at_bet_moment = (SELECT MAX(CAST(`sum_cents_at_bet_moment` as SIGNED)) FROM m9.md3_bets WHERE Date(`created_at`) = CURDATE())')
              ->whereHas('rounds')
              ->whereHas('bets_statuses', function($query){
                $query->where('id_status', 3);
              })
              ->orderBy('created_at', 'desc')
              ->first();

          // 1.3] Если $bet не пуста
          // - Добавить инфу из $win в $result
          if(!empty($bet)) {

            // Получить пользователя
            $user = $bet->m5_users[0];

            // Записать данные в $result
            $result = [
              'id'                      => $user['id'],
              'nickname'                => $user['nickname'],
              'avatar_steam'            => $user['avatar_steam'],
              'sum_cents_at_bet_moment' => $bet['sum_cents_at_bet_moment'],
              'date'                    => \Carbon\Carbon::now()->toDateString(),
              'updated_at'              => $user['updated_at']->toDateTimeString()
            ];

          }

          // 1.4] Если $bet пуста, добавить пустую инфу в $result
          else {

            $result = [
              'id'                      => '',
              'nickname'                => '',
              'avatar_steam'            => '',
              'sum_cents_at_bet_moment' => '',
              'date'                    => '',
              'updated_at'              => ''
            ];

          }

          // n] Вернуть $result
          return $result;

        });

        // 2] Обновить кэш
        Cache::put('m9:stats:thebiggestbet', json_encode($thebiggestbet, JSON_UNESCAPED_UNICODE), 30);

      }

      // 5. Транслировать данные всем клиентам чере публичный канал, если broadcast == true
      if($this->data['broadcast'] == true) {
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:public'],
          'queue'    => 'default',
          'data'     => [
            'task' => 'm9:stats:update:thebiggestbet',
            'data' => [
              'thebiggestbet' => json_decode(Cache::get('m9:stats:thebiggestbet'), true)
            ]
          ]
        ]));
      }

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "thebiggestbet"   => json_decode(Cache::get('m9:stats:thebiggestbet'), true)
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C55_get_stats_thebiggestbet from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C55_get_stats_thebiggestbet']);
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

