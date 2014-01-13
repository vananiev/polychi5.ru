<?php
		/********************************************************************

			Администратор переводит деньги с/на счета пользователей.
			Просмотр таблицы money

		********************************************************************
		POST:
		user_id -  с каким пользователем оперируем
		Symma - сумма перевода (положительная даем, отрицательная отнимаем)
		Commissiya - комиссия операции
		desc	- описание
		*/
		if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) $user_id = (int)$_POST['user_id'];
		if(isset($_POST['Symma']) && is_numeric($_POST['Symma'])) $Symma = 0+$_POST['Symma'];
		if(isset($_POST['Commissiya']) && is_numeric($_POST['Commissiya'])) $Commissiya = 0+$_POST['Commissiya'];
		//desc	- не исп в запросах
?>
<?php
	echo '<h1>',$URL['TITLE'],'</h1>';
	//проверка прав
	if( check_right('MNY_SYS_HISTORY'))
		{;}
	else if( check_right('MNY_SYS_TRANSFER')) 
		{;}
	else
		{
		show_msg(NULL, 'Вы не имеете доступа к этой странице',MSG_CRITICAL);
		return;
		}		
?>
<?php
  	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
	// осуществляем перевод средств между системой и пользователем
	if(isset($user_id))
		{
			// есть ли такой пользователь
			$query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
			$res = mysql_query($query,$msconnect_users) or die(mysql_error());
				if(mysql_num_rows($res)==0)
				{
					show_msg(NULL, "Такой пользователь не существует",MSG_WARNING);
				return;
				}
			//обновляем баланс
			if(isset($Symma) && isset($Commissiya) && $Symma!=0 )
				{
				if( !check_right('MNY_SYS_TRANSFER',R_MSG)) return; 	//проверка прав
				
				//запись в таблицу money
				if( $Symma>=0 )
					{
					$from = SYS_DOXOD;      //с доходов системы
					$to =  (int)$user_id;
					$give = (int)$Symma;
					$get = ((int)$Symma-(int)$Commissiya);
					$commision = (int)$Commissiya;
					$query="UPDATE `$table_users` SET balance = balance + '{$get}' WHERE id = '{$user_id}'";
					}
				else
					{
					$Symma = abs($Symma);
					$from =  (int)$user_id;
					$to = SYS_MONEY;      //системе
					$give = (int)$Symma+(int)$Commissiya;
					$get = (int)$Symma;
					$commision = (int)$Commissiya;
					$query="UPDATE `$table_users` SET balance = balance - '{$give}' WHERE id = '{$user_id}'";
					}
				$row_id = -1;    //добавляем новую запись
				$res = mysql_query($query,$msconnect_users) or die(mysql_error());
				$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$_POST['desc'],(int)$row_id,(int)$commision);  //добавляем
				if($money_row_id==0)
					{
					echo "<p align='center'><span style='color:red;'>Ошибка</font> при записи в таблицу money. Введите вручную.<br>Сумма: ".$Symma.'<br>Пользователь: '.$user_id.'<br>Комиссия: '.$Commissiya.'<br>Описание: '.htmlspecialchars ($_POST['desc'],ENT_QUOTES).'</p>';
							}
				else
							echo "<p align='center'>Сумма ".$Symma."руб. успешно переведена.</p>";
				}
			// получаем данные
			$query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
			$res = mysql_query($query,$msconnect_users) or die(mysql_error());
			$row = mysql_fetch_array($res);
			if(mysql_num_rows($res)==0)
				show_msg(NULL, "Такой пользователь не существует",MSG_WARNING);
			else
				{
				//проверка истории циркуляции денег по пользователю(мог ли пользователь столько заработать?)
				 $expect_balance = get_account_by_user((int)$user_id); //ожидаемый баланс
				}
		}
?>
<form method="POST" action="<?php echo url(NULL, 'MONEY', 'admin/money_move');?>">
	<div align="center">
		<table  width="562" id="table1" style="border-width: 0px">
			<tr>
				<td style="border-style: none; border-width: medium" align="right" width="325">
				Пользователь:</td>
				<td style="border-style: none; border-width: medium" align="left" width="227">
				<input type="text" name="user_id" size='11' value='<?php if(isset($user_id)) echo $user_id; ?>'></td>
			</tr>
			<?php
			if( check_right('MNY_SYS_TRANSFER') )
				{ ?>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<p style="line-height: 200%">Добавить(+)/забрать(-) к балансу пользователя (без учета комиссии):
					</td>
					<td style="border-style: none; border-width: medium" align="left" width="227">
					<p style="line-height: 200%">
					<input type="text" name="Symma" size='11' value='0'></td>
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<p style="line-height: 200%">
					<span lang="ru">Комиссия (снимается с пользователя):</span></td>
					<td style="border-style: none; border-width: medium" align="left" width="227">
					<p style="line-height: 200%">
					<input type="text" name="Commissiya" size='11' value='0'></td>
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<span lang="ru">Описание:</span></td>
					<td style="border-style: none; border-width: medium" align="left" width="227">
					<textarea rows="2" name="desc" cols="25"></textarea></td>
				</tr>
				<?php
				} ?>
		</table>
		<p align='center'><input type="submit" value="Подтвердить" name="B1"></p>
		<?php if( check_right('MNY_SYS_TRANSFER') )
				{ ?>
				<span class='info_text'>
					Внимание! Ошибки в истории счетов нельзя исправить на этой странице. Для исправления разницы балансов переидите 
						<?php echo url('сюда', 'MONEY', 'admin/get_system_balance');?>.<br>
					Для увеличения баланса пользователя вводите положительную сумму (деньги переведутся из доходов системы пользователю).<br>
					Для снятия с баланса - отрицательную (деньги переведутся от пользователя в систему(а не в ее доходы)).
				</span>
		<?php } ?>
	</div>
</form>
	
<?php
//Вывод истории счета
if( check_right('MNY_SYS_HISTORY') ||  check_right('MNY_SYS_TRANSFER'))
	{
	?>
	<h2>История счетов</h2>
	<div align="center">
		<table  class="history styled_table" width=100% id="table1" >
			<tr class="table_header">
				<th align="center"><b>Номер</b></th>
				<th align="center"><b>От</b></th>
				<th align="center"><b>Кому</b></th>
				<th align="center"><b>Отдано</b></th>
				<th align="center"><b>Получено</b></th>
				<th align="center"><b>Комиссия</b></th>
				<th align="center" width=150px><b>Дата</b></th>
				<th align="center"><b>Информация</b></th>
			</tr>
			<?php
			//формируем запрос в историю счетов
			$on_page = 30;	//число сток в таблице
	        $query = "SELECT count(*) FROM `$table_money` ";
	        if(isset($user_id_m))
	        	$query .= " WHERE ot = '{$user_id}' or komy = '{$user_id}' ";
	       	$res_cnt = mysql_query($query,$msconnect_money) or die(mysql_error());
	       	$row_cnt = mysql_fetch_array($res_cnt);
	       	$max = $row_cnt['count(*)'];
		    if(empty($URL['PAGE']))
				$p=1;
			else
				$p=$URL['PAGE'];
			$from = ($p-1)*$on_page;
			$query = "SELECT * FROM `$table_money`  ";
			if(isset($user_id))
	        	$query .= " WHERE ot = '{$user_id}' or komy = '{$user_id}' ";
	        $query .= " ORDER BY id DESC LIMIT $from,$on_page ";
			$res_history = mysql_query($query,$msconnect_money) or die(mysql_error());
			$cnt=$from+1;
			while($history = mysql_fetch_array($res_history))
					{
					?>
					<tr class='tr_ticket_other'>
						<td align="center"><?php echo $history['id']; ?></td>
						<td align="center"><?php
							if($history['ot']==SYS_MONEY)
								echo "Система";
							else if($history['ot']==SYS_DOXOD)
								echo "Доход системы";
							else if($history['ot']==WEBMONEY_IN)
								echo "Вебмани";
							else if($history['ot']==YANDEX_MONEY_IN)
								echo "Яндекс";
							else if($history['ot']==ROBOKASSA_IN)
								echo "Робокасса";
							else if($history['ot']>=0)
								echo get_user_link($history['ot']);
							else
								echo $history['ot'];
						?></td>
						<td align="center"><?php
							if($history['komy']==SYS_MONEY)
								echo "Система";
							else if($history['komy']==SYS_DOXOD)
								echo "Доход системы";
							else if($history['komy']==WEBMONEY_OUT)
								echo "Вебмани";
							else if($history['komy']==YANDEX_MONEY_OUT)
								echo "Яндекс";
							else if($history['ot']==ROBOKASSA_OUT)
								echo "Робокасса";
							else if($history['komy']>=0)
								echo get_user_link($history['komy']);
							else
								echo $history['komy'];
						?></td>
						<td align="center"><?php echo $history['give']; ?></td>
						<td align="center"><?php echo $history['get']; ?></td>
						<td align="center"><?php echo $history['commission']; ?></td>
						<td align="center">
							<?php echo date(DATE_TIME_FORMAT, strtotime($history['date']));?>
						</td>
						<td align="center">
							<?php echo htmlspecialchars($history['description'],ENT_QUOTES);?>
						</td>
					</tr>
					<?php
					$cnt++;
					} ?>
		</table>
	</div>
	<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
		get_table_nav('MONEY', 'admin/money_move', NULL, $max, $on_page); 
		
		if(isset($row['id'])) 
			{?>
			<table  width="562" id="table1" style="border-width: 0px">
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<?php echo url("<p style='line-height: 200%'>".$row['id'], 'USERS', 'about_user', 'user_id='.$row['id'], NULL,"target='_blank'");?>:
					</td>
					<td style="border-style: none; border-width: medium" align="left" width="227"><?php echo $row['login']; ?></td>
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<p style="line-height: 200%">Ожидаемый баланс: </td>
					<td style="border-style: none; border-width: medium" align="left" width="227"><?php echo $expect_balance; ?></td>
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<p style="line-height: 200%"><span style='color:blue;'>Текущий баланс:</font></td>
					<td style="border-style: none; border-width: medium" align="left" width="227"><span style='color:blue;'><?php echo $row['balance']; ?></font></td>
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="right" width="325">
					<p style="line-height: 200%">Разница балансов (Ожидаемый - Текущий): </td>
					<td style="border-style: none; border-width: medium" align="left" width="227"><?php echo ($expect_balance - $row['balance']); ?></td>
				</tr>
				<tr>
					<td colspan="2" align='center'>
					<?php
						if($row['balance'] == $expect_balance)
							echo "<span style='color:green;'>Баланс пользователя верен.</font>";
						 else if($row['balance'] > $expect_balance)
							echo "<span style='color:red;'>Баланс пользователя ЗАВЫШЕН судя по истории его заработка!!!</font>";
						 else
							echo "<span style='color:#0000FF;'>Баланс пользователя меньше ожидаемого судя по истории заработка</font>";
					?>
					</td>
				</tr>
			</table>
			<?php 
			}
	}
	?>
