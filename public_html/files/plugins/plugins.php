<?php
	/***************************************************************************

				Файл подключает плагины

	**************************************************************************/

	// Подключаем скрипты java
?>
	<script type="text/javascript" src="<?php echo JS_ROOT_REL;?>/functions.js"></script>
<?php	
	// конфигурация плагинов
	require('config.php');
	
	foreach($INCLUDE_PLUGINS as $NAME=>$PLUGIN)
		define('PLUGIN_'.$NAME, $NAME);	// какие плагины были подключены
	foreach($INCLUDE_PLUGINS as $PLUGIN)
		require_once(PLUGING_ROOT."/".$PLUGIN."/plugin.php");//подключаем
?>