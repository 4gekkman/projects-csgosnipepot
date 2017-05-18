/*//==============================================////
////																							////
////   j.js - главный js-файл шаблона документа 	////
////																							////
////==============================================////
//// 			        		 	    	   ////
//// 			    Оглавление  			 ////
//// 			         				       ////
////=============================//*/
/**
 *
 * 1. Принять и обработать данные с сервера
 *
 *   1.1. Пример...
 * 
 * 2. Создать экземпляр модели из m.js с функционалом из f.js
 * 3. Добавить в ko все необходимые кастомные связки
 * 4. Активизировать модель для нужных DOM-элементов
 *
 */

		
//-----------------------------//
// 1. Принять данные с сервера //
//-----------------------------//

	//-----------------------------------------------//
	// 1.1. Пример ... //
	//-----------------------------------------------//		

/*
    // 1] Подготовить массив для результата
    server4lo.mainmenu_groups_prepeared = [];

		// 2] Заполнить server4layout.mainmenu_groups_prepeared
    for(var i=0; i<server4lo.mainmenu_groups.length; i++) {

      // 2.1] Сформировать объект для добавления

				// Создать пустой объект
				var obj = {};

				// Добавить в obj свойства
				obj.id 									= ko.observable(server4lo.mainmenu_groups[i].id);
				obj.name 								= ko.observable(server4lo.mainmenu_groups[i].name);
				obj.is_on 							= ko.observable(server4lo.mainmenu_groups[i].is_on);
				obj.group_desc 					= ko.observable(server4lo.mainmenu_groups[i].group_desc);
				obj.group_position 			= ko.observable(server4lo.mainmenu_groups[i].group_position);

      // 2.2] Добавить этот объект в server4lo.mainmenu_groups_prepeared
      server4lo.mainmenu_groups_prepeared.push(ko.observable(obj))

    }
*/


//------------------------------------------------------------//
// 2. Создать экземпляр модели из m.js с функционалом из f.js //
//------------------------------------------------------------//
var layoutmodel = Object.create(LayoutModelProto).constructor(LayoutModelFunctions);


//---------------------------------------------------//
// 3. Добавить в ko все необходимые кастомные связки //
//---------------------------------------------------//

	// 1] Связка phoneRF для input'а для приема номера телефона в российском формате
	//ko.bindingHandlers.phoneRF = custom_bindings_for_ko.phoneRF;

	// 2] Связка phoneRF для input'а для приема номера телефона в российском формате
	//ko.bindingHandlers.birthday = custom_bindings_for_ko.birthday;


//----------------------------------------------------//
// 4. Активизировать модель для нужных DOM-элементов  //
//----------------------------------------------------//

	// 4.1. Подготовить кастомный биндинг для отмены биндинга на дочерние элементы //
	//-----------------------------------------------------------------------------//
	// - Чтобы потом к этим дочерним эл-там можно было applyBindings другую модель.
	// - Обычно применяется к контейнеру с контентом документа (чтобы потом применить к контенту модель D-пакета).
	ko.bindingHandlers.stopBindings = {
			init: function() {
					return { 'controlsDescendantBindings': true };
			}
	};

	// 4.2. Активизировать модель для DOM-элемента 'layoutmodel' //
	//-----------------------------------------------------------//
	ko.applyBindings(layoutmodel, document.getElementById('layoutmodel'));
