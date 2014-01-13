<?php
	/*********************************************************

					Конфигурация сайта

	**********************************************************

*/?>
<?php
	//-----------работа с сессиями---------------------------
  preg_match("/([^\.]+\.[^\.]+)$/", $_SERVER['HTTP_HOST'], $matches);
	define('DOMAIN', $matches[0]);
  /*чтобы сессия на мобильном и полноценном сайте была одна и та же*/
	session_set_cookie_params(0, '/', '.'.DOMAIN); // set one SESSIONID for all subdomains
	session_start();
?>
<?php

	//отображаем ошибки
	define('DEBUG_MODE', NULL); //E_ALL, E_ERROR, E_WARNING, NULL

	//Имя компании
	define('COMPANY_NAME' , 'Получи [5]');

	//Перевод строки

	// Ключевые слова по умолчанию
	define('META_KEYWORDS','Заказать недорого контрольную, физика, математика, программирование');

	// Описание сайта по умолчанию
	define('META_DESCRIPTION','Недорого! Заказ контрольной работы по математике, физике, химии, информатике и другим предметам.');

	//директория общедоступных файлов
	@define('DOCUMENT_ROOT' , ereg_replace("/public_html/.*", "/public_html/",$_SERVER['DOCUMENT_ROOT']));

	//директория скриптов
	define('SCRIPT_ROOT' , DOCUMENT_ROOT."/../");

	define('MOBILE_ROOT' , DOCUMENT_ROOT."/m/");

	define('MOBILE_DOMAIN', 'm.'.DOMAIN);

	define('MAIL_DOMAIN','polychi5.ru');

	//путь к движку
	define('ENGINE_ROOT' , SCRIPT_ROOT.'/_engine/');

	//путь к ядру
	define('KERNEL_ROOT' , ENGINE_ROOT.'/kernel/');

	//набор символов сайта
	define('SITE_CHARSET', 'utf8'); //cp1251, utf8, latin1

	//Набор симфолов в БД
	define('DB_CHARSET', 'utf8');

	// Локаль (влияет на вывод некоторых php функций, напр: даты, и на перевод посредство gettext())
	define('LOCALE', 'ru_RU');		//зависит от ОС на которой работает сервер (ru_RU, en_US ...)

	// директория с локалями
	define('LOCALE_ROOT', ENGINE_ROOT.'/locale/');

	// имя *.mo-файла для перевода через gettext() (см.)
	define('MO_FILE', 'main');

	//случайная(!) последовательность символов (безопасность)
	define('SECRET', '---');

	//Установка часового пояса для сайта
	if(isset($_SESSION['time_zone']) && @date_default_timezone_set($_SESSION['time_zone']))
		define('TIME_ZONE',$_SESSION['time_zone']);	//Установка временной зоны, если пользователь вошел
	else
		{
		define('TIME_ZONE','Etc/GMT-3');	//Europe/Moscow Установка временной зоны, если пользователь не вошел
		date_default_timezone_set(TIME_ZONE);
		unset($_SESSION['time_zone']);
		}

	//Глобальные параметры БД
	//(могут переопределяться БД для конкретного модуля - см. файл модуль/kernel/DB.php для распределения нагрузки и исп. разных БД)
	//если не исп. БД, то не указывается
	define('DB_HOST', 'localhost');
	define('DB_PORT', '3306');
	define('DB_USER', '---');
	define('DB_PASSWORD', '---');
	define('DB_NAME', '---');
	define('DB_TIME_ZONE',substr(date("O"),0,3).":00");

	//Формат даты-времени
	define('DATE_TIME_FORMAT', 'd M,  G:i \'Y');

	//временная папка
	define('TEMP_ROOT_RELATIVE' , "/files/temp/");
	define('TEMP_ROOT' , DOCUMENT_ROOT . TEMP_ROOT_RELATIVE);

	//папка темы сайта
	define('THEME_ROOT_RELATIVE', '/files/themes/default/');
	define('THEME_ROOT', DOCUMENT_ROOT . THEME_ROOT_RELATIVE);

	//папка темы кабинета
	define('CABINET_THEME_ROOT_RELATIVE', '/files/modules/cabinet/themes/default');
	define('CABINET_THEME_ROOT', DOCUMENT_ROOT . CABINET_THEME_ROOT_RELATIVE);

	//папка с медиафайлами для темы
	define('THEME_MEDIA_RELATIVE', '/files/themes/default/');
	define('THEME_MEDIA', DOCUMENT_ROOT.THEME_MEDIA_RELATIVE);

	//папка с медиафайлами для модулей
	define('MODULES_MEDIA_RELATIVE', '/files/modules/');
	define('MODULES_MEDIA', DOCUMENT_ROOT.MODULES_MEDIA_RELATIVE);

	//папка с plugins
	define('PLUGING_ROOT_RELATIVE', '/files/plugins/');
	define('PLUGING_ROOT', DOCUMENT_ROOT.PLUGING_ROOT_RELATIVE);

	//папка с javascript
	define('JS_ROOT_REL', '/files/js/');
	define('JS_ROOT', DOCUMENT_ROOT.JS_ROOT_REL);

	//папка с общедоступными файлами для движка
	define('ENGINE_MEDIA_RELATIVE' , '/files/_engine/');
	define('ENGINE_MEDIA', DOCUMENT_ROOT.ENGINE_MEDIA_RELATIVE);

	//размер закачиваемых файлов в Мб
	define('MAX_FILE_SIZE',10);

	// Страница по умолчанию
	define('DEFAULT_PAGE', "/task/add_task.html");

	//Используем ЧПУ или нет
	define('USE_FRIENDLY_URL', true);

	// Настройки ЧПУ
	define('URL_MODEL', "/(MODULE)/(FILE)(PAGE)\.html(ARGS)"); 		// Использование () обязателооьно, все что в круглых скобка возвращается функцией в виде отдельной переменной
	define('URL_SPLITTER',  '.');									//символ, вставляемый между переменными ((FILE), (PAGE) или другими), если они находятся сразу друг за другом, без делителя
	define('URL_SPLITTER_REG', '[\.]?');							//необходимо ли использовать escape-последовательность для данного разделительного символа для '-' необходимо '\-'
																	// для '_' остается также '_' (смори функцию ereg())
	//права на файлы  и папки при создании через php (установка)
	// не забываем, что владельцем и группой, при создании файлов из php назначается apache:apache
	define('FOLDER_EXEC' ,0710);	//из папки можно читать файлы, но нет списка файлов (R)
	define('FOLDER_WRITE' ,0730);
	define('FILE_READ' ,0640);
	define('FILE_WRITE' ,0660);
	define('FILE_EXEC' ,0700);

	/*----------------------- Модули ---------------------------------------
	задается в формате:
			array('PATH'=>'ПУТЬ_К_ПАПКЕ',  'PAGE'=>'ПЕРЕМЕННАЯ_ЗАПРОСА',	'INTRODUCE'=>FALSE)
	'PAGE' == NULL 			- доступ из броузера до модуля не возможен
	'INTRODUCE'=>'TRUE'		- внедренный модуль, т.е. файлы этого_модуля могут встречаться в подпаке 'PATH ' папки_других_модулей
	*/
	$INCLUDE_MODULES = array(
								'ADMIN'=>		array('PATH'=>'admin', 	'PAGE'=>'admin',	'INTRODUCE'=>FALSE),		// Админка
								'USERS'=>		array('PATH'=>'users',	'PAGE'=>'users',	'INTRODUCE'=>FALSE),		// Работа с пользователями
								'TASK'=>		array('PATH'=>'task',	'PAGE'=>'task',		'INTRODUCE'=>FALSE),		// Работа с заданиями
								'TICKET'=>		array('PATH'=>'ticket',	'PAGE'=>'ticket',	'INTRODUCE'=>FALSE),		// Модуль техподдержки
								'MONEY'=>		array('PATH'=>'money', 	'PAGE'=>'money',	'INTRODUCE'=>FALSE),		// Ведение записей по переводу средств
								'WEBMONEY'=>	array('PATH'=>'webmoney','PAGE'=>'webmoney','INTRODUCE'=>FALSE),		// Работа с вебмани
								'YANDEX_MONEY'=>array('PATH'=>'yandex_money','PAGE'=>'yandex','INTRODUCE'=>FALSE),		// Работа с яндекс деньгами
								'ROBOKASSA'=>	array('PATH'=>'robokassa','PAGE'=>'robokassa','INTRODUCE'=>FALSE),		// Работа с робокассой
								'INFO'=>		array('PATH'=>'info',	'PAGE'=>'info',		'INTRODUCE'=>FALSE),		// Раздел с информацией
								'CABINET'=>		array('PATH'=>'cabinet','PAGE'=>'cabinet',	'INTRODUCE'=>FALSE),		// Личный кабинет пользователя
								'WORDPRESS'=>	array('PATH'=>'wordpress','PAGE'=>'wp',		'INTRODUCE'=>FALSE),		// Вордпресс
								);
?>
