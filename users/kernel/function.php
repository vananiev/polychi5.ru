<?php
	/***************************************************************************

					Функции для модуля USERS

	**************************************************************************/

//-------------------- Вывод аватара ----------------------
function output_avatar($user_id,$tegs='')
{
	echo show_avatar($user_id,$tegs);
}
//-------------------- Получение ссылки на аватар ----------------------
function show_avatar($user_id,$tegs='')
{
	$usr= get_user($user_id,'photo');
	if( $usr['photo'] != NULL )
		return "<img class='avatar' src=\"". $usr['photo'] ."\" $tegs>";

	$files = glob( AVATAR_ROOT.'/'.$user_id.'.*');
	if(!isset($files[0]))
		return "<img class='avatar' src=\"".AVATAR_ROOT_RELATIVE."default.png\" $tegs>";
	else
		return "<img class='avatar' src=\"".AVATAR_ROOT_RELATIVE.'/'.basename($files[0])."\" $tegs>";
}
//------------------получение ссылки на пользователя -----------
function get_user_link($user_id,$teg_a='',$text='')
{
	global $USER, $table_users, $users;
	if($USER['id'] == $user_id){
		if(empty($text)) $text = empty($USER['name'])?$USER['login']:$USER['name'];
	}else{
		if(empty($text))
			{
			$res = $users->db->query("SELECT name,login FROM $table_users WHERE id = '%d' LIMIT 0,1",$user_id) or die("Ошибка чтения таблицы users<br>.".$users->db->error());
			$row = $res->fetch_assoc();
			$text = empty($row['name'])?$row['login']:$row['name'];
			}
	}
	return url($text, 'USERS', 'about_user', 'user_id='.$user_id, NULL, $teg_a);
}
//------------------- Получить информация о пользователе по id -----------
function get_user($usr = NULL, $fields = '*')
{
	if( $usr !== NULL && !is_numeric($usr)) return false;
	$ROW = NULL;
	if($usr===NULL && isset($_SESSION['user_id']))
		$usr = $_SESSION['user_id'];
	elseif($usr===NULL && !isset($_SESSION['user_id']))
		return $ROW;
	global $users, $table_users;
	return $users->db->row($table_users, (int)$usr, $fields);
}
//------------------- Получить id пользователя по login -----------
function get_user_id($login)
{
	global $users, $table_users;
	// получаем id
	$sql = $users->db->query("SELECT `id` FROM `$table_users` WHERE login='%s' LIMIT 1",$login) or die($users->db->error());
	if($sql->num_rows==0) return NULL;
	$row = $sql->fetch_assoc();
	if( ((int)$row['id']) == $row['id'] )
		return (int)$row['id'];
	else
		return NULL;
}
//-------------------- Получение всех прав пользователя ---------
function get_user_rights($usr = NULL)		//$usr - id-пользователя, если не передан, то берется текущий пользователь.
											// $usr = NULL - ,если текущий пользователь не регистрированный
{
	$RIGHTS = "";	//права по умолчанию
	global $msconnect_users;
	if($usr === NULL)
		{
		if(isset($_SESSION['user_id']))
			{
			if(isset($_SESSION['USER_RIGHTS']))
				return $_SESSION['RIGHTS'];			//<--- уже просчитывали всю функцию
			$usr = $_SESSION['user_id'];			// выбор текущего пользователя
			global $USER;
			$groups = $USER['groups'];
			}
		else
			return $RIGHTS;
		}
	else	// задан конкретный пользователь
		{
		$key = 'get_user_rights_'.$usr;
		if(isset($GLOBAL[$key]))	//<--- уже просчитывали всю функцию
			return $GLOBAL[$key];
		//группы пользователя чтение из базы
		global $table_users;
		$query="SELECT groups
				FROM `$table_users`
				WHERE id='{$usr}'";
		$res_grp = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_grp = mysql_fetch_array($res_grp);

		if(isset($row_grp['groups']))
			$groups = $row_grp['groups'];
		else
			{
			$GLOBAL[$key] = $RIGHTS;	//запоминаем
			return $RIGHTS;			// пользователь не найден
			}
		}
	$group = explode(',',$groups);
	for($i=0; isset($group[$i]); $i++)
		{
		if($group[$i]!='')
			{
			// Список разрешенных действий для группы
			global $table_groups;
			$group[$i] = mysql_real_escape_string($group[$i], $msconnect_users);
			$query="SELECT action
					FROM `$table_groups`
					WHERE name='{$group[$i]}'";
			$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
			$row_act = mysql_fetch_array($res_act);
			if(isset($row_act['action']))
				$RIGHTS .= $row_act['action'];
			}
		}

	// запоминаем результат
	if(isset($_SESSION['user_id']))
		$_SESSION['RIGHTS'] = $RIGHTS;
	else
		$GLOBAL[$key]= $RIGHTS;
	return $RIGHTS;
}
//-------------------- Проверка прав на действие -----------------------
define('R_NO_MSG', 0);	//не отображать ругань
define('R_MSG', 1);
function check_right($ACTION , $MSG = R_NO_MSG, $usr = NULL)	//$usr - id-пользователя, если не передан, то берется текущий пользователь.
																// $usr = NULL - ,если текущий пользователь не регистрированный
{
	if($usr === NULL)
		{
		if(isset($_SESSION['user_id']))
			{
			$usr = (int)$_SESSION['user_id'];			// выбор текущего пользователя
			global $USER;
			$groups = $USER['groups'];
			}
		else
			{
			if($MSG == R_MSG)show_msg(NULL,$ACTION.": У Вас не достаточно прав. Войдите в систему",MSG_CRITICAL, MSG_RETURN);
			return false;
			}
		}
	//может сохранен ранее просчитанные результат
	$key = 'check_right_'.$ACTION.'_'.$usr;
	$saved=GET_VAR($key);
	if($saved!==NULL)
		{
		if($saved==false && $MSG == R_MSG) show_msg(NULL,$ACTION.": У Вас не достаточно прав",MSG_CRITICAL, MSG_RETURN);
		return $saved;
		}
	//группы пользователя
	if(!isset($groups))
		{
		//группы пользователя чтение из базы
		global $table_users;
		global $msconnect_users;
		$res_grp = mysql_query("SELECT groups
									FROM `$table_users`
									WHERE id='{$usr}'",$msconnect_users) or die(mysql_error());
		$row_grp = mysql_fetch_array($res_grp);
		if(isset($row_grp['groups']))
			$groups = $row_grp['groups'];
		else
			{	// пользователь не найден
			if($MSG == R_MSG)show_msg(NULL,"check_right(): Не верный user_id",MSG_CRITICAL, MSG_RETURN);
				return SET_VAR($key,$r=false);
			}
		}
	//администратор
	if(isset($_SESSION['user_id']) && $_SESSION['user_id']==0)
		return check_admin_access();
	// id действия
	$action = $ACTION;
	if(!is_int($ACTION))
		{
		global $table_actions;
		global $msconnect_users;
		$query="SELECT id
				FROM `$table_actions`
				WHERE name='{$ACTION}'";
		$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_act = mysql_fetch_array($res_act);
		if(!isset($row_act['id']))
			{
			if($MSG == R_MSG)show_msg(NULL,$action.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key,$r=false);
			}
		$ACTION = (int)$row_act['id'];
		}
	// проверка прав
	$group = explode(',',$groups);
	for($i=0; isset($group[$i]); $i++)
		if($group[$i]!='')
			if(check_group_right($ACTION, $group[$i]))
				// запоминаем результат
				return SET_VAR($key,$r=true);

	// прав не оказалось
	if($MSG == R_MSG) show_msg(NULL,$action.": У Вас не достаточно прав",MSG_CRITICAL, MSG_RETURN);
	return SET_VAR($key,$r=false);
}
//-------------------- Проверка прав группы на действие -----------------------
function check_group_right($ACTION , $GROUP, $MSG = R_NO_MSG)	//$ACTION - действие
																//$GROUP - id или имя руппы
{
	//администратор
	if(isset($_SESSION['user_id']) && $_SESSION['user_id']==0)
		return check_admin_access();
	$key = 'check_group_right_'.$ACTION.'_'.$GROUP;
	// возвращаем запомненные ранее значения
	if( isset($GLOBALS[$key]) )
		{
		if($GLOBALS[$key]==false && $MSG == R_MSG) show_msg(NULL,$ACTION.": У группы не достаточно прав",MSG_CRITICAL, MSG_RETURN);
		return $GLOBALS[$key];
		}

	// id действия
	$action = $ACTION;
	if(!is_int($ACTION))
		{
		global $table_actions;
		global $msconnect_users;
		$query="SELECT id
				FROM $table_actions
				WHERE name='{$ACTION}'";
		$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_act = mysql_fetch_array($res_act);
		if(!($ACTION = (int)$row_act['id']))
			{
			if($MSG == R_MSG)show_msg(NULL,$ACTION.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			$GLOBALS[$key] = false; // запоминаем
			return false;
			}
		}
	//определяем права группы
	global $table_groups;
	global $msconnect_users;
	if(is_int($GROUP))
		$res = mysql_query("SELECT action
				FROM `$table_groups`
				WHERE id='{$GROUP}'",$msconnect_users) or die(mysql_error());
	else
		$res = mysql_query("SELECT action
				FROM `$table_groups`
				WHERE name='{$GROUP}'",$msconnect_users) or die(mysql_error());
	$row = mysql_fetch_array($res);
	if(!isset($row['action']))
		{
		if($MSG == R_MSG)show_msg(NULL,"Не известная группа",MSG_CRITICAL, MSG_RETURN);
		$GLOBALS[$key] = false; // запоминаем
		return false;
		}
	// проверка прав
	if(strpos($row['action'],','.$ACTION.',')!==false)
		{
		$GLOBALS[$key] = true; // запоминаем
		return true;
		}

	if($MSG == R_MSG) show_msg(NULL,$action.": У группы не достаточно прав",MSG_CRITICAL, MSG_RETURN);
	$GLOBALS[$key] = false; // запоминаем
	return false;
}
//-------------------- Принадлежит ли пользователь группам --------------
function user_in_group($GROUPS , $MSG = R_NO_MSG, $usr = NULL)		//$GROUPS - список групп через запятую
{
	//администратор
	if(isset($_SESSION['user_id']) && $_SESSION['user_id']==0)
		return check_admin_access();
	//группы пользователя
	if($usr===NULL)
		{
		if(isset($_SESSION['user_id']))
			{
			$usr = $_SESSION['user_id'];	// выбор текущего пользователя
			global $USER;
			$user_groups = $USER['groups'];
			}
		else
			{
			if($MSG == R_MSG) show_msg(NULL,"Пожалуйста войдите в систему",MSG_WARNING, MSG_RETURN);
			return false;		// пользователь не в системе
			}
		}
	//может сохранен ранее просчитанные результат
	$key = 'user_in_group_'.$GROUPS.'_'.$usr;
	$saved=GET_VAR($key);
	if($saved!==NULL)
		{
		if($saved==false && $MSG == R_MSG) show_msg(NULL,"Вы не состоите в необходимой группе",MSG_CRITICAL, MSG_RETURN);
		return $saved;
		}

	//группы пользователя
	if(!isset($user_groups))
		{
		//группы пользователя чтение из базы
		global $table_users;
		global $msconnect_users;
		$query="SELECT groups
				FROM `$table_users`
				WHERE id='{$usr}'";
		$res_grp = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_grp = mysql_fetch_array($res_grp);
		if(isset($row_grp['groups']))
			$user_groups = $row_grp['groups'];
		else
			{
			if($MSG == R_MSG) show_msg(NULL,"К сожалению  Вы не зарегистрированы",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key,$r=false);			// пользователь не найден
			}
		}
	$usr_grp = explode(',',$user_groups);
	// возможно задана 1 группа в $GROUPS
	if(in_array($GROUPS, $usr_grp)) return SET_VAR($key,$r=true);
	// задано несколько группа в $GROUPS
	$GROUP = explode(',', $GROUPS);
	for($i=0; isset($GROUP[$i]); $i++)
		if($GROUP[$i]!='')
			if(!in_array($GROUP[$i], $usr_grp))
				{
				if($MSG == R_MSG) show_msg(NULL,"Вы не состоите в необходимой группе",MSG_CRITICAL, MSG_RETURN);
				return SET_VAR($key,$r=false);
				}
	return SET_VAR($key,$r=true);
}
//------------ Получить список пользователей обладающих правом ---------
function users_whith_right($ACTION, $FIELDS='id')		//$ACTION - имя или номер права
{
	global $msconnect_users;												//$FIELDS - какие поля вернуть
	$USERS = array();

	//может сохранен ранее просчитанные результат
	$key = 'users_whith_right_'.$ACTION.'_'.$FIELDS;
	$saved=GET_VAR($key);
	if($saved!==NULL) return $saved;

	// id действия
	if(!is_int($ACTION))
		{
		global $table_actions;
		$query="SELECT id
				FROM `$table_actions`
				WHERE name='{$ACTION}'";
		$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_act = mysql_fetch_array($res_act);
		if(!($ACTION = (int)$row_act['id']))
			{
			//if($MSG == R_MSG)show_msg(NULL,$ACTION.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key,$r=false);
			}
		}


	//перебираем
	if(strpos('groups',$FIELDS)===false)
		$FIELDS .= ',groups';
	global $table_users;
	$res_usr = mysql_query("SELECT ".$FIELDS." FROM `$table_users`",$msconnect_users) or die(mysql_error());
	while($usr = mysql_fetch_array($res_usr))
		{
		$groups = explode(',',$usr['groups']);
		foreach($groups as $group)
			if($group!='')
				if(check_group_right($ACTION,$group))
					{
					$USERS = array_merge($USERS, array($usr));
					break;
					}
		}
	return SET_VAR($key, $USERS);
}
//------------ Получить список пользователей обладающих правом (2 вар)----------
function users_whith_right2($ACTION, $FIELDS='id')		//$ACTION - имя или номер права
{														//$FIELDS - какие поля вернуть
	$USERS = array();

	//может сохранен ранее просчитанные результат
	$key = 'users_whith_right2_'.$ACTION.'_'.$FIELDS;
	$saved=GET_VAR($key);
	if($saved!==NULL) return $saved;

	// id действия
	if(!is_int($ACTION))
		{
		global $table_actions;
		global $msconnect_users;
		$query="SELECT id
				FROM `$table_actions`
				WHERE name='{$ACTION}'";
		$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_act = mysql_fetch_array($res_act);
		if(!($ACTION = (int)$row_act['id']))
			{
			//if($MSG == R_MSG)show_msg(NULL,$ACTION.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key, $r=false);
			}
		}
	// получаем список групп с правами
	$groups = groups_whith_right($ACTION, 'name');
	if($groups===false) return SET_VAR($key, $r=false);
	foreach($groups as &$group)
		{
		$usr = users_whith_group($group['name'],$FIELDS);
		if($usr!==false)
			$USERS = array_merge($USERS,$usr);
		}
	return SET_VAR($key, $USERS);
}
//-------------------- Получить список групп обладающих правом ----------
function groups_whith_right($ACTION, $FIELDS='id')		//$ACTION - имя или номер права
{														//$FIELDS - какие поля вернуть
	//может сохранен ранее просчитанные результат
	$key = 'groups_whith_right_'.$ACTION.'_'.$FIELDS;
	$saved=GET_VAR($key);
	if($saved!==NULL) return $saved;

	global $msconnect_users;
	$GROUPS = array();
	// id действия
	if(!is_int($ACTION))
		{
		global $table_actions;
		$query="SELECT id
				FROM `$table_actions`
				WHERE name='{$ACTION}'";
		$res_act = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row_act = mysql_fetch_array($res_act);
		if(!($ACTION = (int)$row_act['id']))
			{
			//if($MSG == R_MSG)show_msg(NULL,$ACTION.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key, $r=false);
			}
		}
	global $table_groups;
	$ACTION=str_replace('\\','\\\\',$ACTION);
	$ACTION=mysql_real_escape_string($ACTION,$msconnect_users);
	$ACTION=addCslashes($ACTION, '_%');
	$query = "SELECT ".$FIELDS." FROM `$table_groups`
				WHERE action LIKE '%,{$ACTION},%'";
	$res_grps = mysql_query($query,$msconnect_users) or die(mysql_error());
	while($grp = mysql_fetch_array($res_grps))
		$GROUPS = array_merge($GROUPS, array($grp));
	return SET_VAR($key, $GROUPS);
}
//-------------------- Получить список пользователей состоящих в группе ----------
function users_whith_group($GROUP,$FIELDS='id')		//$GROUP - имя или номер группы
{													//$FIELDS - какие поля вернуть
	//может сохранен ранее просчитанные результат
	$key = 'users_whith_right_'.$GROUP.'_'.$FIELDS;
	$saved=GET_VAR($key);
	if($saved!==NULL) return $saved;

	global $msconnect_users;
	$USERS=array();
	// имя группы
	if(!is_string($GROUP))
		{
		global $table_groups;
		$query="SELECT name
				FROM `$table_groups`
				WHERE id='{$GROUP}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row = mysql_fetch_array($res);
		if(!($GROUP = $row['name']))
			{
			//if($MSG == R_MSG)show_msg(NULL,$ACTION.": Не известное действие",MSG_CRITICAL, MSG_RETURN);
			return SET_VAR($key, $r=false);
			}
		}
	$GROUP=str_replace('\\','\\\\',$GROUP);
	$GROUP=mysql_real_escape_string($GROUP,$msconnect_users);
	$GROUP=addCslashes($GROUP, '_%');
	global $table_users;
	$query = "SELECT ".$FIELDS." FROM `$table_users`
				WHERE groups LIKE '%,{$GROUP},%'";
	$res_usr = mysql_query($query,$msconnect_users) or die(mysql_error());
	while($usr = mysql_fetch_array($res_usr))
		$USERS = array_merge($USERS, array($usr));
	return SET_VAR($key, $USERS);
}
//-------------------- Существует ли группа --------------------------------------
function group_exists($GROUP)		//$GROUP - имя или номер группы
{
	//может сохранен ранее просчитанные результат
	$key = 'group_exists_'.$GROUP;
	$saved=GET_VAR($key);
	if($saved!==NULL) return $saved;

	global $table_groups;
	global $msconnect_users;
	if(is_int($GROUP))
		{
		$query="SELECT id
				FROM `$table_groups`
				WHERE id='{$GROUP}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		if(mysql_num_rows($res)!=0) return SET_VAR($key, $r=true);
		}
	else if(is_string($GROUP))
		{
		$query="SELECT id
				FROM `$table_groups`
				WHERE name='{$GROUP}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		if(mysql_num_rows($res)!=0) return SET_VAR($key, $r=true);
		}
	else return SET_VAR($key, $r=false);
}
//---------------------------- Хеширование пароля -----------------------------------------------------
function hash_pass($password) { return md5($password); }
//---------------------------- Проверка пароля --------------------------------------------------------
function check_pass($password, $pass_hash) { global $users; return (call_user_func($users->hash_pass, $password) === $pass_hash); }
//---------------------------- регистрация пользователя -----------------------------------------------
function reg_user($login, $password, $data=array())
{
	global $_, $users, $table_users;
	if (!isset($login) || !isset($password) || $login=='admin' || empty($login) || empty($password))
		return $_('Введите логин и пароль.');
	// проверим  логин
	if(!ereg("^[0-9\-_a-zA-Z]+$",$login)) return $_('Недопустимые символы в логине. Вы не зарегистрированы');
	$pswd = call_user_func($users->hash_pass, $password);
	//нет ли другого пользователя с таким логином
	$sql = $users->db->query("SELECT login FROM `$table_users` WHERE login='%s' LIMIT 1",$login) or die($users->db->error());
	// если такой пользователь не нашелся
	if ($sql->num_rows > 0)
		return $_('Этот логин занят. Введите другой и попробуйте еще раз.');

	$vars=''; $query=''; $values=array($login, $pswd);
	if(!empty($data))
		foreach($data as $key=>$val)
			{
			$vars .= ','.$key;
			$values[] = $val;
			$query .= ',\'%s\'';
			}

	if(!($users->db->query("INSERT INTO `$table_users` (login,password,reg_date,groups{$vars})
						VALUES('%s','%s',CURRENT_TIMESTAMP(),'".DEFAULT_GROUP."'{$query})",
						$values)))
		return $_('Ошибка при регистрации: ').$users->db->error();
	$new_user_id = $users->db->insert_id;

	//добавить тикет отзывов о пользователе
	if( USERS_DIALOG ){
		global $ticket;
		$dialog_id = call_user_func($ticket->add, 'HIDDEN', 'user_id='.$new_user_id, '', NULL, NULL, NULL, -1, $new_user_id); // -1 -для всех
		if(is_numeric($dialog_id))
			$users->db->query("UPDATE `$table_users` SET `dialog_id`='%u' WHERE `id`='%u'", array($dialog_id, $new_user_id) );
	}
	
	// возникает событие
	global $event;
	$event->create('user_registered', array($new_user_id, $login, $password));
	return true;

}
//---------------------------- обновляем данные пользователя -----------------------------------------------
function update_user( $user_id, $data ){
	global $_, $table_users, $users;
	if(!is_array($data) || empty($data) ) return $_('Должен быть передан массив');
	if(isset($data['password']) && $data['password']=="") unset($data['password']);

	$query = "UPDATE `$table_users` SET ";
	$values=array();
	if(isset($data['login'])) 	{
		$row = $users->db->query("select count(id) from `%s` where login='%s' and id!='%u'", array($table_users,$data['login'],(int)$user_id))->fetch_assoc();
		if(0<$row['count(id)'])
			return $_("Логин занят попробуйте другой");
		$query .= "login = '%s', "; 		$values[] = $data['login'];}
	if(isset($data['name'])) 	{$query .= "name = '%s', "; 		$values[] = $data['name'];}
	if(isset($data['surname'])) {$query .= "surname = '%s', ";		$values[] = $data['surname'];}
	if(isset($data['mail'])) 	{$query .= "mail = '%s', ";			$values[] = $data['mail'];}
  	if(isset($data['phone'])) 	{$query .= "phone = '%s', ";			$values[] = $data['phone'];}
  	if(isset($data['notification'])){
    	if($data['notification']=='NULL') $query .= "notification = NULL, ";
    	else {$query .= "notification = '%s', ";			$values[] = $data['notification'];}
 	}
	if(isset($data['password'])){$query .= "password = '%s', ";		$values[] = md5($data['password']);}
	if(isset($data['city'])) 	{$query .= "city = '%s', ";			$values[] = $data['city'];}
	if(isset($data['occupation'])){$query .= "occupation = '%s', "; $values[] = $data['occupation'];}
	if(isset($data['age'])) 	{$query .= "age = '%u', ";			$values[] = $data['age'];}
	if(isset($data['connect'])) {$query .= "connect = '%s', ";		$values[] = $data['connect'];}
	if(isset($data['koshelek'])){$query .= "koshelek = '%s', ";		$values[] = $data['koshelek'];}
	if(isset($data['time_zone'])){$query .= "time_zone = '%s', ";	$values[] = $data['time_zone'];}
	if(isset($data['photo'])){
		if($data['photo']!='NULL') {$query .= "photo = '%s', ";	$values[] = $data['photo'];}
		else $query .= "photo = NULL, ";
	}
	$query = substr($query, 0, -2);		// удаляем справа запятую с пробелом
	$query .= " WHERE id='%u'";		$values[] = $user_id;

	if( !($users->db->query($query, $values)) ) return $_("Ошибка. Попробуйте еще раз.");
	// возникает событие
	global $event;
	$event->create('user_updated', array($user_id, $data));
	return true;
}
//---------------------------- удаление пользователя -----------------------------------------------
function delete_user($del_id=NULL){
	if($del_id === NULL || !is_int($del_id)) {$del_id = $_SESSION['user_id']; }
	if( $del_id == 0 ) return false;
	global $users, $table_users;
	$usr = get_user($del_id, 'login,dialog_id');
	// Удаляем отзывы о пользователе
	if(is_numeric($usr['dialog_id'])){
		$res = delete_ticket( (int)$usr['dialog_id'] );
		if($res !== true) show_msg(NULL, $res, MSG_WARNING, MSG_NO_BACK);
	}
	logout();
	if($users->db->query("DELETE FROM `$table_users` WHERE id='%u'", array($del_id))){
		// возникает событие
		global $event;
		$event->create('user_deleted', array($del_id, $usr['login']));
		return true;
		}
	else{
		//echo $users->db->last_query();
		//echo $users->db->error();
		//exit;
		return false;
		}
}
//---------------------------- Проверка секрет-кода -----------------------------------------------------
function check_auth($auth, $user_id=NULL) {
	global $users, $USER;
	if($user_id==NULL) $user = $USER;
	else $user = get_user((int)$user_id);
	$hash = call_user_func($users->hash_auth, $auth, $user);
	$authorize = unserialize($user['authorize']);	
	if(is_array($authorize))
		foreach ($authorize as $a){
			if($a['hash'] === $hash)
				return true;
		}
	return false;
}
//---------------------------- Получение секрет-кода для идентификации пользователя -------------------
function hash_auth($auth, $user) {
if(isset($_SESSION['time_zone'])) @date_default_timezone_set($_SESSION['time_zone']);
return md5(SECRET.$auth.$user['id'].$_SERVER['HTTP_USER_AGENT']);
//return md5($auth.$user['id'].$user['login'].strtotime($user['last_visit']).$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
}
//----------------------------- Сохранение секрет-кода --------------------------------------------------
function save_auth_hash($auth, $user_id){
	global $users,$table_users;
	$usr = get_user($user_id);
	$authorize = unserialize($usr['authorize']);
	if($authorize == NULL) $authorize = array();
	$secret['time'] = time();
	$secret['hash'] = call_user_func($users->hash_auth, $auth, $usr);
	while(count($authorize) > 9){
		$oldest_record_key = array_keys($authorize);
		$oldest_record_key = $oldest_record_key[0];
		foreach ($authorize as $key => $a) {
			if( $a['time'] < $authorize[$oldest_record_key]['time'] )
				$oldest_record_key = $key;
		}
		unset($authorize[$oldest_record_key]);
	}
	$authorize[] = $secret;
	$users->db->query("UPDATE `$table_users` SET last_visit = '%s', authorize = '%s' WHERE id='%d'",
		array(date('Y-m-d G:i:s',time()),serialize($authorize),(int)$user_id)) or die($users->db->error());
}
//----------------------------- Исключение секрет-кода --------------------------------------------------
function delete_auth_hash($auth){
	global $users,$table_users,$USER;
	if(!isset($USER['authorize'])) return false;
	$authorize = unserialize($USER['authorize']);
	if(!is_array($authorize)) return false;
	$hash = call_user_func($users->hash_auth, $auth, $USER);
	foreach ($authorize as $key => $a){
		if( $a['hash'] == $hash ){
			unset($authorize[$key]);
			break;
			}
	}
	$users->db->query("UPDATE `$table_users` SET last_visit = '%s', authorize = '%s' WHERE id='%d'",
		array(date('Y-m-d G:i:s',time()),serialize($authorize),(int)$USER['id'])) or die($users->db->error());
	return true;
}
//-------------------------------------------------------------------------------------------------------
// я анонимный пользователь?
function is_password_null($user_id=NULL){
	if($user_id==NULL && !isset($_SESSION['user_id']))
		{echo "Неверное использование функции USERS:is_password_null()"; exit;}
	if($user_id==NULL) $user_id=$_SESSION['user_id'];
	$usr = get_user($user_id,'password');
	global $users;
	return call_user_func($users->check_pass, 'NOPASSWD', $usr['password']);
}
//---------------------------------------------- логинимся ----------------------------------------------
// login( $login ) - позволяет логинить анонимных пользователей, у которых нет
// пароля (что задается паролем равеным 'NOPASSWD')
function login($login, $password='NOPASSWD')
{
	global $_,$users,$table_users;
	if(empty($login) || empty($password)) return $_('Такой логин и паролем не найдены');
	// получаем хеш пароля
	$sql = $users->db->query("SELECT * FROM `$table_users` WHERE login='%s' LIMIT 1",$login) or die($users->db->error());
		if(!$sql) return $_('Не верен логин или пароль');
	$row = $sql->fetch_assoc();
	$pass_hash = $row['password'];
	// если пароль верен
	if (call_user_func($users->check_pass, $password, $pass_hash) || check_right('SUDO') ) {	// нужен пароль или права SUDO
		//обнуляем сессию
		if(isset($_SESSION['QUERY_STRING'])) $_SESSION = array('QUERY_STRING'=>$_SESSION['QUERY_STRING']);
		else $_SESSION = array();
		// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)
		$_SESSION['user_id'] = (int)$row['id'];       // не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
		//задание уникального кода вода, идентифицирующего пользователя
		$_SESSION['time_zone'] = $row['time_zone'];	//узнаем часовой пояс пользователя
		//даже взломав сервер (БД, сессии и узнав алгоритм получения $auth), злоумышленник не узнает rand(), для этого ему надо взломать пользователя
		//взлом пользователя, без взлома сервера не решает все проблемы (на машине хакера надо иметь такой же
		//IP и броузер(либо изменять $_SERVER[] отправляемый своим браузером) и неизвестны сессия и данные БД, чтобы получить доступ)
		//еще слабость - это смена authorize в БД, на другой необходимый хакеру - решает все проблемы
		$auth = md5(uniqid(rand(),1));
		$_COOKIE['authorize'] = $auth; //чтобы не вылетели на этой странице
		@setcookie ("uid", $_SESSION['user_id'], time()+86400*30, "/", DOMAIN);	// сохраняем номер залогинившегося пользователя
		if( !@setcookie ("authorize", $auth, time()+86400*30, "/", DOMAIN)){
			?>
			<script language="javascript">
				document.cookie = "authorize=<?php echo $auth;?>; path=/; domain=<?php echo ".".DOMAIN; ?>;"; // expires=<?php echo time()+3600;?>
			</script>
			<?php
			}
		//авторизация
		save_auth_hash($auth, (int)$row['id']);
		// Обновляем Информация о пользователе  и проверка подступа
		global $USER;
		$USER = get_user();
		// возникает событие
		global $event;
		$event->create('login', array($_SESSION['user_id'],$login));
		return true;
		}
	else
		return $_('Неверный логин или пароль');
}
//---------------------------------------------- логинимся ----------------------------------------------
function soft_login($login, $hash_password)
{
	global $_,$users,$table_users;
	if(empty($login) || empty($hash_password)) return $_('Такой логин и паролем не найдены');
	// получаем хеш пароля
	$sql = $users->db->query("SELECT * FROM `$table_users` WHERE login='%s' LIMIT 1",$login) or die($users->db->error());
		if(!$sql) return $_('Не верен логин или пароль');
	$row = $sql->fetch_assoc();
	$pass_hash = $row['password'];
	// если пароль верен
	if ( $hash_password == $pass_hash || check_right('SUDO') ) {	// нужен пароль или права SUDO
		//обнуляем сессию
		if(isset($_SESSION['QUERY_STRING'])) $_SESSION = array('QUERY_STRING'=>$_SESSION['QUERY_STRING']);
		else $_SESSION = array();
		// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)
		$_SESSION['user_id'] = (int)$row['id'];       // не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
		//задание уникального кода вода, идентифицирующего пользователя
		$_SESSION['time_zone'] = $row['time_zone'];	//узнаем часовой пояс пользователя
		//даже взломав сервер (БД, сессии и узнав алгоритм получения $auth), злоумышленник не узнает rand(), для этого ему надо взломать пользователя
		//взлом пользователя, без взлома сервера не решает все проблемы (на машине хакера надо иметь такой же
		//IP и броузер(либо изменять $_SERVER[] отправляемый своим браузером) и неизвестны сессия и данные БД, чтобы получить доступ)
		//еще слабость - это смена authorize в БД, на другой необходимый хакеру - решает все проблемы
		$auth = md5(1000000*rand().$row['login']);
		$_COOKIE['authorize'] = $auth; //чтобы не вылетели на этой странице
		setcookie ("uid", $_SESSION['user_id'], time()+86400*30, "/", DOMAIN);	// сохраняем номер залогинившегося пользователя
		if( !setcookie ("authorize", $auth, time()+86400*30, "/", DOMAIN)){	// запоминаем 10 дней
			?>
			<script language="javascript">
				document.cookie = "authorize=<?php echo $auth;?>; path=/; domain=<?php echo ".".DOMAIN; ?>;"; // expires=<?php echo time()+86400*30;?>
			</script>
			<?php
			}
		//авторизация
		save_auth_hash($auth, (int)$row['id']);
		// Обновляем Информация о пользователе  и проверка подступа
		global $USER;
		$USER = get_user();
		// возникает событие
		global $event;
		$event->create('login', array($_SESSION['user_id'],$login));
		return true;
		}
	else
		return $_('Неверный логин или пароль');
}
//---------------------------------------------- входим с использованием куки -------------------------
function cookie_login(){
	return session_variable_login($_COOKIE);
}
//------------------------------------------- входим с использованием get-переменных ------------------
function get_variable_login(){
	if( session_variable_login($_GET) ){
		$_COOKIE['authorize'] = $_GET['authorize'];
		@setcookie ("uid", $_SESSION['user_id'], time()+86400*30, "/", DOMAIN);	// сохраняем номер залогинившегося пользователя
		@setcookie ("authorize", $_COOKIE['authorize'], time()+86400*30, "/", DOMAIN);
		return true;
	}
	return false;
}
//---------------------------- входим с использованием массива сессионных параметров ------------------
function session_variable_login(&$SESSION_VAR){
	if(isset($_SESSION['user_id'])) return false;	// если  еще не залогинены
	if(!isset($SESSION_VAR['uid']) || $SESSION_VAR['uid']=='false') return false;
	if(!isset($SESSION_VAR['authorize']) || $SESSION_VAR['authorize']=='false') return false;
	// если ключи авторизации совпали
	if( check_auth($SESSION_VAR['authorize'], $SESSION_VAR['uid'] ) ){
		// логиним пользователя
		global $USER;
		$_SESSION['user_id'] = (int)$SESSION_VAR['uid'];
		$USER = get_user();
		$_SESSION['time_zone'] = $USER['time_zone'];	//узнаем часовой пояс пользователя
		// возникает событие
		global $event;
		$event->create('login', array($_SESSION['user_id'],$USER['login']));
		return true;
		}
}
//---------------------------------------------- выходим из системы -----------------------------------
function logout(){
	global $USER;
	$id = $USER['id'];
	$login = $USER['login'];
	if(isset($_COOKIE['authorize']))
		setcookie ("authorize", "false", time() + 86400*30, "/", DOMAIN);
	if(isset($_COOKIE['uid']))
		setcookie ("uid", "false", time() + 86400*30, "/", DOMAIN);

	if(isset($_SESSION['user_id']))
		delete_auth_hash($_COOKIE['authorize']);
	//session_unregister('user_id'); //Эта функция не разустанавливает/unset соответствующую глобальную переменную name, она только предотвращает сохранение переменной как части сессии. Вы обязаны вызывать unset() для удаления соответствующей глобальной переменной.
	unset($_SESSION['user_id']); //разустанавливает данную переменную.
	unset($_SESSION);
	session_unset();	//освобождает все переменные сессии, зарегистрированные на данный момент.
	session_destroy(); 	//разрушить сессию
	$USER=false;
	// возникает событие
	global $event;
	$event->create('logout', array($id,$login));
}
//---------------------------------------------Восстановление пароля--------------------
function restore_password( $get_authorize ) {
	//поиск пользователя, запросившего восстановление
	global $users, $table_users;
	$query = "SELECT id,login
				FROM `$table_users`
				WHERE authorize='".md5($get_authorize)."'
				LIMIT 1";
	$sql = $users->db->query($query) or dir($users->db->error());
	// если такой пользователь нашелся и он один
	if ($sql!=NULL && $sql->num_rows == 1)
		{
		$row = $sql->fetch_assoc();
		//устанавливаем новый пароль
		$pass = rand(1000,9999);
		$password = md5($pass);
		$query = "UPDATE `$table_users`
	    SET `password` = '%s',
			`authorize` = NULL
            WHERE id='%u'";
    	$users->db->query($query,array($password, $row['id'])) or die($users->db->error());

		$rel = url('Войти в систему','USERS','in');
		show_msg("Новый пароль",
				"Ваш новый пароль: ".$pass."<br>Рекомендуем сменить его.<br>".$rel,MSG_INFO,MSG_NO_BACK);
		global $event;
		$event->create('restore_password', array($row['login'],$pass));
		}
	else
		{
		show_msg("Восстановление пароля",
				"Не пытайся обмануть систему. Иначе твой IP заблокируется",MSG_CRITICAL,MSG_NO_BACK);
		}
}
//----------------------------------- вывод отзывов о пользователе ------------------------------------------
// $usr - либо id пользователя, либо массив со строчкой из БД
//
function show_user_dialog($usr)
{
	global $users, $_, $ticket,$table_users;
	if(is_numeric($usr)) $usr = get_user((int)$usr, 'id,dialog_id');
	else if(  !is_array($usr)) return $_('Пользователь не найден');
	if(!isset($_SESSION['user_id'])) return $_('Отзывы не отображаются. Войдите в систему.');

	if( $usr['dialog_id'] == NULL ){
		global $ticket;
		$dialog_id = call_user_func($ticket->add, 'HIDDEN', 'user_id='.$usr['id'], '', NULL, NULL, NULL, -1, $usr['id']); // -1 -для всех
		if(is_numeric($dialog_id))
			$users->db->query("UPDATE `$table_users` SET `dialog_id`='%u' WHERE `id`='%u'", array($dialog_id, (int)$usr['id']) );
		$usr['dialog_id'] = $dialog_id;
	}

	if(USERS_DIALOG && !isset($usr['dialog_id']))
		user_error($_('Функция отзывов о пользователях не используется, удалите поле \'dialog_id\' из таблицы ').$table_users, E_USER_WARNING);

	echo '<h3 style=\'text-align:center;\'>Отзывы о пользователе</h3>';
	call_user_func($ticket->show_dialog, (int)$usr['dialog_id']);

	return true;
}
//---------------- Действия выполняемые после загрузки движка -------------------------------------
// добавляем обработчик события
$event->add('init', 'init_USERS');

$LOGIN_OK = NULL;		// показывает, верно ли прошел логин
function init_USERS(){
	global $URL, $users;
	// логинимся
	if($URL['MODULE']=='USERS' && $URL['FILE']=='in'){
		// логинимся
		if (isset($_POST['login']) && isset($_POST['password'])){
			global $LOGIN_OK;
			$LOGIN_OK = call_user_func($users->login, $_POST['login'], $_POST['password']);
			}
		}
	// логаутимся
	if( $URL['MODULE']=='USERS' && $URL['FILE']=='exit_user' ){
		logout();
		header("HTTP/1.1 301 Moved Temporarily");
		header("Location: /");
		exit();
		}
}
?>
