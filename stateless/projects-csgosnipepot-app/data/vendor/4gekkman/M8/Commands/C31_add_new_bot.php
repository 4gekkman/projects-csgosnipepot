<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Add new empty bot
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        steamid
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

  namespace M8\Commands;

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
class C31_add_new_bot extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Убедиться, что такого steamid нет в таблице пользователей
     *  3. Убедитсья, что такого steamid нет в таблице ботов
     *  4. Проверить валидность steamid и открытость инвентаря бота
     *  5. Создать пользователя с ha_provider_name = steam и ha_provider_uid = Steam ID
     *  6. Добавить этого пользователя в группы Steam-юзеров и Steam-ботов (их названия брать из конфига)
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------//
    // Создать нового пустого бота //
    //-----------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "steamid"              => ["required", "regex:/^[0-9]+$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Убедиться, что такого steamid нет в таблице пользователей
      // - Искать в строках с ha_provider_name = steam
      $users = \M5\Models\MD1_users::where('ha_provider_name', 'steam')->where('ha_provider_uid', $this->data['steamid'])->get();
      if(count($users) != 0)
        throw new \Exception('There is already a user with that Steam ID');

      // 3. Убедитсья, что такого steamid нет в таблице ботов
      $users = \M8\Models\MD1_bots::where('steamid', $this->data['steamid'])->get();
      if(count($users) != 0)
        throw new \Exception('There is already a user with that Steam ID');

      // 4. Проверить валидность steamid и открытость инвентаря бота
      $result = runcommand('\M8\Commands\C4_getinventory', [
        'steamid' => $this->data['steamid']
      ]);
      if($result['status'] != 0)
        throw new \Exception("Not valid steamid (bot's inventory must be public)");

      // 5. Создать пользователя с ha_provider_name = steam и ha_provider_uid = Steam ID

        // Создать
        $result = runcommand('\M5\Commands\C9_newuser', [
          'nickname'          => '[New bot]',
          'gender'            => 'u',
          'isanonymous'       => 'no',
          'ha_provider_name'  => 'steam',
          'ha_provider_uid'   => $this->data['steamid'],
          'ha_provider_data'  => json_encode([], JSON_UNESCAPED_UNICODE)
        ]);
        if($result['status'] != 0)
          throw new \Exception($result['data']['errormsg']);

        // Получить его ID
        $new_user_id = $result['data']['id'];

      // 6. Добавить этого пользователя в группы Steam-юзеров и Steam-ботов (их названия брать из конфига)

        // 6.1. Получить экземпляр пользователя $new_user_id
        $new_user = call_user_func(function() USE ($new_user_id) {

          // 1] Попробовать найти пользователя с ID = $new_user_id
          $user = \M5\Models\MD1_users::find($new_user_id);

          // 2] Если $user пуст, возбудить исключение
          if(empty($user))
            throw new \Exception("Can't find steam user with id = '".$new_user_id."'");

          // 3] Вернуть результат
          return $user;

        });

        // 6.2. Получить экземпляр группы пользователей
        $group_steam_users = call_user_func(function(){

          // 1] Получить название группы Steam-пользователей из конфига M8
          $group_name = config("M8.group_steam_users");
          if(empty($group_name))
            throw new \Exception("Can't find name for steam users group in the M8 config");

          // 2] Получить группу $group_name
          $group = \M5\Models\MD2_groups::where('name', $group_name)->first();

          // 3] Если $group пуста, возбудить исключение
          if(empty($group))
            throw new \Exception("Can't find steam users group '".$group."'");

          // 4] Вернуть результат
          return $group;

        });

        // 6.3. Получить экземпляр группы ботов
        $group_steam_bots = call_user_func(function(){

          // 1] Получить название группы Steam-ботов из конфига M8
          $group_name = config("M8.group_steam_bots");
          if(empty($group_name))
            throw new \Exception("Can't find name for steam bots group in the M8 config");

          // 2] Получить группу $group_name
          $group = \M5\Models\MD2_groups::where('name', $group_name)->first();

          // 3] Если $group пуста, возбудить исключение
          if(empty($group))
            throw new \Exception("Can't find steam bots group '".$group."'");

          // 4] Вернуть результат
          return $group;

        });

        // 6.4. Связать $new_user с $group_steam_users и $group_steam_bots

          // 1] $new_user с $group_steam_users
          if(!$group_steam_users->users->contains($new_user->id))
            $group_steam_users->users()->attach($new_user->id);

          // 2] $new_user с $group_steam_bots
          if(!$group_steam_bots->users->contains($new_user->id))
            $group_steam_bots->users()->attach($new_user->id);



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C31_add_new_bot from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C31_add_new_bot']);
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
