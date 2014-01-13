<?php
		/*********************************************************************
						Восстановление пароля
		*********************************************************************
		GET:
		authorize - идентификатор, с помощью которого узнаем кто запрашивал восстановление пароля 
					(так же в куках должна содержаться специальная переменная для идентификации пользователя, все это дает возможность входа в систему)
		POST:
		user_id - id пользователя
					
		*/	
		if(isset($_GET['authorize'])) $get_authorize = mysql_real_escape_string($_GET['authorize'],$msconnect_users);
		if (isset($_POST['login'])) $login = mysql_real_escape_string($_POST['login']);
?>
<?php
if(isset($_GET['authorize']))
	// входим в систему в обход пароля
	restore_password($_GET['authorize']);
else
{
	//отправляем письмо с ссылкой для входа и устанавливаем кукки
	if (isset($login))
	{
		// щем юзера с таким логином
		$usr = get_user_id($login);
		if ($usr !== NULL) {
			$row = get_user($usr, 'id,login,mail');
			if(isset($row['mail']) && $row['mail']!='')
			{
				//устанавливаем пометку о смене пароля
				$auth = md5(100000*rand().$row['login']);
				$last_visit = time();
				$authorize = md5($auth.$row['id'].$row['login'].$last_visit.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);	
				$authorize2 = md5($authorize);
				$query = "UPDATE `$table_users`
				SET authorize = '{$authorize2}'
					WHERE id='{$row['id']}'";
				mysql_query($query,$msconnect_users) or die(mysql_error());	//авторизация
				//отправляем письмо
				$rel = url(NULL, 'USERS', 'restore_password', 'authorize='.$authorize);
				$mes = "Уважаемый, {$row['login']}.
					На сайте http://{$_SERVER['SERVER_NAME']} было запрошено восстановление пароля
					по Вашему логину. Если не вы были инициатором запроса просто проигнорируйте это письмо.
					Для смены пароля переидите по ссылке:
					http://{$_SERVER['SERVER_NAME']}".$rel."
					С уважением, http://{$_SERVER['SERVER_NAME']}/.";
				if(sendmail($row['mail'], "Восстановление пароля", $mes))
					show_msg("Восстановление пароля",	
						"На почтовый ящик, указанный при регистрации отправлено письмо с дальнейшими действиями <a href=\"/\">[ok]</a>",MSG_INFO,MSG_NO_BACK);
				else
					show_msg("Восстановление пароля",	
						"Внутренняя ошибка. Письмо не отправлено. Попытайтесь еще раз.",MSG_WARNING,MSG_OK);
			}
			else //почтового ящика нет
				show_msg("Восстановление пароля", "При регистрации вы не указали почтовый ящик. Обратитесь в службу поддержки",MSG_INFO,MSG_NO_BACK);
		}
		else
			show_msg("Ошибка", "Такой пользователь не найден ",MSG_WARNING,MSG_OK);
	}
}
?>
<div id="login-form">
  <div id='header'>ВОССТАНОВИТЬ ПАРОЛЬ</div>
    <fieldset>
        <form method='POST'>
            <input name='login'    type="login" required placeholder="Логин"> 
            <input type="submit" value="ВОССТАНОВИТЬ">
        </form>
        <a id="register" href="<?php echo url(NULL, 'USERS','in')?>">Войти в личный кабинет &#10132;</a>
    </fieldset>
</div>