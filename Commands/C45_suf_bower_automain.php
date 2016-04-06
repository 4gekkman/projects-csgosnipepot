<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Auto fill mains.json of bower packs by data from main from bower.json of pack, if mains of pack is totally empty
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
class C45_suf_bower_automain extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить свежие сведения
     *  2. Создать в R5/data4bower из __sample__ папочки для bower-пакетов $diff
     *  3. Обойти все все навигационные папочки из $r5data4bowerpacks для bower-пакетов
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------------------------------------------------------//
    // Авто-ки заполнить mains.json bower-пакетов данными из main из bower.json соотв.пакетов, если mains пакета полностью пуст //
    //--------------------------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить свежие сведения
      // - Список имён всех установленных bower-пакетов приложения
      // - Список имён bower-пакетов, для которых есть навигац-ые данные в R5
      // - Список имён bower-пакетов, для которых нет навигац-ых данных в R5
      $info = runcommand('\M1\Commands\C41_suf_check_deps');
      if($info['status'] != 0) {
        Log::info('Error: '.$info['data']);
        write2log('Error: '.$info['data']);
      }
      $bowerpacks         = $info['data']['bowerpacks'];
      $r5data4bowerpacks  = $info['data']['r5data4bowerpacks'];
      $diff               = $info['data']['diff'];

      // 2. Создать в R5/data4bower из __sample__ папочки для bower-пакетов $diff
      foreach($diff as $bowerpack) {

        // 2.1. Скопировать и переименовать каталог M
        // - Из vendor/4gekkman/R5/data4bower/__sample__ в vendor/4gekkman/R5/data4bower/$bowerpack
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor')]);
        $this->storage = new \Illuminate\Filesystem\Filesystem(); // new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->copyDirectory('vendor/4gekkman/R5/data4bower/__sample__', 'vendor/4gekkman/R5/data4bower/'.$bowerpack);

        // 2.2. Дождаться, пока mains.json в новом каталоге будет создан

          // 1] Подготовить объект-storage
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/R5/data4bower')]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

          // 2] В этом цикле будут проверяться условия, и если не выполнены, то sleep(1)
          // - Условия такие:
          //   1) Существует ли папочка $bowerpack в data4bower
          //   2) Существует ли файл mains.json в папочке $bowerpack
          //   3) Существует ли в файле mains.json поле ["mains"]
          //   4) Существует ли в файле mains.json поля ["mains"]["css"] и ["mains"]["js"]
          $condition = 0;
          while(!$condition) {

            if(
                $this->storage->exists($bowerpack) &&
                $this->storage->exists($bowerpack.'/mains.json') &&
                array_key_exists('mains', json_decode($this->storage->get($bowerpack.'/mains.json'), true)) &&
                array_key_exists('css', json_decode($this->storage->get($bowerpack.'/mains.json'), true)['mains']) &&
                array_key_exists('js', json_decode($this->storage->get($bowerpack.'/mains.json'), true)['mains'])
            ) $condition = 1;

          }

        // 2.3. Добавить $bowerpack в $r5data4bowerpacks
        array_push($r5data4bowerpacks, $bowerpack);

        // 2.4. Сообщить о том, что для $bowerpack создан каталог в R5/data4bower
        $msg = "У пакета ".$bowerpack." отсутствовал навигационный каталог в R5/data4bower, поэтому он был создан автоматически командой suf_bower_automain";
        write2log($msg, []);
        Log::info($msg);

      }

      // 3. Обойти все все навигационные папочки из $r5data4bowerpacks для bower-пакетов
      collect($r5data4bowerpacks)->each(function($packname) {

        // 3.1. Получить содержимое mains.json пакета $packname в виде массива
        $mains = call_user_func(function() USE ($packname) {

          // 1] Проверить существование файла mains.json в для bower-пакета $package
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/R5/data4bower/'.$packname)]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          if(!$this->storage->exists('mains.json'))
            throw new \Exception('Для bower-пакета '.$packname.' не найден файл mains.json в R5');

          // 2] Получить содержимое mains.json в формате php-массива
          $file = json_decode($this->storage->get('mains.json'), true);

          // 3] Если в массиве $file нет ключей css или js, возбудить исключение
          if(!array_key_exists('css', $file['mains']) || !array_key_exists('js', $file['mains']))
            throw new \Exception('В файле mains.json пакета '.$packname.' в R5 нет необходимых ключей "css" или "js"');

          // 4] Вернуть результат
          return $file;

        });

        // 3.2. Если массивы $mains['mains']['css'] и $mains['mains']['js'] не пусты, перейти к след.итерации
        if(count($mains['mains']['css']) !== 0 || count($mains['mains']['js']) !== 0) return;

        // 3.3. Получить содержимое main bower-пакета $packname в виде массива
        // - Если, конечно, bower.json пакета и поле main в нём присутствуют
        $mains_from_bowerjson = call_user_func(function() USE ($packname) {

          // 1] Проверить существование файла bower.json в bower-пакете $package
          // - Если он не существует, вернуть пустой массив
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('public/public/bower/'.$packname)]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          if(!$this->storage->exists('bower.json')) return [];

          // 2] Получить содержимое bower.json в формате php-массива
          $file = json_decode($this->storage->get('bower.json'), true);

          // 3] Если в $file отсутствует поле main, вернуть пустой массив

            // 3.1] Проверить
            if(!array_key_exists('main', $file)) return [];

            // 3.2] Сообщить о том, что для $packname идёт автовставка mains
            $msg = "У bower-пакета ".$packname." в R5 в mains.json пусты массивы с css/js, команда C45_suf_bower_automain проводит автовставку данных из bower.json пакета...";
            write2log($msg, []);
            \Log::info($msg, []);

          // 4] Извлечь из $file все пути, заканчивающиеся на .css
          $main_css = call_user_func(function() USE ($file, $packname) {

            // 4.1] Подготовить массив для результатов
            $results = [];

            // 4.2] Если $file['main'] это массив
            if(is_array($file['main'])) {

              // Пробежаться по $file['main']
              foreach($file['main'] as $path) {
                if(preg_match("/^.*\.css$/ui", $path) !== 0 && !in_array($path, $results))
                  array_push($results, "public/bower/" . $packname . "/" . $path);
              }

            }

            // 4.3] Если $file['main'] это строка
            if(is_string($file['main'])) {

              // Пробежаться по $file['main']
              if(preg_match("/^.*\.css$/ui", $file['main']) !== 0 && !in_array($file['main'], $results))
                array_push($results, "public/bower/" . $packname . "/" . $file['main']);

            }

            // 4.n] Вернуть результаты
            return $results;

          });

          // 5] Извлечь из $file все пути, заканчивающиеся на .js
          $main_js = call_user_func(function() USE ($file, $packname) {

            // 5.1] Подготовить массив для результатов
            $results = [];

            // 5.2] Если $file['main'] это массив
            if(is_array($file['main'])) {

              // Пробежаться по $file['main']
              foreach($file['main'] as $path) {
                if(preg_match("/^.*\.js$/ui", $path) !== 0 && !in_array($path, $results))
                  array_push($results, "public/bower/" . $packname . "/" . $path);
              }

            }

            // 5.3] Если $file['main'] это строка
            if(is_string($file['main'])) {

              // Пробежаться по $file['main']
              if(preg_match("/^.*\.js$/ui", $file['main']) !== 0 && !in_array($file['main'], $results))
                array_push($results, "public/bower/" . $packname . "/" . $file['main']);

            }

            // 5.n] Вернуть результаты
            return $results;

          });

          // 6] Вернуть результаты
          return [
            "css" => $main_css,
            "js"  => $main_js
          ];

        });

        // 3.4. Если в $mains_from_bowerjson пусто
        if(count($mains_from_bowerjson['css']) == 0 && count($mains_from_bowerjson['js']) == 0) {
          $msg = "У bower-пакета ".$packname." в R5 в mains.json пусты массивы с css/js, команда C45_suf_bower_automain проводит автовставку данных из bower.json пакета... но данные не обнаружены, либо bower.json отсутствует у пакета, либо в нём нет поля main, либо в поле main нет путей к css/js файлам.";
          write2log($msg, []);
          \Log::info($msg, []);
        }

        // 3.5. Записать в $mains данные из $mains_from_bowerjson
        $mains['mains']['css'] = $mains_from_bowerjson['css'];
        $mains['mains']['js'] = $mains_from_bowerjson['js'];

        // 3.6. Заменить $mains для $packname
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/R5/data4bower/'.$packname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('mains.json', json_encode($mains, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

      });


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C45_suf_bower_automain from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C45_suf_bower_automain']);
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

