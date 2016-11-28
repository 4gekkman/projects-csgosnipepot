<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Cache updating for wins processing
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        all           | True/False, если true, то обновить весь кэш
 *        cache2update  | Массив ключей кэша, который надо обновить (нужен, только если all не указано)
 *        force         | (по умолчанию, == true) Обновлять кэш, даже если он присутствует
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

  namespace M9\Commands;

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
class C25_update_wins_cache extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Принять и проверить входящие данные
     *  2. Назначить значения по умолчанию
     *  3. Обновить кэш, который указан в cache2update
     *    3.1. processing:wins:active + processing:wins:active:<id пользователя>
     *    3.2. processing:wins:not_paid_expired + processing:wins:not_paid_expired:<id пользователя>
     *    3.3. processing:wins:paid + processing:wins:paid:<id пользователя>
     *    3.4. processing:wins:expired + processing:wins:expired:<id пользователя>
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------//
    // Обновляет указанный кэш в рамках процессинга игры //
    //---------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Принять и проверить входящие данные
      $validator = r4_validate($this->data, [
        "all"             => ["boolean"],
        "cache2update"    => ["required_without:all", "array"],
        "force"           => ["boolean"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Назначить значения по умолчанию

        // 2.1. Если all не передано, задать ей значение по умолчанию false
        if(!array_key_exists('all', $this->data))
          $this->data['all'] = false;

        // 2.2. Если cache2update не передан, назначить пустой массив
        if(!array_key_exists('cache2update', $this->data))
          $this->data['cache2update'] = [];

        // 2.3. Если force отсутствует, назначить true
        if(!array_key_exists('force', $this->data))
          $this->data['force'] = true;

      // 3. Обновить кэш, который указан в cache2update

        // 3.1. processing:wins:active + processing:wins:active:<id пользователя>

          // 3.1.1. Получить кэш
          $cache = json_decode(Cache::get('processing:wins:active'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:wins:active') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:wins:active", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все выигрыши со статусом Active
              // - Включая все их связи.
              $active_wins = \M9\Models\MD4_wins::with(["debts","rounds","rounds.rooms","wins_statuses","m5_users","m8_items","m8_bots","safecodes"])
                ->whereHas('wins_statuses', function($query){
                  $query->where('status', 'Active');
                })
                ->get();

              // 2] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$active_wins) {

                // 2.1] Записать JSON с $active_wins в кэш
                Cache::put('processing:wins:active', json_encode($active_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 2.2] Пробежаться по $cache, и записать индивидуальный кэш активных выигрышей
                // - У каждого пользователя может быть одновременно лишь 1 активный выигрыш.
                foreach($active_wins as $win) {
                  $id_user = $win['m5_users'][0]['id'];
                  Cache::tags(['processing:wins:active:personal'])->put('processing:wins:active:'.$id_user, json_encode($win, JSON_UNESCAPED_UNICODE), 30);
                }

                  //  // 4.1] Получить не повторяющийся список ID пользователей, которые есть в $active_wins
                  //  $users_ids = call_user_func(function() USE ($active_wins) {
                  //    $result = [];
                  //    foreach($active_wins as $win) {
                  //      $id_user = $win['m5_users'][0]['id'];
                  //      if(!in_array($id_user, $result))
                  //        array_push($result, $id_user);
                  //    }
                  //    return $result;
                  //  });
                  //
                  //  // 4.2] Получить для каждого $users_ids свой массив с активными офферами выигрышей
                  //  $users_active_wins = call_user_func(function() USE ($users_ids, $active_wins) {
                  //    $result = [];
                  //    foreach($users_ids as $id) {
                  //      foreach($active_wins as $win) {
                  //        $id_user = $win['m5_users'][0]['id'];
                  //        if($id_user == $id) {
                  //          if(!array_key_exists($id_user, $result)) $result[$id_user] = [];
                  //          array_push($result[$id_user], $win);
                  //        }
                  //      }
                  //    }
                  //    return $result;
                  //  });
                  //
                  //  // 4.3] Записать $users_active_bets в кэш
                  //  foreach($users_active_wins as $id_user => $wins) {
                  //    Cache::tags(['processing:wins:active:personal'])->put('processing:wins:active:'.$id_user, json_encode($wins, JSON_UNESCAPED_UNICODE), 30);
                  //  }

                // 2.3] Если $active_wins пуст, сбросить весь персонализированный кэш
                if(count($active_wins) == 0) {
                  Cache::tags(['processing:wins:active:personal'])->flush();
                }

              });

              // 3] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$active_wins) {

                // 3.1] Получить версию коллекции $active_wins с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • Для m8_bots оставить только id и pivot.
                //
                $active_wins->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [
                    [
                      "id"    => $value_arr['m8_bots'][0]['id'],
                      "pivot" => $value_arr['m8_bots'][0]['pivot'],
                    ]
                  ];

                  // 2) Вернуть $value_arr
                  return $value_arr;

                });

                // 3.2] Записать JSON с $active_wins в кэш
                Cache::put('processing:wins:active:safe', json_encode($active_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3.3] Пробежаться по $cache, и записать индивидуальный кэш активных выигрышей
                // - У каждого пользователя может быть одновременно лишь 1 активный выигрыш.
                foreach($active_wins as $win) {
                  $id_user = $win['m5_users'][0]['id'];
                  Cache::tags(['processing:wins:active:personal:safe'])->put('processing:wins:active:safe:'.$id_user, json_encode($win, JSON_UNESCAPED_UNICODE), 30);
                }

                // 3.4] Если $active_wins пуст, сбросить весь персонализированный кэш
                if(count($active_wins) == 0) {
                  Cache::tags(['processing:wins:active:personal:safe'])->flush();
                }

              });

            }

          }

        // 3.2. processing:wins:not_paid_expired + processing:wins:not_paid_expired:<id пользователя>

          // 3.2.1. Получить кэш
          $cache = json_decode(Cache::get('processing:wins:not_paid_expired'), true);

          // 3.1.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:wins:not_paid_expired') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:wins:not_paid_expired", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все выигрыши со статусами кроме Paid и Expired
              // - Включая все их связи.
              $wins = \M9\Models\MD4_wins::with(["debts","rounds","rounds.rooms","wins_statuses","m5_users","m8_items","m8_bots","safecodes"])
                ->whereDoesntHave('wins_statuses', function($query){
                  $query->where('status', 'Paid')
                    ->orWhere('status', 'Expired');
                })
                ->get();

              // 2] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$wins) {

                // 2.1] Записать JSON с $wins в кэш
                Cache::put('processing:wins:not_paid_expired', json_encode($wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 2.2] Пробежаться по $cache, и записать индивидуальный кэш не-paid-expired выигрышей
                Cache::tags(['processing:wins:not_paid_expired:personal'])->flush();
                foreach($wins as $win) {

                  // 2.2.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 2.2.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:not_paid_expired:personal'])->get('processing:wins:not_paid_expired:'.$id_user), true) ?: [];

                  // 2.2.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 2.2.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:not_paid_expired:personal'])->put('processing:wins:not_paid_expired:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 2.3] Если $wins пуст, сбросить весь персонализированный кэш
                if(count($wins) == 0) {
                  Cache::tags(['processing:wins:not_paid_expired:personal'])->flush();
                }

              });

              // 3] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$wins) {

                // 3.1] Получить версию коллекции $wins с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • Полностью m8_bots.
                //
                $wins->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [];

                  // 2) Вернуть $value_arr
                  return $value_arr;

                });

                // 3.2] Записать JSON с $wins в кэш
                Cache::put('processing:wins:not_paid_expired:safe', json_encode($wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3.3] Пробежаться по $cache, и записать индивидуальный кэш не-paid-expired выигрышей
                Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->flush();
                foreach($wins as $win) {

                  // 3.3.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 3.3.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->get('processing:wins:not_paid_expired:'.$id_user), true) ?: [];

                  // 3.3.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 3.3.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->put('processing:wins:not_paid_expired:safe:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 3.4] Если $wins пуст, сбросить весь персонализированный кэш
                if(count($wins) == 0) {
                  Cache::tags(['processing:wins:not_paid_expired:personal:safe'])->flush();
                }

              });

            }

          }

        // 3.3. processing:wins:paid + processing:wins:paid:<id пользователя>

          // 3.3.1. Получить кэш
          $cache = json_decode(Cache::get('processing:wins:paid'), true);

          // 3.3.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:wins:paid') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:wins:paid", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все выигрыши со статусом Paid
              // - Включая все их связи.
              $paid_wins = \M9\Models\MD4_wins::with(["debts","rounds","rounds.rooms","wins_statuses","m5_users","m8_items","m8_bots","safecodes"])
                ->whereHas('wins_statuses', function($query){
                  $query->where('status', 'Paid');
                })
                ->get();

              // 2] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$paid_wins) {

                // 2.1] Записать JSON с $paid_wins в кэш
                Cache::put('processing:wins:paid', json_encode($paid_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 2.2] Пробежаться по $cache, и записать индивидуальный кэш для paid-выигрышей
                Cache::tags(['processing:wins:paid:personal'])->flush();
                foreach($paid_wins as $win) {

                  // 2.2.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 2.2.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:paid:personal'])->get('processing:wins:paid:'.$id_user), true) ?: [];

                  // 2.2.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 2.2.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:paid:personal'])->put('processing:wins:not_paid_expired:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 2.3] Если $paid_wins пуст, сбросить весь персонализированный кэш
                if(count($paid_wins) == 0) {
                  Cache::tags(['processing:wins:paid:personal'])->flush();
                }

              });

              // 3] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$paid_wins) {

                // 3.1] Получить версию коллекции $paid_wins с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • Полностью m8_bots.
                //
                $paid_wins->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [];

                  // 2) Вернуть $value_arr
                  return $value_arr;

                });

                // 3.2] Записать JSON с $paid_wins в кэш
                Cache::put('processing:wins:paid:safe', json_encode($paid_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3.3] Пробежаться по $cache, и записать индивидуальный кэш для paid-выигрышей
                Cache::tags(['processing:wins:paid:personal:safe'])->flush();
                foreach($paid_wins as $win) {

                  // 3.3.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 3.3.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:paid:personal:safe'])->get('processing:wins:paid:safe:'.$id_user), true) ?: [];

                  // 3.3.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 3.3.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:paid:personal:safe'])->put('processing:wins:not_paid_expired:safe:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 3.4] Если $paid_wins пуст, сбросить весь персонализированный кэш
                if(count($paid_wins) == 0) {
                  Cache::tags(['processing:wins:paid:personal:safe'])->flush();
                }

              });

            }

          }

        // 3.4. processing:wins:expired + processing:wins:expired:<id пользователя>

          // 3.4.1. Получить кэш
          $cache = json_decode(Cache::get('processing:wins:expired'), true);

          // 3.4.2. Обновить кэш
          // - Если он отсутствует, или если параметро force == true
          if(
            (!Cache::has('processing:wins:expired') || empty($cache) || count($cache) == 0) ||
            $this->data['force'] == true
          ) {

            // Обновить этот кэш, если в параметрах указано, что его надо обновить
            if(in_array("processing:wins:expired", $this->data['cache2update']) == true || $this->data['all'] == true) {

              // 1] Получить все выигрыши со статусом Expired
              // - Включая все их связи.
              $expired_wins = \M9\Models\MD4_wins::with(["debts","rounds","rounds.rooms","wins_statuses","m5_users","m8_items","m8_bots","safecodes"])
                ->whereHas('wins_statuses', function($query){
                  $query->where('status', 'Expired');
                })
                ->get();

              // 2] Обновить полную (не safe) версию кэша
              call_user_func(function() USE (&$expired_wins){

                // 2.1] Записать JSON с $expired_wins в кэш
                Cache::put('processing:wins:expired', json_encode($expired_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 2.2] Пробежаться по $cache, и записать индивидуальный кэш для expired-выигрышей
                Cache::tags(['processing:wins:expired:personal'])->flush();
                foreach($expired_wins as $win) {

                  // 2.2.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 2.2.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:expired:personal'])->get('processing:wins:expired:'.$id_user), true) ?: [];

                  // 2.2.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 2.2.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:expired:personal'])->put('processing:wins:not_expired_expired:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 2.3] Если $expired_wins пуст, сбросить весь персонализированный кэш
                if(count($expired_wins) == 0) {
                  Cache::tags(['processing:wins:expired:personal'])->flush();
                }

              });

              // 3] Обновить безопасную (safe) версию кэша
              call_user_func(function() USE (&$expired_wins){

                // 3.1] Получить версию коллекции $expired_wins с отфильтрованной секретной информацией
                // - Что должно быть вырезано цензурой:
                //
                //    • Полностью m8_bots.
                //
                $expired_wins->transform(function($value, $key){

                  // 1) Удалить m8_bots из $value
                  $value_arr = $value->toArray();
                  $value_arr['m8_bots'] = [];

                  // 2) Вернуть $value_arr
                  return $value_arr;

                });

                // 3.2] Записать JSON с $expired_wins в кэш
                Cache::put('processing:wins:expired:safe', json_encode($expired_wins->toArray(), JSON_UNESCAPED_UNICODE), 30);

                // 3.3] Пробежаться по $cache, и записать индивидуальный кэш для expired-выигрышей
                Cache::tags(['processing:wins:expired:personal:safe'])->flush();
                foreach($expired_wins as $win) {

                  // 2.3.1] Получить ID пользователя
                  $id_user = $win['m5_users'][0]['id'];

                  // 2.3.2] Получить его текущий кэш
                  $curcache = json_decode(Cache::tags(['processing:wins:expired:personal:safe'])->get('processing:wins:expired:safe:'.$id_user), true) ?: [];

                  // 2.3.3] Добавить $win в $curcache
                  array_push($curcache, $win);

                  // 2.3.4] Засунуть $curcache в кэш
                  Cache::tags(['processing:wins:expired:personal:safe'])->put('processing:wins:not_expired_expired:safe:'.$id_user, json_encode($curcache, JSON_UNESCAPED_UNICODE), 30);

                }

                // 3.4] Если $expired_wins пуст, сбросить весь персонализированный кэш
                if(count($expired_wins) == 0) {
                  Cache::tags(['processing:wins:expired:personal:safe'])->flush();
                }

              });

            }

          }


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C25_update_wins_cache from M-package M9 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M9', 'C25_update_wins_cache']);
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

