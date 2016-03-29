<?php

$dogAny = function() {

  \Route::any('/{p1?}/{p2?}/{p3?}/{p4?}/{p5?}/{p6?}/{p7?}/{p8?}/{p9?}/{p10?}/{p11?}/{p12?}/{p13?}/{p14?}/{p15?}/{p16?}/{p17?}/{p18?}/{p19?}/{p20?}/{p21?}/{p22?}/{p23?}/{p24?}/{p25?}/{p26?}/{p27?}/{p28?}/{p29?}/{p30?}/{p31?}/{p32?}/{p33?}/{p34?}/{p35?}/{p36?}/{p37?}/{p38?}/{p39?}/{p40?}/{p41?}/{p42?}/{p43?}/{p44?}/{p45?}/{p46?}/{p47?}/{p48?}/{p49?}/{p50?}',
  function($x1='',$x2='',$x3='',$x4='',$x5='',$x6='',$x7 ='',$x8 ='',$x9 ='',$x10 = '',$x11='',$x12='',$x13='',$x14='',$x15='',$x16='',$x17 ='',$x18 ='',$x19 ='',$x20 = '',$x21='',$x22='',$x23='',$x24='',$x25='',$x26='',$x27 ='',$x28 ='',$x29 ='',$x30 = '',$x31='',$x32='',$x33='',$x34='',$x35='',$x36='',$x37 ='',$x38 ='',$x39 ='',$x40 = '',$x41='',$x42='',$x43='',$x44='',$x45='',$x46='',$x47 ='',$x48 ='',$x49 ='',$x50 = '',$x51='',$x52='',$x53='',$x54='',$x55='',$x56='',$x57 ='',$x58 ='',$x59 ='',$x60 = '')
  {

    /**
     *
     *  1. Извлечь параметры запроса
     *  2. Получить из конфига время кэширования
     *  3. По данным из $params найти роут
     *
     */

    // 1. Извлечь параметры запроса

      // 1.1. Подготовить массив для них
      $params = [
        "domain"      => "",
        "protocol"    => "",
        "subdomain"   => "",
        "uri"         => ""
      ];

      // 1.2. Получить domain
      $host_segments = explode('.', \Request::getHost());
      if(count(count($host_segments)) == 1) $params["domain"] = $host_segments[count($host_segments) - 1];
      else $params["domain"] = $host_segments[count($host_segments) - 2] . '.' . $host_segments[count($host_segments) - 1];

      // 1.3. Получить protocol
      $params["protocol"] = \Request::secure() ? "https" : "http";

      // 1.4. Получить subdomain
      if(count($host_segments) > 2) {
        for($i=0; $i<count($host_segments)-2; $i++) {
          $params["subdomain"] = $params["subdomain"] . $host_segments[$i] . ".";
        }
      }

      // 1.5. Получить uri
      $params["uri"] = '/' . \Request::path();

    // 2. Получить из конфига время кэширования
    $routescachetime = \Cache::remember("m4_routescachetime", 60, function() {
      return config("M4.routescachetime");
    });

    // 3. Составить массив URI, по которым потребуется искать роуты
    // - Все URI должны начинатсья с символа /

      // 3.1. Получить все сегменты URI
      $segments = \Request::segments();

      // 3.2. Подготовить массив
      $uris4search = ['/'];

      // 3.3. Наполнить $uris4search
      for($i=0; $i<count($segments); $i++) {

        // Создать переменную для результата
        $uri2add = '';

        // Добавить в $uri2add последний элемент массива $uris4search
        $uri2add = $uris4search[+count($uris4search)-1];

        // Добавить в $uri2add $i-ый элемент массива $segments
        if($i==0) $uri2add = $uri2add . $segments[$i];
        else $uri2add = $uri2add . '/' . $segments[$i];

        // Добавить $uri2add в $uris4search
        array_push($uris4search, $uri2add);

      }

      // 3.4. Преобрабовать $uris4search в массив с элементами в обратном порядке
      $uris4search = array_reverse($uris4search);

    // 4. По данным из $params найти роут и ID DLW-пакета
    // - И индекс сегмента в $uris4search

      // 4.1. Подготовить переменную для индекса
      $index = '';

      // 4.2. Произвести поиск роута и индекса
      for($i=0; $i<count($uris4search); $i++) {

        // 4.2.1. Искать сначала среди ручных роутов
        $dlw_pack_id = \Cache::remember("m4_".$params['protocol']."_".$params['subdomain']."_".$params['domain']."_".$uris4search[$i], $routescachetime, function() USE ($params, $uris4search, $i) {
          return \M4\Models\MD1_routes::with(['m1_packages'])->where(function($query) USE ($params, $uris4search, $i){
            $query->whereHas('types', function($query) USE ($params) { $query->where('name', 'manual'); })->
                    whereHas('protocols', function($query) USE ($params) { $query->where('name', $params['protocol']); })->
                    whereHas('subdomains', function($query) USE ($params) { $query->where('name', $params['subdomain']); })->
                    whereHas('domains', function($query) USE ($params) { $query->where('name', $params['domain']); })->
                    whereHas('uris', function($query) USE ($params, $uris4search, $i) { $query->where('name', $uris4search[$i]); });
          })->first();
        });

        // 4.2.2. Если среди ручных роутов не найдено, искать среди автоматических
        if(empty($dlw_pack_id)) {
          $dlw_pack_id = \Cache::remember("m4_".$params['protocol']."_".$params['subdomain']."_".$params['domain']."_".$uris4search[$i], $routescachetime, function() USE ($params, $uris4search, $i) {
            return \M4\Models\MD1_routes::with(['m1_packages'])->where(function($query) USE ($params, $uris4search, $i){
              $query->whereHas('types', function($query) USE ($params) { $query->where('name', 'auto'); })->
                      whereHas('protocols', function($query) USE ($params) { $query->where('name', $params['protocol']); })->
                      whereHas('subdomains', function($query) USE ($params) { $query->where('name', $params['subdomain']); })->
                      whereHas('domains', function($query) USE ($params) { $query->where('name', $params['domain']); })->
                      whereHas('uris', function($query) USE ($params, $uris4search, $i) { $query->where('name', $uris4search[$i]); });
            })->first()->m1_packages[0]->id_inner;
          });
        }

        // 4.2.3. Если роут найден
        if(!empty($dlw_pack_id)) {
          $index = $i;
          break;
        }

      }

      // 4.3. Если $dlw_pack_id не найден
      if(empty($dlw_pack_id)) {
        return "Document not found.";
      }

    // 5. Осуществить запрос к контроллеру связанного с роутом документа
    // - И вернуть response клиенту

      write2log(\Request::method(), []);



//      // 5.1] Объявить роут c URI == $uris4search[$index] на соответствующий контроллер
//      \Route::any('/d1', '\D1\Controller@getIndex');
//
//      \Route::controller($uris4search[$index], "\\".$dlw_pack_id."\\Controller");
//
//      // 5.2] Заменить оригинальный input на модифицированный
//      // - Модифицированный = оригинальный + $params.
//      // - $params должен быть доступен по ключу "segments_params"
//
//        // Извлечь оригинальный input
//        $modifyedInput = \Request::all();
//
//        // Подготовить массив для модифицированного инпута в
//        $modifyedInput['global'] = [];
//
//        // Дополнить его параметрами-сегментами
//        $modifyedInput['global']['params'] = $params;
//
//        // Дополнить его базовым URI, к которому идёт запрос
//        $modifyedInput['global']['base_uri'] = $uris4search[$index];
//
//        // Заменить
//        \Request::replace($modifyedInput);
//
//      // 5.3] Создать новый объект-запрос класса Request
//      $request = \Request::create($uris4search[$index], \Request::method(), \Request::all());
//
//      // 5.4] Отправить запрос и вернуть присланный в ответ результат
//      $response = \Route::dispatch($request)->getOriginalContent();
//
//      // 5.5] Вернуть $response клиенту
//      return $response;

  });

};
\Route::group(['domain' => getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s6}.{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s7}.{s6}.{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s8}.{s7}.{s6}.{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s9}.{s8}.{s7}.{s6}.{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);
\Route::group(['domain' => '{s10}.{s9}.{s8}.{s7}.{s6}.{s5}.{s4}.{s3}.{s2}.{s1}.'.getenv('APP_URL')], $dogAny);






