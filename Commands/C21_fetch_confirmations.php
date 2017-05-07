<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Fetch all confirmations for specified bot, and (if needed) apply them
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot            // ID бота, с которым работаем
 *        need_to_ids       // 0/1 - нужно ли в каждое $confirmation в $confirmations добавить поле id_tradeoffer с ID торг.предложения, связанного с этим подтверждением
 *        just_fetch_info   // 0/1 - если 1, то подтверждения не будут приняты, а лишь будет извлечена информация
 *        tradeoffer_ids    // Подтверждать только эти офферы, а если отсутствует, тогда все
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
 *  Примечание
 *  ----------
 *    - Может потребоваться запрашивать список подтверждений несколько раз.
 *    - Бывает, что Steam не показывает нужное подтверждение с 1-го раза.
 *
 *  Что возвращает в data
 *  ---------------------
 *
 *    data => [
 *      "confirmations" => [
 *        [
 *          "id"              // ID подтверждения
 *          "key"             // Ключ подтверждения
 *          "description"     // Описание подтверждения
 *          "id_tradeoffer"   // ID торгового предложения подтверждения
 *        ],
 *        [ ... ]
 *      ]
 *    ]
 *
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
class C21_fetch_confirmations extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Проверить наличие у бота непустых device_id и steamid
     *  4. Получить серверное время Steam
     *  5. Получить хэш подтверждения для $tag = conf и $time
     *  6. Подготовить параметры для запроса
     *  7. Запросить у Steam HTML со всеми подтверждениями
     *  8. Извлечь из $html все подтверждения в массив
     *  9. Если требуется, дополнить $confirmations ID-ками торговых операций
     *
     *    // Только если need_to_ids == 1
     *    9.1. Получить хэш подтверждения для $tag = details и $time
     *    9.2. Подготовить параметры для запросов ID торгового предложения
     *    9.3. Для каждого подтверждения узнать ID торгового предложения
     *
     *  10. Принять все подтверждения из $confirmations, если just_fetch_info == 1
     *  11. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------------//
    // Извлечь подтверждения для указанного бота, и если надо, подтвердить их //
    //------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"            => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "need_to_ids"       => ["required", "regex:/^[01]{1}$/ui"],
        "just_fetch_info"   => ["required", "regex:/^[01]{1}$/ui"],
        "tradeoffer_ids"    => ["sometimes", "array"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти модель бота с id_bot, если частота попыток подтвердить офферы для него превышена, завершить

        // 2.1. Попробовать найти
        $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
        if(empty($bot))
          throw new \Exception('Не удалось найти бота с ID = '.$this->data['id_bot']);

        // 2.2. Если частота попыток подтвердить офферы для него превышена превышена, завершить
        // - Не чаще, чем раз в 5 секунд.

          // 1] Получить из кэша дату и время последней попытки
          $last_try_datetime = Cache::get('m8:c21:confirmations:lasttry:datetime:'.$this->data['id_bot']);

          // 2] Если $last_try_datetime не пуста, и прошло менее 55 секунд, завершить по-тихому
          if(!empty($last_try_datetime) && +(\Carbon\Carbon::parse($last_try_datetime)->diffInSeconds(\Carbon\Carbon::now())) < 5) {

            // Завершить
            throw new \Exception('Слишком частые попытки вызвать команду. Должно быть не чаще, чем раз в 5 секунд');

          }

          // 3] Обновить кэш
          Cache::put('m8:c21:confirmations:lasttry:datetime:'.$this->data['id_bot'], \Carbon\Carbon::now()->toDateTimeString(), 300);

        // 2.3. Попробовать найти секретные данные, связанные с $bot
        $secrets = \M8\Models\MD11_secrets::whereHas('bots', function($queue) USE ($bot) {
          $queue->where('id', $bot['id']);
        })->first();
        if(empty($secrets))
          throw new \Exception('Не удалось найти в БД секретные данные для бота с ID = '.$this->data['id_bot']);

        // 2.4. Получить identity_secret бота
        $result = runcommand('\M8\Commands\C38_get_bot_secret', [
          'id_bot' => $bot->id,
          'secret' => 'identity_secret',
          'key'    => env('SECRETS_KEY')
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);
        $identity_secret = $result['data']['value'];

        // 2.5. Получить device_id бота
        $result = runcommand('\M8\Commands\C38_get_bot_secret', [
          'id_bot' => $bot->id,
          'secret' => 'device_id',
          'key'    => env('SECRETS_KEY')
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);
        $device_id = $result['data']['value'];

      // 3. Проверить наличие у бота непустых device_id, steamid и identity_secret

        // 3.1. Проверить device_id
        if(empty($device_id))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой device_id');

        // 3.2. Проверить steamid
        if(empty($bot->steamid))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой steamid');

        // 3.3. Проверить identity_secret
        if(empty($identity_secret))
          throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой identity_secret');

      // 4. Получить серверное время Steam
      $time = runcommand('\M8\Commands\C20_getsteamtime');
      if($time['status'] != 0 || !array_key_exists('data', $time) || !array_key_exists('steamtime', $time['data']))
        throw new \Exception('Не удалось получить серверное время Steam для бота с ID = '.$this->data['id_bot']);
      else $time = $time['data']['steamtime'];

      // 5. Получить хэш подтверждения для $tag = conf и $time
      $conformation_hash_for_time = call_user_func(function() USE ($bot, $time, $identity_secret) {

        $tag = 'conf';
        $identitySecret = base64_decode($identity_secret);
        $array = $tag ? substr($tag, 0, 32) : '';
        for ($i = 8; $i > 0; $i--) {
            $array = chr($time & 0xFF) . $array;
            $time >>= 8;
        }
        $code = hash_hmac("sha1", $array, $identitySecret, true);
        return base64_encode($code);

      });

      // 6. Подготовить параметры для запроса
      $params = call_user_func(function() USE ($bot, $time, $conformation_hash_for_time, $device_id) {

        return [
          "p"       => $device_id,
          "a"       => $bot->steamid,
          "k"       => $conformation_hash_for_time,
          "t"       => $time,
          "m"       => "android",
          "tag"     => "conf"
        ];

      });

      // 7. Запросить у Steam HTML со всеми подтверждениями

        // 7.1. Запросить
        $confirmations_html = call_user_func(function() USE ($bot, $params){

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $bot->id,
            "method"          => "GET",
            "url"             => "https://steamcommunity.com/mobileconf/conf",
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
        if($confirmations_html->getStatusCode() != 200)
          throw new \Exception('Unexpected response from Steam: code '.$confirmations_html->getStatusCode());

        // 7.3. Получить из $response строку с HTML из ответа
        $html = (string) $confirmations_html->getBody();

      // 8. Извлечь из $html все подтверждения в массив
      $confirmations = call_user_func(function() USE ($html, $bot) {

        // 8.1. Подготовить массив для результатов
        $results = [];

        // 8.2. Если в $html нет надписи "Nothing to confirm", наполнить $results
        if(strpos($html, '<div>Nothing to confirm</div>') === false) {
          $confIdRegex = '/data-confid="(\d+)"/';
          $confKeyRegex = '/data-key="(\d+)"/';
          $confDescRegex = '/<div>((Confirm|Trade with|Sell -) .+)<\/div>/';
          preg_match_all($confIdRegex, $html, $confIdMatches);
          preg_match_all($confKeyRegex, $html, $confKeyMatches);
          preg_match_all($confDescRegex, $html, $confDescMatches);
          if(count($confIdMatches[1]) > 0 && count($confKeyMatches[1]) > 0 && count($confDescMatches) > 0) {
            for ($i = 0; $i < count($confIdMatches[1]); $i++) {
              $confId = $confIdMatches[1][$i];
              $confKey = $confKeyMatches[1][$i];
              $confDesc = $confDescMatches[1][$i];
              array_push($results, [
                'id'            => $confId,
                'key'           => $confKey,
                'description'   => $confDesc
              ]);
            }
          } else throw new \Exception('Не удалось получить правильный html-документ с подтверждениями для бота с ID = '.$bot->id);
        }

        // 8.3. Вернуть результаты
        return $results;

      });

      // 9. Если требуется, дополнить $confirmations ID-ками торговых операций
      if($this->data['need_to_ids'] == 1) {

        // 9.1. Получить хэш подтверждения для $tag = details и $time
        $conformation_hash_for_time_details = call_user_func(function() USE ($bot, $time, $identity_secret) {

          $tag = 'details';
          $identitySecret = base64_decode($identity_secret);
          $array = $tag ? substr($tag, 0, 32) : '';
          for ($i = 8; $i > 0; $i--) {
              $array = chr($time & 0xFF) . $array;
              $time >>= 8;
          }
          $code = hash_hmac("sha1", $array, $identitySecret, true);
          return base64_encode($code);

        });

        // 9.2. Подготовить параметры для запросов ID торгового предложения
        $params_4to = call_user_func(function() USE ($bot, $time, $conformation_hash_for_time_details, $device_id) {

          return [
            "p"       => $device_id,
            "a"       => $bot->steamid,
            "k"       => $conformation_hash_for_time_details,
            "t"       => $time,
            "m"       => "android",
            "tag"     => "details"
          ];

        });

        // 9.3. Для каждого подтверждения узнать ID торгового предложения
        // - И добавить соотв.поле в $confirmations
        foreach($confirmations as &$confirmation) {

          // 9.3.1. Запросить
          $conformation_details = call_user_func(function() USE ($bot, $params_4to, $confirmation){

            // 1] Осуществить запрос
            $result = runcommand('\M8\Commands\C6_bot_request_steam', [
              "id_bot"          => $bot->id,
              "method"          => "GET",
              "url"             => "https://steamcommunity.com/mobileconf/details/".$confirmation['id'],
              "cookies_domain"  => 'steamcommunity.com',
              "data"            => $params_4to,
              "ref"             => ""
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

            // 2] Вернуть результаты (guzzle response)
            return $result['data']['response'];

          });

          // 9.3.2. Если код ответа не 200, сообщить и завершить
          if($conformation_details->getStatusCode() != 200)
            throw new \Exception('Unexpected response from Steam: code '.$conformation_details->getStatusCode());

          // 9.3.3. Получить из $response строку с HTML из ответа
          $json = json_decode($conformation_details->getBody(), true);

          // 9.3.4. Если $json['success'] != true, возбудить исключение
          if(!array_key_exists('success', $json) || $json['success'] == false)
            throw new \Exception('В ответ на запрос деталей для потдверждения с ID = '.$confirmation['id'].', для бота с ID = '.$this->data['id_bot'].', пришёл json с success = false.');

          // 9.3.5. Проверить, есть ли поле html в $json
          if(!array_key_exists('html', $json) || empty($json['html']))
            throw new \Exception('В ответ на запрос деталей для потдверждения с ID = '.$confirmation['id'].', для бота с ID = '.$this->data['id_bot'].', пришёл json с отсутствующимили пустым полем html.');

          // 9.3.6. Извлечь из $json['html'] id торгового предложения этого подтверждения
          $id_tradeoffer = call_user_func(function() USE ($json) {

            $html = $json['html'];
            if (preg_match('/<div class="tradeoffer" id="tradeofferid_(\d+)" >/', $html, $matches)) {
              return $matches[1];
            } else return "";

          });

          // 9.3.7. Добавить $id_tradeoffer в $confirmation
          $confirmation['id_tradeoffer'] = $id_tradeoffer;

        }

      }

      // 10. Принять все подтверждения из $confirmations, если just_fetch_info == 1
      // - Если tradeoffer_ids отсутствует, тогда принимать все.
      // - А если присутствует, то только для тех офферов, которые перечислены в tradeoffer_ids.
      if($this->data['just_fetch_info'] == 0) {
        foreach($confirmations as $confirmation) {

          // 11.a. Завершить, если этот оффер на надо подтверждать
          if(array_key_exists('tradeoffer_ids', $this->data) && !in_array($confirmation['id_tradeoffer'], $this->data['tradeoffer_ids']) )
            continue;

          // 10.1. Получить свежее время
          $time_new = time() + $time['data']['difference'];

          // 10.2. Получить хэш подтверждения для $tag = allow и $time_new
          $conformation_hash_for_time_allow = call_user_func(function() USE ($bot, $time_new, $identity_secret) {

            $tag = 'allow';
            $identitySecret = base64_decode($identity_secret);
            $array = $tag ? substr($tag, 0, 32) : '';
            for ($i = 8; $i > 0; $i--) {
                $array = chr($time_new & 0xFF) . $array;
                $time_new >>= 8;
            }
            $code = hash_hmac("sha1", $array, $identitySecret, true);
            return base64_encode($code);

          });

          // 10.3. Подготовить параметры для запросов ID торгового предложения
          $params_4allow = call_user_func(function() USE ($bot, $time_new, $conformation_hash_for_time_allow, $confirmation, $device_id) {

            return [
              "op"      => "allow",
              "p"       => $device_id,
              "a"       => $bot->steamid,
              "k"       => $conformation_hash_for_time_allow,
              "t"       => $time_new,
              "m"       => "android",
              "tag"     => "allow",
              "cid"     => $confirmation['id'],
              "ck"      => $confirmation['key']
            ];

          });

          // 10.4. Отправить запрос для подтверждения $confirmation
          $conformation_allow = call_user_func(function() USE ($bot, $params_4allow, $confirmation){

            // 1] Осуществить запрос
            $result = runcommand('\M8\Commands\C6_bot_request_steam', [
              "id_bot"          => $bot->id,
              "method"          => "GET",
              "url"             => "https://steamcommunity.com/mobileconf/ajaxop",
              "cookies_domain"  => 'steamcommunity.com',
              "data"            => $params_4allow,
              "ref"             => ""
            ]);
            if($result['status'] != 0)
              throw new \Exception($result['data']['errormsg']);

            // 2] Вернуть результаты (guzzle response)
            return $result['data']['response'];

          });

          // 10.2.3. Если код ответа не 200, сообщить и завершить
          if($conformation_allow->getStatusCode() != 200)
            throw new \Exception('Unexpected response from Steam: code '.$conformation_allow->getStatusCode());

          // 10.2.4. Получить из $response строку с HTML из ответа
          $json = json_decode($conformation_allow->getBody(), true);

          // 10.2.5. Если $json['success'] != true, возбудить исключение
          if(!array_key_exists('success', $json) || $json['success'] == false)
            throw new \Exception('В ответ на запрос одобрения потдверждения с ID = '.$confirmation['id'].', для бота с ID = '.$this->data['id_bot'].', пришёл json с success = false (т.е. не удалось подтверждение принять).');

        }
      }

      // 11. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "confirmations" => $confirmations
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C21_fetch_confirmations from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C21_fetch_confirmations']);
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

