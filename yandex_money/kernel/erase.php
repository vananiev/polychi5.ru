<?php
	/***************************************************************************

					Удаление модуля

	**************************************************************************/
?>
<?php

// подключаем БД yandex money
require_once("DB_pay.php");
	
//удаляем тавлицу
$yandex_money->db->query("DROP TABLE IF EXISTS {$table_ym_pay}") or die($yandex_money->db->error()); 

echo "Yandex_Money: DB Delete ok<br>";
//-------------------создаем действия -------------------------------------------------------	
$yandex_money->db->query("DELETE IGNORE FROM $table_actions WHERE name='YM_SEE_MON_IN'") or die("Ошибка при удалении действия WMN_SEE_MON_IN.<br>".$yandex_money->db->error());

echo "Yandex_Money: Actions Delete ok<br>";
	
//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['YANDEX_MONEY']['PATH']),
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

echo "Yandex_Money Erase ok<br>";
?>