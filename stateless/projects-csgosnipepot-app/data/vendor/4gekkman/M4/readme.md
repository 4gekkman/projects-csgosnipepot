# M4 - Routing for the app (master)
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
	- [Общая схема работы](#link3)
	- [Функционал](#link4)
	- [Схемы взаимодействий через события с другими пакетами](#link5)
	- [Транс-пакетные связи](#link6)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета M4 на github
      https://github.com/4gekkman/m4

			
```

## Введение <a id="link2"></a>
```

  ● Это М-пакет для осуществления динамического роутинга в приложении.

 
```
## Общая схема работы <a id="link3"></a>
```
---------------------------------------
Подоглавление:

  - Полная картина в мелком масштабе

    • Парсинг DLW-пакетов для синхронизации авто.роутов в БД М4
    • Формирование и добавление роутов в routes.php в M4
    • Файл routes.php из M4 добавляется к оному из laravel в SP М4

  - Триггеры, запускающие обновление routes.php в M4

    • Пояснение
    • Триггеры

  - Принципы формирования итоговой строки с роутами в routes.php в M4

    • Что происходит при определении 2-х роутов с одинаковым URI
    • Роут с бОльшим кол-вом параметров "перекрывает" роут с мЕньшим
    • Роуты с менее/более точным URI не имеют приоритета друг над другом:
    • Роуты с мЕньшим кол-вом сегментов должны идти выше роутов с бОльшим
    • Среди роутов с одинаковым кол-вом сегментов, раньше должны идти более точные
    • Что делать при одноврем-ом появлении ручного/авто роутов с одинаковыми точными URI
    • Имена параметров д.б. разные у ручных/авто роутов
    • Протокол роута в БД M4 не играет никакой роли
    • Как действовать в случае наличия/отсутствия поддоменов
    • Пример правильной итоговой строки с роутами (сначала ручные, потом авто)

  - Автообновление роутов при работе в режиме разработки

    • Включение/Выключение режима разработки приложения
    • Обновление при каждом запросе в режиме разработки
    • Особенности автообновления

---------------------------------------

# Полная картина в мелком масштабе

  • Парсинг DLW-пакетов для синхронизации авто.роутов в БД М4
    ▪ Команда C1_update парсит конфиги всех DLW-пакетов.
    ▪ Она извлекает данные из св-ва routes в этих конфигах.
    ▪ На их основе синхронизирует авто.роуты в БД М4.
    ▪ C1_update срабатывает также после parseapp в M1.

  • Формирование и добавление роутов в routes.php в M4
    ▪ М4 по сути использует систему роутинга laravel.
    ▪ Последняя работает по особым правилам, описанным ниже.
    ▪ При любом изменении с роутами срабатывает C8_routesphp_sync.
    ▪ Команда формирует строку с авто. и ручными роутами из БД М4.
    ▪ И добавляет её в routes.php в M4 между специальными метками.

  • Файл routes.php из M4 добавляется к оному из laravel в SP М4
    ▪ Добавление происходит в ServiceProvider в M4 с помщью require.
    ▪ В результате система роутинга laravel всасывает все роуты,
      которые есть в routes.php в M4.

# Триггеры, запускающие обновление routes.php в M4

  • Пояснение
    - Список роутов в routes.php в M4 всегда д.б. полным и актуальным на 100%.
    - А, как нам известно, этот список формируется на основе данных в БД M4.
    - Поэтому, при любых изменениях этих данных, всё должно сразу синхронизироваться.
    - Для синхронизации небходимо лишь вызывать команду C8_routesphp_sync.
    - В каких конкретно случаях вызывается эта команда?

  • Триггеры

    ▪ Выполнение команды parseapp в M1 (вызывает выполнение C1_update в M4).
    ▪ Выполнение команды C1_update
    ▪ Выполнение команды C4_new
    ▪ Выполнение команды C5_del
    ▪ Выполнение команды C6_on
    ▪ Выполнение команды C7_off

# Принципы формирования итоговой строки с роутами в routes.php в M4

  • Что происходит при определении 2-х роутов с одинаковым URI
    - Определённый ниже по тексту в routes.php роут имеет приоритет.
    - А определённый выше по тексту роут "затирается" и не срабатывает.
    - То есть в системе роутинга laravel, uri роута - это его Unique ID.
    - Например, при запросе "http://site.ru/ivan" мы получим '222' в этой ситуации:

        Route::get('/ivan/{p2?}', function () {return '111';});
        Route::get('/ivan/{p2?}', function () {return '222';});

  • Роут с бОльшим кол-вом параметров "перекрывает" роут с мЕньшим
    - Но они не перезаписывают друг друга, и существуют одновременно.
    - Пример №1, ответ '222' мы не получим ни при каком URI:

        Route::get('/ivan/{p2?}/{p3?}', function () {return '111';});
        Route::get('/ivan/{p2?}', function () {return '222';});

    - Пример №2, получим '222' при '/ivan/petrov', и '111' при '/ivan/petrov/sidorov/...'

        Route::get('/ivan/{p2?}', function () {return '222';});
        Route::get('/ivan/{p2?}/{p3?}', function () {return '111';});

  • Роуты с менее/более точным URI не имеют приоритета друг над другом:
    - Пример №1, получим '111' при запросе '/ivan/petrov':

        Route::get('/ivan/{p2?}/{p3?}', function () { return '111'; });
        Route::get('/ivan/petrov/{p3?}', function () { return '222'; });

    - Пример №2, получим '222' при запросе '/ivan/petrov':

        Route::get('/ivan/petrov/{p3?}', function () { return '222'; });
        Route::get('/ivan/{p2?}/{p3?}', function () { return '111'; });

  • Роуты с мЕньшим кол-вом сегментов должны идти выше роутов с бОльшим
    - Потому что роуты с бОльшим кол-вом параметров-сегментов перекрывают роуты с мЕньшим.
    - Причём, для ручных и авто роутов это привило применяется отдельно.
    - Т.Е. сначала всё равно идут все ручные, а лишь потом все автоматические.

  • Среди роутов с одинаковым кол-вом сегментов, раньше должны идти более точные
    - Поскольку они перекрывают друг-друга, но при этом пользователь скорее
      всего имеет в виду всё же более точный роут.
    - Причём, для ручных и авто роутов это привило применяется отдельно.
    - Т.Е. сначала всё равно идут все ручные, а лишь потом все автоматические.

  • Что делать при одноврем-ом появлении ручного/авто роутов с одинаковыми точными URI
    - Надо действовать ещё до формирования итоговой строки с роутами.
    - Надо просто из списка авто.роутов удалить этот роут.
    - Нельзя допускать одновременного попадания этих роутов в итоговую строку.
    - Ведь ручные роуты в этой строке будут левее/выше автоматических.
    - Потому что они должны срабатывать раньше, и иметь приоритет.
    - Но система роутинга laravel так устроена, что затрёт ручной роут (это ошибка).

  • Имена параметров д.б. разные у ручных/авто роутов
    - Для ручных надо использовать имя: mp<номер>
    - Для автоматических надо использовать имя: ap<номер>

  • Протокол роута в БД M4 не играет никакой роли
    - По историческим причинам, там доступны 2 протокола: http и https.
    - Однако, бест практис является настраивать http на уровне веб-сервера.
    - К тому же, в Laravel 5 из параметров групп роутов исчез 'https'.

  • Как действовать в случае наличия/отсутствия поддоменов
    - Во всех случаях оборачивать роут в группу.
    - Поскольку это позволит указать домен для роута (параметр 'domain')
    - В случае отсутствия поддоменов, указывать лишь домен без поддоменов:

        Route::group(['domain' => 'localhost'], function () {
          Route::get('/ivan', function () { return 'man1'; });
        });

    - В случае наличия поддоменов, указывать домен с поддоменами:

        Route::group(['domain' => 'sub.localhost'], function () {
          Route::get('/ivan', function () { return 'man1'; });
        });

  • Пример правильной итоговой строки с роутами (сначала ручные, потом авто)
    - Со следующими отличиями:
      • В реале кол-во параметров каждого роута д.б. равно 50.
      • В реале вместо callback идут ссылки на методы getIndex и postIndex контроллеров.
      • В реале роуты идут парами: Route::get + Route::post.
      • В все роуты заворачиваются в группы.
    - Итак:

      Route::get('/ivan', function () { return 'man1'; });                    // man1
      Route::get('/{mp1?}', function () { return 'man2'; });                  // man2
      Route::get('/ivan/petrov', function () { return 'man3'; });             // man3
      Route::get('/ivan/{mp2?}', function () { return 'man4'; });             // man4
      Route::get('/ivan/petrov/sidorov', function () { return 'man5'; });     // man5
      Route::get('/ivan/petrov/{mp3?}', function () { return 'man6'; });      // man6
      Route::get('/ivan/{mp2?}/{mp3?}', function () { return 'man7'; });      // man7

      Route::get('/nikola', function () { return 'auto1'; });                 // auto1
      Route::get('/{ap1?}', function () { return 'auto2'; });                 // man2
      Route::get('/nikola/ololoev', function () { return 'auto3'; });         // auto3
      Route::get('/nikola/{ap2?}', function () { return 'auto4'; });          // auto4
      Route::get('/nikola/ololoev/savelov', function () { return 'auto5'; }); // auto5
      Route::get('/nikola/ololoev/{ap3?}', function () { return 'auto6'; });  // auto6
      Route::get('/nikola/{ap2?}/{ap3?}', function () { return 'auto7'; });   // auto7

      // Route::get('/ivan/petrov', function () { return 'auto8'; });     // точный авто.роут, URI которого совпадает с URI уже существующего ручного роута
                                                                          // - д.б. исключён из списка авто.роутов ещё до формирования итоговой строки роутов
      Route::get('/ivan/petrov/{ap3?}', function () { return 'man9'; });  // не точный авто.роут, URI которого совпадает с URI уже существующего ручного роута
                                                                          // - его можно оставить, ничего с ним не делать
# Автообновление роутов при работе в режиме разработки

  • Включение/Выключение режима разработки приложения
    - Вкл/Выкл режим разработки можно в конфиге M1, св-во "development_mode".

  • Обновление при каждом запросе в режиме разработки
    - Если режим разработки включён, роуты обновляются при каждом запросе к приложению.
    - Сначала выполняется к.команда afterupdate в M1, обновляя в т.ч. БД М4.
      То есть все изменения в routes в конфигах DLW-пакетов попадают в БД M4.
    - Затем возбуждается событие, которое ловит M4. И выполняет команду,
      которая обновляет routes.php в M4 на основе данных из БД.

  • Особенности автообновления
    - Все эти задачи добавляются в очередь, и их выполнение занимает какое-то время.
    - Может понадобиться несколько запросов, чтобы изменения в routes конфигов вступили в силу.


```
## Функционал <a id="link4"></a>
```

  # Команды и к.команды #
  #---------------------#

    Команда           К.Команда               Описание
    ----------------------------------------------------------------------------------------------------------------------
    update            m4:update               Updates auto routes using fresh data about packages
    list              m4:list                 Gets the table of D,L,W-packs and their routes
    check             m4:check                Checks if any conflicts between auto and manual routes
    new               m4:new                  New manual route
    del               m4:del                  Del manual route
    -                 m4:switch               Turn on/off auto or manual route
    on                -                       Turn on auto or manual route
    off               -                       Turn off auto or manual route
    routesphp_sync    m4:routesphp_sync       Synchronize route registrations in routes.php of M4 beetween special marks.


  # Обработчики событий #
  #---------------------#

    Обработчик          Ключи                     Описание
    ----------------------------------------------------------------------------------------------------------------------
    H1_update           m1:afterupdate            Gets a signal that the app has been updated, and invoke C1_update
    H2_devmode_request  m1:devmode_request_event  In dev.mode every request to app fires event, this handler catch it and call C8_routesphp_sync

```
## Схемы взаимодействий через события с другими пакетами <a id="link5"></a>
```

  # Pull-взаимодействия (по инициативе этого пакета) #
  #--------------------------------------------------#

    ---------------------------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие           Комментарий
    ---------------------------------------------------------------------------------------------

      нет

  # Push-взаимодействия (по инициативе других пакетов) #
  #----------------------------------------------------#

    M4 <--- M1 (вызов C1_update в M4 после выполнения C1_parseapp в M1)
    ---------------------------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие           Комментарий
    ---------------------------------------------------------------------------------------------
    M1        C1_parseapp
    M1                                                  m1:afterupdate
    M4                                  H1_update
    M4        C1_update
    M4        C8_routesphp_sync


```
## Транс-пакетные связи <a id="link6"></a>
```

  Pivot     Локальная модель    Связанный M-пакет   Внешняя модель    Комментарий
  ----------------------------------------------------------------------------------------------------------------------
  md2000    MD1_routes          M1                  MD2_packages      Каждый роут должен вести к D,L,W-пакету.



```
## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```









