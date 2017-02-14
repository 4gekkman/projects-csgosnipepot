<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Parse FAQ data from its root folder to M12 database
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
 *  Стратегия парсинга
 *  ------------------
 *
 *    1. Парсинг FAQов
 *      - Парсим список доступных в корневой папке FAQов.
 *      - Получаем из БД доступный там список FAQов.
 *      - Синхронизируем данные в БД с данными из корневой папки:
 *        ▪ Удаляем из БД все FAQи, которых нет в корневой папке, вместе со связями из md1000.
 *        ▪ Если в БД уже есть такой FAQ, не трогаем его.
 *        ▪ Если в БД ещё нет такого FAQа, добавляем.
 *      - В конце делаем save().
 *
 *    2. Парсинг групп
 *      - Делаем цикл по всем доступным ныне FAQам.
 *        ▪ Получаем все доступные в FAQе папки.
 *        ▪ Ищем в каждой папке файл "_files/meta.info".
 *        ▪ Таким образом, наполняем массив доступных групп для данного FAQа.
 *        ▪ Синхронизируем данные в БД с полученными данными:
 *          ▪ Удаляем из БД все не найденные в данных группы, вместе со связями из md1000/md1001.
 *          ▪ Если в БД уже есть такая группа, обновляем её.
 *          ▪ Если в БД ещё нет такой группы, добавляем.
 *          ▪ Делаем save().
 *          ▪ Добавляем связи в md1000.
 *          ▪ Копируем аватар группы в public с заменой.
 *
 *    3. Парсинг статей и кодов стран
 *      - Делаем цикл по всем доступным ныне группам.
 *        ▪ Получаем все доступные в группе папки, кроме _files.
 *        ▪ Ищем в каждой папке файл "_files/meta.info".
 *        ▪ Таким образом, наполняем массив доступных статей для данной группы.
 *        ▪ Синхронизируем данные в БД с полученными данными:
 *          ▪ Удаляем из БД все не найденные в данных статьи, вместе со связями из md1001/md1002.
 *          ▪ Если в БД уже есть такая статья, обновляем её.
 *          ▪ Если в БД ещё нет такой статьи, добавляем.
 *          ▪ Делаем save().
 *          ▪ Если в БД ещё нет такого кода страны статьи, добавляем.
 *          ▪ Делаем save().
 *          ▪ Добавляем связи в md1001 и md1002.
 *          ▪ Копируем файлы статьи в public с заменой.
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M12\Commands;

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
class C1_parse_faq extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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

    // Принять входящие данные
    $this->data = $data;

    // Настроить Storage для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path('')]);
    $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());


  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Получить относ.путь к корн.каталогу
     *  2. Проверить, существует ли в ФС каталог по указанному пути     *
     *  3. Парсинг FAQов
     *  4. Парсинг групп
     *  5. Парсинг статей и кодов стран
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------//
    // Parse FAQ data from its root folder to M12 database //
    //-----------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить относ.путь к корн.каталогу, проверить его существование
      $root = config('M12.faq_root_folder');
      if(empty($root))
        throw new \Exception('В конфиге M12 не найден путь faq_root_folder к корневому каталогу относительно корня проекта.');

      // 2. Проверить, существует ли в ФС каталог по указанному пути
      $is_root_cat = file_exists(base_path($root));
      if(empty($is_root_cat))
        throw new \Exception('Не найден корневой каталог FAQ, путь к которому указан в конфиге M12, в faq_root_folder.');

      // 3. Парсинг FAQов
      call_user_func(function() USE ($root) {

        // 3.1. Получить все доступные в $root FAQи
        // - Формат: <имя FAQа> => <путь к FAQу относ.корня>
        $faqs_in_root = call_user_func(function() USE ($root) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Получить пути ко всем FAQам относ.корня
          $faq_paths = $this->storage->directories($root);

          // 3] Наполнить $results
          foreach($faq_paths as $path) {
            $results[preg_replace('#^.*\/#ui', '', $path)] = $path;
          }

          // n] Вернуть результат
          return $results;

        });

        // 3.2. Получить из БД доступный список FAQов
        $faqs_in_db = \M12\Models\MD1_faqs::get();
        $faqs_in_db_names = $faqs_in_db->pluck('name');

        // 3.3. Удалить из БД все FAQи, которых нет в $faqs_in_root
        if(!empty($faqs_in_db_names)) {
          foreach($faqs_in_root as $name => $path) {
            $faq = \M12\Models\MD1_faqs::where('name', $name)->first();
            if(!empty($faq)) {

              // 1] Отвязать $faq от всех групп
              if($faq->groups->contains($item))
                $user2detach->groups()->detach($item);

            }
          }
        }


        Log::info($faqs_in_root);



        /*
         *    1. Парсинг FAQов
         *      - Парсим список доступных в корневой папке FAQов.
         *      - Получаем из БД доступный там список FAQов.
         *      - Синхронизируем данные в БД с данными из корневой папки:
         *        ▪ Удаляем из БД все FAQи, которых нет в корневой папке, вместе со связями из md1000.
         *        ▪ Если в БД уже есть такой FAQ, не трогаем его.
         *        ▪ Если в БД ещё нет такого FAQа, добавляем.
         *      - В конце делаем save().
         *
         */
      });

      // 4. Парсинг групп
      call_user_func(function() USE ($root) {



      });


      // 5. Парсинг статей и кодов стран
      call_user_func(function() USE ($root) {



      });



      // Log::info($root);
      // Log::info($is_root_cat);


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_parse_faq from M-package M12 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M12', 'C1_parse_faq']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $errortext,
            "errormsg" => $e->getMessage()
          ]
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

