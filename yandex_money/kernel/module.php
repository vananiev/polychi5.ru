<?php
	/************************************************************************************
			
					Модуль для оплаты через яндекс деньги
			
	*************************************************************************************
	https://github.com/melnikovdv/PHP-Yandex.Money-API-SDK  <-- Исходный код и примеры
	Требует наличия модуля money
	*/

	if(!defined('MODULE_MONEY')){ echo $_('YANDEX_MONEY: требуется модуль MONEY'); exit;}
	
	//описание модуля
	$INCLUDE_MODULES['YANDEX_MONEY']['INFO']['DESCRIPTION'] = 'Ввод и вывод денежных средств через Яндекс.Деньги';
	
	// Подключение к БД
	require(dirname(__FILE__) . '/DB_pay.php');
	
	// функции модуля
	require(dirname(__FILE__) . '/function.php');
	
	// добавляем пункты меню
	require(dirname(__FILE__) . '/files.php');
	
	/*if($URL['MODULE']=='YANDEX_MONEY'){
		switch($URL['FILE']){
			case 'authorize':	require_once(dirname(__FILE__) . '/authorize.php'); exit; break;
			//case 'result':		require_once(dirname(__FILE__) . '/result.php');          break;
		}
	}*/
	
	// событие вывода информации о способах оплаты
	$event->add('show_paymant_table', 'ym_paymant_table',1);
?>
