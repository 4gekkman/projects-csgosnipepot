<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Clone MDLWR package from github to this project
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
class T33_clone extends Command
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

    protected $signature = 'm1:clone';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Clone MDLWR package from github to this project';

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

    // Настроить Storage для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path('')]);
    $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

    // Настроить Storage2 для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path('')]);
    $this->storage2 = new \Illuminate\Filesystem\Filesystem();


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
     *  1. Обновить приложение перед клонированием пакета с github
     *  2. Получить токен от github
     *  3. Получить список всех (public & private) пакетов пользователя 4gekkman с github
     *  4. Попросить пользователя выбрать, какой пакет он хочет клонировать
     *  5. Провести всестороннюю проверку возможности клонировать пакет $packid в этот проект
     *  6. Клонировать репозиторий $packid в vendor/4gekkman с помощью $token
     *  7. Добавить пр.имён пакета $packid в composer.json проекта -> autoload -> psr-4
     *  8. Добавить запись о пакете $packid в GitAutoPushScripts, с учётом имени папки проекта
     *
     *  n. Обновить приложение после клонирования пакета с github
     *
     *
     */

    // 1. Обновить приложение перед клонированием пакета с github
    //$this->call('m1:parseapp');

    // 2. Получить токен от github

      // 2.1. Получить
      $token = call_user_func(function(){

        // 1] Проверить работоспособность пароля и токена для github, указанных в конфиге M1
        $check = runcommand('\M1\Commands\C48_github_check');
        if($check['status'] != 0)
          return "";

        // 2] Вернуть токен от github
        return $check['data']['token'];

      });

      // 2.2. Если токен получить не удалось, сообщить и завершить
      if(empty($token)) {
        $this->error("The password/token for github from config not working.");
        return;
      }

    // 3. Получить список всех (public & private) пакетов пользователя 4gekkman с github
    // - Но только MWR-пакеты и DL-пакеты 10k+ серии.

      // 3.1. Получить массив имён всех пакетов пользователя 4gekkman с github
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
        $result = collect($result)->filter(function($item){
          if(!preg_match("/^([MWR]{1}[1-9]{1}[0-9]*|[DL]{1}[0-9]{5,100})$/ui", $item))
            return false;
          return true;
        })->toArray();

        // n) Вернуть результаты
        return [
          "success"       => true,
          "result"        => $result
        ];

      });

      // 3.2. Если получить массив имён пакетов с github не удалось
      if($all_github_user_packs['success'] == false) {
        $this->error("Couldn't get packages list from github.");
        return;
      }

    // 4. Попросить пользователя выбрать, какой пакет он хочет клонировать
    $packid = $this->choice('Which M-package do you want to clone?', array_combine($all_github_user_packs['result'], $all_github_user_packs['result']));

    // 5. Провести всестороннюю проверку возможности клонировать пакет $packid в этот проект
    // - Функция возвращает структуру следующего содержания:
    //
    //   [
    //     verdict: <true/false>,
    //     error:   <тест ошибки>
    //   ]
    //

      // 5.1. Выяснить, может ли $packid быть клонирова в этот проект
      // - Если нет, то почему?
      $whether_could_be_cloned = call_user_func(function() USE ($packid, $token, $all_github_user_packs) {

        // 1] Подготовить чек-лист
        $checklist = [
          "format" => [
            "verdict" => false,
            "error"   => "Wrong package ID format. Valid examples: M1, D10000, L10000, W1, R1."
          ],
          "presence" => [
            "verdict" => false,
            "error"   => "This package (".$packid.") is already in the project among: "
          ],
          "github" => [
            "verdict" => false,
            "error"   => "This package is absent on github, or we could not verify."
          ]
        ];

        // 2] Выяснить, верный ли формат имеет $packid
        call_user_func(function() USE (&$checklist, $packid) {

          // 2.1] Провести валидацию
          $validator = r4_validate(['packid' => $packid], [
            "packid"              => ["required", "regex:/^([MWR]{1}[1-9]{1}[0-9]*|[DL]{1}[0-9]{5,100})$/ui"],
          ]); if($validator['status'] == -1)
            return;

          // 2.2] Если всё в порядке
          $checklist['format']['verdict'] = true;

        });

        // 3] Выяснить, есть ли уже пакет в проекте
        call_user_func(function() USE (&$checklist, $packid) {

          // 3.1] Получить список имён всех пакетов в vendor/4gekkman
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('')]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          $all_package_names = collect($this->storage->directories('vendor/4gekkman'))->map(function($item){
            return preg_replace("#vendor/4gekkman/#ui", "", $item);
          })->toArray();

          // 3.2] Если $packid есть в $all_package_names
          if(in_array($packid, $all_package_names)) {
            $checklist['presence']['error'] = $checklist['presence']['error'] . implode(',', $all_package_names);
            return;
          }

          // 3.3] Если нет
          $checklist['presence']['verdict'] = true;

        });

        // 4] Выяснить, есть ли пакет, который требуется склонировать, на github
        call_user_func(function() USE (&$checklist, $packid, $token, $all_github_user_packs) {

          // 4.1] Если такого пакета нет
          if(!in_array($packid, $all_github_user_packs['result']))
            return;

          // 4.2] Если такой пакет есть
          $checklist['github']['verdict'] = true;

        });

        // 5] Вынести финальный вердикт
        $final_verdict = call_user_func(function() USE ($checklist) {

          // 5.1] Попробовать найти в $checklist пункт с отрицательным вердиктом
          $notvalid = "";
          foreach($checklist as $item) {
            if($item['verdict'] == false) {
              $notvalid = $item;
              break;
            }
          }

          // 5.2] Если $notvalid не пуст, вернуть отрицательный вердикт
          if(!empty($notvalid))
            return $notvalid;

          // 5.3] А если пуст, вернуть положительный
          else
            return [
              "verdict" => true,
              "error"   => ""
            ];

        });

        // n] Вернуть результаты
        return [
          "verdict" => $final_verdict['verdict'],
          "error"   => $final_verdict['error']
        ];

      });

      // 5.2. Если нет, то сообщить, почему, и завершить
      if($whether_could_be_cloned['verdict'] == false) {
        $this->error("The package ".$packid." could not be cloned: ".$whether_could_be_cloned['error']);
        return;
      }

    // 6. Клонировать репозиторий $packid в vendor/4gekkman с помощью $token

      // 6.1. Подготовить команду
      $command =  'cd /root/.ssh/keys_from_server;' .
                  'cp id_rsa4clone ../id_rsa4clone;' .
                  'cd ..;' .
                  'chmod 400 id_rsa4clone;' .
                  'eval `ssh-agent -s`;'.
                  'ssh-add id_rsa4clone;' .
                  'ssh-keyscan github.com >> /root/.ssh/known_hosts;' .
                  'cd '.base_path('vendor/4gekkman').';'.
                  'git clone git@github.com:4gekkman/'.$packid;

      // 6.2. Выполнить клонирование
      $clone_result = shell_exec($command);

      // 6.3. Если $clone_result пуст, сообщить и завершить
      if(empty($clone_result)) {
        $this->error("Can't clone repo ".$packid." from github for some reason.");
        return;
      }

      // 6.4. Иначе, сообщить, что репозиторий успешно склонирован
      else
        $this->info('Repository '.$packid.' was successfully cloned.');

    // 7. Добавить пр.имён пакета $packid в composer.json проекта -> autoload -> psr-4

      // 7.1. Получить содержимое composer.json проекта
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('')]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $composer = $this->storage->get('composer.json');

      // 7.2. Получить содержимое объекта "psr-4" из $composer в виде массива
      preg_match("/\"psr-4\" *: *\{.*\}/smuiU", $composer, $namespaces);
      $namespaces = preg_replace("/\"psr-4\" *: */smuiU", '', $namespaces);
      $namespaces = preg_replace("/['\n\r\s\{\}]/smuiU", '', $namespaces);
      $namespaces = explode(',', $namespaces[0]);
      $namespaces = array_values(array_filter($namespaces, function($item){
        return !empty($item);
      }));

      // 7.3. Добавить в $namespaces пространство имён нового пакета
      array_push($namespaces, '"' . $packid . '\\\\":"vendor/4gekkman/' . $packid . '"');

      // 7.4. Сформировать строку в формате значения "psr-4" из composer.json

        // 1] Подготовить строку для результата
        $namespaces_result = "{" . PHP_EOL;

        // 2] Вставить в $namespaces_result все значения из $commands
        for($i=0; $i<count($namespaces); $i++) {
          if($i != count($namespaces)-1 )
            $namespaces_result = $namespaces_result . "            " . $namespaces[$i] . "," . PHP_EOL;
          else
            $namespaces_result = $namespaces_result . "            " . $namespaces[$i] . PHP_EOL;
        }

        // 3] Завершить квадратной скобкой c запятой
        $namespaces_result = $namespaces_result . "        }";

      // 7.5. Заменить все \\\\ в $namespaces_result на \\\\\\
      $namespaces_result = preg_replace("/\\\\/smuiU", "\\\\\\", $namespaces_result);

      // 7.6. Вставить $namespaces_result в $composer
      $composer = preg_replace("/\"psr-4\" *: *\{.*\}/smuiU", '"psr-4": '.$namespaces_result, $composer);

      // 7.7. Заменить $composer
      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path('')]);
      $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
      $this->storage->put('composer.json', $composer);

    // 8. Добавить запись о пакете $packid в GitAutoPushScripts, с учётом имени папки проекта


      //$result = runcommand('\M1\Commands\C50_github_new_autopush', ["id_inner" => $this->data['id_inner']]);
      //if($result['status'] != 0)
      //  throw new \Exception($result['data']);







      $this->info('Успех!');



    // n. Обновить приложение после клонирования пакета с github
    //Artisan::queue('m1:run_light');

  }

}