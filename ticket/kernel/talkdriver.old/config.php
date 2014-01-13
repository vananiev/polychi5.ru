<?php
/*
			Конфигурация для рнаботы с мессенджером стороннего производителя TalkDriver
			Необходимо регистрироваться в TalkDriver.ru
		
*/

define('TD_PASS', '---');	// 'Пароль для ссылок' мессендера на сайте в личном кабинете http://talkdriver.ru/
define('TD_SID', '---');				// SID мессендера на сайте в личном кабинете http://talkdriver.ru/
define('TD_SITENAME', 'talkdriver.ru'); // можно настроить CNAME (синоним) в DNS, который будет ссылаться на talkdriver.ru:  help.polychi5.ru CNAME talkdriver.ru
define('MIN_USER_ID', 900000);			// минимальный user_id для незарегистрированного пользователя
define('MAX_USER_ID', 999999);			// максимальный user_id для незарегистрированного пользователя
define('MIN_CONSULTANT_ID', 1000000);	// минимальный user_id для человека из тех поддержки\
define('EXTERNAL_IP', '');// внешний ip, нужно указывать если сервер находится за NATб в противном случае == ""
$TD_CONSULTANT = array(					// люди из тех поддержки
	array('NAME'=>'Ирина',  'DEPARTMENT'=>'консультант отдела поддержки клиентов', 	 'FOTO'=>MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_1.png'),
	array('NAME'=>'Наталия','DEPARTMENT'=>'консультант отдела качества', 			 'FOTO'=>MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_2.png'),
	array('NAME'=>'Сергей', 'DEPARTMENT'=>'консультант отдела технической поддержки','FOTO'=>MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_3.png'),
	);
?>