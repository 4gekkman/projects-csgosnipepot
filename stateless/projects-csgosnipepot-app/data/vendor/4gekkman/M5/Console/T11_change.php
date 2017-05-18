<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Change a user / group / privilege / tag
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
class T11_change extends Command
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

    protected $signature = 'm5:change';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Change a user / group / privilege / tag';

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
     *  1. Выяснить тип ресурса, который пользователь хочет изменить
     *  2. Запросить у пользователя соответствующие изменяемому типу ресурса параметры
     *  3. Выполнить команду для изменения желаемого ресурса, передав ей параметры
     *
     */

    //-------------------------------------------------------------//
    // 1. Выяснить тип ресурса, который пользователь хочет создать //
    //-------------------------------------------------------------//

      // 1.1. Подготовить таблицу кодов типов ресурсов
      $restypes = [
        "u"       => "Change user",
        "g"       => "Change group",
        "t"       => "Change tag",
        "cp"      => "Change custom privilege",
      ];

      // 1.2. Узнать у пользователя, ресурс какого типа он хочет создать
      $restype = $this->choice('Choose type of resource that you want to change', $restypes, 'u');

    //--------------------------------------------------------------------------------//
    // 2. Запросить у пользователя соответствующие изменяемому типу ресурса параметры //
    //--------------------------------------------------------------------------------//
    $params = call_user_func(function() USE ($restype) { try {

      // 2.1. Если $restype == "u"
      if($restype == 'u') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the user that you want to change", []);

        // 3] Запросить необязательные параметры
        $params['name']             = $this->anticipate("Name", [], "0");
        $params['surname']          = $this->anticipate("Surname", [], "0");
        $params['patronymic']       = $this->anticipate("Patronymic", [], "0");

        $params['email']            = $this->anticipate("Email", [], "0");
        $params['phone']            = $this->anticipate("Phone", [], "0");
        $params['password']         = $this->anticipate("Password", [], "0");

        $params['secret_question']  = $this->anticipate("Secret_question", [], "0");
        $params['secret_phrase']    = $this->anticipate("Secret_phrase", [], "0");

        $params['gender']           = $this->choice('Gender', ["m" => "male", "f" => "female", "u" => "undefined"], 'u');
        $params['birthday']         = $this->anticipate("Birthday", [], "0");

        $params['skype']            = $this->anticipate("Skype", [], "0");
        $params['other_contacts']   = $this->anticipate("Other_contacts", [], "0");

        $params['isanonymous']      = $this->choice("Isanonymous", ["yes" => "Yes, it's anonymous user", "no" => "No, it's not anonymous user"], 'no');

        // n] Вернуть $params
        return $params;

      }

      // 2.2. Если $restype == "g"
      if($restype == 'g') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the group that you want to change", []);

        // 3] Запросить необязательные параметры
        $params['name'] = $this->anticipate("[NOT REQUIRED] Name", [], "0");
        $params['description'] = $this->anticipate("[NOT REQUIRED] Description", [], "0");
        $params['isadmin'] = $this->choice("[NOT REQUIRED] Is admin", ["yes" => "Yes, it's group for admins", "no" => "No, it's group not for admins", "undef" => "undef"], 'undef');

        // n] Вернуть $params
        return $params;

      }

      // 2.3. Если $restype == "t"
      if($restype == 't') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the tag that you want to change", []);

        // 3] Запросить необязательные параметры
        $params['name'] = $this->anticipate("[NOT REQUIRED] Name", [], "0");

        // n] Вернуть $params
        return $params;

      }

      // 2.4. Если $restype == "cp"
      if($restype == 'cp') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id'] = $this->anticipate("[REQUIRED] ID of the privilege that you want to change", []);

        // 3] Запросить необязательные параметры
        $params['name'] = $this->anticipate("[NOT REQUIRED] Name", [], "0");
        $params['description'] = $this->anticipate("[NOT REQUIRED] Description", [], "0");

        // n] Вернуть $params
        return $params;

      }

    } catch(\Exception $e) {
        $this->error('Error: '.$e->getMessage());
        return 'error';
    }}); if(empty($params) || $params == 'error') return;

    //----------------------------------------------------------------------------//
    // 3. Выполнить команду для изменения желаемого ресурса, передав ей параметры //
    //----------------------------------------------------------------------------//

      // 3.1. Подготовить список команд

        // 3.1.1. Подготовить таблицу кодов типов ресурсов
        $commandsarr = [
          "u"       => "\\M5\\Commands\\C17_changeuser",
          "g"       => "\\M5\\Commands\\C18_changegroup",
          "t"       => "\\M5\\Commands\\C20_changetag",
          "cp"      => "\\M5\\Commands\\C19_changeprivilege",
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
            case "u"      : $this->info("User with id = '".$result['data']['id']."' has been changed successfully."); break;
            case "g"      : $this->info("Group '".$result['data']['name']."' has been changed successfully."); break;
            case "t"      : $this->info("Tag '".$result['data']['name']."' has been changed successfully."); break;
            case "cp"     : $this->info("Custom privilege '".$result['data']['name']."' has been changed successfully."); break;

            default    : $this->info("Success");
          }


  }

}