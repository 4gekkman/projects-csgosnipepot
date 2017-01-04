<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Foreach thought all items in m8 db and update their quality from steamcommunity
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *
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

  namespace M8\Commands;

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
class C33_update_items_quality_indb extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Пробежаться по всем вещам в md2_items
     *    1.1. Извлечь из $item ссылку на вещь в steammarket
     *    1.2. Извлечь информацию о качестве вещи
     *    1.3. Определить качество
     *    1.4. Записать тип в БД
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------------------//
    // Пробежаться во всем вещам в M8 и обновить их качество инфой из steamcommunity //
    //-------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Подготовить функцию для осуществления запроса без proxy в виде TOR
      $get = function($url){

        // 1] Подготовить
        $result = runcommand('\M9\Commands\C37_get_request', [
          "url" => $url
        ]);
        if($result['status'] != 0) {}

        // 2] Вернуть результат
        return $result['data'];

      };

      // 2. Подготовить функцию для осуществления запроса через proxy в виде TOR
      $get_proxy = function($url) {

        // 1] Подготовить адрес proxy
        $proxy = "localhost:9050";

        // 2] Подготовить
        $result = runcommand('\M9\Commands\C38_get_request_tor', [
          "url"   => $url,
          "proxy" => $proxy
        ]);
        if($result['status'] != 0) {}

        // 3] Вернуть результат
        return $result['data'];

      };

      // 3. Подготовить функцию для обновления IP TOR'а
      $update_tor_ip = function(){

        // 1] Подготовить
        $result = runcommand('\M9\Commands\C36_update_tor_ip', []);
        if($result['status'] != 0) {}

        // 2] Вернуть результат
        return $result['data'];

      };

      // 4. Пробежаться по всем вещам в md2_items
      \M8\Models\MD2_items::query()->chunk(10, function($items) USE ($get, $get_proxy, $update_tor_ip) {
        foreach ($items as $item) {

          // 4.1. Извлечь из $item ссылку на вещь в steammarket
          $url = $item->steammarket_link;

          // 4.2. Получить html со страницы $url
          $html = call_user_func(function() USE ($url, $get, $get_proxy, $update_tor_ip) {

            // 1] Назначить таймаут в секундах
            $timeout = 60;

            // 2] Зафиксировать время в будущем, когда должен произойти таймаут
            $start = \Carbon\Carbon::now()->addSeconds($timeout);

            // 3] Пробовать извлечь html до наступления таймаута
            while($start->gte(\Carbon\Carbon::now())) {

              // 3.1] Попробовать получить html естественным путём

                // Попробовать
                $result = $get($url);

                // Провести валидацию
                $validator = r4_validate($result, [
                  "status"          => ["required", "in:200"],
                  "body"            => ["required"],
                ]);

                // Если удалось, вернуть $result
                if($validator['status'] != -1) {
                  return $result;
                }

              // 3.2] Попробовать получить html через TOR

                // Попробовать
                $result = $get_proxy($url);

                // Провести валидацию
                $validator = r4_validate($result, [
                  "status"          => ["required", "in:200"],
                  "body"            => ["required", "json"],
                ]);

                // Если удалось, вернуть $result
                if($validator['status'] != -1) {
                  return $result;
                }

                // Если не удалось, обновить IP TOR'а, и подождать 3 секунды
                else {

                  // 1] Получить дату/время последнего обновления TOR
                  $last = Cache::get('m9:processing:tor_last_ip_update');

                  // 2] Получить текущие дату и время
                  $now = \Carbon\Carbon::now();

                  // 3] Если $last пуст, или с момента $last прошло уже 3 секунды
                  if(empty($last) || abs($now->diffInSeconds($last)) >= 3) {

                    // 3.1] Записать $now в кэш
                    Cache::put('m9:processing:tor_last_ip_update', $now, 300);

                    // 3.2] Обновить IP TOR'а
                    $update_tor_ip();

                  }

                }

            }

            // 4] HTML извлечь не удалось, вернуть пустую строку
            return "";

          });

          // 4.3. Если $html пуст, перейти к следующей итерации
          if(empty($html) || !is_array($html) || !array_key_exists('status', $html) || $html['status'] != 200 || !array_key_exists('body', $html) || empty($html['body'])) continue;
          $html = $html['body'];

          // 4.4. Извлечь информацию о качестве вещи
          $quality_info = call_user_func(function() USE ($html) {

            // 1] Получить тип
            $pattern = '/g_rgAssets = (.*);/';
            preg_match($pattern, $html, $matches);
            if(!isset($matches[1]))
              throw new \Exception('Не удалось найти g_rgAssets в ответном HTML.');
            $info = json_decode($matches[1], true);
            $type = call_user_func(function() USE ($info) {
              foreach($info[730][2] as $item) {
                return $item['type'];
              }
              return "";
            });

            // 2] Вернуть результат
            return $type;

          });

          Log::info('ID: '.$item->id);
          Log::info('Quality: '.$quality_info);
          Log::info('-------');

          // 1.3. Определить качество
          $quality = call_user_func(function() USE ($quality_info) {

            // 1] Consumer Grade
            if(preg_match("/Consumer Grade/ui", $quality_info) != 0)
              return "Consumer Grade";

            // 2] Industrial Grade
            if(preg_match("/Industrial Grade/ui", $quality_info) != 0)
              return "Industrial Grade";

            // 3] Mil-Spec Grade
            if(preg_match("/Mil-Spec Grade/ui", $quality_info) != 0)
              return "Mil-Spec Grade";

            // 4] Restricted
            if(preg_match("/Restricted/ui", $quality_info) != 0)
              return "Restricted";

            // 5] Classified
            if(preg_match("/Classified/ui", $quality_info) != 0)
              return "Classified";

            // 6] Covert
            if(preg_match("/Covert/ui", $quality_info) != 0)
              return "Covert";

            // 7] Base Grade
            if(preg_match("/Base Grade/ui", $quality_info) != 0)
              return "Base Grade";

            // 8] High Grade
            if(preg_match("/High Grade/ui", $quality_info) != 0)
              return "High Grade";

            // n] Ничего не найдено
            return "";

          });

          // 1.4. Записать тип в БД, если его удалось определить
          if(!empty($quality)) {
            DB::beginTransaction();
            $item->quality = $quality;
            $item->save();
            DB::commit();
          }

        }
      });

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C33_update_items_quality_indb from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C33_update_items_quality_indb']);
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

