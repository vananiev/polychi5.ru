<h1><?php echo $URL['TITLE']; ?></h1>
<?php
	/* 
	 * Файл на который редиректит яндекс после перевода денег
	 * Выполнение операций
	 *
	 * SESSION:
	 * task_id	- оплата за решение
	POST:
	* notification_type - Для переводов из кошелька — p2p-incoming.
						Для переводов с произвольной карты — card-incoming.
	* operation_id - Идентификатор операции в истории счета получателя.
	* amount - Сумма, которая зачислена на счет получателя.
	* withdraw_amount - Сумма, которая списана со счета отправителя.
	* datetime - Дата и время совершения перевода.
	* sender - Для переводов из кошелька — номер счета отправителя.
			Для переводов с произвольной карты — параметр содержит пустую строку.
	* label - Метка платежа. Если ее нет, параметр содержит пустую строку.
	* currency - Код валюты — всегда 643 (рубль РФ согласно ISO 4217).

	* codepro - Для переводов из кошелька — перевод защищен кодом протекции.
			Для переводов с произвольной карты — всегда false.
	* sha1_hash - SHA-1 hash параметров уведомления.
	*/

require_once(dirname(__FILE__) . '/kernel/config.php');
$string_for_hash="{$_POST['notification_type']}&{$_POST['operation_id']}&{$_POST['amount']}&{$_POST['currency']}&{$_POST['datetime']}&{$_POST['sender']}&{$_POST['codepro']}&".CLIENT_SECRET."&{$_POST['label']}";
$sha1_hash_calc = sha1($string_for_hash);

function log_error($msg){
	$fp = fopen(dirname(__FILE__) .'/pay.log', 'a');
	global $string_for_hash, $sha1_hash_calc;
	$msg = date(DATE_TIME_FORMAT)."\n".$string_for_hash."\n".$sha1_hash_calc."\n".$_POST['sha1_hash']."\n".$msg."\n---------------------------\n";
	fwrite($fp, $msg);
	fclose($fp);
}


if (!isset($_POST['sha1_hash']) || $_POST['sha1_hash'] != $sha1_hash_calc) {
	log_error('Ошибка при подсчете контрольной суммы');
	return;
}

// Начисляем деньги
$ym_pay_id = (int)$_POST['label'];
if( $ym_pay_id  == 0) log_error('Не верный label. Запись с данным ym_pay_id не найдена');
$sum = (int)$_POST['amount'];

$paymant_info = $yandex_money->db->row($table_ym_pay, $ym_pay_id, 'id_pokypatelya');
$user_id  = (int)$paymant_info['id_pokypatelya'];
if( !$users->db->query("UPDATE `$table_users` SET `balance` = `balance` + '%u' WHERE id = '%u'",
	array($sum,  $user_id ) ) ){		
	log_error('Ошибка пополнения баланса: '.$users->db->error());
	}

// Запись информации о платеже в таблицу YM
if ( !$yandex_money->db->query("UPDATE `{$table_ym_pay}` SET
		id_pokypatelya = '%u',
		ot = '%s',
		komy = '%s',
		symma = '%u',
		paymant_id = '%s',
		status = '%s'
		WHERE id = '%u'",	array($user_id , $_POST['sender'], YM_KOSHELEK, $sum, $_POST['operation_id'], 'OK', $ym_pay_id) )
)
{
	log_error('Ошибка записи данных о платеже в таблицу YANDEX_MONEY: '.$yandex_money->db->error());
}

// Запись информации о платеже в таблицу money
require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
if ( 0 == add_record(
	YANDEX_MONEY_IN,				 //от
	$user_id ,						//пользователю
	$sum,         	 				//заплатил
	$sum,         	 				//перевелось
	"Пополнение баланса №{$ym_pay_id} через Yandex.Money",
	-1,        //добаляем новую запись
	0)        //комиссия
)
{
	log_error("Ошибка обновления таблицы money. Оплата Yandex.Money №{$ym_pay_id}, operation_id={$_POST['operation_id']}. Обратитесь к администратору с этим сообщением");
}
?>