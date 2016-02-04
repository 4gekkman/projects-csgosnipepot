<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New model for existing M-package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        mpackid       // ID M-пакета, для которого создаётся модель
 *        name          // Имя (eng) для новой модели
 *        modelid       // ID для новой модели
 *        timestamps    // Вкл/Выкл поддержку created_at/updated_at
 *        softdeletes   // Вкл/Выкл поддержку deleted_at
 *        issync        // Специальный режим для команды C36_workbench_sync (при синхронизации для M1)
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
class C12_new_m_m extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  4. Определить $modelid
     *  5. Сформировать название команды (напр.: C1_name)
     *  6. Скопировать и переименовать файл _C1_sample.php
     *  7. Заменить плейсхолдеры в файле значениями параметров
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------//
    // Создать новую модель для указанного M-пакета //
    //----------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $mpackid      = $this->data['mpackid'];
      $name         = $this->data['name'];
      $modelid      = $this->data['modelid'];
      $timestamps   = $this->data['timestamps'];
      $softdeletes  = $this->data['softdeletes'];
      $issync       = $this->data['issync'];

      // 2. Провести валидацию входящих параметров

        // 1] $mpackid
        if(!preg_match("/^[M]{1}[0-9]$/ui", $mpackid))
          throw new \Exception("$mpackid is not valid (must match \"/^[M]{1}[0-9]*$/ui\")");

        // 2] $name
        if(!preg_match("/^[a-z_0-9]+$/ui", $name))
          throw new \Exception("$name is not valid (must match \"/^[a-z_0-9]+$/ui\")");

        // 3] $modelid
        if(!preg_match("/^[0-9]+$/ui", $modelid))
          throw new \Exception("$modelid is not valid (must match \"/^[0-9]+$/ui\")");

        // 4] $timestamps
        if(!preg_match("/^true|false$/ui", $timestamps))
          throw new \Exception("$timestamps is not valid (must match \"/^true|false/ui\")");

        // 5] $softdeletes
        if(!preg_match("/^true|false$/ui", $softdeletes))
          throw new \Exception("$softdeletes is not valid (must match \"/^true|false/ui\")");

        // 6] $issync

          // Провести валидацию
          if(!preg_match("/^([01]{1}|)$/ui", $issync))
            throw new \Exception("$issync is not valid (must match \"/^([01]{1}|)$/ui\")");

          // Если $issync == 1, а $modelid пуст, завершить
          if($issync == 1 && empty($modelid))
            throw new \Exception('В режими синхронизации моделей для пакета M1, modelid является обязательным параметром.');

      // 3. Проверить существование M-пакета $mpackid
      if(!$issync) {
        $mpack = \M1\Models\MD2_packages::where('id_inner','=',$mpackid)->first();
        if(empty($mpack))
          throw new \Exception("Package $mpackid does not exist.");
      }

      // 4. Определить $modelid
      if(!$issync) {

        // 4.1. Получить список ID (номеров) всех моделей M-пакета $mpackid
        $modelids = array_map(function($item){
          return mb_substr($item, 2);
        }, \M1\Models\MD3_models::whereHas('package',function($query) USE ($mpackid) {
            $query->where('id_inner','=',$mpackid);
        })->pluck('id_inner')->toArray());

        // 4.2. Если $modelid не передан, определить его автоматически
        if(empty($modelid)) {
          $modelid = call_user_func(function() USE ($modelids) {
            if(!is_array($modelids) || empty($modelids)) {
              return 1;
            }
            else {
              return +max($modelids) + 1;
            }
          });
        }

        // 4.3. Если $modelid передан, определить, доступен ли он
        if(!empty($modelid)) {
          if(in_array($modelid, $modelids)) {
            throw new \Exception("Can't create model with id $modelid for M-package $mpackid, because command with id $modelid already exists.");
          }
        }

      }

      // 5. Сформировать название модели (напр.: MD1_name)
      $modelfullname = 'MD'.$modelid.'_'.$name;

      // 6. Скопировать и переименовать файл _MD1_sample.php
      // - Из M1/Samples в Models пакета $mpackid
      // - Назвать его именем $modelfullname.'.php'
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path()]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->copy('vendor/4gekkman/M1/Samples/M/Models/_MD1_sample.php', 'vendor/4gekkman/'.$mpackid.'/Models/'.$modelfullname.'.php');

      // 7. Заменить плейсхолдеры в файле значениями параметров

        // 1] Создать новый экземпляр локального хранилища
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$mpackid.'/Models')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 2] Извлечь содержимое файла
        $file = $this->storage->get($modelfullname.'.php');

        // 3] Найти и заменить плейсхолдеры
        $file = preg_replace("/PARAMmpackidPARAM/ui", $mpackid, $file);
        $file = preg_replace("/PARAMmpackid_strtolowerPARAM/ui", mb_strtolower($mpackid), $file);
        $file = preg_replace("/PARAMmodelfullnamePARAM/ui", $modelfullname, $file);
        $file = preg_replace("/PARAMmodelfullname_strtolowerPARAM/ui", mb_strtolower($modelfullname), $file);
        $file = preg_replace("/PARAMtimestampsPARAM/ui", $timestamps, $file);
        $file = preg_replace("/PARAMsoftdeletesPARAM/ui", $softdeletes == 'true' ? PHP_EOL . '    use SoftDeletes; protected $dates = ["deleted_at"];' : '', $file);

        // 4] Перезаписать файл
        $this->storage->put($modelfullname.'.php', $file);

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "modelfullname" => $modelfullname,
          "package"       => $mpackid
        ]
      ];


    } catch(\Exception $e) {
        $errortext = "Creating of model for M-package have ended with error: ".$e->getMessage();
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

