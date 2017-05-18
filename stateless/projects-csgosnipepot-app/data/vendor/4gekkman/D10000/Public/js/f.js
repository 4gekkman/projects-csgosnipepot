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
 *    f.s0.update_groups            | s0.2. Обновить модель групп на основе переданных данных
 *    f.s0.update_privs             | s0.3. Обновить модель прав на основе переданных данных
 *    f.s0.update_tags              | s0.4. Обновить модель тегов на основе переданных данных
 *    f.s0.update_users             | s0.5. Обновить модель пользователей на основе переданных данных
 *    f.s0.get_all_selected         | s0.6. Получить массивы выделенных пользователей / групп / прав / тегов
 *    f.s0.update_all               | s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера
 *
 *  s1. Функционал модели управления поддокументами приложения
 *
 *		f.s1.is_tab_visible						| s1.1. Определяет, какой таб в меню главной таб-панели видим, а какой нет
 *		f.s1.is_tab_active						| s1.2. Определяет, какой таб в меню главной таб-панели активен
 *    f.s1.choose_subdoc            | s1.3. Выбрать subdoc с указанным id
 *
 *  s2. Функционал группы документов, связанных с группами
 *
 * 		f.s2.select_all_change 				| s2.1. Изменение значения чекбокса "Выбрать все группы"
 *    f.s2.pagi 										| s2.2. Пагинационный функционал групп
 * 		f.s2.create                   | s2.3. Создать новую группу
 * 		f.s2.showedit                 | s2.4. Открыть форму редактирования кликнутой группы
 * 	  f.s2.edit                     | s2.5. Отредактировать группу
 * 	  f.s2.del                      | s2.6. Удалить все выбранные группы
 *    f.s2.filters_clear            | s2.7. Очистить указанные фильтры (присвоить значения по умолчанию)
 *    f.s2.filters_all_on           | s2.8. Включить все фильтры
 *    f.s2.filters_all_off          | s2.9. Выключить все фильтры
 *    f.s2.filters_apply_users      | s2.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую
 *    f.s2.filters_apply_privs     	| s2.11. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую
 *    f.s2.filters_apply_tags     	| s2.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую
 *    f.s2.attach2groups            | s2.13. Связать выделенные группы с выделенными пользователями / правами / тегами
 *    f.s2.detach2groups            | s2.14. Отвязать выделенные группы от выделенных пользователей / прав / тегов
 *
 *  s3. Функционал группы документов, связанных с правами
 *
 * 		f.s3.select_all_change 				| s3.1. Изменение значения чекбокса "Выбрать все права"
 *    f.s3.pagi 										| s3.2. Пагинационный функционал прав
 * 		f.s3.create                   | s3.3. Создать новое право
 * 		f.s3.showedit                 | s3.4. Открыть форму редактирования кликнутого права
 * 	  f.s3.edit                     | s3.5. Отредактировать право
 * 	  f.s3.del                      | s3.6. Удалить все выбранные права
 *    f.s3.filters_clear            | s3.7. Очистить указанные фильтры (присвоить значения по умолчанию)
 *    f.s3.filters_all_on           | s3.8. Включить все фильтры
 *    f.s3.filters_all_off          | s3.9. Выключить все фильтры
 *    f.s3.filters_apply_users      | s3.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую
 *    f.s3.filters_apply_groups    	| s3.11. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую
 *    f.s3.filters_apply_tags     	| s3.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую
 *    f.s3.attach2privs             | s3.13. Связать выделенные права с выделенными пользователями / группами / тегами
 *    f.s3.detach2privs             | s3.14. Отвязать выделенные права от выделенных пользователей / групп / тегов
 *
 *  s4. Функционал группы документов, связанных с тегами
 *
 * 		f.s4.select_all_change 				| s4.1. Изменение значения чекбокса "Выбрать все теги"
 *    f.s4.pagi 										| s4.2. Пагинационный функционал тегов
 * 		f.s4.create                   | s4.3. Создать новый тег
 * 		f.s4.showedit                 | s4.4. Открыть форму редактирования кликнутого тега
 * 	  f.s4.edit                     | s4.5. Отредактировать тег
 * 	  f.s4.del                      | s4.6. Удалить все выбранные теги
 *    f.s4.filters_clear            | s4.7. Очистить указанные фильтры (присвоить значения по умолчанию)
 *    f.s4.filters_all_on           | s4.8. Включить все фильтры
 *    f.s4.filters_all_off          | s4.9. Выключить все фильтры
 *    f.s4.filters_apply_users      | s4.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую
 *    f.s4.filters_apply_groups    	| s4.11. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую
 *    f.s4.filters_apply_privs     	| s4.12. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую
 *    f.s4.attach2tags              | s4.13. Связать выделенные теги с выделенными пользователями / группами / правами
 *    f.s4.detach2tags              | s4.14. Отвязать выделенные теги от выделенных пользователей / групп / прав
 *
 *  s5. Функционал группы документов, связанных с пользователями
 *
 * 		f.s5.select_all_change 				| s5.1. Изменение значения чекбокса "Выбрать всех пользователей"
 *    f.s5.pagi 										| s5.2. Пагинационный функционал пользователей
 * 		f.s5.create                   | s5.3. Создать нового пользователя
 * 		f.s5.showedit                 | s5.4. Открыть форму редактирования кликнутого пользователя
 * 	  f.s5.edit                     | s5.5. Отредактировать пользователя
 * 	  f.s5.del                      | s5.6. Удалить всех выбранных пользователей
 *    f.s5.filters_clear            | s5.7. Очистить указанные фильтры (присвоить значения по умолчанию)
 *    f.s5.filters_all_on           | s5.8. Включить все фильтры
 *    f.s5.filters_all_off          | s5.9. Выключить все фильтры
 *    f.s5.filters_apply_groups     | s5.10. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую
 *    f.s5.filters_apply_privs     	| s5.11. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую
 *    f.s5.filters_apply_tags     	| s5.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую
 *    f.s5.attach2users             | s5.13. Связать выделенных пользователей с выделенными группами / правами / тегами
 *    f.s5.detach2users             | s5.14. Отвязать выделенных пользователей от выделенных групп / прав / тегов
 *    f.s5.change_password          | s5.15. Изменить пароль редактируемого пользователя
 *    f.s5.send_email_ver_code      | s5.16. Отправить код верификации email
 *    f.s5.email_verify_code        | s5.17. Провести верификацию email по указанному коду
 *    f.s5.pswd_recovery_email_code | s5.18. Отправить код восстановления пароля через email
 *    f.s5.recover_pswd_email_code  | s5.19. Восстановить пароль пользователя по email и аутентификационному коду
 *
 *  s6. Функционал документа, связанного с аутентификацией
 *
 *    f.s6.auth_email_password      | s6.1. Аутентифицироваться через email и пароль
 *    f.s6.logout                   | s6.2. Выйти из своей учётной записи
 *    f.s6.auth_phone_password      | s6.3. Аутентифицироваться через phone и пароль
 *    f.s6.auth_steam               | s6.4. Аутентифицироваться через steam
 *
 *
 */


//========================//
// 			        		 	    //
// 			 Функционал  			//
// 			         			    //
//====================----//


//--------------------------------------------------------------------//
// 			        		 			                                            //
// 			 s0. Функционал, доступный всему остальному функционалу 			//
// 			         					                                            //
//--------------------------------------------------------------------//
var ModelFunctions = { constructor: function(self) { var f = this;
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


		//---------------------------------------------------------//
		// s0.2. Обновить модель групп на основе переданных данных //
		//---------------------------------------------------------//
		// - Пояснение
		f.s0.update_groups = function(data) {

			// 1. Обновить self.m.s2.groups

				// 1.1. Очистить
				self.m.s2.groups.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.groups.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.groups[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.groups[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.groups[i][key]);

					}

					// 1.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1+(self.m.s2.pagi.pages_current()-1)*server.data.groups.data.items_at_page);

					// 1.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 1.2.4. Добавить этот объект в подготовленный массив
					self.m.s2.groups.push(ko.observable(obj))

				}

			// 2. Обновить m.s2.pagi.groups_total
			self.m.s2.pagi.groups_total(data.groups_total);

			// 3. Обновить m.s2.pagi.groups_filtered
			self.m.s2.pagi.groups_filtered(data.groups_filtered);

			// 4. Обновить m.s2.pagi.pages_total
			self.m.s2.pagi.pages_total(data.pages_total);

			// 5. Обновить m.s2.pagi.pages_current
			(function(){

				// 5.1. Если pages_current > pages_total
				if(self.m.s2.pagi.pages_current() > self.m.s2.pagi.pages_total()) {

					// 1] Установить номер страницы 1
					self.m.s2.pagi.pages_current(1);

					// 2] Запустить обновление всего интерфейса
					self.f.s0.update_all([], 'groups:update_groups:overpages', '', '');

				}

			})();

			// 6. Обновить m.s2.pagi.pages
			(function(){

				// 6.1. Очистить m.s2.pagi.pages
				self.m.s2.pagi.pages.removeAll();

				// 6.2. Рассчитать стартовую точку
				var start = (function(){

					// 6.2.1] Если номер текущей страницы <= 3
					if(+self.m.s2.pagi.pages_current() <= 3) return 1;

					// 6.2.2] Если номер текущей страницы > 3
					else {
						if(+self.m.s2.pagi.pages_current() + 2 >= +self.m.s2.pagi.pages_total()) {
							if(self.m.s2.pagi.pages_total() == 4) return +self.m.s2.pagi.pages_total() - 3;
							else return +self.m.s2.pagi.pages_total() - 4;
						}
						else return +self.m.s2.pagi.pages_current() - 2;
					}

				})();

				// 6.3. Рассчитать конечную точку
				var end = (function(){

					if(+start + 4 > +self.m.s2.pagi.pages_total()) return self.m.s2.pagi.pages_total();
					else return +start + 4;

				})();

				// 6.4. Наполнить m.s2.pagi.pages
				for(var i=start; i<=end; i++) {
					self.m.s2.pagi.pages.push(ko.observable({
						pagenum: ko.observable(+i)
					}));
				}

			})();

			// 7. Если чб "Select all" включён, включить все чб в m.s2.groups
			(function(){

				if(self.m.s2.select_all_groups()) {

					// Изменить состояние всех чб в s2.groups на true
					for(var i=0; i<self.m.s2.groups().length; i++) {
						self.m.s2.groups()[i]().selected(true);
					}

				}

			})();

			// 8. Выделить все группы, присутствующие в selected_group_ids
			if(data.selected_group_ids && !self.m.s2.select_all_groups()) {
				for(var i=0; i<data.selected_group_ids.length; i++) {
					if(self.m.s2.indexes.groups[data.selected_group_ids[i]])
						self.m.s2.indexes.groups[data.selected_group_ids[i]].selected(true);
				}
			}

		};


		//--------------------------------------------------------//
		// s0.3. Обновить модель прав на основе переданных данных //
		//--------------------------------------------------------//
		// - Пояснение
		f.s0.update_privs = function(data) {

			// 1. Обновить self.m.s3.privtypes и сделать локальный индекс

				// 1.1. Очистить
				self.m.s3.privtypes.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.privtypes.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.privtypes[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.privtypes[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.privtypes[i][key]);

					}

					// 1.2.2. Добавить этот объект в подготовленный массив
					self.m.s3.privtypes.push(ko.observable(obj))

				}

			// 2. Обновить self.m.s3.privs

				// 2.1. Очистить
				self.m.s3.privs.removeAll();

				// 2.2. Наполнить
				for(var i=0; i<data.privileges.length; i++) {

					// 2.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.privileges[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.privileges[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.privileges[i][key]);

					}

					// 2.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1+(self.m.s3.pagi.pages_current()-1)*server.data.privs.data.items_at_page);

					// 2.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 2.2.4. Добавить св-во type
					obj['type'] = self.m.s3.indexes.privtypes[data.privileges[i]['id_privtype']].name();

					// 2.2.5. Добавить этот объект в подготовленный массив
					self.m.s3.privs.push(ko.observable(obj))

				}

			// 3. Обновить m.s3.pagi.privs_total
			self.m.s3.pagi.privs_total(data.privs_total);

			// 4. Обновить m.s3.pagi.privs_filtered
			self.m.s3.pagi.privs_filtered(data.privs_filtered);

			// 5. Обновить m.s3.pagi.pages_total
			self.m.s3.pagi.pages_total(data.pages_total);

			// 6. Обновить m.s3.pagi.pages_current
			(function(){

				// 6.1. Если pages_current > pages_total
				if(self.m.s3.pagi.pages_current() > self.m.s3.pagi.pages_total()) {

					// 1] Установить номер страницы 1
					self.m.s3.pagi.pages_current(1);

					// 2] Запустить обновление всего интерфейса
					self.f.s0.update_all([], 'privs:update_privs:overpages', '', '');

				}

			})();

			// 7. Обновить m.s3.pagi.pages
			(function(){

				// 7.1. Очистить m.s3.pagi.pages
				self.m.s3.pagi.pages.removeAll();

				// 7.2. Рассчитать стартовую точку
				var start = (function(){

					// 7.2.1] Если номер текущей страницы <= 3
					if(+self.m.s3.pagi.pages_current() <= 3) return 1;

					// 7.2.2] Если номер текущей страницы > 3
					else {
						if(+self.m.s3.pagi.pages_current() + 2 >= +self.m.s3.pagi.pages_total()) {
							if(self.m.s3.pagi.pages_total() == 4) return +self.m.s3.pagi.pages_total() - 3;
							else return +self.m.s3.pagi.pages_total() - 4;
						}
						else return +self.m.s3.pagi.pages_current() - 2;
					}

				})();

				// 7.3. Рассчитать конечную точку
				var end = (function(){

					if(+start + 4 > +self.m.s3.pagi.pages_total()) return self.m.s3.pagi.pages_total();
					else return +start + 4;

				})();

				// 7.4. Наполнить m.s3.pagi.pages
				for(var i=start; i<=end; i++) {
					self.m.s3.pagi.pages.push(ko.observable({
						pagenum: ko.observable(+i)
					}));
				}

			})();

			// 8. Если чб "Select all" включён, включить все чб в m.s3.privs
			(function(){

				if(self.m.s3.select_all_privs()) {

					// Изменить состояние всех чб в s3.privs на true
					for(var i=0; i<self.m.s3.privs().length; i++) {
						self.m.s3.privs()[i]().selected(true);
					}

				}

			})();

			// 9. Выделить все права, присутствующие в selected_priv_ids
			if(data.selected_priv_ids && !self.m.s3.select_all_privs()) {
				for(var i=0; i<data.selected_priv_ids.length; i++) {
					if(self.m.s3.indexes.privs[data.selected_priv_ids[i]])
						self.m.s3.indexes.privs[data.selected_priv_ids[i]].selected(true);
				}
			}

		};


		//---------------------------------------------------------//
		// s0.4. Обновить модель тегов на основе переданных данных //
		//---------------------------------------------------------//
		// - Пояснение
		f.s0.update_tags = function(data) {

			// 1. Обновить self.m.s4.tags

				// 1.1. Очистить
				self.m.s4.tags.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.tags.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.tags[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.tags[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.tags[i][key]);

					}

					// 1.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1+(self.m.s4.pagi.pages_current()-1)*server.data.tags.data.items_at_page);

					// 1.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 1.2.4. Добавить этот объект в подготовленный массив
					self.m.s4.tags.push(ko.observable(obj))

				}

			// 2. Обновить m.s4.pagi.tags_total
			self.m.s4.pagi.tags_total(data.tags_total);

			// 3. Обновить m.s4.pagi.tags_filtered
			self.m.s4.pagi.tags_filtered(data.tags_filtered);

			// 4. Обновить m.s4.pagi.pages_total
			self.m.s4.pagi.pages_total(data.pages_total);

			// 5. Обновить m.s4.pagi.pages_current
			(function(){

				// 5.1. Если pages_current > pages_total
				if(self.m.s4.pagi.pages_current() > self.m.s4.pagi.pages_total()) {

					// 1] Установить номер страницы 1
					self.m.s4.pagi.pages_current(1);

					// 2] Запустить обновление всего интерфейса
					self.f.s0.update_all([], 'tags:update_tags:overpages', '', '');

				}

			})();

			// 6. Обновить m.s4.pagi.pages
			(function(){

				// 6.1. Очистить m.s4.pagi.pages
				self.m.s4.pagi.pages.removeAll();

				// 6.2. Рассчитать стартовую точку
				var start = (function(){

					// 6.2.1] Если номер текущей страницы <= 3
					if(+self.m.s4.pagi.pages_current() <= 3) return 1;

					// 6.2.2] Если номер текущей страницы > 3
					else {
						if(+self.m.s4.pagi.pages_current() + 2 >= +self.m.s4.pagi.pages_total()) {
							if(self.m.s4.pagi.pages_total() == 4) return +self.m.s4.pagi.pages_total() - 3;
							else return +self.m.s4.pagi.pages_total() - 4;
						}
						else return +self.m.s4.pagi.pages_current() - 2;
					}

				})();

				// 6.3. Рассчитать конечную точку
				var end = (function(){

					if(+start + 4 > +self.m.s4.pagi.pages_total()) return self.m.s4.pagi.pages_total();
					else return +start + 4;

				})();

				// 6.4. Наполнить m.s4.pagi.pages
				for(var i=start; i<=end; i++) {
					self.m.s4.pagi.pages.push(ko.observable({
						pagenum: ko.observable(+i)
					}));
				}

			})();

			// 7. Если чб "Select all" включён, включить все чб в m.s4.tags
			(function(){

				if(self.m.s4.select_all_tags()) {

					// Изменить состояние всех чб в s4.tags на true
					for(var i=0; i<self.m.s4.tags().length; i++) {
						self.m.s4.tags()[i]().selected(true);
					}

				}

			})();

			// 8. Выделить все теги, присутствующие в selected_tag_ids
			if(data.selected_tag_ids && !self.m.s4.select_all_tags()) {
				for(var i=0; i<data.selected_tag_ids.length; i++) {
					if(self.m.s4.indexes.tags[data.selected_tag_ids[i]])
						self.m.s4.indexes.tags[data.selected_tag_ids[i]].selected(true);
				}
			}

		};


		//-----------------------------------------------------------------//
		// s0.5. Обновить модель пользователей на основе переданных данных //
		//-----------------------------------------------------------------//
		// - Пояснение
		f.s0.update_users = function(data) {

			// 1. Обновить self.m.s5.genders и сделать локальный индекс

				// 1.1. Очистить
				self.m.s5.genders.removeAll();

				// 1.2. Наполнить
				for(var i=0; i<data.genders.length; i++) {

					// 1.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.genders[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.genders[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.genders[i][key]);

					}

					// 1.2.2. Добавить этот объект в подготовленный массив
					self.m.s5.genders.push(ko.observable(obj))

				}

				// 1.3. Добавить в m.s5.edit.genders соотв.значение
				if(self.m.s5.edit.id() && self.m.s5.indexes.users[self.m.s5.edit.id()])
					self.m.s5.edit.gender(self.m.s5.indexes.genders[self.m.s5.indexes.users[self.m.s5.edit.id()].gender()].name());

			// 2. Обновить self.m.s5.users

				// 2.1. Очистить
				self.m.s5.users.removeAll();

				// 2.2. Наполнить
				for(var i=0; i<data.users.length; i++) {

					// 2.2.1. Сформировать объект для добавления
					var obj = {};
					for(var key in data.users[i]) {

						// 1] Если свойство не своё, пропускаем
						if(!data.users[i].hasOwnProperty(key)) continue;

						// 2] Добавим в obj свойство key
						obj[key] = ko.observable(data.users[i][key]);

					}

					// 2.2.2. Добавить св-во number
					obj['number'] = ko.observable(i+1+(self.m.s5.pagi.pages_current()-1)*server.data.users.data.items_at_page);

					// 2.2.3. Добавить св-во selected
					obj['selected'] = ko.observable(false);

					// 2.2.4. Добавить св-во gender_name
					obj['gender_name'] = self.m.s5.indexes.genders[data.users[i]['gender']].name();

					// 2.2.5. Добавить этот объект в подготовленный массив
					self.m.s5.users.push(ko.observable(obj))

				}

			// 3. Обновить m.s5.pagi.users_total
			self.m.s5.pagi.users_total(data.users_total);

			// 4. Обновить m.s5.pagi.users_filtered
			self.m.s5.pagi.users_filtered(data.users_filtered);

			// 5. Обновить m.s5.pagi.pages_total
			self.m.s5.pagi.pages_total(data.pages_total);

			// 6. Обновить m.s5.pagi.pages_current
			(function(){

				// 6.1. Если pages_current > pages_total
				if(self.m.s5.pagi.pages_current() > self.m.s5.pagi.pages_total()) {

					// 1] Установить номер страницы 1
					self.m.s5.pagi.pages_current(1);

					// 2] Запустить обновление всего интерфейса
					self.f.s0.update_all([], 'users:update_privs:overpages', '', '');

				}

			})();

			// 7. Обновить m.s5.pagi.pages
			(function(){

				// 7.1. Очистить m.s5.pagi.pages
				self.m.s5.pagi.pages.removeAll();

				// 7.2. Рассчитать стартовую точку
				var start = (function(){

					// 7.2.1] Если номер текущей страницы <= 3
					if(+self.m.s5.pagi.pages_current() <= 3) return 1;

					// 7.2.2] Если номер текущей страницы > 3
					else {
						if(+self.m.s5.pagi.pages_current() + 2 >= +self.m.s5.pagi.pages_total()) {
							if(self.m.s5.pagi.pages_total() == 4) return +self.m.s5.pagi.pages_total() - 3;
							else return +self.m.s5.pagi.pages_total() - 4;
						}
						else return +self.m.s5.pagi.pages_current() - 2;
					}

				})();

				// 7.3. Рассчитать конечную точку
				var end = (function(){

					if(+start + 4 > +self.m.s5.pagi.pages_total()) return self.m.s5.pagi.pages_total();
					else return +start + 4;

				})();

				// 7.4. Наполнить m.s5.pagi.pages
				for(var i=start; i<=end; i++) {
					self.m.s5.pagi.pages.push(ko.observable({
						pagenum: ko.observable(+i)
					}));
				}

			})();

			// 8. Если чб "Select all" включён, включить все чб в m.s5.users
			(function(){

				if(self.m.s5.select_all_users()) {

					// Изменить состояние всех чб в s5.users на true
					for(var i=0; i<self.m.s5.users().length; i++) {
						self.m.s5.users()[i]().selected(true);
					}

				}

			})();

			// 9. Выделить всех пользователей, присутствующих в selected_user_ids
			if(data.selected_user_ids && !self.m.s5.select_all_users()) {
				for(var i=0; i<data.selected_user_ids.length; i++) {
					if(self.m.s5.indexes.users[data.selected_user_ids[i]])
						self.m.s5.indexes.users[data.selected_user_ids[i]].selected(true);
				}
			}

		};


		//------------------------------------------------------------------------//
		// s0.6. Получить массивы выделенных пользователей / групп / прав / тегов //
		//------------------------------------------------------------------------//
		f.s0.get_all_selected = function(data, event){

			// 1] Подготовить объект для результата
			var result = {
				users: 				[],
				groups: 			[],
				privileges: 	[],
				tags: 				[]
			};

			// 2] Получить всех выделенных пользователей
			result['users'] = (function(){

				// Если стоит галочка "Выбрать все"
				if(self.m.s5.select_all_users())
					return server.data.users.data.users_filtered_ids;

				// Если не стоит галочка "Выбрать все"
				else return self.m.s5.indexes.selected_user_ids;

			})();

			// 3] Получить всех выделенных групп
			result['groups'] = (function(){

				// Если стоит галочка "Выбрать все"
				if(self.m.s2.select_all_groups())
					return server.data.groups.data.groups_filtered_ids;

				// Если не стоит галочка "Выбрать все"
				else return self.m.s2.indexes.selected_group_ids;

			})();

			// 4] Получить всех выделенных прав
			result['privileges'] = (function(){

				// Если стоит галочка "Выбрать все"
				if(self.m.s3.select_all_privs())
					return server.data.privs.data.privs_filtered_ids;

				// Если не стоит галочка "Выбрать все"
				else return self.m.s3.indexes.selected_priv_ids;

			})();

			// 5] Получить всех выделенных тегов
			result['tags'] = (function(){

				// Если стоит галочка "Выбрать все"
				if(self.m.s4.select_all_tags())
					return server.data.tags.data.tags_filtered_ids;

				// Если не стоит галочка "Выбрать все"
				else return self.m.s4.indexes.selected_tag_ids;

			})();

			// 6] Вернуть результат
			return result;

		};




		//------------------------------------------------------------------------//
		// s0.x. Обновить всю фронтенд-модель документа свежими данными с сервера //
		//------------------------------------------------------------------------//
		// - Пояснение
		f.s0.update_all = function(what, from, data, event) {

			// 1] Подготовить объект с функциями-обновлялками моделей документа
			var update_funcs = {

				// 1.1] Обновлялка групп
				groups: function(){
					ajaxko(self, {
						command: 	    "\\M5\\Commands\\C6_groups",
						from: 		    "f.s0.update_all",
						data: 		    {
							page: 							self.m.s2.pagi.pages_current(),
							pages_total: 				"",
							items_at_page: 			server.data.groups.data.items_at_page,
							filters: 						ko.toJSON(self.m.s2.filters),
							selected_group_ids: self.m.s2.indexes.selected_group_ids
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счёрчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель групп на основе полученных данных
							self.f.s0.update_groups(data.data);

						}
					});
				},

				// 1.2] Обновлялка прав
				privs: function(){

					ajaxko(self, {
						command: 	    "\\M5\\Commands\\C7_privileges",
						from: 		    "f.s0.update_all",
						data: 		    {
							page: 							self.m.s3.pagi.pages_current(),
							pages_total: 				"",
							items_at_page: 			server.data.privs.data.items_at_page,
							filters: 						ko.toJSON(self.m.s3.filters),
							selected_priv_ids: 	self.m.s3.indexes.selected_priv_ids
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счёрчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель прав на основе полученных данных
							self.f.s0.update_privs(data.data);

						}
					});

				},

				// 1.3] Обновлялка тегов
				tags: function(){
					ajaxko(self, {
						command: 	    "\\M5\\Commands\\C8_tags",
						from: 		    "f.s0.update_all",
						data: 		    {
							page: 						self.m.s4.pagi.pages_current(),
							pages_total: 			"",
							items_at_page: 		server.data.tags.data.items_at_page,
							filters: 					ko.toJSON(self.m.s4.filters),
							selected_tag_ids: self.m.s4.indexes.selected_tag_ids
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счёрчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель тегов на основе полученных данных
							self.f.s0.update_tags(data.data);

						}
					});					
				},

				// 1.4] Обновлялка пользователей
				users: function(){
					ajaxko(self, {
						command: 	    "\\M5\\Commands\\C5_users",
						from: 		    "f.s0.update_all",
						data: 		    {
							page: 							self.m.s5.pagi.pages_current(),
							pages_total: 				"",
							items_at_page: 			server.data.users.data.items_at_page,
							filters: 						ko.toJSON(self.m.s5.filters),
							selected_user_ids: 	self.m.s5.indexes.selected_user_ids
						},
						prejob:       function(config, data, event){},
						postjob:      function(data, params){

							// Уменьшить счёрчик ajax-запросов на 1
							self.m.s0.ajax_counter(+self.m.s0.ajax_counter() - 1);

						},
						ok_0:         function(data, params){

							// Обновить модель пользователей на основе полученных данных
							self.f.s0.update_users(data.data);

						}
					});
				}

			};

			// 2] Подсчитать, сколько обновлялок будет запущено
			var updates_counter = (function(){

				// 2.1] Подготовить переменную для результата
				var result = 0;

				// 2.2] Если what пуст, или является пустым массивом
				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {
					for(var key in update_funcs) {
						if(!update_funcs.hasOwnProperty(key)) continue;
						result = +result + 1;
					}
				}

				// 2.3] В ином случае
				else {
					for(var i=0; i<what.length; i++) {
						if(update_funcs[what[i]]) result = +result + 1;
					}
				}

				// 2.n] Вернуть результат
				return result;

			})();

			// 3] Включить экран обновления
			self.m.s0.ajax_counter(+self.m.s0.ajax_counter() + +updates_counter);

			// 4] Произвести обновление

				// 4.1] Если what пуст, или является пустым массивом, обновить всё
				if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

					for(var key in update_funcs) {

						// 1] Если свойство не своё, пропускаем
						if(!update_funcs.hasOwnProperty(key)) continue;

						// 2] Выполнить обновление для key
						update_funcs[key]();

					}

				}

				// 4.2] Если what не пуст, обновить только указанное в нём
				else {
					for(var i=0; i<what.length; i++) {
						update_funcs[what[i]]();
					}
				}

		};


	//------------------------------------------------------------------------//
	// 			        		 			                                                //
	// 			 s1. Функционал модели управления поддокументами приложения 			//
	// 			         					                                                //
	//------------------------------------------------------------------------//
	f.s1 = {};

		//--------------------------------------------------------------------------//
		// s1.1. Определяет, какой таб в меню главной таб-панели видим, а какой нет //
		//--------------------------------------------------------------------------//
		// - Пояснение
		f.s1.is_tab_visible = function(data) {

			return ['1','4','7','10','13', '14'].indexOf(data.id()) != -1;

		};

		//---------------------------------------------------------------//
		// s1.2. Определяет, какой таб в меню главной таб-панели активен //
		//---------------------------------------------------------------//
		// - Пояснение
		f.s1.is_tab_active = function(data, root) {

			// 1] Получить необходимые данные
			var id_current = data.id();
			var id_choosen = root.m.s1.selected_subdoc().id();

			// 2] Если id_current относится к поддокументам подгруппы Users
			if(['1','2','3'].indexOf(id_current) != -1) {
				if(['1','2','3'].indexOf(id_choosen) != -1) return true;
			}

			// 3] Если id_current относится к поддокументам подгруппы Groups
			if(['4','5','6'].indexOf(id_current) != -1) {
				if(['4','5','6'].indexOf(id_choosen) != -1) return true;
			}

			// 4] Если id_current относится к поддокументам подгруппы Privs
			if(['7','8','9'].indexOf(id_current) != -1) {
				if(['7','8','9'].indexOf(id_choosen) != -1) return true;
			}

			// 5] Если id_current относится к поддокументам подгруппы Tags
			if(['10','11','12'].indexOf(id_current) != -1) {
				if(['10','11','12'].indexOf(id_choosen) != -1) return true;
			}

			// 6] Если id_current относится к поддокументам подгруппы Settings
			if(['13'].indexOf(id_current) != -1) {
				if(['13'].indexOf(id_choosen) != -1) return true;
			}

			// 7] Если id_current относится к поддокументам подгруппы Auth
			if(['14'].indexOf(id_current) != -1) {
				if(['14'].indexOf(id_choosen) != -1) return true;
			}

			// n] Вернуть false
			return false;

		};

		//-------------------------------------//
		// s1.3. Выбрать subdoc с указанным id //
		//-------------------------------------//
		// - Пояснение
		f.s1.choose_subdoc = function(id, data, event) {

			// 1] Получить ID
			var subdoc_id = (function(){

				if(id) return id;
				else return data.id();

			})();
			if(!subdoc_id) return;

			// 2] Получить объект поддокумента с id
			var subdoc = self.m.s1.indexes.subdocs[subdoc_id];

			// 3] Если этот subdoc уже выбран, завершить
			if(subdoc.id() == self.m.s1.selected_subdoc().id()) return;

			// 4] Если subdoc не найден, завершить
			if(!subdoc) {
				console.log('Поддокумент с id == '+subdoc_id+' не найден.');
				return;
			}

			// 5] Записать subdoc в m.s1.selected_subdoc
			self.m.s1.selected_subdoc(subdoc);

			// 6] Добавить историю новое состояние
			History.pushState({state:subdoc.id()}, subdoc.name(), subdoc.query());

			// 7] Выполнить update_all
			// - Но только если data != "without reload"
			if(data != "without reload")
				self.f.s0.update_all([], 'subdocs:choose_subdoc', '', '');

		};


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s2. Функционал группы документов, связанных с группами 			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s2 = {};

		//--------------------------------------------------------//
		// s2.1. Изменение значения чекбокса "Выбрать все группы" //
		//--------------------------------------------------------//
		// - Пояснение
		f.s2.select_all_change = function(data, event) {

			// 1] Получить текущее состояние чекбокса
			var state = self.m.s2.select_all_groups();

			// 2] Изменить состояние всех чб в s2.groups на state
			for(var i=0; i<self.m.s2.groups().length; i++) {
				self.m.s2.groups()[i]().selected(state);
			}

		};

		//--------------------------------------//
		// s2.2. Пагинационный функционал групп //
		//--------------------------------------//
		f.s2.pagi = {};

			// 1] Кнопка "Назад" //
			//-------------------//
			f.s2.pagi.back = function(data) {

				// 1.1] Если мы уже на первой странице, завершить
				if(self.m.s2.pagi.pages_current() == 1) return;

				// 1.2] Отнять от текущей страницы единицу
				self.m.s2.pagi.pages_current(+self.m.s2.pagi.pages_current()-1);

				// 1.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["groups"], 'groups:pagi:back', '', '');

			};

			// 2] Цифровая кнопка пагинации //
			//------------------------------//
			f.s2.pagi.goto_data = function(data) {

				// 2.1] Если текущая страница уже равна data.pagenum(), завершить
				if(self.m.s2.pagi.pages_current() == data.pagenum()) return;

				// 2.2] Установить текущую страницу, равной data.pagenum()
				self.m.s2.pagi.pages_current(data.pagenum());

				// 2.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["groups"], 'groups:pagi:goto_data', '', '');

			};

			// 3] Кнопка "Вперёд" //
			//--------------------//
			f.s2.pagi.forward = function(data) {

				// 3.1] Если мы уже на последней странице, завершить
				if(self.m.s2.pagi.pages_current() == self.m.s2.pagi.pages_total()) return;

				// 3.2] Прибавить к текущей странице единицу
				self.m.s2.pagi.pages_current(+self.m.s2.pagi.pages_current()+1);

				// 3.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["groups"], 'groups:pagi:forward', '', '');

			};

		//----------------------------//
		// s2.3. Создать новую группу //
		//----------------------------//
		f.s2.create = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C10_newgroup",
				from: 		    "f.s2.create",
			  data: 		    {
					name: 				self.m.s2.new.title(),
					description: 	self.m.s2.new.description(),
					isadmin: 			self.m.s2.new.admin() ? "yes" : "no"
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Groups"
					self.f.s1.choose_subdoc(4);

					// 2] Сообщить, что группа успешно создана
					notify({msg: 'New group has been created', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму создания
					self.m.s2.new.title("");
					self.m.s2.new.admin(false);
					self.m.s2.new.description("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//-----------------------------------------------------//
		// s2.4. Открыть форму редактирования кликнутой группы //
		//-----------------------------------------------------//
		f.s2.showedit = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемой группы
			self.m.s2.edit.id(data.id());
			self.m.s2.edit.title(data.name());
			self.m.s2.edit.admin(data.isadmin());
			self.m.s2.edit.description(data.description());

			// 2] Открыть поддокумент редактирования группы
			self.f.s1.choose_subdoc(5);

		};

		//------------------------------//
		// s2.5. Отредактировать группу //
		//------------------------------//
		f.s2.edit = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C18_changegroup",
				from: 		    "f.s2.edit",
			  data: 		    {
					id:   				self.m.s2.edit.id(),
					name: 				self.m.s2.edit.title(),
					description: 	self.m.s2.edit.description(),
					isadmin: 			self.m.s2.edit.admin() ? "yes" : "no"
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Groups"
					self.f.s1.choose_subdoc(4);

					// 2] Сообщить, что группа успешно отредактирована
					notify({msg: 'The group has been edited', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму создания
					self.m.s2.new.title("");
					self.m.s2.new.admin(false);
					self.m.s2.new.description("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//------------------------------------//
		// s2.6. Удалить все выбранные группы //
		//------------------------------------//
		f.s2.del = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C33_delgroups",
				from: 		    "f.s2.del",
			  data: 		    {
					selectall:    self.m.s2.select_all_groups(),
					ids:   				self.m.s2.indexes.selected_group_ids,
					filtered_ids: server.data.groups.data.groups_filtered_ids
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что группы успешно удалены
					notify({msg: 'The group(s) have been deleted', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
					self.f.s0.update_all([], 'groups:del', '', '');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//--------------------------------------------------------------------//
		// s2.7. Очистить указанные фильтры (присвоить значения по умолчанию) //
		//--------------------------------------------------------------------//
		f.s2.filters_clear = function(what, data, event){

			// 1] Если what пуст, или является пустым массивом, обновить всё
			if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

				for(var i=0; i<self.m.s2.filters().length; i++) {
					self.m.s2.filters()[i]().clearfunc(self);
				}

			}

			// 2] Если what не пуст, обновить только указанное в нём
			else {
				for(var i=0; i<what.length; i++) {
					for(var j=0; j<self.m.s2.filters().length; j++) {
						if(what[i].toLowerCase() == self.m.s2.filters()[j]().name().toLowerCase())
							self.m.s2.filters()[j]().clearfunc(self);
					}
				}
			}

		};

		//----------------------------//
		// s2.8. Включить все фильтры //
		//----------------------------//
		f.s2.filters_all_on = function(data, event){

			for(var i=0; i<self.m.s2.filters().length; i++) {
				self.m.s2.filters()[i]().on(true);
			}

		};

		//-----------------------------//
		// s2.9. Выключить все фильтры //
		//-----------------------------//
		f.s2.filters_all_off = function(data, event){

			for(var i=0; i<self.m.s2.filters().length; i++) {
				self.m.s2.filters()[i]().on(false);
			}

		};

		//---------------------------------------------------------------------------------//
		// s2.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую //
		//---------------------------------------------------------------------------------//
		f.s2.filters_apply_users = function(data, event){

			// 1] Получить массив ID выбранных групп
			var users = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s5.select_all_users())
					return server.data.users.data.users_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s5.indexes.selected_user_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s2.indexes.filters['ids of users'].value(users.join(','));

		};

		//-----------------------------------------------------------------------------//
		// s2.11. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую //
		//-----------------------------------------------------------------------------//
		f.s2.filters_apply_privs = function(data, event){

			// 1] Получить массив ID выбранных прав
			var privs = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s3.select_all_privs())
					return server.data.privs.data.privs_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s3.indexes.selected_priv_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s2.indexes.filters['ids of privileges'].value(privs.join(','));

		};

		//------------------------------------------------------------------------//
		// s2.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую //
		//------------------------------------------------------------------------//
		f.s2.filters_apply_tags = function(data, event){

			// 1] Получить массив ID выбранных тегов
			var tags = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s4.select_all_tags())
					return server.data.tags.data.tags_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s4.indexes.selected_tag_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s2.indexes.filters['ids of tags'].value(tags.join(','));

		};

		//----------------------------------------------------------------------------------//
		// s2.13. Связать выделенные группы с выделенными пользователями / правами / тегами //
		//----------------------------------------------------------------------------------//
		f.s2.attach2groups = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если связывать нечего, сообщить и завершить

				// 2.1] Если не выбрана ни одна группа
				if(selected.groups.length == 0) {
					notify({target: document.body, msg: 'The number of selected groups = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одно/один) пользователь/право/тег
				if(selected.users.length == 0 && selected.privileges.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected users and privs and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C39_attach2groups",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что группы успешно связаны
					notify({msg: 'The groups(s) have been attached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'groups:attach', '', '');

				}
			});

		};

		//-------------------------------------------------------------------------------//
		// s2.14. Отвязать выделенные группы от выделенных пользователей / права / тегов //
		//-------------------------------------------------------------------------------//
		f.s2.detach2groups = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если отвязывать нечего, сообщить и завершить

				// 2.1] Если не выбрана ни одна группа
				if(selected.groups.length == 0) {
					notify({target: document.body, msg: 'The number of selected groups = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одно/один) пользователь/право/тег
				if(selected.users.length == 0 && selected.privileges.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected users and privs and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C40_detach2groups",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что группы успешно отвязаны
					notify({msg: 'The groups(s) have been detached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'groups:detach', '', '');

				}
			});

		};

	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s3. Функционал группы документов, связанных с правами   			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s3 = {};

		//-------------------------------------------------------//
		// s3.1. Изменение значения чекбокса "Выбрать все права" //
		//-------------------------------------------------------//
		// - Пояснение
		f.s3.select_all_change = function(data, event) {

			// 1] Получить текущее состояние чекбокса
			var state = self.m.s3.select_all_privs();

			// 2] Изменить состояние всех чб в s3.privs на state
			for(var i=0; i<self.m.s3.privs().length; i++) {
				self.m.s3.privs()[i]().selected(state);
			}

		};

		//-------------------------------------//
		// s3.2. Пагинационный функционал прав //
		//-------------------------------------//
		f.s3.pagi = {};

			// 1] Кнопка "Назад" //
			//-------------------//
			f.s3.pagi.back = function(data) {

				// 1.1] Если мы уже на первой странице, завершить
				if(self.m.s3.pagi.pages_current() == 1) return;

				// 1.2] Отнять от текущей страницы единицу
				self.m.s3.pagi.pages_current(+self.m.s3.pagi.pages_current()-1);

				// 1.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["privs"], 'privs:pagi:back', '', '');

			};

			// 2] Цифровая кнопка пагинации //
			//------------------------------//
			f.s3.pagi.goto_data = function(data) {

				// 2.1] Если текущая страница уже равна data.pagenum(), завершить
				if(self.m.s3.pagi.pages_current() == data.pagenum()) return;

				// 2.2] Установить текущую страницу, равной data.pagenum()
				self.m.s3.pagi.pages_current(data.pagenum());

				// 2.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["privs"], 'privs:pagi:goto_data', '', '');

			};

			// 3] Кнопка "Вперёд" //
			//--------------------//
			f.s3.pagi.forward = function(data) {

				// 3.1] Если мы уже на последней странице, завершить
				if(self.m.s3.pagi.pages_current() == self.m.s3.pagi.pages_total()) return;

				// 3.2] Прибавить к текущей странице единицу
				self.m.s3.pagi.pages_current(+self.m.s3.pagi.pages_current()+1);

				// 3.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["privs"], 'privs:pagi:forward', '', '');

			};

		//---------------------------//
		// s3.3. Создать новое право //
		//---------------------------//
		f.s3.create = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C11_newprivilege",
				from: 		    "f.s3.create",
			  data: 		    {
					name: 				self.m.s3.new.title(),
					description: 	self.m.s3.new.description()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Privileges"
					self.f.s1.choose_subdoc(7);

					// 2] Сообщить, что кастомное право успешно создано
					notify({msg: 'New custom privilege has been created', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму создания
					self.m.s3.new.title("");
					self.m.s3.new.description("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//-----------------------------------------------------//
		// s3.4. Открыть форму редактирования кликнутого права //
		//-----------------------------------------------------//
		f.s3.showedit = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемого права
			self.m.s3.edit.id(data.id());
			self.m.s3.edit.title(data.name());
			self.m.s3.edit.type(data.type);
			self.m.s3.edit.description(data.description());

			// 2] Открыть поддокумент редактирования права
			self.f.s1.choose_subdoc(8);

		};

		//-----------------------------//
		// s3.5. Отредактировать право //
		//-----------------------------//
		f.s3.edit = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C19_changeprivilege",
				from: 		    "f.s3.edit",
			  data: 		    {
					id:   				self.m.s3.edit.id(),
					name: 				self.m.s3.edit.title(),
					description: 	self.m.s3.edit.description()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Privileges"
					self.f.s1.choose_subdoc(7);

					// 2] Сообщить, что право успешно отредактировано
					notify({msg: 'The privilege has been edited', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму редактирования
					self.m.s3.edit.id("");
					self.m.s3.edit.title("");
					self.m.s3.edit.type("");
					self.m.s3.edit.description("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//-----------------------------------//
		// s3.6. Удалить все выбранные права //
		//-----------------------------------//
		f.s3.del = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C34_delprivileges",
				from: 		    "f.s3.del",
			  data: 		    {
					selectall:    self.m.s3.select_all_privs(),
					ids:   				self.m.s3.indexes.selected_priv_ids,
					filtered_ids: server.data.privs.data.privs_filtered_ids
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что права успешно удалены
					notify({msg: 'The privilege(s) have been deleted', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'privs:del', '', '');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//--------------------------------------------------------------------//
		// s3.7. Очистить указанные фильтры (присвоить значения по умолчанию) //
		//--------------------------------------------------------------------//
		f.s3.filters_clear = function(what, data, event){

			// 1] Если what пуст, или является пустым массивом, обновить всё
			if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

				for(var i=0; i<self.m.s3.filters().length; i++) {
					self.m.s3.filters()[i]().clearfunc(self);
				}

			}

			// 2] Если what не пуст, обновить только указанное в нём
			else {
				for(var i=0; i<what.length; i++) {
					for(var j=0; j<self.m.s3.filters().length; j++) {
						if(what[i].toLowerCase() == self.m.s3.filters()[j]().name().toLowerCase())
							self.m.s3.filters()[j]().clearfunc(self);
					}
				}
			}

		};

		//----------------------------//
		// s3.8. Включить все фильтры //
		//----------------------------//
		f.s3.filters_all_on = function(data, event){

			for(var i=0; i<self.m.s3.filters().length; i++) {
				self.m.s3.filters()[i]().on(true);
			}

		};

		//-----------------------------//
		// s3.9. Выключить все фильтры //
		//-----------------------------//
		f.s3.filters_all_off = function(data, event){

			for(var i=0; i<self.m.s3.filters().length; i++) {
				self.m.s3.filters()[i]().on(false);
			}

		};

		//---------------------------------------------------------------------------------//
		// s3.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую //
		//---------------------------------------------------------------------------------//
		f.s3.filters_apply_users = function(data, event){

			// 1] Получить массив ID выбранных групп
			var users = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s5.select_all_users())
					return server.data.users.data.users_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s5.indexes.selected_user_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s3.indexes.filters['ids of users'].value(users.join(','));

		};

		//--------------------------------------------------------------------------//
		// s3.11. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую //
		//--------------------------------------------------------------------------//
		f.s3.filters_apply_groups = function(data, event){

			// 1] Получить массив ID выбранных прав
			var groups = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s2.select_all_groups())
					return server.data.groups.data.groups_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s2.indexes.selected_group_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s3.indexes.filters['ids of groups'].value(groups.join(','));

		};

		//------------------------------------------------------------------------//
		// s3.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую //
		//------------------------------------------------------------------------//
		f.s3.filters_apply_tags = function(data, event){

			// 1] Получить массив ID выбранных тегов
			var tags = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s4.select_all_tags())
					return server.data.tags.data.tags_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s4.indexes.selected_tag_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s3.indexes.filters['ids of tags'].value(tags.join(','));

		};

		//----------------------------------------------------------------------------------//
		// s3.13. Связать выделенные права с выделенными пользователями / группами / тегами //
		//----------------------------------------------------------------------------------//
		f.s3.attach2privs = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если связывать нечего, сообщить и завершить

				// 2.1] Если не выбрано ни одно право
				if(selected.privileges.length == 0) {
					notify({target: document.body, msg: 'The number of selected privileges = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одна/один) пользователь/группа/тег
				if(selected.users.length == 0 && selected.groups.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected users and groups and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C41_attach2privileges",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что права успешно связаны
					notify({msg: 'The privileges(s) have been attached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'privs:attach', '', '');

				}
			});

		};

		//------------------------------------------------------------------------------//
		// s3.14. Отвязать выделенные права от выделенных пользователей / групп / тегов //
		//------------------------------------------------------------------------------//
		f.s3.detach2privs = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если отвязывать нечего, сообщить и завершить

				// 2.1] Если не выбрано ни одно право
				if(selected.privileges.length == 0) {
					notify({target: document.body, msg: 'The number of selected privileges = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одна/один) пользователь/группа/тег
				if(selected.users.length == 0 && selected.groups.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected users and groups and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C42_detach2privileges",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что права успешно отвязаны
					notify({msg: 'The privileges(s) have been detached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'privs:detach', '', '');

				}
			});

		};


	//--------------------------------------------------------------------//
	// 			        		 			                                            //
	// 			 s4. Функционал группы документов, связанных с тегами   			//
	// 			         					                                            //
	//--------------------------------------------------------------------//
	f.s4 = {};

		//------------------------------------------------------//
		// s4.1. Изменение значения чекбокса "Выбрать все теги" //
		//------------------------------------------------------//
		// - Пояснение
		f.s4.select_all_change = function(data, event) {

			// 1] Получить текущее состояние чекбокса
			var state = self.m.s4.select_all_tags();

			// 2] Изменить состояние всех чб в s4.tags на state
			for(var i=0; i<self.m.s4.tags().length; i++) {
				self.m.s4.tags()[i]().selected(state);
			}

		};

		//--------------------------------------//
		// s4.2. Пагинационный функционал тегов //
		//--------------------------------------//
		f.s4.pagi = {};

			// 1] Кнопка "Назад" //
			//-------------------//
			f.s4.pagi.back = function(data) {

				// 1.1] Если мы уже на первой странице, завершить
				if(self.m.s4.pagi.pages_current() == 1) return;

				// 1.2] Отнять от текущей страницы единицу
				self.m.s4.pagi.pages_current(+self.m.s4.pagi.pages_current()-1);

				// 1.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["tags"], 'tags:pagi:back', '', '');

			};

			// 2] Цифровая кнопка пагинации //
			//------------------------------//
			f.s4.pagi.goto_data = function(data) {

				// 2.1] Если текущая страница уже равна data.pagenum(), завершить
				if(self.m.s4.pagi.pages_current() == data.pagenum()) return;

				// 2.2] Установить текущую страницу, равной data.pagenum()
				self.m.s4.pagi.pages_current(data.pagenum());

				// 2.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["tags"], 'tags:pagi:goto_data', '', '');

			};

			// 3] Кнопка "Вперёд" //
			//--------------------//
			f.s4.pagi.forward = function(data) {

				// 3.1] Если мы уже на последней странице, завершить
				if(self.m.s4.pagi.pages_current() == self.m.s4.pagi.pages_total()) return;

				// 3.2] Прибавить к текущей странице единицу
				self.m.s4.pagi.pages_current(+self.m.s4.pagi.pages_current()+1);

				// 3.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["tags"], 'tags:pagi:forward', '', '');

			};

		//-------------------------//
		// s4.3. Создать новый тег //
		//-------------------------//
		f.s4.create = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C12_newtag",
				from: 		    "f.s4.create",
			  data: 		    {
					name: 				self.m.s4.new.title()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Tags"
					self.f.s1.choose_subdoc(10);

					// 2] Сообщить, что кастомный тег успешно создан
					notify({msg: 'New tag has been created', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму создания
					self.m.s4.new.title("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//----------------------------------------------------//
		// s4.4. Открыть форму редактирования кликнутого тега //
		//----------------------------------------------------//
		f.s4.showedit = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемого права
			self.m.s4.edit.id(data.id());
			self.m.s4.edit.title(data.name());

			// 2] Открыть поддокумент редактирования тега
			self.f.s1.choose_subdoc(11);

		};

		//---------------------------//
		// s4.5. Отредактировать тег //
		//---------------------------//
		f.s4.edit = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C20_changetag",
				from: 		    "f.s4.edit",
			  data: 		    {
					id:   				self.m.s4.edit.id(),
					name: 				self.m.s4.edit.title()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Tags"
					self.f.s1.choose_subdoc(10);

					// 2] Сообщить, что тег был успешно отредактирован
					notify({msg: 'The tag has been edited', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму редактирования
					self.m.s4.edit.id("");
					self.m.s4.edit.title("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//----------------------------------//
		// s4.6. Удалить все выбранные теги //
		//----------------------------------//
		f.s4.del = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C35_deltags",
				from: 		    "f.s4.del",
			  data: 		    {
					selectall:    self.m.s4.select_all_tags(),
					ids:   				self.m.s4.indexes.selected_tag_ids,
					filtered_ids: server.data.tags.data.tags_filtered_ids
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что теги успешно удалены
					notify({msg: 'The tag(s) have been deleted', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'tags:del', '', '');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//--------------------------------------------------------------------//
		// s4.7. Очистить указанные фильтры (присвоить значения по умолчанию) //
		//--------------------------------------------------------------------//
		f.s4.filters_clear = function(what, data, event){

			// 1] Если what пуст, или является пустым массивом, обновить всё
			if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

				for(var i=0; i<self.m.s4.filters().length; i++) {
					self.m.s4.filters()[i]().clearfunc(self);
				}

			}

			// 2] Если what не пуст, обновить только указанное в нём
			else {
				for(var i=0; i<what.length; i++) {
					for(var j=0; j<self.m.s4.filters().length; j++) {
						if(what[i].toLowerCase() == self.m.s4.filters()[j]().name().toLowerCase())
							self.m.s4.filters()[j]().clearfunc(self);
					}
				}
			}

		};

		//----------------------------//
		// s4.8. Включить все фильтры //
		//----------------------------//
		f.s4.filters_all_on = function(data, event){

			for(var i=0; i<self.m.s4.filters().length; i++) {
				self.m.s4.filters()[i]().on(true);
			}

		};

		//-----------------------------//
		// s4.9. Выключить все фильтры //
		//-----------------------------//
		f.s4.filters_all_off = function(data, event){

			for(var i=0; i<self.m.s4.filters().length; i++) {
				self.m.s4.filters()[i]().on(false);
			}

		};

		//---------------------------------------------------------------------------------//
		// s4.10. Вписать в фильтр "IDs of users" ID выбранных пользователей через запятую //
		//---------------------------------------------------------------------------------//
		f.s4.filters_apply_users = function(data, event){

			// 1] Получить массив ID выбранных групп
			var users = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s5.select_all_users())
					return server.data.users.data.users_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s5.indexes.selected_user_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s4.indexes.filters['ids of users'].value(users.join(','));

		};

		//--------------------------------------------------------------------------//
		// s4.11. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую //
		//--------------------------------------------------------------------------//
		f.s4.filters_apply_groups = function(data, event){

			// 1] Получить массив ID выбранных прав
			var groups = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s2.select_all_groups())
					return server.data.groups.data.groups_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s2.indexes.selected_group_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s4.indexes.filters['ids of groups'].value(groups.join(','));

		};

		//-----------------------------------------------------------------------------//
		// s4.12. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую //
		//-----------------------------------------------------------------------------//
		f.s4.filters_apply_privs = function(data, event){

			// 1] Получить массив ID выбранных прав
			var privs = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s3.select_all_privs())
					return server.data.privs.data.privs_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s3.indexes.selected_priv_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s4.indexes.filters['ids of privileges'].value(privs.join(','));

		};

		//----------------------------------------------------------------------------------//
		// s4.13. Связать выделенные теги с выделенными пользователями / группами / правами //
		//----------------------------------------------------------------------------------//
		f.s4.attach2tags = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если связывать нечего, сообщить и завершить

				// 2.1] Если не выбран ни один тег
				if(selected.tags.length == 0) {
					notify({target: document.body, msg: 'The number of selected tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одна/одно) пользователь/группа/право
				if(selected.users.length == 0 && selected.groups.length == 0 && selected.privileges.length == 0) {
					notify({msg: 'The number of selected users and groups and privs = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C43_attach2tags",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что теги успешно связаны
					notify({msg: 'The tag(s) have been attached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'tags:attach', '', '');

				}
			});

		};

		//----------------------------------------------------------------------------//
		// s4.14. Отвязать выделенные теги от выделенных пользователей / групп / прав //
		//----------------------------------------------------------------------------//
		f.s4.detach2tags = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если отвязывать нечего, сообщить и завершить

				// 2.1] Если не выбран ни один тег
				if(selected.tags.length == 0) {
					notify({target: document.body, msg: 'The number of selected tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни один(одна/одно) пользователь/группа/право
				if(selected.users.length == 0 && selected.groups.length == 0 && selected.privileges.length == 0) {
					notify({msg: 'The number of selected users and groups and privs = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C44_detach2tags",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что теги успешно отвязаны
					notify({msg: 'The tags(s) have been detached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'tags:detach', '', '');

				}
			});

		};


	//----------------------------------------------------------------------------//
	// 			        		 			                                                    //
	// 			 s5. Функционал группы документов, связанных с пользователями   			//
	// 			         					                                                    //
	//----------------------------------------------------------------------------//
	f.s5 = {};

		//----------------------------------------------------------------//
		// s5.1. Изменение значения чекбокса "Выбрать всех пользователей" //
		//----------------------------------------------------------------//
		// - Пояснение
		f.s5.select_all_change = function(data, event) {

			// 1] Получить текущее состояние чекбокса
			var state = self.m.s5.select_all_users();

			// 2] Изменить состояние всех чб в s5.users на state
			for(var i=0; i<self.m.s5.users().length; i++) {
				self.m.s5.users()[i]().selected(state);
			}

		};

		//----------------------------------------------//
		// s5.2. Пагинационный функционал пользователей //
		//----------------------------------------------//
		f.s5.pagi = {};

			// 1] Кнопка "Назад" //
			//-------------------//
			f.s5.pagi.back = function(data) {

				// 1.1] Если мы уже на первой странице, завершить
				if(self.m.s5.pagi.pages_current() == 1) return;

				// 1.2] Отнять от текущей страницы единицу
				self.m.s5.pagi.pages_current(+self.m.s5.pagi.pages_current()-1);

				// 1.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["users"], 'users:pagi:back', '', '');

			};

			// 2] Цифровая кнопка пагинации //
			//------------------------------//
			f.s5.pagi.goto_data = function(data) {

				// 2.1] Если текущая страница уже равна data.pagenum(), завершить
				if(self.m.s5.pagi.pages_current() == data.pagenum()) return;

				// 2.2] Установить текущую страницу, равной data.pagenum()
				self.m.s5.pagi.pages_current(data.pagenum());

				// 2.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["users"], 'users:pagi:goto_data', '', '');

			};

			// 3] Кнопка "Вперёд" //
			//--------------------//
			f.s5.pagi.forward = function(data) {

				// 3.1] Если мы уже на последней странице, завершить
				if(self.m.s5.pagi.pages_current() == self.m.s5.pagi.pages_total()) return;

				// 3.2] Прибавить к текущей странице единицу
				self.m.s5.pagi.pages_current(+self.m.s5.pagi.pages_current()+1);

				// 3.3] Обновить всю фронтенд-модель документа
				self.f.s0.update_all(["users"], 'users:pagi:forward', '', '');

			};

		//-----------------------------------//
		// s5.3. Создать нового пользователя //
		//-----------------------------------//
		f.s5.create = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C9_newuser",
				from: 		    "f.s5.create",
			  data: 		    {
					email: 				self.m.s5.new.email(),
					surname: 			self.m.s5.new.surname(),
					name: 				self.m.s5.new.name(),
					patronymic: 	self.m.s5.new.patronymic(),
					gender: 			self.m.s5.new.gender(),
					birthday: 		self.m.s5.new.birthday(),
					phone: 				self.m.s5.new.phone(),
					isanonymous: 	self.m.s5.new.isanonymous() ? "yes" : "no",
					password: 		self.m.s5.new.password(),
					adminnote: 		self.m.s5.new.adminnote(),
					nickname: 		self.m.s5.new.nickname()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Users"
					self.f.s1.choose_subdoc(1);

					// 2] Сообщить, что пользователь успешно создан
					notify({msg: 'New user has been created', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму создания
					self.m.s5.new.email("");
					self.m.s5.new.surname("");
					self.m.s5.new.name("");
					self.m.s5.new.patronymic("");
					self.m.s5.new.gender("");
					self.m.s5.new.birthday("");
					self.m.s5.new.phone("");
					self.m.s5.new.isanonymous(0);
					self.m.s5.new.password("");
					self.m.s5.new.adminnote("");
					self.m.s5.new.nickname("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//------------------------------------------------------------//
		// s5.4. Открыть форму редактирования кликнутого пользователя //
		//------------------------------------------------------------//
		f.s5.showedit = function(data, event){

			// 1] Загрузить в форму текущие данные редактируемого права
			self.m.s5.edit.id(data.id());
			self.m.s5.edit.email(data.email());
			self.m.s5.edit.surname(data.surname());
			self.m.s5.edit.name(data.name());
			self.m.s5.edit.patronymic(data.patronymic());
			self.m.s5.edit.gender(data.gender_name);
			self.m.s5.edit.birthday(data.birthday());
			self.m.s5.edit.phone(data.phone());
			self.m.s5.edit.isanonymous(data.isanonymous());
			self.m.s5.edit.isblocked(data.is_blocked());
			self.m.s5.edit.adminnote(data.adminnote());
			self.m.s5.edit.ha_provider_name(data.ha_provider_name());
			self.m.s5.edit.ha_provider_uid(data.ha_provider_uid());
			self.m.s5.edit.nickname(data.nickname());

			// 2] Открыть поддокумент редактирования пользователя
			self.f.s1.choose_subdoc(2);

		};

		//------------------------------------//
		// s5.5. Отредактировать пользователя //
		//------------------------------------//
		f.s5.edit = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C17_changeuser",
				from: 		    "f.s5.edit",
			  data: 		    {
					id:     					self.m.s5.edit.id(),
					email:   					self.m.s5.edit.email(),
					surname: 					self.m.s5.edit.surname(),
					name:    					self.m.s5.edit.name(),
					patronymic: 			self.m.s5.edit.patronymic(),
					gender:   				self.m.s5.edit.gender(),
					birthday:  				self.m.s5.edit.birthday(),
					phone:  					self.m.s5.edit.phone(),
					isanonymous:			self.m.s5.edit.isanonymous() ? "yes" : "no",
					is_blocked:				self.m.s5.edit.isblocked() ? "yes" : "no",
					adminnote:				self.m.s5.edit.adminnote(),
					ha_provider_name:	self.m.s5.edit.ha_provider_name(),
					ha_provider_uid:	self.m.s5.edit.ha_provider_uid(),
					nickname:					self.m.s5.edit.nickname()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Перейти к поддокументу "Users"
					self.f.s1.choose_subdoc(1);

					// 2] Сообщить, что пользователь был успешно отредактирован
					notify({msg: 'The user has been edited', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 3] Очистить форму редактирования
					self.m.s5.edit.id("");
					self.m.s5.edit.email("");
					self.m.s5.edit.surname("");
					self.m.s5.edit.name("");
					self.m.s5.edit.patronymic("");
					self.m.s5.edit.gender("");
					self.m.s5.edit.birthday("");
					self.m.s5.edit.phone("");
					self.m.s5.edit.isanonymous("");
					self.m.s5.edit.isblocked("");
					self.m.s5.edit.adminnote("");
					self.m.s5.edit.ha_provider_name("");
					self.m.s5.edit.ha_provider_uid("");
					self.m.s5.edit.nickname("");

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//--------------------------------------------//
		// s5.6. Удалить всех выбранных пользователей //
		//--------------------------------------------//
		f.s5.del = function(data, event){

			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C36_delusers",
				from: 		    "f.s5.del",
			  data: 		    {
					selectall:    self.m.s5.select_all_users(),
					ids:   				self.m.s5.indexes.selected_user_ids,
					filtered_ids: server.data.users.data.users_filtered_ids
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что теги успешно удалены
					notify({msg: 'The user(s) have been deleted', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'users:del', '', '');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			  //ajax_params:  {},
			  //key: 			    "D1:1",
				//from_ex: 	    [],
			  //callback:     function(data, params){},
			  //ok_1:         function(data, params){},
			  //error:        function(){},
			  //timeout:      function(){},
			  //timeout_sec:  200,
			  //url:          window.location.href,
			  //ajax_method:  "post",
			  //ajax_headers: {"Content-Type": "application/json", "X-CSRF-TOKEN": server.csrf_token}
			});

		};

		//--------------------------------------------------------------------//
		// s5.7. Очистить указанные фильтры (присвоить значения по умолчанию) //
		//--------------------------------------------------------------------//
		f.s5.filters_clear = function(what, data, event){

			// 1] Если what пуст, или является пустым массивом, обновить всё
			if(!what || (get_object_type(what) == "Array" && what.length == 0)) {

				for(var i=0; i<self.m.s5.filters().length; i++) {
					self.m.s5.filters()[i]().clearfunc(self);
				}

			}

			// 2] Если what не пуст, обновить только указанное в нём
			else {
				for(var i=0; i<what.length; i++) {
					for(var j=0; j<self.m.s5.filters().length; j++) {
						if(what[i].toLowerCase() == self.m.s5.filters()[j]().name().toLowerCase())
							self.m.s5.filters()[j]().clearfunc(self);
					}
				}
			}

		};

		//----------------------------//
		// s5.8. Включить все фильтры //
		//----------------------------//
		f.s5.filters_all_on = function(data, event){

			for(var i=0; i<self.m.s5.filters().length; i++) {
				self.m.s5.filters()[i]().on(true);
			}

		};

		//-----------------------------//
		// s5.9. Выключить все фильтры //
		//-----------------------------//
		f.s5.filters_all_off = function(data, event){

			for(var i=0; i<self.m.s5.filters().length; i++) {
				self.m.s5.filters()[i]().on(false);
			}

		};

		//--------------------------------------------------------------------------//
		// s5.10. Вписать в фильтр "IDs of groups" ID выбранных групп через запятую //
		//--------------------------------------------------------------------------//
		f.s5.filters_apply_groups = function(data, event){

			// 1] Получить массив ID выбранных групп
			var groups = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s2.select_all_groups())
					return server.data.groups.data.groups_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s2.indexes.selected_group_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s5.indexes.filters['ids of groups'].value(groups.join(','));

		};

		//-----------------------------------------------------------------------------//
		// s5.11. Вписать в фильтр "IDs of privileges" ID выбранных прав через запятую //
		//-----------------------------------------------------------------------------//
		f.s5.filters_apply_privs = function(data, event){

			// 1] Получить массив ID выбранных прав
			var privs = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s3.select_all_privs())
					return server.data.privs.data.privs_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s3.indexes.selected_priv_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s5.indexes.filters['ids of privileges'].value(privs.join(','));

		};

		//------------------------------------------------------------------------//
		// s5.12. Вписать в фильтр "IDs of tags" ID выбранных тегов через запятую //
		//------------------------------------------------------------------------//
		f.s5.filters_apply_tags = function(data, event){

			// 1] Получить массив ID выбранных тегов
			var tags = (function(){

				// 1.1] Если стоит галочка "Выбрать все"
				if(self.m.s4.select_all_tags())
					return server.data.tags.data.tags_filtered_ids;

				// 1.2] Если не стоит галочка "Выбрать все"
				else return self.m.s4.indexes.selected_tag_ids;

			})();

			// 2] Перезаписать поле фильтра
			self.m.s5.indexes.filters['ids of tags'].value(tags.join(','));

		};

		//-----------------------------------------------------------------------------------//
		// s5.13. Связать выделенных пользователей с выделенными группами / правами / тегами //
		//-----------------------------------------------------------------------------------//
		f.s5.attach2users = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если связывать нечего, сообщить и завершить

				// 2.1] Если не выбран ни один пользователь
				if(selected.users.length == 0) {
					notify({target: document.body, msg: 'The number of selected users = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни одна(одно/один) группа/право/тег
				if(selected.groups.length == 0 && selected.privileges.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected groups and privs and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C37_attach2users",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно связаны
					notify({msg: 'The user(s) have been attached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'users:attach', '', '');

				}
			});

		};

		//-----------------------------------------------------------------------------//
		// s5.14. Отвязать выделенных пользователей от выделенных групп / прав / тегов //
		//-----------------------------------------------------------------------------//
		f.s5.detach2users = function(who, data, event){

			// 1] Получить массивы ID всех выбранных пользователей / групп / прав / тегов
			var selected = self.f.s0.get_all_selected();

			// 2] Если отвязывать нечего, сообщить и завершить

				// 2.1] Если не выбран ни один пользователь
				if(selected.users.length == 0) {
					notify({target: document.body, msg: 'The number of selected users = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

				// 2.2] Если не выбраны ни одна(одно/один) группа/право/тег
				if(selected.groups.length == 0 && selected.privileges.length == 0 && selected.tags.length == 0) {
					notify({msg: 'The number of selected groups and privs and tags = 0', time: 10, fontcolor: 'RGB(200,50,50)'});
					return;
				}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C38_detach2users",
				from: 		    "ajaxko",
				data: 		    {
					selected: selected,
					who: who
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					notify({msg: 'The user(s) have been detached', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Выполнить update_all
          self.f.s0.update_all([], 'users:detach', '', '');

				}
			});

		};

		//----------------------------------------------------//
		// s5.15. Изменить пароль редактируемого пользователя //
		//----------------------------------------------------//
		f.s5.change_password = function(data, event){

			// 1] Проверить, совпадают ли new1 и new2
			if(self.m.s5.changepass.new1() != self.m.s5.changepass.new2()) {
				notify({msg: 'Strings in password fields are not equal', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос на сервер
			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C45_change_password_admin",
				from: 		    "ajaxko",
			  data: 		    {
					user_id: 						self.m.s5.edit.id(),
					new_password: 			self.m.s5.changepass.new1(),
					new_password_again: self.m.s5.changepass.new2()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пароль пользователя успешно изменён
					notify({msg: 'The password of this user have been changed', time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			});

		};

		//----------------------------------------//
		// s5.16. Отправить код верификации email //
		//----------------------------------------//
		f.s5.send_email_ver_code = function(data, event){

			// 1] Проверить, если email пуст, сообщить и завершить
			if(!self.m.s5.edit.email()) {
				notify({msg: 'Email is empty', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос на сервер
			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C46_email_getcode",
				from: 		    "ajaxko",
			  data: 		    {
					user_id: 			self.m.s5.edit.id(),
					email:        self.m.s5.edit.email()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пароль пользователя успешно изменён
					notify({msg: 'The verification code has been created and sent', time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			});

		};

		//------------------------------------------------------//
		// s5.17. Провести верификацию email по указанному коду //
		//------------------------------------------------------//
		f.s5.email_verify_code = function(data, event){

			// 1] Проверить, если код верификации не введён, завершить
			if(!self.m.s5.emailapprove.code()) {
				notify({msg: 'Code field is empty', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос на сервер
			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C47_email_verify",
				from: 		    "ajaxko",
			  data: 		    {
					user_id: 			self.m.s5.edit.id(),
					email:        self.m.s5.edit.email(),
					code:         self.m.s5.emailapprove.code()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пароль пользователя успешно изменён
					notify({msg: 'The email has been verificated!', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Очистить поле верификационного кода
					self.m.s5.emailapprove.code('');

					// 3] Выполнить update_all
          self.f.s0.update_all([], 'users:detach', '', '');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			});

		};

		//--------------------------------------------------------//
		// s5.18. Отправить код восстановления пароля через email //
		//--------------------------------------------------------//
		f.s5.pswd_recovery_email_code = function(data, event){

			// 1] Проверить, если email пуст, сообщить и завершить
			if(!self.m.s6.pass_recover_email.email()) {
				notify({msg: 'Email is empty', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос на сервер
			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C62_pswd_recovery_email_getcode",
				from: 		    "ajaxko",
			  data: 		    {
					email:        self.m.s6.pass_recover_email.email()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пароль пользователя успешно изменён
					notify({msg: 'The auth code has been created and sent', time: 5, fontcolor: 'RGB(50,120,50)'});

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			});

		};

		//-----------------------------------------------------------------------------//
		// s5.19. Восстановить пароль пользователя по email и аутентификационному коду //
		//-----------------------------------------------------------------------------//
		f.s5.recover_pswd_email_code = function(data, event){

			// 1] Проверить, совпадают ли new1 и new2
			if(self.m.s6.pass_recover_email.password1() != self.m.s6.pass_recover_email.password2()) {
				notify({msg: 'Strings in password fields are not equal', time: 5, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос на сервер
			ajaxko(self, {
			  command: 	    "\\M5\\Commands\\C63_recover_pswd_by_email_and_code",
				from: 		    "ajaxko",
			  data: 		    {
					email: 					self.m.s6.pass_recover_email.email(),
					code: 					self.m.s6.pass_recover_email.code(),
					new_password1: 	self.m.s6.pass_recover_email.password1(),
					new_password2: 	self.m.s6.pass_recover_email.password2()
				},
			  prejob:       function(config, data, event){},
			  postjob:      function(data, params){},
			  ok_0:         function(data, params){

					// 1] Сообщить, что пароль пользователя успешно изменён
					notify({msg: 'The password of this user have been recovered', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Очистить поля формы
					self.m.s6.pass_recover_email.email('');
					self.m.s6.pass_recover_email.code('');
					self.m.s6.pass_recover_email.password1('');
					self.m.s6.pass_recover_email.password2('');

				},
			  ok_2:         function(data, params){
					notify({msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
					console.log(data.data.errortext);
				}
			});

		};


	//----------------------------------------------------------------------//
	// 			        		 			                                              //
	// 			 s6. Функционал документа, связанного с аутентификацией   			//
	// 			         					                                              //
	//----------------------------------------------------------------------//
	f.s6 = {};

		//------------------------------------------------//
		// s6.1. Аутентифицироваться через email и пароль //
		//------------------------------------------------//
		f.s6.auth_email_password = function(data, event){

			// 1] Если email пуст, завершить
			if(!self.m.s6.email_password.email()) {
				notify({target: document.body, msg: 'Email is empty', time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C58_auth_email_password",
				from: 		    "ajaxko",
				data: 		    {
					email: self.m.s6.email_password.email(),
					password: self.m.s6.email_password.password()
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					notify({msg: 'You are successfully authenticated!', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Перезагрузить страницу
          window.location.reload(true);

				},
				ok_2:         function(data, params){

					// 1] Сообщить
					notify({target: document.body, msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});

				}
			});

		};

		//-------------------------------------//
		// s6.2. Выйти из своей учётной записи //
		//-------------------------------------//
		f.s6.logout = function(data, event){

			// 1] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C59_logout",
				from: 		    "ajaxko",
				data: 		    {},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					notify({msg: 'You are successfully logged out!', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Перезагрузить страницу
          window.location.reload(true);

				}
			});

		};

		//------------------------------------------------//
		// s6.3. Аутентифицироваться через phone и пароль //
		//------------------------------------------------//
		f.s6.auth_phone_password = function(data, event){

			// 1] Если phone пуст, завершить
			if(!self.m.s6.phone_password.phone()) {
				notify({target: document.body, msg: 'Phone is empty', time: 10, fontcolor: 'RGB(200,50,50)'});
				return;
			}

			// 2] Отправить запрос
			ajaxko(self, {
				command: 	    "\\M5\\Commands\\C61_auth_phone_password",
				from: 		    "ajaxko",
				data: 		    {
					phone: self.m.s6.phone_password.phone(),
					password: self.m.s6.phone_password.password()
				},
				prejob:       function(config, data, event){},
				postjob:      function(data, params){},
				ok_0:         function(data, params){

					// 1] Сообщить, что пользователи успешно отвязаны
					notify({msg: 'You are successfully authenticated!', time: 5, fontcolor: 'RGB(50,120,50)'});

					// 2] Перезагрузить страницу
          window.location.reload(true);

				},
				ok_2:         function(data, params){

					// 1] Сообщить
					notify({target: document.body, msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});

				}
			});

		};

		//---------------------------------------//
		// s6.4. Аутентифицироваться через steam //
		//---------------------------------------//
		f.s6.auth_steam = function(data, event){

			var result = popupCenter('https://steamcommunity.com/openid/login?openid.ns=http://specs.openid.net/auth/2.0&openid.mode=checkid_setup&openid.return_to=http://easydrop.maxpain.pro/_oauth/steam?close%26b3GkXMPb4btsnQwnm&openid.realm=http://easydrop.maxpain.pro/&openid.identity=http://specs.openid.net/auth/2.0/identifier_select&openid.claimed_id=http://specs.openid.net/auth/2.0/identifier_select&state=eyJsb2dpblN0eWxlIjoicG9wdXAiLCJjcmVkZW50aWFsVG9rZW4iOiJiM0drWE1QYjRidHNuUXdubSIsImlzQ29yZG92YSI6ZmFsc2V9','steam','1024','768');




//			// 2] Отправить запрос
//			ajaxko(self, {
//				command: 	    "\\M5\\Commands\\C58_auth_email_password",
//				from: 		    "ajaxko",
//				data: 		    {
//					email: self.m.s6.email_password.email(),
//					password: self.m.s6.email_password.password()
//				},
//				prejob:       function(config, data, event){},
//				postjob:      function(data, params){},
//				ok_0:         function(data, params){
//
//					// 1] Сообщить, что пользователи успешно отвязаны
//					notify({msg: 'You are successfully authenticated!', time: 5, fontcolor: 'RGB(50,120,50)'});
//
//					// 2] Перезагрузить страницу
//          window.location.reload(true);
//
//				},
//				ok_2:         function(data, params){
//
//					// 1] Сообщить
//					notify({target: document.body, msg: data.data.errormsg, time: 10, fontcolor: 'RGB(200,50,50)'});
//
//				}
//			});



		};






return f; }};




























