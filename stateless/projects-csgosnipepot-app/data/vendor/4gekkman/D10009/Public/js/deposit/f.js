/*//==============================================================================================================////
////																			                                                                        ////
////   Функционал интерфейса пополнения баланса скинами, предназначен для подключения в основной f.js документа   ////
////																			                                                                        ////
////==============================================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 	s6. Функционал интерфейса пополнения баланса скинами
 *
 *    f.s6.update_inventory      			| s6.1. Обновить инвентарь
 *    f.s6.update_inventory_data  		| s6.2. Обновить инвентарь переданными в аргументе данными
 *    f.s6.choose_sort            		| s6.3. Выбрать сортировку
 *    f.s6.get_cat_quality_item_color | s6.4. Вычислить цвет для вещи (зависящий от категории и качества)
 *    f.s6.pagi_next                  | s6.5. Изменить номер пагинационной страницы на следующий
 *    f.s6.pagi_prev                  | s6.6. Изменить номер пагинационной страницы на предыдущий
 *    f.s6.move_to_mybet              | s6.7. Выбрать вещь для отправки в качестве депозита
 *    f.s6.move_to_myinventory        | s6.8. Снять выбор с вещи для отправки в качестве депозита
 *    f.s6.deposit                    | s6.9. Пополнить баланс выбранными скинами
 *
 */

	//--------------------------------------------------------------------//


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctionsDeposit = { constructor: function(self, f) { f.s6 = this;

//--------------------------//
// s6.1. Обновить инвентарь //
//--------------------------//
f.s6.update_inventory = function(force, verbose, data, event) {

	// Если force не передан, пусть он будет равен false
	if(!force || (force != true && force != false)) force = false;

	// Завершить, если имеются активные AJAX-запросы
	if(self.m.s0.ajax_counter()) return;

	// Завершить, если force == false и m.s6.inventory.items не пуст
	if(force == false && self.m.s6.inventory.items().length) return;

	// Отправить на сервер ajax-запрос с pinned и expanded для сохранения в куки
	ajaxko(self, {
		key: 	    		"D10009:6",
		from: 		    "update_inventory",
		data: 		    {
			force: 							force
		},
		prejob:       function(config, data, event){

			// 1] Если verbose == true, сообщить пользователю о своих действия
			if(verbose) toastr.info("Это может занять несколько секунд...", "Инвентарь обновляется");

			// 2] Показать модальный щит со спиннером загрузки
			self.m.s6.is_inv_loading_shield_visible(true);

		},
		postjob:      function(data, params){


		},
		ajax_params: {
			'verbose': verbose
		},
		ok_0:         function(data, params){

			// 1] Обновить инвентарь
			f.s6.update_inventory_data(data.data.inventory.data.rgDescriptions, params.verbose);

			// 2] Скрыть модальный щит
			self.m.s6.is_inv_loading_shield_visible(false);

		},
		ok_1:         function(data, params){

			// 1] Скрыть модальный щит
			self.m.s6.is_inv_loading_shield_visible(false);

		},
		ok_2:         function(data, params){

			// 1] Если это ошибка, связанная с запретом на слишком частые запросы инвентаря
			if(data.data.left_secs) {
				if(params.verbose) toastr.error("Инвентарь можно запрашивать не чаще, чем раз в "+data.data.howoften_sec+" секунд. Осталось подождать "+data.data.left_secs);
			}

			// 2] Если это обычная ошибка
			else {
				if(params.verbose) toastr.error("К сожалению, инвентарь обновить не удалось. Проверьте настройки своего профиля в Steam, инвентарь должен быть доступен публично (Public).");
			}

			// 3] Скрыть модальный щит
			self.m.s6.is_inv_loading_shield_visible(false);

		}
	});

};


//----------------------------------------------------------//
// s6.2. Обновить инвентарь переданными в аргументе данными //
//----------------------------------------------------------//
f.s6.update_inventory_data = function(data, verbose) {

	// 1] Обновить инвентарь
	self.m.s6.inventory.items.removeAll();
	self.m.s6.inventory.items(ko.mapping.fromJS(data)());

	// 2] Наполнить массивы items и items_filtered_sorted_paginated модели выбора вещей из инвентаря для депозита
	(function(){

		// 2.1] Очистить массивы
		self.m.s6.deposit.left.items.removeAll();
		self.m.s6.deposit.left.items_filtered_sorted_paginated.removeAll();

		// 2.2] Наполнить массивы
		for(var i=0; i<self.m.s6.inventory.items().length; i++) {
			self.m.s6.deposit.left.items.push(self.m.s6.inventory.items()[i]);
			self.m.s6.deposit.left.items_filtered_sorted_paginated.push(self.m.s6.inventory.items()[i]);
		}

	})();

	// 3] Очистить m.s6.deposit.choosen.items
	self.m.s6.deposit.choosen.items.removeAll();

	// 4] Переключить на 1-ю страницу
	self.m.s6.deposit.pagi.pagenum(1);

	// n] Сообщить об успехе
	if(verbose) toastr.success("Инвентарь успешно обновлён!");

};


//--------------------------//
// s6.3. Выбрать сортировку //
//--------------------------//
f.s6.choose_sort = function(data, event) {

	// 1] Если нужно изменить только направление текущей сортировки
	if(data.name() == self.m.s6.sort.criterias.choosen().name()) {

		// 1.1] Изменить направление сортировки
		if(self.m.s6.sort.directions.choosen().name() == 'asc')
			self.m.s6.sort.directions.choosen(self.m.s6.sort.directions.list()[1]);
		else
			self.m.s6.sort.directions.choosen(self.m.s6.sort.directions.list()[0]);

		// 1.n] Завершить
		return;

	}

	// 2] Если нужно изменить тип сортировки
	else {

		// 2.1] Если текущая сортировка 'byprice'
		if(data.name() == 'byprice') {

			// 2.1.1] Изменить критерий сортировки на 'byname'
			self.m.s6.sort.criterias.choosen(self.m.s6.sort.criterias.list()[0]);

			// 2.1.2] Изменить направление на asc
			self.m.s6.sort.directions.choosen(self.m.s6.sort.directions.list()[1]);

			// 2.1.n] Завершить
			return;

		}

		// 2.2] Если текущая сортировка 'byname'
		if(data.name() == 'byname') {

			// 2.2.1] Изменить критерий сортировки на 'byprice'
			self.m.s6.sort.criterias.choosen(self.m.s6.sort.criterias.list()[1]);

			// 2.2.2] Изменить направление на desc
			self.m.s6.sort.directions.choosen(self.m.s6.sort.directions.list()[0]);

			// 2.2.n] Завершить
			return;

		}

	}

};


//-------------------------------------------------------------------//
// s6.4. Вычислить цвет для вещи (зависящий от категории и качества) //
//-------------------------------------------------------------------//
f.s6.get_cat_quality_item_color = function(data, event) {

	// 1] Получить качество для вещи data
	var quality = data.quality();

	// 2] Получить itemtypes
	var itemtypes = data.itemtypes;

	// 3] Получить инфу, является ли вещь StarTrack-вещью или ножом
	var is_startrak = itemtypes.startrak();
	var is_knife = itemtypes.knife();

	// 4] Вернуть соответствующий этой вещи цвет

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


//----------------------------------------------------------//
// s6.5. Изменить номер пагинационной страницы на следующий //
//----------------------------------------------------------//
f.s6.pagi_next = function(data, event) {

	// 1] Вычислить номер следующей пагинационной страницы
	var next = +self.m.s6.deposit.pagi.pagenum() + 1;

	// 2] Если next не превышает общее кол-во страниц, переключить
	if(next <= self.m.s6.deposit.pagi.count())
		self.m.s6.deposit.pagi.pagenum(next);

};

//-----------------------------------------------------------//
// s6.6. Изменить номер пагинационной страницы на предыдущий //
//-----------------------------------------------------------//
f.s6.pagi_prev = function(data, event) {

	// 1] Если номер текущей страницы уже равен 1, завершить
	if(self.m.s6.deposit.pagi.pagenum() == 1) return;

	// 2] Вычислить номер предыдущей пагинационной страницы
	var prev = +self.m.s6.deposit.pagi.pagenum() - 1;

	// 3] Если prev не меньше 1, переключить
	if(prev >= 1)
		self.m.s6.deposit.pagi.pagenum(prev);

};

//-----------------------------------------------------//
// s6.7. Выбрать вещь для отправки в качестве депозита //
//-----------------------------------------------------//
f.s6.move_to_mybet = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	if(self.m.s0.ajax_counter()) return;

	// 2] Добавить data в m.s6.deposit.choosen.items
	// - Но только если data ещё нет там.
	if(self.m.s6.deposit.choosen.items().indexOf(data) == -1)
		self.m.s6.deposit.choosen.items.push(data);

	// 3] Убрать data из deposit.left.items и deposit.left.items_filtered_sorted_paginated

		// 3.1] Из deposit.left.items
		self.m.s6.deposit.left.items.remove(function(item){
			return item == data;
		});

		// 3.2] Из deposit.left.items_filtered_sorted_paginated
		self.m.s6.deposit.left.items_filtered_sorted_paginated.remove(function(item){
			return item == data;
		});

	// 4] Если все скины на странице выбраны, переключить на предыдущую
	if(!self.m.s6.deposit.left.items_filtered_sorted_paginated().length)
		self.f.s6.pagi_prev();

	// n] Обновить perfect scrollbar
	Ps.update(document.getElementsByClassName('deposit-choosen-items-cont')[0]);

};

//-----------------------------------------------------------//
// s6.8. Снять выбор с вещи для отправки в качестве депозита //
//-----------------------------------------------------------//
f.s6.move_to_myinventory = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	if(self.m.s0.ajax_counter()) return;

	// 2] Добавить data в deposit.left.items
	// - Но только если data ещё нет там.
	if(self.m.s6.deposit.left.items().indexOf(data) == -1)
		self.m.s6.deposit.left.items.push(data);

	// 3] Убрать data из deposit.choosen.items
	self.m.s6.deposit.choosen.items.remove(function(item){
		return item == data;
	});

	// n] Обновить perfect scrollbar
	Ps.update(document.getElementsByClassName('deposit-choosen-items-cont')[0]);

};

//-------------------------------------------//
// s6.9. Пополнить баланс выбранными скинами //
//-------------------------------------------//
f.s6.deposit = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	// - Это анонимный пользователь.
	if(self.m.s0.ajax_counter() || !layoutmodel.m.s0.is_logged_in()) return;

	// 2] Учесть лимиты и ограничения
	// - В случае нарушения лимитов, сообщить и завершить

		// 2.1] Если для ставки не выбрана ни одна вещь
		if(self.m.s6.deposit.choosen.items().length <= 0) {
			toastr.info("Выберите хотя бы 1-ну вещь в инвентаре слева, кликнув/тапнув по ней.", "Нельзя поставить ничего");
			return;
		}

	// 3] Сформировать параметры ставки

		// 3.1] Выбранные для ставки предметы
		var items2bet = ko.mapping.toJS(self.m.s6.deposit.choosen.items);

		// 3.2] Количество выбранных для ставки предметов
		var items2bet_length = self.m.s6.deposit.choosen.items().length;

	// 4] Отправить на сервер ajax-запрос
	// - По результатам которого бот должен сделать игроку торговое предложение.
	ajaxko(self, {
		key: 	    		"D10009:7",
		from: 		    "f.s6.deposit",
		data: 		    {
			items2bet: 				items2bet
		},
		ajax_params: {
			items2bet_length: items2bet_length
		},
		prejob:       function(config, data, event){

			// Показать тост
			toastr.info("Идёт подготовка торгового предложения. Это может занять несколько секунд...");

		},
		postjob:      function(data, params){


		},
		ok_0:         function(data, params){

			// 1] Подготовить html для тоста
			var html = (function(){

				// 1.1] Подготовить результат
				var result =

					// Об успешно созданном торговом предложении
					"<p>Торговое предложение №"+data.data.tradeofferid+" успешно создано и отправлено вам.</p>" +

					// Просьба подтвердить
					"<p>Для завершения операции, <a style='color: #8ab4f8; ' target='_blank' href='https://steamcommunity.com/tradeoffer/"+data.data.tradeofferid+"'>подтвердите оффер в Steam</a>. " +

					// Просьба сверить код безопасности
					"Обязательно проверьте код безопасности: "+data.data.safecode+".</p>" +

					// Пояснение, когда придут монеты
					"<p>Монеты будут начислены в течение нескольких минут после подтверждения торгового предложения.</p>"

				;

				// 1.2] Вернуть результат
				return result;

			})();

			// n] Показать тост
			toastr.warning(html, "Пополнение баланса скинами", {
				timeOut: 					"300000",
				extendedTimeOut: 	"300000"
			});

		},
		ok_2:         function(data, params){

			// 1] Показать стандартный тост с ошибкой
			toastr.error(data.data.errormsg, "Не удалось создать трейд");

		}
	});


};






return this; }};




























