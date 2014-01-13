<?php
	/*
	if(isset($_COOKIE['authorize']))
		{?>
		<script language="javascript"> 
			document.cookie = "authorize=false; path=/" 
		</script>
		<?php }

	if(isset($_SESSION['user_id']))
		{
		global $table_users;
		global $msconnect_users;
		$query = "UPDATE `$table_users`
		    		SET authorize = NULL
	            	WHERE id='".((int)$_SESSION['user_id'])."'";
	    	mysql_query($query,$msconnect_users);// or die("Ошибка при выходе из системы".mysql_error());	//пользователь не в сети
		}
	session_unregister('user_id'); //Эта функция не разустанавливает/unset соответствующую глобальную переменную name, она только предотвращает сохранение переменной как части сессии. Вы обязаны вызывать unset() для удаления соответствующей глобальной переменной.
	unset($_SESSION['user_id']); //разустанавливает данную переменную.
	unset($_SESSION);
	session_unset();	//освобождает все переменные сессии, зарегистрированные на данный момент.
	session_destroy(); 	//разрушить сессию
	*/
	//require(get_request_file(DEFAULT_PAGE));
	/*if(function_exists('show_msg'))
		show_msg("Выход",
		"Выход успешно выполнен
		<a href='/'>[ok]</a>",MSG_INFO,MSG_NO_BACK);
	else
		echo "Выход успешно выполнен<br>";
	*/
?>
<script type="text/javascript" >
	location.href="/";
</script>