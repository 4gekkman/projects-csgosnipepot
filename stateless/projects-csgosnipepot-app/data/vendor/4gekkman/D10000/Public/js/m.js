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
 * 	s2. Модель группы документов, связанных с группами
 *
 *		s2.1. Объект-контейнер для всех свойств модели
 *    s2.2. Наблюдаемый массив с элементами-группами
 *    s2.3. Модель чекбокса "Выбрать все группы"
 *    s2.4. Количественно-пагинационная модель
 *   	s2.5. Модель создания новой группы
 *   	s2.6. Модель редактирования группы
 *   	s2.7. Модель фильтров групп
 *   	s2.n. Индексы и вычисляемые значения
 *
 * 	s3. Модель группы документов, связанных с правами
 *
 *		s3.1. Объект-контейнер для всех свойств модели
 *    s3.2. Наблюдаемый массив с элементами-правами
 *    s3.3. Модель чекбокса "Выбрать все права"
 *    s3.4. Количественно-пагинационная модель
 *   	s3.5. Модель создания нового права
 *   	s3.6. Модель редактирования права
 *   	s3.7. Наблюдаемый массив с элементами-типами прав
 *   	s3.8. Модель фильтров прав
 *   	s3.n. Индексы и вычисляемые значения
 *
 * 	s4. Модель группы документов, связанных с тегами
 *
 *		s4.1. Объект-контейнер для всех свойств модели
 *    s4.2. Наблюдаемый массив с элементами-тегами
 *    s4.3. Модель чекбокса "Выбрать все теги"
 *    s4.4. Количественно-пагинационная модель
 *   	s4.5. Модель создания нового тега
 *   	s4.6. Модель редактирования тега
 *   	s4.7. Модель фильтров тегов
 *   	s4.n. Индексы и вычисляемые значения
 *
 * 	s5. Модель группы документов, связанных с пользователями
 *
 *		s5.1. Объект-контейнер для всех свойств модели
 *    s5.2. Наблюдаемый массив с элементами-пользователями
 *    s5.3. Модель чекбокса "Выбрать всех пользователей"
 *    s5.4. Количественно-пагинационная модель
 *    s5.5. Наблюдаемый массив с элементами-полом пользователей
 *   	s5.6. Модель создания нового пользователя
 *   	s5.7. Модель редактирования пользователя
 *   	s5.8. Модель фильтров пользователей
 *   	s5.9. Модель изменения пароля
 *   	s5.10. Модель подтверждения email
 *   	s5.11. Модель подтверждения phone
 *   	s5.n. Индексы и вычисляемые значения
 *
 *  s6. Модель документа, связанного с аутентификацией
 *
 *    s6.1. Объект-контейнер для всех свойств модели
 *    s6.2. ФИО аутентифицированного пользователя
 *    s6.3. Аутентификация через email и пароль
 *    s6.4. Аутентификация через phone и пароль
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

			// A4.3.1. Обработка сообщений об успешной аутентификации через Steam //
			//--------------------------------------------------------------------//
			self.websocket.ws1.on(server.data.websockets_channel, function(message) {

				// 1] Сообщить об успешной аутентификации через Steam
				notify({msg: 'Successful auth with Steam!', time: 5, fontcolor: 'RGB(50,120,50)'});
				console.log('Successful auth with Steam!');

				// 2] Перезагрузить документ
				location.reload();

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

	//---------------------------------------------------//
	// s1.2. Наблюдаемый массив поддокументов приложения //
	//---------------------------------------------------//
	self.m.s1.subdocs = ko.observableArray([
		ko.observable({
			id: ko.observable('1'),
			name: ko.observable('Users'),
			query: ko.observable('?page=users')
		}),
		ko.observable({
			id: ko.observable('2'),
			name: ko.observable('User properties'),
			query: ko.observable('?page=userprops')
		}),
		ko.observable({
			id: ko.observable('3'),
			name: ko.observable('New user'),
			query: ko.observable('?page=newuser')
		}),
		ko.observable({
			id: ko.observable('4'),
			name: ko.observable('Groups'),
			query: ko.observable('?page=groups')
		}),
		ko.observable({
			id: ko.observable('5'),
			name: ko.observable('Group properties'),
			query: ko.observable('?page=groupprops')
		}),
		ko.observable({
			id: ko.observable('6'),
			name: ko.observable('New group'),
			query: ko.observable('?page=newgroup')
		}),
		ko.observable({
			id: ko.observable('7'),
			name: ko.observable('Privileges'),
			query: ko.observable('?page=privs')
		}),
		ko.observable({
			id: ko.observable('8'),
			name: ko.observable('Privilege properties'),
			query: ko.observable('?page=privprops')
		}),
		ko.observable({
			id: ko.observable('9'),
			name: ko.observable('New privilege'),
			query: ko.observable('?page=newprivilege')
		}),
		ko.observable({
			id: ko.observable('10'),
			name: ko.observable('Tags'),
			query: ko.observable('?page=tags')
		}),
		ko.observable({
			id: ko.observable('11'),
			name: ko.observable('Tag properties'),
			query: ko.observable('?page=tagprops')
		}),
		ko.observable({
			id: ko.observable('12'),
			name: ko.observable('New tag'),
			query: ko.observable('?page=newtag')
		}),
		ko.observable({
			id: ko.observable('13'),
			name: ko.observable('Settings'),
			query: ko.observable('?page=settings')
		}),
		ko.observable({
			id: ko.observable('14'),
			name: ko.observable('Auth'),
			query: ko.observable('?page=auth')
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


	//---------------------------------------------------------------//
	// 			        		 	                                           //
	// 			 s2. Модель группы документов, связанных с группами 		 //
	// 			         			                                           //
	//---------------------------------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//------------------------------------------------//
	// s2.2. Наблюдаемый массив с элементами-группами //
	//------------------------------------------------//
	self.m.s2.groups = ko.observableArray([]);

	//--------------------------------------------//
	// s2.3. Модель чекбокса "Выбрать все группы" //
	//--------------------------------------------//
	self.m.s2.select_all_groups = ko.observable(false);

	//------------------------------------------//
	// s2.4. Количественно-пагинационная модель //
	//------------------------------------------//
	self.m.s2.pagi = {};

		// 1] Групп всего //
		//----------------//
		self.m.s2.pagi.groups_total = ko.observable(1);

		// 2] Групп прошло фильтры //
		//-------------------------//
		self.m.s2.pagi.groups_filtered = ko.observable(1);

		// 3] Страниц всего //
		//------------------//
		self.m.s2.pagi.pages_total = ko.observable(1);

		// 4] Номер текущей страницы //
		//---------------------------//
		self.m.s2.pagi.pages_current = ko.observable(1);

		// 5] Кол-во выделенных групп (зависит ещё от того, стоит ли галочка "Выбрать все группы") //
		//-----------------------------------------------------------------------------------------//
		self.m.s2.pagi.num_of_selected_groups = ko.computed(function(){

			var result = 0;
			if(self.m.s2.select_all_groups()) result = self.m.s2.pagi.groups_total();
			else {
				for(var i=0; i<self.m.s2.groups().length; i++) {
					if(self.m.s2.groups()[i]().selected()) result = +result+1;
				}
			}
			return result;

		});

		// 6] Наблюдаемый массив с пагинационными страницами //
		//---------------------------------------------------//
		self.m.s2.pagi.pages = ko.observableArray([]);

	//------------------------------------//
	// s2.5. Модель создания новой группы //
	//------------------------------------//
	self.m.s2.new = {};

		// 1] Название группы //
		//--------------------//
		self.m.s2.new.title = ko.observable("");

		// 2] Является ли административной //
		//---------------------------------//
		self.m.s2.new.admin = ko.observable(false);

		// 3] Описание группы //
		//--------------------//
		self.m.s2.new.description = ko.observable("");

	//------------------------------------//
	// s2.6. Модель редактирования группы //
	//------------------------------------//
	self.m.s2.edit = {};

		// 1] ID группы //
		//--------------//
		self.m.s2.edit.id = ko.observable("");

		// 2] Название группы //
		//--------------------//
		self.m.s2.edit.title = ko.observable("");

		// 3] Является ли административной //
		//---------------------------------//
		self.m.s2.edit.admin = ko.observable(false);

		// 4] Описание группы //
		//--------------------//
		self.m.s2.edit.description = ko.observable("");

	//-----------------------------//
	// s2.7. Модель фильтров групп //
	//-----------------------------//
	self.m.s2.filters = ko.observableArray([
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of groups'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s2.indexes.filters['ids of groups'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Title'),
			placeholder: ko.observable('Type 1-st letters of title...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s2.indexes.filters['title'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Admin'),
			placeholder: ko.observable(''),
			value: ko.observable({
				admin: ko.observable(true),
				not_admin: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s2.indexes.filters['admin'].value().admin(true);
				self.m.s2.indexes.filters['admin'].value().not_admin(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of users'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected users'),
			clearfunc: function(self){
				self.m.s2.indexes.filters['ids of users'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of privileges'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected privileges'),
			clearfunc: function(self){
				self.m.s2.indexes.filters['ids of privileges'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of tags'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected tags'),
			clearfunc: function(self){
				self.m.s2.indexes.filters['ids of tags'].value("");
			}
		})
	]);
	self.m.s2.filters_total 		= ko.observable(0);
	self.m.s2.filters_on_count 	= ko.observable(0);

	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s2.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s2.indexes = {};

		//----------------------------------------------------//
		// s2.n.2. Массив ID всех выбранных на странице групп //
		//----------------------------------------------------//
		self.m.s2.indexes.selected_group_ids = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.groups().length; i++) {
				if(self.m.s2.groups()[i]().selected()) results.push(self.m.s2.groups()[i]().id());
			}

			// 3. Вернуть results
			return results;

		}());

		//----------------------------------//
		// s2.n.3. Индекс групп на странице //
		//----------------------------------//
		self.m.s2.indexes.groups = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s2.groups().length; i++) {
				results[self.m.s2.groups()[i]().id()] = self.m.s2.groups()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//-------------------------------//
		// s2.n.4. Индекс фильтров групп //
		//-------------------------------//
		self.m.s2.indexes.filters = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s2.filters().length; i++) {
				results[self.m.s2.filters()[i]().name().toLowerCase()] = self.m.s2.filters()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//-----------------------------------//
		// s2.n.5. Общее количество фильтров //
		//-----------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s2.filters().length; i++) {
				results = +results + 1;
			}

			// 3] Записать results
			self.m.s2.filters_total(results);

		}());

		//----------------------------------------------//
		// s2.n.6. Общее количество включенных фильтров //
		//----------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s2.filters().length; i++) {
				if(self.m.s2.filters()[i]().on())
					results = +results + 1;
			}

			// 3] Записать results
			self.m.s2.filters_on_count(results);

		}());


	});


	//---------------------------------------------------------------//
	// 			        		 	                                           //
	// 			 s3. Модель группы документов, связанных с правами  		 //
	// 			         			                                           //
	//---------------------------------------------------------------//

	//------------------------------------------------//
	// s3.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s3 = {};

	//-----------------------------------------------//
	// s3.2. Наблюдаемый массив с элементами-правами //
	//-----------------------------------------------//
	self.m.s3.privs = ko.observableArray([]);

	//-------------------------------------------//
	// s3.3. Модель чекбокса "Выбрать все права" //
	//-------------------------------------------//
	self.m.s3.select_all_privs = ko.observable(false);

	//------------------------------------------//
	// s3.4. Количественно-пагинационная модель //
	//------------------------------------------//
	self.m.s3.pagi = {};

		// 1] Прав всего //
		//---------------//
		self.m.s3.pagi.privs_total = ko.observable(1);

		// 2] Прав прошло фильтры //
		//------------------------//
		self.m.s3.pagi.privs_filtered = ko.observable(1);

		// 3] Страниц всего //
		//------------------//
		self.m.s3.pagi.pages_total = ko.observable(1);

		// 4] Номер текущей страницы //
		//---------------------------//
		self.m.s3.pagi.pages_current = ko.observable(1);

		// 5] Кол-во выделенных прав (зависит ещё от того, стоит ли галочка "Выбрать все права") //
		//---------------------------------------------------------------------------------------//
		self.m.s3.pagi.num_of_selected_privs = ko.computed(function(){

			var result = 0;
			if(self.m.s3.select_all_privs()) result = self.m.s3.pagi.privs_total();
			else {
				for(var i=0; i<self.m.s3.privs().length; i++) {
					if(self.m.s3.privs()[i]().selected()) result = +result+1;
				}
			}
			return result;

		});

		// 6] Наблюдаемый массив с пагинационными страницами //
		//---------------------------------------------------//
		self.m.s3.pagi.pages = ko.observableArray([]);

	//------------------------------------//
	// s3.5. Модель создания нового права //
	//------------------------------------//
	self.m.s3.new = {};

		// 1] Название права //
		//-------------------//
		self.m.s3.new.title = ko.observable("");

		// 2] Описание права //
		//-------------------//
		self.m.s3.new.description = ko.observable("");

	//-----------------------------------//
	// s3.6. Модель редактирования права //
	//-----------------------------------//
	self.m.s3.edit = {};

		// 1] ID права //
		//-------------//
		self.m.s3.edit.id = ko.observable("");

		// 2] Имя права //
		//--------------//
		self.m.s3.edit.title = ko.observable("");

		// 3] Тип права //
		//--------------//
		self.m.s3.edit.type = ko.observable("");

		// 4] Описание права //
		//-------------------//
		self.m.s3.edit.description = ko.observable("");

	//---------------------------------------------------//
	// s3.7. Наблюдаемый массив с элементами-типами прав //
	//---------------------------------------------------//
	self.m.s3.privtypes = ko.observableArray([]);

	//----------------------------//
	// s3.8. Модель фильтров прав //
	//----------------------------//
	self.m.s3.filters = ko.observableArray([
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of privileges'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s3.indexes.filters['ids of privileges'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Name'),
			placeholder: ko.observable('Type 1-st letters of name...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s3.indexes.filters['name'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Privilege type'),
			placeholder: ko.observable(''),
			value: ko.observable({
				access: ko.observable(true),
				exec: 	ko.observable(true),
				custom: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s3.indexes.filters['privilege type'].value().access(true);
				self.m.s3.indexes.filters['privilege type'].value().exec(true);
				self.m.s3.indexes.filters['privilege type'].value().custom(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of users'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected users'),
			clearfunc: function(self){
				self.m.s3.indexes.filters['ids of users'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of groups'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected groups'),
			clearfunc: function(self){
				self.m.s3.indexes.filters['ids of groups'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of tags'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected tags'),
			clearfunc: function(self){
				self.m.s3.indexes.filters['ids of tags'].value("");
			}
		})
	]);
	self.m.s3.filters_total 		= ko.observable(0);
	self.m.s3.filters_on_count 	= ko.observable(0);

	//--------------------------------------//
	// s3.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s3.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s3.indexes = {};

		//---------------------------------------------------//
		// s3.n.2. Массив ID всех выбранных на странице прав //
		//---------------------------------------------------//
		self.m.s3.indexes.selected_priv_ids = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s3.privs().length; i++) {
				if(self.m.s3.privs()[i]().selected()) results.push(self.m.s3.privs()[i]().id());
			}

			// 3. Вернуть results
			return results;

		}());

		//---------------------------//
		// s3.n.3. Индекс типов прав //
		//---------------------------//
		self.m.s3.indexes.privtypes = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.privtypes().length; i++) {
				results[self.m.s3.privtypes()[i]().id()] = self.m.s3.privtypes()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//---------------------//
		// s3.n.4. Индекс прав //
		//---------------------//
		self.m.s3.indexes.privs = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s3.privs().length; i++) {
				results[self.m.s3.privs()[i]().id()] = self.m.s3.privs()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//------------------------------//
		// s3.n.5. Индекс фильтров прав //
		//------------------------------//
		self.m.s3.indexes.filters = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.filters().length; i++) {
				results[self.m.s3.filters()[i]().name().toLowerCase()] = self.m.s3.filters()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//-----------------------------------//
		// s3.n.6. Общее количество фильтров //
		//-----------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.filters().length; i++) {
				results = +results + 1;
			}

			// 3] Записать results
			self.m.s3.filters_total(results);

		}());

		//----------------------------------------------//
		// s3.n.7. Общее количество включенных фильтров //
		//----------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s3.filters().length; i++) {
				if(self.m.s3.filters()[i]().on())
					results = +results + 1;
			}

			// 3] Записать results
			self.m.s3.filters_on_count(results);

		}());


	});


	//-------------------------------------------------------------//
	// 			        		 	                                         //
	// 			 s4. Модель группы документов, связанных с тегами  		 //
	// 			         			                                         //
	//-------------------------------------------------------------//

	//------------------------------------------------//
	// s4.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s4 = {};

	//----------------------------------------------//
	// s4.2. Наблюдаемый массив с элементами-тегами //
	//----------------------------------------------//
	self.m.s4.tags = ko.observableArray([]);

	//------------------------------------------//
	// s4.3. Модель чекбокса "Выбрать все теги" //
	//------------------------------------------//
	self.m.s4.select_all_tags = ko.observable(false);

	//------------------------------------------//
	// s4.4. Количественно-пагинационная модель //
	//------------------------------------------//
	self.m.s4.pagi = {};

		// 1] Тегов всего //
		//----------------//
		self.m.s4.pagi.tags_total = ko.observable(1);

		// 2] Тегов прошло фильтры //
		//-------------------------//
		self.m.s4.pagi.tags_filtered = ko.observable(1);

		// 3] Страниц всего //
		//------------------//
		self.m.s4.pagi.pages_total = ko.observable(1);

		// 4] Номер текущей страницы //
		//---------------------------//
		self.m.s4.pagi.pages_current = ko.observable(1);

		// 5] Кол-во выделенных тегов (зависит ещё от того, стоит ли галочка "Выбрать все теги") //
		//---------------------------------------------------------------------------------------//
		self.m.s4.pagi.num_of_selected_tags = ko.computed(function(){

			var result = 0;
			if(self.m.s4.select_all_tags()) result = self.m.s4.pagi.tags_total();
			else {
				for(var i=0; i<self.m.s4.tags().length; i++) {
					if(self.m.s4.tags()[i]().selected()) result = +result+1;
				}
			}
			return result;

		});

		// 6] Наблюдаемый массив с пагинационными страницами //
		//---------------------------------------------------//
		self.m.s4.pagi.pages = ko.observableArray([]);

	//-----------------------------------//
	// s4.5. Модель создания нового тега //
	//-----------------------------------//
	self.m.s4.new = {};

		// 1] Тег //
		//--------//
		self.m.s4.new.title = ko.observable("");

	//----------------------------------//
	// s4.6. Модель редактирования тега //
	//----------------------------------//
	self.m.s4.edit = {};

		// 1] ID тега //
		//------------//
		self.m.s4.edit.id = ko.observable("");

		// 2] Имя тега //
		//-------------//
		self.m.s4.edit.title = ko.observable("");

	//-----------------------------//
	// s4.7. Модель фильтров тегов //
	//-----------------------------//
	self.m.s4.filters = ko.observableArray([
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of tags'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s4.indexes.filters['ids of tags'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Tag'),
			placeholder: ko.observable('Type 1-st letters of tag...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s4.indexes.filters['tag'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of users'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected users'),
			clearfunc: function(self){
				self.m.s4.indexes.filters['ids of users'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of groups'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected groups'),
			clearfunc: function(self){
				self.m.s4.indexes.filters['ids of groups'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of privileges'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected privileges'),
			clearfunc: function(self){
				self.m.s4.indexes.filters['ids of privileges'].value("");
			}
		})
	]);
	self.m.s4.filters_total 		= ko.observable(0);
	self.m.s4.filters_on_count 	= ko.observable(0);

	//--------------------------------------//
	// s4.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s4.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s4.indexes = {};

		//----------------------------------------------------//
		// s4.n.2. Массив ID всех выбранных на странице тегов //
		//----------------------------------------------------//
		self.m.s4.indexes.selected_tag_ids = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s4.tags().length; i++) {
				if(self.m.s4.tags()[i]().selected()) results.push(self.m.s4.tags()[i]().id());
			}

			// 3. Вернуть results
			return results;

		}());

		//----------------------//
		// s4.n.3. Индекс тегов //
		//----------------------//
		self.m.s4.indexes.tags = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s4.tags().length; i++) {
				results[self.m.s4.tags()[i]().id()] = self.m.s4.tags()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//-------------------------------//
		// s4.n.4. Индекс фильтров тегов //
		//-------------------------------//
		self.m.s4.indexes.filters = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s4.filters().length; i++) {
				results[self.m.s4.filters()[i]().name().toLowerCase()] = self.m.s4.filters()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//-----------------------------------//
		// s4.n.5. Общее количество фильтров //
		//-----------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s4.filters().length; i++) {
				results = +results + 1;
			}

			// 3] Записать results
			self.m.s4.filters_total(results);

		}());

		//----------------------------------------------//
		// s4.n.6. Общее количество включенных фильтров //
		//----------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s4.filters().length; i++) {
				if(self.m.s4.filters()[i]().on())
					results = +results + 1;
			}

			// 3] Записать results
			self.m.s4.filters_on_count(results);

		}());

	});


	//---------------------------------------------------------------------//
	// 			        		 	                                                 //
	// 			 s5. Модель группы документов, связанных с пользователями  		 //
	// 			         			                                                 //
	//---------------------------------------------------------------------//

	//------------------------------------------------//
	// s5.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s5 = {};

	//------------------------------------------------------//
	// s5.2. Наблюдаемый массив с элементами-пользователями //
	//------------------------------------------------------//
	self.m.s5.users = ko.observableArray([]);

	//----------------------------------------------------//
	// s5.3. Модель чекбокса "Выбрать всех пользователей" //
	//----------------------------------------------------//
	self.m.s5.select_all_users = ko.observable(false);

	//------------------------------------------//
	// s5.4. Количественно-пагинационная модель //
	//------------------------------------------//
	self.m.s5.pagi = {};

		// 1] Пользователей всего //
		//------------------------//
		self.m.s5.pagi.users_total = ko.observable(1);

		// 2] Пользователей прошло фильтры //
		//---------------------------------//
		self.m.s5.pagi.users_filtered = ko.observable(1);

		// 3] Страниц всего //
		//------------------//
		self.m.s5.pagi.pages_total = ko.observable(1);

		// 4] Номер текущей страницы //
		//---------------------------//
		self.m.s5.pagi.pages_current = ko.observable(1);

		// 5] Кол-во выделенных пользователей (зависит ещё от того, стоит ли галочка "Выбрать всех пользователей") //
		//---------------------------------------------------------------------------------------------------------//
		self.m.s5.pagi.num_of_selected_users = ko.computed(function(){

			var result = 0;
			if(self.m.s5.select_all_users()) result = self.m.s5.pagi.users_total();
			else {
				for(var i=0; i<self.m.s5.users().length; i++) {
					if(self.m.s5.users()[i]().selected()) result = +result+1;
				}
			}
			return result;

		});

		// 6] Наблюдаемый массив с пагинационными страницами //
		//---------------------------------------------------//
		self.m.s5.pagi.pages = ko.observableArray([]);

	//-----------------------------------------------------------//
	// s5.5. Наблюдаемый массив с элементами-полом пользователей //
	//-----------------------------------------------------------//
	self.m.s5.genders = ko.observableArray([]);

	//-------------------------------------------//
	// s5.6. Модель создания нового пользователя //
	//------------------------------------------//
	self.m.s5.new = {};

		// 1] Email //
		//----------//
		self.m.s5.new.email = ko.observable("");

		// 2] Фамилия //
		//------------//
		self.m.s5.new.surname = ko.observable("");

		// 3] Имя //
		//--------//
		self.m.s5.new.name = ko.observable("");

		// 4] Отчество //
		//-------------//
		self.m.s5.new.patronymic = ko.observable("");

		// 5] Пол //
		//--------//
		self.m.s5.new.gender = ko.observable("");

		// 6] День рождения //
		//------------------//
		self.m.s5.new.birthday = ko.observable("");
		ko.computed(function(){
			if(!ko.computedContext.isInitial()) return;
			$('#m-s5-new-birthday').datepicker({
				format: 'yyyy-mm-dd'
			});
			$('#m-s5-edit-birthday').datepicker({
				format: 'yyyy-mm-dd'
			});
		});

		// 7] Телефон //
		//------------//
		self.m.s5.new.phone = ko.observable("");

		// 8] Анонимный ли пользователь //
		//------------------------------//
		self.m.s5.new.isanonymous = ko.observable(0);

		// 9] Пароль //
		//-----------//
		self.m.s5.new.password = ko.observable("");

		// 10] Заметка админа //
		//--------------------//
		self.m.s5.new.adminnote = ko.observable("");

		// 11] Никнэйм //
		//-------------//
		self.m.s5.new.nickname = ko.observable("");

	//------------------------------------------//
	// s5.7. Модель редактирования пользователя //
	//------------------------------------------//
	self.m.s5.edit = {};

		self.m.s5.edit.id 								= ko.observable("");
		self.m.s5.edit.email 							= ko.observable("");
		self.m.s5.edit.surname 		 				= ko.observable("");
		self.m.s5.edit.name 							= ko.observable("");
		self.m.s5.edit.patronymic 				= ko.observable("");
		self.m.s5.edit.gender 						= ko.observable("");
		self.m.s5.edit.birthday 					= ko.observable("");
		self.m.s5.edit.phone 			 				= ko.observable("");
		self.m.s5.edit.isanonymous 				= ko.observable("");
		self.m.s5.edit.isblocked 	  			= ko.observable("");
		self.m.s5.edit.adminnote 	 				= ko.observable("");
		self.m.s5.edit.ha_provider_name 	= ko.observable("");
		self.m.s5.edit.ha_provider_uid 	 	= ko.observable("");
		self.m.s5.edit.nickname 	 				= ko.observable("");

	//-------------------------------------//
	// s5.8. Модель фильтров пользователей //
	//-------------------------------------//
	self.m.s5.filters = ko.observableArray([
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of users'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['ids of users'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Email'),
			placeholder: ko.observable('Type 1-st letters of email...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['email'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Phone'),
			placeholder: ko.observable('Type 1-st digits of phone...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['phone'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Name'),
			placeholder: ko.observable('Type 1-st digits of name...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['name'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Surname'),
			placeholder: ko.observable('Type 1-st digits of surname...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['surname'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Patronymic'),
			placeholder: ko.observable('Type 1-st digits of patronymic...'),
			value: ko.observable(""),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['patronymic'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Gender'),
			placeholder: ko.observable(''),
			value: ko.observable({
				male: ko.observable(true),
				female: ko.observable(true),
				undefined: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['gender'].value().male(true);
				self.m.s5.indexes.filters['gender'].value().female(true);
				self.m.s5.indexes.filters['gender'].value().undefined(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Anonymity'),
			placeholder: ko.observable(''),
			value: ko.observable({
				anonymous: ko.observable(true),
				not_anonymous: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['anonymity'].value().anonymous(true);
				self.m.s5.indexes.filters['anonymity'].value().not_anonymous(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Block'),
			placeholder: ko.observable(''),
			value: ko.observable({
				blocked: ko.observable(true),
				not_blocked: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['block'].value().blocked(true);
				self.m.s5.indexes.filters['block'].value().not_blocked(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Email approvement'),
			placeholder: ko.observable(''),
			value: ko.observable({
				approved: ko.observable(true),
				not_approved: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['email approvement'].value().approved(true);
				self.m.s5.indexes.filters['email approvement'].value().not_approved(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Phone approvement'),
			placeholder: ko.observable(''),
			value: ko.observable({
				approved: ko.observable(true),
				not_approved: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['phone approvement'].value().approved(true);
				self.m.s5.indexes.filters['phone approvement'].value().not_approved(true);
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of groups'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected groups'),
			clearfunc: function(self){
				self.m.s5.indexes.filters['ids of groups'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of privileges'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected privileges'),
			clearfunc: function(self){
				self.m.s5.indexes.filters['ids of privileges'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('IDs of tags'),
			placeholder: ko.observable('1,2,3...'),
			value: ko.observable(""),
			applybutton: ko.observable('Apply IDs of the selected tags'),
			clearfunc: function(self){
				self.m.s5.indexes.filters['ids of tags'].value("");
			}
		}),
		ko.observable({
			on: ko.observable(false),
			name: ko.observable('Admin'),
			placeholder: ko.observable(''),
			value: ko.observable({
				admin: ko.observable(true),
				not_admin: ko.observable(true)
			}),
			applybutton: ko.observable(''),
			clearfunc: function(self){
				self.m.s5.indexes.filters['admin'].value().admin(true);
				self.m.s5.indexes.filters['admin'].value().not_admin(true);
			}
		})
	]);
	self.m.s5.filters_total 		= ko.observable(0);
	self.m.s5.filters_on_count 	= ko.observable(0);

	//-------------------------------//
	// s5.9. Модель изменения пароля //
	//-------------------------------//
	self.m.s5.changepass = {};

		// 1] Новый пароль //
		//-----------------//
		self.m.s5.changepass.new1 = ko.observable("");

		// 2] Новый пароль ещё раз //
		//-------------------------//
		self.m.s5.changepass.new2 = ko.observable("");

	//-----------------------------------//
	// s5.10. Модель подтверждения email //
	//-----------------------------------//
	self.m.s5.emailapprove = {};

		// 1] Код подтверждения //
		//----------------------//
		self.m.s5.emailapprove.code = ko.observable("");

		// 2] Подтверждён ли email редактируемого пользователя //
		//-----------------------------------------------------//
		self.m.s5.emailapprove.isapproved = ko.observable(true);

	//-----------------------------------//
	// s5.11. Модель подтверждения phone //
	//-----------------------------------//
	self.m.s5.phoneapprove = {};

		// 1] Код подтверждения //
		//----------------------//
		self.m.s5.phoneapprove.code = ko.observable("");

		// 2] Подтверждён ли phone редактируемого пользователя //
		//-----------------------------------------------------//
		self.m.s5.phoneapprove.isapproved = ko.observable(true);

	//--------------------------------------//
	// s5.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s5.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s5.indexes = {};

		//------------------------------------------------------------//
		// s5.n.2. Массив ID всех выбранных на странице пользователей //
		//------------------------------------------------------------//
		self.m.s5.indexes.selected_user_ids = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s5.users().length; i++) {
				if(self.m.s5.users()[i]().selected()) results.push(self.m.s5.users()[i]().id());
			}

			// 3. Вернуть results
			return results;

		}());

		//-----------------------------------//
		// s5.n.3. Индекс пола пользователей //
		//-----------------------------------//
		self.m.s5.indexes.genders = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s5.genders().length; i++) {
				results[self.m.s5.genders()[i]().id()] = self.m.s5.genders()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//------------------------------//
		// s5.n.4. Индекс пользователей //
		//------------------------------//
		self.m.s5.indexes.users = (function(){

			// 1. Подготовить массив для результатов
			var results = [];

			// 2. Заполнить results
			for(var i=0; i<self.m.s5.users().length; i++) {
				results[self.m.s5.users()[i]().id()] = self.m.s5.users()[i]();
			}

			// 3. Вернуть results
			return results;

		}());

		//---------------------------------------//
		// s5.n.5. Индекс фильтров пользователей //
		//---------------------------------------//
		self.m.s5.indexes.filters = (function(){

			// 1] Подготовить объект для результатов
			var results = {};

			// 2] Заполнить results
			for(var i=0; i<self.m.s5.filters().length; i++) {
				results[self.m.s5.filters()[i]().name().toLowerCase()] = self.m.s5.filters()[i]();
			}

			// 3] Вернуть results
			return results;

		}());

		//-----------------------------------//
		// s5.n.6. Общее количество фильтров //
		//-----------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s5.filters().length; i++) {
				results = +results + 1;
			}

			// 3] Записать results
			self.m.s5.filters_total(results);

		}());

		//----------------------------------------------//
		// s5.n.7. Общее количество включенных фильтров //
		//----------------------------------------------//
		(function(){

			// 1] Подготовить объект для результатов
			var results = 0;

			// 2] Заполнить results
			for(var i=0; i<self.m.s5.filters().length; i++) {
				if(self.m.s5.filters()[i]().on())
					results = +results + 1;
			}

			// 3] Записать results
			self.m.s5.filters_on_count(results);

		}());

		//--------------------------------------------------------------------------------//
		// s5.n.8. Видимы ли панели подтверждения email/phone редактируемого пользователя //
		//--------------------------------------------------------------------------------//
		(function(){

			// email
			if(self.m.s5.indexes.users[self.m.s5.edit.id()])
				self.m.s5.emailapprove.isapproved(!self.m.s5.indexes.users[self.m.s5.edit.id()].is_email_approved());
			else
				return true;

			// phone
			if(self.m.s5.indexes.users[self.m.s5.edit.id()])
				self.m.s5.phoneapprove.isapproved(!self.m.s5.indexes.users[self.m.s5.edit.id()].is_phone_approved());
			else
				return true;

		}());

	});


	//---------------------------------------------------------------//
	// 			        		 	                                           //
	// 			 s6. Модель документа, связанного с аутентификацией  		 //
	// 			         			                                           //
	//---------------------------------------------------------------//

	//------------------------------------------------//
	// s6.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s6 = {};

	//---------------------------------------------//
	// s6.2. ФИО аутентифицированного пользователя //
	//---------------------------------------------//
	self.m.s6.fio = ko.observable('');

	//-------------------------------------------//
	// s6.3. Аутентификация через email и пароль //
	//-------------------------------------------//
	self.m.s6.email_password = {};

		// 1] Email //
		//----------//
		self.m.s6.email_password.email = ko.observable('');

		// 2] Password //
		//-------------//
		self.m.s6.email_password.password = ko.observable('');

	//-------------------------------------------//
	// s6.4. Аутентификация через phone и пароль //
	//-------------------------------------------//
	self.m.s6.phone_password = {};

		// 1] Phone //
		//----------//
		self.m.s6.phone_password.phone = ko.observable('');

		// 2] Password //
		//-------------//
		self.m.s6.phone_password.password = ko.observable('');

	//-----------------------------------------//
	// s6.5. Восстановление пароля через email //
	//-----------------------------------------//
	self.m.s6.pass_recover_email = {};

		// 1] Email //
		//----------//
		self.m.s6.pass_recover_email.email = ko.observable('');

		// 2] Code //
		//----------//
		self.m.s6.pass_recover_email.code = ko.observable('');

		// 3] Password1 //
		//--------------//
		self.m.s6.pass_recover_email.password1 = ko.observable('');

		// 4] Password2 //
		//--------------//
		self.m.s6.pass_recover_email.password2 = ko.observable('');

	//--------------------------------------//
	// s6.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//--------------------------------------------------------------//
		// s6.n.1. Объект-контейнер для индексов и вычисляемых значений //
		//--------------------------------------------------------------//
		self.m.s6.indexes = {};

		//----------------------------------------------------------//
		// s6.n.2. Рассчитать ФИО аутентифицированного пользователя //
		//----------------------------------------------------------//
		(function(){

			// 1] Подготовить строку для результатов
			var result = "";

			// 2] Если данных нет, записать "Not authenticated" и завершить
			if(!self.m.s0.auth.user() || !self.m.s0.auth.user().id || !self.m.s0.auth.user().id()) {
				self.m.s6.fio("Not authenticated");
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

			// 7] Записать результат в m.s6.fio
			self.m.s6.fio(result);

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

		//---------------------------------------------------------------------//
		// X1.2. Добавление в историю стартового состояния (подменой текущего) //
		//---------------------------------------------------------------------//
		History.replaceState({state:self.m.s1.selected_subdoc().id()}, self.m.s1.selected_subdoc().name(), self.m.s1.selected_subdoc().query());

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
		// X1.4. Обновить модель групп на основе переданных в аргументе данных //
		//---------------------------------------------------------------------//
		(function(){

			self.f.s0.update_groups(server.data.groups.data);

		})();

		//--------------------------------------------------------------------//
		// X1.5. Обновить модель прав на основе переданных в аргументе данных //
		//--------------------------------------------------------------------//
		(function(){

			self.f.s0.update_privs(server.data.privs.data);

		})();

		//---------------------------------------------------------------------//
		// X1.6. Обновить модель тегов на основе переданных в аргументе данных //
		//---------------------------------------------------------------------//
		(function(){

			self.f.s0.update_tags(server.data.tags.data);

		})();

		//-----------------------------------------------------------------------------//
		// X1.7. Обновить модель пользователей на основе переданных в аргументе данных //
		//-----------------------------------------------------------------------------//
		(function(){

			self.f.s0.update_users(server.data.users.data);

		})();

		//----------------------------------------------------------------//
		// X1.8. На основе параметра page с сервера открыть соотв.докуент //
		//----------------------------------------------------------------//
		(function(){

			// 1] Получить ссылку на поддокумент
			var subdoc = self.m.s1.indexes.subdocs_by_name[server.data.page];

			// 2] Если subdoc пуст, завершить
			if(!subdoc) return;

			// 3] Открыть subdoc
			self.f.s1.choose_subdoc(subdoc.id(), "without reload", '');

		})();

	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









