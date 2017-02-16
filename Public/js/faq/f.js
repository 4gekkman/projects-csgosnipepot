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
 * 	s5. Функционал ТОПа игроков
 *
 *    f.s5.get_faq      	| s5.1. Запросить данные FAQа с сервера
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
	f.s5.get_faq = function(is_initial, group) {

		// 1] Если is_initial == true
		if(is_initial && !group && is_initial == true) {

			// 1.1] Если группы уже загружены, завершить
			if(self.m.s5.groups().length)
				return;

			// 1.2] Назначить значение mode == 1
			var what2return = 1;

		}

		// 2] Если group не пуста
		if(group && !is_initial) {

			// 2.1] Если статьи для group есть в m.s5.articles, завершить
			if(self.m.s5.articles()[group] && self.m.s5.articles()[group]() && self.m.s5.articles()[group]().length)
				return;

			// 2.2] Назначить значение mode == 3
			var what2return = 3;

		}

		// 3] Отправить AJAX-запрос и получить данные FAQа
		ajaxko(self, {
			key: 	    		"D10009:5",
			from: 		    "ajaxko",
			data: 		    {
 				what2return: what2return,
 				group: 			 group ? group : ""
			},
			prejob:       function(config, data, event){

				// 1] Показать модальный щит со спиннером загрузки
				self.m.s5.is_initial_shield_visible(true);

			},
			postjob:      function(data, params){},
			ok_0:         function(data, params){

				// 1] Получить входящие данные
				var faq 			= data.data.faq;
				var group 		= data.data.group;
				var groups 		= data.data.groups;
				var articles 	= data.data.articles;

				// 2] Записать groups в m.s5.groups
				(function(){

					// 2.1] Очистить m.s5.groups
					self.m.s5.groups.removeAll();

					// 2.2] Записать groups в m.s5.groups
					for(var i=0; i<groups.length; i++) {
						self.m.s5.groups.push(ko.mapping.fromJS(groups[i]));
					}

				})();

				// 3] Записать articles в m.s5.articles
				// - Но только, если там ещё нет статей для этой группы.
				(function(){

					// 3.1] Если статей для choosen_group ещё нет, создать набл.массив
					if(!self.m.s5.articles[group])
						self.m.s5.articles[group] = ko.observableArray([]);

					// 3.2] Записать groups в m.s5.groups
					for(var i=0; i<articles.length; i++) {

						// 3.2.1] Добавить статью article[i] в m.s5.articles
						self.m.s5.articles[group].push(ko.mapping.fromJS(articles[i]));

					}

				})();

				// 4] Выбрать группу group среди m.s5.groups
				self.m.s5.choosen_group((function(){
					var choosen_group = "";
					for(var i=0; i<self.m.s5.groups().length; i++) {
						if(self.m.s5.groups()[i].name_folder() == group)
							choosen_group = self.m.s5.groups()[i];
					}
					return choosen_group;
				})());

				// 5] Записать название текущего FAQа
				self.m.s5.current_faq(faq);

				// n] Скрыть модальный щит со спиннером загрузки
				self.m.s5.is_initial_shield_visible(false);

			},
			ok_1:         function(data, params){

				// n] Скрыть модальный щит со спиннером загрузки
				self.m.s5.is_initial_shield_visible(false);

			},
			ok_2:         function(data, params){

				// n] Скрыть модальный щит со спиннером загрузки
				self.m.s5.is_initial_shield_visible(false);

			}
		});

	};



return this; }};




























