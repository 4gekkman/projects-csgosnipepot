<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get all jackpot statistics to show in public jackpot document
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force         | (по умолчанию, == true) Обновлять кэш, даже если он присутствует
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
class C40_get_statistics extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Обновить кэш
     *    3.1. m9:statistics:lastwinners
     *    3.2. m9:statistics:luckyoftheday
     *    3.3. m9:statistics:thebiggetsbet
     *
     *  n. Вернуть результаты
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------------//
    // Извлечь всю необходимую статистику для демонстрации в публичном документе игры //
    //--------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "force"           => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

      // 3. Обновить кэш

        // 3.1. m9:statistics:lastwinners

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('m9:statistics:lastwinners'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m9:statistics:lastwinners') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // - Найти последнюю победу для каждой из комнат

            // 1] Получить массив последних победителей для каждой комнаты
            // - Формат:
            //
            //    [
            //      <id комнаты> => <победитель>,
            //      <id комнаты> => <победитель>,
            //      ...
            //    ]
            //
            $lastwinners = call_user_func(function(){

              // 1.1] Подготовить массив для результата
              $result = [];

              // 1.2] Получить коллекцию всех комнат
              $rooms = \M9\Models\MD1_rooms::get();

              // 1.3] Наполнить $result
              foreach($rooms as $room) {

                // 1.3.1] Получить последний раунд комнаты $room, связанный с победой
                // - Добавить его в $result
                $last = \M9\Models\MD2_rounds::with(['wins.m5_users', 'wins.m8_items'])
                ->whereHas('rooms', function($queue) USE ($room) {
                  $queue->where('id', $room['id']);
                })->whereHas('wins')->orderBy('id', 'desc')->first();

                // 1.3.2] Если $last не пуст
                // - Добавить инфу из $last в $result
                if(!empty($last)) {

                  // 1) Получить выигрыш
                  $win = $last->wins[0];

                  // 2) Получить пользователя
                  $user = $win->m5_users[0];

                  // 3) Подсчитать шансы победителя
                  $odds = round(($win['winner_bets_items_cents']/$win['jackpot_total_sum_cents'])*100*100)/100;

                  // 4) Записать данные в $result
                  $result[$room['id']] = [
                    'id'                      => $user['id'],
                    'nickname'                => $user['nickname'],
                    'avatar_steam'            => $user['avatar_steam'],
                    'jackpot_total_sum_cents' => $win['jackpot_total_sum_cents'],
                    'odds'                    => $odds
                  ];

                }

                // 1.3.3] Если $last пуст, добавить пустую инфу в $result
                else {

                  // 1) Записать пустые значения
                  $result[$room['id']] = [
                    'id'                      => '',
                    'nickname'                => '',
                    'avatar_steam'            => '',
                    'jackpot_total_sum_cents' => '',
                    'odds'                    => ''
                  ];

                }

              }

              // 1.n] Вернуть $result
              return $result;

            });

            // 2] Обновить кэш
            Cache::put('m9:statistics:lastwinners', json_encode($lastwinners, JSON_UNESCAPED_UNICODE), 30);

          }

        // 3.2. m9:statistics:luckyoftheday

          // 3.2.1. Получить кэш
          $cache = json_decode(Cache::get('m9:statistics:luckyoftheday'), true);

          // 3.2.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m9:statistics:luckyoftheday') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // 1] Получить данные по счастливчику дня
            $luckyoftheday = call_user_func(function(){

              // 1.1] Получить счастливчика текущего дня
              $luckyoftheday_win = \M9\Models\MD4_wins::with(['m5_users'])
                ->whereRaw('Date(created_at) = CURDATE()')
                ->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= 5')
                ->whereRaw('winner_bets_items_cents = (select max(`winner_bets_items_cents`) from m9.md4_wins)')
                ->first();

              // 1.2] Получить предыдущего счастливчика
              // - Это не обязательно будет счастливчик предыдущего дня.
              //   Вполне возможно, что за последнюю неделю вообще счастливчиков
              //   не было.
              $luckyoftheday_previous_win = \M9\Models\MD4_wins::with(['m5_users'])
                ->whereRaw('(winner_bets_items_cents/jackpot_total_sum_cents)*100 <= 5')
                ->whereRaw('winner_bets_items_cents = (select max(`winner_bets_items_cents`) from m9.md4_wins)')
                ->orderBy('created_at', 'desc')->first();

              // 1.3] Если $luckyoftheday_win или $luckyoftheday_previous_win не пусты
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

              // 1.4] Если и $luckyoftheday_win, и $luckyoftheday_previous_win пусты
              return [
                'id'                      => '',
                'nickname'                => '',
                'avatar_steam'            => '',
                'jackpot_total_sum_cents' => '',
                'odds'                    => ''
              ];

            });

            // 2] Обновить кэш
            Cache::put('m9:statistics:luckyoftheday', json_encode($luckyoftheday, JSON_UNESCAPED_UNICODE), 30);

          }

        // 3.3. m9:statistics:thebiggetsbet

          // 3.3.1. Получить кэш
          $cache = json_decode(Cache::get('m9:statistics:thebiggetsbet'), true);

          // 3.3.2. Обновить кэш
          // - Если он отсутствует, или если параметр force == true
          if(
            (!Cache::has('m9:statistics:thebiggetsbet') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // 1] Получить данные о наибольшей ставке, и кто её сделал
            $thebiggetsbet = call_user_func(function(){

              // 1,1] Подготовить переменную для результатов
              $result = "";

              // 1.2] Среди всех ставок найти наибольшую по сумме
              // - За текущий день.
              $bet = \M9\Models\MD3_bets::with(['m5_users'])
                  ->whereRaw('sum_cents_at_bet_moment = (SELECT MAX(CAST(`sum_cents_at_bet_moment` as SIGNED)) FROM m9.md3_bets WHERE Date(`created_at`) = CURDATE())')
                  ->first();

              // 1.3] Если $bet не пуста
              // - Добавить инфу из $win в $result
              if(!empty($bet)) {

                // Получить пользователя
                $user = $bet->m5_users[0];

                // Записать данные в $result
                $result = [
                  'id'                      => $user['id'],
                  'nickname'                => $user['nickname'],
                  'avatar_steam'            => $user['avatar_steam'],
                  'sum_cents_at_bet_moment' => $bet['sum_cents_at_bet_moment']
                ];

              }

              // 1.4] Если $bet пуста, добавить пустую инфу в $result
              else {

                $result = [
                  'id'                      => '',
                  'nickname'                => '',
                  'avatar_steam'            => '',
                  'sum_cents_at_bet_moment' => ''
                ];

              }

              // n] Вернуть $result
              return $result;

            });

            // 2] Обновить кэш
            Cache::put('m9:statistics:thebiggetsbet', json_encode($thebiggetsbet, JSON_UNESCAPED_UNICODE), 30);

          }


      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "m9:statistics:lastwinners"   => json_decode(Cache::get('m9:statistics:lastwinners'), true),
          "m9:statistics:luckyoftheday" => json_decode(Cache::get('m9:statistics:luckyoftheday'), true),
          "m9:statistics:thebiggetsbet" => json_decode(Cache::get('m9:statistics:thebiggetsbet'), true)
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C40_get_statistics from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C40_get_statistics']);
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

