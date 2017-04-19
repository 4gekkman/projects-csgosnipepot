<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Add specified message to the specified room
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_room       // ID комнаты, в которую добавить сообщение
 *        message       // Сообщение, которое требуется добавить
 *        from_who_id   // От кого сообщение (ID пользователя в БД M5)
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
 *  Если from_who = 0
 *  -----------------
 *    - То сообщение записывается от имени текущего аутентифицированного пользователя.
 *
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
class C2_add_message_to_the_room extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  2. Попробовать найти комнату с id_room
     *  3. Получить модель пользователя, от имени которого надо запостить сообщение
     *  4. Если пользователь $user заблокирован, возбудить исключение
     *  5. Если пользователь $user забанен, возбудить исключение
     *  6. Если в $room запрещено публиковать гостям, а $user гость, возбудить исключение
     *  7. Если размер сообщения превышен, возбудить исключение
     *  8. Записать сообщение в базу данных
     *  9. Связать $new_message с $room
     *  10. Связать $new_message с $user
     *  11. Транслировать сообщение всем клиентам-подписчикам
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------------------------------------------------------------------//
    // Добавить сообщение в комнату от имени указанного пользователя (по умолчанию, от имени аутентиф.пользователя) //
    //--------------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_room"         => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "message"         => ["required", "string"],
        "from_who_id"     => ["required", "regex:/^[0-9]+$/ui"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти комнату с id_room
      $room = \M10\Models\MD1_rooms::with(['bans','bans.m5_users'])
          ->where('id', $this->data['id_room'])
          ->first();
      if(empty($room))
        throw new \Exception("Can't find the room with ID = ".$this->data['id_room']);

      // 3. Получить модель пользователя, от имени которого надо запостить сообщение
      $user = call_user_func(function(){

        // 3.1. Если from_who_id == 0
        // - Т.Е. публиковать пост надо от имени текущего аутентиф.пользователя.
        if($this->data['from_who_id'] == 0) {

          // 1] Получить из сессии аутентификационную информацию о пользователе
          $authdata = session('auth_cache');

          // 2] Извлечь из неё информацию об ID пользователя
          $id_user = json_decode($authdata, true)['user']['id'];

          // 3] Найти пользователя с $id_user
          $user = \M5\Models\MD1_users::find($id_user);
          if(empty($user))
            throw new \Exception("Can't find user with ID = ".$id_user);

          // 4] Вернуть $user
          return $user;

        }

        // 3.2. Если from_who_id != 0
        // - Т.Е. публиковать пост надо от имени пользователя с указанным ID.
        else {

          // 1] Найти пользователя с from_who_id
          $user = \M5\Models\MD1_users::find($this->data['from_who_id']);
          if(empty($user))
            throw new \Exception("Can't find user with ID = ".$this->data['from_who_id']);

          // 2] Вернуть $user
          return $user;

        }

      });

      // 4. Если пользователь $user заблокирован, возбудить исключение
      if($user->is_blocked != 0)
        throw new \Exception("The user with ID = ".$this->data['from_who_id'].' is blocked.');

      // 5. Если пользователь $user забанен, возбудить исключение
      foreach($room['bans'] as $ban) {
        foreach($ban['m5_users'] as $banned_user) {
          if($banned_user['id'] == $user['id'] && \Carbon\Carbon::now()->lte(\Carbon\Carbon::parse($ban['will_be_ended_at'])))
            throw new \Exception("Вы были забанены в чате до ".$ban['will_be_ended_at']." UTC. <br><br><b>Причина бана</b><br>".$ban['reason']);
        }
      }

      // 6. Если в $room запрещено публиковать гостям, а $user гость, возбудить исключение
      if($room->allow_guests != '1') {
        if($user->isanonymous == '1') {
          throw new \Exception("The user with ID = ".$this->data['from_who_id'].' is a guest, them not allowed in that room.');
        }
      }

      // 7. Если размер сообщения превышен, возбудить исключение
      if(mb_strlen($this->data['message']) > $room->max_msg_length)
        throw new \Exception("The message is too long.");

      // 8. Записать сообщение в базу данных

        // 8.1. Создать новое сообщение
        $new_message = new \M10\Models\MD2_messages();

        // 8.2. Наполнить $new_message
        $new_message->message = $this->data['message'];

        // 8.3. Сохранить $new_message
        $new_message->save();

      // 9. Связать $new_message с $room
      if(!$room->messages->contains($new_message->id))
        $room->messages()->attach($new_message->id);

      // 10. Связать $new_message с $user
      if(!$user->m10_messages->contains($new_message->id))
        $user->m10_messages()->attach($new_message->id);

      // 11. Транслировать сообщение

        // 11.1. Если получатели не указаны, то всем подписчикам через публичный канал
        if(count($room['m5_users_md2003']) == 0) {

          Event::fire(new \R2\Broadcast([
            'channels' => ['m10:chat:public:'.$room['name']],
            'queue'    => 'chat',
            'data'     => [
              'task'    => "new_message",
              'message' => [
                'id'          => $new_message->id,
                'steamname'   => $user->nickname,
                'avatar'      => !empty($user->avatar_steam) ? $user->avatar_steam : (!empty($user->avatar) ? $user->avatar : 'http://placehold.it/34x34/ffffff'),
                'level'       => '1',
                'message'     => $new_message->message,
                'id_user'     => $user->id,
                'system'      => 0,
                'created_at'  => $new_message->created_at,
                'updated_at'  => $new_message->updated_at,
              ]
            ]
          ]));

        }

        // 11.2. Если получатели указаны, то только этим получателям через частный канал
        else if(count($room['m5_users_md2003']) > 0) {
          foreach($room['m5_users_md2003'] as $recipient) {

            // 1] Получить ID получателя
            $id = $recipient['id'];

            // 2] Транслировать данные получателю
            Event::fire(new \R2\Broadcast([
              'channels' => ['m10:chat:private:'.$room['name'].':'.$id],
              'queue'    => 'chat',
              'data'     => [
                'message' => [
                  'id'          => $new_message->id,
                  'steamname'   => $user->nickname,
                  'avatar'      => !empty($user->avatar_steam) ? $user->avatar_steam : (!empty($user->avatar) ? $user->avatar : 'http://placehold.it/34x34/ffffff'),
                  'level'       => '1',
                  'message'     => $new_message->message,
                  'id_user'     => $user->id,
                  'system'      => 0,
                  'created_at'  => $new_message->created_at,
                  'updated_at'  => $new_message->updated_at,
                ]
              ]
            ]));

          }
        }



    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C2_add_message_to_the_room from M-package M10 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M10', 'C2_add_message_to_the_room']);
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

