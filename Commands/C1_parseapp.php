<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Parse of packages of 4gekkman vendor to database of M1-package
 *    - After, fires event with key "m1:afterupdate".
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      dontfire        // если true, событие в конец не возбуждается
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
// - Пример для админ.документов:  M1\Commands

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
class C1_parseapp extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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

    // Принять входящие данные
    $this->data = $data;

    // Настроить Storage для текущей сессии
    config(['filesystems.default' => 'local']);
    config(['filesystems.disks.local.root' => base_path('vendor/4gekkman')]);
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
     *  1. Парсинг данных для md1_packtypes
     *  2. Парсинг данных для md4_locales
     *  3. Парсинг данных для md2_packages, md1002
     *  4. Парсинг связей между пакетами для md1000
     *  5. Для M-пакетов парсинг моделей для md3_models и связей для md1001
     *  6. Для M-пакетов парсинг команд для md5_commands и связей для md1003
     *  7. Для M-пакетов парсинг команд для md6_console и связей для md1004
     *  8. Для M-пакетов парсинг команд для md7_handlers и связей для md1005
     *
     *  X. Возбудить событие с ключём "m1:afterupdate"
     *  N. Вернуть статус 0
     *
     */

    //-------------------------------------//
    // 1. Парсинг данных для md1_packtypes //
    //-------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1.1. Мягко удалить всё из md1_packtypes

        // 1] Проверить, существует ли класс \M1\Models\MD1_packtypes
        if(!class_exists('\M1\Models\MD1_packtypes'))
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD1_packtypes" не существует.');

        // 2] Мягко удалить все packtypes
        \M1\Models\MD1_packtypes::query()->delete();

      // 1.2. Подготовить массив типов пакетов для добавления
      $packtypes = [
        ['name' => 'M', 'description' => 'M-пакет - базы данных и логика.'],
        ['name' => 'D', 'description' => 'D-пакет - blade-документы для @extends blade-шаблонов.'],
        ['name' => 'L', 'description' => 'L-пакет - blade-шаблоны.'],
        ['name' => 'W', 'description' => 'W-пакет - blade-документы (виджеты) для @include в другие blade-документы.'],
        ['name' => 'R', 'description' => 'R-пакет - ресурсы общего назначения.'],
      ];

      // 1.3. Наполнить md1_packtypes
      foreach($packtypes as $packtype) {

        // 1] Восстановить мягко удалённые записи, имеющиеся в $packtypes
        $packtype_lookfor = \M1\Models\MD1_packtypes::onlyTrashed()->where('name','=',$packtype['name'])->first();
        if(!empty($packtype_lookfor)) {
          $packtype_lookfor->restore();
          $packtype_lookfor->description = $packtype['description'];
          $packtype_lookfor->save();
          continue;
        }

        // 2] Добавить новые записи, которе есть в $packtypes, но нет даже среди мягко удалённых в md1_packtypes
        else {
          $packtype_new = new \M1\Models\MD1_packtypes();
          $packtype_new->name = $packtype['name'];
          $packtype_new->description = $packtype['description'];
          $packtype_new->save();
        }

      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md1_packtypes have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md1_packtypes have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //-----------------------------------//
    // 2. Парсинг данных для md4_locales //
    //-----------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 2.1. Мягко удалить всё из md4_locales

        // 1] Проверить, существует ли класс \M1\Models\MD1_packtypes
        if(!class_exists('\M1\Models\MD4_locales'))
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD4_locales" не существует.');

        // 2] Удалить
        \M1\Models\MD4_locales::query()->delete();

      // 2.2. Получить массив имён всех каталогов вендора

        // 1] Получить
        $dirs = $this->storage->directories();

        // 2] Отфильтровать из него все каталоги, имена которых не являются именами пакетов M,D,L,W
        // - Потому что только пакеты этих типов имеют свои локальные локали в своих конфигах
        $dirs = array_filter($dirs, function($item){
          if(preg_match("/^[MDLW]{1}[0-9]*$/ui", $item)) return true; else return false;
        });

      // 2.3. Получить списки пакетов типов M,D,L,W вендора, разбитые по типам
      $packages = [
        'M' => array_values(array_filter($dirs, function($item){ if(preg_match("/^M[0-9]*$/ui", $item)) return true; else return false; })),
        'D' => array_values(array_filter($dirs, function($item){ if(preg_match("/^D[0-9]*$/ui", $item)) return true; else return false; })),
        'L' => array_values(array_filter($dirs, function($item){ if(preg_match("/^L[0-9]*$/ui", $item)) return true; else return false; })),
        'W' => array_values(array_filter($dirs, function($item){ if(preg_match("/^W[0-9]*$/ui", $item)) return true; else return false; })),
      ];

      // 2.4. Написать функцию для получения доступных пакету локалей
      // - Из опции locales настроек вендора
      $get_pack_locales = function($packname) {

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить массив поддерживаемых пакетом локалей
        $locales = config($packname.'.locales');

        // 3] Если $file_exists отсутствует, или $locales пуст, назначить пустой массив
        if(empty($file_exists) || is_null($locales) || !is_array($locales)) $locales = [];

        // 4] Вернуть результат
        return $locales;

      };

      // 2.5. Наполнить md4_locales
      foreach($packages as $key => $packtype) {
        foreach($packtype as $package) {
          foreach($get_pack_locales($package) as $locale) {

            // 1] Попробовать найти такую локаль
            $locale_in_trash = \M1\Models\MD4_locales::onlyTrashed()->where('name','=',$locale)->first();
            $locale_not_in_trash = \M1\Models\MD4_locales::where('name','=',$locale)->first();

            // 2] Если такая локаль уже есть среди актуальных
            // - Перейти к следующему шагу
            if(!empty($locale_not_in_trash))
              continue;

            // 3] Если такая локаль уже есть среди мягко удалённых
            // - Восстановить эту локаль
            if(empty($locale_not_in_trash) && !empty($locale_in_trash)) {
              $locale_in_trash->restore();
              $locale_in_trash->save();
              continue;
            }

            // 4] Если такой локали ещё нет
            // - Добавить эту локаль
            if(empty($locale_not_in_trash) && empty($locale_in_trash)) {
              $locale_new = new \M1\Models\MD4_locales();
              $locale_new->name = $locale;
              $locale_new->save();
              continue;
            }

          }
        }
      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md4_locales have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md4_locales have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //--------------------------------------------//
    // 3. Парсинг данных для md2_packages, md1002 //
    //--------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 3.1. Мягко удалить всё из md2_packages
      // - А также обнулить md_1002 (какие локали поддерживает пакет)
      // - А также обнулить md_1006 (какая локаль установлена у пакета)

        // 1] Проверить, существует ли класс \M1\Models\MD2_packages
        if(!class_exists('\M1\Models\MD2_packages')) {
          DB::rollback();
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD2_packages" не существует.');
        }

        // 2] Удалить
        \M1\Models\MD2_packages::query()->delete();
        DB::table('m1.md1002')->delete();

      // 3.2. Получить массив имён всех каталогов вендора

        // 1] Получить
        $dirs = $this->storage->directories();

        // 2] Отфильтровать из него все каталоги, имена которых не являются именами пакетов
        $dirs = array_filter($dirs, function($item){
          if(preg_match("/^[MDLWR]{1}[0-9]*$/ui", $item)) return true; else return false;
        });

      // 3.3. Получить списки всех пакетов вендора, разбитые по типам
      $packages = [
        'M' => array_values(array_filter($dirs, function($item){ if(preg_match("/^M[0-9]*$/ui", $item)) return true; else return false; })),
        'D' => array_values(array_filter($dirs, function($item){ if(preg_match("/^D[0-9]*$/ui", $item)) return true; else return false; })),
        'L' => array_values(array_filter($dirs, function($item){ if(preg_match("/^L[0-9]*$/ui", $item)) return true; else return false; })),
        'W' => array_values(array_filter($dirs, function($item){ if(preg_match("/^W[0-9]*$/ui", $item)) return true; else return false; })),
        'R' => array_values(array_filter($dirs, function($item){ if(preg_match("/^R[0-9]*$/ui", $item)) return true; else return false; })),
      ];

      // 3.4. Получить все доступные локали
      $locales = \M1\Models\MD4_locales::all();

      // 3.5. Написать функцию для получения доступных пакету локалей
      // - Из опции locales настроек вендора
      $get_pack_locales = function($packname) {

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить массив поддерживаемых пакетом локалей
        $locales = config($packname.'.locales');

        // 3] Если $file_exists отсутствует, или $locales пуст, назначить пустой массив
        if(empty($file_exists) || is_null($locales) || !is_array($locales)) $locales = [];

        // 4] Вернуть результат
        return $locales;

      };

      // 3.6. Написать функцию для получения установленной в настройках локали пакета
      $get_pack_locale = function($packname) USE ($locales) {

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить имя локали
        if(!$file_exists) $locale = 'APP';
        else $locale = config($packname.'.locale');

        // 3] Проверить, есть ли $locale в списке локалей, если нет, установить 'APP'
        if(count(array_filter($locales->toArray(), function($item) USE ($locale) {
          if($item['name'] == $locale) return true; else return false;
        })) == 0) $locale = 'APP';

        // 4] Вернуть результат
        return $locale;

      };

      // 3.7. Написать функцию для получения имён и описаний пакета (на разных языках)
      $get_aboutpack = function($packname){

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить массив имён и описаний пакеты
        if(!$file_exists)
          $aboutpack = [
           "RU" => [
             "name" => "",
             "description" => "",
           ],
           "EN" => [
             "name" => "",
             "description" => "",
           ],
         ];
        else $aboutpack = config($packname.'.aboutpack');

        // 3] Вернуть названия
        return $aboutpack;

      };

      // 3.8. Написать функцию для получения опции domain пакета
      $get_domain = function($packname){

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить опцию domain пакета
        if(!$file_exists) $domain = "";
        else $domain = config($packname.'.domain');

        // 3] Вернуть названия
        return empty($domain) ? '' : $domain;

      };

      // 3.9. Написать функцию для получения опции uri пакета
      $get_uri = function($packname){

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить опцию domain пакета
        if(!$file_exists) $uri = "";
        else $uri = config($packname.'.uri');

        // 3] Вернуть названия
        return empty($uri) ? '' : $uri;

      };

      // 3.10. Наполнить md2_packages, md1002
      foreach($packages as $key => $packtype) {
        foreach($packtype as $package) {

          // 1] Попробовать найти пакет $package
          $package_in_trash = \M1\Models\MD2_packages::onlyTrashed()->where('id_inner','=',$package)->first();
          $package_not_in_trash = \M1\Models\MD2_packages::where('id_inner','=',$package)->first();

          // 2] Получить значение последней версии пакета $package

            // 2.1] Получить содержимое composer.json пакета $package
            $composer = json_decode($this->storage->get($package.'/composer.json'), true);

            // 2.2] Получить версию
            $lastversion = $composer['extra']['version'];

          // 3] Если такой пакет находится среди мягко удалённых
          if(!empty($package_in_trash) && empty($package_not_in_trash)) {
            $p = $package_in_trash;
            $p->restore();
            $p->id_packtype = \M1\Models\MD1_packtypes::where('name','=',$key)->first()->id;
            $p->id_inner = $package;
            $p->aboutpack = json_encode($get_aboutpack($package), JSON_UNESCAPED_UNICODE);
            //$p->domain = $get_domain($package);
            //$p->uri = $get_uri($package);
            $p->lastversion = $lastversion;
            $p->save();
          }

          // 4] Если такой пакет отсутствует
          if(empty($package_in_trash) && empty($package_not_in_trash)) {
            $p = new \M1\Models\MD2_packages();
            $p->id_packtype = \M1\Models\MD1_packtypes::where('name','=',$key)->first()->id;
            $p->id_inner = $package;
            $p->aboutpack = json_encode($get_aboutpack($package), JSON_UNESCAPED_UNICODE);
            //$p->domain = $get_domain($package);
            //$p->uri = $get_uri($package);
            $p->lastversion = $lastversion;
            $p->save();
          }

          // 5] Наполнить md1002
          foreach($get_pack_locales($package) as $locale) {

            // 5.1] Получить ID локали $locale
            $locale_id = \M1\Models\MD4_locales::where('name','=',$locale)->first()->id;

            // 5.2] Проверить, существует ли у \M1\Models\MD2_packages связь locales
            if(!r1_rel_exists("m1","md2_packages","locales")) {
              DB::rollback();
              throw new \Exception('Необходимая для осуществления парсинга приложения связь "locales" класса "\M1\Models\MD2_packages" не существует.');
            }

            // 5.3] Связать $package и $locale
            $p->locales()->attach($locale_id);

          }

        }
      }

    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md2_packages, md1002 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md2_packages, md1002 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //---------------------------------------------//
    // 4. Парсинг связей между пакетами для md1000 //
    //---------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 4.1. Очистить таблицу связей пакетов md1000
      DB::table('m1.md1000')->delete();

      // 4.2. Получить все пакеты приложения
      $packages = \M1\Models\MD2_packages::all();

      // 4.3. Для каждого пакета получить массив имён пакетов, от которых он зависит
      $dependencies = [];
      foreach($packages as $package) {

        // 1] Получить массив зависимостей пакета $packages
        $d = json_decode($this->storage->get($package['id_inner'].'/composer.json'), true)['require'];

        // 2] Заменить в нём значения на индексы от 0 и выше
        $i = 0;
        foreach($d as $key => $value) {
          $d[$key] = $i;
          $i++;
        }

        // 3] Извлечь зависимости пакета $package из его composer.json
        $result = array_map(function($item){
          return preg_replace("#^.*/#ui", '', $item);
        }, array_flip($d));

        // 4] Отсеять из $result не относящиеся к M,D,L,W,R-пакетам зависимости
        $result = array_filter($result, function($item){
          return preg_match("/^[MDLWR]{1}[0-9]*$/ui", $item);
        });

        // 5] Записать $result в $dependencies с ключём $package['id_inner']
        $dependencies[$package['id_inner']] = $result;

      }

      // 4.4. Проверить, нет ли пакетов, зависящих от самих себя
      foreach($dependencies as $key => $value) {
        foreach($value as $pack_id) {
          if($key == $pack_id) {
            DB::rollback();
            throw new \Exception('Package '.$pack_id.' depends on himself, that is not good.');
          }
        }
      }

      // 4.5. Проверить, нет ли запрещённых схем зависимостей

        // 1] Сформировать массив с разрешёнными схемами зависимостей
        $allowed_dependencies = [
          'M' => ['R','M'],
          'D' => ['M','L','W'],
          'L' => ['M','W'],
          'W' => ['M'],
          'R' => ['R'],
        ];

        // 2] Проверить, не зависит ли какой пакет от пакетов таких типов, от которых ему зависеть запрещено
        foreach($dependencies as $key => $value) {
          foreach($value as $pack_id) {

            if(!in_array(mb_substr($pack_id,0,1,"UTF-8"), $allowed_dependencies[mb_substr($key,0,1,"UTF-8")])) {
              DB::rollback();
              throw new \Exception('Package '.$key.' has forbidden dependencies.');
            }

          }
        }

      // 4.6. Наполнить таблицу связей пакетов md1000
      foreach($packages as $package) {
        foreach($dependencies[$package['id_inner']] as $dependency) {
          if(!r1_rel_exists("m1","md2_packages","packages")) {
            DB::rollback();
            throw new \Exception('Необходимая для осуществления парсинга приложения связь "packages" класса "\M1\Models\MD2_packages" не существует.');
          }
          $package->packages()->attach(\M1\Models\MD2_packages::where('id_inner','=',$dependency)->first()->id);
        }
      }


    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md1000 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md1000 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //---------------------------------------------------------------------//
    // 5. Для M-пакетов парсинг моделей для md3_models и связей для md1001 //
    //---------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 5.1. Очистить таблицу md1001 связей моделей с M-пакетами
      DB::table('m1.md1001')->delete();

      // 5.2. Удалить всё из md3_models, сбросить автоинкремент

        // 1] Проверить, существует ли класс \M1\Models\MD2_packages
        if(!class_exists('\M1\Models\MD3_models')) {
          DB::rollback();
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD3_models" не существует.');
        }

        // 2] Удалить
        \M1\Models\MD3_models::query()->delete();

      // 5.3. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name','=','M');
      })->get();

      // 5.4. Наполнить md3_models и md1001 для каждого M-пакета
      foreach($mpackages as $mpackage) {

        // 1] Получить пути ко всем дочерним файлам в Models этого M-пакета
        $paths = $this->storage->files($mpackage->id_inner.'/Models');

        // 2] Отсеять те, у которых последняя секция не матчится с ^MD[0-9]+$
        $paths = array_filter($paths, function($value){

          // Извлечь имя модели из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $value);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^MD[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        });

        // 3] Подготовить массив для ID моделей пакета $mpackage
        $mpackage_model_ids = [];

        // 4] Пробежаться по $paths
        foreach($paths as $path) {

          // 3.1] Извлечь имя модели из пути (без .php на конце)
          $name = preg_replace("/^.*\\//ui", "", $path);
          $name = preg_replace("/\\.php$/ui", "", $name);

          // 3.2] Извлечь внутренний ID модели из имени
          $modelinnerid = [];
          preg_match("/^MD[0-9]+_/ui", $name, $modelinnerid);
          $id_inner = mb_substr($modelinnerid[0], 0, -1);

          // 3.3] Добавить новую запись с $id_inner
          $new = new \M1\Models\MD3_models();
          $new->uid = $mpackage->id_inner . '_' . $id_inner;
          $new->id_inner = $id_inner;
          $new->name = $name;
          $new->save();

          // 3.4] Добавить ID модели $new в $mpackage_model_ids
          array_push($mpackage_model_ids, $new->id);

        }

        // 5] Проверить, существует ли у \M1\Models\MD2_packages связь models
        if(!r1_rel_exists("m1","md2_packages","models")) {
          DB::rollback();
          throw new \Exception('Необходимая для осуществления парсинга приложения связь "models" класса "\M1\Models\MD2_packages" не существует.');
        }

        // 6] Связать пакет $mpackage с моделями $mpackage_model_ids
        $mpackage->models()->attach($mpackage_model_ids);

      }

    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md3_models and md1001 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md3_models and md1001 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //----------------------------------------------------------------------//
    // 6. Для M-пакетов парсинг команд для md5_commands и связей для md1003 //
    //----------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 6.1. Очистить md1003, удалить всё из md5_commands, сбросить автоинкремент

        // 1] Проверить, существует ли класс \M1\Models\MD2_packages
        if(!class_exists('\M1\Models\MD5_commands')) {
          DB::rollback();
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD5_commands" не существует.');
        }

        // 2] Удалить
        DB::table('m1.md1003')->delete();
        \M1\Models\MD5_commands::query()->delete();

      // 6.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name','=','M');
      })->get();

      // 6.3. Наполнить md5_commands и md1003
      foreach($mpackages as $mpackage) {

        // 1] Получить список имён команд M-пакета $mpackage
        $commands = array_map(function($item){

          return preg_replace("/\\.php$/ui", "", preg_replace("/^.*\\//ui", "", $item));

        }, array_values(array_filter($this->storage->files($mpackage->id_inner.'/Commands'), function($item){

          // Извлечь имя команды из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $item);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^C[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        })));

        // 2] Добавить эти команды в md5_commands, а их связи в md1003
        foreach($commands as $command) {

          // 2.1] Извлечь внутренний ID команды

            // Осуществить поиск
            $innerid = [];
            preg_match("/^C[0-9]+_/ui", $command, $innerid);

            // Извлечь результат
            $innerid = $innerid[0];

            // Удалить последний символ _ из $innerid
            $innerid = mb_substr($innerid, 0, -1);

          // 2.2] Извлечь описание команды
          $description = call_user_func(function() USE ($mpackage, $command) {

            // 2.2.1] Извлечь содержимое команды $command пакета $mpackage
            $contents = $this->storage->get($mpackage->id_inner.'/Commands/'.$command.'.php');

            // 2.2.2] Извлечь описание команды
            preg_match("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*(\r?\n){1}/smuiU", $contents, $result);
            $result = preg_replace("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*/smuiU", '', $result[0]);
            $result = preg_replace("/(\r?\n){1}$/smuiU", '', $result);

            // 2.2.3] Вернуть результат
            return $result;

          });

          // 2.3] Добавить $command в md5_commands
          $new = new \M1\Models\MD5_commands();
          $new->uid         = $mpackage->id_inner . '_' . $innerid;
          $new->id_inner    = $innerid;
          $new->name        = $command;
          $new->description = $description;
          $new->save();

          // 2.4] Проверить, существует ли у \M1\Models\MD2_packages связь commands
          if(!r1_rel_exists("m1","md2_packages","commands")) {
            DB::rollback();
            throw new \Exception('Необходимая для осуществления парсинга приложения связь "commands" класса "\M1\Models\MD2_packages" не существует.');
          }

          // 2.5] Связать пакет $mpackage с командой $command
          $mpackage->commands()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md5_commands and md1003 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md5_commands and md1003 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //------------------------------------------------------------------------//
    // 7. Для M-пакетов парсинг к.команд для md6_console и связей для md1004  //
    //------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 7.1. Очистить md1004, удалить всё из md6_console, сбросить автоинкремент

        // 1] Проверить, существует ли класс \M1\Models\MD2_packages
        if(!class_exists('\M1\Models\MD6_console')) {
          DB::rollback();
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD6_console" не существует.');
        }

        // 2] Удалить
        DB::table('m1.md1004')->delete();
        \M1\Models\MD6_console::query()->delete();

      // 7.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name','=','M');
      })->get();

      // 7.3. Наполнить md6_console и md1004
      foreach($mpackages as $mpackage) {

        // 1] Получить список имён к.команд докуменда $doc
        $commands = array_map(function($item){

          return preg_replace("/\\.php$/ui", "", preg_replace("/^.*\\//ui", "", $item));

        }, array_values(array_filter($this->storage->files($mpackage->id_inner.'/Console'), function($item){

          // Извлечь имя к.команды из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $item);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^T[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        })));

        // 2] Добавить эти к.команды в md6_console, а их связи в md1004
        foreach($commands as $command) {

          // 2.1] Извлечь внутренний ID к.команды

            // Осуществить поиск
            $innerid = [];
            preg_match("/^T[0-9]+_/ui", $command, $innerid);

            // Извлечь результат
            $innerid = $innerid[0];

            // Удалить последний символ _ из $innerid
            $innerid = mb_substr($innerid, 0, -1);

          // 2.2] Извлечь описание к.команды
          $description = call_user_func(function() USE ($mpackage, $command) {

            // 2.2.1] Извлечь содержимое к.команды $command пакета $mpackage
            $contents = $this->storage->get($mpackage->id_inner.'/Console/'.$command.'.php');

            // 2.2.2] Извлечь описание к.команды
            preg_match("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*(\r?\n){1}/smuiU", $contents, $result);
            $result = preg_replace("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*/smuiU", '', $result[0]);
            $result = preg_replace("/(\r?\n){1}$/smuiU", '', $result);

            // 2.2.3] Вернуть результат
            return $result;

          });

          // 2.3] Добавить $command в md6_console
          $new = new \M1\Models\MD6_console();
          $new->uid         = $mpackage->id_inner . '_' . $innerid;
          $new->id_inner    = $innerid;
          $new->name        = $command;
          $new->description = $description;
          $new->save();

          // 2.4] Проверить, существует ли у \M1\Models\MD2_packages связь console
          if(!r1_rel_exists("m1","md2_packages","console")) {
            DB::rollback();
            throw new \Exception('Необходимая для осуществления парсинга приложения связь "console" класса "\M1\Models\MD2_packages" не существует.');
          }

          // 2.5] Связать пакет $mpackage с к.командой $command
          $mpackage->console()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md6_console and md1004 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md6_console and md1004 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //----------------------------------------------------------------------------//
    // 8. Для M-пакетов парсинг обработчиков для md7_handlers и связей для md1005 //
    //----------------------------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 8.1. Очистить md1005, удалить всё из md7_handlers, сбросить автоинкремент

        // 1] Проверить, существует ли класс \M1\Models\MD2_packages
        if(!class_exists('\M1\Models\MD7_handlers')) {
          DB::rollback();
          throw new \Exception('Необходимый для осуществления парсинга приложения класс "\M1\Models\MD7_handlers" не существует.');
        }

        // 2] Удалить
        DB::table('m1.md1005')->delete();
        \M1\Models\MD7_handlers::query()->delete();

      // 8.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtypes', function($query){
        $query->where('name','=','M');
      })->get();

      // 8.3. Наполнить md7_handlers и md1005
      foreach($mpackages as $mpackage) {

        // 1] Получить список имён обработчиков докуменда $doc
        $handlers = array_map(function($item){

          return preg_replace("/\\.php$/ui", "", preg_replace("/^.*\\//ui", "", $item));

        }, array_values(array_filter($this->storage->files($mpackage->id_inner.'/EventHandlers'), function($item){

          // Извлечь имя обработчика из пути (включая .php на конце)
          $lastsection = preg_replace("/^.*\\//ui", "", $item);

          // Если $lastsection не матчится, отсеять
          if( !preg_match("/^H[0-9]+_.*$/ui", $lastsection) ) return false;

          // В противном случае, включить в результирующий массив
          return true;

        })));

        // 2] Добавить эти обработчики в md7_handlers, а их связи в md1005
        foreach($handlers as $handler) {

          // 2.1] Извлечь внутренний ID обработчика

            // Осуществить поиск
            $innerid = [];
            preg_match("/^H[0-9]+_/ui", $handler, $innerid);

            // Извлечь результат
            $innerid = $innerid[0];

            // Удалить последний символ _ из $innerid
            $innerid = mb_substr($innerid, 0, -1);

          // 2.2] Извлечь описание обработчика
          $description = call_user_func(function() USE ($mpackage, $handler) {

            // 2.2.1] Извлечь содержимое обработчика $handler пакета $mpackage
            $contents = $this->storage->get($mpackage->id_inner.'/EventHandlers/'.$handler.'.php');

            // 2.2.2] Извлечь описание обработчика
            preg_match("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*(\r?\n){1}/smuiU", $contents, $result);
            $result = preg_replace("/Что делает(\r?\n){1}.*(\r?\n){1}.*- {1}.*/smuiU", '', $result[0]);
            $result = preg_replace("/(\r?\n){1}$/smuiU", '', $result);

            // 2.2.3] Вернуть результат
            return $result;

          });

          // 2.3] Добавить $handler в md8_handlers
          $new = new \M1\Models\MD7_handlers();
          $new->uid         = $mpackage->id_inner . '_' . $innerid;
          $new->id_inner    = $innerid;
          $new->name        = $handler;
          $new->description = $description;
          $new->save();

          // 2.4] Проверить, существует ли у \M1\Models\MD2_packages связь handlers
          if(!r1_rel_exists("m1","md2_packages","handlers")) {
            DB::rollback();
            throw new \Exception('Необходимая для осуществления парсинга приложения связь "handlers" класса "\M1\Models\MD2_packages" не существует.');
          }

          // 2.5] Связать пакет $mpackage с обработчиком $handler
          $mpackage->handlers()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        Log::info('Parsing for md7_handlers and md1005 have ended with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Parsing for md7_handlers and md1005 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //------------------------------------------------//
    // X. Возбудить событие с ключём "m1:afterupdate" //
    //------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1] Возбудить событие с ключём 'm1:afterupdate', и передать данные $data
      if(!empty($this->data) && array_key_exists('dontfire', $this->data) && $this->data['dontfire'] == true) {

      } else {
        Event::fire(new \R2\Event([
          'keys'  =>  ['m1:afterupdate'],
          'data'  =>  ""
        ]));
      }

    } catch(\Exception $e) {
        Log::info('Event firing ("m1:afterupdate") has failed with error: '.$e->getMessage());
        return [
          "status"  => -2,
          "data"    => 'Event fireing ("m1:afterupdate") has failed with error: '.$e->getMessage()
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

