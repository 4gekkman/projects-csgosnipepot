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
     *  3. Парсинг данных для md2_packages, md1002 и md1006
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
      \M1\Models\MD1_packtypes::query()->delete();

      // 1.2. Подготовить массив типов пакетов для добавления
      $packtypes = [
        ['name' => 'M', 'description' => 'M-пакет - базы данных и логика.'],
        ['name' => 'D', 'description' => 'D-пакет - blade-документы для @extends blade-шаблонов.'],
        ['name' => 'L', 'description' => 'L-пакет - blade-шаблоны.'],
        ['name' => 'W', 'description' => 'W-пакет - blade-документы (виджеты) для @include в другие blade-документы.'],
        ['name' => 'R', 'description' => 'R-пакет - ресурсы общего назначения.'],
        ['name' => 'P', 'description' => 'P-пакет - public-ресурсы (js/css/img).'],
        ['name' => 'K', 'description' => 'K-пакет - knockout-компоненты.'],
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
        write2log('Parsing for md1_packtypes have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
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
      \M1\Models\MD4_locales::query()->delete();

      // 2.2. Получить массив имён всех каталогов вендора

        // 1] Получить
        $dirs = $this->storage->directories();

        // 2] Отфильтровать из него все каталоги, имена которых не являются именами пакетов D,L,W
        // - Потому что только пакеты этих типов имеют свои локальные локали в своих конфигах
        $dirs = array_filter($dirs, function($item){
          if(preg_match("/^[DLW]{1}[0-9]*$/ui", $item)) return true; else return false;
        });

      // 2.3. Получить списки пакетов типов D,L,W вендора, разбитые по типам
      $packages = [
        'D' => array_values(array_filter($dirs, function($item){ if(preg_match("/^D[0-9]*$/ui", $item)) return true; else return false; })),
        'L' => array_values(array_filter($dirs, function($item){ if(preg_match("/^L[0-9]*$/ui", $item)) return true; else return false; })),
        'W' => array_values(array_filter($dirs, function($item){ if(preg_match("/^W[0-9]*$/ui", $item)) return true; else return false; })),
      ];

      // 2.4. Написать функцию для получения доступных пакету локалей
      // - Из опции locales настроек вендора
      $get_pack_locales = function($packname) {

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить массив локалей
        if(!$file_exists) $locales = ['APP'];
        else $locales = config($packname.'.locales');

        // 3] Если $locales пуста, или не массив, или в ней нет 'APP'
        // - Назначить ей значение ['APP']
        if(empty($locales) || !is_array($locales) || !in_array('APP', $locales))
          $locales = ['APP'];

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

      // 2.6. Если в locales ещё нет локали 'APP', добавить её
      call_user_func(function(){

        // 1] Попробовать найти локаль 'APP'
        $app_locale_in_trash = \M1\Models\MD4_locales::onlyTrashed()->where('name','=','APP')->first();
        $app_locale_not_in_trash = \M1\Models\MD4_locales::where('name','=','APP')->first();

        // 2] Если такая локаль уже есть среди актуальных
        // - Завершить
        if(!empty($app_locale_not_in_trash))
          return;

        // 3] Если такая локаль уже есть среди мягко удалённых
        // - Восстановить эту локаль
        if(empty($app_locale_not_in_trash) && !empty($app_locale_in_trash)) {
          $app_locale_in_trash->restore();
          $app_locale_in_trash->save();
          return;
        }

        // 4] Если такой локали ещё нет
        // - Добавить эту локаль
        if(empty($app_locale_not_in_trash) && empty($app_locale_in_trash)) {
          $locale_new = new \M1\Models\MD4_locales();
          $locale_new->name = 'APP';
          $locale_new->save();
          return;
        }

      });

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md4_locales have ended with error: '.$e->getMessage());
        write2log('Parsing for md4_locales have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
        return [
          "status"  => -2,
          "data"    => 'Parsing for md4_locales have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //-----------------------------------------------------//
    // 3. Парсинг данных для md2_packages, md1002 и md1006 //
    //-----------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 3.1. Мягко удалить всё из md2_packages
      // - А также обнулить md_1002 (какие локали поддерживает пакет)
      // - А также обнулить md_1006 (какая локаль установлена у пакета)
      \M1\Models\MD2_packages::query()->delete();
      DB::table('m1.md1002')->truncate();
      DB::table('m1.md1006')->truncate();

      // 3.2. Получить массив имён всех каталогов вендора

        // 1] Получить
        $dirs = $this->storage->directories();

        // 2] Отфильтровать из него все каталоги, имена которых не являются именами пакетов
        $dirs = array_filter($dirs, function($item){
          if(preg_match("/^[MDLWRPK]{1}[0-9]*$/ui", $item)) return true; else return false;
        });

      // 3.3. Получить списки всех пакетов вендора, разбитые по типам
      $packages = [
        'M' => array_values(array_filter($dirs, function($item){ if(preg_match("/^M[0-9]*$/ui", $item)) return true; else return false; })),
        'D' => array_values(array_filter($dirs, function($item){ if(preg_match("/^D[0-9]*$/ui", $item)) return true; else return false; })),
        'L' => array_values(array_filter($dirs, function($item){ if(preg_match("/^L[0-9]*$/ui", $item)) return true; else return false; })),
        'W' => array_values(array_filter($dirs, function($item){ if(preg_match("/^W[0-9]*$/ui", $item)) return true; else return false; })),
        'R' => array_values(array_filter($dirs, function($item){ if(preg_match("/^R[0-9]*$/ui", $item)) return true; else return false; })),
        'P' => array_values(array_filter($dirs, function($item){ if(preg_match("/^P[0-9]*$/ui", $item)) return true; else return false; })),
        'K' => array_values(array_filter($dirs, function($item){ if(preg_match("/^K[0-9]*$/ui", $item)) return true; else return false; })),
      ];

      // 3.4. Получить все доступные локали
      $locales = \M1\Models\MD4_locales::all();

      // 3.5. Написать функцию для получения доступных пакету локалей
      // - Из опции locales настроек вендора
      $get_pack_locales = function($packname) {

        // 1] Проверить, существует ли файл с настройками пакета $packname
        $file_exists = file_exists(base_path('config/'.$packname.'.php'));

        // 2] Получить массив локалей
        if(!$file_exists) $locales = ['APP'];
        else $locales = config($packname.'.locales');

        // 3] Если $locales пуста, или не массив, или в ней нет 'APP'
        // - Назначить ей значение ['APP']
        if(empty($locales) || !is_array($locales) || !in_array('APP', $locales))
          $locales = ['APP'];

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
        if(!$file_exists) $aboutpack = "";
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

      // 3.10. Наполнить md2_packages, md1002 и md1006
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
            continue;
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

            // 4.1] Получить ID локали $locale
            $locale_id = \M1\Models\MD4_locales::where('name','=',$locale)->first()->id;

            // 4.2] Связать $package и $locale
            $p->locales()->attach($locale_id);

          }

          // 5] Наполнить md1006

            // 5.1] Получить ID локали $locale
            $locale_id = \M1\Models\MD4_locales::where('name','=',$get_pack_locale($package))->first()->id;

            // 5.2] Связать $package и $locale
            $p->locale()->attach($locale_id);

        }
      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md2_packages, md1002 и md1006 have ended with error: '.$e->getMessage());
        write2log('Parsing for md2_packages, md1002 и md1006 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
        return [
          "status"  => -2,
          "data"    => 'Parsing for md2_packages, md1002 и md1006 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //---------------------------------------------//
    // 4. Парсинг связей между пакетами для md1000 //
    //---------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 4.1. Очистить таблицу связей пакетов md1000
      DB::table('m1.md1000')->truncate();

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

        // 4] Отсеять из $result не относящиеся к M,D,L,W,R,P,K-пакетам зависимости
        $result = array_filter($result, function($item){
          return preg_match("/^[MDLWRPK]{1}[0-9]*$/ui", $item);
        });

        // 5] Записать $result в $dependencies с ключём $package['id_inner']
        $dependencies[$package['id_inner']] = $result;

      }

      // 4.4. Проверить, нет ли пакетов, зависящих от самих себя
      foreach($dependencies as $key => $value) {
        foreach($value as $pack_id) {
          if($key == $pack_id)
            throw new \Exception('Package '.$pack_id.' depends on himself, that is not good.');
        }
      }

      // 4.5. Проверить, нет ли запрещённых схем зависимостей

        // 1] Сформировать массив с разрешёнными схемами зависимостей
        $allowed_dependencies = [
          'M' => ['R'],
          'D' => ['M','L','W','R','P','K'],
          'L' => ['M','W','R','P','K'],
          'W' => ['M','R','P','K'],
          'R' => ['R'],
          'P' => ['P'],
          'K' => ['P'],
        ];

        // 2] Проверить, не зависит ли какой пакет то пакетов таких типов, от которых ему зависеть запрещено
        foreach($dependencies as $key => $value) {
          foreach($value as $pack_id) {

            if(!in_array(mb_substr($pack_id,0,1,"UTF-8"), $allowed_dependencies[mb_substr($key,0,1,"UTF-8")]))
              throw new \Exception('Package '.$key.' has forbidden dependencies.');

          }
        }

      // 4.6. Наполнить таблицу связей пакетов md1000
      foreach($packages as $package) {
        foreach($dependencies[$package['id_inner']] as $dependency) {
          $package->dependencies()->attach(\M1\Models\MD2_packages::where('id_inner','=',$dependency)->first()->id);
        }
      }


    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md1000 have ended with error: '.$e->getMessage());
        write2log('Parsing for md1000 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
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
      DB::table('m1.md1001')->truncate();

      // 5.2. Удалить всё из md3_models, сбросить автоинкремент
      \M1\Models\MD3_models::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m1.md3_models AUTO_INCREMENT = 1;'));

      // 5.3. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtype', function($query){
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

        // 5] Связать пакет $mpackage с моделями $mpackage_model_ids
        $mpackage->models()->attach($mpackage_model_ids);

      }


    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md3_models and md1001 have ended with error: '.$e->getMessage());
        write2log('Parsing for md3_models and md1001 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
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
      DB::table('m1.md1003')->truncate();
      \M1\Models\MD5_commands::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m1.md5_commands AUTO_INCREMENT = 1;'));

      // 6.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtype', function($query){
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

          // 2.4] Связать пакет $mpackage с командой $command
          $mpackage->commands()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md5_commands and md1003 have ended with error: '.$e->getMessage());
        write2log('Parsing for md5_commands and md1003 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
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
      DB::table('m1.md1004')->truncate();
      \M1\Models\MD6_console::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m1.md6_console AUTO_INCREMENT = 1;'));

      // 7.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtype', function($query){
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

          // 2.4] Связать пакет $mpackage с к.командой $command
          $mpackage->consoles()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md6_console and md1004 have ended with error: '.$e->getMessage());
        write2log('Parsing for md6_console and md1004 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
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
      DB::table('m1.md1005')->truncate();
      \M1\Models\MD7_handlers::query()->delete();
      DB::select ( DB::raw('ALTER TABLE m1.md7_handlers AUTO_INCREMENT = 1;'));

      // 8.2. Получить все M-пакеты
      $mpackages = \M1\Models\MD2_packages::whereHas('packtype', function($query){
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

          // 2.4] Связать пакет $mpackage с обработчиком $handler
          $mpackage->handlers()->attach($new->id);

        }

      }

    DB::commit(); } catch(\Exception $e) {
        DB::rollback();
        Log::info('Parsing for md7_handlers and md1005 have ended with error: '.$e->getMessage());
        write2log('Parsing for md7_handlers and md1005 have ended with error: '.$e->getMessage(), ['M1', 'parseapp']);
        return [
          "status"  => -2,
          "data"    => 'Parsing for md7_handlers and md1005 have ended with error: '.$e->getMessage()
        ];
    }}); if(!empty($res)) return $res;


    //------------------------------------------------//
    // X. Возбудить событие с ключём "m1:afterupdate" //
    //------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1] Подготовить массив данных для отправки с событием
      $data = [];

      // 2] Сформировать массив данных для отправки с событием

        // 2.1] MD1_packtypes
        $data['packtypes'] = json_encode(\M1\Models\MD1_packtypes::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.2] MD2_packages
        $data['packages'] = json_encode(\M1\Models\MD2_packages::with([
          'dependencies',
          'locales',
          'locale',
          'packtype',
          'models',
          'commands',
          'consoles',
          'handlers',
        ])->get(), JSON_UNESCAPED_UNICODE);

        // 2.3] MD3_models
        $data['models'] = json_encode(\M1\Models\MD3_models::with([
          'package',
        ])->get(),JSON_UNESCAPED_UNICODE);

        // 2.4] MD4_locales
        $data['locales'] = json_encode(\M1\Models\MD4_locales::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.8] MD5_commands
        $data['commands'] = json_encode(\M1\Models\MD5_commands::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.9] MD6_console
        $data['console'] = json_encode(\M1\Models\MD6_console::query()->get(), JSON_UNESCAPED_UNICODE);

        // 2.10] MD7_handlers
        $data['handlers'] = json_encode(\M1\Models\MD7_handlers::query()->get(), JSON_UNESCAPED_UNICODE);

      // 3] Возбудить событие с ключём 'm1:afterupdate', и передать данные $data
      Event::fire(new \R2\Event([
        'keys'  =>  ['m1:afterupdate'],
        'data'  =>  $data
      ]));

      // 4] Вернуть результат
      return [
        "status"  => 0,
        "data"    => $data
      ];

    } catch(\Exception $e) {
        Log::info('Event fireing ("m1:afterupdate") has failed with error: '.$e->getMessage());
        write2log('Event fireing ("m1:afterupdate") has failed with error: '.$e->getMessage(), ['M1', 'parseapp']);
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

