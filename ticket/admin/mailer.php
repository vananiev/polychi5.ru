<?php
	/*************************************************
				Е-mail оповещения
	**************************************************

	POST:
	name	- имя выполняемой задачи
	secret	- md5() от константы SECRET
	*/
if(!isset($_POST['secret']) || md5(SECRET) != $_POST['secret'])
	{
	show_msg(NULL, 'Ошибка доступа', MSG_CRITICAL);
	return;
	}

/*/ добавлено новый тикет
if(isset($_POST['name']) && $_POST['name'] == 'new_ticket')	
	{
	if(isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']))
		{
		$ticket_id = $_POST['ticket_id'];
		// получаем список пользователей с правом
		$usrs = users_whith_right('TKT_SUPPORT_ANS','mail');
		foreach($usrs  as $usr)
			{
			$sub ="Новый запрос";
			$rel = url(NULL, 'TICKET', 'dialog', 'ticket_id='.$ticket_id);
			$mes = "Смотрите переписку по адресу: http://{$_SERVER['SERVER_NAME']}".$rel;
			$mes .= "\n".$post_text;
			if($issetFile == 'YES')
				$mes .= "\nПрикрепленный файл: http://{$_SERVER['SERVER_NAME']}".TICKET_FILE_RELATIVE."/$ticket_id.rar";
			$mes .=	"\n\nС уважением, http://{$_SERVER['SERVER_NAME']}/.";
			sendmail ($usr['mail'],$sub,$mes); //отсылаем службе поддержки
			}
		}
	}
*/
// добавлен новый вопрос, оповещаем службу поддержки
if(isset($_POST['name']) && $_POST['name'] == 'new_question')	
	{
	if(isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']))
		{
		$ticket_id = $_POST['ticket_id'];
		$rel = url(NULL, 'TICKET', 'dialog', 'ticket_id='.$ticket_id);
		// не является ли переписка обсуждением задания ?
		if( defined('MODULE_TASK') ){
			global $table_task, $task;
			$res = $task->db->query("SELECT `id` FROM {$table_task} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
			if( $tsk = $res->fetch_assoc() )
				$rel = url(NULL, 'TASK', 'task', 'id='.$tsk['id']);
			}
		if( defined('MODULE_USERS') ){
			global $users, $table_users;
			$res = $ticket->db->query("SELECT `id` FROM {$table_users} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
			if( $usr = $res->fetch_assoc() )
				$rel = url(NULL, 'USERS', 'about_user', 'user_id='.$usr['id']);
			}
		// получаем список пользователей с правом
		$usrs = users_whith_right('TKT_SUPPORT_ANS','mail,login');
		foreach($usrs  as $usr)
			{
			if($usr['mail']=="" || $usr['mail']=="-") continue;
			$sub ="Новое сообщение";
			$mes = "Уважаемый, {$usr['login']}.\n";
			$mes .= "Смотрите переписку по адресу: http://{$_SERVER['SERVER_NAME']}".$rel;
			//if($issetFile == 'YES')
			//	$mes .= "\nПрикрепленный файл: http://{$_SERVER['SERVER_NAME']}".TICKET_FILE_RELATIVE."/$ticket_id.rar";
			$mes .=	"\n\nС уважением, http://{$_SERVER['SERVER_NAME']}/.";
			sendmail ($usr['mail'],$sub,$mes); //отсылаем службе поддержки
			}
		}
	}

// получен ответ от службы поддержки
if(isset($_POST['name']) && $_POST['name'] == 'ans_for_ticket')	
	{
	if(isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id']))
		{
		$ticket_id = $_POST['ticket_id'];
		if( isset($_POST['from_mail']) && $_POST['from_mail']!='')
			{
			$rel = url (NULL, 'TICKET', 'dialog', 'ticket_id='.$ticket_id.'&mail='.$_POST['from_mail']);
			$sendmail = $_POST['from_mail'];
			}
		else if( isset($_POST['user_mail']) && $_POST['user_mail']!='')
			{
			$rel = url (NULL, 'TICKET', 'dialog', 'ticket_id='.$ticket_id);
			$sendmail = $_POST['user_mail'];
			}
		else
			return;
		// не является ли переписка обсуждением задания ?
		if( defined('MODULE_TASK') ){
			global $table_task, $task;
			$res = $task->db->query("SELECT `id` FROM {$table_task} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
			if( $tsk = $res->fetch_assoc() )
				$rel = url(NULL, 'TASK', 'task', 'id='.$tsk['id']);
			}
		if( defined('MODULE_USERS') ){
			global $users, $table_users;
			$res = $ticket->db->query("SELECT `id` FROM {$table_users} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
			if( $usr = $res->fetch_assoc() )
				$rel = url(NULL, 'USERS', 'about_user', 'user_id='.$usr['id']);
			}
		$sub ="Получен ответ";
		$mes  = "Cмотрите переписку по адресу: http://{$_SERVER['SERVER_NAME']}".$rel;
		$mes .= "\n"; 
		//if($issetFile == 'YES')
		//	$mes .= "\nПрикрепленный файл: http://{$_SERVER['SERVER_NAME']}/".TICKET_FILE_RELATIVE."/$ticket_id.rar";
		$mes .=	"\n\nС уважением, http://{$_SERVER['SERVER_NAME']}/.";
		sendmail ($sendmail,$sub,$mes);
		}
	}

// к пользовалю обратились в сообщении
if(isset($_POST['name']) && $_POST['name'] == 'write_to_user')	{
	if(!isset($_POST['ticket_id']) || !is_numeric($_POST['ticket_id'])) return;
	if(!isset($_POST['to_id']) || !is_numeric($_POST['to_id'])) return;
	$to_user = get_user($_POST['to_id'], 'login, mail');
	$ticket_id = $_POST['ticket_id'];
	if($to_user['mail']=="") return;
	// ссылка на страницу переписки
	$rel = url (NULL, 'TICKET', 'dialog', 'ticket_id='.$ticket_id);
	// не является ли переписка обсуждением задания ?
	if( defined('MODULE_TASK') ){
		global $table_task, $task;
		$res = $task->db->query("SELECT `id` FROM {$table_task} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
		if( $tsk = $res->fetch_assoc() )
			$rel = url(NULL, 'TASK', 'task', 'id='.$tsk['id']);
		}
	if( defined('MODULE_USERS') ){
		global $users, $table_users;
		$res = $ticket->db->query("SELECT `id` FROM {$table_users} WHERE `dialog_id`='%u' LIMIT 1", $ticket_id);
		if( $usr = $res->fetch_assoc() )
			$rel = url(NULL, 'USERS', 'about_user', 'user_id='.$usr['id']);
		}
	$sub = "У Вас новое сообщение";
	$mes = "Уважаемый, {$to_user['login']}.\n";
	$mes.= "К Вам обратились с сообщением.\n";
	$mes.= "Смотрите переписку по адресу http://{$_SERVER['SERVER_NAME']}".$rel."\n"; 
	$mes.= "С уважением, http://{$_SERVER['SERVER_NAME']}/.";
	sendmail ($to_user['mail'],$sub,$mes);
}