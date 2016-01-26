<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Synchronize models of M-packages and their relationships with corresponding workbench models
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
class C36_workbench_sync extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1.
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------------------//
    // Провести синхронизацию моделей и связей всех M-пакетов с из базами данных //
    //---------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить массив ID всех M-пакетов
      $packages = \M1\Models\MD2_packages::whereHas('packtype', function($query){
        $query->where('name','=','M');
      })->pluck('id_inner');

      // 2. Пробежатсья по всем M-пакетам
      foreach($packages as $package) {

        // 2.1. Проверить существование базы данных пакета $package
        // - Если не существует, перейти к следующей итерации.
        if(count(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".mb_strtolower($package)."'")) == 0)
          continue;

        // 2.2. Получить список имён всех имеющихся в БД пакета $package таблиц
        // - Отфильтровать таблицы "^md100", и начинающиеся не с "^md"
        $list = DB::select('SHOW tables FROM '.mb_strtolower($package));
        $list = array_map(function($item) USE ($package){
          $item = (array)$item;
          return $item['Tables_in_'.mb_strtolower($package)];
        }, $list);
        $list = array_values(array_filter($list, function($item){
          return preg_match("/^md/ui",$item) != 0 && preg_match("/^md100/ui",$item) == 0 && preg_match("/^md[0-9]+_/ui",$item) != 0;
        }));

        // 2.3. Получить список связей типа foreign key в БД пакета $package
        // - В формате: [CONSTRAINT_NAME => TABLE_NAME]
        $fkeys_data = DB::select("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_SCHEMA='".mb_strtolower($package)."'");
        $fkeys = [];
        foreach($fkeys_data as &$fkey) {
          $fkeys[$fkey->CONSTRAINT_NAME] = $fkey->TABLE_NAME;
        }






        // Если table_name это pivot таблица
        // - Значит, это связь типа belongsToMany.
        // - Значит надо искать 2 FK, т.к. они идут всегда парами.
        //   Пример:
        //    "fk_md1005_md2_packages1": "md1003"
        //    "fk_md1005_md5_commands1": "md1003"
        // - Надо извлечь имена связанных таблиц:
        //    md2_packages1 ---> (удалить цифры в конце) md2_packages
        //    md5_commands1 ---> (удалить цифры в конце) md5_commands
        // - Надо создать 2 связи в моделях MD2_packages и MD5_commands:
        //    1) Связь packages в модели MD5_commands
        //    2) Связь commands в модели MD2_packages
        //

        // Если table_name это не pivot таблица
        // - Значит, это пара связей типа hasMany, belongsTo.
        // - Значит, нам хватит инфы из одного этого FK.
        //   Пример:
        //    "fk_md1_routes_md2_types": "md1_routes"
        // - Надо извлечь имена связанных таблиц.
        //    1) Имя первой таблицы берём из TABLE_NAME.
        //      - В нашем случае это "md1_routes".
        //    2) Имя 2-й таблицы берём из CONSTRAINT_NAME.
        //      - Для этого из него удаляем 'fk_', 'md1_routes'
        //        и все '_' из начала и конца оставшейся строки.
        //        Получаем: "md2_types"
        // - FK всегда у зависимой таблицы. Это значит, что:
        //    1) Модели MD1_routes добавляем belongsTo связь.
        //       Имя связи получаем из имени независимой
        //       таблицы, удаляя s на конце, если оно есть.
        //       Получаем: "type"
        //    2) Модели MD2_types добавляем hasMany связь.
        //       Имя связи получаем из имени зависимой таблицы.
        //       Получаем: routes.
        //

        // При создании новой модели
        // - Если у таблицы есть столбцы "created_at" и "updated_at",
        //   то включить по умолчанию их авто.поддержку.
        // - Если у таблицы есть столбец "deleted_at", включить
        //   по умолчанию поддержку мягкого удаления.



        write2log($fkeys, []);

        // 2.3.

      }





      // Для каждого M-пакета:

      // - Получить список имеющихся в БД таблиц
      // - Получить список имеющихся связей между таблицами (foreign keys)
      // - Для каждой таблицы получить список столбцов

      // - Удалить все файлы-модели пакета с помощью C25_del_m_m

      // - Создать новые файлы-модели с помощью C12_new_m_m
      // - Добавить в новые файлы-модели связи



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C36_workbench_sync from M-package M1 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C36_workbench_sync']);
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

