<?php
	/***************************************************************************

					Удаление модуля

	**************************************************************************/
?>
<?php
	// подключаем БД заданий
	require_once("DB_task.php");

//Удаляем тавлицу
mysql_query("DROP TABLE IF EXISTS $table_task",$msconnect_task) or die(mysql_error());

echo "Task: DB delete ok<br>";

//-------------------Удаляем действия -------------------------------------------------------
$solver_access = '';
$user_access = '';
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_MK_OK'", $msconnect_users) or die("Ошибка при удалении действия TSK_MK_OK.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_SHTRAV'", $msconnect_users) or die("Ошибка при удалении действия TSK_SHTRAV.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_SEE_OTHERS_TASK'", $msconnect_users) or die("Ошибка при удалении действия TSK_SEE_OTHERS_TASK.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_DEL_OTHERS_TASK'", $msconnect_users) or die("Ошибка при удалении действия TSK_DEL_OTHERS_TASK.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_CNG_STATUS'", $msconnect_users) or die("Ошибка при удалении действия TSK_CNG_STATUS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_SOLVING'", $msconnect_users) or die("Ошибка при удалении действия TSK_SOLVING.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_SOLVING_OTHERS'", $msconnect_users) or die("Ошибка при удалении действия TSK_SOLVING_OTHERS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_RATING_OTHERS'", $msconnect_users) or die("Ошибка при удалении действия TSK_RATING_OTHERS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_PRICING_OTHERS'", $msconnect_users) or die("Ошибка при удалении действия TSK_PRICING_OTHERS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TSK_SELECT_SOLVER_OTHERS'", $msconnect_users) or die("Ошибка при удалении действия TSK_SELECT_SOLVER_OTHERS.<br>".mysql_error());

echo "Task: Actions delete ok<br>";

//-------------------Удаляем группы ---------------------------------------------------------
mysql_query("DELETE IGNORE FROM $table_groups WHERE name='USER'", $msconnect_users) or die("Ошибка при удалении группы USER.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_groups WHERE name='SOLVER'", $msconnect_users) or die("Ошибка при удалении группы SOLVER.<br>".mysql_error());

echo "Task: Groups delete ok<br>";

//-------------------Удаляем необходимые папки ----------------------------------------------
	//$OWN="apache";
	$dirs = array (
		'WRITE'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['TASK']['PATH'], TASK_ROOT, SOLVING_ROOT, OPENED_SOLVING_ROOT),
		'EXEC'=>array()
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
echo "Task: удалите папку ".SCRIPT_ROOT.'/'.$INCLUDE_MODULES['TASK']['PATH']."<br>";
	
echo "Task: удалите из \$INCLUDE_MODULES ключ 'TASK' (config.php)<br>";
	
echo "Task Erase ok<br>";
?>