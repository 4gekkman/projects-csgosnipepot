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

      // 1. Составить индекс bower-зависимостей
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
      //      ]
      //    ]
      //
      $index_bower = call_user_func(function(){

        // 1.1.

        // 1.1. Получить информацию обо всём дереве bower-зависимостей
        $bowerdeps = call_user_func(function(){

          // 1] Сформировать команду
          $cmd = "cd ".base_path()." && bower -j --allow-root list";

          // 2] Получить информацию в формате json
          $json = shell_exec('sshpass -p "password" ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no root@node "'.$cmd.'"');

          // 3] Вернуть зависимости
          return json_decode($json, true)['dependencies'];

        });

        // 1.2. Составить полное дерево bower-зависимостей



      });








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

