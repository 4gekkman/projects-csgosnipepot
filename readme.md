# M1 - Управление Приложением
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
	- [Функционал](#link3)
	- [Интерфейсы](#link4)
	- [Виджеты](#link5)
	- [Установка, обновление, удаление](#link6)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета М1 на github
      https://github.com/4gekkman/M1

	
			
```

## Введение <a id="link2"></a>
```

  ● Это М-пакет для управления приложением.
  ● В нём сосредоточен весь соответствующий функционал.
  ● Он является корневым для всего приложения.
  ● Без него приложение не будет функционировать корректно.
  ● Но это не значит, что остальные М-пакеты зависят от этого.
  ● Они могут с успехом работать и без него.
  ● Однако, это будет в 100 раз менее удобно, чем с этим М-пакетом.

 
```

## Функционал <a id="link3"></a>
```
----------------------
Оглавление

  # Команды и к.команды #
  #---------------------#

    Команда           К.Команда               Описание
    ----------------------------------------------------------------------------------------------------------------------
    afterupdate     | m1:afterupdate        | Срабатывает после composer update, выполняет набор задач.
    parseapp        | m1:parseapp           | Парсит приложение, возбуждает событие "m1:afterupdate".
    sp_regs_update  | m1:sp_regs_update     | Актуализирует регистрации сервис-провайдеров пакетов.
    allrespublish   | m1:allrespublish      | Публикует ресурсы всех пакетов (имеющие соотв.теги).
    m_dbs_update    | m1:m_dbs_update       | Устанавливает / обновляет базы данных M-пакетов.
    minify          | m1:minify             | Создаёт c.min.css и j.min.js в public для C,M,L-пакетов.
    -               | m1:new                | Запустить конструктор создания нового ресурса

    new_m           | m1:new                | Новый M-пакет
    new_m_d         | m1:new                | Новый M-документ
    new_m_d_c       | m1:new                | Новая M-команда
    new_m_d_t       | m1:new                | Новая M-к.команда
    new_m_d_h       | m1:new                | Новый M-обработчик
    new_m_d_ct      | m1:new                | Новая пара M-команда - M-к.команда

    new_c           | m1:new                | Новый C-пакет
    new_c_d         | m1:new                | Новый C-документ

    new_l           | m1:new                | Новый L-пакет
    new_l_c         | m1:new                | Новая L-команда
    new_l_t         | m1:new                | Новая L-к.команда
    new_l_h         | m1:new                | Новый L-обработчик

    new_w           | m1:new                | Новый W-пакет
    new_w_c         | m1:new                | Новая W-команда
    new_w_t         | m1:new                | Новая W-к.команда
    new_w_h         | m1:new                | Новый W-обработчик

    new_k           | m1:new                | Новый K-пакет
    new_r           | m1:new                | Новый R-пакет
    new_s           | m1:new                | Новый S-пакет
    new_j           | m1:new                | Новый J-пакет


    deldb           | m1:deldb              | Удалить БД M-пакета с [id]
    release         | m1:release            | Создать на github новый релиз уровня [lvl] пакета [id]

  # Обработчики событий #
  #---------------------#

    Обработчик        Ключи                   Описание
    ----------------------------------------------------------------------------------------------------------------------
    H1_call4packs     m4:call4packs           Catches event from M4, starts m1:parseapp, which sends data to M4 to update its DB
    H2_fresh4m5       m5:call4update          Returns data about packages and commands for M5


----------------------

//------------------------------//
// afterupdate | m1:afterupdate //
//------------------------------//

  # Что делает
    - Срабатывает после composer update, выполняет набор задач.
    - Выполняет команды: parseapp, sp_regs_update, allrespublish,
      minify, m_dbs_update.
    - К.Команда m1:afterupdate добавляется в composer.json проекта,
      в массив "post-install-cmd"

  # Аргументы
    - Нет.


//------------------------//
// parseapp | m1:parseapp //
//------------------------//
// - Парсит приложение, возбуждает событие "m1:afterupdate".

  # Что делает
    ● Парсит структуру приложения, обновляя данные в БД M1.
    ● Возбуждает событие с ключём "m1:afterupdate"

  # Аргументы
    - Нет.


//------------------------------------//
// sp_regs_update | m1:sp_regs_update //
//------------------------------------//

  # Что делает
    ● Актуализирует регистрации сервис-провайдеров пакетов.
    ● По свежей структуре приложения составляет список СП.
    ● Синхронизирует config/app.php -> providers с этим списком.
    ● Удаляет из providers СП пакетов, которых нет в этом списке.
    ● Добавляет в providers СП пакетов, которые есть в этом списке.

  # Аргументы
    - Нет.


//----------------------------------//
// allrespublish | m1:allrespublish //
//----------------------------------//

  # Что делает
    ● Публикует ресурсы всех пакетов (имеющие соотв.теги).
    ● Выполняет publish для тегов, обозначающих все типы пакетов.

  # Аргументы
    - Нет.


//--------------------//
// minify | m1:minify //
//--------------------//

  # Что делает
    ● Создаёт c.min.css и j.min.js в public для C,M,L-пакетов.

  # Аргументы
    - Нет.


//--------------------------------//
// m_dbs_update | m1:m_dbs_update //
//--------------------------------//

  # Что делает
    ● Устанавливает / обновляет базы данных M-пакетов.
    ● Результаты отражаются в опубликованных конфигах
      M-пакетов, в массиве "databaseupdates".

  # Аргументы
    - Нет.


//------------------//
// deldb | m1:deldb //
//------------------//

  # Что делает
    ● Удалить БД M-пакета с [id].

  # Аргументы
    - [id] - базу данных пакета с каким ID удалить.


//----------------------//
// release | m1:release //
//----------------------//

  # Что делает
    ● Создать на github новый релиз уровня [lvl] пакета [id].
    ● OAuth2 token берётся из опубликованного конфига модуля M1.
    ● При создании нового релиза, добавляется единица к версии старого.
    ● Новая версия также прописывается в composer.json пакета,
      в extra -> version.

  # Аргументы
    - [lvl] - "patch", "minor" или "major".
    - [id] - ID модуля, релиз для которого требуется создать.




```

## Интерфейсы <a id="link4"></a>
```
----------------------
Оглавление

  # Main

----------------------

> Main
  - Нужен, чтобы визуально убедиться, что всё спарсилось правильно.
  - На главной должен быть список всех пакетов.
  - С фильтрацией по типам, доменам, локалям.
  - В каждый пакет должно быть можно "зайти" для получения доп.инфы.
  - У каждого типа пакетов свой формат страницы с этой доп.инфой.


```

## Виджеты <a id="link5"></a>
```

  - Отсутствуют.

```

## Установка, обновление, удаление <a id="link6"></a>
```

----------------------
Оглавление

  # Установка
  # Обновление
  # Удаление

----------------------

> Установка

  1. Добавить инфу о пакете в composer.json проекта
    - Добавить зависимость (укажите нужную вам версию):

        "require": {
            "4gekkman/m1": "1.0.*"
        }

    - Добавить адрес git-репозитория:

        "repositories": [
            {
                "type": "vcs",
                "url":  "git@github.com:4gekkman/m1.git"
            }
        ]

  2. Добавить к.команду "m1:afterupdate" в composer.json проекта
    - К.Команда m1:afterupdate добавляется в composer.json проекта,
      в массивы "post-install-cmd" и "post-update-cmd":

        php artisan m1:afterupdate

    - После этого она будет выполняться автоматически, после каждого
      composer update.

  3. Добавить сервис-провайдер пакета в config/app.php
    - Открыть конфиг config/app.php.
    - Найти там массив providers.
    - Добавить в него сервис-провайдер пакета M1:

        M1\ServiceProvider::class,

  4. Выполнить composer update

  5. Настроить модуль в его конфиге config/m1.php
    - И после этого выполнить composer update.

> Обновление

  1. Прописать в composer.json нужную версию пакета
    - Можно указать конкретную версию пакета.
    - А можно указать, чтобы при выполнении composer update
      автоматически устанавливалась свежая patch- / minor- / major-версия.
    - Примеры:

      "1.0.*"    // >=1.0.0 <1.1.0
      "~1.3"     // >=1.3.0 <2.0.0
      ">=2"      // >= 2.0.0

  2. Выполнить composer update
    - Сабж.

> Удаление

  1. Выполнить 1,2,3 из "Установка" наоборот.
  2. Выполнить composer update
  3. Вручную удалить базу данных M1 из СУБД.

```




## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










