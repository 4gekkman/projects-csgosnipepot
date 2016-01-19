<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Delete console command from M-package
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
class C22_del_m_t extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Переменная такая-то
    // protected $data;

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
     *  2. Проверить существование M-пакета $packid
     *  3. Проверить существование к.команды $command2del в пакете $mpack
     *  4. Удалить файл команды
     *  5. Удалить запись об удалённой к.команде из сервис-провайдера пакета $packid
     *  6. Вернуть результаты
     *
     */

    //---------------------------------------------------//
    // Удалить консольную команду из указанного M-пакета //
    //---------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $packid = $this->data['packid'];
      $command2del = $this->data['command2del'];

      // 2. Проверить существование M-пакета $packid
      $pack = \M1\Models\MD2_packages::where('id_inner','=',$packid)->first();
      if(empty($pack))
        throw new \Exception("Package $packid does not exist.");

      // 3. Проверить существование к.команды $command2del в пакете $pack
      $command = \M1\Models\MD6_console::whereHas('package', function($query) USE ($packid) {
        $query->where('id_inner','=',$packid);
      })->where('id_inner','=',$command2del)->first();
      if(empty($command))
        throw new \Exception("Console command ".$command2del." in package $packid does not exist.");

      // 4. Удалить файл команды
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path()]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->delete('vendor/4gekkman/'.$packid.'/Console/'.$command->name.'.php');

      // 5. Удалить запись об удалённой к.команде из сервис-провайдера пакета $packid

        // 5.1. Получить содержимое сервис-провайдера M-пакета $packid
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $sp = $this->storage->get('ServiceProvider.php');

        // 5.2. Получить содержимое массива $commands из $sp в виде массива
        preg_match("/commands *= *\[.*\]/smuiU", $sp, $commands);
        $commands = preg_replace("/commands *= */smuiU", '', $commands);
        $commands = preg_replace("/['\n\r\s\[\]]/smuiU", '', $commands);
        $commands = explode(',', $commands[0]);
        $commands = array_values(array_filter($commands, function($item){
          return !empty($item);
        }));

        // 5.3. Удалить из $commands запись, содержащую $command->name
        $commands = array_values(array_filter($commands, function($item) USE ($command) {
          return !preg_match("/".$command->name."/ui", $item);
        }));

        // 5.4. Сформировать строку в формате массива из $commands

          // 1] Подготовить строку для результата
          $commands_result = "[" . PHP_EOL;

          // 2] Вставить в $commands_result все значения из $commands
          for($i=0; $i<count($commands); $i++) {
            if($i != count($commands)-1 )
              $commands_result = $commands_result . "          '" . $commands[$i] . "'," . PHP_EOL;
            else
              $commands_result = $commands_result . "          '" . $commands[$i] . "'" . PHP_EOL;
          }

          // 3] Завершить квадратной скобкой c запятой
          $commands_result = $commands_result . "        ]";

        // 5.5. Вставить $commands_result в $sp
        $sp = preg_replace("/commands *= *\[.*\]/smuiU", 'commands = '.$commands_result, $sp);

        // 5.6. Заменить $sp
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packid)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put('ServiceProvider.php', $sp);

      // 6. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "ccomfullname" => $command->name,
          "package"      => $pack->id_inner
        ]
      ];

    } catch(\Exception $e) {
        $errortext = "Deleting of console command from M-package have ended with error: ".$e->getMessage();
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;

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

