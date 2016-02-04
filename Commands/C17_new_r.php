<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New R-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        endescription     // Описание пакета на английском
 *        packid            // Желаемый ID пакета
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
class C17_new_r extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Определить $packid
     *  4. Сформировать внутренний ID пакета (напр.: R1)
     *  5. Скопировать и переименовать каталог R
     *  6. Заменить плейсхолдеры в файле composer.json
     *  7. Заменить плейсхолдеры в файле readme.md
     *  8. Заменить плейсхолдеры в файле ServiceProvider.php
     *  9. Добавить пр.имён R-пакета в composer.json проекта -> autoload -> psr-4
     *  10. Вернуть результаты
     *
     */

    //-----------------------//
    // Создать новый R-пакет //
    //-----------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $endescription = empty($this->data['endescription']) ? "New R-package" : $this->data['endescription'];
      $packid = $this->data['packid'];

      // 2. Провести валидацию входящих параметров

        // 1] $endescription
        if(!preg_match("/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $endescription))
          throw new \Exception("endescription is not valid (must match \"/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

        // 2] $packid
        if(!preg_match("/^[0-9]+$/ui", $packid))
          throw new \Exception("packid is not valid (must match \"/^[0-9]+$/ui\")");

      // 3. Определить $packid

        // 3.1. Получить список ID (номеров) всех R-пакетов
        $packids = array_map(function($item){
          return mb_substr($item, 1);
        }, \M1\Models\MD2_packages::whereHas('packtypes',function($query) {
            $query->where('name','=','R');
        })->pluck('id_inner')->toArray());

        // 3.2. Если $packid не передан, определить его автоматически
        if(empty($packid)) {
          $packid = call_user_func(function() USE ($packids) {
            if(!is_array($packids) || empty($packids)) {
              return 1;
            }
            else {
              return +max($packids) + 1;
            }
          });
        }

        // 3.3. Если $packid передан, определить, доступен ли он
        if(!empty($packid)) {
          if(in_array($packid, $packids)) {
            throw new \Exception("Can't create R-package with id $packid, because M-package with id $packid already exists.");
          }
        }

      // 4. Сформировать внутренний ID пакета (напр.: R1)
      $packfullname = 'R'.$packid;

      // 5. Скопировать и переименовать каталог R
      // - Из Samples в vendor/4gekkman
      // - Назвать его именем $packfullname
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('vendor')]);
      $this->storage = new \Illuminate\Filesystem\Filesystem(); // new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copyDirectory('vendor/4gekkman/M1/Samples/R', 'vendor/4gekkman/'.$packfullname);

      // 6. Заменить плейсхолдеры в файле composer.json

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('composer.json');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMrudescriptionPARAM/ui", $endescription, $file);

        // 4] Перезаписать файл
        $this->storage->put('composer.json', $file);

      // 7. Заменить плейсхолдеры в файле readme.md

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('readme.md');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMrudescriptionPARAM/ui", $endescription, $file);

        // 4] Перезаписать файл
        $this->storage->put('readme.md', $file);

      // 8. Заменить плейсхолдеры в файле ServiceProvider.php

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('ServiceProvider.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);

        // 4] Перезаписать файл
        $this->storage->put('ServiceProvider.php', $file);

      // 9. Добавить пр.имён R-пакета в composer.json проекта -> autoload -> psr-4

        // 9.1. Получить содержимое composer.json проекта
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $composer = $this->storage->get('composer.json');

        // 9.2. Получить содержимое объекта "psr-4" из $composer в виде массива
        preg_match("/\"psr-4\" *: *\{.*\}/smuiU", $composer, $namespaces);
        $namespaces = preg_replace("/\"psr-4\" *: */smuiU", '', $namespaces);
        $namespaces = preg_replace("/['\n\r\s\{\}]/smuiU", '', $namespaces);
        $namespaces = explode(',', $namespaces[0]);
        $namespaces = array_values(array_filter($namespaces, function($item){
          return !empty($item);
        }));

        // 9.3. Добавить в $namespaces пространство имён нового пакета
        array_push($namespaces, '"' . $packfullname . '\\\\":"vendor/4gekkman/' . $packfullname . '"');

        // 9.4. Сформировать строку в формате значения "psr-4" из composer.json

          // 1] Подготовить строку для результата
          $namespaces_result = "{" . PHP_EOL;

          // 2] Вставить в $namespaces_result все значения из $commands
          for($i=0; $i<count($namespaces); $i++) {
            if($i != count($namespaces)-1 )
              $namespaces_result = $namespaces_result . "            " . $namespaces[$i] . "," . PHP_EOL;
            else
              $namespaces_result = $namespaces_result . "            " . $namespaces[$i] . PHP_EOL;
          }

          // 3] Завершить квадратной скобкой c запятой
          $namespaces_result = $namespaces_result . "        }";

        // 9.5. Заменить все \\\\ в $namespaces_result на \\\\\\
        $namespaces_result = preg_replace("/\\\\/smuiU", "\\\\\\", $namespaces_result);

        // 9.6. Вставить $namespaces_result в $composer
        $composer = preg_replace("/\"psr-4\" *: *\{.*\}/smuiU", '"psr-4": '.$namespaces_result, $composer);

        // 9.7. Заменить $composer
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('composer.json', $composer);

      // 10. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "packfullname" => $packfullname,
        ]
      ];


    } catch(\Exception $e) {
        $errortext = "Creating of new R-package have ended with error: ".$e->getMessage();
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

