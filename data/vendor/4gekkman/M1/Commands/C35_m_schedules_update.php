<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update schedules of console commands of M-packages
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

  namespace M1\Commands;

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
class C35_m_schedules_update extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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

    // Принять входящие данные
    $this->data = $data;

    // Настроить Storage для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path()]);
    $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());


  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Получить коллекцию ID всех M-пакетов в системе
     *  2. Извлечь актуальный список строк для планировщика, без повторов
     *  3. Извлечь текущий список строк из планировщика (помеченных, как 4gekkman's, но без меток)
     *  4. Получить список строк, которые надо удалить из планировщика, с метками 4gekkman's
     *  5. Получить список строк, которые надо оставить в планировщике
     *  6. Получить список строк, которые надо добавить в планировщик, с метками 4gekkman's
     *  7. Удалить строки $str2leave из $kernel
     *  8. Добавить строки $str2add в конец функции schedule в $kernel
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------//
    // Обновить планирование консольных команд для M-пакетов //
    //-------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить коллекцию ID всех M-пакетов в системе
      $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where(function($query){
          $query->where('name','=','M');
        });
      })->pluck('id_inner');

      // 2. Извлечь актуальный список строк для планировщика, без повторов

        // 2.1. Подготовить массив для результата
        $schedule_actual = [];

        // 2.2. Пробежаться по всем M-пакетам
        foreach($packages as $package) {

          // 2.2.1. Получить содержимое сервис-провайдера M-пакета $package
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$package)]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          $sp = $this->storage->get('ServiceProvider.php');

          // 2.2.2. Извлечь массив $add2schedule из $sp
          $add2schedule_temp = [];
          preg_match('#\$add2schedule = \[.*\];#smuiU', $sp, $add2schedule_temp);
          $add2schedule = eval($add2schedule_temp[0].' return $add2schedule; ');

          // 2.2.3. Добавить содержимое $add2schedule в $schedule_actual без повторов
          foreach($add2schedule as $item) {
            if(!in_array($item, $schedule_actual)) array_push($schedule_actual, $item);
          }

        }

      // 3. Извлечь текущий список строк из планировщика (помеченных, как 4gekkman's, но без меток)

        // 3.1. Получить содержимое консольного ядра приложения
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $kernel = $this->storage->get('app/Console/Kernel.php');

        // 3.2. Получить текущий список, без меток 4gekkman's
        preg_match_all("#.* *// *4gekkman's#ui", $kernel, $schedule_current_temp, PREG_SET_ORDER);
        foreach($schedule_current_temp as &$elem) {
          $elem[0] = preg_replace("/^ */ui", '', $elem[0]);
          $elem[0] = preg_replace("# *// *4gekkman's.*$#sui", '', $elem[0]);
        };
        $schedule_current = [];
        for($i=0; $i<count($schedule_current_temp); $i++) {
          if(!in_array($schedule_current_temp[$i][0], $schedule_current))
            array_push($schedule_current, $schedule_current_temp[$i][0]);
        }

      // 4. Получить список строк, которые надо удалить из планировщика, с метками 4gekkman's
      $str2del = array_values(array_diff($schedule_current, $schedule_actual));

      // 5. Получить список строк, которые надо оставить в планировщике
      $str2leave = array_values(array_intersect($schedule_current, $schedule_actual));

      // 6. Получить список строк, которые надо добавить в планировщик, с метками 4gekkman's
      $str2add = [];
      foreach($schedule_actual as $item) {
        if(!in_array($item, $str2del) && !in_array($item, $str2leave))
          array_push($str2add, $item);
      }

      // 7. Удалить строки $str2leave из $kernel
      foreach($str2del as $del) {

        // Добавить к $del в конце // 4gekkman's
        $del = $del . " // 4gekkman's";

        // Заэкранировать строку $del
        $del = preg_quote($del);

        // Удалить
        $kernel = preg_replace("#$del#ui", '', $kernel);

      }

      // 8. Добавить строки $str2add в конец функции schedule в $kernel

        // 8.1. Получить содержимое блока функции schedule из $kernel в виде строки
        preg_match("/protected function schedule\(Schedule .{1}schedule\).*{.*}/smuiU", $kernel, $schedule_str);
        $schedule_str = $schedule_str[0];
        $schedule_str = preg_replace("/[{}]/smuiU", '', $schedule_str);
        $schedule_str = preg_replace("/protected function schedule\(Schedule .{1}schedule\)/smuiU", '', $schedule_str);
        $schedule_str = preg_replace("/^[\s\t]*(\r\n|\n)/smuiU", '', $schedule_str);
        $schedule_str = PHP_EOL . $schedule_str . PHP_EOL;

        // 8.2. Сформировать строку в формате функции schedule

          // 1] Начать формирование
          $schedule_result = "protected function schedule(Schedule \$schedule)" . PHP_EOL;

          // 2] Добавить открывающую фигурную скобку
          $schedule_result = $schedule_result . "    {" . PHP_EOL;

          // 3] Добавить $schedule_str
          $schedule_result = $schedule_result . $schedule_str;

          // 4] Добавить строки из $str2add
          foreach($str2add as $add) {

            $schedule_result = $schedule_result . "        " . $add . ' // 4gekkman\'s' .  PHP_EOL;

          }

          // 4] Добавить закрывающую фигурную скобку
          $schedule_result = $schedule_result . PHP_EOL . "    }";

        // 8.3. Вставить $schedule_result в $kernel
        $kernel = preg_replace("/protected function schedule\(Schedule .{1}schedule\).*{.*}/smuiU", $schedule_result, $kernel);

        // 8.4. Заменить $kernel
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('app/Console/Kernel.php', $kernel);

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C35_m_schedules_update from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C35_m_schedules_update']);
        return [
          "status"  => -2,
          "data"    => $errortext
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

