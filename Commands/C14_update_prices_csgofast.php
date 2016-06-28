<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Gets prices from csgofast and updates local DB of items and prices
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
class C14_update_prices_csgofast extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Обновить данные в некоторых таблицах-списках в БД, используя данные из конфига
     *  2. Выполнить HTTP-запрос и получить данные из API csgofast.com
     *  3. Провести валидацию результатов запроса с $csgofast_data
     *  4. Получить все цены в виде массива
     *  5. Извлечь все knife types и weapon models из БД
     *  6. Использовать $prices для наполнения локальной базы данных
     *  7. Отметить в MD6_price_update_bugs, что ошибок при обновлении не возникло
     *
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------------------------------------------------------//
    // Извлечь цены на вещи CS:GO из API сайта csgofast.com, и обновить локальную базу цен //
    //-------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Обновить данные в некоторых таблицах-списках в БД, используя данные из конфига
      $result = runcommand('\M8\Commands\C13_update_db_lists');
      if($result['status'] != 0) {
        throw new \Exception($result['data']['errormsg']);
      }

      // 2. Выполнить HTTP-запрос и получить данные из API csgofast.com
      $csgofast_data = call_user_func(function() {

        // 1] Подготовить массив для результата
        $result = [];

        // 2] Создать экземпляр guzzle
        $guzzle = new \GuzzleHttp\Client();

        // 3] Сформировать URL для запроса
        $url = "https://api.csgofast.com/price/all";

        // 4] Выполнить запрос
        $request_result = $guzzle->request('GET', $url, []);

        // 5] Наполнить $result
        $result['result'] = $request_result;
        $result['status'] = $request_result->getStatusCode();
        $result['body'] = $request_result->getBody();

        // n] Вернуть результат
        return $result;

      });

      // 3. Провести валидацию результатов запроса с $csgofast_data
      $validator = r4_validate($csgofast_data, [
        "status"          => ["required", "in:200"],
        "body"            => ["required", "json"],
      ]); if($validator['status'] == -1) {

        // 1] Записать сведения об ошибке в MD6_price_update_bugs
        $model = \M8\Models\MD6_price_update_bugs::find(1);
        $model->csgofast_last_update = (string) \Carbon\Carbon::now();
        $model->csgofast_last_bug = $validator['data'];
        $model->save();

        // 2] Возбудить исключение
        throw new \Exception($validator['data']);

      }

      // 4. Получить все цены в виде массива
      $prices = json_decode((string) $csgofast_data['body'], true);

      // 5. Извлечь все knife types и weapon models из БД

        // 5.1. Извлечь knife types
        $all_knife_types = \M8\Models\MD4_knife_types::query()->get();
        if(empty($all_knife_types))
          throw new \Exception('Knife types table contents is empty.');

        // 5.2. Извлечь weapon models
        $all_weapon_models = \M8\Models\MD5_weapon_models::query()->get();
        if(empty($all_weapon_models))
          throw new \Exception('Weapon models table contents is empty.');

      // 6. Использовать $prices для наполнения локальной базы данных
      foreach($prices as $name => $price) {

        // 1] Попробовать найти запись с $name в MD2_items
        $item = \M8\Models\MD2_items::where('name','=',$name)->first();

        // 2] Если $item не найдена, создать запись с таким $name
        if(empty($item)) {
          $item = new \M8\Models\MD2_items();
          $item->name = $name;
        }

        // 3] Записать новую/стару цену (csgofast) для $item, проверить на unstable, и сохранить $item

          // Получить значение unstable_price_threshold из конфига
          $unstable_price_threshold = config("M8.unstable_price_threshold");
          $unstable_price_threshold = !empty($unstable_price_threshold) ? $unstable_price_threshold : "50";

          // Записать
          call_user_func(function() USE ($item, $price, $unstable_price_threshold) {

            // 3.1] Записать предыдущую цену
            $item->csgofast_price_prev = $item->csgofast_price;

            // 3.2] Записать текущую цену
            $item->csgofast_price = $price;

            // 3.3] Рассчитать, на сколько % изменилась цена
            if(!empty($item->csgofast_price_prev))
              $change_percents = round((abs($item->csgofast_price_prev - $item->csgofast_price)/$item->csgofast_price_prev)*100, 2);
            else
              $change_percents = 0;

            // 3.4] Если $change_percents > $unstable_price_threshold, пометить цену вещи, как нестабильную
            // - И если $item->csgofast_price_prev не пустое значение
            if($change_percents > $unstable_price_threshold && !empty($item->csgofast_price_prev) && $item->csgofast_price_prev != 0)
              $item->is_price_unstable = 1;

            // 3.5] Сохранить $item
            $item->save();

          });

        // 4] Определить exterior предмета, если он есть
        call_user_func(function() USE ($item, $name) {

          // 4.1] Узнать, есть ли в $name информация об exterior
          // - Если нет, отвязать $item от $exterior, если они связаны, и завершить определение exterior.
          // - Если есть, сохранить эту информацию в $match.
          if(!preg_match("/\(.+\)/ui", $name, $match)) {

            // Отвязать $item от $exterior, если
            $item->exteriors()->detach();

            // Завершить определение exterior
            return;

          }

          // 4.2] Получить имя exterior вещи $item
          $exterior_str = substr($match[0], 1, +count($match[0]) - 2);

          // 4.3] Найти такой $exterior_str в MD3_exteriors
          $exterior = \M8\Models\MD3_exteriors::where('exterior','=',$exterior_str)->first();

          // 4.4] Если найти не удалось, сообщить об этом и завершить определение exterior
          if(empty($exterior)) {
            // $errortext = "Can't find exterior = ".$exterior_str." in \\M8\\Models\\MD3_exteriors";
            // Log::info($errortext);
            // write2log($errortext, ['M8', 'C14_update_prices_csgofast']);
            return;
          }

          // 4.5] Связать $item и $exterior, если они ещё не связаны
          if(!$item->exteriors->contains($exterior))
            $item->exteriors()->attach($exterior);

        });

        // 5] Определить, является ли $item кейсом
        call_user_func(function() USE ($item, $name) {

          // 5.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item кейсом, указав, что это не кейс
          if(!preg_match("/(Case$|Weapon Case|Winter Case|Summer Case)/ui", $name, $match)) {
            $item->is_case = 0;
          }

          // 5.2] В противном случае указать, что $item является кейсом
          else $item->is_case = 1;

        });

        // 6] Определить, является ли $item ключём
        call_user_func(function() USE ($item, $name) {

          // 6.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item ключём, указав, что это не ключ
          if(!preg_match("/(Case Key|Capsule Key|Capsule .* Key|eSports Key)/ui", $name, $match)) {
            $item->is_key = 0;
          }

          // 6.2] В противном случае указать, что $item является ключём
          else $item->is_key = 1;

        });

        // 7] Определить, является ли $item StarTrak-вещью
        call_user_func(function() USE ($item, $name) {

          // 7.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item StarTrak-вещью, указав, что это не она
          if(!preg_match("/StatTrak/ui", $name, $match)) {
            $item->is_startrak = 0;
          }

          // 7.2] В противном случае указать, что $item является StarTrak-вещью
          else $item->is_startrak = 1;

        });

        // 8] Определить, является ли $item сувенирным набором
        call_user_func(function() USE ($item, $name) {

          // 8.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item сувенирным набором, указав, что это не она
          if(!preg_match("/Souvenir Package/ui", $name, $match)) {
            $item->is_souvenir_package = 0;
          }

          // 8.2] В противном случае указать, что $item является сувенирным набором
          else $item->is_souvenir_package = 1;

        });

        // 9] Определить, является ли $item сувениром
        call_user_func(function() USE ($item, $name) {

          // 9.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item сувениром, указав, что это не он
          if(!preg_match("/^Souvenir/ui", $name, $match)) {
            $item->is_souvenir = 0;
          }

          // 9.2] В противном случае указать, что $item является сувениром
          else $item->is_souvenir = 1;

        });

        // 10] Определить, является ли $item ножом, и каким
        call_user_func(function() USE ($item, $name, $all_knife_types) {

          // 10.1] Узнать, есть ли в $name соотв.ключевые слова
          // - Если нет, завершить определение, является ли $item ножом, указав, что это не он
          if(!preg_match("/^★/ui", $name, $match)) {
            $item->is_knife = 0;
          }

          // 10.2] В противном случае
          else {

            // Указать, что $item является ножом
            $item->is_knife = 1;

            // Опознать тип этого ножа
            $knife_type_str = call_user_func(function() USE ($name, $all_knife_types) {

              // 1) Подготовить переменную для результата
              $type = "";

              // 2) Пробежаться по $all_knife_types и найти нужный
              foreach($all_knife_types as $knife_type) {
                if(!preg_match("/$knife_type->type/ui", $name, $match))
                  continue;
                $type = $knife_type->type;
                break;
              }

              // n) Вернуть результат
              return $type;

            });

            // Извлечь тип ножа из MD4_knife_types
            $knife_type = \M8\Models\MD4_knife_types::where('type','=',$knife_type_str)->first();

            // Связать этот нож с типом, если они ещё не связаны, и $knife_type найден
            if(!empty($knife_type) && !$item->knife_types->contains($knife_type))
              $item->knife_types()->attach($knife_type);

          }

        });

        // 11] Определить, является ли $item огнестрельным оружием, и каким
        call_user_func(function() USE ($item, $name, $all_weapon_models) {

          // 11.1] Если это огнестрельное оружие, опознать его тип
          $weapon_model_str = call_user_func(function() USE ($name, $all_weapon_models) {

            // 1) Подготовить переменную для результата
            $model = "";

            // 2) Пробежаться по $all_knife_types и найти нужный
            foreach($all_weapon_models as $weapon_model) {
              if(!preg_match("/".$weapon_model['model']."/ui", $name, $match))
                continue;
              $model = $weapon_model['model'];
              break;
            }

            // n) Вернуть результат
            return $model;

          });

          // 11.2] На основе значения $weapon_model_str указать, является ли $item оружием
          if(!empty($weapon_model_str))
            $item->is_weapon = 1;
          else
            $item->is_weapon = 0;

          // 11.3] Извлечь модель оружия из MD5_weapon_models
          $weapon_model = \M8\Models\MD5_weapon_models::where('model','=',$weapon_model_str)->first();

          // Связать $item с моделью, если они ещё не связаны, и $weapon_model найден
          if(!empty($weapon_model) && !$item->weapon_models->contains($weapon_model))
            $item->weapon_models()->attach($weapon_model);

        });

        // n] Сохранить $item в БД
        $item->save();

      }

      // 7. Отметить в MD6_price_update_bugs, что ошибок при обновлении не возникло
      call_user_func(function(){

        $model = \M8\Models\MD6_price_update_bugs::find(1);
        $model->csgofast_last_update = (string) \Carbon\Carbon::now();
        $model->csgofast_last_bug = '';
        $model->save();

      });


    DB::commit(); } catch(\Exception $e) {

      // 1] Откатить все изменения в БД
      DB::rollback();

      // 2] Записать ошибку в логи
      $errortext = 'Invoking of command C14_update_prices_csgofast from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
      Log::info($errortext);
      write2log($errortext, ['M8', 'C14_update_prices_csgofast']);

      // n] Вернуть код -2 и тексты ошибок
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

