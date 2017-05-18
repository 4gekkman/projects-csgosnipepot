/*//================================================////
////																							  ////
////   j.js - функционал модели шаблона документа		////
////																								////
////================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *  s0. Функционал, доступный всему остальному функционалу
 *
 *		f.s0.sm_func									| s0.1. Показывает модальное окно с text, заголовком "ошибка" и кнопкой "закрыть"
 *		f.s0.txt_delay_save           | s0.2. Функционал "механизма отложенного сохранения для текстовых полей"
 *    f.s0.logout                   | s0.3. Выйти из своей учётной записи
 *    f.s0.update_messages          | s0.4. Обновить модель чата данными с сервера
 *
 *  s2. Функционал модели механики левого сайдбара (с главным меню)
 *
 *    f.s2.switch 									| s2.1. Изменить состояние раскрытости левого меню
 *    f.s2.scroll                   | s2.2. Управление скроллом левого меню
 *
 *  s3. Функционал модели механики правого сайдбара (с чатом)
 *
 *    f.s3.switch 									| s3.1. Изменить состояние раскрытости правого сайдбара
 *
 *  s5. Функционал модели чата
 *
 *    f.s5.post_to_the_chat_main    | s5.1. Запостить сообщение в главную комнату чата
 *    f.s5.add_incoming_msg         | s5.2. Добавить в чат новое, поступившее по websocket сообщение
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
var LayoutModelFunctions = { constructor: function(self) { var f = this;


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s0. Функционал, доступный всему остальному функционалу 			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s0 = {};

		//----------------------------------------------------------------------------------//
		// s0.1. Показывает модальное окно с text, заголовком "ошибка" и кнопкой "закрыть"  //
		//----------------------------------------------------------------------------------//
		// - Применяется обычно при ajax-запросах, для при обработке серверных ошибок
		f.s0.sm_func = function(text) {
			return showModal({
				header: 'Ошибка',
				ok_name: 'Закрыть',
				cancel_name: '',
				width: 350,
				standard_css: '1',
				target: document.body,
				html: text,
				params: {},
				callback: function(arg, params){
					if(arg !== null) {}
					else {}
				}
			});
		};

		//-------------------------------------------------------------------------//
		// s0.2. Функционал "механизма отложенного сохранения для текстовых полей" //
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

		//-------------------------------------//
		// s0.3. Выйти из своей учётной записи //
		//-------------------------------------//
		f.s0.logout = function(data, event){

			// 1] Отправить запрос
			ajaxko(self, {
				url:          window.location.protocol + '//' + window.location.host + "/layouts/l10004",
				key: 	    		"L10004:1",
				from: 		    "ajaxko",
				data: 		    {},
				prejob:       function(config, data, event){

					// 1] Сообщить, что идёт сохранение нового trade url
					toastr.info("Подожди немного...", "Произвожу выход из твоего аккаунта", {
						timeOut: 					"9999999999999",
						extendedTimeOut: 	"9999999999999"
					});

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					toastr.info("Перезагружаю документ...", "Успешный выход из аккаунта!", {
						timeOut: 					"9999999999999",
						extendedTimeOut: 	"9999999999999"
					});

					// 2] Перезагрузить страницу
          window.location.reload(true);

				}
			});

		};

		//----------------------------------------------//
		// s0.4. Обновить модель чата данными с сервера //
		//----------------------------------------------//
		f.s0.update_messages = function(data) {

			// 1. Обновить m.s5.messages

				// 1.1. Очистить
				self.m.s5.messages.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.messages.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.messages[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.messages[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.messages[i][key]);

					}

					// 1.2.2. Добавить этот объект в подготовленный массив
					self.m.s5.messages.push(ko.observable(obj))

				}

			// 2. Прокрутить чат до конца вниз
			setTimeout(function(){

				// 2.1. Получить контейнер чата
				var container = document.getElementsByClassName('chat-messages')[0];

				// 2.2. Получить вертикальный размер прокрутки контейнера
				var scrollHeight = container.scrollHeight;

				// 2.3. Прокретить container в конец
				container.scrollTop = scrollHeight;
				Ps.update(container);

			}, 100);

		};


	//----------------------------------------------------------------------------//
	// 			        		 			                                                    //
	// 			 s2. Функционал модели механики левого сайдбара (с главным меню) 			//
	// 			         					                                                    //
	//----------------------------------------------------------------------------//
	f.s2 = {};

		//--------------------------------------------------//
		// s2.1. Изменить состояние раскрытости левого меню //
		//--------------------------------------------------//
		f.s2.switch = function(data, event) {

			// 1] Изменить состояние раскрытости на противоположное
			self.m.s2.expanded(!self.m.s2.expanded());

			// 2] При ширине экрана от 1280 до 1456 включительно
			// - Скрыть правый сайдбар с чатом.
			if(self.m.s0.cur_browser_width() >= 1280 && self.m.s0.cur_browser_width() <= 1456) {
				self.m.s3.expanded(false);
			}

		};

		//---------------------------------------//
		// s2.2. Управление скроллом левого меню //
		//---------------------------------------//
		f.s2.scroll = function(event, params) {

			// 1] Получить текущее значение прокрутки
			var scrolled = window.pageYOffset || document.documentElement.scrollTop;

			// 2] Получить текущую высоту контента меню
			var menucontent_height = (function(){

				// 2.1] Подготовить переменную для результата
				var height = 0;

				// 2.2] Добавить высоту всех пунктов меню
				height = height + getBoundingDocRect(document.getElementsByClassName('items')[0]).height;

				// 2.3] Добавить высоту кнопки-переключателя меню, если она отображается
				if(!self.m.s2.hidden())
					height = height + getBoundingDocRect(document.getElementsByClassName('toggle')[0]).height;

				// 2.4] Добавить высоту счётчика посетителей
				height = height + getBoundingDocRect(document.getElementsByClassName('users-counter')[0]).height;

				// 2.n] Вернуть результат
				return height;

			})();

			// 3] Получить текущую высоту DOM-элемента меню
			var menuheight = getBrowserWindowMetrics().height - self.m.s2.topStart;

			// 4] Если прокрутка не требуется, вернуть стартовое значение top для меню
			if(menuheight >= menucontent_height)
				self.m.s2.top(self.m.s2.topStart);

			// 5] А если требуется, то:
			else {

				// Назначить новое значение св-ва top для меню
				self.m.s2.top((function(){

					// 5.1] Получить MAX возможную прокрутку меню
					var maxscroll = menucontent_height - menuheight;

					// 5.2] Получить MIN значения для параметра top у меню
					var mintop = self.m.s2.topStart - maxscroll;

					// 5.3] Получить итоговое значение, на которое надо прокрутить
					var finalscroll = (function(){

						// Получить разницу между предыдущей и текущей прокрутками
						// - Если он положительная, значит прокрутка вниз. Иначе - вверх.
						var diff = self.m.s0.prev_browser_scroll() - scrolled;

						// Вычислить предположительное значение следующей прокрутки
						var futuretop = self.m.s2.top() + diff;

						// Вернуть подходящий futurescroll
						if(futuretop >= self.m.s2.topStart) return self.m.s2.topStart;
						if(futuretop <= mintop) return mintop;
						return futuretop;

					})();

					// 5.4] Осуществить прокрутку
					return finalscroll;

				})());

			}

			// n] Записать последнее известное значение scrolled
			self.m.s0.prev_browser_scroll(scrolled);

		};


	//----------------------------------------------------------------------//
	// 			        		 			                                              //
	// 			 s3. Функционал модели механики правого сайдбара (с чатом) 			//
	// 			         					                                              //
	//----------------------------------------------------------------------//
	f.s3 = {};

		//--------------------------------------------------//
		// s3.1. Изменить состояние раскрытости левого меню //
		//--------------------------------------------------//
		f.s3.switch = function(data, event) {

			// 1] Изменить состояние раскрытости на противоположное
			self.m.s3.expanded(!self.m.s3.expanded());

			// 2] При ширине экрана от 1280 до 1456 включительно
			// - Скрыть левый сайдбар с главным меню.
			if(self.m.s0.cur_browser_width() >= 1280 && self.m.s0.cur_browser_width() <= 1456) {
				self.m.s2.expanded(false);
			}

		};


	//----------------------------------------//
	// 			        		 			                //
	// 			 s5. Функционал модели чата 			//
	// 			         					                //
	//----------------------------------------//
	f.s5 = {};

		//--------------------------------------------------//
		// s5.1. Запостить сообщение в главную комнату чата //
		//--------------------------------------------------//
		// - Пояснение
		f.s5.post_to_the_chat_main = function(data, event) {

			// 1] Если нажата клавиша Enter
			if(event.keyCode == 13 || event.type != 'keypress') {

				// 1.1] Если поле для ввода сообщений пусто, завершить
				if(!self.m.s5.new_message()) return;

				// 1.2] Проверить, не длиннее ли сообщение лимита max_msg_length
				if(self.m.s5.new_message().length > self.m.s5.max_msg_length()) {

					// Сообщить
					notify({msg: 'Too long message. Max = '+self.m.s5.max_msg_length()+' symbols.', time: 5, fontcolor: 'RGB(200,50,50)'});

					// Завершить
					return;

				}

				// 1.3] Отправить сообщение на сервер
				ajaxko(self, {
					url:          window.location.protocol + '//' + window.location.host + "/layouts/l10004",
					key: 	    		"L10004:2",
					from: 		    "ajaxko",
					data: 		    {
						message: self.m.s5.new_message()
					},
					prejob:       function(config, data, event){},
					postjob:      function(data, params){},
					ok_0:         function(data, params){},
					ok_2:         function(data, params){

						// Сообщить об ошибке
						notify({msg: data.data.errormsg, time: 5, fontcolor: 'RGB(200,50,50)'});

					}
				});

				// 1.n] Очистить поле для ввода сообщений
				self.m.s5.new_message('');

			}

			// 2] Если нажата НЕ клавиша Enter
			else {

				// 2.1] Если превышен лимит на длину сообщения, принять меры
				if(self.m.s5.new_message().length >= self.m.s5.max_msg_length()) {

					// Обрезать сообщение по max длине
					self.m.s5.new_message(self.m.s5.new_message().substring(0, self.m.s5.max_msg_length()));

					// Запретить действие браузера по умолчанию, если был введён символ
					if(event.which !== 0)
						return false;

				}

			}

			// n] Разрешить действие браузера по умолчанию
			return true;

		};

		//----------------------------------------------------------------//
		// s5.2. Добавить в чат новое, поступившее по websocket сообщение //
		//----------------------------------------------------------------//
		f.s5.add_incoming_msg = function(message) {

			// 1] Сформировать объект для добавления
			var obj = {};
			for(var key in message) {

				// Если свойство не своё, пропускаем
				if(!message.hasOwnProperty(key)) continue;

				// Добавим в obj свойство key
				obj[key] = ko.observable(message[key]);

			}

			// 2] Добавить obj в m.s5.messages
			self.m.s5.messages.push(obj);

			// 3] Если длина m.s5.messages больше разрешённой
			// - Удалить 1-й элемент из массива
			if(self.m.s5.messages().length > self.m.s5.max_messages())
				self.m.s5.messages.shift();

		};


return f; }};



























