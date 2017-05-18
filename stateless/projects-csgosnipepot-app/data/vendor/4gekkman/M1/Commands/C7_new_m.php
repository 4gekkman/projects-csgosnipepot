<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New M-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        runame          // Имя пакета на русском
 *        enname          // Имя пакета на английском
 *        rudescription   // Описание пакета на русском
 *        endescription   // Описание пакета на английском
 *        packid          // Желаемый ID пакета
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
class C7_new_m extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  4. Сформировать внутренний ID пакета (напр.: M1)
     *  5. Скопировать и переименовать каталог M
     *  6. Удалить шаблоны из нового пакета
     *  7. Заменить плейсхолдеры в файле Middlewares/AfterMiddleware.php
     *  8. Заменить плейсхолдеры в файле Middlewares/BeforeMiddleware.php
     *  9. Заменить плейсхолдеры в файле composer.json
     *  10. Заменить плейсхолдеры в файле readme.md
     *  11. Заменить плейсхолдеры в файле settings.php
     *  12. Заменить плейсхолдеры в файле ServiceProvider.php
     *  13. Переименовать файл Database/_m1_model.mwb
     *  14. Добавить пр.имён M-пакета в composer.json проекта -> autoload -> psr-4
     *  15. Создать новый репозиторий для нового пакета
     *  16. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Создать новый M-пакет //
    //-----------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $runame = $this->data['runame'];
      $enname = $this->data['enname'];
      $rudescription = empty($this->data['rudescription']) ? "Новый M-пакет" : $this->data['rudescription'];
      $endescription = empty($this->data['endescription']) ? "New M-package" : $this->data['endescription'];
      $packid = $this->data['packid'];
      $github = $this->data['github'];

      // 2. Провести валидацию входящих параметров

        // 2] $runame
        if(!preg_match("/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*/ui", $runame))
          throw new \Exception("runame is not valid (must match \"/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

        // 2] $enname
        if(!preg_match("/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $enname))
          throw new \Exception("enname is not valid (must match \"/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

        // 3] $rudescription
        if(!preg_match("/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $rudescription))
          throw new \Exception("rudescription is not valid (must match \"/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

        // 4] $endescription
        if(!preg_match("/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $endescription))
          throw new \Exception("endescription is not valid (must match \"/^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui\")");

        // 5] $packid
        if(!preg_match("/^[0-9]+$/ui", $packid))
          throw new \Exception("packid is not valid (must match \"/^[0-9]+$/ui\")");

        // 6] $github
        if(!preg_match("/^(yes|no)$/ui", $github))
          throw new \Exception("github is not valid (must match \"/^(yes|no)$/ui\")");

      // 3. Определить $packid
      $packid = call_user_func(function() USE ($packid) {

        // 3.1. Получить токен от github
        $token = call_user_func(function(){

          // 1] Проверить работоспособность пароля и токена для github, указанных в конфиге M1
          $check = runcommand('\M1\Commands\C48_github_check');
          if($check['status'] != 0)
            return "";

          // 2] Вернуть токен от github
          return $check['data']['token'];

        });
        if(empty($token))
          throw new \Exception("The password/token for github from config not working.");

        // 3.2. Получить массив имён всех пакетов пользователя 4gekkman с github
        $all_github_user_packs = call_user_func(function() USE ($token) {

          // 1] Создать экземпляр guzzle
          $guzzle = new \GuzzleHttp\Client();

          // 2] Выполнить запрос
          $request_result = $guzzle->request('GET', 'https://api.github.com/user/repos', [
            'headers' => [
              'Authorization' => 'token '. $token
            ],
            'query' => [
              'affiliation' => 'owner',
              'direction'   => 'asc',
              'per_page'    => 10000
            ]
          ]);
          $status = $request_result->getStatusCode();
          $body = $request_result->getBody();
          if($status != 200)
            return [
              "success" => false,
              "result"  => []
            ];

          // 3] Получить результирующий массив
          $result = collect(json_decode($body, true))->pluck('name')->toArray();

          // 4] Провести фильтрацию результирующего массива
          $result = array_values(collect($result)->filter(function($item){
            if(!preg_match("/^([MWR]{1}[1-9]{1}[0-9]*|[DL]{1}[0-9]{5,100})$/ui", $item))
              return false;
            return true;
          })->toArray());

          // n) Вернуть результаты
          return [
            "success"       => true,
            "result"        => $result
          ];

        });
        if($all_github_user_packs['success'] == false)
          throw new \Exception("Couldn't get packages list from github.");

        // 3.3. Отфильтровать из $all_github_user_packs['result'] все не M-пакеты
        $all_github_user_packs['result'] = array_values(collect($all_github_user_packs['result'])->filter(function($item){
          if(!preg_match("/^[M]{1}[1-9]{1}[0-9]*$/ui", $item))
            return false;
          return true;
        })->toArray());

        // 3.4. Получить из $all_github_user_packs['result'] список номеров из ID
        $all_github_user_packs_nums = collect($all_github_user_packs['result'])->map(function($item){
          return preg_replace("/^m/ui", "", $item);
        })->toArray();

        // 3.5. Получить список ID (номеров) всех M-пакетов, которые фактически есть в проекте
        $packids = array_map(function($item){
          return mb_substr($item, 1);
        }, \M1\Models\MD2_packages::whereHas('packtypes',function($query) {
            $query->where('name','=','M');
        })->pluck('id_inner')->toArray());

        // 3.6. Добавить в $packids значения из $all_github_user_packs_nums, которых там ещё нет
        $packids = array_values(array_unique(array_merge($packids, $all_github_user_packs_nums), SORT_REGULAR));

        // 3.7. Если $packid не передан, определить его автоматически
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

        // 3.8. Если $packid передан, определить, доступен ли он
        if(!empty($packid)) {
          if(in_array($packid, $packids)) {
            throw new \Exception("Can't create M-package with id $packid, because M-package with id $packid already exists.");
          }
        }

        // 3.n. Вернуть результат
        return $packid;

      });

      // 4. Сформировать внутренний ID пакета (напр.: M1)
      $packfullname = 'M'.$packid;

      // 5. Скопировать и переименовать каталог M
      // - Из Samples в vendor/4gekkman
      // - Назвать его именем $packfullname
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('vendor')]);
      $this->storage = new \Illuminate\Filesystem\Filesystem(); // new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copyDirectory('vendor/4gekkman/M1/Samples/M', 'vendor/4gekkman/'.$packfullname);

      // 6. Удалить шаблоны из нового пакета
      // - Commands, Console, EventHandlers, Models, Cnfupds
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->delete('Commands/_C1_sample.php');
      $this->storage->delete('Console/_T1_sample.php');
      $this->storage->delete('EventHandlers/_H1_sample.php');
      $this->storage->delete('Models/_MD1_sample.php');
      $this->storage->delete('Cnfupds/_cfgupdate_sample.php');

      // 7. Заменить плейсхолдеры в файле Middlewares/AfterMiddleware.php

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname.'/Middlewares')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('AfterMiddleware.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);

        // 4] Перезаписать файл
        $this->storage->put('AfterMiddleware.php', $file);

      // 8. Заменить плейсхолдеры в файле Middlewares/BeforeMiddleware.php

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname.'/Middlewares')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('BeforeMiddleware.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);

        // 4] Перезаписать файл
        $this->storage->put('BeforeMiddleware.php', $file);

      // 9. Заменить плейсхолдеры в файле composer.json

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('composer.json');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMrudescriptionPARAM/ui", $rudescription, $file);

        // 4] Перезаписать файл
        $this->storage->put('composer.json', $file);

      // 10. Заменить плейсхолдеры в файле readme.md

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('readme.md');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMrudescriptionPARAM/ui", $rudescription, $file);

        // 4] Перезаписать файл
        $this->storage->put('readme.md', $file);

      // 11. Заменить плейсхолдеры в файле settings.php

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get('settings.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMrudescriptionPARAM/ui", $rudescription, $file);
        $file = preg_replace("/PARAMendescriptionPARAM/ui", $endescription, $file);
        $file = preg_replace("/PARAMrunamePARAM/ui", $runame, $file);
        $file = preg_replace("/PARAMennamePARAM/ui", $enname, $file);

        // 4] Перезаписать файл
        $this->storage->put('settings.php', $file);

      // 12. Заменить плейсхолдеры в файле ServiceProvider.php

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

      // 13. Переименовать файл Database/_m1_model.mwb

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname.'/Database')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Переименовать файл
        $this->storage->move('_m1_model.mwb', $packfullname.'_model.mwb');

      // 14. Добавить пр.имён M-пакета в composer.json проекта -> autoload -> psr-4

        // 14.1. Получить содержимое composer.json проекта
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $composer = $this->storage->get('composer.json');

        // 14.2. Получить содержимое объекта "psr-4" из $composer в виде массива
        preg_match("/\"psr-4\" *: *\{.*\}/smuiU", $composer, $namespaces);
        $namespaces = preg_replace("/\"psr-4\" *: */smuiU", '', $namespaces);
        $namespaces = preg_replace("/['\n\r\s\{\}]/smuiU", '', $namespaces);
        $namespaces = explode(',', $namespaces[0]);
        $namespaces = array_values(array_filter($namespaces, function($item){
          return !empty($item);
        }));

        // 14.3. Добавить в $namespaces пространство имён нового пакета
        array_push($namespaces, '"' . $packfullname . '\\\\":"vendor/4gekkman/' . $packfullname . '"');

        // 14.4. Сформировать строку в формате значения "psr-4" из composer.json

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

        // 14.5. Заменить все \\\\ в $namespaces_result на \\\\\\
        $namespaces_result = preg_replace("/\\\\/smuiU", "\\\\\\", $namespaces_result);

        // 14.6. Вставить $namespaces_result в $composer
        $composer = preg_replace("/\"psr-4\" *: *\{.*\}/smuiU", '"psr-4": '.$namespaces_result, $composer);

        // 14.7. Заменить $composer
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('composer.json', $composer);

      // 15. Создать новый репозиторий для нового пакета
      // - Если $github == 'yes'
      if($github == 'yes') {
        $result = runcommand('\M1\Commands\C49_github_new', ["id_inner" => $packfullname]);
        if($result['status'] != 0)
          throw new \Exception($result['data']);
      }

      // 16. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "packfullname" => $packfullname,
        ]
      ];



    } catch(\Exception $e) {
        $errortext = 'Invoking of command C7_new_m from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C49_github_new']);
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

