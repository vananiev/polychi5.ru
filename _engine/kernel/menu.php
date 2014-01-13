<?php
	/***************************************************************************

							–едактирование и вывод меню

	**************************************************************************/

	class Menu {

	//---------------------------------- ¬ывод блока ссылок ---------------------------------------------------------------
	// ID 	- номер или им€ блока ссылок
	// TEG 	- в каком html-теге вывести ссылки
	function &get($ID, $TEG='li')
	{
		global $URL;
		global $FILE;
		global $db;
		global $table_linkblocks;
		$ret='';
		//ищем в кеше
		$key = __CLASS__ . __FUNCTION__ . $ID;
		$linkblocks = &GET_VAR($key);
		if($linkblocks == NULL)
			{
			if(is_int($ID))
				$res2 = $db->query("SELECT * FROM `$table_linkblocks` WHERE `id`='%u' LIMIT 1", $ID);
			else
				$res2 = $db->query("SELECT * FROM `$table_linkblocks` WHERE `name`='%s' LIMIT 1", $ID);
			//  ешируем результаты Ѕƒ
			$tmp = &$db->fetch($res2);
			if(isset($tmp[0]))
				$linkblocks = &SET_VAR($key, $tmp[0]);
			else
				$linkblocks = &SET_VAR($key, $r=false);
			}
		if(!$linkblocks) return SET_VAR('a123', $r=''); // не  найден блок ссылок
		
		$module = explode(',', $linkblocks['MODULE']);
		$file = explode(',', $linkblocks['FILE']);
		$args = explode(',', $linkblocks['ARGS']);
		$page = explode(',', $linkblocks['PAGE']);
		$tegs = explode(',', $linkblocks['TEGS']);
		$cnt = count($module);
		for($i=0; $i < $cnt; $i++)		// перебор ссылок
			if(isset($module[$i]) && $module[$i]!='')
				{
				if(isset($FILE[$module[$i]][$file[$i]]['ANCHOR'])) $ankor = $FILE[$module[$i]][$file[$i]]['ANCHOR'];		// получаем анкор
				else if(isset($FILE[$module[$i]][$file[$i]]['TITLE'])) $ankor = $FILE[$module[$i]][$file[$i]]['TITLE'];
				elseif($module[$i] == 'OPTIONAL') {$ankor= $args[$i]; $args[$i]=NULL; $page[$i]=NULL;}
				else $ankor = '_';
				$ret .= 	'<'.$TEG.(( $URL['FILE']==$file[$i] && $URL['MODULE']==$module[$i] )?' class=\'active\'':'').'>	'.url($ankor,$module[$i],$file[$i],$args[$i],$page[$i],$tegs[$i]).'	</'.$TEG.'> ';
				}
		return $ret;		
	}
	
	//---------------------------------- ”даление блока ссылок ---------------------------------------------------------------
	// ID - номер или им€ блока ссылок
	function delete($ID)
	{
		global $db;
		global $table_linkblocks;
		if(is_int($ID))
			if(!$db->query("DELETE FROM `$table_linkblocks` WHERE id = %u", $ID)) return false;
		else
			if(!$db->query("DELETE FROM `$table_linkblocks` WHERE name = %s", $ID)) return false;
		return true;
	}
	
	//----------------------------------- —оздать блока ссылок ---------------------------------------------------------------
	// NAME - им€ блока ссылок
	function add($NAME)
	{
		global $db;
		global $table_linkblocks;
		if($NAME == 'GLOBAL') user_error('GLOBAL');
		if($db->query("INSERT INTO `$table_linkblocks` (name,MODULE,FILE,ARGS,PAGE,TEGS,description)	VALUES('%s','','','','','','')", $NAME))
			return true;
		return false;
	}
		
	//----------------------------------- ќбновить блок ссылок ---------------------------------------------------------------
	// ID 		- номер или им€ блока ссылок
	// LINKS 	- ссылки на сохранение  = array ( module, file, args, page, tegs) 
	// DESCRIPTION - описание
	function update($ID, &$LINKS, &$DESCRIPTION = '')
	{
		global $db;
		global $table_linkblocks;
		// если передаетс€ разное количество параметров ссылок
		$cnt = count($LINKS);
		$module = '';
		$file = '';
		$args = '';
		$page = '';
		$tegs = '';
		// запись ссылок
		for($i=0; $i < $cnt; $i++)
			{
			if(!isset($LINKS[$i][0])) $LINKS[$i][0]='';
			if(!isset($LINKS[$i][1])) $LINKS[$i][1]='';
			if(!isset($LINKS[$i][2])) $LINKS[$i][2]='';
			if(!isset($LINKS[$i][3]) || !is_numeric($LINKS[$i][3])) $LINKS[$i][3]='';
			if(!isset($LINKS[$i][4])) $LINKS[$i][4]='';
			if(ereg('[<>]',$LINKS[$i][4])) $LINKS[$i][4] = ''; // если имеютс€ запрещенные символы
			$module .= $LINKS[$i][0].',';
			$file 	.= $LINKS[$i][1].',';
			$args 	.= $LINKS[$i][2].',';
			$page 	.= $LINKS[$i][3].',';
			$tegs 	.= $LINKS[$i][4].',';
			}
		if(is_int($ID))
			$query = "UPDATE `$table_linkblocks`
				SET `MODULE` = '%s',
				`FILE` = '%s',
				`ARGS` = '%s',
				`PAGE` = '%s',
				`TEGS` = '%s',
				`description` = '%s'
				WHERE id = '%u'";
		else
			$query = "UPDATE `$table_linkblocks`
				SET `MODULE` = '%s',
				`FILE` = '%s',
				`ARGS` = '%s',
				`PAGE` = '%s',
				`TEGS` = '%s',
				`description` = '%s'
				WHERE id = '%s'";
		if( $db->query($query,array($module, $file, $args, $page, $tegs, $DESCRIPTION, $ID)) ) return true;
		return false;
	}
	
	//----------------------------------- ѕрочитать блок ссылок ---------------------------------------------------------------
	// ID - номер или им€ блока ссылок
	// возврат значений идет через $LINKS = array ( module, file, args, page, tegs) и $DESCRIPTION
	function read($ID, &$LINKS, &$DESCRIPTION)
	{
		global $db;
		global $table_linkblocks;
		if(is_int($ID))
			$res=$db->query("SELECT * FROM `$table_linkblocks` WHERE id = '%u'", $ID);
		else
			$res=$db->query("SELECT * FROM `$table_linkblocks` WHERE name = '%s'", $ID);
		if(!$res) return false;
		$linkblocks = $res->fetch_assoc();
		$m = explode(',', $linkblocks['MODULE']);
		$f = explode(',', $linkblocks['FILE']);
		$a = explode(',', $linkblocks['ARGS']);
		$p = explode(',', $linkblocks['PAGE']);
		$t = explode(',', $linkblocks['TEGS']);
		$LINKS=array();
		$cnt = count($m);
		for($i=0; $i < $cnt; $i++)
			if(isset($m[$i]) && isset($f[$i]) && isset($a[$i]) && isset($p[$i]) && isset($t[$i]) && $m[$i]!='' && $f[$i]!='' )
				$LINKS[] = array($m[$i], $f[$i], $a[$i], $p[$i], $t[$i]);
		$DESCRIPTION = $linkblocks['description'];
		return true;
	}
	}
	
	
	$MENU = new Menu();
?>
