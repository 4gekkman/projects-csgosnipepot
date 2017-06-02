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
 *    f.s0.sound                    | s0.5. Проиграть указанный аудио-файл
 *    f.s0.update_balance           | s0.6. Обрабатывает входящие обновления баланса из websocket текущего аутентиф.пользователя
 *    f.s0.localize 								| s0.7. Возвращает по ключу строку согласно выбранной локали
 *
 *  s1. Функционал модели управления поддокументами приложения
 *
 *		f.s1.choose_subdoc            | s1.1. Выбрать subdoc с указанным URI
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
 *  s4. Функционал модель по управлению звуком
 *
 *    f.s4.switch                   | s4.1. Изменить состояние раскрытости левого меню
 *
 *  s5. Функционал модели чата
 *
 *    f.s5.post_to_the_chat_main    | s5.1. Запостить сообщение в главную комнату чата
 *    f.s5.add_incoming_msg         | s5.2. Добавить в чат новое, поступившее по websocket сообщение
 *    f.s5.ban_open_interface       | s5.3. Открыть интерфейс бана
 *    f.s5.ban                      | s5.4. Забанить указанного пользователя
 *    f.s5.ban_user_handle          | s5.5. Обработка бана пользователя
 *
 *  s6. Функционал для работы с классической игрой
 *
 *    f.s6.notify_animate           | s6.1. Запустить анимацию уведомления о ставке в пункте меню Classic game
 *
 *  s7. Функционал для работы с popup-окном выбора способа депозита
 *
 *    f.s7.close_popup              | s7.1. Закрыть popup-окно выбора депозита
 *    f.s7.kopeyky                  | s7.2. За "каждые/каждую" N копеек/копейку
 *
 *  s9. Функционал виджета выбора языка
 *
 *    f.s9.choose_lang              | s9.1. Выбрать язык и записать выбор в куки
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
				url:          window.location.protocol + '//' + window.location.host + "/layouts/l10003",
				key: 	    		"L10003:1",
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

		//--------------------------------------//
		// s0.5. Проиграть указанный аудио-файл //
		//--------------------------------------//
		/*
		/* Описание:
		 * 	Sound takes three arguments. The url of the sound, the volume (from 0 to 100), and the loop (true to loop, false not to loop).
		 * 	stop allow to start after (contrary to remove).
		 * 	init re-set the argument volume and loop.
		 *
		 * Пример:
		 * 	var foo=new Sound("url",100,true);
		 * 	foo.start();
		 * 	foo.stop();
		 * 	foo.start();
		 * 	foo.init(100,false);
		 * 	foo.remove();
		 * 	//Here you you cannot start foo any more
		 *
		 */
		f.s0.sound = function(source, volume, loop) {

			this.source=source;
			this.volume=volume;
			this.loop=loop;
			var son;
			this.son=son;
			this.finish=false;
			this.stop=function()
			{
					document.body.removeChild(this.son);
			}
			this.start=function()
			{
					if(this.finish)return false;
					this.son=document.createElement("embed");
					this.son.setAttribute("src",this.source);
					this.son.setAttribute("hidden","true");
					this.son.setAttribute("volume",this.volume);
					this.son.setAttribute("autostart","true");
					this.son.setAttribute("loop",this.loop);
					document.body.appendChild(this.son);
			}
			this.remove=function()
			{
					document.body.removeChild(this.son);
					this.finish=true;
			}
			this.init=function(volume,loop)
			{
					this.finish=false;
					this.volume=volume;
					this.loop=loop;
			}

		};

		//--------------------------------------------------------------------------------------------//
		// s0.6. Обрабатывает входящие обновления баланса из websocket текущего аутентиф.пользователя //
		//--------------------------------------------------------------------------------------------//
		f.s0.update_balance = function(data) {

			// 1] Обновить баланс
			self.m.s0.balance(data.balance);

			// 2] Сообщить об успешном изменении баланса
			// - В зависимости от типа операции.

				// 2.1] Увеличение баланса
				if(data.type == 1) {

					// Монету / Монеты / Монет
					var coins_str = (function(){

						var declension = declension_by_number(data.coins);
						if(declension == 1) return 'монету';
						if(declension == 2) return 'монеты';
						if(declension == 3) return 'монет';
						return 'монет';

					})();

					// Вывести сообщение
					toastr.success('Ваш баланс успешно пополнен на '+data.coins+' '+coins_str+'.');

				}

				// 2.2] Уменьшение баланса
				if(data.type == 2) {

					// Монета / Монеты / Монет
					var coins_str = (function(){

						var declension = declension_by_number(data.coins);
						if(declension == 1) return 'монета';
						if(declension == 2) return 'монеты';
						if(declension == 3) return 'монет';
						return 'монет';

					})();

					// Списана / Списаны
					var withdraw_str = (function(){

						var declension = declension_by_number(data.coins);
						if(declension == 1) return 'списана';
						if(declension == 2) return 'списаны';
						if(declension == 3) return 'списаны';
						return 'списаны';

					})();

					// Вывести сообщение
					toastr.success('С вашего баланса успешно '+withdraw_str+' '+data.coins+' '+coins_str+'.');

				}

		};

		//------------------------------------------------------------//
		// s0.7. Возвращает по ключу строку согласно выбранной локали //
		//------------------------------------------------------------//
		f.s0.localize = function(key) {

			return layout_data.data.localization_data[key];

		};





	//------------------------------------------------------------------------//
	// 			        		 			                                                //
	// 			 s1. Функционал модели управления поддокументами приложения 			//
	// 			         					                                                //
	//------------------------------------------------------------------------//
	f.s1 = {};

		//--------------------------------------//
		// s1.1. Выбрать subdoc с указанным URI //
		//--------------------------------------//
		f.s1.choose_subdoc = function(parameters, data, event) {

			// a] Если в parameters есть параметр redirect, и пользователь анонимный, то:
			// - Переадресовать по указанному адресу, и завершить
			if(parameters.redirect && self.m.s0.is_logged_in() == false) {

				window.location = parameters.redirect;
				return;

			}

			// б] Если в parameters есть not_clckbl == true, просто завершить
			if(parameters.not_clckbl) return;

			// 1] Сформировать целевой URI из parameters
			var uri = (function(){

				// 1.1] Если uri передан в параметрах, выбрать его
				if(parameters.uri)
					var result = parameters.uri;

				// 1.2] В противном случае
				else {
					result = '/';
					for(var i=0; i<parameters.parameters.length; i++) {
						result = result + parameters.parameters[i];
						if(i != parameters.parameters.length-1)
							result = result + '/';
					}
				}

				// 1.3] Вернуть результат
				return result;

			})();

			// 2] Получить по uri поддокумент из m.s1.subdocs
			// - Если там такого uri нет, взять первый из subdocs
			var subdoc = (function(){

				// 2.1] Попробовать найти поддокумент
				var subdoc = self.m.s1.indexes.subdocs[uri];

				// 2.2] Если subdoc не найден, взять первый из m.s1.subdocs
				if(!subdoc)
					subdoc = self.m.s1.subdocs()[0];

				// 2.n] Вернуть результат
				return subdoc;

			})();

			// 3] Если subdoc не найден, вернуть ошибку
			if(!subdoc) {

				// 3.1] Сообщить об ошибке
				console.log('Ошибка! Наблюдаемый массив с поддокументами пуст.');

				// 3.2] Завершить
				return;

			}

			// 4] Если это анон, а доступ к документу для анонов закрыт, переадресовать на anon_redir, и завершить
			if(!self.m.s0.is_logged_in() && (!subdoc.vis4anon() || !subdoc.access4anon())) {

				// 4.1] Переадресовать
				self.f.s1.choose_subdoc({
					uri: subdoc.anon_redir()
				});

				// 4.2] Завершить
				return;

			}

			// 5] В зависимости от URI предпринять доп.действия
			(function(){

				// 5.1] Если URI == '/top'
				if(uri == '/top') {

					// 5.1.1] Запросить TOP игроков с сервера, если он ещё не получен
					(function(){

						// Подготовить рекурсивную функцию
						var recur = function recur(){
							if(typeof model == 'undefined') {
								setTimeout(function(){
									recur();
								}, 10);
							}
							else
								model.f.s4.get_top();
						};

						// Запросить TOP игроков с сервера, если он ещё не получен
						setImmediate(function(){
							recur();
						});

					})();

				}

				// 5.2] Если URI == '/faq'
				if(uri == '/faq') {

					// 5.2.1] Запросить FAQ с сервера, если он ещё не получен
					(function(){

						// Подготовить рекурсивную функцию
						var recur = function recur(){
							if(typeof model == 'undefined') {
								setTimeout(function(){
									recur();
								}, 10);
							}
							else {

								// Если callback не передан
								if(!parameters.callback)
									model.f.s5.get_faq({is_initial: true, group: ""});

								// Если callback передан
								if(parameters.callback)
									model.f.s5.get_faq({is_initial: true, group: "", callback: parameters.callback});

							}
						};

						// Запросить TOP игроков с сервера, если он ещё не получен
						setImmediate(function(){
							recur();
						});

					})();

				}

				// 5.3] Если URI == '/'
				if(uri == '/') {

					// 5.3.1] Переключить вкладку внутри classic game на игру
					(function(){

						// Подготовить рекурсивную функцию
						var recur = function recur(){
							if(typeof model == 'undefined') {
								setTimeout(function(){
									recur();
								}, 10);
							}
							else
								model.f.s1.choose_tab('game');
						};

						// Переключить вкладку внутри classic game на игру
						setImmediate(function(){
							recur();
						});

					})();

				}

				// 5.4] Если URI == '/deposit'
				if(uri == '/deposit') {

					// 5.4.1] Запросить инвентарь пользователя с сервера, если он ещё не получен, без force
					(function(){

						// Подготовить рекурсивную функцию
						var recur = function recur(){
							if(typeof model == 'undefined') {
								setTimeout(function(){
									recur();
								}, 10);
							}
							else
								model.f.s6.update_inventory(false, false);
						};

						// Запросить TOP игроков с сервера, если он ещё не получен
						setImmediate(function(){
							recur();
						});

					})();

				}

				// 5.5] Если URI == '/shop'
				if(uri == '/shop') {

					// 5.5.1] Выполнить начальную загрузку товаров магазина, если они ранее ещё не загружены
					(function(){

						// Подготовить рекурсивную функцию
						var recur = function recur(){
							if(typeof model == 'undefined') {
								setTimeout(function(){
									recur();
								}, 10);
							}
							else
								model.f.s7.init_goods();
						};

						// Запросить TOP игроков с сервера, если он ещё не получен
						setImmediate(function(){
							recur();
						});

					})();

				}

			})();

			// 6] Выбрать поддокумент subdoc
			self.m.s1.selected_subdoc(subdoc);

			// 7] Прокрутить документ в самый верх
			// window.scrollTo(0, 0);

			// 8] Если это первый вызов subdoc
			if(parameters.first) {

				// Подменить текущее состояние, а не добавлять новое
				History.replaceState({state:subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + (layout_data.data.request.querystring ? '?' + layout_data.data.request.querystring : ""));  // document.getElementsByTagName("title")[0].innerHTML

			}

			// 9] Если это не первый вход в документ
			else {

				// 9.1] Определить значение для query string
				var querystring = (function(){

					// Включать URI в строку адреса, если uri == "/faq"
					if(subdoc.uri() == "/faq")
						return (layout_data.data.request.querystring ? '?' + layout_data.data.request.querystring : "");

					// Иначе, не включать
					else
						return "";

				})();

				// 9.n] Добавить в историю новое состояние
				History.pushState({state:subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + querystring);

			}

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

				// 2.5] Добавить высоту блока иконок соц.сетей
				height = height + getBoundingDocRect(document.getElementsByClassName('social-icons')[0]).height;

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


	//--------------------------------------------------------//
	// 			        		 			                                //
	// 			 s4. Функционал модель по управлению звуком 			//
	// 			         					                                //
	//--------------------------------------------------------//
	f.s4 = {};

		//-------------------------------------//
		// s4.1. Переключить выключатель звука //
		//-------------------------------------//
		f.s4.switch = function(data, event) {

			// 1] Изменить состоятие выключателя звука на противоположное
			self.m.s4.is_global_volume_on(!self.m.s4.is_global_volume_on());

			// 2] Отправить на сервер ajax-запрос с pinned и expanded для сохранения в куки
			ajaxko(self, {
				key: 	    		"D10009:2",
				from: 		    "ajaxko",
				data: 		    {
					is_global_volume_on: self.m.s4.is_global_volume_on()
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){},
				ok_2:         function(data, params){

					// Сообщить об ошибке
					toastr.error(data.data.errormsg, "Ошибка переключения звука");

				}
			});

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
					url:          window.location.protocol + '//' + window.location.host + "/layouts/l10003",
					key: 	    		"L10003:2",
					from: 		    "ajaxko",
					data: 		    {
						message: self.m.s5.new_message()
					},
					prejob:       function(config, data, event){},
					postjob:      function(data, params){},
					ok_0:         function(data, params){},
					ok_2:         function(data, params){

						// Сообщить об ошибке
						toastr.error(data.data.errormsg, "Ошибка");

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

			// 1] Если message отсутствует, завершит
			if(!message)
				return;

			// 2] Добавить новое сообщение в конец m.s5.messages

				// 2.1] Сформировать объект для добавления
				var obj = {};
				for(var key in message) {

					// Если свойство не своё, пропускаем
					if(!message.hasOwnProperty(key)) continue;

					// Добавим в obj свойство key
					obj[key] = ko.observable(message[key]);

				}

				// 2.2] Добавить obj в m.s5.messages
				self.m.s5.messages.push(ko.observable(obj));

				// 2.3] Если длина m.s5.messages больше разрешённой
				// - Удалить 1-й элемент из массива
				if(self.m.s5.messages().length > self.m.s5.max_messages())
					self.m.s5.messages.shift();

			// 3] Прокрутить чат до конца вниз

				// 3.1] Получить контейнер чата
				var container = document.getElementsByClassName('chat-messages')[0];

				// 3.2] Получить вертикальный размер прокрутки контейнера
				var scrollHeight = container.scrollHeight;

				// 3.3] Прокрутить container в конец
				container.scrollTop = scrollHeight;
				Ps.update(container);

		};

		//------------------------------//
		// s5.3. Открыть интерфейс бана //
		//------------------------------//
		f.s5.ban_open_interface = function(data, event) {

			// 1] Записать id_user и steamname сообщения data в модель бана
			self.m.s5.ban.id_user(data.id_user());
			self.m.s5.ban.steamname(data.steamname());

			// 2] Сбросить значения параметров на дефолнтые
			self.m.s5.ban.ban_time_min(1440);
			self.m.s5.ban.reason('За нарушение правил использования чата.');

			// n] Раскрыть интерфейс
			self.m.s5.ban.visible(1);

		};

		//----------------------------------------//
		// s5.4. Забанить указанного пользователя //
		//----------------------------------------//
		f.s5.ban = function(data, event) {

			// 1] Завершить, если (или):
			// - Интерфейс заблокирован.
			// - Это анонимный пользователь.
			if(self.m.s0.ajax_counter() || self.m.s5.ban.is_spinner_vis() || !layoutmodel.m.s0.is_logged_in()) return;

			// n] Отправить запрос
			ajaxko(self, {
				url:          window.location.protocol + '//' + window.location.host + "/layouts/l10003",
				key: 	    		"L10003:3",
				from: 		    "ajaxko",
				data: 		    {
					id_user: self.m.s5.ban.id_user(),
					reason: self.m.s5.ban.reason(),
					ban_time_min: self.m.s5.ban.ban_time_min()
				},
				prejob:       function(config, data, event){

					// n] Включить спиннер на кнопке
					self.m.s5.ban.is_spinner_vis(true);

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Закрыть интерфейс бана
					self.m.s5.ban.visible(0);

					// 2] ...

					// n] Выключить спиннер на кнопке
					self.m.s5.ban.is_spinner_vis(false);

				},
				ok_1:         function(data, params){

					// n] Выключить спиннер на кнопке
					self.m.s5.ban.is_spinner_vis(false);

				},
				ok_2:         function(data, params){

					// 1] Сообщить об ошибке
					toastr.error(data.data.errormsg, "Ошибка");

					// n] Выключить спиннер на кнопке
					self.m.s5.ban.is_spinner_vis(false);

				}
			});

		};

		//-----------------------------------//
		// s5.5. Обработка бана пользователя //
		//-----------------------------------//
		f.s5.ban_user_handle = function(data) {

			// 1] Получить ID пользователя, которого надо забанить
			var id = data.id;

			// 2] Если этот клиент и есть id, показть ему тост
			if(self.m.s0.auth.user().id() == id)
				toastr.error("<b>Причина бана</b><br>"+data.reason+"<br><br><b>Когда разбанят</b><br>"+data.will_be_ended_at+" UTC.", "Вы получили БАН в чате");

			// 3] Удалить из messages все сообщения пользователя id
			self.m.s5.messages.remove(function(item){
				return item().id_user() == id;
			});

			// 4] Добавить в чат сообщение от пользователя СИСТЕМА
			layoutmodel.f.s5.add_incoming_msg({
				id: 							0,
				steamname: 				'Система',
				avatar: 					layoutmodel.m.s0.full_host() + '/public/L10003/assets/images/chat_daemon.jpg',
				level: 						1,
				message: 					data.nickname+' заблокирован в чате на '+data.ban_time_min+' мин. '+data.reason,
				id_user: 					0,
				system: 					1,
				created_at: 			"",
				updated_at: 			"",
				user_updated_at: 	""
			});


		};






	//------------------------------------------------------------//
	// 			        		 			                                    //
	// 			 s6. Функционал для работы с классической игрой 			//
	// 			         					                                    //
	//------------------------------------------------------------//
	f.s6 = {};

		//--------------------------------------------------------------------------//
		// s6.1. Запустить анимацию уведомления о ставке в пункте меню Classic game //
		//--------------------------------------------------------------------------//
		f.s6.notify_animate = function(value) {

			// 1] Снизить длительность анимации до 0
			self.m.s6.notify.traisitionDuration('0s');

			// 2] Показать уведомление
			self.m.s6.notify.is_hidden(0);

			// 3] Обновить текст уведомления
			self.m.s6.notify.text(value);

			// 4] Через .5s
			setTimeout(function(){

				// Вернуть длительность анимации 01s
				self.m.s6.notify.traisitionDuration('1s');

				// Скрыть уведомление
				self.m.s6.notify.is_hidden(1);

			}, 1000);

		};


	//----------------------------------------------------------------------------//
	// 			        		 			                                                    //
	// 			 s7. Функционал для работы с popup-окном выбора способа депозита 			//
	// 			         					                                                    //
	//----------------------------------------------------------------------------//
	f.s7 = {};

		//------------------------------------------//
		// s7.1. Закрыть popup-окно выбора депозита //
		//------------------------------------------//
		f.s7.close_popup = function(data, event) {

			// 1] Закрыть popup-окно выбора депозита
			self.m.s7.ison(false);

			// 2] Сделать body снова прокручеваемым
			document.body.style.overflow = 'auto';

		};

		//-------------------------------------------//
		// s7.2. За "каждые/каждую" N копеек/копейку //
		//-------------------------------------------//
		// - 1 копейку, 2-4 копейки, 5 копеек.
		// - За каждые 20 копеек.
		// - За каждую 1/21/31/41.../101 копейку.
		// - За каждую 101 копейку.
		f.s7.kopeyky = function(data, event) {

			// 1] Получить кол-во копеек в 1 центе США по текущему курсу
			// - В виде строки.
			var value = ''+Math.round(layout_data.data.usdrub_rate);

			// 2] Каждые или каждую?
			// - Если последняя цифра 1, и всего цифр не 2, то каждую.
			// - Иначе: каждые.
			var every = (function(){

				if(value.length != 2 && value[value.length-1] == 1) return 'каждую';
				else return 'каждые';

			})();

			// 3] Копейку, копейки или копеек
			// - Всё зависит от последней цифры:
			//     1: 	копейку
			//     2-4: копейки
			//     5+: 	копеек.
			var kop = (function(){

				if(value[value.length-1] == 1) return 'копейку';
				else if([2,3,4].indexOf(value[value.length-1]) != -1) return 'копейки';
				else return 'копеек';

			})();

			// n] Вернуть результаты
			return {
				every: every,
				kop: kop
			};

		};


	//------------------------------------------------//
	// 			        		 			                        //
	// 			 s9. Функционал виджета выбора языка 			//
	// 			         					                        //
	//------------------------------------------------//
	f.s9 = {};

		//--------------------------------------------//
		// s9.1. Выбрать язык и записать выбор в куки //
		//--------------------------------------------//
		f.s9.choose_lang = function(data, event) {

			// 1] Получить локаль выбранного языка
			var locale = data.locale();

			// 2] Получить ссылку на объект-язык
			var lang = self.m.s9.indexes.langs[locale];
			if(!lang) {
				toastr.error("Can't get locale link.");
				return;
			}

			// 3] Отправить сообщение на сервер
			ajaxko(self, {
				url:          window.location.protocol + '//' + window.location.host + "/layouts/l10003",
				key: 	    		"L10003:4",
				from: 		    "ajaxko",
				data: 		    {
					locale: locale
				},
				ajax_params: {
					lang: lang
				},
				prejob:       function(config, data, event){

					// Сообщить
					toastr.info("Changing language...")

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сделать lang выбранным
					//self.m.s9.choosen_lang(params.lang);

					// 2. Сообщить
					toastr.success("Language successfully changed! Reloading page...")

					// 3] Перезагрузить страницу
          window.location.reload(true);

				},
				ok_2:         function(data, params){

					// Сообщить об ошибке
					toastr.error("An error occurred.", "Error");

				}
			});

		};












return f; }};




























