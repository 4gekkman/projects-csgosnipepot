/*//===================================================================================////
////																			                                             ////
////   Функционал Free Coins, предназначен для подключения в основной f.js документа   ////
////																			                                             ////
////===================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 	s8. Функционал Free Coins игроков
 *
 * 		А. Модель ежедневной награды
 * 		-------------------------
 *    f.s8.get_freecoins      	| sА8.1. Получить бесплатные монеты
 *    f.s8.fc_block             | sА8.2. Заблокировать интерфейс бесплатных монет
 *    f.s8.fc_unblock           | sА8.3. Разблокировать интерфейс бесплатных монет
 *    f.s8.fc_left2unblock      | sА8.4. Обновить время, оставшееся до следующего дня
 *
 *    Б. Модель будь онлайн
 *    ------------------
 *    f.s8.counters_freshdata   	| sБ8.1. Обновить данные счётчиков свежими
 *    f.s8.create_giveaway_offer  | sБ8.2. Обновить данные счётчиков свежими
 *    f.s8.create_giveaway_resp   | sБ8.3. Обработать ответ на запрос создания оффера для получения скина за онлайн
 *    f.s8.new_giveaway           | sБ8.4. Была создана новая выдача, надо обновить её модель клиента
 *    f.s8.del_giveaway           | sБ8.5. Удалить выдачу
 *
 *
 *
 */

	//--------------------------------------------------------------------//


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctionsFc = { constructor: function(self, f) { f.s8 = this;

	//-----------------------------//
	// 			        		 	         //
	// 	s8. Функционал Free Coins  //
	// 			         			         //
	//-----------------------------//

	//----------------------------------//
	// sА8.1. Получить бесплатные монеты //
	//----------------------------------//
	f.s8.get_freecoins = function() {

		// 1] Если пользователь уже получал сегодня награду, завершить
		if(self.m.s8.reword.is_got_reword() == 1) return;

		// 2] Завершить, если (или):
		// - Интерфейс заблокирован.
		// - Это анонимный пользователь.
		if(self.m.s0.ajax_counter() || !layoutmodel.m.s0.is_logged_in()) return;

		// 3] Выполнить ajax-запрос
		ajaxko(self, {
			key: 	    		"D10009:10",
			from: 		    "f.s8.get_freecoins",
			data: 		    {

			},
			ajax_params: {

			},
			prejob:       function(config, data, event){

				// 1] Включить спиннер на кнопке
				self.m.s8.reword.is_spinner_vis(true);

			},
			postjob:      function(data, params){


			},
			ok_0:         function(data, params){

				// n] Выключить спиннер на кнопке
				self.m.s8.reword.is_spinner_vis(false);

			},
			ok_1:         function(data, params){

				// n] Выключить спиннер на кнопке
				self.m.s8.reword.is_spinner_vis(false);

			},
			ok_2:         function(data, params){

				// 1] Если пользователь уже получил награду
				if(data.data.errormsg == "1")
					toastr.error("Сегодня Вы уже получили бесплатные монеты.", "Ошибка");

				// 2] Если запрос от анонимуса
				else if(data.data.errormsg == "2")
					toastr.error("Бесплатные монеты может получить только аутентифицированный пользователь.", "Ошибка");

				// n] Если это обычная ошибка
				else {
					toastr.error(data.data.errormsg, "Ошибка при обработке заказа");
				}

				// n] Выключить спиннер на кнопке
				self.m.s8.reword.is_spinner_vis(false);

			}
		});


	};

	//------------------------------------------------//
	// sА8.2. Заблокировать интерфейс бесплатных монет //
	//------------------------------------------------//
	f.s8.fc_block = function() {

		self.m.s8.reword.is_got_reword(1);

	};

	//-------------------------------------------------//
	// sА8.3. Разблокировать интерфейс бесплатных монет //
	//-------------------------------------------------//
	f.s8.fc_unblock = function() {

		self.m.s8.reword.is_got_reword(0);

	};

	//----------------------------------------------------//
	// sА8.4. Обновить время, оставшееся до следующего дня //
	//----------------------------------------------------//
	f.s8.fc_left2unblock = function(data) {

		self.m.s8.reword.time_until_next_day(data.end);

	};


	//------------------------------------------//
	// sБ8.1. Обновить данные счётчиков свежими //
	//------------------------------------------//
	f.s8.counters_freshdata  = function(data) {

		// 1] Обновить данные конфига
		self.m.s8.beonline.giveaway_period_min(data.config.giveaway_period_min);
		self.m.s8.beonline.offline2drop_online_counter_sec(data.config.offline2drop_online_counter_sec);

		// 2] Обновить счётчик
		self.m.s8.beonline.counter((function(){
			if(data.counters[0])
				return data.counters[0].counter;
			else
				return 0;
		})());

		// 3] Обновить клиентский timestamp последнего обновления на клиенте счётчика онлайна
		self.m.s8.beonline.counter_updated_at(Date.now());

	};

	//------------------------------------------//
	// sБ8.2. Обновить данные счётчиков свежими //
	//------------------------------------------//
	f.s8.create_giveaway_offer = function() {

		// 1] Завершить, если (или):
		// - Интерфейс заблокирован.
		// - Это анонимный пользователь.
		// - Предыдущий запрос ещё не обработан.
		if(self.m.s0.ajax_counter() || !layoutmodel.m.s0.is_logged_in() || self.m.s8.reword.is_spinner_vis()) return;

		// 2] Выполнить ajax-запрос
		ajaxko(self, {
			key: 	    		"D10009:11",
			from: 		    "f.s8.create_giveaway_offer",
			data: 		    {

			},
			ajax_params: {

			},
			prejob:       function(config, data, event){

				// 1] Включить спиннер на кнопке
				self.m.s8.beonline.is_spinner_vis(true);

			},
			postjob:      function(data, params){


			},
			ok_0:         function(data, params){

				// n] Выключить спиннер на кнопке
				//self.m.s8.beonline.is_spinner_vis(false);

			},
			ok_1:         function(data, params){

				// n] Выключить спиннер на кнопке
				//self.m.s8.beonline.is_spinner_vis(false);

			},
			ok_2:         function(data, params){

				// n] Выключить спиннер на кнопке
				//self.m.s8.reword.is_spinner_vis(false);

			}
		});


	};

	//---------------------------------------------------------------------------------//
	// sБ8.3. Обработать ответ на запрос создания оффера для получения скина за онлайн //
	//---------------------------------------------------------------------------------//
	f.s8.create_giveaway_resp = function(data) {

		// 1] Выключить спиннер на кнопке
		self.m.s8.beonline.is_spinner_vis(false);

		// 2] Если успех
		if(data.status == 0) {

			// 2.1] Подготовить html для тоста
			var html = (function(){

				// 2.1.1] Подготовить результат
				var result =

					// Сколько офферов должно быть отправлено для выполнения заказа
					"<p>Вам успешно отправлен оффер в Steam с бесплатным скином.</p>" +

					// ID оффера в steam
					"ID оффера в steam: "+data.data.tradeofferid+"<br>" +

					// Просьба подтвердить
					"<p>Для завершения операции, <a style='color: #8ab4f8; text-decoration: underline' target='_blank' href='https://steamcommunity.com/tradeoffer/"+data.data.tradeofferid+"'>подтвердите оффер в Steam</a>. " +

					// Просьба сверить код безопасности
					"Обязательно проверьте код безопасности: "+data.data.safecode+".</p>"

				;

				// 2.1.2] Вернуть результат
				return result;

			})();

			// 2.2] Очистить модель выдачи
			self.f.s8.del_giveaway();

			// 2.n] Показать тост
			toastr.warning(html, "Бесплатный скин за онлайн", {
				timeOut: 					"300000",
				extendedTimeOut: 	"300000"
			});

		}

		// 3] Если ошибка
		else {

			// 1] Если это анонимный пользователь
			if(data.data.errormsg == "1")
				toastr.error("Анонимным пользователям скины за онлайн не положены.", "Ошибка");

			// 2] Если выдача не надена
			else if(data.data.errormsg == "2")
				toastr.error("Что-то пошло не так, ваша выдача не найдена.", "Ошибка");

			// 3] Если выдача не надена
			else if(data.data.errormsg == "3")
				toastr.error("Сделайте хотя бы 1 ставку в комнате Main, чтобы получать бесплатные скины за онлайн.", "Ошибка");

			// 4] Если не удалось создать оффер
			else if(data.data.errormsg == "4")
				toastr.error("Нашему боту не удалось отправить вам оффер в Steam. Проверьте свой торговый URL, и попробуйте ещё раз.", "Ошибка");

			// 5] Если не удалось найти такого пользователя
			else if(data.data.errormsg == "5")
				toastr.error("Не удалось найти такого пользователя.", "Ошибка");

			// 6] Если не удалось найти такого пользователя
			else if(data.data.errormsg == "6")
				toastr.error("Не удалось найти комнату Main.", "Ошибка");

			// 7] Вам уже отправлен оффер с бесплатным скином из этой выдачи
			else if(data.data.errormsg == "7")
				toastr.error("Вам уже отправлен оффер с бесплатным скином из этой выдачи.", "Ошибка");

			// n] Если это обычная ошибка
			else {
				toastr.error(data.data.errormsg, "Возникла ошибка при создании оффера, проверьте ваш торговый URL.");
			}

		}

	};

	//-------------------------------------------------------------------//
	// sБ8.4. Была создана новая выдача, надо обновить её модель клиента //
	//-------------------------------------------------------------------//
	f.s8.new_giveaway = function(data) {

		// Обновить модель выдачи
		self.m.s8.beonline.giveaway(ko.mapping.fromJS(data.giveaway));

	};

	//-----------------------//
	// sБ8.5. Удалить выдачу //
	//-----------------------//
	f.s8.del_giveaway = function(data) {

		// Обновить модель выдачи
		self.m.s8.beonline.giveaway("");

	};









return this; }};




























