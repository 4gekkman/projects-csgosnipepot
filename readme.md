# M8 - Steam bots and trade operations automate
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
  - [Теоретическая база](#link3)
	- [Функционал](#link98)
	- [Схемы взаимодействий с другими пакетами](#link99)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета M8 на github
      https://github.com/4gekkman/m8


```

## Введение <a id="link2"></a>
```

  > Каково назначение этого M-пакета?
    - Управление Steam-ботами.
    - Автоматизация торговых операций в Steam.

  > Пакет работает в связке с M5
    - Пакет M5 предназначен для управления пользователями и правами.
    - Steam-ботами могут быть любые зарегистрированные там Steam-пользователи.
    - Без пакета M5 пакет M8 не может функционировать, т.е. является зависимым.
    - Эта зависимость проявляется в транс-пакетной их связи, определённой в БД M8.
 
 
```

## Теоретическая база <a id="link3"></a>
```

-----------------------------------
Оглавление

  # Ссылки
  # Введение



-----------------------------------

> Ссылки

  #

> Введение
  - Это теоретическая база пакета M8.
  - Здесь дана полная цельная картина его работы в мелком масштабе.
  - А также рассмотрены подробнее все наиболее важные части этой картины.





> Мозговой штурм

  - Ботом может стать лишь steam-пользователь.
  - То есть пользователь, состоящей в определённой группе, например "SteamUsers".
  - Имя этой группы должно быть можно указать в настройках M8.

  - Ботами являются все пользователи, состоящие в определённой группе, например "SteamBots".
  - Имя этой группы должно быть можно указать в настройках M8.

  - В M8 есть отдельная таблица с доп.свойствами для ботов.
  - Это, как минимум, 4 свойства:

    • login
    • password
    • shared_secret
    • identity_secret

  - В M8 должен быть интерфейс, показывающий всех ботов из SteamBots
  - В в нём должно быть можно входить редактировать доп.свойства каждого бота
  - Во фронт-таблице должны быть показаны какие-то важные хар-ки каждого бота

  - Необходимо обеспечить ботов функционалом по работе с торговыми предложениями.
  - Боты должны уметь делать следующее:

    • Создавать ТП к указанному торговому URL с указанными вещами из своего инвентаря.



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










