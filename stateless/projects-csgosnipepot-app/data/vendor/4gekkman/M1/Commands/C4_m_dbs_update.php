<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Install / Update databases of M-packages
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
 *      - Текст ошибки.
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример для админ.документов:  M1\Documents\Main\Commands

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
class C4_m_dbs_update extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить массив ID всех установленных M-пакетов
     *  2. Обновить базу каждого M-пакета из $packids
     *
     *  N. Вернуть статус 0
     *
     */


    //------------------------------------------------//
    // Устанавливить / обновить базы данных M-пакетов //
    //------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить массив ID всех установленных M-пакетов
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name','=','M');
      })->pluck('id_inner');

      // 2. Обновить базу каждого M-пакета из $packids
      foreach($mpackages as $mpackage) {

        // 1] Если файл с настройками пакета не опубликован, завершить с ошибкой
        if(!file_exists(base_path('config/'.$mpackage.'.php')))
          throw new \Exception('Package '.$mpackage.' has not published settings file.');

        // 2] Получить значение опции databaseupdates из файла настроек пакета
        $settings = $this->storage->get('config/'.$mpackage.'.php');
        $settings = eval("?> $settings");
        $databaseupdates = $settings['updateshistory'];

        // 3] Получить массив имён SQL-обновлений для БД пакета
        // - Это файлы "^[0-9].sql$", которые лежат в Database M-пакета.
        // - Нужно получить массив цифр-имён этих файлов.
        $sqls = array_map(function($item){

          return preg_replace("/\\.sql$/ui", "", preg_replace("/^.*\\//ui", "", $item));

        }, array_values(array_filter($this->storage->files('vendor/4gekkman/'.$mpackage.'/Database'), function($item){

          // Извлечь имя файла из пути (включая .sql на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $item);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^[0-9]*.sql$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        })));

        // 4] Определить номера SQL-обновлений, которые надо установить
        // - Отсортировать их по значению в возрастающем порядке.
        $updates2install = array_values(array_diff($sqls, $databaseupdates));
        usort($updates2install, function($a, $b){
          return gmp_cmp($a, $b);
        });
        array_values($updates2install);

        // 5] Выполнить по очереди все SQL из $updates2install
        foreach($updates2install as $update2install) {
          DB::select( DB::raw($this->storage->get('vendor/4gekkman/'.$mpackage.'/Database/'.$update2install.'.sql')) );
        }

        // 6] Дополнить $databaseupdates номерами из $updates2install
        foreach($updates2install as $update2install) {
          array_push($databaseupdates, $update2install);
        }

        // 7] С помощью regex вставить $databaseupdates в updateshistory конфига пакета

          // 7.1] Получить содержимое конфига пакета $mpackage
          $config = $this->storage->get('config/'.$mpackage.'.php');

          // 7.2] Сформировать строку в формате массива из $databaseupdates
          $databaseupdates_str = call_user_func(function() USE ($databaseupdates) {

            // 7.2.1] Подготовить строку для результата
            $result = "[";

            // 7.2.2] Вставить в $result все значения из $databaseupdates
            for($i=0; $i<count($databaseupdates); $i++) {
              if($i != count($databaseupdates)-1 )
                $result = $result . "'" . $databaseupdates[$i] . "',";
              else
                $result = $result . "'" . $databaseupdates[$i] . "'";
            }

            // 7.2.3] Завершить квадратной скобкой c запятой
            $result = $result . "],";

            // 7.2.4] Вернуть результат
            return $result;

          });

          // 7.3] Вставить $databaseupdates_str в $config
          $config = preg_replace("#'updateshistory' *=> *\[.*\],#smuiU", "'updateshistory' => ".$databaseupdates_str, $config);

          // 7.4] Заменить config
          $this->storage->put('config/'.$mpackage.'.php', $config);

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Updating of databases of M-packages have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'm_dbs_update']);
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

