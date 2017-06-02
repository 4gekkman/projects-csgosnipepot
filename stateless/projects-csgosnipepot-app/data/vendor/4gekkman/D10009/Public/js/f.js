/*//========================================////
////																			  ////
////   f.js - функционал модели документа   ////
////																			  ////
////========================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 *  s0. Функционал, доступный всему остальному функционалу
 *
 *    f.s0.txt_delay_save						| s0.1. Функционал "механизма отложенного сохранения для текстовых полей"
 *    f.s0.tooltipster_init         | s0.2. Переинициализирует tooltipster
 *    f.s0.testfunc                 | s0.3. Функция для тестов
 *
 *  s1. Функционал игры Jackpot
 *  s2. Функционал зоны уведомлений
 *
 * 		f.s2.save_steam_tradeurl 			| s2.1. Сохранить Steam Trade URL
 *
 *  s3. Функционал профиля пользователя
 *  s4. Функционал ТОПа игроков
 *  s5. Функционал FAQ
 *  s6. Функционал интерфейса пополнения баланса скинами
 *  s7. Функционал магазина скинов
 *  s8. Функционал Free Coins
 *
 *
 */

//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctions = { constructor: function(self) { var f = this;


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s0. Функционал, доступный всему остальному функционалу 			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s0 = {};

		//-------------------------------------------------------------------------//
		// s0.1. Функционал "механизма отложенного сохранения для текстовых полей" //
		//-------------------------------------------------------------------------//
		f.s0.txt_delay_save = {};

			//----------------------------------------------------------------------//
			// 1] Применить "механизм отложенного сохранения для текстовых полей"   //
			//----------------------------------------------------------------------//
			// - Он особенно актуален для текстовых полей.
			// - Делает так, что функция сохранения срабатывает не при каждом нажатии.
			// - А лишь спустя заданные N секунд после последнего изменения.
			f.s0.txt_delay_save.use = function(savefunc){

				// 2.1. Остановить ранее запланированный setTimeout
				if(self.m.s0.txt_delay_save.settimeoutid())
					clearTimeout(self.m.s0.txt_delay_save.settimeoutid());

				// 2.2] Если время для сохранения не пришло
				if(+Date.now() - +self.m.s0.txt_delay_save.lastupdate() < +self.m.s0.txt_delay_save.gap) {

					// Поставить выполнение на таймер
					var timerId = setTimeout(savefunc, self.m.s0.txt_delay_save.gap);

					// Сохранить timerId в модель
					self.m.s0.txt_delay_save.settimeoutid(timerId);

					// Сохранить текущий timestamp в модель
					self.m.s0.txt_delay_save.lastupdate(Date.now());

					// Указать, что имееются не сохранённые данные
					self.m.s0.txt_delay_save.is_unsaved_data(1);

					// Завершить
					return 1;

				}

				// 2.3] Если время для сохранения пришло
				else {

					// Сохранить текущий timestamp в модель
					self.m.s0.txt_delay_save.lastupdate(Date.now());

				}

			};

			//-------------------------------------//
			// 2] Заблокировать закрытие документа //
			//-------------------------------------//
			// - Иными словами указать, что есть несохранённые данные.
			// - Попытка закрыть страницу в итоге приведёт к вызову модального confirm.
			f.s0.txt_delay_save.block = function(){
				self.m.s0.txt_delay_save.is_unsaved_data(1);
			};

			//--------------------------------------//
			// 3] Разблокировать закрытие документа //
			//--------------------------------------//
			// - Иными словами указать, что нет несохранённых данных.
			// - Попытка закрыть страницу в итоге уже не приведёт к вызову модального confirm.
			f.s0.txt_delay_save.unblock = function(){
				self.m.s0.txt_delay_save.is_unsaved_data(0);
			};


		//--------------------------------------//
		// s0.2. Переинициализирует tooltipster //
		//--------------------------------------//
		f.s0.tooltipster_init = function(data, event) {

			// 1] Подготовить конфигурационный объект
			var config = {
				theme: 'tooltipster-borderless',
				side: 'top',
				trigger: 'custom',
				debug: false,
				triggerOpen: {
					mouseenter: true,
					tap: true
				},
				triggerClose: {
					click: true,
					tap: true,
					scroll: true,
					mouseleave: true
				}
			};

			// 2] Подключить tooltipster
			$('.tooltipstered').tooltipster(config);

		};


		//--------------------------//
		// s0.3. Функция для тестов //
		//--------------------------//
		f.s0.testfunc = function(data, event) {

			ajaxko(self, {

				command: 	"\\M8\\Commands\\C25_new_trade_offer",
				from: 		"ajaxko",
				data: 		{

				},
				prejob: 	function(config, data, event){},
				postjob: 	function(data, params){},
				ok_0: 		function(data, params){
					console.log('Успех');
				},
				ok_1: 		function(data, params){
					console.log('Нет доступа');
				},
				ok_2: 		function(data, params){
					console.log(data.data.errormsg);
				}

			});

		};



	//----------------------------------------//
	// 			        		 			                //
	// 			 s1. Функционал игры Jackpot			//
	// 			         					                //
	//----------------------------------------//
	f.s1 = Object.create(ModelFunctionsJackpot).constructor(self, f);


	//--------------------------------------------//
	// 			        		 			                    //
	// 			 s2. Функционал зоны уведомлений			//
	// 			         					                    //
	//--------------------------------------------//
	f.s2 = {};

		//---------------------------------//
		// s2.1. Сохранить Steam Trade URL //
		//---------------------------------//
		f.s2.save_steam_tradeurl = function(data, event){

			// 1] Если поле со Steam URL пусто, сообщит и завершить
			if(!self.m.s2.notif_tradeurl.tradeurl()) {
				toastr.info("Сначала введите свой торговый URL в Steam. Потом нажмите кнопку 'Сохранить'.", "Торговый URL пуст");
				return;
			}

			// 2] Отправить запрос
			ajaxko(self, {
				key: 	    		"D10009:1",
				from: 		    "ajaxko",
				data: 		    {
					steam_tradeurl: self.m.s2.notif_tradeurl.tradeurl()
				},
				prejob:       function(config, data, event){

					// 1] Сообщить, что идёт сохранение нового trade url
					toastr.info("Произвожу проверку введённого торгового URL в Steam...", "Идёт проверка");

				},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, торговый URL успешно сохранён
					toastr.success("Ваш торговый URL принят и сохранён.", "Успех");

					// 2] Записать новый торговый URL в self.m.s2.notif_tradeurl.tradeurl_server
					self.m.s2.notif_tradeurl.tradeurl_server(self.m.s2.notif_tradeurl.tradeurl);

				},
				ok_2:         function(data, params){

					// 1] Сообщить, что ведён неверный Steam URL
					toastr.error("Мы проверили введённый вами торговый URL. И оказалось, что он неправильный. Перепроверьте его, и попробуйте ещё раз.", "Неверный торговый URL", {
						timeOut: 					"15000",
						extendedTimeOut: 	"15000"
					});

				}
			});

		};


	//------------------------------------------------//
	// 			        		 			                        //
	// 			 s3. Функционал профиля пользователя			//
	// 			         					                        //
	//------------------------------------------------//
	// - См. D10009/Public/js/profile/f.js
	f.s3 = Object.create(ModelFunctionsProfile).constructor(self, f);


	//----------------------------------------//
	// 			        		 			                //
	// 			 s4. Функционал ТОПа игроков			//
	// 			         					                //
	//----------------------------------------//
	// - См. D10009/Public/js/top/f.js
	f.s4 = Object.create(ModelFunctionsTop).constructor(self, f);


	//------------------------------//
	// 			        		 			      //
	// 			 s5. Функционал FAQ			//
	// 			         					      //
	//------------------------------//
	// - См. D10009/Public/js/faq/f.js
	f.s5 = Object.create(ModelFunctionsFaq).constructor(self, f);


	//----------------------------------------------------------------//
	// 			        		 			                                        //
	// 			 s6. Функционал интерфейса пополнения баланса скинами			//
	// 			         					                                        //
	//----------------------------------------------------------------//
	// - См. D10009/Public/js/deposit/f.js
	f.s6 = Object.create(ModelFunctionsDeposit).constructor(self, f);


	//------------------------------------------//
	// 			        		 			                  //
	// 			 s7. Функционал магазина скинов			//
	// 			         					                  //
	//------------------------------------------//
	// - См. D10009/Public/js/shop/f.js
	f.s7 = Object.create(ModelFunctionsShop).constructor(self, f);


	//--------------------------------------//
	// 			        		 			              //
	// 			 s8. Функционал Free Coins			//
	// 			         					              //
	//--------------------------------------//
	// - См. D10009/Public/js/freecoins/f.js
	f.s8 = Object.create(ModelFunctionsFc).constructor(self, f);




return f; }};



























