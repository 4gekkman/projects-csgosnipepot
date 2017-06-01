<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create giveaway offer and drop online counter of the user
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
class C11_create_giveaway_offer extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих данных
     *  2. Попробовать найти пользователя id_user
     *  3. Если пользователь анонимный, завершить
     *  4. Получить id комнаты, наличие MIN 1 ставки в которой надо проверять
     *  5. Получить Ready-выдачу, связанную с этим пользователем
     *  6. Получить ID связанного с $giveaway бота
     *  7. Получить массив вещей, который надо отдать
     *  8. Проверить, выполнено ли задание со вступлением в группу
     *  9. Проверить, выполнено ли задание с добавлением строки в ник
     *  10. Проверить кол-во ставок в комнате Main
     *  11. Отправить игроку торговое предложение
     *  12. Начать транзакцию
     *  13. Если $tradeofferid отправить не удалось
     *  14. Если $tradeofferid удалось успешно отправить
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------//
    // Create giveaway offer and drop online counter of the user //
    //-----------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих данных
      $validator = r4_validate($this->data, [
        "id_user"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "steamid"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти пользователя id_user
      $id_user = $this->data['id_user'];
      $user = \M5\Models\MD1_users::where('id', $id_user)->first();
      if(empty($user))
        throw new \Exception('5');

      // 3. Если пользователь анонимный, завершить
      if($user['isanonymous'] == 1)
        throw new \Exception('1');

      // 4. Получить id комнаты, наличие MIN 1 ставки в которой надо проверять
      $id_room = config("M16.id_room2check") ?: 2;

      // 5. Получить Ready-выдачу, связанную с этим пользователем
      $giveaway = \M16\Models\MD2_giveaways::with(['m5_users', 'm8_items', 'm8_bots'])
        ->where('giveaway_status', '1')
        ->whereHas('m5_users', function($queue) USE ($id_user) {
          $queue->where('id', $id_user);
        })->first();
      if(empty($giveaway)) {

        // 5.1. Попробовать найти активную выдачу
        $giveaway_active = \M16\Models\MD2_giveaways::with(['m5_users', 'm8_items', 'm8_bots'])
          ->where('giveaway_status', '1')
          ->whereHas('m5_users', function($queue) USE ($id_user) {
            $queue->where('id', $id_user);
          })->first();

        // 5.2. Если есть, вернуть ошибку 7
        if(!empty($giveaway_active))
          throw new \Exception('7');

        // 5.3. Если нет, вернуть ошибку 2
        else
          throw new \Exception('2');

      }

      // 6. Получить ID связанного с $giveaway бота
      $id_bot = $giveaway['m8_bots'][0]['id'];

      // 7. Получить массив вещей, который надо отдать
      $assets2send = [$giveaway['m8_items'][0]['pivot']['assetid_bots']];

      // 8. Проверить, выполнено ли задание со вступлением в группу

        // 8.1. Провенить
        $is_user_in_groups2join = runcommand('\M18\Commands\C3_check_if_user_in_group', [
          'steamid'       => $this->data['steamid'],
          'groups2join'   => config('M18.groups2join') ?: []
        ]);
        if($is_user_in_groups2join['status'] != 0)
          throw new \Exception('9');
        else
          $is_user_in_groups2join = $is_user_in_groups2join['data']['result'];

        // 8.2. Если не выполнено, вернуть ошибку
        if($is_user_in_groups2join == 0)
          throw new \Exception('9');

      // 9. Проверить, выполнено ли задание с добавлением строки в ник

        // 9.1. Провенить
        $is_strings2check_in_nickname = runcommand('\M17\Commands\C3_check_strings_in_nickname', [
          'steamid'       => $this->data['steamid'],
          'strings2check' => config('M17.strings2check') ?: []
        ]);
        if($is_strings2check_in_nickname['status'] != 0)
          throw new \Exception('8');
        else
          $is_strings2check_in_nickname = $is_strings2check_in_nickname['data']['result'];

        // 9.2. Если не выполнено, вернуть ошибку
        if($is_strings2check_in_nickname == 0)
          throw new \Exception('8');

      // 10. Проверить кол-во ставок в комнате Main

        // 10.1. Проверить
        $bets_in_main = \M9\Models\MD3_bets::whereHas('m5_users', function($queue) USE ($id_user, $id_room) {
          $queue->where('id', $id_user);
        })->whereHas('rounds', function($queue) USE ($id_room) {
          $queue->where('id_room', $id_room);
        })->count();

        // 10.2. Если ставок нет, вернуть ошибку
        if(empty($bets_in_main))
          throw new \Exception('3');

      // 11. Отправить игроку торговое предложение
      $tradeofferid = call_user_func(function() USE ($id_bot, $giveaway, $user, $assets2send){

        // 1] Получить steam_tradeurl пользователя $user
        $steam_tradeurl = $user['steam_tradeurl'];
        if(empty($steam_tradeurl))
          return [
            "tradeofferid" => "",
            "error"        => "Чтобы получить оффер, сначала введите свой Steam Trade URL в настройках."
          ];

        // 2] Получить partner и token пользователя из его trade url
        $partner_and_token = runcommand('\M8\Commands\C26_get_partner_and_token_from_trade_url', [
          "trade_url" => $steam_tradeurl
        ]);
        if($partner_and_token['status'] != 0)
          return [
            "tradeofferid" => "",
            "error"        => "Похоже, что ты ввёл неправильный Steam Trade URL в настройках. Перепроверь его."
          ];
        $partner = $partner_and_token['data']['partner'];
        $token = $partner_and_token['data']['token'];

        // 3] Сформировать сообщение для торгового предложения
        $tradeoffermessage = call_user_func(function() USE ($giveaway) {
          return "Safecode: ".$giveaway['safecode'];
        });

        // 4] Отправить пользователю торговое предложение

          // 4.1] Отправить
          $tradeoffer = runcommand('\M8\Commands\C25_new_trade_offer', [
            "id_bot"                => $id_bot,
            "steamid_partner"  			=> $user['ha_provider_uid'],
            "id_partner"            => $partner,
            "token_partner"         => $token,
            "dont_trade_with_gays"  => "0",
            "assets2send"           => $assets2send,
            "assets2recieve"        => [],
            "tradeoffermessage"     => $tradeoffermessage
          ]);

          // 4.2] Если возникла ошибка
          if($tradeoffer['status'] != 0)
            return [
              "tradeofferid" => "",
              "error"        => "Не удалось отправить торговое предложение. Возможные причины: ты указал неправильный Steam Trade URL; Steam тормозит; проблемы с ботом."
            ];

          // 4.3] Если с этим пользователем нельзя торговать из-за escrow
          if(array_key_exists('data', $tradeoffer) && array_key_exists('could_trade', $tradeoffer['data']) && $tradeoffer['data']['could_trade'] == 0)
            return [
              "tradeofferid" => "",
              "error"        => "Ты не включил подтверждения трейдов через приложения и защиту аккаунта - бот будет отменять твои трейды. После включения аутентификатора надо ждать 7 дней."
            ];

        // 5] Подождать секундочку
        usleep(1000000);

        // 6] Подтвердить исходящее торговое предложение $tradeoffer бота $id_bot
        $result = runcommand('\M8\Commands\C21_fetch_confirmations', [
          "id_bot"                => $id_bot,
          "need_to_ids"           => "1",
          "just_fetch_info"       => "0",
          "tradeoffer_ids"        => [
            $tradeoffer['data']['tradeofferid']
          ]
        ]);
        if($result['status'] != 0)
          return [
            "tradeofferid" => "",
            "error"        => $result['data']['errormsg']
          ];

        // n] Вернуть ID торгового предложения
        return [
          "tradeofferid" => $tradeoffer['data']['tradeofferid'],
          "error"        => ""
        ];

      });

      // 12. Начать транзакцию
      DB::beginTransaction();

      // 13. Если $tradeofferid отправить не удалось
      if(empty($tradeofferid['tradeofferid'])) {

        // 1] Сообщить
        $errortext = 'Invoking of command C11_create_giveaway_offer from M-package M16 have ended with error. Пользователь №'.$this->data['id_user'].'; выдача №'.$giveaway->id.'; ошибка: '.$tradeofferid['error'];
        Log::info($errortext);

        // 2] Изменить статус трейда на 9
        $giveaway->tradeoffer_status = 9;
        $giveaway->save();

        // 3] Подтвердить транзакцию
        DB::commit();

        // 4] Обновить весь кэш, кроме связанного с ботами и инвентарём
        $cacheupdate = runcommand('\M16\Commands\C6_update_cache', [
          "all"   => true,
          "force" => true
        ]);
        if($cacheupdate['status'] != 0)
          throw new \Exception($cacheupdate['data']['errormsg']);

        // 5] Обнулить счётчик онлайна пользователя $id_user
        //Redis::set('m16:online:counter:'.$id_user, 0);

        // n] Вернуть ошибку
        throw new \Exception($tradeofferid['error']);

      }

      // 14. Если $tradeofferid удалось успешно отправить
      else {

        // 1] Изменить статус выдачи на Active
        $giveaway->giveaway_status = 2;

        // 2] Записать $tradeofferid
        $giveaway->tradeofferid = $tradeofferid['tradeofferid'];

        // 3] Изменить tradeoffer_status на 2
        $giveaway->tradeoffer_status = 2;

        // 4] Сохранить изменения
        $giveaway->save();

        // 5] Обнулить счётчик онлайна пользователя $id_user
        Redis::set('m16:online:counter:'.$id_user, 0);

        // 6] Подтвердить транзакцию
        DB::commit();

        // 7] Обновить весь кэш, кроме связанного с ботами и инвентарём
        $cacheupdate = runcommand('\M16\Commands\C6_update_cache', [
          "all"   => true,
          "force" => true
        ]);
        if($cacheupdate['status'] != 0)
          throw new \Exception($cacheupdate['data']['errormsg']);

        // 8] Отправить информацию через частный канал
        Event::fire(new \R2\Broadcast([
          'channels' => ['m16:private:'.$id_user],
          'queue'    => 'm16_broadcast',
          'data'     => [
            'task'   => 'm16_giveaway_offer',
            'status' => 0,
            'data'   => [
              'tradeofferid' => $tradeofferid['tradeofferid'],
              'safecode'     => $giveaway->safecode
            ]
          ]
        ]));

      }

    } catch(\Exception $e) {

      // 1] Через частный канал отправить информацию об ошибке
      Event::fire(new \R2\Broadcast([
        'channels' => ['m16:private:'.$id_user],
        'queue'    => 'm16_broadcast',
        'data'     => [
          'task'   => 'm16_giveaway_offer',
          'status' => -1,
          'data' => [
            'errormsg'           => $e->getMessage()
          ]
        ]
      ]));

      // 2] Обработать ошибку
      $errortext = 'Invoking of command C11_create_giveaway_offer from M-package M16 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
      DB::rollback();
      Log::info($errortext);
      write2log($errortext, ['M16', 'C11_create_giveaway_offer']);
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

