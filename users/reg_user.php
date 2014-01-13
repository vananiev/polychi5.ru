<?php
	/***********************************************************************
		Регистрация нового пользователя
	***********************************************************************/

//регистрация
if(isset($_POST['login']) && isset($_POST['password']))
{
	//добавляем пользователя
	if($_POST['login']=='Логин' || $_POST['password']=='Пароль') 
		show_msg(NULL, 'Не верный логин или пароль');
	else
	{
		if($_POST['mail']=='e-mail') $_POST['mail']='';
		$ret = call_user_func($users->reg_user, $_POST['login'], $_POST['password']);
		if($ret !== true)
			show_msg(NULL, $ret, MSG_WARNING, MSG_OK);
		else
		{
			// логинимся
			login($_POST['login'], $_POST['password']);
			?>
			<script lang="javascript">location.href='<?php echo url(NULL, 'USERS', 'in');?>'</script>
			<?php
		}
	}
}

if(isset($_SESSION['user_id']))
	{
	?>
	<h2>О пользователе</h2>
	<?php
	require(SCRIPT_ROOT."/users/in.php");
	}
else
	{ ?>
	<div id="login-form">
      <div id='header'>РЕГИСТРАЦИЯ</div>
        <fieldset>
            <form method='POST'>
                <input name='login'    type="login" required placeholder="Логин"> 
                <input name='password' type="password" required placeholder="Пароль"> 
                <input type="submit" value="Присоединиться">
            </form>
            <a id="register" href="<?php echo url(NULL, 'USERS','in')?>">Войти в личный кабинет &#10132;</a>
        </fieldset>
	</div>
	<?php } ?>
