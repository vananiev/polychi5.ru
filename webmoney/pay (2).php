<?php /*
	Скрипт создания заявки на платеж. Вызывается по нажатию пользователем по кнопке отплатить.
	Передаваемые параметры.
	Метод - POST
	Koshelek	-номер кошелька продавца
	Symma		-сумма оплачиваемых денег
	fone_num	-при указании, оплата будет производиться через терминал
	fone_pass	-при указании, оплата будет производиться через терминал
	Naznachenie	-описание платежа
	id		-если получаем решение, то номер решения

	$_SESSION['user_id']	-номер пользователя в системе,который производит оплату
	*/
	if(isset($_POST['Symma']) && is_numeric($_POST['Symma'])) $_POST['Symma'] = 0+$_POST['Symma'];
?>
<?php
	if(!isset($_SESSION['user_id']))
		{
		show_msg(NULL,"Не найден номер Вашего электронного счета. Пожалуйста, войдите в систему.",MSG_WARNING);
		return;
		}
?>
<?php
	//проверяем заполненность полей
	$webMoneyCheck = 0;
	if($_POST['Koshelek']=="" && ($_POST['Symma']==0 || $_POST['Symma']==""))
		{show_msg(NULL, "Не выбран кошелек продавца или сумма оплаты",MSG_CRITICAL);
		return;}
	if(isset($_POST['Symma']) && !ereg("^[0-9]+$",$_POST['Symma']))
		{show_msg(NULL, "Не верно указана сумма.<br>Сумма должна быть целым числом.",MSG_WARNING);
		return;}
	if(isset($_POST['fone_num']) &&  isset($_POST['fone_pass']))
		{
		echo "123123123213";
		//способ оплаты webMoneyCheck
		$webMoneyCheck=1;
		if(!ereg("^[0-9]{11}$",$_POST['fone_num']) || !ereg("^[0-9]{4}$",$_POST['fone_pass']))
			{
			show_msg(NULL,"<span style='color:red;'>Не верно введен номер телефона (пример:79003332211)<br>или пароль (4 числа).</span><br>Пожалуйста попробуйте еще раз<br>",MSG_WARNING,MSG_RETURN);
			return;
			}
		}

	//записываем информацию о платеже
	$time_for_DB=date('Ymd H:i:s',time());
	$query = "INSERT INTO `%s` 
		(Koshelek_prodavcha,
		Symma,
		Koshelek_pokypatelya,
		wmid_pokypatelya,
		nomer_scheta_vm,
		nomer_platezha_vm,
		id_pokypatelya,
		data_platezha,
		status";
	$vars = array($table_pay);
	if($webMoneyCheck==1)
		$query .= ",fone";
	$query .= ") 
		VALUES('%s',
		'%d',
		'-','-','-','-','%d','%s','NOACCEPT'";
	$vars[] = $_POST['Koshelek'];
	$vars[] = (0+$_POST['Symma']);
	$vars[] = $_SESSION['user_id'];
	$vars[] = $time_for_DB;
if($webMoneyCheck==1)
		{$query .= ",'%s'"; $vars[] = $_POST['fone_num'];}
	$query .= ")";
	$webmoney->db->query($query, $vars) or die("Ошибка. Попробуйте еще раз.<br>".$webmoney->db->error()." <a href='javascript:history.go(-1)'>[Назад]</a>");
	$Nomer_paltezha = $webmoney->db->insert_id;	//номер платежа в системе учета продавца
?>
<form id=pay name=pay method='POST' action='https://merchant.webmoney.ru/lmi/payment.asp<?php echo ($webMoneyCheck == 1)?"?at=authtype_13":"?at=authtype_8"; ?>'>
	<input type='hidden' name='LMI_PAYEE_PURSE' value="<?php echo htmlspecialchars($_POST['Koshelek'],ENT_QUOTES,'cp1251'); ?>">
	<input type='hidden' name='LMI_PAYMENT_AMOUNT' value="<?php echo htmlspecialchars($_POST['Symma'],ENT_QUOTES,'cp1251'); ?>">
	<input type='hidden' name='LMI_PAYMENT_NO' value="<?php echo $Nomer_paltezha; ?>">
	<input type='hidden' name='LMI_PAYMENT_DESC_BASE64' value="<?php echo base64_encode(htmlspecialchars($_POST['Naznachenie'],ENT_QUOTES,'cp1251')); ?>">
	<input type='hidden' name='LMI_SIM_MODE' value="<?php echo LMI_SIM_MODE?>">
	<?php if($webMoneyCheck == 1){ ?>
		<input type='hidden' name='lmi_wmcheck_numberinside' value="<?php echo htmlspecialchars($_POST['fone_num'],ENT_QUOTES,'cp1251'); ?>">
		<input type='hidden' name='lmi_wmcheck_codeinside' value="<?php echo htmlspecialchars($_POST['fone_pass'],ENT_QUOTES,'cp1251'); ?>">
	<?php } ?>
	<?php if(isset($_SESSION['user_id'])) { ?>
		<input type='hidden' name='user_id' value="<? echo ((int)$_SESSION['user_id']); ?>'">
	<?php } ?>
	<?php if(isset($_POST['id'])) { ?>
		<input type='hidden' name='task_id' value="<?php echo ((int)$_POST['id']); ?>">
	<?php } ?>
</form>
<script type="text/javascript">
	$('#pay').submit();
</script>
