<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Save image from file, use specified parameters while saving
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        file          // Файл-изображение для сохранения
 *        group         // Группа параметров из конфига
 *        params        // Параметры сохранения изображения
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

  namespace M7\Commands;

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
class C1_saveimage extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
     *  1. Провести валидацию
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //----------------------------------------------------------//
    // Сохранить изображение в соотв. с переданными параметрами //
    //----------------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Провести валидацию
      $validator = r4_validate($this->data, [

        "file"                                    => ["required", "image"],
        "group"                                   => ["sometimes", "string"],
        "params"                                  => ["sometimes", "array"],
        "params.folderpath_relative_to_basepath"  => ["sometimes", "string"],
        "params.should_save_original"             => ["sometimes", "boolean"],
        "params.should_save_not_filtered_images"  => ["sometimes", "boolean"],
        "params.sizes"                            => ["sometimes", "array"],
        "params.sizes.*"                          => ["sometimes", "array"],
        "params.sizes.*.*"                        => ["sometimes", "numeric"],
        "params.types"                            => ["sometimes", "array"],
        "params.types.*"                          => ["sometimes", "in:image/jpeg,image/png,image/gif"],
        "params.quality"                          => ["sometimes", "numeric"],
        "params.filters"                          => ["sometimes", "array"],
        "params.filters.*"                        => ["sometimes", "string"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 2. Получить параметры по умолчанию и группы параметров из конфига
      $default_parameters = config("M7.default_parameters");
      $parameter_groups = config("M7.parameter_groups");

      // 3. Если $default_parameters пуста, завершить с ошибкой
      if(empty($default_parameters))
        throw new \Exception("Can't find parameter default_parameters in M7 config.");

      // 4. Определить итоговый набор параметров
      $params_final = call_user_func(function() USE ($default_parameters, $parameter_groups) {

        // 1] Подготовить массив для результата, с параметрами по умолчанию
        $result = $default_parameters;

        // 2] Если $this->data['group'] не пуста и есть в $parameter_groups
        // - Заменить в $result присутствующие в $parameter_groups[$this->data['group']] параметры
        if(!empty($this->data['group']) && array_key_exists($this->data['group'], $parameter_groups)) {

          foreach($parameter_groups[$this->data['group']] as $key => $value) {
            $result[$key] = $value;
          }

        }

        // 3] Заменить в $result присутствующие в $this->data['params'] параметры
        if(!empty($this->data['params'])) {

          foreach($this->data['params'] as $key => $value) {
            $result[$key] = $value;
          }

        }

        // n] Вернуть результат
        return $result;

      });

      // 5. Провести валидацию итогового набора параметров
      $validator = r4_validate($params_final, [

        "folderpath_relative_to_basepath"  => ["required", "string"],
        "should_save_original"             => ["required", "boolean"],
        "should_save_not_filtered_images"  => ["required", "boolean"],
        "sizes"                            => ["required", "array"],
        "sizes.*"                          => ["required", "array"],
        "sizes.*.*"                        => ["required", "numeric"],
        "types"                            => ["required", "array"],
        "types.*"                          => ["required", "in:image/jpeg,image/png,image/gif"],
        "quality"                          => ["required", "numeric"],
        "filters"                          => ["required", "array"],
        "filters.*"                        => ["required", "string"],

      ]); if($validator['status'] == -1) {

        throw new \Exception($validator['data']);

      }

      // 6. Создать оригинальный экземпляр II из оригинала изображения
      $image = \Intervention\Image\ImageManagerStatic::make($this->data['file']);

      // 7. Проверить mime-тип изображения
      if(!in_array($image->mime(), ['image/jpeg', 'image/png', 'image/gif']))
        throw new \Exception('Не поддерживаемый mime-тип');

      // 8. Подготовить md5-хэш, который будет служить именем каталога изображения
      // - Берётся md5-хэш, который формируется из суммы следующих строк:
      //
      //   1] Закодированной в формате "data-url" строки изображения.
      //   2] Ширины изображения.
      //   3] Высоты изображения.
      //   4] MIME-типа изображения.
      //   5] Размера изображения в байтах.
      //   6] Случайно сгенерированного
      //

        // 8.1. Написать функцию для получения имени
        $make_name = function($modifier = "") USE ($image) {

          // 1] Сформировать имя
          $name = md5(
              (string) $image->encode('data-url') .
              $image->width() .
              $image->height() .
              $image->mime() .
              $image->filesize() .
              mt_rand(0, 999999999999999)
          );

          // 2] Вырезать из него запрещённые в Linux и Windows символы
          $name = preg_replace("#[/\\\\:*?\"<>]+#ui", "", $name);

          // 3] Вернуть результат
          return $name;

        };

        // 8.2. Получить имя и путь каталога изображения
        $name = $make_name();
        $path = base_path($params_final['folderpath_relative_to_basepath'] . '/' . $name);
        while(file_exists($path)) {
          $name = $make_name(mt_rand(0, 999999999999999) . "");
          $path = base_path($params_final['folderpath_relative_to_basepath'] . '/' . $name);
        }

      // 9. Создать каталог изображения, используя имя $name
      $fs = r1_fs($params_final['folderpath_relative_to_basepath']);
      $fs->makeDirectory($name);

      // 10. Получить информацию обо всех доступных фильтрах
      // - Которые находятся в папке "Filters" в M7
      $filters = call_user_func(function(){

        // 1] Подготовить массив для свежих данных о фильтрах //
        $newfilters = [];

        // 2] Выяснить имя каталога-домена
        $domain = basename(dirname(dirname(dirname(dirname(__DIR__)))));

        // 3] Получить путь к каталогу Filters модуля M6 относительно корня приложения
        $path = "vendor/4gekkman/M7/Filters";

        // 4] Получить массив путей ко всем каталогам-фильтрам
        $fs = r1_fs("");
        $filters_paths = $fs->directories($path);

        // 5] Пробежатсья по каждому фильтру
        foreach($filters_paths as $filterpath) {

          // 5.1] Если файл config.json отсутствует, перейти к след.итерации
          if(!$fs->exists($filterpath.'/config.json')) continue;

          // 5.2] Если файл Filter.php отсутствует, перейти к след.итерации
          if(!$fs->exists($filterpath.'/Filter.php')) continue;

          // 5.3] Получить имя каталога с фильтром
          $filtercat_name = basename($filterpath);

          // 5.4] Если $filtercat_name начнается с символа _ , перейти к след.итерации
          if(mb_substr($filtercat_name, 0, 1, 'utf-8') == '_') continue;

          // 5.5] Извлечь config.json, преобразовать его из json в массив
          $config = json_decode($fs->get($filterpath.'/config.json'), true);

          // 5.6] Провести проверку на ошибки

            // 1) Имя фильтра
            if(empty($config['name']) || !preg_match("/^[-0-9а-яёa-z\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $config['name']))
              return "Для фильтра ".$filterpath.", в config.json, не указано значение для 'name', или оно содержит недопустимы символы. Допустимые: а-яёa-z0-9.-";

            // 2) Описание фильтра
            if(empty($config['description']) || !preg_match("/^[-0-9а-яёa-z\/\\\\_!№@#$&:()\[\]{}*%?\"'`.,\r\n ]*$/ui", $config['description']))
              return "Для фильтра ".$filterpath.", в config.json, не указано значение для 'description', или оно содержит недопустимы символы. Допустимые: а-яёa-z0-9., :;()!?@_-/\\\\";

          // 5.7] Добавить информацию об этом фильтре в $newfilters
          $newfilters[$filtercat_name] = [
            "name"          => $config['name'],
            "name_folder"   => $filtercat_name,
            "description"   => $config['description']
          ];

        }

        // 6] Вернуть результаты
        return $newfilters;

      });

      // 11. Получить массив фильтров, которые надо применять к изображению

        // 11.1. Получить
        $filters2apply = collect($filters)->keys()->intersect($params_final['filters']); //->values()->toArray();

        // 11.2. Сформировать из массива имён массив путей
        // - В стиле: "\\M7\\Filters\\<имя фильтра>".
        $filters2apply_paths = collect($filters2apply)->map(function($value, $key){

          return "\\M7\\Filters\\" . $value . "\\Filter";

        });

      // 12. Написать функцию для вычисления расширения по mime-типу
      $get_res_by_mime = function($mime){

        if($mime == 'image/jpeg') return ".jpg";
        if($mime == 'image/png') return ".png";
        if($mime == 'image/gif') return ".gif";
        return "";

      };

      // 12. Сохранить изображения

        // 12.1. Оригинал
        $image->save($path . '/original' . $get_res_by_mime($image->mime()), 0);

        // 12.2. Оригинал фильтрованный
        call_user_func(function() USE ($image, $filters2apply_paths, $path, $get_res_by_mime, $params_final) {

          // 1] Получить клон изображения
          $clone = clone $image;

          // 2] Применить к клону все фильтры из $filters2apply
          foreach($filters2apply_paths as $filter) {
            $clone->filter(new $filter());
          }

          // 3] Сохранить
          $clone->save($path . '/original_filtered' . $get_res_by_mime($image->mime()), $params_final['quality']);

        });

        // 12.3. Разные размеры, типы, фильтрованные/нефильтрованные



        //$params_final['sizes']
        //$params_final['types']
        //$params_final['quality']







//      // 8. Написать функцию для вычисления ID для нового изображения
//      // - Где $folder это путь относительно корня проекта.
//      $new_id = function($folder){
//
//        // 1] Подготовить переменную для результата
//        $result = 1;
//
//        // 2] Создать экземпляр ФС в $folder
//        $fs = r1_fs($folder);
//
//        // 3] Попробовать извлечь файл lastid
//        $index = $fs->exists("lastid") ? $fs->get('lastid') : "";
//
//        // 4] Если $index пуст:
//        if(empty($index)) {
//
//          // 4.1] Извлечь имена всех каталогов
//          $dirs = $fs->directories();
//
//          // 4.2] Отфильтровать из массива $dirs все не числовые имена
//          // - Получив коллекцию.
//          $dirs = collect($dirs)->filter(function($item){
//            return is_numeric($item);
//          });
//
//          // 4.3] Получить максимальное число из коллекции $dirs
//          $max = $dirs->max();
//
//          // 4.4]
//
//
//        }
//
//
//        // n] Вернуть результат
//        return $result;
//
//      };





//      $fs = r1_fs('public');
//      write2log($fs->get('index1.php'), []);



//      $image_clone = clone $image;
//      $image_clone->greyscale();
//
//      $image->save(public_path('image1'));
//      $image_clone->save(public_path('image2'));






      //$image->save(public_path().'/image');




    DB::commit(); } catch(\Exception $e) {

      write2log($params_final, []);
      write2log($name, []);

        // Если ошибка, удалить созданную папку изображения
        if(!empty($params_final) && !empty($name)) {
          $fs = r1_fs($params_final['folderpath_relative_to_basepath']);
          $fs->deleteDirectory($name);
        }

        $errortext = 'Invoking of command C1_saveimage from M-package M7 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M7', 'C1_saveimage']);
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

