# M5 - Package to manage users and privileges (master)
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
	- [Общее описание работы пакета](#link3)
	- [Функционал](#link4)
	- [Схемы взаимодействий с другими пакетами](#link5)
	- [Транс-пакетные связи](#link6)
	- [Кэширование](#link7)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета M5 на github
      https://github.com/4gekkman/m5

	
			
```

## Введение <a id="link2"></a>
```

  ● Это М-пакет для управления пользователями, группами и правами.
  ● Права могут быть:
    - На доступ к документам D-пакетов.
    - На выполнение команд.
    - Кастомные права.


```
## Общее описание работы пакета <a id="link3"></a>
```

  --- Оглавление

    # Пользователи
    # Администраторы
    # Анонимный пользователь
    # Группы
    # Права
    # Принцип формирования итогового набора прав пользователей
    # Принцип формирования итогового набора прав групп
    # Теги
    # Автопометка автоматических прав тегами
    # Обновление состава формируемых автоматических прав
    # Выключатель системы проверки автоматически прав
    # Ограничение на минимальное кол-во символов в пароле
    # Аутентификация: как работает
    # Аутентификация: варианты



    # Механизм восстановления пароля
    # Защита от брутфорса
    # Настройка времени жизни аутентификационной куки
    # Настройка переадресации после входа
    # Настройка столбцов, которые можно использовать в качестве логина
    # Настройка факторов и сценариев аутентификации
    # Настройка SMS-аутентификации
    # Настройка аутентификации через секретную фразу
    #

  ---

  # Пользователи
    - Эта сущность нужна для персонализации пользователей приложения.
    - Чтобы при каждом визите пользователя приложение его узнавало.
    - Пользователи м.б. связаны с группами / правами / тегами.

  # Администраторы
    - Администратором является пользователь в группе с флагом "администраторы".
    - В системе может быть только 1 группа с флагом "администраторы".
    - Если таковой группы нет, то значит в системе нет администраторов.

  # Анонимный пользователь
    - Анонимными называют не аутентифицированных пользователей.
    - В системе может быть лишь 1 пользователь с флагом "анонимный".
    - Если такового нет, то анонимные пользователи не имеют никаких прав.

  # Группы
    - Группы облегчают организацию воздействия на массы пользователей.
    - Группы м.б. связаны с пользователями / правами / тегами.

  # Права
    - Общий принцип такой: всё, что явно не разрешено, запрещено.
    - Права м.б. связаны с пользователями / группами / тегами.
    - Бывают 3 типа прав:

      1) access    | На доступ к интерфейсам D,L,W-пакетов.
        - Формируются системой автоматически.
        - Получают имя по шаблону: access_[ID D,L,W-пакета]
        - Реализуются в before middleware пакета M5.

      2) exec      | На выполнение команд.
        - Формируются системой автоматически.
        - Получают имя по шаблону: exec_[ID M-пакета]_[ID команды]
        - Реализуются в хелпере runcommand.

      3) custom    | Кастомные права.
        - Формируются пользователем вручную.
        - Получают имя по шаблону: custom_[любая строка]
        - Стандарты по реализации этих прав отсутствуют.

    - Инфу о пакетах и командах, необходимую для автоматического
      формирования соответствующих прав, пакет получает транс-пакетные
      связи с пакетами M1 и M4.

  # Принцип формирования итогового набора прав пользователей
    - Итоговый набор прав пользователя складывается так:

      1) Права, прямо связанные с пользователем.
      2) Права, связанные с группами, с которыми связан пользователь.
      3) Права, связанные с тегами, связанные с группами, с которыми связан пользователь.
      4) Права, связанные с тегами, с которыми связан пользователь.
      5) Если пользователь состоит в группе с флагом "Admin",
         ему присваиваются все имеющиеся права.

  # Принцип формирования итогового набора прав групп
    - Итоговый набор прав группы складывается так:

      1) Права, прямо связанные с группой.
      2) Права, связанные с тегами, которые связаны с группами.
      3) Если группа имеет флаг "Admin", она получает все имеющиеся права.

  # Теги
    - Правам, пользователям и группам могут присваиваться теги.
    - Один тег может быть присвоен и тем, и другим и третьим.
    - Теги помогают находить и воздействовать на наборы сущностей.
    - Кроме того, они задействованы в механизме автоприсвоения прав.

  # Автопометка автоматических прав тегами
    - Права типов exec и access автоматически помечаются тегами при создании.
    - А именно, тегами принадлежности к тому или ному пакету.
    - Примеры: 'm1', 'd5', 'l3'.

  # Обновление состава формируемых автоматических прав
    - Все права типов access и exec формируются исплючительно автоматически.
    - Первые формируются на основе данных из базы пакета M1.
    - Вторые формируются на основе данных из базы пакета M4.
    - Соответствующие данные приложение извлекает через транс-пакетные связи.
    - При любом обновлении баз M1/M4 необходимо обновлять все авто-права в M5.
    - Поэтому, в M5 обработчик ловит события "m1:afterupdate" и "m4:afterupdate".
    - Эти события являются уведомлениями от M1/M4 об очередном обновлении базы.
    - И запускает команду обновления C1_update.
    - В ней все существующие автоматические права мяго удаляются.
    - Затем данные извлекаются из M1/M4 через транс-пакетные связи.
    - И восстанавливается или создаётся полный набор access и exec прав.

  # Выключатель системы проверки автоматических прав
    - Присутствует выключатель системы проверки автоматических прав.
    - Он находится в параметре "privs_check_ison" конфига пакета.
    - Параметр принимает значения true / false.
    - Если выкл., состоянии права типов access и exec не проверяются.

  # Ограничение на минимальное кол-во символов в пароле
    - Во время регистрации нового пользователя требуется указать пароль.
    - Можно установить ограничение на его минимальную длину в символах.
    - За это отвечает параметр "min_chars_in_pass" в конфиге.

  # Аутентификация: как работает
    - 

  # Аутентификация: варианты
    - Каждый из вариантов аутентификации реализован в отдельной команде.
    - Какой вариант применять, на усмотрение разработчика соотв.проекта.
    - Варианты аутентификации:

      1) Аутентификация через email и пароль
        - Для аутентификации нужен email и пароль.

      2) Аутентификация через email и код
        - Для аутентификации нужен email и код.
        - Код аутентификации приходит по email по запросу.
        - Действие кода аутентификации ограничено значением из конфига в мин.

      3) Аутентификация через phone и пароль
        - Для аутентификации нужен phone и пароль.

      4) Аутентификация через phone и код
        - Для аутентификации нужен phone и код.
        - Код аутентификации приходит по sms по запросу.
        - Действие кода аутентификации ограничено значением из конфига в мин.

      5) Классическая двухэтапная аутентификация
        - Для аутентификации нужен email, пароль и код.
        - Код приходит по sms по запросу.










  # Механизм восстановления пароля
    - Восстановление пароля происходит по стандартной схеме.
    - Пользователь через интерфейс вводит email и запрашивает восстановление.
    - Генерируется секретный код, и записывается в спец.таблицу в БД.
    - В этой таблице есть datetime-стобец expire, где указано время истечения кода.
    - Время действия кода в минутах можно указать в параметре "common_pass_recovery_code_lifetime".
    - Система формирует URL с кодом в query string и посылает пользователю.
    - URL формируется на основе значения из параметра "common_url_auth_doc".
    - Если пользователь кликает поссылке до expire, ему формируется новый пароль.

  # Защита от брутфорса
    - Пакет имеет встроенную систему от брутфорса - взлома пароля перебором.
    - Её можно вкл./выкл. с помощью параметра "bruteforce_protection_ison".
    - Как работает:

        После "bruteforce_protection_threshold" неудачных попыток аутентификации
        за "bruteforce_protection_counter_lifetime" минут, с каждой последующей
        попыткой механизм аутентификации для этого пользователя блокируется
        на "bruteforce_protection_delay" * X, где X - кол-во неудачных попыток
        аутентификации за указанное время, минус "bruteforce_protection_threshold".

    - Механизм делает попытки взломать пароль брутфорсом бессмысленными.

  # Настройка времени жизни аутентификационной куки
    - Пакет позволяет гибко настроить время жизни в минутах.
    - Глобальное значение можно указать в параметре "auth_cookie_lifetime_global".
    - Локальные значения (для групп) можно указать в "auth_cookie_lifetime_locals".
    - Пользователь может состоять 0, 1 или одновременно в нескольких группах.
    - В любом случае, используется MIN значение (глобальное тоже берётся в расчёт).

  # Настройка переадресации после входа
    - То есть, настройка куда попадает пользователь после аутентификации.
    - Можно указать глобально url для переадресации в "redirect_global_url".
    - Но он не действует, если включить глобально механизм переадресации на ref-url.
    - Он включается / выключается в параметре "redirect_global_ref_ison".
    - Это такое общее правило, оно работает и на глобальном, и на локальных уровнях.
    - В "redirect_local_urls" можно локально указать url'ы для групп.
    - Если пользователь состоит в нескольких, берётся первое вхождение, начиная с глобального.
    - В "redirect_local_ref_ison" можно вкл./выкл. переадресацию на ref-url локально для базовых URL.

  # Настройка столбцов, которые можно использовать в качестве логина
    - В теории, в качестве логина можно использовать любой столбец.
    - Главное, чтобы у этого столбца был unique-индекс.
    - Пакет позволяет указать список таких столбцов.
    - Настроимть этот список можно в параметре "logins".

  # Настройка факторов и сценариев аутентификации
    - По умолчанию поддерживается 3 фактора аутентификации:
    - Их список находится в параметре "authfactors":

      1) password         // с помощью пароля
      2) sms              // через смс
      3) phrase           // вопрос - ответ (кодовая фраза)

    - Пакет позволяет настраивать сценарии аутентификации.
    - Глобальный сценарий можно указать в "authfactors_scenario_global".
    - Локальные сценарии для групп в "authfactors_scenarios_local".
    - Подробно сценарии аутентификации описаны в конфиге.

  # Настройка SMS-аутентификации
    - Длину пароля и набор символов можно указать в "smsauth_length" и "smsauth_charset".
    - Можно ограничить max кол-во смс в "smsauth_smslimit", отправляемых
      за время жизни счётчика "smsauth_counter_lifetime".
    - Последнее обновляется после каждой отправки смс.
    - Это ограничение позволит защититься от атак на наш счёт у смс-провайдера.

  # Настройка аутентификации через секретную фразу
    - Можно ограничить MIN длину ответа в символах параметром "phraseauth_length".


```
## Функционал <a id="link4"></a>
```

  # Команды и к.команды #
  #---------------------#

    Команда           К.Команда               Описание
    ----------------------------------------------------------------------------------------------------------------------
    update            m5:update               Fires m5:call4update event, gets data from M1/M4, updates DB of M5
    switch            m5:switch               Turn on / Turn off the system access restrictions
    checkanon         m5:checkanon            Check whether an anonymous user in the system
    checkadmins       m5:checkadmins          Check whether admins in the system
    getuserprivs      m5:getuserprivs         Get all user privileges, taking into account personal and privileges of groups, where user consists
    getgroupprivs     m5:getgroupprivs        Get all group privileges
    users             m5:users                Get users list (can use filters)
    groups            m5:groups               Get groups list (can use filters)
    privileges        m5:privileges           Get get privileges list (can use filters)
    tags              m5:tags                 Get tags list (can use filters)
    -                 m5:new                  Create a new user / group / privilege / tag
    newuser           -                       Create a new user
    newgroup          -                       Create a new group
    newprivilege      -                       Create a new privilege
    newtag            -                       Create a new tag
    -                 m5:del                  Delete a user / group / privilege / tag
    deluser           -                       Delete a user
    delgroup          -                       Delete a group
    delprivilege      -                       Delete a privilege
    deltag            -                       Delete a tag
    -                 m5:change               Change a user / group / privilege / tag
    changeuser        -                       Change a user
    changegroup       -                       Change a group
    changeprivilege   -                       Change a privilege
    changetag         -                       Change a tag
    -                 m5:restore              Restore a user / group / privilege / tag
    restoreuser       -                       Restore deleted user
    restoregroup      -                       Restore deleted group
    restoreprivilege  -                       Restore deleted privilege
    restoretag        -                       Restore deleted tag
    -                 m5:attach               Attach one entity to another
    attachuser        -                       Attach a user to a group
    attachprivilege   -                       Attach a privilege to a user / group
    attachtag         -                       Attach a tag to a user / group / privilege
    -                 m5:detach               Detach one entity from another
    detachuser        -                       Detach a user from a group
    detachprivilege   -                       Detach a privilege from a user / group
    detachtag         -                       Detach a tag from a user / group / privilege



  # Обработчики событий #
  #---------------------#

    Обработчик        Ключи                   Описание
    ----------------------------------------------------------------------------------------------------------------------
    H1_update         m1:afterupdate          Calls c.command m5:update, which invokes update of DB of M5
                      m4:afterupdate


```
## Схемы взаимодействий с другими пакетами <a id="link5"></a>
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
## Кэширование <a id="link7"></a>
```





```
## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










