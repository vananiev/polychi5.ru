<?php /*
	Скрипт создания заявки на платеж. Вызывается по нажатию пользователем по кнопке отплатить.
	Передаваемые параметры.
	Метод - POST
	Symma		-сумма оплачиваемых денег
	Naznachenie	-описание платежа
	id		    -если получаем решение, то номер решения

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
?>
<?php
	require_once(dirname(__FILE__) . '/kernel/config.php');

	//проверяем заполненность полей
	if(!isset($_POST['Naznachenie']) || !isset($_POST['Symma']) || !preg_match("#^[0-9]+$#",$_POST['Symma']))
		{show_msg(NULL, "Не верно указана назначение платежа или сумма.<br>Сумма должна быть целым числом.",MSG_WARNING);
		return;}

	//записываем информацию о платеже
	if(isset($_POST['id']) && preg_match("#^[0-9]+$#",$_POST['id'])) $_SESSION['task_id'] = $_POST['id'];

	$ROBOKASSA->db->query("INSERT INTO `{$table_robokassa}` 
		(id_pokypatelya, symma, status) 
		VALUES ('%u','%u','NOACCEPT')", 
		array($_SESSION['user_id'], (int)$_POST['Symma']));
	$inv_id = $ROBOKASSA->db->insert_id;	//номер платежа в системе учета продавца
	if( $inv_id==0 ){
		show_msg(NULL, 'Ошибка записи в таблицу ROBOKASSA', MSG_WARNING);
		return;
	}
?>
<?php

// 2.
// Оплата заданной суммы с выбором валюты на сайте ROBOKASSA
// Payment of the set sum with a choice of currency on site ROBOKASSA

// номер заказа
// number of order
//$inv_id ; устанавливается выше

// описание заказа
// order description
$inv_desc = htmlspecialchars($_POST['Naznachenie'],ENT_QUOTES);

// сумма заказа
// sum of order
$out_summ = htmlspecialchars($_POST['Symma'],ENT_QUOTES);

// предлагаемая валюта платежа
// default payment e-currency
$in_curr = "";

// язык
// language
$culture = "ru";

// формирование подписи
// generate signature
$crc  = md5(rb_login.":$out_summ:$inv_id:".rb_pass1);

// форма оплаты товара
// payment form
?>
<form id="pay" action='<?php echo rb_url; ?>' method=POST>
	<input type=hidden name=MrchLogin value="<?php echo rb_login;?>">
	<input type=hidden name=OutSum value="<?php echo $out_summ;?>">
	<input type=hidden name=InvId value="<?php echo $inv_id;?>">
	<input type=hidden name=Desc value="<?php echo $inv_desc;?>">
	<input type=hidden name=SignatureValue value="<?php echo $crc;?>">
	<input type=hidden name=IncCurrLabel value="<?php echo $in_curr;?>">
	<input type=hidden name=Culture value="<?php echo $culture;?>">
</form>
<script type="text/javascript">
	$('#pay').submit();
</script>
