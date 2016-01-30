<?php
////======================================================////
////																										  ////
////                 Обработчик M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Returns data about packages and commands for M5
 *
 *  Какое событие обрабатывает
 *  --------------------------
 *    - \R2\Event
 *
 *  На какие ключи реагирует
 *  ------------------------
 *    - ["m5:call4update"]
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data": [
 *        keys          // Массив ключей события
 *        data          // Данные, переданные с событием
 *      ]
 *    ]
 *
 *  Что ожидает увидеть в data
 *  --------------------------
 *
 *    [
 *
 *    ]
 *
 *  Формат возвращаемого значения
 *  -----------------------------
 *    - Если ни 1 key из keys события не подходит, ничего не возвращает.
 *    - Если хоть 1 key из keys события подходит, возвращает:
 *
 *      [
 *        status          // 0 - всё ОК, -2 - ошибка
 *        data            // результат выполнения команды
 *      ]
 *
 *  Значение data в зависимости от статуса
 *  --------------------------------------
 *
 *    status == 0
 *    -----------
 *      - ""
 *
 *    status = -2
 *    -----------
 *      - Текст ошибки. Может заменяться на "" в контроллерах (чтобы скрыть от клиента).
 *
 */

//-------------------//
// Пространство имён //
//-------------------//
// - Пример:  M1\EventHandlers

  namespace M1\EventHandlers;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Трейт, дающий доступ к методам delete и release
  use Illuminate\Queue\InteractsWithQueue;

  // Контракт, позволяющий добавлять обработчик в очередь
  use Illuminate\Contracts\Queue\ShouldQueue;

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

//------------//
// Обработчик //
//------------//

////===================================================////
class H2_fresh4m5  // TODO: написать "implements ShouldQueue", и тогда обработчик будет добавляться в очередь задач, а не исполняться в реалтайм
{

  //------------------------------------//
  // А. Конструктор обработчика событий //
  //------------------------------------//
  public function __construct()
  {

  }

  //-------------------------------//
  // Б. Функция-обработчик события //
  //-------------------------------//
  public function handle(\R2\Event $event) // принять обрабатываемый объект-событие
  {

  /**
   * Оглавление
   *
   *  A. Проверить ключи и получить входящие данные
   *
   *  1.
   *  2.
   *
   *  X. Вернуть результат
   *
   */

    //--------------------//
    // A. Проверить ключи //
    //--------------------//
    try {

      // 1.1. Получить ключи, присланные с событием
      $eventkeys = $event->data['keys'];

      // 1.2. Получить ключи, поддерживаемые обработчиком
      $handlerkeys = ["m5:call4update"];

      // 1.3. Если ни один ключ не подходит, завершить
      $testkeys = array_intersect($handlerkeys, $eventkeys);
      if(empty($testkeys)) return;

      // 1.4. Получить входящие данные
      $data = $event->data;

    } catch(\Exception $e) {
      $errortext = 'Keys checking in event handler H2_fresh4m5 of M-package M1 have ended with error: '.$e->getMessage();
      Log::info($errortext);
      write2log($errortext, ['M1', 'H2_fresh4m5']);
      return [
        "status"  => -2,
        "data"    => $errortext
      ];
    }

    //-------------------------//
    // Вернуть данные из БД M1 //
    //-------------------------//
    $res = call_user_func(function() USE ($event) { try {

      // 1] Подготовить массив данных для отправки с событием
      $data = [];

      // 2] Сформировать массив данных для отправки с событием

        // 2.1] MD1_packtypes
        $data['packtypes'] = json_encode(\M1\Models\MD1_packtypes::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.2] MD2_packages
        $data['packages'] = json_encode(\M1\Models\MD2_packages::with([
          'dependencies',
          'locales',
          'locale',
          'packtype',
          'models',
          'commands',
          'consoles',
          'handlers',
        ])->get(), JSON_UNESCAPED_UNICODE);

        // 2.3] MD3_models
        $data['models'] = json_encode(\M1\Models\MD3_models::with([
          'package',
        ])->get(),JSON_UNESCAPED_UNICODE);

        // 2.4] MD4_locales
        $data['locales'] = json_encode(\M1\Models\MD4_locales::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.8] MD5_commands
        $data['commands'] = json_encode(\M1\Models\MD5_commands::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.9] MD6_console
        $data['console'] = json_encode(\M1\Models\MD6_console::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.10] MD7_handlers
        $data['handlers'] = json_encode(\M1\Models\MD7_handlers::query()->get(), JSON_UNESCAPED_UNICODE);

      // 3] Вернуть результат
      return [
        "status"    => 0,
        "packages"  => $data
      ];

    } catch(\Exception $e) {
        DB::rollback();
        $errortext = 'Invoking of event handler H2_fresh4m5 of M-package M1 have ended with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M1', 'H2_fresh4m5']);
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;


    //----------------------//
    // X. Вернуть результат //
    //----------------------//
    return [
      "status"  => 0,
      "data"    => ""
    ];


  }

}