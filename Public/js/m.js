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


	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s2.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s2.indexes = {};


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


	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









