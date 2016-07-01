<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get ID of trade offer of confirmation
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
class C22_get_confirmation_tradeoffer_id extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти модель бота с id_bot
     *  3. Проверить наличие у бота непустых device_id, steamid и identity_secret
     *  4. Получить серверное время Steam
     *  5. Получить хэш подтверждения для $tag = details и $time
     *  6. Подготовить параметры для запроса
     *  7. Запросить у Steam html с подробностями об указанном подтверждении
     *  8. Извлечь из $json['html'] id торгового предложения этого подтверждения
     *  9. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------//
    // Получить ID торгового предложения подтверждения //
    //-------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"            => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_confirmation"   => ["required", "regex:/^[1-9]+[0-9]*$/ui"]
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти модель бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception('Не удалось найти бота с ID = '.$this->data['id_bot']);

      // 3. Проверить наличие у бота непустых device_id, steamid и identity_secret

        // 3.1. Проверить device_id
        if(empty($bot->device_id))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой device_id');

        // 3.2. Проверить steamid
        if(empty($bot->steamid))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой steamid');

        // 3.3. Проверить identity_secret
        if(empty($bot->identity_secret))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой identity_secret');

      // 4. Получить серверное время Steam
      $time = runcommand('\M8\Commands\C20_getsteamtime');
      if($time['status'] != 0 || !array_key_exists('data', $time) || !array_key_exists('steamtime', $time['data']))
        throw new \Exception('Не удалось получить серверное время Steam для бота с ID = '.$this->data['id_bot']);
      else $time = $time['data']['steamtime'];

      // 5. Получить хэш подтверждения для $tag = details и $time
      $conformation_hash_for_time = call_user_func(function() USE ($bot, $time) {

        $tag = 'details';
        $identitySecret = base64_decode($bot->identity_secret);
        $array = $tag ? substr($tag, 0, 32) : '';
        for ($i = 8; $i > 0; $i--) {
            $array = chr($time & 0xFF) . $array;
            $time >>= 8;
        }
        $code = hash_hmac("sha1", $array, $identitySecret, true);
        return base64_encode($code);

      });

      // 6. Подготовить параметры для запроса
      $params = call_user_func(function() USE ($bot, $time, $conformation_hash_for_time) {

        return [
          "p"       => $bot->device_id,
          "a"       => $bot->steamid,
          "k"       => $conformation_hash_for_time,
          "t"       => $time,
          "m"       => "android",
          "tag"     => "details"
        ];

      });

      // 7. Запросить у Steam html с подробностями об указанном подтверждении

        // 7.1. Запросить
        $conformation_details = call_user_func(function() USE ($bot, $params){

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $bot->id,
            "method"          => "GET",
            "url"             => "https://steamcommunity.com/mobileconf/details/".$this->data['id_confirmation'],
            "cookies_domain"  => 'steamcommunity.com',
            "data"            => $params,
            "ref"             => ""
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });

        // 7.2. Если код ответа не 200, сообщить и завершить
        if($conformation_details->getStatusCode() != 200)
          throw new \Exception('Unexpected response from Steam: code '.$conformation_details->getStatusCode());

        // 7.3. Получить из $response строку с HTML из ответа
        $json = json_decode($conformation_details->getBody(), true);;

        // 7.4. Если $json['success'] != true, возбудить исключение
        if(!array_key_exists('success', $json) || $json['success'] == false)
          throw new \Exception('В ответ на запрос деталей для потдверждения с ID = '.$this->data['id_confirmation'].', для бота с ID = '.$this->data['id_bot'].', пришёл json с success = false.');

        // 7.5. Проверить, есть ли поле html в $json
        if(!array_key_exists('html', $json) || empty($json['html']))
          throw new \Exception('В ответ на запрос деталей для потдверждения с ID = '.$this->data['id_confirmation'].', для бота с ID = '.$this->data['id_bot'].', пришёл json с отсутствующимили пустым полем html.');

      // 8. Извлечь из $json['html'] id торгового предложения этого подтверждения
      $id_tradeoffer = call_user_func(function() USE ($json) {

        $html = $json['html'];
        if (preg_match('/<div class="tradeoffer" id="tradeofferid_(\d+)" >/', $html, $matches)) {
          return $matches[1];
        } else return "";

      });

      // 9. Вернуть результаты


      write2log($id_tradeoffer, []);



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C22_get_confirmation_tradeoffer_id from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C22_get_confirmation_tradeoffer_id']);
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

