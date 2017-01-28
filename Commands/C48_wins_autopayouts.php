<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make wins autopayouts
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
class C48_wins_autopayouts extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из кэша текущее состояние игры
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Make wins autopayouts //
    //-----------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из кэша текущее состояние игры
      $rooms = json_decode(Cache::get('processing:rooms'), true);
      if(empty($rooms))
        throw new \Exception('Не найдены комнаты в кэше.');

      // 2. Обработать выигрыши для каждой комнаты отдельно
      foreach($rooms as $room) {

        // 2.1. Получить время выплаты выигрыша в этой комнате, в минутах
        $payout_limit_min = !empty($room['payout_limit_min']) ? $room['payout_limit_min'] : 60;

        // 2.2. Получить дату и время в прошлом, на $payout_limit_min минут раньше текущего
        $max_age = \Carbon\Carbon::now()->subMinutes($payout_limit_min);

        // 2.3. Получить все выигрыши комнаты $room, не старше $payout_limit_min
        // - И статус раундов которых pending или выше.
        $wins = \M9\Models\MD4_wins::with(['m5_users'])
          -> whereHas('rounds', function($query) USE ($room) {
            $query->whereHas('rooms', function($query) USE ($room) {
              $query->where('id', $room['id']);
            })->whereHas('rounds_statuses', function($query){
              $query->where('id', '>=', 4);
            });
          })->where('created_at', '>', $max_age->toDateTimeString())
            ->get();

        // 2.4. По очереди обработать каждый выигрыш
        foreach($wins as $win) {

          // 1] Составить список всех вещей на выплату по выигрышу $win
          $items = call_user_func(function() USE ($win) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Наполнить $results
            // - И добавить каждой вещи св-во percentage
            foreach($win['m8_items'] as $item)
              array_push($results, $item->toArray());

            // n] Вернуть результаты
            return $results;

          });

          // 2] Выяснить, есть ли среди $items вещи с пустыми assetid

            // Выяснить
            $is_empty_assetid_in_bet = call_user_func(function() USE ($items) {

              $result = false;
              foreach($items as $item) {
                if(empty($item['pivot']['assetid'])) {
                  $result = true;
                  break;
                }
              }
              return $result;

            });

            // Если $is_empty_assetid_in_bet == true, перейти к следующей итерации
            if($is_empty_assetid_in_bet === true) continue;

          // 3] Получить ID и STEAMID победителя из $win
          $steamid_and_id = call_user_func(function() USE ($win) {

            // 1] Получить массив из $win
            $win_arr = $win->toArray();

            // 2] Получить информацию о пользователе
            if(array_key_exists('m5_users', $win_arr) && is_array($win_arr['m5_users']) && count($win_arr['m5_users']) > 0)
              $user = $win_arr['m5_users'][0];
            else
              $user = '';

            // 3] Вернуть результат
            if(empty($user))
              return [
                "steamid" => "",
                "id"      => "",
                "user"    => ""
              ];
            else
              return [
                "steamid" => $user['ha_provider_uid'],
                "id"      => $user['id'],
                "user"    => $user
              ];

          });
          if(empty($steamid_and_id) || count($steamid_and_id) == 0 || empty($steamid_and_id['steamid']) || empty($steamid_and_id['id']))
            continue;

          // 4]



          //$not_paid_expired = json_decode(Cache::tags(['processing:wins:not_paid_expired:personal'])->get('processing:wins:not_paid_expired:'.$win['m5_users'][0]['id']), true);
          //Log::info($not_paid_expired);


        }

      }



      // - Берём все not paid/expired выигрыши, работаем с каждым индивидуально
      // - Получаем id и steamid получателя выигрыша.
      // - Смотрим связаных с этим выигрышем ботов, работаем с каждым индивидуально
      // - Если is_free == true или tradeofferid не пуст, пропускаем такого бота
      // - Получаем инвентарь бота, ищем в нём вещи из m8_items выигрыша.
      // - Пытаемся отправить оффер с этими вещами победителю, если ошибка - к след.итерации.
      // - Если оффер отправить удалось:
      //   - Переключаем статус выигрыша на active.
      //   - Записываем tradeofferid в pivot-таблицу между выигрышем и ботом.
      //   - Записываем offer_expired_at в pivot-таблицу между выигрышем и ботом.




    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C48_wins_autopayouts from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C48_wins_autopayouts']);
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

