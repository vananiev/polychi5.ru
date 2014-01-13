<?php
// логин происходит в файле module.php
if($LOGIN_OK!==true && $LOGIN_OK!==NULL) show_msg(NULL, $LOGIN_OK, MSG_WARNING, MSG_OK);
// переходим если перед входом запрашивали страницу
if(isset($USER) && isset($_SESSION['QUERY_STRING'])) {?>
	<script lang="Java">	
			<?php $s=$_SESSION['QUERY_STRING'];unset($_SESSION['QUERY_STRING']); ?>
			location.href='<?php echo $s;?>';
	</script>
<?php 
	return;
	} ?>
<?php
if(isset($_SESSION['user_id'])){
	echo "<div class='box_standart in_box'>";
	//задания
	$not_finished_tasks_num =  $users->db->query("SELECT count(id)
		FROM `$table_task`
		WHERE ((user='{$_SESSION['user_id']}' or
			solver='{$_SESSION['user_id']}') and
			status!='OK' and status!='SOLV')")->fetch_assoc();
	$not_finished_tasks_num =  $not_finished_tasks_num['count(id)'];
	echo "<font style='font-size:12px;' color=blue>";
	echo "Здравствуйте, ".get_user_link($_SESSION['user_id'],"style='color:blue;text-decoration:none;'")."!<br>";
	echo show_avatar($_SESSION['user_id'],"style='width:35px;height:35px;margin:5px;float:left;'");
	echo "</font>";
	echo "&nbsp;&nbsp;<font style='font-size:12px;'  face='Comic Sans MS'><u title='Ваш баланс' style='cursor: pointer;'>".$USER['balance']." руб</u></font><br>";
	echo "<font style='font-size:10px;'>";	
	echo "&nbsp;&nbsp;  : <u title='Время входа' style='cursor: pointer;'>".date("d M H:i",strtotime($USER['last_visit']))."</u>&nbsp;";
	echo "&nbsp;&nbsp;  : <u title='Ожидают решения' style='cursor: pointer;'>".$not_finished_tasks_num." заданий</u>";
	if(file_exists( SCRIPT_ROOT.'/'.$INCLUDE_MODULES['TICKET']['PATH'].'/kernel/talkdriver/function.php' ))
		echo "<a ".link_for_consultant()." title='Непрочитанные сообщения'><img src=".td_cnt_new_mes( $_SESSION['user_id'])." height=14 align=absmiddle></a>";
	?>
	</font>
	<div style="clear:both;"></div>
	<font style='font-size:10px;'>
	<?php  
		echo url('[выйти]', 'USERS','exit_user',NULL,NULL,"title='Выйти из системы'")."&nbsp;&nbsp;";
		echo url('[изменить данные]', 'USERS','admin/update_user',NULL,NULL,"title='Настройка учетной записи'")."&nbsp;&nbsp;";
		echo url('[удалить личный кабинет]', 'USERS','unreg_user',NULL,NULL,"title='Удалить вашу учетную запись'")."&nbsp;&nbsp;"; 
	?>
	</font>
</div>
	<?php
	}
else{
	?>
	<div id="login-form">
      <div id='header'>АВТОРИЗАЦИЯ</div>
        <fieldset>
            <form method='POST' action="<?php echo url(NULL,'USERS','in');?>">
                <input name='login'    type="text" required placeholder="Логин"> 
                <input name='password' type="password" required placeholder="Пароль"> 
                <a id="restore_password" title="Восстановить пароль" href="<?php echo url(NULL, 'USERS','restore_password');?>">?</a>
                <input type="submit" value="ВОЙТИ">
            </form>
            <a id="register" href="<?php echo url(NULL, 'USERS','reg_user')?>">Создать личный кабинет &#10132;</a>
        </fieldset>
        <div id="social-login">
        	<span id="social-desc">Войти через:</span>
        	<?php echo users_ulogin_panel(array('display'=>'panel')); ?>
        </div>
    </div>
<?php } ?>
