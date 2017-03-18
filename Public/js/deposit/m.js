/*//============================================================================================================////
////																		                                                                        ////
////   Модель интерфейса пополнения баланса скинами, предназначенная для подключения в основной m.js	документа	////
////																				                                                                    ////
////============================================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *
 * 	s6. Модель интерфейса пополнения баланса скинами
 *
 * 		s6.1. Модель инвентаря пользователя
 * 	  s6.2. Виден ли модальный щит загрузки инвентаря
 * 	  s6.3. Модель сортировки
 * 	  	s6.3.1. Критерии сортировки инвентаря
 * 	  	s6.3.2. Направления сортировки инвентаря
 *    s6.4. Модель выбора вещей из инвентаря для депозита
 *      s6.4.1. Модель остающихся в инвентаре вещей
 *      s6.4.2. Модель выбранных для депозита вещей
 *
 *    s6.n. Индексы и вычисляемые значения
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
var ModelDeposit = { constructor: function(self, m) { m.s6 = this;

	//-------------------------------------//
	// s6.1. Модель инвентаря пользователя //
	//-------------------------------------//
	self.m.s6.inventory = {};

 		// 1] Инвентарь пользователя
    self.m.s6.inventory.items = ko.observableArray([]);

	//-------------------------------------------------//
	// s6.2. Виден ли модальный щит загрузки инвентаря //
	//-------------------------------------------------//
	self.m.s6.is_inv_loading_shield_visible = ko.observable(false);

	//-------------------------//
	// s6.3. Модель сортировки //
	//-------------------------//
  self.m.s6.sort = {};

		//---------------------------------------//
		// s6.3.1. Критерии сортировки инвентаря //
		//---------------------------------------//
		self.m.s6.sort.criterias = {};

			// 1] Критерии сортировки инвентаря
			self.m.s6.sort.criterias.list = ko.mapping.fromJS([

				// По цене
				{
					name: 'byprice',
					text: 'Цене'
				},

				// По названию
				{
					name: 'byname',
					text: 'Названию'
				}

			]);

			// 2] Выбранный критерий сортировки
			self.m.s6.sort.criterias.choosen = ko.observable(self.m.s6.sort.criterias.list()[0]);

		//------------------------------------------//
		// s6.3.2. Направления сортировки инвентаря //
		//------------------------------------------//
		self.m.s6.sort.directions = {};

			// 1] Направления сортировки инвентаря
			self.m.s6.sort.directions.list = ko.mapping.fromJS([

				// По возрастанию
				{
					name: 'asc',
					text: 'По возрастанию'
				},

				// По убыванию
				{
					name: 'desc',
					text: 'По убыванию'
				}

			]);

			// 2] Выбранный критерий сортировки
			self.m.s6.sort.directions.choosen = ko.observable(self.m.s6.sort.directions.list()[1]);

	//-----------------------------------------------------//
	// s6.4. Модель выбора вещей из инвентаря для депозита //
	//-----------------------------------------------------//
  self.m.s6.deposit = {};

		//---------------------------------------------//
		// s6.4.1. Модель остающихся в инвентаре вещей //
		//---------------------------------------------//
		self.m.s6.deposit.left = {};

			// 1] Не фильтрованные/сортированные вещи
			self.m.s6.deposit.left.items = ko.observableArray([]);

			// 2] Отфильтрованные/Отсортированные/Пагинированные вещи
			self.m.s6.deposit.left.items_filtered_sorted_paginated = ko.observableArray([]);

			// 3] Вещи с текущей страницы
			self.m.s6.deposit.left.items_at_curpage = ko.observableArray([]);

			// 4] Значение поисковой строки
			self.m.s6.deposit.left.searchinput = ko.observable('');

			// 5] Модель клиентской пагинации
			self.m.s6.deposit.pagi = {};

				// 5.1] Номер текущей страницы клиентской пагинации
				self.m.s6.deposit.pagi.pagenum = ko.observable(1);

				// 5.2] Всего пагинационных страниц
				self.m.s6.deposit.pagi.count = ko.observable(1);

				// 5.3] MAX кол-во вещей на 1-й странице
				self.m.s6.deposit.pagi.max_items_per_page = ko.observable(20);

		//---------------------------------------------//
		// s6.4.2. Модель выбранных для депозита вещей //
		//---------------------------------------------//
		self.m.s6.deposit.choosen = {};

			// 1] Выбранные для депозита вещи
			self.m.s6.deposit.choosen.items = ko.observableArray([]);

			// 2] Сколько монет мы дадим за все выбранные вещи (1 монета == 1 цент)
			self.m.s6.deposit.choosen.items_value_in_coins = ko.observable(0);


	//--------------------------------------//
	// s6.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	/**
	 	*
	 	* s6.n.1. Общие вычисления
	 	* s6.n.2. Индексы и вычисляемые модели выбора вещей из инвентаря для депозита
	 	*
	 	*/
	self.m.s6.indexes = {};

		//--------------------------//
		// s6.n.1. Общие вычисления //
		//--------------------------//
		ko.computed(function(){

			// 1] Рассчитать полное количество пагинационных страниц
			(function(){

				//self.m.s6.deposit.pagi.count()

			})();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		//-----------------------------------------------------------------------------//
		// s6.n.2. Индексы и вычисляемые модели выбора вещей из инвентаря для депозита //
		//-----------------------------------------------------------------------------//

			// 1] Рассчитать не фильтрованные/сортированные вещи
			// - m.s6.deposit.left.items
			ko.computed(function(){



			}).extend({deferred: true}); //.extend({rateLimit: 10, method: "notifyWhenChangesStop"});

			// 2] Рассчитать отфильтрованные/Отсортированные/Пагинированные вещи
			// - m.s6.deposit.left.items_filtered_sorted_paginated
			ko.computed(function(){

				// 2.1] Наполнить items_filtered_sorted_paginated, отфильтровав по market_hash_name
				(function(){

					//self.m.s6.deposit.left.items_filtered_sorted_paginated.removeAll();
					//self.m.s6.deposit.left.items_filtered_sorted_paginated(self.m.s6.deposit.left.items());

					// 2.1.1] Убрать из items_filtered_sorted_paginated все вещи, которых нет в deposit.left.items
					// - И которые не проходят фильтр по имени.
					self.m.s6.deposit.left.items_filtered_sorted_paginated.remove(function(item){
						if(self.m.s6.deposit.left.items().indexOf(item) == -1 || (self.m.s6.deposit.left.searchinput() && !item.market_hash_name().match((new RegExp('^'+self.m.s6.deposit.left.searchinput(), 'i')))))
							return true;
						return false;
					});

					// 2.1.2] Добавить в items_filtered_sorted_paginated все вещи, которые есть в deposit.left.items, но ещё нет в items_filtered_sorted_paginated
					// - И которые проходят фильтр по имени.
					for(var i=0; i<self.m.s6.deposit.left.items().length; i++) {

						// Если searchinput не пуста, отфильтровать
						if(self.m.s6.deposit.left.searchinput()) {
							if(!self.m.s6.deposit.left.items()[i].market_hash_name().match((new RegExp('^'+self.m.s6.deposit.left.searchinput(), 'i'))))
								continue;
						}

						// Добавить прошедшую фильтр вещь
						// - Если её ещё нет в items_filtered_sorted_paginated.
						if(self.m.s6.deposit.left.items_filtered_sorted_paginated().indexOf(self.m.s6.deposit.left.items()[i]) == -1)
							self.m.s6.deposit.left.items_filtered_sorted_paginated.push(self.m.s6.deposit.left.items()[i]);

					}

				})();

				// 2.2] Отфильтровать из items_filtered_sorted_paginated те типы вещей, которые мы не принимаем в качестве депозита
				(function(){

					// Получить объект фильтров по типам вещей и их значений
					var item_type_filters = server.data.deposit_configs.item_type_filters;

					// Профести фильтрацию
					self.m.s6.deposit.left.items_filtered_sorted_paginated(
						self.m.s6.deposit.left.items_filtered_sorted_paginated().filter(function(value){

							// 1] Является ли value запрещённым для депозита
							var is_forbidden = (function(){
								for(var key in value.itemtypes) { if(!value.itemtypes.hasOwnProperty(key)) continue;
									for(var key2 in item_type_filters) { if(!item_type_filters.hasOwnProperty(key2)) continue;

										if(value.itemtypes[key]() == true && key == key2 && item_type_filters[key2] == false)
											return true;

									}
								}
								return false;
							})();

							// 2] Если является, вернуть false, иначе true
							return !is_forbidden;

						})
					);

				})();

				// 2.3] Отфильтровать из items_filtered_sorted_paginated вещи, стоимость которых меньше server.data.deposit_configs.min_skin2accept_price_cents
				(function(){

					// Получить объект фильтров по типам вещей и их значений
					var item_type_filters = server.data.deposit_configs.item_type_filters;

					// Профести фильтрацию
					self.m.s6.deposit.left.items_filtered_sorted_paginated(
						self.m.s6.deposit.left.items_filtered_sorted_paginated().filter(function(value){

							// Отфильтровать
							return !(Math.round(value.price()*100) < server.data.deposit_configs.min_skin2accept_price_cents);

						})
					);

				})();

				// 2.4] Посчитать общее кол-во пагинационных страниц
				self.m.s6.deposit.pagi.count((function(){

					// 2.4.1] Получить в короткие переменные необходимые данные

						// 1) Кол-во подходящих для депозита (видимых в интерфейсе) скинов в инвентаре
						var skins_count = self.m.s6.deposit.left.items_filtered_sorted_paginated().length;

						// 2) MAX кол-во скинов на 1 странице
						var per_page = self.m.s6.deposit.pagi.max_items_per_page();

					// 2.4.2] Получить их частное
					var coef = skins_count/per_page;

					// 2.4.3] Если coef <= 1, вернуть 1
					if(coef <= 1) return 1;

					// 2.4.4] Иначе, вернуть округлённый coef + 1
					else return Math.round(coef) + 1;

				})());

				// 2.5] Отсортировать items_filtered_sorted_paginated
				// - В соотв.с выбранным типом и направлением сортировки.
				(function(){

					// 2.5.1] Если выбрана сортировка по цене
					// - Отсортировать вещи по цене.
					// - А вещи одной цены, по имени.
					if(self.m.s6.sort.criterias.choosen().name() == 'byprice') {
						self.m.s6.deposit.left.items_filtered_sorted_paginated.sort(function(a,b){

							// По цене

								// По убыванию
								if(self.m.s6.sort.directions.choosen().name() == 'desc') {
									if(+a.price()*100 < +b.price()*100) return 1;
									else if(+a.price()*100 > +b.price()*100) return -1;
								}

								// По возрастанию
								if(self.m.s6.sort.directions.choosen().name() == 'asc') {
									if(+a.price()*100 < +b.price()*100) return -1;
									else if(+a.price()*100 > +b.price()*100) return 1;
								}

							// По имени
							else {

								if(a.market_hash_name() < b.market_hash_name()) return -1;
								if(a.market_hash_name() > b.market_hash_name()) return 1;
								return 0;

							}

						});
					}

					// 2.5.2] Если выбрана сортировка по имени
					else if(self.m.s6.sort.criterias.choosen().name() == 'byname') {
						self.m.s6.deposit.left.items_filtered_sorted_paginated.sort(function(a,b){

							// По убыванию
							if(self.m.s6.sort.directions.choosen().name() == 'desc') {
								if(a.market_hash_name() < b.market_hash_name()) return 1;
								if(a.market_hash_name() > b.market_hash_name()) return -1;
							}

							// По возрастанию
							if(self.m.s6.sort.directions.choosen().name() == 'asc') {
								if(a.market_hash_name() < b.market_hash_name()) return -1;
								if(a.market_hash_name() > b.market_hash_name()) return 1;
							}

							// Не сортировать
							return 0;

						});
					}

				})();

				// 2.6] Применить пагинацию, и оставить только скины с выбранной страницы
				// - То есть отфильтровать из items_filtered_sorted_paginated все
				//   скины, индексы которых не соответствуют выбранной странице.
				(function(){

					// 2.6.1] Получить необходимые данные в короткие переменные

							// 1) Номер текущей страницы
							var num = self.m.s6.deposit.pagi.pagenum();

							// 2) MAX кол-во вещей на 1 странице
							var per_page = self.m.s6.deposit.pagi.max_items_per_page();

							// 3) Текущий номер страницы
							var pagenum = self.m.s6.deposit.pagi.pagenum();

					// 2.6.2] Получить диапазон индексов, которые можно показывать на текущей странице
					// - От min до max включительно.

						// min
						var min = (function(){
							var result = 0 + (num-1)*per_page;
							return result > 0 ? result : 0;
						})();

						// max
						var max = (function(){
							return 0 + num*per_page - 1;
						})();

					// 2.6.3] Провести фильтрацию
					self.m.s6.deposit.left.items_filtered_sorted_paginated(
						self.m.s6.deposit.left.items_filtered_sorted_paginated().filter(function(value, index){

							// Если index в диапазоне между min и max включительно, вернуть true
							if(index >= min && index <= max)
								return true;

						})
					);

				})();

				// 2.7] Подсчитать, сколько монет мы дадим за все выбранные вещи (1 монета == 1 цент)
				self.m.s6.deposit.choosen.items_value_in_coins((function(){

					// 2.7.1] Подготовить переменную для результата
					var result = 0;

					// 2.7.2] Подсчитать результат
					for(var i=0; i<self.m.s6.deposit.choosen.items().length; i++) {

						result = +result + Math.round(self.m.s6.deposit.choosen.items()[i].price()*100*((100 - server.data.deposit_configs.skin_price2accept_spread_in_perc)/100));

					}

					// 2.7.n] Вернуть результат
					return result;

				})());

				// 2.8] Записать содержимое items_filtered_sorted_paginated в items_at_curpage
				self.m.s6.deposit.left.items_at_curpage(self.m.s6.deposit.left.items_filtered_sorted_paginated());

			}).extend({deferred: true});  //extend({rateLimit: 100, method: "notifyWhenChangesStop"});



	//------------------------------//
	// 			        		 	          //
	// 	X. Подготовка к завершению  //
	// 			         			          //
	//------------------------------//

	//------------------------------------------//
	// X1. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self.m.s6;


}};	// конец модели









