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
 *    f.s0.logout                   | s1.1. Выйти из своей учётной записи
 *
 *
 *  s2. Функционал модели механики левого сайдбара (с главным меню)
 *
 *    f.s2.switch 									| s2.1. Изменить состояние раскрытости левого меню
 *
 *  s3. Функционал модели механики правого сайдбара (с чатом)
 *
 *    f.s3.switch 									| s3.1. Изменить состояние раскрытости правого сайдбара
 *
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

		};


	//--------------------------------------------------------//
	// 			        		 			                                //
	// 			 s4. Функционал модель по управлению звуком 			//
	// 			         					                                //
	//--------------------------------------------------------//
	f.s4 = {};

		//--------------------------------------------------//
		// s4.1. Изменить состояние раскрытости левого меню //
		//--------------------------------------------------//
		f.s4.switch = function(data, event) {

			// 1] Изменить состояние раскрытости на противоположное
			self.m.s4.is_global_volume_on(!self.m.s4.is_global_volume_on());

		};




return f; }};




























