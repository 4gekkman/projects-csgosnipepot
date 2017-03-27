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
 *    s0.7. Можно ли торговать с текущим пользователем (escrow и прочее)
 *    s0.8. Включён ли модальный щит для контента центрального столбца
 *
 *  s1. Модель игры Jackpot
 *  s2. Модель зоны уведомлений
 *
 * 		s2.1. Объект-контейнер для всех свойств модели
 * 		s2.2. Модель панели уведомления о не введённом Steam Trade URL
 * 		s2.n. Индексы и вычисляемые значения
 *
 *  s3. Модель профиля пользователя
 *  s4. Модель ТОПа игроков
 *  s5. Модель FAQ
 *  s6. Модель интерфейса пополнения баланса скинами
 *  s7. Модель магазина скинов
 *  s8. Модель Free Coins
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
				self.websocket.ws1 = layoutmodel.websocket.ws1; //io(server.data.websocket_server);


		//--------------------------------------------------------------//
		// А4.3. Назначение обработчиков сообщений с websocket-серверов //
		//--------------------------------------------------------------//

			// A4.3.1. Публичный канал данной игры //
			//-------------------------------------//
			self.websocket.ws1.on('m9:public', function(data) {

				// 1] Получить имя задачи
				var task = data.data.data.task;

				// 2] В зависимости от task выполнить соотв.метод
				switch(task) {
					case "reload_page": 		  						self.f.s1.reload_page(data.data.data.data); break;
					case "m14:update:goods": 							self.f.s7.update_goods_add_subtract(data.data.data.data); break;
					case "m14:update:goods:order": 				self.f.s7.update_goods_order_add_sub(data.data.data.data); break;
				}

			});

			// A4.3.2. Частный канал данной игры игры для этого игрока //
			//---------------------------------------------------------//
			// - Только для аутентифицированных пользователей.
			if(JSON.parse(layout_data.data.auth).is_anon == 0) {
				self.websocket.ws1.on('m9:private:'+JSON.parse(layout_data.data.auth).user.id, function(data) {

					// 1] Получить имя задачи
					var task = data.data.data.task;

					// 2] В зависимости от task выполнить соотв.метод
					switch(task) {
						//case "tradeoffer_expire_secs": 	self.f.s6.tradeoffer_expire_secs(data.data.data.data); break;
						//case "active_offers_update": 		self.f.s6.active_offers_update(data.data.data.data); break;
						//case "update_inventory": 				self.f.s0.update_inventory_data(data.data.data.data.inventory.data.rgDescriptions); break;
						//case "tradeoffer_wins_cancel": 	self.f.s8.tradeoffer_cancel(data.data.data.data); break;

						case "m13:balance:inventory:subtract:update": self.f.s6.update_inventory_data(data.data.data.data.inventory_cache.rgDescriptions, true); break;
						case "m14:trade:created": 										self.f.s7.offer_created(data.data.data.data); break;
						case "m14:trade:not_enough_money": 						self.f.s7.offer_not_enough_money(data.data.data.data); break;
						case "m14:trade:reserved": 										self.f.s7.offer_reserved(data.data.data.data); break;
						case "m14:buy:order:reserved": 								self.f.s7.items2order_reserved(data.data.data.data); break;
						case "m14:buy:order:success": 								self.f.s7.items2order_success(data.data.data.data); break;

					}

				});
			}


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

	//--------------------------------------------------------------------//
	// s0.7. Можно ли торговать с текущим пользователем (escrow и прочее) //
	//--------------------------------------------------------------------//
	self.m.s0.istradable = ko.observable(self.m.s0.is_logged_in() ? true : false);

	//------------------------------------------------------------------//
	// s0.8. Включён ли модальный щит для контента центрального столбца //
	//------------------------------------------------------------------//
	self.m.s0.is_load_shield_on = ko.observable(false);


	//-----------------------------------//
	// 			        		 	               //
	// 			 s1. Модель игры Jackpot 		 //
	// 			         			               //
	//-----------------------------------//
	// - См. D10009/Public/js/jackpot/m.js
	self.m.s1 = Object.create(ModelJackpot).constructor(self, self.m);


	//---------------------------------------//
	// 			        		 	                   //
	// 			 s2. Модель зоны уведомлений 		 //
	// 			         			                   //
	//---------------------------------------//

	//------------------------------------------------//
	// s2.1. Объект-контейнер для всех свойств модели //
	//------------------------------------------------//
	self.m.s2 = {};

	//----------------------------------------------------------------//
	// s2.2. Модель панели уведомления о не введённом Steam Trade URL //
	//----------------------------------------------------------------//
	self.m.s2.notif_tradeurl = {};

		// 1] Торговая ссылка пользователя, которая пришла с сервера при загрузке документа
		self.m.s2.notif_tradeurl.tradeurl_server = ko.observable(server.data.steam_tradeurl);

		// 2] Торговая ссылка аутентифицированного пользователя
		self.m.s2.notif_tradeurl.tradeurl = ko.observable(server.data.steam_tradeurl);


	//--------------------------------------//
	// s2.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	ko.computed(function(){

		//---------------------------------------//
		// s2.n.1. Объект-контейнер для индексов //
		//---------------------------------------//
		self.m.s2.indexes = {};


	});


	//-------------------------------------------//
	// 			        		 	                       //
	// 			 s3. Модель профиля пользователя 		 //
	// 			         			                       //
	//-------------------------------------------//
	// - См. D10009/Public/js/profile/m.js
	self.m.s3 = Object.create(ModelProfile).constructor(self, self.m);


	//-----------------------------------//
	// 			        		 	               //
	// 			 s4. Модель ТОПа игроков 		 //
	// 			         			               //
	//-----------------------------------//
	// - См. D10009/Public/js/top/m.js
	self.m.s4 = Object.create(ModelTop).constructor(self, self.m);


	//---------------------------//
	// 			        		 	       //
	// 			 s5. Модель FAQ 		 //
	// 			         			       //
	//---------------------------//
	// - См. D10009/Public/js/faq/m.js
	self.m.s5 = Object.create(ModelFaq).constructor(self, self.m);


	//-------------------------------------------------------------//
	// 			        		 	                                         //
	// 			 s6. Модель интерфейса пополнения баланса скинами 		 //
	// 			         			                                         //
	//-------------------------------------------------------------//
	// - См. D10009/Public/js/deposit/m.js
  self.m.s6 = Object.create(ModelDeposit).constructor(self, self.m);


	//---------------------------------------//
	// 			        		 	                   //
	// 			 s7. Модель магазина скинов 		 //
	// 			         			                   //
	//---------------------------------------//
	// - См. D10009/Public/js/shop/m.js
  self.m.s7 = Object.create(ModelShop).constructor(self, self.m);


	//---------------------------------//
	// 			        		 	             //
	// 			 s8. Модель Free Coins 		 //
	// 			         			             //
	//---------------------------------//
	// - См. D10009/Public/js/freecoins/m.js
  self.m.s8 = Object.create(ModelFc).constructor(self, self.m);


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
		// X1.2. Наполнить m.s1.game.palette данными с сервера //
		//-----------------------------------------------------//
		(function(){

			self.m.s1.game.palette(server.data.palette);

		})();

		//----------------------------------------------------------------------------------//
		// X1.3. Обновить m.s1.game.rooms (все игровые данные) начальными данными с сервера //
		//----------------------------------------------------------------------------------//
		(function(){

			// 1] Обновить
			self.f.s1.update_rooms(server.data.rooms);

			// 2] Выбрать комнату по переданному choosen_room_id
			// - Но только, если он не равен 0.
			if(server.data.choosen_room_id != 0)
				self.m.s1.game.choosen_room(self.m.s1.indexes.rooms[server.data.choosen_room_id]);

			// 3] Инициировать статистику последнего победителя
			self.f.s1.stats_init_lastwinner();

		})();
		
		//----------------------------------------------------------------//
		// X1.4. Обновить m.s1.game.statuses начальными данными с сервера //
		//----------------------------------------------------------------//
		// - При клике в любом месте, кроме как на самой панели.
		(function(){

			// 1] Обновить
			self.f.s1.update_lottery_statuses(server.data.lottery_game_statuses);

		})();
		
		//-----------------------------------------------------//
		// X1.5. Вычислить стартовое состояние текущего раунда //
		//-----------------------------------------------------//
		(function(){

			// 1] Проверить наличие необходимых ресурсов
			if(!self.m.s1.game.curprev().current().rounds_statuses) return;

			// 2] Записать имя статуса текущего раунда текущей комнаты в choosen_status
			//self.m.s1.game.choosen_status(self.m.s1.game.curprev().current().rounds_statuses()[self.m.s1.game.curprev().current().rounds_statuses().length-1].status());

		})();

		//-------------------------------------------------------------------------------//
		// X1.6. Инициализировать perfect scrollbar для блока распред.шансов с аватарами //
		//-------------------------------------------------------------------------------//
//		(function(){
//
//			Ps.initialize(document.getElementsByClassName('odds-avatars')[0], {
//				wheelSpeed: 2,
//				wheelPropagation: true,
//				minScrollbarLength: 20,
//				suppressScrollY: true
//			});
//
//		})();

		//--------------------------------------------//
		// X1.7. Активировать и настроить tooltipster //
		//--------------------------------------------//
		(function(){

			$(document).ready(function(){
				self.f.s0.tooltipster_init();
			});

		})();

		//-------------------------------------------------------------------------------//
		// X1.8. Организовать ежесекундные тики, синхронизированные с серверным временем //
		//-------------------------------------------------------------------------------//
		(function(){

			// 1. Подготовить рекурсивную функцию
			var f = function f(self) {

				// 1.1. Прибавить единицу к m.s1.game.time.gone_ms
				self.m.s1.game.time.gone_ms(+self.m.s1.game.time.gone_ms() + 10);

				// 1.2. Рекурсивно запустить f
				setTimeout(f, 10, self);

			}.bind(null, self);

			// 2. Запустить f, чтобы срабатывала ежесекундно
			setTimeout(f, 10, self);

		})();

		//--------------------------------//
		// X1.9. Запустить работу очереди //
		//--------------------------------//
		(function(){

			// 1. Подготовить рекурсивную функцию
			var f = function f(self) {

				// Выполнить процессор очереди
				self.f.s1.queue_processor();

				// Рекурсивно запустить f
				setTimeout(f, 250, self);

			}.bind(null, self);

			// 2. Запустить f, чтобы срабатывала ежесекундно
			setTimeout(f, 250, self);

		})();

		//----------------------------------//
		// X1.10. Наполнить smoothbets.bets //
		//----------------------------------//
		(function(){

			self.f.s1.smootbets_update();

		})();

		//------------------------------------------------------------------------------------------------//
		// X1.11. Инициализировать perfect scrollbar для блока с выбранными для пополнения баланса вещами //
		//------------------------------------------------------------------------------------------------//
		(function(){

			Ps.initialize(document.getElementsByClassName('deposit-choosen-items-cont')[0], {
				wheelSpeed: 1.03,
				wheelPropagation: false,
				minScrollbarLength: 10
			});

		})();

		//-------------------------------------------------------------------------------------//
		// X1.12. Инициализировать perfect scrollbar для блока с выбранными для покупки вещами //
		//-------------------------------------------------------------------------------------//
		(function(){

			Ps.initialize(document.getElementsByClassName('shop-choosen-items-cont')[0], {
				wheelSpeed: 1.03,
				wheelPropagation: false,
				minScrollbarLength: 10
			});

		})();






	});

	//------------------------------------------//
	// X2. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self;


}};	// конец модели









