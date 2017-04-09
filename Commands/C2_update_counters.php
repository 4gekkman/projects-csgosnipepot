<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update counters of users online
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

  namespace M16\Commands;

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
class C2_update_counters extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из redis все существующие метки онлайна
     *  2. Из $marks_keys извлечь массив ID пользователей, которые онлайн
     *  3. Обновить счетчик онлайна для каждого из $id_users
     *    3.1. Записать ключ счётчика для $id_user в переменную
     *    3.2. Попробовать найти в redis счетчик онлайна игрока $id_user
     *    3.3. Если $counter пуст, создать его со значением 1
     *    3.4. Если $counter не пуст
     *  4. Получить из redis все существующие счётчики онлайна
     *  5. Из $online_keys извлечь массив ID пользователей, у которых есть счётчики онлайна
     *  6. Найти ID всех пользователей, которые есть в $id_users_with_counters, но нет в $id_users
     *  7. Сколько секунд нужно быть оффлайн, чтобы сбросился счётчик онлайна
     *  8. Обновить счетчик оффлайна для каждого из $id_users_recently_online
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------//
    // Update counters of users online //
    //---------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить из redis все существующие метки онлайна
      // - Они начинаются с "m16:online:mark:"
      $marks_keys = Redis::executeRaw(['KEYS', 'm16:online:mark:*']);

      // 2. Из $marks_keys извлечь массив ID пользователей, которые онлайн
      $id_users = call_user_func(function() USE ($marks_keys) {

        $results = [];
        foreach($marks_keys as $value) {
          $exploded = explode(':', $value);
          $id = $exploded[count($exploded) - 1];
          if(is_numeric($id) && !in_array($id, $results))
            array_push($results, $id);
        }
        return $results;

      });

      // 3. Обновить счетчик онлайна для каждого из $id_users
      foreach($id_users as $id_user) {

        // 3.1. Записать ключ счётчика для $id_user в переменную
        $key = 'm16:online:counter:'.$id_user;

        // 3.2. Попробовать найти в redis счетчик онлайна игрока $id_user
        $counter = Redis::get($key);

        // 3.3. Если $counter пуст, создать его со значением 1
        if(empty($counter))
          Redis::set($key, 1);

        // 3.4. Если $counter не пуст
        else {

          // 1] Прибавить к нему 1
          $counter = +$counter + 1;

          // 2] Если в итоге получилось число, перезаписать в redis
          if(is_numeric($counter))
            Redis::set($key, $counter);

          // 3] Если в итоге получилось не число, удалить счётчик
          else
            Redis::executeRaw(['DEL', $key]);

        }

        // 3.5. Удалить счётчик оффлайна
        Redis::executeRaw(['DEL', 'm16:offline:counter:'.$id_user]);

      }

      // 4. Получить из redis все существующие счётчики онлайна
      $online_keys = Redis::executeRaw(['KEYS', 'm16:online:counter:*']);

      // 5. Из $online_keys извлечь массив ID пользователей, у которых есть счётчики онлайна
      $id_users_with_counters = call_user_func(function() USE ($online_keys) {

        $results = [];
        foreach($online_keys as $value) {
          $exploded = explode(':', $value);
          $id = $exploded[count($exploded) - 1];
          if(is_numeric($id) && !in_array($id, $results))
            array_push($results, $id);
        }
        return $results;

      });

      // 6. Найти ID всех пользователей, которые есть в $id_users_with_counters, но нет в $id_users
      $id_users_recently_online = array_diff($id_users_with_counters, $id_users);

      // 7. Сколько секунд нужно быть оффлайн, чтобы сбросился счётчик онлайна
      $drop_sec = config("M16.offline2drop_online_counter_sec") ?: 180;

      // 8. Обновить счетчик оффлайна для каждого из $id_users_recently_online
      foreach($id_users_recently_online as $id_user) {

        // 8.1. Записать ключ счётчика для $id_user в переменную
        $key = 'm16:offline:counter:'.$id_user;

        // 8.2.  Попробовать найти в redis счетчик оффлайна игрока $id_user
        $counter = Redis::get($key);

        // 8.3. Если $counter пуст, создать его со значением 1
        if(empty($counter)) {
          $counter = 1;
          Redis::set($key, $counter);
        }

        // 8.4. Если $counter не пуст
        else {

          // 1] Прибавить к нему 1
          $counter = +$counter + 1;

          // 2] Если в итоге получилось число, перезаписать в redis
          if(is_numeric($counter))
            Redis::set($key, $counter);

          // 3] Если в итоге получилось не число, удалить счётчик
          else
            Redis::executeRaw(['DEL', $key]);

        }

        // 8.5. Если $counter >= $drop_sec, удалить счётчик онлайна пользователя
        if($counter >= $drop_sec) {
          Redis::executeRaw(['DEL', 'm16:online:counter:'.$id_user]);
        }

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C2_update_counters from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M16', 'C2_update_counters']);
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

