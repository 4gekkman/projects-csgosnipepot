<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update forks in projects github account via token
 *
 *  Аргументы
 *  ---------
 *
 *
 *  Опции
 *  -----
 *
 *
 *
 *
 */

//-----------------------------------//
// Пространство имён artisan-команды //
//-----------------------------------//
// - Пример:  M1\Console

  namespace M1\Console;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Базовые классы, необходимые для работы команд вообще
  use Illuminate\Console\Command;

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


//--------------------//
// Консольная команда //
//--------------------//
class T35_update_forks extends Command
{

  //---------------------------//
  // 1. Шаблон artisan-команды //
  //---------------------------//
  //  - '[имя] {user}'        | задать аргумент
  //  - '[имя] {user=foo}'    | задать аргумент с значением по умолчанию
  //  - '[имя] {--queue}'     | задать аргумент-опцию
  //  - '[имя] {--queue=}'    | задать аргумент-опцию со значением
  //  - '[имя] {--queue=foo}' | задать аргумент-опцию со значением по умолчанию
  //  - '[имя] {user : desc}' | задать описание аргументу / опции
  // - TODO: настроить шаблон консольной команды

    protected $signature = 'm1:update_forks';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Update forks in projects github account via token';

  //---------------------------------------------------//
  // 3. Свойства для принятия значений из конструктора //
  //---------------------------------------------------//
  // - TODO: подготовить св-ва для принятия значений из конструктора

    //protected $drip;

  //----------------------------------------------------------//
  // 4. DI и другая подготовка объекта команды в конструкторе //
  //----------------------------------------------------------//
  public function __construct()  // здесь можно сделать DI, например: __construct(DripEmailer $drip)
  {

      // Вызвать конструктор класса Command
      parent::__construct();

      // Записать значение аргумента в св-во $drip
      //$this->drip = $drip;

  }

  //------------------------//
  // 5. Код artisan-команды //
  //------------------------//
  //  - Получение значений аргументов artisan-команды в handle():
  //
  //    - $this->argument()    | извлечь значение аргумента по имени, или массив всех аргументов
  //    - $this->option()      | аналог argument, но без аргументов возвращает массив всех опций
  //
  //  - Осуществление запроса информации у пользователя:
  //
  //    - $this->ask()         | запросить ввод пользователем строки
  //    - $this->secret()      | запросить ввод пользователем строки в безопасном невидимом режиме
  //    - $this->confirm()     | спросить, согласен ли (y/n)
  //    - $this->anticipate()  | дать выбрать из нескольких вариантов + свободный ввод
  //    - $this->choice()      | дать выбрать строго из нескольких вариантов (без сводобного ввода)
  //
  //        $x = $this->ask('Введите строку');
  //        $y = $this->choice('Введите строку', ['1'=>'Вариант №1', '2'=>'Вариант 2'], '1');
  //        $z = $this->confirm('Да?', true);
  //
  //  - Вывод информации в окно терминала:
  //
  //    - $this->info()        | вывести в окно терминала сообщение цвета info
  //    - $this->comment()     | вывести в окно терминала сообщение цвета comment
  //    - $this->question()    | вывести в окно терминала сообщение цвета question
  //    - $this->error()       | вывести в окно терминала сообщение цвета error
  //    - $this->table()       | вывести в окно терминала таблицу данных
  //
  //        $this->table(['header1','header2','header3'], ['row1_cell1', 'row1_cell2', 'row1_cell3'], ['row2_cell1', 'row2_cell2', 'row2_cell3'] )
  //
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Получить из конфига параметры деплой-аккаунта проекта
     *  2. Получить массив имён всех пакетов пользователя $account с github
     *  3. Обновить каждый из форков $all_github_user_packs
     *    3.1. Попытатсья создать pull-запрос
     *    3.2. Провести анализ полученных результатов
     *
     *
     */

      // a. Сообщить о начале выполнения команды
      $this->info('-------------------------------------------------------');
      $this->info('---------- Обновление форков деплой-аккаунта ----------');
      $this->info('-------------------------------------------------------');

      // 1. Получить из конфига параметры деплой-аккаунта проекта

        // 1.1. Прокомментировать
        $this->comment('----> Получаю из конфига параметры деплой-аккаунта проекта...');

        // 1.2. Имя деплой-аккаунта на github
        $account = config("M1.deploy_github_account_name");
        if(empty($account)) {
          $this->error('----> Автообновление форков на деплой-аккаунте проектов включено, а его имя в конфиге не указано.');
          return;
        }

        // 1.3. Oauth2-токен деплой-аккаунта на github
        $token = config("M1.deploy_github_oauth2");
        if(empty($token)) {
          $this->error('----> Автообновление форков на деплой-аккаунте проектов включено, а его oauth2-токен в конфиге не указано.');
          return;
        }

      // 2. Получить массив имён всех пакетов пользователя $account с github

        // 2.1. Прокомментировать
        $this->comment("----> Получаю массив имён всех форков деплой-аккаунта $account с github");

        // 2.2. Получить
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

        // 2.3. Если получить не удалось
        if($all_github_user_packs['success'] == false) {
          $this->error("----> Не удалось получить список форков с github для деплой-аккаунта ".$account);
          return;
        }

        // 2.4. Если получить удалось, но массив пуст
        if($all_github_user_packs['success'] == true && empty($all_github_user_packs['result'])) {
          $this->error("----> Список форков на github пуст для деплой-аккаунта ".$account);
          return;
        }

        // 2.5. Вывести список форков
        $this->comment('----> '.json_encode($all_github_user_packs['result']));

      // 3. Обновить каждый из форков $all_github_user_packs

        // 3.1. Прокомментировать
        $this->comment("----> Обновляю каждый форк из полученного списка...");

        // 3.2. Обновить
        foreach($all_github_user_packs['result'] as $fork) {

          // 3.a. Прокомментировать
          $this->question("--> $fork <--");

          // 3.1. Попытатсья создать pull-запрос
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

          // 3.2. Провести анализ полученных результатов
          $analysis = call_user_func(function() USE ($pull) {

            // a] Подождать, пока не будет известен статус запроса
            while(!$pull['status']) sleep(1);

            // 1] Составить чек-лист
            $checklist = [

              // 1.1] Был ли PULL-запрос успешно создан?
              "is_created" => [
                "verdict" => false,
                "error"   => "",
                "number"  => ""
              ],

              // 1.2] Ответил ли github статусом 422 и ошибкой "No commits between..."
              "no_commits_422" => [
                "verdict" => false,
                "error"   => "",
                "status"  => 422,
              ],

              // 1.3] Ответил ли github статусом 422 и ошибкой "A pull request already exists..."
              "already_exists_422" => [
                "verdict" => false,
                "error"   => "",
                "status"  => 422,
              ],

              // 1.4] Ответил ли github статусом не 201, или статусом не 422 и одной из вышеперечисленных ошибок
              "unknown" => [
                "verdict" => false,
                "error"   => "",
                "status"  => "",
              ]

            ];

            // 2] is_created
            if($pull['status'] == 201) {
              $checklist['verdict'] = true;
              $checklist['number'] = $pull['contents']['number'];
            }

            // 3] no_commits_422
            if($pull['status'] == 422) {

              // 3.1] Получить сообщение об ошибке
              if(
                array_key_exists('errors', $pull['contents']) &&
                array_key_exists(0, $pull['contents']['errors']) &&
                array_key_exists('message', $pull['contents']['errors'][0])
              ) $message = $pull['contents']['errors'][0]['message'];
              else $message = "";

              // 3.2] Если $message содержит строку "No commits between"
              if(preg_match("/No commits between/ui", $message)) {
                $checklist['no_commits_422']['verdict'] = true;
                $checklist['no_commits_422']['error'] = $message;
              }

            }

            // 4] already_exists_422
            if($pull['status'] == 422) {

              // 4.1] Получить сообщение об ошибке
              if(
                array_key_exists('errors', $pull['contents']) &&
                array_key_exists(0, $pull['contents']['errors']) &&
                array_key_exists('message', $pull['contents']['errors'][0])
              ) $message = $pull['contents']['errors'][0]['message'];
              else $message = "";

              // 4.2] Если $message содержит строку "No commits between"
              if(preg_match("/A pull request already exists/ui", $message)) {
                $checklist['already_exists_422']['verdict'] = true;
                $checklist['already_exists_422']['error'] = $message;
              }

            }

            // 5] unknown
            if(
              $checklist['is_created']['verdict']         == false &&
              $checklist['no_commits_422']['verdict']     == false &&
              $checklist['already_exists_422']['verdict'] == false
            ) {

              // 4.1] Получить сообщение об ошибке
              if(
                array_key_exists('errors', $pull['contents']) &&
                array_key_exists(0, $pull['contents']['errors']) &&
                array_key_exists('message', $pull['contents']['errors'][0])
              ) $message = $pull['contents']['errors'][0]['message'];
              else $message = "";

              // 4.2] Записать инфу в checklist
              $checklist['unknown']['verdict']  = true;
              $checklist['unknown']['error']    = json_encode($pull['contents']);
              $checklist['unknown']['status']   = $pull['status'];

            }

            // n] Вернуть результат
            return $checklist;

          });

          // 3.3. На основе $analysis предпринять соответствующие действия

            // 3.3.1. Если is_created
            if($analysis['is_created']['verdict'] == true) {

              // 1] Прокомментировать
              $this->comment("- Успешно создан pull-запрос. Выполняю merge...");

              // 2] Выполнить merge
              $merge = call_user_func(function() USE ($token, $account, $fork, $analysis) {

                // 1] Создать экземпляр guzzle
                $guzzle = new \GuzzleHttp\Client();

                // 2] Выполнить запрос
                $request_result = $guzzle->request('PUT', "https://api.github.com/repos/$account/$fork/pulls/".$analysis['is_created']['number']."/merge", [
                  'headers' => [
                    'Authorization' => 'token '. $token
                  ],
                  'exceptions' => false
                ]);

                // n] Вернуть результаты
                return [
                  "status"    => $request_result->getStatusCode(),
                  "body"      => $request_result->getBody(),
                  "contents"  => json_decode($request_result->getBody()->getContents(), true),
                ];

              });

              // 3] Прокомментировать
              if($merge['status'] != 200) {
                $this->error("- Выполнить merge не удалось по какой-то причине.");
                continue;
              }
              else {
                $this->info("- Merge успешно выполнен!");
                continue;
              }

              // 4] Перейти к следующей итерации
              continue;

            }

            // 3.3.2. Если no_commits_422
            else if($analysis['no_commits_422']['verdict'] == true) {
              $this->comment("- Обновление не требуется, новые коммиты отсутствуют.");
              continue;
            }

            // 3.3.3. Если already_exists_422
            else if($analysis['already_exists_422']['verdict'] == true) {

              // 1] Прокомментировать
              $this->comment("- Pull-запрос для этого форка уже был создан ранее, запрашиваю его номер...");

              // 2] Запросить номер предыдущего pull-запроса
              $number = call_user_func(function() USE ($token, $account, $fork) {

                // 1] Создать экземпляр guzzle
                $guzzle = new \GuzzleHttp\Client();

                // 2] Выполнить запрос
                $request_result = $guzzle->request('GET', "https://api.github.com/repos/$account/$fork/pulls", [
                  'headers' => [
                    'Authorization' => 'token '. $token
                  ],
                  'exceptions' => false
                ]);

                // 3] Получить status, body и contents
                $results = [
                  "status"    => $request_result->getStatusCode(),
                  "body"      => $request_result->getBody(),
                  "contents"  => json_decode($request_result->getBody()->getContents(), true),
                ];

                // 4] Получить number
                $number = call_user_func(function() USE ($results) {

                  // 4.1] Если status != 200, вернуть пустую строку
                  if($results['status'] != 200) return "";

                  // 4.2] Если массив в contents пуст, вернуть ""
                  if(empty($results['contents'])) return "";

                  // 4.3] Если в contents нет number, вентуь пустую строку
                  if(!array_key_exists('number', $results['contents'][0])) return "";

                  // 4.4] Вернуть number
                  return $results['contents'][0]['number'];

                });

                // n] Вернуть результат
                return $number;

              });

              // 3] Прокомментировать, а если $number пуст, перейти к след.итерации
              if(!empty($number))
                $this->comment("- Номер найден: $number. Выполняю merge...");
              else {
                $this->error("- Номер НЕ найден по какой-то причине.");
                continue;
              }

              // 4] Выполнить merge
              $merge = call_user_func(function() USE ($token, $account, $fork, $number) {

                // 1] Создать экземпляр guzzle
                $guzzle = new \GuzzleHttp\Client();

                // 2] Выполнить запрос
                $request_result = $guzzle->request('PUT', "https://api.github.com/repos/$account/$fork/pulls/$number/merge", [
                  'headers' => [
                    'Authorization' => 'token '. $token
                  ],
                  'exceptions' => false
                ]);

                // n] Вернуть результаты
                return [
                  "status"    => $request_result->getStatusCode(),
                  "body"      => $request_result->getBody(),
                  "contents"  => json_decode($request_result->getBody()->getContents(), true),
                ];

              });

              // 5] Прокомментировать
              if($merge['status'] != 200) {
                $this->error("- Выполнить merge не удалось по какой-то причине.");
                continue;
              }
              else {
                $this->info("- Merge успешно выполнен!");
                continue;
              }

            }

            // 3.3.4. Если unknown
            else if($analysis['unknown']['verdict'] == true) {
              $this->comment("- Возникла неизвестная ошибка: ".$analysis['unknown']['error']);
              continue;
            }

        }


  }

}