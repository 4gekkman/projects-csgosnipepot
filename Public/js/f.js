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
 *    f.s0.upd_price_update_errors  | s0.4. Обновить модель ошибок обновления цен на вещи
 *    f.s0.update_inventory_tp      | s0.5. Обновить инвентарь торгового партнёра
 *    f.s0.send_trade_offer         | s0.6. Отправить торговое предложение
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
 *    f.s2.authorize_bot            | s2.4. Авторизовать бота
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
 *    f.s4.copy   									| s4.2. Скопировать текущий код в буфер обмена
 *
 *  s5. Функционал модели торгового партнёра
 *
 *  	f.s5.update_tp                | s5.1. Обновить инфу о торговом партнёре по указанному торговому URL
 *
 *  s6. Функционал модели инвентаря торгового партнёра
 *
 *    f.s6.update 									| s6.1. Обновить инвентарь торгового партнёра
 *    f.s6.get_item_title           | s6.2. Формирует title для вещей в инвентаре
 *    f.s6.deselect_all             | s6.3. Развыделить все элементы в инвентаре
 *
 *
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

//			// 2. Применить к боксу с инвентарём perfect-scroll
//			(function(){
//
//				// 2.1. Получить ссылку на DOM-элемент
//				var dom = document.getElementsByClassName('inventory-container')[0];
//				if(!dom) return;
//
//				// 2.2. Если у dom нет класса ps-container
//				// - Тогда инициилизировать perfect-scroll на этом элементе
//				if(!checkClass('', 'ps-container', dom)) {
//					Ps.initialize(document.getElementsByClassName('inventory-container')[0], {
//						'wheelSpeed': .2
//					});
//				}
//
//				// 2.3. Иначе обновить
//				else {
//					Ps.update(dom);
//				}
//
//			})();

  	};


		//-----------------------------------------------------//
		// s0.4. Обновить модель ошибок обновления цен на вещи //
		//-----------------------------------------------------//
		// - Пояснение
		f.s0.upd_price_update_errors = function(data) {

			// 1] Обновить значение csgofast_last_bug
			if(data.csgofast_last_bug) self.m.s2.price_update_errors.csgofast_last_bug(data.csgofast_last_bug);

			// 2] Обновить значение steammarket_last_bug
			if(data.steammarket_last_bug) self.m.s2.price_update_errors.steammarket_last_bug(data.steammarket_last_bug);

 		};


		//---------------------------------------------//
		// s0.5. Обновить инвентарь торгового партнёра //
		//---------------------------------------------//
		// - Пояснение
		f.s0.update_inventory_tp = function(data) {

			// 1. Обновить m.s6.inventory

				// 1.1. Очистить
				self.m.s6.inventory.removeAll();

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
					self.m.s6.inventory.push(ko.observable(obj))

				}

//			// 2. Применить к боксу с инвентарём perfect-scroll
//			(function(){
//
//				// 2.1. Получить ссылку на DOM-элемент
//				var dom = document.getElementsByClassName('inventory-container')[0];
//				if(!dom) return;
//
//				// 2.2. Если у dom нет класса ps-container
//				// - Тогда инициилизировать perfect-scroll на этом элементе
//				if(!checkClass('', 'ps-container', dom)) {
//					Ps.initialize(document.getElementsByClassName('inventory-container')[0], {
//						'wheelSpeed': .2
//					});
//				}
//
//				// 2.3. Иначе обновить
//				else {
//					Ps.update(dom);
//				}
//
//			})();

  	};


		//--------------------------------------//
		// s0.6. Отправить торговое предложение //
		//--------------------------------------//
		f.s0.send_trade_offer	= function(what, from, data, event) {

			// 1] Если никакие вещи не выбраны для торговли, сообщить и завершить
			if(!self.m.s3.inventory_items2trade().length && !self.m.s6.inventory_items2trade().length) {
				notify({msg: "Choose some items to trade", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Запретить торговать с партнёрами, у которых escrow hold > 0
			if(+self.m.s5.escrow_days_partner() > 0) {
				notify({msg: "The partner's escrow hold days > 0. Trade canceled.", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 3] Проверить наличие необходимых данных
			if(!self.m.s2.edit.id()) {
				notify({msg: "Check partner's id", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}
			if(!self.m.s5.trade_url()) {
				notify({msg: "Check partner's trade url", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}
			if(!self.m.s5.steamid_partner()) {
				notify({msg: "Check partner's Steam ID", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}
			if(!self.m.s5.partner()) {
				notify({msg: "Check partner's partner ID", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}
			if(!self.m.s5.token()) {
				notify({msg: "Check partner's token", time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 4] Выполнить запрос
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C25_new_trade_offer",
				from: 		    "f.s0.send_trade_offer",
			  data: 		    {
					id_bot: 							self.m.s2.edit.id(),
					steamid_partner: 			self.m.s5.steamid_partner(),
					id_partner: 					self.m.s5.partner(),
					token_partner: 				self.m.s5.token(),
					dont_trade_with_gays: "1",
					assets2send: 					self.m.s3.inventory_items2trade(),
					assets2recieve: 			self.m.s6.inventory_items2trade(),
					tradeoffermessage: 		"Manual trade via dashboard."
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Тихо обновить инвентарь выбранного бота
					self.f.s3.update({silent: true});

					// 2] Сообщить, что новое торговое предложение успешно создано
					notify({msg: "New trade offer successfully created", time: 5, fontcolor: 'RGB(50,120,50)'});

				},
				ok_2: function(data, params){

					// 1] Сообщить об ошибке
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);

				},
				callback:     function(data, params){

					// 1] Подтвердить все торговые предложения выбранного бота
					ajaxko(self, {
						command: 	    "\\M8\\Commands\\C21_fetch_confirmations",
						from: 		    "f.s0.send_trade_offer",
						data: 		    {
							id_bot: 							self.m.s2.edit.id(),
							need_to_ids:          "0",
							just_fetch_info:      "0"
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){},
						ok_0:         function(data, params){

							// 1] Сообщить, что новое торговое предложение было подтверждено
							notify({msg: "New trade offer successfully approved", time: 5, fontcolor: 'RGB(50,120,50)'});

						},
						ok_2: function(data, params){

							// 1] Сообщить об ошибке
							notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
							console.log(data.data.errortext);

						},
						callback:     function(data, params){

							// 1] Тихо обновить инвентарь партнёра
							self.f.s6.update({silent: true});

						}
						//ajax_params:  {},
						//key: 			    "D1:1",
						//from_ex: 	    [],
						//ok_1:         function(data, params){},
						//error:        function(){},
						//timeout:      function(){},
						//timeout_sec:  200,
						//url:          window.location.href,
						//ajax_method:  "post",
						//ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
					});

				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

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
							self.f.s3.update({silence: true});

							// 1.2.3] Обновить инвентарь торгового партнёра
							// - Если таковой, конечно, уже выбран
							if(self.m.s5.steam_name_partner)
								self.f.s6.update({silence: true});

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
		f.s1.choose_subdoc = function(parameters, data, event) {

			// 1] Получить на основе parameters объекты группы и поддокумента

				// 1.1] Получить группу
				var group = (function(){

					// 1.1.1] Если self.m.s1.groups пуст, вернуть ''
					if(self.m.s1.groups().length == 0) return '';

					// 1.1.2] Если parameters.group пуста, вернуть 1-й эл-т из m.s1.groups
					if(!parameters.group) return self.m.s1.groups()[0]();

					// 1.1.3] Попробовать найти группу в индексе по имени
					var group = self.m.s1.indexes.groups_by_name[parameters.group];

					// 1.1.4] Если group пуста, вернуть 1-й эл-т из m.s1.groups
					if(!group) return self.m.s1.groups()[0]();

					// 1.1.5] Вернуть group
					return group;

				})();

				// 1.2] Получить поддокумент
				var subdoc = (function(){

					// 1.2.1] Если self.m.s1.subdocs пуст, вернуть ''
					if(self.m.s1.subdocs().length == 0) return '';

					// 1.2.2] Если parameters.subdoc пуста, вернуть 1-й эл-т из m.s1.subdocs
					if(!parameters.subdoc) return self.m.s1.subdocs()[0]();

					// 1.2.3] Попробовать найти поддокумент в индексе по имени
					var subdoc = self.m.s1.indexes.subdocs_by_name[parameters.subdoc];

					// 1.2.4] Если subdoc пуста, вернуть 1-й эл-т из m.s1.subdocs
					if(!subdoc) return self.m.s1.subdocs()[0]();

					// 1.2.5] Вернуть subdoc
					return subdoc;

				})();

			// 2] В зависимости от результатов выполнить ряд задач
			(function(){

				// 2.1] !group && !subdoc
				if(!group && !subdoc) {

					// 2.1.1] Сообщить об ошибке
					console.log("Ошибка! Наблюдаемый массив m.s1.groups не должен быть пуст!");
					console.log("Ошибка! Наблюдаемый массив m.s1.subdocs не должен быть пуст!");

					// 2.1.2] Завершить
					return;

				}

				// 2.2] !group && subdoc
				if(!group && subdoc) {

					// 2.2.1] Сообщить об ошибке
					console.log("Ошибка! Наблюдаемый массив m.s1.groups не должен быть пуст!");

					// 2.2.2] Завершить
					return;

				}

				// 2.3] group && !subdoc
				if(group && !subdoc) {

					// 2.3.1] Сообщить об ошибке
					console.log("Ошибка! Наблюдаемый массив m.s1.subdocs не должен быть пуст!");

					// 2.3.2] Завершить
					return;

				}

				// 2.4] group && subdoc
				if(group && subdoc) {

					// 2.4.1] Установить группу
					self.m.s1.selected_group(group);

					// 2.4.2] Установить поддокумент
					self.m.s1.selected_subdoc(subdoc);

					// 2.4.3] Добавить в историю новое состояние
					History.pushState({state:subdoc.id()}, subdoc.name(), subdoc.query());

					// Выполнить дополнительную работу


				}

			})();

			// 3] Если выбрана не группа поддокументов с именем "Bot"
			if(self.m.s1.selected_group().name() != 'Bot') {

				// 3.1] Очистить m.s3.inventory и m.s6.inventory
				self.m.s3.inventory.removeAll();
				self.m.s6.inventory.removeAll();

				// 3.2] Очистить m.s2.edit
				for(var key in self.m.s2.edit) {

					// Если свойство не своё, пропускаем
					if(!self.m.s2.edit.hasOwnProperty(key)) continue;

					// Добавим в obj свойство key
					self.m.s2.edit[key]("");

				}

			}

			// 4] Если выбрана группа поддокументов с именем "Bot"
			if(self.m.s1.selected_group().name() == 'Bot') {

				// 4.1] Обновить инвентарь по-тихому
				self.f.s3.update({silent: true});

			}

			// n] Выполнить update_all
			// - Но только если parameters.without_reload != "1"
			if(parameters.without_reload != "1")
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
			self.m.s2.edit.device_id(data.device_id());
			self.m.s2.edit.apikey(data.apikey());
			self.m.s2.edit.apikey_domain(data.apikey_domain());
			self.m.s2.edit.apikey_last_update(data.apikey_last_update());
			self.m.s2.edit.apikey_last_bug(data.apikey_last_bug());
			self.m.s2.edit.trade_url(data.trade_url());
			self.m.s2.edit.avatar_steam(data.avatar_steam());

			self.m.s2.edit.id(data.id());
			self.m.s2.edit.ison_incoming(data.ison_incoming());
			self.m.s2.edit.ison_outcoming(data.ison_outcoming());

			self.m.s2.edit.steam_name(data.steam_name());

			self.m.s2.edit.authorization(data.authorization());
			self.m.s2.edit.authorization_status_last_bug(data.authorization_status_last_bug());
			self.m.s2.edit.authorization_last_bug(data.authorization_last_bug());
			self.m.s2.edit.authorization_last_bug_code(data.authorization_last_bug_code());

			self.m.s2.edit.captchagid();
			self.m.s2.edit.captcha_text();

			// 2] Открыть поддокумент редактирования пользователя
			self.f.s1.choose_subdoc({group: 'bot', subdoc: 'properties'});

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
					device_id: 			  self.m.s2.edit.device_id(),
					apikey_domain:   	self.m.s2.edit.apikey_domain(),
					trade_url:   			self.m.s2.edit.trade_url(),
					avatar_steam:			self.m.s2.edit.avatar_steam(),

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

		//-------------------------//
		// s2.4. Авторизовать бота //
		//-------------------------//
		f.s2.authorize_bot = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C8_bot_login",
				from: 		    "f.s2.authorize_bot",
			  data: 		    {
					id_bot:           self.m.s2.edit.id(),
					relogin:          "1",
					captchagid:       "0",
					captcha_text:     "0",
					method:           "GET",
					cookies_domain:   "steamcommunity.com"
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пользователь был успешно авторизован
					notify({msg: 'The bot has been authorized', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Изменить св-ва авторизации бота
					self.m.s2.edit.authorization(1);
					self.m.s2.edit.authorization_last_bug("");
					self.m.s2.edit.authorization_last_bug_code("");
					self.m.s2.edit.captchagid("");
					self.m.s2.edit.captcha_text("");
					self.m.s2.indexes.bots[self.m.s2.edit.id()].authorization(1);
					self.m.s2.indexes.bots[self.m.s2.edit.id()].authorization_last_bug("");
					self.m.s2.indexes.bots[self.m.s2.edit.id()].authorization_last_bug_code("");
					self.m.s2.indexes.bots[self.m.s2.edit.id()].sessionid(data.data.sessionid);

				},
			  ok_2:         function(data, params){

					// 1] Сообщить, что авторизовать пользователя не удалось
					notify({msg: 'The bots authorization have failed', time: 10, fontcolor: 'RGB(200,50,50)'});

					// 2] Изменить св-ва авторизации бота
					self.m.s2.edit.captchagid(data.captchagid);
					self.m.s2.edit.authorization_last_bug_code(data.error_code);
					self.m.s2.edit.authorization_last_bug(data.data.errortext);
					self.m.s2.indexes.bots[self.m.s2.edit.id()].authorization_last_bug_code(data.error_code);
					self.m.s2.indexes.bots[self.m.s2.edit.id()].authorization_last_bug(data.data.errortext);

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
		f.s3.update = function(parameters, data, event) {

			// 1] Если steamid выбранного бота пуст, сообщить и завершить
			if(!self.m.s2.edit.steamid()) {
				notify({msg: 'Enter steamid of the bot', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Выполнить запрос
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C4_getinventory",
				from: 		    "f.s3.update",
			  data: 		    {
					steamid: 				  self.m.s2.edit.steamid()
				},
			  prejob:       function(config, data, event){

					// 1] Отметить, что идёт ajax-запрос
					self.m.s3.is_ajax_invoking(true);

					// 2] Очистить содержимое инвентаря
					self.m.s3.inventory.removeAll();

					// 3] Сообщить, что начинается запрос инвентаря
					// - Но только если parameters.silent != true
					if(parameters.silent != true)
						notify({msg: "Inventory updating...", time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Обновить инвентарь выбранного бота
					self.f.s0.update_inventory(data);

					// 2] Сообщить, что инвентарь бота был успешно отредактирован
					// - Но только если parameters.silent != true
					if(parameters.silent != true)
						notify({msg: "The bot's inventory successfully updated", time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Отметить, что ajax-запрос закончился
					self.m.s3.is_ajax_invoking(false);

				},
				ok_1: function(data, params){

					// 1] Отметить, что ajax-запрос закончился
					self.m.s3.is_ajax_invoking(false);

				},
				ok_2: function(data, params){

					// 1] Сообщить об ошибке
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);

					// 2] Отметить, что ajax-запрос закончился
					self.m.s3.is_ajax_invoking(false);

				},
				dont_touch_ajax_counter: true
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

			// 2] Запросить свежий код
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C5_bot_get_mobile_code",
				from: 		    "f.s4.update",
			  data: 		    {
					id_bot:       self.m.s2.edit.id()
				},
			  prejob:       function(config, data, event){

					// Отметить, что идёт ajax-запрос
					self.m.s4.is_ajax_invoking(true);

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Записать новый код
					self.m.s4.code(data.data.code);

					// 2] Записать, сколько секунд осталось до истечения текущего кода
					self.m.s4.expires_in_secs(data.data.expires_in_secs);

					// 3] Записать временную метку клиентского времени последнего получения кода
					self.m.s4.last_code_update_timestamp(Date.now());

					// 4] Отметить, что ajax-запрос закончился
					self.m.s4.is_ajax_invoking(false);

				},
			  ok_1:         function(data, params){

					// 1] Отметить, что ajax-запрос закончился
					self.m.s4.is_ajax_invoking(false);

				},
			  ok_2:         function(data, params){

					// 1] Сообщить об ошибке
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);

					// 2] Отметить, что ajax-запрос закончился
					self.m.s4.is_ajax_invoking(false);

				},
				dont_touch_ajax_counter: true
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

		//----------------------------------------------//
		// s4.2. Скопировать текущий код в буфер обмена //
		//----------------------------------------------//
		f.s4.copy = function(){

			var textArea = document.createElement("textarea");

			//
			// *** This styling is an extra step which is likely not required. ***
			//
			// Why is it here? To ensure:
			// 1. the element is able to have focus and selection.
			// 2. if element was to flash render it has minimal visual impact.
			// 3. less flakyness with selection and copying which **might** occur if
			//    the textarea element is not visible.
			//
			// The likelihood is the element won't even render, not even a flash,
			// so some of these are just precautions. However in IE the element
			// is visible whilst the popup box asking the user for permission for
			// the web page to copy to the clipboard.
			//

			// Place in top-left corner of screen regardless of scroll position.
			textArea.style.position = 'fixed';
			textArea.style.top = 0;
			textArea.style.left = 0;

			// Ensure it has a small width and height. Setting to 1px / 1em
			// doesn't work as this gives a negative w/h on some browsers.
			textArea.style.width = '2em';
			textArea.style.height = '2em';

			// We don't need padding, reducing the size if it does flash render.
			textArea.style.padding = 0;

			// Clean up any borders.
			textArea.style.border = 'none';
			textArea.style.outline = 'none';
			textArea.style.boxShadow = 'none';

			// Avoid flash of white box if rendered for any reason.
			textArea.style.background = 'transparent';


			textArea.value = self.m.s4.code();

			document.body.appendChild(textArea);

			textArea.select();

			try {
				var successful = document.execCommand('copy');
				var msg = successful ? 'successful' : 'unsuccessful';
				notify({msg: "The code has copied to the clipboard!", time: 5, fontcolor: 'RGB(50,120,50)'});
			} catch (err) {
				notify({msg: "Can't copy the code to the clipboard...", time: 5, fontcolor: 'RGB(200,50,50)'});
			}

			document.body.removeChild(textArea);

		};


	//--------------------------------------------------------//
	// 			        		 			                                //
	// 			 s5. Функционал модели торгового партнёра   			//
	// 			         					                                //
	//--------------------------------------------------------//
	f.s5 = {};

		//---------------------------------------------------------------------//
		// s5.1. Обновить инфу о торговом партнёре по указанному торговому URL //
		//---------------------------------------------------------------------//
		f.s5.update_tp = function(){

			// 1] Если trade_url выбранного бота пуст, сообщить и завершить
			if(!self.m.s5.trade_url()) {
				notify({msg: "Enter partner's trade url first", time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Получить "Partner ID" и "Token" из m.s5.trade_url
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C26_get_partner_and_token_from_trade_url",
				from: 		    "f.s5.update_tp",
			  data: 		    {
					trade_url: 				self.m.s5.trade_url()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Обновить инфу о торговом партнёре
					ajaxko(self, {
						command: 	    "\\M8\\Commands\\C30_get_steamname_and_steamid_by_tradeurl",
						from: 		    "f.s5.update_tp",
						data: 		    {
							id_bot: 				  self.m.s2.edit.id(),
							partner:          data.data.partner,
							token:            data.data.token
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){},
						ok_0:         function(data, params){

							// 1] Сохранить полученные данные в модель партнёра
							self.m.s5.steam_name_partner(data.data.steam_name_partner);
							self.m.s5.steamid_partner(data.data.steamid_partner);
							self.m.s5.partner(data.data.partner);
							self.m.s5.token(data.data.token);
							self.m.s5.escrow_days_partner(data.data.escrow_days_partner);
							self.m.s5.avatar(data.data.avatar);

							// 2] Обновить инвентарь торгового партнёра
							self.f.s6.update({silent: false});

							// n] Сообщить, что торговый партнёр по указанному торговому URL найден
							notify({msg: "Trade partner has been found", time: 5, fontcolor: 'RGB(50,120,50)'});

						},
						ok_1: function(data, params){},
						ok_2: function(data, params){

							notify({msg: "The entered trade url is not correct", time: 10, fontcolor: 'RGB(200,50,50)'});
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

					// n] Сообщить, что торговый партнёр по указанному торговому URL найден
					notify({msg: "Trade partner has been found", time: 5, fontcolor: 'RGB(50,120,50)'});

				},
				ok_2: function(data, params){

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
	// 			 s6. Функционал модели инвентаря торгового партнёра			//
	// 			         					                                      //
	//--------------------------------------------------------------//
	f.s6 = {};

		//---------------------------------------------//
		// s6.1. Обновить инвентарь торгового партнёра //
		//---------------------------------------------//
		f.s6.update = function(parameters, data, event) {

			// 1] Если steamid торгового партнёра пуст, сообщить и завершить
			if(!self.m.s5.steamid_partner()) {
				notify({msg: 'Enter and check trade url of a trade partner', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Выполнить запрос
			ajaxko(self, {
			  command: 	    "\\M8\\Commands\\C4_getinventory",
				from: 		    "f.s6.update",
			  data: 		    {
					steamid: 				  self.m.s5.steamid_partner()
				},
			  prejob:       function(config, data, event){

					// 1] Отметить, что идёт ajax-запрос
					self.m.s6.is_ajax_invoking(true);

					// 2] Очистить содержимое инвентаря
					self.m.s6.inventory.removeAll();

					// 3] Сообщить, что начинается запрос инвентаря
					// - Но только если parameters.silent != true
					if(parameters.silent != true)
						notify({msg: "Inventory updating...", time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Обновить инвентарь торгового партнёра
					self.f.s0.update_inventory_tp(data);

					// 2] Сообщить, что инвентарь партнёра был успешно отредактирован
					// - Но только если parameters.silent != true
					if(parameters.silent != true)
						notify({msg: "The partner's inventory successfully updated", time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Отметить, что ajax-запрос закончился
					self.m.s6.is_ajax_invoking(false);

				},
				ok_1: function(data, params){

					// 1] Отметить, что ajax-запрос закончился
					self.m.s6.is_ajax_invoking(false);

				},
				ok_2: function(data, params){

					// 1] Сообщить об ошибке
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);

					// 2] Отметить, что ajax-запрос закончился
					self.m.s6.is_ajax_invoking(false);

				},
				dont_touch_ajax_counter: true
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
		// s6.2. Формирует title для вещей в инвентаре //
		//---------------------------------------------//
		f.s6.get_item_title = function(data) {

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
		// s6.3. Развыделить все элементы в инвентаре //
		//--------------------------------------------//
		f.s6.deselect_all = function(data) {

			for(var i=0; i<self.m.s6.inventory().length; i++) {
				self.m.s6.inventory()[i]().selected(false);
			}

		};






return f; }};




























