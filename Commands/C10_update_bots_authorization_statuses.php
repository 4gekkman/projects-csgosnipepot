<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Invokes every hour and update authorization statuses of the all bots.
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
class C10_update_bots_authorization_statuses extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить коллекцию всех ботов
     *  2. Обновить статусы авторизации всех ботов, осуществить авто-авторизацию
     *    2.1. Узнать, авторизован ли бот
     *    2.2. Если бот авторизован, обновить информацию об авторизации бота
     *    2.3. Если бот не авторизован, попытаться его автоматически авторизовать
     *  3. Транслировать клиентам через websocket свежие данные об авторизации ботов
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------//
    // Ежечасно обновляет статусы авторизации всех ботов //
    //---------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить коллекцию всех ботов
      $bots = \M8\Models\MD1_bots::query()->get();

      // 2. Обновить статусы авторизации всех ботов, осуществить авто-авторизацию
      foreach($bots as $bot) {

        // 2.1. Узнать, авторизован ли бот
        // - Если узнать это не удалось, завершить.
        $result = runcommand('\M8\Commands\C7_bot_get_sessid_steamid', [
          'id_bot'          => $bot->id,
          'method'          => 'GET',
          'cookies_domain'  => 'steamcommunity.com'
        ]);
        if($result['status'] != 0) {
          $bot->authorization = 0;
          $bot->authorization_last_update = (string) \Carbon\Carbon::now();
          $bot->authorization_status_last_bug = "Can't update the bots authorization status, because: ".$result['data']['errormsg'];
          $bot->save();
          continue;
        } else {
          $bot->authorization_status_last_bug = "";   // Багов при проверке статуса авторизации нет.
          $bot->save();
        }

        // 2.2. Если бот авторизован, обновить информацию об авторизации бота
        if($result['data']['is_bot_authenticated']) {

          // 1] Информация о последнем баге авторизации
          $bot->authorization_last_bug = "";
          $bot->authorization_status_last_bug = "";
          $bot->authorization_last_bug_code = "";

          // 2] Прочая информация
          $bot->authorization               = 1;
          $bot->authorization_last_update   = (string) \Carbon\Carbon::now();
          $bot->authorization_used_attempts = "0";
          $bot->sessionid = $result['data']['sessionid'];

          // 3] Сохранить
          $bot->save();

        }

        // 2.3. Если бот не авторизован, обновить информацию, и попытаться его автоматически авторизовать
        if(!$result['data']['is_bot_authenticated']) {

          // 1] Обновить информацию
          $bot->authorization = 0;
          $bot->authorization_status_last_bug = "";
          $bot->authorization_last_update = (string) \Carbon\Carbon::now();
          $bot->save();

          // 2] Получить из конфига значение MAX числа попыток авто.авторизации
          $max_attempts = config('M8.max_num_of_auto_authorization_attempts') ?: 2;
          if(!is_numeric($max_attempts)) $max_attempts = 2;

          // 3] Если у $bot ещё остались попытки авто.авторизации
          if(gmp_cmp($bot->authorization_used_attempts, $max_attempts) <= 0) {

            // 3.1] Осуществить попытку авторизации
            //$result = runcommand('\M8\Commands\C8_bot_login', [
            //  "id_bot"          => $bot->id,
            //  "relogin"         => "0",
            //  "captchagid"      => "0",
            //  "captcha_text"    => "0",
            //  "method"          => "GET",
            //  "cookies_domain"  => "steamcommunity.com"
            //]);
            //
            //// 3.2] Если авторизация не удалась
            //if($result['status'] != 0)
            //  continue;
            //
            //// 3.3] Если авторизация удалась
            //else
            //  continue;

            continue;
          }

        }

      }

      // 3. Транслировать клиентам через websocket свежие данные об авторизации ботов
      //Event::fire(new \R2\Broadcast([
      //  'channels' => ['m8:update_bots_authorization_statuses'],
      //  'queue'    => 'smallbroadcast',
      //  'data'     => [
      //    'bots' => \M8\Models\MD1_bots::query()->get()
      //  ]
      //]));


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C10_update_bots_authorization_statuses from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C10_update_bots_authorization_statuses']);
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

