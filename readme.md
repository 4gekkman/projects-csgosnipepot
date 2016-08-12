# M11 - This M-package make ticks every N seconds and translate it via event system
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
  -
	- [Функционал](#link98)
	- [Схемы взаимодействий с другими пакетами](#link99)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета M11 на github
      https://github.com/4gekkman/m11



```

## Введение <a id="link2"></a>
```

  --- Оглавление

    ▪ Тики: что это такое?
    ▪ Тики: как часто могут тикать?
    ▪ Тики: зачем нужны?

    ▪ Скорость и надёжность: демон queue:work
    ▪ Скорость и надёжность: очередь задач "tick"
    ▪ Скорость и надёжность: используется supervisor

    ▪ queue:work: опция --daemon
    ▪ queue:work: опция --queue <очередь1>,<очередь2>
    ▪ queue:work: опция --sleep=0
    ▪ queue:work: посмотреть все доступные опции
    ▪ queue:work: особенность в том, что он загружает всё при старте

    ▪ Принципы работы: цепочку тиков запускает cron
    ▪ Принципы работы: Laravel scheduler запускает С1_start
    ▪ Принципы работы: как C1_start проверяет, не прервалась ли цепочка тиков
    ▪ Принципы работы: задачи C2_link и C3_tick
    ▪ Принципы работы: задача C4_stop

    ▪ Потенциальные проблемы: ждать до ~1 минуты восстановления цепочки тиков
    ▪ Потенциальные проблемы: не ровная периодичность тиков

    ▪ Бенчмаркинг: время вызова С3_tick из C2_link
    ▪ Бенчмаркинг: время выполнения C2_link, от начала до цикла while

  ---

  # Тики: что это такое?
    - Это происходящие каждую секунду события.
    - Эти события происходят в системе событий Laravel.
    - На эти события можно подписать любые обработчики.

  # Тики: как часто могут тикать?
    - Ограничение в меньшую сторону можно установить лишь опытным путём.
    - Также, я уверен, это зависит от мощности сервера.
    - Мне кажется, что хотя бы кажду секунду можно тикать на среднем сервере.

  # Тики: зачем нужны?
    - На тики можно подписывать любые обработчики событий.
    - При каждом тике эти обработчики будут срабатывать.
    - Тики полезны, когда надо очень часть проверять положение вещей где-то.
    - Например, организовывать таймеры, управляемые полностью с сервера,
      и транслировать время этих таймеров через websocket всех подписанным
      клиентам.

  # Скорость и надёжность: демон queue:work
    - Обрабатывать инициируемые тиками команды будет демон queue:work.
    - Единожды запустив приложение и фреймворк, он так и продолжает работать.
    - То есть, ему не надо при каждом запросе заново грузить весь фреймворк,
      а лишь на загруженном фрейворке выполнить соответствующую команду.

  # Скорость и надёжность: очередь задач "tick"
    - Демон queue:work будет обслуживать лишь 1-ну очередь задач "tick".

  # Скорость и надёжность: используется supervisor
    - Для поддержания queue:work работоспособным используется supervisor.
    - В случае краша queue:work supervisor исправит ситуацию.

  # queue:work: опция --daemon
    - При настройке queue:work в конфиге supervisor надо указать опцию --daemon
    - Иначе queue:work выполнится, и завершит работу.
    - А опция --daemon позволяет сделать queue:work демоном.

  # queue:work: опция --queue <очередь1>,<очередь2>
    - При настройке queue:work в конфиге supervisor надо указать опцию --queue
    - В ней указанть одну очередь: "tick".

  # queue:work: опция --sleep=0
    - Эта опция регулирует время ожидания демона перед опросом очереди
      на предмет новых задач.
    - Пока в очереди не 0 задач, демон не "засыпает".
    - Но когда становится 0, он засыпает на --sleep секунд.
    - И только по истечении этого времени снова опрашивает очередь.
    - Необходимо указать --sleep=0.

  # queue:work: посмотреть все доступные опции
    - Посмотреть все доступные для queue:work опции можно вот так:

        artisan help queue:work

  # queue:work: особенность в том, что он загружает всё при старте
    - Например, запустил ты свой Docker-проект.
    - После этого изменил какие-то команды, конфиги и т.д.
    - Однако, экземпляр laravel в queue:work имеет те версии этих команд
      и конфигов, которые были при старте.
    - Чтобы это дело обновить, надо перезагрузить Docker-проект.
    - Однако, этим можно управлять, например, через Redis.

  # Принципы работы: цепочку тиков запускает cron
    - А именно, Laravel scheduler, срабатывающий по cron'у ежеминутно.

  # Принципы работы: Laravel scheduler запускает C1_start
    - Она проверяет, не прервалась ли цепочка тиков.
    - И если прервалась, запускает цепочку тиков заново.
    - Но только в том случае, если в конфиге оно включено.
    - Эту задачу cron запускает каждую минуту, и если в конфиге
      is_system_on равно "on", и при этом действующей цепочки тиков нет,
      то новая запускается.

  # Принципы работы: как C1_start проверяет, не прервалась ли цепочка тиков
    - Она в течение заданного в конфиге времени "слушает" тики.
    - Если хоть 1 тик будет, значит цепочка тиков жива, а если нет - значит мертва.
    - Задача не сработает, если значение в Redis по ключу "M11.is_system_on" не равно "on".

  # Принципы работы: задачи C2_link и C3_tick
    - Задача C1_start запускает 1-ну задачу-звено C2_link.
    - Задача C2_link делает всего основные 3 вещи:

      • Сохраняет в Redis данные о текущем и предыдущем тике

      • Добавляет задачу C3_tick в очередь "tick"
        - С которой работает демон queue:work.

      • Задача C3_tick

      • По прошествии N секунд выполняет саму себя
        - Значение N должно быть указано в конфиге, и м.б. менее 1 секунды.
        - Задача-звено должно свериться со временем запуска предыдущего своего экземпляра.
        - И на основании этого, в конце себя, запустить саму себя снова.
        - Плюс, делается поправка на время исполнения задач.

  # Принципы работы: задача C4_stop
    - Всё, что она делает, это меняет значение в Redis по ключу "M11.is_system_on" на "off".

  # Потенциальные проблемы: ждать до ~1 минуты восстановления цепочки тиков
    - Если произошёл краш queue:work, то supervisor его восстановит.
    - Однако, следующего пинка от cron возможно придётся ждать до 1 минуты.
    - Плюс ждать, пока задача-стартер поймёт, что цепочка легла (по умолчанию
      10 секунд, но точно указано в конфиге).

  # Потенциальные проблемы: не ровная периодичность тиков
    - Тики могут идти не ровно каждые N секунд, как указано в конфиге.
    - Это может происходить по различным причинам:

      ▪ Время выполнения задачи-звена превышает указанный в конфиге период
        - Надо либо повысить мощность сервера, либо увеличить период.

      ▪ Система событий Laravel вносит свой вклад в задержку

      ▪ Система обработки команд Laravel вносит свой вклад в задержку

  # Бенчмаркинг: время вызова С3_tick из C2_link
    - Составляет в районе 30мс.

  # Бенчмаркинг: время выполнения C2_link, от начала до цикла while
    - В районе 60мс.
    - Потом начинается цикл while, в котором через ticks_period_ms
      (указанный в конфиге) после начала C2_link, она должна выполнить
      сама себя повторно.
    - То есть, ticks_period_ms лучше не ставить менее 100мс.


```
## Функционал <a id="link98"></a>
```

  # Команды и к.команды общего назначения #
  #---------------------------------------#

    Команда                 К.Команда                   Описание
    ----------------------------------------------------------------------------------------------------------------------
    command                 | m1:command                | Description of the command


```
## Схемы взаимодействий с другими пакетами <a id="link99"></a>
```

  # Pull-взаимодействия (по инициативе этого пакета) #
  #--------------------------------------------------#

    ---------------------------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие           Комментарий
    ---------------------------------------------------------------------------------------------

      -

  # Push-взаимодействия (по инициативе других пакетов) #
  #----------------------------------------------------#

    ---------------------------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие           Комментарий
    ---------------------------------------------------------------------------------------------

      -


```
## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










