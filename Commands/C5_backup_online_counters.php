<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Backup and restore online counters after server reboot
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
class C5_backup_online_counters extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Сделать бэкап счетчиков онлайна
     *  2. Сделать рестор счётчиков онлайна (единожды, до следующей перезагрузки сервера)
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------//
    // Backup and restore online counters after server reboot //
    //--------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Сделать бэкап счетчиков онлайна
      call_user_func(function(){

        // 1.1. Если с предыдущего выполнения ещё не прошло online_counters_backup_period_sec, ничего не делать

          // 1] Получить значение online_counters_backup_period_sec из конфига
          $online_counters_backup_period_sec = config("M16.online_counters_backup_period_sec") ?: 300;

          // 2] Получить из кэша дату и время последней попытки
          $last_try_datetime = Cache::get('m16:c5_backup_online_counters:backup:lasttry:datetime');

          // 3] Если $last_try_datetime не пуста, и прошло менее $online_counters_backup_period_sec секунд, завершить
          if(!empty($last_try_datetime) && +(\Carbon\Carbon::parse($last_try_datetime)->diffInSeconds(\Carbon\Carbon::now())) < $online_counters_backup_period_sec)
            return;

          // 4] Обновить кэш
          Cache::put('m16:c5_backup_online_counters:backup:lasttry:datetime', \Carbon\Carbon::now()->toDateTimeString(), 300);

        // 1.2. Получить информацию о счётчиках запрашивающего пользователя
        // - Но только, если пользователья на анонимный.
        $counters = runcommand('\M16\Commands\C4_get_counters_data', []);
        if($counters['status'] != 0)
          throw new \Exception($counters['data']['errormsg']);

        // 1.3. Удалить все записи из таблицы MD1_online_counters_backups
        \M16\Models\MD1_online_counters_backups::chunk(200, function ($backups) {
          foreach ($backups as $backup) {
            $backup->delete();
          }
        });

        // 1.4. Сохранить значения $counters в БД
        foreach($counters['data']['counters'] as $counter) {

          // 1] Получить ID пользователя и значение счётчика
          $id_user = array_key_exists('id_user', $counter) ? $counter['id_user'] : "";
          $counter = array_key_exists('counter', $counter) ? $counter['counter'] : "";

          // 2] Если $id_user и $counter, это числа
          if(!empty($id_user) && is_numeric($id_user) && !empty($counter) && is_numeric($counter)) {

            // 2.1] Попробовать найти бэкап для этого пользователя
            $backup = \M16\Models\MD1_online_counters_backups::where('id_user', $id_user)->first();

            // 2.2] Если бэкап найден, обновить и перейти к следующей итерации
            if(!empty($backup)) {
              $backup->online_counter = $counter;
              $backup->save();
              continue;
            }

            // 2.3] Если бэкап не найден, создать, записать, и перейти к следующей итерации
            else {
              $backup = new \M16\Models\MD1_online_counters_backups();
              $backup->id_user = $id_user;
              $backup->online_counter = $counter;
              $backup->save();
              continue;
            }

          }

        }


      });

      // 2. Сделать рестор счётчиков онлайна (единожды, до следующей перезагрузки сервера)
      call_user_func(function(){

        // 2.1. Получить метку, производился ли ресто после запуска
        $is_restored = Redis::get('m16:was_online_counters_restored_after_server_boot');

        // 2.2. Если метка не пуста, завершить
        if(!empty($is_restored))
          return;

        // 2.3. Извлекать данные счётчиков из бэкапа кусками, и применять
        \M16\Models\MD1_online_counters_backups::chunk(200, function ($counters) {
          foreach ($counters as $counter) {

            // 1] Получить id пользователя и значение счётчика
            $id_user = $counter['id_user'];
            $counter = $counter['online_counter'];

            // 2] Установить новое значение в Redis
            Redis::set('m16:online:counter:'.$id_user, $counter);

          }
        });

        // 2.4. Записать 1 в метку $is_restored
        Redis::set('m16:was_online_counters_restored_after_server_boot', 1);

      });

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C5_backup_online_counters from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M16', 'C5_backup_online_counters']);
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

