// кросплатформенный способ созать событие Пример вызова:
// fireEvent(document.getElementById('iContactThisSite'),'click');
function fireEvent(obj,evt){
	
	var fireOnThis = obj;
	if( document.createEvent ) {
	  var evObj = document.createEvent('MouseEvents');
	  evObj.initEvent( evt, true, false );
	  fireOnThis.dispatchEvent(evObj);
	} else if( document.createEventObject ) {
	  fireOnThis.fireEvent('on'+evt);
	}
}
// ------------ Выводит диалог для общения с техподдержкой ---------------------------
function ShowSupportDlg(){
	// если мы не вошли
	if(document.getElementById('ibuddiesCount').innerHTML == "Общайтесь с друзьями")
		{fireEvent(document.getElementById('ibuddiesCount'),'click');} 	// показываем окно входа
	else
		{fireEvent(document.getElementById('iContactThisSite'),'click'); } // показываем окно для общения iContactThisSite
}
/*function initOnLoad(e)
	{  
		//Проверка e.length необходима, если нужно знать, имеет ли элемент вложенные элементы. Если нужно лишь удостовериться в наличии элемента, то эту строку нужно убрать, а в startMonitoring() использовать getElementById
		if(e != null){loadComplete();}//if(e != null && e.length != 0) 
		else {setTimeout(function()	   {initOnLoad(e);}, 0);}
	}

function loadComplete()
	{
		//Действия после появления необходимого элемента в DOM
		ShowSupportDlg();
	}
	
function startMonitoring()
	{
		//"Прослушиваемый" элемент
		e = document.getElementById('icqBar');
		initOnLoad(e);
	}
startMonitoring();*/
