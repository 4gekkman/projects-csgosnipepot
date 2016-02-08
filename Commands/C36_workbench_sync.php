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
     *  1. Получить M-пакет, для которого требуется синхронизация
     *  2. Проверить существование базы данных пакета $this->data['data']['packid']
     *  3. Получить список имён всех имеющихся в БД пакета $this->data['data']['packid'] таблиц
     *  4. Для каждой таблицы узнать следующее
     *  5. Удалить все существующие модели пакета $this->data['data']['packid']
     *  6. Создать модели из списка $list_final для пакета $this->data['data']['packid']
     *  7. Подготовить массив связей для добавления моделям пакета
     *  8. Добавить в каждую модель её связи
     *
     */

    //---------------------------------------------------------------------------//
    // Провести синхронизацию моделей и связей всех M-пакетов с из базами данных //
    //---------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить M-пакет, для которого требуется синхронизация
      // - Но только, если речь не идёт о M1
      $pack = \M1\Models\MD2_packages::where('id_inner', $this->data['data']['packid'])->first();

      // 2. Проверить существование базы данных пакета $this->data['data']['packid']
      // - Если не существует, возбудить ошибку
      if(count(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".mb_strtolower($this->data['data']['packid'])."'")) == 0)
        throw new \Exception('База данных пакета '.$this->data['data']['packid'].' не найдена.');

      // 3. Получить список имён всех имеющихся в БД пакета $this->data['data']['packid'] таблиц
      // - Отфильтровать таблицы "^md1[0-9]{3}", и начинающиеся не с "^md"
      $list = DB::select('SHOW tables FROM '.mb_strtolower($this->data['data']['packid']));
      $list = array_map(function($item) {
        $item = (array)$item;
        return $item['Tables_in_'.mb_strtolower($this->data['data']['packid'])];
      }, $list);
      $list = array_values(array_filter($list, function($item){
        return preg_match("/^md/ui",$item) != 0 && preg_match("/^md1[0-9]{3}/ui",$item) == 0 && preg_match("/^md[0-9]+_/ui",$item) != 0;
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

      // 5. Удалить все существующие модели пакета $this->data['data']['packid']

        // 1] Выполнить парсинг приложения
        // - Только если это не обновление пакета M1
        runcommand('\M1\Commands\C1_parseapp');

        // 2] Получить пути ко всем дочерним файлам в Models M-пакета $this->data['data']['packid']
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path()]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $paths = $this->storage->files('vendor/4gekkman/'.$this->data['data']['packid'].'/Models');

        // 3] Отсеять те, у которых последняя секция не матчится с ^MD[0-9]+$
        $paths = array_filter($paths, function($value){

          // Извлечь имя модели из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $value);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^MD[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        });

        // 4] Пробежаться по $paths
        foreach($paths as $path) {

          // 4.1] Извлечь имя модели из пути
          $name = preg_replace("/^.*\\//ui", "", $path);

          // 4.2] Удалить файл модели
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path()]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          $this->storage->delete('vendor/4gekkman/'.$this->data['data']['packid'].'/Models/'.$name);

        }

      // 6. Создать модели из списка $list_final для пакета $this->data['data']['packid']

        // 1] Создать
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

        // 2] Выполнить парсинг приложения
        runcommand('\M1\Commands\C1_parseapp');

//
//      // 7. Подготовить массив связей для добавления моделям пакета
//      // - Его формат должен соответствовать представленному ниже
//      /**
//       *
//       *  [
//       *    "M4" => [                         // ID пакета
//       *      "md1_routes" => [               // Имя модели, которой надо добавить эту связь
//       *        "name1" => [                  // Имя связи
//       *          "type"            => "",    // Тип связи: hasMany / belongsTo / belongsToMany
//       *          "pivot"           => "",    // Имя pivot-таблицы (для связей типа belongsToMany)
//       *          "related_model"   => "",    // Полный путь к связанной модели
//       *          "foreign_key"     => "",    // Внешний ключ
//       *          "local_key"       => ""     // Локальный ключ
//       *        ],
//       *        "name2" => [...]
//       *      ],
//       *      "md2_types" => [
//       *        ...
//       *      ]
//       *    ],
//       *    "M5" => ...
//       *  ]
//       *
//       */
//      $relationships2add = call_user_func(function() USE ($list_final){
//
//        // 1] Подготовить массив для результата
//        $result = [];
//
//        // 2] Извлечь из MySQL инфу обо всех связях в БД пакета $this->data['data']['packid']
//        $all_rels = DB::select("SELECT CONSTRAINT_SCHEMA, CONSTRAINT_NAME, TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME is not null AND CONSTRAINT_SCHEMA='".mb_strtolower($this->data['data']['packid'])."'");
//
//        // 3] Добавить в $result все имена моделей пакета $this->data['data']['packid']
//        // - В формате: "имя модели" => []
//
//          // 3.1] Создать ключ/значение [id пакета] => []
//          $result[$this->data['data']['packid']] = [];
//
//          // 3.2] Добавить туда все имена моделей пакета $this->data['data']['packid']
//          foreach($list_final as $model) {
//            $result[$this->data['data']['packid']][$model['table']] = [];
//          }
//
//        // 4] Найти в $all_rels пары с одинаковыми TABLE_NAME
//        // - Формат которых соответствует "^md1[0-9]{3}"
//        // - И получить массив следующего вида:
//        /**
//         *  [
//         *    "TABLE_NAME" => [
//         *      [
//         *        ...
//         *      ],
//         *      [
//         *        ...
//         *      ]
//         *    ]
//         *  ]
//         */
//        $rels4mn = call_user_func(function() USE ($all_rels) {
//
//          // 1] Подготовить массив для результатов
//          $result = [];
//
//          // 2] Найти
//          foreach($all_rels as $rel) {
//            if(preg_match("/^md1[0-9]{3}/ui", $rel->TABLE_NAME) != 0) {
//
//              // 2.1] Если ключа TABLE_NAME ещё нет в $result, добавить
//              if(!array_key_exists($rel->TABLE_NAME, $result))
//                $result[$rel->TABLE_NAME] = [];
//
//              // 2.2] Добавить $rel в $result[$rel->TABLE_NAME]
//              array_push($result[$rel->TABLE_NAME], $rel);
//
//            }
//          }
//
//          // 3] Проверить целостность $result
//          // - Надо, чтобы каждый элемент-массив $result содержал ровно 2 элемента
//          foreach($result as $rel) {
//            if(!array_key_exists(0, $rel) || !array_key_exists(1, $rel) || array_key_exists(2, $rel))
//              throw new \Exception("В БД пакета $this->data['data']['packid'] есть недоделанная m:n связь (см. pivot-таблицу $rel->TABLE_NAME).");
//          }
//
//          // n] Вернуть результаты
//          return $result;
//
//        });
//
//        // 5] Подготовить и добавить в $result belongsToMany-связи
//        call_user_func(function() USE (&$result, $rels4mn) {
//          foreach($rels4mn as $rel) {
//
//            // 5.1] Проверить в $result существование ключей
//            // - $rel[0]->REFERENCED_TABLE_NAME
//            // - $rel[1]->REFERENCED_TABLE_NAME
//            // - Если какого-то из них нет, создать
//            if(!array_key_exists($rel[0]->REFERENCED_TABLE_NAME, $result[$this->data['data']['packid']]))
//              $result[$this->data['data']['packid']][$rel[0]->REFERENCED_TABLE_NAME] = [];
//            if(!array_key_exists($rel[1]->REFERENCED_TABLE_NAME, $result[$this->data['data']['packid']]))
//              $result[$this->data['data']['packid']][$rel[1]->REFERENCED_TABLE_NAME] = [];
//
//            // 5.2] Добавить связь для модели $rel[0]->REFERENCED_TABLE_NAME
//
//              // 5.2.1] Определить имя связи
//              $relname = $rel[1]->REFERENCED_TABLE_NAME;
//              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//
//              // 5.2.2] Определить имя связанной модели
//              $relmodel = $rel[1]->REFERENCED_TABLE_NAME;
//              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);
//
//              // 5.2.3] Добавить связь
//              $result[$this->data['data']['packid']][$rel[0]->REFERENCED_TABLE_NAME][$relname] = [
//                "type"            => "belongsToMany",
//                "pivot"           => mb_strtolower($this->data['data']['packid']).".".$rel[0]->TABLE_NAME,
//                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
//                "foreign_key"     => $rel[1]->COLUMN_NAME,
//                "local_key"       => $rel[0]->COLUMN_NAME
//              ];
//
//            // 5.3] Добавить связь для модели $rel[1]->REFERENCED_TABLE_NAME
//
//              // 5.3.1] Определить имя связи
//              $relname = $rel[0]->REFERENCED_TABLE_NAME;
//              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//
//              // 5.3.2] Определить имя связанной модели
//              $relmodel = $rel[0]->REFERENCED_TABLE_NAME;
//              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);
//
//              // 5.3.3] Добавить связь
//              $result[$this->data['data']['packid']][$rel[1]->REFERENCED_TABLE_NAME][$relname] = [
//                "type"            => "belongsToMany",
//                "pivot"           => mb_strtolower($this->data['data']['packid']).".".$rel[1]->TABLE_NAME,
//                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
//                "foreign_key"     => $rel[0]->COLUMN_NAME,
//                "local_key"       => $rel[1]->COLUMN_NAME
//              ];
//
//          }
//        });
//
//        // 6] Подготовить и добавить в $result belongsTo- и hasMany-связи
//        call_user_func(function() USE (&$result, $all_rels) {
//          foreach($all_rels as $rel) {
//
//            // 6.1] Отсеять belongsToMany-связи
//            if(preg_match("/^md[0-9]{1,3}_/ui", $rel->TABLE_NAME) == 0)
//              continue;
//
//            // 6.2] Проверить в $result существование ключей
//            // - $rel->TABLE_NAME
//            // - $rel->REFERENCED_TABLE_NAME
//            // - Если какого-то из них нет, создать
//            if(!array_key_exists($rel->TABLE_NAME, $result[$this->data['data']['packid']]))
//              $result[$this->data['data']['packid']][$rel->TABLE_NAME] = [];
//            if(!array_key_exists($rel->REFERENCED_TABLE_NAME, $result[$this->data['data']['packid']]))
//              $result[$this->data['data']['packid']][$rel->REFERENCED_TABLE_NAME] = [];
//
//            // 6.3] Добавить связь типа belongsTo
//
//              // 6.3.1] Определить имя связи
//              $relname = $rel->REFERENCED_TABLE_NAME;
//              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//
//              // 6.3.2] Определить имя связанной модели
//              $relmodel = $rel->REFERENCED_TABLE_NAME;
//              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);
//
//              // 6.3.3] Добавить связь
//              $result[$this->data['data']['packid']][$rel->TABLE_NAME][$relname] = [
//                "type"            => "belongsTo",
//                "pivot"           => "",
//                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
//                "foreign_key"     => $rel->REFERENCED_COLUMN_NAME,
//                "local_key"       => $rel->COLUMN_NAME
//              ];
//
//            // 6.4] Добавить связь типа hasMany
//
//              // 6.4.1] Определить имя связи
//              $relname = $rel->TABLE_NAME;
//              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//
//              // 6.4.2] Определить имя связанной модели
//              $relmodel = $rel->TABLE_NAME;
//              $relmodel = preg_replace("/^md/u", 'MD', $relmodel);
//
//              // 6.4.3] Добавить связь
//              $result[$this->data['data']['packid']][$rel->REFERENCED_TABLE_NAME][$relname] = [
//                "type"            => "hasMany",
//                "pivot"           => "",
//                "related_model"   => "\\".mb_strtoupper($this->data['data']['packid'])."\\Models\\$relmodel",
//                "foreign_key"     => $rel->COLUMN_NAME,
//                "local_key"       => $rel->REFERENCED_COLUMN_NAME
//              ];
//
//          }
//        });
//
//        // 7] Найти в $all_rels связи с TABLE_NAME вида "^md2[0-9]{3}"
//        // - И получить массив следующего вида:
//        /**
//         *  [
//         *    "TABLE_NAME" => [
//         *      [
//         *        ...
//         *      ]
//         *    ]
//         *  ]
//         */
//        $foreign_rels = call_user_func(function() USE ($all_rels) {
//
//          // 1] Подготовить массив для результатов
//          $result = [];
//
//          // 2] Найти
//          foreach($all_rels as $rel) {
//            if(preg_match("/^md2[0-9]{3}/ui", $rel->TABLE_NAME) != 0) {
//
//              // 2.1] Если ключа TABLE_NAME ещё нет в $result, добавить
//              if(!array_key_exists($rel->TABLE_NAME, $result))
//                $result[$rel->TABLE_NAME] = [];
//
//              // 2.2] Добавить $rel в $result[$rel->TABLE_NAME]
//              array_push($result[$rel->TABLE_NAME], $rel);
//
//            }
//          }
//
//          // n] Вернуть результаты
//          return $result;
//
//        });
//
//        // 8] Подготовить и добавить в $result foreign-belongsToMany-связи
//        call_user_func(function() USE (&$result, $foreign_rels) {
//          foreach($foreign_rels as $rel) {
//
//            // 8.1] Проверить в $result существование ключей
//            // - $rel[0]->REFERENCED_TABLE_NAME
//            // - Если нет, создать
//            if(!array_key_exists($rel[0]->REFERENCED_TABLE_NAME, $result[$this->data['data']['packid']]))
//              $result[$this->data['data']['packid']][$rel[0]->REFERENCED_TABLE_NAME] = [];
//
//            // 8.2] Извлечь мета-информацию из DESCRIPTION таблицы TABLE_NAME
//
//              // 8.2.1] Извлечь мета-информацию
//              $meta = DB::select("SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='".mb_strtolower($this->data['data']['packid'])."' AND table_name='".$rel[0]->TABLE_NAME."'");
//
//              // 8.2.2] Если извлечь мета-информацию не удалось
//              if(empty($meta) || (array_key_exists(0, $meta) && empty($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && !is_object($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && is_object($meta[0]) && !property_exists($meta[0], 'table_comment') )) {
//                write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' не удалось извлечь мета-информацию из description таблицы связи, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                continue;
//              }
//
//              // 8.2.3] Если $meta[0]->table_comment не json-строка, перейти к следующей итерации, сообщив в лог
//              if(!r1_isJSON($meta[0]->table_comment)) {
//                write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что мета-информация в description таблицы связи не является валидной JSON-строкой, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                continue;
//              }
//
//              // 8.2.4] Извлечь данные из $meta в виде массива
//              $meta = json_decode($meta[0]->table_comment, true);
//
//              // 8.2.5] Провести валидацию содержимого $meta
//              $validator = r4_validate($meta, [
//
//                "mpackid"         => ["required", "regex:/^M[1-9]{1}[0-9]*$/ui"],
//                "table"           => ["required", "regex:/^MD[1-9]{1}[0-9]*_/ui"]
//
//              ]); if($validator['status'] == -1) {
//
//                write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что мета-информация в description таблицы связи не является валидной, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                continue;
//
//              }
//
//            // 8.3] Проверить наличие пакетов/баз моделей/таблиц
//
//              // 8.3.1] Проверяем наличие пакетов/баз
//
//                // Получить и проверить базы
//                $basename1 = $this->data['data']['packid'];
//                $basename2 = $meta['mpackid'];
//                if(!r1_is_schema_exists(mb_strtolower($basename1))) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что база данных '.$basename1.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//                if(!r1_is_schema_exists(mb_strtolower($basename2))) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что база данных '.$basename2.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//
//                // Получить и проверить пакеты
//                $pack1 = \M1\Models\MD2_packages::where('id_inner', mb_strtoupper($basename1))->first();
//                $pack2 = \M1\Models\MD2_packages::where('id_inner', mb_strtoupper($basename2))->first();
//                if(empty($pack1)) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что пакет '.$basename1.' не установлен, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//                if(empty($pack2)) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что пакет '.$basename2.' не установлен, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//
//              // 8.3.2] Проверяем наличие моделей/таблиц
//
//                // Получить имена моделей, и проверить их наличие
//                $modelname1 = preg_replace('/^md/ui', 'MD', $rel[0]->REFERENCED_TABLE_NAME);
//                $modelname2 = preg_replace('/^md/ui', 'MD', $meta['table']);
//                if(!class_exists("\\".mb_strtoupper($basename1)."\\Models\\".$modelname1)) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что модель '."\\".mb_strtoupper($basename1)."\\Models\\".$modelname1.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//                if(!class_exists("\\".mb_strtoupper($basename2)."\\Models\\".$modelname2)) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что модель '."\\".mb_strtoupper($basename2)."\\Models\\".$modelname2.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//
//                // Получить имена таблиц, и проверить их наличие
//                $tablename1 = preg_replace('/^MD/u', 'md', $modelname1);
//                $tablename2 = preg_replace('/^MD/u', 'md', $modelname2);
//                if(!r1_hasTable(mb_strtolower($basename1), mb_strtolower($tablename1))) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что таблица '.mb_strtolower($tablename1).' отсутствует в базе данных '.$basename1.', в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//                if(!r1_hasTable(mb_strtolower($basename2), mb_strtolower($tablename2))) {
//                  write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).' выяснилось, что таблица '.mb_strtolower($tablename2).' отсутствует в базе данных '.$basename2.', в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                  continue;
//                }
//
//            // 8.4] Добавить связь для модели $rel[0]->REFERENCED_TABLE_NAME
//
//              // 8.4.1] Определить имя связи
//              $relname = $modelname2;
//              $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//              $relname = mb_strtolower($basename2) . '_' . $relname;
//
//              // 8.4.2] Определить foreign_key
//              $foreign_key = call_user_func(function() USE ($basename1, $rel){
//
//                // Получить список столбцов для $rel[0]->TABLE_NAME
//                $columns = r1_getColumns(mb_strtolower($basename1), $rel[0]->TABLE_NAME);
//
//                // Отфильтровать из $columns значение $rel[0]->COLUMN_NAME
//                $columns = array_values(array_filter($columns, function($item) USE ($rel) {
//                  if($item == $rel[0]->COLUMN_NAME) return false;
//                  return true;
//                }));
//
//                // Вернуть оставшееся в $columns значение
//                return $columns[0];
//
//              });
//
//              // 8.4.3] Добавить связь
//              $result[$this->data['data']['packid']][$rel[0]->REFERENCED_TABLE_NAME][$relname] = [
//                "type"            => "belongsToMany",
//                "pivot"           => mb_strtolower($this->data['data']['packid']).".".$rel[0]->TABLE_NAME,
//                "related_model"   => "\\".mb_strtoupper($basename2)."\\Models\\$modelname2",
//                "foreign_key"     => $foreign_key,
//                "local_key"       => $rel[0]->COLUMN_NAME
//              ];
//
//          }
//        });
//
//        // 9] Найти внешние связи других M-пакетов, связанные с этим
//        // - И добавить их в result
//        call_user_func(function() USE (&$result) {
//
//          // Получить список ID всех установленных M-пакетов
//          // - Исключив из него $this->data['data']['packid']
//          $mpacks = call_user_func(function(){
//            $dirs = r1_fs('vendor/4gekkman')->directories();
//            $dirs = array_filter($dirs, function($item){
//              if(preg_match("/^[M]{1}[0-9]*$/ui", $item)) return true; else return false;
//            });
//            $mpacks = array_values(array_filter($dirs, function($item){ if(preg_match("/^M[0-9]*$/ui", $item)) return true; else return false; }));
//            $mpacks = array_values(array_filter($mpacks, function($item){
//              if($item == $this->data['data']['packid']) return false;
//              return true;
//            }));
//            return $mpacks;
//          });
//
//          // Пробежаться по $mpacks
//          foreach($mpacks as $mpack) {
//
//            // 1] Извлечь из MySQL инфу обо всех связях в БД пакета $this->data['data']['packid']
//            $all_rels = DB::select("SELECT CONSTRAINT_SCHEMA, CONSTRAINT_NAME, TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME is not null AND CONSTRAINT_SCHEMA='".mb_strtolower($mpack)."'");
//
//            // 2] Найти в $all_rels связи с TABLE_NAME вида "^md2[0-9]{3}"
//            // - И получить массив следующего вида:
//            /**
//             *  [
//             *    "TABLE_NAME" => [
//             *      [
//             *        ...
//             *      ]
//             *    ]
//             *  ]
//             */
//            $foreign_rels = call_user_func(function() USE ($all_rels) {
//
//              // 1] Подготовить массив для результатов
//              $result = [];
//
//              // 2] Найти
//              foreach($all_rels as $rel) {
//                if(preg_match("/^md2[0-9]{3}/ui", $rel->TABLE_NAME) != 0) {
//
//                  // 2.1] Если ключа TABLE_NAME ещё нет в $result, добавить
//                  if(!array_key_exists($rel->TABLE_NAME, $result))
//                    $result[$rel->TABLE_NAME] = [];
//
//                  // 2.2] Добавить $rel в $result[$rel->TABLE_NAME]
//                  array_push($result[$rel->TABLE_NAME], $rel);
//
//                }
//              }
//
//              // n] Вернуть результаты
//              return $result;
//
//            });
//
//            // 3] Подготовить и добавить в $result связи из $foreign_rels
//            call_user_func(function() USE (&$result, $foreign_rels, $mpack) {
//              foreach($foreign_rels as $rel) {
//
////                // 3.1] Проверить в $result существование ключей
////                // - $rel[0]->REFERENCED_TABLE_NAME
////                // - Если нет, создать
////                if(!array_key_exists($rel[0]->REFERENCED_TABLE_NAME, $result[$mpack]))
////                  $result[$mpack][$rel[0]->REFERENCED_TABLE_NAME] = [];
//
//                // 3.2] Извлечь мета-информацию из DESCRIPTION таблицы TABLE_NAME
//
//                  // 3.2.1] Извлечь мета-информацию
//                  $meta = DB::select("SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='".mb_strtolower($mpack)."' AND table_name='".$rel[0]->TABLE_NAME."'");
//
//                  // 3.2.2] Если извлечь мета-информацию не удалось
//                  if(empty($meta) || (array_key_exists(0, $meta) && empty($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && !is_object($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && is_object($meta[0]) && !property_exists($meta[0], 'table_comment') )) {
//                    write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', не удалось извлечь мета-информацию из description таблицы связи, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                    continue;
//                  }
//
//                  // 3.2.3] Если $meta[0]->table_comment не json-строка, перейти к следующей итерации, сообщив в лог
//                  if(!r1_isJSON($meta[0]->table_comment)) {
//                    write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что мета-информация в description таблицы связи не является валидной JSON-строкой, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                    continue;
//                  }
//
//                  // 3.2.3] Извлечь данные из $meta в виде массива
//                  $meta = json_decode($meta[0]->table_comment, true);
//
//                  // 3.2.4] Провести валидацию содержимого $meta
//                  $validator = r4_validate($meta, [
//
//                    "mpackid"         => ["required", "regex:/^M[1-9]{1}[0-9]*$/ui"],
//                    "table"           => ["required", "regex:/^MD[1-9]{1}[0-9]*_/ui"]
//
//                  ]); if($validator['status'] == -1) {
//
//                    write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что мета-информация в description таблицы связи не является валидной, что является ошибкой. Её требуется исправить.', ['m1', 'C36_workbench_sync']);
//                    continue;
//
//                  }
//
//                // 3.3] Проверить наличие пакетов/баз моделей/таблиц
//
//                  // 3.3.1] Проверяем наличие пакетов/баз
//
//                    // Получить и проверить базы
//                    $basename1 = $meta['mpackid'];
//                    $basename2 = $mpack;
//                    if(!r1_is_schema_exists(mb_strtolower($basename1))) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что база данных '.$basename1.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//                    if(!r1_is_schema_exists(mb_strtolower($basename2))) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что база данных '.$basename2.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//
//                    // Получить и проверить пакеты
//                    // - Только если $basename1 != 'M1'
//                    if($basename1 != 'M1') {
//                      $pack1 = \M1\Models\MD2_packages::where('id_inner', mb_strtoupper($basename1))->first();
//                      $pack2 = \M1\Models\MD2_packages::where('id_inner', mb_strtoupper($basename2))->first();
//                      if(empty($pack1)) {
//                        write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что пакет '.$basename1.' не установлен, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                        continue;
//                      }
//                      if(empty($pack2)) {
//                        write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что пакет '.$basename2.' не установлен, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                        continue;
//                      }
//                    }
//
//                  // 3.3.2] Проверяем наличие моделей/таблиц
//
//                    // Получить имена моделей, и проверить их наличие
//                    $modelname1 = $meta['table'];
//                    $modelname2 = preg_replace('/^md/u', 'MD', $rel[0]->REFERENCED_TABLE_NAME);
//                    if(!class_exists("\\".mb_strtoupper($basename1)."\\Models\\".$modelname1)) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что модель '."\\".mb_strtoupper($basename1)."\\Models\\".$modelname1.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//                    if(!class_exists("\\".mb_strtoupper($basename2)."\\Models\\".$modelname2)) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что модель '."\\".mb_strtoupper($basename2)."\\Models\\".$modelname2.' не существует, в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//
//                    // Получить имена таблиц, и проверить их наличие
//                    $tablename1 = preg_replace('/^MD/u', 'md', $modelname1);
//                    $tablename2 = preg_replace('/^MD/u', 'md', $modelname2);
//                    if(!r1_hasTable(mb_strtolower($basename1), mb_strtolower($tablename1))) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что таблица '.mb_strtolower($tablename1).' отсутствует в базе данных '.$basename1.', в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//                    if(!r1_hasTable(mb_strtolower($basename2), mb_strtolower($tablename2))) {
//                      write2log('Во время обновления внешней связи '.$rel[0]->TABLE_NAME.' пакета '.mb_strtolower($this->data['data']['packid']).', тянущейся от пакета '.$mpack.', выяснилось, что таблица '.mb_strtolower($tablename2).' отсутствует в базе данных '.$basename2.', в связи с чем связь не была создана.', ['m1', 'C36_workbench_sync']);
//                      continue;
//                    }
//
//                // 3.4] Добавить связь для модели $rel[0]->REFERENCED_TABLE_NAME
//
//                  // 3.4.1] Определить имя связи
//                  $relname = $modelname2;
//                  $relname = preg_replace("/^md[0-9]{1,3}_/ui", '', $relname);
//                  $relname = mb_strtolower($basename2) . '_' . $relname;
//
//                  // 3.4.2] Определить foreign_key
//                  $local_key = call_user_func(function() USE ($basename2, $rel){
//
//                    // Получить список столбцов для $rel[0]->TABLE_NAME
//                    $columns = r1_getColumns(mb_strtolower($basename2), $rel[0]->TABLE_NAME);
//
//                    // Отфильтровать из $columns значение $rel[0]->COLUMN_NAME
//                    $columns = array_values(array_filter($columns, function($item) USE ($rel) {
//                      if($item == $rel[0]->COLUMN_NAME) return false;
//                      return true;
//                    }));
//
//                    // Вернуть оставшееся в $columns значение
//                    return $columns[0];
//
//                  });
//
//                  // 3.4.3] Добавить связь
//                  $result[$this->data['data']['packid']][$tablename1][$relname] = [
//                    "type"            => "belongsToMany",
//                    "pivot"           => mb_strtolower($mpack).".".$rel[0]->TABLE_NAME,
//                    "related_model"   => "\\".mb_strtoupper($mpack)."\\Models\\$modelname2",
//                    "foreign_key"     => $rel[0]->COLUMN_NAME,
//                    "local_key"       => $local_key
//                  ];
//
//              }
//            });
//
//          }
//
//        });
//
//        // n] Вернуть результат
//        return $result;
//
//      });
//
//      // 8. Добавить в каждую модель её связи
//      foreach($relationships2add[$this->data['data']['packid']] as $model => $rels) {
//
//        // 8.1. Если $model содержит пустой массив, перейти к след.итерации
//        if(count($rels) == 0) continue;
//
//        // 8.2. Проверить существование файла-модели $model
//        config(['filesystems.default' => 'local']);
//        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$this->data['data']['packid'].'/Models')]);
//        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
//        if(!$this->storage->exists($model.'.php'))
//          throw new \Exception('Файл модели '.$model.'.php не существует в '.'vendor/4gekkman/'.$this->data['data']['packid'].'/Models');
//
//        // 8.3. Получить содержимое файла-модели $model
//        $file = $this->storage->get($model.'.php');
//
//        // 8.4. Составить строку со связями для добавления в $file
//        $rels2add = call_user_func(function() USE ($rels) {
//
//          // 1] Подготовить строку для результата
//          $result = "// relationships start" . PHP_EOL;
//
//          // 2] Добавить связи в $result
//          foreach($rels as $name => $sets) {
//
//            // 2.1] Если тип связи belongsToMany
//            if($sets['type'] == 'belongsToMany') {
//
//              // 2.1.1] Добавить пробелы
//              $result = $result . '    ';
//
//              // 2.1.2] Добавить связь
//              $result = $result . 'public function '.$name.'() { return $this->belongsToMany(\''.$sets['related_model'].'\', \''.$sets['pivot'].'\', \''.$sets['local_key'].'\', \''.$sets['foreign_key'].'\'); }';
//
//              // 2.1.3] Добавить перенос строки
//              $result = $result . PHP_EOL;
//
//            }
//
//            // 2.2] Если тип связи belongsTo
//            if($sets['type'] == 'belongsTo') {
//
//              // 2.1.1] Добавить пробелы
//              $result = $result . '    ';
//
//              // 2.1.2] Добавить связь
//              $result = $result . 'public function '.$name.'() { return $this->belongsTo(\''.$sets['related_model'].'\', \''.$sets['local_key'].'\', \''.$sets['foreign_key'].'\'); }';
//
//              // 2.1.3] Добавить перенос строки
//              $result = $result . PHP_EOL;
//
//            }
//
//            // 2.3] Если тип связи hasMany
//            if($sets['type'] == 'hasMany') {
//
//              // 2.1.1] Добавить пробелы
//              $result = $result . '    ';
//
//              // 2.1.2] Добавить связь
//              $result = $result . 'public function '.$name.'() { return $this->hasMany(\''.$sets['related_model'].'\', \''.$sets['foreign_key'].'\', \''.$sets['local_key'].'\'); }';
//
//              // 2.1.3] Добавить перенос строки
//              $result = $result . PHP_EOL;
//
//            }
//
//          }
//
//          // 3] Финальные штрики для $result
//          $result = $result . "    // relationships stop";
//
//          // n] Вернуть результат
//          return $result;
//
//        });
//
//        // 8.5] Вставить $result в $file
//        $file = preg_replace("#// *relationships *start.*// *relationships *stop#smuiU", $rels2add, $file);
//
//        // 8.6] Заменить $file
//        config(['filesystems.default' => 'local']);
//        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$this->data['data']['packid'].'/Models')]);
//        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
//        $this->storage->put($model.'.php', $file);
//
//      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C36_workbench_sync from M-package M1 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
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

