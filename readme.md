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

  1. В конфигах D,L,W-пакетов задаются параметры роутинга
    - У каждого D,L,W-пакета есть опубликованный в /config конфиг.
    - В нём есть параметр 'routing', содержащий массив параметров роутинга.

  2. Обработчик H1_update ловит события m1:afterupdate
    - И запускает команду C1_update

  3. Команда C1_update обновляет автоматические роуты
    - Она извлекает данные о D,L,W-пакетах в системе.
    - Извлекает данные о роутинге из их опубликованных конфигов.
    - И на основании этих данных обновляет автоматические роуты.
    - Связывая их через транс-пакетную связь с пакетами в M1.

```
## Функционал <a id="link4"></a>
```

  # Команды и к.команды #
  #---------------------#

    Команда           К.Команда               Описание
    ----------------------------------------------------------------------------------------------------------------------
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

    ---------------------------------------------------------------------------------------------
    Пакет     Команда     К.Команда     Обработчик      Событие           Комментарий
    ---------------------------------------------------------------------------------------------

      нет

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










