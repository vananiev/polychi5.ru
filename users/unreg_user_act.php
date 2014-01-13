<?php

//удаляем пользователя
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != 0 && check_access())
{
	if( delete_user() ){ 
		if(function_exists('show_msg'))
			show_msg(NULL,'Ваша учетная запись удалена',MSG_INFO,MSG_NO_BACK);
		else
			echo "<p align='center'>Ваша учетная запись удалена.</p>";
		}
	else{ 
		if(function_exists('show_msg'))
			show_msg(NULL,'Ваша учетная запись не удалена',MSG_CRITICAL,MSG_NO_BACK);
		else
			echo "<p align='center'>Ваша учетная запись не удалена.</p>";
		}
		
}
else
	if(function_exists('show_msg'))
		show_msg(NULL,'Действие запрещено',MSG_CRITICAL);
	else
		echo 'Действие запрещено';
	
	//закрываем БД
	//mysql_close($msconnect_users);
?>