<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get stats lucky of the day and update cache if needed
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force       | Обновлять ли принудительно кэш (даже если он есть),
 *        broadcast   | Транслировать ли обновлённую статистику через публичный канал всем клиентам
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

  namespace M9\Commands;

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
class C56_get_stats_luckyoftheday extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и обработать входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Получить кэш статистики
     *  4. Если кэш пуст, или force == true, обновить кэш
     *  5. Транслировать данные всем клиентам чере публичный канал, если broadcast == true
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------//
    // Get stats lucky of the day and update cache if needed //
    //-------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Принять и обработать входящие данные
      $validator = r4_validate($this->data, [
        "force"           => ["boolean"],
        "broadcast"       => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

        // 2.2. Если broadcast отсутствует, назначить true
        if(!array_key_exists('broadcast', $this->data))
          $this->data['broadcast'] = true;

      // 3. Получить кэш статистики
      $cache = json_decode(Cache::get('m9:stats:luckyoftheday'), true);

      // 4. Если кэш пуст, или force == true, обновить кэш
      if(empty($cache) || $this->data['force'] == true) {

        // 1] Получить данные о наиболее удачной ставке, и кто её сделал
        $luckyoftheday = call_user_func(function(){

          // 1.1] Задать MIN % для lucky of the day
          $max_odds = 20;

          // 1.2] Получить счастливчика текущего дня
          // - Искать такого, который:
          //
          //    • Играл сегодня.
          //    • Победил с шансом менее или равным $max_odds.
          //    • Среди всех кандидатов, выиграл больше всех.
          //
          $luckyoftheday_win = call_user_func(function() USE ($max_odds) {

            // 1) Сперва получить значение MAX выигрыша среди кандидатов
            // - Если значения нет, вернуть пустую строку.
            $max_jackpot_among_applicants = \M9\Models\MD4_wins::with(['m5_users'])->whereRaw('Date(created_at) = CURDATE()')->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= '.$max_odds)->max('jackpot_total_sum_cents');
            if(empty($max_jackpot_among_applicants))
              return "";

            // 2) Получить нужного кандидата
            // - Если по какой-то причине его нет, вернуть пустую строку.
            $applicant = \M9\Models\MD4_wins::with(['m5_users'])->whereRaw('Date(created_at) = CURDATE()')->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= '.$max_odds)->where('jackpot_total_sum_cents', $max_jackpot_among_applicants)->orderBy('created_at', 'desc')->first();
            if(empty($applicant))
              return "";

            // 3) Вернуть $applicant
            return $applicant;

          });

          // 1.3] Получить предыдущего счастливчика
          // - Это не обязательно будет счастливчик предыдущего дня.
          //   Вполне возможно, что за последнюю неделю вообще счастливчиков
          //   не было.
          $luckyoftheday_previous_win = call_user_func(function() USE ($max_odds) {

            // 1) Сперва получить значение MAX выигрыша среди кандидатов
            // - Если значения нет, вернуть пустую строку.
            $max_jackpot_among_applicants = \M9\Models\MD4_wins::with(['m5_users'])->whereRaw('Date(created_at) != CURDATE()')->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= '.$max_odds)->max('jackpot_total_sum_cents');
            if(empty($max_jackpot_among_applicants))
              return "";

            // 2) Получить нужного кандидата
            // - Если по какой-то причине его нет, вернуть пустую строку.
            $applicant = \M9\Models\MD4_wins::with(['m5_users'])->whereRaw('Date(created_at) != CURDATE()')->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= '.$max_odds)->where('jackpot_total_sum_cents', $max_jackpot_among_applicants)->orderBy('created_at', 'desc')->first();
            if(empty($applicant))
              return "";

            // 3) Вернуть $applicant
            return $applicant;

          });

          // 1.4] Если $luckyoftheday_win или $luckyoftheday_previous_win не пусты
          if(!empty($luckyoftheday_win) || !empty($luckyoftheday_previous_win)) {

            // 1) Получить выигрыш
            $win = !empty($luckyoftheday_win) ? $luckyoftheday_win : $luckyoftheday_previous_win;

            // 2) Получить пользователя
            $user = $win->m5_users[0];

            // 3) Подсчитать шансы победителя
            $odds = round(($win['winner_bets_items_cents']/$win['jackpot_total_sum_cents'])*100*100)/100;

            // 4) Записать данные в $result
            return [
              'id'                      => $user['id'],
              'nickname'                => $user['nickname'],
              'avatar_steam'            => $user['avatar_steam'],
              'jackpot_total_sum_cents' => $win['jackpot_total_sum_cents'],
              'odds'                    => $odds
            ];

          }

          // 1.5] Если и $luckyoftheday_win, и $luckyoftheday_previous_win пусты
          return [
            'id'                      => '',
            'nickname'                => '',
            'avatar_steam'            => '',
            'jackpot_total_sum_cents' => '',
            'odds'                    => ''
          ];

        });

        // 2] Обновить кэш
        Cache::put('m9:stats:luckyoftheday', json_encode($luckyoftheday, JSON_UNESCAPED_UNICODE), 30);

      }

      // 5. Транслировать данные всем клиентам чере публичный канал, если broadcast == true
      if($this->data['broadcast'] == true) {
        Event::fire(new \R2\Broadcast([
          'channels' => ['m9:public'],
          'queue'    => 'default',
          'data'     => [
            'task' => 'm9:stats:update:luckyoftheday',
            'data' => [
              'luckyoftheday' => json_decode(Cache::get('m9:stats:luckyoftheday'), true)
            ]
          ]
        ]));
      }

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "luckyoftheday"   => json_decode(Cache::get('m9:stats:luckyoftheday'), true)
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C56_get_stats_luckyoftheday from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C56_get_stats_luckyoftheday']);
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

