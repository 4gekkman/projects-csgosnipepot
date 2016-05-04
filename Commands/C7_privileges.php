<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get get privileges list (can use filters)
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

  namespace M5\Commands;

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
class C7_privileges extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих параметров
     *  2. Сформировать запрос с учётом фильтров, извлечь данные
     *  3. Вернуть результат
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------//
    // Вернуть информацию о правах с учётом фильтров и пагинации //
    //-----------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров

        // 1.1. Общая валидация
        $validator = r4_validate($this->data, [

          "page"            => ["required", "numeric"],
          "pages_total"     => ["r4_defined", "regex:/^([1-9]+[0-9]*|)$/ui"],
          "items_at_page"   => ["required", "numeric"],
          "filters"         => ["required", "array"]

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }

        // 1.2. Валидация наличия полного набора ключей для фильтров
        $validator = r4_validate($this->data['filters'], [

          "ids"             => ["r4_defined", "array"],
          "names"           => ["r4_defined", "array"],
          "users"           => ["r4_defined", "array"],
          "tags"            => ["r4_defined", "array"],
          "groups"          => ["r4_defined", "array"],
          "privtypes"       => ["r4_defined", "array"],
          "m1_packages"     => ["r4_defined", "array"],
          "m1_commands"     => ["r4_defined", "array"]

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }

        // 1.3. Получить список всех доступных типов прав
        $privtypes = \M5\Models\MD5_privtypes::all();
        if(empty($privtypes))
          throw new \Exception('Таблица типов прав MD5_privtypes пуста.');
        $privtypes = implode(",", $privtypes->pluck('name')->toArray());

        // 1.4. Валидация содержимого фильтров

          // 1] ids
          $validator = r4_validate($this->data['filters']['ids'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['ids'] as $key => $value) {
              $result[$key] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 2] names
          $validator = r4_validate($this->data['filters']['names'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['names'] as $key => $value) {
              $result[$key] = ["required", "regex:/^[a-zа-яё]{1}[a-zа-яё0-9]*$/ui"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 3] users
          $validator = r4_validate($this->data['filters']['users'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['users'] as $key => $value) {
              $result[$key] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 4] tags
          $validator = r4_validate($this->data['filters']['tags'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['tags'] as $key => $value) {
              $result[$key] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 5] groups
          $validator = r4_validate($this->data['filters']['groups'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['groups'] as $key => $value) {
              $result[$key] = ["required", "regex:/^[a-zа-яё]{1}[a-zа-яё0-9]*$/ui"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 6] privtypes
          $validator = r4_validate($this->data['filters']['privtypes'], call_user_func(function() USE ($privtypes) {
            $result = [];
            foreach($this->data['filters']['privtypes'] as $key => $value) {
              $result[$key] = ["required", "in:".$privtypes];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 7] m1_packages
          $validator = r4_validate($this->data['filters']['m1_packages'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['m1_packages'] as $key => $value) {
              $result[$key] = ["required"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

          // 7] m1_commands
          $validator = r4_validate($this->data['filters']['m1_commands'], call_user_func(function() {
            $result = [];
            foreach($this->data['filters']['m1_commands'] as $key => $value) {
              $result[$key] = ["required"];
            }
            return $result;
          })); if($validator['status'] == -1) {
            throw new \Exception($validator['data']);
          }

      // 2. Сформировать запрос с учётом фильтров, извлечь данные

        // 2.1. Зачать формированиез запроса
        $query = \M5\Models\MD3_privileges::query();
        $privs_total = with(clone $query)->count();

        // 2.2. Учесть все фильтры

          // 1] ids
          if(count($this->data['filters']['ids']) != 0) {
            $query->whereIn('id', $this->data['filters']['ids']);
          }

          // 2] names
          if(count($this->data['filters']['names']) != 0) {
            $query->whereIn('name', $this->data['filters']['names']);
          }

          // 3] users
          if(count($this->data['filters']['users']) != 0) {
            $query->whereHas('users', function($query){
              $query->whereIn('id', $this->data['filters']['users']);
            });
          }

          // 4] tags
          if(count($this->data['filters']['tags']) != 0) {
            $query->whereHas('tags', function($query){
              $query->whereIn('id', $this->data['filters']['tags']);
            });
          }

          // 5] groups
          if(count($this->data['filters']['groups']) != 0) {
            $query->whereHas('groups', function($query){
              $query->whereIn('id', $this->data['filters']['groups']);
            });
          }

          // 6] privtypes
          if(count($this->data['filters']['privtypes']) != 0) {
            $query->whereHas('privileges', function($query){
              $query->whereHas('privtypes', function($query){
                $query->whereIn('name', $this->data['filters']['privtypes']);
              });
            });
          }

          // 7] m1_packages
          if(r1_rel_exists("M5", "MD3_privileges", "m1_packages")) {
            if(count($this->data['filters']['m1_packages']) != 0) {
              $query->whereHas('privileges', function($query){
                $query->whereHas('m1_packages', function($query){
                  $query->whereIn('id_inner', $this->data['filters']['m1_packages']);
                });
              });
            }
          }

          // 8] m1_commands
          if(r1_rel_exists("M5", "MD3_privileges", "m1_commands")) {
            if(count($this->data['filters']['m1_commands']) != 0) {
              $query->whereHas('privileges', function($query){
                $query->whereHas('m1_commands', function($query){
                  $query->whereIn('uid', $this->data['filters']['m1_commands']);
                });
              });
            }
          }

        // 2.3. Получить pages_total и items_at_page
        $privs_filtered  = with(clone $query)->count();
        $items_at_page    = $this->data['items_at_page'];
        $pages_total      = (+with(clone $query)->count() < +$items_at_page) ? 1 : (int)ceil(+with(clone $query)->count()/$items_at_page);
        $page             = $this->data['page'];

        // 2.4. Получить коллекцию групп
        $privileges = with(clone $query)->with(['privtypes'])->skip($items_at_page*(+$page-1))->take($items_at_page)->get();

      // 3. Вернуть результат
      return [
        "status"  => 0,
        "data"    => [
          "privileges"      => $privileges,
          "pages_total"     => $pages_total,
          "privs_total"     => $privs_total,
          "privs_filtered"  => $privs_filtered,
          "items_at_page"   => $this->data['items_at_page'],
          "privtypes"       => \M5\Models\MD5_privtypes::all()
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C7_privileges from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C7_privileges']);
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

