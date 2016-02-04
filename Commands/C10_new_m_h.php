<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New event handler for existing M-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        mpackid       // ID M-пакета, для которого создаётся обработчик
 *        name          // Имя (eng) для нового обработчика
 *        comid         // ID для нового обработчика
 *        description   // Описание для нового обработчика
 *        event         // Какое событие должен обрабатывать обработчик
 *        keys          // Ключи, на которые реагирует обработчик (массив)
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


//---------//
// Команда //
//---------//
class C10_new_m_h extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить входящие параметры
     *  2. Провести валидацию входящих параметров
     *  3. Проверить существование M-пакета $mpackid
     *  4. Проработать $comid
     *  5. Сформировать название команды (напр.: T1_name)
     *  6. Скопировать и переименовать файл _T1_sample.php
     *  7. Заменить плейсхолдеры в файле значениями параметров
     *  8. Добавить запись о новом обработчике в сервис-провайдер M-пакета $mpackid
     *  9. Вернуть результаты
     *
     */

    //--------------------------------------------------//
    // Создать новый обработчик для указанного M-пакета //
    //--------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $mpackid = $this->data['mpackid'];
      $event = mb_substr($this->data['event'], 1);
      $name = $this->data['name'];
      $comid = $this->data['comid'];
      $description = empty($this->data['description']) ? "Event handler of M-package" : $this->data['description'];
      $keys = $this->data['keys'];

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

      // 4. Определить $comid

        // 4.1. Получить список ID (номеров) всех обработчиков M-пакета $mpackid
        $comids = array_map(function($item){
          return mb_substr($item, 1);
        }, \M1\Models\MD7_handlers::whereHas('packages',function($query) USE ($mpackid) {
            $query->where('id_inner','=',$mpackid);
        })->pluck('id_inner')->toArray());

        // 4.2. Если $comid не передан, определить его автоматически
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

        // 4.3. Если $comid передан, определить, доступен ли он
        if(!empty($comid)) {
          if(in_array($comid, $comids)) {
            throw new \Exception("Can't create event handler with id $comid for M-package $mpackid, because event handler with id $comid already exists.");
          }
        }

      // 5. Сформировать название команды (напр.: H1_name)
      $handlerfullname = 'H'.$comid.'_'.$name;

      // 6. Скопировать и переименовать файл _H1_sample.php
      // - Из Samples в M1 в Commands пакета $mpackid
      // - Назвать его именем $handlerfullname.'.php'
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path()]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copy('vendor/4gekkman/M1/Samples/M/EventHandlers/_H1_sample.php', 'vendor/4gekkman/'.$mpackid.'/EventHandlers/'.$handlerfullname.'.php');

      // 7. Заменить плейсхолдеры в файле значениями параметров

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid.'/EventHandlers')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get($handlerfullname.'.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $file = preg_replace("/PARAMeventPARAM/ui", $event, $file);
        $file = preg_replace("/PARAMkeysPARAM/ui", json_encode($keys, JSON_UNESCAPED_UNICODE), $file);
        $file = preg_replace("/PARAMmpackidPARAM/ui", $mpackid, $file);
        $file = preg_replace("/PARAMhandlerfullnamePARAM/ui", $handlerfullname, $file);
        $file = preg_replace("/PARAMnamePARAM/ui", $name, $file);

        // 4] Перезаписать файл
        $this->storage->put($handlerfullname.'.php', $file);

      // 8. Добавить запись о новом обработчике в сервис-провайдер M-пакета $mpackid

        // 8.1. Получить содержимое сервис-провайдера M-пакета $mpackid
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $sp = $this->storage->get('ServiceProvider.php');

        // 8.2. Получить содержимое массива $pairs2register из $sp в виде массива
        preg_match("/pairs2register *= *\[.*\]/smuiU", $sp, $pairs2register);
        $pairs2register = preg_replace("/pairs2register *= */smuiU", '', $pairs2register);
        $pairs2register = preg_replace("/['\n\r\s\[\]]/smuiU", '', $pairs2register);
        $pairs2register = explode(',', $pairs2register[0]);
        $pairs2register = call_user_func(function() USE ($pairs2register) {
          $result = [];
          foreach($pairs2register as $pair) {
            if(!empty($pair)) {
              $pair_keyval = explode('=>', $pair);
              if(array_key_exists(0, $pair_keyval) && array_key_exists(1, $pair_keyval))
                $result[$pair_keyval[0]] = $pair_keyval[1];
            }
          }
          return $result;
        });
        $pairs2register = array_filter($pairs2register, function($value, $key){
          return !empty($key) && !empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        // 8.3. Добавить в $pairs2register запись про новый обработчик
        // - Ключём в ней должен быть путь к путь к обработчику.
        // - Значением в ней должен быть путь к обрабатываемому событию.
        $pairs2register[$mpackid.'\\EventHandlers\\'.$handlerfullname] = $event;

        // 8.4. Сформировать строку в формате массива из $pairs2register

          // 1] Подготовить строку для результата
          $pairs2register_result = "[" . PHP_EOL;

          // 2] Вставить в $pairs2register_result все значения из $pairs2register
          $len = count($pairs2register); $i = 0;
          foreach($pairs2register as $handler => $event) {
            if($i != +$len-1) $pairs2register_result = $pairs2register_result . "          '" . $handler . "' => '" . $event . "'," . PHP_EOL;
            if($i == +$len-1) $pairs2register_result = $pairs2register_result . "          '" . $handler . "' => '" . $event . "'" . PHP_EOL;
            $i++;
          }

          // 3] Завершить квадратной скобкой
          $pairs2register_result = $pairs2register_result . "        ]";

        // 8.5. Вставить $pairs2register_result в $sp
        $sp = preg_replace("/pairs2register *= *\[.*\]/smuiU", 'pairs2register = '.$pairs2register_result, $sp);

        // 8.6] Заменить $sp
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('ServiceProvider.php', $sp);

      // 9. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "handlerfullname"   => $handlerfullname,
          "package"           => $mpackid
        ]
      ];

    } catch(\Exception $e) {
        $errortext = "Creating of event handler for document for M-package have ended with error: ".$e->getMessage();
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

