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
 *                  POST-API1   D10003:1              (v)      Описание
 *                  POST-API2   D10003:2              (v)      Описание
 *
 *
 *
 */


//-------------------------------//
// Пространство имён контроллера //
//-------------------------------//

  namespace D10003;


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
  use League\Flysystem\Exception;

  // Модели и прочие классы



//------------//
// Контроллер //
//------------//
class Controller extends BaseController {

  //-------------------------------------------------//
  // ID пакета, которому принадлежит этот контроллер //
  //-------------------------------------------------//
  public $packid = "D10003";
  public $layoutid = "L10001";

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

      // 1. Получить значение входящего параметра "provider" из query string

        // Получить
        $provider = Input::get('provider');
        if(empty($provider) && !is_string($provider)) return "Error: wrong provider";

        // Привести к нижнему регистру
        $provider = mb_strtolower($provider);

      // 2. Получить массив-конфиг для HybridAuth и обработать его

        // 2.1. Получить
        $hybridauth_config = config("M5.hybridauth_config");
        if(empty($hybridauth_config) || !is_array($hybridauth_config)) return "Error: config is absent";

        // 2.2. Определить, через какой хост проводить аутентификацию
        // - Это зависит от среды, в которой запущено приложение.
        // - Данные берутся из конфига M5.
        $host2auth = call_user_func(function(){

          // 1] Получить параметры
          $params = config("M5.hybridauth_env_params");

          // 2] Получить название текущей среды
          $env = env('APP_ENV', 'dev');

          // 3] Получить параметр для этой среды
          $env_params = array_key_exists($env, $params) ? $params[$env] : false;
          if($env_params == false)
            throw new \Exception("Не удалось получить параметры аутентификации для среды ".$env);

          // 4] Получить хост
          $host = $env_params['host'];

          // 5] Если хост равен auto, присвоить ему \Request::getHost()
          if($host == 'auto') $host = \Request::getHost();

          // n] Вернуть хост
          return $host;

        });

        // 2.2. Добавить к base_url в конфиге в виде приставки протокол, хост и порт
        $hybridauth_config['base_url'] =  (\Request::secure() ? "https://" : "http://") . ($host2auth) . ":" . (\Request::getPort()) . $hybridauth_config['base_url']; // "http://matrixcsgo.com/authwith/hybrid-auth-endpoint";

      // 3. В зависимости от $provider выполнить соответствующую команду
      // - Каждой команде передавать ID текущей сессии (выступит каналом для коммуникации через websockets)
      // - Каждой команде также передавать $provider и $hybridauth_config.
      // - Если провайдер указан неправильно, сообщить об этом.
      switch($provider) {
        case "steam": $result = runcommand('\M5\Commands\C69_auth_steam', ["websockets_channel" => Session::getId(), "provider" => $provider, "hybridauth_config" => $hybridauth_config]); break;
        default: return "Error: wrong provider";
      }

      // 4. Если команда вернула ошибку, сообщить об этом
      if($result['status'] != 0) {
        Log::info('Error: '.$result['data']['errormsg']);
        write2log('Error: '.$result['data']['errormsg']);
        return "Error: ".$result['data']['errortext'];
      }

      // 5. Получить режим аутентификации
      // - window     | [По умолчанию] Аутентификация в окне, которое потом исчезает
      // - redirect   | Редирект на аутентификацию, потом редирект обратно по полученному URL
      $authmode = call_user_func(function(){

        // 1] Получить значение входящего параметра "authmode" из query string
        $authmode = Input::get('authmode');

        // 2] Если $authmode не найден, или не равен window/redirect задать значение по умолчанию
        if(empty($authmode) || !in_array($authmode, ['window', 'redirect']))
          $authmode = "window";

        // 3] Если $authmode равен 'redirect', получить ещё URL для редиректа
        $url = "";
        if($authmode == 'redirect') {

          // 3.1] Получить значение входящего параметра url_redirect
          $url_redirect = Input::get('url_redirect');

          // 3.2] Если $url_redirect пуст
          if(empty($url_redirect))
            $authmode = "window";

          // 3.3] Иначе
          else
            $url = $url_redirect;

        }

        // n] Вернуть результат
        return [
          'mode' => $authmode,
          'url'  => $url
        ];

      });

      // n. Завершить аутентификацию

        // n.1. Если authmode == 'window'
        if($authmode['mode'] == 'window') {

          View::make($this->packid.'::view', ['data' => json_encode([

            'document_locale'       => r1_get_doc_locale($this->packid),
            'auth'                  => session('auth_cache') ?: '',
            'packid'                => $this->packid,
            'layoutid'              => $this->layoutid

          ]), 'layoutid' => $this->layoutid.'::layout']);

        }

        // n.2. Если authmode == 'redirect'
        else if($authmode['mode'] == 'redirect') {

          return redirect()->away($authmode['url']);

        }


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
      // - $key       - ключ операции (напр.: D10003:1)
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

        //-----------------------------//
        // Нестандартная операция D10003:1 //
        //-----------------------------//
        if($key == 'D10003:1') {



        }


      }

  } // конец postIndex()


}?>