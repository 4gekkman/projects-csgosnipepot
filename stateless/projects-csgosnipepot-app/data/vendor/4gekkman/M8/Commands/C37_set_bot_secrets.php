<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update and crypt unsafe bot properties
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot
 *        password
 *        sessionid
 *        shared_secret
 *        serial_number
 *        revocation_code
 *        uri
 *        server_time
 *        account_name
 *        token_gid
 *        identity_secret
 *        secret_1
 *        device_id
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
class C37_set_bot_secrets extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти бота id_bot
     *  3. Попробовать найти секретные данные, связанные с $bot
     *  4. Обовить свойства бота
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------//
    // Update and crypt unsafe bot properties //
    //----------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "password"        => ["sometimes", "string"],
        "sessionid"       => ["sometimes", "string"],
        "shared_secret"   => ["sometimes", "string"],
        "serial_number"   => ["sometimes", "string"],
        "revocation_code" => ["sometimes", "string"],
        "uri"             => ["sometimes", "string"],
        "server_time"     => ["sometimes", "string"],
        "account_name"    => ["sometimes", "string"],
        "token_gid"       => ["sometimes", "string"],
        "identity_secret" => ["sometimes", "string"],
        "secret_1"        => ["sometimes", "string"],
        "device_id"       => ["sometimes", "string"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти бота id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception('Не удалось найти в БД бота с ID = '.$this->data['id_bot']);

      // 3. Попробовать найти секретные данные, связанные с $bot
      $secrets = \M8\Models\MD11_secrets::whereHas('bots', function($queue) USE ($bot) {
        $queue->where('id', $bot['id']);
      })->first();
      if(empty($secrets))
        throw new \Exception('Не удалось найти в БД секретные данные для бота с ID = '.$this->data['id_bot']);

      // 4. Обовить свойства бота, зашифровав значения
      if(array_key_exists('password', $this->data))         $secrets->password         = r1_encrypt_data(encrypt($this->data['password']), env("SECRETS_KEY"));
      if(array_key_exists('sessionid', $this->data))        $secrets->sessionid        = r1_encrypt_data(encrypt($this->data['sessionid']), env("SECRETS_KEY"));
      if(array_key_exists('shared_secret', $this->data))    $secrets->shared_secret    = r1_encrypt_data(encrypt($this->data['shared_secret']), env("SECRETS_KEY"));
      if(array_key_exists('serial_number', $this->data))    $secrets->serial_number    = r1_encrypt_data(encrypt($this->data['serial_number']), env("SECRETS_KEY"));
      if(array_key_exists('revocation_code', $this->data))  $secrets->revocation_code  = r1_encrypt_data(encrypt($this->data['revocation_code']), env("SECRETS_KEY"));
      if(array_key_exists('uri', $this->data))              $secrets->uri              = r1_encrypt_data(encrypt($this->data['uri']), env("SECRETS_KEY"));
      if(array_key_exists('server_time', $this->data))      $secrets->server_time      = r1_encrypt_data(encrypt($this->data['server_time']), env("SECRETS_KEY"));
      if(array_key_exists('account_name', $this->data))     $secrets->account_name     = r1_encrypt_data(encrypt($this->data['account_name']), env("SECRETS_KEY"));
      if(array_key_exists('token_gid', $this->data))        $secrets->token_gid        = r1_encrypt_data(encrypt($this->data['token_gid']), env("SECRETS_KEY"));
      if(array_key_exists('identity_secret', $this->data))  $secrets->identity_secret  = r1_encrypt_data(encrypt($this->data['identity_secret']), env("SECRETS_KEY"));
      if(array_key_exists('secret_1', $this->data))         $secrets->secret_1         = r1_encrypt_data(encrypt($this->data['secret_1']), env("SECRETS_KEY"));
      if(array_key_exists('device_id', $this->data))        $secrets->device_id        = r1_encrypt_data(encrypt($this->data['device_id']), env("SECRETS_KEY"));
      $secrets->save();

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C37_set_bot_secrets from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C37_set_bot_secrets']);
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
