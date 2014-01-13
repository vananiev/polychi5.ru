<?php
	/************************************************************************************
			
					Модуль для оплаты через робокассу
			
	*************************************************************************************
	Требует наличия модуля money
	*/

	if(!defined('MODULE_MONEY')){ echo $_('ROBOKASSA: требуется модуль MONEY'); exit;}
	
	//описание модуля
	$INCLUDE_MODULES['ROBOKASSA']['INFO']['DESCRIPTION'] = 'Ввод и вывод денежных средств через Робокассу';
	
	// Конфигурация модуля
	require(dirname(__FILE__) . '/config.php');
	
	// Подключение к БД
	require(dirname(__FILE__) . '/DB_pay.php');
	
	// функции модуля
	require(dirname(__FILE__) . '/function.php');
	
	// добавляем пункты меню
	require(dirname(__FILE__) . '/files.php');
	
	if($URL['MODULE']=='ROBOKASSA'){
		switch($URL['FILE']){
			case 'result':		require_once(dirname(__FILE__) . '/result.php');          break;
		}
	}
	
	// событие вывода информации о способах оплаты
	$event->add('show_paymant_table', 'robokassa_paymant_table',4);
?>
