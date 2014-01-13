<?php
	/***************************************************************************

					Удаление модуля

	**************************************************************************/
?>
<?php
	// Подключаем БД
	require_once("DB_money.php");
	
	//создаем тавлицу
	mysql_query("DROP TABLE IF EXISTS $table_money",$msconnect_money) or die(mysql_error());
	echo "Money DB Delete ok<br>";
	
//-------------------создаем действия -------------------------------------------------------	
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='MNY_SEE_SYS_BAL'", $msconnect_users) or die("Ошибка при удалении действия MNY_SEE_SYS_BAL.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='MNY_CNG_USR_BAL'", $msconnect_users) or die("Ошибка при удалении действия MNY_CNG_USR_BAL.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='MNY_SYS_HISTORY'", $msconnect_users) or die("Ошибка при удалении действия MNY_SYS_HISTORY.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='MNY_SYS_TRANSFER'", $msconnect_users) or die("Ошибка при удалении действия MNY_TRANSFER.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='MNY_OUT'", $msconnect_users) or die("Ошибка при удалении действия MNY_OUT.<br>".mysql_error());

echo "Money: Actions Delete ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['MONEY']['PATH']),
		'WRITE'=>array()
		);
	foreach($dirs['WRITE'] as $dir)
		{
		$obj=search_dir($dir,'*.*',0,1000);
		foreach($obj['file'] as $file)
			if(!unlink($file)) echo("Ошибка удаления файла: $file<br>");
		foreach($obj['dir'] as $d)
			if(!rmdir($d)) echo("Ошибка удаления папки: $d<br>");
		if(!rmdir($dir))echo("Ошибка удаления папки: $dir<br>");
		}
	foreach($dirs['EXEC'] as $dir)
		{
		$obj=search_dir($dir,'*.*',0,1000);
		foreach($obj['file'] as $file)
			if(!unlink($file)) echo("Ошибка удаления файла: $file<br>");
		foreach($obj['dir'] as $d)
			if(!rmdir($d)) echo("Ошибка удаления папки: $d<br>");
		if(!rmdir($dir))echo("Ошибка удаления папки: $dir<br>");
		}
	
	echo "Money Erase ok<br>";
?>