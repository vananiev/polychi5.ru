<?php
		/********************************************************************

					Перевод состояний заданий в OK и оплата решающим

		********************************************************************
		Принимаемые параметры POST
        $task_id - номер задания, статус которого надо поменять
        $task_id == -1 - поменять статус всех заданий с истекшим сроком (более 7 дней решенные)
        $task_id - не установлен вывести список всех заданий с истекшим сроком (более 7 дней решенные)
*/
		if(isset($_POST['task_id']) && is_numeric($_POST['task_id'])) $task_id = (int)$_POST['task_id'];

?>
<h1>Подтвердить решение</h1>
<?php

if(!check_right('TSK_MK_OK',R_MSG)) return;	//проверка прав

require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
function pay_solver($row)
{
global $table_users;
global $msconnect_users;
global $table_task;
global $msconnect_task;
//если пользователь не олатил решение(не скачал его), то автоматически снимаем деньги
if($row['user_pay']==0)
	{
	echo "<br>Ученик "; 
	echo url('№'.$row['user'], 'USERS', 'about_user', 'user_id='.$row['user']);
	echo "<span style='color:red;'>НЕ ОПЛАТИЛ</span> решение. Деньги решающим ";
	echo url('№'.$row['solver'], 'USERS', 'about_user', 'user_id='.$row['solver']);
	echo " не получены.";
	exit;
	/*
    	//снимаем деньги
 	$query="UPDATE $table_users
		SET balance = balance - {$row['price']}
      		WHERE id = {$row['user']}";
	$res = mysql_query($query,$msconnect_users);
	if(mysql_affected_rows() != -1)
		{
		$from = (int)$row['user'];
		$to = SYS_MONEY;      //системе
		$give = (int)$row['price'];
		$get = sum_after_comm((int)$row['price']);
		$description = "Снятие средств за решения №{$row['id']} c пользователя №{$row['user']}.  Сумма: {$row['price']}.";
		$row_id = -1;    //добавляем новую запись
		$commision = $give - $get;
		//указываем что оплатил
		$query="UPDATE $table_task
				SET user_pay = 1
	         	WHERE id = {$row['id']}";
	   	$res = mysql_query($query,$msconnect_task);
		$print = "Снятие средств за решения №{$row['id']} c пользователя ";
		echo url($row['user'], 'USERS', 'about_user', 'user_id='.$row['user'], NULL, "target='_blank'");
		echo ".  Сумма: {$row['price']}.";
	   	if(mysql_affected_rows() == -1)
	   		{
			$print .=" Флаг оплаты не установлен(задание №{$row['id']})";
	   		}
	  	//запись в таблицу циркуляции денег (money)
		echo "<br>".$print;
		$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
		//ошибка записи
		if($money_row_id==0)
			{
			//если $money_row_id ==0 - ученик оплатил
			echo "<br>Ошибка записи об оплате в Историю счетов(тавлица money)";
			}
		}
	else
		{
		echo "<br><span style='color:red;'>Ошибка при оплате</span> решения задания ";
		echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");
		echo ".Снять с Ученика ";
		echo url('№'.$row['user'], 'USERS', 'about_user', 'user_id='.$row['user'], NULL, "target='_blank'");
		echo ".Сумму:{$row['price']}";
		}
	*/
	}
else
	{
	echo "<br>Ученик ";
	echo url('№'.$row['user'], 'USERS', 'about_user', 'user_id='.$row['user'], NULL, "target='_blank'");
	echo " оплатил решение ранее.";
	}

//даем деньги решающему, только 1 раз
if($row['solver_get_pay']==0)
	{
   	//платим
	$sys_money = sum_after_comm($row['price']);	//учет комиссии после заказчика
   	$payed_maney = price_after_all_comm($row['price']);   //оплачиваемая сумма (учет комиссии от заказчика и автора)
   	$query="UPDATE `$table_users`
				SET balance = (`balance` + '$payed_maney')
        		WHERE id = '{$row['solver']}'";
	$res = mysql_query($query,$msconnect_users);
	if(mysql_affected_rows() != -1)
		{
		$from = SYS_MONEY;      //система
		$to = (int)$row['solver'];
		$give = (int)$sys_money;
		$get = (int)$payed_maney;
		$description = "Оплата решения задания №{$row['id']}.Деньги получены Решающим №{$row['solver']}.Сумма: $payed_maney.";
		$row_id = -1;    //добавляем новую запись
		$commision = (int)($sys_money-$payed_maney);
		//флаг: решающий получил плату
		$query="UPDATE `$table_task`
	    				SET solver_get_pay = 1
	            		WHERE id = '{$row['id']}'";
		$res = mysql_query($query,$msconnect_task);
		$print = "Оплата решения задания №{$row['id']}.Деньги получены Решающим ";
		$print .= url('№'.$row['solver'], 'USERS', 'about_user', 'user_id='.$row['solver'], NULL, "target='_blank'");		
		$print .= ".Сумма: $payed_maney.";
		if(mysql_affected_rows() == -1)	//ошибка при указании флага того, что оплатили решающему
			{
			$$print .= " Флаг получения денег не установлен(задание №{$row['id']})";
			}
		//запись в таблицу циркуляции денег (money)
		echo "<br>".$print;
		$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
		//ошибка записи
		if($money_row_id==0)
			{
			//если $money_row_id ==0 - решающий ПОЛУЧИЛ денеги
			echo "<br>Ошибка записи о получении денег в Историю счетов(тавлица money)";
			}
		}
	else
		{
		echo "<br><span style='color:red;'>Ошибка при оплате</span> решения задания ";
		echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");
		echo ".Оплатить Решающему ";
		echo url('№'.$row['solver'], 'USERS', 'about_user', 'user_id='.$row['solver'], NULL, "target='_blank'");
		echo ".Сумму:{$payed_maney}(Оплатить эти деньги. Комиссия системы уже учтена).";
		}
	}
else
	{
	echo "<br>Деньги решающим ";
	echo url('№'.$row['solver'], 'USERS', 'about_user', 'user_id='.$row['solver'], NULL, "target='_blank'");
	echo " были получены ранее.";
	}
}
//------------------------------------------------------------------------------
    //получаем сведения о задании
	if(!isset($task_id))
		{
		$query="SELECT id,solv_date,status, user_pay
	            FROM `$table_task`
	            WHERE status = 'SOLV'";
		$res = mysql_query($query,$msconnect_task) or die(mysql_error());
  		$cnt = 0;
		$rel = url(NULL, 'TASK', 'admin/make_OK');
  		echo "<form method='POST' action='".$rel."'>";
  		while($row = mysql_fetch_array($res))
			{
			if (strtotime($row['solv_date'])!=NULL &&(strtotime($row['solv_date']) < (time()-7*24*3600)))
				{//прошло более 7 дней с момента решения
				$cnt++;
				?>
		        <p>
		        <?php
		        	echo "$cnt. Задание ";
					echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");
					echo ":статус {$row['status']}.";
				if($row['user_pay']==0)
					echo " <span style='color:red;'>Задание НЕ ОПЛАЧЕНО</span>.";
				else
					echo " Задание оплачено.";
				echo "    - Поменять статус задания";
		        ?>
		        <input type="submit" value="<?php echo $row['id']; ?>" name="task_id"></p>
		  		<?php
		  		}
			}
		if($cnt!=0)
			{
			?>
			<p>Поменять статус всех оплаченных заданий<input type="submit" value="-1" name="task_id"></p>
			<?php
			}
		else
			show_msg(NULL, "Нет заданий решенных уже более7 дней ");
		echo "</form>";
		}
	else if($task_id == -1)
		{
		//меняем статус всех
		$query="SELECT *
	            FROM `$table_task`
	            WHERE status = 'SOLV' AND user_pay=1";
		$res = mysql_query($query,$msconnect_task) or die(mysql_error());
		$cnt=0;
		while($row = mysql_fetch_array($res))
			{
			if (strtotime($row['solv_date']) < (time()-7*24*3600))
				{//прошло более 7 дней с момента решения
				pay_solver($row);   //оплата решающему
		        $query="UPDATE `$table_task`
		        	SET status = 'OK'
		        	WHERE id = '{$row['id']}'";
				mysql_query($query,$msconnect_task) or die("Не удалось поменять статус занания №{$row['id']}".mysql_error());
				$cnt++;
				echo "<br>$cnt. Статус задания ";
				echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");
				echo " сменили на OK";
				}
			}
		echo "<br>Выполнена обработка всех оплаченных решений";
		}
	else if($task_id>0 )
		{
		//меняем статус одного
        $query="SELECT *
	            FROM `$table_task`
	            WHERE id = '{$task_id}'";
		$res = mysql_query($query,$msconnect_task) or die(mysql_error());
		$row = mysql_fetch_array($res);
		if (strtotime($row['solv_date']) < (time()-7*24*3600))
			{//прошло более 7 дней с момента решения
		pay_solver($row);   //оплата решающему
	        $query="UPDATE `$table_task`
	        	SET status = 'OK'
	        	WHERE id = '{$row['id']}'";
			mysql_query($query,$msconnect_task) or die("Не удалось поменять статус занания №{$row['id']}".mysql_error());
			}
		echo "<br>Статус задания ";
		echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");
		echo " сменили на OK";
		}
	else
		show_msg(NULL, "Не верный параметр: ".$task_id,MSG_WARNING);
	// обновляем рейтинги
	if(isset($INCLUDE_MODULES['USERS']['PATH']))
		include(SCRIPT_ROOT.$INCLUDE_MODULES['USERS']['PATH'].'/admin/recalculate_rating.php');
?>