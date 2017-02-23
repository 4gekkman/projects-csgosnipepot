<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update forks in projects github account via token
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
class C57_update_forks extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить из конфига значение выключателя автообновления форков
     *  2. Получить из конфига прочие параметры деплой-аккаунта проекта
     *  3. Получить массив имён всех пакетов пользователя $account с github
     *  4. Обновить каждый из форков $all_github_user_packs
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------//
    // Update forks in projects github account via token //
    //---------------------------------------------------//
    $res = call_user_func(function() { try {

      // 2. Получить из конфига прочие параметры деплой-аккаунта проекта

        // 1] Имя деплой-аккаунта на github
        $account = config("M1.deploy_github_account_name");
        if(empty($account))
          throw new \Exception('Автообновление форков на деплой-аккаунте проектов включено, а его имя в конфиге не указано.');

        // 2] Oauth2-токен деплой-аккаунта на github
        $token = config("M1.deploy_github_oauth2");
        if(empty($token))
          throw new \Exception('Автообновление форков на деплой-аккаунте проектов включено, а его oauth2-токен в конфиге не указано.');

      // 3. Получить массив имён всех пакетов пользователя $account с github
      $all_github_user_packs = call_user_func(function() USE ($token) {

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
        //$result = array_values(collect($result)->filter(function($item){
        //  if(!preg_match("/^([MWR]{1}[1-9]{1}[0-9]*|[DL]{1}[0-9]{5,100})$/ui", $item))
        //    return false;
        //  return true;
        //})->toArray());

        // n) Вернуть результаты
        return [
          "success"       => true,
          "result"        => $result
        ];

      });
      if($all_github_user_packs['success'] == false)
        throw new \Exception("Не удалось получить список форков с github для деплой-аккаунта ".$account);

      // 4. Обновить каждый из форков $all_github_user_packs
      foreach($all_github_user_packs['result'] as $fork) {

        // 4.1. Попытатсья создать pull-запрос
        $pull = call_user_func(function() USE ($account, $token, $fork) {

          // 1] Создать экземпляр guzzle
          $guzzle = new \GuzzleHttp\Client();

          // 2] Выполнить запрос
          $request_result = $guzzle->request('POST', "https://api.github.com/repos/$account/$fork/pulls", [
            'headers' => [
              'Authorization' => 'token '. $token
            ],
            'json' => [
              "title" => "С57_update_forks",
              "body"  => "Auto update forks by С57_update_forks",
              "head"  => "4gekkman:master",
              "base"  => "master"
            ],
            'exceptions' => false
          ]);

          // n] Вернуть результат
          return [
            "status"    => $request_result->getStatusCode(),
            "body"      => $request_result->getBody(),
            "contents"  => json_decode($request_result->getBody()->getContents(), true)
          ];

        });

        // 4.2. Провести анализ полученных результатов
        $analysis = call_user_func(function() USE ($pull) {

          // 1] Составить чек-лист
          $checklist = [

            // 1.1] Был ли PULL-запрос успешно создан?
            "is_created" => [
              "verdict" => false,
              "error"   => ""
            ],

            // 1.2] Ответил ли github статусом 422 и ошибкой "No commits between..."
            "no_commits_422" => [
              "verdict" => false,
              "error"   => ""
            ],

            // 1.3] Ответил ли github статусом 422 и ошибкой "A pull request already exists..."
            "already_exists_422" => [
              "verdict" => false,
              "error"   => ""
            ],

            // 1.4] Ответил ли github статусом не 201, или статусом не 422 и одной из вышеперечисленных ошибок
            "unknown" => [
              "verdict" => false,
              "error"   => ""
            ]

          ];

          // 2]



          // n] Вернуть результат
          return $checklist;


        });


        Log::info($pull['contents']['errors'][0]['message']);


        // 4.2. Если $pull прошёл успешно, выполнить merge
//        if($pull['status'] != 422) {
//
//          // 1] Создать экземпляр guzzle
//          $guzzle = new \GuzzleHttp\Client();
//
//          // 2] Выполнить запрос
//          $request_result = $guzzle->request('PUT', "https://api.github.com/repos/$account/$fork/pulls/".$pull['num']."/merge", [
//            'headers' => [
//              'Authorization' => 'token '. $token
//            ],
//            'exceptions' => false
//          ]);
//
//        }

        // 4.3. Подождать секунду
        sleep(1);



        break;

      }






    } catch(\Exception $e) {
        $errortext = 'Invoking of command C57_update_forks from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C57_update_forks']);
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

