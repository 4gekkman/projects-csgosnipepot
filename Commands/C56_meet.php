<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Meet users in after middleware of M5, and write auth data to session cache and to cookie
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
     *  1. Проверить аутентификационный кэш из сессии
     *  2. Получить аутентификационную куку
     *  3. Провести валидацию аутентификационной куки
     *  4. Если аутентификационная кука не пуста и валидна
     *  5. Если аутентификационная кука пуста, или её содержимое не валидно
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
          ничего не делает.

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
    $res = call_user_func(function() { try {

      // 1. Проверить аутентификационный кэш из сессии

        // 1.1. Получить аутентификационный кэш из сессии
        $auth_cache = session('auth_cache');

        // 1.2. Если он не пуст и валиден, завершить работу функции

          // 1.2.1. Проверить, валиден ли кэш
          $is_cache_valid = call_user_func(function() USE ($auth_cache) {

            // 1] Провести валидацию строки $auth_cache
            $validator = r4_validate(['auth_cache' => $auth_cache], [
              "auth_cache"      => ["required", "json"],
            ]); if($validator['status'] == -1)
              return false;

            // 2] Получить массив из $auth_cache
            $auth_cache_arr = json_decode($auth_cache, true);

            // 3] Если в $auth_cache_arr нет поля user, значит не валиден
            if(!array_key_exists('user', $auth_cache_arr) || !array_key_exists('auth', $auth_cache_arr))
              return false;

            // 4] Провести валидацию $auth_cache_arr['user']
            $validator = r4_validate($auth_cache_arr['user'], [
              "id"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
            ]); if($validator['status'] == -1) {
              throw new \Exception($validator['data']);
            }

            // 5] Проверить, существует ли в БД пользователь с таким ID, и с валидной аутентификационной записью
            $user = \M5\Models\MD1_users::where('id', $auth_cache_arr['user']['id'])
                ->whereHas('auth', function($query){
                  $query->whereDate('expired_at', '>', \Carbon\Carbon::now()->toDateTimeString());
                })->count();
            if($user == 0)
              return false;
write2log($user, []);
            // n] Вернуть true
            return true;

          });

          // 1.2.2. Если кэш валиден, завершить
          if($is_cache_valid)
            return [
              "status"  => 0,
              "data"    => ""
            ];

      // 2. Получить аутентификационную куку
      $auth_cookie = Request::cookie('auth');
      //if(!empty($auth_cookie)) $auth_cookie = Crypt::decrypt($auth_cookie);

      // 3. Провести валидацию аутентификационной куки
      // - Если она не пуста
      $is_auth_cookie_valid = call_user_func(function() USE ($auth_cookie) {

        // 3.1. Если кука пуста, значит она не валидна
        if(empty($auth_cookie)) return false;

        // 3.2. Удостовериться, что кука содержит валидный json
        $validator = r4_validate(["auth" => $auth_cookie], [
          "auth"              => ["required", "json"],
        ]); if($validator['status'] == -1) {
          return false;
        }

        // 3.3. Провести валидацию важного для meet содержимого этого json
        $validator = r4_validate(json_decode($auth_cookie, true), [
          "user"              => ["required", "array"],
          "user.id"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "auth"              => ["required", "array"],
          "auth.id"           => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "is_anon"           => ["required", "regex:/^[01]{1}$/ui"],
        ]); if($validator['status'] == -1) {
          return false;
        }

        // 3.4. Получить массив из $auth_cache
        $auth_cookie_arr = json_decode($auth_cookie, true);

        // 3.5. Если в $auth_cache_arr нет поля user, значит не валиден
        if(!array_key_exists('user', $auth_cookie_arr) || !array_key_exists('auth', $auth_cookie_arr))
          return false;

        // 3.6. Проверить, существует ли в БД пользователь с таким ID
        $user = \M5\Models\MD1_users::where('id', $auth_cookie_arr['user']['id'])
            ->whereHas('auth', function($query){
              $query->whereDate('expired_at', '>', \Carbon\Carbon::now()->toDateTimeString());
            })->count();
        if($user == 0)
          return false;

        // 3.n. Вернуть true (успешная валидация)
        return true;

      });

      // 4. Если аутентификационная кука не пуста и валидна
      if($is_auth_cookie_valid == true) {

        // 4.1. Декодировать json-строку с данными аутентиф.куки в массив
        $auth_cookie_arr = json_decode($auth_cookie, true);

        // 4.2. Извлечь ID пользователя и ID аутентиф.записи из $auth_cookie_arr
        $auth_cookie_user_id = $auth_cookie_arr['user']['id'];
        $auth_cookie_auth_id = $auth_cookie_arr['auth']['id'];

        // 4.3. Попробовать найти валидную аутентиф.запись по данным из куки
        $auth_note = \M5\Models\MD8_auth::where('id', $auth_cookie_auth_id)->whereHas('users', function($query) USE ($auth_cookie_user_id) {
          $query->where('id', $auth_cookie_user_id);
        })->first();

        // 4.4. Проверить, валидна ли $auth_note
        $is_valid_auth_note = call_user_func(function() USE ($auth_cookie, $auth_cookie_arr, $auth_note, $auth_cookie_user_id) {

          // 1] Если $auth_cookie_arr['is_anon'] == 1, вернуть 525600
          if($auth_cookie_arr['is_anon'] == 1) return 525600;

          // 2] Если $auth_note пуста, вернуть false
          if(empty($auth_note)) return false;

          // 3] Получить время жизни аутентификации для пользователя $auth_cookie_user_id в часах
          $lifetime = runcommand('\M5\Commands\C57_get_auth_limit', ['id_user' => $auth_cookie_user_id]);
          if($lifetime['status'] != 0)
            throw new \Exception($lifetime['data']);
          $lifetime = $lifetime['data'];

          // 4] Получить Carbon-объект с датой и временем создания $auth_note
          $created_at = $auth_note->created_at;

          // 5] Получить Carbon-объект с текущими серверными датой и временем
          $now = \Carbon\Carbon::now();

          // 6] Получить разницу в минутах между $now и $created_at
          $diff_in_min = $now->diffInMinutes($created_at);

          // 7] Если эта разница больше/равна $lifetime*60, вернуть false
          if($diff_in_min >= $lifetime*60)
            return false;

          // n] Вернуть оставшееся время жизни $auth_note в минутах
          return +$lifetime*60 - +$diff_in_min;

        });

        // 4.5. Если $auth_note валидна
        if($is_valid_auth_note !== false) {

          // 1] Если мы имеем дело с анонимным пользователем
          if($auth_cookie_arr['is_anon'] == 1) {

            // 1.1] Попробовать найти анонимного пользователя
            $anon = collect(\M5\Models\MD1_users::where('isanonymous', 1)->first())->except(['is_blocked', 'adminnote', 'password_hash', 'ha_provider_name', 'ha_provider_uid', 'ha_provider_data', 'created_at', 'updated_at', 'deleted_at']);
            if(count($anon) == 0) {

              // Обнулить аутентификационный кэш в сессии
              Session::forget('auth_cache');

              // Завершить
              return [
                "status"  => 0,
                "data"    => ""
              ];

            }

            // 1.2] Подготовить json-строку (зашифрованную и нет) со свежими аутентификационными данными пользвоателя
            $json = [
              'auth'    => ["id" => 1],
              'user'    => $anon,
              'is_anon' => 1
            ];
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $json_encrypted = Crypt::encrypt($json);

            // 1.3] Записать пользователю новый аутентиф.кэш в сессию
            // - В сессию пишем не зашифрованный json
            session(['auth_cache' => $json]);

            // 1.4] Записать пользователю новую куку
            // - С временем жизни 525600 минут.
            // - В куку пишем зашифрованный json
            Cookie::queue('auth', $json, 525600);

          }

          // 2] Если мы имеем дело не с анонимным пользователем
          if($auth_cookie_arr['is_anon'] == 0) {

            // 2.1] Подготовить json-строку (зашифрованную и нет) со свежими аутентификационными данными пользвоателя
            $auth_cookie_user = collect(\M5\Models\MD1_users::find($auth_cookie_user_id))->except(['is_blocked', 'adminnote', 'password_hash', 'ha_provider_name', 'ha_provider_uid', 'ha_provider_data', 'created_at', 'updated_at', 'deleted_at']);
            $json = [
              'auth'    => $auth_note,
              'user'    => $auth_cookie_user,
              'is_anon' => 0
            ];
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $json_encrypted = Crypt::encrypt($json);

            // 2.2] Записать пользователю новый аутентиф.кэш в сессию
            // - В сессию пишем не зашифрованный json
            session(['auth_cache' => $json]);

            // 2.3] Записать пользователю новую куку
            // - С временем жизни $is_valid_auth_note минут.
            // - В куку пишем зашифрованный json
            Cookie::queue('auth', $json, $is_valid_auth_note);

          }

        }

        // 4.6] Если $auth_note не валидна, ничего не делать
        else {

        }

      }

      // 5. Если аутентификационная кука пуста, или её содержимое не валидно
      else {

        // 5.1. Попробовать найти анонимного пользователя
        $anon = collect(\M5\Models\MD1_users::where('isanonymous', 1)->first())->except(['is_blocked', 'adminnote', 'password_hash', 'ha_provider_name', 'ha_provider_uid', 'ha_provider_data', 'created_at', 'updated_at', 'deleted_at']);
        if(count($anon) == 0) {

          // Обнулить аутентификационный кэш в сессии
          Session::forget('auth_cache');

          // Завершить
          return [
            "status"  => 0,
            "data"    => ""
          ];

        }

        // 5.2. Подготовить json-строку (зашифрованную и нет) со свежими аутентификационными данными пользвоателя
        $json = [
          'auth'    => ["id" => 1],
          'user'    => $anon,
          'is_anon' => 1
        ];
        $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        $json_encrypted = Crypt::encrypt($json);

        // 5.3. Записать пользователю новый аутентиф.кэш в сессию
        // - В сессию пишем не зашифрованный json
        session(['auth_cache' => $json]);

        // 5.4. Записать пользователю новую куку
        // - С временем жизни 525600 минут.
        // - В куку пишем зашифрованный json
        Cookie::queue('auth', $json, 525600);

      }

    } catch(\Exception $e) {
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

