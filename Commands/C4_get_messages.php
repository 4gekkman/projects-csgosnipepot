<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get pointed number of messages from the specified room with the specified parameters
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        room_name     // Имя (уникальное) комнаты, из которой надо извлечь сообщения
 *        number        // Кол-во последних сообщений, которое надо извлечь (0=все)
 *        active_only   // Извлекать только сообщения не заблокированных/забаненых пользователей (==1)
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

  namespace M10\Commands;

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
class C4_get_messages extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих параметров
     *  2. Попробовать найти комнату с room_name
     *  3. Извлечь сообщения
     *  4. Обработать $messages
     *  5. Реверсировать $messages
     *  6. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------//
    // Извлечь сообщения из указанной комнаты указанным образом //
    //----------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "room_name"           => ["required", "string"],
        "number"              => ["required", "regex:/^[0-9]+$/ui"],
        "active_only"         => ["required", "regex:/^[01]{1}$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти комнату с room_name

        // 2.1. Попробовать найти
        $room = \M10\Models\MD1_rooms::where('name', $this->data['room_name'])->first();

        // 2.2. Если найти не удалось, провести синхронизацию комнат с конфигом, и попробовать снова
        if(empty($room)) {

          // 1] Провести синхронизацию
          $result = runcommand('\M10\Commands\C1_sync_rooms', []);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Попробовать найти $room повторно
          $room = \M10\Models\MD1_rooms::where('name', $this->data['room_name'])->first();

        }

        // 2.3. Если и после синхронизации пусто, возбудить исключение
        if(empty($room))
          throw new \Exception("Can't find room with name = ".$this->data['room_name']);

      // 3. Извлечь сообщения
      $messages = call_user_func(function(){

        // 3.1. Подготовить запрос
        $query = \M10\Models\MD2_messages::with('m5_users');

        // 3.2. Искать записи, связанные с комнатой room_name
        $query->whereHas('rooms', function($query){
          $query->where('name', $this->data['room_name']);
        });

        // 3.3. Если нужны только сообщения не забаненных и не заблокированных пользователей
        if($this->data['active_only']) {

          $query->whereHas('rooms', function($query) {
            $query->doesntHave('m5_users_md2002');
          })->whereHas('m5_users', function($query){
            $query->where('is_blocked',0);
          });

        }

        // 3.4. Исключить скрытые (hided) сообщения
        $query->where('hided', 0);

        // 3.5. Брать записи с конца
        $query->orderBy('created_at', 'desc');

        // 3.6. Если number > 0, брать лишь number записей
        if($this->data['number'] != 0) {
          $query->take($this->data['number']);
        }

        // 3.7. Получить и вернуть результаты
        return $query->get();

      });

      // 4. Обработать $messages
      // - Убрать из него поля m5_users и hided
      // - Добавить поля: avatar, level, steamname
      $messages = $messages->each(function($item, $key){

        // 1] Добавить поля: avatar, level, steamname
        $item->level      = '1';
        $item->steamname  = $item->m5_users[0]['nickname'];
        $item->avatar     = !empty($item->m5_users[0]['avatar_steam']) ? $item->m5_users[0]['avatar_steam'] : (!empty($item->m5_users[0]['avatar']) ? $item->m5_users[0]['avatar'] : 'http://placehold.it/34x34/ffffff');

        // 2] Удалить m5_users и hided
        unset($item->m5_users);
        unset($item->hided);

      });

      // 5. Реверсировать $messages
      $messages = $messages->reverse()->values();

      // 6. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "messages" => $messages
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C4_get_messages from M-package M10 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M10', 'C4_get_messages']);
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

