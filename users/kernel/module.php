<?php
	/***************************************************************************

					Модуль работы с пользователями

	**************************************************************************/
?>
<?php
	// конфигурация модуля
	require('config.php');

	// функции модуля
	require_once('function.php');

	$USER_ADDITIONAL=array('DESCRIPTION'=>'Управление зарегистрированными на сайте пользователями, редактирование их профилей и блокировка аккаунта');
	$INCLUDE_MODULES['USERS']['INFO'] = &$USER_ADDITIONAL;

	// спользуемые функции (их можно переопределить)
	class ClassUsers
		{
		public $db;
		public $reg_user 		= 'reg_user';	// регистрация пароля
		public $hash_pass 		= 'hash_pass';	// получение хеша пароля
		public $check_pass 		= 'check_pass';	// проверка пароля
		public $login 			= 'login';		// логинимся
		public $hash_auth 		= 'hash_auth';	// получение хеша для кода идентификации пользователя (хранится в куках)
		public $check_auth 		= 'check_auth';	// проверка хеша для кода идентификации пользователя (хранится в куках)
		}
	$users = new ClassUsers();

	//БД пользователи
	require_once("DB.php");

	//Функции Проверка прав пользователей
	require_once(SCRIPT_ROOT."/users/kernel/access.php");

	// Информация о пользователе  и проверка подступа
	$USER = get_user();

	//проверка доступа пользователей
	check_access();

	// файлы модуля
	require('files.php');

	// логиним по кукам (выполняем после загрузки ядра движка)
	$event->add('init', 'cookie_login');

	// логиним по переменным get запроса (uid, authorize) (выполняем после загрузки ядра движка)
	$event->add('init', 'get_variable_login');

	// функции логина через uLogin
	require('ulogin/ulogin.php');

  	// отправка смс уведомлений
	require('smsc_api.php');

	// устанавливаем время доступа
	if(isset($USER['id']))
		$users->db->query("UPDATE `$table_users` SET last_visit = '%s' WHERE id='%d'", array(date('Y-m-d G:i:s',time()),(int)$USER['id']));

	// расширяем функционал меню
	class groupMenu extends Menu{

		//---------------------------------- Вывод меню для группы ------------------------------------------------------------
		// ID 		- имя меню
		// GROUPS	- array(), вывести меню для группы (меню с одинаковым именем разное для разных групп)
		// TEG 		- в каком html-теге вывести ссылки
		function &out($ID, $GROUPS = NULL, $TEG = 'li')
		{
			global $USER;
			global $msconnect_users;
			global $table_groups;
			$ret='';
			if($GROUPS == NULL)
				// получаем группы пользователя
				$groups = explode(',', $USER['groups']);
			else
				$groups = &$GROUPS;
			foreach($groups as $grp)		// перебор групп
				if($grp!='')
					{
					$menus[$ID]=GET_VAR(__FUNCTION__ . $ID . $_SESSION['user_id']);
					DEL_VAR(__FUNCTION__ . $ID . $_SESSION['user_id']);
					if($menus[$ID] == NULL)
						{
						$group = mysql_real_escape_string($grp, $msconnect_users);
						$res = mysql_query("SELECT menus FROM `$table_groups` WHERE `name`='$group' LIMIT 1",$msconnect_users) or die(mysql_error()); // выбираем блоки
						$row = mysql_fetch_assoc($res);
						if($row)
							{
							$menus = $row['menus'];
							$menus = unserialize($menus);
							}
						else
							{
							$ret = $_('Группа не существует: ').$group;
							return $ret;
							}
						}
					if(isset($menus[$ID]) && $menus[$ID] != NULL)
						{
						foreach($menus[$ID] as $block_id)	// перебор блоков в меню, задаваемом $ID
								{
								SET_VAR(__FUNCTION__ . $ID . $_SESSION['user_id'], $menus[$ID]);
								$ret .= $this->get($block_id, $TEG);
								}
						}
					else
						{
						$ret = _('Не найдено меню с именем: ').$ID;
						return $ret;
						}
					}
			return $ret;
		}
	}
	$MENU = new groupMenu(); // обновляем системную переменную для меню
?>
