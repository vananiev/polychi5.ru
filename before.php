<?php
	/***************************************************************************

					Файл подключаемый до вывода <head>

	**************************************************************************/
?>
<?php
	$start_time = microtime(true);
	/************ Параметры конфигурации *************/
	require('config.php');

	/******************* Определяем кодирвку *********/
	header("Content-Type: text/html; charset=".SITE_CHARSET);

	//-------- Определяем сособ отображения контента -------------------------------
	if(isset($_GET['how_show_page']) && !isset($_COOKIE['how_show_page'])){
		setcookie ("how_show_page", $_GET['how_show_page'], time()+864000, "/", DOMAIN);
		$_COOKIE['how_show_page'] = $_GET['how_show_page'];
		}
	if((!isset($_GET['how_show_page']) || $_GET['how_show_page']=='all') && !isset($_COOKIE['how_show_page'])){
		setcookie ("how_show_page", "", time()-864000, "/", DOMAIN);
		unset($_COOKIE['how_show_page']);
		}
	if($_SERVER['REQUEST_URI']!='/' && !isset($_COOKIE['how_show_page'])){
		setcookie ("how_show_page", "body", time()+864000, "/", DOMAIN);
		$_COOKIE['how_show_page'] = "body";
		}
	
	/************ Инициализация сайта ****************/
	require(KERNEL_ROOT.'/init.php');
?>
