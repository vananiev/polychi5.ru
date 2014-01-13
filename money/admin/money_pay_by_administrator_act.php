<?php
		/********************************************************************
			Администратор выводит деньги из системы. Запись в БД

		Параметры POST:
		user_id  - номер пользователя, которому производится оплата
		********************************************************************/
		
		if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) $user_id = (int)$_POST['user_id'];
    if(isset($_POST['payment_system']) && is_numeric($_POST['payment_system'])) $payment_system = (int)$_POST['payment_system'];


	if( !check_right('MNY_OUT', R_MSG)) return;  //проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	//если случайно забрели на страницу в избежание недоразумений снятия средств
	//останавливаем скрипт
	if(isset($_COOKIE['pay_'.$user_id]))
		{
		show_msg(NULL, "Оплата пользователю №{$user_id} уже была произведена.",MSG_CRITICAL);
		return;
		}
?>

<?php
    require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
	if(isset($user_id) && isset($payment_system))
		{
		// получаем данные
        $query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
	    $res = mysql_query($query,$msconnect_users) or die("Обновление таблиц не произошло.
	    										 Ошибка при чтения номера кошелька пользователя.".mysql_error());
        $row = mysql_fetch_array($res);
        $Symma = 0+$row['money_out_query'];
        $balance = 0+$row['balance'];
        if($row['koshelek'] == "")
        	{
        	show_msg(NULL,  "Пользователь не указал кошелек",MSG_WARNING);
        	return;
        	}


        //обновляем таблицу user
        $new_balance = $balance - $Symma;
        $query="UPDATE `$table_users` SET money_out_query = 0, balance = '$new_balance' WHERE id = '{$user_id}'";
        echo "Запрос обновления таблицы user: ".$query."<br><br>";
	    $res = mysql_query($query,$msconnect_users) or die("Ошибка обновления в users <br>".mysql_error());
        
       	//обновляем таблицу money
       	$money_row_id = add_record(
       		(int)$user_id, //от пользователя
       		$payment_system,  			//на кошелек пользователя
       		(int)$Symma,    //вывод из системы
       		(int)$Symma,    //пользователь получил
       		"Вывод средств из системы",
       		-1, //добавление новой записи
       		0);   //комиссия
       	if($money_row_id == 0)
       		{
       		echo "Ошибка обновения истории счетов пользователя (таблицы money), (но таблица user обновлена). Система денег не потеряла, но пользователь
       				№{$user_id} потерял сумму $Symma. Верните их ему на счет.";
       		exit;
       		}
        // снова получаем данные
        $query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
	    $res = mysql_query($query,$msconnect_users) or die("Обновление таблиц произошло.
	    										 Ошибка при чтения номера кошелька пользователя.
	    										 Не забудьте оплатить:<br><br>
											       	пользователю №{$user_id} [{$row['login']}]<br><br>
											       	сумму: {$Symma}
											       	И убедитесь, что у пользователя balance==$new_balance и money_out_query==0".mysql_error());
        $row = mysql_fetch_array($res);

       	echo "<span style='color:#0000FF;'>Обновление таблиц прошло без ошибок. Не забудьте оплатить:<br><br></font>
       	<span style='color:red;'>пользователю №{$user_id} [{$row['login']}]<br><br>
       	на кошелек: {$row['koshelek']}<br><br>
       	сумму: $Symma руб.<br><br>
       	</font>
       	***********************<br><br>
       	Запрос на вывод средств после оплаты: {$row['money_out_query']} (должен быть ==0!)<br><br>
       	Баланс пользователя до вывода: $balance руб.<br><br>
       	Баланс пользователя после вывода: {$row['balance']} руб.<br><br>
       	Разница балансов: ".($balance-$row['balance'])." руб. (должно быть == $Symma!!!)<br><br>";
		?>
		<script language="javascript"> 
			document.cookie = "<?php echo 'pay_'.$user_id;?>=ok; ; path=/" 
		</script>
 		<?php
		}
	else
		show_msg(NULL, "Не передан id пользователя",MSG_WARNING);
?>