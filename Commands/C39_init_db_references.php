<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Init db references
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
class C39_init_db_references extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. md5_rounds_statuses
     *  2. md7_bet_accepting_modes
     *  3. md8_bets_statuses
     *  4. md9_wins_statuses
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------------//
    // Провести инициализацию таблиц-справочников в БД M9, если это необходимо //
    //-------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. md5_rounds_statuses
      call_user_func(function(){

        // 1] Получить все статусы из БД, записав их в кэш на 10080 минут
        $md5_rounds_statuses = Cache::remember('md5_rounds_statuses', '10080', function() {
          return \M9\Models\MD5_rounds_statuses::get();
        });

        // 2] Если содержимое коллекции $md5_rounds_statuses не пусто, завершить
        if(count($md5_rounds_statuses) != 0) return;

        // 3] Если коллекция $md5_rounds_statuses пуста
        // - Наполнить таблицу указанными данными.
        if(count($md5_rounds_statuses) == 0) {

          // 3.1] Начать транзакцию
          DB::beginTransaction();

          // 3.2] Подготовить массив с данными
          $data = [
            [
              "id"              => "1",
              "status"          => "Created",
              "description"     => "Начался новый раунд, но ещё никто не сделал ставку."
            ],
            [
              "id"              => "2",
              "status"          => "First bet",
              "description"     => "Сделана первая и единственная пока ставка за раунд."
            ],
            [
              "id"              => "3",
              "status"          => "Started",
              "description"     => "Сделана вторая ставка за раунд, начался обратный отсчёт таймера."
            ],
            [
              "id"              => "4",
              "status"          => "Pending",
              "description"     => "Время игры закончилось, или достигнут лимит вещей. Ожидание обработки сделанных ранее ставок."
            ],
            [
              "id"              => "5",
              "status"          => "Lottery",
              "description"     => "Визуальное проведение розыгрыша."
            ],
            [
              "id"              => "6",
              "status"          => "Winner",
              "description"     => "Демонстрация победителя розыгрыша и его выигрыша."
            ],
            [
              "id"              => "7",
              "status"          => "Finished",
              "description"     => "Раунд закончен."
            ],
          ];

          // 3.3] Очистить таблицу MD5_rounds_statuses
          call_user_func(function() USE ($md5_rounds_statuses) {
            for($i=0; $i<count($md5_rounds_statuses); $i++) {
              $md5_rounds_statuses[$i]->rounds()->detach();
              $md5_rounds_statuses[$i]->delete();
            }
          });

          // 3.4] Наполнить таблицу указанными данными
          for($i=0; $i<count($data); $i++) {
            $new = new \M9\Models\MD5_rounds_statuses();
            $new->id          = $data[$i]['id'];
            $new->status      = $data[$i]['status'];
            $new->description = $data[$i]['description'];
            $new->save();
          }

          // 3.5] Подтвердить транзакцию
          DB::commit();

          // 3.6] Удалить кэш по ключу md7_bet_accepting_modes
          Cache::pull('md5_rounds_statuses');

        }

      });

      // 2. md7_bet_accepting_modes
      call_user_func(function(){

        // 1] Получить все режимы из БД, записав их в кэш на 10080 минут
        $md7_bet_accepting_modes = Cache::remember('md7_bet_accepting_modes', '10080', function() {
          return \M9\Models\MD7_bet_accepting_modes::get();
        });

        // 2] Если содержимое коллекции $md7_bet_accepting_modes не пусто, завершить
        if(count($md7_bet_accepting_modes) != 0) return;

        // 3] Если коллекция $md7_bet_accepting_modes пуста
        // - Наполнить таблицу указанными данными.
        if(count($md7_bet_accepting_modes) == 0) {

          // 3.1] Начать транзакцию
          DB::beginTransaction();

          // 3.2] Подготовить массив с данными
          $data = [
            [
              "id"              => "1",
              "mode"            => "onebot_oneround_inturn_circled",
              "description"     => "В 1-м раунде ставки принимает лишь 1 бот. В следующем - следующий бот. И так по кругу."
            ],
            [
              "id"              => "2",
              "mode"            => "nbots_inturn_circled",
              "description"     => "Каждую ставку принимает следующий бот, даже внутри 1 раунда. И так по кругу."
            ],
          ];

          // 3.3] Очистить таблицу MD7_bet_accepting_modes
          call_user_func(function() USE ($md7_bet_accepting_modes) {
            for($i=0; $i<count($md7_bet_accepting_modes); $i++) {
              $md7_bet_accepting_modes[$i]->rooms()->detach();
              $md7_bet_accepting_modes[$i]->delete();
            }
          });

          // 3.4] Наполнить таблицу указанными данными
          call_user_func(function() USE ($data) {
            for($i=0; $i<count($data); $i++) {
              $new = new \M9\Models\MD7_bet_accepting_modes();
              $new->id          = $data[$i]['id'];
              $new->mode        = $data[$i]['mode'];
              $new->description = $data[$i]['description'];
              $new->save();
            }
          });

          // 3.5] Подтвердить транзакцию
          DB::commit();

          // 3.6] Удалить кэш по ключу md7_bet_accepting_modes
          Cache::pull('md7_bet_accepting_modes');

        }

      });

      // 3. md8_bets_statuses
      call_user_func(function(){

        // 1] Получить все статусы из БД, записав их в кэш на 10080 минут
        $md8_bets_statuses = Cache::remember('md8_bets_statuses', '10080', function() {
          return \M9\Models\MD8_bets_statuses::get();
        });

        // 2] Если содержимое коллекции $md8_bets_statuses не пусто, завершить
        if(count($md8_bets_statuses) != 0) return;

        // 3] Если коллекция $md8_bets_statuses пуста
        // - Наполнить таблицу указанными данными.
        if(count($md8_bets_statuses) == 0) {

          // 3.1] Начать транзакцию
          DB::beginTransaction();

          // 3.2] Подготовить массив с данными
          $data = [
            [
              "id"              => "1",
              "status"          => "Invalid",
              "description"     => "Недействителен"
            ],
            [
              "id"              => "2",
              "status"          => "Active",
              "description"     => "Оффер был отправлен, но ни одна из сторон не предприняла ещё с ним никаких действий"
            ],
            [
              "id"              => "3",
              "status"          => "Accepted",
              "description"     => "Оффер был принят получателем, и обмен вещами был произведён"
            ],
            [
              "id"              => "4",
              "status"          => "Countered",
              "description"     => "Получатель сделал контрпредложение"
            ],
            [
              "id"              => "5",
              "status"          => "Expired",
              "description"     => "Время жизни оффера истекло, он так и не был принят получателем"
            ],
            [
              "id"              => "6",
              "status"          => "Canceled",
              "description"     => "Отправитель отменил оффер"
            ],
            [
              "id"              => "7",
              "status"          => "Declined",
              "description"     => "Получатель отклонил оффер"
            ],
            [
              "id"              => "8",
              "status"          => "InvalidItems",
              "description"     => "Некоторые из вещей, указанных в оффере, более недоступны (отмечены флагом missing)"
            ],
            [
              "id"              => "9",
              "status"          => "NeedsConfirmation",
              "description"     => "Оффер ещё не отправлен, и виден лишь отправителю, который должен его подтвердить через email/телефон"
            ],
            [
              "id"              => "10",
              "status"          => "CanceledBySecondFactor",
              "description"     => "Любая из сторон отменила оффер через email/телефон, оффер виден обеим сторонам, даже если отправитель отменил его до отправки"
            ],
            [
              "id"              => "11",
              "status"          => "InEscrow",
              "description"     => "Оффер помещён в escrow на соотв.время, вещи удалены из инвентарей обменивающихся, и будут доставлены по истечении escrow"
            ],
          ];

          // 3.3] Очистить таблицу $md8_bets_statuses
          call_user_func(function() USE ($md8_bets_statuses) {
            for($i=0; $i<count($md8_bets_statuses); $i++) {
              $md8_bets_statuses[$i]->bets()->detach();
              $md8_bets_statuses[$i]->delete();
            }
          });

          // 3.4] Наполнить таблицу указанными данными
          call_user_func(function() USE ($data) {
            for($i=0; $i<count($data); $i++) {
              $new = new \M9\Models\MD8_bets_statuses();
              $new->id          = $data[$i]['id'];
              $new->status      = $data[$i]['status'];
              $new->description = $data[$i]['description'];
              $new->save();
            }
          });

          // 3.5] Подтвердить транзакцию
          DB::commit();

          // 3.6] Удалить кэш по ключу md8_bets_statuses
          Cache::pull('md8_bets_statuses');

        }

      });

      // 4. md9_wins_statuses
      call_user_func(function(){

        // 1] Получить все статусы из БД, записав их в кэш на 10080 минут
        $md9_wins_statuses = Cache::remember('md9_wins_statuses', '10080', function() {
          return \M9\Models\MD9_wins_statuses::get();
        });

        // 2] Если содержимое коллекции $md9_wins_statuses не пусто, завершить
        if(count($md9_wins_statuses) != 0) return;

        // 3] Если коллекция $md9_wins_statuses пуста
        // - Наполнить таблицу указанными данными.
        if(count($md9_wins_statuses) == 0) {

          // 3.1] Начать транзакцию
          DB::beginTransaction();

          // 3.2] Подготовить массив с данными
          $data = [
            [
              "id"              => "1",
              "status"          => "Wait",
              "description"     => "Готовится к выплате"
            ],
            [
              "id"              => "2",
              "status"          => "Ready",
              "description"     => "Готов к выплате"
            ],
            [
              "id"              => "3",
              "status"          => "Active",
              "description"     => "Готов к выплате, есть активный оффер"
            ],
            [
              "id"              => "4",
              "status"          => "Paid",
              "description"     => "Успешно выплачен"
            ],
            [
              "id"              => "5",
              "status"          => "Expired",
              "description"     => "Выигрыш не был выплачен, и истёк"
            ]
          ];

          // 3.3] Очистить таблицу $md9_wins_statuses
          call_user_func(function() USE ($md9_wins_statuses) {
            for($i=0; $i<count($md9_wins_statuses); $i++) {
              $md9_wins_statuses[$i]->wins()->detach();
              $md9_wins_statuses[$i]->delete();
            }
          });

          // 3.4] Наполнить таблицу указанными данными
          call_user_func(function() USE ($data) {
            for($i=0; $i<count($data); $i++) {
              $new = new \M9\Models\MD9_wins_statuses();
              $new->id          = $data[$i]['id'];
              $new->status      = $data[$i]['status'];
              $new->description = $data[$i]['description'];
              $new->save();
            }
          });

          // 3.5] Подтвердить транзакцию
          DB::commit();

          // 3.6] Удалить кэш по ключу md9_wins_statuses
          Cache::pull('md9_wins_statuses');

        }

      });

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C39_init_db_references from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C39_init_db_references']);
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

