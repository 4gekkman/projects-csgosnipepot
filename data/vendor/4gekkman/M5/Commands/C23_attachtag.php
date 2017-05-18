<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Attach a tag to a user / group / privilege
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
class C23_attachtag extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти тег с $this->data['id']
     *  3. Попробовать найти право с $this->data['id_privilege']
     *  4. Попробовать найти пользователя с $this->data['id_user']
     *  5. Попробовать найти группу с $this->data['id_group']
     *  6. Если $user/$group/$privilege ещё не имеет такого $tag, прикрепить
     *  7. Сделать commit
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------//
    // Прикрепить тег указанному пользователю / группе / праву //
    //---------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, call_user_func(function(){

        $validators = [];
        $validators["id"] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
        if(array_key_exists('id_user', $this->data)) $validators["id_user"] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
        if(array_key_exists('id_group', $this->data)) $validators["id_group"] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
        if(array_key_exists('id_privilege', $this->data)) $validators["id_privilege"] = ["required", "regex:/^[1-9]+[0-9]*$/ui"];
        return $validators;

      })); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Попробовать найти тег с $this->data['id']
      $tag = \M5\Models\MD4_tags::find($this->data['id']);
      if(empty($tag))
        throw new \Exception("Тег с id '".$this->data['id']."' не найден в системе среди активных (не мягко удалённых)");

      // 3. Попробовать найти право с $this->data['id_privilege']
      if(array_key_exists('id_privilege', $this->data)) {
        $privilege = \M5\Models\MD3_privileges::find($this->data['id_privilege']);
        if(empty($privilege))
          throw new \Exception("Право с id '".$this->data['id_privilege']."' не найдено в системе среди активных (не мягко удалённых)");
      }

      // 4. Попробовать найти пользователя с $this->data['id_user']
      if(array_key_exists('id_user', $this->data)) {
        $user = \M5\Models\MD1_users::find($this->data['id_user']);
        if(empty($user))
          throw new \Exception("Пользователь с id '".$this->data['id']."' не найден в системе среди активных (не мягко удалённых)");
      }

      // 5. Попробовать найти группу с $this->data['id_group']
      if(array_key_exists('id_group', $this->data)) {
        $group = \M5\Models\MD2_groups::find($this->data['id_group']);
        if(empty($group))
          throw new \Exception("Группа с id '".$this->data['id']."' не найдена в системе среди активных (не мягко удалённых)");
      }

      // 6. Если $user/$group/$privilege ещё не имеет такого $tag, прикрепить

        // 6.1. Если тег надо прикрепить пользователю
        if(array_key_exists('id_user', $this->data)) {
          if(!$user->tags->contains($tag->id))
            $user->tags()->attach($tag->id);
        }

        // 6.2. Если тег надо прикрепить группе
        if(array_key_exists('id_group', $this->data)) {
          if(!$group->tags->contains($tag->id))
            $group->tags()->attach($tag->id);
        }

        // 6.3. Если тег надо прикрепить праву
        if(array_key_exists('id_privilege', $this->data)) {
          if(!$privilege->tags->contains($tag->id))
            $privilege->tags()->attach($tag->id);
        }

      // 7. Сделать commit
      DB::commit();

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "tag"          => $tag->name,
          "id_user"      => array_key_exists('id_user', $this->data) ? $user->id : "",
          "id_group"     => array_key_exists('id_group', $this->data) ? $group->id : "",
          "id_privilege" => array_key_exists('id_privilege', $this->data) ? $privilege->id : ""
        ]
      ];

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C23_attachtag from M-package M5 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C23_attachtag']);
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

