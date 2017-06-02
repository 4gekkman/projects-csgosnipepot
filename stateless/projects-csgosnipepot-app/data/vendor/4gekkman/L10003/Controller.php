<?php
////==============================================////
////																				      ////
////             Контроллер L-пакета		  	      ////
////																							////
////==============================================////


/**
 *
 *
 *     HTTP-метод   Имя API     Ключ              Защита   Описание
 * ------------------------------------------------------------------------------------------------------------
 * Стандартные операции
 *
 *     POST         POST-API    любой post-запрос          Обработка всех POST-запросов
 *
 * ------------------------------------------------------------------------------------------------------------
 * Нестандартные POST-операции
 *
 *                  POST-API1   L10003:1                   Безопасная обёртка для команды логаута
 *                  POST-API2   L10003:2                   Безопасная обёртка для команды постинга в чат
 *                  POST-API3   L10003:3                   Безопасная обёртка для команды бана
 *                  POST-API4   L10003:4                   Сохранение в куки нового значения для языка
 *
 *
 *
 */


//-------------------------------//
// Пространство имён контроллера //
//-------------------------------//

  namespace L10003;


//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

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

  // Модели и прочие классы



//------------//
// Контроллер //
//------------//
class Controller extends BaseController {

  //-------------------------------------------------//
  // ID пакета, которому принадлежит этот контроллер //
  //-------------------------------------------------//
  public $packid = "L10003";


  //----------------------------------------//
  // POST-API. Обработка всех POST-запросов //
  //----------------------------------------//
  public function postIndex() {

    //----------------------------------------------------------------------------------//
    // Провести авторизацию прав доступа запрашивающего пользователя к этому интерфейсу //
    //----------------------------------------------------------------------------------//
    // - Если команда для проведения авторизации доступна, и если авторизация включена.
    if(class_exists('\M5\Commands\C66_authorize_access') && config("M5.authorize_access_ison") == true) {

      // Провести авторизацию
      $authorize_results = runcommand('\M5\Commands\C66_authorize_access', ['packid' => $this->packid, 'userid' => lib_current_user_id()]);

      // Если доступ запрещён, вернуть документ с кодом 403
      if($authorize_results['status'] == -1)
        return Response::make("Unfortunately, access to this document is forbidden for you.", 403);

    }

    //------------------------//
    // Обработать POST-запрос //
    //------------------------//

      //------------------------------------------//
      // 1] Получить значение опций key и command //
      //------------------------------------------//
      // - $key       - ключ операции (напр.: L10003:1)
      // - $command   - полный путь команды, которую требуется выполнить
      $key        = Input::get('key');
      $command    = Input::get('command');


      //----------------------------------------//
      // 2] Обработка стандартных POST-запросов //
      //----------------------------------------//
      // - Это около 99% всех POST-запросов.
      if(empty($key) && !empty($command)) {

        // 1. Получить присланные данные

          // Получить данные data
          $data = Input::get('data');   // массив


        // 2. Выполнить команду и получить результаты
        $response = runcommand(

            $command,                   // Какую команду выполнить
            $data,                      // Какие данные передать команде
            lib_current_user_id()       // ID пользователя, от чьего имени выполнить команду

        );


        // 3. Добавить к $results значение timestamp поступления запроса
        $response['timestamp'] = $data['timestamp'];


        // 4. Сформировать ответ и вернуть клиенту
        return Response::make(json_encode($response, JSON_UNESCAPED_UNICODE));

      }


      //------------------------------------------//
      // 3] Обработка нестандартных POST-запросов //
      //------------------------------------------//
      // - Очень редко алгоритм из 2] не подходит.
      // - Например, если надо принять файл.
      // - Тогда $command надо оставить пустой.
      // - А в $key прислать ключ-код операции.
      if(!empty($key) && empty($command)) {

        //---------------------------------//
        // Нестандартная операция L10003:1 //
        //---------------------------------//
        // - Безопасная обёртка для команды логаута
        if($key == 'L10003:1') { try {

          // 1. Выполнить команду
          $result = runcommand('\M5\Commands\C59_logout', [

          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2. Вернуть результаты
          return $result;

        } catch(\Exception $e) {
          $errortext = 'Invoking of command L10003:L10003:1 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
          Log::info($errortext);
          write2log($errortext, ['M9', 'L10003:L10003:1']);
          return [
            "status"  => -2,
            "data"    => [
              "errortext" => $errortext,
              "errormsg" => $e->getMessage()
            ]
          ];
        }}

        //---------------------------------//
        // Нестандартная операция L10003:2 //
        //---------------------------------//
        // - Безопасная обёртка для команды постинга в чат
        if($key == 'L10003:2') { try {

          // 1. Получить комнату с именем $this->data['room']
          $room = \M10\Models\MD1_rooms::where('name', "main")->first();
          if(empty($room))
            throw new \Exception("Can't find the room with NAME = 'main'");

          // 2. Выполнить команду
          $result = runcommand('\M10\Commands\C2_add_message_to_the_room', [
            "message"     => Input::get('data')['message'],
            "id_room"     => $room->id,
            "from_who_id" => 0
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 3. Вернуть результаты
          return $result;

        } catch(\Exception $e) {
          $errortext = 'Invoking of command L10003:L10003:2 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
          Log::info($errortext);
          write2log($errortext, ['M9', 'L10003:L10003:2']);
          return [
            "status"  => -2,
            "data"    => [
              "errortext" => $errortext,
              "errormsg" => $e->getMessage()
            ]
          ];
        }}

        //---------------------------------//
        // Нестандартная операция L10003:3 //
        //---------------------------------//
        // - Безопасная обёртка для команды бана
        if($key == 'L10003:3') { try {

          // 1. Получить ID пользователя
          $id_user = lib_current_user_id();

          // 2. Извлечь из конфига информацию о модераторах комнаты чата с именем 'main'
          $chat_main_moderators = config("M10.rooms.main.moderator_ids");
          if(empty($chat_main_moderators))
            $chat_main_moderators = [];

          // 3. Если $id_user не состоит в $chat_main, возбудить исключение
          if(!in_array($id_user, $chat_main_moderators))
            throw new \Exception('Вы не являетесь модератором этого чата.');

          // 4. Выполнить команду бана
          $result = runcommand('\M10\Commands\C6_ban', [
            "room_name"     => 'main',
            "id_user"       => Input::get('data')['id_user'],
            "ban_time_min"  => Input::get('data')['ban_time_min'],
            "reason"        => Input::get('data')['reason']
          ]);

          // 5. Вернуть результаты
          return $result;

        } catch(\Exception $e) {
          $errortext = 'Invoking of command L10003:L10003:2 from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
          Log::info($errortext);
          write2log($errortext, ['M9', 'L10003:L10003:2']);
          return [
            "status"  => -2,
            "data"    => [
              "errortext" => $errortext,
              "errormsg" => $e->getMessage()
            ]
          ];
        }}

        //---------------------------------//
        // Нестандартная операция L10003:4 //
        //---------------------------------//
        // - Сохранение в куки нового значения для языка.
        if($key == 'L10003:4') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Сформировать $response
          $response = [
            "status"      => 0,
            "data"        => "",
            "timestamp"   => $data['timestamp']
          ];

          // 3. Провести валидацию входящих параметров
          $validator = r4_validate($data, [
            "locale"          => ["required", "in:ru,en"]
          ]); if($validator['status'] == -1) {
            return [
              "status"  => -2,
              "data"    => [
                "errortext" => $validator['data'],
                "errormsg" => $validator['data']
              ],
              "timestamp"   => $data['timestamp']
            ];
          }

          // 4. Установить новые значения кук
          $cookie = cookie()->forever('app_locale_cookie', $data['locale']);

          // 5. Сформировать ответ и вернуть клиенту
          return Response::make(json_encode($response, JSON_UNESCAPED_UNICODE))
              ->withCookie($cookie);

        }



      }

  } // конец postIndex()


}?>