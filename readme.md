# M12 - Frequently Asked Questions
---
## Оглавление

  - [Ссылки](#link1)
  - [Концепция работы FAQ](#link2)
  - [Заметки к релизам](#link100)

---

## Ссылки <a id="link1"></a>
```

  # Пакет для работы с markdown в Laravel 5.*
      https://packagist.org/packages/graham-campbell/markdown

  # Markdown cheat sheet
	  https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet

			
```

## Концепция работы FAQ <a id="link2"></a>
```
-----------------------------------
Оглавление

  # Каждая статья, это отдельный markdown-файл
  # Базовая папка со статьями FAQa
  # Формирование относительных URI групп и статей на клиенте
  # Парсинг в БД данных из базовой папки, и кэширование
  # Подстановка базового URL для изображений на клиенте
  # Интернационализация
  # Формат файла с мета-информацией о статье

-----------------------------------

  # Каждая статья, это отдельный markdown-файл
    - Все статьи пишутся в формате markdown.
    - Каждая статья является отдельным файлом с расширением .md

  # Базовая папка со статьями FAQa
    - В конфиге можно указать путь к базовой папке со статьями от корня проекта.
    - Формат того, что внутри базовой папки, такой:

      <faq №1>            | Папка, представляющая отдельный FAQ
        <Группа №1>     | Папка, представляющая отдельную группу
          <Статья №1>   | Папка со статьёй
            files
              articles      | Папка со статьями
                ru.md       | Статья в формате .md на русском языке
                en.md       | Статья в формате .md на английском языке
              files         | Файлы статьи howtoplay
                cat.jpg
                dog.png
                files.zip

    - Пример:

      • Путь к базовой папке относительно корня проекта
        - Пусть корневая папка с проектом у нас называется project, путь будет относительно неё.
        - "vendor/4gekkman/R6/faq".
        - Вот, что может в ней лежать:

          csgohap             | Раздел FAQ
            classicgame       | Группа статей FAQ
              howtoplay       | Папка статьи howtoplay
                articles      | Папка со статьями
                  ru.md       | Статья в формате .md на русском языке
                  en.md       | Статья в формате .md на английском языке
                files         | Файлы статьи howtoplay
                  cat.jpg
                  dog.png
                  files.zip

  # Формирование относительных URI групп и статей на клиенте
    - У каждой группы и статьи есть свой относительный URI.
    - Он формируется на основе названий папок разделов/групп и файлов статей.
    - Этот URI формируется относительно базового URL (см.ниже).
    - Рассмотрим, какие относительные URL будут у некоторых групп и статей из примера выше:

      cshogap             |
        classicgame       | <базовый URL>/classicgame
          howtoplay       | <базовый URL>/classicgame/howtoplay/ru
          howtoplay       | <базовый URL>/classicgame/howtoplay/en
          withdrawal      | <базовый URL>/classicgame/withdrawal/ru
          fairplay        | <базовый URL>/classicgame/fairplay/ru

  # Парсинг в БД данных из базовой папки, и кэширование

    • Общий принцип работы парсинга и кэширования
      - И парсинг, и кэширование производятся отдельными командами.
      - Но они всегда идут рука об руку. Последовательно, сначала парсинг, потом кэширование.
      - Это объединение производится в функции update faq.
      - При этом, запрос данных FAQ выполняется отдельной командой get faq.
      - Update faq выполняется при запросе get faq (при отсутствии кэша), или раз в час по расписанию.
      - Данные кэшируются на 120 минут. Таким образом, кэш всегда актуален.

    • В кэш попадают уже не MD-файлы, а HTML-файлы
      - В кэш помещаются уже HTML-, а не md-версии статей. Плюс, вспомогательная инфа.
      - MD-версии статей преобразуются в HTML-версии с помощью одного из многих парсеров.

    • При парсинге в public проекта копируются и все изображения
      - Аватар и картинки групп копируются в public-папку проекта.
      - Рассмотрим, что куда копируется на примере статьи howtoplay:

        ▪ Пусть базовая папка для данных FAQ у нас такая (относ.корня проекта):
          - vendor/4gekkman/R6/faq

        ▪ Пусть в ней лежит вот такая статья howtoplay:

          cshogap             | Раздел FAQ
            classicgame       | Группа статей FAQ
              howtoplay       | Папка статьи howtoplay
                articles
                  ru.md       | Статья в формате .md на русскомя языке
                  en.md       | Статья в формате .md на английском языке
                files         | Файлы статьи howtoplay
                  cat.jpg
                  dog.png
                  files.zip
            avatar.jpg        | Аватар группы classicgame

        ▪ Пусть папка с публичными ресурсами проекта находится по адресу (относ.корня проекта):
          - public/public

        ▪ Что нам надо туда скопировать:
          - Аватар группы classicgame (avatar.jpg).
          - Файлы статьи howtoplay: (cat.jpg, dog.png, files.zip).

        ▪ Что куда будет скопировано:

          public/public/R6/cshogap/classicgame/avatar.jpg
          public/public/R6/cshogap/classicgame/howtoplay/cat.jpg
          public/public/R6/cshogap/classicgame/howtoplay/dog.png
          public/public/R6/cshogap/classicgame/howtoplay/files.zip

  # Подстановка базового URL для изображений на клиенте

    • В чем проблема с URL изображений?
      - У одного и того же сайта может быть несколько разных хостов.
      - Например: localhost, localhost:3000 (browser sync), 180.10.10.10 (ip), site.ru (домен).
      - Необходимо, чтобы изображения в FAQ работали и загружались независимо от хоста.

    • Базовый URL для изображений FAQа
      - Эта та часть URL изображений FAQа, которая никогда не меняется относительно остальных оных.

    • Используем плейсхолдеры, заменяем их на клиенте на базовый URL
      - В MD-файлах можно либо писать full-url, либо вместо базового URL подставлять плейсхолдер.
      - Примеры:

        ▪ Базовый URL
          - http://site.ru/public/R6

        ▪ Полный URL
          - http://site.ru/public/R6/csgohap/classicgame/avatar.jpg

        ▪ URL с плейсхолдером на месте базового URL
          - {{$baseuri}}/csgohap/classicgame/avatar.jpg

      - Базовый URL изображений FAQ передаётся в клиент при загрузке документа.
      - И уже в клиенте он, с помощью рег.выражений, подставляется при загрузке HTML-документов FAQ,
        непосредственно перед подстановкой этих HTML куда надо.

  # Интернационализация
    - Может быть несколько версий одной и той же статьи, каждая на своём языке.
    - Внутри папки articles, названия статей представляют 2-буквенный код соотв.страны.
    - Например: ru.md, en.md, и т.д.
    - Также интернационализация находит своё применение в файлах мета-информации для статей.

  # Формат файла с мета-информацией о группе/статье
    - Этот файл должен содержать валидный json с информацией о статье.
    - Примеры:

      ▪ Мета-информация о группе

        {
          "ru": {
            "name": "Как играть в классическую игру?",
            "description": "Пошаговая инструкция, как играть в классическую игру."
          },
          "en": {
            "name": "How to play classic game?",
            "description": "Step-by-stem manual how to play classic game."
          },
          "avatar": "avatar.jpg"
        }

          *Путь к avatar.jpg вычисляется относительно папки files соотв.группы.

      ▪ Мета-информация о статье

        {
          "ru": {
            "name": "Как играть в классическую игру?",
            "description": "Пошаговая инструкция, как играть в классическую игру.",
            "author": {
              "name": "Иван Иванов",
              "url": "https://vk.com/3542452"
            }
          },
          "en": {
            "name": "How to play classic game?",
            "description": "Step-by-stem manual how to play classic game.",
            "author": {
              "name": "Ivan Ivanov",
              "url": "https://vk.com/3542452"
            }
          }
        }


```

## Заметки к релизам <a id="link100"></a>
```

  1.0.0
    - Первый релиз.

```










