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
 *    s1.n. Индексы и вычисляемые значения
 *
 * 			s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ...
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
		self.m.s1.game.choosen_status = ko.observable("");

		// 9] Палитра цветов для игроков текущего раунда //
		//-----------------------------------------------//
		// - Загружается с сервера.
		self.m.s1.game.palette = ko.observableArray([]);

		// 10] Работа с кривой Безье и вращением колеса //
		//----------------------------------------------//
		self.m.s1.game.bezier = {};

			// 10.1] Параметры кривой Безье //
			//------------------------------//
			self.m.s1.game.bezier.params = [.17, .01, 0, 1];

			// 10.2] Параметры кривой Безье //
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
	

	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//

		// s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ... //
		//-------------------------------------------------------------------//
		ko.computed(function(){

			//---------------------------------------//
			// s1.n.1. Объект-контейнер для индексов //
			//---------------------------------------//
			self.m.s1.indexes = {};

			//-----------------------//
			// s1.n.2. Индекс комнат //
			//-----------------------//
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

			//---------------------------------------------//
			// s1.n.3. Если комната не выбрана, выбрать её //
			//---------------------------------------------//
			(function(){

				if(!self.m.s1.game.choosen_room()) {
					if(server.data.choosen_room_id != 0)
						self.m.s1.game.choosen_room(self.m.s1.indexes.rooms[server.data.choosen_room_id]);
				}

			})();

			//-------------------------------------------------------------------//
			// s1.n.4. Индекс состояний игры модели для отладки игровой механики //
			//-------------------------------------------------------------------//
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

			//-----------------------------------------------------------------------------------------------//
			// s1.n.5. Индекс позиций состояний игры модели для отладки игровой механики в массиве состояний //
			//-----------------------------------------------------------------------------------------------//
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

			//-------------------------------------------------------------------------//
			// s1.n.6. Наполнить модель текущего/предыдущего раундов выбранной комнаты //
			//-------------------------------------------------------------------------//
			(function(){

				// 1] Обновить ссылку на объект текущего раунда выбранной комнаты
				if(self.m.s1.game.choosen_room() && self.m.s1.game.choosen_room().rounds()[0])
					self.m.s1.game.curprev().current(self.m.s1.game.choosen_room().rounds()[0]);

				// 2] Обновить ссылку на объект предыдущего раунда выбранной комнаты
				if(self.m.s1.game.choosen_room() && self.m.s1.game.choosen_room().rounds()[1])
					self.m.s1.game.curprev().previous(self.m.s1.game.choosen_room().rounds()[1]);

			})();

			//-----------------------------------------------------------//
			// s1.n.7. Ссылки на текущий и предыдущий раунды всех комнат //
			//-----------------------------------------------------------//
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

			//----------------------------------------------------//
			// s1.n.8. Рассчитать текущий джекпот текущего раунда //
			//----------------------------------------------------//
			(function(){

				// 1. Если необходимые наблюдаемые отсутствуют, завершить
				if(!self.m.s1.game.choosen_room()) return;

				// 2. Получить короткую ссылку на bets текущего раунда выбранной комнаты
				var bets = self.m.s1.game.choosen_room().rounds()[0].bets();

				// 3. Подсчитать общую суммарную стоимость вещей на кону
				var bank_sum = (function(){
					var result = 0;
					for(var i=0; i<bets.length; i++) {
						for(var j=0; j<bets[i]['m8_items']().length; j++) {
							result = +result + Math.round(+bets[i]['m8_items']()[j].price()*100);
						}
					}
					return result;
				})();

				// 4. Записать bank_sum в наблюдаемую
				self.m.s1.game.curjackpot(bank_sum);

			})();

			//---------------------------------------------//
			// s1.n.9. Вычислить состояние текущего раунда //
			//---------------------------------------------//
			(function(){

				// 1] Проверить наличие необходимых ресурсов
				if(!self.m.s1.game.curprev().current().rounds_statuses) return;

				// 2] Записать имя статуса текущего раунда текущей комнаты в choosen_status
				self.m.s1.game.choosen_status(self.m.s1.game.curprev().current().rounds_statuses()[self.m.s1.game.curprev().current().rounds_statuses().length-1].status());

			})();

			//-------------------------------//
			// s1.n.10. Индекс главных табов //
			//-------------------------------//
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

			//-------------------------------------------------------------------------------------------//
			// s1.n.11. Вычисление bank.items_sorted и подсчитать суммарную стоимость поставленных вещей //
			//-------------------------------------------------------------------------------------------//
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
			
			//--------------------------------------------------------------------------//
			// s1.n.12. Вычислить игрока-победителя текущего раунда в выбранной комнате //
			//--------------------------------------------------------------------------//
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

			//---------------------------------------------------------------------//
			// s1.n.13. Вычислить состояние индикатора предметов в текущей комнате //
			//---------------------------------------------------------------------//
			(function(){

				self.m.s1.bank.indicator_percents((function(){

					// 1] Если отсутствуют необходимые ресурсы, вернуть 0
					if(!self.m.s1.game.choosen_room() || self.m.s1.game.choosen_room().max_items_per_round() == 0) {
						return 0;
					}

					// 2] Иначе, рассчитать знаение и вернуть его
					return Math.round((self.m.s1.bank.items_sorted().length/self.m.s1.game.choosen_room().max_items_per_round())*100);

				})());

			})();




		}); 	// .extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.2. Рассчитать оставшееся время до конца состояний Started, Pending, Winner, для choosen_room //
		//---------------------------------------------------------------------------------------------------//
		ko.computed(function(){	

			// 1] Если отсутствуют необходимые ресурсы, записать нулевые значения и завершить
			if(!self.m.s1.game.choosen_room() || !self.m.s1.game.choosen_room().rounds()[0]) {

				// Started
				self.m.s1.game.timeleft.sec("0");
				self.m.s1.game.timeleft.human("00:00:00");

				// Pending
				self.m.s1.game.timeleft_pending.sec("0");
				self.m.s1.game.timeleft_pending.human("00:00:00");

				// Winner
				self.m.s1.game.timeleft_winner.sec("0");
				self.m.s1.game.timeleft_winner.human("00:00:00");

				// Завершить
				return;

			}

			// 2] Записать необходимые для расчётов значения в короткие переменные

				// Время начала состояния "Started/Pending/Winner" раунда (started_at), в секундах
				// - В зависимости от того, в каком состоянии сейчас находится раунд.
				var Ts = Math.round(moment.utc(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].pivot.started_at()).unix());

				// Текущее серверное время, timestamp в секундах
				var Tn = layoutmodel.m.s0.servertime.timestamp_s();

				// Длительность состояния Started (значение room_round_duration_sec), в секундах
				var Tl_started = self.m.s1.game.choosen_room().room_round_duration_sec();
				var Tl_pending = self.m.s1.game.choosen_room().pending_duration_s();
				var Tl_winner = self.m.s1.game.choosen_room().winner_duration_s();

			// 3] Вычислить результаты
			(function(){

				// 3.1] Если лимит на длительность состояний не установлен
				if(Tl_started == "0") {
					self.m.s1.game.timeleft.sec("∞");
					self.m.s1.game.timeleft.human("∞");
					self.m.s1.game.timeleft.seconds("∞");
					self.m.s1.game.timeleft.minutes("∞");
					self.m.s1.game.timeleft.hours("∞");
					return;
				}
				if(Tl_pending == "0") {
					self.m.s1.game.timeleft_pending.sec("∞");
					self.m.s1.game.timeleft_pending.human("∞");
					return;
				}
				if(Tl_winner == "0") {
					self.m.s1.game.timeleft_winner.sec("∞");
					self.m.s1.game.timeleft_winner.human("∞");
					return;
				}

				// 3.2] Вычислить результаты
				(function(){

					// 1) Если состояние раунда "Created" или "First bet"
					if(['Created', 'First bet'].indexOf(self.m.s1.game.choosen_status()) != -1) {
						self.m.s1.game.timeleft.sec(self.m.s1.game.choosen_room().room_round_duration_sec());
						self.m.s1.game.timeleft.human(moment.utc(self.m.s1.game.choosen_room().room_round_duration_sec()*1000).format("HH:mm:ss"));
						self.m.s1.game.timeleft.seconds(moment.utc(self.m.s1.game.choosen_room().room_round_duration_sec()*1000).format("ss"));
						self.m.s1.game.timeleft.minutes(moment.utc(self.m.s1.game.choosen_room().room_round_duration_sec()*1000).format("mm"));
						self.m.s1.game.timeleft.hours(moment.utc(self.m.s1.game.choosen_room().room_round_duration_sec()*1000).format("HH"));
						return;
					}

					// 2) Если состояние раунда "Pending", "Lottery", "Winner" или "Finished"
					if(['Pending', 'Lottery', 'Winner', 'Finished'].indexOf(self.m.s1.game.choosen_status()) != -1) {
						self.m.s1.game.timeleft.sec("0");
						self.m.s1.game.timeleft.human("00:00:00");
						self.m.s1.game.timeleft.seconds("00");
						self.m.s1.game.timeleft.minutes("00");
						self.m.s1.game.timeleft.hours("00");
						return;
					}

					// 3) Если состояние раунда "Started"
					if(['Started'].indexOf(self.m.s1.game.choosen_status()) != -1) {

						// Определить значение
						var value = (+Ts + +Tl_started) - +Tn;

						// Если оно <= 0
						// - То есть, раунд (состояние Started) уже закончился
						if(value <= 0) {
							self.m.s1.game.timeleft.sec("0");
							self.m.s1.game.timeleft.human("00:00:00");
							self.m.s1.game.timeleft.seconds("00");
							self.m.s1.game.timeleft.minutes("00");
							self.m.s1.game.timeleft.hours("00");
						}

						// Если оно > 0
						// - То есть, раунд (состояние Started) ещё не закончился
						else {
							self.m.s1.game.timeleft.sec(value);
							self.m.s1.game.timeleft.human(moment.utc((value)*1000).format("HH:mm:ss"));
							self.m.s1.game.timeleft.seconds(moment.utc(value*1000).format("ss"));
							self.m.s1.game.timeleft.minutes(moment.utc(value*1000).format("mm"));
							self.m.s1.game.timeleft.hours(moment.utc(value*1000).format("HH"));
						}

						// Завершить
						return;

					}

				})();

				// 3.3] Вычислить результаты для Pending
				(function(){

					// 1) Если состояние раунда не "Pending"
					if(['Pending'].indexOf(self.m.s1.game.choosen_status()) == -1) {
						self.m.s1.game.timeleft_pending.sec("0");
						self.m.s1.game.timeleft_pending.human("00:00:00");
						return;
					}

					// 2) Если состояние раунда "Pending"
					if(['Pending'].indexOf(self.m.s1.game.choosen_status()) != -1) {

						// Определить значение
						var value = (+Ts + +Tl_pending) - +Tn;

						// Если оно <= 0
						// - То есть, состояние Pending уже закончилось
						if(value <= 0) {
							self.m.s1.game.timeleft_pending.sec("0");
							self.m.s1.game.timeleft_pending.human("00:00:00");
						}

						// Если оно > 0
						// - То есть, состояние Pending ещё не закончилось
						else {
							self.m.s1.game.timeleft_pending.sec(value);
							self.m.s1.game.timeleft_pending.human(moment.utc((value)).format("HH:mm:ss"));
						}

					}

				})();

				// 3.4] Вычислить результаты для Winner
				(function(){

					// 1) Если состояние раунда не "Winner"
					if(['Winner'].indexOf(self.m.s1.game.choosen_status()) == -1) {
						self.m.s1.game.timeleft_winner.sec("0");
						self.m.s1.game.timeleft_winner.human("00:00:00");
						return;
					}

					// 2) Если состояние раунда "Winner"
					if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {

						// Определить значение
						var value = (+Ts + +Tl_winner) - +Tn;

						// Если оно <= 0
						// - То есть, состояние Winner уже закончилось
						if(value <= 0) {
							self.m.s1.game.timeleft_winner.sec("0");
							self.m.s1.game.timeleft_winner.human("00:00:00");
						}

						// Если оно > 0
						// - То есть, состояние Winner ещё не закончилось
						else {
							self.m.s1.game.timeleft_winner.sec(value);
							self.m.s1.game.timeleft_winner.human(moment.utc((value)).format("HH:mm:ss"));
						}

					}

				})();

			})();			
			
		});			
			

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









