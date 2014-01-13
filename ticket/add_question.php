<?php /*
		**************************************************************
				Добавление/редактирование сообщения
		**************************************************************
		GET:
		ticket_id	- номер тикета
		mail - адрес почты не регистрированного пользователя для определения "его" запросов
		password - пароль доступа к запросу
*/
		if(isset($_GET['ticket_id']) && is_numeric($_GET['ticket_id'])) $ticket_id = (int)$_GET['ticket_id'];
		if(isset($_GET['mail'])) $mail = htmlspecialchars($_GET['mail'],ENT_QUOTES);		// в запросах в БД не учавстнует, а только выводится на экран
		//ввод пароля доступа к тикету
		if(!isset($_GET['password']))
			$password = "";
		else if(strlen($_GET['password'])==32)
			$password = $_GET['password'];
		else
			$password = md5($_GET['password']);
		

	// проверка задание переменной
	if( !isset($ticket_id) )
		{
		show_msg(NULL,'Не указан тикет',MSG_WARNING);
		return;
		}

	//вывод формы для добавления сообщения
	if(!isset($mail))			$mail=NULL;
	call_user_func($ticket->add_question_form, $ticket_id, $mail,  $password);
?>