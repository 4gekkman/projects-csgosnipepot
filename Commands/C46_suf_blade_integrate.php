<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Integrate css/js paths from suf_get_deptrees to blade-docs of D-packs between corresponding marks
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
class C46_suf_blade_integrate extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить индекс со всеми css/js зависимостями для всех D-пакетов
     *  2. Получить массив ID всех D-пакетов
     *  3. Обойти все D-пакеты из $packages
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------------------//
    // Интегрировать css/js пути из suf_get_deptrees в blade-документы D-пакетов между соотв.метками //
    //-----------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить индекс со всеми css/js зависимостями для всех D-пакетов
      $index = runcommand('\M1\Commands\C44_suf_get_deptrees');
      if($index['status'] != 0) {
        Log::info('Error: '.$index['data']);
        write2log('Error: '.$index['data']);
      }
      $index = $index['data']['index_final'];

      // 2. Получить массив ID всех D-пакетов
      $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->whereIn('name',['D']);
      })->pluck('id_inner');

      // 3. Обойти все D-пакеты из $packages
      foreach($packages as $package) {

        // 3.1. Сформировать строку для вставки CSS для $package
        $css = call_user_func(function() USE ($index, $package) {

          // 1] Подготовить строку для результата
          $result = "<!-- document css: start -->" . PHP_EOL;

          // 2] Добавить пути к CSS-файлам
          collect($index[$package]['css'])->each(function($path) USE (&$result, $package) {

            // 1] Добавить пробелы
            $result = $result . '  ';

            // 2] Добавить путь
            $result = $result . '<link rel="stylesheet" type="text/css" href="' . config('app.url'). '/' . $path . '">';

            // 3] Добавить перенос строки
            $result = $result . PHP_EOL;

          });

          // 3] Финальные штрихи для $result
          $result = $result . "  <!-- document css: stop -->";

          // 4] Вернуть $result
          return $result;

        });

        // 3.2. Сформировать строку для вставки JS для $package
        $js = call_user_func(function() USE ($index, $package) {

          // 1] Подготовить строку для результата
          $result = "<!-- document js: start -->" . PHP_EOL;

          // 2] Добавить пути к CSS-файлам
          collect($index[$package]['js'])->each(function($path) USE (&$result, $package) {

            // 1] Добавить пробелы
            $result = $result . '  ';

            // 2] Добавить путь
            $result = $result . '<script src="' . config('app.url'). '/' . $path . '"></script>';

            // 3] Добавить перенос строки
            $result = $result . PHP_EOL;

          });

          // 3] Финальные штрихи для $result
          $result = $result . "  <!-- document js: stop -->";

          // 4] Вернуть $result
          return $result;

        });

        // 3.3. Вставить $css и $js в blade-документ D-пакета $package

          // 1] Проверить существование файла view.blade.php в $package
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$package)]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          if(!$this->storage->exists('view.blade.php'))
            throw new \Exception('В пакете '.$package.' не найден файл view.blade.php');

          // 2] Получить содержимое view.blade.php
          $file = $this->storage->get('view.blade.php');

          // 3] Вставить $css в $file
          $file = preg_replace("#<!-- document css: start -->.*<!-- document css: stop -->#smuiU", $css, $file);

          // 4] Вставить $js в $file
          $file = preg_replace("#<!-- document js: start -->.*<!-- document js: stop -->#smuiU", $js, $file);

          // 5] Заменить $file
          $this->storage->put('view.blade.php', $file);

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C46_suf_blade_integrate from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C46_suf_blade_integrate']);
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

