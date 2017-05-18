/*//================================================////
////																								////
////   m.js - клиентская модель шаблона документа		////
////																								////
////================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *
 *  А. Стартовая подготовка
 *
 *    А1. Сохранить ссылку на объект-модель в self
 *    А2. Подготовить объект-контейнер для всех свойств модели
 *    А3. Загрузить в модель весь её функционал из f.js
 *    А4. Подключение к websocket-серверам, назначение обработчиков для входящих сообщений
 *
 *	s0. Библиотека данных, доступных всем прочим моделям
 *
 * 		s0.1. Объект-контейнер для всех свойств модели
 * 	  s0.2. Модель "механизма отложенного сохранения для текстовых полей"
 *		s0.3. Счётчик ожидающих ответа ajax-запросов
 *		s0.4. Таймеры для функций, осуществляющих ajax-запросы
 *		s0.5. Виден ли щит "идёт ajax-запрос"
 *    s0.6. Аутентификационная модель
 *
 *  s1. Связанная с аутентификацией модель
 *
 *		s1.1. Объект-контейнер для всех свойств модели
 *    s1.2. ФИО аутентифицированного пользователя
 *    s1.3. Аватар аутентифицированного пользователя
 *    s1.n. Индексы и вычисляемые значения
 *
 *  s2. Модель левого навигационного меню
 *
 *		s2.1. Объект-контейнер для всех свойств модели
 *    s2.2. Наблюдаемый массив поддокументов приложения
 *    s2.3. Ссылка на выбранный поддокумент приложения
 *    s2.4. Корневой URL запроса
 *    s2.n. Индексы и вычисляемые значения
 *
 *  sN. Данные, которым доступны все прочие данные
 *
 *    sN.1. Объект-контейнер для всех свойств модели
 *    sN.2.
 * 		sN.n. Индексы и вычисляемые значения
 *
 *  X. Подготовка к завершению
 *
 * 		X1. Сервис провайдер клиентской модели
 *    X2. Вернуть ссылку self на объект-модель
 *
 *
 *
 */


//====================//
// 			        		 	//
// 			 Модель  			//
// 			         			//
//====================//
var LayoutModelProto = { constructor: function(LayoutModelFunctions) {
	
	//--------------------------------------//
	// 			        		 	                  //
	// 			 А. Стартовая подготовка  			//
	// 			         			                  //
	//--------------------------------------//

	//----------------------------------------------//
	// А1. Сохранить ссылку на объект-модель в self //
	//----------------------------------------------//
	var self = this;

	//----------------------------------------------------------//
	// А2. Подготовить объект-контейнер для всех свойств модели //
	//----------------------------------------------------------//
	self.m = {};

	//---------------------------------------------------//
	// А3. Загрузить в модель весь её функционал из f.js //
	//---------------------------------------------------//
	self.f = Object.create(LayoutModelFunctions).constructor(self);

	//--------------------------------------------------------------------------------------//
	// А4. Подключение к websocket-серверам, назначение обработчиков для входящих сообщений //
	//--------------------------------------------------------------------------------------//
	ko.computed(function(){


		//-------------------------------------------------------------//
		// А4.1. Выполнять код ниже лишь 1 раз, при загрузке документа //
		//-------------------------------------------------------------//
		if(!ko.computedContext.isInitial()) return;


		//----------------------------------------//
		// А4.2. Подключения к websocket-серверам //
		//----------------------------------------//
		self.websocket = {};

			// А4.2.1. Подключение ws1 //
			//-------------------------//
//			self.websocket.ws1 = io('http://'+server.settings.websocket_server_ip+':6001');


		//--------------------------------------------------------------//
		// А4.3. Назначение обработчиков сообщений с websocket-серверов //
		//--------------------------------------------------------------//

			// А4.3.1. Добавление в интерфейс новых записей, поступивших в лог //
			//-----------------------------------------------------------------//
			//
			// 	> Сервер
			// 	  - ws1
			//
			// 	> Канал
			// 	  - m2:M2\\Documents\\Main\\Events\\E1_broadcast
			//
			// 	> Описание
			// 	  - В лог, в БД модуля M2, могут поступать новые сообщения.
			// 	  - Задача в том, чтобы они сразу же отображались в открытых интерфейсах лога.
			// 	  - Для этого слушаем вышеуказанны канал, и принимаем из него сообщения.
			// 	  - Одно сообщение означает одну новую запись в лог.
			// 	  - Сообщения содержат текст добавляемого в лог сообщения, и список его тегов.
			// 	  - В качестве обработчика, назначаем функцию, которая добавляет сообщение в интерфейс.
			//
//			self.websocket.ws1.on("m2:M2\\Documents\\Main\\Events\\E1_broadcast", function(message) {
//
//				// Вызвать функцию-обработчик входящих через этот канал сообщений
//				self.f.s2.log_websocket_handler(message);
//
//			});


	});


	//------------------------------------------------------------------//
	// 			        		 	                                              //
	// 			 s0. Библиотека данных, доступных всем прочим моделям  			//
	// 			         			                                              //
	//------------------------------------------------------------------//

	//------------------------------------------------//
	// s0.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s0 = {};

	//---------------------------------------------------------------------//
	// s0.2. Модель "механизма отложенного сохранения для текстовых полей" //
	//---------------------------------------------------------------------//
	self.m.s0.txt_delay_save = {};

		//-------------------------//
		// s0.2.1. Свойства модели //
		//-------------------------//

			// 1] Через сколько мс после послед.редакт. выполнять сохранение
			//--------------------------------------------------------------------//
			self.m.s0.txt_delay_save.gap = 2000;

			// 2] Свойство для сохранения timestamp послед.редактирования //
			//-----------------------------------------------------------------//
			self.m.s0.txt_delay_save.lastupdate = ko.observable(Date.now());

			// 3] Свойство для сохранения id последнего установленного таймера //
			//----------------------------------------------------------------------//
			self.m.s0.txt_delay_save.settimeoutid = ko.observable();

			// 4] Есть ли не сохранённые данные (для предотвращения закрытия документа) //
			//-------------------------------------------------------------------------------//
			self.m.s0.txt_delay_save.is_unsaved_data = ko.observable();

		//--------------------------------//
		// s0.2.2. Назначение обработчика //
		//--------------------------------//
		// - он будет запрашивать confirm, если есть не сохранённые данные
		ko.computed(function(){

			// Если это не первый запуск, завершить
			if(!ko.computedContext.isInitial()) return;

			// Назначить событию beforeunload функцию обработчик
			// - Она должна проверять свойство self.m.s0.save.is_unsaved_data()
			// - Если оно равно 1, то с помощью confirm уведомлять пользователя.
			//   о возможной потере данных при выходе.
			addEvent(window, 'beforeunload', function(event, params){

				if(self.m.s0.txt_delay_save.is_unsaved_data() == 1) {
					var message = "В документе есть не сохранённые данные, его закрытие приведёт к их потере. Вы уверены, что хотите закрыть документ?";
					event.returnValue = message;
				}

			}, {});

		});

	//----------------------------------------------//
	// s0.3. Счётчик ожидающих ответа ajax-запросов //
	//----------------------------------------------//
	self.m.s0.ajax_counter = ko.observable(0);

	//--------------------------------------------------------//
	// s0.4. Таймеры для функций, осуществляющих ajax-запросы //
	//--------------------------------------------------------//
	// - С их помощью можно игнорировать устаревшие ajax-ответы.
	self.m.s0.ajax_timers = {};

	//---------------------------------------//
	// s0.5. Виден ли щит "идёт ajax-запрос" //
	//---------------------------------------//
	self.m.s0.is_loadshield_on = ko.observable(0);
	ko.computed(function(){

		if(self.m.s0.ajax_counter() > 0)
			self.m.s0.is_loadshield_on(1);
		else
			self.m.s0.is_loadshield_on(0);

	});

	//---------------------------------//
	// s0.6. Аутентификационная модель //
	//---------------------------------//
	self.m.s0.auth = {};

		// 1] Аутентифицирован ли пользователь, как анонимный (гость) //
		//------------------------------------------------------------//
		self.m.s0.auth.is_anon = ko.observable(-1);

		// 2] Данные об аутентифицированном пользователе //
		//-----------------------------------------------//
		self.m.s0.auth.user = ko.observable({});

		// 3] Данные об аутентификационной записи пользователя //
		//-----------------------------------------------------//
		self.m.s0.auth.auth = ko.observable({});

		// 4] Вошёл ли пользователь в аккаунт //
		//------------------------------------//
		self.m.s0.is_logged_in = ko.observable(false);

		// n] Приём и обработка аутентификационных данных при 1-й загрузке //
		//-----------------------------------------------------------------//
		ko.computed(function(){

			// n.1] Если это не первый запуск, завершить
			if(!ko.computedContext.isInitial()) return;

			// n.2] Если сервер не прислал данные, завершить
			if(!server || !server.data || !server.data.auth) return;

			// n.3] Распаковать данные, и проверить
			var auth = JSON.parse(server.data.auth);
			if((!auth.is_anon && auth.is_anon != 0) || !auth.user || !auth.auth) return;

			// n.4] Наполнить m.s0.auth.is_anon
			self.m.s0.auth.is_anon(auth.is_anon);

			// n.5] Наполнить m.s0.auth.user
			for(var key in auth.user) {

				// 1] Если свойство не своё, пропускаем
				if(!auth.user.hasOwnProperty(key)) continue;

				// 2] Добавим свойство key в m.s0.auth.user
				self.m.s0.auth.user()[key] = ko.observable(auth.user[key]);

			}

			// n.6] Наполнить m.s0.auth.auth
			for(var key in auth.auth) {

				// 1] Если свойство не своё, пропускаем
				if(!auth.auth.hasOwnProperty(key)) continue;

				// 2] Добавим свойство key в m.s0.auth.auth
				self.m.s0.auth.auth()[key] = ko.observable(auth.auth[key]);

			}

			// n.7] Наполнить is_logged_in
			(function(){

				// Если пользователь "Not authenticated", записать false
				if(!self.m.s0.auth.user() || !self.m.s0.auth.user().id || !self.m.s0.auth.user().id())
					self.m.s0.is_logged_in(false);

				// Если этот анонимный пользователь, записать false
				else if(self.m.s0.auth.is_anon() != 0)
					self.m.s0.is_logged_in(false);

				// В противном случае, записать true
				else
					self.m.s0.is_logged_in(true);

			})();

		});


	//---------------------------------------------------//
	// 			        		 	                               //
	// 			 s1. Связанная с аутентификацией модель  		 //
	// 			         			                               //
	//---------------------------------------------------//

	//------------------------------------------------//
	// s1.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s1 = {};

	//---------------------------------------------//
	// s1.2. ФИО аутентифицированного пользователя //
	//---------------------------------------------//
	self.m.s1.fio = ko.observable('');

	//------------------------------------------------//
	// s1.3. Аватар аутентифицированного пользователя //
	//------------------------------------------------//
	self.m.s1.avatar = ko.observable('');

	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s1.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s1.indexes = {};

		//----------------------------------------------------------//
		// s1.n.2. Рассчитать ФИО аутентифицированного пользователя //
		//----------------------------------------------------------//
		(function(){

			// 1] Подготовить строку для результатов
			var result = "";

			// 2] Если данных нет, записать "Not authenticated" и завершить
			if(!self.m.s0.auth.user() || !self.m.s0.auth.user().id || !self.m.s0.auth.user().id()) {
				self.m.s1.fio("Not authenticated");
				return;
			}

			// 3] Извлечть имя, фамилию и отчество аутентифицированного пользователя
			$name = self.m.s0.auth.user().name();
			$surname = self.m.s0.auth.user().surname();
			$patronymic = self.m.s0.auth.user().patronymic();

			// 4] Сформировать результат
			if($surname) result = result + $surname;
			if($name) {
				if(result) result = result + ' ' + $name;
				else result = result + $name;
			}
			if($patronymic) {
				if(result) result = result + ' ' + $patronymic;
				else result = result + $patronymic;
			}

			// 5] Если result пуст, использовать nickname
			if(!result) result = self.m.s0.auth.user().nickname();

			// 6] Если result снова пуст, использовать [nameless]
			if(!result) result = '[nameless]';

			// 7] Записать результат в m.s1.fio
			self.m.s1.fio(result);

		})();

		//-------------------------------------------------------------//
		// s1.n.3. Рассчитать аватар аутентифицированного пользователя //
		//-------------------------------------------------------------//
		(function(){

			// 1] Если никаких данных нет, записать placehold.it
			if(!self.m.s0.auth.user() || ( (!self.m.s0.auth.user().avatar || !self.m.s0.auth.user().avatar()) && (!self.m.s0.auth.user().avatar_steam || !self.m.s0.auth.user().avatar_steam()) ) )
				self.m.s1.avatar("http://placehold.it/100x100/ffffff?text=avatar");

			// 2] Если есть пользовательский аватар, взять его
			else if(self.m.s0.auth.user().avatar && self.m.s0.auth.user().avatar())
				self.m.s1.avatar(self.m.s0.auth.user().avatar());

			// 3] Если есть аватар Steam, взять его
			else if(self.m.s0.auth.user().avatar_steam && self.m.s0.auth.user().avatar_steam())
				self.m.s1.avatar(self.m.s0.auth.user().avatar_steam());

		})();

	});


	//-------------------------------------------------//
	// 			        		 	                             //
	// 			 s2. Модель левого навигационного меню 		 //
	// 			         			                             //
	//-------------------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//---------------------------------------------------//
	// s2.2. Наблюдаемый массив поддокументов приложения //
	//---------------------------------------------------//
	self.m.s2.subdocs = ko.observableArray([]);

	//--------------------------------------------------//
	// s2.3. Ссылка на выбранный поддокумент приложения //
	//--------------------------------------------------//
	self.m.s2.selected_subdoc = ko.observable((function(){
		if(self.m.s2.subdocs().length) return self.m.s2.subdocs()[0]();
		else return {id: ko.observable(1)};
	})());

	//----------------------------//
	// s2.4. Корневой URL запроса //
	//----------------------------//
	self.m.s2.root_url = ko.observable((function(){

		// 1] Получить корневой URL
		var root_url = layout_data.data.root_url;

		// 2] Найти и заменить 2-е и прочие вхождени порта в root_url
		var nth = 0;
		root_url = root_url.replace(/:[0-9]+/gi, function (match, i, original) {
			nth++;
			return (nth >= 2) ? "" : match;
		});

		// 3] Если у root_url порт отсутствует, добавить ему layout_data.data.port
		$matches = root_url.match(/:[0-9]+/gi);
		if(!$matches || $matches.length === 0)
			root_url = root_url + ':' + layout_data.data.port;

		// n] Вернуть root_url
		return root_url;

	})());

	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s2.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s2.indexes = {};

		//------------------------------//
		// s2.n.2. Индекс поддокументов //
		//------------------------------//
		// - По ID поддокумента можно получить ссылку на соотв. объект в self.m.s2.subdocs
		self.m.s2.indexes.subdocs = (function(){

			// 1. Подготовить объект для результатов
			var results = {};

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.subdocs().length; i++) {
				results[self.m.s2.subdocs()[i]().id()] = self.m.s2.subdocs()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//--------------------------------------//
		// s2.n.3. Именной индекс поддокументов //
		//--------------------------------------//
		// - По name поддокумента можно получить ссылку на соотв. объект в self.m.s2.subdocs
		self.m.s2.indexes.subdocs_by_name = (function(){

			// 1. Подготовить объект для результатов
			var results = {};

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.subdocs().length; i++) {
				results[self.m.s2.subdocs()[i]().name().toLowerCase()] = self.m.s2.subdocs()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

	});


	//------------------------------------------------------------//
	// 			        		 	                                        //
	// 			 sN. Данные, которым доступны все прочие данные  			//
	// 			         			                                        //
	//------------------------------------------------------------//

	//------------------------------------------------//
	// sN.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.sN = {};

	//-------------------------------------------------------//
	// sN.2. 	 //
	//-------------------------------------------------------//



	//--------------------------------------//
	// sN.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// sN.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.sN.indexes = {};


	});

	//----------------------------------------//
	// 			        		 	                    //
	// 			 X. Подготовка к завершению  			//
	// 			         			                    //
	//----------------------------------------//

	//----------------------------------------//
	// X1. Сервис провайдер клиентской модели //
	//----------------------------------------//
	// - Код здесь выполняется лишь 1 раз, при открытии документа в браузере.
	// - Отличное место, скажем, для назначения обработчиков событий.
	ko.computed(function(){

		//-------------------------------------------------------------//
		// X1.1. Выполнять код ниже лишь 1 раз, при загрузке документа //
		//-------------------------------------------------------------//
		if(!ko.computedContext.isInitial()) return;

		//----------------------------//
		// X1.1. Обновить модель меню //
		//----------------------------//
		(function(){

			self.f.s0.update_menu(layout_data.data.menu);

		})();

		//----------------------------0=-------------------------------------//
		// X1.2. Применить animsition (анимация загрузки/выгрузки документа) //
		//-------------------------------------------------------------------//
		(function(){

			$(document).ready(function() {
				$(".animsition").animsition({
					inClass: 'fade-in-up-sm',
					outClass: 'fade-out-down-sm',
					inDuration: 1500,
					outDuration: 800,
					linkElement: '.animsition-link',
					// e.g. linkElement: 'a:not([target="_blank"]):not([href^="#"])'
					loading: true,
					loadingParentElement: 'body', //animsition wrapper element
					loadingClass: 'animsition-loading',
					loadingInner: '', // e.g '<img src="loading.svg" />'
					timeout: false,
					timeoutCountdown: 5000,
					onLoadEvent: true,
					browser: [ 'animation-duration', '-webkit-animation-duration'],
					// "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
					// The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
					overlay : false,
					overlayClass : 'animsition-overlay-slide',
					overlayParentElement : 'body',
					transition: function(url){ window.location.href = url; }
				});
			});

		})();



	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









