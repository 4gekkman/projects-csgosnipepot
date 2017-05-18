<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Create and replace gitmodules file of the project using deploy github account and token from config
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

  namespace M1\Commands;

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
class C56_make_gitmodules extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить токен для деплоя от github
     *  2. Получить имя github-аккаунта проекта
     *  3. Получить массив имён всех пакетов пользователя $account с github с помощью $token
     *  4. Сгенерировать содержимое для .gitmodules
     *  5. Перезаписать файл .gitmodules содержимым из $gitmodules
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------------------------//
    // Create and replace gitmodules file of the project using deploy github account and token from config //
    //-----------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить токен для деплоя от github
      $token = config("M1.deploy_github_oauth2");
      if(!$token)
        throw new \Exception('Отсутствует токен для деплоя проекта.');

      // 2. Получить имя github-аккаунта проекта
      $account = config("M1.deploy_github_account_name");
      if(!$token)
        throw new \Exception('Отсутствует имя github-аккаунта проекта.');

      // 3. Получить массив имён всех пакетов пользователя $account с github с помощью $token
      $all_github_user_packs = call_user_func(function() USE ($account, $token) {

        // 1] Создать экземпляр guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 2] Выполнить запрос
        $request_result = $guzzle->request('GET', 'https://api.github.com/user/repos', [
          'headers' => [
            'Authorization' => 'token '. $token
          ],
          'query' => [
            'affiliation' => 'owner',
            'direction'   => 'asc',
            'per_page'    => 10000
          ]
        ]);
        $status = $request_result->getStatusCode();
        $body = $request_result->getBody();
        if($status != 200)
          return [
            "success" => false,
            "result"  => []
          ];

        // 3] Получить результирующий массив
        $result = collect(json_decode($body, true))->pluck('name')->toArray();

        // 4] Провести фильтрацию результирующего массива
        $result = array_values(collect($result)->filter(function($item){
          if(!preg_match("/^([MWR]{1}[1-9]{1}[0-9]*|[DL]{1}[0-9]{5,100})$/ui", $item))
            return false;
          return true;
        })->toArray());

        // n) Вернуть результаты
        return [
          "success"       => true,
          "result"        => $result
        ];

      });

      // 4. Сгенерировать содержимое для .gitmodules
      // - Пример:
      //
      //  [submodule "M1"]
      //  path = project/vendor/4gekkman/M1
      //  url = https://<token>@github.com/owner/repo.git
      //
      //  [submodule "M2"]
      //  path = project/vendor/4gekkman/M2
      //  url = https://<token>@github.com/owner/repo.git
      //
      $gitmodules = call_user_func(function() USE ($token, $account, $all_github_user_packs) {

        // 1] Подготовить строку для результатов
        $result = "";

        // 2] Наполнить $result
        foreach($all_github_user_packs['result'] as $pack) {

          // 2.1] [submodule "$pack"]
          $result = $result . '[submodule "'.$pack.'"]' . PHP_EOL;

          // 2.2] path = project/vendor/$account/$pack
          $result = $result . "path = project/vendor/4gekkman/$pack" . PHP_EOL;

          // 2.3] url = https://$token@github.com/$account/$pack.git
          $result = $result . "url = https://$token@github.com/$account/$pack.git" . PHP_EOL . PHP_EOL;

        }

        // n] Вернуть результат
        return $result;

      });

      // 5. Перезаписать файл .gitmodules содержимым из $gitmodules
      call_user_func(function() USE ($gitmodules) {

        // 1] Создать файл .gitmodules_temp с содержимым $gitmodules
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => "/"]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        $create_result = $this->storage->put(preg_replace("/\/[^\/]*$/ui", "", base_path())."/.gitmodules_temp", $gitmodules);

        // 2] Если создать gitmodules_temp удалось
        if($create_result) {

          // 2.1] Удалить gitmodules, если есть
          $delete_result = $this->storage->delete(preg_replace("/\/[^\/]*$/ui", "", base_path())."/.gitmodules");

          // 2.2] Переименовать gitmodules_temp в gitmodules
          $this->storage->move(preg_replace("/\/[^\/]*$/ui", "", base_path())."/.gitmodules_temp", preg_replace("/\/[^\/]*$/ui", "", base_path())."/.gitmodules");

        }


      });


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C56_make_gitmodules from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C56_make_gitmodules']);
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

