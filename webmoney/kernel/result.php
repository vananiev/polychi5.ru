<?php /*
	Форма автоматически принимает параметры платежа в ходе его выполнения.
	Должен выдавать YES для продолжения платежа.
	Это необходимо для Контрольная подпись данных о платеже.
*/
	//отображаем ошибки
	ini_set("display_errors","0");
	ini_set("display_startup_errors","0");
	ini_set('error_reporting', E_NONE);
?>
<?php
	require_once(SCRIPT_ROOT."/users/kernel/DB.php");
	require_once(SCRIPT_ROOT."/webmoney/kernel/DB_pay.php");
	header("http/1.0 200 Ok");
	header("Content-Type: text/html;charset=windows-1251");
	
	//это предварительный запрос
	if(isset($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST']==1)
		{
			//тестовый платеж
			if(!isset($_POST['LMI_MODE']) || $_POST['LMI_MODE']==1)
				{
				echo "Предварительный запрос: Тестовые платежи не разрешены.";
				exit;
				}
			//запись данных о платеже в БД
			
			$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];

			//проверка верности платежа
				$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
				$row = mysql_fetch_array($r);
				if ( mysql_num_rows($r) == 1 )
					{
					if($row['Koshelek_prodavcha']!=$_POST['LMI_PAYEE_PURSE'] || 
						$row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT'])
						{	
						echo "Предварительный запрос: Неверны кошелек или сумма.";
						exit;
						}
					/* больше не сохраняем номер телефона у себя (проверка номера телефона, если оплата через webMoneyCheck)
					if(isset($_POST['LMI_WMCHECK_NUMBER']) && $row['fone']!=$_POST['LMI_WMCHECK_NUMBER'])
						{	
						echo "Предварительный запрос: Неверен номер телефона.";
						exit;
						}*/
					//проверка id покупателя
					if(!isset($_POST['user_id']) || $row['id_pokypatelya']!=$_POST['user_id'])
						{	
						echo "Предварительный запрос: Неверен номер покупателя.";
						exit;
						}		
					}
				else
					{
					echo "Предварительный запрос: В БД не найдена запись о платеже.";
					exit;
					}
			$LMI_MODE = mysql_real_escape_string($_POST['LMI_MODE'],$msconnect_pay);
			$LMI_PAYER_PURSE = mysql_real_escape_string($_POST['LMI_PAYER_PURSE'],$msconnect_pay);
			$LMI_PAYER_WM = mysql_real_escape_string($_POST['LMI_PAYER_WM'],$msconnect_pay);
			mysql_query("UPDATE `$table_pay`
				SET testovii_platezh='{$LMI_MODE}',
				Koshelek_pokypatelya='{$LMI_PAYER_PURSE}',
				wmid_pokypatelya='{$LMI_PAYER_WM}',
				nomer_scheta_vm='-',
				nomer_platezha_vm='-',
				status='NOFINISH'
				WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
				or die("Предварительный запрос: Не удалось записать информацию о платеже в базу данных.".mysql_error());

		//продолжение платежа. Выводим разрешение
		echo "YES";
		exit(0);
		}
	//это оповещения о платеже
	else if(isset($_POST['LMI_SYS_INVS_NO']))
		{
			//тестовый платеж
			if(!isset($_POST['LMI_MODE']) || $_POST['LMI_MODE']==1)
				{
				echo "Предварительный запрос: Тестовые платежи не разрешены.";
				exit;
				}
			//запись данных о платеже в БД
			
			$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];

			//проверка верности платежа
				$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
				$row = mysql_fetch_array($r);
				if ( mysql_num_rows($r) == 1 )
					{
					if($row['Koshelek_prodavcha']!=$_POST['LMI_PAYEE_PURSE'] ||
						$row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT'] ||
						$row['testovii_platezh']!=$_POST['LMI_MODE'] ||
						$row['Koshelek_pokypatelya']!=$_POST['LMI_PAYER_PURSE'] ||
						$row['wmid_pokypatelya']!=$_POST['LMI_PAYER_WM'])
						{echo "Оповещение о платеже: Неверны кошелек, сумма, флаг тестового запроса, кошелек покупателя, телефон или wmid покупателя";
						exit;}
					/* больше не сохраняем номер телефона у себя (проверка номера телефона, если оплата через webMoneyCheck)
					if(isset($_POST['LMI_WMCHECK_NUMBER']) && $row['fone']!=$_POST['LMI_WMCHECK_NUMBER'])
						{	
						echo "Оповещение о платеже: Неверен номер телефона.";
						exit;
						}*/
					//проверка id покупателя
					if(!isset($_POST['user_id']) || $row['id_pokypatelya']!=$_POST['user_id'])
						{	
						echo "Оповещение о платеже: Неверен номер покупателя.";
						exit;
						}
					}else
					{echo "Оповещение о платеже: В БД не найдена запись о платеже.";
					exit;}
			$LMI_SYS_INVS_NO = mysql_real_escape_string($_POST['LMI_SYS_INVS_NO'],$msconnect_pay);
			$LMI_SYS_TRANS_NO = mysql_real_escape_string($_POST['LMI_SYS_TRANS_NO'],$msconnect_pay);
			$LMI_SYS_TRANS_DATE = mysql_real_escape_string($_POST['LMI_SYS_TRANS_DATE'],$msconnect_pay);
			mysql_query("UPDATE `$table_pay`
				SET nomer_scheta_vm='{$LMI_SYS_INVS_NO}',
				nomer_platezha_vm='{$LMI_SYS_TRANS_NO}',
				data_platezha='{$LMI_SYS_TRANS_DATE}'
				WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
				or die("Оповещение о платеже: Не удалось записать информацию о платеже в базу данных.".mysql_error());

		//проверка платежа
		if( (float)$row['Symma']==(float)$_POST['LMI_PAYMENT_AMOUNT'] )	// $_POST['LMI_PAYMENT_AMOUNT']  возвращает с дробной частью 1.00, a $row['Symma'] - это целое
			{												// при вычислении хеша используем $_POST['LMI_PAYMENT_AMOUNT'], при условии, что он равен $row['Symma']
			$str =  KOSHELEK.$_POST['LMI_PAYMENT_AMOUNT'].$Nomer_paltezha.$_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].
					Secret_Key.$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'];
			$Hash =strtoupper(hash('sha256',$str));
			//$r=$str.":".$Hash.":".$_POST['LMI_HASH'];
			}
		else
			$Hash = NULL;		// $row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT']
		if($Hash === $_POST['LMI_HASH'])
			$status="OK";
			else
			$status="ERROR";
		mysql_query("UPDATE `$table_pay` SET status='{$status}' WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
			or die("Оповещение о платеже: Не удалось записать информацию о статусе платежа.".mysql_error());
		
		//пополнение счета
		$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];
		$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
		$row = mysql_fetch_array($r);
		if(isset($_POST['user_id']) && $row['status']=='OK' && $_POST['LMI_MODE']==0)
			{
			require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
			$perev = (int)$row['Symma'];
			$money_row_id = add_record(
				WEBMONEY_IN,				 //от вебмани
				(int)$_POST['user_id'],      //пользователю
				(int)$perev,         	 //заплатил
				(int)$perev,         	 //перевелось
				"Webmoney payment #{$row['id']}",
				-1,        //добаляем новую запись
				0);        //комиссия
			if($money_row_id ==0)
				{
				show_msg(NULL,"Ошибка обновления таблицы money. Оплата webmoney №{$row['id']}. Обратитесь к администратору с этим сообщением",MSG_CRITICAL,MSG_NOBACK);
				}
			//увеличиваем баланс
			//БД пользователи
			$add = (int)$row['Symma'];
			$user_id = (int)$_POST['user_id'];
			$query="UPDATE `$table_users` SET balance = `balance` + '{$add}' WHERE id = '{$user_id}'";
			mysql_query($query,$msconnect_users);
			}
		else
			{
			//оплату произвел неизвестный покупатель или не верен LMI_HASH или тестовый платеж
			exit;
			}

		}
	else
		{
		//запрос злоумышленников
		echo "Ошибка. Доступ запрещен.";
		} ?>