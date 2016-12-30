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

		// 1. Очистить m.s1.game.rooms
		self.m.s1.game.rooms.removeAll();

		// 2. Обновить
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

	};
	
	//--------------------------------------------------//
	// 1.5. Получить и обработать свежие игровые данные //
	//--------------------------------------------------//
	f.s1.fresh_game_data = function(data) {

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

		// 2. Обновить данные в rooms данными rooms_new_data
		self.m.s1.game.rooms(ko.mapping.fromJS(tryParseJSON(data.rooms))());

		// 3. Обновить ссылку на choosen_room
		self.m.s1.game.choosen_room((function(){

			// 1] Получить имя текущей выбранной комнаты
			var name = self.m.s1.game.choosen_room().name();

			// 2] Сделать выбранной комнату с name из game.rooms
			for(var i=0; i<self.m.s1.game.rooms().length; i++) {
				if(self.m.s1.game.rooms()[i].name() == name)
					return self.m.s1.game.rooms()[i];
			}

		})());

		// 4. Обновить значение m.s1.game.choosen_status
		self.m.s1.game.choosen_status(self.m.s1.game.choosen_room().rounds()[0].rounds_statuses()[self.m.s1.game.choosen_room().rounds()[0].rounds_statuses().length-1].status());

		//		// 5. Если имя текущего статуса == "Created", "First Bet, "Started"
		//		if(["Created", "First bet", "Started"].indexOf(self.m.s1.game.choosen_status()) != -1) {
		//
		//			// Установить колесо в исходное положение (угол == 0)
		//			self.f.s1.lottery_setangle_0();
		//
		//			// Установить исходный цвет стрелочки
		//			var arrow = $('.winnerarrow path');
		//			arrow.attr('style', 'fill: #3a3a3a');
		//
		//		}
		//
		//		// 6. Установить правильный аватар, если статус == "Pending"
		//		if(self.m.s1.game.choosen_status() == "Pending") {
		//			(function(){
		//
		//				var avatar = $('.wheel-non-svg-panel .wrapper .player-avatar img');
		//				if(self.m.s1.indexes.segments && self.m.s1.indexes.segments[180])
		//					avatar.attr('src', self.m.s1.indexes.segments[180].avatar);
		//
		//			})();
		//		}
		//
		//		// 7. Запустить колесо, если текущий статус "Lottery"
		//		// - Передать в f.s1.lottery переданный с сервера итоговый угол (от 0 до 359).
		//		if(self.m.s1.game.choosen_status() == "Lottery") {
		//
		//			// Колесо запускается в m.js после перерасчёта модели колеса
		//			// - Т.К. в этой точке модель колеса ещё не до конца перерасчиталась.
		//			// - Ищи в m.js: "Перерасчитать модель для отрисовки кольца"
		//
		//			// TODO: выставить угол (после того, как он начнёт определяться на сервере)
		//			//self.f.s1.lottery(270, null, null););
		//
		//		}


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



return this; }};




























