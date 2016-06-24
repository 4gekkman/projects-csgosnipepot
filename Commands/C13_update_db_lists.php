<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Updates some lists-tables data in DB, using the data from config.
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

  namespace M8\Commands;

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
class C13_update_db_lists extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Обновить данные в MD3_exteriors
     *  2. Обновить данные в MD4_knife_types
     *  3. Обновить данные в MD5_weapon_models
     *  4. Создать строку с ID = 1 в MD6_price_update_bugs
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------------//
    // Обновляет данные в некоторых таблица-списках в БД, используя данные из конфига //
    //--------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Обновить данные в MD3_exteriors
      call_user_func(function(){

        // 1.1. Мягко удалить все exteriors
        \M8\Models\MD3_exteriors::query()->delete();

        // 1.2. Извлечь эталонные данные из конфига
        $data_exteriors_standard = config("M8.exteriors");

        // 1.3. Провести валидацию
        $validator = r4_validate(['data_exteriors_standard' => $data_exteriors_standard], [
          "data_exteriors_standard" => ["required", "array"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 1.4. Обновить данные в MD3_exteriors
        foreach($data_exteriors_standard as $exterior) {

          // 1] Восстановить мягко удалённые записи, имеющиеся в $packtypes
          $lookfor = \M8\Models\MD3_exteriors::onlyTrashed()->where('exterior','=',$exterior)->first();
          if(!empty($lookfor)) {
            $lookfor->restore();
            $lookfor->save();
            continue;
          }

          // 2] Добавить новые записи, которе есть в $data_exteriors_standard, но нет даже среди мягко удалённых в MD3_exteriors
          else {
            $exterior_new = new \M8\Models\MD3_exteriors();
            $exterior_new->exterior = $exterior;
            $exterior_new->save();
          }

        }

      });

      // 2. Обновить данные в MD4_knife_types
      call_user_func(function(){

        // 1.1. Мягко удалить все exteriors
        \M8\Models\MD4_knife_types::query()->delete();

        // 1.2. Извлечь эталонные данные из конфига
        $data_knife_types_standard = config("M8.knife_types");

        // 1.3. Провести валидацию
        $validator = r4_validate(['data_knife_types_standard' => $data_knife_types_standard], [
          "data_knife_types_standard" => ["required", "array"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 1.4. Обновить данные в MD4_knife_types
        foreach($data_knife_types_standard as $knife_type) {

          // 1] Восстановить мягко удалённые записи, имеющиеся в $packtypes
          $lookfor = \M8\Models\MD4_knife_types::onlyTrashed()->where('type','=',$knife_type)->first();
          if(!empty($lookfor)) {
            $lookfor->restore();
            $lookfor->save();
            continue;
          }

          // 2] Добавить новые записи, которе есть в $data_knife_types_standard, но нет даже среди мягко удалённых в MD4_knife_types
          else {
            $knife_type_new = new \M8\Models\MD4_knife_types();
            $knife_type_new->type = $knife_type;
            $knife_type_new->save();
          }

        }

      });

      // 3. Обновить данные в MD5_weapon_models
      call_user_func(function(){

        // 1.1. Мягко удалить все exteriors
        \M8\Models\MD5_weapon_models::query()->delete();

        // 1.2. Извлечь эталонные данные из конфига
        $data_weapon_models_standard = config("M8.weapon_models");

        // 1.3. Провести валидацию
        $validator = r4_validate(['data_weapon_models_standard' => $data_weapon_models_standard], [
          "data_weapon_models_standard" => ["required", "array"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 1.4. Обновить данные в MD5_weapon_models
        foreach($data_weapon_models_standard as $weapon_model) {

          // 1] Восстановить мягко удалённые записи, имеющиеся в $packtypes
          $lookfor = \M8\Models\MD5_weapon_models::onlyTrashed()->where('model','=',$weapon_model)->first();
          if(!empty($lookfor)) {
            $lookfor->restore();
            $lookfor->save();
            continue;
          }

          // 2] Добавить новые записи, которе есть в $data_weapon_models_standard, но нет даже среди мягко удалённых в MD5_weapon_models
          else {
            $weapon_model_new = new \M8\Models\MD5_weapon_models();
            $weapon_model_new->model = $weapon_model;
            $weapon_model_new->save();
          }

        }

      });

      // 4. Создать строку с ID = 1 в MD6_price_update_bugs
      call_user_func(function(){

        // 4.1. Попытаться получить модель с ID = 1 из MD6_price_update_bugs
        $model = \M8\Models\MD6_price_update_bugs::find(1);

        // 4.2. Если $model отсутствует, создать таковую
        if(empty($model)) {
          $new = new \M8\Models\MD6_price_update_bugs();
          $new->id = 1;
          $new->save();
        }

      });


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C13_update_db_lists from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C13_update_db_lists']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $errortext,
            "errormsg" => $e->getMessage()
          ]
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

