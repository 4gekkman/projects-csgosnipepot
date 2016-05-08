<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get groups list (can use filters)
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
class C6_groups extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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

    //------------------------------------------------------------//
    // Вернуть информацию о группах с учётом фильтров и пагинации //
    //------------------------------------------------------------//
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

          "0.value"               => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of groups
          "1.value"               => ["r4_defined", "string"],                                        // Email
          "2.value.admin"         => ["r4_defined", "boolean"],                                       // Admin -> Admin
          "2.value.not_admin"     => ["r4_defined", "boolean"],                                       // Admin -> Not_admin
          "3.value"               => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of users
          "4.value"               => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of privileges
          "5.value"               => ["r4_defined", "regex:/^([1-9]+[0-9]*(,[1-9]+[0-9]*)*|)$/ui"],   // IDs of tags

        ]); if($validator['status'] == -1) {

          throw new \Exception($validator['data']);

        }
      }

      // 4. Сформировать запрос с учётом фильтров, извлечь данные

        // 4.1. Зачать формирование запроса
        $query = \M5\Models\MD2_groups::query();
        $groups_total = with(clone $query)->count();

        // 4.2. Учесть все фильтры
        if(!empty($filters)) {

          // 1] IDs of groups
          if($filters[0]['on'] === true) {
            $ids_of_groups = explode(',', $filters[0]['value']);
            if(!empty($ids_of_groups) && !empty($ids_of_groups[0]))
              $query->whereIn('id', $ids_of_groups);
          }

          // 2] Title
          if(!empty($filters[1]['value']) && $filters[1]['on'] === true) {
            $query->where('name', 'like', $filters[1]['value'] . '%');
          }

          // 3] Admin
          if($filters[2]['on'] === true) {
            $query->where(function($query) USE ($filters) {

              if($filters[2]['value']['admin'] === true && $filters[2]['value']['not_admin'] === false)
                $query->where('isadmin', 1);

              if($filters[2]['value']['not_admin'] === true && $filters[2]['value']['admin'] === false)
                $query->where('isadmin', 0);

            });
          }

          // 4] IDs of users
          if($filters[3]['on'] === true) {
            $ids_of_users = explode(',', $filters[3]['value']);
            if(!empty($ids_of_users) && !empty($ids_of_users[0]))
              $query->whereHas('users', function($query) USE ($ids_of_users) {
                $query->whereIn('id', $ids_of_users);
              });
          }

          // 5] IDs of privileges
          if($filters[4]['on'] === true) {
            $ids_of_privs = explode(',', $filters[4]['value']);
            if(!empty($ids_of_privs) && !empty($ids_of_privs[0])) {
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
            }
          }

          // 6] IDs of tags
          if($filters[5]['on'] === true) {
            $ids_of_tags = explode(',', $filters[5]['value']);
            if(!empty($ids_of_tags) && !empty($ids_of_tags[0]))
              $query->whereHas('tags', function($query) USE ($ids_of_tags) {
                $query->whereIn('id', $ids_of_tags);
              });
          }

        }

        // 4.3. Получить pages_total и items_at_page
        $groups_filtered      = with(clone $query)->count();
        $groups_filtered_ids  = with(clone $query)->pluck('id');
        $items_at_page        = $this->data['items_at_page'];
        $pages_total          = (+$groups_filtered < +$items_at_page) ? 1 : (int)ceil(+with(clone $query)->count()/$items_at_page);
        $page                 = $this->data['page'];

        // 4.4. Получить коллекцию групп
        $groups = with(clone $query)->skip($items_at_page*(+$page-1))->take($items_at_page)->get();

      // 5. Вернуть результат
      return [
        "status"  => 0,
        "data"    => [
          "groups"                => $groups,
          "pages_total"           => $pages_total,
          "groups_total"          => $groups_total,
          "groups_filtered"       => $groups_filtered,
          "items_at_page"         => $this->data['items_at_page'],
          "groups_filtered_ids"   => $groups_filtered_ids,
          "selected_group_ids"    => (!empty($this->data['selected_group_ids']) && is_array($this->data['selected_group_ids'])) ? $this->data['selected_group_ids'] : []
        ]
      ];

    } catch(\Exception $e) {
        $errortext = 'Invoking of command C6_groups from M-package M5 have ended with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M5', 'C6_groups']);
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

