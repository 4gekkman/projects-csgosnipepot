<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Add note about the new MDLWR-pack in GitAutoPushScripts
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        project     | Имя папки проекта
 *        id_inner    | ID пакета
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
class C50_github_new_autopush extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять входящие данные и провести валидацию
     *  2. Получить массив ID всех MDLWR-пакетов
     *  3. Добавить в него $this->data['id_inner']
     *  4. Сформировать JSON-строку в определённом формате
     *  5. Сформировать строку $laravelpacks
     *  6. Записать $laravelpacks в autosave2github.ps1
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------//
    // Добавить запись об указанном MDLWR-пакете в GitAutoPushScripts //
    //----------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Принять входящие данные и провести валидацию
      $validator = r4_validate($this->data, [
        "project"   => ["required", "string"],
        "id_inner"  => ["required", "regex:/^[MDLWR]{1}[1-9]+[0-9]*$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Получить массив ID всех MDLWR-пакетов
      $ids = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->whereIn('name',['M','D','L','W','R']);
      })->pluck('id_inner')->toArray();

      // 3. Добавить в него $this->data['id_inner']
      array_push($ids, $this->data['id_inner']);

      // 4. Сформировать JSON-строку в определённом формате
      // - В таком:
      //
      //    '[
      //      {"project": "devapp", "packid": "M1"},
      //      {"project": "devapp", "packid": "M2"}
      //    ]'
      $json = call_user_func(function() USE ($ids) {

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Наполнить $results
        foreach($ids as $id) {
          array_push($results, [
            "project" => $this->data['project'],
            "packid"  => $id
          ]);
        }

        // m] Преобразовать $results в json-строку
        $json = json_encode($results, JSON_PRETTY_PRINT);

        // n] Вернуть результаты
        return $json;

      });

      // 5. Сформировать строку $laravelpacks
      $laravelpacks = call_user_func(function() USE($json) {

        // 1] Подготовить строку для результата
        $result = '$laravelpacks = ' . "'";

        // 2] Добавить $json в $result
        $result = $result . $json;

        // 3] В конец добавить закрывающую кавычку
        $result = $result . "'";

        // n] Вернуть $result
        return $result;

      });

      // 6. Записать $laravelpacks в autosave2github.ps1
      call_user_func(function() USE ($laravelpacks) {

        // 1] Получить путь к скрипту
        $path = preg_replace("/\/[^\/]*$/ui", "", base_path()) . '/' . 'autosave2github.ps1';

        // 2] Подготовить storage
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => "/"]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

        // 3] Получить содержимое файла $path
        $file = $this->storage->get($path);

        // 4] Вставить $laravelpacks в $file
        $file = preg_replace('/\$laravelpacks = \'.*\'/smui', $laravelpacks, $file);

        // 5] Заменить $file
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => "/"]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put($path, $file);

      });


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C50_github_new_autopush from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C50_github_new_autopush']);
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

