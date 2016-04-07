<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Setting gulpfile.js of D-packs - past between marks sources and dests to watch
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
class C47_suf_watch_setting extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить массив ID всех D-пакетов
     *  2. Получить dlw-индекс с помощью C44_suf_get_deptrees
     *  3. Обойти все D-пакеты из $packages
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------------------------------------------------//
    // Настроить для каждого D-пакета gulpfile.js, разместив между спец.маркерами пути к каталогам sources и dest //
    //------------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить массив ID всех D-пакетов
      $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->whereIn('name',['D']);
      })->pluck('id_inner');

      // 2. Получить dlw-индекс с помощью C44_suf_get_deptrees
      $index = runcommand('\M1\Commands\C44_suf_get_deptrees');
      if($index['status'] != 0) {
        Log::info('Error: '.$index['data']);
        write2log('Error: '.$index['data']);
      }
      $index = $index['data']['index_dlw'];

      // 3. Обойти все D-пакеты из $packages
      foreach($packages as $package) {

        // 3.1. Получить плоский стек зависимостей D-пакета $package
        $stack = $index[$package]['stack'];

        // 3.2. Сформировать строку с sources для вставки в gulpfile.js пакета $package
        $sources = call_user_func(function() USE ($stack, $package) {

          // 0] Подготовить строку для результата
          $result = "// sources: start" . PHP_EOL;

          // 1] Добавить пути к исходникам типа styles
          call_user_func(function() USE (&$result, $package, $stack) {

            // 1.1] Начать массив sources["styles"]
            $result = $result . '    ' . 'sources["styles"] = [';

            // 1.2] Добавить путь к public пакетов из $stack с их исходными фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/Public/css/**/*.*", ';
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/**/*.php", ';
            });

            // 1.n] Завершить массив sources["styles"]
            $result = $result . PHP_EOL . '    ' . '];' . PHP_EOL;

          });

          // 2] Добавить пути к исходникам типа javascript
          call_user_func(function() USE (&$result, $package, $stack) {

            // 2.1] Начать массив sources["javascript"]
            $result = $result . '    ' . 'sources["javascript"] = [';

            // 2.2] Добавить путь к public пакетов из $stack с их исходными фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/Public/js/**/*.*", ';
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/**/*.php", ';
            });

            // 2.n] Завершить массив sources["javascript"]
            $result = $result . PHP_EOL . '    ' . '];' . PHP_EOL;

          });

          // 3] Добавить пути к исходникам типа assets
          call_user_func(function() USE (&$result, $package, $stack) {

            // 3.1] Начать массив sources["assets"]
            $result = $result . '    ' . 'sources["assets"] = [';

            // 3.2] Добавить путь к public пакетов из $stack с их исходными фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/Public/assets/**/*.*", ';
              $result = $result . PHP_EOL . '      ' . '"../'.$inner_id.'/**/*.php", ';
            });

            // 3.n] Завершить массив sources["javascript"]
            $result = $result . PHP_EOL . '    ' . '];';

          });

          // 4] Добавить перенос строки в конце
          $result = $result . PHP_EOL;

          // 5] Финальные штрихи для $result
          $result = $result . "    // sources: end";

          // 6] Вернуть $result
          return $result;

        });

        // 3.3. Сформировать строку с dests для вставки в gulpfile.js пакета $package
        $dests = call_user_func(function() USE ($stack, $package) {

          // 0] Подготовить строку для результата
          $result = "// dests: start" . PHP_EOL;

          // 1] Добавить пути к результатам типа styles
          call_user_func(function() USE (&$result, $package, $stack) {

            // 1.1] Начать массив dests["styles"]
            $result = $result . '    ' . 'dests["styles"] = [';

            // 1.2] Добавить путь к public пакетов из $stack с их результирующими фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../../../public/'.$inner_id.'/css/**/*.*", ';
            });

            // 1.n] Завершить массив dests["styles"]
            $result = $result . PHP_EOL . '    ' . '];' . PHP_EOL;

          });

          // 2] Добавить пути к результатам типа javascript
          call_user_func(function() USE (&$result, $package, $stack) {

            // 2.1] Начать массив dests["javascript"]
            $result = $result . '    ' . 'dests["javascript"] = [';

            // 2.2] Добавить путь к public пакетов из $stack с их результирующим фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../../../public/'.$inner_id.'/js/**/*.*", ';
            });

            // 2.n] Завершить массив dests["javascript"]
            $result = $result . PHP_EOL . '    ' . '];' . PHP_EOL;

          });

          // 3] Добавить пути к результатам типа assets
          call_user_func(function() USE (&$result, $package, $stack) {

            // 3.1] Начать массив dests["assets"]
            $result = $result . '    ' . 'dests["assets"] = [';

            // 3.2] Добавить путь к public пакетов из $stack с их результирующими фронтенд-ресурсами
            collect($stack)->each(function($inner_id) USE (&$result, $package) {
              $result = $result . PHP_EOL . '      ' . '"../../../public/'.$inner_id.'/assets/**/*.*", ';
            });

            // 3.n] Завершить массив dests["javascript"]
            $result = $result . PHP_EOL . '    ' . '];';

          });

          // 4] Добавить перенос строки в конце
          $result = $result . PHP_EOL;

          // 5] Финальные штрихи для $result
          $result = $result . "    // dests: end";

          // 6] Вернуть $result
          return $result;

        });

        // 3.4. Вставить $sources и $dests в gulpfile.js D-пакета $package

          // 1] Проверить существование файла gulpfile.js в $package
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$package)]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          if(!$this->storage->exists('gulpfile.js'))
            throw new \Exception('В пакете '.$package.' не найден файл gulpfile.js');

          // 2] Получить содержимое gulpfile.js
          $file = $this->storage->get('gulpfile.js');

          // 3] Вставить $sources в $file
          $file = preg_replace("#// sources: start.*// sources: end#smuiU", $sources, $file);

          // 4] Вставить $dests в $file
          $file = preg_replace("#// dests: start.*// dests: end#smuiU", $dests, $file);

          // 5] Заменить $file
          $this->storage->put('gulpfile.js', $file);

      }



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C47_suf_watch_setting from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C47_suf_watch_setting']);
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

