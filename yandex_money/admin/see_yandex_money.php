<?php
		/********************************************************************

				Просмотр таблицы webmoney

		********************************************************************
		POST:
		user_id -  с каким пользователем оперируем
*/
		if(isset($_POST['user_id']) && is_numeric($_POST['user_id'])) $user_id = (int)$_POST['user_id'];
?>
<?php
	if( !check_right('YM_SEE_MON_IN',R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<div align="center">
	<form method="POST" action="<?php echo url(NULL);?>">
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
</div>

<div align="center">
	<table class="yandex_money_table styled_table" width="100%">
		<tr class="table_header">
			<th align="center">Номер</th>
			<th align="center">Покупатель</th>
			<th align="center" width=150px>Дата</th>
			<th align="center">Пок: Кошелек</th>
			<th align="center">Прод: Кошелек</th>
			<th align="center">Сумма</th>
			<th align="center">Номер счета</th>
			<th align="center">Статус</th>
		</tr>
		<?php
		//формируем запрос в историю счетов
		$on_page =20;	//число сток в таблице
        $query = "SELECT count(id) FROM `%s` ";
		$vars = array($table_ym_pay);
        if(isset($user_id))
        	{$query .= " WHERE id_pokypatelya = '%d' "; $vars[] = $user_id;}
       	$res = $yandex_money->db->query($query,$vars) or die($yandex_money->db->error());
       	$row = $res->fetch_assoc();
       	$max = $row['count(id)'];
		if(empty($URL['PAGE']))
			$p=1;
		else
			$p=(int)$URL['PAGE'];
		$from = ($p-1)*$on_page;
		$query = "SELECT * FROM `%s`  ";
		$vars = array($table_ym_pay);
		if(isset($user_id))
        	{$query .= " WHERE id_pokypatelya = '%d' "; $vars[] = $user_id;}
        $query .= " ORDER BY id DESC LIMIT $from,$on_page ";
		$res = $yandex_money->db->query($query,$vars) or die($yandex_money->db->error());
		$cnt=$from+1;
		while($row = $res->fetch_assoc())
				{
				?>
				<tr>
					<td align="center"><?php echo (int)$row['id']; ?></td>
					<td align="center"><?php if($row['id_pokypatelya']!=NULL)echo get_user_link($row['id_pokypatelya']); ?></td>
					<td align="center"><?php echo date(DATE_TIME_FORMAT,strtotime($row['pay_date']));?></td>
					<td align="center"><?php echo htmlspecialchars($row['ot'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['komy'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo 0+$row['symma']; ?></td>
					<td align="center"><?php echo htmlspecialchars($row['paymant_id'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['status'],ENT_QUOTES); ?></td>
				</tr>
				<?php
				$cnt++;
				} ?>
	</table>
	<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
		get_table_nav('YANDEX_MONEY', 'admin/see_yandex_money', NULL, $max, $on_page); 
	?>
</div>
<style type="text/css">
	.yandex_money_table td {border: 1px solid grey;}
</style>
