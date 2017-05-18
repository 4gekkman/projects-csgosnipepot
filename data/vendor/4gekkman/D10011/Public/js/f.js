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
 *    f.s0.txt_delay_save								| s0.1. Функционал "механизма отложенного сохранения для текстовых полей"
 *
 *  s1. Функционал групп и фильтров
 *
 *		f.s1.choose_group									| s1.1. Выбрать группу среди перменентных и групп из БД
 *    f.s1.turnon_rename_group_mode     | s1.2. Переключить группу в режим переименования
 *    f.s1.cancel_group_rename          | s1.3. Отменить переименование группы
 *    f.s1.apply_group_rename           | s1.4. Подтвердить переименование группы
 *    f.s1.create_new_group             | s1.5. Создать новую группу
 *    f.s1.delete_group             		| s1.6. Удалить группу
 *    f.s1.choose_bot                   | s1.7. Выбрать кликнутого бота
 *    f.s1.unchoose_bot                 | s1.8. Развыбрать выбранного бота
 *    f.s1.edit_bot_safe                | s1.9. Отредактировать безопасные свойства бота
 *    f.s1.edit_bot_unsafe              | s1.10. Отредактировать небезопасные свойства бота
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



	//--------------------------------------------//
	// 			        		 			                    //
	// 			 s1. Функционал групп и фильтров 			//
	// 			         					                    //
	//--------------------------------------------//
	f.s1 = {};

		//-------------------------------------------------------//
		// s1.1. Выбрать группу среди перменентных и групп из БД //
		//-------------------------------------------------------//
		f.s1.choose_group = function(data, event) {

			// 1] Если кликнута перманентная группа
			if(data.permanent && data.permanent()) {

				self.m.s1.groups.permanent.choosen(data);
				self.m.s1.groups.variable.choosen("");

			}

			// 2] Если кликнута группа из БД
			else {

        self.m.s1.groups.variable.choosen(data);
				self.m.s1.groups.permanent.choosen("");

			}

			// 3] Развыбрать выбранного бота
			self.m.s2.choosen_bot("");

		};

		//-------------------------------------------------//
		// s1.2. Переключить группу в режим переименования //
		//-------------------------------------------------//
		f.s1.turnon_rename_group_mode = function(data, event) {

			// Сообщить, что функция недоступна, и завершить
			toastr.warning("К сожалению, эта функция недоступна на данный момент.");
			return;

			// 1] Наполнить input, предназначенный для переименования
			self.m.s1.groups.rename.input(data.name());

			// 2] Поместить фокус на input переименования
			(function(){

				// 2.1] Получить input переименования для группы с data.id()
				var e = document.getElementById('rename_input_of_db_group_'+data.id());

				// 2.2] Навести фокус на e, если он найден
				if(e)
					setTimeout(function(){
						e.focus();
					}, 10);

			})();


			// n] Переключить группу в режим переименования
			self.m.s1.groups.rename.group(data);

		};

		//--------------------------------------//
		// s1.3. Отменить переименование группы //
		//--------------------------------------//
		f.s1.cancel_group_rename = function(data, event) {

			// 1] Очистить input, предназначенный для переименования
			self.m.s1.groups.rename.input("");

			// n] Удалить группу из режима переименования
			self.m.s1.groups.rename.group("");

		};

		//-----------------------------------------//
		// s1.4. Подтвердить переименование группы //
		//-----------------------------------------//
		f.s1.apply_group_rename = function(data, event) {

			// 1] Если новое имя группы пустое, сообщить и завершить
			if(!self.m.s1.groups.rename.input()) {
				toastr.error("Новое имя группы не может быть пустым.");
				return;
			}

			// 2] Если новое и старое имя не отличаются
			if(self.m.s1.groups.rename.input() == self.m.s1.groups.rename.group().name()) {

				// 2.1] Очистить input, предназначенный для переименования
				self.m.s1.groups.rename.input("");

				// 2.2] Удалить группу из режима переименования
				self.m.s1.groups.rename.group("");

				// 2.3] Завершить
				return;

			}

			// 3] Отправить запрос
			ajaxko(self, {
				key: 	    		"D10011:1",
				from: 		    "ajaxko",
				data: 		    {
					id: 	self.m.s1.groups.rename.group().id(),
					name: self.m.s1.groups.rename.input()
				},
				prejob:       function(config, data, event){

					// 1] Сообщить, что идёт сохранение нового имени группы
					toastr.info("Сохраняю новое имя группы...");

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить об успехе
					toastr.success("Группа успешно переименована!");

					// 2] Переименовать группу
					self.m.s1.groups.rename.group().name(self.m.s1.groups.rename.input());

					// n] Очистить input, предназначенный для переименования
					self.m.s1.groups.rename.input("");

					// m] Удалить группу из режима переименования
					self.m.s1.groups.rename.group("");

				},
				ok_2:         function(data, params){

					// 1] Если тип ошибки 1, сообщить, что группа с таким именем уже есть
					if(data.data.errormsg == '1') {
						toastr.error("Группа с таким именем уже есть.");
						return;
					}

					// 2] Если тип ошибки 1, сообщить, что группа с таким именем уже есть
					if(data.data.errormsg == '1') {
						toastr.error("Не удалось найти группу с таким ID.");
						return;
					}

					// n] В ином случае, сообщить о неизвестной ошибке
					else {
						toastr.error("Возникла ошибка.");
					}

				}
			});

		};

		//----------------------------//
		// s1.5. Создать новую группу //
		//----------------------------//
		f.s1.create_new_group = function(data, event) {

			toastr.warning("К сожалению, эта функция недоступна на данный момент.");

		};

		//----------------------//
		// s1.6. Удалить группу //
		//----------------------//
		f.s1.delete_group = function(data, event) {

			toastr.warning("К сожалению, эта функция недоступна на данный момент.");

		};

		//----------------------//
		// s1.7. Выбрать кликнутого бота //
		//----------------------//
		f.s1.choose_bot = function(data, event) {

			// 1] Выбрать кликнутого бота
			self.m.s2.choosen_bot(data);

			// 2] Обновить безопасные свойства бота для редактирования
			self.m.s2.edit_safe.login(self.m.s2.choosen_bot().login());
			self.m.s2.edit_safe.steamid(self.m.s2.choosen_bot().steamid());
			self.m.s2.edit_safe.apikey_domain(self.m.s2.choosen_bot().apikey_domain());
			self.m.s2.edit_safe.apikey(self.m.s2.choosen_bot().apikey());
			self.m.s2.edit_safe.trade_url(self.m.s2.choosen_bot().trade_url());
			self.m.s2.edit_safe.description(self.m.s2.choosen_bot().description());

			// 3] Обнулить небезопасные свойства бота для редактирования
			self.m.s2.edit_unsafe.password("");
			self.m.s2.edit_unsafe.sessionid("");
			self.m.s2.edit_unsafe.shared_secret("");
			self.m.s2.edit_unsafe.serial_number("");
			self.m.s2.edit_unsafe.revocation_code("");
			self.m.s2.edit_unsafe.uri("");
			self.m.s2.edit_unsafe.server_time("");
			self.m.s2.edit_unsafe.account_name("");
			self.m.s2.edit_unsafe.token_gid("");
			self.m.s2.edit_unsafe.identity_secret("");
			self.m.s2.edit_unsafe.secret_1("");
			self.m.s2.edit_unsafe.device_id("");

		};

		//----------------------------------//
		// s1.8. Развыбрать выбранного бота //
		//----------------------------------//
		f.s1.unchoose_bot = function(data, event) {

			// 1] Развыбрать выбранного бота
			self.m.s2.choosen_bot("");

		};

		//------------------------------------------------//
		// s1.9. Отредактировать безопасные свойства бота //
		//------------------------------------------------//
		f.s1.edit_bot_safe = function(callback, data, event) {

			// 1] Подготовить объект с данными для отправки
			var obj2send = {
				id_bot:         self.m.s2.choosen_bot().id(),
				login: 					self.m.s2.edit_safe.login(),
				steamid: 				self.m.s2.edit_safe.steamid(),
				apikey_domain:  self.m.s2.edit_safe.apikey_domain(),
				apikey: 				self.m.s2.edit_safe.apikey(),
				trade_url:  		self.m.s2.edit_safe.trade_url(),
				description:		self.m.s2.edit_safe.description()
			};

			// 1] Отправить запрос
			ajaxko(self, {
				key: 	    		"D10011:2",
				from: 		    "ajaxko",
				data: 		    obj2send,
				prejob:       function(config, data, event){

					// 1] Сообщить, что идёт сохранение
					toastr.info("Сохраняю безопасные свойства бота...");

				},
				ajax_params: {
					obj2send: obj2send
				},
				postjob:      function(data, params){},
				callback: 		callback,
				ok_0:         function(data, params){

					// 1] Сообщить об успехе
					toastr.success("Безопасные свойства бота успешно сохранены!");

					// 2] Обновить выбранного бота
					self.m.s2.choosen_bot().login(params.obj2send.login);
					self.m.s2.choosen_bot().steamid(params.obj2send.steamid);
					self.m.s2.choosen_bot().apikey_domain(params.obj2send.apikey_domain);
					self.m.s2.choosen_bot().apikey(params.obj2send.apikey);
					self.m.s2.choosen_bot().trade_url(params.obj2send.trade_url);
					self.m.s2.choosen_bot().description(params.obj2send.description);

				},
				ok_2:         function(data, params){

					// n] Сообщить о неизвестной ошибке
				  toastr.error(data.data.errormsg);

				}
			});

		};

		//---------------------------------------------------//
		// s1.10. Отредактировать небезопасные свойства бота //
		//---------------------------------------------------//
		f.s1.edit_bot_unsafe = function(callback, data, event) {

			// 1] Подготовить объект с данными для отправки
			var obj2send = {
				id_bot:         	self.m.s2.choosen_bot().id()
			};
			if(self.m.s2.edit_unsafe.password()) 				obj2send.password 				= self.m.s2.edit_unsafe.password();
			if(self.m.s2.edit_unsafe.sessionid()) 			obj2send.sessionid 				= self.m.s2.edit_unsafe.sessionid();
			if(self.m.s2.edit_unsafe.shared_secret()) 	obj2send.shared_secret 		= self.m.s2.edit_unsafe.shared_secret();
			if(self.m.s2.edit_unsafe.serial_number()) 	obj2send.serial_number 		= self.m.s2.edit_unsafe.serial_number();
			if(self.m.s2.edit_unsafe.revocation_code()) obj2send.revocation_code 	= self.m.s2.edit_unsafe.revocation_code();
			if(self.m.s2.edit_unsafe.uri()) 						obj2send.uri 							= self.m.s2.edit_unsafe.uri();
			if(self.m.s2.edit_unsafe.server_time()) 		obj2send.server_time 			= self.m.s2.edit_unsafe.server_time();
			if(self.m.s2.edit_unsafe.account_name()) 		obj2send.account_name 		= self.m.s2.edit_unsafe.account_name();
			if(self.m.s2.edit_unsafe.token_gid()) 			obj2send.token_gid 				= self.m.s2.edit_unsafe.token_gid();
			if(self.m.s2.edit_unsafe.identity_secret()) obj2send.identity_secret 	= self.m.s2.edit_unsafe.identity_secret();
			if(self.m.s2.edit_unsafe.secret_1()) 				obj2send.secret_1 				= self.m.s2.edit_unsafe.secret_1();
			if(self.m.s2.edit_unsafe.device_id()) 			obj2send.device_id 				= self.m.s2.edit_unsafe.device_id();

			// 2] Отправить запрос
			ajaxko(self, {
				key: 	    		"D10011:3",
				from: 		    "ajaxko",
				data: 		    obj2send,
				prejob:       function(config, data, event){

					// 1] Сообщить, что идёт сохранение и шифрование
					toastr.info("Сохраняю и шифрую небезопасные свойства бота...");

				},
				ajax_params: {
					obj2send: obj2send
				},
				postjob:      function(data, params){},
				callback: 		callback,
				ok_0:         function(data, params){

					// 1] Сообщить об успехе
					toastr.success("Небезопасные свойства бота успешно зашифрованы и сохранены!");

				},
				ok_2:         function(data, params){

					// n] Сообщить о неизвестной ошибке
				  toastr.error(data.data.errormsg);

				}
			});

		};





return f; }};




























