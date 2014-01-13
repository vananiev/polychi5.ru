<?php
	/*****************************************************************
		Открытие доступа зарегистрированным пользователям
	******************************************************************

	******************************************************************
	$_SESSION['user_id'] - id пользователя

	Администратором является пользователь с
	user_id==0
	name = admin
	solver = ADMIN
	и заданным паролем в куках
	*****************************************************************/
?>
<?php
//проверка доступа по роли в системе
function check_access()
{
	global $_, $users;
	/*/нет входа в систему
	if(!isset($_SESSION['user_id']))
		{
		eval("\$access = ".str_replace ("QUERY", "\$_SERVER['QUERY_STRING']", ACCESSED_FOR_NOT_REG).";");
		if(defined('ACCESSED_FOR_NOT_REG') && $access)
               	{
				return true;	//нерегистрирован, и ссылается на разрешенные адреса
				}
			else
				{
				$_SESSION['QUERY_STRING'] = $_SERVER['QUERY_STRING'];
				echo "</table></div><div style='clear:both;'></div>
					<div class='main'>";
				echo "<font size=2pt color='blue'>Пожалуйста, ";
				echo url('водите в систему', 'USERS', 'in');
				echo "или зарегистрируйтесь для доступа к этой странице.</font>";
				require(SCRIPT_ROOT."/users/reg_user.php");
				echo "</div>";
				require_once(THEME_ROOT.'/footer.php');
				exit;
				}
		//	}
		//else		//НЕТ разрешения для нерегистрированных пользователей
		//	{
		//	$_SESSION['QUERY_STRING'] = $_SERVER['QUERY_STRING'];
		//	echo "<div class='main' style="position:absolute;top:30%;left:20%;">";
		//	echo "<font size=2pt color='blue'>Пожалуйста, зарегистрируйтесь для доступа к этой странице.</font>";
		//	require(SCRIPT_ROOT."/users/reg_user.php");
		//	echo "<p></p><p></p><font size=2pt color='blue'>Или войдите в систему.</font>";
		//	require(SCRIPT_ROOT."/users/in.php");
		//	echo "</div>";
		//	exit;
		//	}
		}*/
	if(isset($_SESSION['user_id']))
		{
		if($_SESSION['user_id']==0)     //администратор
			return check_admin_access();
		else
			{//известные статусы
			if(!isset($_COOKIE['authorize']))
				{
				//автовыход
				//require(SCRIPT_ROOT."/users/exit_user.php");
				logout();
				//if(function_exists('show_msg')) show_msg(NULL,"Ошибка доступа, разрешите javascript",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW);
				//	else echo "Failed to access, enable javascript";
				exit;
				}
			global $USER;
			//echo $_COOKIE['authorize'].'<br>';
			//echo $USER['authorize'];
			//var_dump($users->check_auth.' '. $_COOKIE['authorize'] .' '. $USER['authorize']);
			if( call_user_func($users->check_auth, $_COOKIE['authorize']) )
		        	{
		        	return true; //доступ разрешен
		        	}
			}
		//не известные статусы
		//require(SCRIPT_ROOT."/users/exit_user.php");
		logout();
		//if(function_exists('show_msg')) show_msg(NULL,"У вас  не достаточно прав!",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW);
		//	else echo $_("You are not have rights!");
		}
}

//проверка доступа администратора
function check_admin_access()
{
	global $_, $users;
	if(isset($_SESSION['user_id']) && $_SESSION['user_id']==0)
		{
        //получаем информацию о пользователе
	    $query="SELECT * FROM `{$GLOBALS['table_users']}` WHERE id = '{$_SESSION['user_id']}' LIMIT 0,1";
    	if(!($res = mysql_query($query,$GLOBALS['msconnect_users'])) )
				{
				unset($_SESSION['user_id']);
				die("Ошибка чтения таблицы users check_admin_access()<br>".mysql_error());
				}
    	$row = mysql_fetch_array($res);
       	if($row['login']!='admin')
       		{
       		//require(SCRIPT_ROOT."/users/exit_user.php");
			logout();
			//if(function_exists('show_msg')) show_msg(NULL,"У вас  не достаточно прав!",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW);
			//	else echo $_("You are not have rights! Relogin.");
       		exit;
       		}
       	if(!isset($_COOKIE['authorize']))
       		{
       		//автовыход
       		//require(SCRIPT_ROOT."/users/exit_user.php");
			logout();
			//if(function_exists('show_msg')) show_msg(NULL,"У вас  не имеется соответствующих прав!",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW);
			//	else echo $_("You are not have rights! You must relogin.");
			exit;
       		}
    	if( call_user_func($users->check_auth, $_COOKIE['authorize'])  && (strpos($row['groups'],',ADMIN,')!==false)  )
        	{
        	return true; //доступ администратора разрешен
        	}
		}
	//что-то не так
	//require(SCRIPT_ROOT."/users/exit_user.php");
	logout();
	//if(function_exists('show_msg')) show_msg(NULL,"У вас  не достаточно прав! Войдите в систему",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW);
	//	else echo $_("You are not have rights! Please, relogin.");
}
?>
