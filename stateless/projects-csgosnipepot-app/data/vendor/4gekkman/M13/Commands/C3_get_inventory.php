<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get inventory with time limit
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force     | Брать ли свежий инвентарь из БД, даже если он есть в кэше?
 *        steamid   | Steam ID пользователя, чей инвентарь нужен
 *        tlimit_on | Использовать ли ограничение по частоте запросов инвентаря в опред.в конфиге период
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

  namespace M13\Commands;

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
class C3_get_inventory extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Если force == false, вернуть инвентарь
     *  3. Если force == true, вернуть инвентарь, если с пред.запроса прошло N секунд
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------//
    // Get inventory with time limit //
    //-------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров, назначить значения по умолчанию

        // Провести валидацию
        $validator = r4_validate($this->data, [
          "force"              => ["required", "boolean"],
          "steamid"            => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
          "tlimit_on"          => ["boolean"],
        ]); if($validator['status'] == -1) {
          throw new \Exception($validator['data']);
        }

        // Назначить значения по умолчанию
        if(!array_key_exists('tlimit_on', $this->data))
          $this->data['tlimit_on'] = true;

      // 2. Если force == false, вернуть инвентарь
      if($this->data['force'] == false) {

        // 1] Получить инвентарь
        $inventory = runcommand('\M8\Commands\C4_getinventory', [
          "force"   => $this->data['force'],
          "steamid" => $this->data['steamid']
        ]);
        if($inventory['status'] != 0)
          throw new \Exception($inventory['data']['errormsg']);

        // 2] Вернуть результаты
        return [
          "status"  => 0,
          "data"    => [
            "inventory" => $inventory
          ]
        ];

      }

      // 3. Если force == true, вернуть инвентарь, если с пред.запроса прошло N секунд
      else {

        // 3.1. Узнать, как часто пользователь может обновлять инвентарь (в сек)
        // - По умолчанию, раз в 300 секунд.
        $howoften_sec = config("M13.how_ofen_user_can_update_inv_sec");
        if(empty($howoften_sec))
          $howoften_sec = 300;

        // 3.2. Получить кэш с датой/временем предыдущего запроса инвентаря этим пользователем с force == true
        $last_inv_req_with_force = Cache::get('D10009:invreqwithforce_datetime:'.$this->data['steamid']);

        // 3.3. Прошло ли уже 5 минут с предыдущего запроса, и сколько секунд по факту прошло
        $times = call_user_func(function() USE ($last_inv_req_with_force, $howoften_sec) {

          // 1] Если $last_inv_req_with_force пуст
          if(empty($last_inv_req_with_force)) {

            // 1.1] Записать новую дату/время в кэш
            Cache::put('D10009:invreqwithforce_datetime:'.$this->data['steamid'], \Carbon\Carbon::now()->toDateTimeString(), $howoften_sec/60);

            // 1.2] Вернуть результат
            return [
              "gone_secs" => "",    // Сколько секунд по факту прошло
              "left_secs" => "",    // Сколько секунд осталось до того комента, когда можно запросить инвентарь с force == true
              "verdict"   => true   // Прошло ли уже 5 минут с предыдущего запроса
            ];

          }

          // 2] Если же он не пуст
          else {

            // 2.1] Получить $last_inv_req_with_force в формате Carbon
            $last_carbon = \Carbon\Carbon::parse($last_inv_req_with_force);

            // 2.2] Получить текущие дату/время в формате Carbon
            $now = \Carbon\Carbon::now();

            // 2.3] Сколько прошло с предыдущего запроса в секундах
            $gone_secs = $last_carbon->diffInSeconds($now);

            // 2.4] Сколько осталось ждать
            $left_secs = call_user_func(function() USE ($gone_secs, $howoften_sec) {

              if($gone_secs >= $howoften_sec) return 0;
              else
                return +$howoften_sec - +$gone_secs;

            });

            // 2.5] Записать новую дату/время в кэш, если $gone_secs >= $howoften_sec
            if($gone_secs >= $howoften_sec)
              Cache::put('D10009:invreqwithforce_datetime:'.$this->data['steamid'], \Carbon\Carbon::now()->toDateTimeString(), $howoften_sec/60);

            // 2.n] Вернуть результаты
            return [
              "gone_secs" => $gone_secs,
              "left_secs" => $left_secs,
              "verdict"   => $gone_secs >= $howoften_sec
            ];

          }

        });

        // 3.4. Если не прошло, и tlimit_on = true, завершить с ошибкой
        if($times['verdict'] == false && $this->data['tlimit_on'] == true)
          throw new \Exception($times['left_secs']);
          //throw new \Exception("Инвентарь можно запрашивать не чаще, чем раз в $howoften_sec секунд. Осталось подождать: ".$times['left_secs']);

        // 3.5. Если прошло, вернуть инвентарь
        else {

          // 1] Получить инвентарь
          $inventory = runcommand('\M8\Commands\C4_getinventory', [
            "force"   => $this->data['force'],
            "steamid" => $this->data['steamid']
          ]);
          if($inventory['status'] != 0)
            throw new \Exception($inventory['data']['errormsg']);

          // 2] Вернуть результаты
          return [
            "status"  => 0,
            "data"    => [
              "inventory" => $inventory
            ]
          ];

        }

      }

    } catch(\Exception $e) {

        // 1] Записать данные в логи
        $errortext = 'Invoking of command C3_get_inventory from M-package M13 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M13', 'C3_get_inventory']);

        // 2] Если errormsg, это цифра
        if(is_numeric($e->getMessage())) {

          // 2.1] Узнать, как часто пользователь может обновлять инвентарь (в сек)
          // - По умолчанию, раз в 300 секунд.
          $howoften_sec = config("M13.how_ofen_user_can_update_inv_sec");
          if(empty($howoften_sec))
            $howoften_sec = 300;

          // 2.2] Вернуть результат
          return [
            "status"  => -2,
            "data"    => [
              "errortext"     => $errortext,
              "errormsg"      => $e->getMessage(),
              "left_secs"     => $e->getMessage(),
              "howoften_sec"  => $howoften_sec
            ]
          ];

        }

        // n] Если errormsg, это не цифра
        else {
          return [
            "status"  => -2,
            "data"    => [
              "errortext" => $errortext,
              "errormsg" => $e->getMessage()
            ]
          ];
        }

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

