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
     *  1. Написать функцию для получени плоского стека из N-мерного массива
     *  2. Написать функцию для получения полного дерева всех bower-зависимостей
     *  3. Написать функцию для получения ветки дерева с указанным корнем
     *  4. Написать функцию для извлечения mains указанного bower-пакета из R5
     *  5. Составить индекс bower-зависимостей
     *
     *  6. Написать функцию для получения полного дерева всех DLW-зависимостей
     *  7. Написать функцию для извлечения mains указанного DLW-пакета
     *  8. Написать функцию для получения списка bower-зависимостей указанного DLW-пакета
     *  9. Написать функцию для проверки, находится ли bower-пакет X в ветке bower-пакета Y
     *  10. Составить индекс DLW-зависимостей
     *
     *  11. Сформировать итоговый css/js индекс для всех D-пакетов
     *  12. Сформировать и вернуть ответ с итоговым css/js индексом
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

                // 2.1.1] Если в $results нет такого $key
                if(!array_key_exists($key, $results))
                  $results[$key] = $level;

                // 2.1.2] Если в $results есть такой $key
                else {

                  // Если $level > $results[$key], заменить $level
                  if($level > $results[$key])
                    $results[$key] = $level;

                }

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

      // 2. Написать функцию для получения полного дерева всех bower-зависимостей
      // - Каждый узел должен содержать только лишь имя bower-зависимости
      $get_full_bower_tree = call_user_func(function(){

        // 2.1. Подготовить массив для результатов
        $results = [];

        // 2.2. Сформировать команду
        $cmd = "cd ".base_path()." && bower -j --allow-root list";

        // 2.3. Получить информацию в формате json
        $json = shell_exec('sshpass -p "password" ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no root@node "'.$cmd.'"');

        // 2.4. Написать рекурсивную функцию для формирования дерева
        $recursive = function($dependencies, &$dest) USE (&$recursive) {

          // Обойти все $dependencies
          foreach($dependencies as $key => $value) {

            // 1] Добавить все ключи из $dependencies в $dest
            // - Со значениями - пустыми массивами
            $dest[$key] = [];

            // 2] Для каждого ключа применить $recursive
            // - Но только если его dependencies не пустой массив
            if(count($value['dependencies']) != 0)
              $recursive($value['dependencies'], $dest[$key]);

          }

        };

        // 2.5. Сформировать дерево
        $recursive(json_decode($json, true)['dependencies'], $results);

        // 2.6. Вернуть результаты
        return $results;

      });

      // 3. Написать функцию для получения ветки дерева с указанным корнем
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

        // 4.2. Проверить существование файла mains.json в DLW-пакете $package
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

        // 4.6. Вернуть результаты
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
        $tree = $get_full_bower_tree;

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

      //  6. Написать функцию для получения полного дерева всех DLW-зависимостей
      // - Каждый узел должен содержать только лишь имя bower-зависимости
      $get_full_dlw_tree = function(){

        // 6.1. Подготовить массив для результатов
        $results = [];

        // 6.2. Получить коллекцию всех установленных D,L,W-пакетов,
        $packages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
          $query->whereIn('name',['D','L','W']);
        })->get();

        // 6.3. Написать рекурсивную функцию для формирования дерева
        $recursive = function($packages, &$dest) USE (&$recursive) {

          // Обойти все $dependencies
          foreach($packages as $package) {

            // 1] Получить коллекцию DLW-пакетов, от которой зависит $package
            $dependencies = $package->packages->filter(function($pack){
              return in_array($pack->packtypes->name, ['D','L','W']);
            });

            // 2] Добавить все id_inner из $packages в $dest
            // - Со значениями - пустыми массивами
            $dest[$package->id_inner] = [];

            // 3] Для каждого ключа применить $recursive
            // - Но только если его dependencies не пустой массив
            if(!empty($dependencies) != 0)
              $recursive($dependencies, $dest[$package->id_inner]);

          }

        };

        // 6.4. Сформировать дерево
        $recursive($packages, $results);

        // 6.5. Вернуть результаты
        return $results;

      };

      //  7. Написать функцию для извлечения mains указанного DLW-пакета
      $get_dlw_mains = function($packname){

        // 7.1. Подготовить массив для результатов
        $results = [];

        // 7.2. Проверить существование файла bower.json в DLW-пакете $package
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        if(!$this->storage->exists('bower.json'))
          throw new \Exception('Для DLW-пакета '.$packname.' не найден файл bower.json');

        // 7.3. Получить содержимое mains.json в формате php-массива
        $file = json_decode($this->storage->get('bower.json'), true);

        // 7.4. Если в массиве $file нет ключей css или js, возбудить исключение
        if(!array_key_exists('css', $file['mains']) || !array_key_exists('js', $file['mains']))
          throw new \Exception('В файле bower.json пакета '.$packname.' нет необходимых ключей "css" или "js"');

        // 7.5. Добавить в $results содержимое mains
        $results = $file['mains'];

        // 7.n. Вернуть результаты
        return $results;

      };

      //  8. Написать функцию для получения списка bower-зависимостей указанного DLW-пакета
      $get_dlw_bower_deps = function($packname) USE ($get_sub_tree, $get_flat_stack, $get_full_bower_tree) {

        // 8.1. Подготовить массив для результатов
        $results = [];

        // 8.2. Проверить существование файла bower.json в DLW-пакете $package
        config(['filesystems.default' => 'local']);
        config(['filesystems.disks.local.root' => base_path('vendor/4gekkman/'.$packname)]);
        $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
        if(!$this->storage->exists('bower.json'))
          throw new \Exception('Для DLW-пакета '.$packname.' не найден файл bower.json');

        // 8.3. Получить содержимое mains.json в формате php-массива
        $file = json_decode($this->storage->get('bower.json'), true);

        // 8.4. Если в массиве $file нет ключа dependencies, возбудить исключение
        if(!array_key_exists('dependencies', $file))
          throw new \Exception('В файле bower.json пакета '.$packname.' нет необходимого ключа "dependencies"');

        // 8.5. Получить содержимое dependencies
        $results = array_keys($file['dependencies']);

        // 8.6. Вернуть результаты
        return $results;

      };

      // 9. Написать функцию для проверки, находится ли bower-пакет X в ветке bower-пакета Y
      $check_bower_packs_deps = function($pack_x, $pack_y) USE ($get_full_bower_tree, $get_sub_tree, $get_flat_stack) {

        // 9.1. Получить полное дерево bower-зависимостей
        $tree = $get_full_bower_tree;

        // 9.1. Получить поддеревья для пакетов X/Y
        $subtree_x = $get_sub_tree($tree, $pack_x);
        $subtree_y = $get_sub_tree($tree, $pack_y);

        // 9.2. Получить плоские стеки поддеревьев X/Y
        $stack_x = $get_flat_stack($subtree_x);
        $stack_y = $get_flat_stack($subtree_y);

        // 9.3. Вернуть результат
        return in_array($pack_x, $stack_y);

      };

      // 10. Составить индекс DLW-зависимостей
      // - Для каждой зависимости д.б.: дерево (его ветка), плоский стек, mains и bowers
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
      //        ],
      //        "bowers" => [
      //          "animate.css",
      //          "headjs"
      //        ]
      //      ],
      //      "<pack2>" => [...]
      //    ]
      //
      $index_dlw = call_user_func(function() USE ($get_full_dlw_tree, $get_flat_stack, $get_sub_tree, $get_dlw_mains, $get_dlw_bower_deps) {

        // 10.1. Подготовить массив для результатов
        $results = [];

        // 10.2. Получить полное дерево всех dlw-зависимостей
        // - Каждый узел должен содрежать только имя bower-зависимости, и всё
        $tree = $get_full_dlw_tree();

        // 10.3. Получить полный плоский стек всех dlw-зависимостей
        $stack = $get_flat_stack($tree);

        // 10.4. Пробежаться по всему $stack
        foreach($stack as $dep) {

          // 1] Добавить пустой массив с ключём $dep в $results
          $results[$dep] = [];

          // 2] Добавить ключ tree и наполнить его
          $results[$dep]['tree'] = $get_sub_tree($tree, $dep);

          // 3] Добавить ключ stack и наполнить его
          $results[$dep]['stack'] = $get_flat_stack($results[$dep]['tree']);

          // 4] Добавить ключ mains и наполнить его
          $results[$dep]['mains'] = $get_dlw_mains($dep);

          // 5] Добавить ключ bowers и наполнить его
          $results[$dep]['bowers'] = $get_dlw_bower_deps($dep);

        }

        // 10.5. Вернуть результаты
        return $results;

      });

      // 11. Сформировать итоговый css/js индекс для всех D-пакетов
      // - Он содержит итоговые массивы css/js для вставки в blade-документы D-пакетов.
      // - В каком порядке пути в массивах идут, в таком и надо вставлять их в документ.
      // - Массив-индекс должен выглядеть примерно так:
      //
      //    [
      //      "<D-pack1>" => [
      //        "css"   => [
      //          "some/path/to/file.css",
      //          "some/path/to/file.css"
      //        ],
      //        "js"  => [
      //          "some/path/to/file.js",
      //          "some/path/to/file.js"
      //        ]
      //      ],
      //      "<D-pack2>" => [...]
      //    ]
      //
      $index_final = call_user_func(function() USE ($index_dlw, $check_bower_packs_deps, $get_full_bower_tree, $index_bower, $get_flat_stack) {

        // 11.1. Подготовить массив для результатов
        $results = [];

        // 11.2. Получить массив ID всех D-пакетов
        $dpacks = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
          $query->whereIn('name',['D']);
        })->pluck('id_inner');

        // 11.3. Получить итоговый индекс css/js путей для bower-зависимостей D-пакетов
        $index_dpacks_bower = call_user_func(function() USE ($index_dlw, $check_bower_packs_deps, $get_full_bower_tree, $index_bower, $get_flat_stack, $dpacks){

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Пробежаться по всем $dpacks
          foreach($dpacks as $dpack) {

            // 2.1] Извлечь stack этого D-пакета
            $stack = $index_dlw[$dpack]['stack'];

            // 2.2] Получить все bower-зависимости из $stack без повторов
            $allbowers = call_user_func(function() USE ($stack, $index_dlw, $get_full_bower_tree) {

              // 1) Подготовить массив для результата
              $results = [];

              // 2) Пробежаться по всем $stack
              foreach($stack as $dep) {

                // 2.1) Извлечь bowers для $dep
                $bowers = $index_dlw[$dep]['bowers'];

                // 2.2) Добавить в $results те $bowers, которых там ещё нет
                foreach($bowers as $bower) {
                  if(!in_array($bower, $results))
                    array_push($results, $bower);
                }

              }

              // n) Вернуть результат
              return $results;

            });

            // 2.3] Получить массив лишь "независимых" bowers
            // - Каждое значение проверить на независимость с помощью $check_bower_packs_deps
            $independent_bowers = collect($allbowers)->filter(function($value) USE ($allbowers, $check_bower_packs_deps, $get_full_bower_tree) {

              // 2.3.1] Произвести проверку на независимость
              // - Но только, если $bower !== $value
              foreach($allbowers as $bower)
                if($bower !== $value && $check_bower_packs_deps($value, $bower)) return false;

              // 2.3.2] Вернуть true (значение независимо, если курсор дошёл сюда)
              return true;

            })->toArray();

            // 2.4] Сформировать из $independent_bowers дерево
            $independent_tree = call_user_func(function() USE ($independent_bowers, $index_bower){

              // 1) Подготовить массив для результата
              $results = [];

              // 2) Пробежаться по $independent_bowers
              foreach($independent_bowers as $ib) {
                $results[$ib] = $index_bower[$ib]['tree'][$ib];
              }

              // n) Вернуть результат
              return $results;

            });

            // 2.5] Получить плоский стек из $independent_bowers
            $independent_stack = $get_flat_stack($independent_tree);

            // 2.6] Сформировать итоговые css/js списки для D-пакета dpack
            // - И добавить эту информации в $results
            $results[$dpack] = call_user_func(function() USE ($independent_stack, $index_bower) {

              // 1) Подготовить массив для результата
              $results = [
                "css" => [],
                "js"  => []
              ];

              // 2) Пробежаться по всем пакетам в $independent_stack
              foreach($independent_stack as $bower) {

                // 2.1) Добавить все css-ки в $results['css'] из $index_bower
                foreach($index_bower[$bower]['mains']['css'] as $css) {
                  if(!empty($css))
                    array_push($results['css'], $css);
                }

                // 2.2) Добавить все js-ки в $results['js'] из $index_bower
                foreach($index_bower[$bower]['mains']['js'] as $js) {
                  if(!empty($js))
                    array_push($results['js'], $js);
                }

              }

              // 3) Вернуть результат
              return $results;

            });

          }

          // 3] Вернуть результаты
          return $results;

        });

        // 11.4. Получить итоговый индекс css/js путей для DLW-зависимостей D-пакетов
        $index_dpacks_dlw = call_user_func(function() USE ($index_dlw, $check_bower_packs_deps, $get_full_bower_tree, $index_bower, $get_flat_stack, $dpacks){

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Пробежаться по всем $dpacks
          // - И сформировать итоговые css/js списки для D-пакета dpack
          // - И добавить эту информации в $results
          foreach($dpacks as $dpack) {
            $results[$dpack] = call_user_func(function() USE ($index_dlw, $dpack) {

              // 1) Подготовить массив для результата
              $results = [
                "css" => [],
                "js"  => []
              ];

              // 2) Получить плоский стек DLW-зависимостей $dpack
              $stack = $index_dlw[$dpack]['stack'];

              // 3) Обойти весь $stack с начала до конца
              foreach($stack as $dep) {

                // 3.1) Добавить все css-ки в $results['css'] из $index_bower
                foreach($index_dlw[$dep]['mains']['css'] as $css) {
                  if(!empty($css))
                    array_push($results['css'], $css);
                }

                // 3.2) Добавить все js-ки в $results['js'] из $index_bower
                foreach($index_dlw[$dep]['mains']['js'] as $js) {
                  if(!empty($js))
                    array_push($results['js'], $js);
                }

              }

              // n) Вернуть результат
              return $results;

            });
          }

          // 3] Вернуть результат
          return $results;

        });

        // 11.5. Объединить итоговые bower- и dlw-индексы в единый индекс
        $index_dpacks_dlw = call_user_func(function() USE ($index_dpacks_bower, $index_dpacks_dlw){

          // 1] Подготовить массив для результата
          $results = [];

          // 2] Получить массив ID всех D-пакетов
          $dpacks = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->whereIn('name',['D']);
          })->pluck('id_inner');

          // 3] Сформировать единый индекс
          foreach($dpacks as $dpack) {

            // 3.1. Подготовить массивы для результатов
            $results[$dpack] = [
              "css" => [],
              "js"  => []
            ];

            // 3.2. Сначала добавить данные из $index_dpacks_bower

              // 3.2.1. Добавить css
              foreach($index_dpacks_bower[$dpack]['css'] as $css)
                array_push($results[$dpack]['css'], $css);

              // 3.2.2. Добавить js
              foreach($index_dpacks_bower[$dpack]['js'] as $js)
                array_push($results[$dpack]['js'], $js);

            // 3.3. Теперь добавить данные из $index_dpacks_dlw

              // 3.3.1. Добавить css
              foreach($index_dpacks_dlw[$dpack]['css'] as $css)
                array_push($results[$dpack]['css'], $css);

              // 3.3.2. Добавить js
              foreach($index_dpacks_dlw[$dpack]['js'] as $js)
                array_push($results[$dpack]['js'], $js);

          }

          // 4] Вернуть результат
          return $results;

        });

        // 11.6. Вернуть результаты
        $results = $index_dpacks_dlw;
        return $results;

      });

      // 12. Сформировать и вернуть ответ с итоговым css/js индексом
      return [
        "status"  => 0,
        "data"    => [
          "index_final" => $index_final,
          "index_dlw"   => $index_dlw
        ]
      ];

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

