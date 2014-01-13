<?php
		/********************************************************************

		Администратор выводит деньги из системы. Получение списка желающих

		*******************************************************************
		POST/GET:
		user_id -  вывод какого пользователя смотрим
		*/
		if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) $user_id = (int)$_POST['user_id'];
		if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) $user_id = (int)$_GET['user_id'];

	if( !check_right('MNY_OUT', R_MSG)) return;  //проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	// получаем данные
    $query="SELECT * FROM `$table_users` WHERE money_out_query != 0";
    $res = mysql_query($query,$msconnect_users) or die("Ошибка чтения таблицы users<br>.".mysql_error());
    if(mysql_num_rows($res)==0)
    	echo "Желающих вывести деньги из системы нет";
	else
		echo "<h2>Ожидают оплаты</h2>";
    while($row = mysql_fetch_array($res))
    	{
		echo url($row['id'], 'MONEY', 'admin/money_pay_by_administrator_about_user', 'user_id='.$row['id']);
		echo ": {$row['login']}   -   {$row['money_out_query']} руб.<br>";
    	}
?>
<p>&nbsp;</p>
<?php
//Вывод истории счета
require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
?>
<h2>История счетов</h2>
<div align="center">
	<form method="POST" action="<?php echo url(NULL, 'MONEY', 'admin/money_pay_by_administrator');?>">
		<table width="562" id="table1" style="border-width: 0px">
			<tr>
				<td style="border-style: none; border-width: medium" align="right">
				Пользователь:</td>
				<td style="border-style: none; border-width: medium" align="left">
				<input type="text" name="user_id" size='5' value='<?php if(isset($user_id)) echo $user_id; ?>'></td>
			</tr>
		</table>
		<p align='center'><input type="submit" value="Просмотреть" name="B1"></p>
	</form>

	<div align="center">
		<table class="history styled_table" width=100% id="table1">
			<tr class="table_header">
				<td align="center"><b>Номер</b></td>
				<td align="center"><b>От</b></td>
				<td align="center"><b>Кому</b></td>
				<td align="center"><b>Отдано</b></td>
				<td align="center"><b>Получено</b></td>
				<td align="center"><b>Комиссия</b></td>
				<td align="center" width=150px><b>Дата</b></td>
				<td align="center"><b>Информация</b></td>
			</tr>
			<?php
			//формируем запрос в историю счетов
			$on_page =30;	//число сток в таблице
			$query = "SELECT count(*) FROM `$table_money` WHERE komy = ".WEBMONEY_OUT;
			if(isset($user_id))
				$query .= " AND ot = '{$user_id}' ";
			$res = mysql_query($query,$msconnect_money) or die(mysql_error());
			$row = mysql_fetch_array($res);
			$max = $row['count(*)'];
			if(empty($URL['PAGE']))
				$p=1;
			else
				$p=$URL['PAGE'];
			$from = ($p-1)*$on_page;
			$query = "SELECT * FROM `$table_money` WHERE ( komy = ".WEBMONEY_OUT." or komy = ".YANDEX_MONEY_OUT." or komy = ".ROBOKASSA_OUT." )";
			if(isset($user_id))
					$query .= " AND ot = '{$user_id}' ";
				$query .= " ORDER BY id DESC LIMIT $from,$on_page ";
			$res = mysql_query($query,$msconnect_money) or die(mysql_error());
			$cnt=$from+1;
			while($row = mysql_fetch_array($res))
					{
					?>
					<tr class='tr_ticket_other'>
						<td align="center"><?php echo $row['id']; ?></td>
						<td align="center"><?php
							if($row['ot']==SYS_MONEY)
								echo "Система";
							else if($row['ot']==WEBMONEY_IN)
								echo "Вебмани";
							else if($row['ot']>=0)
								echo get_user_link($row['ot']);
							else
								echo $row['ot'];
						?></td>
						<td align="center"><?php
							if($row['komy']==SYS_MONEY)
								echo "Система";
							else if($row['komy']==WEBMONEY_OUT)
								echo "Вебмани";
							else if($row['komy']==YANDEX_MONEY_OUT)
								echo "Яндекс";
							else if($row['komy']==ROBOKASSA_OUT)
								echo "Робокасса";
							else if($row['komy']>=0)
								echo get_user_link($row['komy']);
							else
								echo $row['komy'];
						?></td>
						<td align="center"><?php echo $row['give']; ?></td>
						<td align="center"><?php echo $row['get']; ?></td>
						<td align="center"><?php echo $row['commission']; ?></td>
						<td align="center">
							<?php echo date(DATE_TIME_FORMAT, strtotime($row['date']));?>
						</td>
						<td align="center">
							<?php echo $row['description'];?>
						</td>
					</tr>
					<?php
					$cnt++;
					} ?>
		</table>
		
		<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
		$args = NULL;
		if(isset($user_id)) $args='user_id='.$user_id;
		get_table_nav('MONEY', 'admin/money_pay_by_administrator', $args, $max, $on_page); 
		?>
	</div>
</div>
<style type="text/css">
	.history td {border: 1px solid grey;}
</style>
