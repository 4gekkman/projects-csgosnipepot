<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Delete a user / group / privilege / tag
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
class T10_del extends Command
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

    protected $signature = 'm5:del';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Delete a user / group / privilege / tag';

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
     *  1. Выяснить тип ресурса, который пользователь хочет удалить
     *  2. Запросить у пользователя соответствующие удаляемому типу ресурса параметры
     *  3. Выполнить команду для удаления желаемого ресурса, передав ей параметры
     *
     *
     */

    //-------------------------------------------------------------//
    // 1. Выяснить тип ресурса, который пользователь хочет удалить //
    //-------------------------------------------------------------//

      // 1.1. Подготовить таблицу кодов типов ресурсов
      $restypes = [
        "u"       => "New user",
        "g"       => "New group",
        "t"       => "New tag",
        "cp"      => "New custom privilege",
      ];

      // 1.2. Узнать у пользователя, ресурс какого типа он хочет создать
      $restype = $this->choice('Choose type of resource that you want to delete', $restypes);


    //-------------------------------------------------------------------------------//
    // 2. Запросить у пользователя соответствующие удаляемому типу ресурса параметры //
    //-------------------------------------------------------------------------------//
    $params = call_user_func(function() USE ($restype) { try {

      // 2.1. Если $restype == "u"
      if($restype == 'u') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id_or_email'] = $this->anticipate("[REQUIRED] ID or email of the user that you want to delete", []);

        // n] Вернуть $params
        return $params;

      }

      // 2.2. Если $restype == "g"
      if($restype == 'g') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id_or_name'] = $this->anticipate("[REQUIRED] ID or name of the group that you want to delete", []);

        // n] Вернуть $params
        return $params;

      }

      // 2.3. Если $restype == "t"
      if($restype == 't') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id_or_name'] = $this->anticipate("[REQUIRED] ID or name of the tag that you want to delete", []);

        // n] Вернуть $params
        return $params;

      }

      // 2.4. Если $restype == "cp"
      if($restype == 'cp') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Запросить обязательные параметры
        $params['id_or_name'] = $this->anticipate("[REQUIRED] ID or name of the custom privilege that you want to delete", []);

        // n] Вернуть $params
        return $params;

      }

    } catch(\Exception $e) {
        $this->error('Error: '.$e->getMessage());
        return 'error';
    }}); if(empty($params) || $params == 'error') return;

    //---------------------------------------------------------------------------//
    // 3. Выполнить команду для удаления желаемого ресурса, передав ей параметры //
    //---------------------------------------------------------------------------//

      // 3.1. Подготовить список команд

        // 3.1.1. Подготовить таблицу кодов типов ресурсов
        $commandsarr = [
          "u"       => "\\M5\\Commands\\C13_deluser",
          "g"       => "\\M5\\Commands\\C14_delgroup",
          "t"       => "\\M5\\Commands\\C16_deltag",
          "cp"      => "\\M5\\Commands\\C15_delprivilege",
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
            case "u"      : $this->info("User ".$result['data']['name']." (".$result['data']['email'].") was successfully deleted."); break;
            case "g"      : $this->info("Group '".$result['data']['name']."' was successfully deleted."); break;
            case "t"      : $this->info("Tag '".$result['data']['name']."' was successfully deleted."); break;
            case "cp"     : $this->info("Custom privilege '".$result['data']['name']."' was successfully deleted."); break;

            default    : $this->info("Success");
          }


  }

}