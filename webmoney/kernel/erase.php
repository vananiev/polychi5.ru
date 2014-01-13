<?php
	/***************************************************************************

					Удаление модуля

	**************************************************************************/
?>
<?php
/*
status:
NOACCEPT - не были приняты условия покупателем на странице pay.php, переход в мерчант не осуществлен
NOFINISH - переход в мерчант осуществлен, но произошла ошибка при оплате
OK - оплачено, деньги зачислены
*/

// подключаем БД webmoney
require_once("DB_pay.php");
	
//удаляем тавлицу
mysql_query("DROP TABLE IF EXISTS $table_pay",$msconnect_pay) or die(mysql_error()); 

echo "Webmoney: DB Delete ok<br>";
//-------------------создаем действия -------------------------------------------------------	
mysql_query("DELETE IGNORE FROM $table_actions WHERE name='WMN_SEE_MON_IN'", $msconnect_task) or die("Ошибка при удалении действия WMN_SEE_MON_IN.<br>".mysql_error());

echo "Webmoney: Actions Delete ok<br>";
	
//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['WEBMONEY']['PATH']),
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

echo "Webmoney Erase ok<br>";
?>