<?php
////======================================================////
////																										  ////
////              	Библиотека php-хелперов	              ////
////																											////
////======================================================//*/
//// 			        		         ////
//// 	   Подключение классов	 ////
//// 			         		         ////
////===========================////

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

////======================================================//*/
//// 			         ////
//// 	   Функции	 ////
//// 			         ////
////===============////

  //-----//
  // r1_ //
  //-----//
  if(!function_exists('r1_')) {
    /**
     *  <h1>Список хелперов пакета R1</h1>
     *  <pre>
     *
     *    write2log                  | Возбудить событие R2\Event с ключём "m2:write2log"
     *    runcommand                 | Провести авторизацию и выполнить команду
     *    lib_current_user_id        | Получить ID текущего пользователя
     *    r1_get_doc_locale          | Получить установленную локаль M,D,L,W-пакета
     *    r1_url_exist               | Узнать, существует ли указанный URL
     *    r1_array_unique_recursive  | Аналог array_unique, только для многомерных массивов
     *    r1_udatetime               | Получить строковое представление datetime с микросекундами.
     *    r1_fs                      | Получить новый экземпляр класса FilesystemManager
     *    r1_fs2                     | Получить новый экземпляр класса Filesystem.
     *    r1_countdim                | Подсчитать кол-во измерений многомерного массива.
     *    r1_config_set              | Изменить значение любого параметра в любом конфиге laravel из каталога config.
     *    r1_query                   | Хелпер для безопасного осуществления транс-пакетных запросов.
     *    r1_isJSON                  | Является ли переданная строка валидным JSON
     *    r1_is_schema_exists        | Проверить, существует ли в текущем подключении указанная БД
     *    r1_hasTable                | Проверить, существует ли таблица $table_name в БД $db_name, в текущем подключении
     *    r1_hasColumn               | Проверить, существует ли столбец $column_name таблице $table_name в БД $db_name, в текущем подключении
     *    r1_getColumns              | Получить список имён столбцов из таблицы $table_name БД $db_name текущего подключения
     *    r1_rel_exists              | Проверить существование связи $relation у модели $model M-пакета $packid
     *    r1_checksum                | Получить контрольную сумму для файла или каталога по заданному path
     *    r1_encrypt_data            | Зашифровать $text указанным $key
     *    r1_decrypt_data            | Расшифровать $text указанным $key
     *
     *  </pre>
     * @return bool
     */
    function r1_() {

      return true;

    }
  } else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_, поскольку такая уже есть!', ['R1','r1_']);
  }


  //-----------//
  // write2log //
  //-----------//
  if(!function_exists('write2log')) {
    /**
     *  <h1>Описание</h1>
     *  <pre>
     *    Возбудить событие R2\Event с ключём "m2:write2log"
     *    M-пакет M2 перехватит его, и запишет $msg в лог, с тегами $tags
     *    Не вернёт ничего в случае успеха.
     *    Вернёт следующий массив в случае ошибки:
     *
     *      [
     *        "status"  => -2,
     *        "data"    => $errortext  // Текст ошибки
     *      ];
     *
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    write2log("сообщение", ["тег1", "тег2"]);
     *  </pre>
     *
     * @param  string $msg
     * @param  array $tags
     *
     * @return mixed
     */
    function write2log($msg, $tags = []) {

      return Event::fire(new \R2\Event([
        'keys'      => ['m2:write2log'],
        'tags'      => $tags,
        'msg'       => $msg
      ]));

    }
  } else \Log::info('Внимание! Пакету R1 не удалось определить функцию write2log, поскольку такая уже есть!');


  //------------//
  // runcommand //
  //------------//
  if(!function_exists('runcommand')) {
    /**
     *  <h1>Описание</h1>
     *  <pre>
     *    Провести авторизацию и выполнить команду
     *  </pre>
     *  <h1>Что возвращает</h1>
     *  <pre>
     *    - JSON-массив:
     *
     *      [
     *        "status"        // Статус результата выполнения команды
     *        "timestamp"     // Timestamp прихода запроса от клиента
     *        "data"          // Данные
     *      ]
     *  </pre>
     *  <h1>Какие статусы бывают, зависимость data от статуса</h1>
     *  <pre>
     *    0   // Команда выполнена успешно. В data результаты её выполненя.
     *    -1  // Нет доступа. В data строка-сообщение об этом.
     *    -2  // Команда завершилась с ошибкой. В data текст ошибки.
     *  </pre>
     *  <h1>Примеры использования</h1>
     *  <pre>
     *
     *    1. Синхронное выполнение
     *
     *      1.1. Простой синхронный запуск
     *        runcommand('\M1\Commands\C1_parseapp');
     *
     *      1.2. С передачей данных
     *        runcommand('\M1\Commands\C1_parseapp', ['key1'=>'value1','key2'=>'value2']);
     *
     *      1.3. С авторизацией по ID текущего пользователя (ID == "15" в примере)
     *        runcommand('\M1\Commands\C1_parseapp', [], 15);
     *
     *    2. Добавление в очередь задач
     *
     *      2.1. Без дополнительной отсрочки выполнения
     *      runcommand('\M1\Commands\C1_parseapp', [], "", ['on'=>true, 'delaysecs'=>'']);
     *
     *      2.2. С дополнительной отсрочкой выполнения в 10 секунд
     *      runcommand('\M1\Commands\C1_parseapp', [], "", ['on'=>true, 'delaysecs'=>'10']);
     *
     *  </pre>
     *
     * @param  mixed $command
     * @param  array $data
     * @param  mixed $userid
     * @param  mixed $queue
     *
     * @return mixed
     */
    function runcommand($command, $data = [], $userid = 0, $queue = ['on'=>false, 'delaysecs'=>'', 'name' => 'default']) {

      // 1. Провести exec-авторизацию, если она включена
      if(config("M5.authorize_exec_ison") == true) {

        // 1.1. Если $userid == -1
        // - Вернуть ответ со статусом -1 (доступ запрещён)
        if($userid == -1)
          return [
            "status"  => -1,
            "data"    => $command
          ];

        // 1.2. В ином случае, это должно быть число от 0 и выше
        // - Иначе вернуть ответ со статусом -1 (доступ запрещён)
        else {
          $validator = r4_validate(['userid' => $userid], [
            "userid"              => ["required", "regex:/^[0-9]+$/ui"],
          ]); if($validator['status'] == -1) {
            return [
              "status"  => -1,
              "data"    => $command
            ];
          }
        }

        // 1.3. Если $userid !== 0, то провести exec-авторизацию
        if($userid !== 0) {

          // 1] Извлечь из сессии значение по ключу "authorize_exec"
          $authorize_exec = session('authorize_exec');

          // 2] Провести валидацию $authorize_exec
          $is_authorize_exec_valid = call_user_func(function() USE ($authorize_exec) {

            // 2.1] Если $authorize_exec пусто, вернуть false
            if(empty($authorize_exec)) return false;

            // 2.2] Если $authorize_exec не массив строк, вернуть false
            $validator = r4_validate(['authorize_exec' => $authorize_exec], [
              "authorize_exec"              => ["required", "array"],
              "authorize_exec.*"            => ["string"]
            ]); if($validator['status'] == -1) {
              return false;
            }

            // 2.3] Вернуть true
            return true;

          });

          // 3] Искать $command в $authorize_exec
          // - Если $authorize_exec не пусто и валидно.
          // - Если права нет, вернуть статус -1 (доступ запрещён).
          if($is_authorize_exec_valid) {

            if(!in_array($command, $authorize_exec))
              return [
                "status"  => -1,
                "data"    => $command
              ];

          }

          // 4] Иначе, искать право на выполнение $command пользователя в БД
          // - А заодно и перезаписать кэш "authorize_exec" в сессии.
          // - Если права нет, вернуть статус -1 (доступ запрещён).
          else {

            // 4.1] Получить коллекцию всех команд, которые $userid может выполнять
            $commands = call_user_func(function() USE ($userid) {

              // 4.1.1] Попробовать найти пользователя $userid
              // - Если найти его не удастся, вернуть пустую коллекцию.
              $user = \M5\Models\MD1_users::find($userid);
              if(empty($user))
                return collect([]);

              // 4.1.2] Получить коллекцию всех exec-прав, связанных с $user
              $privileges = call_user_func(function() USE ($user) {

                // 1) Состоит ли $user в любой административной группе?
                $admingroup = \M5\Models\MD2_groups::where('isadmin', 1)
                  ->whereHas('users', function($query) USE ($user) {
                    $query->where('id', $user->id);
                  })->first();

                // 2) Если состоит, вернуть все exec-права
                if(!empty($admingroup))
                  return \M5\Models\MD3_privileges::whereHas('privtypes', function($query){
                    $query->where('name', 'exec');
                  })->get();

                // 3) Если не состоит, вычислить и вернуть все его права
                else
                  return \M5\Models\MD3_privileges::whereHas('privtypes', function($query) {
                    $query->where('name', 'exec');
                  })->where(function($query) USE ($user) {

                    // Права, прямо связанные с пользователем
                    $query->whereHas('users', function($query) USE ($user) {
                      $query->where('id', $user->id);
                    });

                    // Права, связанные с группами, с которыми связан пользователь
                    $query->orWhereHas('groups', function($query) USE ($user) {
                      $query->whereHas('users', function($query) USE ($user) {
                        $query->where('id', $user->id);
                      });
                    });

                    // Права, связанные с тегами, с которыми связан пользователь
                    $query->orWhereHas('tags', function($query) USE ($user) {
                      $query->whereHas('users', function($query) USE ($user) {
                        $query->where('id', $user->id);
                      });
                    });

                    // Права, связанные с тегами, связанные с группами, с которыми связан пользователь.
                    $query->orWhereHas('tags', function($query) USE ($user) {
                      $query->whereHas('groups', function($query) USE ($user) {
                        $query->whereHas('users', function($query) USE ($user) {
                          $query->where('id', $user->id);
                        });
                      });
                    });

                  })->get();

              });

              // 4.1.3] Если у модели MD5_commands в M1 нет связи m5_privileges
              // - Вернуть пустую коллекцию.
              if(!r1_rel_exists("m1", "md5_commands", "m5_privileges"))
                return collect([]);

              // 4.1.4] В противном случае, вернуть коллекцию соотв.команд
              $commands = r1_query(function() USE ($privileges) {
                return \M1\Models\MD5_commands::with(['packages'])->where(function($query) USE ($privileges) {
                  $query->whereHas('m5_privileges', function($query) USE ($privileges) {
                    $query->whereIn('id', $privileges->pluck('id'));
                  });
                })->get();
              });
              if(!$commands) return collect([]);
              else return $commands;

            });

            // 4.2] Превратить $commands в массив полн.квалиф.команд
            $commands_final = call_user_func(function() USE ($commands) {

              // 4.2.1] Если $commands эта пустая коллекция, вернуть пустой массив
              if(count($commands) == 0) return [];

              // 4.2.2] Подготовить массив для результата
              $result = [];

              // 4.2.3] Пробежаться по $commands и наполнить $result
              $commands->each(function($item) USE (&$result) {

                array_push($result, "\\".$item->packages[0]->id_inner."\\Commands\\".$item->name);

              });

              // 4.2.n] Вернуть результат
              return $result;

            });

            // 4.3] Перезаписать в сессии кэш authorize_exec
            session(['authorize_exec' => $commands_final]);

            // 4.4] Если $command не в $commands_final
            // - Вернуть статус -1 (нет доступа).
            if(!in_array($command, $commands_final))
              return [
                "status"  => -1,
                "data"    => $command
              ];

          }

        }

      }

      // 2. Добавить запись в лог

        // 2.1. Если команда выполнена без ограничений по правам
        if($userid === 0) {

          file_put_contents(env('LOG_EXEC_UNLIMITED'), json_encode([
            'ip'        => \Request::ip(),
            'id_user'   => $userid,
            'command'   => $command,
            'data'      => json_encode($data, JSON_UNESCAPED_UNICODE),
            'datetime'  => \Carbon\Carbon::now()->toDateTimeString()
          ], JSON_UNESCAPED_UNICODE).PHP_EOL , FILE_APPEND | LOCK_EX);

        }

        // 2.2. Если команда выполнена от имени пользователя
        else {

          file_put_contents(env('LOG_EXEC'), json_encode([
            'ip'        => \Request::ip(),
            'id_user'   => $userid,
            'command'   => $command,
            'data'      => json_encode($data, JSON_UNESCAPED_UNICODE),
            'datetime'  => \Carbon\Carbon::now()->toDateTimeString()
          ], JSON_UNESCAPED_UNICODE).PHP_EOL , FILE_APPEND | LOCK_EX);

        }

      // 3. Выполнить команду $command
      // - Передав ей данные $data

        // 3.1. Синхронно, если иное не указано в 4-м аргументе runcommand
        if($queue['on'] == false) $result = Bus::dispatch(new $command($data));

        // 3.2. Асинхронно (отправить в очередь)
        else {

          // 1] Без задержки
          if(empty($queue['delaysecs'])) Queue::push(new $command($data), [], $queue['name']);

          // 2] С задержкой, если она назначена в 4-м агрументе runcommand
          else Queue::later($queue['delaysecs'], new $command($data), [], $queue['name']);

        }

      // 4. Подготовить массив с ответом, и вернуть

        // 4.1. Если команда выполняется синхронно
        if($queue['on'] == false) {
          $response = [
            "status"    => $result['status'],
            "data"      => $result['data']
          ];
          if(array_key_exists('timestamp', $data))
            $response['timestamp'] = $data['timestamp'];
          return $response;
        }

        // 4.2. Если команда выполняется асинхронно
        if($queue['on'] == true) {
          $response = [
            "status"    => 0,
            "data"      => ""
          ];
          if(array_key_exists('timestamp', $data))
            $response['timestamp'] = $data['timestamp'];
          return $response;
        }

    }
  } else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию runcommand, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию runcommand, поскольку такая уже есть!', ['R1','runcommand']);
  }


  //---------------------//
  // lib_current_user_id //
  //---------------------//
	if(!function_exists('lib_current_user_id')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить ID текущего пользователя
     *    Вернёт -1, если оный вычислить не удалось (пользователь не аутентифицирован).
     *    Вернёт ID текущего пользователя, если оный вычислить удалось (в т.ч. это м.б. id guest'а).
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $id = lib_current_user_id();
     *  </pre>
     *
		 * @return number
		 */
    function lib_current_user_id()
    { try {

      // 1. Получить из сессии значение по ключу "auth_cache"
      $auth_cache = session('auth_cache');

      // 2. Провести валидацию $auth_cache
      $is_auth_cache_valid = call_user_func(function() USE ($auth_cache) {

        // 2.1] Если $auth_cache пусто, вернуть -1
        if(empty($auth_cache)) return -1;

        // 2.2] Если $auth_cache не является валидным json, вернуть -1
        $validator = r4_validate(['auth_cache' => $auth_cache], [
          "auth_cache"         => ["required", "json"],
        ]); if($validator['status'] == -1) {
          return -1;
        }

        // 2.3] Расшифровать json
        $auth_cache_json = json_decode($auth_cache, true);

        // 2.4] Если в массиве $auth_cache_json нет ключей 'user', 'user.id', вернуть -1
        if(!array_key_exists('user', $auth_cache_json) || !array_key_exists('id', $auth_cache_json['user'])) return -1;

        // 2.5] Если $auth_cache_json['user']['id'] пусто или не является положительным целым числом, вернуть -1
        $validator = r4_validate(['id' => $auth_cache_json['user']['id']], [
          "id"              => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        ]); if($validator['status'] == -1) {
          return -1;
        }

        // 2.6] Вернуть $auth_cache_json['user']['id']
        return $auth_cache_json['user']['id'];

      });

      // 3. Вернуть результат
      return $is_auth_cache_valid;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере lib_current_user_id: '.$e->getMessage(), ['lib_current_user_id']);
      return NULL;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию lib_current_user_id, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию lib_current_user_id, поскольку такая уже есть!', ['R1','lib_current_user_id']);
  }


  //-------------------//
  // r1_get_doc_locale //
  //-------------------//
	if(!function_exists('r1_get_doc_locale')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить установленную локаль M,D,L,W-пакета
     *  </pre>
     *  <h1>Правила вычисления локали пакета</h1>
     *  <pre>
     *
     *    1. Если нет опубликованного конфига пакета
     *      - В этом случае применяется локаль "RU".
     *
     *    2. Если
     *        - Есть опубликованный конфиг.
     *        - В нём есть параметры $locales и $locale.
     *        - $locales является массивом, а $locale строкой.
     *        - $locale есть в $locales.
     *       То
     *        - Возвращается локаль $locale.
     *
     *    3. Если
     *        - Есть опубликованный конфиг.
     *        - $locales нет, или это не массив строк.
     *        - Возвращается локаль "RU".
     *
     *    4. Если
     *        - Есть опубликованный конфиг.
     *        - $locales есть, и это не пустой массив строк.
     *        - $locale нет, или это пустая строка.
     *        - $applocale есть, и это строка.
     *        - $applocale есть в $locales.
     *       То:
     *        - Возвращается локаль $applocale.
     *
     *    5. Если
     *        - Есть опубликованный конфиг.
     *        - $locales есть, и это не пустой массив строк.
     *        - $locale нет, или это пустая строка.
     *        - $applocale есть, и это строка.
     *        - $applocale нет в $locales.
     *       То
     *        - Возвращается локаль $locales[0].
     *
     *    6. Если ни одно из предыдущих не сработало
     *      - Возвращается локаль "RU".
     *
     *  </pre>
     *  <h1>Возвращает</h1>
     *  <pre>
     *    В случае успеха возвращает найденную локаль.
     *    В случае ошибки возвращает локаль "RU" и пишет сообщение в лог.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Получить локаль пакета M1:
     *    r1_get_doc_locale("M1")
     *  </pre>
     *
		 * Получить установленную локаль M,D,L,W-пакета
     *
     * @param  string $packid
     *
		 * @return array
		 */
		function r1_get_doc_locale($packid)
    { try {

      // 1] Провести валидацию значения $packid
      if(preg_match("/^[MDLW]{1}[1-9]{1}[0-9]*$/ui", $packid) == 0)
        throw new \Exception('Параметр packid не является валидным ID M,D,L,W-пакета');

      // 2] Подготовить переменную для локали
      $locale = '';

      // 3] Вернуть локаль

        // 3.1] Проверить наличие опубликованного конфига пакета $packid
        if(!file_exists(base_path('config/'.mb_strtoupper($packid).'.php')))
          throw new \Exception('Конфиг пакета '.$packid.' не найден в каталоге config.');

        // 3.2] Получить значения параметров locales, locale и applocale

          // Получить
          $locales    = config(mb_strtoupper($packid).'.locales');
          $locale     = config(mb_strtoupper($packid).'.locale');
          $applocale  = config('app.locale');

          // Проверить $locales
          if(is_null($locales))
            throw new \Exception("Параметр locales отсутствует в конфиге.");
          if(empty($locales))
            throw new \Exception("Параметр locales пуст, что является ошибкой (пакет не может не поддерживать ни одного языка).");
          if(!is_array($locales))
            throw new \Exception("Параметр locales не является массивом.");
          foreach($locales as $l) {
            if(!is_string($l))
              throw new \Exception("Одно из значений массива locales не является строкой, что является ошибкой.");
          }

          // Привести $locales к нижнему регистру
          foreach($locales as &$l) {
            $l = mb_strtolower($l);
          }

          // Если $locale существует и строка, привести к нижнему регистру
          if(!empty($locale) && is_string($locale))
            $locale = mb_strtolower($locale);

          // Если $applocale существует и строка, привести к нижнему регистру
          if(!empty($applocale) && is_string($applocale))
            $applocale = mb_strtolower($applocale);

        // 3.2] Если
        //       - Есть опубликованный конфиг.
        //       - В нём есть параметры $locales и $locale.
        //       - $locales является массивом, а $locale строкой.
        //       - $locale есть в $locales.
        //      То
        //       - Возвращается локаль $locale.
        if(!empty($locale) && is_string($locale) && in_array($locale, $locales))
          return $locale;

        // 3.3] Если
        //       - Есть опубликованный конфиг.
        //       - $locales есть, и это не пустой массив строк.
        //       - $locale нет, или это пустая строка.
        //       - $applocale есть, и это строка.
        //       - $applocale есть в $locales.
        //      То
        //       - Возвращается локаль $applocale.
        if(empty($locale) && !empty($applocale) && is_string($applocale) && in_array($applocale, $locales))
          return $applocale;

        // 3.4] Если
        //       - Есть опубликованный конфиг.
        //       - $locales есть, и это не пустой массив строк.
        //       - $locale нет, или это пустая строка.
        //       - $applocale есть, и это строка.
        //       - $applocale нет в $locales.
        //      То
        //       - Возвращается локаль $locales[0].
        if(empty($locale) && !empty($applocale) && is_string($applocale) && !in_array($applocale, $locales))
          return $locales[0];

        // 3.5] Вернуть "RU"
        return "RU";

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_get_doc_locale: '.$e->getMessage(), ['r1_get_doc_locale']);
      return "RU";
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_locale, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_get_doc_locale, поскольку такая уже есть!', ['R1','r1_get_doc_locale']);
  }


  //---------------//
  // r1_url_exists //
  //---------------//
	if(!function_exists('r1_url_exists')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Узнать, существует ли указанный URL
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Сущетсвует ли URL: "http://google.com"
     *    r1_url_exists("http://google.com");
     *  </pre>
     *
     * @param  string $url
     *
		 * @return bool
		 */
		function r1_url_exists($url) {

      if(preg_match("#^https://#ui", $url) != 0)
        $url = str_replace("https://", "", $url);
      if(preg_match("#^http://#ui", $url) != 0)
        $url = str_replace("http://", "", $url);

      if (strstr($url, "/")) {
          $url = explode("/", $url, 2);
          $url[1] = "/".$url[1];
      } else {
          $url = array($url, "/");
      }

      $fh = fsockopen($url[0], 80);
      if ($fh) {
          fputs($fh,"GET ".$url[1]." HTTP/1.1\nHost:".$url[0]."\n\n");
          if (fread($fh, 22) == "HTTP/1.1 404 Not Found") { return FALSE; }
          else { return TRUE;    }

      } else { return FALSE;}

		}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_url_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_url_exists, поскольку такая уже есть!', ['R1','r1_url_exists']);
  }


  //---------------------------//
  // r1_array_unique_recursive //
  //---------------------------//
	if(!function_exists('r1_array_unique_recursive')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Аналог array_unique, только для многомерных массивов
     *    Возвращает обработанный массив.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $array = [1,2,3,2,1,[1,2,3,2,1]];
     *    r1_array_unique_recursive($array);  // [1,2,3,[1,2,3]]
     *  </pre>
     *
     * @param  array $array
     *
		 * @return array
		 */
    function r1_array_unique_recursive($array)
    {
      $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
      $result = array_values($result);

      foreach ($result as $key => $value)
      {
        if ( is_array($value) )
        {
          $result[$key] = r1_array_unique_recursive($value);
        }
      }

      return $result;
    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_array_unique_recursive, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_array_unique_recursive, поскольку такая уже есть!', ['R1','r1_array_unique_recursive']);
  }


  //--------------//
  // r1_udatetime //
  //--------------//
	if(!function_exists('r1_udatetime')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить строковое представление datetime с микросекундами.
     *    Возвращает строковое представление времени.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Получим строковое представление текущего времени с микросекундами.
     *    r1_udatetime('Y-m-d H:i:s.u');
     *  </pre>
     *
     * @param  string $format
     * @param  string $utimestamp
     *
		 * @return array
		 */
    function r1_udatetime($format = 'u', $utimestamp = null)
    {
      if (is_null($utimestamp))
          $utimestamp = microtime(true);

      $timestamp = floor($utimestamp);
      $milliseconds = round(($utimestamp - $timestamp) * 1000000);

      return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_udatetime, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_udatetime, поскольку такая уже есть!', ['R1','r1_udatetime']);
  }


  //-------//
  // r1_fs //
  //-------//
	if(!function_exists('r1_fs')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить новый экземпляр класса FilesystemManager
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Получим список имён всех каталогов в каталоге vendor/4gekkman
     *    r1_fs('vendor/4gekkman')->directories();
     *  </pre>
     *
     * @param  string $path
     *
		 * @return object
		 */
    function r1_fs($path)
    {

      config(['filesystems.default' => 'local']);
      config(['filesystems.disks.local.root' => base_path($path)]);
      return new \Illuminate\Filesystem\FilesystemManager(app());

    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_fs, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_fs, поскольку такая уже есть!', ['R1','r1_fs']);
  }


  //--------//
  // r1_fs2 //
  //--------//
	if(!function_exists('r1_fs2')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить новый экземпляр класса Filesystem.
     *    Отличие от FilesystemManager в том, что в Filesystem больше методов.
     *    Но используется Filesystem реже, т.к. нельзя в нём задать basepath.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Получим список имён всех каталогов в каталоге vendor/4gekkman
     *    r1_fs2()->directories('vendor/4gekkman');
     *  </pre>
     *
		 * @return object
		 */
    function r1_fs2()
    {

      return new \Illuminate\Filesystem\Filesystem();

    }
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_fs2, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_fs2, поскольку такая уже есть!', ['R1','r1_fs2']);
  }


  //-------------//
  // r1_countdim //
  //-------------//
	if(!function_exists('r1_countdim')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Подсчитать кол-во измерений многомерного массива.
     *    Возвращает число от 1 и выше в случае успеха.
     *    Возвращает 0 в случае неудачи.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $arr = [1,2,[1,2,[1,2]]];
     *    $dims = r1_countdim($arr);  // 3
     *  </pre>
     *
     * @param  string $array
     *
		 * @return object
		 */
    function r1_countdim($array)
    { try {

      // 1] Если $array не массив, возбудить исключение
      if(!is_array($array))
        throw new \Exception('Параметр array не является массивом');

      // 2] Подготовить переменную для результата
      $result = 1;

      // 3] Если длина массива $array == 0, вернуть $result
      if(count($array) == 0) return $result;

      // 4] Написать рекурсивную функцию для прощупывания глубины
      $recur = function($item, $depth = 0) USE (&$recur) {

        // 4.1] Если $item не массив, вернуть 0
        if(!is_array($item)) return 0;

        // 4.2] Если $item это пустой массив
        if(count($item) == 0) return $depth;

        // 4.3] Если же $item это массив
        $results = [];
        foreach($item as $elem) {

          // 4.3.1] Если $elem не массив
          if(!is_array($elem)) array_push($results, +$depth);

          // 4.3.2] Если $elem массив
          else array_push($results, $recur($elem, +$depth+1));

        }

        // 4.4] Вернуть максимальное из $results
        return max($results);

      };

      // 5] Вернуть результат
      return +$result + +$recur($array);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_countdim: '.$e->getMessage(), ['r1_countdim']);
      return 0;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_countdim, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_countdim, поскольку такая уже есть!', ['R1','r1_countdim']);
  }


  //---------------//
  // r1_config_set //
  //---------------//
	if(!function_exists('r1_config_set')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Изменить значение любого параметра в любом конфиге laravel из каталога config.
     *    Возвращает: 1 (в случае успеха) / 0 (в случае неудачи).
     *    Может работать со значениями любых типов (в т.ч. массивами).
     *    Новое значение должно быть того же типа, что и старое, иначе ошибка.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Изменим значение параметра locale конфига app на "en"
     *    r1_config_set("app.locale", "en");  // 1
     *  </pre>
     *
     * @param  string $option
     * @param  string $value2set
     *
		 * @return string
		 */
    function r1_config_set($option, $value2set)
    { try {

      // 1. Создать новую FS относительно каталога config
      $fs = r1_fs('config');

      // 2. Разобрать $option на составляющие
      //
      //  $option_arr[0]   | имя конфига
      //  $option_arr[1]   | имя опции в конфиге
      //  $option_arr[2..] | для опций-массивов, имена ключей массивов
      //
      $option_arr = explode('.',$option);
      if(!array_key_exists(0,$option_arr) || !array_key_exists(1,$option_arr))
        throw new \Exception('Значение параметра option не соответствует формату. Пример правильного значения: "M5.common_ison"');

      // 3. Получить значение опции
      $value = config($option_arr[0].'.'.$option_arr[1]);

      // 4. Узнать тип значения опции
      $type = call_user_func(function() USE ($value) {

        switch (gettype($value)) {
          case 'boolean':       return 'boolean';
          case 'integer':       return 'integer';
          case 'double':        return 'double';
          case 'string':        return 'string';
          case 'array':         return 'array';
          case 'object':        return 'object';
          case 'resource':      return 'resource';
          case 'NULL':          return 'NULL';
          case 'unknown type':  return 'unknown type';
          default:              return 'unknown type';
        }

      });
      if(in_array($type, ['object', 'resource', 'NULL', 'unknown type']))
        throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");

      // 5. Узнать тип значения $value2set
      $type2set = call_user_func(function() USE ($value2set) {

        switch (gettype($value2set)) {
          case 'boolean':       return 'boolean';
          case 'integer':       return 'integer';
          case 'double':        return 'double';
          case 'string':        return 'string';
          case 'array':         return 'array';
          case 'object':        return 'object';
          case 'resource':      return 'resource';
          case 'NULL':          return 'NULL';
          case 'unknown type':  return 'unknown type';
          default:              return 'unknown type';
        }

      });
      if(in_array($type2set, ['object', 'resource', 'NULL', 'unknown type']))
        throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");
      if($type != 'array' && $type !==  $type2set)
        throw new \Exception('Тип назначаемого значения должен соответствовать типу опции: '.$type);

      // 6. Если $type относится к тем, что умещаются на 1-й строке
      if(in_array($type, ['boolean', 'integer', 'double', 'string'])) {

        // 6.1. Преобразовать $value2set к его строковому эквиваленту
        switch (gettype($value2set)) {
          case 'boolean':  $value2set = $value2set ? 'true' : 'false'; break;
          case 'integer':  $value2set = ''.$value2set; break;
          case 'double':   $value2set = ''.$value2set; break;
          case 'string':   break;
        }

        // 6.2. Извлечь содержимое конфига $option_arr[0]
        $config = r1_fs('config')->get($option_arr[0].'.php');

        // 6.3. Найти и заменить в конфиге значение опции $option_arr[1]
        if(in_array($type, ['boolean', 'integer', 'double']))
          $config = preg_replace("/'".$option_arr[1]."' *=> *.*/ui", "'".$option_arr[1]."' => ".$value2set.",", $config);
        else
          $config = preg_replace("/'".$option_arr[1]."' *=> *.*/ui", "'".$option_arr[1]."' => '".$value2set."',", $config);

        // 6.4. Перезаписать $config
        r1_fs('config')->put($option_arr[0].'.php', $config);

      }

      // 7. Если $type - массив
      if(in_array($type, ['array'])) {

        // 7.1. Извлечь значение, которое надо изменить, и его тип
        $value2change = call_user_func(function() USE ($value, $type, $option_arr, $type2set) {

          // 1] Если в $option_arr лишь 2 элемента, вернуть $value и $type
          if(count($option_arr) == 2) return [
            "value" => $value,
            "type"  => $type
          ];

          // 2] Проверить существование искомого значения
          $arrpath = "";
          for($i=2; $i<count($option_arr); $i++) {

            // 2.1] Дополнить $arrpath
            $arrpath = $arrpath . '.' . $option_arr[$i];

            // 2.2] Проверить существование такого св-ва массива
            if(is_null(config($option_arr[0].'.'.$option_arr[1].$arrpath)))
              throw new \Exception('Попытка изменить значение несуществующего в опции-массиве свойства: '.$arrpath);

          }

          // 3] Получить искомое значение
          $res_value = config($option_arr[0].'.'.$option_arr[1].$arrpath);

          // 4] Получить тип искомого значения
          $res_type = call_user_func(function() USE ($res_value) {

            switch (gettype($res_value)) {
              case 'boolean':       return 'boolean';
              case 'integer':       return 'integer';
              case 'double':        return 'double';
              case 'string':        return 'string';
              case 'array':         return 'array';
              case 'object':        return 'object';
              case 'resource':      return 'resource';
              case 'NULL':          return 'NULL';
              case 'unknown type':  return 'unknown type';
              default:              return 'unknown type';
            }

          });
          if(in_array($res_type, ['object', 'resource', 'NULL', 'unknown type']))
            throw new \Exception("Хелпер r1_config_set не работает со значениями следующих типов: 'object', 'resource', 'NULL', 'unknown type'");
          if($type2set !== $res_type)
            throw new \Exception('Тип назначаемого значения должен соответствовать типу опции: '.$res_type);

          // 5] Вернуть результат
          return [
            "value" => $res_value,
            "type"  => $res_type
          ];

        });

        // 7.2. Узнать кол-во измерений массива config($option_arr[0].'.'.$option_arr[1].$arrpath
        $dimensions = r1_countdim(config($option_arr[0].'.'.$option_arr[1]));

        // 7.3. Если $value2change['type'] относится к следующим
        if(in_array($value2change['type'], ['boolean', 'integer', 'double', 'string', 'array'])) {

          // 1] Преобразовать $value2set к его строковому эквиваленту
          switch (gettype($value2set)) {
            case 'boolean':  $value2set = $value2set ? 'true' : 'false'; break;
            case 'integer':  $value2set = ''.$value2set; break;
            case 'double':   $value2set = ''.$value2set; break;
            case 'string':   break;
          }

          // 2] Извлечь содержимое конфига $option_arr[0]
          $config = r1_fs('config')->get($option_arr[0].'.php');

          // 3] Напис.функц.для получения ссылки на св-во многомерного массива
          $get_multidem_prop = function &(&$array, $keys){
            $result = &$array;
            foreach($keys as &$key) {
              $result = &$result[$key];
            }
            return $result;
          };

          // 4] Изменить значение $value
          $keys = [];
          for($i=2; $i<count($option_arr); $i++) {
            array_push($keys, $option_arr[$i]);
          }
          $newvalue = &$get_multidem_prop($value, $keys);
          $newvalue = $value2set;

          // 5] Сформировать строку для замены $value

            // 5.1] Создать новый экземпляр энкодера
            $encoder = new \Riimu\Kit\PHPEncoder\PHPEncoder();

            // 5.2] Закодировать $value
            $value = $encoder->encode($value, ['array.indent' => 2, 'array.base' => 4]);

          // 6] Найти и заменить $value в $config

            // 6.1] Сформировать регулярное выражение
            $regex = "/'" . $option_arr[1] . "' *=> *\[.*\]";

            // 6.2] Учесть кол-во измерений в массиве
            if(+$dimensions >= 2) {
              for($i=0; $i<count($dimensions) + 1; $i++) {
                $regex = $regex . '.*\]';
              }
            }

            // 6.3] Завершить регулярное выражение
            $regex = $regex . "/smuiU";

            // 6.4] Заменить
            $config = preg_replace($regex, "'".$option_arr[1]."' => ".$value, $config);

          // 7] Перезаписать $config
          r1_fs('config')->put($option_arr[0].'.php', $config);

        }

      }

      // n] Вернуть ответ
      return 1;

    }
    catch(\Exception $e) {
      write2log('При попытке назначить новое значение опции M5.common_ison возникла ошибка: '.$e->getMessage(), ['r1_config_set']);
      return 0;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_config_set, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_config_set, поскольку такая уже есть!', ['R1','r1_config_set']);
  }


  //----------//
  // r1_query //
  //----------//
	if(!function_exists('r1_query')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Хелпер для безопасного осуществления транс-пакетных запросов.
     *    Возвращает NULL в случае неудачи.
     *    Возвращает результат запроса в случае успеха.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Запросим коллекцию всех пакетов приложения у пакета M1:
     *    $packages = r1_query(function(){
     *      return \M1\Models\MD2_packages::all();
     *    });  // NULL или коллекция пакетов
     *  </pre>
     *
     * @param  string $callback
     *
		 * @return object
		 */
    function r1_query($callback)
    { try {

      $result = call_user_func($callback);
      return $result;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_query: '.$e->getMessage(), ['r1_query']);
      return NULL;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_query, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_query, поскольку такая уже есть!', ['R1','r1_query']);
  }


  //-----------//
  // r1_isJSON //
  //-----------//
	if(!function_exists('r1_isJSON')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Является ли переданная строка валидным JSON
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $maybe_json = '{"name":"Ivan"}';
     *    $is_json = r1_isJSON($maybe_json);  // true
     *  </pre>
     *
		 * Является ли переданная строка валидным JSON
     *
     * @param  string $string
     *
		 * @return object
		 */
    function r1_isJSON($string)
    { try {

      json_decode($string);
      return (json_last_error() == JSON_ERROR_NONE);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_isJSON: '.$e->getMessage(), ['r1_isJSON']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_isJSON, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_isJSON, поскольку такая уже есть!', ['R1','r1_isJSON']);
  }


  //---------------------//
  // r1_is_schema_exists //
  //---------------------//
	if(!function_exists('r1_is_schema_exists')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Проверить, существует ли в текущем подключении указанная БД
     *    Внимание! Проверка регистро-зависимая!
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $schema = "m1";
     *    $is_schema_exists = r1_is_schema_exists($schema);  // true
     *  </pre>
     *
     * @param  string $schema
     *
		 * @return bool
		 */
    function r1_is_schema_exists($schema)
    { try {

      $check = \DB::SELECT("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$schema."'");
      if(empty($check)) return false;
      return true;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_is_schema_exists: '.$e->getMessage(), ['r1_is_schema_exists']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_is_schema_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_is_schema_exists, поскольку такая уже есть!', ['R1','r1_is_schema_exists']);
  }


  //-------------//
  // r1_hasTable //
  //-------------//
	if(!function_exists('r1_hasTable')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Проверить, существует ли таблица $table_name в БД $db_name, в текущем подключении
     *    Внимание! Не путать таблицу с моделью! Проверка регистро-зависима!
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $schema = "m1";
     *    $table  = "md2_packages";
     *    $result  = r1_hasTable($schema, $table);
     *  </pre>
     *
		 * Проверить наличие таблицы в указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
     *
		 * @return bool
		 */
    function r1_hasTable($db_name, $table_name)
    { try {

			// Проверить
			$exists = DB::table('information_schema.tables')
					->where('table_schema','=',$db_name)
					->where('table_name','=',$table_name)
					->first();

			// Вернуть результат
			return !empty($exists);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_hasTable: '.$e->getMessage(), ['r1_hasTable']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_hasTable, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_hasTable, поскольку такая уже есть!', ['R1','r1_hasTable']);
  }


  //--------------//
  // r1_hasColumn //
  //--------------//
	if(!function_exists('r1_hasColumn')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Проверить, существует ли столбец $column_name таблице $table_name в БД $db_name, в текущем подключении
     *    Внимание! Не путать таблицу с моделью! Проверка регистро-зависима!
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $schema  = "m1";
     *    $table   = "md2_packages";
     *    $column  = "deleted_at";
     *    $result  = r1_hasColumn($schema, $table, $column);
     *  </pre>
     *
		 * Проверить наличие столбца в указанной таблице в указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
		 * @param  string $column_name
     *
		 * @return bool
		 */
    function r1_hasColumn($db_name, $table_name, $column_name)
    { try {

			// Проверить
			$exists = DB::table('information_schema.columns')
						->where('table_schema','=',$db_name)
						->where('table_name','=',$table_name)
						->where('column_name','=',$column_name)
						->first();

			// Вернуть результат
			return !empty($exists);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_hasColumn: '.$e->getMessage(), ['r1_hasColumn']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_hasColumn, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_hasColumn, поскольку такая уже есть!', ['R1','r1_hasColumn']);
  }


  //---------------//
  // r1_getColumns //
  //---------------//
	if(!function_exists('r1_getColumns')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить список имён столбцов из таблицы $table_name БД $db_name текущего подключения
     *    Возвращает NULL в случае неудачи.
     *    Или массив с именами столбцов в случае успеха.
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $schema  = "m1";
     *    $table   = "md2_packages";
     *    $columns = r1_getColumns($schema, $table);
     *  </pre>
     *
		 * Получить список имён столбцов из указанной таблицы указанной БД
     *
		 * @param  string $db_name
		 * @param  string $table_name
     *
		 * @return bool
		 */
    function r1_getColumns($db_name, $table_name)
    { try {

      // Получить
      $columns = DB::SELECT("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$db_name."' AND TABLE_NAME = '".$table_name."'");

      // Отвильтровать
      $columns = array_map(function($item){
        return $item->COLUMN_NAME;
      }, $columns);

			// Вернуть результат
			return $columns;

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_getColumns: '.$e->getMessage(), ['r1_getColumns']);
      return NULL;
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_getColumns, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_getColumns, поскольку такая уже есть!', ['R1','r1_getColumns']);
  }


  //---------------//
  // r1_rel_exists //
  //---------------//
	if(!function_exists('r1_rel_exists')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Проверить существование связи $relation у модели $model M-пакета $packid
     *    Возвращает: true / false
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    Сущетсвует ли связь "m4_routes" в модели "MD2_packages" в M-пакете "m1":
     *    r1_rel_exists("m1","md2_packages","m4_routes");
     *  </pre>
     *
		 * @param  string $packid
		 * @param  string $model
		 * @param  string $relation
     *
		 * @return bool
		 */
    function r1_rel_exists($packid, $model, $relation)
    { try {

      // 1. Провести валидацию
      $validator = r4_validate(["packid"=>$packid,"model"=>$model,"relation"=>$relation], [

        "packid"      => ["required", "regex:/^M[1-9]{1}[0-9]*$/ui"],
        "model"       => ["required", "regex:/^MD[1-9]{1}[0-9]*_/ui"],
        "relation"    => ["required"]

      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Обработать некоторые аргументы
      $packid = mb_strtoupper($packid);
      $model = preg_replace("/^md/ui", "MD", $model);

      // 3. Вернуть результат
      return method_exists("\\".$packid."\\Models\\".$model, $relation);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_rel_exists: '.$e->getMessage(), ['r1_rel_exists']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_rel_exists, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_rel_exists, поскольку такая уже есть!', ['R1','r1_rel_exists']);
  }


  //-------------//
  // r1_checksum //
  //-------------//
	if(!function_exists('r1_checksum')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Получить контрольную сумму для файла или каталога по заданному path
     *    Возвращает: контрольную сумму
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $path       = "/c/some/dir";
     *    $checksum   = r1_checksum($path);
     *  </pre>
     *
		 * @param  string $path
     *
		 * @return mixed
		 */
    function r1_checksum($path)
    { try {

      // 1. Если по адресу $path нет ни файла, ни папки, вернуть пустую строку
      if(!file_exists($path)) return "";

      // 2. Если по адресу $path находится файл, вернуть его md5-хэш
      if(is_file($path)) return md5_file($path);

      // 3. Если по адресу $path находится каталог, вернуть сумму хэшей его файлов
      $md5_dir = function($path) USE (&$md5_dir) {

        $filemd5s = array();
        $d = dir($path);

        while (false !== ($entry = $d->read()))
        {

            if ($entry != '.' && $entry != '..')
            {
                 if (is_dir($path.'/'.$entry))
                 {
                     $filemd5s[] = $md5_dir($path.'/'.$entry);
                 }
                 else
                 {
                     $filemd5s[] = md5_file($path.'/'.$entry);
                 }
             }
        }

        $d->close();
        return md5(implode('', $filemd5s));

      };
      return $md5_dir($path);

    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_checksum: '.$e->getMessage(), ['r1_checksum']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_checksum, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_checksum, поскольку такая уже есть!', ['R1','r1_checksum']);
  }


  //-----------------//
  // r1_encrypt_data //
  //-----------------//
	if(!function_exists('r1_encrypt_data')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Зашифровать $text указанным $key
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $crypttext = r1_encrypt_data($key,$text);
     *  </pre>
     *
		 * @param  string $key
		 * @param  string $text
     *
		 * @return mixed
		 */
    function r1_encrypt_data($text, $key)
    { try {
      $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
      $encrypted_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
      return base64_encode($encrypted_text);
    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_encrypt_data: '.$e->getMessage(), ['r1_encrypt_data']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_encrypt_data, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_encrypt_data, поскольку такая уже есть!', ['R1','r1_encrypt_data']);
  }


  //-----------------//
  // r1_decrypt_data //
  //-----------------//
	if(!function_exists('r1_decrypt_data')) {
		/**
     *  <h1>Описание</h1>
     *  <pre>
     *    Расшифровать $text указанным $key
     *  </pre>
     *  <h1>Пример использования</h1>
     *  <pre>
     *    $crypttext = r1_decrypt_data($key,$text);
     *  </pre>
     *
		 * @param  string $key
		 * @param  string $text
     *
		 * @return mixed
		 */
    function r1_decrypt_data($text, $key)
    { try {
      $text = base64_decode($text);
      $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
      $decrypted_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
      return rtrim($decrypted_text, "\0");
    } catch(\Exception $e) {
      write2log('Ошибка в хелпере r1_decrypt_data: '.$e->getMessage(), ['r1_decrypt_data']);
    }}
	} else {
    \Log::info('Внимание! Пакету R1 не удалось определить функцию r1_decrypt_data, поскольку такая уже есть!');
    write2log('Внимание! Пакету R1 не удалось определить функцию r1_decrypt_data, поскольку такая уже есть!', ['R1','r1_decrypt_data']);
  }











