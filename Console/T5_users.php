<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get users list (can use filters)
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

  namespace M5\Console;

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
class T5_users extends Command
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

    protected $signature = 'm5:users';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Get users list (can use filters)';

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
     *  1. Подготовить массив параметров запроса
     *  2. Подготовить необходимый функционал
     *  3. Запросить стартовый набор фильтров
     *  4. Запустить демона
     *
     */

    // 1. Подготовить массив параметров запроса
    $options = [
      "page"            => 1,
      "pages_total"     => "",
      "items_at_page"   => 2,
      "filters"         => [
        "ids"             => [],
        "genders"         => [],
        "groups"          => [],
        "tags"            => [],
        "privileges"      => [],
        "privtypes"       => [],
        "m1_packages"     => [],
        "m1_commands"     => []
      ]
    ];

    // 2. Подготовить необходимый функционал
    $exit = "no";
    $functions_list = [];
    $functions_list = [

      //-----------------------//
      // 2.1. Общий функционал //
      //-----------------------//

      "info"     => ["description" => "Show reference", "src" => function() USE (&$options, &$functions_list){

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Наполнить $result
        foreach($functions_list as $key => $value)
          array_push($result, [$key, $value['description']]);

        // 3] Вывести таблицу с $result
        $this->table(['function', 'description'], $result);

      }],

      "exit"     => ["description" => "Quit the program", "src" => function() USE (&$options, &$exit){
        $exit = 'yes';
      }],

      "details"     => ["description" => "Get info about specified user in details", "src" => function() USE (&$options, &$exit){

        // 1] Спросить ID пользователя
        while(!is_numeric($id = $this->anticipate("Enter the user ID", []))) {}

        // 2] Получить пользователя с $id
        $user = \M5\Models\MD1_users::find($id);
        if(empty($user)) {
          $this->error('Пользователь с ID = '.$id.' не найден (среди активных, т.е. не мягко удалённых)');
          return;
        }

        // 3] Лениво подгрузить связи пользователя
        $user->load(['groups', 'tags', 'privileges']);

        // 4] Сформировать красивую строку для вывода
        $output = json_encode($user, JSON_PRETTY_PRINT);

        // 5] Вывести $output
        $this->info($output);

      }],

      //----------------------------//
      // 2.2. Управление пагинацией //
      //----------------------------//

      "n"     => ["description" => "Goto next page", "src" => function() USE (&$options){

        // 1] Если дальше некуда
        if($options['page'] >= $options['pages_total']) {
          $this->error('We are already at the last page #'.$options['page']);
          return;
        }

        // 2] Изменить номер страницы
        $options['page'] = +$options['page'] + 1;

      }],

      "p"     => ["description" => "Goto previous page", "src" => function() USE (&$options){

        // 1] Если дальше некуда
        if($options['page'] == 1) {
          $this->error('We are already at the first page');
          return;
        }

        // 2] Изменить номер страницы
        $options['page'] = +$options['page'] - 1;

      }],

      "goto"     => ["description" => "Goto specified page", "src" => function() USE (&$options){

        // 1] Спросить номер страницы, куда пользователь желает перейти
        $pagenum = $this->anticipate("Type page number", []);
        while(!is_numeric($pagenum) || +$pagenum<1 || +$pagenum>$options['pages_total']) {
          $this->error('Wrong page number');
          $pagenum = $this->anticipate("Type page number", []);
        }

        // 2] Перейти на указанный номер
        $options['page'] = $pagenum;

      }],

      //-----------------------------//
      // 2.3. Управление фильтрацией //
      //-----------------------------//

      "filters"            => ["description" => "Reset and update all filters", "src" => function() USE (&$options){

        // 1] Спросить, требуются ли какие-либо фильтры
        $need_filters = $this->choice('Do you want to use some filters', ['no' => 'no', 'yes' => 'yes'], 'no');

        // 2] Если фильтры требуются, запросить данные о них
        if($need_filters == 'yes') {

          // 2.1] ids
          $options['filters']['ids'] = $this->anticipate("By ids (specify user ids separated by commas, or leave blank)", [], "next");
          $options['filters']['ids'] = $options['filters']['ids'] == "next" ? [] : explode(",", $options['filters']['ids']);

          // 2.2] genders

            // 2.2.1] Получить список всех доступных полов
            $genders = \M5\Models\MD11_genders::pluck('name', 'name')->toArray();

            // 2.2.2] Подмешать в него значение "next"
            $genders["next"] = "next";

            // 2.2.3] Запросить, по каким полам фильтровать
            while(($gender = $this->choice('By genders', $genders, "next")) != "next") {
              if(!in_array($gender, $options['filters']['genders'])) array_push($options['filters']['genders'], $gender);
              $this->info(implode(',', $options['filters']['genders']));
            }

          // 2.3] groups
          $options['filters']['groups'] = $this->anticipate("By groups (specify group ids separated by commas, or leave blank)", [], "next");
          $options['filters']['groups'] = $options['filters']['groups'] == "next" ? [] : explode(",", $options['filters']['groups']);

          // 2.4] tags
          $options['filters']['tags'] = $this->anticipate("By tags (specify tag ids separated by commas, or leave blank)", [], "next");
          $options['filters']['tags'] = $options['filters']['tags'] == "next" ? [] : explode(",", $options['filters']['tags']);

          // 2.5] privileges
          $options['filters']['privileges'] = $this->anticipate("By privileges (specify privilege ids separated by commas, or leave blank)", [], "next");
          $options['filters']['privileges'] = $options['filters']['privileges'] == "next" ? [] : explode(",", $options['filters']['privileges']);

          // 2.6] privtypes

            // 2.6.1] Получить список всех доступных типов прав
            $privtypes = \M5\Models\MD5_privtypes::pluck('name', 'name')->toArray();

            // 2.6.2] Подмешать в него значение "next"
            $privtypes["next"] = "next";

            // 2.6.3] Запросить, по каким типам прав фильтровать
            while(($privtype = $this->choice('By privilege types', $privtypes, "next")) != "next") {
              if(!in_array($privtype, $options['filters']['privtypes'])) array_push($options['filters']['privtypes'], $privtype);
              $this->info(implode(',', $options['filters']['privtypes']));
            }

          // 2.7] m1_packages
          if(r1_rel_exists("M5", "MD3_privileges", "m1_packages")) {
            $options['filters']['m1_packages'] = $this->anticipate("By packages (specify packages id_inners separated by commas, or leave blank)", [], "next");
            $options['filters']['m1_packages'] = $options['filters']['m1_packages'] == "next" ? [] : explode(",", $options['filters']['m1_packages']);
          }

          // 2.8] m1_commands
          if(r1_rel_exists("M5", "MD3_privileges", "m1_commands")) {
            $options['filters']['m1_packages'] = $this->anticipate("By commands (specify commands uids separated by commas, or leave blank)", [], "next");
            $options['filters']['m1_packages'] = $options['filters']['m1_packages'] == "next" ? [] : explode(",", $options['filters']['m1_packages']);
          }

        }

      }],

      "filters_reset"          => ["description" => "Reset all filters", "src" => function() USE (&$options){
        foreach($options['filters'] as &$filter) $filter = [];
      }],

      "filters_add_id"      => ["description" => "Add a new value to the filter by ids", "src" => function() USE (&$options){

        // 1] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['ids']));

        // 2] Запросить ID
        $group = $this->anticipate("Add id (enter user id)", []);

        // 3] Добавить указанное значение
        array_push($options['filters']['ids'], $group);

      }],

      "filters_add_gender"     => ["description" => "Add a new value to the filter by genders", "src" => function() USE (&$options){

        // 1] Получить список всех доступных полов
        $genders = \M5\Models\MD11_genders::pluck('name', 'name')->toArray();

        // 2] Подмешать в него значение "next"
        $genders["next"] = "next";

        // 3] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['genders']));

        // 4] Запросить, по каким полам фильтровать
        while(($gender = $this->choice('Add genders', $genders, "next")) != "next") {
          if(!in_array($gender, $options['filters']['genders'])) array_push($options['filters']['genders'], $gender);
          $this->info(implode(',', $options['filters']['genders']));
        }

      }],

      "filters_add_group"      => ["description" => "Add a new value to the filter by groups", "src" => function() USE (&$options){

        // 1] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['groups']));

        // 2] Запросить ID группы
        $group = $this->anticipate("Add group (enter group id)", []);

        // 3] Добавить указанное значение
        array_push($options['filters']['groups'], $group);

      }],

      "filters_add_tag"        => ["description" => "Add a new value to the filter by tags", "src" => function() USE (&$options){

        // 1] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['tags']));

        // 2] Запросить ID тега
        $tag = $this->anticipate("Add tag (enter tag id)", []);

        // 3] Добавить указанное значение
        array_push($options['filters']['tags'], $tag);

      }],

      "filters_add_privilege"  => ["description" => "Add a new value to the filter by privileges", "src" => function() USE (&$options){

        // 1] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['privilege']));

        // 2] Запросить ID права
        $privilege = $this->anticipate("Add privilege (enter privilege id)", []);

        // 3] Добавить указанное значение
        array_push($options['filters']['privileges'], $privilege);

      }],

      "filters_add_privtypes"   => ["description" => "Add a new value to the filter by privtypes", "src" => function() USE (&$options){

        // 1] Получить список всех доступных типов прав
        $privtypes = \M5\Models\MD5_privtypes::pluck('name', 'name')->toArray();

        // 2] Подмешать в него значение "next"
        $privtypes["next"] = "next";

        // 3] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['privtypes']));

        // 4] Запросить, по каким типам прав фильтровать
        while(($privtype = $this->choice('Add privtypes', $privtypes, "next")) != "next") {
          if(!in_array($privtype, $options['filters']['privtypes'])) array_push($options['filters']['privtypes'], $privtype);
          $this->info(implode(',', $options['filters']['privtypes']));
        }

      }],

      "filters_add_package"    => ["description" => "Add a new value to the filter by m1_packages", "src" => function() USE (&$options){

        // 1] Проверить наличие связи m1_packages
        if(!r1_rel_exists("M5", "MD3_privileges", "m1_packages"))
          $this->error('Связь m1_packages не найдена в модели MD3_privileges пакета M5');

        // 2] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['m1_packages']));

        // 3] Запросить id_inner пакета
        $item = $this->anticipate("Add package (enter id_inner of package)", []);

        // 4] Добавить указанное значение
        array_push($options['filters']['m1_packages'], $item);

      }],

      "filters_add_command"    => ["description" => "Add a new value to the filter by m1_commands", "src" => function() USE (&$options){

        // 1] Проверить наличие связи m1_packages
        if(!r1_rel_exists("M5", "MD3_privileges", "m1_commands"))
          $this->error('Связь m1_commands не найдена в модели MD3_privileges пакета M5');

        // 2] Показать текущий состав фильтра
        $this->info(implode(',', $options['filters']['m1_commands']));

        // 3] Запросить id_inner пакета
        $item = $this->anticipate("Add command (enter uid of command)", []);

        // 4] Добавить указанное значение
        array_push($options['filters']['m1_commands'], $item);

      }]

    ];

    // 3. Запросить стартовый набор фильтров
    $functions_list['filters']['src']();

    // 4. Запустить демона
    while($exit != "yes") {

      // 4.1. Выполнить команду
      $result = runcommand('\M5\Commands\C5_users', $options);

      // 4.2. В случае неудачи, вывести текст ошибки
      if($result['status'] != 0) {
        $this->error('Error: '.$result['data']);
        return;
      }

      // 4.3. Сохранить общее кол-во страниц
      $options['pages_total'] = $result['data']['pages_total'];

      // 4.4. Вывести список пользователей на текущей странице в терминал

        // 1] Получить список logins из конфига пакета
        $logins = config("M5.logins");
        if(!is_array($logins) || is_null($logins)) {
          $this->error('Error: не удалось найти опцию logins в конфиге пакета M5. Возможно, отсутствует сам опубликованный конфиг, либо в нём отсутствует опция.');
          return;
        }

        // 2] Подготовить данные для вывода в таблице
        // - Выводить фамилию, имя и столбцы из $logins
        $result['data']['users']->transform(function($item, $key) USE ($logins) {
          $result = [$item->name, $item->surname];
          foreach($logins as $login) {
            array_push($result, $item[$login]);
          }
          return $result;
        })->toArray();

        // 3] Вывести данные в виде таблицы
        $this->table(call_user_func(function() USE ($logins){
          $result = ['name', 'surname'];
          foreach($logins as $login) {
            array_push($result, $login);
          }
          return $result;
        }), $result['data']['users']);

      // 4.5. Вывести пагинационную информацию
      $this->info('--- Page ' . $options['page'] . ' / ' . $options['pages_total'] . ' --- ');

      // 4.6. Вывети информацию о действующих фильтрах
      $filters = $options['filters'];
      $filters = array_filter($filters, function($item){ return !empty($item); });
      if(!empty($filters)) $this->info('--- Applied filters --- '.json_encode($filters, JSON_UNESCAPED_UNICODE));

      // 4.7. Спросить, что делать дальше
      while(!in_array($command = $this->anticipate("What next? Enter a command. Examples: p (prev) / n (next) / goto / info / exit", []), array_keys($functions_list))) {
        $this->error('Wrong command');
      }

      // 4.8. Выполнить команду, указанную в $command
      $functions_list[$command]['src']();

    }

  }

}