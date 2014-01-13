<?php /*
	Скрипт создания заявки на платеж. Вызывается по нажатию пользователем по кнопке отплатить.
	Передаваемые параметры.
	Метод - POST
	Symma		-сумма оплачиваемых денег
	Naznachenie	-описание платежа
	id		    -если получаем решение, то номер решения
	paymentType	- PC: c яндекс денег, AC: c банковской карты

	$_SESSION['user_id']	-номер пользователя в системе,который производит оплату
	*/
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php
	if(!isset($_SESSION['user_id']))
		{
		show_msg(NULL,"Не найден номер Вашего электронного счета. Пожалуйста, войдите в систему.",MSG_WARNING);
		return;
		}
	$paymentType = 'PC';
	if(isset($_POST['paymentType']) and $_POST['paymentType']=='AC')
		$paymentType = 'AC';
?>
<?php
	// редирект яндекса без единого параметра
	if( !isset($_POST['Symma']) ) {
		if(isset($_SESSION['task_id'])){
			$text  = "Платеж успешно выполен ";	
			$text .= "<br>Переидти к получению ";
			$text .= url('решения', 'TASK', 'get_solving', 'id='.$_SESSION['task_id']);
			show_msg(NULL,$text,MSG_INFO,MSG_NO_BACK);	//отображаем сообщение
			?>
			<script lang="text/javascript">location.href='<?php echo url(NULL, 'TASK', 'get_solving', 'id='.$_SESSION['task_id']); ?>';</script>
			<?php 
			unset($_SESSION['task_id']);
		}else { ?>
			<script type="text/javascript">
				location.href='<?php echo url(NULL, 'TASK', 'get_balance'); ?>';
			</script>
<?php 	} 
		return;
} ?>
<?php
	require_once(dirname(__FILE__) . '/kernel/config.php');

	//проверяем заполненность полей
	if(!isset($_POST['Naznachenie']) || !isset($_POST['Symma']) || !preg_match("#^[0-9]+$#",$_POST['Symma']))
		{show_msg(NULL, "Не верно указана назначение платежа или сумма.<br>Сумма должна быть целым числом.",MSG_WARNING);
		return;}

	//записываем информацию о платеже
	$_POST['Naznachenie'] = htmlspecialchars($_POST['Naznachenie'],ENT_QUOTES,'UTF-8');
	$_SESSION['YM_Naznachenie'] = $_POST['Naznachenie'];
	if(isset($_POST['id']) && preg_match("#^[0-9]+$#",$_POST['id'])) $_SESSION['task_id'] = $_POST['id'];

	$yandex_money->db->query("INSERT INTO `{$table_ym_pay}` 
		(id_pokypatelya, ot, komy, symma, paymant_id, status) 
		VALUES ('%u','-','" . YM_KOSHELEK . "','%u','-','NOACCEPT')", 
		array($_SESSION['user_id'], (int)$_POST['Symma']));
	$YM_PAY_ID = $yandex_money->db->insert_id;	//номер платежа в системе учета продавца
	if( $YM_PAY_ID==0 ){
		show_msg(NULL, 'Ошибка записи в таблицу YANDEX_MONEY', MSG_WARNING);
		return;
	}
	if($paymentType == 'AC')
		$Symma = ceil(100*(1+YM_COMMISION_AC)*$_POST['Symma'])/100;
	else
		$Symma = ceil(100*(1+YM_COMMISION_PC)*$_POST['Symma'])/100;	// с учетом комиссии и округлением до копейки в большую сторону
	$Comissiya = $Symma - $_POST['Symma'];
?>
<!--form id=pay method='POST' action="<?php echo url(NULL, 'YANDEX_MONEY','authorize');?>">
		<input type='hidden' name='Symma' value="<?php echo htmlspecialchars($Symma,ENT_QUOTES,'cp1251'); ?>">
</form-->
<form id=pay method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
	<input type="hidden" name="receiver" value="<?php echo YM_KOSHELEK; ?>">
	<input type="hidden" name="formcomment" value="<?php echo $_POST['Naznachenie']; ?>">
	<input type="hidden" name="short-dest" value="<?php echo $_POST['Naznachenie']; ?>">
	
	<input type="hidden" name="quickpay-form" value="shop">
	<input type="hidden" name="targets" value="<?php echo $_POST['Naznachenie'];?>">
	<input type="hidden" name="sum" value="<?php echo $Symma; ?>" data-type="number" >
	<input type="hidden" name="paymentType" value="<?php echo $paymentType;?>">
	<!--input type="radio" name="paymentType" value="AC">Банковской картой</input-->
	<input type="hidden" name="label" value="<?php echo $YM_PAY_ID; ?>">
	<!--input type="hidden" name="comment" value="" -->
	<input type="hidden" name="need-fio" value="false">
	<input type="hidden" name="need-email" value="false" >
	<input type="hidden" name="need-phone" value="false">
	<input type="hidden" name="need-address" value="false">
	<!--input type="submit" name="submit-button" value="Перевести"-->
</form>
<script type="text/javascript">
	$('#pay').submit();
</script>

