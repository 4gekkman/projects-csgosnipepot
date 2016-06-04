<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Auth with Steam
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

  namespace M5\Commands;

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
class C69_auth_steam extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить имя канала для Websockets
     *  2. Провести валидацию
     *  3. Получить данные о пользователе через HybridAuth
     *  4. Получить полную информацию о пользователе
     *  5. Провести валидацию $full_profile_data
     *  6. Сформировать json-строку из $full_profile_data
     *  7. Попробовать найти пользователя с такими ha_provider_name и ha_provider_uid
     *  8. Если пользователь не найден, создать нового пользователя
     *  9. Аутентифицировать пользователя $user2auth
     *  10. Через websocket послать аутентиф.информацию по каналу websockets_channel
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------//
    // Произвести аутентификацию через Steam //
    //---------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить имя канала для Websockets
      $websockets_channel = $this->data['websockets_channel'];

      // 2. Провести валидацию
      $validator = r4_validate($this->data, [
        "websockets_channel"    => ["required"],
        "provider"              => ["required"],
        "hybridauth_config"     => ["required", "array"],

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 3. Получить первичные данные о пользователе через HybridAuth

        // 3.1. Проверить, если класс Hybrid_Auth недоступен, вернуть ошибку
        if(!class_exists("Hybrid_Auth")) throw new \Exception("Hybrid_Auth class is not available");

        // 3.2. Получить API-ключ от Steam из конфига M5
        $apikey = config("M5.steam_api_key");
        if(!$apikey || !is_string($apikey)) throw new \Exception("Steam api key is absent.");

        // 3.3. Создать объект класса Hybrid_Auth
        $hybridauth = new \Hybrid_Auth($this->data['hybridauth_config']);

        // 3.4. Попробовать аутентифицироваться через выбранного провайдера
        // - При заходе на контроллер через сайт это переадресует на сайт провайдера.
        // - При возврате запроса с сайта провайдера через HA Endpoint, это даст экземпляр провайдера.
        $adapter = $hybridauth->authenticate($this->data['provider']);

      // 4. Получить полную информацию о пользователе

        // 4.1. Получить из сессии некоторую информацию о профиле пользователя
        $not_full_profile = $hybridauth->storage()->get("hauth_session.steam.user")->profile;

        // 4.2. С помощью API-ключа и ID пользователя, получить через API Steam полную инфу о профиле
        $full_profile = call_user_func(function() USE ($apikey, $not_full_profile) {

          $apiUrl = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='
              . $apikey . '&steamids=' . $not_full_profile->identifier;
          $data = @file_get_contents($apiUrl);
          $data = json_decode($data);
          return $data;

        });

        // 4.3. Получить из $full_profile наиболее важные сведения
        $full_profile_data = [
          "provider"      => "steam",
          "steamid"       => $full_profile->response->players[0]->steamid,
          "personaname"   => $full_profile->response->players[0]->personaname ?: "",
          "profileurl"    => $full_profile->response->players[0]->profileurl ?: "",
          "avatar"        => $full_profile->response->players[0]->avatar ?: "",
          "avatarmedium"  => $full_profile->response->players[0]->avatarmedium ?: "",
          "avatarfull"    => $full_profile->response->players[0]->avatarfull ?: "",
        ];

      // 5. Провести валидацию $full_profile_data
      $validator = r4_validate($full_profile_data, [
        "provider"      => ["required"],
        "steamid"       => ["required", "regex:/^[0-9]+$/ui"],
        "personaname"   => ["required"],
        "profileurl"    => ["required"],
        "avatar"        => ["required"],
        "avatarmedium"  => ["required"],
        "avatarfull"    => ["required"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 6. Сформировать json-строку из $full_profile_data
      $full_profile_data_json = json_encode($full_profile_data, true);

      // 7. Попробовать найти пользователя с такими ha_provider_name и ha_provider_uid
      $user2auth = \M5\Models\MD1_users::where('ha_provider_name', $full_profile_data['provider'])
          ->where('ha_provider_uid', $full_profile_data['steamid'])
          ->first();

      // 8. Если пользователь не найден, создать нового пользователя
      // - И получить его экземпляр в переменную $user2auth
      if(empty($user2auth)) {

        $result = runcommand('\M5\Commands\C9_newuser', [
          "nickname"          => $full_profile_data['personaname'],
          "gender"            => "u",
          "isanonymous"       => "no",
          "password"          => "",
          "adminnote"         => "Вошёл через аккаунт Steam",
          "ha_provider_name"  => $full_profile_data['provider'],
          "ha_provider_uid"   => $full_profile_data['steamid'],
          "ha_provider_data"  => $full_profile_data_json
        ]);
        if($result['status'] != 0) {
          throw new \Exception($result['data']['errormsg']);
        }

      }

      // 9. Аутентифицировать пользователя $user2auth



      // 10. Через websocket послать аутентиф.информацию по каналу websockets_channel





      if(empty($user2auth))
        throw new \Exception('User with ha_provider_name = "'.$full_profile_data['provider'].'" not found.');






    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C69_auth_steam from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C69_auth_steam']);
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

