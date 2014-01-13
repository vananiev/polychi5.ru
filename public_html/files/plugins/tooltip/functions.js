var Panel_Position = new Object();		//позиции панелей
Panel_Position.id = new Array();
Panel_Position.where = new Array();
Panel_Position.position = new Array();
// Устанавливаем позиции панелей после загрузки страницы
RUN_AFTER_LOAD += "set_panel_position();"; 

/*----------------- Сохраняем позиции панелей ------------------------*/
function save_panel_position(id, where, position) {
	//Узнаем надо обновить или создать запись
	var i=-1;
	var count = Panel_Position.id.length;
	for (j=0; j < count; j++)
		if(Panel_Position.id[j] == id) {i=j;break;}
	//Записываем значение
	if(i==-1)i=count;
	Panel_Position.id[i] = id;
	Panel_Position.where[i] = where;
	Panel_Position.position[i] = position;
	//сохраняем
	var save="";
	count = Panel_Position.id.length;
	for (i=0; i < count; i++)
		{
		save += Panel_Position.id[i] + ',' + Panel_Position.where[i] + ',' + Panel_Position.position[i] ; 
		if(i!=(count-1)) save += ',';
		}
	var expires = new Date(); // получаем текущую дату
	expires.setTime(expires.getTime() + (1000*3600*24*31)); // вычисляем срок хранения cookie
	set_cookie("panels_position", save, expires, "/"); // устанавливаем cookie с помощью функции set_cookie
}

/*----------------- Читаем позиции панелей ------------------------*/
function read_panel_position() {
	var arr = get_cookie("panels_position"); // читаем значение cookie
	if(arr == null) return;
	arr = arr.split(",") // разбираем значение, помещяя его в массив
	var i;
	for (i=0, j=0; i < arr.length;j++) // проходимся по массиву и форматируем его для вывода
		{
		Panel_Position.id[j] = arr[i++];
		Panel_Position.where[j] = arr[i++];
		Panel_Position.position[j] = arr[i++];
		}
}

/*----------------- Устанавливаем позиции панелей ------------------------*/
function set_panel_position() 
	{
	read_panel_position();
	var count = Panel_Position.id.length;
	for (i=0; i < count; i++)
		{
		var obj = document.getElementById(Panel_Position.id[i]);	
		if(obj != null)
			switch( Panel_Position.where[i] )
				{
				case 'down': 	obj.style.top = 'auto';		obj.style.bottom = 	Panel_Position.position[i]  + 'px'; break; 	  
				case 'up': 		obj.style.bottom = 'auto';	obj.style.top = 	Panel_Position.position[i]  + 'px'; break; 
				case 'left': 	obj.style.right = 'auto';	obj.style.left = 	Panel_Position.position[i]  + 'px'; break; 	
				case 'right': 	obj.style.left = 'auto';	obj.style.right = 	Panel_Position.position[i]  + 'px'; break; 
				default: return;
				}
		}
	}

/*----------------- Двигаем панель -------------------------------*/
function slide(id, doing, where, from, to , time) { //id, куда двигать, сколько пикселей оставить на показ
	var obj = document.getElementById(id);
	if(doing == 'hide' && to == null)
		{
		var display = obj.style.display;
		obj.style.display = 'block';
		if(where=='down' || where=='up')
			to = from - obj.offsetHeight + 7;
		else
			to = from - obj.offsetWidth + 7;
		obj.style.display = display;
		}
	if(doing == 'show' && from == null)
		{
		var display = obj.style.display;
		obj.style.display = 'block';
		if(where=='down' || where=='up')
			from = to - obj.offsetHeight;
		else
			from = to - obj.offsetWidth;
		obj.style.display = display;
		}
	var pixels = 1;										// количество пиклелей за одно движение
	var count = Math.abs(to - from)/pixels;					//сколько раз двигаем							
	var inc = ((to-from)>0)?pixels:-pixels;					//на сколько сдвига и напрвление сдвига
	//обнуляем привязки к противоположным координатам
	switch( where )
	{
	case 'down': 	obj.style.top = 'auto'; break; 	  
	case 'up': 	obj.style.bottom = 'auto'; break; 
	case 'left': 	obj.style.right = 'auto'; break; 	
	case 'right': 	obj.style.left = 'auto';break; 
	default: return;
	}
	
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

	var sliding = false;
	sliding = clearInterval(sliding);
	sliding = setInterval(function() {
		if(count > 0) { // до начальной позиции
			from +=inc;
			count--;
			switch( where )
				{
				case 'down': 	obj.style.bottom = from + 'px'; break; 	  
				case 'up': 		obj.style.top = from + 'px'; break; 
				case 'left': 	obj.style.left = from + 'px'; break; 	
				case 'right': 	obj.style.right = from + 'px';break; 
				default: return;
				}
			}
		else
			{
			// Устанавливаем точно
			switch( where )
				{
				case 'down': 	obj.style.bottom = to + 'px'; break; 	  
				case 'up': 		obj.style.top = to + 'px'; break; 
				case 'left': 	obj.style.left = to + 'px'; break; 	
				case 'right': 	obj.style.right = to + 'px';break; 
				default: return;
				}
			sliding = clearInterval(sliding);
			}
	}, sleep );
	//Сохраняем позицию панели
	save_panel_position(id, where, to);
}
/*======================================================================*/
var Panel_Dimention = new Object();		//габариты панелей
Panel_Dimention.id = new Array();
Panel_Dimention.width = new Array();
Panel_Dimention.height = new Array();
Panel_Dimention.display = new Array();
// Устанавливаем позиции панелей после загрузки страницы
RUN_AFTER_LOAD += "set_panel_dimension();"; 

/*----------------- Сохраняем габариты панелей ------------------------*/
function save_panel_dimension(id, width, height, display) {
	//Узнаем надо обновить или создать запись
	var i=-1;
	var count = Panel_Dimention.id.length;
	for (j=0; j < count; j++)
		if(Panel_Dimention.id[j] == id) {i=j;break;}
	//Записываем значение
	if(i==-1)i=count;
	Panel_Dimention.id[i] = id;
	Panel_Dimention.width[i] = width;
	Panel_Dimention.height[i] = height;
	Panel_Dimention.display[i] = display;
	//сохраняем
	var save="";
	count = Panel_Dimention.id.length;
	for (i=0; i < count; i++)
		{
		save += Panel_Dimention.id[i] + ',' + Panel_Dimention.width[i] + ',' + Panel_Dimention.height[i] + ',' + Panel_Dimention.display[i]; 
		if(i!=(count-1)) save += ',';
		}
	var expires = new Date(); // получаем текущую дату
	expires.setTime(expires.getTime() + (1000*3600*24*31)); // вычисляем срок хранения cookie
	set_cookie("panels_dimention", save, expires, "/"); // устанавливаем cookie с помощью функции set_cookie
}

/*----------------- Читаем габариты панелей ------------------------*/
function read_panel_dimension() {
	var arr = get_cookie("panels_dimention"); // читаем значение cookie
	if(arr!=null){ // <- добавлено, но не отттестировано
	//alert(arr);
	var arr = arr.split(",") // разбираем значение, помещяя его в массив
	var i;
	for (i=0, j=0; i < arr.length;j++) // проходимся по массиву и форматируем его для вывода
		{
		Panel_Dimention.id[j] = arr[i++];
		Panel_Dimention.width[j] = arr[i++];
		Panel_Dimention.height[j] = arr[i++];
		Panel_Dimention.display[j] = arr[i++];
		}
	}
}

/*----------------- Устанавливаем габариты панелей ------------------------*/
function set_panel_dimension() 
{
	read_panel_dimension();
	var count = Panel_Dimention.id.length;
	for (i=0; i < count; i++)
		{
		var obj = document.getElementById(Panel_Dimention.id[i]);	
		if(obj != null)
				{
				obj.style.width = 	Panel_Dimention.width[i]  + 'px';  
				obj.style.height = 	Panel_Dimention.height[i]  + 'px';
				obj.style.display = 	Panel_Dimention.display[i];
				}
		}
}
/*---------------Изменяем габариты панелей--------------------------*/
function resize(id, which, to, time)  //id, что изменять, до, за какое время
{
	var obj = document.getElementById(id);
	var display;
	if(to == 0)
		display = 'none';
	else
		display = 'block';
	var overflow = obj.style.overflow;
	obj.style.overflow='hidden';
	var display_old = obj.style.display;
	obj.style.height='auto';
	obj.style.display='block';
	var width = obj.clientWidth ;// - parseInt(window.getComputedStyle(obj, null).paddingLeft) - parseInt(window.getComputedStyle(obj, null).paddingRight);;
	var height = obj.clientHeight ;//- parseInt(window.getComputedStyle(obj, null).paddingTop) - parseInt(window.getComputedStyle(obj, null).paddingBottom);

	var from;	//откуда начинаем движение
	if(display_old!='none')
		from = (which == 'width')?width:height;
	else
		from = 0;
	if(to == null )
		{
		to_old=null;
		to = (which == 'width')?width:height;
		}

	var pixels = 1;										// количество пиклелей за одно движение
	var count = Math.abs(to - from)/pixels;					//сколько раз двигаем							
	var inc = ((to-from)>0)?pixels:-pixels;					//на сколько сдвига и напрвление сдвига

	// Вычисляем задержку между передвижениями
	var sleep;
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
	var sliding2 = false;
	sliding2 = clearInterval(sliding2);
	sliding2 = setInterval(function() {
		if(count>0 ) { // до начальной позиции
			from +=inc;
			if(from<0){count=0;from=0;}
			count--;
			switch( which )
				{
				case 'width': 		obj.style.width = from + 'px'; break; 	  
				case 'height': 		obj.style.height = from + 'px'; break; 
				default: return;
				}
			}
		else // Устанавливаем точно
			{
			if(to == 0) //если скрыли
				{
				// Устанавливаем изначальный размер
				obj.style.display='none';
				switch( which )
					{
					case 'width': 		obj.style.width = width + 'px'; break; 	  
					case 'height': 		obj.style.height = height + 'px'; break; 
					}
				}
			else // Если все еще отображаем
				{
				if(to_old!=null)
					switch( which )
						{
						case 'width': 		obj.style.width = from + 'px'; break; 	  
						case 'height': 		obj.style.height = from + 'px'; break; 
						default: return;
						}
				else
					switch( which )
						{
						case 'width': 		obj.style.width = 'auto'; break; 	  
						case 'height': 		obj.style.height = 'auto'; break; 
						default: return;
						}
				}
			obj.style.overflow=overflow;
			sliding2 = clearInterval(sliding2);
			}
	}, sleep );
	//Сохраняем позицию панели
	if(display != 'none')
		{
		if(which == 'width') width = to;
		else				height = to;
		}
	save_panel_dimension(id, width, height, display);
}
/*==================================================================*/