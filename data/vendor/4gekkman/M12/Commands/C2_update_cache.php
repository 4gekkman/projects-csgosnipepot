<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update FAQs cache
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        force         | (по умолчанию, == true) Обновлять кэш, даже если он присутствует
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
class C2_update_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Обновить кэш для FAQ, если он отсутствует, или если force == true
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------//
    // Update FAQs cache //
    //-------------------//
    $res = call_user_func(function() { try {

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "force"           => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

      // 3. Обновить кэш для FAQ, если он отсутствует, или если force == true

        // 3.1. Получить кэш со списком всех FAQов
        $faqs_list = json_decode(Cache::get('m12:faqs'), true);

        // 3.2. Обновить кэш
        // - Если он отсутствует, или если параметр force == true
        if(
          ((!Cache::has('m12:faqs') || empty($faqs_list) || count($faqs_list) == 0) ||
          $this->data['force'] == true)
        ) {

          // 3.2.1. Кэш со списком всех FAQов (ключ: "m12:faqs")
          call_user_func(function(){

            // 1] Получить список всех FAQов
            $faqs_list = \M12\Models\MD1_faqs::get()->pluck('name')->toArray();

            // 2] Записать $faqs_list в кэш
            Cache::put('m12:faqs', json_encode($faqs_list, JSON_UNESCAPED_UNICODE), 120);

          });

          // 3.2.2. Кэш всех групп FAQа (ключ: "m12:<faq>")
          // - Должен помечаться тегом m12:faq.
          call_user_func(function(){

            // 1] Получить коллекцию всех FAQов вместе с группами
            $faqs_list = \M12\Models\MD1_faqs::with(['groups'])->get();

            // 2] Пробежаться по всем $faqs_list
            foreach($faqs_list as &$faq) {

              // 2.1] Обработать группы $faq'а
              $faq->groups->map(function($group, $key){

                // 1) Раскодировать name из json в массив
                $group->name = json_decode($group->name, true);

                // 2) Раскодировать description из json в массив
                $group->description = json_decode($group->description, true);

                // n) Добавить доп.свойства
                $group->is_spinner_visible = false;

              });

              // 2.n] Добавить в кэш все группы, связанные с $faq
              Cache::tags(['m12:faq'])->put('m12:'.$faq['name'], json_encode($faq['groups'], JSON_UNESCAPED_UNICODE), 120);

            }

          });

          // 3.2.3. Кэш всех статей группы (ключ: "m12:<faq>/<group>")
          // - Должен помечаться тегом m12:faq.
          call_user_func(function(){

            // 1] Получить коллекцию всех FAQов вместе с группами и статьями
            $faqs_list = \M12\Models\MD1_faqs::with(['groups', 'groups.articles'])->get();

            // 2] Пробежаться по всем $faqs_list
            foreach($faqs_list as $faq) {

              // 2.1] Пробежаться по всем $faq['groups']
              foreach($faq['groups'] as $group) {

                // Обработать статьи $group'ы
                $group->articles->map(function($article, $key){

                  // 1) Раскодировать name из json в массив
                  $article->name = json_decode($article->name, true);

                  // 2) Раскодировать description из json в массив
                  $article->description = json_decode($article->description, true);

                  // 3) Раскодировать html из json в массив
                  $article->html = json_decode($article->html, true);

                  // 4) Раскодировать author из json в массив
                  $article->author = json_decode($article->author, true);

                  // n) Добавить доп.свойства
                  $article->is_expanded = false;

                });

                // Добавить в кэш все статьи, связанные с $group
                Cache::tags(['m12:faq'])->put('m12:'.$group['uri_group_relative'], json_encode($group['articles'], JSON_UNESCAPED_UNICODE), 120);

              }

            }

          });

        }

      //Log::info(json_decode(Cache::get('m12:faqs'), true));
      //Log::info(json_decode(Cache::tags(['m12:faq'])->get('m12:csgohap'), true));
      //Log::info(json_decode(Cache::tags(['m12:faq'])->get('m12:csgohap/cats'), true));

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C2_update_cache from M-package M12 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M12', 'C2_update_cache']);
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

