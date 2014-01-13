<?php
	/***************************************************************

		Форма выполненного платежа

	***************************************************************
	* SESSION:
	* user_id 	- тот кто оплачивает
	* task_id	- оплата за решение
	*/
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php
require_once(SCRIPT_ROOT. '/' . $INCLUDE_MODULES['ROBOKASSA']['PATH'] ."/do_after_pay.php");

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:".rb_pass1));

// проверка корректности подписи
if ($my_crc != $crc)
{
	show_msg(NULL,"Неверная цифровая подпись. Статус платежа не известен",MSG_WARNING,MSG_NO_BACK);
	return;
}

// проверка наличия номера счета в истории операций и статуса
$paymant_info = $ROBOKASSA->db->row($table_robokassa, (int)$inv_id, 'id_pokypatelya, status');
if(!isset($paymant_info['id_pokypatelya']) || $paymant_info['id_pokypatelya'] != $_SESSION['user_id']){
	show_msg(NULL,"Платеж запрашивали не вы. Статус платежа не известен",MSG_WARNING,MSG_NO_BACK);	
	return;	
}
if(!isset($paymant_info['status']) || $paymant_info['status'] != 'OK'){
	show_msg(NULL,"Статус платежа не равен 'OK'",MSG_WARNING,MSG_NO_BACK);	
	return;	
}
	
$text = "Платеж успешно ";

if(isset($_SESSION['task_id']) && $_SESSION['task_id']!=""){
	$text .= "выполен ";				//если получали задание
	$text .= "<br>Переидти к получению ";
	$text .= url('решения', 'TASK', 'get_solving', 'id='.$_SESSION['task_id']);
	?>
	<script lang="text/javascript">location.href='<?php echo url(NULL, 'TASK', 'get_solving', 'id='.$_SESSION['task_id']); ?>';</script>
	<?php
	unset($_SESSION['task_id']);
}
else
	$text .= url('выполнен', 'TASK', 'get_balance');	 //пополняли баланс	

show_msg(NULL,$text,MSG_INFO,MSG_NO_BACK);	//отображаем сообщение
?>
