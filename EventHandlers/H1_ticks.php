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

  namespace M9\EventHandlers;

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
   *    Канал                     Очередь                     Описание
   *    ------------------------------------------------------------------------------------------
   *    m9:servertime           | broadcastworkers          | 1. Трансляция серверного время всем клиентам
   *
   *
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
      $errortext = 'Keys checking in event handler H1_ticks of M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
      Log::info($errortext);
      write2log($errortext, ['M9', 'H1_ticks']);
      return [
        "status"  => -2,
        "data"    => $errortext
      ];
    }

    //-------------------------//
    // Отлавливать тики из M11 //
    //-------------------------//
    $res = call_user_func(function() USE ($event) { try {

      // a. Если игра стоит на паузе, завершить
      $cache = Cache::get('lottery:pause');
      if(!empty($cache) && $cache == 1)
        return;

      Log::info('tick');

      // 1. Трансляция серверного время всем клиентам
      Event::fire(new \R2\Broadcast([
        'channels' => ['m9:servertime'],
        'queue'    => 'broadcastworkers',
        'data'     => [
          'secs' => \Carbon\Carbon::now()->toDateTimeString()
        ]
      ]));

      // 2. Процессинг игры "Лоттерея"
      $result = runcommand('\M9\Commands\C11_processor', [], 0, ['on'=>true, 'name'=>'processor_main']);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 3. Процессинг выигрышей игры "Лоттерея"
      $result = runcommand('\M9\Commands\C24_processor_wins', [], 0, ['on'=>true, 'name'=>'processor_wins_main']);
      if($result['status'] != 0)
        throw new \Exception($result['data']['errormsg']);

      // 4. Если от C11_processor ничего не слышно более 10 секунд
      // - То, вероятно, он "замёрз".
      // - Поэтому, найти и прибить его, а supervisord его сам перезапустит.
      (call_user_func(function(){

        // 1] Получить предыдущие дату/время выполнения C11_processor
        $prev = Cache::get('m9:processing:prev_datetime');
        $prev = !empty($prev) ? \Carbon\Carbon::parse($prev) : "";

        // 2] Получить текущие дату/время выполнения C11_processor
        $last = Cache::get('m9:processing:last_datetime');
        $last = !empty($last) ? \Carbon\Carbon::parse($last) : "";

        // 3] Получить текущие дату и время
        $cur = \Carbon\Carbon::now();

        // Log::info($prev);
        // Log::info($cur);

        // 4] Если $prev или $last отсутствуют, завершить
        if(empty($prev) || empty($last)) return;

        Log::info('diff = '.$cur->diffInSeconds($last));

        // 5] Если разница между $last и $cur более 20 секунд
        // - Перезапустить все queue worker-ы.
        // - Записать в кэш текущие дату/время, чтобы оно не перезапускалось ежесекундно, а лишь раз в 20 сек.
        if($cur->diffInSeconds($last) > 20 && Cache::get('m9:processing:restarting') != '1') {

          // 5.1] Отметить в кэше, что начался процесс перезапуска
          Cache::put('m9:processing:restarting', '1', 300);

          // 5.2] Перезапустить воркеры
          Artisan::call('queue:restart');

          // 5.3] Перезапустить тики
          Artisan::call('m11:stop');
            sleep(3);
          Artisan::call('m11:start');

          // 5.4] Отметить в кэше, что процесс перезапуска закончился
          Cache::forget('m9:processing:restarting');

          // 5.5] Перезаписать кэш
          Cache::put('m9:processing:prev_datetime', Cache::get('m9:processing:last_datetime'), 300);
          Cache::put('m9:processing:last_datetime', \Carbon\Carbon::now()->toDateTimeString(), 300);

        }

      }));


    } catch(\Exception $e) {
        DB::rollback();
        $errortext = 'Invoking of event handler H1_ticks of M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M9', 'H1_ticks']);
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