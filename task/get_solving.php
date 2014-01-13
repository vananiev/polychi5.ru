<?php
		/*********************************************************************
				Скрипт получения решения.
				Перенаправление на скрипт оплаты, в случае
					не достаточности средств на балансе.
		*********************************************************************
		Параметры GET:
		id	- номер задания
		*/
		if(isset($_GET['id']) && is_numeric($_GET['id'])) $get_id = (int)$_GET['id'];
?>
<?php
		//откуда переходим
    	$_SESSION['from'] = 'get_solving';
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php
if( check_can_get_solv($get_id) ){
	go_download($get_id);
	return;
}

if(isset($USER)){

	$row = $task->db->row($table_task, (int)$get_id) or die($task->db->error());

	//получаем решение
	if($USER['balance'] >= $row['price'])
		{
		$rel = url('[Продолжить]', 'TASK', 'get_solving_link', 'id='.$get_id);
		show_msg("Внимание!","Вы пытаетесь получить решение задания №{$get_id}.<br>
				Продолжение приведет к снятию  с вашего счета суммы в размере {$row['price']} рублей.<br>
				Если вы хотите получить решение нажмите продолжить...<br>
				".$rel."
				<a href='javascript:history.go(-1)'>[Отмена]</a>",MSG_INFO,MSG_NO_BACK);
		}
	else{
		$to_pay = $row['price']-$USER['balance'];
		show_msg("Информация","На вашем баланс не хватает {$to_pay} руб. ".url('[пополнить]', 'TASK', 'get_balance',"id=".$get_id."&sum=".$to_pay) ,MSG_INFO,MSG_RETURN);
		}
}
?>
