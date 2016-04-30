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
 *
 *  s1. Модель [такая-то]
 *
 *		s1.1. Объект-контейнер для всех свойств модели
 *    s1.2. ...
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


	//-----------------------------------//
	// 			        		 	               //
	// 			 s1. Модель главного меню		 //
	// 			         			               //
	//-----------------------------------//

	//------------------------------------------------//
	// s1.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s1 = {};

	//--------------------------------//
	// s1.2. ... //
	//--------------------------------//
	self.m.s1.is_mainmenu_opened = ko.observable(0);


	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s1.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s1.indexes = {};

		//------------------------------------------------------//
		// s1.n.2. ... //
		//------------------------------------------------------//



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

		//-------------------------------------------------------------//
		// X1.2. ... //
		//-------------------------------------------------------------//

			// ... код ...



	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









