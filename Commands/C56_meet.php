<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Meet users in before middleware of M5, and write auth data to session cache and to cookie
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
class C56_meet extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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

    //----------------------//
    // Теоретический ликбез //
    /*----------------------//

      > Задачи аутентификационнго кэша
        - Этот кэш живёт в сессии, пока она жива, то есть до закрытия браузера.
        - Когда пользователь 1-й раз открывает сайт в браузере, команда meet создаёт этот кэш.
        - При последующих запросах meet в начале себя этот кэш в сессии обнаруживает,
          и завершает свою работу (ничего больше не делая).
        - Это позволяет экономить ресурсы сервера.

      > Задачи аутентификационной куки
        - Если meet не нашла кэш, она пытается найти аутентификационную куку.
        - По ней meet узнаёт пользователя, который ранее закрыл браузер, потом
          открыл его снова, и зашёл на наш сайт.
        - Meet проверяет по id пользователя и id аутентификационной записи из
          значения куки, а также по вычисленному времени жизни аутентификации,
          наличие и валидность аутентиф.записи в таблице аутентификаций.
        - В случае успеха, meet создаёт новый аутентиф.кэш для пользователя в сессии.
        - В случае неудачи, meet ищет анонимного пользователя, и если находит, то
          создаёт аутентификационный кэш и куку с его данными, а если не находит, то
          создаёт аутентификационный кэш и куку с пустыми данными.

      > Задачи аутентификационной записи
        - Во-первых, АЗ позволяют одновременно войти в один аккаунт из разных
          браузеров или устройств, при этом никого "выкидывать" из аккаунта не будет.
          При этом у каждого такого пользователя сессия и аутентиф.кэш будут свои.
        - Во-вторых, АЗ позволяет эффективно контролировать возможность входа в аккаунт,
          например, для заблокированных пользователей все АЗ удаляются, а новые АЗ
          не могут быть созданы.
        - Валидность АЗ (не истекла ли она) определяется по следующей методике:

            • Валидна:      if(now - created_at > life_time)
            • Не валидна:   if(now - created_at >= life_time)

        - Где:

            • now           | текущие дата и время
            • created_at    | дата и время создания аутентификационной записи
            • life_time     | рассчитанное время жизни аутентификационной записи (по настройам из конфига)

    //---------------------------------*/
    // Провести "встречу" пользователя //
    //---------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1] Проверить аутентификационный кэш из сессии

        // 1.1] Получить аутентификационный кэш из сессии
        $auth_cache = session('auth_cache');

        // 1.2] Если он не пуст, завершить работу функции
        if(!empty($auth_cache))
          return [
            "status"  => 0,
            "data"    => ""
          ];

      // 2] Получить аутентификационную куку
      $auth_cookie = Request::cookie('auth');

      // 3] Провести валидацию аутентификационной куки
      $is_auth_cookie_valid = call_user_func(function() USE ($auth_cookie) {

        // 3.1.1] Удостовериться, что кука содержит валидный json
        $validator = r4_validate(["auth", $auth_cookie], [
          "auth"              => ["required", "json"],
        ]); if($validator['status'] == -1) {
          return false;
        }

        // 3.1.2] Провести валидацию важного для meet содержимого этого json
        $validator = r4_validate(json_decode($auth_cookie, true), [
          "user"              => ["required", "array"],
          "user.id"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "auth"              => ["required", "array"],
          "auth.id"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        ]); if($validator['status'] == -1) {
          return false;
        }

        // 3.1.3] Вернуть true (успешная валидация)
        return true;

      });

      // 4] Если аутентификационная кука не пуста и валидна
      if(!empty($auth_cookie) && $is_auth_cookie_valid == true) {

        // 4.1] Декодировать json-строку с данными аутентиф.куки в массив
        $auth_cookie_arr = json_decode($auth_cookie, true);

        // 4.2] Извлечь ID пользователя и ID аутентиф.записи из $auth_cookie_arr
        $auth_cookie_user_id = $auth_cookie_arr['user']['id'];
        $auth_cookie_auth_id = $auth_cookie_arr['auth']['id'];

        // 4.3] Попробовать найти валидную аутентиф.запись по данным из куки
        $auth_note = \M5\Models\MD8_auth::where('id', $auth_cookie_auth_id)->whereHas('users', function($query) USE ($auth_cookie_user_id) {
          $query->where('id', $auth_cookie_user_id);
        })->first();

        // 4.4] Проверить, валидна ли $auth_note
        $is_valid_auth_note = call_user_func(function() USE ($auth_note) {

//          // 1) Получить Carbon-объект с датой и временем создания кода
//          $created_at = $code->created_at;
//
//          // 2) Получить Carbon-объект с текущими серверными датой и временем
//          $now = \Carbon\Carbon::now();
//
//          // 3) Получить разницу в минутах между $now и $created_at
//          $diff_in_min = $now->diffInMinutes($created_at);
//
//          // 4) Если эта разница больше/равна указанному в конфиге времени жизни, удалить $code
//          if($diff_in_min >= (config('M5.phone_verify_code_lifetime_min') ?: 15)) {
//            $code->users()->detach();
//            $code->delete();
//          }

          // n] Вернуть true, что значит "валидна"
          return true;

        });

      }

      // 5] Если аутентификационная кука пуста, или её содержимое не валидно
      else {



      }






      // 1] Получаем параметры времени жизни аутентификации из конфига
      // 2] Ищем аутентификационный кэш в сессии
      // 3] Ищем аутентификационную куку
      // 4] Ищем валидную аутентификационную запись в auth
      // 5] Ищем анонимного пользователя

      // 6] Записываем аутентификационный кэш в сессию
      // 7] Записываем аутентификационную куку





    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C56_meet from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C56_meet']);
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

