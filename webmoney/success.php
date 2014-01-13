<?php
	/***************************************************************

		Форма выполненного платежа

	***************************************************************
	POST:
	task_id	-номер оплачиваемого задания,если оплачиваем
	user_id -пользователь оплативший
*/?>
<?php
	//проверка статуса платежа
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php 
	require_once(SCRIPT_ROOT."/webmoney/do_after_pay.php");
	$text = "Платеж успешно ";

	if(isset($_POST['task_id']) && $_POST['task_id']!="")
		$text .= "выполен ";				//если получали задание
	else
		$text .= url('выполнен', 'TASK', 'get_balance');	 //пополняли баланс

	//если платили через терминал
	if(isset($_POST['LMI_WMCHECK_NUMBER']))
		$text .= "с Вашего мобильного Webmoney счета: ".$_POST['LMI_WMCHECK_NUMBER'];

	//если получали задание
	if(isset($_POST['task_id']) && $_POST['task_id']!="")
		{
		$text .= "<br>Переидти к получению ";
		$text .= url('решения', 'TASK', 'get_solving', 'id='.$_POST['task_id']);
		?>
		<script lang="text/javascript">location.href='<?php echo url(NULL, 'TASK', 'get_solving', 'id='.$_POST['task_id']); ?>';</script>
		<?php
		}		

	show_msg(NULL,$text,MSG_INFO,MSG_NO_BACK);	//отображаем сообщение
?>