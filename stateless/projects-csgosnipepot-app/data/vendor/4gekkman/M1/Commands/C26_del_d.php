<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Delete D-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        packid      // ID M-пакета для удаления
 *        delconf     // yes/no удалять ли конфиг M-пакета
 *        deldb       // yes/no удалять ли БД M-пакета
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
class C26_del_d extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Проверить существование D-пакета $packid
     *  3. Отменить автосохранение пакета $packid на github
     *  4. Удалить каталог D-пакета $packid
     *  5. Удалить пр.имён D-пакета из composer.json проекта -> autoload -> psr-4
     *  6. Удалить файл-конфиг D-пакета (если требуется)
     *  7. Удалить запись о пакете из providers в config/app.php
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------//
    // Удалить указанный D-пакет //
    //---------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $packid   = $this->data['packid'];
      $delconf  = $this->data['delconf'];
      $delremote  = $this->data['delremote'];

      // 2. Проверить существование D-пакета $packid
      $pack = \M1\Models\MD2_packages::where('id_inner','=',$packid)->first();
      if(empty($pack))
        throw new \Exception("Package $packid does not exist.");

      // 3. Отменить автосохранение пакета $packid на github
      $result = runcommand('\M1\Commands\C53_github_del', [
        "id_inner"  => $packid,
        "delremote" => $delremote
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']);

      // 4. Удалить каталог D-пакета $packid
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path()]);
      $this->storage = new \Illuminate\Filesystem\Filesystem(); // new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->deleteDirectory('vendor/4gekkman/'.$packid);

      // 5. Удалить пр.имён D-пакета из composer.json проекта -> autoload -> psr-4

        // 5.1. Получить содержимое composer.json проекта
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $composer = $this->storage->get('composer.json');

        // 5.2. Получить содержимое объекта "psr-4" из $composer в виде массива
        preg_match("/\"psr-4\" *: *\{.*\}/smuiU", $composer, $namespaces);
        $namespaces = preg_replace("/\"psr-4\" *: */smuiU", '', $namespaces);
        $namespaces = preg_replace("/['\n\r\s\{\}]/smuiU", '', $namespaces);
        $namespaces = explode(',', $namespaces[0]);
        $namespaces = array_values(array_filter($namespaces, function($item){
          return !empty($item);
        }));

        // 5.3. Удалить из $namespaces запись, содержащую 'vendor/4gekkman/'.$packid
        $namespaces = array_values(array_filter($namespaces, function($item) USE ($packid) {
          return !preg_match("#vendor/4gekkman/".$packid."#ui", $item);
        }));

        // 5.4. Сформировать строку в формате значения "psr-4" из composer.json

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

        // 5.5. Заменить все \\\\ в $namespaces_result на \\\\\\
        $namespaces_result = preg_replace("/\\\\/smuiU", "\\\\\\", $namespaces_result);

        // 5.6. Вставить $namespaces_result в $composer
        $composer = preg_replace("/\"psr-4\" *: *\{.*\}/smuiU", '"psr-4": '.$namespaces_result, $composer);

        // 5.7. Заменить $composer
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('composer.json', $composer);

      // 6. Удалить файл-конфиг D-пакета (если требуется)
      if($delconf == "yes") {

        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('config')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->delete($packid.'.php');

      }

      // 7. Удалить запись о пакете из providers в config/app.php

        // 7.1. Получить содержимое конфига app.php
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('config')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $config = $this->storage->get('app.php');

        // 7.2. Получить текущий список провайдеров из конфига app.php
        // - И отфильтровать 1 так, чтобы удалить регистрацию всех провайдеров не моих пакетов.
        $approviders = config('app.providers');

        // 7.3. Удалить из $approviders запись, содержащую $packid.'\ServiceProvider::class'
        $approviders = array_values(array_filter($approviders, function($item) USE ($packid) {
          return !preg_match("/^".$packid."\\\\ServiceProvider$/ui", $item);
        }));

        // 7.4. С помощью regex вставить $approviders в providers конфига $config

          // 1] Сформировать строку в формате массива из $approviders
          $providers_str = call_user_func(function() USE ($approviders) {

            // 1.1] Подготовить строку для результата
            $result = "[" . PHP_EOL;

            // 1.2] Вставить в $result все значения из $approviders
            for($i=0; $i<count($approviders); $i++) {
              if($i != count($approviders)-1 )
                $result = $result . "        " . $approviders[$i] . "::class," . PHP_EOL;
              else
                $result = $result . "        " . $approviders[$i] . "::class" . PHP_EOL;
            }

            // 1.3] Завершить квадратной скобкой c запятой
            $result = $result . "    ],";

            // 1.4] Вернуть результат
            return $result;

          });

          // 2] Вставить $providers_str в $config
          $config = preg_replace("#'providers' *=> *\[.*\],#smuiU", "'providers' => ".$providers_str, $config);

          // 3] Заменить config
          $this->storage->put('app.php', $config);

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "packfullname"  => $packid,
          "delconf"       => $delconf
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C26_del_d from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'del_d']);
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

