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
     *  2. Запросить у пользователя ID пакета, который он хочет клонировать с github
     *  3. Провести всестороннюю проверку возможности клонировать пакет $packid в этот проект
     *
     *  n. Обновить приложение после клонирования пакета с github
     *
     *
     */

    // 1. Обновить приложение перед клонированием пакета с github
    //$this->call('m1:parseapp');

    // 2. Запросить у пользователя ID пакета, который он хочет клонировать с github
    $packid = $this->ask("Type ID of the package you want to clone. For example: M12");

    // 3. Провести всестороннюю проверку возможности клонировать пакет $packid в этот проект
    // - Функция возвращает структуру следующего содержания:
    //
    //   [
    //     verdict: <true/false>,
    //     error:   <тест ошибки>
    //   ]
    //

      // 3.1. Выяснить, может ли $packid быть клонирова в этот проект
      // - Если нет, то почему?
      $whether_could_be_cloned = call_user_func(function() USE ($packid) {

        // 1] Подготовить чек-лист
        $checklist = [
          "format" => [
            "verdict" => false,
            "error"   => "Wrong package ID format. Valid examples: M1, D10000, L10000, W1, R1."
          ],
          "presence" => [
            "verdict" => false,
            "error"   => "This package is already in the project."
          ],
          "github" => [
            "verdict" => false,
            "error"   => "This package is absent on github, or we could not verify."
          ]
        ];

        // 2] Выяснить, верный ли формат имеет $packid


        
        // 3] Выяснить, есть ли уже пакет в проекте



        // 4] Выяснить, есть ли уже пакет на github



        // 5] Вынести финальный вердикт
        $final_verdict = call_user_func(function() USE ($checklist) {

          // 5.1] Попробовать найти в $checklist пункт с отрицательным вердиктом
          $notvalid = "";
          foreach($checklist as $item) {
            if($item['verdict'] == false)
              $notvalid = $item;
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

      // 3.2. Если нет, то сообщить, почему, и завершить
      if($whether_could_be_cloned['verdict'] == false) {
        $this->error("The package ".$packid." could not be cloned: ".$whether_could_be_cloned['error']);
        return;
      }






    // n. Обновить приложение после клонирования пакета с github
    //Artisan::queue('m1:run_light');

  }

}