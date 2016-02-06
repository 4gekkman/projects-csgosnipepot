<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Check presence for each foreign relationship, for each m-package, checks presence of related package/models presence in system. If not, clears pivot of foreign relation.
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
class C38_clear_interpack_rels extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить список ID всех доступных M-пакетов
     *  2. Пробежаться по $mpackages
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------//
    // Произвести очистку, если требуется //
    //------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить список ID всех доступных M-пакетов
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name', 'M');
      })->pluck('id_inner');

      // 2. Пробежаться по $mpackages
      foreach($mpackages as $package) {

        // 2.1. Проверить существование базы данных пакета $package
        // - Если не существует, перейти к следующей итерации.
        if(count(DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".mb_strtolower($package)."'")) == 0)
          continue;

        // 2.2. Получить список всех md200* таблиц
        $md2000_tables = DB::select('SHOW tables FROM '.mb_strtolower($package));
        $md2000_tables = array_map(function($item) USE ($package) {
          $item = (array)$item;
          return $item['Tables_in_'.mb_strtolower($package)];
        }, $md2000_tables);
        $md2000_tables = array_values(array_filter($md2000_tables, function($item){
          return preg_match("/^md2[0-9]{3}/ui",$item) != 0;
        }));

        // 2.3. Пробежатсья по всем $md2000_tables
        foreach($md2000_tables as $md2000_table) {

          // 2.3.1. Извлечь мета-информацию из DESCRIPTION таблицы TABLE_NAME

            // 1] Извлечь мета-информацию
            $meta = DB::select("SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='".mb_strtolower($package)."' AND table_name='".$md2000_table."'");

            // 2] Если извлечь мета-информацию не удалось
            // - Перейти к следующей итерации
            if(empty($meta) || (array_key_exists(0, $meta) && empty($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && !is_object($meta[0])) || (array_key_exists(0, $meta) && !empty($meta[0]) && is_object($meta[0]) && !property_exists($meta[0], 'table_comment') ))
              continue;

            // 3] Если $meta[0]->table_comment не json-строка, перейти к следующей итерации, сообщив в лог
            if(!r1_isJSON($meta[0]->table_comment))
              continue;

            // 4] Извлечь данные из $meta в виде массива
            $meta = json_decode($meta[0]->table_comment, true);

            // 5] Провести валидацию содержимого $meta
            // - Если не пройдёт валидацию, перейти к следующей итерации
            $validator = r4_validate($meta, [

              "mpackid"         => ["required", "regex:/^M[1-9]{1}[0-9]*$/ui"],
              "table"           => ["required", "regex:/^MD[1-9]{1}[0-9]*_/ui"]

            ]); if($validator['status'] == -1) {

              continue;

            }

          // 2.3.2. Проверить наличие базы/пакета $meta['mpackid'] и таблицы/модели $meta['table']
          $is_exists = [];
          $is_exists['base'] = r1_is_schema_exists(mb_strtolower($meta['mpackid']));
          $pack = \M1\Models\MD2_packages::where('id_inner', mb_strtoupper($meta['mpackid']))->first();
          $is_exists['pack'] = empty($pack) ? false : true;
          $is_exists['model'] = class_exists("\\".mb_strtoupper($meta['mpackid'])."\\Models\\".preg_replace('/^md/ui', 'MD', $meta['table']));
          $is_exists['table'] = r1_hasTable(mb_strtolower($meta['mpackid']), mb_strtolower($meta['table']));
          $is_exists = $is_exists['base'] && $is_exists['pack'] && $is_exists['model'] && $is_exists['table'];

          // 2.3.3. Если $is_exists == false, сделать truncate для таблицы $md2000_table
          if(!$is_exists) {

            DB::table(mb_strtolower($package).'.'.mb_strtolower($md2000_table))->truncate();

          }

        }

      }

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C38_clear_interpack_rels from M-package M1 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C38_clear_interpack_rels']);
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

