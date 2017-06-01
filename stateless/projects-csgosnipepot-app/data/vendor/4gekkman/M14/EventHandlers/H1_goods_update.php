<?php
////======================================================////
////																										  ////
////                 Обработчик M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Handles m14 goods update event and invoke all_goods_update command
 *
 *  Какое событие обрабатывает
 *  --------------------------
 *    - \R2\Event
 *
 *  На какие ключи реагирует
 *  ------------------------
 *    - ["m14:goods:update"]
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

  namespace M14\EventHandlers;

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
class H1_goods_update  // TODO: написать "implements ShouldQueue", и тогда обработчик будет добавляться в очередь задач, а не исполняться в реалтайм
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
   *  1. Добавить команду all_goods_update в очередь m14_goods_update_queue
   *
   *  X. Вернуть результат
   *
   */

    //--------------------//
    // A. Проверить ключи //
    //--------------------//
    try {

      // 1] Получить ключи, присланные с событием
      $eventkeys = $event->data['keys'];

      // 2] Получить ключи, поддерживаемые обработчиком
      $handlerkeys = ["m14:goods:update"];

      // 3] Если ни один ключ не подходит, завершить
      $testkeys = array_intersect($handlerkeys, $eventkeys);
      if(empty($testkeys)) return;

      // 4] Получить входящие данные
      $data = $event->data;

    } catch(\Exception $e) {
      $errortext = 'Keys checking in event handler H1_goods_update of M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
      Log::info($errortext);
      write2log($errortext, ['M14', 'H1_goods_update']);
      return [
        "status"  => -2,
        "data"    => $errortext
      ];
    }

    //--------------------------------------------------------------------//
    // Handles m14 goods update event and invoke all_goods_update command //
    //--------------------------------------------------------------------//
    $res = call_user_func(function() USE ($event) { try {

      // 1. Добавить команду all_goods_update в очередь m14_goods_update_queue
      runcommand('\M14\Commands\C3_all_goods_update', [

      ], 0, ['on'=>true, 'delaysecs'=>'', 'name' => 'm14_goods_update_queue']);

    } catch(\Exception $e) {
        DB::rollback();
        $errortext = 'Invoking of event handler H1_goods_update of M-package M14 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M14', 'H1_goods_update']);
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