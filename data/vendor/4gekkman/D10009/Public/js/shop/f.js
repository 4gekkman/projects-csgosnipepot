/*//========================================================================================////
////																			                                                  ////
////   Функционал магазина скинов, предназначен для подключения в основной f.js документа   ////
////																			                                                  ////
////========================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 	s7. Функционал интерфейса пополнения баланса скинами
 *
 *    f.s7.init_goods									| s7.1. Загрузить в клиент текущие товарные остатки магазина при 1-м его открытии
 *    f.s7.update_goods_full    			| s7.2. Обновить товарные остатки переданными в аргументе данными (все товары магазина)
 *    f.s7.choose_sort          			| s7.3. Выбрать сортировку
 *    f.s7.get_cat_quality_item_color | s7.4. Вычислить цвет для вещи (зависящий от категории и качества)
 *    f.s7.pagi_next                  | s7.5. Изменить номер пагинационной страницы на следующий
 *    f.s7.pagi_prev                  | s7.6. Изменить номер пагинационной страницы на предыдущий
 *    f.s7.move_to_cart               | s7.7. Положить вещь в корзину
 *    f.s7.remove_from_cart           | s7.8. Убрать вещь из корзины
 *    f.s7.buy                        | s7.9. Купить выбранные скины
 *    f.s7.update_goods_add_subtract  | s7.10. Обрабатывать входящие через websocket пуш-изменения товарных остатков
 *    f.s7.offer_created              | s7.11. Уведомление пользователя о создании нового оффера
 *    f.s7.offer_not_enough_money     | s7.12. Уведомление пользователя о том, что недостаточно монет для оплаты некоторых вещей из заказа
 *    f.s7.offer_reserved     				| s7.13. Уведомление пользователя о том, что некоторые вещи из его покупки уже зарезервированы
 *    f.s7.items2order_reserved     	| s7.14. Уведомление пользователя о том, что некоторые вещи из его покупки (на заказ) уже зарезервированы
 *    f.s7.items2order_success     		| s7.15. Уведомление пользователя о том, что заказанные им скины на заказ он получит в течение N часов
 *    f.s7.update_goods_order_add_sub | s7.10. Обрабатывать входящие через websocket пуш-изменения товарных остатков на заказываемые вещи
 *
 *
 */

	//--------------------------------------------------------------------//


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctionsShop = { constructor: function(self, f) { f.s7 = this;

//---------------------------------------------------------------------------------//
// s7.1. Загрузить в клиент текущие товарные остатки магазина при 1-м его открытии //
//---------------------------------------------------------------------------------//
f.s7.init_goods = function(data, event) {

	// Завершить, если имеются активные AJAX-запросы
	if(self.m.s0.ajax_counter()) return;

	// Завершить, если m.s7.goods не пуст
	if(self.m.s7.goods().length) return;

	// Отправить на сервер ajax-запрос
	ajaxko(self, {
		key: 	    		"D10009:8",
		from: 		    "init_goods",
		data: 		    {

		},
		prejob:       function(config, data, event){

			// 1] Показать модальный щит со спиннером загрузки
			self.m.s7.is_goods_loading_shield_visible(true);

		},
		postjob:      function(data, params){


		},
		ajax_params: {

		},
		ok_0:         function(data, params){

			// 1] Обновить инвентарь
			f.s7.update_goods_full(data.data.goods, data.data.goods2order);

			// 2] Скрыть модальный щит
			self.m.s7.is_goods_loading_shield_visible(false);

		},
		ok_1:         function(data, params){

			// 1] Скрыть модальный щит
			self.m.s7.is_goods_loading_shield_visible(false);

		},
		ok_2:         function(data, params){

			// 1] Сообщить об ошибке
			toastr.error("К сожалению, не удалось загрузить товары магазина.");

			// 2] Скрыть модальный щит
			self.m.s7.is_goods_loading_shield_visible(false);

		}
	});

};

//---------------------------------------------------------------------------------------//
// s7.2. Обновить товарные остатки переданными в аргументе данными (все товары магазина) //
//---------------------------------------------------------------------------------------//
f.s7.update_goods_full = function(goods, goods2order) {

	// 1] Обновить товары
	(function(){

		// 1.1] Удалить все товары из m.s7.goods
		self.m.s7.goods.removeAll();

		// 1.2] Наполнить m.s7.goods обычными товарами
		self.m.s7.goods(ko.mapping.fromJS(goods)());

		// 1.3] Дополнить m.s7.goods товарами на заказ
		for(var i=0; i<goods2order.length; i++) {
			self.m.s7.goods.push(ko.mapping.fromJS(goods2order[i]));
		}

	})();

	// 2] Наполнить массивы items и items_filtered_sorted_paginated модели выбора вещей из инвентаря для депозита
	(function(){

		// 2.1] Очистить массивы
		self.m.s7.shop.left.items.removeAll();
		self.m.s7.shop.left.items_filtered_sorted_paginated.removeAll();

		// 2.2] Наполнить массивы
		for(var i=0; i<self.m.s7.goods().length; i++) {

			// 2.2.1] Наполнить m.s7.shop.left.items
			self.m.s7.shop.left.items.push(self.m.s7.goods()[i]);

			// 2.2.2] Наполнить m.s7.shop.left.items_filtered_sorted_paginated
			self.m.s7.shop.left.items_filtered_sorted_paginated.push(self.m.s7.goods()[i]);

		}

	})();

	// 3] Очистить m.s7.shop.choosen.items
	self.m.s7.shop.choosen.items.removeAll();

	// 4] Переключить на 1-ю страницу
	self.m.s7.shop.pagi.pagenum(1);

};

//--------------------------//
// s7.3. Выбрать сортировку //
//--------------------------//
f.s7.choose_sort = function(data, event) {

	// 1] Если нужно изменить только направление текущей сортировки
	if(data.name() == self.m.s7.sort.criterias.choosen().name()) {

		// 1.1] Изменить направление сортировки
		if(self.m.s7.sort.directions.choosen().name() == 'asc')
			self.m.s7.sort.directions.choosen(self.m.s7.sort.directions.list()[1]);
		else
			self.m.s7.sort.directions.choosen(self.m.s7.sort.directions.list()[0]);

		// 1.n] Завершить
		return;

	}

	// 2] Если нужно изменить тип сортировки
	else {

		// 2.1] Если текущая сортировка 'byprice'
		if(data.name() == 'byprice') {

			// 2.1.1] Изменить критерий сортировки на 'byname'
			self.m.s7.sort.criterias.choosen(self.m.s7.sort.criterias.list()[0]);

			// 2.1.2] Изменить направление на asc
			self.m.s7.sort.directions.choosen(self.m.s7.sort.directions.list()[1]);

			// 2.1.n] Завершить
			return;

		}

		// 2.2] Если текущая сортировка 'byname'
		if(data.name() == 'byname') {

			// 2.2.1] Изменить критерий сортировки на 'byprice'
			self.m.s7.sort.criterias.choosen(self.m.s7.sort.criterias.list()[1]);

			// 2.2.2] Изменить направление на desc
			self.m.s7.sort.directions.choosen(self.m.s7.sort.directions.list()[0]);

			// 2.2.n] Завершить
			return;

		}

	}

};

//-------------------------------------------------------------------//
// s7.4. Вычислить цвет для вещи (зависящий от категории и качества) //
//-------------------------------------------------------------------//
f.s7.get_cat_quality_item_color = function(data, event) {

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
// s7.5. Изменить номер пагинационной страницы на следующий //
//----------------------------------------------------------//
f.s7.pagi_next = function(data, event) {

	// 1] Вычислить номер следующей пагинационной страницы
	var next = +self.m.s7.shop.pagi.pagenum() + 1;

	// 2] Если next не превышает общее кол-во страниц, переключить
	if(next <= self.m.s7.shop.pagi.count())
		self.m.s7.shop.pagi.pagenum(next);

};

//-----------------------------------------------------------//
// s7.6. Изменить номер пагинационной страницы на предыдущий //
//-----------------------------------------------------------//
f.s7.pagi_prev = function(data, event) {

	// 1] Если номер текущей страницы уже равен 1, завершить
	if(self.m.s7.shop.pagi.pagenum() == 1) return;

	// 2] Вычислить номер предыдущей пагинационной страницы
	var prev = +self.m.s7.shop.pagi.pagenum() - 1;

	// 3] Если prev не меньше 1, переключить
	if(prev >= 1)
		self.m.s7.shop.pagi.pagenum(prev);

};

//-------------------------------//
// s7.7. Положить вещь в корзину //
//-------------------------------//
f.s7.move_to_cart = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	if(self.m.s0.ajax_counter()) return;

	// 2] Добавить data в m.s7.shop.choosen.items
	// - Но только если data ещё нет там.
	if(self.m.s7.shop.choosen.items().indexOf(data) == -1)
		self.m.s7.shop.choosen.items.push(data);

	// 3] Убрать data из shop.left.items и shop.left.items_filtered_sorted_paginated

		// 3.1] Из shop.left.items
		self.m.s7.shop.left.items.remove(function(item){
			return item == data;
		});

		// 3.2] Из shop.left.items_filtered_sorted_paginated
		self.m.s7.shop.left.items_filtered_sorted_paginated.remove(function(item){
			return item == data;
		});

	// 4] Если все скины на странице выбраны, переключить на предыдущую
	if(!self.m.s7.shop.left.items_filtered_sorted_paginated().length)
		self.f.s7.pagi_prev();

	// n] Обновить perfect scrollbar
	Ps.update(document.getElementsByClassName('shop-choosen-items-cont')[0]);

};

//------------------------------//
// s7.8. Убрать вещь из корзины //
//------------------------------//
f.s7.remove_from_cart = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	if(self.m.s0.ajax_counter()) return;

	// 2] Добавить data в shop.left.items
	// - Но только если data ещё нет там.
	if(self.m.s7.shop.left.items().indexOf(data) == -1)
		self.m.s7.shop.left.items.push(data);

	// 3] Убрать data из shop.choosen.items
	self.m.s7.shop.choosen.items.remove(function(item){
		return item == data;
	});

	// n] Обновить perfect scrollbar
	Ps.update(document.getElementsByClassName('shop-choosen-items-cont')[0]);

};

//------------------------------//
// s7.9. Купить выбранные скины //
//------------------------------//
f.s7.buy = function(data, event) {

	// 1] Завершить, если (или):
	// - Интерфейс заблокирован.
	// - Это анонимный пользователь.
	if(self.m.s0.ajax_counter() || !layoutmodel.m.s0.is_logged_in()) return;

	// 2] Учесть лимиты и ограничения
	// - В случае нарушения лимитов, сообщить и завершить

		// 2.1] Если для покупки не выбрана ни одна вещь
		if(self.m.s7.shop.choosen.items().length <= 0) {
			toastr.info("Выберите хотя бы 1-ну вещь в магазине слева, кликнув/тапнув по ней.", "Нельзя купить ничто");
			return;
		}

		// 2.2] Если для покупки не хватает монет
		if(self.m.s7.shop.choosen.items_value_in_coins() > layoutmodel.m.s0.balance()) {
			toastr.info("Для покупки выбранных вещей не хватает монет.", "Не хватает монет");
			return;
		}

	// 3] Сформировать параметры ставки

		// 3.1] Сформировать items2buy
		var items2buy = (function(){

			// Получить все выбранные вещи
			var items = ko.mapping.toJS(self.m.s7.shop.choosen.items);

			// Отфильтровать из items все, у которых assetid начинается c 'o'
			items = items.filter(function(item, index){
				if(item.assetid.match(/^o/i)) return false;
				return true;
			});

			// Вернуть результат
			return items;

		})();

		// 3.2] Сформировать items2order
		var items2order = (function(){

			// Получить все выбранные вещи
			var items = ko.mapping.toJS(self.m.s7.shop.choosen.items);

			// Отфильтровать из items все, у которых assetid не начинается c 'o'
			items = items.filter(function(item, index){
				if(item.assetid.match(/^o/i)) return true;
				return false;
			});

			// Вернуть результат
			return items;

		})();

	// 4] Отправить на сервер ajax-запрос
	// - По результатам которого бот должен сделать игроку торговое предложение.
	ajaxko(self, {
		key: 	    		"D10009:9",
		from: 		    "f.s7.buy",
		data: 		    {
			items2buy: 				items2buy,
			items2order: 			items2order
		},
		ajax_params: {

		},
		prejob:       function(config, data, event){

			// 1] Показать тост
			toastr.info("Пожалуйста, подождите...", "Принимаем ваш заказ");

			// 2] Очистить корзину
			for(var i=0; i<self.m.s7.shop.choosen.items().length; i++) {
				self.f.s7.remove_from_cart(self.m.s7.shop.choosen.items()[i]);
			}

		},
		postjob:      function(data, params){


		},
		ok_0:         function(data, params){

			// 1] Показать тост
			toastr.info("Мы проверим заказ, и если всё в порядке, отправим вам офферы с заказанными вещами.", "Ваш заказ принят в обработку");

			// 2] Очистить корзину
			for(var i=0; i<self.m.s7.shop.choosen.items().length; i++) {
				self.f.s7.remove_from_cart(self.m.s7.shop.choosen.items()[i]);
			}

		},
		ok_2:         function(data, params){

			// 1] Если заказываемые вещи отсутствуют в UAC
			if(data.data.errormsg == "1")
				toastr.error("К сожалению, некоторых из заказанных вещей нет в наличии.", "Ошибка при обработке заказа");

			// 2] Если заказываемые вещи уже кем-то зарезервированы
			else if(data.data.errormsg == "2")
				toastr.error("К сожалению, некоторые из заказанных вещей уже зарезервированы другим покупателем.", "Ошибка при обработке заказа");

			// 3] Если отсутствует информация о некоторых заказываемых вещах
			else if(data.data.errormsg == "3")
				toastr.error("К сожалению, у нас отсутствует информация о некоторых из заказанных вещей.", "Ошибка при обработке заказа");

			// 4] Если не хватает средств на балансе
			else if(data.data.errormsg == "4")
				toastr.error("К сожалению, у вас недостаточно монет для покупки выбранных вещей.", "Ошибка при обработке заказа");

			// 5] Если пользователь не сделал ещё ни 1-й ставки в Classic Game
			else if(data.data.errormsg == "5")
				toastr.error("Что бы делать покупки в магазине, необходимо сделать 1 ставку в Main комнате.", "Ошибка при обработке заказа");

			// n] Если это обычная ошибка
			else {
				toastr.error(data.data.errormsg, "Ошибка при обработке заказа");
			}

		}
	});




};

//------------------------------------------------------------------------------//
// s7.10. Обрабатывать входящие через websocket пуш-изменения товарных остатков //
//------------------------------------------------------------------------------//
f.s7.update_goods_add_subtract = function(data) {

	// 1] Добавить в m.s7.goods все товары из data.add
	for(var i=0; i<data.add.length; i++) {

		// 1] Подготовить объект к добавлению
		var obj = ko.mapping.fromJS(data.add[i]);

		// 2] Проверить, нет ли уже i-го товара в goods
		var is_i_already_in = (function(){

			// 2.1] Получить список assetid, которые уже в goods
			var assetids = (function(){
				var results = [];
				for(var j=0; j<self.m.s7.goods().length; j++) {
					results.push(self.m.s7.goods()[j].assetid());
				}
				return results;
			})();

			// 2.2] Проверить, есть ли уже assetid i-го товара в assetids
			return (assetids.indexOf(data.add[i].assetid) != -1);

		})();

		// 2] Добавить его в m.s7.goods
		if(is_i_already_in == false)
			self.m.s7.goods.push(obj);

		// 3] Добавить его в s7.shop.left
		if(is_i_already_in == false)
			self.m.s7.shop.left.items.push(obj);

	}

	// 2] Убрать из m.s7.goods все товары из data.subtract
	(function(){

		// 2.1] Убрать из m.s7.goods
		self.m.s7.goods.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid())
					is_item_in_subtract = true;
			}
			return is_item_in_subtract;

		});

		// 2.2] Убрать из s7.shop.left.items
		self.m.s7.shop.left.items.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid())
					is_item_in_subtract = true;
			}
			return is_item_in_subtract;

		});

		// 2.3] Убрать из s7.shop.choosen.items
		self.m.s7.shop.choosen.items.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid()) {
					toastr.warning("К сожалению, '"+item.market_name()+"' уже был куплен другим пользователем. Поэтому, мы удаляем его из Вашей корзины.");
					is_item_in_subtract = true;
				}
			}
			return is_item_in_subtract;

		});

	})();


};

//----------------------------------------------------------//
// s7.11. Уведомление пользователя о создании нового оффера //
//----------------------------------------------------------//
f.s7.offer_created = function(data) {

	// 1] Оффер/Оффера/Офферов
	var offers = (function(){

		var declension = declension_by_number(data.purchase_trades_num);
		if(declension == 1) return "оффер";
		else if(declension == 2) return "оффера";
		else if(declension == 3) return "офферов";
		return "офферов";

	})();

	// 2] Подготовить html для тоста
	var html = (function(){

		// 2.1] Подготовить результат
		var result =

			// Сколько офферов должно быть отправлено для выполнения заказа
			"<p>Вам успешно отправлен оффер в Steam с заказанными вещами.</p>" +

			// Номер заказа
			"<p>Номер заказа: "+data.id_purchase+"<br>" +

			// ID оффера в steam
			"ID оффера в steam: "+data.tradeofferid+"<br>" +

			// Внутренний ID оффера
			"Внутренний ID оффера: "+data.id_trade+"<br>" +

			// Офферов отправлено
			"Всего должно быть офферов: "+data.purchase_trades_num+"</p>" +

			// Просьба подтвердить
			"<p>Для завершения операции, <a style='color: #8ab4f8; ' target='_blank' href='https://steamcommunity.com/tradeoffer/"+data.tradeofferid+"'>подтвердите оффер в Steam</a>. " +

			// Просьба сверить код безопасности
			"Обязательно проверьте код безопасности: "+data.safecode+".</p>"

		;

		// 2.2] Вернуть результат
		return result;

	})();

	// n] Показать тост
	toastr.warning(html, "Покупка вещей в магазине", {
		timeOut: 					"300000",
		extendedTimeOut: 	"300000"
	});


};

//----------------------------------------------------------------------------------------------------//
// s7.12. Уведомление пользователя о том, что недостаточно монет для оплаты некоторых вещей из заказа //
//----------------------------------------------------------------------------------------------------//
f.s7.offer_not_enough_money = function(data) {

	// Показать простое сообщение
	toastr.info("Для покупки выбранных вещей не хватает монет. А именно: <br><br>"+data.items, "Не хватает монет");

};

//----------------------------------------------------------------------------------------------//
// s7.13. Уведомление пользователя о том, что некоторые вещи из его покупки уже зарезервированы //
//----------------------------------------------------------------------------------------------//
f.s7.offer_reserved = function(data) {

	// Показать простое сообщение
	toastr.info("К сожалению, некоторые из выбранных для покупки вещей уже зарезервированы. А именно: <br><br>"+data.items, "Некоторые вещи уже зарезервированы");

};

//---------------------------------------------------------------------------------------------------------//
// s7.14. Уведомление пользователя о том, что некоторые вещи из его покупки (на заказ) уже зарезервированы //
//---------------------------------------------------------------------------------------------------------//
f.s7.items2order_reserved = function(data) {

	// Показать простое сообщение
	toastr.info("К сожалению, некоторые из выбранных для покупки вещей уже зарезервированы, или на них не хватило монет. Они не войдут в заказ. А именно: <br><br>"+data.reserved_items, "Не вошедшие в заказ вещи");

};

//------------------------------------------------------------------------------------------------------//
// s7.15. Уведомление пользователя о том, что заказанные им скины на заказ он получит в течение N часов //
//------------------------------------------------------------------------------------------------------//
f.s7.items2order_success = function(data) {

	// Показать простое сообщение
	toastr.warning("Указанные ниже скины будут отправлены Вам в Steam отдельно, в течение 24 часов: <br><br>"+data.reserved_items, "Отдельно от основного заказа", {
		timeOut: 					"300000",
		extendedTimeOut: 	"300000"
	});

};

//---------------------------------------------------------------------------------------------------//
// s7.16. Обрабатывать входящие через websocket пуш-изменения товарных остатков на заказываемые вещи //
//---------------------------------------------------------------------------------------------------//
f.s7.update_goods_order_add_sub = function(data) {

	// 1] Добавить в m.s7.goods все товары из data.add
	for(var i=0; i<data.add.length; i++) {

		// 1] Подготовить объект к добавлению
		var obj = ko.mapping.fromJS(data.add[i]);

		// 2] Проверить, нет ли уже i-го товара в goods
		var is_i_already_in = (function(){

			// 2.1] Получить список assetid, которые уже в goods
			var assetids = (function(){
				var results = [];
				for(var j=0; j<self.m.s7.goods().length; j++) {
					results.push(self.m.s7.goods()[j].assetid());
				}
				return results;
			})();

			// 2.2] Проверить, есть ли уже assetid i-го товара в assetids
			return (assetids.indexOf(data.add[i].assetid) != -1);

		})();

		// 3] Добавить его в m.s7.goods
		if(is_i_already_in == false)
			self.m.s7.goods.push(obj);

		// 4] Добавить его в s7.shop.left, если его уже там нет
		if(is_i_already_in == false)
			self.m.s7.shop.left.items.push(obj);

	}

	// 2] Убрать из m.s7.goods все товары из data.subtract
	(function(){

		// 2.1] Убрать из m.s7.goods
		self.m.s7.goods.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid())
					is_item_in_subtract = true;
			}
			return is_item_in_subtract;

		});

		// 2.2] Убрать из s7.shop.left.items
		self.m.s7.shop.left.items.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid())
					is_item_in_subtract = true;
			}
			return is_item_in_subtract;

		});

		// 2.3] Убрать из s7.shop.choosen.items
		self.m.s7.shop.choosen.items.remove(function(item) {

			var is_item_in_subtract = false;
			for(var i=0; i<data.subtract.length; i++) {
				if(data.subtract[i]['assetid'] == item.assetid()) {
					toastr.warning("К сожалению, '"+item.market_name()+"' уже был куплен другим пользователем. Поэтому, мы удаляем его из Вашей корзины.");
					is_item_in_subtract = true;
				}
			}
			return is_item_in_subtract;

		});

	})();


};


return this; }};




























