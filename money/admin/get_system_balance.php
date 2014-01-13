<?php
		/********************************************************************

					Проверка баланса системы

		********************************************************************
		Принимаемые параметры POST
		user_id - ид пользователя, баланс которого надо посмотреть
		set_balance - устновить баланс пользователя user_id равным set_balance		
*/
	if(isset($_POST['set_balance'])) $set_balance = 0+$_POST['set_balance'];
	if(isset($_POST['user_id'])) $user_id = (int)$_POST['user_id'];

	if( !check_right('MNY_SEE_SYS_BAL',R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<p align='center'>
<?php
	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");

    $sys_money = get_account_by_user(SYS_MONEY);
    echo "Баланс системы: ".$sys_money;
    $webmoney_in =  get_account_by_user(WEBMONEY_IN);
    echo "<br><br>Суммарный ввод в систему (Webmoney): ".$webmoney_in;
    $yandex_money_in =  get_account_by_user(YANDEX_MONEY_IN);
    echo "<br><br>Суммарный ввод в систему (Яндекс): ".$yandex_money_in;
    $rb_in =  get_account_by_user(ROBOKASSA_IN);
    echo "<br><br>Суммарный ввод в систему (Robokassa): ".$rb_in;
    $webmoney_out = get_account_by_user(WEBMONEY_OUT);
    echo "<br><br>Суммарный вывод из системы (Webmoney): ".$webmoney_out;
    $yandex_money_out = get_account_by_user(YANDEX_MONEY_OUT);
    echo "<br><br>Суммарный вывод из системы (Яндекс): ".$yandex_money_out;
    $rb_out = get_account_by_user(ROBOKASSA_OUT);
    echo "<br><br>Суммарный вывод из системы (Robokassa): ".$rb_out;
	$salary = get_account_by_user(SOLV_SALARY);
    echo "<br><br>Ожидают выплаты решающим: ".$salary;
    $all_users_balance =  get_account_by_user(ALL_USERS_BALANCE);
    echo "<br><br>Суммарный баланс всех пользователей: ".$all_users_balance;
    $sys_doxod = get_account_by_user(SYS_DOXOD);
    echo "<br><br>Доходность системы: ".$sys_doxod;
    //
    echo "<br><br>Верно ли:<br> [Суммарный ввод в систему] == [Баланс системы] + [Суммарный вывод из системы]:   ";
    echo "<span style='color:red;'>";
    if(!is_int($sys_money) || !is_int($webmoney_out) || !is_int($webmoney_in))
        echo "Ошибки в истории счетов";
 	else
 		{
		$s = $webmoney_in + $yandex_money_in + $rb_in - ($sys_money + $webmoney_out + $yandex_money_out + $rb_out);
		if($s == 0)
			echo "да";
		else if($s > 0)
			echo "($s) нет. Ввод больше. Это плюс системе.";
		else
			echo "($s) нет. Ввод меньше. Мы в убытке!!!";
	  	}
    echo "</span>";
    //
    echo "<br><br>Верно ли:<br> [Баланс системы] == [Cумма балансов всех пользователей] + [Зарплата решающим] + [Доходность системы]:   ";
    echo "<span style='color:red;'>";
    if(!is_int($all_users_balance) || !is_int($sys_doxod) || !is_int($sys_money))
        echo "Ошибки в истории счетов";
 	else
 		{
		$sm = $sys_money - ($all_users_balance + $sys_doxod + $salary);
		$d = $sys_money - ($all_users_balance + $salary);
		if($sm == 0)
			echo "да";
		else if($sm > 0)
			echo "($sm) нет. Баланс системы больше. Это плюс системе.";
		else if($d >= 0)
			echo "($sm) нет.Но Баланса системы хватит, чтобы расплатиться с пользователями.";
		else if ($d < 0)
			echo "($sm) нет. Баланса системы НЕ хватит, чтобы расплатиться с пользователями!!!";
	  	}
    echo "</span>";
?>
</p>
<hr>
<form method="POST" action="<?php echo url(NULL, 'MONEY', 'admin/get_system_balance');?>">
	<p align='center'>
	<?php
	if(isset($user_id) && ereg("^[0-9]{1,}$",$user_id))
		{
			//обновляем баланс
			if(isset($set_balance) && ereg("^[0-9\-]{1}[0-9]{0,}$",$set_balance))
				{
				if( !check_right('MNY_CNG_USR_BAL',R_MSG)) return; 	//проверка прав
				
				$query="UPDATE `$table_users` SET balance = '{$set_balance}' WHERE id = '{$user_id}'";

		   		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
				}
			// получаем данные
	        $query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
		    $res = mysql_query($query,$msconnect_users) or die(mysql_error());
	        $row = mysql_fetch_assoc($res);
	        if(mysql_num_rows($res)==0)
	        	echo "Такой пользователь не существует";
	      	else
	      		{
				//проверка истории циркуляции денег по пользователю(мог ли пользователь столько заработать?)
	             $expect_balance = get_account_by_user((int)$user_id); //ожидаемый баланс
	             echo url($row['id'], 'USERS', 'about_user', 'user_id='.$row['id']);
				 echo ": {$row['login']}<br>";
	             echo "Ожидаемый баланс: ".$expect_balance."<br>";
	             echo "Текущий баланс: ".$row['balance']."<br>";
	             echo "Разница балансов (Ожидаемый - Текущий): ".($expect_balance - $row['balance'])."<br><br>";
	             if($row['balance'] == $expect_balance)
	             	echo "<span style='color:green;'>Баланс пользователя верен.</span>";
	             else if($row['balance'] > $expect_balance)
	             	echo "<span style='color:red;'>Баланс пользователя ЗАВЫШЕН судя по истории его заработка!!!</span>";
	             else
	             	echo "<span style='color:#0000FF;'>Баланс пользователя меньше ожидаемого судя по истории заработка</span>";
	           	}
		}
	?>
	</p>
	<p align='center'>Пользователь: <input type="text" name="user_id" size='3' value='<?php if(isset($user_id)) echo $user_id; ?>'>
	<input type="submit" value="Узнать баланс по ID пользователя" name="B1"></p>
</form>
<?php if(isset($expect_balance)) {?>
	<form method="POST" action="<?php echo url(NULL, 'MONEY', 'admin/get_system_balance');?>"><p align='center'>Установить баланс: <input type="text" name="set_balance" size='3' value='<?php echo $expect_balance; ?>'>
	<input type="hidden" name="user_id" value="<?php if(isset($user_id)) echo $user_id; ?>" >
	<input type="submit" value="Сменить баланс" name="B1"></p>
	</form>
<?php } ?>
<hr>
<span style='color:#0000FF;'>Известные Ошибки в истории счетов (таблица money)</span>
<span>
<?php
	$query = "SELECT id,get,give,commission,description FROM `$table_money`";
	$res = mysql_query($query,$msconnect_money);
	$num_error = 0;
	//ошибки в пределах 1 записи
	while($row = mysql_fetch_array($res))
		{
		if($row['give'] != ($row['get'] + $row['commission']))
			{
			//найдена ошибка
			$num_error++;
			echo "<br>$num_error.[give] != [get] + [commission] (таблица $table_money)  в строке: {$row['id']}   -  {$row['description']}";
			}
		}
   //ошибки балансов + отрицательные балансы
  	$query="SELECT id,balance FROM `$table_users`";
    $res = mysql_query($query,$msconnect_users) or die(mysql_error());
    while($row = mysql_fetch_array($res))
    	{
    	$expect_balance = get_account_by_user((int)$row['id']); //ожидаемый баланс
    	if($row['balance'] < 0 )
    		{
    		$num_error++;
	    	echo "<br>$num_error.Баланс пользователя: ".get_user_link($row['id'])." <span style='color:red;'>ОТРИЦАТЕЛЕН</span> (таблица $table_users) - баланс = {$row['balance']}( ожидаемый баланс = $expect_balance). Если он не вернет деньги, мы в убытке (или даже в долгах).";
    		}
	if($row['balance'] > $expect_balance)
	    	{
	  	$num_error++;
	    	echo "<br>$num_error.Баланс пользователя: ".get_user_link($row['id'])." <span style='color:red;'>ЗАВЫШЕН</span> (таблица $table_users) - разница балансов (Ожидаемый - Текущий): ".($expect_balance - $row['balance']);
	     	}
	else if($row['balance'] < $expect_balance)
	    	{
	  	$num_error++;
	    	echo "<br>$num_error.Баланс пользователя: ".get_user_link($row['id'])." Занижен (таблица $table_users) - разница балансов (Ожидаемый - Текущий): ".($expect_balance - $row['balance']);
        	}
        }
	echo "<br><br><br><span>Найдено $num_error ошибок(а)</span>";

?>
</span>