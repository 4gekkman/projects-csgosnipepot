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
 *  s1. Модель управления главным меню интерфейса
 *
 *    s1.1. Объект-контейнер для всех свойств модели
 *    s1.2. Наблюдаемый массив поддокументов интерфейса
 *    s1.3. Выбранный поддокумент интерфейса
 *    s1.n. Индексы и вычисляемые значения
 *
 *  s2. Модель управления поддокументами поддокумента "Скины на заказ"
 *
 *    s2.1. Объект-контейнер для всех свойств модели
 *    s2.2. Наблюдаемый массив поддокументов интерфейса
 *    s2.3. Выбранный поддокумент интерфейса
 *    s2.n. Индексы и вычисляемые значения
 *
 *  s3. Модель актуального списка скинов на заказ в магазине
 *
 *  	s3.1. Объект-контейнер для всех свойств модели
 *  	s3.2. Не фильтрованные/сортированные вещи
 *    s3.n. Индексы и вычисляемые значения
 *
 *  s4. Модель добавления скинов в актуальный список скинов на заказ
 *
 *  	s4.1. Объект-контейнер для всех свойств модели
 *    s4.2. Не фильтрованные/сортированные вещи
 *    s4.n. Индексы и вычисляемые значения
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

			// A4.3.1. Обработка входящих websocket-сообщений через пубничный канал "m9:public //
			//---------------------------------------------------------------------------------//
			self.websocket.ws1.on('m9:public', function(data) {

				// 1] Получить имя задачи
				var task = data.data.data.task;

				// 2] В зависимости от task выполнить соотв.метод
				switch(task) {
					case "m14:update:goods:order": 				self.f.s0.update_goods_order_add_sub(data.data.data.data); break;
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


	//---------------------------------------------------------//
	// 			        		 	                                     //
	// 			 s1. Модель управления главным меню интерфейса 		 //
	// 			         			                                     //
	//-------------------------------------------------------- //

	//------------------------------------------------//
	// s1.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s1 = {};

	//---------------------------------------------------//
	// s1.2. Наблюдаемый массив поддокументов интерфейса //
	//---------------------------------------------------//
	self.m.s1.subdocs = ko.mapping.fromJS([
		{
			name:       'skins2order',
			title:      'Скины на заказ',
			visible:    true
		},
		{
			name:       'settings',
			title:      'Настройки',
			visible:    true
		}
	]);

	//----------------------------------------//
	// s1.3. Выбранный поддокумент интерфейса //
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

		//-----------------------//
		// s1.n.2. ... //
		//-----------------------//




	});


	//-------------------------------------------------------------------------------//
	// 			        		 	                                                           //
	// 			 s2. Модель управления поддокументами поддокумента "Скины на заказ" 		 //
	// 			         			                                                           //
	//------------------------------------------------------------------------------ //

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//---------------------------------------------------//
	// s2.2. Наблюдаемый массив поддокументов интерфейса //
	//---------------------------------------------------//
	self.m.s2.subdocs = ko.mapping.fromJS([
		{
			name:       'list',
			title:      'Список',
			visible:    true
		},
		{
			name:       'add',
			title:      'Добавить',
			visible:    true
		}
	]);

	//----------------------------------------//
	// s2.3. Выбранный поддокумент интерфейса //
	//----------------------------------------//
	self.m.s2.selected_subdoc = ko.observable(self.m.s2.subdocs()[0]);

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
		// - По name поддокумента можно получить ссылку на соотв. объект в self.m.s2.subdocs
		self.m.s2.indexes.subdocs = (function(){

			// 1. Подготовить объект для результатов
			var results = {};

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.subdocs().length; i++) {
				results[self.m.s2.subdocs()[i].name()] = self.m.s2.subdocs()[i];
			}

			// 3. Вернуть results
			return results;

		}());


	});


	//---------------------------------------------------------------------//
	// 			        		 	                                                 //
	// 			 s3. Модель актуального списка скинов на заказ в магазине 		 //
	// 			         			                                                 //
	//---------------------------------------------------------------------//

	//------------------------------------------------//
	// s3.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s3 = {};

	//-------------------------------------------//
	// s3.2. Не фильтрованные/сортированные вещи //
	//-------------------------------------------//
	self.m.s3.items = ko.observableArray([]);


	//--------------------------------------//
	// s3.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s3.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s3.indexes = {};

		//------------------------------//
		// s3.n.2. ... //
		//------------------------------//



	});


	//-----------------------------------------------------------------------------//
	// 			        		 	                                                         //
	// 			 s4. Модель добавления скинов в актуальный список скинов на заказ 		 //
	// 			         			                                                         //
	//-----------------------------------------------------------------------------//

	//------------------------------------------------//
	// s4.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s4 = {};

	//-------------------------------------------//
	// s4.2. Не фильтрованные/сортированные вещи //
	//-------------------------------------------//
	self.m.s4.items = ko.observableArray([]);



	//--------------------------------------//
	// s4.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s4.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s4.indexes = {};

		//------------------------------//
		// s4.n.2. ... //
		//------------------------------//



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

		//---------------------------------------------------------------------------------------//
		// X1.2. Загрузить goods2order с сервера в m.s3.items, отсортировать по цене по убыванию //
		//---------------------------------------------------------------------------------------//
		(function(){

			// 1] Загрузить
			self.m.s3.items(ko.mapping.fromJS(server.data.allgoods.goods2order)());

			// 2] Отсортировать по цене, по убыванию
			self.m.s3.items.sort(function(a,b){

				if(+a.price()*100 < +b.price()*100) return 1;
				else if(+a.price()*100 > +b.price()*100) return -1;
				return 0;

			});

		})();

		//-------------------------------------------------------------------------------------//
		// X1.3. Загрузить skins2add с сервера в m.s4.items, отсортировать по цене по убыванию //
		//-------------------------------------------------------------------------------------//
		(function(){

			// 1] Загрузить
			self.m.s4.items(ko.mapping.fromJS(server.data.skins2add)());

			// 2] Отсортировать по цене, по убыванию
			self.m.s4.items.sort(function(a,b){

				if(+a.price()*100 < +b.price()*100) return 1;
				else if(+a.price()*100 > +b.price()*100) return -1;
				return 0;

			});

		})();


	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









