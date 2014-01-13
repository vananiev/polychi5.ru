<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/
?>
<?php
/*
status:
NOACCEPT - не были приняты условия покупателем на странице pay.php, переход в мерчант не осуществлен
NOFINISH - переход в мерчант осуществлен, но произошла ошибка при оплате
OK - оплачено, деньги зачислены
*/

//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "WEBMONEY: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "WEBMONEY: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		return;
		}

// подключаем БД webmoney
require_once("DB_pay.php");

//если нет БД то создаем ее
mysql_query("CREATE DATABASE IF NOT EXISTS ".DB_WEBMONEY_NAME." CHARACTER SET ".DB_WEBMONEY_CHARSET,$GLOBALS['msconnect_pay']) or die(mysql_error());

//создаем тавлицу
mysql_query("CREATE TABLE IF NOT EXISTS $table_pay ( 
	id int(10) unsigned NOT NULL auto_increment,
	Koshelek_prodavcha char(16),
	Symma int(10),
	testovii_platezh char(1),
	nomer_scheta_vm varchar(32),
	nomer_platezha_vm varchar(32),
	data_platezha char(17),	
	id_pokypatelya int(10),
	Koshelek_pokypatelya char(16),
	fone varchar(11) NOT NULL DEFAULT '7----------',
	wmid_pokypatelya varchar(32),
	status varchar(8),
	PRIMARY KEY  (`ID`)
	) CHARSET=utf8",$msconnect_pay) or die(mysql_error()); 

//-------------------создаем действия -------------------------------------------------------	
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('WMN_SEE_MON_IN','Разрешает просмотр ввода средств через вебмани')", $msconnect_task) or die("Ошибка при регистрации действия WMN_SEE_MON_IN.<br>".mysql_error());

echo "Webmoney: Actions Make ok<br>";
	
//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['WEBMONEY']['PATH']),
		'WRITE'=>array()
		);
	foreach($dirs['WRITE'] as $dir)
		{
		if(@mkdir($dir,FOLDER_WRITE)==false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_WRITE)==false)echo("Права (".decoct(FOLDER_WRITE).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
	foreach($dirs['EXEC'] as $dir)
		{
		if(@mkdir($dir,FOLDER_EXEC)==false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_EXEC)==false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
echo "Webmoney: папки созданы, права на данные папки выставлены<br>";
	
echo "Webmoney DB Make ok<br>";
echo "Webmoney Setup ok<br>";
?>