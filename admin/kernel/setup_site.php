<?php
	/*****************************************************
				Начальная установка модулей
	******************************************************
	
	!!!
	После выполения установки необходимо УДАЛИТЬ этот файл
	или перенести с сервера
	
	*/
?>
<?php
	// конфигурирование сайта
	require($_SERVER['DOCUMENT_ROOT']."/../config.php");
	
	// Инициализация сайта
	require(KERNEL_ROOT.'/init.php');
	
	header("Content-Type: text/html; charset=".SITE_CHARSET);	
	echo "Установка модулей...<br>";
	// устанавливаемые модули 
	// БУДЬТЕ ВНИМАТЕЛЬНЫ: если модуль устанавливается 2 раз,
	// вся ответственность по обработке этой ошибки ложится 
	// на сам модуль (файл setup.php модуля )
	$SETUP_MODULES = array(
							'USERS',		// Работа с пользователями
							'TASK',			// Работа с заданиями
							'TICKET',		// Модуль техподдержки
							'MONEY',		// Ведение записей по переводу средств
							'WEBMONEY'		// Работа с вебмани
							);

	require_once(KERNEL_ROOT."/setup.php");
	foreach($SETUP_MODULES as $KEY)
		if(file_exists(SCRIPT_ROOT."/".$INCLUDE_MODULES[$KEY]['PATH'].'/kernel/setup.php'))
			{echo "<br>Модуль: ".$INCLUDE_MODULES[$KEY]['PATH']."<br>";
			require_once(SCRIPT_ROOT."/".$INCLUDE_MODULES[$KEY]['PATH']."/kernel/setup.php");
			}
	echo "<br>Установка модулей завершена.<br>";
?>