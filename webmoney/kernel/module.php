<?php
	/***************************************************************************

				Модуль для работы с вебмани

	**************************************************************************/

	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "WEBMONEY: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "WEBMONEY: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		return;
		}	

	// конфигурация модуля
	require('config.php');

	// функции модуля
	require(dirname(__FILE__) . '/function.php');
	
	//описание модуля
	$INCLUDE_MODULES['WEBMONEY']['INFO']['DESCRIPTION'] = 'Ввод и вывод денежных средств через webmoney';
	
	// подключаем БД webmoney
	require_once("DB_pay.php");

	//оплата с помощью webmoney
	if($URL['MODULE'] == 'WEBMONEY' && $URL['FILE'] == "result")
		{
		require_once(SCRIPT_ROOT."/webmoney/kernel/result.php");
		exit;	
		}
	
	// добавляем пункт меню
	require('files.php');
	
	// событие вывода информации о способах оплаты
	$event->add('show_paymant_table', 'wm_paymant_table',3);
?>
