<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get users list (can use filters)
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
class C5_users extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию входящих параметров
     *  2. Декодировать json-строку с фильтрами
     *  3. Провести валидацию значений фильтров, если $filters не пуста
     *  4. Сформировать запрос с учётом фильтров, извлечь данные
     *  5. Вернуть результат
     *
     *  N. Вернуть статус 0
     *
     */

    //------------------------------------------------------------------//
    // Вернуть информацию о пользователях с учётом фильтров и пагинации //
    //------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [

        "page"            => ["required", "numeric"],
        "pages_total"     => ["r4_defined", "regex:/^([1-9]+[0-9]*|)$/ui"],
        "items_at_page"   => ["required", "numeric"],
        "filters"         => ["r4_defined", "json"]

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Декодировать json-строку с фильтрами
      $filters = json_decode($this->data['filters'], true);

      // 3. Провести валидацию значений фильтров, если $filters не пуста
      if(!empty($filters)) {
        $validator = r4_validate($filters, [

          "0.value"               => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of users
          "1.value"               => ["r4_defined", "string"],                                        // Email
          "2.value"               => ["r4_defined", "regex:/^[0-9]*$/ui"],                            // Phone
          "3.value"               => ["r4_defined", "regex:/^[a-zа-яё]*$/ui"],                        // Name
          "4.value"               => ["r4_defined", "regex:/^[a-zа-яё]*$/ui"],                        // Surname
          "5.value"               => ["r4_defined", "regex:/^[a-zа-яё]*$/ui"],                        // Patronymic
          "6.value.male"          => ["r4_defined", "boolean"],                                       // Gender -> Male
          "6.value.female"        => ["r4_defined", "boolean"],                                       // Gender -> Female
          "6.value.undefined"     => ["r4_defined", "boolean"],                                       // Gender -> Undefined
          "7.value.anonymous"     => ["r4_defined", "boolean"],                                       // Anonymity -> Anonymous
          "7.value.not_anonymous" => ["r4_defined", "boolean"],                                       // Anonymity -> Not_anonymous
          "8.value.blocked"       => ["r4_defined", "boolean"],                                       // Block -> Blocked
          "8.value.not_blocked"   => ["r4_defined", "boolean"],                                       // Block -> Not_blocked
          "9.value.approved"      => ["r4_defined", "boolean"],                                       // Email approvement -> Approved
          "9.value.not_approved"  => ["r4_defined", "boolean"],                                       // Email approvement -> Not_approved
          "10.value.approved"     => ["r4_defined", "boolean"],                                       // Phone approvement -> Approved
          "10.value.not_approved" => ["r4_defined", "boolean"],                                       // Phone approvement -> Not_approved
          "11.value"              => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of groups
          "12.value"              => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of privileges
          "13.value"              => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of tags
          "14.value.admin"        => ["r4_defined", "boolean"],                                       // Admin -> Admin
          "14.value.not_admin"    => ["r4_defined", "boolean"],                                       // Admin -> Not_admin

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }
      }

      // 4. Сформировать запрос с учётом фильтров, извлечь данные

        // 4.1. Зачать формированиез запроса
        $query = \M5\Models\MD1_users::query();
        $users_total = with(clone $query)->count();

        // 4.2. Учесть все фильтры, если $filters не пуста
        if(!empty($filters)) {

          // 1] IDs of users
          if($filters[0]['on'] === true) {
            $ids_of_users = explode(',', $filters[0]['value']);
            if(!empty($ids_of_users) && !empty($ids_of_users[0]))
              $query->whereIn('id', $ids_of_users);
          }

          // 2] Email
          if(!empty($filters[1]['value']) && $filters[1]['on'] === true) {
            $query->where('email', 'like', $filters[1]['value'] . '%');
          }

          // 3] Phone
          if(!empty($filters[2]['value']) && $filters[2]['on'] === true) {
            $query->where('phone', 'like', $filters[2]['value'] . '%');
          }

          // 4] Name
          if(!empty($filters[3]['value']) && $filters[3]['on'] === true) {
            $query->where('name', 'like', $filters[3]['value'] . '%');
          }

          // 5] Surname
          if(!empty($filters[4]['value']) && $filters[4]['on'] === true) {
            $query->where('surname', 'like', $filters[4]['value'] . '%');
          }

          // 6] Patronymic
          if(!empty($filters[5]['value']) && $filters[5]['on'] === true) {
            $query->where('patronymic', 'like', $filters[5]['value'] . '%');
          }

          // 7] Gender
          if($filters[6]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[6]['value']['male'] === true)
                $query->whereHas('genders', function($query){
                  $query->where('name', 'm');
                });

              if($filters[6]['value']['female'] === true)
                $query->orWhereHas('genders', function($query){
                  $query->where('name', 'f');
                });

              if($filters[6]['value']['undefined'] === true)
                $query->orWhereHas('genders', function($query){
                  $query->where('name', 'u');
                });

            });
          }

          // 8] Anonymity
          if($filters[7]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[7]['value']['anonymous'] === true)
                $query->where('isanonymous', 1);

              if($filters[7]['value']['not_anonymous'] === true)
                $query->orWhere('isanonymous', 0);

            });
          }

          // 9] Block
          if($filters[8]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[8]['value']['blocked'] === true)
                $query->where('is_blocked', 1);

              if($filters[8]['value']['not_blocked'] === true)
                $query->orWhere('is_blocked', 0);

            });
          }

          // 10] Email approvement
          if($filters[9]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[9]['value']['approved'] === true)
                $query->where('is_email_approved', 1);

              if($filters[9]['value']['not_approved'] === true)
                $query->orWhere('is_email_approved', 0);

            });
          }

          // 11] Phone approvement
          if($filters[10]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[10]['value']['approved'] === true)
                $query->where('is_phone_approved', 1);

              if($filters[10]['value']['not_approved'] === true)
                $query->orWhere('is_phone_approved', 0);

            });
          }

          // 12] IDs of groups
          if($filters[11]['on'] === true) {
            $ids_of_groups = explode(',', $filters[11]['value']);
            if(!empty($ids_of_groups) && !empty($ids_of_groups[0]))
              $query->whereHas('groups', function($query) USE ($ids_of_groups) {
                $query->whereIn('id', $ids_of_groups);
              });
          }

          // 13] IDs of privileges
          if($filters[12]['on'] === true) {
            $ids_of_privs = explode(',', $filters[12]['value']);
            if(!empty($ids_of_privs) && !empty($ids_of_privs[0]))
              $query->where(function($query) USE ($ids_of_privs) {

                $query->whereHas('privileges', function($query) USE ($ids_of_privs) {
                  $query->whereIn('id', $ids_of_privs);
                })->orWhereHas('groups', function($query) USE ($ids_of_privs){

                  $query->where(function($query) USE ($ids_of_privs) {
                    $query->whereHas('privileges', function($query) USE ($ids_of_privs) {
                      $query->whereIn('id', $ids_of_privs);
                    })->orWhereHas('tags', function($query) USE ($ids_of_privs){
                      $query->whereHas('privileges', function($query) USE ($ids_of_privs) {
                        $query->whereIn('id', $ids_of_privs);
                      });
                    })->orWhere(function($query){
                      $query->where('isadmin', 1);
                    });
                  });

                })->orWhereHas('tags', function($query) USE ($ids_of_privs){
                  $query->whereHas('privileges', function($query) USE ($ids_of_privs) {
                    $query->whereIn('id', $ids_of_privs);
                  });
                });

              });
          }

          // 14] IDs of tags
          if($filters[13]['on'] === true) {
            $ids_of_tags = explode(',', $filters[13]['value']);
            if(!empty($ids_of_tags) && !empty($ids_of_tags[0]))
              $query->whereHas('tags', function($query) USE ($ids_of_tags) {
                $query->whereIn('id', $ids_of_tags);
              });
          }

          // 15] Admin
          if($filters[14]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[14]['value']['admin'] === true && $filters[14]['value']['not_admin'] === false)
                $query->whereHas('groups', function($query){
                  $query->where('isadmin', 1);
                });

              if($filters[14]['value']['not_admin'] === true && $filters[14]['value']['admin'] === false)
                $query->whereDoesntHave('groups', function($query){
                  $query->where('isadmin', 1);
                });

            });
          }

        }

        // 4.3. Получить pages_total и items_at_page
        $users_filtered       = with(clone $query)->count();
        $users_filtered_ids   = with(clone $query)->pluck('id');
        $items_at_page        = $this->data['items_at_page'];
        $pages_total          = (+with(clone $query)->count() < +$items_at_page) ? 1 : (int)ceil(+with(clone $query)->count()/$items_at_page);
        $page                 = $this->data['page'];

        // 4.4. Получить коллекцию пользователей
        $users = with(clone $query)->skip($items_at_page*(+$page-1))->take($items_at_page)->get();

        // 4.5. Убрать из $users поле "password_hash"
        $users = $users->map(function(&$value, $key){
          $value->password_hash = "";
          return $value;
        });

      // 5. Вернуть результат
      return [
        "status"  => 0,
        "data"    => [
          "users"               => $users,
          "pages_total"         => $pages_total,
          "users_total"         => $users_total,
          "users_filtered"      => $users_filtered,
          "items_at_page"       => $this->data['items_at_page'],
          "genders"             => \M5\Models\MD11_genders::all(),
          "users_filtered_ids"  => $users_filtered_ids,
          "selected_user_ids"   => (!empty($this->data['selected_user_ids']) && is_array($this->data['selected_user_ids'])) ? $this->data['selected_user_ids'] : []
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C5_users from M-package M5 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C5_users']);
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

