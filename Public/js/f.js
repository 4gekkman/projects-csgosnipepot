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
 *    f.s0.update_rooms             | s0.2. Обновить модель комнат на основе переданных данных
 *    f.s0.update_all               | s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера
 *
 *  s1. Функционал модели управления поддокументами приложения
 *
 *		f.s1.choose_subdoc            | s1.1. Выбрать subdoc с указанным id
 *
 *  s2. Функционал игровых комнат
 *
 *    f.s2.sortfunc                 | s2.1. Функция для сортировки списка ботов
 *    f.s2.create_new_room          | s2.2. Создать новую комнату
 *    f.s2.show_rooms_interface     | s2.3. Открыть интерфейс кликнутой комнаты
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


		//----------------------------------------------------------//
		// s0.2. Обновить модель комнат на основе переданных данных //
		//----------------------------------------------------------//
		// - Пояснение
		f.s0.update_rooms = function(data) {

			// 1. Обновить self.m.s2.rooms

				// 1.1. Очистить
				self.m.s2.rooms.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.rooms.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.rooms[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.rooms[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.rooms[i][key]);

					}

					// 1.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1);

					// 1.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 1.2.4. Добавить этот объект в подготовленный массив
					self.m.s2.rooms.push(ko.observable(obj))

				}

			// 2. Обновить m.s2.rooms_total
			self.m.s2.rooms_total(data.rooms_total);

		};


		//------------------------------------------------------------------------//
		// s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера //
		//------------------------------------------------------------------------//
		// - Пояснение
		f.s0.update_all = function(what, from, data, event) {

			// 1] Подготовить объект с функциями-обновлялками моделей документа
			var update_funcs = {

				// 1.1] Обновлялка комнат
				rooms: function(){
					ajaxko(self, {
						command: 	    "\\M9\\Commands\\C1_rooms",
						from: 		    "f.s0.update_all",
						data: 		    {

						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счётчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель комнат на основе полученных данных
							self.f.s0.update_rooms(data.data);

						},
						callback:			function(){



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

		//-------------------------------------//
		// s1.1. Выбрать subdoc с указанным id //
		//-------------------------------------//
		// - Пояснение
		f.s1.choose_subdoc = function(parameters, data, event) {

			// 1] Получить на основе parameters объекты группы и поддокумента

				// 1.1] Получить группу
				var group = (function(){

					// 1.1.1] Если это первый вход в документ, то:
					// - Использовать group с id = 1
					if(parameters.first)
						return self.m.s1.groups()[0]();

					// 1.1.2] Если self.m.s1.groups пуст, вернуть ''
					if(self.m.s1.groups().length == 0) return '';

					// 1.1.3] Если parameters.group пуста, вернуть 1-й эл-т из m.s1.groups
					if(!parameters.group) return self.m.s1.groups()[0]();

					// 1.1.4] Попробовать найти группу в индексе по имени
					var group = self.m.s1.indexes.groups_by_name[parameters.group];

					// 1.1.5] Если group пуста, вернуть 1-й эл-т из m.s1.groups
					if(!group) return self.m.s1.groups()[0]();

					// 1.1.6] Вернуть group
					return group;

				})();

				// 1.2] Получить поддокумент
				var subdoc = (function(){

					// 1.2.1] Если это первый вход в документ, то:
					// - Использовать subdoc с id = 1
					if(parameters.first)
						return self.m.s1.subdocs()[0]();

					// 1.2.2] Если self.m.s1.subdocs пуст, вернуть ''
					if(self.m.s1.subdocs().length == 0) return '';

					// 1.2.3] Если parameters.subdoc пуста, вернуть 1-й эл-т из m.s1.subdocs
					if(!parameters.subdoc) return self.m.s1.subdocs()[0]();

					// 1.2.4] Попробовать найти поддокумент в индексе по имени
					var subdoc = self.m.s1.indexes.subdocs_by_name[parameters.subdoc];

					// 1.2.5] Если subdoc пуста, вернуть 1-й эл-т из m.s1.subdocs
					if(!subdoc) return self.m.s1.subdocs()[0]();

					// 1.2.6] Вернуть subdoc
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

					// 2.4.3] Если это первый вход в документ, то:
					// - Подменить текущее состояние, а не добавлять новое.
					// - Назначить состояние с id = 1
					if(parameters.first) {

						// Подменить текущее состояние на новое
						History.replaceState({state:self.m.s1.subdocs()[0]().id()}, self.m.s1.subdocs()[0]().name(), self.m.s1.subdocs()[0]().query());

						// Установить

					}

					// 2.3.4] Если это не первый вход в документ
					else {

						// Добавить в историю новое состояние
						History.pushState({state:subdoc.id()}, subdoc.name(), subdoc.query());

					}

					// 2.3.5] Если group.id = 2
					if(group.id() == 2) {

//						// Переключить блок радио-кнопок на опцию №1 (Incoming)
//						self.m.s7.types.choosen('1');
//
//						// Обновить входящие торговые операции бота
//						self.f.s7.update({silent: true});
//
//						// Очистить наблюдаемые массивы с торговыми операциями
//						self.m.s7.tradeoffers_incoming.removeAll();
//						self.m.s7.tradeoffers_incoming_history.removeAll();
//						self.m.s7.tradeoffers_sent.removeAll();
//						self.m.s7.tradeoffers_sent_history.removeAll();

					}

				}

			})();

			// 3] Если выбрана не группа поддокументов с именем "Room"
			if(self.m.s1.selected_group().name() != 'Room') {

//				// 3.1] Очистить m.s3.inventory и m.s6.inventory
//				self.m.s3.inventory.removeAll();
//				self.m.s6.inventory.removeAll();
//
//				// 3.2] Очистить m.s2.edit
//				for(var key in self.m.s2.edit) {
//
//					// Если свойство не своё, пропускаем
//					if(!self.m.s2.edit.hasOwnProperty(key)) continue;
//
//					// Добавим в obj свойство key
//					self.m.s2.edit[key]("");
//
//				}

			}

			// 4] Если выбрана группа поддокументов с именем "Room"
			if(self.m.s1.selected_group().name() == 'Room') {

//				// 4.1] Обновить инвентарь по-тихому
//				self.f.s3.update({silent: true});

			}

			// n] Выполнить update_all
			// - Но только если parameters.without_reload != "1"
			if(parameters.without_reload != "1")
				self.f.s0.update_all([], 'subdocs:choose_subdoc', '', '');

		};


	//------------------------------------------//
	// 			        		 			                  //
	// 			 s2. Функционал игровых комнат 			//
	// 			         					                  //
	//------------------------------------------//
	f.s2 = {};

		//-------------------------------------------//
		// s2.1. Функция для сортировки списка ботов //
		//-------------------------------------------//
		f.s2.sortfunc = function(data, event) {

			// 1] Если выбрана сортировка по ID
			if(self.m.s2.sortrooms.choosen().value() == 1) {
				self.m.s2.rooms.sort(function(left, right){
					return left().id() >= right().id();
				});
			}

		};

		//-----------------------------//
		// s2.2. Создать новую комнату //
		//-----------------------------//
		f.s2.create_new_room = function(data, event) {

			// 1] Если поле с именем комнаты пусто, сообщить и завершить
			if(!self.m.s2.newroom.name()) {
				notify({msg: 'Enter name for a new room', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Осуществить запрос
			ajaxko(self, {
			  command: 	    "\\M9\\Commands\\C2_create_new_room",
				from: 		    "f.s2.create_new_room",
			  data: 		    {
					name:           self.m.s2.newroom.name()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что новый бот был успешно добавлен
					notify({msg: 'A new room has been successfully added', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Обновить ботов
					self.f.s0.update_all([], 'f.s2.create_new_room', '', '');

					// 3] Очистить поле ввода steamid
					self.m.s2.newroom.name('');

				},
			  ok_2:         function(data, params){

					// 1] Сообщить, что авторизовать пользователя не удалось
					notify({msg: 'Could not create a new room', time: 10, fontcolor: 'RGB(200,50,50)'});

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

		//-------------------------------------------//
		// s2.3. Открыть интерфейс кликнутой комнаты //
		//-------------------------------------------//
		f.s2.show_rooms_interface = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемой комнаты
			self.m.s2.edit.id(data.id());

			self.m.s2.edit.is_on(data.is_on());
			self.m.s2.edit.bet_accepting_mode(data.bet_accepting_mode());
			self.m.s2.edit.name(data.name());
			self.m.s2.edit.room_round_duration_sec(data.room_round_duration_sec());
			self.m.s2.edit.max_items_per_bet(data.max_items_per_bet());
			self.m.s2.edit.max_items_per_round(data.max_items_per_round());
			self.m.s2.edit.min_items_per_bet(data.min_items_per_bet());
			self.m.s2.edit.min_items_per_round(data.min_items_per_round());
			self.m.s2.edit.min_bet(data.min_bet());
			self.m.s2.edit.max_bet(data.max_bet());
			self.m.s2.edit.min_bet_round(data.min_bet_round());
			self.m.s2.edit.max_bet_round(data.max_bet_round());
			self.m.s2.edit.allow_unstable_prices(data.allow_unstable_prices());
			self.m.s2.edit.fee_percents(data.fee_percents());
			self.m.s2.edit.change(data.change());
			self.m.s2.edit.one_bot_payout(data.one_bot_payout());
			self.m.s2.edit.payout_limit_min(data.payout_limit_min());

			// 2] Загрузить данные для allow_only_types
			var allow_only_types = JSON.parse(data.allow_only_types());
			for(var i=0; i<allow_only_types.length; i++) {
				self.m.s2.edit.allow_only_types[allow_only_types[i]](true);
			}

			// 3] Открыть поддокумент редактирования пользователя
			self.f.s1.choose_subdoc({group: 'room', subdoc: 'properties'});

		};


		//---------------------------------------------------//
		// s2.4. Функция для сброса фильтров по списку ботов //
		//---------------------------------------------------//
		f.s2.reset_filters = function(data, event) {

			self.m.s2.filterrooms.mode.roll(true);
			self.m.s2.filterrooms.mode.availability(true);

			self.m.s2.filterrooms.status.enabled(true);
			self.m.s2.filterrooms.status.disabled(true);

			self.m.s2.filterrooms.change.enabled(true);
			self.m.s2.filterrooms.change.disabled(true);

			self.m.s2.filterrooms.onebotpayout.enabled(true);
			self.m.s2.filterrooms.onebotpayout.disabled(true);

		};


return f; }};




























