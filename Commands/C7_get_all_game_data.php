<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get all game data - rooms - rounds - status, bets - users
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        rounds_limit          // Сколько последних раундов должно войти в результат по каждой комнате (0 - значит без ограничений)
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
class C7_get_all_game_data extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Получить коллекцию всех включенных комнат
     *  3. Получить куку пользователя с ID включенной комнаты
     *  4. Добавить доп.свойства всем ставкам всех комнат
     *  5. Добавить доп.свойства всем комнат
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------------//
    // Получить все игровые данные - rooms -> rounds -> status, bets -> users //
    //------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "rounds_limit"              => ["required", "regex:/^[0-9]+$/ui"]
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Получить коллекцию всех игровых данных
      //				   Комната
      //				    /   \
      //				Раунд   Раунд
      //      /   \       /   \
      // Статус Ставки  Ставки Статус
      //          /       \
      //   Пользователи Пользователи
      //

        // 2.1. Извлечь коллекцию всех игровых данных из кэша
        $rooms = collect(json_decode(Cache::get('processing:rooms'), true));

        // 2.2. Если комнат у игры вообще нет, синхронизировать их с конфигом
        if($rooms->count() == 0) {

          // 1] Синхронизировать
          $result = runcommand('\M9\Commands\C8_sync_rooms', []);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Попробовать снова извлечь $rooms
          $rooms = \M9\Models\MD1_rooms::with(['rounds.rounds_statuses', 'rounds.bets.m5_users'])
              ->where('is_on', 1)->get();

        }

      // 3. Получить куку пользователя с ID включенной комнаты
      $choosen_room_id = call_user_func(function() USE ($rooms) {

        // 1] Получить
        $choosen_room_id = Cookie::get('choosen_room_id');

        // 2] Если $choosen_room пуста, а $rooms не пуста
        // - Добавить в неё ID первой из $rooms.
        if(empty($choosen_room_id) && !$rooms->count() !== 0) {
          $choosen_room_id = $rooms->first()['id'];
        }

        // 3] Если $choosen_room_id нет в $rooms
        if(!in_array($choosen_room_id, $rooms->pluck(['id'])->toArray())) {

          // 3.1] Если $rooms не пуста
          // - Добавить в $choosen_room_id ID первой из $rooms
          if($rooms->count() !== 0) {
            $choosen_room_id = $rooms->first()['id'];
          }

          // 3.2] Если $rooms пуста
          // - Добавить в $choosen_room_id число 0
          if($rooms->count() === 0) {
            $choosen_room_id = 0;
          }

        }

        // 4] Если $choosen_room пуста, и $rooms пуста
        // - Сделать её 0
        if(empty($choosen_room_id) && $rooms->count() === 0) {
          $choosen_room_id = 0;
        }

        // 5] Если $choosen_room_id не число от 1 и выше, сделать её 0
        $validator = r4_validate(['choosen_room_id'=>$choosen_room_id], [
          "choosen_room_id"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        ]); if($validator['status'] == -1) {
          $choosen_room_id = 0;
        }

        // 6] Вернуть результат
        return $choosen_room_id;

      });

      // 4. Добавить доп.свойства всем ставкам всех комнат
      call_user_func(function() USE (&$rooms) {

        // 4.1. Получить палитру цветов для игроков
        $palette = config('M9.palette');

        // 4.2. Добавить доп.свойства
        for($i=0; $i<count($rooms); $i++) {
          for($j=0; $j<count($rooms[$i]['rounds']); $j++) {

            // 1] Добавить доп.свойство total_bet_amount
            call_user_func(function() USE (&$rooms, &$bet, $i, $j) {
              for($k=0; $k<count($rooms[$i]['rounds'][$j]['bets']); $k++) {

                // 1.1] Получить k-ую ставку в короткую переменную
                $bet = $rooms[$i]['rounds'][$j]['bets'][$k];

                // 1.2] Добавить доп.свойство total_bet_amount

                  // 1.2.1] Получить все связанные с $bet вещи
                  $items = \M8\Models\MD2_items::with(['m9_bets'])
                    ->whereHas('m9_bets', function($query) USE ($bet) {
                      $query->where('id', $bet->id);
                    })->get();

                  // 1.2.2] Посчитать итоговую сумму поставленных вещей
                  // - Используя значение item_price_at_bet_time из pivot-таблицы
                  $total_bet_amount = call_user_func(function() USE ($items, $bet) {

                    $result = 0;
                    for($i=0; $i<count($items); $i++) {

                      // 1.2.2.1] Получить ставку с id == $bet->id
                      $bet_with_id = call_user_func(function() USE ($i, $items, $bet) {
                        for($j=0; $j<count($items[$i]['m9_bets']); $j++) {
                          if($items[$i]['m9_bets'][$j]->id == $bet->id)
                            return $items[$i]['m9_bets'][$j];
                        }
                      });

                      // 1.2.2.2] Вернуть результат
                      $result = +$result + $bet_with_id['pivot']['item_price_at_bet_time'];

                    }
                    return $result;

                  });

                  // 1.2.3] Записать итоговую сумму в $bet
                  $bet['total_bet_amount'] = $total_bet_amount;

              }
            });

            // 2] Добавить доп.свойства final_bet_odds и final_bet_odds_human
            call_user_func(function() USE (&$rooms, &$bet, $i, $j) {
              for($k=0; $k<count($rooms[$i]['rounds'][$j]['bets']); $k++) {

                // 2.1] Получить k-ую ставку в короткую переменную
                $bet = $rooms[$i]['rounds'][$j]['bets'][$k];

                // 2.2] Получить общую сумму ставок для j-го раунда
                $bets_total_sum = call_user_func(function() USE ($rooms, $i, $j) {
                  $result = 0;
                  for($k=0; $k<count($rooms[$i]['rounds'][$j]['bets']); $k++) {
                    $result = +$result + $rooms[$i]['rounds'][$j]['bets'][$k]['total_bet_amount'];
                  }
                  return $result;
                });

                // 2.3] Получить шанс
                $odds = $rooms[$i]['rounds'][$j]['bets'][$k]['total_bet_amount'] / $bets_total_sum;

                // 2.4] Определить значение целой части
                $the_whole_part = call_user_func(function() USE ($odds) {
                  return [
                    "value"   => (int) floor($odds),
                    "length"  => count(str_split( (int) floor($odds*100) . '' ))
                  ];
                });

                // 2.5] Если $the_whole_part['length'] == 2
                if($the_whole_part['length'] == 2) {
                  $bet['final_bet_odds'] = round($odds*pow(10,12));
                }

                // 2.6] Если $the_whole_part['length'] == 1
                if($the_whole_part['length'] == 1) {
                  $bet['final_bet_odds'] = round($odds*pow(10,11));
                }

                // 2.7] Если $the_whole_part['length'] == 0
                if($the_whole_part['length'] == 0) {
                  $bet['final_bet_odds'] = round($odds*pow(10,10));
                }

                // 2.8] Добавить final_bet_odds_human
                call_user_func(function() USE (&$bet) {

                  // 2.8.1] Определить размер final_bet_odds
                  $length = count(str_split($bet['final_bet_odds'].'')).'';

                  // 2.8.2] Определить результат
                  switch($length) {
                    case '12': $result = $bet['final_bet_odds']/pow(10,10); break;
                    case '11': $result = $bet['final_bet_odds']/pow(10,10); break;
                    case '10': $result = +('0.'.$bet['final_bet_odds']); break;         
                    case '9':  $result = +('0.0'.$bet['final_bet_odds']); break;        
                    case '8':  $result = +('0.00'.$bet['final_bet_odds']); break;       
                    case '7':  $result = +('0.000'.$bet['final_bet_odds']); break;      
                    case '6':  $result = +('0.0000'.$bet['final_bet_odds']); break;     
                    case '5':  $result = +('0.00000'.$bet['final_bet_odds']); break;    
                    case '4':  $result = +('0.000000'.$bet['final_bet_odds']); break;   
                    case '3':  $result = +('0.0000000'.$bet['final_bet_odds']); break;  
                    case '2':  $result = +('0.00000000'.$bet['final_bet_odds']); break; 
                    case '1':  $result = +('0.000000000'.$bet['final_bet_odds']); break;
                  }

                  // 2.8.3] Записать результат в $bet
                  $bet['final_bet_odds_human'] = $result;

                });

              }
            });

            // 3] Добавить доп.свойство bet_color_hex
            call_user_func(function() USE (&$rooms, &$bet, $i, $j, $palette) {
              for($k=0; $k<count($rooms[$i]['rounds'][$j]['bets']); $k++) {

                // 3.1] Получить k-ую ставку в короткую переменную
                $bet = $rooms[$i]['rounds'][$j]['bets'][$k];

                // 3.2] Вычислить, ставил ли уже этот пользователь ранее
                // - Если ставил, то получить ссылку на старую ставку.
                $previous_bet = call_user_func(function() USE ($rooms, $i, $j, $k){

                   for($m=0; $m<count($rooms[$i]['rounds'][$j]['bets']); $m++) {
                     if($rooms[$i]['rounds'][$j]['bets'][$k]['m5_users'][0]['id'] == $rooms[$i]['rounds'][$j]['bets'][$m]['m5_users'][0]['id'] && $m != $k)
                       return $rooms[$i]['rounds'][$j]['bets'][$m];
                   }

                });

                // 3.3] Если не ставил
                if(empty($previous_bet)) {

                  // 3.3.1] Вычислить цвет пользователя в текущем раунде
                  $color = call_user_func(function() USE ($palette, $k) {

                    // 1) Если в палитре есть цвет с индексом $k
                    if($k < count($palette))
                      return $palette[$k];

                    // 2) Если же нет, то взять один из цветов палитры
                    else {

                      // Размер палитры
                      $l = +count($palette);

                      // Вернуть результат
                      return $palette[(int)(round(+$l * floor(+$k / +$l)))];

                    }

                  });

                  // 3.3.2] Записать bet_color_hex
                  $bet['bet_color_hex'] = $color;

                }

                // 3.4] Если это не первая ставка пользователя в этом раунде
                else {

                  // 3.4.1] Перезаписать bet_color_hex тем же значением
                  $bet['bet_color_hex'] = $previous_bet['bet_color_hex'];

                }

              }
            });

          }
        }
      });

      // 5. Добавить доп.свойства всем комнат
      // - Сначала преобразовав коллекцию $rooms в массив
      $rooms = $rooms->toArray();
      call_user_func(function() USE (&$rooms) {

        // 1] Добавить свойство is_some_active_bets
        // - Означающее, есть ли на данный момент у данного
        //   пользователя, в данной комнате, хотя бы 1 ставка
        //   со статусом Active.
        // - Эти данные извлекаются из кэша по ключу: "processing:bets:active"
        call_user_func(function() USE (&$rooms) {

          // 1.1] Получить кэш
          $cache = json_decode(Cache::get('processing:bets:active'), true);

          // 1.2] Получить информацию о пользователе
          $user = json_decode(session('auth_cache'), true);

          // 1.3] Если имеем дела с анонимным пользователем
          if($user['is_anon'] == 1) {

            // Пробежимся по всем комнатам
            for($i=0; $i<count($rooms); $i++) {

              // Записать 1 во всех комнатах
              $rooms[$i]['is_some_active_bets'] = 1;

            }

            // Завершить
            return;

          }

          // 1.4] Если кэша нет
          if(empty($cache)) {

            // Пробежимся по всем комнатам
            for($i=0; $i<count($rooms); $i++) {

              // Записать 0 во всех комнатах
              $rooms[$i]['is_some_active_bets'] = 0;

            }

            // Завершить
            return;

          }

          // 1.5] Если кэш есть
          else {

            // Пробежимся по всем комнатам
            for($i=0; $i<count($rooms); $i++) {

              // 1.3.1] Определить, есть ли в комнате $rooms[$i] активные офферы
              // - Для текущего пользователя $user['user']['id']
              $is_any_active_offers = call_user_func(function() USE ($user, $cache, $rooms, $i) {

                // Если активные ставки есть в $i-й комнате, вернуть 1
                foreach($cache as $activebet) {

                  // Если $activebet находится $i-й комнате, вернуть 1
                  if($activebet['rooms']['0']['id'] == $rooms[$i]['id'])
                    return 1;

                }

                // В противном случае, вернуть 0
                return 0;

              });

              // 1.3.2] Записать результат
              $rooms[$i]['is_some_active_bets'] = $is_any_active_offers;

            }

            // Завершить
            return;

          }

        });

      });

      // n. Вернуть результаты
      DB::commit();
      return [
        "status"  => 0,
        "data"    => [
          "choosen_room_id" => $choosen_room_id,
          "rooms"           => $rooms
        ]
      ];

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C7_get_all_game_data from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C7_get_all_game_data']);
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

