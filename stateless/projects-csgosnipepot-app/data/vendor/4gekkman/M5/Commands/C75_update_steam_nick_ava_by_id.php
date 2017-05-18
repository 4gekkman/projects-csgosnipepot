<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update steam nick and avatar of specified user by steamid
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id
 *        id_steam
 *        besides_groups
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
class C75_update_steam_nick_ava_by_id extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Назначить значения по умолчанию некоторым входящим параметрам
     *  3. Попробовать найти в БД пользователя, чей ник/аватар надо обновить
     *  4. Запросить HTML профиля пользователя $user
     *  5. Создать новые объекты DOMDocument и DOMXpath, загрузить в них $html
     *  6. Проверить, скрыт ли профиль пользователя
     *  7. Получить аватар и ник из $html
     *  8. Обновить ник и аватар пользователя в БД
     *  9. Сохранить аватар
     *
     *  m. Сделать commit
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------//
    // Update steam nick and avatar of specified user by steamid //
    //-----------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [

        "id"              => ["required_without:steam_id", "regex:/^[1-9]+[0-9]*$/ui"],
        "id_steam"        => ["required_without:id", "regex:/^[1-9]+[0-9]*$/ui"],
        "besides_groups"  => ["array"]

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Назначить значения по умолчанию некоторым входящим параметрам

        // besides_groups
        if(!array_key_exists('besides_groups', $this->data))
          $this->data['besides_groups'] = [];

      // 3. Попробовать найти в БД пользователя, чей ник/аватар надо обновить

        // 3.1. Если передан id, попробовать найти пользователя по ID
        if(array_key_exists('id', $this->data))
          $user = \M5\Models\MD1_users::with(['groups'])->where('id', $this->data['id'])->first();

        // 3.2. Если id не передан, но передан steam_id, попробовать найти пользователя по нему
        else if(array_key_exists('id_steam', $this->data))
          $user = \M5\Models\MD1_users::with(['groups'])->where('ha_provider_uid', $this->data['id_steam'])->first();

        // 3.3. Если $user не найден, вернуть ошибку
        if(empty($user))
          throw new \Exception('Не удалось найти в БД пользователя с ID = '.$this->data['id'].' и steam id = '.$this->data['id_steam']);

        // 3.4. Если пользователь состоит в группе Winners, завершить без ошибки
        if(count(collect($user['groups'])->pluck('name')->intersect($this->data['besides_groups'])) != 0)
          return [
            "status"  => 0,
            "data"    => [
              "is_updated" => 0,
              "why"        => "Пользователь состоит в одной из групп".implode(",", $this->data['besides_groups'])
            ]
          ];

      // 4. Запросить HTML профиля пользователя $user
      $html = call_user_func(function() USE ($user) {

        // 4.1. Подготовить запрос
        $request = new \GuzzleHttp\Psr7\Request("GET", "http://steamcommunity.com/profiles/".$user->ha_provider_uid);

        // 4.2. Осуществить запрос
        $response = (new \GuzzleHttp\Client())->send($request, [
          'connect_timeout' => 15.00,
          'query'           => [],
          'form_params'     => [],
          'http_errors'     => false
        ]);

        // 4.3. Подготовить результаты
        $results = [
          'response'  => $response,
          'status'    => $response->getStatusCode(),
          'body'      => $response->getBody()
        ];

        // 4.4. Если код ответа не 200, сообщить и завершить
        if($results['status'] != 200)
          throw new \Exception('Unexpected response from Steam: code '.$response->getStatusCode());

        // 4.n. Вернуть результат
        return $results['body'];

      });

      // 5. Создать новые объекты DOMDocument и DOMXpath, загрузить в них $html
      libxml_use_internal_errors(true);
      $doc = new \DOMDocument();
      $doc->loadHTML($html);
      $xpath = new \DOMXPath($doc);

      // 6. Проверить, скрыт ли профиль пользователя

        // 6.1. Проверить
        $is_profile_hided = call_user_func(function() USE ($xpath) {

          // 6.1. Попробовать найти div с классами "profile_page private_profile"
          $div = $xpath->query('.//div[contains(@class, "profile_page private_profile")]')->length;

          // 6.2. Если $div не найден, вернуть false
          if($div == 0)
            return false;

          // 6.3. Елси $div найден, вернуть true
          else
            return true;

        });

        // 6.2. Если профиль скрыт, завершить
        if($is_profile_hided == true)
          return [
            "status"  => 0,
            "data"    => [
              "is_updated" => 0,
              "why"        => "Профиль пользователя скрыт"
            ]
          ];

      // 7. Получить аватар и ник из $html

        // 7.1. Получить ник
        $nickname = call_user_func(function() USE ($xpath) {
          $nickname = $xpath->query('.//span[contains(@class, "actual_persona_name")]');
          if($nickname->length != 0)
            return $nickname->item(0)->nodeValue;
          else
            return "";
        });

        $avatar = call_user_func(function() USE ($xpath) {
          $avatar = $xpath->query('.//div[contains(@class, "playerAvatar")]/img/@src');
          if($avatar->length != 0)
            return $avatar->item(0)->nodeValue;
          else
            return "";
        });

        // 7.3. Если $nickname или $avatar пусты, завершить
        if(empty($nickname) || empty($avatar))
          return [
            "status"  => 0,
            "data"    => [
              "is_updated" => 0,
              "why"        => "Не удалось получить аватар или ник пользователя, или что-то из них пустое. Возможно, пользователь не авторизован."
            ]
          ];

      // 8. Обновить ник и аватар пользователя в БД
      $user->nickname = $nickname;
      $user->avatar_steam = $avatar;
      $user->save();

      // 9. Сохранить аватар
      $result = runcommand('\M7\Commands\C1_saveimage', [
        "url"         => $avatar,
        "group"       => "",
        "params"      => [
          "name"        => $user->id,
          "sizes"       => [ [184, 184] ],
          "types"       => ["image/jpeg"],
          "filters"     =>  []
        ],
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // m. Сделать commit
      DB::commit();

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "is_updated" => 0,
          "nickname"   => $nickname,
          "avatar"     => $avatar
        ]
      ];







    } catch(\Exception $e) {
        $errortext = 'Invoking of command C75_update_steam_nick_ava_by_id from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C75_update_steam_nick_ava_by_id']);
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

