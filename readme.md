# M10 - Universal Chat
---
## Оглавление

  - [Ссылки](#link1)
  - [Введение](#link2)
  - [Общее описание работы пакета](#link3)
  -
	- [Функционал](#link98)
	- [Схемы взаимодействий с другими пакетами](#link99)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  > Адрес репозитория пакета M10 на github
      https://github.com/4gekkman/m10

	
			
```

## Введение <a id="link2"></a>
```

  ● Это M-пакет, представляющий собой универсальный чат.
 
 
```

## Общее описание работы пакета <a id="link3"></a>
```

-----------------------------------
Оглавление

  # Ссылки
  # Введение

  # Основные возможности универсального чата

    ▪ Возможность создавать несколько чатов (комнат)
    ▪ Связь с системой пользователей в M5
    ▪ Возможность назначать модераторов
    ▪ Лимитирование хранящихся в чатах сообщений
    ▪ Мгновенная доставка обновлений всем клиентам-подписчикам через websocket

  # Система банов

-----------------------------------

> Ссылки

> Введение
  - Здесь рассмотрены основные моменты работы универсального чата.
  - Общим планом рассмотрена вся картина в целом, и ключевые её части.

> Основные возможности универсального чата

  • Возможность создавать несколько чатов (комнат)
    - Можно создавать сколько угодно отдельных чатов (комнат).
    - Какие конкретно это комнаты, следует указывать в конфиге.
    - Система автоматически в течение ~5-10 минут создаст соотв.комнаты.

  • Связь с системой пользователей в M5
    - Писать в чат может только зарегистрированный в M5 пользователь.
    - Каждое сообщение в чате связано с конкретным пользователем.

  • Возможность назначать модераторов
    - Любого пользователя можно назначить модератором.
    - Модераторы могут:

      ▪ Скрывать/Раскрывать сообщения.
      ▪ Банить/Разбанить пользователей чата.

  • Лимитирование хранящихся в чатах сообщений
    - Кол-во сообщений, которые хранят чаты, можно ограничивать.
    - Есть 2 способа это делать:

      ▪ По времени хранения.
      ▪ По общему кол-ву сообщений.

  • Мгновенная доставка обновлений всем клиентам-подписчикам через websocket
    - Каждый клиент может быть подписан на от 0 и более чатов.
    - Любое обновление в этих чатах мгновенно доставляется ему через вебсокет.
    - Это могут быть следующие обновления:

      ▪ Поступление новых сообщений.
      ▪ Скрытие/Раскрытие каких-либо сообщений.

> Система банов

  - Пользователя можно забанить в конкретной комнате
  - Таблица банов, связана с rooms и m5_users
  - Можно указать срок бана (начиная с момента бана) и причину бана
  - Интерфейс бана:
    - Доступен прямо в чате для модераторов.
    - Модераторы указаны в конфиге M10, в настройках каждой из комнат.
    - При наведении на сообщение появляется кнопка "Бан".
    - При нажатии на кнопку, окошко между верхней плашкой "чат" и нижней "отправить сообщение"
      превращаются в интерфейс для бана.
    - В интерфейсе бана есть input (срок бана в минутах), textarea (причина бана), кнопки "Бан" и "Отмена".
  - Обработка банов производится по очереди демоном чата.
  - При нажатии на "Бан" в очередь чата добавляется задача на бан.
  - Что происходит при бане:
    - В таблицу банов добавляется новая запись.
      Запись содержит дату/время истечения бана, и причину бана.
      Она связывается с комнатой и пользователем.
    - Всем клиентам посылается через публичный канал сигнал удалить из себя
      все сообщения забаненного пользователя.
    - В чат добавляется новое сообщение от псевдо-пользователя "Система",
      имя которого в чате выделено красным цветом, а в качестве аватарки
      используется оная от ботов "csgohap". Сообщение содержит информацию
      типа: "Бан <юзер>. Причина: <причина>".












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










