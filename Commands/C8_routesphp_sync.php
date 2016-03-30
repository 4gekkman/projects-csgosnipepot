<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Syncronyze route registrations in routes.php of M4 beetween special marks.
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
 *  Принципы формирования итоговой строки с роутами
 *  -----------------------------------------------
 *
 *    • Что происходит при определении 2-х роутов с одинаковым URI
 *      - Определённый ниже по тексту в routes.php роут имеет приоритет.
 *      - А определённый выше по тексту роут "затирается" и не срабатывает.
 *      - То есть в системе роутинга laravel, uri роута - это его Unique ID.
 *      - Например, при запросе "http://site.ru/ivan" мы получим '222' в этой ситуации:
 *
 *          Route::get('/ivan/{p2?}', function () {return '111';});
 *          Route::get('/ivan/{p2?}', function () {return '222';});
 *
 *    • Роут с бОльшим кол-вом параметров "перекрывает" роут с мЕньшим
 *      - Но они не перезаписывают друг друга, и существуют одновременно.
 *      - Пример №1, ответ '222' мы не получим ни при каком URI:
 *
 *          Route::get('/ivan/{p2?}/{p3?}', function () {return '111';});
 *          Route::get('/ivan/{p2?}', function () {return '222';});
 *
 *      - Пример №2, получим '222' при '/ivan/petrov', и '111' при '/ivan/petrov/sidorov/...'
 *
 *          Route::get('/ivan/{p2?}', function () {return '222';});
 *          Route::get('/ivan/{p2?}/{p3?}', function () {return '111';});
 *
 *    • Роуты с менее/более точным URI не имеют приоритета друг над другом:
 *      - Пример №1, получим '111' при запросе '/ivan/petrov':
 *
 *          Route::get('/ivan/{p2?}/{p3?}', function () { return '111'; });
 *          Route::get('/ivan/petrov/{p3?}', function () { return '222'; });
 *
 *      - Пример №2, получим '222' при запросе '/ivan/petrov':
 *
 *          Route::get('/ivan/petrov/{p3?}', function () { return '222'; });
 *          Route::get('/ivan/{p2?}/{p3?}', function () { return '111'; });
 *
 *    • Роуты с мЕньшим кол-вом сегментов должны идти выше роутов с бОльшим
 *      - Потому что роуты с бОльшим кол-вом параметров-сегментов перекрывают роуты с мЕньшим.
 *      - Причём, для ручных и авто роутов это привило применяется отдельно.
 *      - Т.Е. сначала всё равно идут все ручные, а лишь потом все автоматические.
 *
 *    • Среди роутов с одинаковым кол-вом сегментов, раньше должны идти более точные
 *      - Поскольку они перекрывают друг-друга, но при этом пользователь скорее
 *        всего имеет в виду всё же более точный роут.
 *      - Причём, для ручных и авто роутов это привило применяется отдельно.
 *      - Т.Е. сначала всё равно идут все ручные, а лишь потом все автоматические.
 * 
 *    • Что делать при одноврем-ом появлении ручного/авто роутов с одинаковыми URI
 *      - Это касается лишь роутов с 100% ТОЧНЫМ URI !
 *      - Надо действовать ещё до формирования итоговой строки с роутами.
 *      - Надо просто из списка авто.роутов удалить этот роут.
 *      - Нельзя допускать одновременного попадания этих роутов в итоговую строку.
 *      - Ведь ручные роуты в этой строке будут левее/выше автоматических.
 *      - Потому что они должны срабатывать раньше, и иметь приоритет.
 *      - Но система роутинга laravel так устроена, что затрёт ручной роут (это ошибка).
 *
 *    • Имена параметров д.б. разные у ручных/авто роутов
 *      - Для ручных надо использовать имя: mp<номер>
 *      - Для автоматических надо использовать имя: ap<номер>
 *
 *    • Пример правильной итоговой строки с роутами (сначала ручные, потом авто)
 *      - Со следующими отличиями:
 *        • В реале кол-во параметров каждого роута д.б. равно 50.
 *        • В реале вместо callback идут ссылки на методы getIndex и postIndex контроллеров.
 *        • В реале роуты идут парами: Route::get + Route::post.
 *      - Итак:
 *
 *        Route::get('/ivan', function () { return 'man1'; });                    // man1
 *        Route::get('/{mp1?}', function () { return 'man2'; });                  // man2
 *        Route::get('/ivan/petrov', function () { return 'man3'; });             // man3
 *        Route::get('/ivan/{mp2?}', function () { return 'man4'; });             // man4
 *        Route::get('/ivan/petrov/sidorov', function () { return 'man5'; });     // man5
 *        Route::get('/ivan/petrov/{mp3?}', function () { return 'man6'; });      // man6
 *        Route::get('/ivan/{mp2?}/{mp3?}', function () { return 'man7'; });      // man7
 *
 *        Route::get('/nikola', function () { return 'auto1'; });                 // auto1
 *        Route::get('/{ap1?}', function () { return 'auto2'; });                 // man2
 *        Route::get('/nikola/ololoev', function () { return 'auto3'; });         // auto3
 *        Route::get('/nikola/{ap2?}', function () { return 'auto4'; });          // auto4
 *        Route::get('/nikola/ololoev/savelov', function () { return 'auto5'; }); // auto5
 *        Route::get('/nikola/ololoev/{ap3?}', function () { return 'auto6'; });  // auto6
 *        Route::get('/nikola/{ap2?}/{ap3?}', function () { return 'auto7'; });   // auto7
 *
 *        // Route::get('/ivan/petrov', function () { return 'auto8'; });     // точный авто.роут, URI которого совпадает с URI уже существующего ручного роута
 *                                                                            // - д.б. исключён из списка авто.роутов ещё до формирования итоговой строки роутов
 *        Route::get('/ivan/petrov/{ap3?}', function () { return 'man9'; });  // не точный авто.роут, URI которого совпадает с URI уже существующего ручного роута
 *                                                                            // - его можно оставить, ничего с ним не делать
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M4\Commands;

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
class C8_routesphp_sync extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Составить список вкл. ручных роутов
     *  2. Составить список вкл. автоматических роутов
     *  3. Подготовить итоговую строку для вставки в routes.php в M4
     *  4. Выполнить синхронизацию регистраций роутов в routes.php в M4
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------//
    // Произвести синхронизацию регистраций роутов в routes.php в M4 //
    //---------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Составить список вкл. ручных роутов
      $listof_manual_routes = call_user_func(function(){

        // 1.1. Подготовить массив для результатов
        $results = [];

        // 1.2. Получить все вкл. роуты из m4.md1_routes
        $routes = \M4\Models\MD1_routes::where('ison', 1)->whereHas('types', function($query){
          $query->where('name', '=', 'manual');
        })->get();

        return $routes;


        // \Route::get("/s1/s2/{s3?}/{s4?}/{s5?}", "\\D1\\Controller@getIndex");





        // 1.n. Вернуть массив с результатами
        return $results;

      });

      write2log($listof_manual_routes, []);

      // 2. Составить список вкл. автоматических роутов
      $listof_auto_routes = call_user_func(function(){

      });

      // 3. Подготовить итоговую строку для вставки в routes.php в M4
      $prepeared_routes_str = call_user_func(function(){

      });

      // 4. Выполнить синхронизацию регистраций роутов в routes.php в M4

        // 4.1.



      // План работ, часть 1: составляем список включенных ручных роутов
      // - Готовим массив для результатов
      // - Получаем все включённые ручные роуты из m4.md1_routes
      // - На каждый роут добавляем в массив 2-ва, для getIndex и postIndex, например:
      //
      //  \Route::get("/d1", "\\D1\\Controller@getIndex");
      //  \Route::get("/d1", "\\D1\\Controller@postIndex");

      // План работ, часть 2: составляем список включенных авто.роутов
      // - Получаем все включённые авто роуты из m4.md1_routes
      // -

      // План работ, часть 3: вставляем ручные, затем авто.роуты в routes.php в M4
      // -


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C8_routesphp_sync from M-package M4 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M4', 'C8_routesphp_sync']);
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

