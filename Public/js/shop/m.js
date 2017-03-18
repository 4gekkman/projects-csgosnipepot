/*//======================================================================================////
////																		                                                  ////
////   Модель магазина скинов, предназначенная для подключения в основной m.js	документа	////
////																				                                              ////
////======================================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *
 * 	s7. Модель магазина скинов
 *
 * 		s7.1. Модель товаров магазина
 * 	  s7.2. Виден ли модальный щит загрузки товаров магазина
 * 	  s7.3. Модель сортировки
 * 	  s7.4. Модель корзины
 *    s7.n. Индексы и вычисляемые значения
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
var ModelShop = { constructor: function(self, m) { m.s7 = this;

	//-------------------------------//
	// s7.1. Модель товаров магазина //
	//-------------------------------//
  self.m.s7.goods = ko.observableArray([]);

	//--------------------------------------------------------//
	// s7.2. Виден ли модальный щит загрузки товаров магазина //
	//--------------------------------------------------------//
	self.m.s7.is_goods_loading_shield_visible = ko.observable(false);

	//-------------------------//
	// s7.3. Модель сортировки //
	//-------------------------//
  self.m.s7.sort = {};

		//-------------------------------------//
		// s7.3.1. Критерии сортировки товаров //
		//-------------------------------------//
		self.m.s7.sort.criterias = {};

			// 1] Критерии сортировки товаров
			self.m.s7.sort.criterias.list = ko.mapping.fromJS([

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
			self.m.s7.sort.criterias.choosen = ko.observable(self.m.s7.sort.criterias.list()[0]);

		//----------------------------------------//
		// s7.3.2. Направления сортировки товаров //
		//----------------------------------------//
		self.m.s7.sort.directions = {};

			// 1] Направления сортировки инвентаря
			self.m.s7.sort.directions.list = ko.mapping.fromJS([

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
			self.m.s7.sort.directions.choosen = ko.observable(self.m.s7.sort.directions.list()[1]);

	//-----------------------//
	// s7.4. Модель магазина //
	//-----------------------//
  self.m.s7.shop = {};

		//-------------------------------------------------------//
		// s7.4.1. Модель остающихся "на полке" товаров магазина //
		//-------------------------------------------------------//
		self.m.s7.shop.left = {};

			// 1] Не фильтрованные/сортированные вещи
			self.m.s7.shop.left.items = ko.observableArray([]);

			// 2] Отфильтрованные/Отсортированные/Пагинированные вещи
			self.m.s7.shop.left.items_filtered_sorted_paginated = ko.observableArray([]);

			// 3] Вещи с текущей страницы
			self.m.s7.shop.left.items_at_curpage = ko.observableArray([]);

			// 4] Значение поисковой строки
			self.m.s7.shop.left.searchinput = ko.observable('');

			// 5] Модель клиентской пагинации
			self.m.s7.shop.pagi = {};

				// 5.1] Номер текущей страницы клиентской пагинации
				self.m.s7.shop.pagi.pagenum = ko.observable(1);

				// 5.2] Всего пагинационных страниц
				self.m.s7.shop.pagi.count = ko.observable(1);

				// 5.3] MAX кол-во вещей на 1-й странице
				self.m.s7.shop.pagi.max_items_per_page = ko.observable(20);

		//---------------------------------------------//
		// s7.4.2. Модель товаров в магазине в корзине //
		//---------------------------------------------//
		self.m.s7.shop.choosen = {};

			// 1] Товары в корзине
			self.m.s7.shop.choosen.items = ko.observableArray([]);

			// 2] Сколько монет стоят все выбранные вещи (1 монета == 1 цент)
			self.m.s7.shop.choosen.items_value_in_coins = ko.observable(0);


	//--------------------------------------//
	// s7.n. Индексы и вычисляемые значения //
	//--------------------------------------//
	/**
	 	*
	 	* s7.n.1. Общие вычисления
	 	* s7.n.2. Индексы и вычисляемые модели выбора вещей из инвентаря для депозита
	 	*
	 	*/
	self.m.s7.indexes = {};

		//--------------------------//
		// s7.n.1. Общие вычисления //
		//--------------------------//
		ko.computed(function(){

			// 1] Рассчитать полное количество пагинационных страниц
			(function(){

				//self.m.s7.shop.pagi.count()

			})();

		}).extend({rateLimit: 10, method: "notifyWhenChangesStop"});

		//-----------------------------------------------------------------------------//
		// s7.n.2. Индексы и вычисляемые модели выбора вещей из инвентаря для депозита //
		//-----------------------------------------------------------------------------//

			// 1] Рассчитать не фильтрованные/сортированные вещи
			// - m.s7.shop.left.items
			ko.computed(function(){



			}).extend({deferred: true}); //.extend({rateLimit: 10, method: "notifyWhenChangesStop"});

			// 2] Рассчитать отфильтрованные/Отсортированные/Пагинированные вещи
			// - m.s7.shop.left.items_filtered_sorted_paginated
			ko.computed(function(){

				// 2.1] Наполнить items_filtered_sorted_paginated, отфильтровав по market_hash_name
				(function(){

					//self.m.s7.shop.left.items_filtered_sorted_paginated.removeAll();
					//self.m.s7.shop.left.items_filtered_sorted_paginated(self.m.s7.shop.left.items());

					// 2.1.1] Убрать из items_filtered_sorted_paginated все вещи, которых нет в shop.left.items
					// - И которые не проходят фильтр по имени.
					self.m.s7.shop.left.items_filtered_sorted_paginated.remove(function(item){
						if(self.m.s7.shop.left.items().indexOf(item) == -1 || (self.m.s7.shop.left.searchinput() && !item.market_hash_name().match((new RegExp('^'+self.m.s7.shop.left.searchinput(), 'i')))))
							return true;
						return false;
					});

					// 2.1.2] Добавить в items_filtered_sorted_paginated все вещи, которые есть в shop.left.items, но ещё нет в items_filtered_sorted_paginated
					// - И которые проходят фильтр по имени.
					for(var i=0; i<self.m.s7.shop.left.items().length; i++) {

						// Если searchinput не пуста, отфильтровать
						if(self.m.s7.shop.left.searchinput()) {
							if(!self.m.s7.shop.left.items()[i].market_hash_name().match((new RegExp('^'+self.m.s7.shop.left.searchinput(), 'i'))))
								continue;
						}

						// Добавить прошедшую фильтр вещь
						// - Если её ещё нет в items_filtered_sorted_paginated.
						if(self.m.s7.shop.left.items_filtered_sorted_paginated().indexOf(self.m.s7.shop.left.items()[i]) == -1)
							self.m.s7.shop.left.items_filtered_sorted_paginated.push(self.m.s7.shop.left.items()[i]);

					}

				})();

				// 2.2] Посчитать общее кол-во пагинационных страниц
				self.m.s7.shop.pagi.count((function(){

					// 2.2.1] Получить в короткие переменные необходимые данные

						// 1) Кол-во подходящих для депозита (видимых в интерфейсе) скинов в инвентаре
						var skins_count = self.m.s7.shop.left.items_filtered_sorted_paginated().length;

						// 2) MAX кол-во скинов на 1 странице
						var per_page = self.m.s7.shop.pagi.max_items_per_page();

					// 2.2.2] Получить их частное
					var coef = skins_count/per_page;

					// 2.2.3] Если coef <= 1, вернуть 1
					if(coef <= 1) return 1;

					// 2.2.4] Иначе, вернуть округлённый coef + 1
					else return Math.round(coef) + 1;

				})());

				// 2.3] Отсортировать items_filtered_sorted_paginated
				// - В соотв.с выбранным типом и направлением сортировки.
				(function(){

					// 2.3.1] Если выбрана сортировка по цене
					// - Отсортировать вещи по цене.
					// - А вещи одной цены, по имени.
					if(self.m.s7.sort.criterias.choosen().name() == 'byprice') {
						self.m.s7.shop.left.items_filtered_sorted_paginated.sort(function(a,b){

							// По цене

								// По убыванию
								if(self.m.s7.sort.directions.choosen().name() == 'desc') {
									if(+a.price()*100 < +b.price()*100) return 1;
									else if(+a.price()*100 > +b.price()*100) return -1;
								}

								// По возрастанию
								if(self.m.s7.sort.directions.choosen().name() == 'asc') {
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

					// 2.3.2] Если выбрана сортировка по имени
					else if(self.m.s7.sort.criterias.choosen().name() == 'byname') {
						self.m.s7.shop.left.items_filtered_sorted_paginated.sort(function(a,b){

							// По убыванию
							if(self.m.s7.sort.directions.choosen().name() == 'desc') {
								if(a.market_hash_name() < b.market_hash_name()) return 1;
								if(a.market_hash_name() > b.market_hash_name()) return -1;
							}

							// По возрастанию
							if(self.m.s7.sort.directions.choosen().name() == 'asc') {
								if(a.market_hash_name() < b.market_hash_name()) return -1;
								if(a.market_hash_name() > b.market_hash_name()) return 1;
							}

							// Не сортировать
							return 0;

						});
					}

				})();

				// 2.4] Применить пагинацию, и оставить только скины с выбранной страницы
				// - То есть отфильтровать из items_filtered_sorted_paginated все
				//   скины, индексы которых не соответствуют выбранной странице.
				(function(){

					// 2.4.1] Получить необходимые данные в короткие переменные

							// 1) Номер текущей страницы
							var num = self.m.s7.shop.pagi.pagenum();

							// 2) MAX кол-во вещей на 1 странице
							var per_page = self.m.s7.shop.pagi.max_items_per_page();

							// 3) Текущий номер страницы
							var pagenum = self.m.s7.shop.pagi.pagenum();

					// 2.4.2] Получить диапазон индексов, которые можно показывать на текущей странице
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

					// 2.4.3] Провести фильтрацию
					self.m.s7.shop.left.items_filtered_sorted_paginated(
						self.m.s7.shop.left.items_filtered_sorted_paginated().filter(function(value, index){

							// Если index в диапазоне между min и max включительно, вернуть true
							if(index >= min && index <= max)
								return true;

						})
					);

				})();

				// 2.5] Подсчитать, сколько монет мы заберём за все выбранные вещи (1 монета == 1 цент)
				self.m.s7.shop.choosen.items_value_in_coins((function(){

					// 2.5.1] Подготовить переменную для результата
					var result = 0;

					// 2.5.2] Подсчитать результат
					for(var i=0; i<self.m.s7.shop.choosen.items().length; i++) {

						result = +result + Math.round(self.m.s7.shop.choosen.items()[i].price()*100);

					}

					// 2.5.n] Вернуть результат
					return result;

				})());

				// 2.6] Записать содержимое items_filtered_sorted_paginated в items_at_curpage
				self.m.s7.shop.left.items_at_curpage(self.m.s7.shop.left.items_filtered_sorted_paginated());

			}).extend({deferred: true});  //extend({rateLimit: 100, method: "notifyWhenChangesStop"});	
	

	//------------------------------//
	// 			        		 	          //
	// 	X. Подготовка к завершению  //
	// 			         			          //
	//------------------------------//

	//------------------------------------------//
	// X1. Вернуть ссылку self на объект-модель //
	//------------------------------------------//
	return self.m.s7;


}};	// конец модели









