var LayoutModelProto={constructor:function(t){var e=this;return e.m={},e.f=Object.create(t).constructor(e),ko.computed(function(){ko.computedContext.isInitial()&&(e.websocket={})}),e.m.s0={},e.m.s0.txt_delay_save={},e.m.s0.txt_delay_save.gap=2e3,e.m.s0.txt_delay_save.lastupdate=ko.observable(Date.now()),e.m.s0.txt_delay_save.settimeoutid=ko.observable(),e.m.s0.txt_delay_save.is_unsaved_data=ko.observable(),ko.computed(function(){ko.computedContext.isInitial()&&addEvent(window,"beforeunload",function(t,a){if(1==e.m.s0.txt_delay_save.is_unsaved_data()){var s="В документе есть не сохранённые данные, его закрытие приведёт к их потере. Вы уверены, что хотите закрыть документ?";t.returnValue=s}},{})}),e.m.s0.ajax_counter=ko.observable(0),e.m.s0.ajax_timers={},e.m.s0.is_loadshield_on=ko.observable(0),ko.computed(function(){e.m.s0.ajax_counter()>0?e.m.s0.is_loadshield_on(1):e.m.s0.is_loadshield_on(0)}),e.m.s1={},e.m.s1.is_mainmenu_opened=ko.observable(0),ko.computed(function(){e.m.s1.indexes={}}),e.m.sN={},ko.computed(function(){e.m.sN.indexes={}}),ko.computed(function(){!ko.computedContext.isInitial()}),e}},LayoutModelFunctions={constructor:function(t){var e=this;return e.s0={},e.s0.sm_func=function(t){return showModal({header:"Ошибка",ok_name:"Закрыть",cancel_name:"",width:350,standard_css:"1",target:document.body,html:t,params:{},callback:function(t,e){}})},e.s0.txt_delay_save={},e.s0.txt_delay_save.use=function(e){if(t.m.s0.txt_delay_save.settimeoutid()&&clearTimeout(t.m.s0.txt_delay_save.settimeoutid()),+Date.now()-+t.m.s0.txt_delay_save.lastupdate()<+t.m.s0.txt_delay_save.gap){var a=setTimeout(e,t.m.s0.txt_delay_save.gap);return t.m.s0.txt_delay_save.settimeoutid(a),t.m.s0.txt_delay_save.lastupdate(Date.now()),t.m.s0.txt_delay_save.is_unsaved_data(1),1}t.m.s0.txt_delay_save.lastupdate(Date.now())},e.s0.txt_delay_save.block=function(){t.m.s0.txt_delay_save.is_unsaved_data(1)},e.s0.txt_delay_save.unblock=function(){t.m.s0.txt_delay_save.is_unsaved_data(0)},e.s1={},e.s1.someFunc=function(){},e}},layoutmodel=Object.create(LayoutModelProto).constructor(LayoutModelFunctions);
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm0uanMiLCJmLmpzIiwiai5qcyJdLCJuYW1lcyI6WyJMYXlvdXRNb2RlbFByb3RvIiwiY29uc3RydWN0b3IiLCJMYXlvdXRNb2RlbEZ1bmN0aW9ucyIsInNlbGYiLCJ0aGlzIiwibSIsImYiLCJPYmplY3QiLCJjcmVhdGUiLCJrbyIsImNvbXB1dGVkIiwiY29tcHV0ZWRDb250ZXh0IiwiaXNJbml0aWFsIiwid2Vic29ja2V0IiwiczAiLCJ0eHRfZGVsYXlfc2F2ZSIsImdhcCIsImxhc3R1cGRhdGUiLCJvYnNlcnZhYmxlIiwiRGF0ZSIsIm5vdyIsInNldHRpbWVvdXRpZCIsImlzX3Vuc2F2ZWRfZGF0YSIsImFkZEV2ZW50Iiwid2luZG93IiwiZXZlbnQiLCJwYXJhbXMiLCJtZXNzYWdlIiwicmV0dXJuVmFsdWUiLCJhamF4X2NvdW50ZXIiLCJhamF4X3RpbWVycyIsImlzX2xvYWRzaGllbGRfb24iLCJzMSIsImlzX21haW5tZW51X29wZW5lZCIsImluZGV4ZXMiLCJzTiIsInNtX2Z1bmMiLCJ0ZXh0Iiwic2hvd01vZGFsIiwiaGVhZGVyIiwib2tfbmFtZSIsImNhbmNlbF9uYW1lIiwid2lkdGgiLCJzdGFuZGFyZF9jc3MiLCJ0YXJnZXQiLCJkb2N1bWVudCIsImJvZHkiLCJodG1sIiwiY2FsbGJhY2siLCJhcmciLCJ1c2UiLCJzYXZlZnVuYyIsImNsZWFyVGltZW91dCIsInRpbWVySWQiLCJzZXRUaW1lb3V0IiwiYmxvY2siLCJ1bmJsb2NrIiwic29tZUZ1bmMiLCJsYXlvdXRtb2RlbCJdLCJtYXBwaW5ncyI6IkFBcURBLEdBQUFBLG1CQUFBQyxZQUFBLFNBQUFDLEdBV0EsR0FBQUMsR0FBQUMsSUF3UEEsT0FuUEFELEdBQUFFLEtBS0FGLEVBQUFHLEVBQUFDLE9BQUFDLE9BQUFOLEdBQUFELFlBQUFFLEdBS0FNLEdBQUFDLFNBQUEsV0FNQUQsR0FBQUUsZ0JBQUFDLGNBTUFULEVBQUFVLGdCQWdEQVYsRUFBQUUsRUFBQVMsTUFLQVgsRUFBQUUsRUFBQVMsR0FBQUMsa0JBUUFaLEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFDLElBQUEsSUFJQWIsRUFBQUUsRUFBQVMsR0FBQUMsZUFBQUUsV0FBQVIsR0FBQVMsV0FBQUMsS0FBQUMsT0FJQWpCLEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFNLGFBQUFaLEdBQUFTLGFBSUFmLEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFPLGdCQUFBYixHQUFBUyxhQU1BVCxHQUFBQyxTQUFBLFdBR0FELEdBQUFFLGdCQUFBQyxhQU1BVyxTQUFBQyxPQUFBLGVBQUEsU0FBQUMsRUFBQUMsR0FFQSxHQUFBLEdBQUF2QixFQUFBRSxFQUFBUyxHQUFBQyxlQUFBTyxrQkFBQSxDQUNBLEdBQUFLLEdBQUEscUhBQ0FGLEdBQUFHLFlBQUFELFVBVUF4QixFQUFBRSxFQUFBUyxHQUFBZSxhQUFBcEIsR0FBQVMsV0FBQSxHQU1BZixFQUFBRSxFQUFBUyxHQUFBZ0IsZUFLQTNCLEVBQUFFLEVBQUFTLEdBQUFpQixpQkFBQXRCLEdBQUFTLFdBQUEsR0FDQVQsR0FBQUMsU0FBQSxXQUVBUCxFQUFBRSxFQUFBUyxHQUFBZSxlQUFBLEVBQ0ExQixFQUFBRSxFQUFBUyxHQUFBaUIsaUJBQUEsR0FFQTVCLEVBQUFFLEVBQUFTLEdBQUFpQixpQkFBQSxLQWNBNUIsRUFBQUUsRUFBQTJCLE1BS0E3QixFQUFBRSxFQUFBMkIsR0FBQUMsbUJBQUF4QixHQUFBUyxXQUFBLEdBTUFULEdBQUFDLFNBQUEsV0FLQVAsRUFBQUUsRUFBQTJCLEdBQUFFLGFBdUJBL0IsRUFBQUUsRUFBQThCLE1BV0ExQixHQUFBQyxTQUFBLFdBS0FQLEVBQUFFLEVBQUE4QixHQUFBRCxhQWdCQXpCLEdBQUFDLFNBQUEsWUFHQUQsR0FBQUUsZ0JBQUFDLGNBY0FULElDelJBRCxzQkFBQUQsWUFBQSxTQUFBRSxHQUFBLEdBQUFHLEdBQUFGLElBcUhBLE9BN0dBRSxHQUFBUSxNQU1BUixFQUFBUSxHQUFBc0IsUUFBQSxTQUFBQyxHQUNBLE1BQUFDLFlBQ0FDLE9BQUEsU0FDQUMsUUFBQSxVQUNBQyxZQUFBLEdBQ0FDLE1BQUEsSUFDQUMsYUFBQSxJQUNBQyxPQUFBQyxTQUFBQyxLQUNBQyxLQUFBVixFQUNBWCxVQUNBc0IsU0FBQSxTQUFBQyxFQUFBdkIsUUFVQXBCLEVBQUFRLEdBQUFDLGtCQVFBVCxFQUFBUSxHQUFBQyxlQUFBbUMsSUFBQSxTQUFBQyxHQU9BLEdBSkFoRCxFQUFBRSxFQUFBUyxHQUFBQyxlQUFBTSxnQkFDQStCLGFBQUFqRCxFQUFBRSxFQUFBUyxHQUFBQyxlQUFBTSxpQkFHQUYsS0FBQUMsT0FBQWpCLEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFFLGNBQUFkLEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFDLElBQUEsQ0FHQSxHQUFBcUMsR0FBQUMsV0FBQUgsRUFBQWhELEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFDLElBWUEsT0FUQWIsR0FBQUUsRUFBQVMsR0FBQUMsZUFBQU0sYUFBQWdDLEdBR0FsRCxFQUFBRSxFQUFBUyxHQUFBQyxlQUFBRSxXQUFBRSxLQUFBQyxPQUdBakIsRUFBQUUsRUFBQVMsR0FBQUMsZUFBQU8sZ0JBQUEsR0FHQSxFQVFBbkIsRUFBQUUsRUFBQVMsR0FBQUMsZUFBQUUsV0FBQUUsS0FBQUMsUUFXQWQsRUFBQVEsR0FBQUMsZUFBQXdDLE1BQUEsV0FDQXBELEVBQUFFLEVBQUFTLEdBQUFDLGVBQUFPLGdCQUFBLElBUUFoQixFQUFBUSxHQUFBQyxlQUFBeUMsUUFBQSxXQUNBckQsRUFBQUUsRUFBQVMsR0FBQUMsZUFBQU8sZ0JBQUEsSUFTQWhCLEVBQUEwQixNQU1BMUIsRUFBQTBCLEdBQUF5QixTQUFBLGFBU0FuRCxJQ3pGQW9ELFlBQUFuRCxPQUFBQyxPQUFBUixrQkFBQUMsWUFBQUMiLCJmaWxlIjoiai5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0vLy8vXG4vLy8vXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8vL1xuLy8vLyAgIG0uanMgLSDQutC70LjQtdC90YLRgdC60LDRjyDQvNC+0LTQtdC70Ywg0YjQsNCx0LvQvtC90LAg0LTQvtC60YPQvNC10L3RgtCwXHRcdC8vLy9cbi8vLy9cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLy8vXG4vLy8vPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09Ly8vL1xuLy8vLyBcdFx0XHQgICAgICAgIFx0XHQgXHQgICAgXHQgICAvLy8vXG4vLy8vIFx0XHRcdCAgICDQntCz0LvQsNCy0LvQtdC90LjQtSAgXHRcdFx0IC8vLy9cbi8vLy8gXHRcdFx0ICAgICAgICAgXHRcdFx0XHQgICAgICAgLy8vL1xuLy8vLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09Ly8qL1xuLyoqXG4gKlxuICpcbiAqICDQkC4g0KHRgtCw0YDRgtC+0LLQsNGPINC/0L7QtNCz0L7RgtC+0LLQutCwXG4gKlxuICogICAg0JAxLiDQodC+0YXRgNCw0L3QuNGC0Ywg0YHRgdGL0LvQutGDINC90LAg0L7QsdGK0LXQutGCLdC80L7QtNC10LvRjCDQsiBzZWxmXG4gKiAgICDQkDIuINCf0L7QtNCz0L7RgtC+0LLQuNGC0Ywg0L7QsdGK0LXQutGCLdC60L7QvdGC0LXQudC90LXRgCDQtNC70Y8g0LLRgdC10YUg0YHQstC+0LnRgdGC0LIg0LzQvtC00LXQu9C4XG4gKiAgICDQkDMuINCX0LDQs9GA0YPQt9C40YLRjCDQsiDQvNC+0LTQtdC70Ywg0LLQtdGB0Ywg0LXRkSDRhNGD0L3QutGG0LjQvtC90LDQuyDQuNC3IGYuanNcbiAqICAgINCQNC4g0J/QvtC00LrQu9GO0YfQtdC90LjQtSDQuiB3ZWJzb2NrZXQt0YHQtdGA0LLQtdGA0LDQvCwg0L3QsNC30L3QsNGH0LXQvdC40LUg0L7QsdGA0LDQsdC+0YLRh9C40LrQvtCyINC00LvRjyDQstGF0L7QtNGP0YnQuNGFINGB0L7QvtCx0YnQtdC90LjQuVxuICpcbiAqXHRzMC4g0JHQuNCx0LvQuNC+0YLQtdC60LAg0LTQsNC90L3Ri9GFLCDQtNC+0YHRgtGD0L/QvdGL0YUg0LLRgdC10Lwg0L/RgNC+0YfQuNC8INC80L7QtNC10LvRj9C8XG4gKlxuICogXHRcdHMwLjEuINCe0LHRitC10LrRgi3QutC+0L3RgtC10LnQvdC10YAg0LTQu9GPINCy0YHQtdGFINGB0LLQvtC50YHRgtCyINC80L7QtNC10LvQuFxuICogXHQgIHMwLjIuINCc0L7QtNC10LvRjCBcItC80LXRhdCw0L3QuNC30LzQsCDQvtGC0LvQvtC20LXQvdC90L7Qs9C+INGB0L7RhdGA0LDQvdC10L3QuNGPINC00LvRjyDRgtC10LrRgdGC0L7QstGL0YUg0L/QvtC70LXQuVwiXG4gKlx0XHRzMC4zLiDQodGH0ZHRgtGH0LjQuiDQvtC20LjQtNCw0Y7RidC40YUg0L7RgtCy0LXRgtCwIGFqYXgt0LfQsNC/0YDQvtGB0L7QslxuICpcdFx0czAuNC4g0KLQsNC50LzQtdGA0Ysg0LTQu9GPINGE0YPQvdC60YbQuNC5LCDQvtGB0YPRidC10YHRgtCy0LvRj9GO0YnQuNGFIGFqYXgt0LfQsNC/0YDQvtGB0YtcbiAqXHRcdHMwLjUuINCS0LjQtNC10L0g0LvQuCDRidC40YIgXCLQuNC00ZHRgiBhamF4LdC30LDQv9GA0L7RgVwiXG4gKlxuICogIHMxLiDQnNC+0LTQtdC70YwgW9GC0LDQutCw0Y8t0YLQvl1cbiAqXG4gKlx0XHRzMS4xLiDQntCx0YrQtdC60YIt0LrQvtC90YLQtdC50L3QtdGAINC00LvRjyDQstGB0LXRhSDRgdCy0L7QudGB0YLQsiDQvNC+0LTQtdC70LhcbiAqICAgIHMxLjIuIC4uLlxuICpcbiAqICBzTi4g0JTQsNC90L3Ri9C1LCDQutC+0YLQvtGA0YvQvCDQtNC+0YHRgtGD0L/QvdGLINCy0YHQtSDQv9GA0L7Rh9C40LUg0LTQsNC90L3Ri9C1XG4gKlxuICogICAgc04uMS4g0J7QsdGK0LXQutGCLdC60L7QvdGC0LXQudC90LXRgCDQtNC70Y8g0LLRgdC10YUg0YHQstC+0LnRgdGC0LIg0LzQvtC00LXQu9C4XG4gKiAgICBzTi4yLlxuICogXHRcdHNOLm4uINCY0L3QtNC10LrRgdGLINC4INCy0YvRh9C40YHQu9GP0LXQvNGL0LUg0LfQvdCw0YfQtdC90LjRj1xuICpcbiAqICBYLiDQn9C+0LTQs9C+0YLQvtCy0LrQsCDQuiDQt9Cw0LLQtdGA0YjQtdC90LjRjlxuICpcbiAqIFx0XHRYMS4g0KHQtdGA0LLQuNGBINC/0YDQvtCy0LDQudC00LXRgCDQutC70LjQtdC90YLRgdC60L7QuSDQvNC+0LTQtdC70LhcbiAqICAgIFgyLiDQktC10YDQvdGD0YLRjCDRgdGB0YvQu9C60YMgc2VsZiDQvdCwINC+0LHRitC10LrRgi3QvNC+0LTQtdC70YxcbiAqXG4gKlxuICpcbiAqL1xuXG5cbi8vPT09PT09PT09PT09PT09PT09PT0vL1xuLy8gXHRcdFx0ICAgICAgICBcdFx0IFx0Ly9cbi8vIFx0XHRcdCDQnNC+0LTQtdC70YwgIFx0XHRcdC8vXG4vLyBcdFx0XHQgICAgICAgICBcdFx0XHQvL1xuLy89PT09PT09PT09PT09PT09PT09PS8vXG52YXIgTGF5b3V0TW9kZWxQcm90byA9IHsgY29uc3RydWN0b3I6IGZ1bmN0aW9uKExheW91dE1vZGVsRnVuY3Rpb25zKSB7XG5cdFxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gXHRcdFx0ICAgICAgICBcdFx0IFx0ICAgICAgICAgICAgICAgICAgLy9cblx0Ly8gXHRcdFx0INCQLiDQodGC0LDRgNGC0L7QstCw0Y8g0L/QvtC00LPQvtGC0L7QstC60LAgIFx0XHRcdC8vXG5cdC8vIFx0XHRcdCAgICAgICAgIFx0XHRcdCAgICAgICAgICAgICAgICAgIC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vINCQMS4g0KHQvtGF0YDQsNC90LjRgtGMINGB0YHRi9C70LrRgyDQvdCwINC+0LHRitC10LrRgi3QvNC+0LTQtdC70Ywg0LIgc2VsZiAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHR2YXIgc2VsZiA9IHRoaXM7XG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8g0JAyLiDQn9C+0LTQs9C+0YLQvtCy0LjRgtGMINC+0LHRitC10LrRgi3QutC+0L3RgtC10LnQvdC10YAg0LTQu9GPINCy0YHQtdGFINGB0LLQvtC50YHRgtCyINC80L7QtNC10LvQuCAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRzZWxmLm0gPSB7fTtcblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vINCQMy4g0JfQsNCz0YDRg9C30LjRgtGMINCyINC80L7QtNC10LvRjCDQstC10YHRjCDQtdGRINGE0YPQvdC60YbQuNC+0L3QsNC7INC40LcgZi5qcyAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdHNlbGYuZiA9IE9iamVjdC5jcmVhdGUoTGF5b3V0TW9kZWxGdW5jdGlvbnMpLmNvbnN0cnVjdG9yKHNlbGYpO1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyDQkDQuINCf0L7QtNC60LvRjtGH0LXQvdC40LUg0Logd2Vic29ja2V0LdGB0LXRgNCy0LXRgNCw0LwsINC90LDQt9C90LDRh9C10L3QuNC1INC+0LHRgNCw0LHQvtGC0YfQuNC60L7QsiDQtNC70Y8g0LLRhdC+0LTRj9GJ0LjRhSDRgdC+0L7QsdGJ0LXQvdC40LkgLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdGtvLmNvbXB1dGVkKGZ1bmN0aW9uKCl7XG5cblxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0Ly8g0JA0LjEuINCS0YvQv9C+0LvQvdGP0YLRjCDQutC+0LQg0L3QuNC20LUg0LvQuNGI0YwgMSDRgNCw0LcsINC/0YDQuCDQt9Cw0LPRgNGD0LfQutC1INC00L7QutGD0LzQtdC90YLQsCAvL1xuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0aWYoIWtvLmNvbXB1dGVkQ29udGV4dC5pc0luaXRpYWwoKSkgcmV0dXJuO1xuXG5cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdC8vINCQNC4yLiDQn9C+0LTQutC70Y7Rh9C10L3QuNGPINC6IHdlYnNvY2tldC3RgdC10YDQstC10YDQsNC8IC8vXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRzZWxmLndlYnNvY2tldCA9IHt9O1xuXG5cdFx0XHQvLyDQkDQuMi4xLiDQn9C+0LTQutC70Y7Rh9C10L3QuNC1IHdzMSAvL1xuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cbi8vXHRcdFx0c2VsZi53ZWJzb2NrZXQud3MxID0gaW8oJ2h0dHA6Ly8nK3NlcnZlci5zZXR0aW5ncy53ZWJzb2NrZXRfc2VydmVyX2lwKyc6NjAwMScpO1xuXG5cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyDQkDQuMy4g0J3QsNC30L3QsNGH0LXQvdC40LUg0L7QsdGA0LDQsdC+0YLRh9C40LrQvtCyINGB0L7QvtCx0YnQtdC90LjQuSDRgSB3ZWJzb2NrZXQt0YHQtdGA0LLQtdGA0L7QsiAvL1xuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdFx0XHQvLyDQkDQuMy4xLiDQlNC+0LHQsNCy0LvQtdC90LjQtSDQsiDQuNC90YLQtdGA0YTQtdC50YEg0L3QvtCy0YvRhSDQt9Cw0L/QuNGB0LXQuSwg0L/QvtGB0YLRg9C/0LjQstGI0LjRhSDQsiDQu9C+0LMgLy9cblx0XHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdFx0Ly9cblx0XHRcdC8vIFx0PiDQodC10YDQstC10YBcblx0XHRcdC8vIFx0ICAtIHdzMVxuXHRcdFx0Ly9cblx0XHRcdC8vIFx0PiDQmtCw0L3QsNC7XG5cdFx0XHQvLyBcdCAgLSBtMjpNMlxcXFxEb2N1bWVudHNcXFxcTWFpblxcXFxFdmVudHNcXFxcRTFfYnJvYWRjYXN0XG5cdFx0XHQvL1xuXHRcdFx0Ly8gXHQ+INCe0L/QuNGB0LDQvdC40LVcblx0XHRcdC8vIFx0ICAtINCSINC70L7Qsywg0LIg0JHQlCDQvNC+0LTRg9C70Y8gTTIsINC80L7Qs9GD0YIg0L/QvtGB0YLRg9C/0LDRgtGMINC90L7QstGL0LUg0YHQvtC+0LHRidC10L3QuNGPLlxuXHRcdFx0Ly8gXHQgIC0g0JfQsNC00LDRh9CwINCyINGC0L7QvCwg0YfRgtC+0LHRiyDQvtC90Lgg0YHRgNCw0LfRgyDQttC1INC+0YLQvtCx0YDQsNC20LDQu9C40YHRjCDQsiDQvtGC0LrRgNGL0YLRi9GFINC40L3RgtC10YDRhNC10LnRgdCw0YUg0LvQvtCz0LAuXG5cdFx0XHQvLyBcdCAgLSDQlNC70Y8g0Y3RgtC+0LPQviDRgdC70YPRiNCw0LXQvCDQstGL0YjQtdGD0LrQsNC30LDQvdC90Ysg0LrQsNC90LDQuywg0Lgg0L/RgNC40L3QuNC80LDQtdC8INC40Lcg0L3QtdCz0L4g0YHQvtC+0LHRidC10L3QuNGPLlxuXHRcdFx0Ly8gXHQgIC0g0J7QtNC90L4g0YHQvtC+0LHRidC10L3QuNC1INC+0LfQvdCw0YfQsNC10YIg0L7QtNC90YMg0L3QvtCy0YPRjiDQt9Cw0L/QuNGB0Ywg0LIg0LvQvtCzLlxuXHRcdFx0Ly8gXHQgIC0g0KHQvtC+0LHRidC10L3QuNGPINGB0L7QtNC10YDQttCw0YIg0YLQtdC60YHRgiDQtNC+0LHQsNCy0LvRj9C10LzQvtCz0L4g0LIg0LvQvtCzINGB0L7QvtCx0YnQtdC90LjRjywg0Lgg0YHQv9C40YHQvtC6INC10LPQviDRgtC10LPQvtCyLlxuXHRcdFx0Ly8gXHQgIC0g0JIg0LrQsNGH0LXRgdGC0LLQtSDQvtCx0YDQsNCx0L7RgtGH0LjQutCwLCDQvdCw0LfQvdCw0YfQsNC10Lwg0YTRg9C90LrRhtC40Y4sINC60L7RgtC+0YDQsNGPINC00L7QsdCw0LLQu9GP0LXRgiDRgdC+0L7QsdGJ0LXQvdC40LUg0LIg0LjQvdGC0LXRgNGE0LXQudGBLlxuXHRcdFx0Ly9cbi8vXHRcdFx0c2VsZi53ZWJzb2NrZXQud3MxLm9uKFwibTI6TTJcXFxcRG9jdW1lbnRzXFxcXE1haW5cXFxcRXZlbnRzXFxcXEUxX2Jyb2FkY2FzdFwiLCBmdW5jdGlvbihtZXNzYWdlKSB7XG4vL1xuLy9cdFx0XHRcdC8vINCS0YvQt9Cy0LDRgtGMINGE0YPQvdC60YbQuNGOLdC+0LHRgNCw0LHQvtGC0YfQuNC6INCy0YXQvtC00Y/RidC40YUg0YfQtdGA0LXQtyDRjdGC0L7RgiDQutCw0L3QsNC7INGB0L7QvtCx0YnQtdC90LjQuVxuLy9cdFx0XHRcdHNlbGYuZi5zMi5sb2dfd2Vic29ja2V0X2hhbmRsZXIobWVzc2FnZSk7XG4vL1xuLy9cdFx0XHR9KTtcblxuXG5cdH0pO1xuXG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBcdFx0XHQgICAgICAgIFx0XHQgXHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9cblx0Ly8gXHRcdFx0IHMwLiDQkdC40LHQu9C40L7RgtC10LrQsCDQtNCw0L3QvdGL0YUsINC00L7RgdGC0YPQv9C90YvRhSDQstGB0LXQvCDQv9GA0L7Rh9C40Lwg0LzQvtC00LXQu9GP0LwgIFx0XHRcdC8vXG5cdC8vIFx0XHRcdCAgICAgICAgIFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBzMC4xLiDQntCx0YrQtdC60YIt0LrQvtC90YLQtdC50L3QtdGAINC00LvRjyDQstGB0LXRhSDRgdCy0L7QudGB0YLQsiDQvNC+0LTQtdC70LggLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRzZWxmLm0uczAgPSB7fTtcblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIHMwLjIuINCc0L7QtNC10LvRjCBcItC80LXRhdCw0L3QuNC30LzQsCDQvtGC0LvQvtC20LXQvdC90L7Qs9C+INGB0L7RhdGA0LDQvdC10L3QuNGPINC00LvRjyDRgtC10LrRgdGC0L7QstGL0YUg0L/QvtC70LXQuVwiIC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0c2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlID0ge307XG5cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdC8vIHMwLjIuMS4g0KHQstC+0LnRgdGC0LLQsCDQvNC+0LTQtdC70LggLy9cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdFx0XHQvLyAxXSDQp9C10YDQtdC3INGB0LrQvtC70YzQutC+INC80YEg0L/QvtGB0LvQtSDQv9C+0YHQu9C10LQu0YDQtdC00LDQutGCLiDQstGL0L/QvtC70L3Rj9GC0Ywg0YHQvtGF0YDQsNC90LXQvdC40LVcblx0XHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdFx0c2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLmdhcCA9IDIwMDA7XG5cblx0XHRcdC8vIDJdINCh0LLQvtC50YHRgtCy0L4g0LTQu9GPINGB0L7RhdGA0LDQvdC10L3QuNGPIHRpbWVzdGFtcCDQv9C+0YHQu9C10LQu0YDQtdC00LDQutGC0LjRgNC+0LLQsNC90LjRjyAvL1xuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0XHRzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUubGFzdHVwZGF0ZSA9IGtvLm9ic2VydmFibGUoRGF0ZS5ub3coKSk7XG5cblx0XHRcdC8vIDNdINCh0LLQvtC50YHRgtCy0L4g0LTQu9GPINGB0L7RhdGA0LDQvdC10L3QuNGPIGlkINC/0L7RgdC70LXQtNC90LXQs9C+INGD0YHRgtCw0L3QvtCy0LvQtdC90L3QvtCz0L4g0YLQsNC50LzQtdGA0LAgLy9cblx0XHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0XHRzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUuc2V0dGltZW91dGlkID0ga28ub2JzZXJ2YWJsZSgpO1xuXG5cdFx0XHQvLyA0XSDQldGB0YLRjCDQu9C4INC90LUg0YHQvtGF0YDQsNC90ZHQvdC90YvQtSDQtNCw0L3QvdGL0LUgKNC00LvRjyDQv9GA0LXQtNC+0YLQstGA0LDRidC10L3QuNGPINC30LDQutGA0YvRgtC40Y8g0LTQvtC60YPQvNC10L3RgtCwKSAvL1xuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRcdHNlbGYubS5zMC50eHRfZGVsYXlfc2F2ZS5pc191bnNhdmVkX2RhdGEgPSBrby5vYnNlcnZhYmxlKCk7XG5cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyBzMC4yLjIuINCd0LDQt9C90LDRh9C10L3QuNC1INC+0LHRgNCw0LHQvtGC0YfQuNC60LAgLy9cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyAtINC+0L0g0LHRg9C00LXRgiDQt9Cw0L/RgNCw0YjQuNCy0LDRgtGMIGNvbmZpcm0sINC10YHQu9C4INC10YHRgtGMINC90LUg0YHQvtGF0YDQsNC90ZHQvdC90YvQtSDQtNCw0L3QvdGL0LVcblx0XHRrby5jb21wdXRlZChmdW5jdGlvbigpe1xuXG5cdFx0XHQvLyDQldGB0LvQuCDRjdGC0L4g0L3QtSDQv9C10YDQstGL0Lkg0LfQsNC/0YPRgdC6LCDQt9Cw0LLQtdGA0YjQuNGC0Yxcblx0XHRcdGlmKCFrby5jb21wdXRlZENvbnRleHQuaXNJbml0aWFsKCkpIHJldHVybjtcblxuXHRcdFx0Ly8g0J3QsNC30L3QsNGH0LjRgtGMINGB0L7QsdGL0YLQuNGOIGJlZm9yZXVubG9hZCDRhNGD0L3QutGG0LjRjiDQvtCx0YDQsNCx0L7RgtGH0LjQulxuXHRcdFx0Ly8gLSDQntC90LAg0LTQvtC70LbQvdCwINC/0YDQvtCy0LXRgNGP0YLRjCDRgdCy0L7QudGB0YLQstC+IHNlbGYubS5zMC5zYXZlLmlzX3Vuc2F2ZWRfZGF0YSgpXG5cdFx0XHQvLyAtINCV0YHQu9C4INC+0L3QviDRgNCw0LLQvdC+IDEsINGC0L4g0YEg0L/QvtC80L7RidGM0Y4gY29uZmlybSDRg9Cy0LXQtNC+0LzQu9GP0YLRjCDQv9C+0LvRjNC30L7QstCw0YLQtdC70Y8uXG5cdFx0XHQvLyAgINC+INCy0L7Qt9C80L7QttC90L7QuSDQv9C+0YLQtdGA0LUg0LTQsNC90L3Ri9GFINC/0YDQuCDQstGL0YXQvtC00LUuXG5cdFx0XHRhZGRFdmVudCh3aW5kb3csICdiZWZvcmV1bmxvYWQnLCBmdW5jdGlvbihldmVudCwgcGFyYW1zKXtcblxuXHRcdFx0XHRpZihzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUuaXNfdW5zYXZlZF9kYXRhKCkgPT0gMSkge1xuXHRcdFx0XHRcdHZhciBtZXNzYWdlID0gXCLQkiDQtNC+0LrRg9C80LXQvdGC0LUg0LXRgdGC0Ywg0L3QtSDRgdC+0YXRgNCw0L3RkdC90L3Ri9C1INC00LDQvdC90YvQtSwg0LXQs9C+INC30LDQutGA0YvRgtC40LUg0L/RgNC40LLQtdC00ZHRgiDQuiDQuNGFINC/0L7RgtC10YDQtS4g0JLRiyDRg9Cy0LXRgNC10L3Riywg0YfRgtC+INGF0L7RgtC40YLQtSDQt9Cw0LrRgNGL0YLRjCDQtNC+0LrRg9C80LXQvdGCP1wiO1xuXHRcdFx0XHRcdGV2ZW50LnJldHVyblZhbHVlID0gbWVzc2FnZTtcblx0XHRcdFx0fVxuXG5cdFx0XHR9LCB7fSk7XG5cblx0XHR9KTtcblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBzMC4zLiDQodGH0ZHRgtGH0LjQuiDQvtC20LjQtNCw0Y7RidC40YUg0L7RgtCy0LXRgtCwIGFqYXgt0LfQsNC/0YDQvtGB0L7QsiAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRzZWxmLm0uczAuYWpheF9jb3VudGVyID0ga28ub2JzZXJ2YWJsZSgwKTtcblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gczAuNC4g0KLQsNC50LzQtdGA0Ysg0LTQu9GPINGE0YPQvdC60YbQuNC5LCDQvtGB0YPRidC10YHRgtCy0LvRj9GO0YnQuNGFIGFqYXgt0LfQsNC/0YDQvtGB0YsgLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIC0g0KEg0LjRhSDQv9C+0LzQvtGJ0YzRjiDQvNC+0LbQvdC+INC40LPQvdC+0YDQuNGA0L7QstCw0YLRjCDRg9GB0YLQsNGA0LXQstGI0LjQtSBhamF4LdC+0YLQstC10YLRiy5cblx0c2VsZi5tLnMwLmFqYXhfdGltZXJzID0ge307XG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBzMC41LiDQktC40LTQtdC9INC70Lgg0YnQuNGCIFwi0LjQtNGR0YIgYWpheC3Qt9Cw0L/RgNC+0YFcIiAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdHNlbGYubS5zMC5pc19sb2Fkc2hpZWxkX29uID0ga28ub2JzZXJ2YWJsZSgwKTtcblx0a28uY29tcHV0ZWQoZnVuY3Rpb24oKXtcblxuXHRcdGlmKHNlbGYubS5zMC5hamF4X2NvdW50ZXIoKSA+IDApXG5cdFx0XHRzZWxmLm0uczAuaXNfbG9hZHNoaWVsZF9vbigxKTtcblx0XHRlbHNlXG5cdFx0XHRzZWxmLm0uczAuaXNfbG9hZHNoaWVsZF9vbigwKTtcblxuXHR9KTtcblxuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBcdFx0XHQgICAgICAgIFx0XHQgXHQgICAgICAgICAgICAgICAvL1xuXHQvLyBcdFx0XHQgczEuINCc0L7QtNC10LvRjCDQs9C70LDQstC90L7Qs9C+INC80LXQvdGOXHRcdCAvL1xuXHQvLyBcdFx0XHQgICAgICAgICBcdFx0XHQgICAgICAgICAgICAgICAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIHMxLjEuINCe0LHRitC10LrRgi3QutC+0L3RgtC10LnQvdC10YAg0LTQu9GPINCy0YHQtdGFINGB0LLQvtC50YHRgtCyINC80L7QtNC10LvQuCAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdHNlbGYubS5zMSA9IHt9O1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvLyBzMS4yLiAuLi4gLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdHNlbGYubS5zMS5pc19tYWlubWVudV9vcGVuZWQgPSBrby5vYnNlcnZhYmxlKDApO1xuXG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIHMxLm4uINCY0L3QtNC10LrRgdGLINC4INCy0YvRh9C40YHQu9GP0LXQvNGL0LUg0LfQvdCw0YfQtdC90LjRjyAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0a28uY29tcHV0ZWQoZnVuY3Rpb24oKXtcblxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdC8vIHMxLm4uMS4g0J7QsdGK0LXQutGCLdC60L7QvdGC0LXQudC90LXRgCDQtNC70Y8g0LjQvdC00LXQutGB0L7QsiDQuCDQstGL0YfQuNGB0LvRj9C10LzRi9GFINC30L3QsNGH0LXQvdC40LkgLy9cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRzZWxmLm0uczEuaW5kZXhlcyA9IHt9O1xuXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdC8vIHMxLm4uMi4gLi4uIC8vXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cblxuXHR9KTtcblxuXG5cblxuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gXHRcdFx0ICAgICAgICBcdFx0IFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vXG5cdC8vIFx0XHRcdCBzTi4g0JTQsNC90L3Ri9C1LCDQutC+0YLQvtGA0YvQvCDQtNC+0YHRgtGD0L/QvdGLINCy0YHQtSDQv9GA0L7Rh9C40LUg0LTQsNC90L3Ri9C1ICBcdFx0XHQvL1xuXHQvLyBcdFx0XHQgICAgICAgICBcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gc04uMS4g0J7QsdGK0LXQutGCLdC60L7QvdGC0LXQudC90LXRgCDQtNC70Y8g0LLRgdC10YUg0YHQstC+0LnRgdGC0LIg0LzQvtC00LXQu9C4IC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0c2VsZi5tLnNOID0ge307XG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gc04uMi4gXHQgLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblxuXG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIHNOLm4uINCY0L3QtNC10LrRgdGLINC4INCy0YvRh9C40YHQu9GP0LXQvNGL0LUg0LfQvdCw0YfQtdC90LjRjyAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0a28uY29tcHV0ZWQoZnVuY3Rpb24oKXtcblxuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyBzTi5uLjEuINCe0LHRitC10LrRgi3QutC+0L3RgtC10LnQvdC10YAg0LTQu9GPINC40L3QtNC10LrRgdC+0LIgLy9cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0c2VsZi5tLnNOLmluZGV4ZXMgPSB7fTtcblxuXG5cdH0pO1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIFx0XHRcdCAgICAgICAgXHRcdCBcdCAgICAgICAgICAgICAgICAgICAgLy9cblx0Ly8gXHRcdFx0IFguINCf0L7QtNCz0L7RgtC+0LLQutCwINC6INC30LDQstC10YDRiNC10L3QuNGOICBcdFx0XHQvL1xuXHQvLyBcdFx0XHQgICAgICAgICBcdFx0XHQgICAgICAgICAgICAgICAgICAgIC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gWDEuINCh0LXRgNCy0LjRgSDQv9GA0L7QstCw0LnQtNC10YAg0LrQu9C40LXQvdGC0YHQutC+0Lkg0LzQvtC00LXQu9C4IC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIC0g0JrQvtC0INC30LTQtdGB0Ywg0LLRi9C/0L7Qu9C90Y/QtdGC0YHRjyDQu9C40YjRjCAxINGA0LDQtywg0L/RgNC4INC+0YLQutGA0YvRgtC40Lgg0LTQvtC60YPQvNC10L3RgtCwINCyINCx0YDQsNGD0LfQtdGA0LUuXG5cdC8vIC0g0J7RgtC70LjRh9C90L7QtSDQvNC10YHRgtC+LCDRgdC60LDQttC10LwsINC00LvRjyDQvdCw0LfQvdCw0YfQtdC90LjRjyDQvtCx0YDQsNCx0L7RgtGH0LjQutC+0LIg0YHQvtCx0YvRgtC40LkuXG5cdGtvLmNvbXB1dGVkKGZ1bmN0aW9uKCl7XG5cblx0XHQvLyDQldGB0LvQuCDRjdGC0L4g0L3QtSDQv9C10YDQstGL0Lkg0LfQsNC/0YPRgdC6LCDQt9Cw0LLQtdGA0YjQuNGC0Yxcblx0XHRpZigha28uY29tcHV0ZWRDb250ZXh0LmlzSW5pdGlhbCgpKSByZXR1cm47XG5cbi8vXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuLy9cdFx0Ly8gWDEuMS4g0KHQutGA0YvQstCw0YLRjCDQs9C70LDQstC90L7QtSDQvNC10L3RjiDQv9GA0Lgg0LrQu9C40LrQtSDQv9C+INGN0Lst0YLRgyDRgSBpZD09Y29udGVudCAvL1xuLy9cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG4vL1x0XHRhZGRFdmVudChkb2N1bWVudCwgJ2NsaWNrJywgc2VsZi5mLnMxLm1lbnVzdGF0ZS5sdmwxLmNsb3NlLCB7fSk7XG5cblxuXG5cdH0pO1xuXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gWDIuINCS0LXRgNC90YPRgtGMINGB0YHRi9C70LrRgyBzZWxmINC90LAg0L7QsdGK0LXQutGCLdC80L7QtNC10LvRjCAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdHJldHVybiBzZWxmO1xuXG5cbn19O1x0Ly8g0LrQvtC90LXRhiDQvNC+0LTQtdC70LhcblxuXG5cblxuXG5cblxuXG5cbiIsIi8qLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0vLy8vXG4vLy8vXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCAgLy8vL1xuLy8vLyAgIGouanMgLSDRhNGD0L3QutGG0LjQvtC90LDQuyDQvNC+0LTQtdC70Lgg0YjQsNCx0LvQvtC90LAg0LTQvtC60YPQvNC10L3RgtCwXHRcdC8vLy9cbi8vLy9cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLy8vXG4vLy8vPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09Ly8vL1xuLy8vLyBcdFx0XHQgICAgICAgIFx0XHQgXHQgICAgXHQgICAvLy8vXG4vLy8vIFx0XHRcdCAgICDQntCz0LvQsNCy0LvQtdC90LjQtSAgXHRcdFx0IC8vLy9cbi8vLy8gXHRcdFx0ICAgICAgICAgXHRcdFx0XHQgICAgICAgLy8vL1xuLy8vLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09Ly8qL1xuLyoqXG4gKlxuICogIHMwLiDQpNGD0L3QutGG0LjQvtC90LDQuywg0LTQvtGB0YLRg9C/0L3Ri9C5INCy0YHQtdC80YMg0L7RgdGC0LDQu9GM0L3QvtC80YMg0YTRg9C90LrRhtC40L7QvdCw0LvRg1xuICpcbiAqXHRcdGYuczAuc21fZnVuY1x0XHRcdFx0XHRcdFx0XHRcdHwgczAuMS4g0J/QvtC60LDQt9GL0LLQsNC10YIg0LzQvtC00LDQu9GM0L3QvtC1INC+0LrQvdC+INGBIHRleHQsINC30LDQs9C+0LvQvtCy0LrQvtC8IFwi0L7RiNC40LHQutCwXCIg0Lgg0LrQvdC+0L/QutC+0LkgXCLQt9Cw0LrRgNGL0YLRjFwiXG4gKlx0XHRmLnMwLnR4dF9kZWxheV9zYXZlICAgICAgICAgICB8IHMwLjIuINCk0YPQvdC60YbQuNC+0L3QsNC7IFwi0LzQtdGF0LDQvdC40LfQvNCwINC+0YLQu9C+0LbQtdC90L3QvtCz0L4g0YHQvtGF0YDQsNC90LXQvdC40Y8g0LTQu9GPINGC0LXQutGB0YLQvtCy0YvRhSDQv9C+0LvQtdC5XCJcbiAqXG4gKiAgczEuINCk0YPQvdC60YbQuNC+0L3QsNC7IFvRgtCw0LrQvtC5LdGC0L5dXG4gKlxuICogXHRcdGYuczEuc29tZUZ1bmNcdFx0XHRcdFx0XHRcdFx0ICB8IHMxLjEuINCU0LXQu9Cw0LXRgiDRgtC+LdGC0L5cbiAqXG4gKlxuICpcbiAqXG4gKi9cblxuXG4vLz09PT09PT09PT09PT09PT09PT09PT09PS8vXG4vLyBcdFx0XHQgICAgICAgIFx0XHQgXHQgICAgLy9cbi8vIFx0XHRcdCDQpNGD0L3QutGG0LjQvtC90LDQuyAgXHRcdFx0Ly9cbi8vIFx0XHRcdCAgICAgICAgIFx0XHRcdCAgICAvL1xuLy89PT09PT09PT09PT09PT09PT09PS0tLS0vL1xudmFyIExheW91dE1vZGVsRnVuY3Rpb25zID0geyBjb25zdHJ1Y3RvcjogZnVuY3Rpb24oc2VsZikgeyB2YXIgZiA9IHRoaXM7XG5cblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gXHRcdFx0ICAgICAgICBcdFx0IFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9cblx0Ly8gXHRcdFx0IHMwLiDQpNGD0L3QutGG0LjQvtC90LDQuywg0LTQvtGB0YLRg9C/0L3Ri9C5INCy0YHQtdC80YMg0L7RgdGC0LDQu9GM0L3QvtC80YMg0YTRg9C90LrRhtC40L7QvdCw0LvRgyBcdFx0XHQvL1xuXHQvLyBcdFx0XHQgICAgICAgICBcdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Zi5zMCA9IHt9O1xuXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyBzMC4xLiDQn9C+0LrQsNC30YvQstCw0LXRgiDQvNC+0LTQsNC70YzQvdC+0LUg0L7QutC90L4g0YEgdGV4dCwg0LfQsNCz0L7Qu9C+0LLQutC+0LwgXCLQvtGI0LjQsdC60LBcIiDQuCDQutC90L7Qv9C60L7QuSBcItC30LDQutGA0YvRgtGMXCIgIC8vXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyAtINCf0YDQuNC80LXQvdGP0LXRgtGB0Y8g0L7QsdGL0YfQvdC+INC/0YDQuCBhamF4LdC30LDQv9GA0L7RgdCw0YUsINC00LvRjyDQv9GA0Lgg0L7QsdGA0LDQsdC+0YLQutC1INGB0LXRgNCy0LXRgNC90YvRhSDQvtGI0LjQsdC+0Lpcblx0XHRmLnMwLnNtX2Z1bmMgPSBmdW5jdGlvbih0ZXh0KSB7XG5cdFx0XHRyZXR1cm4gc2hvd01vZGFsKHtcblx0XHRcdFx0aGVhZGVyOiAn0J7RiNC40LHQutCwJyxcblx0XHRcdFx0b2tfbmFtZTogJ9CX0LDQutGA0YvRgtGMJyxcblx0XHRcdFx0Y2FuY2VsX25hbWU6ICcnLFxuXHRcdFx0XHR3aWR0aDogMzUwLFxuXHRcdFx0XHRzdGFuZGFyZF9jc3M6ICcxJyxcblx0XHRcdFx0dGFyZ2V0OiBkb2N1bWVudC5ib2R5LFxuXHRcdFx0XHRodG1sOiB0ZXh0LFxuXHRcdFx0XHRwYXJhbXM6IHt9LFxuXHRcdFx0XHRjYWxsYmFjazogZnVuY3Rpb24oYXJnLCBwYXJhbXMpe1xuXHRcdFx0XHRcdGlmKGFyZyAhPT0gbnVsbCkge31cblx0XHRcdFx0XHRlbHNlIHt9XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdC8vIHMwLjIuINCk0YPQvdC60YbQuNC+0L3QsNC7IFwi0LzQtdGF0LDQvdC40LfQvNCwINC+0YLQu9C+0LbQtdC90L3QvtCz0L4g0YHQvtGF0YDQsNC90LXQvdC40Y8g0LTQu9GPINGC0LXQutGB0YLQvtCy0YvRhSDQv9C+0LvQtdC5XCIgLy9cblx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdGYuczAudHh0X2RlbGF5X3NhdmUgPSB7fTtcblxuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRcdC8vIDFdINCf0YDQuNC80LXQvdC40YLRjCBcItC80LXRhdCw0L3QuNC30Lwg0L7RgtC70L7QttC10L3QvdC+0LPQviDRgdC+0YXRgNCw0L3QtdC90LjRjyDQtNC70Y8g0YLQtdC60YHRgtC+0LLRi9GFINC/0L7Qu9C10LlcIiAgIC8vXG5cdFx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdFx0Ly8gLSDQntC9INC+0YHQvtCx0LXQvdC90L4g0LDQutGC0YPQsNC70LXQvSDQtNC70Y8g0YLQtdC60YHRgtC+0LLRi9GFINC/0L7Qu9C10LkuXG5cdFx0XHQvLyAtINCU0LXQu9Cw0LXRgiDRgtCw0LosINGH0YLQviDRhNGD0L3QutGG0LjRjyDRgdC+0YXRgNCw0L3QtdC90LjRjyDRgdGA0LDQsdCw0YLRi9Cy0LDQtdGCINC90LUg0L/RgNC4INC60LDQttC00L7QvCDQvdCw0LbQsNGC0LjQuC5cblx0XHRcdC8vIC0g0JAg0LvQuNGI0Ywg0YHQv9GD0YHRgtGPINC30LDQtNCw0L3QvdGL0LUgTiDRgdC10LrRg9C90LQg0L/QvtGB0LvQtSDQv9C+0YHQu9C10LTQvdC10LPQviDQuNC30LzQtdC90LXQvdC40Y8uXG5cdFx0XHRmLnMwLnR4dF9kZWxheV9zYXZlLnVzZSA9IGZ1bmN0aW9uKHNhdmVmdW5jKXtcblxuXHRcdFx0XHQvLyAyLjEuINCe0YHRgtCw0L3QvtCy0LjRgtGMINGA0LDQvdC10LUg0LfQsNC/0LvQsNC90LjRgNC+0LLQsNC90L3Ri9C5IHNldFRpbWVvdXRcblx0XHRcdFx0aWYoc2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLnNldHRpbWVvdXRpZCgpKVxuXHRcdFx0XHRcdGNsZWFyVGltZW91dChzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUuc2V0dGltZW91dGlkKCkpO1xuXG5cdFx0XHRcdC8vIDIuMl0g0JXRgdC70Lgg0LLRgNC10LzRjyDQtNC70Y8g0YHQvtGF0YDQsNC90LXQvdC40Y8g0L3QtSDQv9GA0LjRiNC70L5cblx0XHRcdFx0aWYoK0RhdGUubm93KCkgLSArc2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLmxhc3R1cGRhdGUoKSA8ICtzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUuZ2FwKSB7XG5cblx0XHRcdFx0XHQvLyDQn9C+0YHRgtCw0LLQuNGC0Ywg0LLRi9C/0L7Qu9C90LXQvdC40LUg0L3QsCDRgtCw0LnQvNC10YBcblx0XHRcdFx0XHR2YXIgdGltZXJJZCA9IHNldFRpbWVvdXQoc2F2ZWZ1bmMsIHNlbGYubS5zMC50eHRfZGVsYXlfc2F2ZS5nYXApO1xuXG5cdFx0XHRcdFx0Ly8g0KHQvtGF0YDQsNC90LjRgtGMIHRpbWVySWQg0LIg0LzQvtC00LXQu9GMXG5cdFx0XHRcdFx0c2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLnNldHRpbWVvdXRpZCh0aW1lcklkKTtcblxuXHRcdFx0XHRcdC8vINCh0L7RhdGA0LDQvdC40YLRjCDRgtC10LrRg9GJ0LjQuSB0aW1lc3RhbXAg0LIg0LzQvtC00LXQu9GMXG5cdFx0XHRcdFx0c2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLmxhc3R1cGRhdGUoRGF0ZS5ub3coKSk7XG5cblx0XHRcdFx0XHQvLyDQo9C60LDQt9Cw0YLRjCwg0YfRgtC+INC40LzQtdC10Y7RgtGB0Y8g0L3QtSDRgdC+0YXRgNCw0L3RkdC90L3Ri9C1INC00LDQvdC90YvQtVxuXHRcdFx0XHRcdHNlbGYubS5zMC50eHRfZGVsYXlfc2F2ZS5pc191bnNhdmVkX2RhdGEoMSk7XG5cblx0XHRcdFx0XHQvLyDQl9Cw0LLQtdGA0YjQuNGC0Yxcblx0XHRcdFx0XHRyZXR1cm4gMTtcblxuXHRcdFx0XHR9XG5cblx0XHRcdFx0Ly8gMi4zXSDQldGB0LvQuCDQstGA0LXQvNGPINC00LvRjyDRgdC+0YXRgNCw0L3QtdC90LjRjyDQv9GA0LjRiNC70L5cblx0XHRcdFx0ZWxzZSB7XG5cblx0XHRcdFx0XHQvLyDQodC+0YXRgNCw0L3QuNGC0Ywg0YLQtdC60YPRidC40LkgdGltZXN0YW1wINCyINC80L7QtNC10LvRjFxuXHRcdFx0XHRcdHNlbGYubS5zMC50eHRfZGVsYXlfc2F2ZS5sYXN0dXBkYXRlKERhdGUubm93KCkpO1xuXG5cdFx0XHRcdH1cblxuXHRcdFx0fTtcblxuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRcdC8vIDJdINCX0LDQsdC70L7QutC40YDQvtCy0LDRgtGMINC30LDQutGA0YvRgtC40LUg0LTQvtC60YPQvNC10L3RgtCwIC8vXG5cdFx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHRcdFx0Ly8gLSDQmNC90YvQvNC4INGB0LvQvtCy0LDQvNC4INGD0LrQsNC30LDRgtGMLCDRh9GC0L4g0LXRgdGC0Ywg0L3QtdGB0L7RhdGA0LDQvdGR0L3QvdGL0LUg0LTQsNC90L3Ri9C1LlxuXHRcdFx0Ly8gLSDQn9C+0L/Ri9GC0LrQsCDQt9Cw0LrRgNGL0YLRjCDRgdGC0YDQsNC90LjRhtGDINCyINC40YLQvtCz0LUg0L/RgNC40LLQtdC00ZHRgiDQuiDQstGL0LfQvtCy0YMg0LzQvtC00LDQu9GM0L3QvtCz0L4gY29uZmlybS5cblx0XHRcdGYuczAudHh0X2RlbGF5X3NhdmUuYmxvY2sgPSBmdW5jdGlvbigpe1xuXHRcdFx0XHRzZWxmLm0uczAudHh0X2RlbGF5X3NhdmUuaXNfdW5zYXZlZF9kYXRhKDEpO1xuXHRcdFx0fTtcblxuXHRcdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0XHQvLyAzXSDQoNCw0LfQsdC70L7QutC40YDQvtCy0LDRgtGMINC30LDQutGA0YvRgtC40LUg0LTQvtC60YPQvNC10L3RgtCwIC8vXG5cdFx0XHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHRcdC8vIC0g0JjQvdGL0LzQuCDRgdC70L7QstCw0LzQuCDRg9C60LDQt9Cw0YLRjCwg0YfRgtC+INC90LXRgiDQvdC10YHQvtGF0YDQsNC90ZHQvdC90YvRhSDQtNCw0L3QvdGL0YUuXG5cdFx0XHQvLyAtINCf0L7Qv9GL0YLQutCwINC30LDQutGA0YvRgtGMINGB0YLRgNCw0L3QuNGG0YMg0LIg0LjRgtC+0LPQtSDRg9C20LUg0L3QtSDQv9GA0LjQstC10LTRkdGCINC6INCy0YvQt9C+0LLRgyDQvNC+0LTQsNC70YzQvdC+0LPQviBjb25maXJtLlxuXHRcdFx0Zi5zMC50eHRfZGVsYXlfc2F2ZS51bmJsb2NrID0gZnVuY3Rpb24oKXtcblx0XHRcdFx0c2VsZi5tLnMwLnR4dF9kZWxheV9zYXZlLmlzX3Vuc2F2ZWRfZGF0YSgwKTtcblx0XHRcdH07XG5cblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdC8vIFx0XHRcdCAgICAgICAgXHRcdCBcdFx0XHQgICAgICAgICAgICAgICAgICAvL1xuXHQvLyBcdFx0XHQgczEuINCk0YPQvdC60YbQuNC+0L3QsNC7INCz0LvQsNCy0L3QvtCz0L4g0LzQtdC90Y4gXHRcdFx0Ly9cblx0Ly8gXHRcdFx0ICAgICAgICAgXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAgIC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Zi5zMSA9IHt9O1xuXG5cdFx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0XHQvLyBzMS4xLiDQlNC10LvQsNC10YIg0YLQvi3RgtC+ICAvL1xuXHRcdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG5cdFx0Ly8gLSDQn9C+0Y/RgdC90LXQvdC40LVcblx0XHRmLnMxLnNvbWVGdW5jID0gZnVuY3Rpb24oKSB7XG5cblx0XHRcdC8vIC4uLiDQutC+0LQgLi4uXG5cblx0XHR9O1xuXG5cblxuXG5yZXR1cm4gZjsgfX07XG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cblxuXG5cbiIsIi8qLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09Ly8vL1xuLy8vL1x0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQvLy8vXG4vLy8vICAgai5qcyAtINCz0LvQsNCy0L3Ri9C5IGpzLdGE0LDQudC7INGI0LDQsdC70L7QvdCwINC00L7QutGD0LzQtdC90YLQsCBcdC8vLy9cbi8vLy9cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0Ly8vL1xuLy8vLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0vLy8vXG4vLy8vIFx0XHRcdCAgICAgICAgXHRcdCBcdCAgICBcdCAgIC8vLy9cbi8vLy8gXHRcdFx0ICAgINCe0LPQu9Cw0LLQu9C10L3QuNC1ICBcdFx0XHQgLy8vL1xuLy8vLyBcdFx0XHQgICAgICAgICBcdFx0XHRcdCAgICAgICAvLy8vXG4vLy8vPT09PT09PT09PT09PT09PT09PT09PT09PT09PT0vLyovXG4vKipcbiAqXG4gKiAxLiDQn9GA0LjQvdGP0YLRjCDQuCDQvtCx0YDQsNCx0L7RgtCw0YLRjCDQtNCw0L3QvdGL0LUg0YEg0YHQtdGA0LLQtdGA0LBcbiAqXG4gKiAgIDEuMS4g0J/RgNC40LzQtdGALi4uXG4gKiBcbiAqIDIuINCh0L7Qt9C00LDRgtGMINGN0LrQt9C10LzQv9C70Y/RgCDQvNC+0LTQtdC70Lgg0LjQtyBtLmpzINGBINGE0YPQvdC60YbQuNC+0L3QsNC70L7QvCDQuNC3IGYuanNcbiAqIDMuINCU0L7QsdCw0LLQuNGC0Ywg0LIga28g0LLRgdC1INC90LXQvtCx0YXQvtC00LjQvNGL0LUg0LrQsNGB0YLQvtC80L3Ri9C1INGB0LLRj9C30LrQuFxuICogNC4g0JDQutGC0LjQstC40LfQuNGA0L7QstCw0YLRjCDQvNC+0LTQtdC70Ywg0LTQu9GPINC90YPQttC90YvRhSBET00t0Y3Qu9C10LzQtdC90YLQvtCyXG4gKlxuICovXG5cblx0XHRcbi8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuLy8gMS4g0J/RgNC40L3Rj9GC0Ywg0LTQsNC90L3Ri9C1INGBINGB0LXRgNCy0LXRgNCwIC8vXG4vLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblxuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cblx0Ly8gMS4xLiDQn9GA0LjQvNC10YAgLi4uIC8vXG5cdC8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1x0XHRcblxuLypcbiAgICAvLyAxXSDQn9C+0LTQs9C+0YLQvtCy0LjRgtGMINC80LDRgdGB0LjQsiDQtNC70Y8g0YDQtdC30YPQu9GM0YLQsNGC0LBcbiAgICBzZXJ2ZXI0bG8ubWFpbm1lbnVfZ3JvdXBzX3ByZXBlYXJlZCA9IFtdO1xuXG5cdFx0Ly8gMl0g0JfQsNC/0L7Qu9C90LjRgtGMIHNlcnZlcjRsYXlvdXQubWFpbm1lbnVfZ3JvdXBzX3ByZXBlYXJlZFxuICAgIGZvcih2YXIgaT0wOyBpPHNlcnZlcjRsby5tYWlubWVudV9ncm91cHMubGVuZ3RoOyBpKyspIHtcblxuICAgICAgLy8gMi4xXSDQodGE0L7RgNC80LjRgNC+0LLQsNGC0Ywg0L7QsdGK0LXQutGCINC00LvRjyDQtNC+0LHQsNCy0LvQtdC90LjRj1xuXG5cdFx0XHRcdC8vINCh0L7Qt9C00LDRgtGMINC/0YPRgdGC0L7QuSDQvtCx0YrQtdC60YJcblx0XHRcdFx0dmFyIG9iaiA9IHt9O1xuXG5cdFx0XHRcdC8vINCU0L7QsdCw0LLQuNGC0Ywg0LIgb2JqINGB0LLQvtC50YHRgtCy0LBcblx0XHRcdFx0b2JqLmlkIFx0XHRcdFx0XHRcdFx0XHRcdD0ga28ub2JzZXJ2YWJsZShzZXJ2ZXI0bG8ubWFpbm1lbnVfZ3JvdXBzW2ldLmlkKTtcblx0XHRcdFx0b2JqLm5hbWUgXHRcdFx0XHRcdFx0XHRcdD0ga28ub2JzZXJ2YWJsZShzZXJ2ZXI0bG8ubWFpbm1lbnVfZ3JvdXBzW2ldLm5hbWUpO1xuXHRcdFx0XHRvYmouaXNfb24gXHRcdFx0XHRcdFx0XHQ9IGtvLm9ic2VydmFibGUoc2VydmVyNGxvLm1haW5tZW51X2dyb3Vwc1tpXS5pc19vbik7XG5cdFx0XHRcdG9iai5ncm91cF9kZXNjIFx0XHRcdFx0XHQ9IGtvLm9ic2VydmFibGUoc2VydmVyNGxvLm1haW5tZW51X2dyb3Vwc1tpXS5ncm91cF9kZXNjKTtcblx0XHRcdFx0b2JqLmdyb3VwX3Bvc2l0aW9uIFx0XHRcdD0ga28ub2JzZXJ2YWJsZShzZXJ2ZXI0bG8ubWFpbm1lbnVfZ3JvdXBzW2ldLmdyb3VwX3Bvc2l0aW9uKTtcblxuICAgICAgLy8gMi4yXSDQlNC+0LHQsNCy0LjRgtGMINGN0YLQvtGCINC+0LHRitC10LrRgiDQsiBzZXJ2ZXI0bG8ubWFpbm1lbnVfZ3JvdXBzX3ByZXBlYXJlZFxuICAgICAgc2VydmVyNGxvLm1haW5tZW51X2dyb3Vwc19wcmVwZWFyZWQucHVzaChrby5vYnNlcnZhYmxlKG9iaikpXG5cbiAgICB9XG4qL1xuXG5cbi8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cbi8vIDIuINCh0L7Qt9C00LDRgtGMINGN0LrQt9C10LzQv9C70Y/RgCDQvNC+0LTQtdC70Lgg0LjQtyBtLmpzINGBINGE0YPQvdC60YbQuNC+0L3QsNC70L7QvCDQuNC3IGYuanMgLy9cbi8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cbnZhciBsYXlvdXRtb2RlbCA9IE9iamVjdC5jcmVhdGUoTGF5b3V0TW9kZWxQcm90bykuY29uc3RydWN0b3IoTGF5b3V0TW9kZWxGdW5jdGlvbnMpO1xuXG5cbi8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLy9cbi8vIDMuINCU0L7QsdCw0LLQuNGC0Ywg0LIga28g0LLRgdC1INC90LXQvtCx0YXQvtC00LjQvNGL0LUg0LrQsNGB0YLQvtC80L3Ri9C1INGB0LLRj9C30LrQuCAvL1xuLy8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdC8vIDFdINCh0LLRj9C30LrQsCBwaG9uZVJGINC00LvRjyBpbnB1dCfQsCDQtNC70Y8g0L/RgNC40LXQvNCwINC90L7QvNC10YDQsCDRgtC10LvQtdGE0L7QvdCwINCyINGA0L7RgdGB0LjQudGB0LrQvtC8INGE0L7RgNC80LDRgtC1XG5cdC8va28uYmluZGluZ0hhbmRsZXJzLnBob25lUkYgPSBjdXN0b21fYmluZGluZ3NfZm9yX2tvLnBob25lUkY7XG5cblx0Ly8gMl0g0KHQstGP0LfQutCwIHBob25lUkYg0LTQu9GPIGlucHV0J9CwINC00LvRjyDQv9GA0LjQtdC80LAg0L3QvtC80LXRgNCwINGC0LXQu9C10YTQvtC90LAg0LIg0YDQvtGB0YHQuNC50YHQutC+0Lwg0YTQvtGA0LzQsNGC0LVcblx0Ly9rby5iaW5kaW5nSGFuZGxlcnMuYmlydGhkYXkgPSBjdXN0b21fYmluZGluZ3NfZm9yX2tvLmJpcnRoZGF5O1xuXG5cbi8vLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS8vXG4vLyA0LiDQkNC60YLQuNCy0LjQt9C40YDQvtCy0LDRgtGMINC80L7QtNC10LvRjCDQtNC70Y8g0L3Rg9C20L3Ri9GFIERPTS3RjdC70LXQvNC10L3RgtC+0LIgIC8vXG4vLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXG5cdC8vIDQuMS4g0JDQutGC0LjQstC40LfQuNGA0L7QstCw0YLRjCDQvNC+0LTQtdC70Ywg0LTQu9GPIERPTS3RjdC70LXQvNC10L3RgtCwICd0YXNrcGFuZWwnICAvL1xuXHQvLy0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvL2tvLmFwcGx5QmluZGluZ3MobGF5b3V0bW9kZWwsIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd0YXNrcGFuZWwnKSk7XG5cblx0Ly8gNC4yLiDQkNC60YLQuNCy0LjQt9C40YDQvtCy0LDRgtGMINC80L7QtNC10LvRjCDQtNC70Y8gRE9NLdGN0LvQtdC80LXQvdGC0LAgJ21haW5tZW51JyAgLy9cblx0Ly8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0vL1xuXHQvL2tvLmFwcGx5QmluZGluZ3MobGF5b3V0bW9kZWwsIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYWlubWVudScpKTtcblxuIl0sInNvdXJjZVJvb3QiOiIvc291cmNlLyJ9
