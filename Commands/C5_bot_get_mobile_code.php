<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get mobile authentication code for specified bot and time.
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot,     // ID бота, для которого надо сгенерировать код
 *        time        // временная метка, для которой надо сгенерировать код
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
 *  Как определить время действия сгенерированного кода?
 *  ----------------------------------------------------
 *    - Время действия любого кода 30 секунд.
 *    - Ориентация идёт по точному серверному времени.
 *    - Новые коды генерятся 2 раза в минуту, в начале соотв.промежутка времени.
 *    - Вот эти промежутки в секундах:
 *
 *        0   -  29
 *        30  -  59
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
class C5_bot_get_mobile_code extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти указанного бота
     *  3. Провести валидацию необходимых для генерации кода свойств бота
     *  4. Подготовить всё необходимое для генерации кода
     *  5. Узнать разницу между временем локального сервера и сервера steam
     *  6. Сгенерировать код для $bot и $time (с учётом $difference)
     *  7. Рассчитать, через сколько секунд данный код устареет
     *  8. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------------------//
    // Получить код мобильного аутентификатора для указанных бота и времени //
    //----------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "time"                => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти указанного бота
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception("Can't find bot with id == ".$this->data['id_bot']);

      // 3. Провести валидацию необходимых для генерации кода свойств бота
      $validator = r4_validate($bot->toArray(), [
        "shared_secret"   => ["required"]
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 4. Подготовить всё необходимое для генерации кода
      $time                   = $this->data['time'];
      $sharedSecret           = $bot->shared_secret;
      $codeTranslations       = [50, 51, 52, 53, 54, 55, 56, 57, 66, 67, 68, 70, 71, 72, 74, 75, 77, 78, 80, 81, 82, 84, 86, 87, 88, 89];
      $codeTranslationsLength = 26;

      // 5. Узнать разницу между временем локального сервера и сервера steam
      $difference = call_user_func(function(){

        // 1] Подготовить новый экземпляр Guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 2] Выполнить POST-запрос к Steam
        $response = $guzzle->post('http://api.steampowered.com/ITwoFactorService/QueryTime/v0001');

        // 3] Если статус ответа не 200, возбудить исключение
        if($response->getStatusCode() != 200)
          throw new \Exception("Can't get time info from Steam server. Response code != 200.");

        // 4] Извлечь тело ответа
        $body = json_decode($response->getBody(), true);

        // 5] Вычислить и вернуть разницу в секундах
        return +$body['response']['server_time'] - +time();

      });

      // 6. Сгенерировать код для $bot и $time (с учётом $difference)
      $code = call_user_func(function() USE ($time, $difference, $sharedSecret, $codeTranslations, $codeTranslationsLength) {

        $intToByte = function($int){
            return $int & (0xff);
        };
        $time = $time + $difference;
        $sharedSecret = base64_decode($sharedSecret);
        $timeHash = pack('N*', 0) . pack('N*', floor($time / 30));
        $hmac = unpack('C*', pack('H*', hash_hmac('sha1', $timeHash, $sharedSecret, false)));
        $hashedData = [];
        $modeIndex = 0;
        foreach ($hmac as $value) {
            $hashedData[$modeIndex] = $intToByte($value);
            $modeIndex++;
        }
        $b = $intToByte(($hashedData[19] & 0xF));
        $codePoint = ($hashedData[$b] & 0x7F) << 24 | ($hashedData[$b+1] & 0xFF) << 16 | ($hashedData[$b+2] & 0xFF) << 8 | ($hashedData[$b+3] & 0xFF);
        $code = '';
        for ($i = 0; $i < 5; ++$i) {
            $code .= chr($codeTranslations[$codePoint % $codeTranslationsLength]);
            $codePoint /= $codeTranslationsLength;
        }
        return $code;

      });

      // 7. Рассчитать, через сколько секунд данный код устареет
      $expires = call_user_func(function(){

        // 1] Получить секундную составляющую текущего времени
        $seconds = \Carbon\Carbon::now()->second;

        // 2] Если $seconds от 0 до 29
        if($seconds >= 0 && $seconds <= 29) {
          return 29 - $seconds;
        }

        // 3] Если $seconds от 30 до 59
        else {
          return 59 - $seconds;
        }

      });

      // 8. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "code"              => $code,      // сгенерированный код
          "expires_in_secs"   => $expires,   // через сколько секунд данный код устареет
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C5_bot_get_mobile_code from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C5_bot_get_mobile_code']);
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

