<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create and send new trade offer
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot                | ID бота, который будет отправлять торговое предложение
 *        id_partner            | ID партнёра, которому будет отправлено торговое предложение
 *        token_partner         | Токен партнёра, которому будет отправлено торговое предложение
 *        dont_trade_with_gays  | Не торговать с партнёрами, у которых trade hold > 0
 *        assets2send           | assetid вещей, которые бот хочет отдать
 *        assets2recieve        | assetid вещей, которые бот хочет получить
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
class C25_new_trade_offer extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Подготовить параметры запроса
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------//
    // Создать и отправить новое торговое предложение //
    //------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [

        "id_bot"                => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_partner"            => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "token_partner"         => ["required", "string"],
        "dont_trade_with_gays"  => ["required", "regex:/^[01]{1}$/ui"],
        "assets2send"           => ["sometimes", "array"],
        "assets2send.*"         => ["sometimes", "regex:/^[0-9]+$/ui"],
        "assets2recieve"        => ["sometimes", "array"],
        "assets2recieve.*"      => ["sometimes", "regex:/^[0-9]+$/ui"],

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти модель бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception('Не удалось найти бота с ID = '.$this->data['id_bot']);

      // 3. Написать функцию для преобразования partnerid хэша для
      $get_partner_hash = function($id) {
        if (preg_match('/^STEAM_/', $id)) {
          $parts = explode(':', $id);
          return bcadd(bcadd(bcmul($parts[2], '2'), '76561197960265728'), $parts[1]);
        } elseif (is_numeric($id) && strlen($id) < 16) {
          return bcadd($id, '76561197960265728');
        } else {
          return $id; // We have no idea what this is, so just return it.
        }
      };

      // 4. Проверить escrow hold свой и торгового партнёра
      // - Отправлять торговое предложение, только если всё по нулям
      if($this->data['dont_trade_with_gays'] == 1) {

        // 4.1. Отправить запрос и получить ответ
        $result = runcommand('\M8\Commands\C23_check_escrow_hold_days', [
          'id_bot'    => $bot->id,
          'partner'   => $this->data['id_partner'],
          'token'     => $this->data['token_partner']
        ]);

        // 4.2. Если торговать нельзя, завершить
        if($result['status'] != 0 || $result['data']['could_trade'] == 0) {

          return [
            "status"  => 0,
            "data"    => [
              "could_trade" => 0
            ]
          ];

        }

      }

      // 5. Подготовить параметры запроса
      $params = call_user_func(function() USE ($bot, $get_partner_hash) {

        // 1] Подготовить массив для результата
        $results = [];

        // 2] Подготовить значение для me
        $me = call_user_func(function(){
          $results = [];
          foreach($this->data['assets2send'] as $asset) {
            $results[] = [
              'appid' => (int)730,
              'contextid' => 2,
              'assetid' => $asset,
              'amount' => (int)1
            ];
          }
          return $results;
        });

        // 3] Подготовить значение для them
        $them = call_user_func(function(){
          $results = [];
          foreach($this->data['assets2recieve'] as $asset) {
            $results[] = [
              'appid' => (int)730,
              'contextid' => 2,
              'assetid' => $asset,
              'amount' => (int)1
            ];
          }
          return $results;
        });

        // 4] Наполнить $results
        $results['sessionid']                   = $bot->sessionid;
        $results['serverid']                    = 1;
        $results['partner']                     = $get_partner_hash($this->data['id_partner']);
        $results['tradeoffermessage']           = "";
        $results['trade_offer_create_params']   = json_encode(['trade_offer_access_token' => $this->data['token_partner']]);
        $results['json_tradeoffer']             = json_encode([
          'newversion'  => true,
          'version'     => 1,
          'me'          => $me,
          'them'        => $them
        ]);

        // n] Вернуть результаты
        return $results;

      });

      // 6. Осуществить запрос к steam и создать новое торговое предложение

        // 5.1. Запросить
        $response = call_user_func(function() USE ($bot, $params){

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $bot->id,
            "method"          => "POST",
            "url"             => "https://steamcommunity.com/tradeoffer/new/send",
            "cookies_domain"  => 'steamcommunity.com',
            "data"            => $params,
            "ref"             => 'https://steamcommunity.com/tradeoffer/new/?partner=' . $this->data['id_partner'] . '&token=' . $this->data['token_partner']
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });

        // 5.2. Если код ответа не 200, сообщить и завершить
        if($response->getStatusCode() != 200)
          throw new \Exception('Unexpected response from Steam: code '.$response->getStatusCode());

        // 5.3. Провести валидацию $response->getBody()
        $validator = r4_validate(['body'=>$response->getBody()], [
          "body"              => ["required", "json"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // 5.4. Получить из $response строку с HTML из ответа
        $json = json_decode($response->getBody(), true);


        write2log($json, []);







      // TradeAsset
      // - appid          | = 730
      // - contextid      | = 2
      // - assetid
      // - amount = 1

      // TradeUser
      // - assets         | TradeAsset json (из массива)
      // - currency       | = []
      // - ready          | = true

      // json_tradeoffer
      // - newversion     | = true
      // - version        | = 1
      // - me             | TraseUser json
      // - them           | TraseUser json








    } catch(\Exception $e) {
        $errortext = 'Invoking of command C25_new_trade_offer from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C25_new_trade_offer']);
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

