<?php
	/*****************************************************
				Удаление модулей
	******************************************************
	
	!!!
	После выполения установки необходимо УДАЛИТЬ этот файл
	или перенести с сервера
	
	*/
?>
<?php
	//директория скриптов
	require_once($_SERVER['DOCUMENT_ROOT']."/../config.php");
	
	header("Content-Type: text/html; charset=".SITE_CHARSET);	
	echo "Удаление модулей...<br>";
	// устанавливаемые модули 
	// БУДЬТЕ ВНИМАТЕЛЬНЫ: если модуль удаляется 2 раза,
	// вся ответственность по обработке этой ошибки ложится 
	// на сам модуль (файл setup.php модуля )
	$ERASE_MODULES = array(
							'USERS',								// Работа с пользователями
							'TASK',									// Работа с заданиями
							'TICKET',								// Модуль техподдержки
							'MONEY',								// Ведение записей по переводу средств
							'WEBMONEY',								// Работа с вебмани
							);

	foreach($ERASE_MODULES as $KEY)
		if(file_exists(SCRIPT_ROOT."/".$INCLUDE_MODULES[$KEY]['PATH'].'/kernel/erase.php'))
			{echo "<br>Модуль: ".$INCLUDE_MODULES[$KEY]['PATH']."<br>";
			require_once(SCRIPT_ROOT."/".$INCLUDE_MODULES[$KEY]['PATH']."/kernel/erase.php");
			}
		else
			echo "<br>Модуль: ".$INCLUDE_MODULES[$KEY]['PATH']." требует ручного удаления<br>";
	echo "<br>Удаление модулей завершено.<br>";
?>