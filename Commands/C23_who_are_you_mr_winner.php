<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Calculates who is the winner of the round in the room, writes data to the DB
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
class C23_who_are_you_mr_winner extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить и проверить входящие данные
     *  2. Получить модель комнаты id_room
     *  3. Получить модель раунда id_round
     *  4. Определить выигравший билет, и игрока-победителя
     *  5. Вычислить размер джекпота (100%, без учёта комиссий) в центах
     *  6. Вычислить угол вращения колеса
     *  7. Записать выигравший билет и угол вращения в раунд
     *  8. Вычислить, какими бонусами обладает игрок
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------//
    // Определяет победителя раунда, делает записи в БД //
    //--------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить и проверить входящие данные
      $validator = r4_validate($this->data, [

        "id_round"            => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_room"             => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Получить модель комнаты id_room
      $room = \M9\Models\MD1_rooms::find($this->data['id_room']);
      if(empty($room))
        throw new \Exception('Не удалось найти комнату с ID = '.$this->data['id_room']);

      // 3. Получить модель раунда id_round
      $round = \M9\Models\MD2_rounds::find($this->data['id_round']);
      if(empty($round))
        throw new \Exception('Не удалось найти раунд с ID = '.$this->data['id_round']);

      // 4. Определить выигравший билет, и игрока-победителя
      $winner_and_ticket = call_user_func(function(){

        // 1] Получить из кэша все игровые данные
        $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1]);
        if($allgamedata['status'] != 0)
          throw new \Exception($allgamedata['data']['errormsg']);
        $rooms = $allgamedata['data']['rooms'];

        // 2] Найти в $rooms комнату с id_room
        $room = call_user_func(function() USE ($rooms) {
          foreach($rooms as $room) {
            if($room['id'] == $this->data['id_room'])
              return $room;
          }
        });
        if(!$room)
          throw new \Exception('Не удалось найти комнату с ID = '.$this->data['id_room'].' в кэше.');

        // 3] Найти в $room раунд с id_round
        $round = call_user_func(function() USE ($room) {
          foreach($room['rounds'] as $round) {
            if($round['id'] == $this->data['id_round'])
              return $round;
          }
        });
        if(!$room)
          throw new \Exception('Не удалось найти раунд с ID = '.$this->data['id_round'].' в кэше.');

        // 4] Получить номер последнего билета последней ставки
        $lastbet_lastticket = $round['bets'][count($round['bets']) - 1]['m5_users'][0]['pivot']['tickets_to'];

        // 5] Получить номер выигравшего билета
        // - От 0 до $lastbet_lastticket включительно.
        $ticket_winner_number = random_int(0, $lastbet_lastticket);

        // 6] Найти пользователя, у которого билет $ticket_winner_number
        $user = call_user_func(function() USE ($round, $ticket_winner_number) {
          foreach($round['bets'] as $bet) {
            foreach($bet['m5_users'] as $user) {
              if($ticket_winner_number >= $user['pivot']['tickets_from'] && $ticket_winner_number <= $user['pivot']['tickets_to'])
                return $user;
            }
          }
        });
        if(!$user)
          throw new \Exception('Не удалось найти пользователя, обладающего билетом-победителем "'.$ticket_winner_number.'" в кэше.');

        // n] Вернуть результат
        return [
          "user_winner"           => $user,
          "ticket_winner_number"  => $ticket_winner_number,
          "round"                 => $round
        ];

      });

      // 5. Вычислить размер джекпота (100%, без учёта комиссий) в центах
      $jackpot_total_sum_cents = call_user_func(function() USE ($winner_and_ticket) {

        // 1] Подготовить переменную для результата
        $result = 0;

        // 2] Получить информацию о раунде, для которого ищем угол вращения
        $round = $winner_and_ticket['round'];

        // 3] Получить короткую ссылку на bets раунда $round
        $bets = $round['bets'];

        // 4] Посчитать банк в центах
        foreach($bets as $bet) {
          foreach($bet['m8_items'] as $item) {
            $result = +$result + +$item['price'];
          }
        }

        // n] Вернуть результат
        return round($result*100);

      });

      // 6. Вычислить угол вращения колеса
      // - Максимально точно.
      $wheel_rotation_angle = call_user_func(function() USE ($winner_and_ticket, $jackpot_total_sum_cents, $winner_and_ticket) {

        // 1] Получить информацию о раунде, для которого ищем угол вращения
        $round = $winner_and_ticket['round'];

        // 2] Получить короткую ссылку на bets раунда $round
        $bets = $round['bets'];

        // 3] Подготовить массив для модели колеса
        $wheel = [];

        // 4] Вычислить данные для модели колеса
        $wheel['data'] = call_user_func(function() USE ($round, $bets, $jackpot_total_sum_cents) {

          // 4.1] Подготовить массив для результатов
          $data = [];

          // 4.2] Наполнить $data
          foreach($bets as $bet) {

            // 4.2.1] Вычислить, ставил ли уже этот пользователь ранее
            // - Если ставил, то получить ссылку на старую ставку.
            $previous_bet = call_user_func(function() USE ($bets, $bet) {
              foreach($bets as $bet2) {
                if(
                  $bet['m5_users'][0]['id'] == $bet2['m5_users'][0]['id'] &&
                  $bet['id'] > $bet2['id']
                )
                  return $bet;
              }
            });

            // 4.2.2] Если не ставил, создать для него новую запись в $data
            if(empty($previous_bet)) {

              // 1) Вычислить шансы пользователя на победу в текущем раунде, в выбранной комнате
              // - Пока что записать 0.
              $odds = call_user_func(function() USE ($bet, $jackpot_total_sum_cents) {
                return (+$bet['total_bet_amount'] / $jackpot_total_sum_cents);
              });

              // 2) Добавить запись в data
              array_push($data, [
                "bets"        => [$bet],
                "user"        => $bet['m5_users'][0],
                "sum"         => $bet['total_bet_amount'],
                "odds"        => $odds,
                "bets_number" => 1
              ]);

            }

            // 4.2.3] Если это не первая ставка пользователя в текущем раунде
            // - То надо склеить её с предыдущей записью этого пользователя в $data
            else {

              // 1) Найти предыдущую запись этого пользователя в $data
              $prev_user_wheel_data = call_user_func(function() USE ($data, $previous_bet) {
                foreach($data as $d) {
                  if($previous_bet['m5_users'][0]['id'] == $d['user']['id'])
                    return $d;
                }
              });

              // 2) Добавить данные о новой ставке пользователя в $data
              array_push($prev_user_wheel_data['bets'], $bet);
              $prev_user_wheel_data['sum'] =
                +$prev_user_wheel_data['sum'] +
                +$bet['total_bet_amount'];
              $prev_user_wheel_data['odds'] =
                +$prev_user_wheel_data['odds'] +
                +(+$bet['total_bet_amount'] / +$jackpot_total_sum_cents);

            }

          }

          // 4.n] Вернуть результаты
          return $data;

        });

        // 5] Создать все сегменты кольца на основе данных из $wheel['data']
        $wheel['segments'] = call_user_func(function() USE ($wheel) {

          // 5.1] Подготовить массив для результатов
          $segments = [];

          // 5.2] Наполнить $segments
          foreach($wheel['data'] as $data) {

            // 1) Вычислить стартовый угол
            $startAngle = call_user_func(function() USE ($data, $segments) {

              // 1.1) Если $segments ещё пуст
              if(count($segments) == 0)
                return 0;

              // 1.2) Если $segments не пуст
              else
                return $segments[count($segments) - 1]['endAngle'];

            });

            // 2) Вычислить конечный угол
            $endAngle = call_user_func(function() USE ($data, $startAngle, $segments) {
              $endAngle = (+$startAngle + (360*$data['odds']));
              if(count($segments) == 0 && $endAngle == 360) $endAngle = 359.99999;
              return $endAngle;
            });

            // 3) Записать $startAngle и $endAngle в $segments
            array_push($segments, [
              "startAngle" => $startAngle,
              "endAngle"   => $endAngle,
              "user"       => $data['user']
            ]);

          }

          // 5.n] Вернуть результаты
          return $segments;

        });

        // 6] Найти сегмент-победитель в $segments
        $segment_winner = call_user_func(function() USE ($wheel, $winner_and_ticket) {
          foreach($wheel['segments'] as $segment) {
            if($segment['user']['id'] == $winner_and_ticket['user_winner']['id'])
              return $segment;
          }
        });
        if(empty($segment_winner))
          throw new \Exception('Не удалось определить сегмент-победитель в модели кольца');

        // 7] Получить угол вращения колеса
        $wheel_rotation_angle = call_user_func(function() USE ($segment_winner) {

          // 7.1] Определим коэффициент
          $coef = pow(10,10);

          // 7.2] Получим startAngle и endAngle
          $startAngle = $segment_winner['startAngle'];
          $endAngle = $segment_winner['endAngle'];

          // 7.3] Умножим их на $coef
          $startAngle = round($startAngle * $coef);
          $endAngle = round($endAngle * $coef);

          // 7.4] Прибавляем 1 к startAngle, вычитаем 1 из endAngle
          $startAngle = +$startAngle + 1;
          $endAngle = +$endAngle - 1;

          // 7.5] Получаем угол вращения
          return random_int($startAngle, $endAngle) / $coef;

        });

        // n] Вернуть результат
        return $wheel_rotation_angle;

      });

      // 7. Записать выигравший билет и угол вращения в раунд
      $round->ticket_winner_number = $winner_and_ticket['ticket_winner_number'];
      $round->wheel_rotation_angle = $wheel_rotation_angle;
      $round->save();

      // 8. Вычислить, какими бонусами обладает игрок
      $bonuses = call_user_func(function() USE ($room, $winner_and_ticket) {

        // 1] Подготовить массив для бонусов-результатов
        $bonuses_results = [
          "bonus_domain"    => 0,
          "bonus_firstbet"  => 0,
          "bonus_secondbet" => 0
        ];

        // 2] Получить номиналы бонусов для комнаты id_room
        $bonus_nominals = [
          "bonus_domain"      => !empty($room['bonus_domain']) ? $room['bonus_domain'] : 0,
          "bonus_domain_name" => !empty($room['bonus_domain_name']) ? $room['bonus_domain'] : "",
          "bonus_firstbet"    => !empty($room['bonus_firstbet']) ? $room['bonus_firstbet'] : 0,
          "bonus_secondbet"   => !empty($room['bonus_secondbet']) ? $room['bonus_secondbet'] : 0
        ];

        // 3] Вычислить, на какие бонусы игрок-победитель имеет право
        $has = [
          "bonus_domain"    => preg_match("/ ".$bonus_nominals['bonus_domain_name']."$/ui", $winner_and_ticket['user_winner']['nickname']),
          "bonus_firstbet"  => $winner_and_ticket['round']['bets'][0]['m5_users'][0]['id'] == $winner_and_ticket['user_winner']['id'],
          "bonus_secondbet" => $winner_and_ticket['round']['bets'][1]['m5_users'][0]['id'] == $winner_and_ticket['user_winner']['id']
        ];

        // 4] Вернуть результаты
        return [
          "bonus_domain"    => $has['bonus_domain'] ? $bonus_nominals['bonus_domain'] : 0,
          "bonus_firstbet"  => $has['bonus_firstbet'] ? $bonus_nominals['bonus_firstbet'] : 0,
          "bonus_secondbet" => $has['bonus_secondbet'] ? $bonus_nominals['bonus_secondbet'] : 0,
        ];

      });

      Log::info($bonuses);






      // Вычислить, какими бонусами обладает пользователь
      // - Сделал ли он ставку первым?
      // - Сделал ли он ставку вторым?
      // - Есть ли у него в нике необходимая строка?


      // Вычислить джекпот раунда (100%, без учёта комиссий)


      // Вычислить комиссию, долговой баланс, какие вещи в комиссии, а какие отдаём
      // - Базовое значение брать из настроек комнаты.
      // - Учесть всевозможные бонусы
      // - Учесть долговой баланс пользователя.
      // - Учесть, какие вещи из джекпота мы можем забрать
      //   в качестве комиссии, а какие отдать.
      // - Вычислить добавку к долговому балансу.

      // Создать новый выигрыш
      // - Заполнить его ранее вычисленными значениями.
      // - Связать его с раундом.
      // - Связать его с пользователем-победителем.
      // - Связать его с ботом, проводившим раунд.
      // - Связать его с вещами, которые нужно выплатить в качестве выигрыша.
      // - Связать его со статусом Ready.







    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C23_who_are_you_mr_winner from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C23_who_are_you_mr_winner']);
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

