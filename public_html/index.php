<?php
	/*********** Загрузка конфигурационных файлов *******************/
	require_once($_SERVER['DOCUMENT_ROOT']."/../before.php");

	/******************* Шапка сайта ********************************/
	if( strpos($URL['FILE'], 'admin')===false && $URL['MODULE'] !== 'CABINET' && $URL['MODULE'] !== 'ADMIN')
		require(THEME_ROOT."/head.php");
	else
		require(CABINET_THEME_ROOT."/head.php");
	?>
	<?php
	// добаляем файл стилей для модуля
	foreach($INCLUDE_MODULES as $name=>$module){
		$dir = media_dir($name);
		if(file_exists( DOCUMENT_ROOT.$dir.'/css/module.css')){ ?><link href="<?php echo $dir.'/css/module.css';?>" type="text/css" rel="stylesheet"><?php echo "\n";}
		}
	// добавляем тег линк
	foreach($LINK_TEGS as $link){ echo "<link href='{$link['HREF']}' rel='{$link['REL']}' type='{$link['TYPE']}'>\n"; }

	?><script type="text/javascript" src="<?php echo JS_ROOT_REL?>/jquery-1.8.2.js"></script>
	<script type="text/javascript" src="<?php echo JS_ROOT_REL?>/jquery.easing.1.3.js"></script><?php /* ускорение в анимации */ ?>
	<?php /* Список библиотек для эффектов http://mattweb.ru/component/k2/item/104-5-bibliotek-dlya-sozdaniya-yarkih-css-effektov */ ?>
	<link href="/files/css/animate.min.css" type="text/css" rel="stylesheet">	<?php /* http://daneden.github.io/animate.css/ */ ?>
	<link href="/files/css/hover-min.css" type="text/css" rel="stylesheet"> 	<?php /* http://ianlunn.github.io/Hover/ */ ?>
	<script type="text/javascript" src="/files/js/wow.min.js"></script> 		<?php /* https://github.com/matthieua/WOW */ ?>
	<link href="/files/css/jquery-ui.min.css" type="text/css" rel="stylesheet"> 	<?php /* autocomplete / */ ?>
	<script type="text/javascript" src="/files/js/jquery-ui.min.js"></script> 		<?php /* autocomplete */ ?>
	<?php
	/******************* Подключаем плагины *************************/
	require(PLUGING_ROOT."/plugins.php");
	/******************* Виджет сайта для Яндекс.Броузер ************/
	?><link rel="yandex-tableau-widget" href="http://polychi5.ru/files/plugins/yandex-tableau.json"/>
	<meta name='yandex-verification' content='43d19f4f4381c2de' />
	<?php
	/******************* Тело сайта *********************************/
	if($_SERVER['REQUEST_URI']=='/')
		require(THEME_ROOT."/index.php");
	else if( strpos($URL['FILE'], 'admin')===false && $URL['MODULE'] !== 'CABINET' && $URL['MODULE'] !== 'ADMIN' )
		require(THEME_ROOT."/body.php");
	else
		require(CABINET_THEME_ROOT."/body.php");

	/****************** Завершающий скрипт **************************/
	require(SCRIPT_ROOT."/after.php");
?>
