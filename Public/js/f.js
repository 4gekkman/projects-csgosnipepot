/*//========================================////
////																			  ////
////   f.js - функционал модели документа   ////
////																			  ////
////========================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *  s0. Функционал, доступный всему остальному функционалу
 *
 *    f.s0.txt_delay_save						| s0.1. Функционал "механизма отложенного сохранения для текстовых полей"
 *    f.s0.update_bots              | s0.2. Обновить модель ботов на основе переданных данных
 *    f.s0.update_all               | s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера
 *
 *  s1. Функционал модели управления поддокументами приложения
 *
 *		f.s1.is_tab_visible						| s1.1. Определяет, какой таб в меню главной таб-панели видим, а какой нет
 *		f.s1.is_tab_active						| s1.2. Определяет, какой таб в меню главной таб-панели активен
 *    f.s1.choose_subdoc            | s1.3. Выбрать subdoc с указанным id
 *
 *  s2. Функционал группы документов, связанных с ботами
 *
 *    f.s2.select_all_change 				| s2.1. Изменение значения чекбокса "Выбрать всех ботов"
 *    f.s2.show_bots_interface      | s2.2. Открыть интерфейс кликнутого бота
 *
 *
 */


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctions = { constructor: function(self) { var f = this;


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s0. Функционал, доступный всему остальному функционалу 			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s0 = {};

		//-------------------------------------------------------------------------//
		// s0.1. Функционал "механизма отложенного сохранения для текстовых полей" //
		//-------------------------------------------------------------------------//
		f.s0.txt_delay_save = {};

			//----------------------------------------------------------------------//
			// 1] Применить "механизм отложенного сохранения для текстовых полей"   //
			//----------------------------------------------------------------------//
			// - Он особенно актуален для текстовых полей.
			// - Делает так, что функция сохранения срабатывает не при каждом нажатии.
			// - А лишь спустя заданные N секунд после последнего изменения.
			f.s0.txt_delay_save.use = function(savefunc){

				// 2.1. Остановить ранее запланированный setTimeout
				if(self.m.s0.txt_delay_save.settimeoutid())
					clearTimeout(self.m.s0.txt_delay_save.settimeoutid());

				// 2.2] Если время для сохранения не пришло
				if(+Date.now() - +self.m.s0.txt_delay_save.lastupdate() < +self.m.s0.txt_delay_save.gap) {

					// Поставить выполнение на таймер
					var timerId = setTimeout(savefunc, self.m.s0.txt_delay_save.gap);

					// Сохранить timerId в модель
					self.m.s0.txt_delay_save.settimeoutid(timerId);

					// Сохранить текущий timestamp в модель
					self.m.s0.txt_delay_save.lastupdate(Date.now());

					// Указать, что имееются не сохранённые данные
					self.m.s0.txt_delay_save.is_unsaved_data(1);

					// Завершить
					return 1;

				}

				// 2.3] Если время для сохранения пришло
				else {

					// Сохранить текущий timestamp в модель
					self.m.s0.txt_delay_save.lastupdate(Date.now());

				}

			};

			//-------------------------------------//
			// 2] Заблокировать закрытие документа //
			//-------------------------------------//
			// - Иными словами указать, что есть несохранённые данные.
			// - Попытка закрыть страницу в итоге приведёт к вызову модального confirm.
			f.s0.txt_delay_save.block = function(){
				self.m.s0.txt_delay_save.is_unsaved_data(1);
			};

			//--------------------------------------//
			// 3] Разблокировать закрытие документа //
			//--------------------------------------//
			// - Иными словами указать, что нет несохранённых данных.
			// - Попытка закрыть страницу в итоге уже не приведёт к вызову модального confirm.
			f.s0.txt_delay_save.unblock = function(){
				self.m.s0.txt_delay_save.is_unsaved_data(0);
			};


		//---------------------------------------------------------//
		// s0.2. Обновить модель ботов на основе переданных данных //
		//---------------------------------------------------------//
		// - Пояснение
		f.s0.update_bots = function(data) {

			// 1. Обновить self.m.s2.bots

				// 1.1. Очистить
				self.m.s2.bots.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.bots.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.bots[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.bots[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.bots[i][key]);

					}

					// 1.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1);

					// 1.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 1.2.4. Добавить этот объект в подготовленный массив
					self.m.s2.bots.push(ko.observable(obj))

				}

			// 2. Обновить m.s2.bots_total
			self.m.s2.bots_total(data.bots_total);

			// 3. Если чб "Select all" включён, включить все чб в m.s2.bots
			(function(){

				if(self.m.s2.select_all_bots()) {

					// Изменить состояние всех чб в s2.bots на true
					for(var i=0; i<self.m.s2.bots().length; i++) {
						self.m.s2.bots()[i]().selected(true);
					}

				}

			})();

		};


		//------------------------------------------------------------------------//
		// s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера //
		//------------------------------------------------------------------------//
		// - Пояснение
		f.s0.update_all = function(what, from, data, event) {
			console.log('f.s0.update_all');
//
//			// 1] Подготовить объект с функциями-обновлялками моделей документа
//			var update_funcs = {
//
//				// 1.1] Обновлялка групп
//				groups: function(){
//					ajaxko(self, {
//						command: 	    "\\M5\\Commands\\C6_groups",
//						from: 		    "f.s0.update_all",
//						data: 		    {
//							page: 							self.m.s2.pagi.pages_current(),
//							pages_total: 				"",
//							items_at_page: 			server.data.groups.data.items_at_page,
//							filters: 						ko.toJSON(self.m.s2.filters),
//							selected_group_ids: self.m.s2.indexes.selected_group_ids
//						},
//						prejob:       function(config, data, event){},
//						postjob:      function(data, params){
//
//							// Уменьшить счёрчик ajax-запросов на 1
//							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);
//
//						},
//						ok_0:         function(data, params){
//
//							// Обновить модель групп на основе полученных данных
//							self.f.s0.update_groups(data.data);
//
//						}
//					});
//				},
//
//				// 1.2] Обновлялка прав
//				privs: function(){
//
//					ajaxko(self, {
//						command: 	    "\\M5\\Commands\\C7_privileges",
//						from: 		    "f.s0.update_all",
//						data: 		    {
//							page: 							self.m.s3.pagi.pages_current(),
//							pages_total: 				"",
//							items_at_page: 			server.data.privs.data.items_at_page,
//							filters: 						ko.toJSON(self.m.s3.filters),
//							selected_priv_ids: 	self.m.s3.indexes.selected_priv_ids
//						},
//						prejob:       function(config, data, event){},
//						postjob:      function(data, params){
//
//							// Уменьшить счёрчик ajax-запросов на 1
//							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);
//
//						},
//						ok_0:         function(data, params){
//
//							// Обновить модель прав на основе полученных данных
//							self.f.s0.update_privs(data.data);
//
//						}
//					});
//
//				},
//
//				// 1.3] Обновлялка тегов
//				tags: function(){
//					ajaxko(self, {
//						command: 	    "\\M5\\Commands\\C8_tags",
//						from: 		    "f.s0.update_all",
//						data: 		    {
//							page: 						self.m.s4.pagi.pages_current(),
//							pages_total: 			"",
//							items_at_page: 		server.data.tags.data.items_at_page,
//							filters: 					ko.toJSON(self.m.s4.filters),
//							selected_tag_ids: self.m.s4.indexes.selected_tag_ids
//						},
//						prejob:       function(config, data, event){},
//						postjob:      function(data, params){
//
//							// Уменьшить счёрчик ajax-запросов на 1
//							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);
//
//						},
//						ok_0:         function(data, params){
//
//							// Обновить модель тегов на основе полученных данных
//							self.f.s0.update_tags(data.data);
//
//						}
//					});
//				},
//
//				// 1.4] Обновлялка пользователей
//				users: function(){
//					ajaxko(self, {
//						command: 	    "\\M5\\Commands\\C5_users",
//						from: 		    "f.s0.update_all",
//						data: 		    {
//							page: 							self.m.s5.pagi.pages_current(),
//							pages_total: 				"",
//							items_at_page: 			server.data.users.data.items_at_page,
//							filters: 						ko.toJSON(self.m.s5.filters),
//							selected_user_ids: 	self.m.s5.indexes.selected_user_ids
//						},
//						prejob:       function(config, data, event){},
//						postjob:      function(data, params){
//
//							// Уменьшить счёрчик ajax-запросов на 1
//							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);
//
//						},
//						ok_0:         function(data, params){
//
//							// Обновить модель пользователей на основе полученных данных
//							self.f.s0.update_users(data.data);
//
//						}
//					});
//				}
//
//			};
//
//			// 2] Подсчитать, сколько обновлялок будет запущено
//			var updates_counter = (function(){
//
//				// 2.1] Подготовить переменную для результата
//				var result = 0;
//
//				// 2.2] Если what пуст, или является пустым массивом
//				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {
//					for(var key in update_funcs) {
//						if(!update_funcs.hasOwnProperty(key)) continue;
//						result = +result + 1;
//					}
//				}
//
//				// 2.3] В ином случае
//				else {
//					for(var i=0; i<what.length; i++) {
//						if(update_funcs[what[i]]) result = +result + 1;
//					}
//				}
//
//				// 2.n] Вернуть результат
//				return result;
//
//			})();
//
//			// 3] Включить экран обновления
//			self.m.s0.ajax_counter(+self.m.s0.ajax_counter() + +updates_counter);
//
//			// 4] Произвести обновление
//
//				// 4.1] Если what пуст, или является пустым массивом, обновить всё
//				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {
//
//					for(var key in update_funcs) {
//
//						// 1] Если свойство не своё, пропускаем
//						if(!update_funcs.hasOwnProperty(key)) continue;
//
//						// 2] Выполнить обновление для key
//						update_funcs[key]();
//
//					}
//
//				}
//
//				// 4.2] Если what не пуст, обновить только указанное в нём
//				else {
//					for(var i=0; i<what.length; i++) {
//						update_funcs[what[i]]();
//					}
//				}

		};



	//------------------------------------------------------------------------//
	// 			        		 			                                                //
	// 			 s1. Функционал модели управления поддокументами приложения 			//
	// 			         					                                                //
	//------------------------------------------------------------------------//
	f.s1 = {};

		//--------------------------------------------------------------------------//
		// s1.1. Определяет, какой таб в меню главной таб-панели видим, а какой нет //
		//--------------------------------------------------------------------------//
		// - Пояснение
		f.s1.is_tab_visible = function(data) {

			return ['1'].indexOf(data.id()) != -1;

		};

		//---------------------------------------------------------------//
		// s1.2. Определяет, какой таб в меню главной таб-панели активен //
		//---------------------------------------------------------------//
		// - Пояснение
		f.s1.is_tab_active = function(data, root) {

			// 1] Получить необходимые данные
			var id_current = data.id();
			var id_choosen = root.m.s1.selected_subdoc().id();

			// 2] Если id_current относится к поддокументам подгруппы Bots
			if(['1','2'].indexOf(id_current) != -1) {
				if(['1','2'].indexOf(id_choosen) != -1) return true;
			}

			// n] Вернуть false
			return false;

		};

		//-------------------------------------//
		// s1.3. Выбрать subdoc с указанным id //
		//-------------------------------------//
		// - Пояснение
		f.s1.choose_subdoc = function(id, data, event) {

			// 1] Получить ID
			var subdoc_id = (function(){

				if(id) return id;
				else return data.id();

			})();

			// 2] Получить объект поддокумента с id
			var subdoc = self.m.s1.indexes.subdocs[subdoc_id];

			// 3] Если этот subdoc уже выбран, завершить
			if(subdoc.id() == self.m.s1.selected_subdoc().id()) return;

			// 4] Если subdoc не найден, завершить
			if(!subdoc) {
				console.log('Поддокумент с id == '+subdoc_id+' не найден.');
				return;
			}

			// 5] Записать subdoc в m.s1.selected_subdoc
			self.m.s1.selected_subdoc(subdoc);

			// 6] Добавить историю новое состояние
			History.pushState({state:subdoc.id()}, subdoc.name(), subdoc.query());

			// 7] Выполнить update_all
			// - Но только если data != "without reload"
			if(data != "without reload")
				self.f.s0.update_all([], 'subdocs:choose_subdoc', '', '');

		};


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s2. Функционал группы документов, связанных с ботами   			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s2 = {};

		//--------------------------------------------------------//
		// s2.1. Изменение значения чекбокса "Выбрать всех ботов" //
		//--------------------------------------------------------//
		// - Пояснение
		f.s2.select_all_change = function(data, event) {

			// 1] Получить текущее состояние чекбокса
			var state = self.m.s2.select_all_bots();

			// 2] Изменить состояние всех чб в s2.bots на state
			for(var i=0; i<self.m.s2.bots().length; i++) {
				self.m.s2.bots()[i]().selected(state);
			}

		};

		//-----------------------------------------//
		// s2.2. Открыть интерфейс кликнутого бота //
		//-----------------------------------------//
		f.s2.show_bots_interface = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемого права
			self.m.s2.edit.login(data.login());
			self.m.s2.edit.password(data.password());
			self.m.s2.edit.steamid(data.steamid());
			self.m.s2.edit.shared_secret(data.shared_secret());
			self.m.s2.edit.serial_number(data.serial_number());
			self.m.s2.edit.revocation_code(data.revocation_code());
			self.m.s2.edit.uri(data.uri());
			self.m.s2.edit.server_time(data.server_time());
			self.m.s2.edit.account_name(data.account_name());
			self.m.s2.edit.token_gid(data.token_gid());
			self.m.s2.edit.identity_secret(data.identity_secret());
			self.m.s2.edit.secret_1(data.secret_1());

			self.m.s2.edit.ison_incoming(data.ison_incoming());
			self.m.s2.edit.ison_outcoming(data.ison_outcoming());

			self.m.s2.edit.steam_name(data.steam_name());

			// 2] Открыть поддокумент редактирования пользователя
			self.f.s1.choose_subdoc(2);

		};


return f; }};




























