// Список автозагружаемых функций - это строка, в которую по мере выполнения java скриптов добавляются сктроки типа
// RUN_AFTER_LOAD += "load();". Данные функции будут выполнены после загрузки страницы
var RUN_AFTER_LOAD = "";

/*----------------- Плавное передвижение к ссылке ----------------*/
function anchorScroller(el, duration) {
        if (this.criticalSection) {
                return false;
        }

        if ((typeof el != 'object') || (typeof el.href != 'string'))
                return true;

        var address = el.href.split('#');
        if (address.length < 2)
                return true;

        address = address[address.length-1];
        el = 0;

        for (var i=0; i<document.anchors.length; i++) {
                if (document.anchors[i].name == address) {
                        el = document.anchors[i];
                        break;
                }
        }
        if (el === 0)
                return true;

        this.stopX = 0;
        this.stopY = 0;
        do {
                this.stopX += el.offsetLeft;
                this.stopY += el.offsetTop;
        } while (el = el.offsetParent);

        this.startX = document.documentElement.scrollLeft || window.pageXOffset || document.body.scrollLeft;
        this.startY = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;

        this.stopX = this.stopX - this.startX;
        this.stopY = this.stopY - this.startY;

        if ( (this.stopX == 0) && (this.stopY == 0) )
                return false;

        this.criticalSection = true;
        if (typeof duration == 'undefined')
                this.duration = 500;
        else
                this.duration = duration;

        var date = new Date();
        this.start = date.getTime();
        this.timer = setInterval(function () {
                var date = new Date();
                var X = (date.getTime() - this.start) / this.duration;
                if (X > 1)
                        X = 1;
                var Y = ((-Math.cos(X*Math.PI)/2) + 0.5);

                cX = Math.round(this.startX + this.stopX*Y);
                cY = Math.round(this.startY + this.stopY*Y);

                document.documentElement.scrollLeft = cX;
                document.documentElement.scrollTop = cY;
                document.body.scrollLeft = cX;
                document.body.scrollTop = cY;

                if (X == 1) {
                        clearInterval(this.timer);
                        this.criticalSection = false;
                }
        }, 10);
        return false;
}


/*----------------- Запись куки --------------------------------------*/
function set_cookie(name, value, expires, path)
{
	if (!expires)
		expires = new Date();
	document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path="+path;
}
/*----------------- Сохраняем куки -----------------------------------*/
function get_cookie(name)
{
	cookie_name = name + "=";
	cookie_length = document.cookie.length;
	cookie_begin = 0;
	while (cookie_begin < cookie_length)
	{
	value_begin = cookie_begin + cookie_name.length;
	if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
	{
	var value_end = document.cookie.indexOf (";", value_begin);
	if (value_end == -1)
	{
	value_end = cookie_length;
	}
	return unescape(document.cookie.substring(value_begin, value_end));
	}
	cookie_begin = document.cookie.indexOf(" ", cookie_begin) + 1;
	if (cookie_begin == 0)
	{
	break;
	}
	}
	return null;
}
/*----------------- Добавить в избранное -----------------------------*/
function getBrowserInfo() {
 var t,v = undefined;
 if (window.opera) t = 'Opera';
 else if (document.all) {
  t = 'IE';
  var nv = navigator.appVersion;
  var s = nv.indexOf('MSIE')+5;
  v = nv.substring(s,s+1);
 }
 else if (navigator.appName) t = 'Netscape';
 return {type:t,version:v};
}
function bookmark(a){
 var url = window.document.location;
 var title = window.document.title;
 var b = getBrowserInfo();
 if (b.type == 'IE' && 7 > b.version && b.version >= 4) window.external.AddFavorite(url,title);
 else if (b.type == 'Opera') {
  a.href = url;
  a.rel = "sidebar";
  a.title = url+','+title;
  return true;
 }
 else if (b.type == "Netscape") window.sidebar.addPanel(title,url,"");
 else alert("Команда принята. Теперь нажмите CTRL-D, чтобы добавить страницу в закладки.");
 return false;
}
/*----------------- Замена подстроки ---------------------------------*/
function str_replace(txt,cut_str,paste_str)
{
	var f=0;
	var ht='';
	ht = ht + txt;
	f=ht.indexOf(cut_str);
	while (f!=-1){
	//цикл для вырезания всех имеющихся подстрок
	f=ht.indexOf(cut_str);
	if (f>0){
	ht = ht.substr(0,f) + paste_str + ht.substr(f+cut_str.length);
	};
	};
	return ht
};
/*---------------Скрытие/раскрытие div -----------------------------*/
function submenu(menu_ID, time)
{
	//print(ID);
	var obj;
	if(typeof(menu_ID)=='object')
		obj=menu_ID;
	else
		obj=document.getElementById(menu_ID);

	if(time == null) time=500;

	if(time>0)
		{
		var sliding3 = false;
		var display = obj.style.display;
		var overflow = obj.style.overflow;
		obj.style.overflow='hidden';
		obj.style.display='block';
		var i=0;for(i=0;i<1000;i++)obj.style.height='auto';
		var height = obj.clientHeight;// - parseInt(window.getComputedStyle(obj, null).paddingTop) - parseInt(window.getComputedStyle(obj, null).paddingBottom);

		var pixels = 8;								// количество пиклелей за одно движение
		var count = height/pixels;					//сколько раз двигаем
		if(count>64)
			{
			count = 64;
			pixels = Math.round(height/count);
			count = height/pixels;
			}
		var sleep = Math.round(time/count);

		if(display!='block')
			{
			var current=0;
			obj.style.height=current+'px';
			sliding3 = clearInterval(sliding3);
			sliding3 = setInterval(function() {
				if(current < height) { // до начальной позиции
					current += pixels;
					obj.style.height=current + 'px';
					}
				else
					{
					// Устанавливаем точно
					obj.style.height='auto';
					obj.style.overflow=overflow;
					sliding3 = clearInterval(sliding3);
					}
				}, sleep );
			}
		else
			{
			var current=height;
			obj.style.height=current+'px';

			sliding3 = clearInterval(sliding3);
			sliding3 = setInterval(function() {
				if(current > pixels) { // до начальной позиции
					current -= pixels;
					obj.style.height=current + 'px';
					}
				else
					{
					// Устанавливаем изначальный размер
					obj.style.display='none';
					obj.style.height=height + 'px';
					obj.style.overflow=overflow;
					sliding3 = clearInterval(sliding3);
					}
				}, sleep );
			}
		}
	else // если необходимо мгновенно
		{
		if(obj.style.display=='none')
			{obj.style.display='block';}
		else
			{obj.style.display='none';}
		}
}
/*----------------- Изменяем Прозрачность --------------------------*/
function opacity(id, to , time) { //id, конечная прозрачностьб время
	var obj = document.getElementById(id);
	var from = 100*document.defaultView.getComputedStyle(obj,null).getPropertyValue('opacity');
	if(obj.style.display!='block')
		{
		from = 0;
		obj.style.display = 'block';
		obj.style.opacity = 0 ;
		obj.style.MozOpacity = 0 ;
		obj.style.filter = "alpha(opacity=0)";
		}
	var step = 5;											// Шаг изменения прозрачности
	var count = Math.abs(to - from)/step;					//сколько раз двигаем
	var inc = ((to-from)>0)?step:-step;					//на сколько сдвига и напрвление сдвига
	// Вычисляем задержку между передвижениями
	if(time == null)
		sleep = 10;
	else
		{
		sleep = Math.round(time/count);
		if(sleep < 10)
			{
			sleep = 10;
			count = Math.round(time/sleep);
			inc = Math.round((to-from)/count);
			}
		}

	var sliding4 = false;
	sliding4 = clearInterval(sliding4);
	sliding4 = setInterval(function() {
		if(count > 0) { // до начальной позиции
			from +=inc;
			count--;
			obj.style.opacity = from/100 ;
			obj.style.MozOpacity = from/100 ;
			obj.style.filter = "alpha(opacity="+from+")";
			}
		else
			{
			// Устанавливаем точно
			if(to == 0)
					obj.style.display = 'none' ;
			else
					obj.style.opacity = to ;
			sliding4 = clearInterval(sliding4);
			}
	}, sleep );
}
//---------------------- Установка класса "active" для выбранного элемента меню ----------
function setSelectItemToActiveForMenu(menuID){
	var menu = document.getElementById(menuID);
  if(menu == null) return;
	for (var childItem in menu.childNodes) {
		if (menu.childNodes[childItem].nodeType == 1){ // is a object, not a '\n'
			var item = menu.childNodes[childItem];
			if( item.getElementsByTagName("a")[0].href == document.URL ){
				if(item.className.indexOf("active")==-1 ) item.className += " active";
			}else
				item.className = item.className.replace("active", "");
		}
	}
}
