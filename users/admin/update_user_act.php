<?php /*******************************************************************
					Скрипт обновлении данных о пользователе
		******************************************************************
		POST:
		user_id		- id пользователя
		login 		- новый логин
		password	- пароль
		name		- имя отчество
		surname		- фамилия
		city		- город
		occupation	- род занятий
		age			- возраст
		mail		- e-mail
		connect		- контактные даные
		koshelek	- кошелек
		time_zone	- временная зона (строка 'Etc/GMT-4')

		FILES:
		avatarfile	-  аватарка
*/
		$data = array();
		if(isset($_POST['login'])) $data['login'] = $_POST['login'];
		//$_POST['password'] не исп напрямую
		if(isset($_POST['name'])) $data['name'] = $_POST['name'];
		if(isset($_POST['surname'])) $data['surname'] = $_POST['surname'];
		if(isset($_POST['city'])) $data['city'] = $_POST['city'];
		if(isset($_POST['occupation'])) $data['occupation'] = $_POST['occupation'];
		if(isset($_POST['age']) && is_numeric($_POST['age'])) $data['age'] = (int)$_POST['age'];
    	if(isset($_POST['notificationMethod'])) $data['notification'] = $_POST['notificationMethod'];
		if(isset($_POST['mail'])) $data['mail'] = $_POST['mail'];
    	if(isset($_POST['phone'])) $data['phone'] = $_POST['phone'];
		if(isset($_POST['connect'])) $data['connect'] = $_POST['connect'];
		if(isset($_POST['koshelek'])) $data['koshelek'] = $_POST['koshelek'];
		if(isset($_POST['time_zone']) && !empty($_POST['time_zone'])) $data['time_zone'] = $_POST['time_zone'];
		if(!isset($_POST['new_password']) || !empty($_POST['new_password']))	$data['password'] = $_POST['new_password'];
?>
<?php
	if(!isset($_SESSION['user_id']))
		{
		show_msg(NULL,"Действие запрещено",MSG_CRITICAL);
		return;
		}
?>
<?php
//проверяем подтверждение нового пароля
if(isset($_POST['new_password']) || isset($_POST['new_password_check']))
	{
	if($_POST['new_password_check'] != $_POST['new_password'])
		{
		show_msg("Ошибка","Поля Новый пароль и Подтверждение Нового пароля не совпадают",MSG_WARNING,MSG_BACK);
		return;
		}
	}

//загрузка аватарки
if(isset($_FILES['avatarfile']) && $_FILES['avatarfile']['name']!="")
	{
	$ext = '.'.end (explode (".", $_FILES["avatarfile"]["name"]));
	if($_FILES["avatarfile"]["size"] > 100*1024)
		{
		show_msg("Ошибка",
				"Размер файла превышает 100 кБ<br>",
			MSG_WARNING,MSG_BACK);
			return;
		}
	if(!copy($_FILES["avatarfile"]["tmp_name"],AVATAR_ROOT.'/'.$_SESSION['user_id'].$ext))
		show_msg("Ошибка","Ошибка при загрузке аватарки<br>",MSG_WARNING,MSG_BACK);
	// Удаляем старые аватарки
	$files = glob( AVATAR_ROOT.'/'.$_SESSION['user_id'].'.*');
	foreach ($files as $file) {
		if( basename($file) != $_SESSION['user_id'].$ext )
			unlink($file);
	}
	// Если загрузили убираем ссылку на внешнее фото
	$data['photo'] = 'NULL';
}

//изменяем данные
if (user_in_group('USER'))
	{
	$ret = update_user($_SESSION['user_id'], $data);
	if(true !== $ret){
		show_msg("Ошибка", $ret.'<br>', MSG_WARNING);
		return;
		}
	$mess = "";
	if(isset($data['password']) && $data['password']!='')
		$mess = "Пароль доступа к учетной записи изменен";
	}
else if(
		isset($data['name']) &&  $data['name'] != "" &&
		isset($data['surname']) && $data['surname']!= "" &&
		isset($data['city']) && $data['city']!= "" &&
		isset($data['occupation']) && $data['occupation']!= "" &&
		isset($data['age']) && ereg("^[0-9]{1,}$",$data['age']) &&
		isset($data['mail']) &&  $data['mail']!= "" &&
		isset($data['connect']) && $data['connect']!= "" &&
		isset($data['koshelek']) && $data['koshelek']!= ""
		)
	{
	//решающий и остальные
	$ret = update_user($_SESSION['user_id'], $data);
	if(true !== $ret){
		show_msg("Ошибка", $ret.'<br>', MSG_WARNING);
		return;
		}
	$mess = "";
	if(isset($_POST['new_password']) && $_POST['new_password']!='')
		$mess = "Пароль доступа к учетной записи изменен";
	}
else
	{
	show_msg("Ошибка",
		"Корректно заполните поля
		<br>",
		MSG_WARNING,MSG_BACK);
	return;
	}
//Меняем настроики временной зоны в сессии
$_SESSION['time_zone'] = $data['time_zone'];
show_msg("Информация",
	"Ваши данные изменены. <br>
	{$mess}",
	MSG_INFO,MSG_BACK
	);
?>
