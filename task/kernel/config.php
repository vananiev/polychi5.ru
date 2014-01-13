<?php
	/***************************************************************************

						Конфигурационный файл

	***************************************************************************/
	
	//папка c заданиями
	define('TASK_ROOT_RELATIVE' , '/m/'.MODULES_MEDIA_RELATIVE.$INCLUDE_MODULES['TASK']['PATH']."/task/");
	define('TASK_ROOT' , DOCUMENT_ROOT.'/'.TASK_ROOT_RELATIVE);

	//папка с решениями
	define('SOLVING_ROOT_RELATIVE' , 'admin/solving');		// должны отсутствовать слешы в начале и конце
	define('SOLVING_ROOT' , SCRIPT_ROOT.'/'.$INCLUDE_MODULES['TASK']['PATH'].'/'.SOLVING_ROOT_RELATIVE.'/');

	//папка с ОТКРЫТЫМИ решениями - старая схема, если новая прижилась, значит удалить
	//define('OPENED_SOLVING_ROOT_RELATIVE' , MODULES_MEDIA_RELATIVE.$INCLUDE_MODULES['TASK']['PATH'].'/opened_solving/');
	//define('OPENED_SOLVING_ROOT' , $_SERVER['DOCUMENT_ROOT'].OPENED_SOLVING_ROOT_RELATIVE);

	// создавать тему (обсуждение) для каждого вопроса
	define('TASK_DIALOG', true);
?>