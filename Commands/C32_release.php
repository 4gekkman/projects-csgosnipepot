<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create new release of specified package
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        packid      // ID пакета, для которого надо создать новый релиз
 *        type        // patch / minor / major - тип нового релиза
 *        version     // Значение для версии нового релиза
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
class C32_release extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *
     *
     *
     */

    //---------------------------------------//
    // Создать новый релиз указанного пакета //
    //---------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить входящие параметры
      $packid     = $this->data['packid'];
      $type       = $this->data['type'];
      $version    = $this->data['version'];

      // 2. Проверить правильность формата версии
      if($version != 0) {
        if(!preg_match("/^[0-9]+\.[0-9]+\.[0-9]+$/ui", $version))
          throw new \Exception("Версия '$version' is not valid (must match \"/^[0-9]+\.[0-9]+\.[0-9]+\.$/ui\")");
      }

      // 3. Проверить существование пакета $packid
      $pack = \M1\Models\MD2_packages::where('id_inner','=',$packid)->first();
      if(empty($pack))
        throw new \Exception("Package $pack does not exist.");

      // 4. Сформировать новое значение версии, в зависимости от $type

        // 4.1. Подготовить переменную для нового значения
        if($version == 0) $newversion = $pack->lastversion;
        else              $newversion = $version;

        // 4.2. Если $type == 'patch'
        if($type == 'patch') {

          // 1] Получить все части версии в виде чисел
          $part1 = preg_replace("/\.[0-9]+\.[0-9]+$/ui",'',$newversion);
          $part2 = preg_replace("/\.[0-9]+$/ui",'',preg_replace("/^[0-9]+\./ui",'',$newversion));
          $part3 = preg_replace("/^[0-9]+\.[0-9]+\./ui",'',$newversion);

          // 2] Прибавить +1 к $part3
          $part3 = +$part3 + 1;

          // 3] Сформировать $newversion
          $newversion = $part1 . '.' . $part2 . '.' . $part3;

        }

        // 4.3. Если $type == 'minor'
        if($type == 'minor') {

          // 1] Получить все части версии в виде чисел
          $part1 = preg_replace("/\.[0-9]+\.[0-9]+$/ui",'',$newversion);
          $part2 = preg_replace("/\.[0-9]+$/ui",'',preg_replace("/^[0-9]+\./ui",'',$newversion));
          $part3 = preg_replace("/^[0-9]+\.[0-9]+\./ui",'',$newversion);

          // 2] Прибавить +1 к $part2
          $part2 = +$part2 + 1;

          // 3] Сформировать $newversion
          $newversion = $part1 . '.' . $part2 . '.' . $part3;

        }

        // 4.4. Если $type == 'major'
        if($type == 'major') {

          // 1] Получить все части версии в виде чисел
          $part1 = preg_replace("/\.[0-9]+\.[0-9]+$/ui",'',$newversion);
          $part2 = preg_replace("/\.[0-9]+$/ui",'',preg_replace("/^[0-9]+\./ui",'',$newversion));
          $part3 = preg_replace("/^[0-9]+\.[0-9]+\./ui",'',$newversion);

          // 2] Прибавить +1 к $part1
          $part1 = +$part1 + 1;

          // 3] Сформировать $newversion
          $newversion = $part1 . '.' . $part2 . '.' . $part3;

        }

      // 5. Извлечь из конфига M1 аутентиф.токен для github
      $oauth2 = config('M1.github_oauth2');

      // 6. Добавить в extra -> version в composer.json новую версию

//        // 6.1. Получить содержимое composer.json пакета $pack
//        config(['filesystems.default' => 'local']);
//        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$pack->id_inner)]);
//        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
//        $composer = $this->storage->get('composer.json');
//
//        // 6.2. Вставить $newversion в $composer
//        $composer = preg_replace("/\"version\" *: *\".*\"/smuiU", '"version": "'.$newversion.'"', $composer);
//
//        // 6.3. Заменить $composer
//        $this->storage->put('composer.json', $composer);

      // 7.
      $x = exec("curl https://api.github.com/?access_token=$oauth2", $output);

    Log::info($output);



    } catch(\Exception $e) {
        $errortext = "Creating of command for M-package have ended with error: ".$e->getMessage();
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

