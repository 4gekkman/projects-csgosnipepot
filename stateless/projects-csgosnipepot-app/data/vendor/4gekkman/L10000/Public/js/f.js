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
 *    f.s0.update_menu              | s0.3. Обновить модель меню
 *
 *  s1. Связанный с аутентификацией функционал
 *
 * 		f.s1.logout                   | s1.1. Выйти из своей учётной записи
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

		//----------------------------//
		// s0.3. Обновить модель меню //
		//----------------------------//
		f.s0.update_menu = function(menu) {

			// 1] Очистить меню
			self.m.s2.subdocs.removeAll();

			// 2] Наполнить меню новыми данными
			for(var i=0; i<menu.length; i++) {

				// 2.1] Сформировать объект для добавления
				var obj = {};
				for(var key in menu[i]) {

					// Если свойство не своё, пропускаем
					if(!menu[i].hasOwnProperty(key)) continue;

					// Добавим в obj свойство key
					obj[key] = ko.observable(menu[i][key]);

				}

				// 2.2] Добавить этот объект в подготовленный массив
				self.m.s2.subdocs.push(ko.observable(obj));

			}

			// 3] Сделать соответствующий элемент меню выбранным
			self.m.s2.selected_subdoc(self.m.s2.subdocs()[+layout_data.data.menu_item_number-1]());

		};

	//--------------------------------------------------------//
	// 			        		 			                                //
	// 			 s1. Связанный с аутентификацией функционал 			//
	// 			         					                                //
	//--------------------------------------------------------//
	f.s1 = {};

		//-------------------------------------//
		// s1.1. Выйти из своей учётной записи //
		//-------------------------------------//
		f.s1.logout = function(data, event){

			// 1] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C59_logout",
				from: 		    "ajaxko",
				data: 		    {},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					notify({msg: 'You are successfully logged out!', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Перезагрузить страницу
          window.location.reload(true);

				}
			});

		};



return f; }};




























