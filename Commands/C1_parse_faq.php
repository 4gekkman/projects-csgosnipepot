<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Parse FAQ data from its root folder to M12 database
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
 *  Стратегия парсинга
 *  ------------------
 *
 *    2. Парсинг групп
 *      - Делаем цикл по всем доступным ныне FAQам.
 *        ▪ Получаем все доступные в FAQе папки.
 *        ▪ Ищем в каждой папке файл "_files/meta.info".
 *        ▪ Таким образом, наполняем массив доступных групп для данного FAQа.
 *        ▪ Синхронизируем данные в БД с полученными данными:
 *          ▪ Удаляем из БД все не найденные в данных группы, вместе со связями из md1000/md1001.
 *          ▪ Если в БД уже есть такая группа, обновляем её.
 *          ▪ Если в БД ещё нет такой группы, добавляем.
 *          ▪ Делаем save().
 *          ▪ Добавляем связи в md1000.
 *          ▪ Копируем аватар группы в public с заменой.
 *
 *    3. Парсинг статей и кодов стран
 *      - Делаем цикл по всем доступным ныне группам.
 *        ▪ Получаем все доступные в группе папки, кроме _files.
 *        ▪ Ищем в каждой папке файл "_files/meta.info".
 *        ▪ Таким образом, наполняем массив доступных статей для данной группы.
 *        ▪ Синхронизируем данные в БД с полученными данными:
 *          ▪ Удаляем из БД все не найденные в данных статьи, вместе со связями из md1001/md1002.
 *          ▪ Если в БД уже есть такая статья, обновляем её.
 *          ▪ Если в БД ещё нет такой статьи, добавляем.
 *          ▪ Делаем save().
 *          ▪ Если в БД ещё нет такого кода страны статьи, добавляем.
 *          ▪ Делаем save().
 *          ▪ Добавляем связи в md1001 и md1002.
 *          ▪ Копируем файлы статьи в public с заменой.
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M12\Commands;

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
  use GrahamCampbell\Markdown\Facades\Markdown;

//---------//
// Команда //
//---------//
class C1_parse_faq extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

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
    config(['filesystems.disks.local.root' => base_path('')]);
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
     *  1. Получить из конфига необходимые относительные пути
     *  2. Проверить, существует ли в ФС каталог по указанному пути     *
     *  3. Парсинг FAQов
     *  4. Парсинг групп
     *  5. Парсинг статей и кодов стран
     *
     *
     *  N. Вернуть статус 0
     *
     */

    //-----------------------------------------------------//
    // Parse FAQ data from its root folder to M12 database //
    //-----------------------------------------------------//
    $res = call_user_func(function() { try { DB::beginTransaction();

      // 1. Получить из конфига необходимые относительные пути

        // 1] Получить относ.путь к корн.каталогу, проверить его существование
        $root = config('M12.faq_root_folder');
        if(empty($root))
          throw new \Exception('В конфиге M12 не найден путь faq_root_folder к корневому каталогу относительно корня проекта.');

        // 2] Получить относ.путь к папке, куда сохранять публичные ресурсы FAQ
        $public = config('M12.public_faq_folder');
        if(empty($root))
          throw new \Exception('В конфиге M12 не найден путь public_faq_folder к папке, куда сохранять публичные ресурсы FAQ.');

      // 2. Проверить, существует ли в ФС каталог по указанному пути
      $is_root_cat = file_exists(base_path($root));
      if(empty($is_root_cat))
        throw new \Exception('Не найден корневой каталог FAQ, путь к которому указан в конфиге M12, в faq_root_folder.');

      // 3. Парсинг FAQов
      call_user_func(function() USE ($root) {

        // 3.1. Получить все доступные в $root FAQи
        // - Формат: <имя FAQа> => <путь к FAQу относ.корня>
        $faqs_in_root = call_user_func(function() USE ($root) {

          // 1] Подготовить массив для результатов
          $results = [];

          // 2] Получить пути ко всем FAQам относ.корня
          $faq_paths = $this->storage->directories($root);

          // 3] Наполнить $results
          foreach($faq_paths as $path) {
            $results[preg_replace('#^.*\/#ui', '', $path)] = $path;
          }

          // n] Вернуть результат
          return $results;

        });

        // 3.2. Получить из БД доступный список FAQов
        $faqs_in_db = \M12\Models\MD1_faqs::get();

        // 3.3. Удалить из БД все FAQи, которых нет в $faqs_in_root
        call_user_func(function() USE ($faqs_in_db, $faqs_in_root) {

          foreach($faqs_in_db as $faq) {

            // 1] Если $db_faq_name есть в $faqs_in_root, перейти к след.итерации
            if(in_array($faq['name'], array_keys($faqs_in_root))) continue;

            // 2] В противном случае:
            else {

              // 2.1] Отвязать $faq от всех групп
              $faq->groups()->detach();

              // 2.2] Удалить $faq из БД
              $faq->delete();

            }

          }

        });

        // 3.4. Установить минимально возможный ID автоинкремента
        call_user_func(function(){

          // 1] Получить значениед для автоинкремента
          $autoincrement_value = call_user_func(function(){

            // 1.1] Если MD1_faqs пуст, то вернуть 1
            if(\M12\Models\MD1_faqs::count() == 0) return 1;

            // 1.2] В противном случае, вернуть ID последней записи + 1
            else
              return +(\M12\Models\MD1_faqs::orderBy('id', 'desc')->first()->id) + 1;

          });

          // 2] Установить счётчик автоинкремента
          DB::statement('ALTER TABLE m12.md1_faqs AUTO_INCREMENT = '.$autoincrement_value.';');

        });

        // 3.5. Добавить из $faqs_in_root в БД те FAQи, которых ещё нет там
        call_user_func(function() USE ($faqs_in_root) {

          // 1] Получить массив имён оставшихся в БД FAQов на данный момент
          $faqs_in_db_names = \M12\Models\MD1_faqs::get()->pluck('name')->toArray();

          // 2] Пробежаться по $faqs_in_root
          foreach($faqs_in_root as $name => $path) {

            // 2.1] Если $name есть в $faqs_in_db_names, перейти к след.итерации
            if(in_array($name, $faqs_in_db_names)) continue;

            // 2.2] В противном случае, добавить в БД новый FAQ
            else {

              $newfaq = new \M12\Models\MD1_faqs();
              $newfaq->name = $name;
              $newfaq->save();

            }

          }

        });

      });

      // 4. Парсинг групп
      call_user_func(function() USE ($root, $public) {

        // 4.1. Получить из БД доступный список FAQов
        $faqs_in_db = \M12\Models\MD1_faqs::get();

        // 4.2. Пробежаться по всем $faqs_in_db
        foreach($faqs_in_db as $faq) {

          // 4.2.1. Получить все доступные в $faq группы
          // - Формат: <имя группы> => <путь к группе относ.корня>
          $groups_in_root = call_user_func(function() USE ($root, $faq) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Получить пути ко всем группам в FAQе $faq относ.корня
            $group_paths = $this->storage->directories($root.'/'.$faq['name']);

            // 3] Наполнить $results
            foreach($group_paths as $path) {
              $results[preg_replace('#^.*\/#ui', '', $path)] = $path;
            }

            // n] Вернуть результат
            return $results;

          });

          // 4.2.2. Получить массив данных по всем группам
          // - Формат:
          //
          //   [
          //     'name'               => ['ru' => 'Группа', 'en' => 'Group'],
          //     'description'        => ['ru' => 'Описание', 'en' => 'Description'],
          //     'avatar'             => 'avatar.png',
          //     'uri_group_relative' => 'classicgame/howtoplay',
          //   ]
          //
          $groups_in_root_data = call_user_func(function() USE ($groups_in_root, $faq) {

            // 1] Подготовить массив для результата
            $results = [];

            // 2] Наполнить $results
            foreach($groups_in_root as $name => $path) {

              // 2.1] Попробовать извлечь файл group.info для группы $name
              $metainfo = json_decode($this->storage->get($path.'/_files/group.info'), true);
              if(empty($metainfo))
                throw new \Exception('В group.json FAQа '.$faq['name'].' группы '.$name.' допущена ошибка.');

              // 2.2] Провести валидацию $metainfo
              $validator = r4_validate($metainfo, [
                "name"                => ["required", "array"],
                "description"         => ["required", "array"],
                "avatar"              => ["required", "string"]
              ]); if($validator['status'] == -1) {
                throw new \Exception($validator['data']);
              }

              // 2.3] Добавить данные в $results
              array_push($results, [
                "name_folder"         => $name,
                "name"                => $metainfo['name'],
                "description"         => $metainfo['description'],
                "avatar"              => $metainfo['avatar'],
                "uri_group_relative"  => $faq['name']."/".$name,
              ]);

            }

            // n] Вернуть результаты
            return $results;

          });

          // 4.2.3. Получить из БД доступный список групп
          $groups_in_db = \M12\Models\MD2_groups::get();

          // 4.2.4. Удалить из БД все группы, их их связи md1000/md1001
          call_user_func(function() USE ($groups_in_db, $groups_in_root_data) {

            // 1] Удалить все связи $groups_in_db
            foreach($groups_in_db as $group) {

              $group->faqs()->detach();
              $group->articles()->detach();

            }

            // 2] Удалить все группы
            \M12\Models\MD2_groups::query()->delete();

          });

          // 4.2.5. Установить автоинкремент, равный 1
          DB::statement('ALTER TABLE m12.md2_groups AUTO_INCREMENT = 1;');

          // 4.2.6. Добавить в БД все группы из $groups_in_root_data
          call_user_func(function() USE ($groups_in_root_data, $faq, $root, $public) {
            foreach($groups_in_root_data as $group) {

              // 1] Добавить данные о $group в БД
              $newgroup = new \M12\Models\MD2_groups();
              $newgroup->name                 = json_encode($group['name'], JSON_UNESCAPED_UNICODE);
              $newgroup->description          = json_encode($group['description'], JSON_UNESCAPED_UNICODE);
              $newgroup->avatar               = $group['avatar'];
              $newgroup->uri_group_relative   = $group['uri_group_relative'];
              $newgroup->save();

              // 2] Связать $newgroup с $faq
              if(!$newgroup->faqs->contains($faq['id']))
                $newgroup->faqs()->attach($faq['id']);

              // 3] Скопировать аватар группы в public

                // 3.1] Получить относительные пути источника/назначения
                $path_source  = $root.'/'.$faq['name'].'/'.$group['name_folder'].'/_files/'.$group['avatar'];
                $path_dest    = $public.'/'.$faq['name'].'/'.$group['name_folder'].'/'.$group['avatar'];

                // 3.2] Удалить аватар, если он уже есть в public
                if($this->storage->exists($path_dest))
                  $this->storage->delete($path_dest);

                // 3.3] Скопировать
                $this->storage->copy(
                  $path_source,
                  $path_dest
                );

            }
          });

        }

      });

      // 5. Парсинг статей и кодов стран
      call_user_func(function() USE ($root, $public) {

        // 5.1. Получить из БД доступный список групп
        $groups_in_db = \M12\Models\MD2_groups::get();

        // 5.2. Пробежаться по всем $faqs_in_db
        foreach($groups_in_db as $group) {

          // 5.2.1. Получить все доступные в $group статьи
          // - Формат: <имя статьи> => <путь к статье относ.корня>
          $articles_in_root = call_user_func(function() USE ($root, $group) {

            // 1] Подготовить массив для результатов
            $results = [];

            // 2] Получить пути ко всем статьям в группе $group относ.корня
            $article_paths = $this->storage->directories($root.'/'.$group['uri_group_relative']);

            // 3] Наполнить $results
            foreach($article_paths as $path) {

              // 3.1] Получить имя папки со статьёй
              $article_folder_name = preg_replace('#^.*\/#ui', '', $path);

              // 3.2] Если это не _files, добавить в $results
              if($article_folder_name != '_files')
                $results[$article_folder_name] = $path;

            }

            // n] Вернуть результат
            return $results;

          });

          // 5.2.2. Получить массив данных по всем статьям
          // - Формат:
          //
          //   [
          //     'name'                 => ['ru' => 'Группа', 'en' => 'Group'],
          //     'description'          => ['ru' => 'Описание', 'en' => 'Description'],
          //     'html'                 => <html код статьи>,
          //     'uri_article_relative' => 'classicgame/howtoplay',
          //     'author'               => ['ru' => ['name' => "Иван Иванов", 'url' => 'https://vk.com/mudakoff']]
          //   ]
          //
          $articles_in_root_data = call_user_func(function() USE ($articles_in_root, $group, $root) {

            // 1] Подготовить массив для результата
            $results = [];

            // 2] Наполнить $results
            foreach($articles_in_root as $name => $path) {

              // 2.1] Попробовать извлечь файл article.info для статьи $name
              $metainfo = json_decode($this->storage->get($path.'/_files/article.info'), true);
              if(empty($metainfo))
                throw new \Exception('В article.info статьи '.$group['uri_group_relative'].'/'.$name.' допущена ошибка.');

              // 2.2] Провести валидацию $metainfo
              $validator = r4_validate($metainfo, [
                "name"                => ["required", "array"],
                "description"         => ["required", "array"],
                "author"              => ["required", "array"]
              ]); if($validator['status'] == -1) {
                throw new \Exception($validator['data']);
              }

              // 2.3] Подготовить HTML со статьями
              // - В формате:
              //
              //    [
              //      'ru' => <html>,
              //      'en' => <html>
              //    ]
              //
              $html = call_user_func(function() USE ($group, $name, $root, $path) {

                // 2.3.1] Подготовить массив для результатов
                $results = [];

                // 2.3.2] Получить пути ко всем статьям в группе $group относ.корня
                $article_multilang_paths = $this->storage->files($path.'/_articles');

                // 2.3.3] Наполнить $results
                foreach($article_multilang_paths as $article) {

                  // 1] Получить локаль статьи (ru, en, pl, и т.д.)
                  $locale = preg_replace('#.md$#ui', '', preg_replace('#^.*\/#ui', '', $article));
                  if(empty($locale)) continue;

                  // 2] Получить содержимое статьи в формате .md
                  $article_md = $this->storage->get($article);

                  // 3] Преобразовать $article_md в html
                  $article_html = Markdown::convertToHtml($article_md);

                  // 4] Добавить данные в $results
                  $results[$locale] = $article_html;

                }

                // 2.3.n] Вернуть результаты
                return $results;

              });

              // 2.4] Добавить данные в $results
              array_push($results, [
                "name_folder"           => $name,
                "name"                  => $metainfo['name'],
                "description"           => $metainfo['description'],
                "author"                => $metainfo['author'],
                "html"                  => $html,
                "uri_article_relative"  => $group['uri_group_relative'].'/'.$name,
              ]);

            }

            // n] Вернуть результаты
            return $results;

          });

          // 5.2.3. Получить из БД доступный список статей, связанных с $group
          $articles_in_db = \M12\Models\MD3_articles::whereHas('groups', function($query) USE ($group) {
            $query->where('id', $group['id']);
          })->doesntHave('groups', 'or')->get();

          // 5.2.4. Удалить из БД все статьи, их их связи md1001/md1002
          call_user_func(function() USE ($articles_in_db, $articles_in_root_data, $group) {

            // 1] Получить массив ID связанных с $group статей
            $articles_ids = $articles_in_db->pluck('id')->toArray();

            // 2] Удалить все связи $articles_in_db
            foreach($articles_in_db as $article) {

              $article->groups()->detach();
              $article->countrycode()->detach();

            }

            // 3] Удалить все статьи с id из $articles_ids
            \M12\Models\MD3_articles::whereIn('id', $articles_ids)->delete();

          });

          // 5.2.5. Установить автоинкремент, равный 1
          call_user_func(function(){

            // 1] Получить значениед для автоинкремента
            $autoincrement_value = call_user_func(function(){

              // 1.1] Если MD3_articles пуст, то вернуть 1
              if(\M12\Models\MD3_articles::count() == 0) return 1;

              // 1.2] В противном случае, вернуть ID последней записи + 1
              else
                return +(\M12\Models\MD3_articles::orderBy('id', 'desc')->first()->id) + 1;

            });

            // 2] Установить счётчик автоинкремента
            DB::statement('ALTER TABLE m12.md3_articles AUTO_INCREMENT = '.$autoincrement_value.';');

          });

          // 5.2.6. Добавить в БД все статьи из $groups_in_root_data
          call_user_func(function() USE ($articles_in_root_data, $root, $group, $public) {
            foreach($articles_in_root_data as $article) {

              // 1] Добавить данные о $group в БД
              $newarticle = new \M12\Models\MD3_articles();
              $newarticle->name                 = json_encode($article['name'], JSON_UNESCAPED_UNICODE);
              $newarticle->description          = json_encode($article['description'], JSON_UNESCAPED_UNICODE);
              $newarticle->html                 = json_encode($article['html'], JSON_UNESCAPED_UNICODE);
              $newarticle->author               = json_encode($article['author'], JSON_UNESCAPED_UNICODE);
              $newarticle->uri_article_relative = $article['uri_article_relative'];
              $newarticle->save();

              // 2] Связать $newarticle с $group
              if(!$newarticle->groups->contains($group['id']))
                $newarticle->groups()->attach($group['id']);

              // 3] Скопировать папку с файлами статьи в public

                // 3.1] Получить относительные пути источника/назначения
                $path_source  = $root.'/'.$article['uri_article_relative'].'/_files/';
                $path_dest    = $public.'/'.$article['uri_article_relative'];

                // 3.2] Удалить папку с ресурсами, если она уже есть в public
                if($this->storage->exists($path_dest))
                  $this->storage->deleteDirectory($path_dest);

                // 3.3] Скопировать
                config(['filesystems.default' => 'local']);
                config(['filesystems.disks.local.root' => base_path('')]);
                $this->storage = new \Illuminate\Filesystem\Filesystem();
                $this->storage->copyDirectory(
                  $path_source,
                  $path_dest
                );

                // 3.4] Удалить файл article.info из $dest
                $this->storage->delete($path_dest.'/article.info');

            }
          });

        }

      });


    DB::commit(); } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_parse_faq from M-package M12 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        DB::rollback();
        Log::info($errortext);
        write2log($errortext, ['M12', 'C1_parse_faq']);
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

