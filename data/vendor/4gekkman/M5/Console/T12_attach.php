<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Attach one entity to another
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
class T12_attach extends Command
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

    protected $signature = 'm5:attach';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Attach one entity to another';

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
     *  1. Выяснить тип ресурса, который пользователь хочет прикрепить
     *  2. Запросить у пользователя соответствующие прикрепляемому типу ресурса параметры
     *  3. Выполнить команду для прикрепления желаемого ресурса, передав ей параметры
     *
     *
     */

    //----------------------------------------------------------------//
    // 1. Выяснить тип ресурса, который пользователь хочет прикрепить //
    //----------------------------------------------------------------//

      // 1.1. Подготовить таблицу кодов типов ресурсов
      $restypes = [
        "u"       => "Attach user",
        "t"       => "Attach tag",
        "cp"      => "Attach custom privilege",
      ];

      // 1.2. Узнать у пользователя, ресурс какого типа он хочет прикрепить
      $restype = $this->choice('Choose type of resource that you want to attach', $restypes);

    //-----------------------------------------------------------------------------------//
    // 2. Запросить у пользователя соответствующие прикрепляемому типу ресурса параметры //
    //-----------------------------------------------------------------------------------//
    $params = call_user_func(function() USE ($restype) { try {

      // 2.1. Если $restype == "u"
      if($restype == 'u') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id_user'] = $this->anticipate("[REQUIRED] ID of the user that you want to attach", []);
        $params['id_group'] = $this->anticipate("[REQUIRED] ID of the group to which you want to attach the user", []);

        // n] Вернуть $params
        return $params;

      }

      // 2.2. Если $restype == "t"
      if($restype == 't') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Узнать, к какому ресурсу требуется прикрепить тег
        $which = $this->choice("[NOT REQUIRED] To which resource do you want to attach the tag", ["user" => "Attach to user", "group" => "Attach to group", "privilege" => "Attach to privilege"]);

        // 3] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the tag that you want to attach", []);
        if($which == "user") $params['id_user'] = $this->anticipate("[REQUIRED] ID of the user to which you want to attach the tag", []);
        if($which == "group") $params['id_group'] = $this->anticipate("[REQUIRED] ID of the group to which you want to attach the tag", []);
        if($which == "privilege") $params['id_privilege'] = $this->anticipate("[REQUIRED] ID of the privilege to which you want to attach the tag", []);

        // n] Вернуть $params
        return $params;

      }

      // 2.3. Если $restype == "cp"
      if($restype == 'cp') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Узнать, к какому ресурсу требуется прикрепить право
        $which = $this->choice("[NOT REQUIRED] To which resource do you want to attach the privilege", ["user" => "Attach to user", "group" => "Attach to group"]);

        // 3] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the custom privilege that you want to attach", []);
        if($which == "user") $params['id_user'] = $this->anticipate("[REQUIRED] ID of the user to which you want to attach the privilege", []);
        if($which == "group") $params['id_group'] = $this->anticipate("[REQUIRED] ID of the group to which you want to attach the privilege", []);

        // n] Вернуть $params
        return $params;

      }

    } catch(\Exception $e) {
        $this->error('Error: '.$e->getMessage());
        return 'error';
    }}); if(empty($params) || $params == 'error') return;

    //-------------------------------------------------------------------------------//
    // 3. Выполнить команду для прикрепления желаемого ресурса, передав ей параметры //
    //-------------------------------------------------------------------------------//

      // 3.1. Подготовить список команд

        // 3.1.1. Подготовить таблицу кодов типов ресурсов
        $commandsarr = [
          "u"       => "\\M5\\Commands\\C21_attachuser",
          "t"       => "\\M5\\Commands\\C23_attachtag",
          "cp"      => "\\M5\\Commands\\C22_attachprivilege",
        ];

        // 3.1.2. Выполнить команду для создания ресурса типа $restype

          // 1] Выполнить команду
          $result = runcommand($commandsarr[$restype], $params);

          // 2] В случае неудачи, вывести текст ошибки:
          if($result['status'] != 0) {
            $this->error('Error: '.$result['data']);
            return;
          }

          // 3] В случае успеха
          switch($restype) {
            case "u"      : $this->info("User '".$result['data']['user_id']."' has been successfully attached to group '".$result['data']['group_id']."'."); break;
            case "t"      : $this->info("Tag '".$result['data']['tag']."' has been attached to the ".($result['data']['id_user'] ? 'user' : ($result['data']['id_group'] ? 'group' : 'privilege'))." with id = '".($result['data']['id_user'] ? $result['data']['id_user'] : ($result['data']['id_group'] ? $result['data']['id_group'] : $result['data']['id_privilege']))."'."); break;
            case "cp"     : $this->info("Custom privilege '".$result['data']['privilege']."' has been attached to the ".($result['data']['id_user'] ? 'user' : 'group')." with id = '".($result['data']['id_user'] ?: $result['data']['id_group'])."'."); break;

            default    : $this->info("Success");
          }


  }

}