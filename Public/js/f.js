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
 *    f.s0.update_inventory         | s0.3. Обновить инвентарь выбранного бота
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
 *    f.s2.edit                     | s2.3. Отредактировать пользователя
 *
 *  s3. Функционал модели инвентаря выбранного бота
 *
 *    f.s3.update 									| s3.1. Обновить инвентарь выбранного бота
 *    f.s3.get_item_title           | s3.2. Формирует title для вещей в инвентаре
 *    f.s3.deselect_all             | s3.3. Развыделить все элементы в инвентаре
 *
 *  s4. Функционал модели генератора мобильных аутентификационных кодов
 *
 *    f.s4.update 									| s4.1. Обновить код мобильного аутентификатора для выбранного бота
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


		//------------------------------------------//
		// s0.3. Обновить инвентарь выбранного бота //
		//------------------------------------------//
		// - Пояснение
		f.s0.update_inventory = function(data) {

			// 1. Обновить m.s3.inventory

				// 1.1. Очистить
				self.m.s3.inventory.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.data.rgDescriptions.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.data.rgDescriptions[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.data.rgDescriptions[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.data.rgDescriptions[i][key]);

					}

					// 1.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1);

					// 1.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 1.2.4. Добавить этот объект в подготовленный массив
					self.m.s3.inventory.push(ko.observable(obj))

				}

			// 2. Применить к боксу с инвентарём perfect-scroll
			(function(){

				// 2.1. Получить ссылку на DOM-элемент
				var dom = document.getElementsByClassName('inventory-container')[0];
				if(!dom) return;

				// 2.2. Если у dom нет класса ps-container
				// - Тогда инициилизировать perfect-scroll на этом элементе
				if(!checkClass('', 'ps-container', dom)) {
					Ps.initialize(document.getElementsByClassName('inventory-container')[0], {
						'wheelSpeed': .2
					});
				}

				// 2.3. Иначе обновить
				else {
					Ps.update(dom);
				}

			})();

  	};


		//------------------------------------------------------------------------//
		// s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера //
		//------------------------------------------------------------------------//
		// - Пояснение
		f.s0.update_all = function(what, from, data, event) {

			// 1] Подготовить объект с функциями-обновлялками моделей документа
			var update_funcs = {

				// 1.1] Обновлялка ботов
				bots: function(){
					ajaxko(self, {
						command: 	    "\\M8\\Commands\\C1_bots",
						from: 		    "f.s0.update_all",
						data: 		    {

						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счёрчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель групп на основе полученных данных
							self.f.s0.update_bots(data.data);

						},
						callback:			function(){

							// 1.2.1] Если steamid пуста или выбран не документ бота, завершить
							if(!self.m.s2.edit.steamid() || self.m.s1.selected_subdoc().id() != 2) return;

							// 1.2.2] Обновить инвентарь выбранного бота
							ajaxko(self, {
								command: 	    "\\M8\\Commands\\C4_getinventory",
								from: 		    "f.s0.update_all",
								data: 		    {
									steamid: 				  self.m.s2.edit.steamid()
								},
								prejob:       function(config, data, event){},
								postjob:      function(data, params){

								},
								ok_0:         function(data, params){

									// Обновить модель групп на основе полученных данных
									self.f.s0.update_inventory(data);

								}
							});

						}
					});
				}

				// 1.2] Обновлялка инвентаря выбранного бота

			};

			// 2] Подсчитать, сколько обновлялок будет запущено
			var updates_counter = (function(){

				// 2.1] Подготовить переменную для результата
				var result = 0;

				// 2.2] Если what пуст, или является пустым массивом
				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {
					for(var key in update_funcs) {
						if(!update_funcs.hasOwnProperty(key)) continue;
						result = +result + 1;
					}
				}

				// 2.3] В ином случае
				else {
					for(var i=0; i<what.length; i++) {
						if(update_funcs[what[i]]) result = +result + 1;
					}
				}

				// 2.n] Вернуть результат
				return result;

			})();

			// 3] Включить экран обновления
			self.m.s0.ajax_counter(+self.m.s0.ajax_counter() + +updates_counter);

			// 4] Произвести обновление

				// 4.1] Если what пуст, или является пустым массивом, обновить всё
				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

					for(var key in update_funcs) {

						// 1] Если свойство не своё, пропускаем
						if(!update_funcs.hasOwnProperty(key)) continue;

						// 2] Выполнить обновление для key
						update_funcs[key]();

					}

				}

				// 4.2] Если what не пуст, обновить только указанное в нём
				else {
					for(var i=0; i<what.length; i++) {
						update_funcs[what[i]]();
					}
				}

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

			// 7] Если выбран не документ бота, очистить m.s2.edit и m.s3.inventory
			if(self.m.s1.selected_subdoc().id() != 2) {

				// 7.1] Очистить m.s3.inventory
				self.m.s3.inventory.removeAll();

				// 7.2] Очистить m.s2.edit
				for(var key in self.m.s2.edit) {

					// Если свойство не своё, пропускаем
					if(!self.m.s2.edit.hasOwnProperty(key)) continue;

					// Добавим в obj свойство key
					self.m.s2.edit[key]("");

				}

			}

			// n] Выполнить update_all
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

			// 1] Загрузить в форму текущие данные редактируемого бота
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
			self.m.s2.edit.apikey(data.apikey());

			self.m.s2.edit.id(data.id());
			self.m.s2.edit.ison_incoming(data.ison_incoming());
			self.m.s2.edit.ison_outcoming(data.ison_outcoming());

			self.m.s2.edit.steam_name(data.steam_name());

			// 2] Открыть поддокумент редактирования пользователя
			self.f.s1.choose_subdoc(2);

		};

		//------------------------------------//
		// s2.3. Отредактировать пользователя //
		//------------------------------------//
		f.s2.edit = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C3_edit_bot",
				from: 		    "f.s2.edit",
			  data: 		    {
					login: 					  self.m.s2.edit.login(),
					password: 			  self.m.s2.edit.password(),
					steamid: 				  self.m.s2.edit.steamid(),
					shared_secret: 		self.m.s2.edit.shared_secret(),
					serial_number: 		self.m.s2.edit.serial_number(),
					revocation_code:	self.m.s2.edit.revocation_code(),
					uri: 						  self.m.s2.edit.uri(),
					server_time: 		 	self.m.s2.edit.server_time(),
					account_name: 	 	self.m.s2.edit.account_name(),
					token_gid: 			  self.m.s2.edit.token_gid(),
					identity_secret:	self.m.s2.edit.identity_secret(),
					secret_1: 			  self.m.s2.edit.secret_1(),
					apikey:   				self.m.s2.edit.apikey(),

					id:               self.m.s2.edit.id(),
					ison_incoming:    self.m.s2.edit.ison_incoming(),
					ison_outcoming:   self.m.s2.edit.ison_outcoming()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пользователь был успешно отредактирован
					notify({msg: 'The bot has been edited', time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};


	//--------------------------------------------------------------//
	// 			        		 			                                      //
	// 			 s3. Функционал модели инвентаря выбранного бота   			//
	// 			         					                                      //
	//--------------------------------------------------------------//
	f.s3 = {};

		//------------------------------------------//
		// s3.1. Обновить инвентарь выбранного бота //
		//------------------------------------------//
		f.s3.update = function(data, event) {

			// 1] Если steamid выбранного бота пуст, сообщить и завершить
			if(!self.m.s2.edit.steamid()) {
				notify({msg: 'Enter steamid of the bot below', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Выполнить запрос
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C4_getinventory",
				from: 		    "f.s3.update",
			  data: 		    {
					steamid: 				  self.m.s2.edit.steamid()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Обновить инвентарь выбранного бота
					self.f.s0.update_inventory(data);

					// 2] Сообщить, что пользователь был успешно отредактирован
					notify({msg: "The bots inventory successfully updated", time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//---------------------------------------------//
		// s3.2. Формирует title для вещей в инвентаре //
		//---------------------------------------------//
		f.s3.get_item_title = function(data) {

			// 1] Подготовить строку для результата
			var result = "";

			// 2] Name
			result = result + "--- Name --- " + (data.name ? data.name() : "''");

			// 3] Type
			result = result + "\n--- Type --- " + (data.type ? data.type() : "''");

			// 4] Weapon
			result = result + "\n--- Weapon --- " + (data.weapon ? data.weapon() : "''");

			// 5] Category
			result = result + "\n--- Category --- " + (data.category ? data.category() : "''");

			// 6] Quality
			result = result + "\n--- Quality --- " + (data.quality ? data.quality() : "''");

			// 7] Exterior
			result = result + "\n--- Exterior --- " + (data.exterior ? data.exterior() : "''");

			// n] Вернуть результат
			return result;

		};


		//--------------------------------------------//
		// s3.3. Развыделить все элементы в инвентаре //
		//--------------------------------------------//
		f.s3.deselect_all = function(data) {

			for(var i=0; i<self.m.s3.inventory().length; i++) {
				self.m.s3.inventory()[i]().selected(false);
			}

		};


	//----------------------------------------------------------------------------------//
	// 			        		 			                                                          //
	// 			 s4. Функционал модели генератора мобильных аутентификационных кодов   			//
	// 			         					                                                          //
	//----------------------------------------------------------------------------------//
	f.s4 = {};

		//-------------------------------------------------------------------//
		// s4.1. Обновить код мобильного аутентификатора для выбранного бота //
		//-------------------------------------------------------------------//
		f.s4.update = function(){

			// 1] Если id выбранного бота пуст, завершить
			if(!self.m.s2.edit.id())
				return;

			// 2]
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C5_bot_get_mobile_code",
				from: 		    "f.s4.update",
			  data: 		    {
					id:               self.m.s2.edit.id()
				},
			  prejob:       function(config, data, event){

					// Уменьшить счёрчик ajax-запросов на 1
					self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Записать новый код
					//self.m.s4.code();

					console.log(data);

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});



		};


return f; }};




























