<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New console command for existing M-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        mpackid       // ID M-пакета, для которого создаётся к.команда
 *        name          // Имя (eng) для новой к.команды
 *        comid         // ID для новой к.команды
 *        description   // Описание для новой к.команды
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
 *      - Текст ошибки.
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример для админ.документов:  M1\Commands

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


//--------------------//
// Консольная команда //
//--------------------//
class C9_new_m_t extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Переменная такая-то
    // protected $data;

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
     *  1. Получить входящие параметры
     *  2. Провести валидацию входящих параметров
     *  3. Проверить существование M-пакета $mpackid
     *  4. Проверить, нет ли уже консольной команды с именем $name у M-пакета $mpackid
     *  5. Проработать $comid
     *  6. Сформировать название команды (напр.: T1_name)
     *  7. Скопировать и переименовать файл _T1_sample.php
     *  8. Заменить плейсхолдеры в файле значениями параметров
     *  9. Добавить запись о новой к.команде в сервис-провайдер M-пакета $mpackid
     *  10. Вернуть результаты
     *
     */

    //----------------------------------------------------------//
    // Создать новую консольную команду для указанного M-пакета //
    //----------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $mpackid = $this->data['mpackid'];
      $name = $this->data['name'];
      $comid = $this->data['comid'];
      $description = empty($this->data['description']) ? "Console command of M-package" : $this->data['description'];

      // 2. Провести валидацию входящих параметров

        // 1] $mpackid
        if(!preg_match("/^[M]{1}[0-9]$/ui", $mpackid))
          throw new \Exception("$mpackid is not valid (must match \"/^[M]{1}[0-9]*$/ui\")");

        // 2] $name
        if(!preg_match("/^[a-z_0-9]+$/ui", $name))
          throw new \Exception("$name is not valid (must match \"/^[a-z_0-9]+$/ui\")");

        // 3] $comid
        if(!preg_match("/^[0-9]+$/ui", $comid))
          throw new \Exception("$comid is not valid (must match \"/^[0-9]+$/ui\")");

        // 4] $description
        if(!preg_match("/^[-0-9a-z\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $description))
          throw new \Exception("$description is not valid (must match \"/^[-0-9a-z\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

      // 3. Проверить существование M-пакета $mpackid
      $mpack = \M1\Models\MD2_packages::where('id_inner','=',$mpackid)->first();
      if(empty($mpack))
        throw new \Exception("Package $mpackid does not exist.");

      // 4. Проверить, нет ли уже консольной команды с именем $name у M-пакета $mpackid

        // 4.1. Получить массив имён всех консольных команд M-пакета $mpackid
        $ccnames = array_map(function($item){
          return preg_replace("/^[T]{1}[0-9]+_/ui","",$item);
        }, \M1\Models\MD6_console::whereHas('package',function($query) USE ($mpackid) {
            $query->where('id_inner','=',$mpackid);
        })->pluck('name')->toArray());

        // 4.2. Если $name есть в $ccnames, возбудить ошибку
        if(in_array($name, $ccnames))
          throw new \Exception("Console command with name '$name' already exists in M-package $mpackid.");

      // 5. Определить $comid

        // 5.1. Получить список ID (номеров) всех к.команд M-пакета $mpackid
        $comids = array_map(function($item){
          return mb_substr($item, 1);
        }, \M1\Models\MD6_console::whereHas('package',function($query) USE ($mpackid) {
            $query->where('id_inner','=',$mpackid);
        })->pluck('id_inner')->toArray());

        // 5.2. Если $comid не передан, определить его автоматически
        if(empty($comid)) {
          $comid = call_user_func(function() USE ($comids) {
            if(!is_array($comids) || empty($comids)) {
              return 1;
            }
            else {
              return +max($comids) + 1;
            }
          });
        }

        // 5.3. Если $comid передан, определить, доступен ли он
        if(!empty($comid)) {
          if(in_array($comid, $comids)) {
            throw new \Exception("Can't create console command with id $comid for M-package $mpackid, because console command with id $comid already exists.");
          }
        }

      // 6. Сформировать название команды (напр.: T1_name)
      $comfullname = 'T'.$comid.'_'.$name;

      // 7. Скопировать и переименовать файл _T1_sample.php
      // - Из Samples в M1 в Commands пакета $mpackid
      // - Назвать его именем $comfullname.'.php'
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path()]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copy('vendor/4gekkman/M1/Samples/M/Console/_T1_sample.php', 'vendor/4gekkman/'.$mpackid.'/Console/'.$comfullname.'.php');

      // 8. Заменить плейсхолдеры в файле значениями параметров

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid.'/Console')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get($comfullname.'.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $file = preg_replace("/PARAMmpackidPARAM/ui", $mpackid, $file);
        $file = preg_replace("/PARAMmpackid_lowcasePARAM/ui", mb_strtolower($mpackid), $file);
        $file = preg_replace("/PARAMccomfullnamePARAM/ui", $comfullname, $file);
        $file = preg_replace("/PARAMnamePARAM/ui", $name, $file);

        // 4] Перезаписать файл
        $this->storage->put($comfullname.'.php', $file);

      // 9. Добавить запись о новой к.команде в сервис-провайдер M-пакета $mpackid

        // 9.1. Получить содержимое сервис-провайдера M-пакета $mpackid
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $sp = $this->storage->get('ServiceProvider.php');

        // 9.2. Получить содержимое массива $commands из $sp в виде массива
        preg_match("/commands *= *\[.*\]/smuiU", $sp, $commands);
        $commands = preg_replace("/commands *= */smuiU", '', $commands);
        $commands = preg_replace("/['\n\r\s\[\]]/smuiU", '', $commands);
        $commands = explode(',', $commands[0]);
        $commands = array_values(array_filter($commands, function($item){
          return !empty($item);
        }));

        // 9.3. Добавить в $commands запись про новую команду
        array_push($commands, '\\'.$mpackid.'\\Console\\'.$comfullname);

        // 9.4. Сформировать строку в формате массива из $commands

          // 1] Подготовить строку для результата
          $commands_result = "[" . PHP_EOL;

          // 2] Вставить в $commands_result все значения из $commands
          for($i=0; $i<count($commands); $i++) {
            if($i != count($commands)-1 )
              $commands_result = $commands_result . "          '" . $commands[$i] . "'," . PHP_EOL;
            else
              $commands_result = $commands_result . "          '" . $commands[$i] . "'" . PHP_EOL;
          }

          // 3] Завершить квадратной скобкой c запятой
          $commands_result = $commands_result . "        ]";

        // 9.5. Вставить $commands_result в $sp
        $sp = preg_replace("/commands *= *\[.*\]/smuiU", 'commands = '.$commands_result, $sp);

        // 9.6. Заменить $sp
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('ServiceProvider.php', $sp);

      // 10. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "ccomfullname"  => $comfullname,
          "package"       => $mpackid
        ]
      ];


    } catch(\Exception $e) {
        $errortext = "Creating of console command for M-package have ended with error: ".$e->getMessage();
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;

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

