# M16 - Be online system
---
## Оглавление

  - [Ссылки](#link1)
  - [Концепция работы системы "Будь онлайн"](#link2)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  # Адрес репозитория пакета M16 на github
      https://github.com/4gekkman/m16


			
```

## Концепция работы системы "Будь онлайн" <a id="link2"></a>
```
-----------------------------------
Оглавление

  # Ссылки
  # Введение

  # Как работает система "Будь онлайн"

    ▪ Отслеживание, онлайн ли игроки
    ▪ Отслеживание, сколько секунд игроки онлайн
    ▪ Отправка трейда не автоматически, а при нажатии на кнопку

-----------------------------------

> Ссылки

> Введение
  - Это система вознаграждения за проведённое игроком на сайте время.
  - За проведённое на сайте время игрок может получать в награду недорогие скины.

> Как работает система "Будь онлайн"

  • Отслеживание, онлайн ли игроки

    ▪ Ключ приложения из env('APP_KEY') должен быть доступен в Redis
      - Чтобы node.js мог расшифровать зашифрованную Laravel'ом куку,
        и извлечь из неё ID пользователя.

    ▪ Подключение к серверу
      - Когда игрок открывает документ, его клиент подключается к серверу node.js
      - Во время подключения серверу node.js передаётся зашифрованное содержимое куки auth.
      - Сервер node.js извлекает из redis ключ Laravel-приложения.
      - Используя пакет CryptoJS и ключ приложения

      расшифровывает содержимое auth с помощью п

      - Сервер node.js записывает в Redis для этого ID метку, что пользователь онлайн.

    ▪ Отключение от сервера
      - Когда игрок закрывает документ, его клиент отключается от сервера.
      - Во время отключения серверу передаётся ID игрока.
      - Сервер node.js удаляет из Redis для этого ID метку, что пользователь онлайн.

    ▪ Все метки имеют общий тег
      - Чтобы их легко можно было все извлечь.

  • Отслеживание, сколько секунд игроки онлайн

    ▪ Ежесекундные тики и команда для актуализации счётчиков онлайна
      - Каждую секунду должна срабатывать специальная команда.
      - Она обновляет в Redis информацию для пользователей, сколько они уже онлайн.
      - А именно, их "счётчики онлайна".
      - Команды добавляются в единую очередь, и выполняются по очереди.

    ▪ Понятие "непрерывный онлайн", удаление счетчиков онлайна
      - В конфиге задаётся значение M в секундах.
      - Если пользователь оффлайн >= M секунд, его "серия" непрерывного онлайна кончается.
      - После этого его счётчик онлайна удаляется из Redis.

    ▪ Создание новых счётчиков онлайна
      - Если метка онлайна есть, а счётчика нет, то счётчик создаётся.
      - Значение нового счётчика по умолчанию: 1 секунда.
      - Все счётчики онлайна имеют общий тег, чтобы их легко можно было извлечь.

    ▪ Обновление счётчиков онлайна
      - Значения всех существующих счётчиков онлайна извлекаются из Redis по тегу.
      - Затем, каждое из них увеличивается на 1, и записывается в Redis.

    ▪ Обнуление счётчика онлайн после отправки скина
      - Пользователь нажимает на кнопку "Получить".
      - Система отправляет ему оффер со скином.
      - Только после подтверждённой отправки счётчик онлайна в Redis обнуляется.

    ▪ Обнуление счётчика онлайн через L минут
      - В конфиге можно указать значение L в минутах.
      - Если счётчик онлайн превысил необходимое для выдачи скина
        значение на L секунд, то он обнуляется.

    ▪ Трансляция счётчика онлайна через частные каналы игрокам
      - Каждый игрок всегда владеет актуальным значением своего счётчика онлайна.

  • Отправка трейда не автоматически, а при нажатии на кнопку

    ▪ MIN для выдачи значение счётчика онлайна задаётся в конфиге
      - В секундах, будем называть его "время выдачи".

    ▪ Управление доступностью кнопки "Получить"
      - На клиенте кнопка недоступна, пока не настало время выдачи.
      - А когда время выдачи настаёт, кнопка становится доступной для нажатия.

    ▪ Асинхронная обработка запросов на выдачу
      - В базе данных должна быть специальная таблица для запросов на выдачу.
      - У каждого запроса могут быть статусы: создан; скин выбран; есть активный оффер; отменён (указать причину); выдан.
      - Это необходимо, чтобы не запрашивать инвентарь бота при каждом нажатии на кнопку.

    ▪ Проверка необходимых для выдачи условий
      - Перед отправкой оффера сервер проверяет чек-лист с условиями.
      - Проверка идёт строго по очереди.
      - Если какое-либо условие не выполнено, сервер сообщает об этом клиенту,
        по частному каналу, а клиент показывает пользователю тост с ошибкой и подробностями.

    ▪ Создание и отправка торгового предложения
      - Если все условия выполнены, то создаётся торговое предложение.
      - Клиент по частному каналу уведомляется, ему показывается сообщение
        со ссылкой на приём.

    ▪ Отмена выдачи
      - Если счётчик онлайн превысил необходимое для выдачи скина
        значение на L секунд, то все офферы и запросы на выдачу отменяются.
      - Пользователь по частному каналу уведомляется об этом.

    ▪ В конфиге можно указать группу ботов, обслуживающих систему
      - Пока что система будет просто брать 1-го бота из неё.


```

## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










