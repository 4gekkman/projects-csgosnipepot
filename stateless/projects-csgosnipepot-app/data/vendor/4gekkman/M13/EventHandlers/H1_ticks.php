<?php
////======================================================////
////																										  ////
////                 Обработчик M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Handles ticks from M11
 *
 *  Какое событие обрабатывает
 *  --------------------------
 *    - \R2\Event
 *
 *  На какие ключи реагирует
 *  ------------------------
 *    - ["m11:tick"]
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

  namespace M13\EventHandlers;

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
class H1_ticks  // TODO: написать "implements ShouldQueue", и тогда обработчик будет добавляться в очередь задач, а не исполняться в реалтайм
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
      $handlerkeys = ["m11:tick"];

      // 1.3. Если ни один ключ не подходит, завершить
      $testkeys = array_intersect($handlerkeys, $eventkeys);
      if(empty($testkeys)) return;

      // 1.4. Получить входящие данные
      $data = $event->data;

    } catch(\Exception $e) {
      $errortext = 'Keys checking in event handler H1_ticks of M-package M13 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
      Log::info($errortext);
      write2log($errortext, ['M13', 'H1_ticks']);
      return [
        "status"  => -2,
        "data"    => $errortext
      ];
    }

    //-------------------------//
    // Отлавливать тики из M11 //
    //-------------------------//
    $res = call_user_func(function() USE ($event) { try {

      // 1. Процессинг системы депозита, производить не чаще, чем раз в 10 секунд
      call_user_func(function(){

        // 1.1. Получить из кэша кол-во прошедших с прошлого процессинга секунд
        $cache = Cache::get('m13:processing:last_datetime');
        if(empty($cache))
          $cache = 0;

        // 1.2. Прибавить к $cache единицу
        $cache = +$cache + 1;

        // 1.3. Записать обновлённый $cache в кэш
        Cache::put('m13:processing:last_datetime', $cache, 30);

        // 1.4. Если $cache >= 10 секундам, запустить C5_processor
        if($cache >= 10) {

          // 1] Запустить процессор
          $result = runcommand('\M13\Commands\C5_processor', [], 0, ['on'=>true, 'name'=>'m13_processor']);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Сбросить кэш в 0
          Cache::put('m13:processing:last_datetime', 0, 30);

        }

      });

    } catch(\Exception $e) {
        $errortext = 'Invoking of event handler H1_ticks of M-package M13 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M13', 'H1_ticks']);
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