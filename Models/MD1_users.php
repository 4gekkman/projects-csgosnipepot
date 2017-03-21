<?php
////======================================================////
////																										  ////
////                    Модель M-пакета						        ////
////																											////
////======================================================////

//------------------------------//
// А. Указать пространство имён //
//------------------------------//
// - Пример: M1\Models

  namespace M5\Models;

//-------------------------------------//
// Б. Импортировать необходимые классы //
//-------------------------------------//

  // Собственно, сам eloquent
  use Illuminate\Database\Eloquent\Model;

  // Реализация мягкого удаления
  use Illuminate\Database\Eloquent\SoftDeletes;


//-----------------//
// В. Класс-модель //
//-----------------//
class MD1_users extends Model {

  /**
   *
   *  В1. Основной функционал
   *  В2. Связи модели
   *  В3. Вспомогательный функционал
   *  В4. Заготовки запросов модели (scopes)
   *  В5. Ассессоры
   *  В6. Мутаторы
   *  В7. Справочник по связям между моделями
   *
   */

  //-------------------------//
  // В1. Основной функционал //
  //-------------------------//

  // 1] Имя таблицы, которую использует модель //
  //-------------------------------------------//
  // - По умолчанию, имя класса модели с 's' на конце
  // - Пример: 'm1.md1_settings'
  // - Внимание! Регистр в имени таблицы имеет значение!
  protected $table = 'm5.md1_users';

    // 2] Вкл (по умолч.) / Выкл автообслуживание столбцов created_at / updated_at //
    //-----------------------------------------------------------------------------//
    public $timestamps = true;

    // 3] Вкл / Выкл (по умолч.) мягкое удаление //
    //-------------------------------------------//
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    // 
    use SoftDeletes; protected $dates = ["deleted_at"];


  //------------------//
  // В2. Связи модели //
  //------------------//

    // relationships start
    public function groups() { return $this->belongsToMany('\M5\Models\MD2_groups', 'm5.md1002', 'id_user', 'id_group'); }
    public function privileges() { return $this->belongsToMany('\M5\Models\MD3_privileges', 'm5.md1004', 'id_user', 'id_privilege'); }
    public function tags() { return $this->belongsToMany('\M5\Models\MD4_tags', 'm5.md1005', 'id_user', 'id_tag'); }
    public function emailvercodes() { return $this->belongsToMany('\M5\Models\MD6_emailvercodes', 'm5.md1008', 'id_user', 'id_code'); }
    public function phonevercodes() { return $this->belongsToMany('\M5\Models\MD7_phonevercodes', 'm5.md1009', 'id_user', 'id_code'); }
    public function auth() { return $this->belongsToMany('\M5\Models\MD8_auth', 'm5.md1010', 'id_user', 'id_auth'); }
    public function emailauthcodes() { return $this->belongsToMany('\M5\Models\MD9_emailauthcodes', 'm5.md1011', 'id_user', 'id_code'); }
    public function phoneauthcodes() { return $this->belongsToMany('\M5\Models\MD10_phoneauthcodes', 'm5.md1012', 'id_user', 'id_code'); }
    public function genders() { return $this->belongsTo('\M5\Models\MD11_genders', 'gender', 'id'); }
    public function m10_rooms() { return $this->belongsToMany('\M10\Models\MD1_rooms', 'm10.md2003', 'id_user', 'id_room'); }
    public function m10_messages() { return $this->belongsToMany('\M10\Models\MD2_messages', 'm10.md2001', 'id_user', 'id_message'); }
    public function m13_wallets() { return $this->belongsToMany('\M13\Models\MD1_wallets', 'm13.md2000', 'id_user', 'id_wallet'); }
    public function m13_trades() { return $this->belongsToMany('\M13\Models\MD4_trades', 'm13.md2001', 'id_user', 'id_trade'); }
    public function m14_trades() { return $this->belongsToMany('\M14\Models\MD4_trades', 'm14.md2001', 'id_user', 'id_trade'); }
    public function m14_purchases() { return $this->belongsToMany('\M14\Models\MD1_purchases', 'm14.md2004', 'id_user', 'id_purchase'); }
    public function m15_statuses() { return $this->belongsToMany('\M15\Models\MD1_statuses', 'm15.md2000', 'id_user', 'id_status'); }
    public function m9_bets() { return $this->belongsToMany('\M9\Models\MD3_bets', 'm9.md2000', 'id_user', 'id_bet')->withPivot(['tickets_from','tickets_to']); }
    public function m9_wins() { return $this->belongsToMany('\M9\Models\MD4_wins', 'm9.md2004', 'id_user', 'id_win'); }
    // relationships stop


  //--------------------------------//
  // В3. Вспомогательный функционал //
  //--------------------------------//

    // 1] Имя соединения с БД //
    //------------------------//
    // - По умолчанию, указанное в 'default' в config/database.php
    // protected $connection = '';

    // 2] Имя столбца с первичным ключём //
    //-----------------------------------//
    // - По умолчанию 'id'
    // $primaryKey = '';

    // 3] Настройка автоинкремента столбца с первичным ключём //
    //--------------------------------------------------------//
    // - По умолчанию включено. Включено: true, выключено: false.
    // - В Eloquent, по умолчанию, для столбца с первичным ключём, включён авто-инкремент.
    // $incrementing = false;

    // 4] Настроить "белый список" mass assignment //
    //---------------------------------------------//
    // protected $fillable = ['id', 'password'];

    // 5] Настроить "черный список" mass assignment //
    //----------------------------------------------//
    // protected $guarded = ['id', 'password'];
    // protected $guarded = ['*'];

    // 6] Настроить автоприведение типов //
    //-----------------------------------//
    // protected $casts = [
    //     'options' => 'array',
    // ];

    // 7] Настроить список $hidden //
    //-----------------------------//
    // - Эта настройка срабатывает при преобразовании модели в массив \ JSON.
    // - Указанные в списке столбцы будут исключены из результатов преобразования.

    // protected $hidden = ['password'];

    // 8] Настроить список $visible //
    //------------------------------//
    // - Эта настройка срабатывает при преобразовании модели в массив \ JSON.
    // - Только указанные в списке столбцы будут присутствовать в результатах преобразования.

    // protected $visible = ['first_name', 'last_name'];

    // 9] Настроить список $appends //
    //------------------------------//
    // - Эта настройка срабатывает при преобразовании модели в массив \ JSON.
    // - Указанные в списке столбцы будут добавлены к результатам преобразования.
    // - Какие именно значения будут добавлены, указывается в соотв. ассессорах.
    // protected $appends = ['is_admin'];


  //----------------------------------------//
  // В4. Заготовки запросов модели (scopes) //
  //----------------------------------------//
  // - !!! Имена должны начинатсья со scope !!!
  // - Как использовать (пример):
  //     User::name1()->name2()->get();


    // 1]  //
    //---------------------------------------//

    // public function scopeName1($query, $param1, $param2) {
    //
    //     return $query->where('votes', '>', 100);
    //
    // }


    // 2]  //
    //---------------------------------------//

    // public function scopeName2($query, $param1, $param2) {
    //
    //     return $query->where('gender', '=', 'W');
    //
    // }


  //---------------//
  // В5. Ассессоры //
  //---------------//
  //| Настраиваем ассессоры модели
  //|
  //| - Аксессор позволяет при получении объекта модели "слегка обработать"
  //|   данные из указанных столбцов - т.е. вносить в них указанные изменения.
  //|   Вообще, это напоминает фильтр.
  //|
  //| - Разберём, из чего состоит имя аксессора в примере ниже:
  //|
  //|     get           | обозначает, что это аксессор
  //|     Name          | обозначает имя столбца, для которого предназначен этот аксессор
  //|     Attribute     | это просто техническая часть функции-аксессора, она всегда неизменна
  //|
  //| - Что делать, если имя столбца в стиле snake_case?
  //|   - Писать его всё равно в CamelCase.
  //|   - Например, если имя столбца id_user, то имя функции-аксессора будет: getIdUserAttribute
  //|

    // 1] Мутатор дат //
    //----------------//
    // public function getDates()
    // {
    //     return ['created_at', 'updated_at'];
    // }

    // 2] Пример ассесора //
    //--------------------//
    // - Допустим, у нас в столбце "user_name" лежит значение "иван".
    // - А мы хотим при получении данного объекта, чтобы "иван" было с большой буквы.
    // - Тогда добавляем в модель вот такой аксессор:

    // public function getUserNameAttribute($value) {
    //
    //     return ucfirst($value);
    //
    // }


  //--------------//
  // В6. Мутаторы //
  //--------------//
  //| Настраиваем мутаторы модели
  //|
  //| - Мутатор позволяет при записи в модель "слегка обработать" записываемое
  //|   значение - т.е. внести в него указанные изменения.
  //|   Вообще, это напоминает фильтр.
  //|
  //| - Разберём, из чего состоит имя мутатора в примере ниже:
  //|
  //|    set           | обозначает, что это аксессор
  //|    Name          | обозначает имя столбца, для которого предназначен этот аксессор
  //|    Attribute     | это просто техническая часть функции-аксессора, она всегда неизменна
  //|
  //| - Что делать, если имя столбца в стиле snake_case?
  //|   - Писать его всё равно в CamelCase.
  //|   - Например, если имя столбца id_user, то имя функции-аксессора будет: getIdUserAttribute
  //|

    // 1] Пример мутатора  //
    //---------------------//
    // - Допустим, у нас есть столбец "user_name", в котором должно лежать имя пользователя.
    // - А мы хотим при записи значений в этот, чтобы оно автоматом записывалось в стиле ucfirst.
    // - Тогда добавляем в модель вот такой мутатор:

    // public function setUserNameAttribute($value) {
    //
    //     $this->attributes['user_name'] = ucfirst($value);
    //
    // }


  //-----------------------------------------//
  // В7. Справочник по связям между моделями //
  //-----------------------------------------//

    //-----------------------------//
    // Три быстрых примера-шаблона //
    //-----------------------------//

      // 1]  //
      //---------------------//
      // - 1:n прямая
      // - [X]---Є[Y] Какие Y принадлежат этому X
      //public function many_y()
      //{
      //  return $this->hasMany('\M5\Models\MD2_y', 'id_y', 'id');
      //}

      // 1]  //
      //---------------------//
      // - 1:n обратная
      // - [Y]Э---[X] Какому X принадлежит этот Y
      //public function one_x()
      //{
      //  return $this->belongsTo('\M5\Models\MD1_x', 'id_y', 'id');
      //}

      // 1]  //
      //---------------------//
      // - n:m
      // - [A]Э---Є[B] С какими A связан этот B
      //public function many_a()
      //{
      //  return $this->belongsToMany('\M5\Models\MD3_a', 'm5.md1000', 'id_b', 'id_a');
      //}


    //------------------------------------------------------//
    // Шаблоны (примеры) определений и использования связей //
    //------------------------------------------------------//

      //----------------------------------//
      // 1] hasOne() - cвязь 1:1 "прямая" //
      //----------------------------------//

        // Определение
        //--------------------------------//
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: имя столбца в той модели, с которым связана эта
        // - 3 аргумент: имя столбца в этой модели, с которым связана та

          //  public function phone()
          //  {
          //    return $this->hasOne('\M1\Models\MD1_name', 'foreign_key', 'local_key');
          //  }

        // Использование
        //--------------------------------//

          // $phone = User::find(1)->phone;


      //-------------------------------------------------------------//
      // 2] belongsTo() - связь 1:1 "обратная", связь 1:n "обратная" //
      //-------------------------------------------------------------//

        // Определение
        //--------------------------------//
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: имя столбца в этой модели, с которым связана та
        // - 3 аргумент: имя столбца в той модели, с которым связана эта

          //  public function user()
          //  {
          //    return $this->belongsTo('\M1\Models\MD1_name', 'local_key', 'parent_key');
          //  }

        // Использование
        //--------------------------------//

          // $user = Phone::find(1)->user;


      //-----------------------------------//
      // 3] hasMany() - связь 1:n "прямая" //
      //-----------------------------------//

        // Определение прямой 1:n связи
        //--------------------------------//
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: имя столбца в той модели, с которым связана эта
        // - 3 аргумент: имя столбца в этой модели, с которым связана та

          //  public function comments()
          //  {
          //    return $this->hasMany('\M1\Models\MD1_name', 'foreign_key', 'local_key');
          //  }

        // Использование прямой 1:n связи
        //--------------------------------//

          //  - Получим коллекцию комментариев по определенному посту:
          //
          //      $comments = Post::find(1)->comments;
          //
          //  - Получим 1 найденный  коммент. к опред.посту с опред.заголовком:
          //
          //      $comment = Post::find(1)->comments()->where('title', '=', 'foo')->first();


        // Определение обратной 1:n связи
        //--------------------------------//
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: имя столбца в этой модели, с которым связана та
        // - 3 аргумент: имя столбца в той модели, с которым связана эта

          //  public function user()
          //  {
          //    return $this->belongsTo('\M1\Models\MD1_name', 'foreign_key', 'parent_key');
          //  }

        // Использование обратной 1:n связи
        //--------------------------------//

          // $post = Comment::find(1)->post;


      //----------------------------------------------//
      // 4] hasManyThrough() - связь 1:n "транзитная" //
      //----------------------------------------------//

        // Определение
        //--------------------------------//
        // - В этом примере связываем текущую модель с MD1 через MD2.
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: модель-посредник, через которую связь
        // - 3 аргумент: имя столбца в этой модели, с которым связана та
        // - 4 аргумент: имя столбца в той модели, с которым связана эта

          //  public function user()
          //  {
          //    return $this->hasManyThrough('\M1\Models\MD1_name', '\M1\Models\MD2_name', 'foreign_key', 'local_key');
          //  }

        // Использование
        //--------------------------------//

          // $posts = Country::find(1)->posts;


      //---------------------------------------------------------//
      // 5] morphMany() - связь 1:n, 1:x, 1:y, ... "полиморфная" //
      //---------------------------------------------------------//

        // Определение
        //--------------------------------//

          //  # Дочерняя модель
          //  class Comments extends Model {
          //    public function morphowner(){
          //      return $this->morphTo();
          //    }
          //  }
          //
          //  # Родительские модели
          //  class Blogs extends Model {
          //    public function comments(){
          //      return $this->morphMany('App\Blogs', 'morphowner');
          //    }
          //  }
          //  class Goods extends Model {
          //    public function comments(){
          //      return $this->morphMany('App\Goods', 'morphowner');
          //    }
          //  }
          //  class News extends Model {
          //    public function comments(){
          //      return $this->morphMany('App\News', 'morphowner');
          //    }
          //  }


        // Использование
        //--------------------------------//

          //  $blog = Blogs::find(1);
          //  foreach($blog->comments as $comment) {
          //    //
          //  }
          //
          //  $good = Goods::find(1);
          //  foreach($good->comments as $comment) {
          //    //
          //  }
          //
          //  $news = News::find(1);
          //  foreach($news->comments as $n) {
          //    //
          //  }


        // Получение родителя полиморфной связи по дочке
        //----------------------------------------------//

          //  $comment = Comments::find(1);
          //  $owner = $comment->morphowner;


      //--------------------------------//
      // 6] belongsToMany() - связь n:m //
      //--------------------------------//

        // Определение (прямая и обратная определяются одинаково)
        //-------------------------------------------------------//
        // - 1 аргумент: с какой моделью связь
        // - 2 аргумент: имя pivot-таблицы
        // - 3 аргумент: столбец в родительской таблице, с которым связь
        // - 4 аргумент: столбец в этой таблице, с которым связь

          //  public function students()
          //  {
          //    return $this->belongsToMany('App\Student', 'user_roles', 'user_id', 'foo_id');
          //  }


        // Использование
        //--------------------------------//

          //  # Получить коллекцию всех студентов данного учителя
          //  $students = Teacher::find(1)->students;
          //
          //  # Получить коллекцию всех учителей данного студента
          //  $teachers = Student::find(1)->teachers;


      //-----------------------------------------------------------//
      // 7] morphToMany() - связь n:m, n:x, n:y, ... "полиморфная" //
      //-----------------------------------------------------------//

        // Определение
        //--------------------------------//

          //  # Дочерняя модель
          //  class Tag extends Model {
          //
          //      public function posts()
          //      {
          //          return $this->morphedByMany('App\Post', 'taggable');
          //      }
          //
          //      public function videos()
          //      {
          //          return $this->morphedByMany('App\Video', 'taggable');
          //      }
          //
          //  }
          //
          //  # Родительские модели
          //  class Post extends Model {
          //
          //      public function tags()
          //      {
          //          return $this->morphToMany('App\Tag', 'taggable');
          //      }
          //
          //  }
          //  class Video extends Model {
          //
          //      public function tags()
          //      {
          //          return $this->morphToMany('App\Tag', 'taggable');
          //      }
          //
          //  }

        // Использование
        //--------------------------------//

          //  # Получить коллекцию видео по указанному тегу
          //  $videos = Tags::find(1)->videos;
          //
          //  # Получить коллекцию постов по указанному тегу
          //  $videos = Tags::find(1)->posts;




}








