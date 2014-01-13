<?php
	/*************************************************************************
		Оперирование записями циркуляции денег внутри системы
	*************************************************************************
	*/

function add_record($from, $to, $give, $get, $description, $id=-1, $commission=-1)
{
	global $table_money, $msconnect_money;
	if(is_int($id) && is_int($from) && is_int($to) && is_int($give) && is_int($get) && is_string($description) && is_int($commission))
		{
		if($id == -1) //добавление
			{
			if($commission == -1)
				$comm = COMMISSION*$give;
			else
				$comm = $commission;
			$description = mysql_real_escape_string($description, $GLOBALS['msconnect_money']);
			$query = "INSERT INTO `{$GLOBALS['table_money']}`
		            (ot,komy,give,get,commission,date,description)
		            VALUES('{$from}','{$to}','{$give}','{$get}','{$comm}',CURRENT_TIMESTAMP(),'{$description}')";
		    $res = mysql_query($query,$GLOBALS['msconnect_money']);// or die("Критическая ошибка добавления в money. Обратитесь к администратору<br>".mysql_error());
		    return(mysql_insert_id());
		    }
		    else //обновление
		    {
		    $query = "UPDATE `{$GLOBALS['table_money']}` SET ";
            if($commission == -1)
				$comm = ($give - $get);
			else
				$comm = $commission;
			$query.=" commission = '{$comm}' ";

            if($from != -1)
            	  $query.=" ,ot = '{$from}'";
            if($to != -1)
            	 	$query.=" ,komy = '{$to}' ";
            if($give != -1)
            	 	$query.=" ,give = '{$give}' ";
	        if($get != -1)
            	 	$query.=" ,get = '{$get}' ";
	        if($description != "")
            	 	$query.=" ,description = '{$description}' ";

	        $query.=" , date = CURRENT_TIMESTAMP() WHERE id = '$id'";
	        $res = mysql_query($query,$GLOBALS['msconnect_money']);// or die("Критическая ошибка обновления в money. Обратитесь к администратору<br>".mysql_error());
	        if(mysql_affected_rows() != -1)
		    	return($id);   //номер обновленной записи
			else
				return(0); //ошибка
		    }
	    }
	 else
	 	{
	 	return(0); //неверные типы данных
	 	}
}

function del_record($id)
{
	if(is_int($id))
		{
		global $table_money, $msconnect_money;
		$query = "DELETE FROM `$table_money`
						WHERE id = '$id'";
		$res = mysql_query($query,$msconnect_money);// or die("Критическая ошибка добавления в money. Обратитесь к администратору<br>".mysql_error());
		return(mysql_affected_rows());
		}
	else
		return false;
}
//------------------------------------ количество денег на счету пользователя ---------------------------
function get_account_by_user($user_id)
/*$user_id == -1 количество денег на счете системы
 (с учетом комиссии и не выплаченных пользователям средств,
 т.е. количество денег на кошельке системы)
 $user_id == -2  ввод средств с вебмани
 $user_id == -3 вывод средств на кошелек пользователя
 $user_id == -4 комиссии - это мой доход
 $user_id == -5 суммарный баланс всех пользователей

Должно выполняться
[сумма на счету всех пользователей](>=0) + [комиссии[ = [на счету системы](-1)
[на счету системы](-1) + [сумма вывода](-3) = [сумма ввода](-2)    */
{
	global $table_money, $msconnect_money;
	$balance = 0;
    if(is_int($user_id))
    	{
        if($user_id>=0 || $user_id == WEBMONEY_IN || $user_id == WEBMONEY_OUT ||
			 	 $user_id == YANDEX_MONEY_IN || $user_id == YANDEX_MONEY_OUT ||
			 	 $user_id == ROBOKASSA_IN || $user_id == ROBOKASSA_OUT		) //если считаем баланс пользователя или ввод/вывод
        	{
		    //добавляем
		    $query = "SELECT `get` FROM `$table_money`
				            WHERE `komy` = '$user_id'";
		    $res = mysql_query($query,$msconnect_money) or die(mysql_error());
		    while($row = mysql_fetch_array($res))
		    	{
		    	$balance = $balance + $row['get'];
		    	}
		    //вычитаем
		    $query = "SELECT give FROM `$table_money`
				            WHERE ot = '$user_id'";
		    $res = mysql_query($query,$msconnect_money);
		    while($row = mysql_fetch_array($res))
		    	{
		    	$balance = $balance - $row['give'];
		    	}
	        if($user_id == WEBMONEY_IN || $user_id == WEBMONEY_OUT || 
	        	$user_id == YANDEX_MONEY_IN || $user_id == YANDEX_MONEY_OUT ||
	        	$user_id == ROBOKASSA_IN || $user_id == ROBOKASSA_OUT)  //если считали ввод и вывод средств
	        		$balance = abs($balance);
		  	return $balance;
		  	}
		else if ($user_id == SYS_MONEY || $user_id == SYS_DOXOD || $user_id == SOLV_SALARY	)  //если считаем системный доход или комиссии
			{
				global $money;
   				//ожидают выплаты
				if ($user_id == SYS_MONEY || $user_id == SOLV_SALARY){
					$salary = 0;
					//добавляем и вычитаем, то что переводилось с(на) счет системы
					$query = "SELECT get FROM `$table_money`
									WHERE komy = '".SYS_MONEY."'";
					$res = mysql_query($query,$msconnect_money) or die(mysql_error());
					while($row = mysql_fetch_array($res))
						$salary += $row['get'];
					//вычитаем
					$query = "SELECT give FROM `$table_money`
									WHERE ot = '".SYS_MONEY."'";
					$res = mysql_query($query,$msconnect_money);
					while($row = mysql_fetch_array($res))
						$salary -= $row['give'];
					if($user_id == SOLV_SALARY)
						return $salary;
					}
				//подсчет комиссии
   				$com = 0;
       			$res = $money->db->query("SELECT sum(commission) FROM `$table_money`") or die($users->db->error());
			    if($row = $res->fetch_array()) $com = 0+$row['sum(commission)'];
			    $res = $money->db->query("SELECT sum(give) FROM `$table_money` WHERE ot = ".SYS_DOXOD) or die($users->db->error());
			    if($row = $res->fetch_array()) $com -= $row['sum(give)'];
				if($user_id == SYS_DOXOD)  //считали только комиссию
					return $com;
				//если считаем деньги на счете системы
				if($user_id == SYS_MONEY) 
					{
					//подсчет средств всех пользователей
					//2 способ подсчета - текущие балансы
					$balance2=0;
					global $table_users;
	            	$query = "SELECT balance FROM `$table_users`";
				    $res = mysql_query($query,$msconnect_money) or die(mysql_error());
				    while($row = mysql_fetch_array($res))
				    	$balance2 = $balance2 + $row['balance'];
					
					return ($balance2 + $com + $salary);
					}
				return "Ошибка";
			}
		else if($user_id == ALL_USERS_BALANCE)
			{
			global $table_users, $users;
			//1 способ подсчета - история счетов
				$balance1 = 0;
	            //добавляем
			    $res = $users->db->query("SELECT sum(get) FROM `$table_money` WHERE komy >= 0") or die($users->db->error());
			    if($row = $res->fetch_array())
			    	$balance1 = $balance1 + $row['sum(get)'];
			    //вычитаем
			    $res = $users->db->query("SELECT sum(give) FROM `$table_money` WHERE ot >= 0") or die($users->db->error());
			    if($row = $res->fetch_array())
			    	$balance1 = $balance1 - $row['sum(give)'];
			//2 способ подсчета - текущие балансы
				$balance2=0;
				$res = $users->db->query("SELECT sum(balance) FROM `$table_users`") or die($users->db->error());
			    if($row = $res->fetch_array())
			   	{
			    	$balance2 = (int)$row['sum(balance)'];
			    }
			if( $balance1 != $balance2)
				return "Ошибки в истории счетов";
			else
				return $balance2;
			}
		else
			return "Ошибка для такого пользователя нельзя посчитать баланс";
	    }
	else
	    return "Ошибка user_id - не число";
}

//Возврат суммы штрафа по стоимости задания
function get_strav_sum ($price)
{
	return (1+(int)(STRAV*($price*(1-2*COMMISSION))));
}
//Оплачиваемая сумма ученик-система, система-решающий
function sum_after_comm ($price)
{
	return ((int)($price*(1-COMMISSION)));
} 
//Стоимость задания для решающего
function price_after_all_comm ($price)
{
	return ((int)($price*(1-2*COMMISSION)));
}
//Стоимость задания для ученика
function price_before_all_comm ($solver)
{
	return  (1 + (int)($solver/(1-2*COMMISSION)));
} 
?>