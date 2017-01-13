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
 *
 *
 *
 */


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

		// 1] Выбрать кликнутую комнату
		self.m.s1.game.choosen_room(data);

		// 2] Наполнить модель m.s1.animation.circumstances
		// - Если m.s1.game.choosen_room() не пуста.
		if(self.m.s1.game.choosen_room()) {

			// 2.1] Номер раунда
			self.m.s1.animation.circumstances.round_number(self.m.s1.game.choosen_room().rounds()[0].id());

			// 2.2] Какой был серверный unix timestamp в секундах
			self.m.s1.animation.circumstances.timestamp_s(self.m.s1.game.time.ts());

			// 2.3] Какой был статус того раунда, чей номер указан
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
			var room2update = self.m.s1.indexes.rooms[data[i].id];

			// 2] Если room2update отсутствует, перейти к следующей итерации
			if(!room2update) continue;

			// 3] Подготовить функцию, обновляющую данные комнаты room2update
			var update = function(data, self, room2update){

				// 3.1] Обновить свежими данными комнату room2update
				for(var key in room2update) {

					// 3.1.1] Если свойство не своё, пропускаем
					if(!room2update.hasOwnProperty(key)) continue;

					// 3.1.2] Если св-ва key нет в data, пропускаем
					if(!data[key]) continue;

					// 3.1.3] Обновить св-во key в room2update данными из data
					room2update[key](ko.mapping.fromJS(data[key])());

				}

				// 1.2] Обновить ссылку на choosen_room
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
				self.m.s1.game.choosen_status(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[self.m.s1.game.choosen_room().rounds()[0].rounds_statuses().length-1].status());

			}.bind(null, data[i], self, room2update);

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
					durations.started = +room2update.room_round_duration_sec() + 5;

					// Pending
					durations.pending = room2update.pending_duration_s();

					// Lottery
					durations.lottery = +room2update.lottery_duration_ms()/1000 + 5;

					// Winner
					durations.winner = +room2update.winner_duration_s() + 9;

				// 4.4] Произвести расчёты
				var st = {};

					// Когда надо переключить в Pending
					st.pending = moment.utc(+started_at_s + +durations.started);

					// Когда надо переключить в Lottery
					st.lottery = moment.utc(+started_at_s + +durations.started + +durations.pending);

					// Когда надо переключить в Winner
					st.winner = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery);

					// Когда надо переключить в Started
					st.started = moment.utc(+started_at_s + +durations.started + +durations.pending + +durations.lottery + +durations.winner);

				// 4.n] Вернуть результаты
				return st;

			})();

			// 5] Получить название нового статуса комнаты
			var newstatus = data[i]['rounds'][0]['rounds_statuses'][0]['status'];

			// 6] В зависимости от условия, выполнить или запланировать выполнение функции update

				// 6.1] Если для room2update пришли данные с состоянием Lottery
				if(newstatus == "Lottery") {

					// 6.1.1] Текущее серверное время, unix timestamp в секундах
					var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();;

					// 6.1.2] Если timestamp_s >= switchtimes.lottery
					// - Выполнить update прямо сейчас.
					if(timestamp_s >= switchtimes.lottery)
						update();

					// 6.1.3] В ином случае, запланировать выполнение update
					// - На момент времени switchtimes.lottery.
					else
						self.f.s1.queue_add(switchtimes.lottery, update);

				}

				// 6.2] Если для room2update пришли данные с состоянием Winner
				else if(newstatus == "Winner") {

					// 6.1.1] Текущее серверное время, unix timestamp в секундах
					var timestamp_s = self.m.s1.game.time.ts();//layoutmodel.m.s0.servertime.timestamp_s();//self.m.s1.game.time.ts();

					// 6.1.2] Если timestamp_s >= switchtimes.lottery
					// - Выполнить update прямо сейчас.
					if(timestamp_s >= switchtimes.winner)
						update();

					// 6.1.3] В ином случае, запланировать выполнение update
					// - На момент времени switchtimes.lottery.
					else
						self.f.s1.queue_add(switchtimes.winner, update);

				}

				// 6.n] Выполнить функцию update
				else
					update();

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
	f.s1.queue_add = function(unixtimestamp, func){
		self.m.s1.game.queue.push({
			unixtimestamp: unixtimestamp,
			func: func
		});
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




























