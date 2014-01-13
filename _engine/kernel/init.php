<?php
	/***************************************************************************

							Инициализация системы

	**************************************************************************/

	// режим вывода сообщений
	if(DEBUG_MODE === NULL)
		{
		ini_set('display_errors',0);
		ini_set('display_startup_errors',0);
		ini_set('error_reporting', 0);
		}
	else
		{
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		ini_set('error_reporting', DEBUG_MODE );
		}
	
	// включаем поддержку событий
	require_once(KERNEL_ROOT.'/event.php');
	
	//подключаем функции
	require(KERNEL_ROOT.'/function.php');

	//----- переносимость вне зависимости от конф PHP -----------	
	# убираем волшебные кавычки
	@set_magic_quotes_runtime(0);
	if (get_magic_quotes_gpc()) delete_magic_quotes();
	
	//---------------- работа с локалью --------------------------
	putenv('LANG='. LOCALE.'.'.SITE_CHARSET);
	putenv('LANGUAGE='. LOCALE.'.'.SITE_CHARSET);
	
	// Задаем текущую локаль + кодировку в формате язык_СТРАНА.кодировка(ru_RU.uft8)
	if( false === setlocale (LC_ALL, LOCALE.'.'.SITE_CHARSET) ) ;//user_error('Locale not exists: '.LOCALE.'.'.SITE_CHARSET, E_USER_WARNING);
	
	// Задаем каталог домена, где содержатся переводы
	bindtextdomain ( MO_FILE, LOCALE_ROOT );

	// Выбираем домен для работы
	textdomain (MO_FILE);

	// Кодировка для вывода текста через gettext()
	bind_textdomain_codeset(MO_FILE, SITE_CHARSET);

	// внутренняя кодировка (кодировка файлов)
	define('INTERNAL_CHARSET','utf8');
	if(INTERNAL_CHARSET !== SITE_CHARSET) define('CONVERT_CHARSET',true);
		else define('CONVERT_CHARSET',false);
		
	//------- перекодировки с помощью ob_iconv_handler() --------
	// внутренняя кодировка строк
	iconv_set_encoding('internal_encoding', INTERNAL_CHARSET);
	// отдаваемая кодировка
	iconv_set_encoding('output_encoding', SITE_CHARSET);
		
	//-------------- модуль работы с данными (БД) ---------------
	require(KERNEL_ROOT.'/data.php');

	//--- Ищем файл для отображения, исходя из строки запрса ----
	get_request_file();
	
	//----------------- Работа с меню -----------------------------
	require(dirname(__FILE__).'/menu.php');

	/* Следующая переменная содержит связи между страницами.
	Типичный пример страница настройки пользователя USERS/users_update.php
	имеет в других модулях связанные страницы, с помощью которых также можно настроить параметры пользователя.
	$LINKED_FILE[МОДУЛЬ][СТРАНИЦА][$i] = 'связанная_страница'
	*/
	$LINKED_FILE=array();
	
	//----------------- Загружаем модули ------------------------
	foreach($INCLUDE_MODULES as $NAME=>$MODULE)
		define('MODULE_'.$NAME, $NAME);	// какие модули подключаем
	foreach($INCLUDE_MODULES as $MODULE)
		require(SCRIPT_ROOT.'/'.$MODULE['PATH'].'/kernel/module.php');

	// извещаем о загрузке движка
	$event->create('init', NULL);
?>
