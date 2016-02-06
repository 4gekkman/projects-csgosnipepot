<?php
////======================================================////
////																										  ////
////            Консольная команда M-пакета					      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Turn on/off auto or manual route
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
class T6_switch extends Command
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

    protected $signature = 'm4:switch';

  //-----------------------------//
  // 2. Описание artisan-команды //
  //-----------------------------//

    protected $description = 'Turn on/off auto or manual route';

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
     *  2. Получить все роуты, и вывести их в терминал
     *  3. Спросить, требуется включить или выключить роут
     *  4. Спросить, с какими роутами требуется провести операцию
     *  5. Если требуется включить роуты
     *  6. Если требуется выключить роуты
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

      // 2. Получить все роуты, и вывести их в терминал
      $show();

      // 3. Спросить, требуется включить или выключить роут
      $what = $this->choice('What do you want: turn on or turn off some routes?', ['1'=>'Turn on', '2'=>'Turn off'], '1');

      // 4. Спросить, с какими роутами требуется провести операцию

        // 4.1. Подготовить массив
        $routes = [];

        // 4.2. Опросить клиента
        while($answer = $this->anticipate("[NOT REQUIRED] Type id (must to be a number) of the route, or leave blank", [], 0)) {
          array_push($routes, $answer);
        }

        // 4.3. Проверить содержимое $routes, всё должно быть цифрами
        foreach($routes as $route) {
          if(preg_match("/^[0-9]+$/ui", $route) == 0) {
            $this->error('One of the values that you have entered is not a number.');
            return;
          }
        }

      // 5. Если требуется включить роуты
      if($what == 'Turn on') {

        // 5.1. Выполнить команду
        $result = runcommand('\M4\Commands\C6_on', ['ids' => $routes]);

        // 5.2. В случае неудачи, вывести текст ошибки
        if($result['status'] != 0) {
          $this->error('Error: '.$result['data']);
          return;
        }

        // 5.3. В случае успеха, вывести соотв.сообщение
        $this->info("Success");

        // 5.4. Получить все роуты, и вывести их в терминал
        $show();

      }

      // 6. Если требуется выключить роуты
      if($what == 'Turn off') {

        // 6.1. Выполнить команду
        $result = runcommand('\M4\Commands\C7_off', ['ids' => $routes]);

        // 6.2. В случае неудачи, вывести текст ошибки
        if($result['status'] != 0) {
          $this->error('Error: '.$result['data']);
          return;
        }

        // 6.3. В случае успеха, вывести соотв.сообщение
        $this->info("Success");

        // 6.4. Получить все роуты, и вывести их в терминал
        $show();

      }

  }

}