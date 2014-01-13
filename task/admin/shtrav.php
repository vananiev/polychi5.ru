<?php
		/********************************************************************

					Наложение штрафов

		*******************************************************************
		GET:
		task_id - задание, из-за которого штрафуем
*/
		if(isset($_GET['task_id']) && is_numeric($_GET['task_id'])) $task_id = (int)$_GET['task_id'];
?>
<h1>Штрафы</h1>
<?php

	if(!check_right('TSK_SHTRAV',R_MSG)) return;	//проверка прав

	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
	//штрафуем
	if(isset($task_id))
		{
		//штрафуем решающего
		$query="SELECT solver,user,price,user_pay 
			FROM `$table_task`
	            	WHERE id = '{$task_id}' and (status='WAIT' OR status='REMK')";
		$res = mysql_query($query,$msconnect_task) or die("Ошибка при получении данных о задании<br>".mysql_error());
		if(mysql_num_rows($res)==0)
			{
			show_msg(NULL,"Не возможно оштрафовать",MSG_WARNING);
			return; 	//если нет удовлетворяющих параметрам запроса заданий
			}
		$row = mysql_fetch_array($res);
		$symma = get_strav_sum ($row['price']);
		$query="UPDATE `$table_users`
			SET balance = `balance` - '{$symma}'
	            	WHERE id = '{$row['solver']}'";
		$res = mysql_query($query,$msconnect_users) or die("Ошибка при наложении штрафа<br>".mysql_error());
		$from = (int)$row['solver'];
		$to = SYS_MONEY;      		//системе
		$give = (int)$symma;
		$get = $row['price'];
		$description = "Наложение штрафа за несвоевременное решение задания №{$task_id}.";
		$row_id = -1;    //добавляем новую запись
		$commision = $give - $get;
		$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
		if($money_row_id==0)
			{
			echo "Ошибка при записи в таблицу money снятия с решающего";
			echo url('№'.(int)$row['solver'], 'USERS', 'about_user', 'user_id='.(int)$row['solver'], NULL, "target='_blank'");
			echo ' денег('.(0+$symma).' руб.)';
			}
		$query="UPDATE $table_task
			SET status = 'NSOL'
	            	WHERE id = '{$task_id}'";
		$task_rel = url('№'.$task_id, 'TASK', 'task', 'id='.$task_id, NULL, "target='_blank'");
		$res = mysql_query($query,$msconnect_task) or die("Ошибка при изменении состояния задания ".$task_rel."<br>".mysql_error());
		//возвращаем деньги решающему
		if($row['user_pay']==1)
			{
			$query="UPDATE `$table_users`
				SET balance = `balance` + '{$row['price']}'
		            	WHERE id = '{$row['user']}'";
			$res = mysql_query($query,$msconnect_users) or die("Ошибка при возврате денег<br>".mysql_error());
			$from = SYS_MONEY;		//системе
			$to = (int)$row['user'];      		
			$give = (int)$row['price'];
			$get = (int)$row['price'];
			$description = "Возврат денег за несвоевременное решение задания №{$task_id}.";
			$row_id = -1;    //добавляем новую запись
			$commision = 0;
			$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
			if($money_row_id==0)
				{
				echo "Ошибка при записи в таблицу money возврата денег (".(0+$row['price'])." руб.) пользователю ";
				echo get_user_link($row['user'],"target='_blank'");
				}
			echo "Деньги вернули пользователю ".get_user_link($row['user'],"target='_blank'").".";
			}
		echo "Решающий ".get_user_link($row['solver'],"target='_blank'")." оштрафован. Задание ";
		echo url('№'.$task_id, 'TASK', 'task', 'id='.$task_id, NULL, "target='_blank'");
		}

?>

<div align='center'>
	<table class='table_shtrav styled_table'>
		<tr class="table_header">
			<td align="center"><b>Решающий</b></td>
			<td align="center"><b>Задание</b></td>
			<td align="center"><b>Состояние</b></td>
			<td align="center"><b>Причина</b></td>
			<td align="center"><b>Просрочен на</b></td>
			<td align="center"><b>Сумма штрафа</b></td>
			<td align="center"><b></b></td>
		</tr>
		<?php
			//решен не в срок
			$today = date('Y-m-d H:i:s',time());
			$query="SELECT *
		            FROM `$table_task`
		            WHERE status='WAIT' OR status='REMK'";
			$res = mysql_query($query,$msconnect_task) or die(mysql_error());
			while($row = mysql_fetch_array($res))
				{
				if(strtotime($row['resolve_until']) < time())
					{
					?>
					<tr class="tr_class_other">
						<td align="center"><?php echo url('№'.$row['solver'], 'USERS', 'about_user', 'user_id='.$row['solver'], NULL, "target='_blank'");?></td>
						<td align="center"><?php echo url('№'.$row['id'], 'TASK', 'task', 'id='.$row['id'], NULL, "target='_blank'");?></td>
						<td align="center"><?php
								if($row['status'] =="REMK")
									echo "<span style='color:#CC0000;'><b>Перерешать</b></span>";
								else if($row['status'] == "NEW")
									echo "<span style='color:red;'>Новый</span>";
								else if($row['status'] == "GETS")
									echo "<span style='color:#FF0066;'>Мои правила</b></span>";
								else if($row['status'] == "WAIT")
									echo "<span style='color:blue;'>Идет решение</span>";
								else if($row['status'] == "SOLV")
									echo "Решен";
								else if($row['status'] == "OK")
									echo "<span style='color:#800080;'>Выполнен</span>";
								else if($row['status'] == "NSOL")
									echo "<span style='color:#800080;'>Не решен</span>";
						               	else
						               		echo $row['status']; 
								 ?></td>
						<td align="center">Задание не решено вовремя</td>
						<td align="center">
							<?php
								$days = (int)((time() - strtotime($row['resolve_until']))/3600/24);
								$hours =  (int)(((time() - strtotime($row['resolve_until'])) - $days*3600*24)/3600);
								echo $days. " дн., ".$hours." ч." ;
							?>
						</td>
						<td align="center"><?php echo get_strav_sum ($row['price']);?></td>
						<td align="center"><?php url('оштрафовать', 'TASK', 'admin/shtrav', 'task_id='.$row['id']);?></td>
					</tr>
					<?php
					}
				}	
		?>
	</table>
</div>
<style type="text/css">
.table_shtrav	{
		margin:0 auto;
		font-family: "Lucida Sans", "Lucida Grande", "Lucida Sans Unicode", sans-serif;
		font-size:14px;
		width:100%;
		}
.table_shtrav td		{ border:1px solid #888;}
</style>