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
     *  9. Если пользователь найден, восстановить и обновить его аккаунт новыми данными
     *  10. Аутентифицировать пользователя $user2auth
     *  11. Создать группу "Steam Users", если её ещё нет
     *  12. Добавить пользователя $user2auth в группу $steamusers
     *  13. Через websocket послать аутентиф.информацию по каналу websockets_channel
     *  14. Через websocket послать всем подписчикам текущее кол-во аутентифицированных Steam-пользователей
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
        if(empty($full_profile)) {
          throw new \Exception('Steam API-key is not valid. Please contact administrator.');
        }

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
      $user2auth = \M5\Models\MD1_users::withTrashed()->where('ha_provider_name', $full_profile_data['provider'])
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
          "avatar_steam"      => $full_profile_data["avatarfull"],
          "ha_provider_name"  => $full_profile_data['provider'],
          "ha_provider_uid"   => $full_profile_data['steamid'],
          "ha_provider_data"  => $full_profile_data_json
        ]);
        if($result['status'] != 0) {
          throw new \Exception($result['data']['errormsg']);
        }
        else {
          $user2auth = \M5\Models\MD1_users::find($result['data']['id']);
        }

      }

      // 9. Если пользователь найден, восстановить и обновить его аккаунт новыми данными
      else {

        $user2auth->restore();
        $user2auth->nickname          = $full_profile_data['personaname'];
        $user2auth->avatar_steam      = $full_profile_data['avatarfull'];
        $user2auth->ha_provider_data  = $full_profile_data_json;
        $user2auth->save();

      }

      // 10. Аутентифицировать пользователя $user2auth
      // - Если он найден и не заблокирован.

        // 10.1. Если пользователь не найден, завершить
        if(empty($user2auth))
          throw new \Exception("The user is not found (auth error)");

        // 10.2. Если пользователь заблокирован, завершить
        if($user2auth->is_blocked !== 0)
          throw new \Exception("The user is blocked.");

        // 10.3. Извлечь для $user2auth время жизни аутентификации в часах
        $lifetime = runcommand('\M5\Commands\C57_get_auth_limit', ['id_user' => $user2auth->id]);
        if($lifetime['status'] != 0)
          throw new \Exception($lifetime['data']);
        $lifetime = $lifetime['data'];

        // 10.4. Получить дату и время, когда эта аутентификация истекает
        $expired_at = \Carbon\Carbon::now()->addHours($lifetime);

        // 10.5. Создать для пользователя новую запись в таблице аутентификаций, связать
        $auth = new \M5\Models\MD8_auth();
        $auth->expired_at = $expired_at;
        $auth->save();
        $auth->users()->attach($user2auth->id);
        $auth->save();

        // 10.6. Сфоромировать json с новыми аутентификационными данными
        $user2auth_excepted = collect($user2auth)->except(['adminnote', 'password_hash', 'ha_provider_name', 'ha_provider_data', 'created_at', 'updated_at', 'deleted_at']);
        $json = [
          'auth'    => $auth,
          'user'    => $user2auth_excepted,
          'is_anon' => 0
        ];
        $json = json_encode($json, JSON_UNESCAPED_UNICODE);
        $json_encrypted = Crypt::encrypt($json);

        // 10.7. Записать пользователю новый аутентиф.кэш в сессию
        session(['auth_cache' => $json]);

        // 10.8. Записать пользователю новую куку с временем жизни $lifetime*60 минут
        Cookie::queue('auth', $json, $lifetime*60);

      // 11. Создать группу "Steam Users", если её ещё нет
      $steamusers = \M5\Models\MD2_groups::where('name', 'SteamUsers')->first();
      if(empty($steamusers)) {

        $result = runcommand('\M5\Commands\C10_newgroup', [
					"name"        => "SteamUsers",
					"description" => "Входящие через Steam пользователи",
					"isadmin"     => "no"
        ]);
        if($result['status'] != 0) {
          throw new \Exception($result['data']['errormsg']);
        }

      }
      if(empty($steamusers)) $steamusers = \M5\Models\MD2_groups::where('name', 'SteamUsers')->first();

      // 12. Добавить пользователя $user2auth в группу $steamusers
      // - Если его ещё там нет
      if(!$steamusers->users->contains($user2auth->id))
        $steamusers->users()->attach($user2auth->id);

      // 13. Через websocket послать аутентиф.информацию по каналу websockets_channel
      Event::fire(new \R2\Broadcast([
        'channels'  => [$this->data['websockets_channel']],
        'queue'     => 'auth',
        'data'      => [
          'status'  => 0,
          'user'    => json_encode($user2auth_excepted->toArray(), JSON_UNESCAPED_UNICODE)
        ]
      ]));

      // 14. Через websocket послать всем подписчикам текущее кол-во аутентифицированных Steam-пользователей

        // 14.1. Получить
        $logged_in_steam_users = call_user_func(function(){

          // 1] Получить
          $result = runcommand('\M5\Commands\C71_count_logged_in_steam_users', []);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результат
          return $result['data']['number'];

        });

        // 14.2. Послать
        Event::fire(new \R2\Broadcast([
          'channels' => ['m5:count_logged_in_steam_users'],
          'queue'    => 'chat',
          'data'     => [
            'number' => $logged_in_steam_users
          ]
        ]));


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C69_auth_steam from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C69_auth_steam']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $e->getMessage(),
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

