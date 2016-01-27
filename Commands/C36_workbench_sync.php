<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Synchronize models of M-package and their relationships with corresponding workbench model
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        packid        // ID пакета, для которого требуется провести синхронизацию
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
     *
     */

    //---------------------------------------------------------------------------//
    // Провести синхронизацию моделей и связей всех M-пакетов с из базами данных //
    //---------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить M-пакет, для которого требуется синхронизация
      $pack = \M1\Models\MD2_packages::where('id_inner', $this->data['data']['packid'])->first();
      if(empty($pack))
        throw new \Exception('M-пакет с id равным '.$pack->id_inner.' не найден.');
      $package = $pack->id_inner;

      // 2. Проверить существование базы данных пакета $package
      // - Если не существует, перейти к следующей итерации.
      if(count(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".mb_strtolower($package)."'")) == 0)
        throw new \Exception('База данных пакета '.$package.' не найдена.');

      // 3. Получить список имён всех имеющихся в БД пакета $package таблиц
      // - Отфильтровать таблицы "^md100", и начинающиеся не с "^md"
      $list = DB::select('SHOW tables FROM '.mb_strtolower($package));
      $list = array_map(function($item) USE ($package){
        $item = (array)$item;
        return $item['Tables_in_'.mb_strtolower($package)];
      }, $list);
      $list = array_values(array_filter($list, function($item){
        return preg_match("/^md/ui",$item) != 0 && preg_match("/^md100/ui",$item) == 0 && preg_match("/^md[0-9]+_/ui",$item) != 0;
      }));

      // 4. Для каждой таблицы узнать следующее:
      // - Включить ли автообслуживание created_at / updated_at
      // - Включить ли мягкое удаление
      $list_final = [];
      foreach($list as $table) {

        // 1] Получить список всех столбцов таблицы $table
        $columns = DB::select("SHOW COLUMNS FROM ".mb_strtolower($package).".".$table);
        $columns = array_map(function($item){
          return $item->Field;
        }, $columns);

        // 2] Выяснить на счёт автообслуживания created_at / updated_at
        $timestamps = call_user_func(function() USE ($columns) {
          return in_array('updated_at',$columns) && in_array('created_at',$columns) ? 'true' : 'false';
        });

        // 3] Выяснить на счёт мягкого удаления
        $softdeletes = call_user_func(function() USE ($columns) {
          return in_array('deleted_at',$columns) ? 'true' : 'false';
        });

        // 4] Определить ID таблицы
        $table_id = call_user_func(function() USE ($table) {

          preg_match("/^MD[0-9]+_/ui", $table, $id);
          $id = preg_replace("/^MD/ui","",$id[0]);
          $id = preg_replace("/_$/ui","",$id);
          return $id;

        });

        // 5] Добавить значение в $list_final
        array_push($list_final, [
          "table"       => $table,
          "id"          => $table_id,
          "timestamps"  => $timestamps,
          "softdeletes" => $softdeletes
        ]);

      }


      // 5. Удалить все существующие модели пакета $package

        // 1] Выполнить парсинг приложения
        $result = runcommand('\M1\Commands\C1_parseapp');
        if($result['status'] != 0)
          throw new \Exception($result['data']);

        // 2] Получить список ID всех моделей пакета $package
        $models = \M1\Models\MD3_models::whereHas('package', function($query) USE ($package){
          $query->where('id_inner',$package);
        })->pluck('id_inner');

        // 3] Удалить
        foreach($models as $model) {
          $result = runcommand('\M1\Commands\C25_del_m_m',[
            "packid"    => $package,
            "model2del" => $model
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']);
        }

      // 6. Создать модели из списка $list_final для пакета $package

        // 1] Выполнить парсинг приложения
        $result = runcommand('\M1\Commands\C1_parseapp');
        if($result['status'] != 0)
          throw new \Exception($result['data']);

        // 2] Создать
        foreach($list_final as $model) {

          $result = runcommand('\M1\Commands\C12_new_m_m',[
            'mpackid'       => $package,
            'name'          => preg_replace("/^MD[0-9]+_/ui","",$model['table']),
            'modelid'       => $model['id'],
            'timestamps'    => $model['timestamps'],
            'softdeletes'   => $model['softdeletes'],
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']);

        }

        // 3] Выполнить парсинг приложения
        $result = runcommand('\M1\Commands\C1_parseapp');
        if($result['status'] != 0)
          throw new \Exception($result['data']);

      // 7. Получить список связей типа foreign key в БД пакета $package
      // - В формате: [CONSTRAINT_NAME => TABLE_NAME]
      $fkeys_data = DB::select("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_SCHEMA='".mb_strtolower($package)."'");
      $fkeys = [];
      foreach($fkeys_data as &$fkey) {
        $fkeys[$fkey->CONSTRAINT_NAME] = $fkey->TABLE_NAME;
      }


      // Для каждой модели составить список связей для добавления в неё
      // - Тип связи
      // - Имя связи
      // - Pivot-таблица (если связь типа belongsToMany).
      // - Связанные столбцы



      //write2log($fkeys, []);







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

