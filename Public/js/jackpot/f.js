/*//======================================================================================////
////																			                                                ////
////   Функционал игры Jackpot, предназначен для подключения в основной f.js	документа   ////
////																			                                                ////
////======================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 	s1. Функционал игры Jackpot
 *
 *    f.s1.update_rooms										| s1.1. Обновить модель всех игровых данных данными с сервера
 *    f.s1.update_lottery_statuses        | s1.2. Обновить модель возможных статусов игры лоттерея
 *    f.s1.choose_tab                     | s1.3. Выбрать кликнутый таб
 *    f.s1.choose_room                    | s1.4. Выбрать кликнутую комнату
 *    f.s1.fresh_game_data 								| s1.5. Получить и обработать свежие игровые данные
 *    f.s1.reload_page 										| s1.6. Перезагрузить страницу
 *    f.s1.get_steam_img_with_size        | s1.7. Получить URL на изображение скина в стим заданных размеров
 *   	f.s1.get_cat_quality_item_color     | s1.8. Вычислить цвет для вещи в ставке (зависящий от категории и качетва)
 *    f.s1.update_statistics              | s1.9. Обновить модель статистики свежими данными с сервера
 *    f.s1.queue_add                      | s1.10. Добавить задачу в очередь
 *    f.s1.queue_processor                | s1.11. Выполняется ежесекундно, выполнить все задачи из очереди, чьё время пришло
 * 		f.s1.bezier 												| s1.12. Порт кривой Безье на JS
 *    f.s1.lottery                        | s1.13. Анимация ленты аватарок с помощью JS
 *    f.s1.onclick_handler                | s1.14. Обработка клика по кнопке "Внести депозит"
 *    f.s1.tradeoffer_cancel              | s1.15. Сообщение от сервера о том, что ставка игрока была отменена
 *    f.s1.tradeoffer_accepted            | s1.16. Сообщение от сервера о том, что ставка игрока принята
 *    f.s1.tradeoffer_processing          | s1.17. Сообщение от сервера о том, что ставка игрока обрабатывается
 *
 *
 */

	//--------------------------------------------------------------------//


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctionsJackpot = { constructor: function(self, f) { f.s1 = this;

	//-------------------------------//
	// 			        		 	           //
	// 	s1. Функционал игры Jackpot  //
	// 			         			           //
	//-------------------------------//

	//-------------------------------------------------------------//
	// s1.1. Обновить модель всех игровых данных данными с сервера //
	//-------------------------------------------------------------//
	f.s1.update_rooms = function(data) {

		// 1] Очистить m.s1.game.rooms
		self.m.s1.game.rooms.removeAll();

		// 2] Обновить
		(function(){

			self.m.s1.game.rooms(ko.mapping.fromJS(data)());

		})();

	};
	
	//--------------------------------------------------------//
	// s1.2. Обновить модель возможных статусов игры лоттерея //
	//--------------------------------------------------------//
	f.s1.update_lottery_statuses = function(data) {

		// 1. Обновить m.s1.game.statuses

			// 1.1. Очистить
			self.m.s1.game.statuses.removeAll();

			// 1.2. Наполнить
			for(var i=0; i<data.length; i++) {

				// 1.2.1. Сформировать объект для добавления
				var obj = {};
				for(var key in data[i]) {

					// 1] Если свойство не своё, пропускаем
					if(!data[i].hasOwnProperty(key)) continue;

					// 2] Добавим в obj свойство key
					obj[key] = ko.observable(data[i][key]);

				}

				// 1.2.2. Добавить этот объект в подготовленный массив
				self.m.s1.game.statuses.push(ko.observable(obj))

			}

	};	

	//-----------------------------//
	// s1.3. Выбрать кликнутый таб //
	//-----------------------------//
	f.s1.choose_tab = function(name, data, event) {

		self.m.s1.maintabs.choosen(self.m.s1.indexes.maintabs[name]);

	};

	//---------------------------------//
	// s1.4. Выбрать кликнутую комнату //
	//---------------------------------//
	f.s1.choose_room = function(data, event) {

		// 1] Если data пуста, ничего не делать
		if(!data) return;

		// 2] Если статус комнаты data - Lottery - выключить css-анимацию
		if(['Lottery', 'Winner', 'Finished'].indexOf(data.rounds()[0].rounds_statuses()[0].status() == 'Lottery') != -1)
			self.m.s1.game.strip.is_css_animation_on(false);

		// 3] Выбрать кликнутую комнату
		self.m.s1.game.choosen_room(data);

		// 4] Наполнить модель m.s1.animation.circumstances
		// - Если m.s1.game.choosen_room() не пуста.
		if(self.m.s1.game.choosen_room()) {

			// 4.1] Номер раунда
			self.m.s1.animation.circumstances.round_number(self.m.s1.game.choosen_room().rounds()[0].id());

			// 4.2] Какой был серверный unix timestamp в секундах
			self.m.s1.animation.circumstances.timestamp_s(self.m.s1.game.time.ts());

			// 4.3] Какой был статус того раунда, чей номер указан
			self.m.s1.animation.circumstances.round_status(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[0].status());

		}

	};
	
	//--------------------------------------------------//
	// 1.5. Получить и обработать свежие игровые данные //
	//--------------------------------------------------//
	f.s1.fresh_game_data = function(jsondata) {

		// 1. Подготовить функцию для парсинга json
		// - Если передан не валидный json, она вернёт jsonString
		var tryParseJSON = function(jsonString){
			try {
					var o = JSON.parse(jsonString);

					// Handle non-exception-throwing cases:
					// Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
					// but... JSON.parse(null) returns null, and typeof null === "object",
					// so we must check for that, too. Thankfully, null is falsey, so this suffices:
					if (o && typeof o === "object") {
							return o;
					}
			}
			catch (e) { return jsonString; }
			return jsonString;
		};

		// 2. Распарсить json с данными
		var data = tryParseJSON(jsondata.rooms);

		// 3. Запланировать обновление данных
		// - Для каждой комнаты раздельно.
		(function(){ for(var i=0; i<data.length; i++) {

			// 1] Получить ссылку на комнату, которую надо обновить
			var room2update_id = data[i].id;
			var room2update = self.m.s1.indexes.rooms[room2update_id];

			// 2] Если room2update отсутствует, перейти к следующей итерации
			if(!room2update) continue;

			// 3] Подготовить функцию, обновляющую данные комнаты room2update
			var update = function(data, self, room2update_id){

				// 3.1] Получить комнату, которую надо обновить
				var room2update = self.m.s1.indexes.rooms[room2update_id];

				// 3.2] Обновить свежими данными комнату room2update
				for(var key in room2update) {

					// 3.2.1] Если свойство не своё, пропускаем
					if(!room2update.hasOwnProperty(key)) continue;

					// 3.2.2] Если св-ва key нет в data, пропускаем
					if(!data[key]) continue;

					// 3.2.3] Обновить св-во key в room2update данными из data
					room2update[key](ko.mapping.fromJS(data[key])());

				}

				// 3.3] Обновить ссылку на choosen_room
				self.m.s1.game.choosen_room((function(){

					// Получить имя текущей выбранной комнаты
					var name = self.m.s1.game.choosen_room().name();

					// Сделать выбранной комнату с name из game.rooms
					for(var i=0; i<self.m.s1.game.rooms().length; i++) {
						if(self.m.s1.game.rooms()[i].name() == name)
							return self.m.s1.game.rooms()[i];
					}

				})());

				// 1.3] Обновить значение m.s1.game.choosen_status
				// self.m.s1.game.choosen_status(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[self.m.s1.game.choosen_room().rounds()[0].rounds_statuses().length-1].status());

			}.bind(null, data[i], self, room2update_id);

			// 4] Рассчитать моменты, когда надо включать то или иное состояние
			// - Конкретно для комнаты room2update.
			var switchtimes = (function(){

				// 4.1] Текущее серверное время, unix timestamp в секундах
				var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();;

				// 4.2] Время начала состояния Started, unix timestamp в секундах
				var started_at_s = Math.round(moment.utc(room2update.rounds()[0].started_at()).unix());

				// 4.3] Получить данные по длительности различных состояний из конфига выбранной комнаты
				// - В секундах.
				var durations = {};

					// Started
					durations.started = +room2update.room_round_duration_sec() + +room2update.started_client_delta_s();

					// Pending
					durations.pending = +room2update.pending_duration_s() + +room2update.pending_client_delta_s();

					// Lottery
					durations.lottery = +room2update.lottery_duration_ms()/1000 + +room2update.lottery_client_delta_ms()/1000;

					// Winner
					durations.winner = +room2update.winner_duration_s() + +room2update.winner_client_delta_s();

				// 4.4] Произвести расчёты
				var st = {};

					// Когда надо переключить в Pending
					st.pending = moment.utc(+started_at_s + +durations.started);

					// Когда надо переключить в Lottery
					st.lottery = moment.utc(+started_at_s + +durations.started + +durations.pending);

					// Когда надо переключить в Winner
					st.winner = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery + 2);

					// Когда надо переключить в Created
					st.created = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery + +durations.winner);

				// 4.n] Вернуть результаты
				return st;

			})();

			// 5] Получить название нового статуса комнаты
			var newstatus = data[i]['rounds'][0]['rounds_statuses'][0]['status'];

			if(data[i].id == 2) console.log('---');
			if(data[i].id == 2) console.log('newstatus = '+newstatus);

			// 6] В зависимости от условия, выполнить или запланировать выполнение функции update

				// 6.1] Если для room2update пришли данные с состоянием Lottery
				if(newstatus == "Lottery") {

					// 6.1.1] Текущее серверное время, unix timestamp в секундах
					var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();;

					if(data[i].id == 2) console.log('timestamp_s = '+timestamp_s);
					if(data[i].id == 2) console.log('switchtimes.lottery = '+switchtimes.lottery);

					// 6.1.2] Если timestamp_s >= switchtimes.lottery
					// - Выполнить update прямо сейчас.
					if(timestamp_s >= switchtimes.lottery) {
						if(data[i].id == 2)console.log('Update now');
						update();
					}

					// 6.1.3] В ином случае, запланировать выполнение update
					// - На момент времени switchtimes.lottery.
					else {
						if(data[i].id == 2) console.log('Delayed update');
						self.f.s1.queue_add(switchtimes.lottery, update, room2update_id, newstatus, 'Lottery fresh data delayed update in room #'+data[i].id);
					}

				}

				// 6.2] Если для room2update пришли данные с состоянием Winner
				else if(newstatus == "Winner") {

					// 6.1.1] Текущее серверное время, unix timestamp в секундах
					var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();

					if(data[i].id == 2) console.log('timestamp_s = '+timestamp_s);
					if(data[i].id == 2) console.log('switchtimes.winner = '+switchtimes.winner);

					// 6.1.2] Если timestamp_s >= switchtimes.lottery
					// - Выполнить update прямо сейчас.
					if(timestamp_s >= switchtimes.winner) {
						if(data[i].id == 2) console.log('Update now');
						update();
					}

					// 6.1.3] В ином случае, запланировать выполнение update
					// - На момент времени switchtimes.winner.
					else {
						if(data[i].id == 2) console.log('Delayed update');
						self.f.s1.queue_add(switchtimes.winner, update, room2update_id, newstatus, 'Winner fresh data delayed update in room #'+data[i].id);
					}

				}

				// 6.3] Если для room2update пришли данные с состоянием Finished
				else if(newstatus == "Finished") {



				}

				// 6.4] Если для room2update пришли данные с состоянием Created
				else if(newstatus == "Created") {

					// 6.4.1] Текущее серверное время, unix timestamp в секундах
					var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();

					if(data[i].id == 2) console.log('timestamp_s = '+timestamp_s);
					if(data[i].id == 2) console.log('switchtimes.created = '+switchtimes.created);

					// 6.4.2] Если timestamp_s >= switchtimes.created
					// - Выполнить update прямо сейчас.
					if(timestamp_s >= switchtimes.created) {
						if(data[i].id == 2) console.log('Update now');
						update();
					}

					// 6.4.3] В ином случае, запланировать выполнение update
					// - На момент времени switchtimes.created.
					else {
						if(data[i].id == 2) console.log('Delayed update');
						self.f.s1.queue_add(switchtimes.created, update, room2update_id, newstatus, 'Created fresh data delayed update in room #'+data[i].id);
					}

				}

				// 6.n] Выполнить функцию update
				else {
					if(data[i].id == 2) console.log('Update now');
					update();
				}

		}})();


	};

	//------------------------------//
	// s1.6. Перезагрузить страницу //
	//------------------------------//
	f.s1.reload_page = function(data) {

		// 1] Сообщить, что необходимо перезагрузить документ
		toastr.warning("Сайт был обновлён, и будет автоматически перезагружен, чтобы изменения вступили в силу.", "Перезагрузка...");

		// 2] Перезагрузить документ через 3 секунды
		setTimeout(function(){
			location.reload();
		}, 3000);

	};

	//------------------------------------------------------------------//
	// s1.7. Получить URL на изображение скина в стим заданных размеров //
	//------------------------------------------------------------------//
	f.s1.get_steam_img_with_size = function(img, size) {

		return img.replace(new RegExp('\/[^\/]*$','ui'), '/'+size);

	};

	//---------------------------------------------------------------------------//
	// s1.8. Вычислить цвет для вещи в ставке (зависящий от категории и качетва) //
	//---------------------------------------------------------------------------//
	f.s1.get_cat_quality_item_color = function(data) {

		// 1] Получить качество для вещи data
		var quality = data.quality();

		// 2] Получить инфу, является ли вещь StarTrack-вещью или ножом
		var is_startrak = data.is_startrak();
		var is_knife = data.is_knife();

		// 3] Вернуть соответствующий этой вещи цвет

			// 3.1] Нож
			if(is_knife == 1) return '#ffff00';

			// 3.2] StarTrak
			if(is_startrak == 1) return '#cf6a32';

			// 3.3] Mil-Spec Grade
			if(quality == 'Mil-Spec Grade') return '#4b69ff';

			// 3.4] Restricted
			if(quality == 'Restricted') return '#8847ff';

			// 3.5] Classified
			if(quality == 'Classified') return '#d32ce6';

			// 3.6] Covert
			if(quality == 'Covert') return '#eb4b4b';

			// 3.7] Прочие качества
			return 'transparent';

	};

	//------------------------------------------------------------//
	// s1.9. Обновить модель статистики свежими данными с сервера //
	//------------------------------------------------------------//
	f.s1.update_statistics = function(data){

		// Обновить модель статистики
		ko.mapping.fromJS(data, self.m.s1.game.statistics);

	};

	//----------------------------------//
	// s1.10. Добавить задачу в очередь //
	//----------------------------------//
	// - Не добавлять дубли обновлений Winner, Lottery, Created в очередь.
	f.s1.queue_add = function(unixtimestamp, func, room2update_id, newstatus, description){

		// 0] Получить комнату, которую надо обновить
		var room2update = self.m.s1.indexes.rooms[room2update_id];

		// 1] Получить UID обновления: <номер комнаты>_<номер раунда>_<имя статуса>
		var uid = room2update.id() + '_' +
							room2update.rounds()[0].id() + '_' +
							room2update.rounds()[0].rounds_statuses()[0].status();

		// 2] Если статус не Winner, Lottery, Created
		if(['Winner', 'Lottery', 'Created'].indexOf(newstatus) == -1) {

			// 2.1] Попробовать найти задачу с UID в m.s1.game.queue
			var is_uid_in_queue = (function(){

				for(var i=0; i<self.m.s1.game.queue().length; i++) {
					if(self.m.s1.game.queue()[i].uid == uid) return true;
				}
				return false;

			})();

			// 2.2] Если uid нет в очереди, добавить задачу
			if(!is_uid_in_queue) {
				self.m.s1.game.queue.push({
					uid: uid,
					unixtimestamp: unixtimestamp,
					func: func,
					description: description
				});
			}

		}

		// 3] Иначе
		else {

			self.m.s1.game.queue.push({
				uid: uid,
				unixtimestamp: unixtimestamp,
				func: func,
				description: description
			});

		}

	};

	//-----------------------------------------------------------------------------------//
	// s1.11. Выполняется ежесекундно, выполнить все задачи из очереди, чьё время пришло //
	//-----------------------------------------------------------------------------------//
	f.s1.queue_processor = function(){

		// Получить текущий серверный unix timestamp в мс
		var ts = self.m.s1.game.time.ts(); //layoutmodel.m.s0.servertime.timestamp_s();

		// Пробежаться по очереди, выполнить функции, чье время пришло
		// - Выполнять эти функции, удалять из очереди
		for(var i=0; i<self.m.s1.game.queue().length; i++) {
			if(ts >= self.m.s1.game.queue()[i].unixtimestamp) {
				self.m.s1.game.queue()[i].func();
				self.m.s1.game.queue.remove(function(item){
					if(item.func == self.m.s1.game.queue()[i].func) return true;
				});
			}
		}

	};

	//-------------------------------//
	// 1.12. Порт кривой Безье на JS //
	//-------------------------------//
	f.s1.bezier = (function(){
		'use strict';

		/**
		 * Duration value to use when one is not specified (400ms is a common value).
		 * @const
		 * @type {number}
		 */
		var DEFAULT_DURATION = 400;//ms

		/**
		 * The epsilon value we pass to UnitBezier::solve given that the animation is going to run over |dur| seconds.
		 * The longer the animation, the more precision we need in the timing function result to avoid ugly discontinuities.
		 * http://svn.webkit.org/repository/webkit/trunk/Source/WebCore/page/animation/AnimationBase.cpp
		 */
		var solveEpsilon = function(duration) {
			return 1.0 / (200.0 * duration);
		};

		/**
		 * Defines a cubic-bezier curve given the middle two control points.
		 * NOTE: first and last control points are implicitly (0,0) and (1,1).
		 * @param p1x {number} X component of control point 1
		 * @param p1y {number} Y component of control point 1
		 * @param p2x {number} X component of control point 2
		 * @param p2y {number} Y component of control point 2
		 */
		var unitBezier = function(p1x, p1y, p2x, p2y) {

			// private members --------------------------------------------

			// Calculate the polynomial coefficients, implicit first and last control points are (0,0) and (1,1).

			/**
			 * X component of Bezier coefficient C
			 * @const
			 * @type {number}
			 */
			var cx = 3.0 * p1x;

			/**
			 * X component of Bezier coefficient B
			 * @const
			 * @type {number}
			 */
			var bx = 3.0 * (p2x - p1x) - cx;

			/**
			 * X component of Bezier coefficient A
			 * @const
			 * @type {number}
			 */
			var ax = 1.0 - cx -bx;

			/**
			 * Y component of Bezier coefficient C
			 * @const
			 * @type {number}
			 */
			var cy = 3.0 * p1y;

			/**
			 * Y component of Bezier coefficient B
			 * @const
			 * @type {number}
			 */
			var by = 3.0 * (p2y - p1y) - cy;

			/**
			 * Y component of Bezier coefficient A
			 * @const
			 * @type {number}
			 */
			var ay = 1.0 - cy - by;

			/**
			 * @param t {number} parametric timing value
			 * @return {number}
			 */
			var sampleCurveX = function(t) {
				// `ax t^3 + bx t^2 + cx t' expanded using Horner's rule.
				return ((ax * t + bx) * t + cx) * t;
			};

			/**
			 * @param t {number} parametric timing value
			 * @return {number}
			 */
			var sampleCurveY = function(t) {
				return ((ay * t + by) * t + cy) * t;
			};

			/**
			 * @param t {number} parametric timing value
			 * @return {number}
			 */
			var sampleCurveDerivativeX = function(t) {
				return (3.0 * ax * t + 2.0 * bx) * t + cx;
			};

			/**
			 * Given an x value, find a parametric value it came from.
			 * @param x {number} value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param epsilon {number} accuracy limit of t for the given x
			 * @return {number} the t value corresponding to x
			 */
			var solveCurveX = function(x, epsilon) {
				var t0;
				var t1;
				var t2;
				var x2;
				var d2;
				var i;

				// First try a few iterations of Newton's method -- normally very fast.
				for (t2 = x, i = 0; i < 8; i++) {
					x2 = sampleCurveX(t2) - x;
					if (Math.abs (x2) < epsilon) {
						return t2;
					}
					d2 = sampleCurveDerivativeX(t2);
					if (Math.abs(d2) < 1e-6) {
						break;
					}
					t2 = t2 - x2 / d2;
				}

				// Fall back to the bisection method for reliability.
				t0 = 0.0;
				t1 = 1.0;
				t2 = x;

				if (t2 < t0) {
					return t0;
				}
				if (t2 > t1) {
					return t1;
				}

				while (t0 < t1) {
					x2 = sampleCurveX(t2);
					if (Math.abs(x2 - x) < epsilon) {
						return t2;
					}
					if (x > x2) {
						t0 = t2;
					} else {
						t1 = t2;
					}
					t2 = (t1 - t0) * 0.5 + t0;
				}

				// Failure.
				return t2;
			};

			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param epsilon {number} the accuracy of t for the given x
			 * @return {number} the y value along the bezier curve
			 */
			var solve = function(x, epsilon) {
				return sampleCurveY(solveCurveX(x, epsilon));
			};

			// public interface --------------------------------------------

			/**
			 * Find the y of the cubic-bezier for a given x with accuracy determined by the animation duration.
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			return function(x, duration) {
				return solve(x, solveEpsilon(+duration || DEFAULT_DURATION));
			};
		};

		// http://www.w3.org/TR/css3-transitions/#transition-timing-function
		return {
			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			linear: unitBezier(0.0, 0.0, 1.0, 1.0),

			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			ease: unitBezier(0.25, 0.1, 0.25, 1.0),

			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			easeIn: unitBezier(0.42, 0, 1.0, 1.0),

			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			easeOut: unitBezier(0, 0, 0.58, 1.0),

			/**
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			easeInOut: unitBezier(0.42, 0, 0.58, 1.0),

			/**
			 * @param p1x {number} X component of control point 1
			 * @param p1y {number} Y component of control point 1
			 * @param p2x {number} X component of control point 2
			 * @param p2y {number} Y component of control point 2
			 * @param x {number} the value of x along the bezier curve, 0.0 <= x <= 1.0
			 * @param duration {number} the duration of the animation in milliseconds
			 * @return {number} the y value along the bezier curve
			 */
			cubicBezier: function(p1x, p1y, p2x, p2y, x, duration) {
				return unitBezier(p1x, p1y, p2x, p2y)(x, duration);
			}
		};
	})();

	//---------------------------------------------//
	// s1.13. Анимация ленты аватарок с помощью JS //
	//---------------------------------------------//
	f.s1.lottery = function(){ setTimeout(function(){

		// 1. Если установлен тип анимации не 'js', завершить
		if(self.m.s1.animation.choosen_type().name() != 'js') return;

		// 2. Получить временные параметры
		// - Общее время розыгрыша, сколько уже прошло, сколько осталось
		var times = (function(){

			// 1] Время начала состояния Started, unix timestamp в секундах
			var started_at_s = Math.round(moment.utc(self.m.s1.game.choosen_room().rounds()[0].started_at()).unix());

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

			// 3] Время начала состояния lottery
			var lottery_start = moment.utc(+started_at_s + +durations.started + +durations.pending);

			// 4] Время конца состояния lottery
			var lottery_end = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery);

			// 5] Получить текущий серверный timestamp в секундах
			var timestamp_s = self.m.s1.game.time.ts();

			// 6] Вычислить значение для passed_s
			var passed_s = (function(){

				// 6.1] Если значение в пределах от 0 до durations.lottery
				if(timestamp_s - lottery_start >= 0 && timestamp_s - lottery_start <= durations.lottery)
					return timestamp_s - lottery_start;

				// 6.2] Если значение меньше 0, то взять 0
				else if(timestamp_s - lottery_start < 0)
					return 0;

				// 6.3] Еслиз начение больше durations.lottery, взять durations.lottery
				else if(timestamp_s - lottery_start > durations.lottery)
					return durations.lottery;

			})();

			// 7] Вычислить значение для left_s
			var left_s = (function(){

				// 7.1] Если значение в пределах от 0 до durations.lottery
				if(lottery_end - timestamp_s >= 0 && lottery_end - timestamp_s <= durations.lottery)
					return lottery_end - timestamp_s;

				// 7.2] Если значение меньше 0, то взять 0
				else if(lottery_end - timestamp_s < 0)
					return 0;

				// 7.3] Еслиз начение больше durations.lottery, взять durations.lottery
				else if(lottery_end - timestamp_s > durations.lottery)
					return durations.lottery;

			})();

			// n] Вернуть результаты
			return {
				passed_s: passed_s, 		// 5
				left_s: 	left_s, 			// 10
				duration: durations.lottery
			};

		})();

		// 4. Получить текущее время, время окончания розыгрыша
		var currenttime = Date.now();
		var futuretime = +currenttime + times.left_s*1000;

		// 5. Установить в исходную позицию ленту аватаров
		(function(){

			// 1] Выключить css-анимацию
			//self.m.s1.game.strip.is_css_animation_on(false);

			// 2] Получить прогресс по Безье
			var progress = self.m.s1.animation.bezier.get(times.passed_s/times.duration);

			// 3] Получить разницу между final_px и start_px
			var path_px = self.m.s1.game.strip.final_px() - self.m.s1.game.strip.start_px();

			// 4] Получить позицию в px, которую надо установить
			var position2set_px = self.m.s1.game.strip.start_px() + path_px * progress;

			// 5] Установить позицию position2set_px
			self.m.s1.game.strip.currentpos(position2set_px);

			// 4.n. Включить css-анимацию
			//self.m.s1.game.strip.is_css_animation_on(true);

		})();

		// 6. Получить длительность состояния lottery в мс
		var lottery_duration_ms = +self.m.s1.game.choosen_room().lottery_duration_ms() + +self.m.s1.game.choosen_room().lottery_client_delta_ms();

		// 7. Подготовить обработчик для проведения анимации розыгрыша
		var handler = function handler(futuretime, times, lottery_duration_ms) {

			// 2] Получить прогресс по Безье
			var progress = self.m.s1.animation.bezier.get((lottery_duration_ms - (futuretime - Date.now()))/lottery_duration_ms);

			// 3] Получить разницу между final_px и start_px
			var path_px = Math.abs(self.m.s1.game.strip.final_px() - self.m.s1.game.strip.start_px());

			// 4] Получить позицию в px, которую надо установить
			var position2set_px = self.m.s1.game.strip.start_px() - path_px * progress;

			// 5] Установить позицию position2set_px
			self.m.s1.game.strip.currentpos(position2set_px);

			// n] Если дошли до конца
			if(((Date.now() > futuretime) && interval)) {

				// n.1) Удалить интервал
				clearInterval(interval);

				self.m.s1.game.strip.rooms_with_working_animation.remove(function(item){
					return item == self.m.s1.game.choosen_room().id();
				});

			}

		};

		// 8. Остановить все предыдущие анимации
		for(var i=0; i<self.m.s1.game.strip.rooms_with_working_animation().length; i++) {
			clearInterval(self.m.s1.game.strip.rooms_with_working_animation()[i].interval);
		}
		self.m.s1.game.strip.rooms_with_working_animation.removeAll();

		// n. Запустить розыгрыш

			// n.1. Запустить
			var interval = setInterval(handler, 25, futuretime, times, lottery_duration_ms);

			// n.2. Добавить в реестр
			self.m.s1.game.strip.rooms_with_working_animation.push({
				id_room: self.m.s1.game.choosen_room().id(),
				interval: interval
			});

	}, 500); };

	//---------------------------------------------------//
	// s1.14. Обработка клика по кнопке "Внести депозит" //
	//---------------------------------------------------//
	f.s1.onclick_handler = function() {
		//if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1)
			window.open(self.m.s1.game.current_bot());
		//else popupCenter(self.m.s1.game.current_bot(),'steam','800','600');
	};

	//--------------------------------------------------------------------//
	// s1.15. Сообщение от сервера о том, что ставка игрока была отменена //
	//--------------------------------------------------------------------//
	f.s1.tradeoffer_cancel = function(data) {

		// Уведомить игрока о том, что его оффер был отклонён
		toastr.error("Ваше торговое предложение отклонено.");

	};

	//--------------------------------------------------------------//
	// s1.16. Сообщение от сервера о том, что ставка игрока принята //
	//--------------------------------------------------------------//
	f.s1.tradeoffer_accepted = function(data) {

		// Уведомить игрока о том, что его оффер был отклонён
		toastr.success("Ваше торговое предложение принято.");

	};

	//---------------------------------------------------------------------//
	// s1.17. Сообщение от сервера о том, что ставка игрока обрабатывается //
	//---------------------------------------------------------------------//
	f.s1.tradeoffer_processing = function(data) {

		// Уведомить игрока о том, что его оффер был отклонён
		toastr.info("Ваше торговое предложение обрабатывается...");

	};

//	//---------------------------------------------------//
//	// s1.16. Обработка клика по кнопке "Внести депозит" //
//	//---------------------------------------------------//
//	f.s1.tradeoffer_cancel = function(data) {
//
//		// Уведомить игрока о том, что его оффер был отклонён
//		toastr.error("Отффер отклонён");
//
//	};



	//	f.s1.update_quality_test = function(){
	//
	//		ajaxko(self, {
	//			command: 	    "\\M8\\Commands\\C33_update_items_quality_indb",
	//			from: 		    "f.s1.update_quality_test",
	//			data: 		    {},
	//			prejob:       function(config, data, event){
	//
	//			},
	//			postjob:      function(data, params){},
	//			ok_0:         function(data, params){
	//
	//				notify({msg: "Успех"});
	//
	//			},
	//			ok_2: function(data, params){
	//
	//				notify({msg: data.data.errormsg});
	//
	//			}
	//		});
	//
	//	};



return this; }};




























