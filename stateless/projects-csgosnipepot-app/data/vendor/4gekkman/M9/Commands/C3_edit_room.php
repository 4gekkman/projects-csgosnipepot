<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Edit the specified room
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
class C3_edit_room extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти комнату с таким ID
     *  3. Попробовать найти комнату с таким именем
     *  4. Внести изменения в $room2edit
     *  5. Сделать commit
     *  6. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------//
    // Изменить комнату //
    //------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [

        "id"                        => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "is_on" 									  => ["required", "regex:/^[01]{1}$/ui"],
        "name" 									    => ["required", "string"],
        "description" 							=> ["r4_defined", "string"],
        "room_round_duration_sec"   => ["required", "regex:/^[0-9]+$/ui"],
        "max_bets_per_round"        => ["required", "regex:/^[0-9]+$/ui"],
        "max_round_jackpot"         => ["required", "regex:/^[0-9]+$/ui"],
        "max_items_per_bet" 			  => ["required", "regex:/^[0-9]+$/ui"],
        "max_items_per_round" 		  => ["required", "regex:/^[0-9]+$/ui"],
        "min_items_per_bet" 			  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "min_items_per_round" 		  => ["required", "regex:/^[0-9]+$/ui"],
        "min_bet" 								  => ["required", "regex:/^[0-9]+$/ui"],
        "max_bet" 								  => ["required", "regex:/^[0-9]+$/ui"],
        "min_bet_round" 					  => ["required", "regex:/^[0-9]+$/ui"],
        "max_bet_round" 					  => ["required", "regex:/^[0-9]+$/ui"],
        "allow_unstable_prices" 	  => ["required", "regex:/^[01]{1}$/ui"],
        "allow_only_types" 			    => ["required", "json"],
        "fee_percents" 	            => ["required", "r4_between:0,100"],
        "debts_collect_per_win_max_percent"  => ["required", "r4_between:0,100"],
        "change" 				            => ["required", "regex:/^[01]{1}$/ui"],
        "one_bot_payout"            => ["required", "regex:/^[01]{1}$/ui"],
        "payout_limit_min"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "revolutions_per_lottery" 	=> ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "lottery_duration_ms" 			=> ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "winner_duration_s" 			  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "pending_duration_s" 			  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "lottery_client_delta_items_limit_s" => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "offers_timeout_sec" 			  => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "bonus_domain" 			        => ["required", "min:0", "max:100"],
        "bonus_domain_name"         => ["required", "string"],
        "bonus_firstbet" 			      => ["required", "min:0", "max:100"],
        "bonus_secondbet" 			    => ["required", "min:0", "max:100"],
        "avatars_num_in_strip" 			=> ["required", "r4_between:10,500"],

        "started_client_delta_s"   => ["required", "regex:/^[0-9]+$/ui"],
        "pending_client_delta_s"   => ["required", "regex:/^[0-9]+$/ui"],
        "lottery_client_delta_ms"  => ["required", "regex:/^[0-9]+$/ui"],
        "winner_client_delta_s"    => ["required", "regex:/^[0-9]+$/ui"],
        "max_items_peruser_perround"=> ["required", "regex:/^[0-9]+$/ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Попробовать найти комнату с таким именем
      $room2edit = \M9\Models\MD1_rooms::find($this->data['id']);
      if(empty($room2edit))
        throw new \Exception('A room with id == '.$this->data['id'].' not found in the system.');

      // 3. Попробовать найти комнату с таким именем
      $room2edit_byname = \M9\Models\MD1_rooms::where('name', $this->data['name'])->first();
      if(!empty($room2edit_byname) && $room2edit->id != $room2edit_byname->id)
        throw new \Exception('A room with name == '.$this->data['name'].' already present in the system.');

      // 4. Внести изменения в $room2edit

        // 4.1. Внести основные изменения
        foreach($this->data as $key => $value) {

          // Если $key == 'timestamp', продолжить
          if($key == 'timestamp') continue;

          // Если $key == 'id', продолжить
          if($key == 'id') continue;

          // Если $key == 'avatar_steam', продолжить
          if($key == 'avatar_steam') continue;

          // В общем случае
          $room2edit[$key] = $value;

        }

        // 4.n. Сохранить
        $room2edit->save();

      // 5. Сделать commit
      DB::commit();

      // 6. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "id"      => $room2edit->id
        ]
      ];


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C3_edit_room from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C3_edit_room']);
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
