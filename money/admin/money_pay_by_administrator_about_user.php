<?php
		/********************************************************************
						Администратор выводит деньги из системы. Подтверждение

		Параметры GET:
		user_id  - номер пользователя, которому производится оплата
		********************************************************************/
		if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) $user_id = (int)$_GET['user_id'];
		
	if( !check_right('MNY_OUT', R_MSG)) return;  //проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
    require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
	if(isset($user_id))
		{
        // получаем данные
		$user_id = (int)mysql_real_escape_string($user_id, $msconnect_users);
        $query="SELECT * FROM `$table_users` WHERE id = '{$user_id}'";
	    $res = mysql_query($query,$msconnect_users) or die(mysql_error());
        $row = mysql_fetch_array($res);
        $Symma = $row['money_out_query'];
        if($Symma == 0)
        	{
        	show_msg(NULL, "Пользователь не желает выводить средства.");
        	return;
        	}
        else
        	{
			//подтверждение
			?>
			<form method="POST" action="<?php echo url(NULL, 'MONEY', 'admin/money_pay_by_administrator_act');?>">
				<p align='center'>Оплатить пользователю: <?php echo $user_id;?> </p>
				<p align='center'>Сумма оплаты: <?php echo $Symma;?></p>
				<p align='center'>
				<?php
				//проверка истории циркуляции денег по пользователю(мог ли пользователь столько заработать?)
                $expect_balance = get_account_by_user((int)$user_id); //ожидаемый баланс
                echo "Ожидаемый баланс: ".$expect_balance."<br>";
                echo "Текущий баланс: ".$row['balance']."<br>";
                echo "Разница балансов(Ожидаемый - Текущий): ".($expect_balance - $row['balance'])."<br><br>";
                if($row['balance'] == $expect_balance)
                	echo "<span style='color:green;'>Баланс пользователя верен.</font>";
                else if($row['balance'] > $expect_balance)
                	echo "<span style='color:red;'>Баланс пользователя ЗАВЫШЕН судя по истории его заработка!!!</font>";
                else
                	echo "<span style='color:#0000FF;'>Баланс пользователя меньше ожидаемого судя по истории заработка</font>";
				?>
				</p>
				<input type="hidden" name="user_id" value="<?php echo $user_id;?>">
				<p style='text-align:center;'>
				   Оплатить через:<br>
				   <input type="radio" name="payment_system" value="<?php echo WEBMONEY_OUT;?>" > Вебмани<br>
				   <input type="radio" name="payment_system" value="<?php echo YANDEX_MONEY_OUT;?>"> Яндекс<br>
				   <input type="radio" name="payment_system" value="<?php echo ROBOKASSA_OUT;?>"> Робокасса<br>
				   <input type="submit" value="Подтвердить" name="B1">
				</p>
			</form>
        <?php
        	}
        //вывод информации о пользователе
        //$user_id = $user_id; //подготавливаем передаваемые параметры для вызова скрипта в следуйщей строке
        require(SCRIPT_ROOT."/users/about_user.php");
		}
	else
		show_msg(NULL, "Введите Номер пользователя",MSG_WARNING);
?>