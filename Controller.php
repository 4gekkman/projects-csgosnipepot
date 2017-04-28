<?php
////==============================================////
////																				      ////
////             Контроллер D-пакета		  	      ////
////																							////
////==============================================////


/**
 *
 *
 *     HTTP-метод   Имя API     Ключ              Защита   Описание
 * ------------------------------------------------------------------------------------------------------------
 * Стандартные операции
 *
 *     GET          GET-API     любой get-запрос           Обработка всех GET-запросов
 *     POST         POST-API    любой post-запрос          Обработка всех POST-запросов
 *
 * ------------------------------------------------------------------------------------------------------------
 * Нестандартные POST-операции
 *
 *                  POST-API1   D10013:1              (v)  Изменить ник пользователя в системе.
 *                  POST-API2   D10013:2              (v)  Изменить аватар пользователя в системе.
 *                  POST-API3   D10013:3              (v)  Обновить updated at пользователя.
 *
 *
 *
 */


//-------------------------------//
// Пространство имён контроллера //
//-------------------------------//

  namespace D10013;


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
  public $packid = "D10013";
  public $layoutid = "L10004";

  //--------------------------------------//
  // GET-API. Обработка всех GET-запросов //
  //--------------------------------------//
  public function getIndex() {

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

    //-----------------------//
    // Обработать GET-запрос //
    //-----------------------//

      // 1. Получить всех победителей
      $winners = \M5\Models\MD1_users::whereHas('groups', function($queue){
        $queue->where('name', 'Winners');
      })->get();

      // 2. Получить все текущие игровые данные
      $allgamedata = runcommand('\M9\Commands\C7_get_all_game_data', ['rounds_limit' => 1, 'safe' => true]);
      if($allgamedata['status'] != 0)
        throw new \Exception($allgamedata['data']['errormsg']);

      // N. Вернуть клиенту представление и данные $data
      return View::make($this->packid.'::view', ['data' => json_encode([

        'document_locale'       => r1_get_doc_locale($this->packid),
        'auth'                  => session('auth_cache') ?: '',
        'packid'                => $this->packid,
        'layoutid'              => $this->layoutid,
        'websocket_server'      => (\Request::secure() ? "https://" : "http://") . (\Request::getHost()) . ':6001',
        'websockets_channel'    => Session::getId(),
        'winners'               => $winners,
        'rooms'                 => $allgamedata['data']['rooms']

      ]), 'layoutid' => $this->layoutid.'::layout']);



  } // конец getIndex()


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
      // - $key       - ключ операции (напр.: D10013:1)
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
      // - А в $key прислать ключ-код номер операции.
      if(!empty($key) && empty($command)) {

        //---------------------------------//
        // Нестандартная операция D10013:1 //
        //---------------------------------//
        // - Изменить ник пользователя в системе.
        if($key == 'D10013:1') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Выполнить команду
          $result = runcommand('\M5\Commands\C73_rename_user', $data);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10013:2 //
        //---------------------------------//
        // - Изменить аватар пользователя в системе.
        if($key == 'D10013:2') {

          // 1. Подготовить данные для команды
          $data = [
            "file"        => \Request::hasFile('file') ? Input::file('file') : "",
            "group"       => Input::get('group') ?: "",
            "params"      => Input::get('params') ? json_decode(Input::get('params'), true) : [],
            "timestamp"   => Input::get('timestamp') ?: 0,
          ];

          // 2. Выполнить команду
          $result = runcommand('\M7\Commands\C1_saveimage', $data);

          // n. Вернуть результаты
          return $result;

        }

        //---------------------------------//
        // Нестандартная операция D10013:3 //
        //---------------------------------//
        // - Обновить updated at пользователя.
        if($key == 'D10013:3') {

          // 1. Получить присланные данные
          $data = Input::get('data');   // массив

          // 2. Выполнить команду
          $result = runcommand('\M5\Commands\C74_touch_user', $data);

          // n. Вернуть результаты
          return $result;

        }

      }





  } // конец postIndex()


}?>