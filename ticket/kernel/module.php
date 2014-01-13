<?php
	/***************************************************************************

						Модуль техподдержки

	**************************************************************************/
?>
<?php
	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "TICKET: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}

	// конфигурация модуля
	require('config.php');
	
	$TICKET_ADDITIONAL=array('DESCRIPTION'=>'Управление запросами пользователей. Отзывы');
	$INCLUDE_MODULES['TICKET']['INFO'] = &$TICKET_ADDITIONAL;

	
	// спользуемые функции (их можно переопределить)
	class ClassTicket
		{
		public $db;
		public $on_page				= 10;					// число строк в таблице на одной странице				
		public $add_question_form 	= 'add_question_form';	// форма добавления вопроса
		public $add_question 		= 'add_question';		// функция добавления вопроса
		public $del_question		= 'del_question';		// удаление вопроса
		public $change_ticket_status= 'change_ticket_status';// смена статуса тикета
		public $show_add_form	 	= 'add_ticket_form';	// форма добавления тикета
		public $add				 	= 'add_ticket';			// добавления тикета
		public $show_messages	 	= 'show_messages';		// вывод всех сообщений тикета
		public $show_tickets	 	= 'show_tickets';		// вывод тикетов, если не указан конкретный (или инф. о тикете и всех сообщений)
		public $update_ticket		= 'update_ticket';		// производит добаление, редактирование или удаление сообщения тикета, необходимо вызвать перед show_tickets
		public $show_dialog			= 'show_dialog';		// вывод переписки, вывод формы добавления, обработка запросов добавления/удаления сообщений
		}
	$ticket = new ClassTicket();

	// Подключаем БД
	require_once('DB_ticket.php'); 
	
	// подлючаем функции
	require_once('function.php'); 
	
	// подключаем функции для работы с мессенджером TalkDriver
	//require_once('talkdriver/function.php'); 
	
	//----------------------------------------------------------------------------------------------------------------------------------
	// добавляем пункты меню
	require('files.php');
?>