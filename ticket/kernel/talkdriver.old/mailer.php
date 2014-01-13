<?
/*
	Рассылка уведомлений о принятых сообщениях
*/
if(!isset($_POST["sid"]) || !isset($_POST["data"])) return;
$sid = $_POST["sid"];
$data = $_POST["data"];

if ($sid != TD_SID)
    exit;

$usrs = explode(";", $data);

foreach ($usrs as $data) {
    $contacts = explode(":", $data);
    $user_id = array_shift($contacts);
	if(is_numeric($user_id)){ 
		$user_id = (int)$user_id;
		// впишите код получения данных пользователя $user_id из базы

		$u = get_user($user_id, 'mail, login');

		if ($u['mail']!='') {
			$list = "";
			$sum_cnt = 0;

			foreach ($contacts as $data) {
				list($contact_id, $cnt) = explode("-", $data);

				// впишите код получения данных пользователя $contact_id из базы
				//$c = ...;

				//$list .= $c["nick"].": ".$cnt."\n\n";
				$sum_cnt += $cnt;
			}
			
			//$message = "Здравствуйте, ".$u["nick"].".\n\nУ вас новые сообщения:\n\n".$list;
			$sub = "Новые сообщения";
			$message = "Здравствуйте, ".$u["login"].".\n\nУ вас ".$sum_cnt." новых сообщения(ий).\r\n".
			"Для просмотра сообщений переидите поссылке: <a ".td_link($user_id,false).">Новые сообщения</a>".
			"С уважением, http://{$_SERVER['SERVER_NAME']}/ ";

			sendmail ($u['mail'],$sub,$message);
		}
	}
}
?> 