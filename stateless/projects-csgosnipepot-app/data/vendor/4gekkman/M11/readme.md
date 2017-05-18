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

    ▪ Принципы работы: полный обзор всего процесса
    ▪ Принципы работы: требования к демону queue:work --daemon

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

  # Принципы работы: полный обзор всего процесса

    1. Выполняется artisan-команда "m11:start"
      - Планировщик laravel делает это каждую минуту
        (она добавлена в планировщик в ServiceProvider.php пакета).
      - Также можно сделать это вручную.

      1.1. Получить из конфига M11 значение ticks_monitoring_s
      1.2. Получить timestamp последнего тика из Redis
        - По ключу "m11:current_tick".
      1.3. Проверить, нужно ли запускать цепочку тиков
        - Если значение timestamp из 1.2 пустое, можно запускать.
        - Если в течение ticks_monitoring_s секунд нет тиков, то
          можно запускать, а если есть, то нельзя.
      1.4. Запустить цепочку тиков, если в 1.3 принято положительное решение
        1.4.1. Получить из конфига значение is_system_on
          - Демон queue:work --daemon всасывает конфиг в начале своей работы.
          - Это значит, что если ты изменишь значение is_system_on в конфиге M11
            уже после начала работы демона, он этого не увидит, для него это
            значение по прежнему останется старым - тем, что было в начале его работы.
        1.4.2. Положить в Redis значение $is_system_on
          - С ключём "M11.is_system_on".
        1.4.3. Запустить цепочку тиков, если $is_system_on == 'on'
          - Для этого:

            1] Отправить команду "\M11\Commands\C2_link" в очередь "link".
            2] Передать текущий timestamp в мс с ключём "old_timestamp_ms".

    2. Выполняется команда C2_link
      - В переводе на русский "link" означает "звено".
      - В нашем случае, имеется в виду звено "цепочки" тиков.
      - В конце своего выполнения C2_link добавит в очередь
        "link" выполнения самоё себя.

      2.1. Получить timestamp текущего серверного времени в мс
      2.2. Прервать цепочку, если в конфиге цепочки отключены
        - То есть, если значение "is_system_on" из конфига M11 равно 'off'.
      2.3. Проверить значение M11.is_system_on в Redis
        - В пункте 1.4.1 обсуждалась интересная особенность демона queue:work --daemon,
          что он получает "слепок" конфига, команд и прочего в начале своей работы,
          и на изменения всего это не реагирует, пока его не перезагрузишь.
        - Однако, если изменять данные, которые laravel не кэшерует, например в базах данных,
          в том же Redis, то таким вот образом можно передавать в queue:work команды.
        - Так что, можно просто изменить в Redis значение с ключём "M11.is_system_on"
          с "on" на "off", и таким образом прервать цепочку тиков.
        - В принципе, команда C4_stop это и делает. И цепочка прерывается на следующем
          выполнении команды C2_link, на пункте 2.3.
      2.4. Сохранить в Redis информацию о текущем и предыдущем тиках
        - А именно, их текущие timestamps в мс.
        - Причём, по предыдущему тику - это так, на всякий слуай, особо оно не надо.
      2.5. Послать в очередь "tick" задачу tick
        - Шлём команду "\M11\Commands\C3_tick" в очередь "tick".
        - Где она будет обработана демоно queue:work --daemon.
      2.6. Извлечь из конфига период тиков
        - А именно, значение с ключём "ticks_period_ms".
      2.7. Спустя ticks_period_ms после $timestamp_ms выполнить C2_link
        - В пункте 2.1 (в начале выполнения C2_link) был получен timestamp в мс.
        - А в 2.6 было получено значение для ticks_period_ms, обозначающее период
          тиков в мс.
        - Соответственно, в следующий раз C2_link должна выполниться ровно через
          ticks_period_ms после начала выполнения предыдущей C2_link.
        - Так что, на этом этапе код ждёт, пока ticks_period_ms пройдут, и после
          этого отправляет очередной экземпляр C2_link в очередь "link".

    3. Выполняется команда C3_tick
      - Она запускается в команте C2_link, пункт 2.5.
      - Всё, что она делает, это возбуждает новое событие с ключём "m11:tick".

    4. Любые обработчики событий могут ловить "m11:tick"
      - В любом M-пакете можно создать любой обработчик событий, который ловит "m11:tick".
      - И выполнять там любые необходимые действия.
      - В том числе, и транслировать информацию подписчикам через websocket.

  # Принципы работы: требования к демону queue:work --daemon

    1. Этот демон должен обрабатывать 3 очереди: broadcastworkers,tick,link

      link                | Для задач C2_link
      tick                | Для задач C3_tick
      broadcastworkers    | Для трансляции клиентам через websocket

    2. Он должен поддерживаться через supervisor
      - Потому что демон может "упасть" ненароком.
      - И supervisor его может, если что, перезапустить.

    3. Должно работать несколько экземпляров демона
      - Чтобы если один упал, второй и прочие продолжали работать, пока
        supervisor перезагружает упавший.

    4. Вот отлаженный конфиг для демона в supervisor

        [program:queue-workerticks]
        process_name=%(program_name)s_%(process_num)03d
        command = php <path_to_the_project>/project/artisan queue:work --daemon --sleep=0 --memory=256 --queue="broadcastworkers,tick,link"  ; command to start the program
        user = root                                                                                     ; user from which name to start program
        autostart = true                                                                                ; whether run program at supervisord start
        autorestart = unexpected                                                                        ; restart program when crash
        numprocs=3                                                                                      ; start only 1 instance of the program
        redirect_stderr=true                                                                            ; send errors to stdout
        stdout_logfile=<path_to_the_project>/other/logs/queue-workerticks.log                           ; write stdout and stderr of the programm to this file
        stdout_logfile_maxbytes=50MB                                                                    ; max size of logfile before it is rotated
        stdout_logfile_backups=0                                                                        ; number of backed up logfiles

  # Потенциальные проблемы: не ровная периодичность тиков
    - Тики могут идти не ровно каждые N секунд, как указано в конфиге.
    - Это может происходить по различным причинам.
    - Отклонения могут быть порядка десятков миллисекунд.
    - Однако, это не критично, ведь на каждом тике всегда можно сверяться
      с системным временем.

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









