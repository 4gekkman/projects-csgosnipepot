<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Start constructor for creating a new resource
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
// - Пример для админ.документов:  M1\Console

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
class T7_new extends Command
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

    protected $signature = 'm1:new';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Start constructor for creating a new resource';

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
  //        $y = $this->choice('Введите строку', ['1'=>'Вариант №1', '2'=>'Вариант 2', '1');
  //        $z = $this->confirm('Да?', true);
  //
  //  - Вывод информации в окно терминала:
  //
  //    - $this->info()        | вывести в окно терминала сообщение цвета info
  //    - $this->comment()     | вывести в окно терминала сообщение цвета comment
  //    - $this->question()    | вывести в окно терминала сообщение цвета question
  //    - $this->error()       | вывести в окно терминала сообщение цвета error
  //    - $this->table()       | вывести в окно терминала таблицу данных
  public function handle()
  {

    /**
     * Оглавление
     *
     *  0. Обновить приложение перед созданием нового ресурса
     *  1. Выяснить, какой ресурс хочет создать пользователь
     *  2. Запросить у пользователя соответствующие создаваемому типу ресурса параметры
     *  3. Выполнить команду для создания желаемого ресурса, передав её параметры
     *
     *
     */

    //-------------------------------------------------------//
    // 0. Обновить приложение перед созданием нового ресурса //
    //-------------------------------------------------------//
    Artisan::call('m1:parseapp');
    $this->callSilent('m1:afterupdate');

    //-------------------------------------------------------------//
    // 1. Выяснить тип ресурса, который пользователь хочет создать //
    //-------------------------------------------------------------//

      // 1.1. Подготовить таблицу кодов типов ресурсов
      $restypes = [
        "m"       => "New M-package",
        "mc"      => "New command for existing M-package",
        "mt"      => "New console command for existing M-package",
        "mh"      => "New event handler for existing M-package",
        "mct"     => "New pair (command + console command) for existing M-package",
        "mm"      => "New model for existing M-package",

        "d"       => "New D-package",
        "w"       => "New W-package",
        "l"       => "New L-package",
        "r"       => "New R-package",
        "p"       => "New P-package",
        "k"       => "New K-package",

        "mdlw_u"  => "New config update for M,D,L,W-package",
      ];

      // 1.2. Узнать у пользователя, ресурс какого типа он хочет создать
      $restype = $this->choice('Choose type of resource that you want to create', $restypes, 'mc');

    //---------------------------------------------------------------------------------//
    // 2. Запросить у пользователя соответствующие создаваемому типу ресурса параметры //
    //---------------------------------------------------------------------------------//
    $params = call_user_func(function() USE ($restype) { try {

      // 2.1. Если $restype == "m"
      if($restype == 'm') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, какое имя (RU,EN) задать новому M-пакету
        $params['enname'] = $this->ask("[NOT REQUIRED] Type name for the new M-package in english", "Name of M-package");
        $params['runame'] = $params['enname'];

        // 3] Спросить у пользователя, какое описание (RU,EN) задать новому M-пакету
        $params['endescription'] = $this->ask("[NOT REQUIRED] Type description for the new M-package in english", 0);
        $params['rudescription'] = $params['endescription'];

        // 4] Спросить у пользователя, какой id задать новому M-пакету
        $params['packid'] = $this->ask("[NOT REQUIRED] Type id for the new M-package", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.2. Если $restype == "mc"
      if($restype == 'mc') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет создать команду

          // 2.1] Получить инфу обо всех M-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','M');
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which command could be created.');

          // 2.3] Спросить
          $params['mpackid'] = $this->choice('Choose M-package, for which needs to create new command', $packages->toArray());

        // 3] Спросить у пользователя, какое имя задать команде
        $params['name'] = $this->ask("[NOT REQUIRED] Type name for the new command in english", "command");

        // 4] Спросить у пользователя, какое описание задать команде
        $params['description'] = $this->ask("[NOT REQUIRED] Type description for the new command in english", 0);

        // 5] Спросить у пользователя, какой id задать команде
        $params['comid'] = $this->ask("[NOT REQUIRED] Type id for the new command", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.3. Если $restype == "mt"
      if($restype == 'mt') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет создать к.команду

          // 2.1] Получить инфу обо всех M-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','M');
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which console command could be created.');

          // 2.3] Спросить
          $params['mpackid'] = $this->choice('Choose M-package, for which needs to create new console command', $packages->toArray());

        // 3] Спросить у пользователя, какое имя задать к.команде
        $params['name'] = $this->ask("[NOT REQUIRED] Type name for the new console command in english", "consolecommand");

        // 4] Спросить у пользователя, какое описание задать к.команде
        $params['description'] = $this->ask("[NOT REQUIRED] Type description for the new console command in english", 0);

        // 5] Спросить у пользователя, какой id задать к.команде
        $params['comid'] = $this->ask("[NOT REQUIRED] Type id for the new console command", 0);

        // 6] Опрос по поводу добавления в планировщик
        $params['add2scheduler'] = $this->ask("[NOT REQUIRED] Do you want to add c.command to scheduler? If yes, type code (if needed, use only double quotes). Otherwise, leave blank.", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.4. Если $restype == "mh"
      if($restype == 'mh') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет создать обработчик

          // 2.1] Получить инфу обо всех M-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','M');
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which event handler could be created.');

          // 2.3] Спросить
          $params['mpackid'] = $this->choice('Choose M-package, for which needs to create new event handler', $packages->toArray());

        // 3] Спросить у пользователя, какие ключи будет поддерживать обработчик
        $params['keys'] = [];
        while($keyanswer = $this->ask("[NOT REQUIRED] Type key on which the handler will react, or leave blank to proceed", 0)) {
          array_push($params['keys'], $keyanswer);
        }

        // 4] Спросить у пользователя, какое имя задать обработчику
        $params['name'] = $this->ask("[NOT REQUIRED] Type name for the new console command in english", "handler");

        // 5] Спросить у пользователя, какое описание задать обработчику
        $params['description'] = $this->ask("[NOT REQUIRED] Type description for the new console command in english", 0);

        // 6] Спросить у пользователя, какой id задать обработчику
        $params['comid'] = $this->ask("[NOT REQUIRED] Type id for the new event handler", 0);

        // 7] Спросить у пользователя, какое событие должен обрабатывать обработчик
        $params['event'] = $this->ask("[NOT REQUIRED] What event handler should handle", "\\R2\\Event");

        // n] Вернуть $params
        return $params;

      }

      // 2.5. Если $restype == "mct"
      if($restype == 'mct') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет пару

          // 2.1] Получить инфу обо всех M-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','M');
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which command/c.command pair could be created.');

          // 2.3] Спросить
          $params['mpackid'] = $this->choice('Choose M-package, for which needs to create command/c.command pair', $packages->toArray());

        // 3] Спросить у пользователя, какое имя задать команде и к.команде
        $params['name'] = $this->ask("[NOT REQUIRED] Type name for the new command/c.command in english", "commandpair");

        // 4] Спросить у пользователя, какое описание задать к.команде
        $params['description'] = $this->ask("[NOT REQUIRED] Type description for the new command/c.command in english", 0);

        // 5] Спросить у пользователя, какой id задать к.команде
        $params['comid'] = $this->ask("[NOT REQUIRED] Type id for the new command/c.command", 0);

        // 6] Опрос по поводу добавления к.команды в планировщик
        $params['add2scheduler'] = $this->ask("[NOT REQUIRED] Do you want to add c.command to scheduler? If yes, type code (if needed, use only double quotes). Otherwise, leave blank.", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.6. Если $restype == "mm"
      if($restype == 'mm') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет создать команду

          // 2.1] Получить инфу обо всех M-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','M');
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which model could be created.');

          // 2.3] Спросить
          $params['mpackid'] = $this->choice('Choose M-package, for which needs to create new model', $packages->toArray());

        // 3] Спросить у пользователя, какое имя задать модели
        $params['name'] = $this->ask("[REQUIRED] Type name for the new model in english");

        // 4] Спросить у пользователя, включить ли автообслуживание created_at / updated_at
        $params['timestamps'] = $this->choice('[NOT REQUIRED] On/Off auto maintenance of created_at/updated_at columns', ['1'=>'true', '2'=>'false'], '2');

        // 5] Спросить у пользователя, включить ли мягкое удаление
        $params['softdeletes'] = $this->choice('On/Off soft deletes', ['1'=>'true', '2'=>'false'], '2');

        // 6] Спросить у пользователя, какой id задать модели
        $params['modelid'] = $this->ask("[NOT REQUIRED] Type id for the new model", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.7. Если $restype == "d"
      if($restype == 'd') {
        $params = [

        ];
      }

      // 2.8. Если $restype == "w"
      if($restype == 'w') {
        $params = [

        ];
      }

      // 2.9. Если $restype == "l"
      if($restype == 'l') {
        $params = [

        ];
      }

      // 2.10. Если $restype == "r"
      if($restype == 'r') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, какое описание (RU,EN) задать новому M-пакету
        $params['endescription'] = $this->ask("[NOT REQUIRED] Type description for the new R-package in english", 0);

        // 3] Спросить у пользователя, какой id задать новому M-пакету
        $params['packid'] = $this->ask("[NOT REQUIRED] Type id for the new R-package", 0);

        // n] Вернуть $params
        return $params;

      }

      // 2.11. Если $restype == "p"
      if($restype == 'p') {
        $params = [

        ];
      }

      // 2.12. Если $restype == "k"
      if($restype == 'k') {
        $params = [

        ];
      }

      // 2.12. Если $restype == "mdlw_u"
      if($restype == 'mdlw_u') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, для какого пакета он хочет создать обновление конфига

          // 2.1] Получить инфу обо всех M,D,L,W-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where(function($query){
              $query->where('name','=','M')->
                      orWhere('name','=','D')->
                      orWhere('name','=','L')->
                      orWhere('name','=','W');
            });
          })->pluck('aboutpack', 'id_inner');
          $packages = $packages->map(function($item, $key){
            return json_decode($item,true)['EN']['description'];
          });

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no M-packages in the app, for which console command could be created.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('Choose package, for which needs to create new config update', $packages->toArray());

        // n] Вернуть $params
        return $params;

      }



    } catch(\Exception $e) {
        $this->error('Error: '.$e->getMessage());
        return 'error';
    }}); if(empty($params) || $params == 'error') return;

    //---------------------------------------------------------------------------//
    // 3. Выполнить команду для создания желаемого ресурса, передав ей параметры //
    //---------------------------------------------------------------------------//

      // 3.1. Подготовить список команд

        // 3.1.1. Подготовить таблицу кодов типов ресурсов
        $commandsarr = [
          "m"       => "\\M1\\Commands\\C7_new_m",
          "mc"      => "\\M1\\Commands\\C8_new_m_c",
          "mt"      => "\\M1\\Commands\\C9_new_m_t",
          "mh"      => "\\M1\\Commands\\C10_new_m_h",
          "mct"     => "\\M1\\Commands\\C11_new_m_ct",
          "mm"      => "\\M1\\Commands\\C12_new_m_m",

          "d"       => "\\M1\\Commands\\C14_new_d",
          "w"       => "\\M1\\Commands\\C15_new_w",
          "l"       => "\\M1\\Commands\\C16_new_l",
          "r"       => "\\M1\\Commands\\C17_new_r",
          "p"       => "\\M1\\Commands\\C18_new_p",
          "k"       => "\\M1\\Commands\\C19_new_k",

          "mdlw_u"  => "\\M1\\Commands\\C34_new_mdlw_u",
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
            case "m"      : $this->info("New M-package '".$result['data']['packfullname']."' was successfully created."); break;
            case "mc"     : $this->info("New command '".$result['data']['comfullname']."' was successfully created for M-package '".$result['data']['package']."'."); break;
            case "mt"     : $this->info("New console command '".$result['data']['ccomfullname']."' was successfully created for M-package '".$result['data']['package']."'."); break;
            case "mh"     : $this->info("New event handler '".$result['data']['handlerfullname']."' was successfully created for M-package '".$result['data']['package']."'."); break;
            case "mct"    : $this->info("New command '".$result['data']['comfullname']."' and console command '" .$result['data']['ccomfullname']. "' were successfully created for M-package '".$result['data']['package']."'."); break;
            case "mm"     : $this->info("New model '".$result['data']['modelfullname']."' was successfully created for M-package '".$result['data']['package']."'."); break;

            case "d"      : $this->info(""); break;
            case "w"      : $this->info(""); break;
            case "l"      : $this->info(""); break;
            case "r"      : $this->info("New R-package '".$result['data']['packfullname']."' was successfully created."); break;
            case "p"      : $this->info(""); break;
            case "k"      : $this->info(""); break;

            case "mdlw_u" : $this->info("New config update '".$result['data']['updatenum']."' for package '".$result['data']['packid']."' was successfully created."); break;

            default    : $this->info("Success");
          }

    //------------------------------------------------------//
    // 4. Обновить приложение после создания нового ресурса //
    //------------------------------------------------------//
    Artisan::call('m1:parseapp');
    $this->callSilent('m1:afterupdate');



  }

}