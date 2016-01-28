<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New manual route
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

  namespace M4\Console;

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
class T4_new extends Command
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

    protected $signature = 'm4:new';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'New manual route';

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
     *  1. Написать функцию для получения списка всех роутов, и вывода его в терминал
     *  2. Получить все параметры для создания нового роута
     *  3. Выполнить команду
     *
     */

    // 1. Написать функцию для получения списка всех роутов, и вывода его в терминал
    $show = function(){

      // 1] Получить список всех роутов
      $result = runcommand('\M4\Commands\C2_list');
      if($result['status'] != 0) {
        $this->error('Error: '.$result['data']);
        return;
      }

      // 2] Вывести его в терминал
      $this->table(['ID', 'type', 'ison', 'package', 'protocol', 'subdomain', 'domain', 'uri'], json_decode($result['data']['prepeared'], true));

    };

    // 2. Получить все параметры для создания нового роута

      // 2.1. Подготовить массив для параметров
      $params = [];

      // 2.2. Определить, к какому D,L,W-пакету будет относиться роут

        // 2.2.1. Получить коллекцию всех D,L,W-пакетов
        $packages = \M4\Models\MD8_packages::whereHas('packtypes', function($query){
          $query->where(function($query){
            $query->where('name','=','D')->
                    orWhere('name','=','L')->
                    orWhere('name','=','W');
          });
        })->pluck('aboutpack', 'id_inner');
        $packages = $packages->map(function($item, $key){
          return json_decode($item,true)['EN']['description'];
        });

        // 2.2.2. Если коллекция $packages пуста, сообщить и завершить
        if($packages->count() == 0) {
          $this->error('There is no D,W,W-packages in the app, for which any route could be created.');
          return;
        }

        // 2.2.3. Спросить
        $params['packid'] = $this->choice('Choose D,W,L-package, for which needs to create a new route', $packages->toArray());

      // 2.3. Получить домен

        // Получить
        $params['domain'] = $this->ask('Enter domain (without http(s)://, and without subdomains). For example: site.ru');

        // Провести валидацию
        if(preg_match("/^[-0-9а-яёa-z.]+$/ui", $params['domain']) == 0) {
          $this->error('The domain you have entered is not valid. It must match: ^[-0-9а-яёa-z.]+$');
          return;
        }

      // 2.4. Получить протокол
      $params['protocol'] = $this->choice('Choose protocol', ['1'=>'http', '2'=>'https'], '1');

      // 2.5. Получить поддомен

        // Получить
        $params['subdomain'] = $this->ask('Enter subdomain, or leave blank. It must ends with a dot. For example: sub1. ', 0);

        // Провести валидацию
        if(preg_match("/^[-0-9а-яёa-z.]+$/ui", $params['subdomain']) == 0) {
          $this->error('The subdomain you have entered is not valid. It must match: ^[-0-9а-яёa-z.]+$');
          return;
        }

      // 2.6. Получить URI

        // Получить
        $params['uri'] = $this->ask('Enter URI, or leave bland (default uri is / ). It must have / as prefix and not have it as postfix. For example: /users/docs ', '/');

        // Провести валидацию
        if(preg_match("/^([\/]{1}[-0-9а-яёa-z\/_]*|\/)$/ui", $params['uri']) == 0) {
          $this->error('The uri you have entered is not valid. It must match: ^([\/]{1}[-0-9а-яёa-z\/_]*|\/)$');
          return;
        }

    // 3. Выполнить команду

      // 3.1. Выполнить команду
      $result = runcommand('\M4\Commands\C4_new', ['params' => $params]);

      // 3.2. В случае неудачи, вывести текст ошибки
      if($result['status'] != 0) {
        $this->error('Error: '.$result['data']);
        return;
      }

      // 3.3. В случае успеха, вывести соотв.сообщение
      $this->info("Success");

      // 3.4. Получить все роуты, и вывести их в терминал
      $show();

  }

}