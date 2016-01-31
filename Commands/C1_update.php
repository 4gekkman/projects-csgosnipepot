<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Accepts array of M1 structure data, and invoke update of M4 database
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
     *  1. Обновить md7_packtypes
     *  2. Обновить MD8_packages
     *  3. Обновить MD4_protocols
     *  4. Обновить MD2_types
     *  5. Обновить MD1_routes/MD2_types + MD3_domains/MD5_subdomains/MD6_uris + md1000/md1001/md1002/md1003/md1004
     *
     *  X. Возбудить событие с ключём "m4:afterupdate"
     *  N. Вернуть статус 0
     *
     */

    //---------------------------//
    // 1. Обновить md7_packtypes //
    //---------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1.1. Получить
      $packtypes = json_decode($this->data['data']['packtypes'], true);

      // 1.2. Если аргументы пусты, завершить с ошибкой
      if(empty($packtypes))
        throw new \exception('От M1 не получены данные о типах пакетов.');

      // 1.3. Очистить md1004
      DB::table('m4.md1004')->truncate();

      // 1.4. Мягко удалить всё из md7_packtypes и md8_packages
      \M4\Models\MD7_packtypes::query()->delete();
      \M4\Models\MD8_packages::query()->delete();

      // 1.5. Наполнить md7_packtypes
      foreach($packtypes as $packtype) {

        // 1.5.1. Попробовать найти $packtype среди мягко-удалённых
        $packtype_sd = \M4\Models\MD7_packtypes::onlyTrashed()->where('name','=',$packtype['name'])->first();

        // 1.5.2. Если $packtype_sd есть среди мягко-удалённых, восстановить
        if(!empty($packtype_sd)){
          $packtype_sd->restore();
          $packtype_sd->description = $packtype['description'];
          $packtype_sd->save();
          continue;
        }

        // 1.5.3. Если $packtype_sd нет среди мягко-удалённых, создать
        else {
          $packtype_new = new \M4\Models\MD7_packtypes();
          $packtype_new->name = $packtype['name'];
          $packtype_new->description = $packtype['description'];
          $packtype_new->save();
          continue;
        }

      }


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


    //--------------------------//
    // 2. Обновить MD8_packages //
    //--------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 2.1. Получить
      $packages = json_decode($this->data['data']['packages'], true);

      // 2.2. Если аргументы пусты, завершить с ошибкой
      if(empty($packages))
        throw new \exception('От M1 не получены данные о пакетах.');

      // 2.3. Получить все типы пакетов
      $packtypes = \M4\Models\MD7_packtypes::all();

      // 2.4. Наполнить md8_packages
      foreach($packages as $package) {

        // 2.4.1. Попробовать найти $package среди мягко-удалённых
        $package_sd = \M4\Models\MD8_packages::onlyTrashed()->where('id_inner','=',$package['id_inner'])->first();

        // 2.4.2. Если $package_sd есть среди мягко-удалённых, восстановить
        if(!empty($package_sd)){
          $package_sd->restore();
          $package_sd->id_packtype = $packtypes->where('name',$package['packtype']['name'])->first()->id;
          $package_sd->aboutpack = $package['aboutpack'];
          $package_sd->lastversion = $package['lastversion'];
          $package_sd->save();
          continue;
        }

        // 2.4.3. Если $package_sd нет среди мягко-удалённых, создать
        else {
          $package_new = new \M4\Models\MD8_packages();
          $package_new->id_packtype = $packtypes->where('name',$package['packtype']['name'])->first()->id;
          $package_new->aboutpack = $package['aboutpack'];
          $package_new->lastversion = $package['lastversion'];
          $package_new->id_inner = $package['id_inner'];
          $package_new->save();
          continue;
        }

      }

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


    //---------------------------//
    // 3. Обновить MD4_protocols //
    //---------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 3.1. Очистить md1001
      DB::table('m4.md1001')->truncate();

      // 3.2. Удалить всё из md4_protocols
      \M4\Models\MD4_protocols::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m4.md4_protocols AUTO_INCREMENT = 1;'));

      // 3.2. Наполнить md4_protocols

        // http
        $http = new \M4\Models\MD4_protocols();
        $http->name = 'http';
        $http->save();

        // https
        $https = new \M4\Models\MD4_protocols();
        $https->name = 'https';
        $https->save();


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


    //-----------------------//
    // 4. Обновить MD2_types //
    //-----------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 4.1. Очистить md1000/md1001/md1002/md1003/md1004
      DB::table('m4.md1000')->truncate();
      DB::table('m4.md1001')->truncate();
      DB::table('m4.md1002')->truncate();
      DB::table('m4.md1003')->truncate();
      DB::table('m4.md1004')->truncate();

      // 4.2. Удалить всё из md3_domains/md5_subdomains/md6_uris

        // Из md3_domains
        \M4\Models\MD3_domains::query()->delete();
        DB::select ( DB::raw('ALTER TABLE m4.md3_domains AUTO_INCREMENT = 1;'));

        // Из md5_subdomains
        \M4\Models\MD5_subdomains::query()->delete();
        DB::select ( DB::raw('ALTER TABLE m4.md5_subdomains AUTO_INCREMENT = 1;'));

        // Из md6_uris
        \M4\Models\MD6_uris::query()->delete();
        DB::select ( DB::raw('ALTER TABLE m4.md6_uris AUTO_INCREMENT = 1;'));

      // 4.3. Удалить всё из md1_routes
      \M4\Models\MD1_routes::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m4.md1_routes AUTO_INCREMENT = 1;'));

      // 4.4. Удалить всё из md2_types
      \M4\Models\MD2_types::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m4.md2_types AUTO_INCREMENT = 1;'));

      // 4.5. Наполнить md2_types

        // auto
        $http = new \M4\Models\MD2_types();
        $http->name = 'auto';
        $http->save();

        // manual
        $https = new \M4\Models\MD2_types();
        $https->name = 'manual';
        $https->save();

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


    //-------------------------------------------------------------------------------------------------------------//
    // 5. Обновить MD1_routes/MD2_types + MD3_domains/MD5_subdomains/MD6_uris + md1000/md1001/md1002/md1003/md1004 //
    //-------------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 5.1. Получить коллекцию всех D,L,W-пакетов
      $packages = \M4\Models\MD8_packages::whereHas('packtypes', function($query){
        $query->where(function($query){
          $query->where('name','=','D')->
                  orWhere('name','=','L')->
                  orWhere('name','=','W');
        });
      })->get();

      // 5.2. Подготовить массив данных, которми далее будут наполнены вышеуказанные таблицы
      /**
       *  [
       *    "D1" => [                     // ID D,L,W-пакета
       *      [                           // Массив данных для роута
       *        "type"        => "auto",    // Тип роута
       *        "domain"      => "",        // Домен роута
       *        "protocol"    => "",        // Протокол роута
       *        "subdomain"   => "",        // Поддомен роута
       *        "uri"         => "",        // uri роута
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
          config(['filesystems.default' => 'local']);
          config(['filesystems.disks.local.root' => base_path('config')]);
          $this->storage = new \Illuminate\Filesystem\FilesystemManager(app());
          if(!$this->storage->exists($package->id_inner.'.php'))
            throw new \Exception('Конфиг пакета '.$package->id_inner.' не опубликован в каталоге /config проекта.');

          // 2] Получить содержимое св-ва 'routing' из опубликованного конфига пакета $package
          $routing = eval("?> ".$this->storage->get($package->id_inner.'.php'));
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

      // 5.3. Наполнить вышеуказанные таблицы данными
      foreach($data2add as $packid => $routes) {
        foreach($routes as $route) {

          // 1] Получить пакет с $packid
          $package = \M4\Models\MD8_packages::where('id_inner',$packid)->first();

          // 2] Получить тип
          $type = \M4\Models\MD2_types::where('name', $route['type'])->first();
          if(empty($type))
            throw new \Exception("Для одного из роутов пакета $packid не удалось найти тип $type.");

          // 3] Создать новый роут
          DB::beginTransaction();
          $newroute = new \M4\Models\MD1_routes();
          $newroute->id_type = $type->id;
          $newroute->save();
          DB::commit();

          // 4] Связать созданный роут с MD8_packages
          $newroute->packages()->attach($package->id);

          // 5] Добавить домен, поддомен и uri

            // 5.1] Домен

              // 5.1.1] Попробовать найти домен $domain в md3_domains
              $domain = \M4\Models\MD3_domains::where('name', $route['domain'])->first();

              // 5.1.2] Если $domain не найден, то создать его
              if(empty($domain)) {
                DB::beginTransaction();
                $domain = new \M4\Models\MD3_domains();
                $domain->name = $route['domain'];
                $domain->save();
                DB::commit();
              }

              // 5.1.3] Связать $domain с $newroute
              if(!$newroute->domains->contains($domain->id))
                $newroute->domains()->attach($domain->id);

            // 5.2] Поддомен

              // 5.2.1] Попробовать найти поддомен $subdomain в md5_subdomains
              $subdomain = \M4\Models\MD5_subdomains::where('name', $route['subdomain'])->first();

              // 5.2.2] Если $subdomain не найден, то создать его, и связать с $newroute
              if(empty($subdomain)) {
                DB::beginTransaction();
                $subdomain = new \M4\Models\MD5_subdomains();
                $subdomain->name = $route['subdomain'];
                $subdomain->save();
                DB::commit();
              }

              // 5.2.3] Связать поддомен с $newroute
              if(!$newroute->subdomains->contains($subdomain->id))
                $newroute->subdomains()->attach($subdomain->id);

            // 5.3] uri

              // 5.3.1] Попробовать найти uri $uri в md6_uris
              $uri = \M4\Models\MD6_uris::where('name', $route['uri'])->first();

              // 5.3.2] Если $uri не найден, то создать его, и связать с $newroute
              if(empty($uri)) {
                DB::beginTransaction();
                $uri = new \M4\Models\MD6_uris();
                $uri->name = $route['uri'];
                $uri->save();
                DB::commit();
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



    } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_update from M-package M4 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M4', 'C1_update']);
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;


    //------------------------------------------------//
    // X. Возбудить событие с ключём "m1:afterupdate" //
    //------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1] Возбудить событие с ключём 'm4:afterupdate'
      Event::fire(new \R2\Event([
        'keys'  =>  ['m4:afterupdate'],
        'data'  =>  ""
      ]));

    } catch(\Exception $e) {
        Log::info('Event fireing ("m4:afterupdate") has failed with error: '.$e->getMessage());
        write2log('Event fireing ("m4:afterupdate") has failed with error: '.$e->getMessage(), ['M1', 'parseapp']);
        return [
          "status"  => -2,
          "data"    => 'Event fireing ("m4:afterupdate") has failed with error: '.$e->getMessage()
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

