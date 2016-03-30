<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - New manual route
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        params        // Параметры для создания нового роута
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

  namespace M4\Commands;

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
class C4_new extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Получить входящие данные
     *  2. Провести валидацию входящих данных
     *  3. Получить пакет с $params['packid'], а если его нет, завершить
     *  4. Создать $params['domain'] в md3_domains, если такового ещё нет
     *  5. Создать $params['protocol'] в md4_protocols, если такового ещё нет
     *  6. Создать $params['subdomains'] в md5_subdomains, если такового ещё нет
     *  7. Создать $params['uri'] в md6_uris, если такового ещё нет
     *  8. Получить тип роута "manual"
     *  9. Создать новый роут, и связать его ресурсами
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------//
    // Создать новый роут //
    //--------------------//
    $res = call_user_func(function() { try {

      // 1. Принять входящие данные
      $params = $this->data['params'];
      $params['subdomain']  = $params['subdomain'] === 0 ? "" : $params['subdomain'];
      $params['uri']        = $params['uri'] === 0 ? "/" : $params['uri'];

      // 2. Провести валидацию входящих данных
      $validator = r4_validate($params, [

        "packid"    => "required|regex:/^[DLW]{1}[0-9]+$/ui",
        "domain"    => "required|regex:/^[-0-9а-яёa-z.]+$/ui",
        "protocol"  => "required|in:http,https",
        "subdomain" => "regex:/^([-0-9а-яёa-z.]+|\/)$/ui",
        "uri"       => ["regex:#^([\/]{1}[-0-9а-яёa-z\/_]*|\/)$#ui"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 3. Получить пакет с $params['packid'], а если его нет, завершить

        // 3.1. Получить
        $pack = r1_query(function() USE ($params) {
          return \M1\Models\MD2_packages::where('id_inner', $params['packid'])->first();
        });

        // 3.2. Если $pack отсутствует
        if(empty($pack))
          throw new \Exception('Не удалось получить пакет с ID '.$pack.' из M-пакета М1.');

      // 4. Создать $params['domain'] в md3_domains, если такового ещё нет
      $domain = \M4\Models\MD3_domains::where('name', $params['domain'])->first();
      if(empty($domain)) {
        $domain = new \M4\Models\MD3_domains();
        $domain->name = $params['domain'];
        $domain->save();
      }

      // 5. Создать $params['protocol'] в md4_protocols, если такового ещё нет
      $protocol = \M4\Models\MD4_protocols::where('name', $params['protocol'])->first();
      if(empty($protocol)) {
        $protocol = new \M4\Models\MD4_protocols();
        $protocol->name = $params['protocol'];
        $protocol->save();
      }

      // 6. Создать $params['subdomains'] в md5_subdomains, если такового ещё нет
      $subdomain = \M4\Models\MD5_subdomains::where('name', $params['subdomain'])->first();
      if(empty($subdomain)) {
        $subdomain = new \M4\Models\MD5_subdomains();
        $subdomain->name = $params['subdomain'];
        $subdomain->save();
      }

      // 7. Создать $params['uri'] в md6_uris, если такового ещё нет
      $uri = \M4\Models\MD6_uris::where('name', $params['uri'])->first();
      if(empty($uri)) {
        $uri = new \M4\Models\MD6_uris();
        $uri->name = $params['uri'];
        $uri->save();
      }

      // 8. Получить тип роута "manual"
      $type = \M4\Models\MD2_types::where('name', 'manual')->first();
      if(empty($type))
        throw new \Exception('В md2_types отсутствует тип manual.');

      // 9. Создать новый роут, и связать его ресурсами

        // 9.1. Создать
        $route = new \M4\Models\MD1_routes();
        $route->id_type = $type->id;
        $route->save();

        // 9.2. Связать с $pack
        // - Если связь в налчиии
        if(r1_rel_exists("M4", "MD1_routes", "m1_packages")) {
          $route->m1_packages()->attach($pack->id);
        }

        // 9.3. Связать с $domain
        $route->domains()->attach($domain->id);

        // 9.4. Связать с $protocol
        $route->protocols()->attach($protocol->id);

        // 9.5. Связать с $subdomain
        $route->subdomains()->attach($subdomain->id);

        // 9.6. Связать с $uri
        $route->uris()->attach($uri->id);


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C4_new from M-package M4 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M4', 'C4_new']);
        return [
          "status"  => -2,
          "data"    => $errortext
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

