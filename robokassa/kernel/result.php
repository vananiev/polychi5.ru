<?php
	/*
	 * 		Получение информации продавцом, что оплата проведена 
	 *
	 * 	OutSum - сумма платежа
	 *  InvId - номер платежа на сайте продавца
	 *  SignatureValue - подпись запроса
	 * 
	 */

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:" . rb_pass2));

// проверка корректности подписи
// check signature
if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}

// Начисляем деньги
$paymant_info = $ROBOKASSA->db->row($table_robokassa, (int)$inv_id, 'id_pokypatelya, symma, status');
if(!isset($paymant_info['status']) || $paymant_info['status']=='OK'){
	echo 'Ошибка статуса';
	exit();
	}
if(!isset($paymant_info['symma']) || $paymant_info['symma']!=$out_summ){
	echo 'Не верная сумма';
	exit();
	}
if( !$users->db->query("UPDATE `$table_users` SET `balance` = `balance` + '%u' WHERE id = '%u'",
	array((int)$out_summ, (int)$paymant_info['id_pokypatelya'] ) ) ){		
	echo 'Ошибка пополнения баланса';
	exit();
	}

// Запись информации о платеже в таблицу robokassa
if ( !$ROBOKASSA->db->query("UPDATE `{$table_robokassa}` SET
		symma = '%u',
		status = '%s'
		WHERE id = '%u'",	array((int)$out_summ, 'OK', (int)$inv_id)) ){
	echo 'Ошибка записи данных о платеже в таблицу ROBOKASSA';
	exit();
	}

// Запись информации о платеже в таблицу money
require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
$money_row_id = add_record(
	ROBOKASSA_IN,				 //от вебмани
	(int)$paymant_info['id_pokypatelya'],      //пользователю
	(int)$out_summ,         	 //заплатил
	(int)$out_summ,         	 //перевелось
	"Пополнение баланса через robokassa №{$inv_id}",
	-1,        //добаляем новую запись
	0);        //комиссия
if($money_row_id ==0)
	{
	show_msg(NULL,"Ошибка обновления таблицы money. Оплата robokassa №{$inv_id}. Обратитесь к администратору с этим сообщением",MSG_CRITICAL,MSG_NOBACK);
	exit();
	}


// признак успешно проведенной операции
// success
echo "OK$inv_id\n";
exit();
?>
