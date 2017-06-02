/*//==================================================================================////
////																		                                              ////
////   Модель игры Jackpot, предназначенная для подключения в основной m.js	документа	////
////																				                                          ////
////==================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *
 * 	s1. Модель игры Jackpot
 *
 * 		s1.1. Модель комнат, раундов, статусов, ставок, поставивших пользователей
 * 		s1.2. Модель табов с доп.разделами Jackpot
 * 		s1.3. Модель поставленных на данный момент вещей
 *    s1.4. Модель интерфейса по распределению шансов в выбранной комнате
 *    s1.5. Модель статистики классической игры
 *    s1.6. Модель полосы аватаров текущего раунда выбранной комнаты
 *    s1.7. Победный билет, победитель, число для текущего раунда выбранной комнаты
 *    s1.8. Очередь задач для исполнения при достижении указанных timestamp'ов
 *    s1.9. Серверный timestamp, рассчитывающийся на клиенте, синхронизирующийся с сервером
 *    s1.10. Счётчики раундов для каждой комнаты
 *    s1.11. Модель анимации ленты аватаров текущей комнаты
 *    s1.12. Модель плавного появления ставок
 *    s1.13. URL звуков игры
 *    s1.14. Модель текущих джекпотов последних раундов всех комнат
 *    s1.15. Модель текущих состояний (на кону/розыгрыш) раундов всех комнат
 *    s1.n. Индексы и вычисляемые значения
 *
 *      s1.n.0. Индексы и вычисляемые без extend
 * 			s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ...
 * 			s1.n.2. Рассчитать значения всех счётчиков для выбранной комнаты и текущего раунда
 *      s1.n.3. Перерасчитать модель для отрисовки кольца
 * 			s1.n.4. Управление текущей позицией и св-вом transform
 *      s1.n.5. Добавление в текущий набор плавных ставок новых ставок
 *      s1.n.6. Передавать в модель шаблона кое-какие данные
 *      s1.n.7. Проигрывать звук тиков игры
 *
 *  W. Обработка websocket-сообщений
 *
 * 		w8.1. Обработка сообщений через публичный канал
 * 		w8.2. Обработка сообщений через частный канал
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
var ModelJackpot = { constructor: function(self, m) { m.s1 = this;

	//---------------------------//
	// 			        		 	       //
	// 	s1. Модель игры Jackpot  //
	// 			         			       //
	//---------------------------//

	//---------------------------------------------------------------------------//
	// s1.1. Модель комнат, раундов, статусов, ставок, поставивших пользователей //
	//---------------------------------------------------------------------------//
	// Комнаты                      				   Комната
	// - Раунды                     				    /   \
	//   - Статусы                  				Раунд   Раунд
	//   - Ставки                         /   \       /   \
	//     - Пользователи            Статус Ставки  Ставки Статус
  //                                        /       \
  //                                 Пользователи Пользователи
  //
	self.m.s1.game = {};

		// 1] Наблюдаемый массив с комнатами //
		//-----------------------------------//
		self.m.s1.game.rooms = ko.observableArray([]);

		// 2] Наблюдаемый массив со списком возможных игровых статусов //
		//-------------------------------------------------------------//
		self.m.s1.game.statuses = ko.observableArray([]);

		// 3] Выбранная комната //
		//----------------------//
		self.m.s1.game.choosen_room = ko.observable("");

		// 4] Раскрыт ли список комнат //
		//-----------------------------//
		self.m.s1.game.is_room_list_expanded = ko.observable(false);

		// 5] Модель текущего и предудыщего раундов для выбранной комнаты //
		//----------------------------------------------------------------//
		self.m.s1.game.curprev = ko.observable({
			current: ko.observable(''),
			previous: ko.observable('')
		});

		// 6] Текущий джекпот текущего раунда //
		//------------------------------------//
		self.m.s1.game.curjackpot = ko.observable(0);

		// 7] Оставшееся до конца текущего раунда время, для choosen_room //
		//----------------------------------------------------------------//
		self.m.s1.game.timeleft = {};

			// 7.1] В секундах //
			//-----------------//
			self.m.s1.game.timeleft.sec = ko.observable("");

			// 7.2] В человеко-понятном формате //
			//----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft.human = ko.observable("");

			// 7.3] В секундах, на момент старта Lottery для этого клиента //
			//-------------------------------------------------------------//
			self.m.s1.game.timeleft.client_start_sec = ko.observable("");

			// 7.4] Секунды/минуты/часы //
			//--------------------------//
			self.m.s1.game.timeleft.seconds = ko.observable("");
			self.m.s1.game.timeleft.minutes = ko.observable("");
			self.m.s1.game.timeleft.hours = ko.observable("");

		// 8] Состояние текущего раунда //
		//------------------------------//
		self.m.s1.game.choosen_status = ko.computed(function(){

			// 1] Проверить наличие необходимых ресурсов
			if(!self.m.s1.game.curprev().current().rounds_statuses) return "";

			// 2] Записать имя статуса текущего раунда текущей комнаты в choosen_status
			return self.m.s1.game.curprev().current().rounds_statuses()[self.m.s1.game.curprev().current().rounds_statuses().length-1].status();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// 9] Палитра цветов для игроков текущего раунда //
		//-----------------------------------------------//
		// - Загружается с сервера.
		self.m.s1.game.palette = ko.observableArray([]);

		// 10] Работа с кривой Безье и вращением колеса //
		//----------------------------------------------//
		self.m.s1.game.bezier = {};

			// 10.1] Параметры кривой Безье //
			//------------------------------//
			self.m.s1.game.bezier.params = [.32, .64, .45, 1];

			// 10.2] Готовое значение cubic-bezier для CSS //
			//---------------------------------------------//
			self.m.s1.game.bezier.cssvalue = ko.computed(function(){
				var p = self.m.s1.game.bezier.params;
				return "cubic-bezier("+p[0]+","+p[1]+","+p[2]+","+p[3]+")";
			}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

			// 10.3] Параметры кривой Безье //
			//------------------------------//
			// - Примеры:
			// 		self.m.s1.game.bezier.get(.2);
			// 		self.m.s1.game.bezier.get(.6);
			// 		self.m.s1.game.bezier.get(.9);
			self.m.s1.game.bezier.get = function(time){
				return self.f.s1.bezier.cubicBezier(
						self.m.s1.game.bezier.params[0],
						self.m.s1.game.bezier.params[1],
						self.m.s1.game.bezier.params[2],
						self.m.s1.game.bezier.params[3],
						time,
						self.m.s1.game.choosen_room().lottery_duration_ms()
				);
			};

		// 11] Игрок-победитель текущего раунда в выбранной комнате //
		//----------------------------------------------------------//
		self.m.s1.game.choosen_room_curround_winner = ko.observable("");

		// 12] Оставшееся до конца состояния Pending время, для choosen_room //
		//-------------------------------------------------------------------//
		self.m.s1.game.timeleft_pending = {};

			// 12.1] В секундах //
			//------------------//
			self.m.s1.game.timeleft_pending.sec = ko.observable("");

			// 12.2] В человеко-понятном формате //
			//-----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft_pending.human = ko.observable("");

		// 13] Оставшееся до конца состояния Winner время, для choosen_room //
		//------------------------------------------------------------------//
		self.m.s1.game.timeleft_winner = {};

			// 13.1] В секундах //
			//------------------//
			self.m.s1.game.timeleft_winner.sec = ko.observable("");

			// 13.2] В человеко-понятном формате //
			//-----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft_winner.human = ko.observable("");

		// 14] Оставшееся до конца раунда время (timeleft + pending + поправка) //
		//----------------------------------------------------------------------//
		self.m.s1.game.timeleft_final = {};

			// 14.1] В секундах, минутах, часах //
			//----------------------------------//
			self.m.s1.game.timeleft_final.sec = ko.observable("");

			// 14.2] В человеко-понятном формате //
			//-----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft_final.human = ko.observable("");

			// 14.3] Секунды/минуты/часы //
			//---------------------------//
			self.m.s1.game.timeleft_final.seconds = ko.observable("");
			self.m.s1.game.timeleft_final.minutes = ko.observable("");
			self.m.s1.game.timeleft_final.hours = ko.observable("");

		// 15] Оставшееся до начала следующего раунда время //
		//--------------------------------------------------//
		self.m.s1.game.timeleft2start_final = {};

			// 15.1] В секундах, минутах, часах //
			//----------------------------------//
			self.m.s1.game.timeleft2start_final.sec = ko.observable("");

			// 15.2] В человеко-понятном формате //
			//-----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft2start_final.human = ko.observable("");

			// 15.3] Секунды/минуты/часы //
			//---------------------------//
			self.m.s1.game.timeleft2start_final.seconds = ko.observable("");
			self.m.s1.game.timeleft2start_final.minutes = ko.observable("");
			self.m.s1.game.timeleft2start_final.hours = ko.observable("");

		// 16] Оставшееся до конца состояния Winner время, для choosen_room //
		//------------------------------------------------------------------//
		self.m.s1.game.timeleft_lottery = {};

			// 16.1] В секундах //
			//------------------//
			self.m.s1.game.timeleft_lottery.sec = ko.observable("");

			// 16.2] В человеко-понятном формате //
			//-----------------------------------//
			// - Например: "00:08:20"
			self.m.s1.game.timeleft_lottery.human = ko.observable("");

		// 17] Trade URL бота, который должен принимать ставки в текущем раунде выбранной комнаты до статуса Lottery не включительно //
	  //---------------------------------------------------------------------------------------------------------------------------//
		self.m.s1.game.current_bot = ko.observable("");

		// 18] Trade URL бота, который должен принимать ставки в текущем раунде выбранной комнаты от статуса Lottery включительно и выше //
	  //-------------------------------------------------------------------------------------------------------------------------------//
		self.m.s1.game.next_bot = ko.observable("");


	//--------------------------------------------//
	// s1.2. Модель табов с доп.разделами Jackpot //
	//--------------------------------------------//
	self.m.s1.maintabs = {};

		// 1] Наблюдаемый массив главных табов игры "Лотерея" //
		//----------------------------------------------------//
		self.m.s1.maintabs.list = ko.mapping.fromJS([
			{
				name: 'game'
			},
			{
				name: 'history'
			}
		]);

		// 2] Выбранный таб //
		//------------------//
		self.m.s1.maintabs.choosen = ko.observable(self.m.s1.maintabs.list()[0]);

	//--------------------------------------------------//
	// s1.3. Модель поставленных на данный момент вещей //
	//--------------------------------------------------//
	self.m.s1.bank = {};

		// 1] Наблюдаемый массив всех поставленынх в наст.момент вещей в текущем раунда //
		//------------------------------------------------------------------------------//
		// - Отсортированный по цене вещей.
		// - Складывается из всех вещей всех ставок всех пользователей текущего раунда, выбранной комнаты.
		self.m.s1.bank.items_sorted = ko.observableArray([]);

		// 2] Суммарная стоимость поставленных вещей //
		//-------------------------------------------//
		self.m.s1.bank.sum = ko.observable(0);

		// 3] Состояние индикатора поставленных вещей текущей комнаты в % //
		//----------------------------------------------------------------//
		self.m.s1.bank.indicator_percents = ko.observable(0);

		// 4] Количество внесённых игроком вещей в текущем раунде в выбранной комнате
		self.m.s1.bank.itemsnum = ko.observable(0);

		// 5] Шансы игрока в текущем раунде в выбранной комнате
		self.m.s1.bank.bets = ko.observable(0);

		// 6] Надпись о кол-ве внесённых предметов с падежами
		self.m.s1.bank.itemsnum_human = ko.observable('');

	//---------------------------------------------------------------------//
	// s1.4. Модель интерфейса по распределению шансов в выбранной комнате //
	//---------------------------------------------------------------------//
	self.m.s1.game.wheel = {};
	
		// 1] Данные по каждому игроку текущего раунд (кто, сколько в сумме поставил, какой цвет и т.д.) //
		//-----------------------------------------------------------------------------------------------//
		self.m.s1.game.wheel.data = ko.observableArray([]);
		
		// 2] Данные для текущего аутентифицированного игрока
		self.m.s1.game.wheel.currentuser = ko.observable();

		// 3] Индекс распределения шансов по пользователям текущего раунда
		self.m.s1.game.wheel.user_bet_index = ko.mapping.fromJS({});

	//-------------------------------------------//
	// s1.5. Модель статистики классической игры //
	//-------------------------------------------//
	self.m.s1.game.stats = {};

		//-----------------------------------------------------------------------------//
		// s1.5.1. Модель общеигровой статистики "Наибольшая ставка" (the biggest bet) //
		//-----------------------------------------------------------------------------//
		self.m.s1.game.stats.thebiggestbet = {};

			// 1] Актуальные статистические данные
			self.m.s1.game.stats.thebiggestbet.data = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_thebiggestbet.data.thebiggestbet);

			// 2] Перевернута ли карта
			self.m.s1.game.stats.thebiggestbet.is_card_flipped = ko.observable(false);

			// 3] Модель front-стороны карты
			self.m.s1.game.stats.thebiggestbet.front = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_thebiggestbet.data.thebiggestbet);

			// 4] Модель back-стороны карты
			self.m.s1.game.stats.thebiggestbet.back = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_thebiggestbet.data.thebiggestbet);

		//----------------------------------------------------------------------------//
		// s1.5.2. Модель общеигровой статистики "Счастливчик дня" (lucky of the day) //
		//----------------------------------------------------------------------------//
		self.m.s1.game.stats.luckyoftheday = {};

			// 1] Актуальные статистические данные
			self.m.s1.game.stats.luckyoftheday.data = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_luckyoftheday.data.luckyoftheday);

			// 2] Перевернута ли карта
			self.m.s1.game.stats.luckyoftheday.is_card_flipped = ko.observable(false);

			// 3] Модель front-стороны карты
			self.m.s1.game.stats.luckyoftheday.front = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_luckyoftheday.data.luckyoftheday);

			// 4] Модель back-стороны карты
			self.m.s1.game.stats.luckyoftheday.back = ko.mapping.fromJS(server.data.classicgame_stats.classicgame_stats_luckyoftheday.data.luckyoftheday);

		//--------------------------------------------------------------------------------------------------------//
		// s1.5.3. Модель статистики последнего раунда выбранной комнаты "Последний победитель" (the last winner) //
		//--------------------------------------------------------------------------------------------------------//
		self.m.s1.game.stats.thelastwinner = {};

			// 1] Перевернута ли карта
			self.m.s1.game.stats.thelastwinner.is_card_flipped = ko.observable(false);

			// 2] Модель front-стороны карты
			self.m.s1.game.stats.thelastwinner.front = ko.mapping.fromJS({});

			// 3] Модель back-стороны карты
			self.m.s1.game.stats.thelastwinner.back = ko.mapping.fromJS({});


	//----------------------------------------------------------------//
	// s1.6. Модель полосы аватаров текущего раунда выбранной комнаты //
	//----------------------------------------------------------------//
	self.m.s1.game.strip = {};

		// 1] Сама полоса аватаров
		self.m.s1.game.strip.avatars = ko.observableArray([]);

		// 2] Ширина полосы аватаров
		self.m.s1.game.strip.width = ko.observableArray(0);

		// 3] Исходная позиция полосы аватаров
		self.m.s1.game.strip.start_px = ko.observable(880); // 880

		// 4] Финальная позиция полосы аватаров
		self.m.s1.game.strip.final_px = ko.computed(function(){

			// 4.1] Если отсутствуют необходимые ресурсы, вернуть 0
			if(!self.m.s1.game.choosen_room())
				return 0;

			// 4.2] Ширина аватара в px с учётом отступа справа
			var avatarwidth_origin = 80;
			var avatarrightmargin = 2;
			var avatarwidth = +avatarwidth_origin + +avatarrightmargin;

			// 4.3] Получить ширину всей ленты
			var width = self.m.s1.game.strip.width();

			// 4.4] Получить поправку для установки позиции в конец ленты
			var endfix = (6*avatarwidth)-62;

			// 4.5] Вычислить позицию в начале 100-го аватара (победителя)
			var winnerpos = width - endfix - (11*avatarwidth);

			// 4.6] Получить значение
			var avatar_winner_stop_percents = (function(){

				// 4.6.1] Если предыдущий раунд есть, и статус Finished, Created или First bet, берём смещение из предыдущего раунда
				if(self.m.s1.game.choosen_room().rounds().length > 1 && ['Finished', 'Created', 'First bet'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[self.m.s1.game.choosen_room().rounds()[0].rounds_statuses().length - 1].status()) != -1)
					return self.m.s1.game.choosen_room().rounds()[1].avatar_winner_stop_percents();

				// 4.6.2] Иначе, берём смещение из текущего раунда
				else
					return self.m.s1.game.choosen_room().rounds()[0].avatar_winner_stop_percents();

			})();

			// 4.7] Вычислить позицию с учётом avatar_winner_stop_percents
			var winnerpos_final = -(winnerpos + avatarwidth_origin*(avatar_winner_stop_percents/100));

			// 4.n] Вернуть результаты
			return winnerpos_final;

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// 5] Текущая позиция полосы с учётом avatar_winner_stop_percents
		self.m.s1.game.strip.currentpos = ko.observable(self.m.s1.game.strip.start_px());

		// 6] Значение свойства transform полосы
		self.m.s1.game.strip.transform = ko.observable('none');

		// 7] Номер предыдущего аватара, проскочившего через стрелку в текущей комнате
		self.m.s1.game.strip.avatar_arrow_num_prev = ko.observable('0');


	//-------------------------------------------------------------------------------//
	// s1.7. Победный билет, победитель, число для текущего раунда выбранной комнаты //
	//-------------------------------------------------------------------------------//
	self.m.s1.game.lwpanel = {};

		// 1] Победный билет
		self.m.s1.game.lwpanel.ticket = ko.observable('???');

		// 2] Имя победителя раунда
		self.m.s1.game.lwpanel.winner = ko.observable('???');

		// 3] Число текущего раунда
		self.m.s1.game.lwpanel.number = ko.observable('???');

		// n] Вычисления
		ko.computed(function(){

			// n.1] Победный билет
			self.m.s1.game.lwpanel.ticket((function(){

				// Если состояние текущего раунда в выбранной комнате: Winner
				if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
					if(self.m.s1.game.choosen_room().rounds()[0].key() && self.m.s1.game.choosen_room_curround_winner())
						return '#' + self.m.s1.game.choosen_room().rounds()[0].ticket_winner_number();
					else
						return '???';
				}

				// В противном случае
				else {
					return '???';
				}

			})());

			// n.2] Имя победителя раунда
			self.m.s1.game.lwpanel.winner((function(){

				// 1.1] Если состояние текущего раунда в выбранной комнате: Winner
				if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
					if(self.m.s1.game.choosen_room().rounds()[0].key() && self.m.s1.game.choosen_room_curround_winner())
						return self.m.s1.game.choosen_room_curround_winner().nickname();
					else
						return '???';
				}

				// 1.2] В противном случае
				else {
					return '???';
				}

			})());

			// n.3] Число текущего раунда
			self.m.s1.game.lwpanel.number((function(){

				// Если состояние текущего раунда в выбранной комнате: Winner
				if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
					if(self.m.s1.game.choosen_room().rounds()[0].key() && self.m.s1.game.choosen_room_curround_winner())
						return self.m.s1.game.choosen_room().rounds()[0].key();
					else
						return '???';
				}

				// В противном случае
				else {
					return '???';
				}

			})());

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});


	//--------------------------------------------------------------------------//
	// s1.8. Очередь задач для исполнения при достижении указанных timestamp'ов //
	//--------------------------------------------------------------------------//
	// - Формат:
	//
	// 		[
	//  		{
	//  			unixtimestamp: 1484142425,
	//        func: function(){}.bind(null, 1, 2, 3)
	//  		}
	// 		]
	//
	self.m.s1.game.queue = ko.observableArray([]);

	//---------------------------------------------------------------------------------------//
	// s1.9. Серверный timestamp, рассчитывающийся на клиенте, синхронизирующийся с сервером //
	//---------------------------------------------------------------------------------------//
	self.m.s1.game.time = {};

		// 1] Сколько ms прошло с момента загрузки документа
		self.m.s1.game.time.gone_ms = ko.observable(0);

		// 2] Получить смещение часовой зоны клиента в секундах относ. UTC
		self.m.s1.game.time.utc_offset_s = ko.observable((function(){

			var d = new Date();
			return d.getTimezoneOffset()*60;

		})());

		// 3] Клиентский timestamp в секундах в UTC на момент загрузки документа
		self.m.s1.game.time.client_ts_utc_s = ko.observable(Math.floor(Date.now()/1000) + self.m.s1.game.time.utc_offset_s());

		// 4] Расчёт текущего серверного unix timestamp
		self.m.s1.game.time.ts = ko.observable(layoutmodel.m.s0.servertime.timestamp_s());
		ko.computed(function(){

			// 4.1] Пусть оно срабатывает каждые 100 мс
			self.m.s1.game.time.gone_ms();

			// 4.2] Получить текущий timestamp в UTC в мс и с
			var current_ts_ms = new Date().getTime();
			var current_ts_s = Math.floor(current_ts_ms/1000);

			// 4.3] Получить дельту в мс между m.s1.game.time.ts и current_ts_s
			var delta_ms = +layoutmodel.m.s0.servertime.timestamp_s()*1000 - +current_ts_ms;

			// 4.4] Сформировать текущий timestamp в UTC в мс (с учётом delta)
			var current_ts_with_delta_ms = Math.floor(+current_ts_ms+delta_ms);

			// 4.5] Получить разницу в мс между current_ts_with_delta_ms и m.s1.game.time.ts
			var delta2_ms = current_ts_with_delta_ms - self.m.s1.game.time.ts()*1000;

			// 4.6] Если time.ts отличается от current_ts_s, записать current_ts_s
			if(current_ts_with_delta_ms > self.m.s1.game.time.ts()*1000) {

				// 4.6.1] Если delta2_ms <= 1000
				if(delta2_ms <= 1000 || !self.m.s1.game.time.ts())
					return self.m.s1.game.time.ts(Math.floor(current_ts_with_delta_ms/1000));

				// 4.6.2] Иначе, увеличить time.ts лишь на 1 секунду
				else
					self.m.s1.game.time.ts(+self.m.s1.game.time.ts() + 1)

			}

		});

	//--------------------------------------------//
	// s1.10. Счётчики раундов для каждой комнаты //
	//--------------------------------------------//
	// - Когда будут приходить новые игровые данные, надо добавлять их в очередь.
	// - Ориентироваться надо по счётчику, который отстаёт.
	// - К моменту, когда он будет доходить до той или иной точки, когда
	//   должны быть применены новые данные, эти данные уже должны находиться
	//   в очереди задачи, и быть хотовыми к применению.
	self.m.s1.game.counters = {};

		// 1] Единый счётчик текущего раунда, который отстаёт на delta
		self.m.s1.game.counters.main = {};

			// 1.1] Секунды
			self.m.s1.game.counters.main.sec = ko.observable(0);

			// 1.2] Для вывода на экран: секунды, минуты, часы
			self.m.s1.game.counters.main.seconds = ko.observable(0);
			self.m.s1.game.counters.main.minutes = ko.observable(0);
			self.m.s1.game.counters.main.hours = ko.observable(0);

		// 2] Delta в секундах, на которую будет отставать "отстающий" счётчик
		self.m.s1.game.counters.delta = ko.observable(5);

		// 3] Счётчик до начала розыгрыша (производный от единого счётчика)
		self.m.s1.game.counters.lottery = {};

			// 3.1] Секунды
			self.m.s1.game.counters.lottery.sec = ko.observable(0);

			// 3.2] Для вывода на экран: секунды, минуты, часы
			self.m.s1.game.counters.lottery.seconds = ko.observable("00");
			self.m.s1.game.counters.lottery.minutes = ko.observable("00");
			self.m.s1.game.counters.lottery.hours = ko.observable("00");

		// 4] Счётчик до начала новой игры (производный от единого счётчика)
		self.m.s1.game.counters.newgame = {};

			// 4.1] Секунды
			self.m.s1.game.counters.newgame.sec = ko.observable(0);

			// 4.2] Для вывода на экран: секунды, минуты, часы
			self.m.s1.game.counters.newgame.seconds = ko.observable("00");
			self.m.s1.game.counters.newgame.minutes = ko.observable("00");
			self.m.s1.game.counters.newgame.hours = ko.observable("00");


	//-------------------------------------------------------//
	// s1.11. Модель анимации ленты аватаров текущей комнаты //
	//-------------------------------------------------------//
	self.m.s1.animation = {};

		// 1] При каких обстоятельствах была открыта выбранная комната
		// - Timestamp, статус и номер раунда
		self.m.s1.animation.circumstances = {};

			// 1.1] Номер раунда
			self.m.s1.animation.circumstances.round_number = ko.observable(0);

			// 1.2] Какой был серверный unix timestamp в секундах
			self.m.s1.animation.circumstances.timestamp_s = ko.observable(0);

			// 1.3] Какой был статус того раунда, чей номер указан
			self.m.s1.animation.circumstances.round_status = ko.observable(0);

		// 2] Возможные типы моделей анимаций
		self.m.s1.animation.types = ko.mapping.fromJS([
			{
				name: 'css',
				description: 'Анимация посредствам CSS transition.'
			},
			{
				name: 'js',
				description: 'Анимация посредствам функции cubic-bezier через JS.'
			}
		]);

		// 3] Выбранный для текущего раунда выбранной комнаты типа анимации
		// - По умолчанию выбираем CSS-анимацию.
		// - Но если комната открыта в состояниях Lottery/Winner, то JS-анимацию.
		self.m.s1.animation.choosen_type = ko.observable(self.m.s1.animation.types()[1]);

		// 4] Работа с кривой Безье и вращением колеса //
		//----------------------------------------------//
		self.m.s1.animation.bezier = {};

			// 4.1] Параметры кривой Безье //
			//-----------------------------//
			self.m.s1.animation.bezier.params = [.17, .01, 0, 1];

			// 4.2] Параметры кривой Безье //
			//-----------------------------//
			// - В качестве time указывается число от 0 до 1, обозначающее прогресс от 0 до 100%.
			// - Примеры:
			// 		self.m.s1.animation.bezier.get(.2);
			// 		self.m.s1.animation.bezier.get(.6);
			// 		self.m.s1.animation.bezier.get(.9);
			self.m.s1.animation.bezier.get = function(time){
				return self.f.s1.bezier.cubicBezier(
						self.m.s1.game.bezier.params[0],
						self.m.s1.game.bezier.params[1],
						self.m.s1.game.bezier.params[2],
						self.m.s1.game.bezier.params[3],
						time,
						+self.m.s1.game.choosen_room().lottery_duration_ms() + +self.m.s1.game.choosen_room().lottery_client_delta_ms()
				);
			};	

		// 5] Вкл/Выкл css-анимацию
		self.m.s1.game.strip.is_css_animation_on = ko.observable(true);

		// 6] Длительность анимации ленты аватаров
		self.m.s1.game.strip.duration = ko.computed(function(){

			if(self.m.s1.game.strip.is_css_animation_on() == true && self.m.s1.game.choosen_room())
				return (+self.m.s1.game.choosen_room().lottery_duration_ms()/1000 + +self.m.s1.game.choosen_room().lottery_client_delta_ms()/1000) +  's';

			else
				return '0s';

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// 7] Список комнат, в которых сейчас работает функция f.m1.lottery
		// - То есть, повторно запускать её в этих комнатах не следует.
		self.m.s1.game.strip.rooms_with_working_animation = ko.observableArray([]);


	//-------------------------------------------//
	// s1.12. Модель плавного появления ставок	 //
	//-------------------------------------------//
	self.m.s1.smoothbets = {};

		// 1] Текущий набор ставок в текущей комнате //
		//-------------------------------------------//
		self.m.s1.smoothbets.bets = ko.observableArray([]);

	//------------------------//
	// s1.13. URL звуков игры	//
	//------------------------//
	self.m.s1.sounds = {};

		// 1] Новая ставка
		self.m.s1.sounds['bet'] = [
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/bet-1.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/bet-2.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/bet-3.mp3'
		];

		// 2] Новое сообщение в чате
		self.m.s1.sounds['add'] = [
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/chat-message-add-1.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/chat-message-add-2.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/chat-message-add-3.mp3'
		];

		// 3] Старт рулетки (барабан начинает крутиться)
		self.m.s1.sounds['lottery'] = [
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/roulette-start-1.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/roulette-start-2.mp3',
			layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/roulette-start-3.mp3'
		];

		// n] Прочие звуки
		self.m.s1.sounds['chat-new'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/chat-message-send.mp3';
		self.m.s1.sounds['click'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/click.mp3';
		self.m.s1.sounds['game-start'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/game-start.mp3';
		self.m.s1.sounds['win'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/win.mp3';
		self.m.s1.sounds['timer-tick-quiet'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/timer-tick-quiet.mp3';
		self.m.s1.sounds['timer-tick-last-5-seconds'] = layout_data.data.request.secure + layout_data.data.request.host + ((/:\d+.*$/i).test(layout_data.data.request.host) ? "" : ':'+layout_data.data.request.port) + '/' + 'public/L10003/assets/sound/classicgame/timer-tick-last-5-seconds.mp3';

	//-----------------------------------------//
	// s1.14. Модель истории классической игры //
	//-----------------------------------------//
	self.m.s1.history = {};

		// 1] История классической игры для всех комнат
		self.m.s1.history.all = ko.observable({});

		// 2] Индикатор наличия истории для текущей комнаты
		self.m.s1.history.is_in_choosen_room = ko.observable(false);

		// 3] Видим ли спинер загрузки доп.истории на кнопке "Показать ещё..."
	 	self.m.s1.history.is_more_history_spinner_vis = ko.observable(false);

		// 4] Общее кол-во единиц истории для всех комнат, чья история загружена
		self.m.s1.history.totalcount = ko.observable({});

		// 5] Сколько страниц истории загружено для всех комнат, чья история загружена
		self.m.s1.history.pagenums = ko.observable({});

	//---------------------------------------------------------------//
	// s1.14. Модель текущих джекпотов последних раундов всех комнат //
	//---------------------------------------------------------------//
	// - Инициируется в X1.3.
	self.m.s1.room_jackpots = ko.observable();

	//------------------------------------------------------------------------//
	// s1.15. Модель текущих состояний (на кону/розыгрыш) раундов всех комнат //
	//------------------------------------------------------------------------//
	// - Инициируется в X1.3.
	self.m.s1.room_states = ko.observable();


	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	self.m.s1.indexes = {};

		// s1.n.0. Индексы и вычисляемые без extend //
		//------------------------------------------//
		ko.computed(function(){

			//------------------//
			// 1] Индекс комнат //
			//------------------//
			// - По ID комнаты можно получить ссылку на соотв. объект в self.m.s1.game.rooms
			self.m.s1.indexes.rooms = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.rooms().length; i++) {
					results[self.m.s1.game.rooms()[i].id()] = self.m.s1.game.rooms()[i];
				}

				// 3. Вернуть results
				return results;

			}());

			//------------------------------------------------------------------//
			// 2] Если комната не выбрана, выбрать её функцией f.s1.choose_room //
			//------------------------------------------------------------------//
			(function(){

				if(!self.m.s1.game.choosen_room()) {
					if(server.data.choosen_room_id != 0) {
						self.f.s1.choose_room(self.m.s1.indexes.rooms[server.data.choosen_room_id]);
					}
				}

			})();

			//--------------------------------------------------------------------//
			// 5] Наполнить модель текущего/предыдущего раундов выбранной комнаты //
			//--------------------------------------------------------------------//
			(function(){

				// 1] Обновить ссылку на объект текущего раунда выбранной комнаты
				if(self.m.s1.game.choosen_room() && self.m.s1.game.choosen_room().rounds()[0])
					self.m.s1.game.curprev().current(self.m.s1.game.choosen_room().rounds()[0]);

				// 2] Обновить ссылку на объект предыдущего раунда выбранной комнаты
				if(self.m.s1.game.choosen_room() && self.m.s1.game.choosen_room().rounds()[1])
					self.m.s1.game.curprev().previous(self.m.s1.game.choosen_room().rounds()[1]);

			})();

	  });

		// s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ... //
		//-------------------------------------------------------------------//
		ko.computed(function(){

			//--------------------------------------------------------------//
			// 1] Индекс состояний игры модели для отладки игровой механики //
			//-------------------------------------------------------------//
			// - По ID состояния игры можно получить ссылку на соотв. объект в self.m.s1.game.statuses
			self.m.s1.indexes.statuses = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.statuses().length; i++) {
					results[self.m.s1.game.statuses()[i]().id()] = self.m.s1.game.statuses()[i]();
				}

				// 3. Вернуть results
				return results;

			}());

			//------------------------------------------------------------------------------------------//
			// 2] Индекс позиций состояний игры модели для отладки игровой механики в массиве состояний //
			//------------------------------------------------------------------------------------------//
			// - По status состояния игры можно получить индекс состояния в массиве self.m.s6.game.statuses
			self.m.s1.indexes.positions = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.statuses().length; i++) {
					results[self.m.s1.game.statuses()[i]().status()] = i;
				}

				// 3. Вернуть results
				return results;

			}());

			//------------------------------------------------------//
			// 3] Ссылки на текущий и предыдущий раунды всех комнат //
			//------------------------------------------------------//
			// - По имени комнаты мы можем получить ссылку на объект.
			// - В этом объекте лишь 2 свойства, current и previous.
			// - Current всегда ссылается на текущий раунд данной комнаты.
			// - Previous всегда ссылается на предыдущий раунд данной комнаты.
			self.m.s1.indexes.curprev = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Наполнить results
				for(var i=0; i<self.m.s1.game.rooms().length; i++) {

					results[self.m.s1.game.rooms()[i].name()] = {
						current: self.m.s1.game.rooms()[i].rounds()[0] ? self.m.s1.game.rooms()[i].rounds()[0] : {},
						previous: self.m.s1.game.rooms()[i].rounds()[1] ? self.m.s1.game.rooms()[i].rounds()[1] : {}
					};

				}

				// 3. Вернуть results
				return results;

			}());

			//-----------------------------------------------------//
			// 4] Индекс игроков текущего раунда выбранной комнаты //
			//-----------------------------------------------------//
			// - По ID игрока можно получить ссылку на него в m.s1.game
			// - Обновляет m.s1.game.wheel.user_bet_index
			(function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
					results[self.m.s1.game.wheel.data()[i].user().id()] = self.m.s1.game.wheel.data()[i];
				}

				// 3. Обновить m.s1.indexes.users
				ko.mapping.fromJS(results, self.m.s1.game.wheel.user_bet_index);

			})();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"}); //.extend({deferred: true});
		ko.computed(function(){

			//-----------------------------------------------//
			// 1] Рассчитать текущий джекпот текущего раунда //
			//-----------------------------------------------//
			(function(){

				// 1. Если необходимые наблюдаемые отсутствуют, завершить
				if(!self.m.s1.game.choosen_room()) return;

				// 2. Получить короткую ссылку на bets текущего раунда выбранной комнаты
				var bets = self.m.s1.game.choosen_room().rounds()[0].bets();

				// 3. Подсчитать и записать m.s1.game.curjackpot
				(function(){

					// Подсчитать общую суммарную стоимость вещей на кону
					var bank_sum = (function(){
						var result = 0;
						for(var i=0; i<bets.length; i++) {
							for(var j=0; j<bets[i]['m8_items']().length; j++) {
								result = +result + Math.round(+bets[i]['m8_items']()[j].price()*100);
							}
						}
						return result;
					})();

					// Записать bank_sum в наблюдаемую
					self.m.s1.game.curjackpot(bank_sum);

				})();

			})();

			////----------------------------------------//
			//// 2] Вычислить состояние текущего раунда //
			////----------------------------------------//
			//(function(){
			//
			//	// 1] Проверить наличие необходимых ресурсов
			//	if(!self.m.s1.game.curprev().current().rounds_statuses) return;
			//
			//	// 2] Записать имя статуса текущего раунда текущей комнаты в choosen_status
			//	self.m.s1.game.choosen_status(self.m.s1.game.curprev().current().rounds_statuses()[self.m.s1.game.curprev().current().rounds_statuses().length-1].status());
			//
			//})();

			//-------------------------//
			// 3] Индекс главных табов //
			//-------------------------//
			// - По name гл.таба можно получить ссылку на оный в m.s1.maintabs.list
			self.m.s1.indexes.maintabs = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.maintabs.list().length; i++) {
					results[self.m.s1.maintabs.list()[i].name()] = self.m.s1.maintabs.list()[i];
				}

				// 3. Вернуть results
				return results;

			}());

			//-------------------------------------------------------------------------------------//
			// 4] Вычисление bank.items_sorted и подсчитать суммарную стоимость поставленных вещей //
			//-------------------------------------------------------------------------------------//
			(function(){

				// 1.1] Если нет необходимых ресурсов, завершить
				if(!self.m.s1.indexes.curprev || !self.m.s1.game.choosen_room() || !self.m.s1.indexes.curprev[self.m.s1.game.choosen_room().name()].current || !self.m.s1.indexes.curprev[self.m.s1.game.choosen_room().name()].current.bets) return;

				// 1.2] Очистить bank.items_sorted
				self.m.s1.bank.items_sorted.removeAll();

				// 1.3] Записать ссылку на ставки текущего раунда в короткую переменную
				var bets = self.m.s1.indexes.curprev[self.m.s1.game.choosen_room().name()].current.bets();

				// 1.4] Наполнить bank.items_sorted
				for(var i=0; i<bets.length; i++) {
					for(var j=0; j<bets[i].m8_items().length; j++) {
						self.m.s1.bank.items_sorted.push(bets[i].m8_items()[j]);
					}
				}

				// 1.5] Отсортировать bank.items_sorted по цене
				self.m.s1.bank.items_sorted.sort(function(a,b){

					// По цене
					if(+a.price()*100 < +b.price()*100) return 1;
					else if(+a.price()*100 > +b.price()*100) return -1;
					return 0;

				});

				// 1.6] Подсчитать суммарную стоимость поставленных вещей
				self.m.s1.bank.sum((function(){

					var result = 0;
					for(var i=0; i<self.m.s1.bank.items_sorted().length; i++) {
						result = +result + +Math.round(self.m.s1.bank.items_sorted()[i].price()*100);
					}
					return result;

				})());

			})();
			
			//--------------------------------------------------------------------//
			// 5] Вычислить игрока-победителя текущего раунда в выбранной комнате //
			//--------------------------------------------------------------------//
			(function(){

				// 1. Если статус раунда Created
				if(self.m.s1.game.choosen_status() == "Created") {

					// 1] Обнулить значнеие choosen_room_curround_winner
					self.m.s1.game.choosen_room_curround_winner("");

				}

				// 2. Если статус раунда Winner
				if(self.m.s1.game.choosen_status() == "Winner") {

					// 1] Получить ссылку на игрока-победителя
					var thewinner = (function(){

						// 1.1] Пробежаться по всем ставкам текущего раунда выбранной комнаты
						for(var bet=0; bet<self.m.s1.game.choosen_room().rounds()[0].bets().length; bet++) {

							// 1.1.1] Получить ссылку на билет-победитель в короткую переменную
							var ticket_winner_number = self.m.s1.game.choosen_room().rounds()[0].ticket_winner_number();

							// 1.1.2] Получить ссылки на диапазон билетов bet
							var tickets_from = self.m.s1.game.choosen_room().rounds()[0].bets()[bet].m5_users()[0].pivot.tickets_from();
							var tickets_to = self.m.s1.game.choosen_room().rounds()[0].bets()[bet].m5_users()[0].pivot.tickets_to();

							// 1.1.3] Если ticket_winner_number попадает в диапазон, вернуть ссылка на соотв.пользователя
							if(ticket_winner_number >= tickets_from && ticket_winner_number <= tickets_to)
								return self.m.s1.game.choosen_room().rounds()[0].bets()[bet].m5_users()[0];

						}

					})();

					// 2] Записать ссылку thewinner в choosen_room_curround_winner
					self.m.s1.game.choosen_room_curround_winner(thewinner);

				}

			})();

			//---------------------------------------------------------------//
			// 6] Вычислить состояние индикатора предметов в текущей комнате //
			//---------------------------------------------------------------//
			(function(){

				self.m.s1.bank.indicator_percents((function(){

					// 1] Если отсутствуют необходимые ресурсы, вернуть 0
					if(!self.m.s1.game.choosen_room() || self.m.s1.game.choosen_room().max_items_per_round() == 0) {
						return 0;
					}

					// 2] Рассчитать значение
					var value = Math.round((self.m.s1.bank.items_sorted().length/self.m.s1.game.choosen_room().max_items_per_round())*100);

					// 3] Если value больше 100, уменьшить его до 100
					if(value >= 100) value = 100;

					// 4] Вернуть value
					return value;

				})());

			})();

			//--------------------------------------------------------------//
			// 7] Индекс аватаров игроков текущего раунда выбранной комнаты //
			//--------------------------------------------------------------//
			// - По ID игрока можно получить его аватар.
			self.m.s1.indexes.users_avatars = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
					results[self.m.s1.game.wheel.data()[i].user().id()] = self.m.s1.game.wheel.data()[i].user().avatar_steam(); //self.m.s1.game.wheel.data()[i].avatar();
				}

				// 3. Вернуть results
				return results;

			})();

			//-----------------------------------------------------------------//
			// 8] Индекс updated_at игроков текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------//
			// - По ID игрока можно получить его аватар.
			self.m.s1.indexes.users_updated_at = (function(){

					// 1. Подготовить объект для результатов
					var results = {};

					// 2. Заполнить results
					for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
						results[self.m.s1.game.wheel.data()[i].user().id()] = self.m.s1.game.wheel.data()[i].user().updated_at();
					}

				// 3. Вернуть results
				return results;

			})();

			//-----------------------------------------------------------------------//
			// 9] Наполнить модель полосы аватаров текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------------//
			(function(){

				// 1] Если нет необходимых ресурсов, ничего не делать
				if(!self.m.s1.game.choosen_room() || !self.m.s1.game.choosen_room().rounds() || !self.m.s1.game.choosen_room().rounds()[0].avatars_strip()) return;

				// 2] Удалить всё из self.m.s1.game.strip.avatars
				self.m.s1.game.strip.avatars.removeAll();

				// 3] Получить массив с ID пользователей ленты аватаров
				var avatars_strip_ids = JSON.parse(self.m.s1.game.choosen_room().rounds()[0].avatars_strip());

				// 4] Наполнить m.s1.game.strip.avatars
				for(var i=0; i<avatars_strip_ids.length; i++) {

					// 4.1] Если нет необходимых ресурсов, всё очистить и завершить
					if(!self.m.s1.indexes.users_avatars[avatars_strip_ids[i]] || !self.m.s1.indexes.users_updated_at[avatars_strip_ids[i]]) {
						self.m.s1.game.strip.avatars.removeAll();
						break;
					}

					// 4.2] Добавить в strip.avatars значение
					self.m.s1.game.strip.avatars.push(layoutmodel.m.s0.asset_url() + 'public/M5/steam_avatars/'+avatars_strip_ids[i]+'.jpg' + '?as=' + self.m.s1.indexes.users_avatars[avatars_strip_ids[i]].slice(-20) + '&ua=' + (self.m.s1.indexes.users_updated_at[avatars_strip_ids[i]]).replace(/[ :-]/g,''));

				}

			})();

			//------------------------------------------------------------------------//
			// 10] Расчитать ширину полосы аватаров текущего раунда выбранной комнаты //
			//------------------------------------------------------------------------//
			(function(){

				self.m.s1.game.strip.width(self.m.s1.game.strip.avatars().length*80 + self.m.s1.game.strip.avatars().length*2);

			})();

			//-----------------------------------------------------------------------------------//
			// 11] Расчитать финальную позицию полосы аватаров текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------------------------//
//			(function(){
//
//				// 11.1] Если отсутствуют необходимые ресурсы, вернуть 0
//				if(!self.m.s1.game.choosen_room())
//					return 0;
//
//				// 11.2] Ширина аватара в px с учётом отступа справа
//				var avatarwidth_origin = 80;
//				var avatarrightmargin = 2;
//				var avatarwidth = +avatarwidth_origin + +avatarrightmargin;
//
//				// 11.3] Получить ширину всей ленты
//				var width = self.m.s1.game.strip.width();
//
//				// 11.4] Получить поправку для установки позиции в конец ленты
//				var endfix = (6*avatarwidth)-62;
//
//				// 11.5] Вычислить позицию в начале 100-го аватара (победителя)
//				var winnerpos = width - endfix - (11*avatarwidth);
//
//				// 11.6] Получить значение
//				var avatar_winner_stop_percents = (function(){
//
//					// 11.6.1] Если предыдущий раунд есть, и статус Finished, Created или First bet, берём смещение из предыдущего раунда
//					if(self.m.s1.game.choosen_room().rounds().length > 1 && ['Finished', 'Created', 'First bet'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[self.m.s1.game.choosen_room().rounds()[0].rounds_statuses().length - 1].status()) != -1)
//						return self.m.s1.game.choosen_room().rounds()[1].avatar_winner_stop_percents();
//
//					// 11.6.2] Иначе, берём смещение из текущего раунда
//					else
//						return self.m.s1.game.choosen_room().rounds()[0].avatar_winner_stop_percents();
//
//				})();
//
//				// 11.7] Вычислить позицию с учётом avatar_winner_stop_percents
//				var winnerpos_final = -(winnerpos + avatarwidth_origin*(avatar_winner_stop_percents/100));
//
//				// 11.n] Вернуть результаты
//				self.m.s1.game.strip.final_px(winnerpos_final);
//
//			})();

			//--------------------------------------------------------------------------------------------//
			// 12] Расчитать кол-во внесенных вещей и шансы игрока в в текущем раунде в выбранной комнате //
			//--------------------------------------------------------------------------------------------//
			(function(){

				// 1] Количество внесённых игроком вещей в текущем раунде в выбранной комнате
				self.m.s1.bank.itemsnum((function(){
					return +(self.m.s1.game.wheel.currentuser() ? self.m.s1.game.wheel.currentuser().itemscount() : '0');
				})());

				// 2] Шансы игрока в текущем раунде в выбранной комнате
				self.m.s1.bank.bets((function(){
					return +(self.m.s1.game.wheel.currentuser() ? Math.round(self.m.s1.game.wheel.currentuser().odds()*100*10)/10 : '0')
				})());

				// 3] Человеко-понятная надпись о кол-ве внесённых предметов
				if(self.m.s1.bank.itemsnum() == 0)
					self.m.s1.bank.itemsnum_human('Вы внесли 0 предметов');
				if(self.m.s1.bank.itemsnum() == 1)
					self.m.s1.bank.itemsnum_human('Вы внесли 1 предмет');
				if(self.m.s1.bank.itemsnum() >= 2 && self.m.s1.bank.itemsnum() <= 4)
					self.m.s1.bank.itemsnum_human('Вы внесли '+self.m.s1.bank.itemsnum()+' предмета');
				if(self.m.s1.bank.itemsnum() >= 5)
					self.m.s1.bank.itemsnum_human('Вы внесли '+self.m.s1.bank.itemsnum()+' предметов');

			})();

			//---------------------------------------------------------------------------------------------//
			// 13] Какой бот должен принимать ставки в текущем раунде выбранной комнаты до статуса Lottery //
			//---------------------------------------------------------------------------------------------//
			(function(){

				// 0] Если нет необходимых ресурсов, завершить
				if(!m.s1.game.choosen_room()) return;

				// 2] Получить ID бота, обслуживающего предыдущий раунд
				var penultimate_bot_id = (function(){

					// 2.1] Если предыдущего раунда нет
					if(self.m.s1.game.choosen_room().rounds().length < 2)
						return "";

					// 2.2] Если предыдущий раунд есть
					else
						return self.m.s1.game.choosen_room().rounds()[1].bets()[0].m8_bots()[0].id();

				})();

				// 3] Получить массив всех ботов, обслуживающих текущую комнату
				var room_bots = (function(){
					var results = [];
					for(var i=0; i<self.m.s1.game.choosen_room().m8_bots().length; i++) {
						results.push(self.m.s1.game.choosen_room().m8_bots()[i]);
					}
					return results;
				})();

				// 4] Если room_bot пуст, записать пустой URL и завершить
				if(room_bots.length == 0) {
					self.m.s1.game.current_bot('');
					return;
				}

				// 5] Если penultimate_bot_id пуста, добавить URL первого из room_bots
				if(!penultimate_bot_id) {
					self.m.s1.game.current_bot(room_bots[0].trade_url());
					return;
				}

				// 6] Получить бота, обслуживающего текущий раунд
				var current_bot = (function(){

 					// 6.1] Найти позицию вхождения penultimate_bot_id в room_bot
					var pos = (function(){
						var result = '';
						for(var i=0; i<room_bots.length; i++) {
							if(room_bots[i].id() == penultimate_bot_id) return i;
						}
						return result;
					})();

					// 6.2] Если pos пуста, взять первого из room_bots
					if(!pos)
						return room_bots[0];

					// 6.3] В противном случае
					else {

						// 6.3.1] Если pos последняя, выбрать первого из room_bots
						if((room_bots.length-1) == pos)
							return room_bots[0];

						// 6.3.2] Если не последняя
						else
							return room_bots[+pos+1];

					}

				})();

				// 7] Получить бота, который будет обслуживать следующий раунд
				var next_bot = (function(){



				})();

				// n] Записать trade_url current_bot в current_bot
				self.m.s1.game.current_bot(current_bot.trade_url());

			})();

			//-----------------------------------------------------------------------------------//
			// 14] Обновить значения odds_player у всех ставок текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------------------------//
			(function(){

				// 14.1] Если ставки отсутствуют, завершить
				if(!self.m.s1.game.curprev().current().bets || !self.m.s1.smoothbets.bets().length) return;

				// 14.2] Получить ставки текущего раунда выбранной комнаты в короткую переменную
				var bets = self.m.s1.smoothbets.bets();

				// 14.3] Обновить odds_player всех ставок
				for(var i=0; i<bets.length; i++) {

					// 14.3.1] Получить шансы владейльца i-й ставки
					var odds = (function(){

						// 1) Получить ID игрока-владельца ставки
						var id = bets[i].m5_users()[0].id();

						// 2) Найти информацию о ставках этого игрока в индексе
						var info = self.m.s1.game.wheel.user_bet_index[bets[i].m5_users()[0].id()];

						// 3) Если id, info или odds пусты, вернуть 0
						if(!id || !info || !info.odds || !info.odds())
							return 0;

						// 4) Получить шансы пользователя на победу
						var odds = info.odds();

						// n) Вернуть odds
						return odds;

					})();

					// 13.3.2] Записать odds в odds_player
					bets[i].odds_player(odds);

				}

			})();

			//-------------------------------------------------------------------------//
			// 15] Обновить текущие джекпоты и состояния последних раундов всех комнат //
			//-------------------------------------------------------------------------//
			(function(){

				for(var i=0; i<self.m.s1.game.rooms().length; i++) {

					// 1] Получить короткую ссылку на bets текущего раунда выбранной комнаты
					var bets = self.m.s1.game.rooms()[i].rounds()[0].bets();

					// 2] Подсчитать и записать m.s1.game.curjackpot
					var bank_sum = (function(){
						var result = 0;
						for(var i=0; i<bets.length; i++) {
							for(var j=0; j<bets[i]['m8_items']().length; j++) {
								result = +result + Math.round(+bets[i]['m8_items']()[j].price()*100);
							}
						}
						return result;
					})();

					// 3] Если bank_sum отличается от старого bank_sum, запустить анимацию
					if(bank_sum != self.m.s1.room_jackpots()[self.m.s1.game.rooms()[i].id()]() && bank_sum != 0) {
						self.f.s1.animate_bank_sum(self.m.s1.game.rooms()[i].id());
					}

					// 4] Записать bank_sum в room_jackpots
					self.m.s1.room_jackpots()[self.m.s1.game.rooms()[i].id()](bank_sum);

					// 5] Обновить состояния
					(function(){

						// Получить текущий статус i-й комнаты
						var state = self.m.s1.game.rooms()[i].rounds()[0].rounds_statuses()[0].status();

						// Если статус: Lottery, Winner, Finished
						if(['Lottery', 'Winner', 'Finished'].indexOf(state) != -1)
							self.m.s1.room_states()[self.m.s1.game.rooms()[i].id()]("Розыгрыш:");

						// В противном случае
						else
							self.m.s1.room_states()[self.m.s1.game.rooms()[i].id()]("На кону:");

					})();

				}

			})();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.2. Рассчитать значения всех счётчиков для выбранной комнаты и текущего раунда //
		//------------------------------------------------------------------------------------//
		ko.computed(function(){

			// 1] Если отсутствуют необходимые ресурсы, записать нулевые значения и завершить
			if(!self.m.s1.game.choosen_room() || !self.m.s1.game.choosen_room().rounds()[0]) {

				// Завершить
				return;

			}

			// 2] Получить данные по длительности различных состояний из конфига выбранной комнаты
			// - В секундах.
			var durations = {};

				// Started
				durations.started = (function(){

					// Если лимит по предметам достигнут
					if(self.m.s1.game.choosen_room().rounds()[0]['is_skins_limit_reached']())
						return self.m.s1.game.choosen_room().rounds()[0]['started_duration_fact_s']();

					// Если нет
					else
						return +self.m.s1.game.choosen_room().room_round_duration_sec() + +self.m.s1.game.choosen_room().started_client_delta_s();

				})();

				// Pending
				durations.pending = +self.m.s1.game.choosen_room().pending_duration_s() + +self.m.s1.game.choosen_room().pending_client_delta_s() + ((self.m.s1.game.choosen_room().rounds()[0]['is_skins_limit_reached']()) ? +self.m.s1.game.choosen_room().lottery_client_delta_items_limit_s() : 0);

				// Lottery
				durations.lottery = +self.m.s1.game.choosen_room().lottery_duration_ms()/1000 + +self.m.s1.game.choosen_room().lottery_client_delta_ms()/1000;

				// Winner
				durations.winner = +self.m.s1.game.choosen_room().winner_duration_s() + +self.m.s1.game.choosen_room().winner_client_delta_s();

			// 3] Вычислить исходные значения для производных единого счётчика
			// - Для единого счётчика.
			// - Для счётчика начала розыгрыша.
			// - Для счётчика начала новой игры.
			var start = {};

				// Стартовое значение для m.s1.game.counters.lottery
				start.lottery = +durations.started + +durations.pending;

				// Стартовое значение для m.s1.game.newgame.lottery
				start.newgame = +durations.lottery + +durations.winner;

				// Стартовое значение для единого счётчика
				start.main = +start.lottery + +start.newgame;

			// 4] Если статус текущего раунда в выбранной комнате:
			// - Created или First Bet.
			if(['Created', 'First bet'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].status()) != -1) {

				// Установить значения единого счётчика
				self.m.s1.game.counters.main.sec(start.main);
				self.m.s1.game.counters.main.seconds(moment.utc(start.main*1000).format("ss"));
				self.m.s1.game.counters.main.minutes(moment.utc(start.main*1000).format("mm"));
				self.m.s1.game.counters.main.hours(moment.utc(start.main*1000).format("HH"));

				// Установить значения для счётчика начала розыгрыша
				if(self.m.s1.game.choosen_room().rounds()[0]['is_skins_limit_reached']())
					start.lottery = 0;
				self.m.s1.game.counters.lottery.sec(start.main);
				self.m.s1.game.counters.lottery.seconds(moment.utc(start.lottery*1000).format("ss"));
				self.m.s1.game.counters.lottery.minutes(moment.utc(start.lottery*1000).format("mm"));
				self.m.s1.game.counters.lottery.hours(moment.utc(start.lottery*1000).format("HH"));

				// Установить значения для счётчика начала новой игры
				self.m.s1.game.counters.newgame.sec(start.main);
				self.m.s1.game.counters.newgame.seconds(moment.utc(start.newgame*1000).format("ss"));
				self.m.s1.game.counters.newgame.minutes(moment.utc(start.newgame*1000).format("mm"));
				self.m.s1.game.counters.newgame.hours(moment.utc(start.newgame*1000).format("HH"));

			}

			// 5] Если статус текущего раунда в выбранной комнате:
			// - Started, Pending, Lottery, Winner, Finished
			if(['Started', 'Pending', 'Lottery', 'Winner', 'Finished'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].status()) != -1) {

				// 4.1] Текущее серверное время, unix timestamp в секундах
				var timestamp_s = self.m.s1.game.time.ts(); //layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();;

				// 4.2] Время начала состояния Started, unix timestamp в секундах
				var started_at_s = Math.round(moment.utc(self.m.s1.game.choosen_room().rounds()[0].started_at()).unix());

				// 4.3] Моменты, когда надо переключать игру в то или иное состояние
				var switchtimes = {};

					// Когда надо переключить в Pending
					switchtimes.pending = moment.utc(+started_at_s + +durations.started);

					// Когда надо переключить в Lottery
					switchtimes.lottery = moment.utc(+started_at_s + +durations.started + +durations.pending);

					// Когда надо переключить в Winner
					switchtimes.winner = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery);

					// Когда надо переключить в Created
					switchtimes.created = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery + +durations.winner);

				// 4.4] Время конца игры, unix timestamp в секундах
				var gameover_at_s = switchtimes.created;

				// 4.5] Вычислить и записать значение единого счётчика

					// Значение
					var value_main = (gameover_at_s-timestamp_s) > 0 ? (gameover_at_s-timestamp_s) : 0;

					// Записать
					self.m.s1.game.counters.main.sec(value_main);
					self.m.s1.game.counters.main.seconds(moment.utc(value_main*1000).format("ss"));
					self.m.s1.game.counters.main.minutes(moment.utc(value_main*1000).format("mm"));
					self.m.s1.game.counters.main.hours(moment.utc(value_main*1000).format("HH"));

				// 4.6] На основании значения счётчика до начала розыгрыша

					// Значение
					var value_lottery = self.m.s1.game.counters.main.sec() - durations.lottery - durations.winner;
					if(value_lottery < 0) value_lottery = 0;

					// Записать
					self.m.s1.game.counters.lottery.sec(value_lottery);
					self.m.s1.game.counters.lottery.seconds(moment.utc(value_lottery*1000).format("ss"));
					self.m.s1.game.counters.lottery.minutes(moment.utc(value_lottery*1000).format("mm"));
					self.m.s1.game.counters.lottery.hours(moment.utc(value_lottery*1000).format("HH"));

				// 4.7] На основании значения счётчика до начала новой игры

					// Значение
					var value_newgame = self.m.s1.game.counters.main.sec();
					if(value_newgame < 0) value_newgame = 0;

					// Записать
					self.m.s1.game.counters.newgame.sec(value_newgame);
					self.m.s1.game.counters.newgame.seconds(moment.utc(value_newgame*1000).format("ss"));
					self.m.s1.game.counters.newgame.minutes(moment.utc(value_newgame*1000).format("mm"));
					self.m.s1.game.counters.newgame.hours(moment.utc(value_newgame*1000).format("HH"));


			}

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});
	
		// s1.n.3. Перерасчитать модель для отрисовки кольца //
		//---------------------------------------------------//
		ko.computed(function(){		

			// 1] Завершить, если отсутствуют необходимые ресурсы
			//if(!self.m.s1.game.choosen_room() || !self.m.s1.bank.sum()) return;

			// 2] Очистить m.s1.game.wheel.data
			self.m.s1.game.wheel.data.removeAll();

			// 3] Получить короткую ссылку на bets текущего раунда выбранной комнаты
			var bets = self.m.s1.game.choosen_room() ? self.m.s1.game.choosen_room().rounds()[0].bets() : [];

			// 4] Наполнить m.s1.game.wheel.data
			for(var i=0; i<bets.length; i++) {

				// 4.1] Вычислить, ставил ли уже этот пользователь ранее
				// - Если ставил, то получить ссылку на старую ставку.
				var previous_bet = (function(){

					for(var j=0; j<bets.length; j++) {
						if(
							bets[i].m5_users()[0].id() == bets[j].m5_users()[0].id() &&
							i != j &&
							(bets[j].bet_color_hex && bets[j].bet_color_hex()) &&
						  self.m.s1.game.wheel.data().length &&
							bets[i].m5_users()[0].pivot.tickets_to() > bets[j].m5_users()[0].pivot.tickets_to()
						)
							return bets[j];
					}

				})();

				// 4.2] Если не ставил, создать для него новую запись в m.s1.game.wheel.data
				if(!previous_bet) {

					// 1) Вычислить шансы пользователя на победу в текущем раунде, в выбранной комнате
					// - Пока что записать 0.
					var odds = (function(){
						return (+bets[i].total_bet_amount() / +self.m.s1.game.curjackpot());
					})();

					// 2) Вычислить URL для аватара
					var avatar_url = (function(){

						// 2.1) Определить, есть ли уже порт у хоста в конце (через : )
						var is_port_in_the_end = (function(){
							if((/:[0-9]+$/i).test(layout_data.data.request.host)) return true;
							return false;
						})();

						// 2.2) Если у хоста уже есть порт в конце (через : )
						if(is_port_in_the_end)
							return layout_data.data.request.secure + layout_data.data.request.host + "/public/M5/steam_avatars/" + bets[i].m5_users()[0].id() + '.jpg';

						// 2.3) Если нет
						else
							return layout_data.data.request.secure + layout_data.data.request.host + ":" + layout_data.data.request.port + "/public/M5/steam_avatars/" + bets[i].m5_users()[0].id() + '.jpg'

					})();

					// 3) Подсчитать кол-во поставленных предметов
					var itemscount = (function(){
						var result = 0;
						for(var j=0; j<bets[i].m8_items().length; j++) {
							result = +result + 1;
						}
						return result;
					})();

					// 4) Добавить запись в m.s1.game.wheel.data
					self.m.s1.game.wheel.data.push({
						bets: 				ko.observableArray([bets[i]]),
						user: 				ko.observable(bets[i].m5_users()[0]),
						avatar: 			ko.observable(avatar_url),  //(bets[i].m5_users()[0].avatar_steam()),
						sum: 					ko.observable(bets[i].total_bet_amount()),
						odds: 				ko.observable(odds),
						color: 				ko.observable(bets[i].bet_color_hex()),
						bets_number: 	ko.observable(1),
						p: 						ko.observable(""),
						itemscount:   ko.observable(itemscount)
					});

				}

				// 4.3] Если это не первая ставка пользователя в текущем раунде
				// - То надо склеить её с предыдущей записью этого пользователя в m.s1.game.wheel.data
				else {

					// 1) Найти предыдущую запись этого пользователя в m.s1.game.wheel.data
					var prev_user_wheel_data = (function(){
						for(var j=0; j<self.m.s1.game.wheel.data().length; j++) {
							if(previous_bet.m5_users()[0].id() == self.m.s1.game.wheel.data()[j].user().id())
								return self.m.s1.game.wheel.data()[j];
						}
					})();

					// 2) Подсчитать кол-во поставленных пользователем предметов в этой ставке
					var itemscount = (function(){
						var result = 0;
						for(var j=0; j<bets[i].m8_items().length; j++) {
							result = +result + 1;
						}
						return result;
					})();

					// 3) Добавить данные о новой ставке пользователя в prev_user_wheel_data
					prev_user_wheel_data.bets.push(bets[i]);
					prev_user_wheel_data.sum((function(){
						return prev_user_wheel_data.sum() + +bets[i].total_bet_amount();
					})());
					prev_user_wheel_data.odds((function(){
						return prev_user_wheel_data.odds() + ((+bets[i].total_bet_amount() / +self.m.s1.game.curjackpot()));
					})());
					prev_user_wheel_data.itemscount((function(){
						return prev_user_wheel_data.itemscount() + +itemscount;
					})());

				}

			}

			// 5] Отсортировать wheel.data по шансам, от больших к меньшим
			self.m.s1.game.wheel.data.sort(function(a,b){
				if(a.odds() < b.odds()) return 1;
				if(a.odds() > b.odds()) return -1;
				return 0;
			});

			// 6] Обновить данные для текущего аутентифицированного игрока
			(function(){

				// 6.1] Попробовать найти данные для текущего аутентифицированного пользователя
				for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
					if(self.m.s1.game.wheel.data()[i].user().id() == layoutmodel.m.s0.auth.user().id()) {
						self.m.s1.game.wheel.currentuser(self.m.s1.game.wheel.data()[i]);
						return;
					}
				}

				// 6.2] Если данных нет, записать пустую строку
				self.m.s1.game.wheel.currentuser("");

			})();

			// 7] Переинициировать tooltipster
			self.f.s0.tooltipster_init();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.4. Управление текущей позицией и св-вом transform //
		//--------------------------------------------------------//
		ko.computed(function(){

			// Пусть оно срабатывает также при смене комнаты
			// - Заодно получить ссылку на неё.
			var room  =self.m.s1.game.choosen_room();

			// Если комната не выбрана, завершить
			if(!room)
				return;

			// Если статус Lottery
			if(['Lottery'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].status()) != -1) {
				setImmediate(self.f.s1.lottery, 500);
			}

			// Если статус Winner или Finished
			if(['Winner', 'Finished'].indexOf(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].status()) != -1) {
				setTimeout(self.f.s1.lottery, 2500);
			}

			//else if(['Winner', 'Finished', 'Created'].indexOf(self.m.s1.game.choosen_status()) != -1 && self.m.s1.game.choosen_status() != 'Lottery')
			//	self.m.s1.game.strip.currentpos(self.m.s1.game.strip.final_px());
			//else
			//	self.m.s1.game.strip.currentpos(self.m.s1.game.strip.start_px());

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.5. Добавление в текущий набор плавных ставок новых ставок //
		//----------------------------------------------------------------//
		ko.computed(function(){

			// 1] Если нет необходимых ресурсов, завершить
			if(!self.m.s1.game.curprev().current().bets) return;

			// 2] Получить массив ставок, которых ещё нет в smoothbets
			var newbets = (function(){

				var result = [];
				for(var i=0; i<self.m.s1.game.curprev().current().bets.slice(0).reverse().length; i++) {
					var verdict = false;
					for(var j=0; j<self.m.s1.smoothbets.bets().length; j++) {
						if(self.m.s1.game.curprev().current().bets.slice(0).reverse()[i].id() == self.m.s1.smoothbets.bets()[j].id())
							verdict = true;
					}
					if(!verdict)
						result.push(self.m.s1.game.curprev().current().bets.slice(0).reverse()[i]);
				}
				return result;

			})();

			// 3] Отсортировать newbets по tickets_from
			newbets.sort(function(a,b){
				if(a.m5_users()[0].pivot.tickets_from() > b.m5_users()[0].pivot.tickets_from()) return 1;
				if(a.m5_users()[0].pivot.tickets_from() < b.m5_users()[0].pivot.tickets_from()) return -1;
				return 0;
			});

			// 4] Добавить newbets в smoothbets
			for(var i=0; i<newbets.length; i++) {

				// 4.1] Добавить
				self.f.s1.smootbets_add(newbets[i], self.m.s1.game.choosen_room().id(), self.m.s1.game.curprev().current().id());

				// 4.2] Добавить текст для уведомления в пункте "Classic game" главного меню
				layoutmodel.m.s6.notify.text((function(){
 					return '+ '+Math.round((newbets[i].sum_cents_at_bet_moment()/100)*server.data.usdrub_rate) + ' руб.';
				})());

			}

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.6. Передавать в модель шаблона кое-какие данные //
		//------------------------------------------------------//
		ko.computed(function(){

			// 1] Текущий jackpot текущего раунда выбранной комнаты
			layoutmodel.m.s6.curjackpot(self.m.s1.game.curjackpot());

			// 2] Имя текущего статуса текущего раунда выбранной комнаты
			layoutmodel.m.s6.status.name(self.m.s1.game.choosen_status());

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.7. Проигрывать звук тиков игры //
		//-------------------------------------//
		ko.computed(function(){

			// 1] Если текущий статус текущего раунда выбранной комнаты не "Started", завершить
			if(!self.m.s1.game.choosen_status() || (['Started', 'Pending'].indexOf(self.m.s1.game.choosen_status()) == -1))
				return;

			// 2] Выполнять при каждом изменении в
			self.m.s1.game.counters.lottery.seconds();

			// 3] Проиграть звук тиков игры
			if(self.m.s1.game.choosen_status() == 'Started' && self.m.s1.game.counters.lottery.seconds() != '00') {
				self.f.s1.playsound('timer-tick-quiet');
			}
			else if(self.m.s1.game.choosen_status() == 'Pending' && self.m.s1.game.counters.lottery.seconds() == '00') {
				self.f.s1.playsound('timer-tick-quiet');
			}

			// 2] Если до начала розыгрыша более 5 секунд
			//if((self.m.s1.game.counters.lottery.sec() || self.m.s1.game.counters.lottery.sec() === 0 || self.m.s1.game.counters.lottery.sec() === '0') && self.m.s1.game.counters.lottery.sec() >= 5)
			//	self.f.s1.playsound('timer-tick-quiet');

			// 3] Если до начала розыгрыша менее 5 секунд
			//else if((self.m.s1.game.counters.lottery.sec() || self.m.s1.game.counters.lottery.sec() === 0 || self.m.s1.game.counters.lottery.sec() === '0') && self.m.s1.game.counters.lottery.sec() < 5)
			//	self.f.s1.playsound('timer-tick-quiet'); //timer-tick-last-5-seconds');

		}); //.extend({rateLimit: 10, method: "notifyWhenChangesStop"});


	//------------------------------------//
	// 			        		 	                //
	// 	W. Обработка websocket-сообщений  //
	// 			         			                //
	//------------------------------------//

	//-------------------------------------------------//
	// w8.1. Обработка сообщений через публичный канал //
	//-------------------------------------------------//
	self.websocket.ws1.on('m9:public', function(data) {

		// 1] Получить имя задачи
		var task = data.data.data.task;

		// 2] В зависимости от task выполнить соотв.метод
		switch(task) {

			case "fresh_game_data": 							self.f.s1.fresh_game_data(data.data.data.data); break;
			case "classicgame_history_new": 			self.f.s1.add_new_history(data.data.data.data); break;
			case "m9:stats:update:thebiggestbet": self.f.s1.stats_update_thebiggestbet(data.data.data.data); break;
			case "m9:stats:update:luckyoftheday": self.f.s1.stats_update_luckyoftheday(data.data.data.data); break;

		}

	});

	//-----------------------------------------------//
	// w8.2. Обработка сообщений через частный канал //
	//-----------------------------------------------//
	if(JSON.parse(layout_data.data.auth).is_anon == 0) {
		self.websocket.ws1.on('m9:private:'+JSON.parse(layout_data.data.auth).user.id, function(data) {

			// 1] Получить имя задачи
			var task = data.data.data.task;

			// 2] В зависимости от task выполнить соотв.метод
			switch(task) {

				case "tradeoffer_cancel": 			self.f.s1.tradeoffer_cancel(data.data.data.data); break;
				case "tradeoffer_accepted": 		self.f.s1.tradeoffer_accepted(data.data.data.data); break;
				case "tradeoffer_processing": 	self.f.s1.tradeoffer_processing(data.data.data.data); break;

			}

		});
	}









	//------------------------------//
	// 			        		 	          //
	// 	X. Подготовка к завершению  //
	// 			         			          //
	//------------------------------//

	//------------------------------------------//
	// X1. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self.m.s1;


}};	// конец модели








