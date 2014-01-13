<?php
	/***************************************************************

		Форма НЕы выполненного платежа

	****************************************************************/
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php 
	if(isset($_POST['LMI_WMCHECK_NUMBER']))
		{
		$rel = url('баланс', 'TASK', 'get_balance');
		show_msg(NULL,"Возникла неустранимая ошибка при выполнении платежа с Вашего мобильного Webmoney счета: ".$_POST['LMI_WMCHECK_NUMBER'].".<br>Проверить ".$rel,MSG_INFO,MSG_NO_BACK);
		}
	else	
		{
		$rel = url('баланс', 'TASK', 'get_balance');
		show_msg(NULL,"Возникла неустранимая ошибка при выполнении платежа.<br>Проверить ".$rel,MSG_CRITICAL,MSG_NO_BACK);
		}
?>