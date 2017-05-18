/*//========================================////
////																		    ////
////   m.js	- клиентская модель документа		////
////																				////
////========================================////
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
 *		А4. Подключение к websocket-серверам, назначение обработчиков для входящих сообщений
 *
 *	s0. Библиотека данных, доступных всем прочим моделям
 *
 * 		s0.1. Объект-контейнер для всех свойств модели
 * 	  s0.2. Модель "механизма отложенного сохранения для текстовых полей"
 *		s0.3. Счётчик ожидающих ответа ajax-запросов
 *		s0.4. Таймеры для функций, осуществляющих ajax-запросы
 *		s0.5. Виден ли щит "идёт ajax-запрос"
 *	  s0.6. Аутентификационная модель
 *
 *  s1. Модель управления поддокументами приложения
 *
 *		s1.1. Объект-контейнер для всех свойств модели
 *    s1.2. Группы поддокументов приложения
 *    s1.3. Наблюдаемый массив поддокументов приложения
 *    s1.n. Индексы и вычисляемые значения
 *
 *  s2. Модель игровых комнат
 *
 *   	s2.1. Объект-контейнер для всех свойств модели
 *   	s2.2. Наблюдаемый массив с элементами-комнатами
 *    s2.3. Модель сортировки списка ботов
 *    s2.4. Модель фильтров списка комнат
 *    s2.5. Комнат всего
 *    s2.6. Модель создания новой комнаты
 *    s2.7. Модель редактирования выбранной комнаты
 *   	s2.n. Индексы и вычисляемые значения
 *
 *  s3. Модель ботов
 *
 *    s3.1. Объект-контейнер для всех свойств модели
 *    s3.2. Наблюдаемый массив с элементами-ботами
 *    s3.3. Ботов всего
 *    s3.4. Массив ID прикреплённых к выбранной комнате ботов
 *    s3.5. Массив ID НЕ прикреплённых к выбранной комнате ботов
 *    s3.n. Индексы и вычисляемые значения
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
var ModelProto = { constructor: function(ModelFunctions) {

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
	self.f = Object.create(ModelFunctions).constructor(self);

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
				server.data.websocket_server = server.data.websocket_server.replace(/:\d+.*$/i, "");
				server.data.websocket_server = server.data.websocket_server + ':6001';

				// 2] Подключить ws1
				self.websocket.ws1 = io(server.data.websocket_server);


		//--------------------------------------------------------------//
		// А4.3. Назначение обработчиков сообщений с websocket-серверов //
		//--------------------------------------------------------------//

			// A4.3.1.  //
			//--------------------------------------------------------------------//
			self.websocket.ws1.on('some_channel', function(message) {

				// 1]
				//console.log(message);

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


	//-----------------------------------------------------------//
	// 			        		 	                                       //
	// 			 s1. Модель управления поддокументами приложения 		 //
	// 			         			                                       //
	//-----------------------------------------------------------//

	//------------------------------------------------//
	// s1.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s1 = {};

	//---------------------------------------//
	// s1.2. Группы поддокументов приложения //
	//---------------------------------------//

		// 1] Наблюдаемый массив групп поддокументов приложения //
		//------------------------------------------------------//
		self.m.s1.groups = ko.observableArray([

			ko.observable({
				id: ko.observable('1'),
				name: ko.observable('Rooms')
			}),
			ko.observable({
				id: ko.observable('2'),
				name: ko.observable('Room')
			})

		]);

		// 2] Выбранная группа поддокументов приложения //
		//----------------------------------------------//
		self.m.s1.selected_group = ko.observable(self.m.s1.groups()[0]());

	//---------------------------------------------------//
	// s1.3. Наблюдаемый массив поддокументов приложения //
	//---------------------------------------------------//

		// 1] Наблюдаемый массив поддокументов приложения //
		//------------------------------------------------//
		self.m.s1.subdocs = ko.observableArray([

			// Поддокументы группы №1
			ko.observable({
				id: ko.observable('1'),
				name: ko.observable('Rooms'),
				query: ko.observable('?group=rooms'),
				group: ko.observable('1')
			}),

			// Поддокументы группы №2
			ko.observable({
				id: ko.observable('100'),
				name: ko.observable('Properties'),
				query: ko.observable('?group=room&subdoc=properties'),
				group: ko.observable('2')
			}),
			ko.observable({
				id: ko.observable('101'),
				name: ko.observable('Bots'),
				query: ko.observable('?group=room&subdoc=bots'),
				group: ko.observable('2')
			})

		]);

		// 2] Выбранный поддокумент приложения //
		//------------------------------------//
		self.m.s1.selected_subdoc = ko.observable(self.m.s1.subdocs()[0]());


	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s1.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s1.indexes = {};

		//---------------------------------------//
		// s1.n.2. Индексы групп и поддокументов //
		//---------------------------------------//

			// 1] Индекс групп поддокументов (по ID) //
			//---------------------------------------//
			// - По ID группы поддокумента можно получить ссылку на соотв. объект в self.m.s1.groups
			self.m.s1.indexes.groups = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.groups().length; i++) {
					results[self.m.s1.groups()[i]().id()] = self.m.s1.groups()[i]();
				}

				// 3. Вернуть results
				return results;

			}());

			// 2] Индекс групп поддокументов (по name) //
			//-----------------------------------------//
			// - По name группы поддокумента можно получить ссылку на соотв. объект в self.m.s1.groups
			self.m.s1.indexes.groups_by_name = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.groups().length; i++) {
					results[self.m.s1.groups()[i]().name().toLowerCase()] = self.m.s1.groups()[i]();
				}

				// 3. Вернуть results
				return results;

			}());

		//-------------------------------//
		// s1.n.3. Индексы поддокументов //
		//-------------------------------//

			// 1] Индекс поддокументов //
			//-------------------------//
			// - По ID поддокумента можно получить ссылку на соотв. объект в self.m.s1.subdocs
			self.m.s1.indexes.subdocs = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.subdocs().length; i++) {
					results[self.m.s1.subdocs()[i]().id()] = self.m.s1.subdocs()[i]();
				}

				// 3. Вернуть results
				return results;

			}());

			// 2] Индекс поддокументов //
			//-------------------------//
			// - По name поддокумента можно получить ссылку на соотв. объект в self.m.s1.subdocs
			self.m.s1.indexes.subdocs_by_name = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.subdocs().length; i++) {
					results[self.m.s1.subdocs()[i]().name().toLowerCase()] = self.m.s1.subdocs()[i]();
				}

				// 3. Вернуть results
				return results;

			}());


	});


	//-------------------------------------//
	// 			        		 	                 //
	// 			 s2. Модель игровых комнат 		 //
	// 			         			                 //
	//-------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//-------------------------------------------------//
	// s2.2. Наблюдаемый массив с элементами-комнатами //
	//-------------------------------------------------//
	self.m.s2.rooms = ko.observableArray([]);

	//--------------------------------------//
	// s2.3. Модель сортировки списка ботов //
	//--------------------------------------//
	self.m.s2.sortrooms = {};

		// 1] Опции //
		//----------//
		self.m.s2.sortrooms.options = ko.observableArray([
			ko.observable({
				id: ko.observable('options_sortrooms1'),
				name: ko.observable('options_sortrooms'),
				text: ko.observable("Room's ID"),
				value: ko.observable('1')
			})
		]);

		// 2] Выбранная опция //
		//--------------------//
		self.m.s2.sortrooms.choosen = ko.observable(self.m.s2.sortrooms.options()[0]());

	//-------------------------------------//
	// s2.4. Модель фильтров списка комнат //
	//-------------------------------------//
	self.m.s2.filterrooms = {};

		// 1] By mode //
		//------------//
		self.m.s2.filterrooms.mode = {};
		self.m.s2.filterrooms.mode.roll = ko.observable(true);
		self.m.s2.filterrooms.mode.availability = ko.observable(true);

		// 2] By status //
		//--------------//
		self.m.s2.filterrooms.status = {};
		self.m.s2.filterrooms.status.enabled = ko.observable(true);
		self.m.s2.filterrooms.status.disabled = ko.observable(true);

		// 3] By change //
		//--------------//
		self.m.s2.filterrooms.change = {};
		self.m.s2.filterrooms.change.enabled = ko.observable(true);
		self.m.s2.filterrooms.change.disabled = ko.observable(true);

		// 4] By onebotpayout //
		//--------------------//
		self.m.s2.filterrooms.onebotpayout = {};
		self.m.s2.filterrooms.onebotpayout.enabled = ko.observable(true);
		self.m.s2.filterrooms.onebotpayout.disabled = ko.observable(true);

		// n] Отфильтрованный массив комнат //
		//----------------------------------//
		self.m.s2.rooms_filtered = ko.computed(function(){

			return ko.utils.arrayFilter(self.m.s2.rooms(), function(item){

				// 1] Фильтрация по mode
//				if(item().bet_accepting_mode() == 'roll') {
//					if(self.m.s2.filterrooms.mode.roll() == false)
//						return false;
//				}
//				if(item().bet_accepting_mode() == 'availability') {
//					if(self.m.s2.filterrooms.mode.availability() == false)
//						return false;
//				}

				// 2] Фильтрация по status
				if(item().is_on() == 1) {
					if(self.m.s2.filterrooms.status.enabled() == false)
						return false;
				}
				if(item().is_on() == 0) {
					if(self.m.s2.filterrooms.status.disabled() == false)
						return false;
				}

				// 3] Фильтрация по change
				if(item().change() == 1) {
					if(self.m.s2.filterrooms.change.enabled() == false)
						return false;
				}
				if(item().change() == 0) {
					if(self.m.s2.filterrooms.change.disabled() == false)
						return false;
				}

				// 4] Фильтрация по onebotpayout
				if(item().one_bot_payout() == 1) {
					if(self.m.s2.filterrooms.onebotpayout.enabled() == false)
						return false;
				}
				if(item().one_bot_payout() == 0) {
					if(self.m.s2.filterrooms.onebotpayout.disabled() == false)
						return false;
				}

				// n] Вернуть результат
				return true;

			});

		});


	//--------------------//
	// s2.5. Комнат всего //
	//--------------------//
  self.m.s2.rooms_total = ko.observable(0);

	//-------------------------------------//
	// s2.6. Модель создания новой комнаты //
	//-------------------------------------//
	self.m.s2.newroom = {};

		// 1] Steamid //
		//------------//
		self.m.s2.newroom.name = ko.observable('');

	//-----------------------------------------------//
	// s2.7. Модель редактирования выбранной комнаты //
	//-----------------------------------------------//
	self.m.s2.edit = {};

		// 1] Поля модели //
		//----------------//
		self.m.s2.edit.id 													= ko.observable("");

		self.m.s2.edit.is_on 												= ko.observable("");
		//self.m.s2.edit.bet_accepting_mode  					= ko.observable("");
		self.m.s2.edit.name 												= ko.observable("");
		self.m.s2.edit.description 									= ko.observable("");
		self.m.s2.edit.room_round_duration_sec 			= ko.observable("");
		self.m.s2.edit.max_items_per_bet 						= ko.observable("");
		self.m.s2.edit.max_round_jackpot 						= ko.observable("");
		self.m.s2.edit.max_bets_per_round 					= ko.observable("");
		self.m.s2.edit.max_items_per_round 					= ko.observable("");
		self.m.s2.edit.min_items_per_bet 						= ko.observable("");
		self.m.s2.edit.min_items_per_round 					= ko.observable("");
		self.m.s2.edit.min_bet 											= ko.observable("");
		self.m.s2.edit.max_bet 											= ko.observable("");
		self.m.s2.edit.min_bet_round 								= ko.observable("");
		self.m.s2.edit.max_bet_round 								= ko.observable("");
		self.m.s2.edit.allow_unstable_prices 				= ko.observable("");
		self.m.s2.edit.allow_only_types 						= {
			"case": 							ko.observable(false),
			"key": 								ko.observable(false),
			"startrak": 					ko.observable(false),
			"souvenir packages": 	ko.observable(false),
			"souvenir": 					ko.observable(false),
			"knife": 							ko.observable(false),
			"weapon": 						ko.observable(false)
		};
		self.m.s2.edit.fee_percents 								= ko.observable("");
		self.m.s2.edit.debts_collect_per_win_max_percent = ko.observable("");
		self.m.s2.edit.change 											= ko.observable("");
		self.m.s2.edit.one_bot_payout 							= ko.observable("");
		self.m.s2.edit.payout_limit_min 						= ko.observable("");
		self.m.s2.edit.revolutions_per_lottery 			= ko.observable("");
		self.m.s2.edit.lottery_duration_ms 					= ko.observable("");
		self.m.s2.edit.lottery_client_delta_items_limit_s = ko.observable("");
		self.m.s2.edit.pending_duration_s 					= ko.observable("");
		self.m.s2.edit.winner_duration_s 					  = ko.observable("");
		self.m.s2.edit.offers_timeout_sec 					= ko.observable("");
		self.m.s2.edit.bonus_domain 								= ko.observable("");
		self.m.s2.edit.bonus_domain_name 						= ko.observable("");
		self.m.s2.edit.bonus_firstbet 							= ko.observable("");
		self.m.s2.edit.bonus_secondbet 							= ko.observable("");
		self.m.s2.edit.avatars_num_in_strip 			  = ko.observable("");
		self.m.s2.edit.started_client_delta_s 			= ko.observable("");
		self.m.s2.edit.pending_client_delta_s 			= ko.observable("");
		self.m.s2.edit.lottery_client_delta_ms 			= ko.observable("");
		self.m.s2.edit.winner_client_delta_s 				= ko.observable("");
		self.m.s2.edit.max_items_peruser_perround 	= ko.observable("");

		// 2] Поле allow_only_types в виде json-строки //
		//---------------------------------------------//
		self.m.s2.edit.allow_only_types_json = ko.computed(function(){

			// 2.1] Подготовить переменную для результата
			var result = [];

			// 2.2] Наполнить result
			for(var key in self.m.s2.edit.allow_only_types) {

				// Если свойство не своё, пропускаем
				if(!self.m.s2.edit.allow_only_types.hasOwnProperty(key)) continue;

				// Добавить key в result, если значение true
				if(self.m.s2.edit.allow_only_types[key]() == true)
					result.push(key);

			}

			// 2.3] Вернуть result
			return JSON.stringify(result);

		});




	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s2.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s2.indexes = {};

		//-------------------------------//
		// s2.n.2. Индекс игровых комнат //
		//-------------------------------//
		// - По ID игровой комнаты можно получить ссылку на него в m.s2.rooms
		self.m.s2.indexes.rooms = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.rooms().length; i++) {
				results[self.m.s2.rooms()[i]().id()] = self.m.s2.rooms()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

	});


	//-----------------------------//
	// 			        		 	         //
	// 			 s3. Модель ботов 		 //
	// 			         			         //
	//-----------------------------//

	//------------------------------------------------//
	// s3.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s3 = {};

	//----------------------------------------------//
	// s3.2. Наблюдаемый массив с элементами-ботами //
	//----------------------------------------------//
	self.m.s3.bots = ko.observableArray([]);

	//-------------------//
	// s3.3. Ботов всего //
	//-------------------//
  self.m.s3.bots_total = ko.observable(0);

	//---------------------------------------------------------//
	// s3.4. Массив ID прикреплённых к выбранной комнате ботов //
	//---------------------------------------------------------//
	self.m.s3.attached2selectedroom_bot_ids = ko.observableArray([]);

	//------------------------------------------------------------//
	// s3.5. Массив ID НЕ прикреплённых к выбранной комнате ботов //
	//------------------------------------------------------------//
	self.m.s3.notattached2selectedroom_bot_ids = ko.observableArray([]);



	//--------------------------------------//
	// s3.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s3.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s3.indexes = {};

		//----------------------//
		// s3.n.2. Индекс ботов //
		//----------------------//
		// - По ID бота можно получить ссылку на него в m.s3.bots
		self.m.s3.indexes.bots = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s3.bots().length; i++) {
				results[self.m.s3.bots()[i]().id()] = self.m.s3.bots()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//---------------------------------------------------------------------------------//
		// s3.n.3. Наполнить массив ID-значениями ботов, прикреплённых к выбранной комнате //
		//---------------------------------------------------------------------------------//
		(function(){

			// 1] Очистить массив m.s3.attached2selectedroom_bot_ids
			self.m.s3.attached2selectedroom_bot_ids.removeAll();

			// 2] Наполнить массив m.s3.attached2selectedroom_bot_ids
			for(var i=0; i<self.m.s3.bots().length; i++) {

				// 2.1] Если комната не выбрана, перейти к следующей итерации
				if(!self.m.s2.edit.id()) continue;

				// 2.2] Если i-й элемент есть bot_ids выбранной комнаты, добавить его ID в attached2selectedroom_bot_ids
				if(self.m.s2.indexes.rooms[self.m.s2.edit.id()].bot_ids().indexOf(self.m.s3.bots()[i]().id()) != -1)
					self.m.s3.attached2selectedroom_bot_ids.push(self.m.s3.bots()[i]().id());

			}

		}());

		//------------------------------------------------------------------------------------//
		// s3.n.4. Наполнить массив ID-значениями ботов, НЕ прикреплённых к выбранной комнате //
		//------------------------------------------------------------------------------------//
		(function(){

			// 1] Очистить массив m.s3.notattached2selectedroom_bot_ids
			self.m.s3.notattached2selectedroom_bot_ids.removeAll();

			// 2] Наполнить массив m.s3.notattached2selectedroom_bot_ids
			for(var i=0; i<self.m.s3.bots().length; i++) {

				// 2.1] Если комната не выбрана, перейти к следующей итерации
				if(!self.m.s2.edit.id()) continue;

				// 2.2] Если i-го элемента нет bot_ids выбранной комнаты, добавить его ID в notattached2selectedroom_bot_ids
				if(self.m.s2.indexes.rooms[self.m.s2.edit.id()].bot_ids().indexOf(self.m.s3.bots()[i]().id()) == -1)
					self.m.s3.notattached2selectedroom_bot_ids.push(self.m.s3.bots()[i]().id());

			}

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

		//--------------------------------------------------------------------------//
		// X1.2. На основе параметров page и subdoc с сервера открыть соотв.докуент //
		//--------------------------------------------------------------------------//
		// - И заодно добавить стартовое состояние.
		// - А также назначить функцию-обработчик, срабатывающую при смене состояния.
		(function(){

			// 1] На основе параметров page и subdoc с сервера открыть соотв.докуент, добавить стартовое состояние.
			self.f.s1.choose_subdoc({
				group: server.data.group,
				subdoc: server.data.subdoc,
				reload: false,
				first: true
			}, '', '');

			// 2] Назначить функцию-обработчик, срабатывающую при смене состояния
			History.Adapter.bind(window, 'statechange', function() {

				// 1] Получить текущее новое состояние
				var state = History.getState();
				var state_id = state.data.state;

				// 2] Если состояния приложения и истории расходятся
				// - То привести состояние приложения в соответствие
				if(self.m.s1.selected_subdoc().id() != state_id) {

					// 2.1] Получить объект поддокумента
					var subdoc = self.m.s1.indexes.subdocs[state_id];

					// 2.2] Если subdoc не найден, вернуть ошибку и завершить
					if(!subdoc) {
						console.log('Ошибка: при смене состояния истории не найден поддокумент.');
						return;
					}

					// 2.3] Получить группу поддокумента
					var group = self.m.s1.indexes.groups[subdoc.group()];

					// 2.4] Если group не найдена, вернуть ошибку и завершить
					if(!group) {
						console.log('Ошибка: при смене состояния истории не найдена группа поддокумента.');
						return;
					}

					// 2.5] Сменить состояние
					self.f.s1.choose_subdoc({
						group: group.name().toLowerCase(),
						subdoc: subdoc.name().toLowerCase()
					});

				}

			});

		})();

		//----------------------------------------------------------------------//
		// X1.3. Обновить модель комнат на основе переданных в аргументе данных //
		//----------------------------------------------------------------------//
		(function(){

			self.f.s0.update_rooms(server.data.rooms.data);

		})();

		//---------------------------------------------------//
		// X1.4. Выполнить стартовую сортировку списка ботов //
		//---------------------------------------------------//
		(function(){

			self.f.s2.sortfunc('', '');

		})();

		//---------------------------------------------------------------------//
		// X1.5. Обновить модель ботов на основе переданных в аргументе данных //
		//---------------------------------------------------------------------//
		(function(){

			self.f.s0.update_bots(server.data.bots.data);

		})();


	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









