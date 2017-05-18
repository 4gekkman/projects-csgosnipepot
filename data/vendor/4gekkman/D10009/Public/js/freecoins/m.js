/*//================================================================================////
////																		                                            ////
////   Модель Free Coins, предназначенная для подключения в основной m.js	документа	////
////																				                                        ////
////================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *
 * 	s8. Модель Free Coins
 *
 * 		s8.1. Модель ежедневной награды
 * 		s8.2. Модель будь онлайн
 * 		s8.3. Модель nick promo
 *    s8.n. Индексы и вычисляемые значения
 *
 *  W. Обработка websocket-сообщений
 *
 * 		w8.1. Обработка сообщений от M15
 * 		w8.2. Обработка сообщений от M16
 *
 * 	X. Подготовка к завершению
 *
 *    X1. Вернуть ссылку self на объект-модель
 *
 *
 */


//====================//
// 			        		 	//
// 			 Модель  			//
// 			         			//
//====================//
var ModelFc = { constructor: function(self, m) { m.s8 = this;

	//-------------------------//
	// 			        		 	     //
	// 	s8. Модель Free Coins  //
	// 			         			     //
	//-------------------------//

	//---------------------------------//
	// s8.1. Модель ежедневной награды //
	//---------------------------------//
	self.m.s8.reword = {};

		// 1] Получил ли уже сегодня этот аутентиф.пользователь ежедневную награду? //
		//--------------------------------------------------------------------------//
		self.m.s8.reword.is_got_reword = ko.observable(server.data.reward.is_got_reword);

		// 2] Размер ежедневное награды в монетах //
		//----------------------------------------//
		self.m.s8.reword.coins = ko.observable(server.data.reward.coins);

		// 3] Сколько времени осталось до наступления следующих суток //
		//------------------------------------------------------------//
		self.m.s8.reword.time_until_next_day = ko.observable(server.data.reward.time_until_next_day);

		// 4] Показан ли спиннер на кнопке //
		//---------------------------------//
		self.m.s8.reword.is_spinner_vis = ko.observable(false);

		// 5] Монета/Монеты/Монет
		self.m.s8.reword.declension = ko.computed(function(){

			var declension = declension_by_number(self.m.s8.reword.coins());
			if(declension == 1) return 'монета';
			if(declension == 2) return 'монеты';
			if(declension == 3) return 'монет';
			return 'монет';

		});

	//--------------------------//
	// s8.2. Модель будь онлайн //
	//--------------------------//
	self.m.s8.beonline = {};

		// 1] Сколько минут нужно быть непрерывно онлайн, чтобы получить раздачу //
		//-----------------------------------------------------------------------//
		self.m.s8.beonline.giveaway_period_min = ko.observable(server.data.counters.config.giveaway_period_min);

		// 2] Сколько секунд нужно быть оффлайн, чтобы сбросился счётчик онлайна //
		//-----------------------------------------------------------------------//
		self.m.s8.beonline.offline2drop_online_counter_sec = ko.observable(server.data.counters.config.offline2drop_online_counter_sec);

		// 3] Счётчик онлайна в секундах
		self.m.s8.beonline.counter = ko.observable((function(){
			if(server.data.counters.counters[0])
				return server.data.counters.counters[0].counter;
			else
				return 0;
		})());

		// 4] Клиентский timestamp последнего обновления на клиенте счётчика онлайна
		self.m.s8.beonline.counter_updated_at = ko.observable(Date.now());

		// 5] Кол-во секунд, оставшиеся до получения возможности получить раздачу
		self.m.s8.beonline.left4giveaway_s = ko.computed(function(){

			// 5.a] Пересчитывать это на каждой секунде
 			layoutmodel.m.s0.servertime.timestamp_s();

			// 5.1] Сколько секунд минут нужно быть непрерывно онлайн, чтобы получить раздачу
			var giveaway_period_sec = self.m.s8.beonline.giveaway_period_min()*60;

			// 5.2] Сколько секунд прошло с последнего обновления счётчика онлайна на клиенте
			var counter_update_left_sec = Math.round((Date.now() - self.m.s8.beonline.counter_updated_at())/1000);

			// 5.3] Вычислить кол-во секунд, оставшиеся до получения возможности получить раздачу
			var left4giveaway_s = giveaway_period_sec - self.m.s8.beonline.counter() - counter_update_left_sec;

			// 5.4] Если left4giveaway_s меньше нуля, записать в него 0
			if(left4giveaway_s < 0)
				left4giveaway_s = 0;

			// 5.5] Если left4giveaway_s == NaN, записать в него giveaway_period_sec
			if(isNaN(left4giveaway_s))
				left4giveaway_s = giveaway_period_sec;

			// 5.n] Вернуть результат
			return left4giveaway_s;

		});

		// 6] Кол-во времени, оставшиеся до получения возможности получить раздачу (для вывода на экран)
		self.m.s8.beonline.left4giveaway = {};

			// 6.1] Секунды, минуты и часы
			self.m.s8.beonline.left4giveaway.human 		= ko.observable("00:00:00");
			self.m.s8.beonline.left4giveaway.seconds 	= ko.observable("00");
			self.m.s8.beonline.left4giveaway.minutes 	= ko.observable("00");
			self.m.s8.beonline.left4giveaway.hours 		= ko.observable("00");

			// 6.2] Произвести вычисления
			ko.computed(function(){

				// Секунды
				self.m.s8.beonline.left4giveaway.seconds(moment.utc(self.m.s8.beonline.left4giveaway_s()*1000).format("ss"));

				// Минуты
				self.m.s8.beonline.left4giveaway.minutes(moment.utc(self.m.s8.beonline.left4giveaway_s()*1000).format("mm"));

				// Часы
				self.m.s8.beonline.left4giveaway.hours(moment.utc(self.m.s8.beonline.left4giveaway_s()*1000).format("HH"));

				// Человеко-понятный формат

					// Если время не вышло, показать в виде времени в формате HH:mm:ss
					if(self.m.s8.beonline.left4giveaway_s())
						self.m.s8.beonline.left4giveaway.human(moment.utc(self.m.s8.beonline.left4giveaway_s()*1000).format("HH:mm:ss"));

					// Если время вышло, показат надпись "Ожидайте"
					else
 						self.m.s8.beonline.left4giveaway.human("Ожидайте...");

			});

		// 7] Модель выдачи
		self.m.s8.beonline.giveaway = ko.observable(
			ko.mapping.fromJS(server.data.giveaway)
		);

		// 8] Виден ли спиннер на кнопке выдачи
		self.m.s8.beonline.is_spinner_vis = ko.observable(false);

	//-------------------------//
	// s8.3. Модель nick promo //
	//-------------------------//
	self.m.s8.nickpromo = {};

		// 1] Получил ли уже игрок 20 монет за добавление строки в ник //
		//-------------------------------------------------------------//
		self.m.s8.nickpromo.is_paid = ko.observable(server.data.nickpromo.is_paid);

		// 2] Количество монет, которые игрок может получить за добавление строки в ник //
		//------------------------------------------------------------------------------//
		self.m.s8.nickpromo.coins = ko.observable(server.data.nickpromo.coins);

		// 3] Монета/Монеты/Монет
		self.m.s8.nickpromo.declension = ko.computed(function(){

			var declension = declension_by_number(self.m.s8.nickpromo.coins());
			if(declension == 1) return 'монета';
			if(declension == 2) return 'монеты';
			if(declension == 3) return 'монет';
			return 'монет';

		});

		// 4] Показан ли спиннер на кнопке //
		//---------------------------------//
		self.m.s8.nickpromo.is_spinner_vis = ko.observable(false);

	//--------------------------------//
	// s8.4. Модель steam group promo //
	//--------------------------------//
	self.m.s8.steamgrouppromo = {};

		// 1] Получил ли уже игрок 20 монет за вступление в группу //
		//---------------------------------------------------------//
		self.m.s8.steamgrouppromo.is_paid = ko.observable(server.data.steamgrouppromo.is_paid);

		// 2] Количество монет, которые игрок может получить за вступление в группу //
		//--------------------------------------------------------------------------//
		self.m.s8.steamgrouppromo.coins = ko.observable(server.data.steamgrouppromo.coins);

		// 3] Монета/Монеты/Монет
		self.m.s8.steamgrouppromo.declension = ko.computed(function(){

			var declension = declension_by_number(self.m.s8.nickpromo.coins());
			if(declension == 1) return 'монета';
			if(declension == 2) return 'монеты';
			if(declension == 3) return 'монет';
			return 'монет';

		});

		// 4] Показан ли спиннер на кнопке //
		//---------------------------------//
		self.m.s8.steamgrouppromo.is_spinner_vis = ko.observable(false);


	//--------------------------------------//
	// s8.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	self.m.s8.indexes = {};

		// s1.n.1. Общие вычисления //
		//--------------------------//
		ko.computed(function(){



		}); //.extend({rateLimit: 10, method: "notifyWhenChangesStop"});


	//------------------------------------//
	// 			        		 	                //
	// 	W. Обработка websocket-сообщений  //
	// 			         			                //
	//------------------------------------//

	//----------------------------------//
	// w8.1. Обработка сообщений от M15 //
	//----------------------------------//

		//----------------------------------------------//
		// 1] Обработка сообщений через публичный канал //
		//----------------------------------------------//
		self.websocket.ws1.on('m15:public', function(data) {

			// 1] Получить имя задачи
			var task = data.data.data.task;

			// 2] В зависимости от task выполнить соотв.метод
			switch(task) {

				case "m15:freecoins:left2unblock": self.f.s8.fc_left2unblock(data.data.data.data); break;
				case "m15:freecoins:unblock": self.f.s8.fc_unblock(); break;

			}

		});

		//--------------------------------------------//
		// 2] Обработка сообщений через частный канал //
		//--------------------------------------------//
		if(JSON.parse(layout_data.data.auth).is_anon == 0) {
			self.websocket.ws1.on('m15:private:'+JSON.parse(layout_data.data.auth).user.id, function(data) {

				// 1] Получить имя задачи
				var task = data.data.data.task;

				// 2] В зависимости от task выполнить соотв.метод
				switch(task) {

					case "m15:freecoins:block": self.f.s8.fc_block(); break;

				}

			});
		}

	//----------------------------------//
	// w8.2. Обработка сообщений от M16 //
	//----------------------------------//

		//-------------------------------------------------//
		// 1] Обработка сообщений через публичный канал //
		//-------------------------------------------------//
		self.websocket.ws1.on('m16:public', function(data) {

			// 1] Получить имя задачи
			var task = data.data.data.task;

			// 2] В зависимости от task выполнить соотв.метод
			switch(task) {

				//case "m15:freecoins:left2unblock": self.f.s8.fc_left2unblock(data.data.data.data); break;

			}

		});

		//--------------------------------------------//
		// 2] Обработка сообщений через частный канал //
		//--------------------------------------------//
		if(JSON.parse(layout_data.data.auth).is_anon == 0) {
			self.websocket.ws1.on('m16:private:'+JSON.parse(layout_data.data.auth).user.id, function(data) {

				// 1] Получить имя задачи
				var task = data.data.data.task;

				// 2] В зависимости от task выполнить соотв.метод
				switch(task) {

					case "m16:counters:freshdata": self.f.s8.counters_freshdata(data.data.data.data); break;
					case "m16_giveaway_offer": self.f.s8.create_giveaway_resp(data.data.data); break;
					case "m16_new_giveaway": self.f.s8.new_giveaway(data.data.data.data); break;
					case "m16_del_giveaway": self.f.s8.del_giveaway(); break;



				}

			});
		}





	//-------------------------------------------------//
	// w8.2. Обработка сообщений через публичный канал //
	//-------------------------------------------------//






	//------------------------------//
	// 			        		 	          //
	// 	X. Подготовка к завершению  //
	// 			         			          //
	//------------------------------//

	//------------------------------------------//
	// X1. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self.m.s8;


}};	// конец модели









