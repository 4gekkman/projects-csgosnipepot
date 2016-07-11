<?php
////======================================================////
////																										  ////
////                    Команда M-пакета						      ////
////																											////
////======================================================////
/**
 *  Что делает
 *  ----------
 *    - Get trade offers via get request to proper steam page and getting html
 *
 *  Какие аргументы принимает
 *  -------------------------
 *
 *    [
 *      "data" => [
 *        id_bot      | id бота, для которого надо извлечь торговые предложения
 *        mode        | режим работы команды
 *      ]
 *    ]
 *
 *  Формат возвращаемого значения
 *  -----------------------------
 *
 *    [
 *      status          // 0 - всё ОК, -1 - нет доступа, -2 - ошибка
 *      data            // результат выполнения команды
 *    ]
 *
 *  Значение data в зависимости от статуса
 *  --------------------------------------
 *
 *    status == 0
 *    -----------
 *      - ""
 *
 *    status == -1
 *    ------------
 *      - Не контролируется в командах. Отслеживается в хелпере runcommand.
 *
 *    status = -2
 *    -----------
 *      - Текст ошибки. Может заменяться на "" в контроллерах (чтобы скрыть от клиента).
 *
 *  Значения аргумента mode
 *  -----------------------
 *
 *    1 - [по умолчанию] извлечь не закрытые входящие торговые предложения (incoming offers)
 *    2 - извлечь закрытые входящие торговые предложения (incoming offers history)
 *    3 - извлечь не закрытые исходящие торговые предложения (sent offers)
 *    4 - извлечь закрытые исходящие торговые предложения (sent offers history)
 *
 *  Возможные состоятие торгового предложения
 *  -----------------------------------------
 *
 *    1   - Invalid
 *    2   - Active
 *    3   - Accepted
 *    4   - Countered
 *    5   - Expired
 *    6   - Canceled
 *    7   - Declined
 *    8   - InvalidItems
 *    9   - NeedsConfirmation
 *    10  - CanceledBySecondFactor
 *    11  - InEscrow
 *
 *  Возможные варианты подтверждения
 *  --------------------------------
 *
 *    0  - Invalid
 *    1  - Email
 *    2  - MobileApp
 *
 *
 *
 */

//---------------------------//
// Пространство имён команды //
//---------------------------//
// - Пример:  M1\Commands

  namespace M8\Commands;

//---------------------------------//
// Подключение необходимых классов //
//---------------------------------//

  // Базовые классы, необходимые для работы команд вообще
  use App\Jobs\Job,
      Illuminate\Queue\SerializesModels,
      Illuminate\Queue\InteractsWithQueue,
      Illuminate\Contracts\Queue\ShouldQueue;   // добавление в очередь задач

  // Классы, поставляемые Laravel
  use Illuminate\Routing\Controller as BaseController,
      Illuminate\Support\Facades\App,
      Illuminate\Support\Facades\Artisan,
      Illuminate\Support\Facades\Auth,
      Illuminate\Support\Facades\Blade,
      Illuminate\Support\Facades\Bus,
      Illuminate\Support\Facades\Cache,
      Illuminate\Support\Facades\Config,
      Illuminate\Support\Facades\Cookie,
      Illuminate\Support\Facades\Crypt,
      Illuminate\Support\Facades\DB,
      Illuminate\Database\Eloquent\Model,
      Illuminate\Support\Facades\Event,
      Illuminate\Support\Facades\File,
      Illuminate\Support\Facades\Hash,
      Illuminate\Support\Facades\Input,
      Illuminate\Foundation\Inspiring,
      Illuminate\Support\Facades\Lang,
      Illuminate\Support\Facades\Log,
      Illuminate\Support\Facades\Mail,
      Illuminate\Support\Facades\Password,
      Illuminate\Support\Facades\Queue,
      Illuminate\Support\Facades\Redirect,
      Illuminate\Support\Facades\Redis,
      Illuminate\Support\Facades\Request,
      Illuminate\Support\Facades\Response,
      Illuminate\Support\Facades\Route,
      Illuminate\Support\Facades\Schema,
      Illuminate\Support\Facades\Session,
      Illuminate\Support\Facades\Storage,
      Illuminate\Support\Facades\URL,
      Illuminate\Support\Facades\Validator,
      Illuminate\Support\Facades\View;

  // Доп.классы, которые использует эта команда


//---------//
// Команда //
//---------//
class C24_get_trade_offers_via_html extends Job { // TODO: добавить "implements ShouldQueue" - и команда будет добавляться в очередь задач

  //----------------------------//
  // А. Подключить пару трейтов //
  //----------------------------//
  use InteractsWithQueue, SerializesModels;

  //-------------------------------------//
  // Б. Переменные для приёма аргументов //
  //-------------------------------------//
  // - Которые передаются через конструктор при запуске команды

    // Принять данные
    public $data;

  //------------------------------------------------------//
  // В. Принять аргументы, переданные при запуске команды //
  //------------------------------------------------------//
  public function __construct($data)  // TODO: указать аргументы
  {

    $this->data = $data;

  }

  //----------------//
  // Г. Код команды //
  //----------------//
  public function handle()
  {

    /**
     * Оглавление
     *
     *  1. Провести валидацию входящих параметров
     *  2. Попробовать найти модель бота с id_bot
     *  3. В зависимости от mode запросить соотв.html с торговыми операциями
     *  4. Извлечь торговые предложения из $html
     *  5. Вернуть результаты
     *
     *  N. Вернуть статус 0
     *
     */

    //---------------------------------------------------------------------------------------------------------//
    // В соотв.со значением mode, запросить html с соотв.страницы в Steam, и спарсить оттуда все торг.операции //
    //---------------------------------------------------------------------------------------------------------//
    $res = call_user_func(function() { try {

      // 1. Провести валидацию входящих параметров
      $validator = r4_validate($this->data, [
        "id_bot"    => ["required", "regex:/^[1-9]+[0-9]*$/ui"],
        "mode"      => ["required", "in:1,2,3,4"],
      ]); if($validator['status'] == -1) {
        throw new \Exception($validator['data']);
      }

      // 2. Попробовать найти модель бота с id_bot
      $bot = \M8\Models\MD1_bots::find($this->data['id_bot']);
      if(empty($bot))
        throw new \Exception('Не удалось найти бота с ID = '.$this->data['id_bot']);
      if(empty($bot->steamid))
        throw new \Exception('У бота с ID = '.$this->data['id_bot'].' пустой steamid');

      // 3. В зависимости от mode запросить соотв.html с торговыми операциями
      $html = call_user_func(function() USE ($bot) {

        // 3.1. В зависимости от mode сформировать URL и query string параметры GET-запроса
        $settings = call_user_func(function() USE ($bot) {

          // 1] Подготовить массив для результатов
          $results = [
            "url"     => "",
            "params"  => []
          ];

          // 2] Если mode = 1
          if($this->data['mode'] == 1) {
            $results['url'] = "http://steamcommunity.com/profiles/".$bot->steamid."/tradeoffers";
            $results['params'] = [];
          }

          // 3] Если mode = 2
          if($this->data['mode'] == 2) {
            $results['url'] = "http://steamcommunity.com/profiles/".$bot->steamid."/tradeoffers";
            $results['params'] = [
              "history" => 1
            ];
          }

          // 4] Если mode = 3
          if($this->data['mode'] == 3) {
            $results['url'] = "http://steamcommunity.com/profiles/".$bot->steamid."/tradeoffers/sent";
            $results['params'] = [];
          }

          // 5] Если mode = 4
          if($this->data['mode'] == 4) {
            $results['url'] = "http://steamcommunity.com/profiles/".$bot->steamid."/tradeoffers/sent";
            $results['params'] = [
              "history" => 1
            ];
          }

          // n] Вернуть результаты
          return $results;

        });

        // 3.2. Осуществить GET-запрос к steam и получить HTML-документ в ответ
        $response = call_user_func(function() USE ($bot, $settings) {

          // 1] Осуществить запрос
          $result = runcommand('\M8\Commands\C6_bot_request_steam', [
            "id_bot"          => $bot->id,
            "method"          => "GET",
            "url"             => $settings['url'],
            "cookies_domain"  => 'steamcommunity.com',
            "data"            => $settings['params'],
            "ref"             => ""
          ]);
          if($result['status'] != 0)
            throw new \Exception($result['data']['errormsg']);

          // 2] Вернуть результаты (guzzle response)
          return $result['data']['response'];

        });

        // 3.3. Если код ответа не 200, сообщить и завершить
        if($response->getStatusCode() != 200)
          throw new \Exception('Unexpected response from Steam: code '.$response->getStatusCode());

        // 3.4. Получить из $response строку с HTML из ответа
        $html = (string) $response->getBody();

        // 3.n. Вернуть результат
        return $html;

      });

      // 4. Извлечь торговые предложения из $html
      // - Формат результата должен совпадать с форматом при извлечении через API.
      // - Он должен быть такой:
      //
      //    [
      //      "trade_offers_sent" => [
      //        0 => [
      //          tradeofferid            // id торгового предложения
      //          accountid_other         // id партнёра по торговле (partner из trade url, не путать со steamid)
      //          message                 // = '' сообщение
      //          expiration_time         // временная метка, когда это ТП истечёт
      //          trade_offer_state       // цифра, обозначающая текущее состояние ТП
      //          is_our_offer            // является ли ТП исходящим (true/false)
      //          time_created            // временная метка создания ТП
      //          time_updated            // временная метка обновления ТП
      //          from_real_time_trade    // = false
      //          escrow_end_date         // временная метка истечения escrow
      //          confirmation_method     // = 2 (через мобильный аутентификатор)
      //          items_to_give => [
      //            0 => [
      //              appid
      //              contextid
      //              assetid
      //              classid
      //              instanceid
      //              amount
      //              missing
      //            ],
      //            1 => [ ... ]
      //          ]
      //          items_to_receive => [ ... ]
      //        ],
      //        1 => [ ... ]
      //      ],
      //      "trade_offers_received" => [
      //        0 => [ ... ],
      //        1 => [ ... ]
      //      ]
      //    ]
      //
      $tradeoffers = call_user_func(function() USE ($html) {

        // 4.1. Подготовить массив для результатов
        $tradeoffers = [
          "trade_offers_sent" => [],
          "trade_offers_received" => []
        ];

        // 4.2. Создать новые объекты DOMDocument и DOMXpath, загрузить в них $html
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        // 4.3. Получить все элементы с торговыми предложениями
        $tradeOfferElements = $xpath->query('//div[@id[starts-with(.,"tradeofferid_")]]');

        // 4.4. Пробежаться по $tradeOfferElements и наполнить $tradeoffers
        foreach ($tradeOfferElements as $tradeOfferElement) {

          // 1] Подготовить массив для ТП
          $tradeoffer = [];

          // 2] tradeofferid
          $tradeoffer['tradeofferid'] = str_replace('tradeofferid_', '', $tradeOfferElement->getAttribute('id'));

          // 3] accountid_other
          $secondaryItemsElement = $xpath->query('.//div[contains(@class, "tradeoffer_items secondary")]', $tradeOfferElement)->item(0);
          $tradeoffer['accountid_other'] = $xpath->query('.//a[@data-miniprofile]/@data-miniprofile', $secondaryItemsElement)->item(0)->nodeValue;

          // 4] message
          $messageElement = $xpath->query('.//div[contains(@class, "tradeoffer_message")]/div[contains(@class, "quote")]', $tradeOfferElement)->item(0);
          if(!is_null($messageElement))
            $tradeoffer['message'] = $messageElement->nodeValue;

          // 5] expiration_time
          $footerElement = $xpath->query('.//div[contains(@class, "tradeoffer_footer")]', $tradeOfferElement)->item(0);
          $tradeoffer['expiration_time'] = !empty($footerElement->nodeValue) ? strtotime(str_replace('Offer expires on ', '', $footerElement->nodeValue)) : "";

          // 6] trade_offer_state + confirmation_method + time_updated + escrow_end_date

            // 6.1] Получить $bannerElement
            $bannerElement = $xpath->query('.//div[contains(@class, "tradeoffer_items_banner")]', $tradeOfferElement)->item(0);

            // 6.2] Active (2)
            if (is_null($bannerElement))
              $tradeoffer['trade_offer_state'] = 2;

            // 6.3] Прочие
            else {

              // 6.3.1] trade_offer_state(9) + confirmation_method(2)
              if (strpos($bannerElement->nodeValue, 'Awaiting Mobile Confirmation') !== false) {
                $tradeoffer['trade_offer_state']  = 9;
                $tradeoffer['confirmation_method'] = 2;
              }

              // 6.3.2] trade_offer_state(9) + confirmation_method(1)
              else if (strpos($bannerElement->nodeValue, 'Awaiting Email Confirmation') !== false) {
                $tradeoffer['trade_offer_state']  = 9;
                $tradeoffer['confirmation_method'] = 1;
              }

              // 6.3.3] trade_offer_state(6) + time_updated
              else if (strpos($bannerElement->nodeValue, 'Trade Offer Canceled') !== false) {
                $tradeoffer['trade_offer_state'] = 6;
                $canceledDate = strtotime(str_replace('Trade Offer Canceled ', '', $bannerElement->nodeValue));
                if($canceledDate !== false)
                  $tradeoffer['time_updated'] = $canceledDate;
              }

              // 6.3.4] trade_offer_state(7) + time_updated
              else if (strpos($bannerElement->nodeValue, 'Trade Declined') !== false) {
                $tradeoffer['trade_offer_state'] = 7;
                $declinedDate = strtotime(str_replace('Trade Declined ', '', $bannerElement->nodeValue));
                if($declinedDate !== false) {
                  $tradeoffer['time_updated'] = $declinedDate;
                }
              }

              // 6.3.5] trade_offer_state(11) + time_updated + escrow_end_date
              else if (strpos($bannerElement->nodeValue, 'On hold') !== false) {

                // trade_offer_state(11)
                $tradeoffer['trade_offer_state'] = 11;

                // time_updated
                $split = explode('.', $bannerElement->nodeValue);
                $acceptedString = trim($split[0]);
                $acceptedDate = \DateTime::createFromFormat('M j, Y @ g:ia', str_replace('Trade Accepted ', '', $acceptedString));
                if ($acceptedDate !== false) {
                  $tradeoffer['time_updated'] = $acceptedDate->getTimestamp();
                }

                // escrow_end_date
                $escrowString = trim($split[1]);
                $escrowDate = \DateTime::createFromFormat('M j, Y @ g:ia', str_replace('On hold until ', '', $escrowString));
                if($escrowDate !== false) {
                  $tradeoffer['escrow_end_date'] = $escrowDate->getTimestamp();
                }

              }

              // 6.3.6] trade_offer_state(3) + time_updated
              else if (strpos($bannerElement->nodeValue, 'Trade Accepted') !== false) {
                $tradeoffer['trade_offer_state'] = 3;
                $acceptedDate = \DateTime::createFromFormat('j M, Y @ g:ia', str_replace('Trade Accepted ', '', trim($bannerElement->nodeValue)));
                if ($acceptedDate !== false) {
                  $tradeoffer['time_updated'] = $acceptedDate->getTimestamp();
                }
              }

              // 6.3.7] trade_offer_state(8)
              else if (strpos($bannerElement->nodeValue, 'Items Now Unavailable For Trade') !== false) {
                $tradeoffer['trade_offer_state'] = 8;
              }

              // 6.3.8] trade_offer_state(4) + time_updated
              else if (strpos($bannerElement->nodeValue, 'Counter Offer Made') !== false) {
                $tradeoffer['trade_offer_state'] = 4;
                $counteredDate = strtotime(str_replace('Counter Offer Made ', '', $bannerElement->nodeValue));
                if ($counteredDate !== false) {
                  $tradeoffer['time_updated'] = $counteredDate;
                }
              }

              // 6.3.9] trade_offer_state(5) + time_updated
              else if (strpos($bannerElement->nodeValue, 'Trade Offer Expired') !== false) {
                $tradeoffer['trade_offer_state'] = 5;
                $expiredDate = strtotime(str_replace('Trade Offer Expired ', '', $bannerElement->nodeValue));
                if ($expiredDate !== false) {
                  $tradeoffer['time_updated'] = $expiredDate;
                }
              }

              // 6.3.10] trade_offer_state(1)
              else {
                $tradeoffer['trade_offer_state'] = 1;
              }

            }

          // 7] is_our_offer
          $tradeoffer['is_our_offer'] = ($this->data['mode'] == 1 || $this->data['mode'] == 2) ? true : false;

          // 8] time_created
          $tradeoffer['time_created'] = "";

          // 9] from_real_time_trade
          $tradeoffer['from_real_time_trade'] = false;

          // 10] Primary: items_to_give / items_to_receive
          $primary = call_user_func(function() USE ($xpath, $tradeOfferElement) {

            // 10.1] Подготовить массив для результатов
            $results = [];

            // 10.2] Получить список элементов, которые надо будет отдать
            $primaryItemsElement = $xpath->query('.//div[contains(@class, "tradeoffer_items primary")]', $tradeOfferElement)->item(0);
            $itemsToGiveList = $xpath->query('.//div[contains(@class, "tradeoffer_item_list")]/div[contains(@class, "trade_item")]', $primaryItemsElement);

            // 10.3] Наполнить $results
            foreach($itemsToGiveList as $itemToGive) {

              // 10.3.1] Подготовить массив для $itemToGive
              $itemToGive_arr = [];

              // 10.3.2] Получить массив с нужной нам информацией
              // - Возможные варианты значения, которое разбивается:
              //
              //   1) classinfo/appId/classId/instanceId     | classinfo/570/583164181/93973071
              //   2) appId/contextId/assetId/steamId        | 570/2/7087209304/76561198045552709
              //
              $itemInfo = explode('/', $itemToGive->getAttribute('data-economy-item'));

              // 10.3.3] Наполнить $itemToGive_arr
              if($itemInfo[0] == 'classinfo') {
                $itemToGive_arr["appid"] = $itemInfo[1];
                $itemToGive_arr["classid"] = $itemInfo[2];
                if(isset($itemInfo[3])) $itemToGive_arr["instanceid"] = $itemInfo[3];
              } else {
                $itemToGive_arr["appid"] = $itemInfo[0];
                $itemToGive_arr["contextid"] = $itemInfo[1];
                $itemToGive_arr["assetid"] = $itemInfo[2];
              }

              // 10.3.4] Добавить значение missing
              if(strpos($itemToGive->getAttribute('class'), 'missing') !== false)
                $itemToGive_arr["missing"] = true;
              else
                $itemToGive_arr["missing"] = false;

              // 10.3.5] Добавить значение image
              $image = call_user_func(function() USE ($itemToGive, $xpath) {

                // Найти изображение в $itemToGive
                $img = $xpath->query('.//img/@src', $itemToGive);

                // Если $img пуста, вернуть пустую строку
                if($img->length == 0) return '';

                // Если $img не пуста, добавить в конце " 2x"
                else $img = $img[0]->nodeValue . ' 2x';

                // Вернуть URL изображения
                return $img;

              });
              $itemToGive_arr["image"] = $image;

              // 10.3.n] Добавить $itemToGive_arr в $results
              array_push($results, $itemToGive_arr);

            }

            // 10.n] Вернуть результат
            return $results;

          });

          // 11] Secondary: items_to_give / items_to_receive
          $secondary = call_user_func(function() USE ($xpath, $tradeOfferElement){

            // 11.1] Подготовить массив для результатов
            $results = [];

            // 11.2] Получить список элементов, которые надо будет отдать
            $secondaryItemsElement = $xpath->query('.//div[contains(@class, "tradeoffer_items secondary")]', $tradeOfferElement)->item(0);
            $itemsToReceiveList = $xpath->query('.//div[contains(@class, "tradeoffer_item_list")]/div[contains(@class, "trade_item")]', $secondaryItemsElement);

            // 11.3] Наполнить $results
            foreach($itemsToReceiveList as $itemToReceive) {

              // 11.3.1] Подготовить массив для $itemToReceive
              $itemToReceive_arr = [];

              // 11.3.2] Получить массив с нужной нам информацией
              // - Возможные варианты значения, которое разбивается:
              //
              //   1) classinfo/appId/classId/instanceId     | classinfo/570/583164181/93973071
              //   2) appId/contextId/assetId/steamId        | 570/2/7087209304/76561198045552709
              //
              $itemInfo = explode('/', $itemToReceive->getAttribute('data-economy-item'));

              // 11.3.3] Наполнить $itemToReceive_arr
              if($itemInfo[0] == 'classinfo') {
                $itemToReceive_arr["appid"] = $itemInfo[1];
                $itemToReceive_arr["classid"] = $itemInfo[2];
                if(isset($itemInfo[3])) $itemToReceive_arr["instanceid"] = $itemInfo[3];
              } else {
                $itemToReceive_arr["appid"] = $itemInfo[0];
                $itemToReceive_arr["contextid"] = $itemInfo[1];
                $itemToReceive_arr["assetid"] = $itemInfo[2];
              }

              // 11.3.4] Добавить значение missing
              if(strpos($itemToReceive->getAttribute('class'), 'missing') !== false)
                $itemToReceive_arr["missing"] = true;
              else
                $itemToReceive_arr["missing"] = false;

              // 11.3.5] Добавить значение image
              $image = call_user_func(function() USE ($itemToReceive, $xpath) {

                // Найти изображение в $itemToReceive
                $img = $xpath->query('.//img/@src', $itemToReceive);

                // Если $img пуста, вернуть пустую строку
                if($img->length == 0) return '';

                // Если $img не пуста, добавить в конце " 2x"
                else $img = $img[0]->nodeValue . ' 2x';

                // Вернуть URL изображения
                return $img;

              });
              $itemToReceive_arr["image"] = $image;

              // 11.3.n] Добавить $itemToReceive_arr в $results
              array_push($results, $itemToReceive_arr);

            }

            // 11.n] Вернуть результат
            return $results;

          });

          // 12] В зависимости от mode записать их в items_to_give / items_to_recieve
          // - Если mode = 1/2, то в items_to_recieve
          // - Если mode = 3/4, то в items_to_give

            // 12.1] Если mode = 1/2
            if($this->data['mode'] == 1 || $this->data['mode'] == 2) {
              $tradeoffer['items_to_recieve'] = $primary;
              $tradeoffer['items_to_give'] = $secondary;
            }

            // 12.2] Если mode = 3/4
            if($this->data['mode'] == 3 || $this->data['mode'] == 4) {
              $tradeoffer['items_to_recieve'] = $secondary;
              $tradeoffer['items_to_give'] = $primary;
            }

          // 13] Аватар партнёра
          $avatar = call_user_func(function() USE ($tradeOfferElement, $xpath) {

            // 13.1] Получить URL аватара партнёра
            // - Если mode == 1 или 2, то брать аватар из primary.
            // - Если mode == 3 или 4, то брать аватар из seconfary.
            $avatar = call_user_func(function() USE ($tradeOfferElement, $xpath) {

              // 13.1.1] Если mode = 1/2
              if($this->data['mode'] == 1 || $this->data['mode'] == 2) {

                // Найти аватар в $html
                $ava = $xpath->query('//div[contains(@class, "tradeoffer_items primary")]/descendant::a/img/@src', $tradeOfferElement);

                // Если $ava пуст, вернуть пустую строку
                if($ava->length == 0) return '';

                // Иначе, вернуть URL аватара
                return $ava[0]->nodeValue;

              }

              // 13.1.2] Если mode = 3/4
              if($this->data['mode'] == 3 || $this->data['mode'] == 4) {

                // Найти аватар в $html
                $ava = $xpath->query('//div[contains(@class, "tradeoffer_items secondary")]/descendant::a/img/@src', $tradeOfferElement);

                // Если $ava пуст, вернуть пустую строку
                if($ava->length == 0) return '';

                // Иначе, вернуть URL аватара
                return $ava[0]->nodeValue;

              }

              // 13.1.3] Иначе вернуть пустую строку
              return "";

            });

            // 13.2] Добавить "_full" в конце к имени аватара
            $full_avatar = call_user_func(function() USE ($avatar) {

              // Если $avatar пуст, ничего не делать
              if(empty($avatar)) return "";

              // Извлечь расширение
              preg_match("#\.[^\/]+$#ui", $avatar, $matches);
              $ext = $matches[0];

              // Удалить из $avatar расширение
              $avatar = preg_replace("#\.[^\/]+$#ui", '', $avatar);

              // Добавить в конец $avatar строку '_full', и затем $ext
              $avatar = $avatar . '_full' . $ext;

              // Вернуть результат
              return $avatar;

            });

            // 13.3] Вернуть URL аватара
            return $full_avatar;

          });
          $tradeoffer['avatar'] = $avatar;

          // n] Добавить $tradeoffer в $tradeoffers
          // - Если mode = 1/2, то в trade_offers_sent
          // - Если mode = 3/4, то в trade_offers_received
          if($this->data['mode'] == 1 || $this->data['mode'] == 2)
            array_push($tradeoffers['trade_offers_sent'], $tradeoffer);
          if($this->data['mode'] == 3 || $this->data['mode'] == 4)
            array_push($tradeoffers['trade_offers_received'], $tradeoffer);

        }

        // 4.n. Вернуть результаты
        return $tradeoffers;

      });

      // 5. Вернуть результаты
      return [
        "status"  => 0,
        "data"    => [
          "tradeoffers" => $tradeoffers
        ]
      ];


    } catch(\Exception $e) {
        $errortext = 'Invoking of command C1_get_trade_offers_via_html from M-package M8 have ended on line "'.$e->getLine().'" on file "'.$e->getFile().'" with error: '.$e->getMessage();
        Log::info($errortext);
        write2log($errortext, ['M8', 'C1_get_trade_offers_via_html']);
        return [
          "status"  => -2,
          "data"    => [
            "errortext" => $errortext,
            "errormsg" => $e->getMessage()
          ]
        ];
    }}); if(!empty($res)) return $res;


    //---------------------//
    // N. Вернуть статус 0 //
    //---------------------//
    return [
      "status"  => 0,
      "data"    => ""
    ];

  }

  //--------------//
  // Д. Заготовки //
  //--------------//
  /**
   *
   * Д1. Провести валидацию
   * Д2. Начать транзакцию
   * Д3. Подтвердить транзакцию
   * Д4. Сохранить данные в БД
   * Д5. Создать элемент
   * Д6. Удалить элемент
   *
   *
   */


    // Д1. Провести валидацию //
    //------------------------//

      //// x. Провести валидацию
      //
      //  // Создать объект-валидатор
      //  $validator = Validator::make($this->data, [
      //    'prop1'               => 'sometimes|rule1',
      //    'prop2'               => 'sometimes|rule2',
      //    'prop3'               => 'sometimes|rule3',
      //  ]);
      //
      //  // Провести валидацию, и если она провалилась...
      //  if ($validator->fails()) {
      //
      //    // Вернуть статус -2 и ошибку
      //    return [
      //      "status"  => -2,
      //      "data"    => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
      //    ];
      //
      //  }


    // Д2. Начать транзакцию //
    //-----------------------//

      //// x. Начать транзакцию
      //DB::beginTransaction();


    // Д3. Подтвердить транзакцию //
    //----------------------------//

      //// x. Подтвердить транзакцию
      //DB::commit();


    // Д4. Сохранить данные в БД //
    //---------------------------//

      //// x. Сохранить данные в БД
      //try {
      //
      //  // 4.1. Получить eloquent-объект
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 4.2. Сохранить в v присланные данные
      //  foreach($this->data as $key => $value) {
      //
      //    // Если $key == 'id', перейти к следующей итерации
      //    if($key == 'id') continue;
      //
      //    // Если в таблице есть столбец $key
      //    if(lib_hasColumn('m1', 'MD1_somemodel', $key)) {
      //
      //      // Изменить значение столбца $key на $value
      //      $item[$key] = $value;
      //
      //      // Сохранить изменения
      //      $item->save();
      //
      //    }
      //
      //  }
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д5. Создать элемент //
    //---------------------//

      //// x. Создать новый элемент
      //try {
      //
      //  // 1] Попробовать найти удалённый элемент
      //  $item = \M7\Models\MD1_somemodel::onlyTrashed()->where('name','=',$this->data['name'])->first();
      //
      //  // 2] Если удалённый элемент с таким именем не найдн
      //  if(empty($item)) {
      //
      //    // 2.1] Создать новый элемент
      //    $item = new \M1\Models\MD1_somemodel();
      //
      //    // 2.2] Наполнить $new данными
      //    $item->name                        = $this->data['name'];
      //    $item->description                 = $this->data['description'];
      //
      //    // 2.3] Сохранить $new в БД
      //    $item->save();
      //
      //  }
      //
      //  // 3] Если удалённый элемент найден
      //  if(!empty($item)) {
      //
      //    // 3.1] Восстановить его
      //    $item->restore();
      //
      //    // 3.2] Обновить некоторые свойства права
      //    $item->description                 = $data['description'];
      //
      //    // 3.3] Сохранить изменения
      //    $item->save();
      //
      //  }
      //
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}


    // Д6. Удалить элемент //
    //---------------------//

      //// x. Удалить элемент
      //try {
      //
      //  // 1] Получить eloquent-модель элемента, который требуется удалить
      //  $item = \M1\Models\MD1_somemodel::find($this->data['id']);
      //
      //  // 2] Удалить элемент
      //  $item->delete();
      //
      //} catch(\Exception $e) {
      //
      //  // Откатить транзакцию
      //  DB::rollBack();
      //
      //  // Вернуть статус -2 и ошибку
      //  return [
      //    "status"  => -2,
      //    "data"    => $e->getMessage()
      //  ];
      //
      //}




}

?>

