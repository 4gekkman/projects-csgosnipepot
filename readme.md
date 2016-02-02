# M4 - Routing for the app (master)
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
	- [Общая схема работы](#link3)
	- [Функционал](#link4)
	- [Схемы взаимодействий с другими пакетами](#link5)
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

  1. В конфигах D,L,W-пакетов задаются параметры роутинга
    - У каждого D,L,W-пакета есть опубликованный в /config конфиг.
    - В нём есть параметр 'routing', содержащий массив параметров роутинга.

	2. Запускается команда m4:call4update
	  - Она возбуждает событие с ключём "m4:call4packs"

	3. Обработчик "H1_call4packs" в пакете "M1" ловит это событие
	  - И выполняет команду m1:parseapp.
	  - В её конце возбуждается событие с ключём "m1:afterupdate".
	  - Оно несёт в себе нужную для M4 информацию о D,L,W-пакетах.

	4. Обработчик "H1_update" ловит это событие
	  - Извлекает из него нужную для M4 информацию.
	  - И вызывает команду "C1_update".
	  - Эта команда обновляет инфу в БД пакета о D,L,W-пакетах в системе.
	  - И парсит 'routing' из конфигов этих пакетов в БД пакета.

```
## Функционал <a id="link4"></a>
```

  # Команды и к.команды #
  #---------------------#

    Команда           К.Команда               Описание
    ----------------------------------------------------------------------------------------------------------------------
    update            -                       Accepts array of M1 structure data, and invoke update of M4 database
    -                 m4:call4update          Fires event m4:call4packs for package M1
    list              m4:list                 Gets the table of D,L,W-packs and their routes
    check             m4:check                Checks if any conflicts between auto and manual routes
    new               m4:new                  New manual route
    del               m4:del                  Del manual route
    -                 m4:switch               Turn on/off auto or manual route
    on                -                       Turn on auto or manual route
    off               -                       Turn off auto or manual route


  # Обработчики событий #
  #---------------------#

    Обработчик        Ключи                   Описание
    ----------------------------------------------------------------------------------------------------------------------
    H1_update         m1:afterupdate          Gets info about D,L,W-pack for M4 from M1, and calls update command
    H2_fresh4m5       m5:call4update          Returns data for M5 about routes and packs with which they associated


```
## Схемы взаимодействий с другими пакетами <a id="link5"></a>
```

  # Pull-взаимодействия (по инициативе этого пакета) #
  #--------------------------------------------------#

    [M4 <---> M1] Обновление БД пакета M4
    ---------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие
    ---------------------------------------------------------------------------
    M4                    m4:call4update
    M4                                                  m4:call4packs
    M1                                  H1_call4packs
    M1        C1_parseapp
    M1                                                  m1:afterupdate
    M4                                  H1_update
    M4        C1_update


  # Push-взаимодействия (по инициативе других пакетов) #
  #----------------------------------------------------#

    [M4 <---> M1] Обновление БД пакета M4
    ---------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие
    ---------------------------------------------------------------------------
    M1        C1_parseapp
    M1                                                  m1:afterupdate
    M4                                  H1_update
    M4        C1_update





```
## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










