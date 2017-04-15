<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Apply nick promo
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_user
 *        steamid
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

  namespace M17\Commands;

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
class C1_apply_nick_promo extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести проверку входящих данных
     *  2. Проверить, не слишком ли часть пользователь id_user запрашивает
     *  3. Получить из конфига массив строк, на которые надо проверять ник игрока
     *  4. Если у id_user уже есть промо, завершить с ошибкой
     *  5. Есть ли в нике пользователя steamid строки из $strings2check
     *  6. Если $is_strings2check_in_nickname == 0, вернуть ошибку
     *  7. Если $is_strings2check_in_nickname == 1
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------//
    // Apply nick promo //
    //------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести проверку входящих данных
      $validator = r4_validate($this->data, [
        "id_user"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "steamid"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Проверить, не слишком ли часть пользователь id_user запрашивает
      // - Давать запрашивать не чаще, чем раз в 1 минуту.

        // 1] Получить из кэша дату и время последней попытки
        $last_try_datetime = Cache::get('m17:c1_apply_nick_promo:lasttry:datetime:'.$this->data['id_user']);

        // 2] Если $last_try_datetime не пуста, и прошло менее 60 секунд, завершить с ошибкой
        if(!empty($last_try_datetime) && +(\Carbon\Carbon::parse($last_try_datetime)->diffInSeconds(\Carbon\Carbon::now())) < 60)
          throw new \Exception("5");

        // 3] Обновить кэш
        Cache::put('m17:c1_apply_nick_promo:lasttry:datetime:'.$this->data['id_user'], \Carbon\Carbon::now()->toDateTimeString(), 300);

      // 3. Получить из конфига необходимые данные

        // 3.1. Массив строк, на которые надо проверять ник игрока
        $strings2check = config('M17.strings2check') ?: [];

        // 3.2. Получить из конфига кол-во монет, которые буду выданы за добавление строки в ник
        $coins = config('M17.coins') ?: 20;

      // 4. Если у id_user уже есть промо, завершить с ошибкой
      $promo = \M17\Models\MD1_nickpromo::whereHas('m5_users', function($queue){
        $queue->where('id', $this->data['id_user']);
      })->first();
      if(!empty($promo))
        throw new \Exception("4");

      // 5. Есть ли в нике пользователя steamid строки из $strings2check
      $is_strings2check_in_nickname = runcommand('\M17\Commands\C3_check_strings_in_nickname', [
        'steamid'       => $this->data['steamid'],
        'strings2check' => $strings2check
      ]);
      if($is_strings2check_in_nickname['status'] != 0)
        throw new \Exception($is_strings2check_in_nickname['data']['errormsg']);
      else
        $is_strings2check_in_nickname = $is_strings2check_in_nickname['data']['result'];

      // 6. Если $is_strings2check_in_nickname == 0, вернуть ошибку
      if($is_strings2check_in_nickname == 0)
        throw new \Exception("2");

      // 7. Если $is_strings2check_in_nickname == 1
      else {

        // 7.1. Создать новый промо для пользователя
        $promo = new \M17\Models\MD1_nickpromo();
        $promo->coins = $coins;
        $promo->save();

        // 7.2. Связать $promo с id_user
        if(!$promo->m5_users->contains($this->data['id_user']))
          $promo->m5_users()->attach($this->data['id_user']);

        // 7.3. Обновить ник пользователя
        $user = \M5\Models\MD1_users::find($this->data['id_user']);
        if(empty($user))
          throw new \Exception('5');
        $user->nickname = $is_strings2check_in_nickname['data']['nickname'];

        // 7.4. Начислить пользователю id_user $coins монет
        $result = runcommand('\M13\Commands\C13_add_coins', [
          'id_user'     => $this->data['id_user'],
          'coins'       => $coins,
          'description' => "Nickname Promo #".$promo['id']
        ]);
        if($result['status'] != 0)
          throw new \Exception("3");

        // 7.5. Сделать commit
        DB::commit();

        // 7.n. Вернуть результаты
        return [
          "status"  => 0,
          "data"    => [
            'coins'    => $coins,
            'is_paid'  => 1
          ]
        ];

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_apply_nick_promo from M-package M17 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M17', 'C1_apply_nick_promo']);
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

