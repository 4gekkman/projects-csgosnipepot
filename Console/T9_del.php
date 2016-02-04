<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Start constructor for deleting existing resource
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
class T9_del extends Command
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

    protected $signature = 'm1:del';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Start constructor for deleting existing resource';

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
  public function handle()
  {

    /**
     * Оглавление
     *
     *  0. Обновить приложение перед удалением существующего ресурса
     *  1. Выяснить тип ресурса, который пользователь хочет удалить
     *  2. Запросить у пользователя соотв.удаляемому ресурсы параметры
     *
     *
     *
     */

    //--------------------------------------------------------------//
    // 0. Обновить приложение перед удалением существующего ресурса //
    //--------------------------------------------------------------//
    $this->callSilent('m1:afterupdate');


    //-------------------------------------------------------------//
    // 1. Выяснить тип ресурса, который пользователь хочет удалить //
    //-------------------------------------------------------------//

      // 1.1. Подготовить таблицу кодов типов ресурсов
      $restypes = [
        "m"     => "Del M-package",
        "mc"    => "Del command from existing M-package",
        "mt"    => "Del console command from existing M-package",
        "mh"    => "Del event handler from existing M-package",
        "mm"    => "Del model from existing M-package",

        "d"     => "Del D-package",
        "w"     => "Del W-package",
        "l"     => "Del L-package",
        "r"     => "Del R-package",
        "p"     => "Del P-package",
        "k"     => "Del K-package",
      ];

      // 1.2. Узнать у пользователя, ресурс какого типа он хочет создать
      $restype = $this->choice('Choose type of resource that you want to delete', $restypes);

    //----------------------------------------------------------------//
    // 2. Запросить у пользователя соотв.удаляемому ресурсы параметры //
    //----------------------------------------------------------------//
    $params = call_user_func(function() USE ($restype) { try {

      // 2.1. Если $restype == "m"
      if($restype == 'm') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, какой M-пакет он хочет удалить

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
            throw new \Exception('There is no M-packages in the app, which could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('Which M-package do you want to delete?', $packages->toArray());

        // 3] Спросить, удалять ли конфиг модуля, или оставить
        $params['delconf'] = $this->choice('[NOT REQUIRED] Should we delete the config of the package?', ["1"=>"no", "2"=>"yes"], "1");

        // 4] Спросить, удалять ли БД модуля, или оставить
        $params['deldb'] = $this->choice('[NOT REQUIRED] Should we delete the package database?', ["1"=>"no", "2"=>"yes"], "1");

        // n] Вернуть $params
        return $params;

      }

      // 2.2. Если $restype == "mc"
      if($restype == 'mc') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, команду из какого M-пакета он хочет удалить

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
            throw new \Exception('There is no M-packages in the app, from which command could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('From which M-package do you want to delete the command?', $packages->toArray());

        // 3] Спросить у пользователя, какую команду из M-пакета $params['packid'] он хочет удалить

          // 3.1] Получить инфу обо всех командах в M-пакете $params['packid']
          // - В формате: id команды => описание команды
          $commands = \M1\Models\MD5_commands::whereHas('packages', function($query) USE ($params) {
            $query->where('id_inner','=',$params['packid']);
          })->pluck('description', 'id_inner');

          // 3.2] Если коллекция $commands пуста, сообщить и завершить
          if($commands->count() == 0)
            throw new \Exception('There is no commands in M-package '.$params['packid'].', that could be deleted.');

          // 3.3] Спросить
          $params['command2del'] = $this->choice('v command do you want to delete?', $commands->toArray());

        // n] Вернуть $params
        return $params;

      }

      // 2.3. Если $restype == "mt"
      if($restype == 'mt') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, к.команду из какого M-пакета он хочет удалить

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
            throw new \Exception('There is no M-packages in the app, from which console command could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('From which M-package do you want to delete the console command?', $packages->toArray());

        // 3] Спросить у пользователя, какую к.команду из M-пакета $params['packid'] он хочет удалить

          // 3.1] Получить инфу обо всех к.командах в M-пакете $params['packid']
          // - В формате: id к.команды => описание к.команды
          $commands = \M1\Models\MD6_console::whereHas('packages', function($query) USE ($params) {
            $query->where('id_inner','=',$params['packid']);
          })->pluck('description', 'id_inner');

          // 3.2] Если коллекция $commands пуста, сообщить и завершить
          if($commands->count() == 0)
            throw new \Exception('There is no console commands in M-package '.$params['packid'].', that could be deleted.');

          // 3.3] Спросить
          $params['command2del'] = $this->choice('Which console command do you want to delete?', $commands->toArray());

        // n] Вернуть $params
        return $params;

      }

      // 2.4. Если $restype == "mh"
      if($restype == 'mh') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, обработчик из какого M-пакета он хочет удалить

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
            throw new \Exception('There is no M-packages in the app, from which event handler could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('From which M-package do you want to delete the event handler?', $packages->toArray());

        // 3] Спросить у пользователя, какой обработчик из M-пакета $params['packid'] он хочет удалить

          // 3.1] Получить инфу обо всех обработчиках в M-пакете $params['packid']
          // - В формате: id обработчика => описание обработчика
          $handlers = \M1\Models\MD7_handlers::whereHas('packages', function($query) USE ($params) {
            $query->where('id_inner','=',$params['packid']);
          })->pluck('description', 'id_inner');

          // 3.2] Если коллекция $handlers пуста, сообщить и завершить
          if($handlers->count() == 0)
            throw new \Exception('There is no event handlers in M-package '.$params['packid'].', that could be deleted.');

          // 3.3] Спросить
          $params['handler2del'] = $this->choice('Which event handler do you want to delete?', $handlers->toArray());

        // n] Вернуть $params
        return $params;

      }

      // 2.5. Если $restype == "mm"
      if($restype == 'mm') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, модель из какого M-пакета он хочет удалить

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
            throw new \Exception('There is no M-packages in the app, from which model could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('From which M-package do you want to delete the model?', $packages->toArray());

        // 3] Спросить у пользователя, какую модель из M-пакета $params['packid'] он хочет удалить

          // 3.1] Получить инфу обо всех моделях в M-пакете $params['packid']
          // - В формате: id модели => полное имя модели
          $handlers = \M1\Models\MD3_models::whereHas('packages', function($query) USE ($params) {
            $query->where('id_inner','=',$params['packid']);
          })->pluck('name', 'id_inner');

          // 3.2] Если коллекция $handlers пуста, сообщить и завершить
          if($handlers->count() == 0)
            throw new \Exception('There is no models in M-package '.$params['packid'].', that could be deleted.');

          // 3.3] Спросить
          $params['model2del'] = $this->choice('Which model do you want to delete?', $handlers->toArray());

        // n] Вернуть $params
        return $params;

      }

      // 2.6. Если $restype == "d"
      if($restype == 'd') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];



        // n] Вернуть $params
        return $params;

      }

      // 2.7. Если $restype == "w"
      if($restype == 'w') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];



      }

      // 2.8. Если $restype == "l"
      if($restype == 'l') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];



        // n] Вернуть $params
        return $params;

      }

      // 2.9. Если $restype == "r"
      if($restype == 'r') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];

        // 2] Спросить у пользователя, какой R-пакет он хочет удалить

          // 2.1] Получить инфу обо всех R-пакетах
          // - В формате: id пакета => описание пакета
          $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->where('name','=','R');
          })->pluck('aboutpack', 'id_inner');

          // 2.2] Если коллекция $packages пуста, сообщить и завершить
          if($packages->count() == 0)
            throw new \Exception('There is no R-packages in the app, which could be deleted.');

          // 2.3] Спросить
          $params['packid'] = $this->choice('Which R-package do you want to delete?', $packages->toArray());

        // n] Вернуть $params
        return $params;

      }

      // 2.10. Если $restype == "p"
      if($restype == 'p') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];



        // n] Вернуть $params
        return $params;

      }

      // 2.11. Если $restype == "k"
      if($restype == 'k') {

        // 1] Подготовить массив для значений запрашиваемых у пользователя параметров
        $params = [];



        // n] Вернуть $params
        return $params;

      }



    } catch(\Exception $e) {
        $this->error('Error: '.$e->getMessage());
        return 'error';
    }}); if(empty($params) || $params == 'error') return;

    //----------------------------------------------------------------------------//
    // 3. Выполнить команду для удаления выбранного ресурса, передав ей параметры //
    //----------------------------------------------------------------------------//

      // 3.1. Подготовить список команд

        // 3.1.1. Подготовить таблицу кодов типов ресурсов
        $commandsarr = [
          "m"     => "\\M1\\Commands\\C20_del_m",
          "mc"    => "\\M1\\Commands\\C21_del_m_c",
          "mt"    => "\\M1\\Commands\\C22_del_m_t",
          "mh"    => "\\M1\\Commands\\C23_del_m_h",
          "mm"    => "\\M1\\Commands\\C25_del_m_m",

          "d"     => "\\M1\\Commands\\C26_del_d",
          "w"     => "\\M1\\Commands\\C27_del_w",
          "l"     => "\\M1\\Commands\\C28_del_l",
          "r"     => "\\M1\\Commands\\C29_del_r",
          "p"     => "\\M1\\Commands\\C30_del_p",
          "k"     => "\\M1\\Commands\\C31_del_k",
        ];

        // 3.1.2. Выполнить команду для удаления ресурса типа $restype

          // 1] Выполнить команду
          $result = runcommand($commandsarr[$restype], $params);

          // 2] В случае неудачи, вывести текст ошибки:
          if($result['status'] != 0) {
            $this->error('Error: '.$result['data']);
            return;
          }

          // 3] В случае успеха
          switch($restype) {
            case "m"   : $this->info("M-package '".$result['data']['packfullname']."' was successfully deleted. Was the config deleted: ".$result['data']['delconf'].". Was the database deleted: ".$result['data']['deldb']); break;
            case "mc"  : $this->info("Command '".$result['data']['comfullname']."' was successfully deleted from M-package '".$result['data']['package']."'."); break;
            case "mt"  : $this->info("Console command '".$result['data']['ccomfullname']."' was successfully deleted from M-package '".$result['data']['package']."'."); break;
            case "mh"  : $this->info("Event handler '".$result['data']['handlerfullname']."' was successfully deleted from M-package '".$result['data']['package']."'."); break;
            case "mm"  : $this->info("Model '".$result['data']['modelfullname']."' was successfully deleted from M-package '".$result['data']['package']."'."); break;

            case "d"   : $this->info(""); break;
            case "w"   : $this->info(""); break;
            case "l"   : $this->info(""); break;
            case "r"   : $this->info("R-package '".$result['data']['packfullname']."' was successfully deleted."); break;
            case "p"   : $this->info(""); break;
            case "k"   : $this->info(""); break;

            default    : $this->info("Success");
          }

        // 3.1.3. Выполнить команду m1:afternew

          // 1] Один раз прямо сейчас
          runcommand('\M1\Commands\C13_afternew');

          // 2] И ещё раз через 10 секунд
          $this->call('m1:afternew');



  }

}