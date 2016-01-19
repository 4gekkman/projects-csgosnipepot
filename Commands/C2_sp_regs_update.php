<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Update registrations of service-providers of 4gekkman vendor
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *
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
 *      - Текст ошибки.
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример для админ.документов:  M1\Documents\Main\Commands

  namespace M1\Commands;

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
class C2_sp_regs_update extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Переменная такая-то
    // protected $data;

  //------------------------------------------------------//
  // В. Принять аргументы, переданные при запуске команды //
  //------------------------------------------------------//
  public function __construct($data)  // TODO: указать аргументы
  {

    // Принять входящие данные
    $this->data = $data;

    // Настроить Storage для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path('config')]);
    $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());

  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Получить текущий список провайдеров из конфига app.php
     *  2. Получить список внутренних ID всех пакетов в системе
     *  3. Добавить в $approviders регистрации сервис-провайдеров для $packids
     *  4. С помощью regex вставить $approviders в providers конфига app.php
     *  5. Вставить $providers_str в $config
     *  6. Заменить config
     *  7. Вернуть результат
     *
     */


    //--------------------------------------------------------//
    // Актуализировать регистрации сервис-провайдеров пакетов //
    //--------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Получить текущий список провайдеров из конфига app.php
      // - И отфильтровать 1 так, чтобы удалить регистрацию всех провайдеров моих пакетов.
      $approviders = array_values(array_filter(config('app.providers'), function($item){
        if(preg_match("#^[MDLWPRK]{1}[0-9]*\\\\#ui", $item)) return false;
        else return true;
      }));

      // 2. Получить список внутренних ID всех пакетов в системе
      $packids = \M1\Models\MD2_packages::query()->pluck('id_inner');

      // 3. Добавить в $approviders регистрации сервис-провайдеров для $packids
      foreach($packids as $packid) {
        array_push($approviders, $packid.'\\ServiceProvider');
      }

      // 4. С помощью regex вставить $approviders в providers конфига app.php

        // 1] Получить содержимое конфига app.php
        $config = $this->storage->get('app.php');

        // 2] Сформировать строку в формате массива из $approviders
        $providers_str = call_user_func(function() USE ($approviders) {

          // 2.1] Подготовить строку для результата
          $result = "[" . PHP_EOL;

          // 2.2] Вставить в $result все значения из $approviders
          for($i=0; $i<count($approviders); $i++) {
            if($i != count($approviders)-1 )
              $result = $result . "        " . $approviders[$i] . "::class," . PHP_EOL;
            else
              $result = $result . "        " . $approviders[$i] . "::class" . PHP_EOL;
          }

          // 2.3] Завершить квадратной скобкой c запятой
          $result = $result . "    ],";

          // 2.4] Вернуть результат
          return $result;

        });

      // 5. Вставить $providers_str в $config
      $config = preg_replace("#'providers' *=> *\[.*\],#smuiU", "'providers' => ".$providers_str, $config);

      // 6. Заменить config
      $this->storage->put('app.php', $config);

      // 7. Вернуть результат
      return [
        "status"  => 0,
        "data"    => ""
      ];

    } catch(\Exception $e) {
        DB::rollback();
        Log::info('Updating of service providers list in config/app.php have ended with error: '.$e->getMessage());
        write2log('Updating of service providers list in config/app.php have ended with error: '.$e->getMessage(), ['M1', 'sp_regs_update']);
        return [
          "status"  => -2,
          "data"    => 'Updating of service providers list in config/app.php have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;

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

