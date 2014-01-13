<?php
	/***************************************************************************

					Удаление модуля

	**************************************************************************/
?>
<?php
	// Подключаем БД
	require_once("DB_ticket.php"); 
//Удаляем тавлицу
mysql_query("DROP TABLE IF EXISTS $table_tickets",$msconnect_ticket) or die(mysql_error());
mysql_query("DROP TABLE IF EXISTS $table_questions",$msconnect_ticket) or die(mysql_error());

echo "Tickets DB Delete ok<br>";

//-------------------создаем действия -------------------------------------------------------	
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TKT_SUPPORT_ANS'", $msconnect_users) or die("Ошибка при удалении действия TKT_SUPPORT_ANS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TKT_CNG_STATUS'", $msconnect_users) or die("Ошибка при удалении действия TKT_CNG_STATUS.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TKT_DEL_MODF_MSG'", $msconnect_users) or die("Ошибка при удалении действия TKT_DEL_MODF_MSG.<br>".mysql_error());
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='TKT_DEL_OTHERS_TICKET'", $msconnect_users) or die("Ошибка при удалении действия TKT_DEL_OTHERS_TICKET.<br>".mysql_error());

echo "Tickets: Actions Delete ok<br>";

//создаем группы
mysql_query("DELETE IGNORE FROM $table_groups WHERE name='SUPPORT'", $msconnect_users) or die("Ошибка при удалении группы SUPPORT.<br>".mysql_error());
	
echo "Users: Groups Delete ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	//на запись
	$dirs = array (
		'WRITE'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['TICKET']['PATH'], TICKET_FILE),
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

echo "Ticket Erase ok<br>";
?>