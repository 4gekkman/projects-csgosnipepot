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
 *    s1.2. Наблюдаемый массив поддокументов приложения
 *    s1.3. Ссылка на выбранный поддокумент приложения
 *    s1.n. Индексы и вычисляемые значения
 *
 *  s2. Модель группы документов, связанных с ботами
 *
 *    s2.1. Объект-контейнер для всех свойств модели
 *    s2.2. Наблюдаемый массив с элементами-ботами
 *    s2.3. Модель чекбокса "Выбрать всех ботов"
 *    s2.4. Ботов всего
 *    s2.5. Кол-во выделенных ботов (зависит ещё от того, стоит ли галочка "Выбрать всех ботов")
 *    s2.6. Модель редактирования бота
 *    s2.7. Модель опций для эл-в select со значениями true/false
 *    s2.n. Индексы и вычисляемые значения
 *
 *  s3. Модель инвентаря выбранного бота
 *
 *    s3.1. Объект-контейнер для всех свойств модели
 *    s3.2. Наблюдаемый массив с элементами-предметами инвентаря
 *    s3.3. Общее количество вещей в инвентаре выбранного бота
 *    s3.4. Общее количество выделенных вещей в инвентаре выбранного бота
 *    s3.5. Объект с экземпляром scroll для инвентаря выбранного бота
 *    s3.6. Идёт ли сейчас ajax-запрос, или нет
 *    s3.n. Индексы и вычисляемые значения
 *
 *  s4. Модель генератора мобильных аутентификационных кодов
 *
 *  	s4.1. Объект-контейнер для всех свойств модели
 *    s4.2. Текущий код для выбранного бота
 *    s4.3. Сколько секунд осталось до истечения текущего кода
 *    s4.4. Временная метка клиентского времени последнего получения кода
 *  	s4.5. Обновляемая таймером каждые N секунд временная метка текущего времени
 *    s4.6. Валиден ли текущий отображаемый код, или нет
 *    s4.7. Сколько осталось жить текущему коду в %
 *    s4.8. Идёт ли сейчас ajax-запрос, или нет
 *  	s4.n. Индексы и вычисляемые значения
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

			// A4.3.1. Обработка сообщений об обновлении информации о кол-ве вещей в инвентарях  //
			//-----------------------------------------------------------------------------------//
			self.websocket.ws1.on('m8:update_bots_inventory_count', function(message) {

				// Обновить данные ботов, связанные с инвентарём
				for(var i=0; i<self.m.s2.bots().length; i++) {
					for(var j=0; j<message.data.data.bots.length; j++) {
						if(self.m.s2.bots()[i]().id() == message.data.data.bots[j].id) {
							self.m.s2.bots()[i]().inventory_count(message.data.data.bots[j].inventory_count);
							self.m.s2.bots()[i]().inventory_count_last_update(message.data.data.bots[j].inventory_count_last_update);
							self.m.s2.bots()[i]().inventory_count_last_bug(message.data.data.bots[j].inventory_count_last_bug);
						}
					}
				}

			});

			// A4.3.2. Обработка сообщений об обновлении информации об авторизации ботов //
			//---------------------------------------------------------------------------//
			self.websocket.ws1.on('m8:update_bots_authorization_statuses', function(message) {

				// Обновить данные ботов, связанные с инвентарём
				for(var i=0; i<self.m.s2.bots().length; i++) {
					for(var j=0; j<message.data.data.bots.length; j++) {
						if(self.m.s2.bots()[i]().id() == message.data.data.bots[j].id) {
							self.m.s2.bots()[i]().authorization(message.data.data.bots[j].authorization);
							self.m.s2.bots()[i]().authorization_last_update(message.data.data.bots[j].authorization_last_update);
							self.m.s2.bots()[i]().authorization_last_bug(message.data.data.bots[j].authorization_last_bug);
						}
					}
				}

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

		// 4] Приём и обработка аутентификационных данных при 1-й загрузке //
		//-----------------------------------------------------------------//
		ko.computed(function(){

			// 4.1] Если это не первый запуск, завершить
			if(!ko.computedContext.isInitial()) return;

			// 4.2] Если сервер не прислал данные, завершить
			if(!server || !server.data || !server.data.auth) return;

			// 4.3] Распаковать данные, и проверить
			var auth = JSON.parse(server.data.auth);
			if((!auth.is_anon && auth.is_anon != 0) || !auth.user || !auth.auth) return;

			// 4.3] Наполнить m.s0.auth.is_anon
			self.m.s0.auth.is_anon(auth.is_anon);

			// 4.4] Наполнить m.s0.auth.user
			for(var key in auth.user) {

				// 1] Если свойство не своё, пропускаем
				if(!auth.user.hasOwnProperty(key)) continue;

				// 2] Добавим свойство key в m.s0.auth.user
				self.m.s0.auth.user()[key] = ko.observable(auth.user[key]);

			}

			// 4.5] Наполнить m.s0.auth.auth
			for(var key in auth.auth) {

				// 1] Если свойство не своё, пропускаем
				if(!auth.auth.hasOwnProperty(key)) continue;

				// 2] Добавим свойство key в m.s0.auth.auth
				self.m.s0.auth.auth()[key] = ko.observable(auth.auth[key]);

			}

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

	//---------------------------------------------------//
	// s1.2. Наблюдаемый массив поддокументов приложения //
	//---------------------------------------------------//
	self.m.s1.subdocs = ko.observableArray([
		ko.observable({
			id: ko.observable('1'),
			name: ko.observable('Bots'),
			query: ko.observable('?page=bots')
		}),
		ko.observable({
			id: ko.observable('2'),
			name: ko.observable('Bot'),
			query: ko.observable('?page=bot')
		})

	]);

	//--------------------------------------------------//
	// s1.3. Ссылка на выбранный поддокумент приложения //
	//--------------------------------------------------//
	self.m.s1.selected_subdoc = ko.observable(self.m.s1.subdocs()[0]());

	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s1.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s1.indexes = {};

		//------------------------------//
		// s1.n.2. Индекс поддокументов //
		//------------------------------//
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

		//--------------------------------------//
		// s1.n.3. Именной индекс поддокументов //
		//--------------------------------------//
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


	//-------------------------------------------------------------//
	// 			        		 	                                         //
	// 			 s2. Модель группы документов, связанных с ботами 		 //
	// 			         			                                         //
	//-------------------------------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//----------------------------------------------//
	// s2.2. Наблюдаемый массив с элементами-ботами //
	//----------------------------------------------//
	self.m.s2.bots = ko.observableArray([]);

	//--------------------------------------------//
	// s2.3. Модель чекбокса "Выбрать всех ботов" //
	//--------------------------------------------//
	self.m.s2.select_all_bots = ko.observable(false);

	//-------------------//
	// s2.4. Ботов всего //
	//-------------------//
  self.m.s2.bots_total = ko.observable(0);

	//--------------------------------------------------------------------------------------------//
	// s2.5. Кол-во выделенных ботов (зависит ещё от того, стоит ли галочка "Выбрать всех ботов") //
	//--------------------------------------------------------------------------------------------//
	self.m.s2.num_of_selected_bots = ko.computed(function(){

		var result = 0;
		if(self.m.s2.select_all_bots()) result = self.m.s2.bots_total();
		else {
			for(var i=0; i<self.m.s2.bots().length; i++) {
				if(self.m.s2.bots()[i]().selected()) result = +result+1;
			}
		}
		return result;

	});

	//----------------------------------//
	// s2.6. Модель редактирования бота //
	//----------------------------------//
	self.m.s2.edit = {};

		self.m.s2.edit.login 						= ko.observable("");
		self.m.s2.edit.password 				= ko.observable("");
		self.m.s2.edit.steamid 					= ko.observable("");
		self.m.s2.edit.shared_secret 		= ko.observable("");
		self.m.s2.edit.serial_number 		= ko.observable("");
		self.m.s2.edit.revocation_code 	= ko.observable("");
		self.m.s2.edit.uri 							= ko.observable("");
		self.m.s2.edit.server_time 			= ko.observable("");
		self.m.s2.edit.account_name 		= ko.observable("");
		self.m.s2.edit.token_gid 				= ko.observable("");
		self.m.s2.edit.identity_secret 	= ko.observable("");
		self.m.s2.edit.secret_1 				= ko.observable("");
		self.m.s2.edit.apikey 					= ko.observable("");

		self.m.s2.edit.id   						= ko.observable("");
		self.m.s2.edit.ison_incoming 		= ko.observable("");
		self.m.s2.edit.ison_outcoming 	= ko.observable("");

		self.m.s2.edit.steam_name       = ko.observable("");

	//-------------------------------------------------------------//
	// s2.7. Модель опций для эл-в select со значениями true/false //
	//-------------------------------------------------------------//
	self.m.s2.options_true_false = ko.observableArray([
		ko.observable({
			value: ko.observable('0'),
			name: ko.observable('off')
		}),
		ko.observable({
			value: ko.observable('1'),
			name: ko.observable('on')
		})
	]);

	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s2.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s2.indexes = {};


	});


	//-------------------------------------------------//
	// 			        		 	                             //
	// 			 s3. Модель инвентаря выбранного бота 		 //
	// 			         			                             //
	//-------------------------------------------------//

	//------------------------------------------------//
	// s3.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s3 = {};

	//------------------------------------------------------------//
	// s3.2. Наблюдаемый массив с элементами-предметами инвентаря //
	//------------------------------------------------------------//
	self.m.s3.inventory = ko.observableArray([]);

	//----------------------------------------------------------//
	// s3.3. Общее количество вещей в инвентаре выбранного бота //
	//----------------------------------------------------------//
	self.m.s3.inventory_total = ko.observable(0);

	//---------------------------------------------------------------------//
	// s3.4. Общее количество выделенных вещей в инвентаре выбранного бота //
	//---------------------------------------------------------------------//
	self.m.s3.inventory_selected = ko.observable(0);

	//-----------------------------------------------------------------//
	// s3.5. Объект с экземпляром scroll для инвентаря выбранного бота //
	//-----------------------------------------------------------------//
	self.m.s3.scroll = ko.observable("");

	//-------------------------------------------//
	// s3.6. Идёт ли сейчас ajax-запрос, или нет //
	//-------------------------------------------//
	self.m.s3.is_ajax_invoking = ko.observable(false);

	//--------------------------------------//
	// s3.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s3.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s3.indexes = {};

		//------------------------------------------------------------//
		// s3.n.2. Общее количество вещей в инвентаре выбранного бота //
		//------------------------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.inventory().length; i++) {
				results = +results + 1;
			}

			// 3] Записать results
			self.m.s3.inventory_total(results);

		}());

		//---------------------------------------------------------------------//
		// s3.4. Общее количество выделенных вещей в инвентаре выбранного бота //
		//---------------------------------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.inventory().length; i++) {
				if(self.m.s3.inventory()[i]().selected())
					results = +results + 1;
			}

			// 3] Записать results
			self.m.s3.inventory_selected(results);

		}());


	});


	//---------------------------------------------------------------------//
	// 			        		 	                                                 //
	// 			 s4. Модель генератора мобильных аутентификационных кодов 		 //
	// 			         			                                                 //
	//---------------------------------------------------------------------//

	//------------------------------------------------//
	// s4.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s4 = {};

	//---------------------------------------//
	// s4.2. Текущий код для выбранного бота //
	//---------------------------------------//
	self.m.s4.code = ko.observable('H8YGB');

	//----------------------------------------------------------//
	// s4.3. Сколько секунд осталось до истечения текущего кода //
	//----------------------------------------------------------//
	self.m.s4.expires_in_secs = ko.observable('');

	//---------------------------------------------------------------------//
	// s4.4. Временная метка клиентского времени последнего получения кода //
	//---------------------------------------------------------------------//
	self.m.s4.last_code_update_timestamp = ko.observable('');

	//-----------------------------------------------------------------------------//
	// s4.5. Обновляемая таймером каждые N секунд временная метка текущего времени //
	//-----------------------------------------------------------------------------//
	self.m.s4.now = ko.observable('');

	//----------------------------------------------------//
	// s4.6. Валиден ли текущий отображаемый код, или нет //
	//----------------------------------------------------//
	self.m.s4.is_current_code_valid = ko.computed(function(){

		// 1] Получить все необходимые временные метки
		var now 						= self.m.s4.now();
		var last 						= self.m.s4.last_code_update_timestamp();
		var expires_in_secs = self.m.s4.expires_in_secs();

		// 2] Определить, пора ли обновлять код
		var isvalid = (function(){

			// 3.1] Если last или expires_in_secs или now пусты, то не валиден
			if(!last || !expires_in_secs || !now) return false;

			// 3.2] Если время пришло, то не валиден
			if(+now - +last > +expires_in_secs*1000) return false;

			// 3.3] Если код дошёл сюда, то валиден
			return true;

		})();

		// 3] Вернуть результат
		return isvalid;

	});

	//-----------------------------------------------//
	// s4.7. Сколько осталось жить текущему коду в % //
	//-----------------------------------------------//
	self.m.s4.expire_percents = ko.computed(function(){

		// 1] Получить все необходимые временные метки
		var now 						= self.m.s4.now();
		var last 						= self.m.s4.last_code_update_timestamp();
		var expires_in_secs = self.m.s4.expires_in_secs();

		// 2] Если last или expires_in_secs или now пусты, то 0%
		if(!last || !expires_in_secs || !now) return '0%';

		// 3] Подсчитать оставшееся время жизни
		var rest = Math.round((+expires_in_secs*1000 - (+now - +last))/1000);

		// 4] Подсчитать оставшееся время в % от 30 секунд, вернуть результат
		return Math.round((rest/30)*100) + '%';

	});

	//-------------------------------------------//
	// s4.8. Идёт ли сейчас ajax-запрос, или нет //
	//-------------------------------------------//
	self.m.s4.is_ajax_invoking = ko.observable(false);



	//--------------------------------------//
	// s4.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s4.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s4.indexes = {};

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

		//-------------------------------------------------//
		// X1.2. Добавление в историю стартового состояния //
		//-------------------------------------------------//
		History.pushState({state:self.m.s1.selected_subdoc().id()}, self.m.s1.selected_subdoc().name(), self.m.s1.selected_subdoc().query());

		//-----------------------------------------------------------------------//
		// X1.3. Назначить функцию-обработчик, срабатывающую при смене состояния //
		//-----------------------------------------------------------------------//
    History.Adapter.bind(window, 'statechange', function(){

			// 1] Получить текущее новое состояние
			var state = History.getState();
			var state_id = state.data.state;

			// 2] Если состояния приложения и истории расходятся
			// - То привести состояние приложения в соответствие
			if(self.m.s1.selected_subdoc().id() != state_id)
				self.f.s1.choose_subdoc(state_id);

    });

		//---------------------------------------------------------------------//
		// X1.4. Обновить модель ботов на основе переданных в аргументе данных //
		//---------------------------------------------------------------------//
		(function(){

			self.f.s0.update_bots(server.data.bots.data);

		})();

		//--------------------------------------------------------------------------------//
		// X1.5. В интерфейсе бота каждую секунду проверять, не пора ли обновить код бота //
		//--------------------------------------------------------------------------------//
		setInterval(function(){

			// 1] Если выбран не поддокумент с интерфейсом бота, завершить
			if(self.m.s1.selected_subdoc().id() != 2) return;

			// 2] Получить все необходимые временные метки
			var now 						= Date.now();
			  self.m.s4.now(now);
			var last 						= self.m.s4.last_code_update_timestamp();
			var expires_in_secs = self.m.s4.expires_in_secs();
			var code 						= self.m.s4.code();

			// 3] Определить, пора ли обновлять код
			var itstime = (function(){

				// 3.1] Если last или expires_in_secs или code пусты, то пора
				if(!last || !expires_in_secs || !code) return true;

				// 3.2] Если время пришло, значит пора
				if(+Date.now() - +last > +expires_in_secs*1000) return true;

				// 3.3] Если код дошёл сюда, значит не пора
				return false;

			})();

			// 4] Обновить код мобильного аутентификатора для выбранного бота
			if(itstime && !self.m.s4.is_ajax_invoking()) self.f.s4.update();

		}, 1000);

	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









