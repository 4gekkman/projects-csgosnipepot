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
 *
 *  s1. Функционал модели управления поддокументами приложения
 *
 *		f.s1.choose_subdoc            | s1.1. Выбрать subdoc с указанным id
 *
 *  s2. Функционал игровых комнат
 *
 *    f.s2.sortfunc                 | s2.1. Функция для сортировки списка ботов
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
			if(self.m.s1.selected_group().name() == 'Bot') {

//				// 4.1] Обновить инвентарь по-тихому
//				self.f.s3.update({silent: true});

			}

			// n] Выполнить update_all
			// - Но только если parameters.without_reload != "1"
//			if(parameters.without_reload != "1")
//				self.f.s0.update_all([], 'subdocs:choose_subdoc', '', '');

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






return f; }};




























