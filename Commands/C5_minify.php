<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Make c.min.css and j.min.js in app Public for D-packages
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *
 *    ]
 *
 *  Формат возвращаемого значения
 *  -----------------------------
 *
 *    [
 *      status          // 0 - всё ОК, -1 - нет доступа, -2 - ошибка
 *      data            // результат выполнения команды
 *    ]
 *
 *  Значение data в зависимости от статуса
 *  --------------------------------------
 *
 *    status == 0
 *    -----------
 *      - ""
 *
 *    status == -1
 *    ------------
 *      - Не контролируется в командах. Отслеживается в хелпере runcommand.
 *
 *    status = -2
 *    -----------
 *      - Текст ошибки.
 */

//-----------------------//
// Ликбез по минификации //
/*-----------------------//

  Общие принципы минификации
  --------------------------

      # При формировании минификации, другие минификации не используются
        - Допустим, надо сделать минификацию пакету D1.
        - А он зависит от пакета L1, у которого есть минификация.
        - Так вот, минификация пакета L1 не используется.
        - Вместо этого вычисляется всё дерево зависимостей D1.
        - И нужные ресурсы из него включаются в итоговую минификацию.

      # Минификация всего дерева зависимостей
        - Допустим, нужна нам минификация для пакета D1.
        - Этот пакет прямо может зависеть от N других пакетов.
        - Те, в свою очередь, ещё от других пакетов, и т.д.
        - В итоге, получается целое дерево зависимостей.
        - Минификации для D1 должны включать нужный CSS и JS
          для всех пакетов из этого дерева.

      # Минификация требуется только для D-пакетов
        - Потому что только D-пакеты являются самостоятельными интерфейсами.
        - А L,W-пакеты, к примеру, являются вспомогательными для D-пакетов.
        - И сами по себе L,W-представления (без D-представлений) отображаться
          не могут, и собственного URL (для возврата представления по GET-запросу)
          не имеют.

      # Результаты минификации помещаются в Public проекта, в каталоги D-пакетов
        - Например, рассмотрим минификации для пакета D5.
        - Они помещаются в:

          /[Проект]
            /Public
              /D5
                Images
                c.min.css
                j.min.js

      # Для представлений L,W-пакетов подключать css/js ресурсы не следует
        - Поскольку они будут представлены в c.min.css и j.min.js для D-пакетов.

      # Пространства имён для CSS различных пакетов
        - Весь CSS каждого пакета должен префикситься своим пространством имён.
        - Допустим, есть у нас пакет P5, и в нём файл c.css
        - Все классы в нём должны идти с префиксом: "P5_".
        - Тогда при слиянии и минификации CSS из разных пакетов,
          не будет возникать коллизий.

      # Решение проблем с коллизиями JS-кода при минификации

        # JS-код P-пакетов
          - Весь JS-код каждого P-пакета должен лежать в отдельном объекте.
          - Имя которого строится по шаблону: "library_" + [ID P-пакета].
          - Примеры: "library_p1", "library_p2"

        # JS-код D-пакетов
          - Весь он лежит в объекте model.

        # JS-код K-пакетов
          - При создании компонента можно задавать параметры.
          - У каждого компонента можно задать параметр 'model'.
          - В него можно поместить ссылку на KO-наблюдаемую,
            находящуюся в (либо/либо):

              - Объекте "model" D-пакета
              - Объекте "widget_..." W-пакета
              - Объекте "layout_..." L-пакета

          - Т.О. JS-код K-пакета встраивается в объект внутри
            одного из вышеуказанных объектов.

        # JS-код W-пакетов
          - Весь он лежит в объекте, имя которого строится по
            шаблону "widget_" + [ID W-пакета].

        # JS-код L-пакетов
          - Весь он лежит в объекте, имя которого строится по
            шаблону "layout_" + [ID L-пакета].


  Для пакетов каких типов требуется минификация css и js
  ------------------------------------------------------

      Только для D-пакетов


  CSS и JS из каких пакетов используются для минификации
  ------------------------------------------------------

      D     | Да
      L     | Да
      W     | Да
      P     | Да
      K     | Да

      R     | Нет, т.к. структура R не стандартизирована
      M     | Не имеют css/js вообще


  От пакетов каких типов может зависеть каждый из типов пакетов
  -------------------------------------------------------------

      M     | R
      D     | M,L,W,R,P,K,
      L     | M,W,R,P,K,
      W     | M,R,P,K,
      R     | R
      P     | P
      K     | P


  CSS из зависимостей каких типов должны войти в c.min.css для D-пакетов
  ----------------------------------------------------------------------

      D     | D,L,W,P,K


  JS из зависимостей каких типов должны войти в j.min.css для D-пакетов
  ---------------------------------------------------------------------

      D     | D,L,W,P,K


  Откуда брать данные для минификации для c.min.css
  -------------------------------------------------

      D     | Minify/c.css
      L     | Minify/c.css
      W     | Minify/c.css
      P     | Minify/c.css
      K     | Minify/c.css


  Откуда брать данные для минификации для j.min.css
  -------------------------------------------------

      D     | Minify/m.js, Minify/f.js, Minify/j.js
      L     | Minify/m.js, Minify/f.js, Minify/j.js
      W     | Minify/m.js, Minify/f.js, Minify/j.js
      P     | Minify/j.js
      K     | Minify/j.js


  Стратегия сбора данных для CSS-минификации
  ------------------------------------------

    - Порядок значения не имеет.
    - Собираем список CSS-файлов для минификации,
      и минифицируем в любом порядке.


  Стратегия сбора данных для JS-минификации
  -----------------------------------------

    1. Сначала подключаем JS из P-пакетов
      - Имеет значение их взаимный порядок подключения.
      - Допустим, для D1 есть такое дерево зависимостей:

              0 1 2 3      - Уровни

            D1
              L1
                P2
                  P3
                    P5
                W1
                  P4
                    P5
              P1
                P5
              P4

      - Нужно просматривать это дерево уровень за уровнем.
      - Начинать с 1-го, затем 2-й, затем 3-й, и так далее.
      - И составить индекс P-пакетов на каждом из уровней:

        0: ['P1']
        1: ['P2','P5']
        2: ['P3','P4']
        3: ['P5']

      - На всех уровнях, каждый P-пакет должен быть уникален.
        В случае появления повтора, приоритет у P-пакета на
        уровне с бОльшим индексом.
      - Составив индекс, подключаем P-пакеты, начиная от большего
        к меньшему индексу. Внутри индекса порядок подключения
        значения не имеет.

    2. Затем подключаем JS из K-пакетов
      - В любом порядке.

    3. Затем подключаем JS из L-пакетов
      - Между L-пакетами, в любом порядке.
      - Внутри пакета, в порядке: m.js -> f.js -> j.js

    4. Затем подключаем JS из W-пакетов
      - Между W-пакетами, в любом порядке.
      - Внутри пакета, в порядке: m.js -> f.js -> j.js

    5. Затем подключаем JS из D-пакетов
      - Между D-пакетами, в любом порядке.
      - Внутри пакета, в порядке: m.js -> f.js -> j.js


//---------------------------*/
// Пространство имён команды //
//---------------------------//
// - Пример для админ.документов:  M1\Documents\Main\Commands

  namespace M1\Commands;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Базовые классы, необходимые для работы команд вообще
  use App\Jobs\Job,
      Illuminate\Queue\SerializesModels,
      Illuminate\Queue\InteractsWithQueue,
      Illuminate\Contracts\Queue\ShouldQueue;   // добавление в очередь задач

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


//---------//
// Команда //
//---------//
class C5_minify extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Принять данные
    public $data;

  //------------------------------------------------------//
  // В. Принять аргументы, переданные при запуске команды //
  //------------------------------------------------------//
  public function __construct($data)  // TODO: указать аргументы
  {

    $this->data = $data;

  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Осуществление CSS-минификации для D-пакетов
     *  2. Осуществление JS-минификации для D-пакетов
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------//
    // 1. Осуществление CSS-минификации для D-пакетов //
    //------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1.1. Получить коллекцию всех D-пакетов
      $dpackages = \M1\Models\MD2_packages::with([
        'dependencies',
        'locale',
        'locales',
        'packtype',
        'models',
        'commands',
        'consoles',
        'handlers',
      ])->whereHas('packtype', function($query){
        $query->where('name','=','D');
      })->get();

      // 1.2. Написать рекурсивную функцию для подгрузки связей всех зависимостей
      $load_all_rels = function(&$dpackages) use (&$load_all_rels){

        // 1] Подгрузить связи всех пакетов из коллекции $dpackages
        $dpackages->load([
          'dependencies',
          'locale',
          'locales',
          'packtype',
          'models',
          'commands',
          'consoles',
          'handlers',
        ]);

        // 2] Подгрузить связи
        foreach($dpackages as $dpackage) {
          $load_all_rels($dpackage->dependencies);
        }

      };

      // 1.3. Для каждого D-пакета сформировать деревья зависимостей
      $load_all_rels($dpackages);

      // 1.4. Написать рекурсивную функцию формирования зависимостей
      // - Она принимает коллекцию D-пакетов со всеми подгруженными зависимостями.
      // - Пробегает рекурсивно все зависимости каждого D-пакета.
      // - Для каждого изначального D-пакета формирует массив зависимостей.
      // - Каждая запись в этом массиве в формате: [id пакета] => [путь к c.css].
      // - Результат работы функции - массив: [id D-паката] = > [массив зависимостей]
      $get_css_dep_arr = function($packages, &$resultarr, $level, $parentpack = '') USE (&$get_css_dep_arr) {
        foreach($packages as $package) {

          // 1] Если $level == 1
          if($level == 1) {
            $resultarr[$package->id_inner] = [];
            $get_css_dep_arr($package->dependencies, $resultarr, $level+1, $package);
          }

          // 2] Если $level > 1
          else {

            // 2.1] Если $package является L-пакетом
            if($package->packtype->name == "L") {
              $resultarr[$parentpack->id_inner][$package->id_inner] = base_path("vendor/4gekkman/$package->id_inner/Minify/c.css");
            }

            // 2.2] Если $package является P-пакетом
            if($package->packtype->name == "P") {

              // 2.2.1] Проверить наличие файла links.json у P-пакета
              $links = file_exists(base_path('vendor/4gekkman/'.$package->id_inner.'/Minify/links.json'));
              if(!$links)
                throw new \Exception('У P-пакета '.$package->id_inner.' не найден файл /Minify/links.json');

              // 2.2.2] Извлечь содержимое файла $links
              config(['filesystems.default' => 'local']);
              config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$package->id_inner.'/Minify')]);
              $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
              $links = json_decode($this->storage->get('links.json'), true);

              // 2.2.3] Проверить CSS-ссылки на существование
              foreach($links['css'] as $link) {
                if(!r1_url_exists($link))
                  throw new \Exception('У P-пакета '.$package->id_inner.', в links.json, указана не рабочая ссылка на css: '.$link);
              }

              // 2.2.4] Подготовить массив для ссылок на css P-пакета
              $resultarr[$parentpack->id_inner][$package->id_inner] = [];

              // 2.2.5] Если linksfirst == false
              if($links['linksfirst'] == false) {

                // Добавить ссылку на c.css P-пакета
                array_push($resultarr[$parentpack->id_inner][$package->id_inner], base_path("vendor/4gekkman/$package->id_inner/Minify/c.css"));

              }

              // 2.2.6] Добавить ссылки из $links в $resultarr
              foreach($links['css'] as $link) {
                array_push($resultarr[$parentpack->id_inner][$package->id_inner], $link);
              }

              // 2.2.7] Если linksfirst == true
              if($links['linksfirst'] == true) {

                // Добавить ссылку на c.css P-пакета
                array_push($resultarr[$parentpack->id_inner][$package->id_inner], base_path("vendor/4gekkman/$package->id_inner/Minify/c.css"));

              }
            }

            // 2.3] Если $package является K-пакетом
            if($package->packtype->name == "K") {
              $resultarr[$parentpack->id_inner][$package->id_inner] = base_path("vendor/4gekkman/$package->id_inner/Minify/c.css");
            }

            // 2.4] Если $package является W-пакетом
            if($package->packtype->name == "W") {
              $resultarr[$parentpack->id_inner][$package->id_inner] = base_path("vendor/4gekkman/$package->id_inner/Minify/c.css");
            }

            // 2.5] Рекурсивно запустить функцию
            $get_css_dep_arr($package->dependencies, $resultarr, $level+1, $parentpack);

          }

        }
      };

      // 1.5. Сформировать для $dpackages массивы зависимостей
      $css_dep_arr = [];
      $get_css_dep_arr($dpackages, $css_dep_arr, 1);

      // 1.6. Создать c.min.css для каждого документа всех D-пакетов
      // - С помощью пакета matthiasmullie/minify.
      foreach($dpackages as $dpackage) {

        // 1] Создать объект Minify для c.css этого документа
        $minify = new \MatthiasMullie\Minify\CSS();

        // 2] Добавить в объект $minify все css файлы из $css_dep_arr
        foreach($css_dep_arr[$dpackage->id_inner] as $packid => $csspath) {

          // 2.1] Если это не P-пакет
          if(preg_match("#^P#ui", $packid) == 0)
            $minify->add($csspath);

          // 2.2] Если это P-пакет
          else {
            foreach($csspath as $css) {

              $minify->add(file_get_contents($css));

            }
          }

        }

        // 3] Добавить в объект $minify css из D-пакета
        $minify->add(base_path("vendor/4gekkman/$dpackage->id_inner/Minify/c.css"));

        // 4] Создать c.min.css и сохранить в Public документа $doc
        $minify->minify(base_path("Public/$dpackage->id_inner/c.min.css"));

      }

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'CSS-minification have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'minify']);
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;


    //-----------------------------------------------//
    // 2. Осуществление JS-минификации для D-пакетов //
    //-----------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 2.1. Получить коллекцию всех D-пакетов
      $dpackages = \M1\Models\MD2_packages::with([
        'dependencies',
        'locale',
        'locales',
        'packtype',
        'models',
        'commands',
        'consoles',
        'handlers',
      ])->whereHas('packtype', function($query){
        $query->where('name','=','D');
      })->get();

      // 1.2. Написать рекурсивную функцию для подгрузки связей всех зависимостей
      $load_all_rels = function(&$dpackages) use (&$load_all_rels){

        // 1] Подгрузить связи всех пакетов из коллекции $dpackages
        $dpackages->load([
          'dependencies',
          'locale',
          'locales',
          'packtype',
          'models',
          'commands',
          'consoles',
          'handlers',
        ]);

        // 2] Подгрузить связи
        foreach($dpackages as $dpackage) {
          $load_all_rels($dpackage->dependencies);
        }

      };

      // 1.3. Для каждого D-пакета сформировать деревья зависимостей
      $load_all_rels($dpackages);

      // 1.4. Сформировать деревья P-зависимостей
      // - Надо рекурсивно пробежаться по всем зависимостям D-пакетов.
      // - При 1-м натыкании на P-пакет, такие пакеты становятся корнями,
      //   и получают уровень №0. И для него ф-ия запускается рекурсивно.
      // - При рекурсивном вызове параметром передаётся ссылка на пакет
      //   последнего добавленного уровня.
      // - Во время просмотра зависимостей очередного пакета,
      //   все найденные в нём P-пакеты добавляются в значение-массив
      //   пакета предыдущего уровня. Для каждого из них функция
      //   запускается рекурсивно.
      // - В итоге должен получитсья такой массив (пример):
      //
      //    [
      //      "P1": ["P3":[],"P4":[]],
      //      "P2": ["P5":[],"P6":["P7":[]]],
      //    ]
      //
      $get_p_dep_arr = function($packages, &$resultarr, $level, $parent_p_pack = '', &$parent_arr = '') USE (&$get_p_dep_arr) {
        foreach($packages as $package) {

          // 1] Если это уровень №1
          if(empty($parent_p_pack)) {

            // Если $package является P-пакетом
            if($package->packtype->name == "P") {
              $resultarr[$package->id_inner] = [];
              $get_p_dep_arr($package->dependencies, $resultarr, $level+1, $package, $resultarr[$package->id_inner]);
            }

            // Если $package не является P-пакетом
            else {
              $get_p_dep_arr($package->dependencies, $resultarr, $level, $parent_p_pack, $parent_arr);
            }

          }

          // 2] Если это уровень > 1
          else {

            // Если $package является P-пакетом
            if($package->packtype->name == "P") {
              $parent_arr[$package->id_inner] = [];
              $get_p_dep_arr($package->dependencies, $resultarr, $level+1, $package, $parent_arr[$package->id_inner]);
            }

            // Если $package не является J-пакетом
            else {
              $get_p_dep_arr($package->dependencies, $resultarr, $level, $parent_p_pack, $parent_arr);
            }

          }

        }
      };
      $p_dep_arr = [];
      $get_p_dep_arr($dpackages, $p_dep_arr, 1);

      // 1.5. Написать функцию, формирующую массив в таком формате
      // - И получить соответствующий массив.
      /**
       *
       * $result = [                    // Результат
       *   0 => [                       //   Дерево №0
       *     0 => [                     //     Уровень №0
       *       "P3" => [
       *          "/path/to",
       *          "/path/to",
       *        ]
       *       "P4" => [
       *          "/path/to",
       *          "/path/to",
       *        ]
       *     ],
       *     1 => [                     //     Уровень №1
       *       "P1" => [
       *          "/path/to",
       *          "/path/to",
       *        ]
       *     ]
       *   ],
       *   1 => [                       //   Дерево №1
       *     0 => [
       *       "P5" => [
       *          "/path/to",
       *          "/path/to",
       *        ],
       *       "P6" => [
       *          "/path/to",
       *          "/path/to",
       *        ]
       *     ],
       *     1 => [
       *       "P2" => [
       *          "/path/to",
       *          "/path/to",
       *        ]
       *     ]
       *   ]
       * ]
       *
      */
      $prepear_p_deps = function($p_dep_arr){

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Написать функцию для рекурсивного обхода p-дерева
        $recur = function($p_dep_arr, &$treearr) USE (&$recur) {

          // 2.1] Добавить новый пустой массив в $treearr
          // - Если $p_dep_arr не пуст
          if(count($p_dep_arr) != 0)
            array_push($treearr, []);

          // 2.2] Пробежаться по $p_dep_arr
          foreach($p_dep_arr as $packid => $arr) {

            // 2.2.1] Проверить наличие файла links.json у P-пакета
            $links = file_exists(base_path('vendor/4gekkman/'.$packid.'/Minify/links.json'));
            if(!$links)
              throw new \Exception('У P-пакета '.$packid.' не найден файл /Minify/links.json');

            // 2.2.2] Извлечь содержимое файла $links
            config(['filesystems.default' => 'local']);
            config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packid.'/Minify')]);
            $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
            $links = json_decode($this->storage->get('links.json'), true);

            // 2.2.3] Проверить JS-ссылки на существование
            foreach($links['js'] as $link) {
              if(!r1_url_exists($link))
                throw new \Exception('У P-пакета '.$packid.', в links.json, указана не рабочая ссылка на js: '.$link);
            }

            // 2.2.4] Подготовить массив для ссылок на js P-пакета
            $treearr[count($treearr)-1][$packid] = [];

            // 2.2.5] Если linksfirst == false
            if($links['linksfirst'] == false) {

              // Добавить ссылку на j.js P-пакета
              array_push($treearr[count($treearr)-1][$packid], base_path("vendor/4gekkman/$packid/Minify/j.js"));

            }

            // 2.2.6] Добавить ссылки из $links в $treearr
            foreach($links['js'] as $link) {
              array_push($treearr[count($treearr)-1][$packid], $link);
            }

            // 2.2.7] Если linksfirst == true
            if($links['linksfirst'] == true) {

              // Добавить ссылку на j.js P-пакета
              array_push($treearr[count($treearr)-1][$packid], base_path("vendor/4gekkman/$packid/Minify/j.js"));

            }

            // 2.2.8] Запустить $recur для $arr
            $recur($arr, $treearr);

          }

        };

        // 3] Пробежаться по $p_dep_arr
        foreach($p_dep_arr as $packid => $arr) {

          // 3.1] Добавить в $result новый массив
          array_push($result, []);

          // 3.2] Добавить в новый массив пустой массив
          array_push($result[count($result) - 1], []);

          // 3.3] Проверить наличие файла links.json у P-пакета
          $links = file_exists(base_path('vendor/4gekkman/'.$packid.'/Minify/links.json'));
          if(!$links)
            throw new \Exception('У P-пакета '.$packid.' не найден файл /Minify/links.json');

          // 3.4] Извлечь содержимое файла $links
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packid.'/Minify')]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          $links = json_decode($this->storage->get('links.json'), true);

          // 3.5] Проверить JS-ссылки на существование
          foreach($links['js'] as $link) {
            if(!r1_url_exists($link))
              throw new \Exception('У P-пакета '.$packid.', в links.json, указана не рабочая ссылка на js: '.$link);
          }

          // 3.6] Подготовить массив для ссылок на js P-пакета
          $result[count($result)-1][count($result[count($result) - 1])-1][$packid] = [];

          // 3.7] Если linksfirst == false
          if($links['linksfirst'] == false) {

            // Добавить ссылку на j.js P-пакета
            array_push($result[count($result)-1][count($result[count($result) - 1])-1][$packid], base_path("vendor/4gekkman/$packid/Minify/j.js"));

          }

          // 3.8] Добавить ссылки из $links в $treearr
          foreach($links['js'] as $link) {
            array_push($result[count($result)-1][count($result[count($result) - 1])-1][$packid], $link);
          }

          // 3.9] Если linksfirst == true
          if($links['linksfirst'] == true) {

            // Добавить ссылку на j.js P-пакета
            array_push($result[count($result)-1][count($result[count($result) - 1])-1][$packid], base_path("vendor/4gekkman/$packid/Minify/j.js"));

          }

          // 3.10] Запустить $recur для $arr
          $recur($arr, $result[count($result)-1]);

        }

        // 4] Инвертировать порядок уровней внутри деревьев
        foreach($result as &$r) {
          $r = array_reverse($r);
        }

        // 5] Вернуть результат
        return $result;

      };
      $p_deps = $prepear_p_deps($p_dep_arr);

      // 1.6. Подготовить массив js-файлов для минификации
      // - Подключение будет проводиться в определённом порядке.
      // - Начиная от 0-го индекса, и выше.
      $arr4min = [];

      // 1.7. Добавить в $arr4min файлы из $p_deps
      foreach($p_deps as $ptree) {
        foreach($ptree as $plevel) {
          foreach($plevel as $packid => $ppath) {
            $arr4min[$packid] = $ppath;
          }
        }
      }

      // 1.8. Сформировать индекс K-зависимостей для каждого D-пакета
      // - По id_inner D-пакета можно получить массив с путями
      //   к j.js его зависимостей - K-пакетов.
      $get_d_deps_of_k_index = function($dpackages, &$resultarr, $id_inner, $level) USE (&$get_d_deps_of_k_index) {
        foreach($dpackages as $dpackage) {

          if($level == 1) {

            // 1] Добавить в $resultarr новый массив
            // - С ключём, равным $dpackage->id_inner
            $resultarr[$dpackage->id_inner] = [];

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_k_index($dpackage->dependencies, $resultarr, $dpackage->id_inner, $level+1);

          } else {

            // 1] Если $dpackage является K-пакетом
            // - Добавить путь к JS этого пакета в $resultarr[$id_inner]
            if($dpackage->packtype->name == "K") {

              array_push($resultarr[$id_inner], base_path("vendor/4gekkman/$dpackage->id_inner/Minify/j.js"));

            }

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_k_index($dpackage['dependencies'], $resultarr, $id_inner, $level+1);

          }

        }
      };
      $kdeps_index = [];
      $get_d_deps_of_k_index($dpackages, $kdeps_index, '', 1);

      // 1.9. Сформировать индекс L-зависимостей для каждого D-пакета
      // - По id_inner D-пакета можно получить массив с путями
      //   к m.js, f.js, j.js его зависимостей - L-пакетов.
      $get_d_deps_of_l_index = function($dpackages, &$resultarr, $id_inner, $level) USE (&$get_d_deps_of_l_index) {
        foreach($dpackages as $dpackage) {

          if($level == 1) {

            // 1] Добавить в $resultarr новый массив
            // - С ключём, равным $dpackage->id_inner
            $resultarr[$dpackage->id_inner] = [];

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_l_index($dpackage->dependencies, $resultarr, $dpackage->id_inner, $level+1);

          } else {

            // 1] Если $dpackage является L-пакетом
            // - Добавить путь к JS этого пакета в $resultarr[$id_inner]
            if($dpackage->packtype->name == "L") {

              array_push($resultarr[$id_inner], [
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/m.js"),
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/f.js"),
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/j.js")
              ]);

            }

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_l_index($dpackage['dependencies'], $resultarr, $id_inner, $level+1);

          }

        }
      };
      $ldeps_index = [];
      $get_d_deps_of_l_index($dpackages, $ldeps_index, '', 1);

      // 1.10. Сформировать индекс W-зависимостей для каждого D-пакета
      // - По id_inner D-пакета можно получить массив с путями
      //   к m.js, f.js, j.js его зависимостей - W-пакетов.
      $get_d_deps_of_w_index = function($dpackages, &$resultarr, $id_inner, $level) USE (&$get_d_deps_of_w_index) {
        foreach($dpackages as $dpackage) {

          if($level == 1) {

            // 1] Добавить в $resultarr новый массив
            // - С ключём, равным $dpackage->id_inner
            $resultarr[$dpackage->id_inner] = [];

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_w_index($dpackage->dependencies, $resultarr, $dpackage->id_inner, $level+1);

          } else {

            // 1] Если $dpackage является W-пакетом
            // - Добавить путь к JS этого пакета в $resultarr[$id_inner]
            if($dpackage->packtype->name == "W") {

              array_push($resultarr[$id_inner], [
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/m.js"),
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/f.js"),
                base_path("vendor/4gekkman/$dpackage->id_inner/Minify/j.js")
              ]);

            }

            // 2] Рекурсивно запустить функцию
            $get_d_deps_of_w_index($dpackage['dependencies'], $resultarr, $id_inner, $level+1);

          }

        }
      };
      $wdeps_index = [];
      $get_d_deps_of_w_index($dpackages, $wdeps_index, '', 1);

      // 1.11. Сформировать индекс всех JS-зависимостей для каждого D-пакета
      $jsdeps_for_dpacks = call_user_func(function() USE ($dpackages, $arr4min, $kdeps_index, $ldeps_index, $wdeps_index) {

        // 1] Создать массив для результата
        $result = [];

        // 2] Сформировать $result
        foreach($dpackages as $dpackage) {

          // 2.1] Добавить в $result новый массив с ключём $dpackage->id_inner
          $result[$dpackage->id_inner] = [];

          // 2.2] Добавить зависимости из $arr4min
          foreach($arr4min as $pdep) {
            array_push($result[$dpackage->id_inner], $pdep);
          }

          // 2.3] Добавить зависимости из $kdeps_index
          foreach($kdeps_index as $kdep) {
            if(!empty($kdep))
              array_push($result[$dpackage->id_inner], $kdep[0]);
          }

          // 2.4] Добавить зависимости из $ldeps_index
          foreach($ldeps_index as $ldep) {
            if(!empty($ldep)) {
              array_push($result[$dpackage->id_inner], $ldep[0][0]);
              array_push($result[$dpackage->id_inner], $ldep[0][1]);
              array_push($result[$dpackage->id_inner], $ldep[0][2]);
            }
          }

          // 2.5] Добавить зависимости из $wdeps_index
          foreach($wdeps_index as $wdep) {
            if(!empty($wdep)) {
              array_push($result[$dpackage->id_inner], $wdep[0][0]);
              array_push($result[$dpackage->id_inner], $wdep[0][1]);
              array_push($result[$dpackage->id_inner], $wdep[0][2]);
            }
          }

          // 2.6] Добавить JS самого D-пакета
          array_push($result[$dpackage->id_inner], base_path("vendor/4gekkman/$dpackage->id_inner/Minify/m.js"));
          array_push($result[$dpackage->id_inner], base_path("vendor/4gekkman/$dpackage->id_inner/Minify/f.js"));
          array_push($result[$dpackage->id_inner], base_path("vendor/4gekkman/$dpackage->id_inner/Minify/j.js"));

        }

        // 3] Вернуть результат
        return $result;

      });

      // 1.12. Убрать из $jsdeps_for_dpacks все повторы
      // - В случае наличия повторов убирать те, у которых индекс больше
      // - После, обновить ключи.
      foreach($jsdeps_for_dpacks as &$jsdeps_for_dpack) {
        $jsdeps_for_dpack = r1_array_unique_recursive($jsdeps_for_dpack);
      }
      $jsdeps_for_dpacks = array_values($jsdeps_for_dpacks);

      // 1.13. Создать j.min.js для каждого D-пакета
      // - С помощью пакета matthiasmullie/minify.
      foreach($dpackages as $dpackage) {

        // 1] Создать объект Minify
        $minify = new \MatthiasMullie\Minify\JS();

        // 2] Добавить в $minify файлы из $jsdeps_for_dpacks
        foreach($jsdeps_for_dpacks as $key => $value) {
          foreach($jsdeps_for_dpacks[$key] as $path) {

            // 2.1] Если это не P-пакет
            if(!is_array($path))
              $minify->add($path);

            // 2.2] Если это P-пакет
            else {
              foreach($path as $js) {
Log::info($js);
                $minify->add(file_get_contents($js));

              }
            }

          }
        }

        // 3] Создать j.min.js и сохранить в Public документа $doc
        $minify->minify(base_path("Public/$dpackage->id_inner/j.min.js"));

      }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'JS-minification have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'minify']);
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;




    //---------------------//
    // N. Вернуть статус 0 //
    //---------------------//
    return [
      "status"  => 0,
      "data"    => ""
    ];

  }

  //--------------//
  // Д. Заготовки //
  //--------------//
  /**
   *
   * Д1. Провести валидацию
   * Д2. Начать транзакцию
   * Д3. Подтвердить транзакцию
   * Д4. Сохранить данные в БД
   * Д5. Создать элемент
   * Д6. Удалить элемент
   *
   *
   */


    // Д1. Провести валидацию //
    //------------------------//

      //// x. Провести валидацию
      //
      //  // Создать объект-валидатор
      //  $validator = Validator::make($this->data, [
      //    'prop1'               => 'sometimes|rule1',
      //    'prop2'               => 'sometimes|rule2',
      //    'prop3'               => 'sometimes|rule3',
      //  ]);
      //
      //  // Провести валидацию, и если она провалилась...
      //  if ($validator->fails()) {
      //
      //    // Вернуть статус -2 и ошибку
      //    return [
      //      "status"  => -2,
      //      "data"    => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
      //    ];
      //
      //  }


    // Д2. Начать транзакцию //
    //-----------------------//

      //// x. Начать транзакцию
      //DB::beginTransaction();


    // Д3. Подтвердить транзакцию //
    //----------------------------//

      //// x. Подтвердить транзакцию
      //DB::commit();


    // Д4. Сохранить данные в БД //
    //---------------------------//

      //// x. Сохранить данные в БД
      //try {
      //
      //  // 4.1. Получить eloquent-объект
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 4.2. Сохранить в v присланные данные
      //  foreach($this->data as $key => $value) {
      //
      //    // Если $key == 'id', перейти к следующей итерации
      //    if($key == 'id') continue;
      //
      //    // Если в таблице есть столбец $key
      //    if(lib_hasColumn('m1', 'MD1_somemodel', $key)) {
      //
      //      // Изменить значение столбца $key на $value
      //      $item[$key] = $value;
      //
      //      // Сохранить изменения
      //      $item->save();
      //
      //    }
      //
      //  }
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д5. Создать элемент //
    //---------------------//

      //// x. Создать новый элемент
      //try {
      //
      //  // 1] Попробовать найти удалённый элемент
      //  $item = \M7\Models\MD1_somemodel::onlyTrashed()->where('name','=',$this->data['name'])->first();
      //
      //  // 2] Если удалённый элемент с таким именем не найдн
      //  if(empty($item)) {
      //
      //    // 2.1] Создать новый элемент
      //    $item = new \M1\Models\MD1_somemodel();
      //
      //    // 2.2] Наполнить $new данными
      //    $item->name                        = $this->data['name'];
      //    $item->description                 = $this->data['description'];
      //
      //    // 2.3] Сохранить $new в БД
      //    $item->save();
      //
      //  }
      //
      //  // 3] Если удалённый элемент найден
      //  if(!empty($item)) {
      //
      //    // 3.1] Восстановить его
      //    $item->restore();
      //
      //    // 3.2] Обновить некоторые свойства права
      //    $item->description                 = $data['description'];
      //
      //    // 3.3] Сохранить изменения
      //    $item->save();
      //
      //  }
      //
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д6. Удалить элемент //
    //---------------------//

      //// x. Удалить элемент
      //try {
      //
      //  // 1] Получить eloquent-модель элемента, который требуется удалить
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 2] Удалить элемент
      //  $item->delete();
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}




}

?>

