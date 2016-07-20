<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create a new game room
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        name
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
class C2_create_new_room extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Проверить, нет ли уже комнаты с таким именем
     *  3. Получить все параметры по умолчанию для новых комнат из конфига
     *  4. Создать новую комнату с параметрами по умолчанию
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------//
    // Создать новую игровую комнату с параметрами по умолчанию //
    //----------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "name"              => ["required", "string"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Проверить, нет ли уже комнаты с таким именем
      $room_check = \M9\Models\MD1_rooms::where('name', $this->data['name'])->first();
      if(!empty($room_check))
        throw new \Exception('There is a room with such name.');

      // 3. Получить все параметры по умолчанию для новых комнат из конфига
      $defaults = [];

        // 1] Режим приёма ставок комнаты
        $defaults['bet_accepting_mode'] = config('M9.bet_accepting_mode');
        if(empty($defaults['bet_accepting_mode'])) $defaults['bet_accepting_mode'] = 'availability';

        // 2] Длительность раундов в комнате
        $defaults['room_round_duration_sec'] = config('M9.room_round_duration_sec');
        if(empty($defaults['room_round_duration_sec'])) $defaults['room_round_duration_sec'] = '35';

        // 3] MAX кол-во вещей в 1-й ставке в раундах комнады
        $defaults['max_items_per_bet'] = config('M9.max_items_per_bet');
        if(empty($defaults['max_items_per_bet'])) $defaults['max_items_per_bet'] = '0';

        // 4] MIN кол-во вещей в 1-й ставке в раундах комнады
        $defaults['min_items_per_bet'] = config('M9.min_items_per_bet');
        if(empty($defaults['min_items_per_bet'])) $defaults['min_items_per_bet'] = '0';

        // 5] MAX кол-во вещей в раунде
        $defaults['max_items_per_round'] = config('M9.max_items_per_round');
        if(empty($defaults['max_items_per_round'])) $defaults['max_items_per_round'] = '0';

        // 6] MIN кол-во вещей в раунде
        $defaults['min_items_per_round'] = config('M9.min_items_per_round');
        if(empty($defaults['min_items_per_round'])) $defaults['min_items_per_round'] = '0';

        // 7] MAX размер ставки в $
        $defaults['max_bet'] = config('M9.max_bet');
        if(empty($defaults['max_bet'])) $defaults['max_bet'] = '0';

        // 8] MIN размер ставки в $
        $defaults['min_bet'] = config('M9.min_bet');
        if(empty($defaults['min_bet'])) $defaults['min_bet'] = '0';

        // 9] MAX общий размер ставок в $ в раунде
        $defaults['max_bet_round'] = config('M9.max_bet_round');
        if(empty($defaults['max_bet_round'])) $defaults['max_bet_round'] = '0';

        // 10] MIN общий размер ставок в $ в раунде
        $defaults['min_bet_round'] = config('M9.min_bet_round');
        if(empty($defaults['min_bet_round'])) $defaults['min_bet_round'] = '0';

        // 11] Разрешить ли делать ставки вещами с нестабильными ценами
        $defaults['allow_unstable_prices'] = config('M9.allow_unstable_prices');
        if(empty($defaults['allow_unstable_prices'])) $defaults['allow_unstable_prices'] = 0;

        // 12] Разрешить принимать в виде ставок только эти типы вещей

          // 12.1] Доступные типы вещей
          $defaults['available_types_of_items'] = config('M9.available_types_of_items');
          if(empty($defaults['available_types_of_items'])) $defaults['available_types_of_items'] = [
            'case',
            'key',
            'startrak',
            'souvenir packages',
            'souvenir',
            'knife',
            'weapon'
          ];

          // 12.2] Разрешить принимать в виде ставок только эти типы вещей
          $defaults['allow_only_types'] = config('M9.allow_only_types');
          if(empty($defaults['allow_only_types'])) $defaults['allow_only_types'] = [
            'case',
            'key',
            'startrak',
            'souvenir packages',
            'knife',
            'weapon'
          ];

        // 13] Размер комиссии сервиса в комнате в % от банка
        $defaults['fee_percents'] = config('M9.fee_percents');
        if(empty($defaults['fee_percents'])) $defaults['fee_percents'] = '0';

        // 14] Включить ли механизм размена при выплате выигрышей
        $defaults['change'] = config('M9.change');
        if(empty($defaults['change'])) $defaults['change'] = 1;

        // 15] Включить ли механизм выплат выигрышей от имени 1-го бота
        $defaults['one_bot_payout'] = config('M9.one_bot_payout');
        if(empty( $defaults['one_bot_payout']))  $defaults['one_bot_payout'] = 0;

        // 16] Лимит в минутах на то, чтобы победитель забрал выигрыш
        $defaults['payout_limit_min'] = config('M9.payout_limit_min');
        if(empty($defaults['payout_limit_min'])) $defaults['payout_limit_min'] = '60';

      // 4. Провести валидацию всех параметров по умолчанию для новых комнат

        // 4.1. Подготовить массив-конфиг валидатора
        $validator_arr = [

          "bet_accepting_mode"        => ["required", "in:roll,availability"],
          "room_round_duration_sec"   => ["required", "regex:/^[0-9]+$/ui"],
          "max_items_per_bet"         => ["required", "regex:/^[0-9]+$/ui"],
          "min_items_per_bet"         => ["required", "regex:/^[0-9]+$/ui"],
          "max_items_per_round"       => ["required", "regex:/^[0-9]+$/ui"],
          "min_items_per_round"       => ["required", "regex:/^[0-9]+$/ui"],
          "max_bet"                   => ["required", "regex:/^[0-9]+$/ui"],
          "min_bet"                   => ["required", "regex:/^[0-9]+$/ui"],
          "max_bet_round"             => ["required", "regex:/^[0-9]+$/ui"],
          "min_bet_round"             => ["required", "regex:/^[0-9]+$/ui"],
          "allow_unstable_prices"     => ["required", "regex:/^[01]{1}$/ui"],
          "available_types_of_items"  => ["required", "array"],
          "allow_only_types"          => ["required", "array"],
          "fee_percents"              => ["required", "min:0", "max:100"],
          "change"                    => ["required", "regex:/^[01]{1}$/ui"],
          "one_bot_payout"            => ["required", "regex:/^[01]{1}$/ui"],
          "payout_limit_min"          => ["required", "regex:/^[1-9]+[0-9]*$/ui"],

        ];

        // 4.2. Дополнить $validator_arr значением для allow_only_types.*
        call_user_func(function() USE (&$validator_arr, $defaults) {

          // 1] Подготовить значение
          $value = 'in:';

          // 2] Наполнить $value
          for($i=0; $i<count($defaults['available_types_of_items']); $i++) {
            if($i != +count($defaults['available_types_of_items']) - 1) {
              $value = $value . $defaults['available_types_of_items'][$i] . ',';
            }
            else {
              $value = $value . $defaults['available_types_of_items'][$i];
            }
          }
          write2log($value, []);
          // 3] Добавить значение в $validator_arr
          $validator_arr["allow_only_types.*"] = ["required", $value];

        });

        // 4.3. Провести валидацию
        $validator = r4_validate($defaults, $validator_arr); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }

      // 5. Создать новую комнату с параметрами по умолчанию

        // 5.1. Создать новую модель комнаты
        $room = new \M9\Models\MD1_rooms();

        // 5.2. Наполнить её поля
        foreach($defaults as $key => $value) {

          // 1] Если $key = available_types_of_items
          if($key == 'available_types_of_items') continue;

          // 2] Если $key = allow_only_types
          if($key == 'allow_only_types') {
            $room[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            continue;
          }

          // n] В общем случае
          $room[$key] = $value;

        }

        // 5.3. Добавить имя комнаты
        $my_mb_ucfirst = function($str) {
            $fc = mb_strtoupper(mb_substr($str, 0, 1));
            return $fc.mb_substr($str, 1);
        };
        $room['name'] = $my_mb_ucfirst(mb_strtolower($this->data['name']));

        // 5.4. Сохранить созданную модель
        $room->save();


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C2_create_new_room from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C2_create_new_room']);
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

