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
 *  s1. Общий функционал модели управления главным меню
 *
 *		f.s1.select_subdoc								| s1.1. Выбрать кликнутый поддокумент
 *
 *  s2. Функционал победителей
 *
 *    f.s2.choose_winner                | s2.1. Выбрать победителя
 *    f.s2.turnon_rename_win_mode       | s2.2. Переключить победителя в режим переименования
 *    f.s2.cancel_win_rename  					| s2.3. Отменить переименование победителя
 *    f.s2.apply_win_rename             | s2.4. Подтвердить переименование победителя
 *
 *    f.s2.saveimage                    | s2.5. Загрузить изображение из файла
 *    f.s2.turnon_chava_win_mode        | s2.6. Переключить победителя в режим изменения аватара
 *    f.s2.saveimage_cancel             | s2.7. Отменить изменение аватара победителя
 *
 *  s3. Функционал модели раздела "победить"
 *
 *    f.s3.let_him_win                  | s3.1. Пусть он победит
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



	//----------------------------------------------------------------//
	// 			        		 			                                        //
	// 			 s1. Общий функционал модели управления главным меню 			//
	// 			         					                                        //
	//----------------------------------------------------------------//
	f.s1 = {};

		//--------------------------------------//
		// s1.1. Выбрать кликнутый поддокумент  //
		//--------------------------------------//
		// - Пояснение
		f.s1.select_subdoc = function(data, event) {

			// 1] Выбрать кликнутый поддокумент
			self.m.s1.selected_subdoc(data);

			// 2] Если переключение происходит на поддокумент "Скины на заказ"
			if(data.name() == 'skins2order') {

				// 2.1] Выбрать поддокумент list поддокумента "Скины на заказ"
				self.m.s2.selected_subdoc(self.m.s2.indexes.subdocs['list']);

			}

		};


	//----------------------------------------//
	// 			        		 			                //
	// 			 s2. Функционал победителей 			//
	// 			         					                //
	//----------------------------------------//
	f.s2 = {};

		//--------------------------//
		// s2.1. Выбрать победителя //
		//--------------------------//
		f.s2.choose_winner = function(data, event) {

			// 1] Если победитель для переименования выбран, и это не дата
			if(self.m.s2.rename.winner() != data) {
				self.f.s2.cancel_win_rename();
			}

			// 2] Выбрать победителя
			self.m.s2.choosen(data);

		};

		//-----------------------------------------------------//
		// s2.2. Переключить победителя в режим переименования //
		//-----------------------------------------------------//
		f.s2.turnon_rename_win_mode = function(data, event) {

			// 1] Наполнить input, предназначенный для переименования
			self.m.s2.rename.input(data.nickname());

			// 2] Поместить фокус на input переименования
			(function(){

				// 2.1] Получить input переименования для группы с data.id()
				var e = document.getElementById('rename_input_of_db_winner_'+data.id());

				// 2.2] Навести фокус на e, если он найден
				if(e)
					setTimeout(function(){
						e.focus();
					}, 10);

			})();


			// n] Переключить победителя в режим переименования
			self.m.s2.rename.winner(data);

		};

		//------------------------------------------//
		// s2.3. Отменить переименование победителя //
		//------------------------------------------//
		f.s2.cancel_win_rename = function(data, event) {

			// 1] Очистить input, предназначенный для переименования
			self.m.s2.rename.input("");

			// n] Удалить победителя из режима переименования
			self.m.s2.rename.winner("");

		};

		//---------------------------------------------//
		// s2.4. Подтвердить переименование победителя //
		//---------------------------------------------//
		f.s2.apply_win_rename = function(data, event) {

			// 1] Если новое имя пустое, сообщить и завершить
			if(!self.m.s2.rename.input()) {
				toastr.error("Новое имя победителя не может быть пустым.");
				return;
			}

			// 2] Если новое и старое имя не отличаются
			if(self.m.s2.rename.input() == self.m.s2.rename.winner().nickname()) {

				// 2.1] Отменить переименование
				self.f.s2.cancel_win_rename();

				// 2.2] Завершить
				return;

			}

			// 3] Отправить запрос
			ajaxko(self, {
				key: 	    		"D10013:1",
				from: 		    "ajaxko",
				data: 		    {
					id_user: self.m.s2.rename.winner().id(),
					new_name: self.m.s2.rename.input()
				},
				prejob:       function(config, data, event){

					// Уведомить о том, что пошёл запрос
					toastr.info("Переименовываю победителя...");

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить об успехе
					toastr.success("Победитель успешно переименован!");

					// 2] Переименовать победителя
					self.m.s2.rename.winner().nickname(self.m.s2.rename.input());

					// n] Выйти из режима переименования
					self.f.s2.cancel_win_rename();

				},
				ok_1:         function(data, params){

					toastr.error('Сервер не отвечает.', 'Таймаут запроса');

				},
				ok_2:         function(data, params){

					toastr.error(data.data.errormsg, "Ошибка");

				}
			});


		};

		//--------------------------------------//
		// s2.5. Загрузить изображение из файла //
		//--------------------------------------//
		// - Пояснение
		f.s2.saveimage = function(data, event) {

			// 1] Проверить, выбран ли файл, если нет, завершить
			if(!self.m.s2.chava.inputurl()) {
				toastr.info("Сначала выбери изображение");
				return;
			}

			// 2] Подготовить данные в формате FormData
			var fd = new FormData();
			fd.append('file', self.m.s2.chava.inputurl());
			fd.append('command', "");
			fd.append('key', "D10013:2");
			fd.append('timestamp', Date.now());
			fd.append('group', "testgroup");
			fd.append('params', JSON.stringify({
				"name": self.m.s2.chava.winner().id(),
				"sizes": [ [184, 184] ],
				"types": ["image/jpeg"],
				"filters": []
			}));

			// 3] Отправить запрос
			ajaxko(self, {
			  command: 	    "",
			  key: 	    		"D10013:2",
				from: 		    "f.s2.saveimage",
				ajax_request_body: fd,
				timestamp:    Date.now(),
				ajax_headers: {"X-CSRF-TOKEN": server.csrf_token},
			  prejob:       function(config, data, event){

					// Уведомить о том, что пошёл запрос
					toastr.info("Изменяю аватар победителя...");

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить
					toastr.success("Аватар успешно изменён.", "Успех");

					// 2] Очистить inputurl
					self.m.s2.chava.inputurl("");

					// 3] Обновить картинку
					var img = document.getElementById('image_of_winner_'+self.m.s2.chava.winner().id());
					img.src = img.src + '&time=' + new Date().getTime();

					// 3] Вывести выбранного победителя из режима изменения ватара
					self.m.s2.chava.winner("");

				},
			  ok_1:         function(data, params){

					toastr.error('Сервер не отвечает.', 'Таймаут запроса');

				},
			  ok_2:         function(data, params){

					toastr.error(data.data.errormsg, "Ошибка");

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

		//--------------------------------------------------------//
		// s2.6. Переключить победителя в режим изменения аватара //
		//--------------------------------------------------------//
		f.s2.turnon_chava_win_mode = function(data, event) {

			// 1] Сбросить значение inputurl
			document.getElementById("inputurl").value = "";

			// n] Переключить победителя в режим изменения аватара
			self.m.s2.chava.winner(data);

		};

		//---------------------------------------------//
		// s2.7. Отменить изменение аватара победителя //
		//---------------------------------------------//
		f.s2.saveimage_cancel = function(data, event) {

			// 1] Очистить input, предназначенный для изменения аватара
			self.m.s2.chava.inputurl("");

			// n] Удалить победителя из режима изменения аватара
			self.m.s2.chava.winner("");

		};


	//------------------------------------------------------//
	// 			        		 			                              //
	// 			 s3. Функционал модели раздела "победить" 			//
	// 			         					                              //
	//------------------------------------------------------//
	f.s3 = {};

		//------------------------//
		// s3.1. Пусть он победит //
		//------------------------//
		f.s3.let_him_win = function(data, event) {

			// 1] Если ticket2win пуст, завершить
			//if(!data.ticket2win()) {
			//	toastr.info('Необходимо указать номер билета, который победит. Он не может быть пустым.');
			//	return;
			//}

			// 2] Отправить запрос
			ajaxko(self, {
			  command: 	    "\\M9\\Commands\\C57_add_new_key",
				from: 		    "f.s3.let_him_win",
				data: {
					id_room: 		data.id(),
					ticket2win: data.ticket2win()
				},
				ajax_params: {
					data: data
				},
			  prejob:       function(config, data, event){

					// Уведомить о том, что пошёл запрос
					toastr.info("Сохраняю данные...");

				},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить
					if(params.data.ticket2win())
						toastr.success("Победитель для раунда №"+data.data.id_round+" комнаты '"+params.data.name()+"' успешно назначен. Победит билет №: "+params.data.ticket2win()+" (если такой будет в раунде). <br><br>Чтобы отменить выбор победителя, отправь пустое значение до перехода раунда в состояние Lottery.", "Успех");
					else
						toastr.success("Теперь победитель для раунда №"+data.data.id_round+" комнаты '"+params.data.name()+"' будет выбран случайным путём");

					// 2] Сбросить значение ticket2win соотв.комнаты
					params.data.ticket2win("");

				},
			  ok_1:         function(data, params){

					toastr.error('Сервер не отвечает.', 'Таймаут запроса');

				},
			  ok_2:         function(data, params){

					toastr.error(data.data.errormsg, "Ошибка");

				}

			});

		};














return f; }};




























