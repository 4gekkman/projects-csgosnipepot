<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Fires m5:call4update event, gets data from M1/M4, updates DB of M5
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

  namespace M5\Commands;

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
  { DB::beginTransaction();

    /**
     * Оглавление
     *
     *  1. Наполнить таблицы md5_privtypes и md11_genders
     *  2. Обновить состав прав типа "access" и "exec"
     *  3. Обновить состав тегов для авто.прав типа "access" и "exec"
     *  4. Добавить польз-м те права, с которыми у них общие теги
     *  5. Добавить группам те права, с которыми у них общие теги
     *  6. Добавить все права группе с флагом "администраторы"
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------//
    // Произвести обновление //
    //-----------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Наполнить таблицы md5_privtypes и md11_genders

        // 1.1. md5_privtypes

          // 1] Мягко удалить всё из md5_privtypes
          \M5\Models\MD5_privtypes::query()->delete();

          // 2] Добавить / Восстановить записи
          $items = ['access', 'exec', 'custom'];
          foreach($items as $name) {

            $item = \M5\Models\MD5_privtypes::withTrashed()->where('name', $name)->first();
            if(empty($item)) {
              $item = new \M5\Models\MD5_privtypes();
              $item->name = $name;
              $item->save();
            }
            else if($item->trashed()) $item->restore();

          }

        // 1.2. md11_genders

          // Мягко удалить всё из таблицы полов
          \M5\Models\MD11_genders::query()->delete();

          // Восстановить / Создать записи в MD11_genders

            // 1] m
            $gender = \M5\Models\MD11_genders::withTrashed()->where('name','m')->first();
            if(!empty($gender) && $gender->trashed()) $gender->restore();
            if(empty($gender)) {
              $gender = new \M5\Models\MD11_genders();
              $gender->name = 'm';
              $gender->save();
            }

            // 2] f
            $gender = \M5\Models\MD11_genders::withTrashed()->where('name','f')->first();
            if(!empty($gender) && $gender->trashed()) $gender->restore();
            if(empty($gender)) {
              $gender = new \M5\Models\MD11_genders();
              $gender->name = 'f';
              $gender->save();
            }

            // 3] u
            $gender = \M5\Models\MD11_genders::withTrashed()->where('name','u')->first();
            if(!empty($gender) && $gender->trashed()) $gender->restore();
            if(empty($gender)) {
              $gender = new \M5\Models\MD11_genders();
              $gender->name = 'u';
              $gender->save();
            }


      // 2. Обновить состав прав типа "access" и "exec"

        // 1] Очистить md2000 и md2001
        DB::table('m5.md2000')->delete();
        DB::table('m5.md2001')->delete();

        // 2] Мягко удалить все права типов 'access' и 'exec' из md3_privileges
        \M5\Models\MD3_privileges::whereHas('privtypes', function($query){
          $query->whereIn('name', ['access', 'exec']);
        })->delete();

        // 3] Создать / Восстановить все права типа 'access'
        // - Связав их через md2000 с пакетами

          // Получить тип 'access'
          $access = \M5\Models\MD5_privtypes::where('name', 'access')->first();

          // Получить коллекцию всех D,L,W-пакетов
          $packages = r1_query(function(){
            return \M1\Models\MD2_packages::whereHas('packtypes', function($query){
              $query->whereIn('name', ["D", "L", "W"]);
            })->get();
          });

          // Если $packages не NULL
          if(!is_null($packages)) {

            // Создать / Восстановить / связать
            foreach($packages as $package) {

              // 3.1] Попробовать найти право для $package в md3_privileges
              $privilege = \M5\Models\MD3_privileges::withTrashed()->where('name', 'access_'.$package->id_inner)->first();

              // 3.2] Если не удалось найти, создать новое право
              if(empty($privilege)) {
                $privilege = new \M5\Models\MD3_privileges();
                $privilege->name = 'access_'.$package->id_inner;
                $privilege->id_privtype = $access->id;
                $privilege->save();
              }

              // 3.3] Если удалось, и оно мягко удалено, восстановить
              else if($privilege->trashed()) {
                $privilege->restore();
                $privilege->id_privtype = $access->id;
                $privilege->save();
              }

              // 3.4] Связать $privilege c $package через транс-пакетную связь
              // - Если она существует
              if(r1_rel_exists("M5", "MD3_privileges", "m1_packages")) {
                $privilege->m1_packages()->attach($package->id);
              }


            }

          }

        // 4] Создать / Восстановить все права типа 'exec'
        // - Связав их через md1009 с MD8_commands

          // Получить тип 'exec'
          $exec = \M5\Models\MD5_privtypes::where('name', 'exec')->first();

          // Получить коллекцию всех команд
          $commands = r1_query(function(){
            return \M1\Models\MD5_commands::all();
          });

          // Если $packages не NULL
          if(!is_null($packages)) {

            foreach($commands as $command) {

              // 3.1] Попробовать найти право для $command в md3_privileges
              $privilege = \M5\Models\MD3_privileges::withTrashed()->where('name', 'exec_'.$command->packages[0]->id_inner.'_'.$command->id_inner)->first();

              // 3.2] Если не удалось найти, создать новое право
              if(empty($privilege)) {
                $privilege = new \M5\Models\MD3_privileges();
                $privilege->name = 'exec_'.$command->packages[0]->id_inner.'_'.$command->id_inner;
                $privilege->id_privtype = $exec->id;
                $privilege->save();
              }

              // 3.3] Если удалось, и оно мягко удалено, восстановить
              else if($privilege->trashed()) {
                $privilege->restore();
                $privilege->id_privtype = $exec->id;
                $privilege->save();
              }

              // 3.4] Связать $command с $privilege
              // - Если она существует
              if(r1_rel_exists("M5", "MD3_privileges", "m1_packages")) {
                $privilege->m1_commands()->attach($command->id);
              }

            }

          }

      // 3. Обновить состав тегов для авто.прав типа "access" и "exec"

        // 1] Мягко удалить все теги, связанные с правами типов "access" и "exec"
        // - И их связи с md3_privileges

          // 1.1] Получить коллекцию этих тегов
          $tags2del = \M5\Models\MD4_tags::whereHas('privileges', function($query){
            $query->whereHas('privtypes', function($query){
              $query->whereIn('name', ['access', 'exec']);
            });
          })->get();

          // 1.2] Мягко удалить $tags2del, и удалить их связи с md3_privileges
          foreach($tags2del as $tag) {
            $tag->privileges()->detach();
            $tag->delete();
          }

        // 2] Создать / Восстановить теги для прав типа "access"
        // - Связав их через md1007 с MD3_privileges

          // Получить коллекцию всех "access"-прав из MD3_privileges
          $access_privileges = \M5\Models\MD3_privileges::whereHas('privtypes', function($query){
            $query->where('name','access');
          })->get();

          // Создать / Восстановить / связать
          foreach($access_privileges as $privilege) {

            // 2.1] Если связь m1_packages в модели MD3_privileges пакета M5 не существует
            // - Перейти к следующей итерации
            if(!r1_rel_exists("M5", "MD3_privileges", "m1_packages")) {
              write2log('Невозможно обновить авто. тег для права '.$privilege->name.' - связь m1_packages в модели MD3_privileges пакета M5 не существует', ['m5', 'C1_update']);
              continue;
            }

            // 2.2] Попробовать найти тег для $privilege в md4_tags
            $tag = \M5\Models\MD4_tags::withTrashed()->where('name', mb_strtolower($privilege->m1_packages[0]->id_inner))->first();

            // 2.3] Если не удалось найти, создать новый тег
            if(empty($tag)) {
              $tag = new \M5\Models\MD4_tags();
              $tag->name = mb_strtolower($privilege->m1_packages[0]->id_inner);
              $tag->save();
            }

            // 2.4] Если удалось, и оно мягко удалено, восстановить
            else if($tag->trashed()) $tag->restore();

            // 2.5] Связать $privilege с $tag
            $privilege->tags()->attach($tag->id);

          }

        // 3] Создать / Восстановить теги для 'exec'-прав
        // - Связав их через md1007 с MD3_privileges

          // Получить коллекцию всех 'exec'-прав из MD3_privileges
          $exec_privileges = \M5\Models\MD3_privileges::whereHas('privtypes', function($query){
            $query->where('name','exec');
          })->get();

          // Создать / Восстановить / связать
          foreach($exec_privileges as $privilege) {

            // 3.1] Если связь m1_commands в модели MD3_privileges пакета M5 не существует
            // - Перейти к следующей итерации
            if(!r1_rel_exists("M5", "MD3_privileges", "m1_commands")) {
              write2log('Невозможно обновить авто. тег для права '.$privilege->name.' - связь m1_commands в модели MD3_privileges пакета M5 не существует', ['m5', 'C1_update']);
              continue;
            }

            // 3.2] Попробовать найти тег для $privilege в md4_tags
            $tag = \M5\Models\MD4_tags::withTrashed()->where('name', mb_strtolower($privilege->m1_commands[0]->packages[0]->id_inner))->first();

            // 3.3] Если не удалось найти, создать новый тег
            if(empty($tag)) {
              $tag = new \M5\Models\MD4_tags();
              $tag->name = mb_strtolower($privilege->m1_commands[0]->packages[0]->id_inner);
              $tag->save();
            }

            // 3.4] Если удалось, и оно мягко удалено, восстановить
            else if($tag->trashed()) $tag->restore();

            // 3.5] Связать $privilege с $tag
            $privilege->tags()->attach($tag->id);

          }

      // 4. Добавить польз-м те права, с которыми у них общие теги
      // - Извлекать их по 100 штук за раз.
      \M5\Models\MD1_users::chunk(100, function($users){
        foreach($users as $user) {

          // 1] Получить все теги, связанные с $user
          $tags = $user->tags;
          $tags_ids = $tags->pluck('id');

          // 2] Получить коллекцию всех прав, имеющих теги $tags
          $privileges = \M5\Models\MD3_privileges::whereHas('tags', function($query) USE ($tags_ids) {

            $query->whereIn('id', $tags_ids);

          })->get();

          // 3] Связать $user с правами из $privileges
          // - Добавлять лишь те связи, которых ещё не существует
          foreach($privileges as $privilege) {
            if(!$privilege->users->contains($user->id))
              $privilege->users()->attach($user->id);
          }

        }
      });

      // 5. Добавить группам те права, с которыми у них общие теги
      // - Извлекать их по 100 штук за раз.
      \M5\Models\MD2_groups::chunk(100, function($groups){
        foreach($groups as $group) {

          // 1] Получить все теги, связанные с $group
          $tags = $group->tags;
          $tags_ids = $tags->pluck('id');

          // 2] Получить коллекцию всех прав, имеющих теги $tags
          $privileges = \M5\Models\MD3_privileges::whereHas('tags', function($query) USE ($tags_ids) {

            $query->whereIn('id', $tags_ids);

          })->get();

          // 3] Связать $group с правами из $privileges
          // - Добавлять лишь те связи, которых ещё не существует
          foreach($privileges as $privilege) {
            if(!$privilege->groups->contains($group->id))
              $privilege->groups()->attach($group->id);
          }

        }
      });

      // 6. Добавить все права группе с флагом "администраторы"

        // 1] Попробовать найти группу с флагом "администраторы"
        $admingroup = \M5\Models\MD2_groups::where('isadmin', 1)->first();

        // 2] Если $admingroup не пуста, наделить её всеми правами
        if(!empty($admingroup)) {
          \M5\Models\MD3_privileges::chunk(100, function($privileges) USE ($admingroup) {
            foreach($privileges as $privilege) {
              if(!$privilege->groups->contains($admingroup->id))
                $privilege->groups()->attach($admingroup->id);
            }
          });
        }

    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_update from M-package M5 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C1_update']);
        return [
          "status"  => -2,
          "data"    => $errortext
        ];
    }}); if(!empty($res)) return $res;


    //---------------------//
    // N. Вернуть статус 0 //
    //---------------------//
    DB::commit();
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

