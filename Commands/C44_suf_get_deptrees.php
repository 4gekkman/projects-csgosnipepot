<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - This command forms dependencies trees for suf_blade_integrate and suf_watch_setting
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *
 *      ]
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
 *      - Текст ошибки. Может заменяться на "" в контроллерах (чтобы скрыть от клиента).
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

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
class C44_suf_get_deptrees extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Составить индекс bower-зависимостей с дерево, плоским стеком и mains
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------------------------------------------------//
    // Сформировать деревья и плоские стеки зависимостей для suf_blade_integrate и suf_watch_setting //
    //-----------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Написать функцию для получени плоского стека из N-мерного массива
      $get_flat_stack = function($arr) {

        // 1.1. Получить плоский стек, и одновременно индекс глубины
        // - Ключами служат элементы стека, а значениями - показатели грубины.
        // - Самый неглубокий уровень - 1. Чем глубже, бем больше цифра.
        $stack = call_user_func(function() USE ($arr) {

          // 1] Подготовить массив для результата
          $results = [];

          // 2] Подготовить рекурсивную функцию для обхода $arr
          $recursive = function($subarr, &$results, $level) USE (&$recursive) {
            foreach($subarr as $key => $value) {

              // 2.1] Добавить все $keys в $results со значением $level
              $results[$key] = $level;

              // 2.2. Если $value это не пустой массив, запустить $recursive для него
              if(count($value) != 0)
                $recursive($value, $results, +$level+1);

            }
          };

          // 3] Сформировать плоский стек
          $recursive($arr, $results, 1);

          // n] Вернуть результат
          return $results;

        });

        // 1.2. Отсортировать $stack по убыванию по индексу глубины
        uasort($stack, function($a,$b){
          return gmp_cmp($b,$a);
        });

        // 1.3. Получить из стека индексированный массив со значеняими - бывшими ключами
        $stack = array_keys($stack);

        // 1.4. Вернуть результаты
        return $stack;

      };

      // 2. Получить полное дерево всех bower-зависимостей
      // - Каждый узел должен содрежать только имя bower-зависимости, и всё
      $get_full_bower_tree = function(){

        // 1] Подготовить массив для результатов
        $results = [];

        // 2] Сформировать команду
        $cmd = "cd ".base_path()." && bower -j --allow-root list";

        // 3] Получить информацию в формате json
        $json = shell_exec('sshpass -p "password" ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no root@node "'.$cmd.'"');

        // 4] Написать рекурсивную функцию для формирования дерева
        $recursive = function($dependencies, &$dest) USE (&$recursive) {

          // Обойти все $dependencies
          foreach($dependencies as $key => $value) {

            // 4.1. Добавить все ключи из $dependencies в $dest
            // - Со значениями - пустыми массивами
            $dest[$key] = [];

            // 4.2. Для каждого ключа применить $recursive
            // - Но только если его dependencies не пустой массив
            if(count($value['dependencies']) != 0)
              $recursive($value['dependencies'], $dest[$key]);

          }

        };

        // 5] Сформировать дерево
        $recursive(json_decode($json, true)['dependencies'], $results);

        // 6] Вернуть результаты
        return $results;

      };

      // 3. Написать функцию для полечения ветки дерева с указанным корнем
      // - Она ищет в дереве узел с указанным значением.
      // - И возвращает поддерево (ветку) этого дерева с корнем в этом узле.
      // - Если ничего не находит, возвращает пустой массив.
      $get_sub_tree = function($tree, $node) {

        // 3.1. Подготовить массив для результатов
        $results = [];

        // 3.2. Написать рекурсивную функцию для поиска ветки
        $recursive = function($tree, $node, &$results) USE (&$recursive) {

          // Обойти все $dependencies
          foreach($tree as $key => $value) {

            // 1] Если $key == $node, добавить $value в $results и завершить
            if($key === $node) {
              $results[$key] = $value;
              break;
            }

            // 2] Если нет, и $value не пустой массив, выполнить $recursive для него
            else {
              if(count($value) != 0)
                $recursive($value, $node, $results);
            }

          }

        };

        // 3.3. Сформировать дерево
        $recursive($tree, $node, $results);

        // 3.4. Вернуть результаты
        return $results;

      };

      // 4. Написать функцию для извлечения mains указанного bower-пакета из R5
      $get_bower_mains_from_R5 = function($packname){

        // 4.1. Подготовить массив для результатов
        $results = [];

        // 4.2. Проверить существование файла bower.json в DLW-пакете $package
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/R5/data4bower/'.$packname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        if(!$this->storage->exists('mains.json'))
          throw new \Exception('Для bower-пакета '.$packname.' не найден файл mains.json в R5');

        // 4.3. Получить содержимое mains.json в формате php-массива
        $file = json_decode($this->storage->get('mains.json'), true);

        // 4.4. Если в массиве $file нет ключей css или js, возбудить исключение
        if(!array_key_exists('css', $file['mains']) || !array_key_exists('js', $file['mains']))
          throw new \Exception('В файле mains.json пакета '.$packname.' в R5 нет необходимых ключей "css" или "js"');

        // 4.5. Добавить в $results содержимое mains
        $results = $file['mains'];

        // 4.n. Вернуть результаты
        return $results;

      };

      // 5. Составить индекс bower-зависимостей
      // - Для каждой зависимости д.б.: дерево (его ветка), плоский стек и mains
      // - Массив-индекс должен выглядеть примерно так:
      //
      //    [
      //      "<pack1>" => [
      //        "tree"   => [
      //          "<pack1>" => [
      //            "<pack2>" => [
      //              "<pack3>" => [],
      //              "<pack4>" => [],
      //            ],
      //            "<pack5>" => []
      //          ]
      //        ],
      //        "stack"  => [
      //          "<pack4>",
      //          "<pack3>",
      //          "<pack2>",
      //          "<pack5>",
      //          "<pack1>"
      //        ]
      //        "mains"  => [
      //          "css"  => [
      //            "<path1>",
      //            "<path2>"
      //          ],
      //          "js"   => [
      //            "<path3>",
      //            "<path4>"
      //          ]
      //        ]
      //      ],
      //      "<pack2>" => [...]
      //    ]
      //
      $index_bower = call_user_func(function() USE ($get_flat_stack, $get_full_bower_tree, $get_sub_tree, $get_bower_mains_from_R5) {

        // 5.1. Подготовить массив для результатов
        $results = [];

        // 5.2. Получить полное дерево всех bower-зависимостей
        // - Каждый узел должен содрежать только имя bower-зависимости, и всё
        $tree = $get_full_bower_tree();

        // 5.3. Получить полный плоский стек всех bower-зависимостей
        $stack = $get_flat_stack($tree);

        // 5.4. Пробежаться по всему $stack
        foreach($stack as $dep) {

          // 1] Добавить пустой массив с ключём $dep в $results
          $results[$dep] = [];

          // 2] Добавить ключ tree и наполнить его
          $results[$dep]['tree'] = $get_sub_tree($tree, $dep);

          // 3] Добавить ключ stack и наполнить его
          $results[$dep]['stack'] = $get_flat_stack($results[$dep]['tree']);

          // 4] Добавить ключ mains и наполнить его
          $results[$dep]['mains'] = $get_bower_mains_from_R5($dep);

        }

        // 5.5. Вернуть результаты
        return $results;

      });

      write2log($index_bower, []);






      // ------------ bower ------------

      // --- Как составить дерево bower-зависимостей ---
      // - Код для получения json с информацией обо всём дереве:
      //    $cmd = "cd ".base_path()." && bower -j --allow-root list";
      //    $json = shell_exec('sshpass -p "password" ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no root@node "'.$cmd.'"');
      // - Надо пробежатсья по всем зависимостям рекурсивно, смотреть dependencies.
      // - В итоге: массив-дерево имён bower-пакетов.

      // --- Как получить плоский стек bower-зависимостей ---
      // - Каждой зависимости присваиваем индекс глубины (начинается с 1, и чем глубже, тем выше).
      // - Зависимостс с бОльшим индексом подключаются раньше зависимостей с мЕньшим.
      // - Зависимости на одном уровне могут подключатсья в произвольном порядке.
      // - Все дубли сливаются в один с наибольшим среди них индексом.

      // --- Где брать paths для css и js для каждой из bower-зависимостей
      // - Для bower-пакета <name> искать их в R5/data4bower/<name>/mains.json
      // - В mains -> css лежит массив с путями (в нужном порядке) к css-файлам пакета.
      // - В mains -> js лежит массив с путями (в нужном порядке) к js-файлам пакета.

      // --- Составление индекса с деревом, плоским стеком и mains
      // - Необходимо создать следующий массив-индекс.
      // - Ключ: имя каждого bower-пакета из дерева bower-зависимостей.
      // - Значение: массив с 3-мя значениями "tree"/ "stack"/ "mains".
      //     tree: содержит ветку с корнем в этом bower-пакете.
      //     stack: содержит плоский стек ветки tree.
      //     mains: содержит 2 массива css/js с путями к css/js файлам пакета.


      // ------------ dlw ------------

      // --- Как составить дерево DLW-зависимостей
      // - С помощью базы данных M1.

      // --- Как получить плоский стек DLW-зависимостей
      // - Точно также, как для bower-зависимостей.

      // --- Где брать paths для css и js для каждой из DLW-зависимостей
      // - Для DLW-пакета <name> искать их в vendor/4gekkman/<name>/bower.json
      // - В mains -> css лежит массив с путями (в нужном порядке) к css-файлам пакета.
      // - В mains -> js лежит массив с путями (в нужном порядке) к js-файлам пакета.

      // --- Составление индекса с деревом, плоским стеком и mains
      // - Необходимо создать следующий массив-индекс.
      // - Ключ: имя каждого dlw-пакета из дерева dlw-зависимостей.
      // - Значение: массив с 4-мя значениями "tree"/ "stack"/ "mains".
      //     tree: содержит ветку с корнем в этом dlw-пакете.
      //     stack: содержит плоский стек ветки tree.
      //     mains: содержит 2 массива css/js с путями к cs/css файлам пакета.
      //     bowers: содержит список bower-зависимостей этого dlw-пакета




      // ------------ dlw + bower ------------

      // --- Каков будет взаимный порядок подключения к blade dlw/bower пакетов
      // - Сначала будет подключаться полностью весь стек bower-пакетов.
      // - А затем будет подключаться полностью весь стек dlw-пакетов.

      // --- Как будет получаться итоговый стек bower-пакетов
      // - Будет получен полный список без повторов bower-пакетов, от которых
      //   зависит указанное дерево DLW-пакетов.
      // - Нам понадобится функция, проверяющая, состоит ли указанный bower-пакет
      //   в дереве другого указанного bower-пакета.
      // - С помощью этой функции будет проверен каждый bower-пакет-зависимость
      //   дерева DLW-пакетов, относительно каждого из этого же дерева.
      // - Будет найден список bower-пакетов, которые не состоят ни в одном из
      //   деревьев других bower-пакетов, то есть являются в данной конфигурации
      //   корнями своих собственных деревьев.
      // - Далее с помощью ранее составленного индекса, для каждого bower-пакета,
      //   являющегося корнем, мы получим плоский стек bower-зависимостей.
      // - И добавляем эти плоские стеки от разных корней в итоговый плоский стек
      //   в любом порядке.

      // --- Как будет получаться итоговый стек DLW-пакетов
      // - С помощью ранее составленного индекса берём и получаем плоский стек
      //   DLW-пакетов для нужного D-пакета. Всё.



    } catch(\Exception $e) {
        $errortext = 'Invoking of command C44_suf_get_deptrees from M-package M1 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M1', 'C44_suf_get_deptrees']);
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

