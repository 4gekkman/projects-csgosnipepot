/*//============================================================================////
////																			                                      ////
////   Функционал FAQ, предназначен для подключения в основной f.js документа   ////
////																			                                      ////
////=============================================================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 	s5. Функционал FAQа
 *
 *    f.s5.get_faq      					| s5.1. Запросить данные FAQа с сервера
 *    f.s5.switch_article					| s5.2. Свернуть/Развернуть статью, добавить новое состояние в history
 *    f.s5.close_all_articles			| s5.3. Свернуть все статьи
 *    f.s5.open_faq_article       | s5.4. Открыть в указанной группе указанную статью FAQ без перезагрузки страницы
 *
 *
 */

	//--------------------------------------------------------------------//


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//
var ModelFunctionsFaq = { constructor: function(self, f) { f.s5 = this;

	//----------------------//
	// 			        		 	  //
	// 	s5. Функционал FAQ  //
	// 			         			  //
	//----------------------//

	//---------------------------------------//
	// s5.1. Запросить данные FAQа с сервера //
	//---------------------------------------//
	// - Если is_initial == true
	//   • group игнорируется, и возвращаются все группы и статьи для стартовой.
	//   • При этом, если m.s5.groups не пуста, то запрос не производится, функция завершается.
	// - Если group не пуста
	//   • То is_initial игрогируется, и возвращаются только статьи для group.
	//   • При этом, если статьи для group уже есть в m.s5.articles, то запрос не производится, функция завершается.
	// - Возможное содержимое config:
	//
	// 		• is_initial 	| [Обязательно в отсутствии group] Требуют ли группы загрузки
	// 		• group 			| [Обязательно в отсутствии is_initial] На какую группу переключиться
	// 		• callback 		| [Не обязательно] Функция, исполняющаяся после выбора группы
	//
	f.s5.get_faq = function(config, context, event) {

		// 1] Если is_initial == true
		if(config.is_initial && !config.group && config.is_initial == true) {

			// 1.1] Если группы уже загружены
			if(self.m.s5.groups().length) {

				// Добавить состояние в историю
				var subdoc = layoutmodel.m.s1.selected_subdoc();
				History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (false ? 'article=' + false : "") + (false ? '&' : '') + (self.m.s5.choosen_group().name_folder() ? 'group=' + self.m.s5.choosen_group().name_folder() : ""));

				// Свернуть все развернутые статьи
				self.f.s5.close_all_articles();

				// Вызвать callback, если он передан
				if(config.callback)
					config.callback();

				// Завершить
				return;

			}

			// 1.2] Назначить значение mode == 1
			var what2return = 1;

		}

		// 2] Если group не пуста
		if(config.group && !config.is_initial) {

			// 2.1] Если статьи для group есть в m.s5.articles
			if(self.m.s5.articles()[config.group] && self.m.s5.articles()[config.group]() && self.m.s5.articles()[config.group]().length) {

				// 2.1.1] Выбрать группу group среди m.s5.groups
				self.m.s5.choosen_group((function(){
					var choosen_group = "";
					for(var i=0; i<self.m.s5.groups().length; i++) {
						if(self.m.s5.groups()[i].name_folder() == config.group)
							choosen_group = self.m.s5.groups()[i];
					}
					return choosen_group;
				})());

				// 2.1.2] Добавить в историю новое состояние
				var subdoc = layoutmodel.m.s1.selected_subdoc();
				History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (false ? 'article=' + false : "") + (false ? '&' : '') + (self.m.s5.choosen_group().name_folder() ? 'group=' + self.m.s5.choosen_group().name_folder() : ""));

				// 2.1.3] Свернуть все статьи
				self.f.s5.close_all_articles();

				// 2.1.4] Вызвать callback, если он передан
				if(config.callback)
					config.callback();

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
			if(!qs_params || !qs_params['group']) return config.group;

			// 4.2] Если what2return == 1
			if(what2return == 1) return qs_params['group'];

			// 4.3] Если what2return == 3
			if(what2return == 3) return config.group;

		})();

		// 5] Отправить AJAX-запрос и получить данные FAQа
		ajaxko(self, {
			key: 	    		"D10009:5",
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
				qs_params:    qs_params,
				callback:     config.callback
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
						History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (expanded_article ? 'article=' + expanded_article : "") + (expanded_article ? '&' : '') + (group ? 'group=' + group : ""));
					}

					// 8.2] Если what2return == 1
					if(what2return == 1) {
						var subdoc = layoutmodel.m.s1.selected_subdoc();
						History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (false ? 'article=' + expanded_article : "") + (false ? '&' : '') + (group ? 'group=' + group : ""));
					}

				// 9] Вызвать callback, если он передан
				if(params.callback)
					params.callback();

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
			History.replaceState({state: subdoc.uri()}, document.title, layout_data.data.request.baseuri + ((subdoc.uri() != '/') ? subdoc.uri() : '') + '?' + (article ? 'article=' + article : "") + (article ? '&' : '') + (group ? 'group=' + group : ""));

	};


	//---------------------------//
	// s5.3. Свернуть все статьи //
	//---------------------------//
	f.s5.close_all_articles = function() {

		for(var i=0; i<self.m.s5.articles()[self.m.s5.choosen_group().name_folder()]().length; i++) {
			self.m.s5.articles()[self.m.s5.choosen_group().name_folder()]()[i].is_expanded(false);
		}

	};


	//---------------------------------------------------------------------------------//
	// s5.4. Открыть в указанной группе указанную статью FAQ без перезагрузки страницы //
	//---------------------------------------------------------------------------------//
	// - Параметры в config: faq_url, group, article
	f.s5.open_faq_article = function(config, data, event) {

		// 1] Переключиться на раздел FAQ в главном меню
		layoutmodel.f.s1.choose_subdoc({uri: config.faq_url, callback: function(config){

			// 3.1] Попробовать найти статью config.article
			var article = (function(){

				// 3.1.1] Если группы статей config.group нет, вернуть пустую строку
				var articles = self.m.s5.articles()[config.group];
				if(!articles || !articles())
					return "";

				// 3.1.2] Попробовать найти статью config.article в articles
				for(var i=0; i<articles().length; i++) {
					if(articles()[i].name_folder() == config.article)
						return articles()[i];
				}

				// 3.1.n] Если ничего не найдено, вернуть пустую строку
				return "";

			})();

			console.log(config);

			// 3.2] Если article не пуста
			if(article)
				self.f.s5.switch_article(article, null);

			// 3.3] Если article пуста
			else {
				self.f.s5.get_faq({is_initial: false, group: config.group, callback: function(){

					console.log('Колбэк');
					//self.f.s5.switch_article(article, null);

				}});
			}



			// 3.2] Открыть статью config.article
			self.f.s5.switch_article(article, null);

		}.bind(data, config)});

		// 2] Прокрутить окно в самый верх
		window.scrollTo(window.scrollX, 0);

		// 3] Переключить FAQ на группу group, и открыть статью article
//		model.f.s5.get_faq({is_initial: false, group: "", callback: function(config){
//
//			// 3.1] Попробовать найти статью config.article
//			var article = (function(){
//
//				// 3.1.1] Если группы статей config.group нет, вернуть пустую строку
//				var articles = self.m.s5.articles()[config.group];
//				if(!articles || !articles())
//					return "";
//
//				// 3.1.2] Попробовать найти статью config.article в articles
//				for(var i=0; i<articles().length; i++) {
//					if(articles()[i].name_folder() == config.article)
//						return articles()[i];
//				}
//
//				// 3.1.n] Если ничего не найдено, вернуть пустую строку
//				return "";
//
//			})();
//
//			// 3.2] Открыть статью config.article
//			self.f.s5.switch_article(article, null);
//
//		}.bind(data, config)});

	};









return this; }};




























