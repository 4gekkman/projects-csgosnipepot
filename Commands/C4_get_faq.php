<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get FAQ data
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        faq         | [Обязательно] Получить данные по всем группам указанного FAQа
 *        group       | [Не обязательно] Получить данные по всем статьям указанной группы фака faq
 *        group_mode  | [Не обязательно] Срабатывает, только если group не передан
 *                                       - 1: в качестве group берётся первая группа FAQа faq из кэша, если он пуст, то ""
 *                                       - 2: в качестве group берётся указанная в конфиге группа, её нет в кэше, то ""
 *        what2return | [Не обязательно] Какие данные возвращать клиенту
 *                                       - 1: [по умолчанию] возвращать всё
 *                                       - 2: возвращать только данные по группам
 *                                       - 3: возвращать только данные по статьям
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
class C4_get_faq extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Если это необходимо, обновить данные FAQа
     *  3. Назначить значения по умолчанию
     *  4. Получить кэш с данными по всем группам фака faq
     *  5. Получить кэш с данными по всем статьям группы group фака faq
     *  n. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------//
    // Get FAQ data //
    //--------------//
    $res = call_user_func(function() { try {

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "faq"           => ["required", "string"],
        "group"         => ["string"],
        "group_mode"    => ["regex:/^[12]{1}$/ui"],
        "what2return"   => ["regex:/^[123]{1}$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Если это необходимо, обновить данные FAQа
      $result = runcommand('\M12\Commands\C3_update_faq', [
        'force' => false
      ]);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 3. Назначить значения по умолчанию

        // 3.1. Если group отсутствует, назначить ""
        if(!array_key_exists('group', $this->data))
          $this->data['group'] = "";

        // 3.2. Если group_mode не пуст
        if(array_key_exists('group_mode', $this->data)) {

          // 1] Если group_mode == 1
          if($this->data['group_mode'] == 1) {

            // 1.1] Получить кэш с данными по всем группам фака faq
            $groups_data = json_decode(Cache::tags(['m12:faq'])->get("m12:".$this->data['faq']), true);

            // 1.2] Если $groups_data не пуст, записать первую группу
            if(!empty($groups_data) && count($groups_data) > 0)
              $this->data['group'] = $groups_data[0]['name_folder'];

          }

          // 2] Если group_mode == 2
          if($this->data['group_mode'] == 2) {

            // 2.1] Получить из конфига имя стартовой группы
            $start_group_name4faq = config('M12.start_group_names.'.$this->data['faq']);

            // 2.2] Получить кэш с данными по всем группам фака faq
            $groups_data = json_decode(Cache::tags(['m12:faq'])->get("m12:".$this->data['faq']), true);

            // 2.3] Получить массив name_folder из $groups_data
            $groups_data_name_folders = collect($groups_data)->pluck('name_folder')->toArray();

            // 2.4] Если $start_group_name4faq не пуст, и есть в $groups_data_name_folders, записать его в group
            if(!empty($start_group_name4faq) && in_array($start_group_name4faq, $groups_data_name_folders))
              $this->data['group'] = $start_group_name4faq;

          }

        }

        // 3.3. Если what2return пуста
        if(!array_key_exists('what2return', $this->data))
          $this->data['what2return'] = 1;

      // 4. Получить кэш с данными по всем группам фака faq
      if($this->data['what2return'] != 3)
        $groups_data = json_decode(Cache::tags(['m12:faq'])->get("m12:".$this->data['faq']), true);
      else
        $groups_data = [];

      // 5. Получить кэш с данными по всем статьям группы group фака faq
      if($this->data['what2return'] != 2)
        $articles_data = json_decode(Cache::tags(['m12:faq'])->get("m12:".$this->data['faq'].'/'.$this->data['group']), true);
      else
        $articles_data = [];

      // n. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          'faq'       => $this->data['faq'],
          'group'     => $this->data['group'],
          'groups'    => $groups_data,
          'articles'  => $articles_data
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C4_get_faq from M-package M12 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M12', 'C4_get_faq']);
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

