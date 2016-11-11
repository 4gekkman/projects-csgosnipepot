<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Enforces not finishd round at any time
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
class C17_new_rounds_provider extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из кэша все включенные комнаты
     *  2. Подготовить маячёк
     *  3. Пробежаться по всем $rooms, проверяя статус последнего раунда
     *  4. Если маячёк true, обновить кэш и транслировать свежие данные
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------------//
    // Обеспечение наличия свежего-не-finished раунда в каждой вкл.комнате //
    //---------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из кэша все включенные комнаты
      $rooms = json_decode(Cache::get('processing:rooms'), true);

      // 2. Подготовить маячёк
      // - Если он true, надо обновить кэш и отправить свежие данные в публ.канал
      $should_cache_update_and_translate = false;

      // 3. Пробежаться по всем $rooms, проверяя статус последнего раунда
      foreach($rooms as $room) {

        // 3.1. Если раундов нет, или статус последнего Finished
        // - Создать новый раунд
        if(count($room['rounds']) == 0 || $room['rounds'][0]['rounds_statuses'][count($room['rounds'][0]['rounds_statuses']) - 1]['status'] == 'Finished') {

          // 1] Записать true в маячёк
          $should_cache_update_and_translate = true;

          // 2] Создать и сохранить новый раунд
          $newround = call_user_func(function() USE($room) {

            // 2.1] Создать
            $newround = new \M9\Models\MD2_rounds();

            // 2.2] Связать $newround с $room
            $newround->id_room = $room['id'];

            // 2.3] Подготовить случайное число от 0 до 1
            // - Обеспечив квинтильен вариантов (10^18)
            $key = random_int(1,pow(10,18))/pow(10,18);

            // 2.4] Получить sha512-хэш для $key
            $key_hash = md5(hash('sha512', $key));

            // 2.5] Записать $key и $key_hash в $newround
            $newround->key        = $key;
            $newround->key_hash   = $key_hash;

            // 2.6] Сохранить $newround
            $newround->save();

            // 2.n] Вернуть $newround
            return $newround;

          });

          // 3] Связать $newround со статусом Created
          call_user_func(function() USE(&$newround) {

            // 3.1] Связать, и не забыть указать дату/время started_at
            $newround->rounds_statuses()->attach(1);

            // 3.2] Указать дату/время started_at
            $newround->rounds_statuses()->updateExistingPivot(1, [
              'started_at' => \Carbon\Carbon::now()->toDateTimeString(),
              'comment' => 'Автоматически созданный командой m9.C17 новый раунд'
            ]);

          });

          // 4] Связать с $newround все Accepted-ставки
          // - Не связанные с другими раундами.
          // - Но связанные с комнатой $room.
          call_user_func(function() USE (&$newround, $room) {

            // 4.1] Получить все accepted-ставки
            $bets_accepted = json_decode(Cache::get('processing:bets:accepted'), true);

            // 4.2] Отфильтровать из $bets_accepted неподходящие ставки
            // - Которые уже связаны с любым другим раундом, кроме $newround
            // - Которые не связаны с комнатой $room
            $bets_accepted_filtered = array_filter($bets_accepted, function($value, $key){



            }, ARRAY_FILTER_USE_BOTH);

          });

          // 5] Сделать commit
          DB::commit();

        }

      }

      // 4. Если маячёк true, обновить кэш и транслировать свежие данные
      if($should_cache_update_and_translate == true) {

        // 4.1. Обновить весь кэш
        $result = runcommand('\M9\Commands\C13_update_cache', [
          "all" => true
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // 4.2. Сообщить всем игрокам через публичный канал websockets свежие игровые данные
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:public'],
          'queue'    => 'm9_lottery_broadcasting',
          'data'     => [
            'task' => 'fresh_game_data',
            'data' => [
              'rooms' => Cache::get('processing:rooms')
            ]
          ]
        ]));

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C17_new_rounds_provider from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C17_new_rounds_provider']);
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

