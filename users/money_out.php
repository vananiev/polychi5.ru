<?php /*
	Скрипт создания заявки на вывод средств.
	Передаваемые параметры.
	Метод - POST
	Symma		-сумма выводимых денег
	user_id 	-номер ползователя

	$_SESSION['user_id']	-номер пользователя в системе,который производит оплату (для проверки с $user_id)

*/ 
	if(isset($_POST['Symma']) && is_numeric($_POST['Symma'])) $Symma = (int)$_POST['Symma'];
	if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) $user_id = (int)$_POST['user_id'];
?>

<?php
	if(!check_right('TSK_SOLVING',R_MSG)) return; 	//проверка прав пользователя
?>
<?php
	//если случайно забрели на страницу в избежание недоразумений снятия средств
	//останавливаем скрипт
	/*
	if($_SERVER['HTTP_REFERER'] != "t=get_balance")
		{
		show_msg(NULL,"Ошибка",MSG_CRITICAL);
		return;
		}
	*/
?>

<?php
	if(isset($user_id) && isset($Symma) && $_SESSION['user_id']==$user_id && $Symma>0)
		{
		//require_once(SCRIPT_ROOT."/task/DB_task.php");
		//проверяем не должен ли перерешать решающий
		$query = "SELECT status
					FROM `$table_task`
					WHERE solver='{$_SESSION['user_id']}'";
			$res = mysql_query($query,$msconnect_task) or die(mysql_error());
			while($row = mysql_fetch_assoc($res))
				{
				if($row['status'] == 'REMK')
					{
					show_msg(NULL,"Вывод средств не возможен. Вы не провели работу над ошибками: У вас есть недорешенные задания.",MSG_WARNING);
					return;
					}
				}

		//получаем сведения о балансе
			$query = "SELECT balance
					FROM `$table_users`
					WHERE id='{$_SESSION['user_id']}'";
			$res = mysql_query($query,$msconnect_users) or die(mysql_error());
			$row = mysql_fetch_array($res);
		if((int)$row['balance'] >=(int)$Symma)
			{
			//посылаем  запрос
			$query = "UPDATE `$table_users`
				   SET money_out_query = '{$Symma}'
				   WHERE id='{$_SESSION['user_id']}'";
			$res = mysql_query($query,$msconnect_users) or die("Ошибка, пожалуйста, попробуйте еще раз<br>".mysql_error());
			show_msg(NULL,"Ваш запрос занесен в базу и будет обработан в ближайшее время",MSG_INFO);
			return;
			}
		 else
			show_msg(NULL,"На вашем балансе не достаточно средств",MSG_WARNING);
		}
	else
		show_msg(NULL,"Не верная сумма",MSG_WARNING);
?>