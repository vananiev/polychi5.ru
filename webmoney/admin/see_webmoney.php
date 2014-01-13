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
	if( !check_right('WMN_SEE_MON_IN',R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<div align="center">
	<form method="POST" action="<?php echo url(NULL, 'WEBMONEY', 'admin/see_webmoney');?>">
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
	<table class="webmoney_table styled_table" width="100%">
		<tr class="table_header">
			<td align="center"><b>Номер</b></td>
			<td align="center"><b>Кошелек продав</b></td>
			<td align="center"><b>Сумма</b></td>
			<td align="center"><b>Тестовый платеж</b></td>
			<td align="center"><b>Номер счета</b></td>
			<td align="center"><b>Номер платежа</b></td>
			<td align="center" width=150px><b>Дата</b></td>
			<td align="center"><b>Покупатель</b></td>
			<td align="center"><b>Пок: Кошелек</b></td>
			<td align="center"><b>Пок: Тел</b></td>
			<td align="center"><b>Пок: WMID</b></td>
			<td align="center"><b>Статус</b></td>
		</tr>
		<?php
		//формируем запрос в историю счетов
		$on_page =20;	//число сток в таблице
        $query = "SELECT count(id) FROM `%s` ";
		$vars = array($table_pay);
        if(isset($user_id))
        	{$query .= " WHERE id_pokypatelya = '%d' "; $vars[] = $user_id;}
       	$res = $webmoney->db->query($query,$vars) or die($webmoney->db->error());
       	$row = $res->fetch_assoc();
       	$max = $row['count(id)'];
		if(empty($URL['PAGE']))
			$p=1;
		else
			$p=(int)$URL['PAGE'];
		$from = ($p-1)*$on_page;
		$query = "SELECT * FROM `%s`  ";
		$vars = array($table_pay);
		if(isset($user_id))
        	{$query .= " WHERE id_pokypatelya = '%d' "; $vars[] = $user_id;}
        $query .= " ORDER BY id DESC LIMIT $from,$on_page ";
		$res = $webmoney->db->query($query,$vars) or die($webmoney->db->error());
		$cnt=$from+1;
		while($row = $res->fetch_assoc())
				{
				?>
				<tr>
					<td align="center"><?php echo (int)$row['id']; ?></td>
					<td align="center"><?php echo htmlspecialchars($row['Koshelek_prodavcha'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo 0+$row['Symma']; ?></td>
					<td align="center"><?php if($row['testovii_platezh']=='1') echo "Да"; 
								else if($row['testovii_platezh']=='0') echo "Нет";
								else echo htmlspecialchars($row['testovii_platezh'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['nomer_scheta_vm'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['nomer_platezha_vm'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo date(DATE_TIME_FORMAT,strtotime($row['data_platezha']));?></td>
					<td align="center"><?php if($row['id_pokypatelya']!=NULL)echo get_user_link($row['id_pokypatelya']); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['Koshelek_pokypatelya'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['fone'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['wmid_pokypatelya'],ENT_QUOTES); ?></td>
					<td align="center"><?php echo htmlspecialchars($row['status'],ENT_QUOTES); ?></td>
				</tr>
				<?php
				$cnt++;
				} ?>
	</table>
	<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
		get_table_nav('WEBMONEY', 'admin/see_webmoney', NULL, $max, $on_page); 
	?>
</div>
<style type="text/css">
	.webmoney_table td {border: 1px solid grey;}
</style>