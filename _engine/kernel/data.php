<?php
	/***************************************************************************

							Модуль работы с данными

	**************************************************************************/

	// средство хранения данных
	define('DATA_STORAGE','MYSQL');

	//----------------------------- подключение классов средства хранения ------------------------------
	if(defined('DATA_STORAGE'))
		{
		if(DATA_STORAGE === 'MYSQL') 	
			{
			include(KERNEL_ROOT.'/storage_mysql.php');
			class DataStorage extends dbMySql
				{
				function __construct ($host = DB_HOST, $user = DB_USER, $password = DB_PASSWORD, $db_name = DB_NAME, $charset = SITE_CHARSET, $db_time_zone = DB_TIME_ZONE, $db_port = DB_PORT, $socket = NULL )
					{
					parent::__construct($host, $user, $password, $db_name, $charset, $db_time_zone, $db_port, $socket);
					return $this;
					}
				}
			}
		}
	else 	// не определено место хранения данных
		debug_msg($_('Не определено место хранения данных'),__FILE__,__LINE__);
	
	
	
	// перемеренная хранения системных данных($db) и способ хранения (DataStorage) информации
	$db = new DataStorage();	// когда появится поддержка новых средств хранения строку заменить на $db = new DataStorage();
	
	
	
	//---------------------- определение системных таблиц хранения информации --------------------------
	$table_linkblocks = 'linkblocks';		// блоки ссылок
	
	
	
	
	//--------------------------- настройка средства хранения ------------------------------------------
	if(DATA_STORAGE === 'MYSQL')
		{
		;
		}
?>