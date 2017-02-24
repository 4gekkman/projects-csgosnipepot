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
 *
 * 	s5. Функционал FAQа
 *
 *    f.s5.get_faq      					| s5.1. Запросить данные FAQа с сервера
 *    f.s5.switch_article					| s5.2. Свернуть/Развернуть статью, добавить новое состояние в history
 *    f.s5.close_all_articles			| s5.3. Свернуть все статьи
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



	//----------------------//
	// 			        		 	  //
	// 	s5. Функционал FAQ  //
	// 			         			  //
	//----------------------//
	f.s5 = {};

		//---------------------------------------//
		// s5.1. Запросить данные FAQа с сервера //
		//---------------------------------------//
		// - Если is_initial == true
		//   • group игнорируется, и возвращаются все группы и статьи для стартовой.
		//   • При этом, если m.s5.groups не пуста, то запрос не производится, функция завершается.
		// - Если group не пуста
		//   • То is_initial игрогируется, и возвращаются только статьи для group.
		//   • При этом, если статьи для group уже есть в m.s5.articles, то запрос не производится, функция завершается.
		f.s5.get_faq = function(is_initial, group, context, event) {

			// 1] Если is_initial == true
			if(is_initial && !group && is_initial == true) {

				// 1.1] Если группы уже загружены
				if(self.m.s5.groups().length) {

					// Добавить состояние в историю
					var subdoc = layoutmodel.m.s1.selected_subdoc();
					History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (false ? 'article=' + false : "") + (false ? '&' : '') + (self.m.s5.choosen_group().name_folder() ? 'group=' + self.m.s5.choosen_group().name_folder() : ""));

					// Свернуть все развернутые статьи
					self.f.s5.close_all_articles();

					// Завершить
					return;

				}

				// 1.2] Назначить значение mode == 1
				var what2return = 1;

			}

			// 2] Если group не пуста
			if(group && !is_initial) {

				// 2.1] Если статьи для group есть в m.s5.articles
				if(self.m.s5.articles()[group] && self.m.s5.articles()[group]() && self.m.s5.articles()[group]().length) {

					// 2.1.1] Выбрать группу group среди m.s5.groups
					self.m.s5.choosen_group((function(){
						var choosen_group = "";
						for(var i=0; i<self.m.s5.groups().length; i++) {
							if(self.m.s5.groups()[i].name_folder() == group)
								choosen_group = self.m.s5.groups()[i];
						}
						return choosen_group;
					})());

					// 2.1.2] Добавить в историю новое состояние
					var subdoc = layoutmodel.m.s1.selected_subdoc();
					History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (false ? 'article=' + false : "") + (false ? '&' : '') + (self.m.s5.choosen_group().name_folder() ? 'group=' + self.m.s5.choosen_group().name_folder() : ""));

					// 2.1.3] Свернуть все статьи
					self.f.s5.close_all_articles();

					// 2.1.n] Завершить
					return;

				}

				// 2.3] Назначить значение mode == 3
				var what2return = 3;

			}

			// 3] Получить параметры из query string
			var qs_params = (function(){

				// 3.1] Подготовить объект для результатов
				var results = "";

				// 3.2] Получить объект qs_array
				var qs_array = layout_data.data.request.qs_array;

				// 3.3] Если qs_array не пуст
				if(qs_array && (qs_array['group'] || qs_array['article'])) {

					// Записать в results объект
					results = {};

					// Если есть group, записать её в results
					if(qs_array['group'])
						results['group'] = qs_array['group'];

					// Если есть article, записать её в results
					if(qs_array['article'])
						results['article'] = qs_array['article'];

				}

				// 3.n] Иначе вернуть пустую строку
				return results;

			})();

			// 4] Определить, какую группу запрашивать из FAQ
			var group2request = (function(){

				// 4.1] Если qs_params пуст, или в нём нет group, то вернуть group
				if(!qs_params || !qs_params['group']) return group;

				// 4.2] Если what2return == 1
				if(what2return == 1) return qs_params['group'];

				// 4.3] Если what2return == 3
				if(what2return == 3) return group;

			})();

			// 4] Отправить AJAX-запрос и получить данные FAQа
			ajaxko(self, {
				key: 	    		"D10010:1",
				from: 		    "ajaxko",
				data: 		    {
					what2return: what2return,
					group: 			 group2request ? group2request : ""
				},
				prejob:       function(config, data, event){

					// 1] Показать модальный щит со спиннером загрузки
					// - Если what2return == 1
					if(what2return == 1)
						self.m.s5.is_initial_shield_visible(true);

					// 2] Спиннером загрузки группы
					// - Если what2return == 3
					if(what2return == 3)
						context.is_spinner_visible(true);

				},
				postjob:      function(data, params){},
				ajax_params:  {
					what2return: 	what2return,
					context: 			context,
					qs_params:    qs_params
				},
				ok_0:         function(data, params){

					// 1] Получить входящие данные
					var faq 			= data.data.faq || "";
					var group 		= data.data.group || "";
					var groups 		= data.data.groups || [];
					var articles 	= data.data.articles || [];

					// 2] Если это инициирующий запуск
					// - При первом клике по FAQ в главном меню.
					if(params.what2return == 1) {

						// 2.1] Записать groups в m.s5.groups
						(function(){

							// Очистить m.s5.groups
							self.m.s5.groups.removeAll();

							// Записать groups в m.s5.groups
							for(var i=0; i<groups.length; i++) {
								self.m.s5.groups.push(ko.mapping.fromJS(groups[i]));
							}

						})();

					}

					// 3] Если это не инициирующий запуск
					// - При переключении групп
					if(params.what2return == 3) {

						// 3.1] Свернуть все статьи
						self.f.s5.close_all_articles();

					}

					// 4] Записать articles в m.s5.articles
					// - Но только, если там ещё нет статей для этой группы.
					(function(){

						// 4.1] Если статей для choosen_group ещё нет, и articles не пуст, создать набл.массив
						if(!self.m.s5.articles()[group] && (articles && articles.length))
							self.m.s5.articles()[group] = ko.observableArray([]);

						// 4.2] Записать groups в m.s5.groups
						for(var i=0; i<articles.length; i++) {

							// 4.2.1] Заменить в articles[i].html $$uri$$ на актуальный URL к папке с данными FAQа
							articles[i].html.ru = articles[i].html.ru.replace(/\$\$url\$\$/, layoutmodel.m.s0.full_host()+'/'+server.data.public_faq_folder);

							// 4.2.2] Добавить статью article[i] в m.s5.articles
							self.m.s5.articles()[group].push(ko.mapping.fromJS(articles[i]));

						}

					})();

					// 5] Выбрать группу group среди m.s5.groups
					self.m.s5.choosen_group((function(){
						var choosen_group = "";
						for(var i=0; i<self.m.s5.groups().length; i++) {
							if(self.m.s5.groups()[i].name_folder() == group)
								choosen_group = self.m.s5.groups()[i];
						}
						return choosen_group;
					})());

					// 6] Если what2return == 1, и в qs_params есть article, и self.m.s5.choosen_group не пуст
					// - Раскрыть эту статью.
					if(params.what2return == 1 && params.qs_params && params.qs_params['article'] && self.m.s5.choosen_group()) {
						for(var i=0; i<self.m.s5.articles()[group]().length; i++) {
							if(params.qs_params['article'] == self.m.s5.articles()[group]()[i].name_folder()) {
								self.m.s5.articles()[group]()[i].is_expanded(true);
								var expanded_article = self.m.s5.articles()[group]()[i].name_folder();
							}
						}
					}

					// 7] Записать название текущего FAQа
					self.m.s5.current_faq(faq);

					// 8] Добавить в историю новое состояние
					// - Если what2return == 3
					// - Если params.qs_params, и в нём есть ключи group/article (хотя бы один из)

						// 8.1] Если what2return == 3
						if(what2return == 3) {
							var subdoc = layoutmodel.m.s1.selected_subdoc();
							History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + '?' + (expanded_article ? 'article=' + expanded_article : "") + (expanded_article ? '&' : '') + (group ? 'group=' + group : ""));
						}

						// 8.2] Если what2return == 1
						if(what2return == 1) {
							var subdoc = layoutmodel.m.s1.selected_subdoc();
							History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + '?' + (false ? 'article=' + expanded_article : "") + (false ? '&' : '') + (group ? 'group=' + group : ""));
						}

					// n] Скрыть все спиннеры
					if(params.what2return == 1) self.m.s5.is_initial_shield_visible(false);
					if(params.what2return == 3) params.context.is_spinner_visible(false);

				},
				ok_1:         function(data, params){

					// n] Скрыть все спиннеры
					if(params.what2return == 1) self.m.s5.is_initial_shield_visible(false);
					if(params.what2return == 3) params.context.is_spinner_visible(false);

				},
				ok_2:         function(data, params){

					// n] Скрыть все спиннеры
					if(params.what2return == 1) self.m.s5.is_initial_shield_visible(false);
					if(params.what2return == 3) params.context.is_spinner_visible(false);

				}
			});

		};


		//----------------------------------------------------------------------//
		// s5.2. Свернуть/Развернуть статью, добавить новое состояние в history //
		//----------------------------------------------------------------------//
		f.s5.switch_article = function(data, event) {

			// 1] Свернуть/Развернуть статью
			data.is_expanded(!data.is_expanded());

			// 2] Добавить новое состояние в history

				// 2.1] Получить имя группы и статьи, а также выбранный поддокумент
				var group = self.m.s5.choosen_group().name_folder();
				var article = data.name_folder();
				var subdoc = layoutmodel.m.s1.selected_subdoc();

				// 2.2] Добавить в историю новое состояние
				History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + '?' + (article ? 'article=' + article : "") + (article ? '&' : '') + (group ? 'group=' + group : ""));

		};


		//---------------------------//
		// s5.3. Свернуть все статьи //
		//---------------------------//
		f.s5.close_all_articles = function() {

			for(var i=0; i<self.m.s5.articles()[self.m.s5.choosen_group().name_folder()]().length; i++) {
				self.m.s5.articles()[self.m.s5.choosen_group().name_folder()]()[i].is_expanded(false);
			}

		};



return f; }};




























