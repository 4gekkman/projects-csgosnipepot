<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New D-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        layoutid      // id шаблона (L-пакета) для этого D-пакета
 *        name          // имя пакета на английском
 *        description   // описание пакета на английском
 *        packid        // желаемый id пакета
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
class C14_new_d extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих параметров
     *  2. Определить $packid
     *  3. Сформировать внутренний ID пакета (напр.: D1)
     *  4. Скопировать и переименовать каталог D
     *  5. Заменить плейсхолдеры в файлах нового D-пакета
     *  6. Добавить пр.имён D-пакета в composer.json проекта -> autoload -> psr-4
     *  7. Создать новый репозиторий для нового пакета
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Создать новый D-пакет //
    //-----------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию и извлечение входящих параметров

        // 1.1. Провести валидацию
        $validator = r4_validate($this->data, [

          "layoutid"        => ["required", "regex:/(^0$|^L[1-9]+[0-9]*$)/ui"],
          "name"            => ["required", "regex:/(^0$|^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$)/ui"],
          "description"     => ["required", "regex:/(^0$|^[-0-9a-zа-яё\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$)/ui"],
          "packid"          => ["required", "regex:/(^0$|^[0-9]+$)/ui"],
          "github"          => ["required", "regex:/^(yes|no)$/ui"],

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }

        // 1.2. Произвести извлечение
        $layoutid     = $this->data['layoutid'] ?: "L1";
        $name         = $this->data['name'] ?: "";
        $description  = $this->data['description'] ?: "Описание нового D-пакета";
        $packid       = $this->data['packid'] ?: "";
        $github       = $this->data['github'];

      // 2. Определить $packid
      $packid = call_user_func(function() USE ($packid) {

        // 2.1. Получить токен от github
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

        // 2.2. Получить массив имён всех пакетов пользователя 4gekkman с github
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

        // 2.3. Отфильтровать из $all_github_user_packs['result'] все не D-пакеты серии 10k+
        $all_github_user_packs['result'] = array_values(collect($all_github_user_packs['result'])->filter(function($item){
          if(!preg_match("/^[D]{1}[0-9]{5,100}$/ui", $item))
            return false;
          return true;
        })->toArray());

        // 2.4. Получить из $all_github_user_packs['result'] список номеров из ID
        $all_github_user_packs_nums = collect($all_github_user_packs['result'])->map(function($item){
          return preg_replace("/^d/ui", "", $item);
        })->toArray();

        // 2.5. Получить список ID (номеров) всех D-пакетов, которые фактически есть в проекте
        $packids = array_map(function($item){
          return mb_substr($item, 1);
        }, \M1\Models\MD2_packages::whereHas('packtypes',function($query) {
            $query->where('name','=','D');
        })->pluck('id_inner')->toArray());

        // 2.6. Добавить в $packids значения из $all_github_user_packs_nums, которых там ещё нет
        $packids = array_values(array_unique(array_merge($packids, $all_github_user_packs_nums), SORT_REGULAR));

        // 2.7. Если $packid не передан, определить его автоматически
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

        // 2.8. Если $packid передан, определить, доступен ли он
        if(!empty($packid)) {
          if(in_array($packid, $packids)) {
            throw new \Exception("Can't create D-package with id $packid, because D-package with id $packid already exists.");
          }
        }

        // 2.n. Вернуть результат
        return $packid;

      });

      // 3. Сформировать внутренний ID пакета (напр.: D1)
      $packfullname = 'D'.$packid;

      // 4. Скопировать и переименовать каталог D
      // - Из Samples в vendor/4gekkman
      // - Назвать его именем $packfullname
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('vendor')]);
      $this->storage = new \Illuminate\Filesystem\Filesystem(); // new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copyDirectory('vendor/4gekkman/M1/Samples/D', 'vendor/4gekkman/'.$packfullname);

      // 5. Заменить плейсхолдеры в файлах нового D-пакета

        // 5.0. Подготовить storage
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packfullname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 5.1. В bower.json
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM --> $packfullname
        $file = $this->storage->get('bower.json');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $this->storage->put('bower.json', $file);

        // 5.2. В composer.json
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM             --> $packfullname
        //   • PARAMpackfullname_strtolowerPARAM  --> mb_strtolower($packfullname)
        //   • PARAMdescriptionPARAM              --> $description
        //   • PARAMlayoutidPARAM                 --> $layoutid
        $file = $this->storage->get('composer.json');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $file = preg_replace("/PARAMlayoutidPARAM/ui", $layoutid, $file);
        $this->storage->put('composer.json', $file);

        // 5.3. В Controller.php
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM --> $packfullname
        //   • PARAMlayoutidPARAM     --> $layoutid
        $file = $this->storage->get('Controller.php');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMlayoutidPARAM/ui", $layoutid, $file);
        $this->storage->put('Controller.php', $file);

        // 5.4. В readme.md
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM             --> $packfullname
        //   • PARAMpackfullname_strtolowerPARAM  --> mb_strtolower($packfullname)
        //   • PARAMdescriptionPARAM              --> $description
        $file = $this->storage->get('readme.md');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $this->storage->put('readme.md', $file);

        // 5.5. В ServiceProvider.php
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM             --> $packfullname
        //   • PARAMpackfullname_strtolowerPARAM  --> mb_strtolower($packfullname)
        $file = $this->storage->get('ServiceProvider.php');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $this->storage->put('ServiceProvider.php', $file);

        // 5.6. В settings.php
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullname_strtolowerPARAM  --> mb_strtolower($packfullname)
        //   • PARAMappurlPARAM                   --> config('app.url')
        //   • PARAMnamePARAM                     --> $name
        //   • PARAMdescriptionPARAM              --> $description
        $file = $this->storage->get('settings.php');
        $file = preg_replace("/PARAMpackfullname_strtolowerPARAM/ui", mb_strtolower($packfullname), $file);
        $file = preg_replace("/PARAMappurlPARAM/ui", config('app.url'), $file);
        $file = preg_replace("/PARAMnamePARAM/ui", $name, $file);
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $this->storage->put('settings.php', $file);

        // 5.7. В view.blade.php
        // - Заменить следующие плейсхолдеры:
        //   • PARAMpackfullnamePARAM             --> $packfullname
        //   • PARAMdescriptionPARAM              --> $description
        $file = $this->storage->get('view.blade.php');
        $file = preg_replace("/PARAMpackfullnamePARAM/ui", $packfullname, $file);
        $file = preg_replace("/PARAMdescriptionPARAM/ui", $description, $file);
        $this->storage->put('view.blade.php', $file);

      // 6. Добавить пр.имён D-пакета в composer.json проекта -> autoload -> psr-4

        // 6.1. Получить содержимое composer.json проекта
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $composer = $this->storage->get('composer.json');

        // 6.2. Получить содержимое объекта "psr-4" из $composer в виде массива
        preg_match("/\"psr-4\" *: *\{.*\}/smuiU", $composer, $namespaces);
        $namespaces = preg_replace("/\"psr-4\" *: */smuiU", '', $namespaces);
        $namespaces = preg_replace("/['\n\r\s\{\}]/smuiU", '', $namespaces);
        $namespaces = explode(',', $namespaces[0]);
        $namespaces = array_values(array_filter($namespaces, function($item){
          return !empty($item);
        }));

        // 6.3. Добавить в $namespaces пространство имён нового пакета
        array_push($namespaces, '"' . $packfullname . '\\\\":"vendor/4gekkman/' . $packfullname . '"');

        // 6.4. Сформировать строку в формате значения "psr-4" из composer.json

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

        // 6.5. Заменить все \\\\ в $namespaces_result на \\\\\\
        $namespaces_result = preg_replace("/\\\\/smuiU", "\\\\\\", $namespaces_result);

        // 6.6. Вставить $namespaces_result в $composer
        $composer = preg_replace("/\"psr-4\" *: *\{.*\}/smuiU", '"psr-4": '.$namespaces_result, $composer);

        // 6.7. Заменить $composer
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('composer.json', $composer);

      // 7. Создать новый репозиторий для нового пакета
      // - Если $github == 'yes'
      if($github == 'yes') {
        $result = runcommand('\M1\Commands\C49_github_new', ["id_inner" => $packfullname]);
        if($result['status'] != 0)
          throw new \Exception($result['data']);
      }

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "packfullname" => $packfullname,
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C14_new_d from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'new_d']);
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
