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
 *    s1.n. Индексы и вычисляемые значения
 *
 *      s1.n.A. Состояние текущего раунда
 * 			s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ...
 * 			s1.n.2. Рассчитать значения всех счётчиков для выбранной комнаты и текущего раунда
 *      s1.n.3. Перерасчитать модель для отрисовки кольца
 * 			s1.n.4. Управление текущей позицией и св-вом transform
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

		});

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
			});

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
	
	//---------------------------------------------------------------------//
	// s1.4. Модель интерфейса по распределению шансов в выбранной комнате //
	//---------------------------------------------------------------------//
	self.m.s1.game.wheel = {};
	
		// 1] Данные по каждому игроку текущего раунд (кто, сколько в сумме поставил, какой цвет и т.д.) //
		//-----------------------------------------------------------------------------------------------//
		self.m.s1.game.wheel.data = ko.observableArray([]);
		
		// 2] Данные для текущего аутентифицированного игрока
		self.m.s1.game.wheel.currentuser = ko.observable();

	//-------------------------------------------//
	// s1.5. Модель статистики классической игры //
	//-------------------------------------------//
	self.m.s1.game.statistics = ko.mapping.fromJS(server.data.classicgame_statistics.data);

	//----------------------------------------------------------------//
	// s1.6. Модель полосы аватаров текущего раунда выбранной комнаты //
	//----------------------------------------------------------------//
	self.m.s1.game.strip = {};

		// 1] Сама полоса аватаров
		self.m.s1.game.strip.avatars = ko.observableArray([]);

		// 2] Ширина полосы аватаров
		self.m.s1.game.strip.width = ko.observableArray(0);

		// 3] Исходная позиция полосы аватаров
		self.m.s1.game.strip.start_px = ko.observable(880);

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
			var avatar_winner_stop_percents = self.m.s1.game.choosen_room().rounds()[0].avatar_winner_stop_percents();

			// 4.7] Вычислить позицию с учётом avatar_winner_stop_percents
			var winnerpos_final = winnerpos + avatarwidth_origin*(avatar_winner_stop_percents/100);

			// 4.n] Вернуть результаты
			return winnerpos_final;

		});

		// 5] Текущая позиция полосы с учётом avatar_winner_stop_percents
		self.m.s1.game.strip.currentpos = ko.observable("0");

		// 6] Значение свойства transform полосы
		self.m.s1.game.strip.transform = ko.observable('none');


	//-------------------------------------------------------------------------------//
	// s1.7. Победный билет, победитель, число для текущего раунда выбранной комнаты //
	//-------------------------------------------------------------------------------//
	self.m.s1.game.lwpanel = {};

		// 1] Победный билет
		self.m.s1.game.lwpanel.ticket = ko.computed(function(){

			// 1.1] Если состояние текущего раунда в выбранной комнате: Winner
			if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
				return '#' + self.m.s1.game.choosen_room().rounds()[0].ticket_winner_number();
			}

			// 1.2] В противном случае
			else {
				return '???';
			}

		});

		// 2] Имя победителя раунда
		self.m.s1.game.lwpanel.winner = ko.computed(function(){

			// 1.1] Если состояние текущего раунда в выбранной комнате: Winner
			if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
				if(self.m.s1.game.choosen_room_curround_winner())
					return self.m.s1.game.choosen_room_curround_winner().nickname()
				else
					return '???';
			}

			// 1.2] В противном случае
			else {
				return '???';
			}

		});

		// 3] Число текущего раунда
		self.m.s1.game.lwpanel.number = ko.computed(function(){

			// 1.1] Если состояние текущего раунда в выбранной комнате: Winner
			if(['Winner'].indexOf(self.m.s1.game.choosen_status()) != -1) {
				if(self.m.s1.game.choosen_room().rounds()[0].key())
					return self.m.s1.game.choosen_room().rounds()[0].key();
				else
					return '???';
			}

			// 1.2] В противном случае
			else {
				return '???';
			}

		});

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

		// 1] Сколько секунд прошло с момента загрузки документа
		self.m.s1.game.time.gone_s = ko.observable(0);

		// 2] Расчёт текущего серверного unix timestamp
		self.m.s1.game.time.ts = ko.computed(function(){

			// 2.1] Получить результирующее время
			var result = +layout_data.data.servertime_s + +self.m.s1.game.time.gone_s();

			// 2.2] Получить TS, который приходит с сервера
			var server_ts = layoutmodel.m.s0.servertime.timestamp_s();

			// 2.3] Если result отстал, подвести
			if(server_ts - result >= 0.5) { // && ['Lottery', 'Winner'].indexOf(self.m.s1.game.choosen_status()) == -1) {
				self.m.s1.game.time.gone_s(+server_ts - +layout_data.data.servertime_s);
				result = server_ts;
			}

			// 2.4] Вернуть result
			return result;

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

		});

		// 7] Список комнат, в которых сейчас работает функция f.m1.lottery
		// - То есть, повторно запускать её в этих комнатах не следует.
		self.m.s1.game.strip.rooms_with_working_animation = ko.observableArray([]);


	//--------------------------------------//
	// s1.n. Индексы и вычисляемые значения //
	//--------------------------------------//

		// s1.n.1. Общие вычисления: комнаты, раунды, состояния, джекпот ... //
		//-------------------------------------------------------------------//
		ko.computed(function(){

			//----------------------------------//
			// 1] Объект-контейнер для индексов //
			//----------------------------------//
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

			//-----------------------------------------------------------------------//
			// s1.n.3. Если комната не выбрана, выбрать её функцией f.s1.choose_room //
			//-----------------------------------------------------------------------//
			(function(){

				if(!self.m.s1.game.choosen_room()) {
					if(server.data.choosen_room_id != 0) {
						self.f.s1.choose_room(self.m.s1.indexes.rooms[server.data.choosen_room_id]);
					}
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

			////---------------------------------------------//
			//// s1.n.9. Вычислить состояние текущего раунда //
			////---------------------------------------------//
			//(function(){
			//
			//	// 1] Проверить наличие необходимых ресурсов
			//	if(!self.m.s1.game.curprev().current().rounds_statuses) return;
			//
			//	// 2] Записать имя статуса текущего раунда текущей комнаты в choosen_status
			//	self.m.s1.game.choosen_status(self.m.s1.game.curprev().current().rounds_statuses()[self.m.s1.game.curprev().current().rounds_statuses().length-1].status());
			//
			//})();

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

					// 2] Рассчитать значение
					var value = Math.round((self.m.s1.bank.items_sorted().length/self.m.s1.game.choosen_room().max_items_per_round())*100);

					// 3] Если value больше 100, уменьшить его до 100
					if(value >= 100) value = 100;

					// 4] Вернуть value
					return value;

				})());

			})();

			//-----------------------------------------------------------//
			// s1.n.14. Индекс игроков текущего раунда выбранной комнаты //
			//-----------------------------------------------------------//
			// - По ID игрока можно получить ссылку на него в m.s1.game
			self.m.s1.indexes.users_avatars = (function(){

				// 1. Подготовить объект для результатов
				var results = {};

				// 2. Заполнить results
				for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
					results[self.m.s1.game.wheel.data()[i].user().id()] = self.m.s1.game.wheel.data()[i].avatar();
				}

				// 3. Вернуть results
				return results;

			})();

			//-----------------------------------------------------------------------------//
			// s1.n.15. Наполнить модель полосы аватаров текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------------------//
			(function(){

				// 1] Если нет необходимых ресурсов, ничего не делать
				if(!self.m.s1.game.choosen_room() || !self.m.s1.game.choosen_room().rounds() || !self.m.s1.game.choosen_room().rounds()[0].avatars_strip()) return;

				// 2] Удалить всё из self.m.s1.game.strip.avatars
				self.m.s1.game.strip.avatars.removeAll();

				// 3] Получить массив с ID пользователей ленты аватаров
				var avatars_strip_ids = JSON.parse(self.m.s1.game.choosen_room().rounds()[0].avatars_strip());

				// 4] Наполнить m.s1.game.strip.avatars
				for(var i=0; i<avatars_strip_ids.length; i++) {
					self.m.s1.game.strip.avatars.push(self.m.s1.indexes.users_avatars[avatars_strip_ids[i]]);
				}

			})();

			//-----------------------------------------------------------------------------//
			// s1.n.16. Расчитать ширину полосы аватаров текущего раунда выбранной комнаты //
			//-----------------------------------------------------------------------------//
			(function(){

				self.m.s1.game.strip.width(self.m.s1.game.strip.avatars().length*80 + self.m.s1.game.strip.avatars().length*2);

			})();


		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"}); 	// .extend({rateLimit: 10, method: "notifyWhenChangesStop"});

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
				durations.started = +self.m.s1.game.choosen_room().room_round_duration_sec() + +self.m.s1.game.choosen_room().started_client_delta_s();

				// Pending
				durations.pending = +self.m.s1.game.choosen_room().pending_duration_s() + +self.m.s1.game.choosen_room().pending_client_delta_s();

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
			if(['Created', 'First bet'].indexOf(self.m.s1.game.choosen_status()) != -1) {

				// Установить значения единого счётчика
				self.m.s1.game.counters.main.sec(start.main);
				self.m.s1.game.counters.main.seconds(moment.utc(start.main*1000).format("ss"));
				self.m.s1.game.counters.main.minutes(moment.utc(start.main*1000).format("mm"));
				self.m.s1.game.counters.main.hours(moment.utc(start.main*1000).format("HH"));

				// Установить значения для счётчика начала розыгрыша
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
			if(['Started', 'Pending', 'Lottery', 'Winner', 'Finished'].indexOf(self.m.s1.game.choosen_status()) != -1) {

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

				// 4.7] На основании значения счётчика до начала розыгрыша

					// Значение
					var value_newgame = self.m.s1.game.counters.main.sec();
					if(value_newgame < 0) value_newgame = 0;

					// Записать
					self.m.s1.game.counters.newgame.sec(value_newgame);
					self.m.s1.game.counters.newgame.seconds(moment.utc(value_newgame*1000).format("ss"));
					self.m.s1.game.counters.newgame.minutes(moment.utc(value_newgame*1000).format("mm"));
					self.m.s1.game.counters.newgame.hours(moment.utc(value_newgame*1000).format("HH"));


			}

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});;
	
		// s1.n.3. Перерасчитать модель для отрисовки кольца //
		//---------------------------------------------------//
		ko.computed(function(){		
	
			// 1] Завершить, если отсутствуют необходимые ресурсы
			if(!self.m.s1.game.choosen_room() || !self.m.s1.bank.sum()) return;

			// 2] Очистить m.s1.game.wheel.data
			self.m.s1.game.wheel.data.removeAll();

			// 3] Получить короткую ссылку на bets текущего раунда выбранной комнаты
			var bets = self.m.s1.game.choosen_room().rounds()[0].bets();

			// 4] Наполнить m.s1.game.wheel.data
			for(var i=0; i<bets.length; i++) {

				// 4.1] Вычислить, ставил ли уже этот пользователь ранее
				// - Если ставил, то получить ссылку на старую ставку.
				var previous_bet = (function(){

					for(var j=0; j<bets.length; j++) {
						if(
							bets[i].m5_users()[0].id() == bets[j].m5_users()[0].id() &&
							i != j &&
							bets[i].id() > bets[j].id()
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

			// 5] Обновить данные для текущего аутентифицированного игрока
			(function(){

				// 5.1] Попробовать найти данные для текущего аутентифицированного пользователя
				for(var i=0; i<self.m.s1.game.wheel.data().length; i++) {
					if(self.m.s1.game.wheel.data()[i].user().id() == layoutmodel.m.s0.auth.user().id()) {
						self.m.s1.game.wheel.currentuser(self.m.s1.game.wheel.data()[i]);
						return;
					}
				}

				// 5.2] Если данных нет, записать пустую строку
				self.m.s1.game.wheel.currentuser("");

			})();

			// 6] Переинициировать tooltipster
			self.f.s0.tooltipster_init();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		// s1.n.4. Управление текущей позицией и св-вом transform //
		//--------------------------------------------------------//
		ko.computed(function(){

			self.m.s1.game.choosen_room();
			if(self.m.s1.game.choosen_status() == 'Lottery')
				setTimeout(self.f.s1.lottery, 100);
			if(['Winner', 'Finished'].indexOf(self.m.s1.game.choosen_status()) != -1)
				self.m.s1.game.strip.currentpos(self.m.s1.game.strip.final_px());

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









