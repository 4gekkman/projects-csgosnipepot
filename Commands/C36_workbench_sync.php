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
      // - Но только, если речь не идёт о M1
      if($this->data['data']['packid'] != 'M1') {
        $pack = \M1\Models\MD2_packages::where('id_inner', $this->data['data']['packid'])->first();
        if(empty($pack))
          throw new \Exception('M-пакет с id равным '.$pack->id_inner.' не найден.');
        $package = $pack->id_inner;
      }

      // 2. Проверить существование базы данных пакета $package
      // - Если не существует, перейти к следующей итерации.
      if(count(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".mb_strtolower($this->data['data']['packid'])."'")) == 0)
        throw new \Exception('База данных пакета '.$this->data['data']['packid'].' не найдена.');

      // 3. Получить список имён всех имеющихся в БД пакета $package таблиц
      // - Отфильтровать таблицы "^md100", и начинающиеся не с "^md"
      $list = DB::select('SHOW tables FROM '.mb_strtolower($this->data['data']['packid']));
      $list = array_map(function($item) {
        $item = (array)$item;
        return $item['Tables_in_'.mb_strtolower($this->data['data']['packid'])];
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
        $columns = DB::select("SHOW COLUMNS FROM ".mb_strtolower($this->data['data']['packid']).".".$table);
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

        // 5.1] Получить пути ко всем дочерним файлам в Models M-пакета $this->data['data']['packid']
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $paths = $this->storage->files($this->data['data']['packid'].'/Models');

        // 5.2] Отсеять те, у которых последняя секция не матчится с ^MD[0-9]+$
        $paths = array_filter($paths, function($value){

          // Извлечь имя модели из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $value);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^MD[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        });

        // 5.3] Пробежаться по $paths
        foreach($paths as $path) {

          // 5.3.1] Извлечь имя модели из пути
          $name = preg_replace("/^.*\\//ui", "", $path);

          // 5.3.2] Удалить файл модели
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path()]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          $this->storage->delete('vendor/4gekkman/'.$this->data['data']['packid'].'/Models/'.$name);

        }

      
      // 6. Создать модели из списка $list_final для пакета $package

        // 1] Выполнить парсинг приложения
        // - Только если это не обновление пакета M1
        if($this->data['data']['packid'] != 'M1') {
          $result = runcommand('\M1\Commands\C1_parseapp');
          if($result['status'] != 0)
            throw new \Exception($result['data']);
        }

        // 2] Создать
        foreach($list_final as $model) {

          $result = runcommand('\M1\Commands\C12_new_m_m',[
            'mpackid'       => $this->data['data']['packid'],
            'name'          => preg_replace("/^MD[0-9]+_/ui","",$model['table']),
            'modelid'       => $model['id'],
            'timestamps'    => $model['timestamps'],
            'softdeletes'   => $model['softdeletes'],
            'issync'        => $this->data['data']['packid'] == 'M1' ? 1 : 0
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']);

        }

        // 3] Выполнить парсинг приложения
        // - Только если это не обновление пакета M1
        if($this->data['data']['packid'] != 'M1') {
          $result = runcommand('\M1\Commands\C1_parseapp');
          if($result['status'] != 0)
            throw new \Exception($result['data']);
        }

      // 7. Подготовить массив связей для добавления моделям пакета
      // - Его формат должен соответствовать представленному ниже
      /**
       *
       *  [
       *    "md1_routes" => [               // Имя модели, которой надо добавить эту связь
       *      "name1" => [                  // Имя связи
       *        "type"            => "",    // Тип связи: hasMany / belongsTo / belongsToMany
       *        "pivot"           => "",    // Имя pivot-таблицы (для связей типа belongsToMany)
       *        "related_model"   => "",    // Полный путь к связанной модели
       *        "foreign_key"     => "",    // Внешний ключ
       *        "local_key"       => ""     // Локальный ключ
       *      ],
       *      "name2" => [...]
       *    ],
       *    "md2_types" => [
       *      ...
       *    ]
       *  ]
       *
       */
      $relationships2add = call_user_func(function() USE ($list_final){

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Извлечь из MySQL инфу обо всех связях в БД пакета $package
        $all_rels = DB::select("SELECT CONSTRAINT_SCHEMA, CONSTRAINT_NAME, TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME is not null AND CONSTRAINT_SCHEMA='".mb_strtolower($this->data['data']['packid'])."'");

        // 3] Добавить в $result все имена моделей пакета $package
        // - В формате: "имя модели" => []
        foreach($list_final as $model) {
          $result[$model['table']] = [];
        }

        // 4] Найти в $all_rels пары с одинаковыми TABLE_NAME
        // - Формат которых соответствует "^md[0-9]{4}"
        // - И получить массив следующего вида:
        /**
         *  [
         *    "TABLE_NAME" => [
         *      [
         *        ...
         *      ],
         *      [
         *        ...
         *      ]
         *    ]
         *  ]
         */
        $rels4mn = call_user_func(function() USE ($all_rels) {

          // 1] Подготовить массив для результатов
          $result = [];

          // 2] Найти
          foreach($all_rels as $rel) {
            if(preg_match("/^md[0-9]{4}/ui", $rel->TABLE_NAME) != 0) {

              // 2.1] Если ключа TABLE_NAME ещё нет в $result, добавить
              if(!array_key_exists($rel->TABLE_NAME, $result))
                $result[$rel->TABLE_NAME] = [];

              // 2.2] Добавить $rel в $result[$rel->TABLE_NAME]
              array_push($result[$rel->TABLE_NAME], $rel);

            }
          }

          // 3] Проверить целостность $result
          // - Надо, чтобы каждый элемент-массив $result содержал ровна 2 элемента
          foreach($result as $rel) {
            if(!array_key_exists(0, $rel) || !array_key_exists(1, $rel) || array_key_exists(2, $rel))
              throw new \Exception("В БД пакета $this->data['data']['packid'] есть недоделанная m:n связь (см. pivot-таблицу $rel->TABLE_NAME).");
          }

          // n] Вернуть результаты
          return $result;

        });

        // 5] Подготовить и добавить в $result belongsToMany-связи
        call_user_func(function() USE (&$result, $rels4mn) {
          foreach($rels4mn as $rel) {

            // 5.1] Проверить в $result существование ключей
            // - $rel[0]->REFERENCED_TABLE_NAME
            // - $rel[1]->REFERENCED_TABLE_NAME
            // - Если какого-то из них нет, создать
            if(!array_key_exists($rel[0]->REFERENCED_TABLE_NAME, $result))
              $result[$rel[0]->REFERENCED_TABLE_NAME] = [];
            if(!array_key_exists($rel[1]->REFERENCED_TABLE_NAME, $result))
              $result[$rel[1]->REFERENCED_TABLE_NAME] = [];

            // 5.2] Добавить связь для модели $rel[0]->REFERENCED_TABLE_NAME

              // 5.2.1] Определить имя связи
              $relname = $rel[1]->REFERENCED_TABLE_NAME;
              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);

              // 5.2.2] Определить имя связанной модели
              $relmodel = $rel[1]->REFERENCED_TABLE_NAME;
              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);

              // 5.2.3] Добавить связь
              $result[$rel[0]->REFERENCED_TABLE_NAME][$relname] = [
                "type"            => "belongsToMany",
                "pivot"           => $rel[0]->TABLE_NAME,
                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
                "foreign_key"     => $rel[1]->COLUMN_NAME,
                "local_key"       => $rel[0]->COLUMN_NAME
              ];

            // 5.3] Добавить связь для модели $rel[1]->REFERENCED_TABLE_NAME

              // 5.3.1] Определить имя связи
              $relname = $rel[0]->REFERENCED_TABLE_NAME;
              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);

              // 5.3.2] Определить имя связанной модели
              $relmodel = $rel[0]->REFERENCED_TABLE_NAME;
              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);

              // 5.3.3] Добавить связь
              $result[$rel[1]->REFERENCED_TABLE_NAME][$relname] = [
                "type"            => "belongsToMany",
                "pivot"           => $rel[1]->TABLE_NAME,
                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
                "foreign_key"     => $rel[0]->COLUMN_NAME,
                "local_key"       => $rel[1]->COLUMN_NAME
              ];

          }
        });

        // 6] Подготовить и добавить в $result belongsTo- и hasMany-связи
        call_user_func(function() USE (&$result, $all_rels) {
          foreach($all_rels as $rel) {

            // 6.1] Отсеять belongsToMany-связи
            if(preg_match("/^md[0-9]{1,3}_/ui", $rel->TABLE_NAME) == 0)
              continue;

            // 6.2] Проверить в $result существование ключей
            // - $rel->TABLE_NAME
            // - $rel->REFERENCED_TABLE_NAME
            // - Если какого-то из них нет, создать
            if(!array_key_exists($rel->TABLE_NAME, $result))
              $result[$rel->TABLE_NAME] = [];
            if(!array_key_exists($rel->REFERENCED_TABLE_NAME, $result))
              $result[$rel->REFERENCED_TABLE_NAME] = [];

            // 6.3] Добавить связь типа belongsTo

              // 6.3.1] Определить имя связи
              $relname = $rel->REFERENCED_TABLE_NAME;
              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);

              // 6.3.2] Определить имя связанной модели
              $relmodel = $rel->REFERENCED_TABLE_NAME;
              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);

              // 6.3.3] Добавить связь
              $result[$rel->TABLE_NAME][$relname] = [
                "type"            => "belongsTo",
                "pivot"           => "",
                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
                "foreign_key"     => $rel->REFERENCED_COLUMN_NAME,
                "local_key"       => $rel->COLUMN_NAME
              ];

            // 6.4] Добавить связь типа hasMany

              // 6.4.1] Определить имя связи
              $relname = $rel->TABLE_NAME;
              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);

              // 6.4.2] Определить имя связанной модели
              $relmodel = $rel->TABLE_NAME;
              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);

              // 6.4.3] Добавить связь
              $result[$rel->REFERENCED_TABLE_NAME][$relname] = [
                "type"            => "hasMany",
                "pivot"           => "",
                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
                "foreign_key"     => $rel->COLUMN_NAME,
                "local_key"       => $rel->REFERENCED_COLUMN_NAME
              ];

          }
        });

        // n] Вернуть результат
        return $result;

      });

      // 8. Добавить в каждую модель её связи
      foreach($relationships2add as $model => $rels) {

        // 8.1. Если $model содержит пустой массив, перейти к след.итерации
        if(count($rels) == 0) continue;

        // 8.2. Проверить существование файла-модели $model
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$this->data['data']['packid'].'/Models')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        if(!$this->storage->exists($model.'.php'))
          throw new \Exception('Файл модели '.$model.'.php не существует в '.'vendor/4gekkman/'.$this->data['data']['packid'].'/Models');

        // 8.3. Получить содержимое файла-модели $model
        $file = $this->storage->get($model.'.php');

        // 8.4. Составить строку со связями для добавления в $file
        $rels2add = call_user_func(function() USE ($rels) {

          // 1] Подготовить строку для результата
          $result = "// relationships start" . PHP_EOL;

          // 2] Добавить связи в $result
          foreach($rels as $name => $sets) {

            // 2.1] Если тип связи belongsToMany
            if($sets['type'] == 'belongsToMany') {

              // 2.1.1] Добавить пробелы
              $result = $result . '    ';

              // 2.1.2] Добавить связь
              $result = $result . 'public function '.$name.'() { return $this->belongsToMany(\''.$sets['related_model'].'\', \''.mb_strtolower($this->data['data']['packid']).'.'.$sets['pivot'].'\', \''.$sets['local_key'].'\', \''.$sets['foreign_key'].'\'); }';

              // 2.1.3] Добавить перенос строки
              $result = $result . PHP_EOL;

            }

            // 2.2] Если тип связи belongsTo
            if($sets['type'] == 'belongsTo') {

              // 2.1.1] Добавить пробелы
              $result = $result . '    ';

              // 2.1.2] Добавить связь
              $result = $result . 'public function '.$name.'() { return $this->belongsTo(\''.$sets['related_model'].'\', \''.$sets['local_key'].'\', \''.$sets['foreign_key'].'\'); }';

              // 2.1.3] Добавить перенос строки
              $result = $result . PHP_EOL;

            }

            // 2.3] Если тип связи hasMany
            if($sets['type'] == 'hasMany') {

              // 2.1.1] Добавить пробелы
              $result = $result . '    ';

              // 2.1.2] Добавить связь
              $result = $result . 'public function '.$name.'() { return $this->hasMany(\''.$sets['related_model'].'\', \''.$sets['foreign_key'].'\', \''.$sets['local_key'].'\'); }';

              // 2.1.3] Добавить перенос строки
              $result = $result . PHP_EOL;

            }

          }

          // 3] Финальные штрики для $result
          $result = $result . "    // relationships stop";

          // n] Вернуть результат
          return $result;

        });

        // 8.5] Вставить $result в $file
        $file = preg_replace("#// *relationships *start.*// *relationships *stop#smuiU", $rels2add, $file);

        // 8.6] Заменить $file
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$this->data['data']['packid'].'/Models')]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $this->storage->put($model.'.php', $file);

      }


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

