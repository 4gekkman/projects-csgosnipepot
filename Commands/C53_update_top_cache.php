<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Updates TOP players cache
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force     | Обновлять кэш, ни смотря ни на какие другие условия
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
class C53_update_top_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  3. Получить кэш ТОПа игроков
     *  4. Если $cache не пуст, получить дату/время последнего обновления
     *  5. Обновить кэш ТОПа игроков, если он пуст, или старше 24 часов
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------//
    // Updates TOP players cache //
    //---------------------------//
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

      // 3. Получить кэш ТОПа игроков
      $cache = json_decode(Cache::get('m9:top_players'), true);

      // 4. Если $cache не пуст, получить дату/время последнего обновления
      if(!empty($cache))
        $updated_at = \Carbon\Carbon::parse($cache['updated_at']);

      // 5. Обновить кэш ТОПа игроков, если он пуст, или старше 24 часов
      if(
        empty($cache) ||
        (!empty($updated_at) && $updated_at->diffInHours(\Carbon\Carbon::now()) >= 24)
      ) {

        Log::info('update_tops');

        // 1] Получить массив ТОП20 пользователей по сумме win_fact_cents за всю историю побед
        // - В формате:
        //
        //    [
        //      <id_пользователя>: <кол-во выигранных центов>
        //    ]
        //
        $users_ids_cents = \M5\Models\MD1_users::select('md1_users.id', 'md2004.id_win', DB::raw('SUM(md4_wins.win_fact_cents) as totalsum'))
            ->leftJoin('m9.md2004', 'id', '=', 'm9.md2004.id_user')
            ->where('id_win', '!=', null)
            ->leftJoin('m9.md4_wins', 'id_win', '=', 'm9.md4_wins.id')
            ->groupBy('id')->orderByRaw("CAST(win_fact_cents as SIGNED) DESC")
            ->take(20)
            ->pluck('totalsum', 'id')
            ->toArray();

        // 2] Получить массив ID ТОП20 пользователей
        $users_ids = collect($users_ids_cents)->keys();

        // 3] Получить всех пользователей из $users_ids
        // - Сохранить порядок сортировки, как в $users_ids.
        // - Для каждого пользователя получить следующие столбцы:
        //
        //   • id             | id пользователя
        //   • nickname       | ник пользователя
        //   • avatar_steam   | аватар пользователя из steam
        //   • totalsum       | общее кол-во выигранных центов
        //   • wins_num       | общее кол-во побед
        //   • rounds_num     | общее кол-во раундов, в которых участвовал
        //
        $users = call_user_func(function() USE ($users_ids_cents) {

          // 3.1] Подготовить массив для результатов
          $results = [];

          // 3.2] Наполнить $results
          foreach($users_ids_cents as $id_user => $totalsum) {

            // 3.2.1] По $id_user получить пользователя
            $user = \M5\Models\MD1_users::where('id', $id_user)->first();
            if(empty($user))
              continue;

            // 3.2.2] Узнать кол-во побед этого пользователя
            $wins_num = \M9\Models\MD4_wins::whereHas('m5_users', function($query) USE ($id_user) {
              $query->where('id', $id_user);
            })->count();

            // 3.2.3] Узнать кол-во раундов, в которых участвовал пользователь
            $rounds_num = \M9\Models\MD2_rounds::whereHas('bets', function($query) USE ($id_user) {
              $query->whereHas('m5_users', function($query) USE ($id_user) {
                $query->where('id', $id_user);
              });
            })->count();

            // 3.2.n] Добавить данные в $results
            array_push($results, [
              'id'            => $id_user,
              'nickname'      => $user->nickname,
              'avatar_steam'  => $user->avatar_steam,
              'totalsum'      => (int)$totalsum,
              'wins_num'      => $wins_num,
              'rounds_num'    => $rounds_num,
            ]);

          }

          // 3.n] Вернуть результаты
          return $results;

        });

        // 4] Подготовить массив для записи в кэш
        $data2record = [
          'users'       => $users,
          'updated_at'  => \Carbon\Carbon::now()->toDateTimeString()
        ];

        // 5] Поместить $users в кэш на 24 часа
        Cache::put('m9:top_players', json_encode($data2record, JSON_UNESCAPED_UNICODE), 1440);

      }

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C53_update_top_cache from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C53_update_top_cache']);
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

