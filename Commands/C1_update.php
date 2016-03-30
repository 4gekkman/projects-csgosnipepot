<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Updates auto routes using fresh data about packages
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
class C1_update extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Обновить данные в protocols
     *  2. Обновить данные в types
     *  3. Удалить все существующие автоматические роуты
     *  4. Создать новые автоматические роуты для D,L,W-пакетов
     *  5. Выполнить команду C8_routesphp_sync
     *
     *  N. Вернуть статус 0
     *
     */

    //--------------------------------------------------//
    // Обновить types, protocols и автоматические роуты //
    //--------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Обновить данные в protocols

        // 1.1. Если нет http, добавить
        $http = \M4\Models\MD4_protocols::where('name','http')->first();
        if(empty($http)) {
          $http = new \M4\Models\MD4_protocols();
          $http->name = 'http';
          $http->save();
        }

        // 1.2. Если нет https, добавить
        $https = \M4\Models\MD4_protocols::where('name','https')->first();
        if(empty($https)) {
          $https = new \M4\Models\MD4_protocols();
          $https->name = 'https';
          $https->save();
        }

      // 2. Обновить данные в types

        // 2.1. Если нет auto, добавить
        $auto = \M4\Models\MD2_types::where('name','auto')->first();
        if(empty($auto)) {
          $auto = new \M4\Models\MD2_types();
          $auto->name = 'auto';
          $auto->save();
        }

        // 2.2. Если нет manual, добавить
        $manual = \M4\Models\MD2_types::where('name','manual')->first();
        if(empty($manual)) {
          $manual = new \M4\Models\MD2_types();
          $manual->name = 'manual';
          $manual->save();
        }

      // 3. Удалить все существующие автоматические роуты

        // 3.1. Получить все автоматические роуты
        $autoroutes = \M4\Models\MD1_routes::whereHas('types', function($query){
          $query->where('name', 'auto');
        })->get();

        // 3.2. Удалить $autoroutes
        foreach($autoroutes as $autoroute) {

          // 1] Удалить все внутренние связи роута $autoroute
          $autoroute->domains()->detach();
          $autoroute->protocols()->detach();
          $autoroute->subdomains()->detach();
          $autoroute->uris()->detach();

          // 2] Удалить все транс-пакетные связи роута $autoroute
          if(r1_rel_exists("M4", "MD1_routes", "m1_packages")) {
            $autoroute->m1_packages()->detach();
          } else {
            DB::table('m4.md2000')->where('id_route',$autoroute->id)->delete();
          }

          // 3] Удалить роут $autoroute
          $autoroute->delete();

        }

      // 4. Создать новые автоматические роуты для D,L,W-пакетов

        // 4.1. Получить все D,L,W-пакеты
        $packages = r1_query(function(){
          return \M1\Models\MD2_packages::whereHas('packtypes', function($query){
            $query->whereIn('name', ["D", "L", "W"]);
          })->get();
        });

        // 4.2. Если $packages не NULL, создать роуты
        if(!is_null($packages)) {

          // 4.2.1. Подготовить массив данных
          /**
           *  [
           *    "D1" => [                     // ID D,L,W-пакета
           *      [                           // Массив данных для роута
           *        "type"        => "auto",  // Тип роута
           *        "domain"      => "",      // Домен роута
           *        "protocol"    => "",      // Протокол роута
           *        "subdomain"   => "",      // Поддомен роута
           *        "uri"         => "",      // uri роута
           *      ],
           *      [ ... ]                     // Массив данных для роута
           *    ]
           *  ]
           */
          $data2add = call_user_func(function() USE ($packages) {

            // Подготовить массив для результата
            $result = [];

            // Пробежаться по всем $packages
            foreach($packages as $package) {

              // 1] Проверить существование конфига пакета $package
              if(!r1_fs('config')->exists($package->id_inner.'.php'))
                throw new \Exception('Конфиг пакета '.$package->id_inner.' не опубликован в каталоге /config проекта.');

              // 2] Получить содержимое св-ва 'routing' из опубликованного конфига пакета $package
              $routing = eval("?> ".r1_fs('config')->get($package->id_inner.'.php'));
              if(!is_array($routing) || !array_key_exists('routing', $routing) || !is_array($routing['routing']))
                throw new \Exception('В конфиге пакета '.$package->id_inner.' нет ключа \'routing\', значение которого д.б. массивом.');
              $routing = $routing['routing'];

              // 3] Наполнить $result
              foreach($routing as $domain => $protocols) {
                foreach($protocols as $protocol => $subdomains) {
                  foreach($subdomains as $subdomain => $uris) {
                    foreach($uris as $uri) {

                      // 3.1] Если в $result нет эл-та с ключём $package->id_inner, добавить
                      if(!array_key_exists($package->id_inner, $result))
                        $result[$package->id_inner] = [];

                      // 3.2] Добавить новый элемент в $result[$package->id_inner]
                      array_push($result[$package->id_inner], [
                        "packid"      => $package->id,
                        "type"        => "auto",
                        "domain"      => $domain,
                        "protocol"    => $protocol,
                        "subdomain"   => $subdomain,
                        "uri"         => $uri
                      ]);

                    }
                  }
                }
              }

            }

            // Вернуть результат
            return $result;

          });

          // 4.2.2. Наполнить таблицы данными
          foreach($data2add as $packid => $routes) {
            foreach($routes as $route) {

              // 1] Получить тип
              $type = \M4\Models\MD2_types::where('name', $route['type'])->first();
              if(empty($type))
                throw new \Exception("Для одного из роутов пакета $packid не удалось найти тип $type.");

              // 2] Создать новый роут
              $newroute = new \M4\Models\MD1_routes();
              $newroute->id_type = $type->id;
              $newroute->save();

              // 4] Связать созданный роут с пакетами
              // - Если сооветствующая связь присутствует
              if(r1_rel_exists("M4", "MD1_routes", "m1_packages")) {
                $newroute->m1_packages()->attach($route['packid']);
              }

              // 5] Добавить домен, поддомен и uri

                // 5.1] Домен

                  // 5.1.1] Попробовать найти домен $domain в md3_domains
                  $domain = \M4\Models\MD3_domains::where('name', $route['domain'])->first();

                  // 5.1.2] Если $domain не найден, то создать его
                  if(empty($domain)) {
                    $domain = new \M4\Models\MD3_domains();
                    $domain->name = $route['domain'];
                    $domain->save();
                  }

                  // 5.1.3] Связать $domain с $newroute
                  if(!$newroute->domains->contains($domain->id))
                    $newroute->domains()->attach($domain->id);

                // 5.2] Поддомен

                  // 5.2.1] Попробовать найти поддомен $subdomain в md5_subdomains
                  $subdomain = \M4\Models\MD5_subdomains::where('name', $route['subdomain'])->first();

                  // 5.2.2] Если $subdomain не найден, то создать его, и связать с $newroute
                  if(empty($subdomain)) {
                    $subdomain = new \M4\Models\MD5_subdomains();
                    $subdomain->name = $route['subdomain'];
                    $subdomain->save();
                  }

                  // 5.2.3] Связать поддомен с $newroute
                  if(!$newroute->subdomains->contains($subdomain->id))
                    $newroute->subdomains()->attach($subdomain->id);

                // 5.3] uri

                  // 5.3.1] Попробовать найти uri $uri в md6_uris
                  $uri = \M4\Models\MD6_uris::where('name', $route['uri'])->first();

                  // 5.3.2] Если $uri не найден, то создать его, и связать с $newroute
                  if(empty($uri)) {
                    $uri = new \M4\Models\MD6_uris();
                    $uri->name = $route['uri'];
                    $uri->save();
                  }

                  // 5.3.3] Связать uri с $newroute
                  if(!$newroute->uris->contains($uri->id))
                    $newroute->uris()->attach($uri->id);

                // 5.4] protocol

                  // 5.4.1] Попробовать найти $protocol в md4_protocols
                  $protocol = \M4\Models\MD4_protocols::where('name', $route['protocol'])->first();
                  if(empty($protocol))
                    throw new \Exception("Протокол '$protocol' не найден в md4_protocols.");

                  // 5.4.2] Елси $newroute ещё не связан с $protocol, связать их
                  if(!$newroute->protocols->contains($protocol->id))
                    $newroute->protocols()->attach($protocol->id);

            }
          }


        }

      // 5. Выполнить команду C8_routesphp_sync
      Artisan::queue('m4:routesphp_sync');


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_update from M-package M4 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M4', 'C1_update']);
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

