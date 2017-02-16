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
 *	  s0.6. Аутентификационная модель
 *    s0.7. Текущая ширина браузера клиента
 *    s0.8. Текущая и предыдущая величины прокрутки браузера
 *    s0.9. Количество залогиненных Steam-пользователей в системе
 *    s0.10. Серверное время
 *    s0.11. Текущий хост (включая http/https)
 *    s0.12. Текущий хост (включая http/https, порт и базовый URI)
 *
 *  s1. Модель управления поддокументами приложения
 *
 *		s1.1. Объект-контейнер для всех свойств модели
 *    s1.2. Наблюдаемый массив поддокументов приложения
 *    s1.3. Выбранный поддокумент приложения
 * 		s1.n. Индексы и вычисляемые значения
 *
 *  s2. Модель механики левого сайдбара (с главным меню)
 *
 *    s2.1. Объект-контейнер для всех свойств модели
 *    s2.2. Состояние левого сайдбара (скрыт/раскрыт)
 *    s2.3. Спрятать ли кнопку раскрытия л.меню (если ширина окна < 1280px)
 *    s2.4. Значение css-свойства top для левого сайдбара (прокрутка)
 *    s2.n. Индексы и вычисляемые значения
 *
 *  s3. Модель механики правого сайдбара (с чатом)
 *
 *    s3.1. Объект-контейнер для всех свойств модели
 *    s3.2. Состояние правого сайдбара (скрыт/раскрыт)
 *    s3.3. Спрятать ли правый сайдбар и кнопку раскрытия (если ширина окна < 1280px)
 *    s3.n. Индексы и вычисляемые значения
 *
 *  s4. Модель по управлению звуком
 *
 *    s4.1. Объект-контейнер для всех свойств модели
 *    s4.2. Глобальный выключатель звука
 *    s4.n. Индексы и вычисляемые значения
 *
 *  s5. Модель чата
 *
 *  	s5.1. Объект-контейнер для всех свойств модели
 *  	s5.2. Наблюдаемый массив сообщений в чате
 *  	s5.3. Поле для ввода сообщений
 *  	s5.4. Максимальная длина сообщений
 *  	s5.5. Разрешено ли публиковать сообщения гостям
 *  	s5.6. Максимальное кол-во сообщений в чате
 *  	s5.7. Массив с ID модераторов чата
 *  	s5.8. Количество залогиненных Steam-пользователей в системе
 *  	s5.n. Индексы и вычисляемые значения
 *
 *  s6. Данные, связанные с классической игрой
 *
 * 		s6.1. Объект-контейнер для всех свойств модели
 *    s6.2. Текущий раунда выбранной комнаты
 *    s6.3. Текущий банк выбранной комнаты
 *    s6.n. Индексы и вычисляемые значения
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

				// 1] Убрать из websocket_server лишние порты
				// - Они появляются при работе через browser-sync
				layout_data.data.websocket_server = layout_data.data.websocket_server.replace(/:\d+.*$/i, "");
				layout_data.data.websocket_server = layout_data.data.websocket_server + ':6001';

				// 2] Подключить ws1
				self.websocket.ws1 = io(layout_data.data.websocket_server);

		//--------------------------------------------------------------//
		// А4.3. Назначение обработчиков сообщений с websocket-серверов //
		//--------------------------------------------------------------//

			// A4.3.1. Обработка сообщений об успешной аутентификации через Steam //
			//--------------------------------------------------------------------//
			self.websocket.ws1.on(layout_data.data.websockets_channel, function(message) {

				// 1] Сообщить об успешной аутентификации через Steam
				toastr.info("Перезагружаю документ...", "Успешный вход через Steam", {
					timeOut: 					"9999999999999",
					extendedTimeOut: 	"9999999999999"
				});

				// 2] Перезагрузить документ через 3 секунды
				setTimeout(function(){
					location.reload();
				}, 3000);

			});

			// A4.3.2. Обновление актуального кол-ва подключений //
			//---------------------------------------------------//
			self.websocket.ws1.on('active_connections_number', function(data) {

				self.m.s0.logged_in_steam_users(data);

			});

			// A4.3.3. Обработка новых сообщений и обновлений в чат //
			//------------------------------------------------------//
			self.websocket.ws1.on('m10:chat_main', function(data) {

				// 1] Если data.data.data.message не пуст
				// - Добавить новое сообщение в конец m.s5.messages
				if(data.data.data.message)
					self.f.s5.add_incoming_msg(data.data.data.message);

				// 2] Если data.data.data.message2hide не пуст
				// - Удалить указанное сообщение
				if(data.data.data.message2hide) {

				}

				// 3] Прокрутить чат до конца вниз

					// 3.1] Получить контейнер чата
					var container = document.getElementsByClassName('chat-messages')[0];

					// 3.2] Получить вертикальный размер прокрутки контейнера
					var scrollHeight = container.scrollHeight;

					// 3.3] Прокрутить container в конец
					container.scrollTop = scrollHeight;
					Ps.update(container);

			});

			// A4.3.4. Ежесекундное обновление серверного времени //
			//----------------------------------------------------//
			self.websocket.ws1.on('m9:servertime', function(data) {

				// 1] Обновить серверное время в виде человеко-понятной строки
				self.m.s0.servertime.human(data.data.data.secs);

				// 2] Обновить серверное время в виде timestamp в секундах
				self.m.s0.servertime.timestamp_s(Math.round(moment.utc(data.data.data.secs).unix()));

			});


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
			if(!layout_data || !layout_data.data || !layout_data.data.auth) return;

			// n.3] Распаковать данные, и проверить
			var auth = JSON.parse(layout_data.data.auth);
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

	//---------------------------------------//
	// s0.7. Текущая ширина браузера клиента //
	//---------------------------------------//
	self.m.s0.cur_browser_width = ko.observable(getBrowserWindowMetrics().width);

	//--------------------------------------------------------//
	// s0.8. Текущая и предыдущая величины прокрутки браузера //
	//--------------------------------------------------------//
	self.m.s0.cur_browser_scroll = ko.observable(window.pageYOffset || document.documentElement.scrollTop);
	self.m.s0.prev_browser_scroll = ko.observable(0);

	//-------------------------------------------------------------//
	// s0.9. Количество залогиненных Steam-пользователей в системе //
	//-------------------------------------------------------------//
	self.m.s0.logged_in_steam_users = ko.observable(layout_data.data.logged_in_steam_users);

	//------------------------//
	// s0.10. Серверное время //
	//------------------------//
	self.m.s0.servertime = {};

		// 1] Серверное время в виде человеко-понятной строки //
		//----------------------------------------------------//
		// - Например: "2016-10-19 15:38:21"
		self.m.s0.servertime.human = ko.observable("");

		// 2] Серверное время в виде timestamp в секундах //
		//------------------------------------------------//
		// - Например: "1476891769"
		self.m.s0.servertime.timestamp_s = ko.observable("");

	//-------------------------------------------------//
	// s0.11. Текущий хост (включая http/https и порт) //
	//-------------------------------------------------//
	self.m.s0.full_host = ko.observable((function(){

		return layout_data.data.request.secure +
					 layout_data.data.request.host.replace(/:\d+.*$/i, "") +
				   ':' + layout_data.data.request.port;

	})());

	//--------------------------------------------------------------//
	// s0.12. Текущий хост (включая http/https, порт и базовый URI) //
	//--------------------------------------------------------------//
	self.m.s0.full_host_andbaseuri = ko.observable((function(){

		return layout_data.data.request.secure +
					 layout_data.data.request.host.replace(/:\d+.*$/i, "") +
				   ':' + layout_data.data.request.port +
				   layout_data.data.request.baseuri;

	})());



	//-----------------------------------------------------------//
	// 			        		 	                                       //
	// 			 s1. Модель управления поддокументами приложения		 //
	// 			         			                                       //
	//-----------------------------------------------------------//

	//------------------------------------------------//
	// s1.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s1 = {};

	//---------------------------------------------------//
	// s1.2. Наблюдаемый массив поддокументов приложения //
	//---------------------------------------------------//
	self.m.s1.subdocs = ko.mapping.fromJS([
		{
			uri:        '/',
			icon_mdi:   'mdi-crown',
			icon_url:   '',
			title:      'Classic game',
			bg_color:   '#284351',
			brd_color:  '#2f5463',
			visible:    true
		},
		{
			uri:        '/double',
			icon_mdi:   'mdi-adjust',
			icon_url:   '',
			title:      'Double game',
			bg_color:   '#284351',
			brd_color:  '#2f5463',
			visible:    true
		},
		{
			uri:        '/coinflip',
			icon_mdi:   'mdi-checkbox-multiple-blank-circle-outline',
			icon_url:   '',
			title:      'Coinflip',
			bg_color:   '#284351',
			brd_color:  '#2f5463',
			visible:    true
		},
		{
			uri:        '/shop',
			icon_mdi:   'mdi-shopping',
			icon_url:   '',
			title:      'Магазин',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/profile',
			icon_mdi:   'mdi-account',
			icon_url:   '',
			title:      'Профиль',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/ref',
			icon_mdi:   'mdi-account-multiple',
			icon_url:   '',
			title:      'Партнёрка',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/top',
			icon_mdi:   'mdi-star-outline',
			icon_url:   '',
			title:      'ТОП игроков',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/faq',
			icon_mdi:   'mdi-information-outline',
			icon_url:   '',
			title:      'F.A.Q.',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/support',
			icon_mdi:   'mdi-help-circle-outline',
			icon_url:   '',
			title:      'Тех.поддержка',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		},
		{
			uri:        '/freecoins',
			icon_mdi:   'mdi-coin',
			icon_url:   '',
			title:      'Free coins',
			bg_color:   '#223340',
			brd_color:  'transparent',
			visible:    true
		}
	]);

	//----------------------------------------//
	// s1.3. Выбранный поддокумент приложения //
	//----------------------------------------//
	self.m.s1.selected_subdoc = ko.observable(self.m.s1.subdocs()[0]);


	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s1.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s1.indexes = {};

		//-------------------------------------//
		// s1.n.2. Индекс поддокументов по URI //
		//-------------------------------------//
		// - По указанному URI можно получить поддокумент.
		self.m.s1.indexes.subdocs = (function(){

			// 1. Подготовить объект для результатов
			var results = {};

			// 2. Заполнить results
			for(var i=0; i<self.m.s1.subdocs().length; i++) {
				results[self.m.s1.subdocs()[i].uri()] = self.m.s1.subdocs()[i];
			}

			// 3. Вернуть results
			return results;

		}());



	});


	//---------------------------------------------------------------//
	// 			        		 	                                           //
	// 			 s2. Модель механики левого сайдбара (с главным меню)		 //
	// 			         			                                           //
	//---------------------------------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//-------------------------------------------------//
	// s2.2. Состояние левого сайдбара (скрыт/раскрыт) //
	//-------------------------------------------------//
	self.m.s2.expanded = ko.observable(true);

	//-----------------------------------------------------------------------//
	// s2.3. Спрятать ли кнопку раскрытия л.меню (если ширина окна < 1280px) //
	//-----------------------------------------------------------------------//
	self.m.s2.hidden = ko.observable(false);

	//-----------------------------------------------------------------//
	// s2.4. Значение css-свойства top для левого сайдбара (прокрутка) //
	//-----------------------------------------------------------------//
	self.m.s2.topStart = 50;
	self.m.s2.top = ko.observable(self.m.s2.topStart);


	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s2.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s2.indexes = {};

		//----------------------------------------------------//
		// s2.n.2. Вычислить текущее значение для m.s2.hidden //
		//----------------------------------------------------//
		(function(){
			self.m.s2.hidden((function(){

				if(self.m.s0.cur_browser_width() < 1280) return true;
				return false;

			})());
		})();

		//-----------------------------------------------------------//
		// s2.n.3. Если ширина окна < 1280px, минимизировать л.меню  //
		//-----------------------------------------------------------//
		(function(){
			if(self.m.s2.hidden()) self.m.s2.expanded(false);
		})();


	});


	//---------------------------------------------------------//
	// 			        		 	                                     //
	// 			 s3. Модель механики правого сайдбара (с чатом)		 //
	// 			         			                                     //
	//---------------------------------------------------------//

	//------------------------------------------------//
	// s3.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s3 = {};

	//--------------------------------------------------//
	// s3.2. Состояние правого сайдбара (скрыт/раскрыт) //
	//--------------------------------------------------//
	self.m.s3.expanded = ko.observable(false);

	//---------------------------------------------------------------------------------//
	// s3.3. Спрятать ли правый сайдбар и кнопку раскрытия (если ширина окна < 1280px) //
	//---------------------------------------------------------------------------------//
	self.m.s3.hidden = ko.observable(false);

	//--------------------------------------//
	// s3.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s3.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s3.indexes = {};

		//----------------------------------------------------//
		// s3.n.2. Вычислить текущее значение для m.s3.hidden //
		//----------------------------------------------------//
		(function(){
			self.m.s3.hidden((function(){

				if(self.m.s0.cur_browser_width() < 1280) return true;
				return false;

			})());
		})();

		//--------------------------------------------------------//
		// s3.n.3. При ширине экрана от 1280 до 1456 включительно //
		//--------------------------------------------------------//
		// - Скрывать чат, если главное меню раскрыто.
		(function(){

			if(self.m.s0.cur_browser_width() >= 1280 && self.m.s0.cur_browser_width() <= 1456) {
				if(self.m.s2.expanded()) self.m.s3.expanded(false);
			}

		})();


	});


	//--------------------------------------------//
	// 			        		 	                       	//
	// 			 s4. Модель по управлению звуком		 	//
	// 			         			                       	//
	//--------------------------------------------//

	//------------------------------------------------//
	// s4.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s4 = {};

	//------------------------------------//
	// s4.2. Глобальный выключатель звука //
	//------------------------------------//
	self.m.s4.is_global_volume_on = ko.observable(layout_data.data.m9_sound_global_ison);

	//--------------------------------------//
	// s4.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s4.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s4.indexes = {};



	});
	
	//------------------------------//
	// 			        		 	          //
	// 			 s5. Модель чата  			//
	// 			         			          //
	//------------------------------//

	//------------------------------------------------//
	// s5.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s5 = {};

	//-------------------------------------------//
	// s5.2. Наблюдаемый массив сообщений в чате //
	//-------------------------------------------//
	self.m.s5.messages = ko.observableArray([]);

	//--------------------------------//
	// s5.3. Поле для ввода сообщений //
	//--------------------------------//
	self.m.s5.new_message = ko.observable('');

	//------------------------------------//
	// s5.4. Максимальная длина сообщений //
	//------------------------------------//
	self.m.s5.max_msg_length = ko.observable(layout_data.data.chat_main.max_msg_length);

	//-------------------------------------------------//
	// s5.5. Разрешено ли публиковать сообщения гостям //
	//-------------------------------------------------//
	self.m.s5.allow_guests = ko.observable(layout_data.data.chat_main.allow_guests);

	//--------------------------------------------//
	// s5.6. Максимальное кол-во сообщений в чате //
	//--------------------------------------------//
	self.m.s5.max_messages = ko.observable(layout_data.data.chat_main.max_messages);

	//------------------------------------//
	// s5.7. Массив с ID модераторов чата //
	//------------------------------------//
	self.m.s5.moderators = ko.observableArray(layout_data.data.chat_main.moderator_ids);


	//--------------------------------------//
	// s5.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s5.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s5.indexes = {};

		//-----------------------//
		// s5.n.2. ... //
		//-----------------------//




	});	

	//--------------------------------------------------------//
	// 			        		 	                                    //
	// 			 s6. Данные, связанные с классической игрой  			//
	// 			         			                                    //
	//--------------------------------------------------------//

	//------------------------------------------------//
	// s6.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s6 = {};

	//-----------------------------------------------//
	// s6.2. Текущий статус раунда выбранной комнаты //
	//-----------------------------------------------//
	// - model.m.s1.game.choosen_status()
	self.m.s6.status = {};

		// 1] Имя текущего статуса
		self.m.s6.status.name = ko.observable("");

		// 2] Надпись, которую показывать ("Розыгрыш" или "На кону")
		self.m.s6.status.title = ko.observable("");

	//--------------------------------------//
	// s6.3. Текущий банк выбранной комнаты //
	//--------------------------------------//
	// - model.m.s1.bank.sum
	self.m.s6.curjackpot = ko.observable(0);

	//-------------------------------------------------------------------//
	// s6.4. Модель всплывающих уведомлений в пункте меню "Classic game" //
	//-------------------------------------------------------------------//
	self.m.s6.notify = {};

		// 1] Состояние всплывающего уведомления
		self.m.s6.notify.is_hidden = ko.observable(1);

		// 2] Длительность transition всплывающих уведомлений в пункте меню "Classic game"
		self.m.s6.notify.traisitionDuration = ko.observable('.5s');

		// 3] Текст уведомления
		self.m.s6.notify.text = ko.observable("");

	//--------------------------------------//
	// s6.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s6.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s6.indexes = {};

		//------------------------------------------------//
		// s6.n.2. Посчитать значение для s6.status.title //
		//------------------------------------------------//
		(function(){

			// Если статус: Lottery, Winner, Finished
			if(['Lottery', 'Winner', 'Finished'].indexOf(self.m.s6.status.name()) != -1)
				self.m.s6.status.title("Розыгрыш:");

			// В противном случае
			else
				self.m.s6.status.title("На кону:");

		})();




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

		//-----------------------------------------------------//
		// X1.2. Настроить toastr (не блокирующие уведомления) //
		//-----------------------------------------------------//
		// - При клике в любом месте, кроме как на самой панели.
		(function(){

			// 1] Настроить toastr
			toastr.options = {
				"closeButton": true,
				"debug": false,
				"newestOnTop": false,
				"progressBar": false,
				"positionClass": "toast-bottom-right",
				"preventDuplicates": false,
				"onclick": null,
				"showDuration": "777",
				"hideDuration": "1777",
				"timeOut": "7777",
				"extendedTimeOut": "7777",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			};

			// 2] Не закрывать тосты при клике
			toastr.options.tapToDismiss = false;

		})();

		//-------------------------------------------------------------------//
		// X1.3. Назначить обработчик события изменения ширины окна браузера //
		//-------------------------------------------------------------------//
		(function(){

			// 1. Назначить обработчик
			addEvent(window, 'resize', function(event, params){

				// 1] Записать новое значение для cur_browser_width
				self.m.s0.cur_browser_width(getBrowserWindowMetrics().width);

				// 2] Обновить прокрутку чата
				// - Через секунду.
				setTimeout(function(){
					Ps.update(document.getElementsByClassName('chat-messages')[0]);
				}, 1000);

				// 3] Перерасчитать скролл левого меню
				self.f.s2.scroll(event, params);

			}, {self: self});

		})();

		//-----------------------------------------------------------------------//
		// X1.4. На основе параметров parameters с сервера открыть соотв.докуент //
		//-----------------------------------------------------------------------//
		// - И заодно добавить стартовое состояние.
		// - А также назначить функцию-обработчик, срабатывающую при смене состояния.
		(function(){

			// 1] На основе параметров page и subdoc с сервера открыть соотв.докуент, добавить стартовое состояние.
			self.f.s1.choose_subdoc({
				parameters: layout_data.data.parameters,
				uri:        '',
				first:      true
			}, '', '');

			// 2] Назначить функцию-обработчик, срабатывающую при смене состояния
			History.Adapter.bind(window, 'statechange', function() {

				// 1] Получить текущее новое состояние
				var state = History.getState();
				var state_uri = state.data.state;

				// 2] Если состояния приложения и истории расходятся
				// - То привести состояние приложения в соответствие
				if(self.m.s1.selected_subdoc().uri() != state_uri) {

					// 2.1] Получить объект поддокумента
					var subdoc = self.m.s1.indexes.subdocs[state_uri];

					// 2.2] Если subdoc не найден, вернуть ошибку и завершить
					if(!subdoc) {
						console.log('Ошибка! Наблюдаемый массив с поддокументами пуст.');
						return;
					}

					// 2.3] Сменить состояние
					self.f.s1.choose_subdoc({
						uri: state_uri
					});

				}

			});

		})();

		//----------------------------------------------------//
		// X1.5. Обновить текущую величину прокрутки браузера //
		//----------------------------------------------------//
		(function(){
			addEvent(window, 'scroll', function(event, params) {

				// 1] Получить текущее значение прокрутки
				var scrolled = window.pageYOffset || document.documentElement.scrollTop;

				// 2] Записать его в cur_browser_scroll
				self.m.s0.cur_browser_scroll(scrolled);

			}, {self: self});
		})();

		//---------------------------------------------------------------//
		// X1.6. Организовать скролл левого меню при прокрутке документа //
		//---------------------------------------------------------------//
		(function(){
			addEvent(window, 'scroll', function(event, params) {

				self.f.s2.scroll(event, params);

			}, {self: self});
		})();

		//--------------------------------------------------------------------//
		// X1.7. Отключить экран загрузки документа после его полной загрузки //
		//--------------------------------------------------------------------//
		(function(){

			$(document).ready(function(){ setTimeout(function(){

				// 1] Получить DOM-элемент экрана загрузки
				var loading_screen = $('.start-loading-screen');

				// 2] Сделать overflow: auto для body
				$(document.body).css('overflow', 'auto');

				// 3] За .5 секунды скрыть экран с помощью opacity
				loading_screen.addClass('hide-start-loading-screen');

				// 4] После скрытия экрана, удалить его из DOM
				loading_screen.remove();

			}, 500);});

		})();

		//-----------------------------------------------------------//
		// X1.8. Обновить m.s5.messages начальными данными с сервера //
		//-----------------------------------------------------------//
		(function(){

			self.f.s0.update_messages(layout_data.data.messages.data);

		})();

		//--------------------------------------------------------//
		// X1.9. Инициализировать perfect scrollbar для чата меню //
		//--------------------------------------------------------//
		(function(){ setTimeout(function(){

			Ps.initialize(document.getElementsByClassName('chat-messages')[0], {
				wheelSpeed:.4,
				wheelPropagation: false,
				minScrollbarLength: 20
			});

		}, 10); })();


	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









