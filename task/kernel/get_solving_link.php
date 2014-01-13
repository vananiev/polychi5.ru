<?php
		/*********************************************************************
				Скрипт получения ссылки на файл с решением.
		*********************************************************************
		Параметры передаваемые вызвваемым скриптом:
		id - номер задания
		
		GET:
		id - номер задания
		*/
		if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id'];
		if(!isset($id))
			{
			header("Content-Type: text/html; charset=".SITE_CHARSET);
			show_msg(NULL, 'Не передан номер задания',MSG_WARNING,MSG_RETURN);
			return;
			}
		$id = (int)$id;
?>
<?php
	/*/ссылка уже была скачана?
	if(isset($_SESSION['getlink_'.$id]))
		{
		$rel = url('[Продолжить]', 'TASK', 'get_solving', 'id='.$id);
		show_msg("Система защиты баланса","Ссылка уже была получена вами РАНЕЕ.<br>
				Если вам нужно ВНОВЬ получить ссылку ЭТОГО ЖЕ задания, нажмите продолжить.<br>
				".$rel,MSG_INFO,MSG_RETURN);
		//ученик согласен повторно скачать решение
    		unset($_SESSION['getlink_'.$id]);
		return;
		}*/
	//откуда зашли
	//если случайно забрели на страницу в избежание недоразумений снятия средств
	//останавливаем скрипт
	/*if(!isset($_SESSION['from']) || $_SESSION['from']!="get_solving")
		{
		header("Content-Type: text/html; charset=".SITE_CHARSET);
		$rel = url('[Продолжить]', 'TASK', 'get_solving', 'id='.$id);
		show_msg("Система защиты баланса","Пожалуйста подтвердите Ваше решение на скачивание файла. Нажмите продолжить.<br>
				".$rel ,MSG_CRITICAL,MSG_RETURN);
		return;
		}*/
	$_SESSION['from'] = '';
?>
<?php
	global $table_users, $msconnect_users, $table_task, $msconnect_task ;
	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
	//может ли быть у пользователя столько денег
	if(isset($_SESSION['user_id'])){
		$query="SELECT balance FROM `$table_users` WHERE id = '{$_SESSION['user_id']}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
		if(mysql_num_rows($res)==0)
			{
			header("Content-Type: text/html; charset=".SITE_CHARSET);
			show_msg("Ошибка","Такой пользователь не существует",MSG_CRITICAL);
			return;
			}
		else
			{
			//проверка истории циркуляции денег по пользователю(мог ли пользователь столько заработать?)
			 $expect_balance = get_account_by_user((int)$_SESSION['user_id']); //ожидаемый баланс
			 if((int)$expect_balance < (int)$row['balance'])
				{
				header("Content-Type: text/html; charset=".SITE_CHARSET);
				show_msg("Ошибка","Зафиксировано нарушение!<br>Обратитесь к администратору.<br>
					Ожидаемый баланс: {$expect_balance}<br>
					Текущий баланс: {$row['balance']}<br>",MSG_CRITICAL);
					return;
				}
			}
		}
?>
<?php
	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");

    //узнаем цену задания
	if(isset($id))
		{
		$query="SELECT *
		           FROM `$table_task`
		           WHERE id = '{$id}'";
		$res = mysql_query($query,$msconnect_task) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
		if
			(
			$row['status']=='NEW' ||
			$row['status']=='GETS' ||
			$row['status']=='WAIT'
			)
			{
			header("Content-Type: text/html; charset=".SITE_CHARSET);
			show_msg("Информация","Решение по заданию №{$id} еще не готово");
			return;
			}
		else if(mysql_num_rows($res)==0)
			{
			header("Content-Type: text/html; charset=".SITE_CHARSET);
			show_msg(NULL,"В системе не существует задания №{$id}",MSG_INFO,MSG_RETURN);
			return;
			}
        	}
  	else
		{
		header("Content-Type: text/html; charset=".SITE_CHARSET);
		show_msg("Ошибка","Не указано задание. Попытайтесь еще раз.",MSG_WARNING);
		return;
		}
	//получаем ссылку на решение
	//$link = get_solving_link($id);

	//любой получают оплаченную задачу
	if($row['user_pay']==1){
		//ссылка получена
		$_SESSION['getlink_'.$id] = 'ok';
		get_solv($id);
		return;
	 	}

	//узнаем баланс
	if(isset($_SESSION['user_id']))
		{
		$query="SELECT balance
            FROM `$table_users`
            WHERE id = '{$_SESSION['user_id']}'";
		$res2 = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row2 = mysql_fetch_assoc($res2);
		}
	else
		{
		header("Content-Type: text/html; charset=".SITE_CHARSET);
		show_msg("Ошибка","Необходим вход в систему.",MSG_CRITICAL);
		return;
		}
	//получаем решение
	if($row2['balance'] >= $row['price'] ||
		$row['solver'] == $_SESSION['user_id'] ||
		($row['user'] == $_SESSION['user_id'] && $row['user_pay']==1) ||	//на всякий случай
		check_right('TSK_RATING_OTHERS') ||
		check_admin_access()
		)
		{
		//администратор
     	if(isset($_SESSION['user_id']) && $_SESSION['user_id']==0)
     		{
     		//проверка прав администратора
			if(!check_admin_access()) return;
			//ссылка получена
    		$_SESSION['getlink_'.$id] = 'ok';
			get_solv($id);
			return;
			}

		// тот кто оценивает может скачать решение без платы
		if(check_right('TSK_RATING_OTHERS'))
		    {
			//ссылка получена
			$_SESSION['getlink_'.$id] = 'ok';
			get_solv($id);
			return;
            }

        //решающий скачивает свое решение
     	if($row['solver'] == $_SESSION['user_id'])
     		{
     		//проверка прав пользователя
			if(!user_in_group('SOLVER',R_MSG)) return;
				//ссылка получена
				$_SESSION['getlink_'.$id] = 'ok';
				get_solv($id);
				return;
            }

    	//ученик получает перерешенную задачу
     	if($row['user_pay']==1 && $row['user'] == $_SESSION['user_id'])
     		{
     		//проверка прав пользователя
			if(!user_in_group('USER',R_MSG)) return;
                //ссылка получена
    			$_SESSION['getlink_'.$id] = 'ok';
				get_solv($id);
	            return;
	        }

			{//снимаем дениги с счета ученика
			if(isset($_SESSION['user_id']))
				{
	            //снимаем деньги
		        $query="UPDATE `$table_users`
			    				SET balance = `balance` - '{$row['price']}'
			            		WHERE id = '{$_SESSION['user_id']}'";
				$res = mysql_query($query,$msconnect_users) or die("Ошибка при оплате. Попытайтесь еще раз.<br>".mysql_error());
				if(mysql_affected_rows() != -1)
					{
					$from = (int)$_SESSION['user_id'];
					$to = SYS_MONEY;      //системе
					$give = (int)$row['price'];
					$get = sum_after_comm((int)$row['price']);
					$description = "Получение решения №{$id}.";
					$row_id = -1;    //добавляем новую запись
					$commision = $give - $get;
					$oplata = "<span style='color:red;'>Решение оплачено.</span>";
					}
				else
					{
					header("Content-Type: text/html; charset=".SITE_CHARSET);
					show_msg("Ошибка","Ошибка при оплате. Попытайтесь еще раз.",MSG_CRITICAL);
					return;
					}
				//указываем что оплатил (действительно только для владельца задания)
				if($row['user'] == $_SESSION['user_id'])
					{
					$query="UPDATE `$table_task`
						SET user_pay = 1
		           		WHERE id = '{$id}'";
		     		$res = mysql_query($query,$msconnect_task);
		     		if(mysql_affected_rows() == -1)
		     			{
						/*show_msg("Ваше решение задания №{$id}",$oplata."<br>".$link."<br>",MSG_INFO,MSG_RETURN); //вывод ссылки к решению
			            //ссылка получена
			    		$_SESSION['getlink_'.$id] = 'ok';
						$description = $description . "Получение ссылки: OK";
						$money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
						*/
						header("Content-Type: text/html; charset=".SITE_CHARSET);
						show_msg(NULL,"Ошибка при указании факта оплаты при получении ссылки решения задания №{$id} дополнительно в money №$money_row_id. Обратитесь к администратору с этим сообщением.",MSG_CRITICAL,MSG_NO_BACK);
						return;
		     			}
		  			}
				//show_msg('Решение готово', "Решение задания №{$id}<br>".$link."<br>",MSG_INFO,MSG_RETURN); //вывод ссылки к решению
		        //обновляем информацию в money
		        $description = $description . "(Получение ссылки: OK)";
		        $money_row_id = add_record((int)$from,(int)$to,(int)$give,(int)$get,$description,(int)$row_id,(int)$commision);  //добавляем
				// нельзя выдавать эту(ниже) ошибку, так как будет отдан битый файл
				//if($money_row_id==0){
				//	header("Content-Type: text/html; charset=".SITE_CHARSET);
				//	show_msg(NULL,"Операция проведена успешно. Ошибка записи об оплате в таблице money, получение ссылки решения задания №{$id}. Обратитесь к администратору с данным сообщением",MSG_CRITICAL,MSG_NO_BACK);
				//	}
				//ссылка получена
		    	$_SESSION['getlink_'.$id] = 'ok';
				get_solv($id);
				unset($id);
				return;
				}
			else
				{
				header("Content-Type: text/html; charset=".SITE_CHARSET);
				show_msg("Ошибка","Чтобы скачать решение войдите в систему",MSG_CRITICAL);
				return;
				}
			} //END:снимаем дениги с счета ученика
		}
	else
		{
		header("Content-Type: text/html; charset=".SITE_CHARSET);
		$link = url('здесь', 'TASK', 'get_solving');
		show_msg("Внимание","У вас не достаточно средств на счету. Пополните баланс ".$link, MSG_WARNING);;
		//require_once(SCRIPT_ROOT."/task/get_solving.php");
		return;
		}
?>