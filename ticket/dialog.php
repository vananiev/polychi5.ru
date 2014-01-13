<?php /*
		**************************************************************
				Просмтр запроса
		**************************************************************
		параметры GET:
		ticket_id - номер тикета
		mail - адрес почты не регистрированного пользователя для определения "его" запросов
		password - пароль доступа к запросу
		
		параметры POST:
		ticket_id - номер тикета, чей статус меняем
		
		// передавать из javascript хешированный пароль -> переидти на куки
*/
		if(isset($_POST['ticket_id']) && is_numeric($_POST['ticket_id'])) $_GET['ticket_id'] = (int)$_POST['ticket_id'];
		if(isset($_GET['ticket_id']) && is_numeric($_GET['ticket_id'])) $get_ticket_id = (int)$_GET['ticket_id'];
		if(isset($_GET['mail'])) $mail = htmlspecialchars($_GET['mail'],ENT_QUOTES);		// в запросах в БД не учавстнует, а только выводится на экран 
			else $mail = NULL;
		//ввод пароля доступа к тикету
		if(!isset($_GET['password']))
			$password = '';
		else if(strlen($_GET['password'])==32)
			$password = $_GET['password'];
		else
			$password = md5($_GET['password']);
?>
<?php 
	// проверка задание переменной
	if( !isset($get_ticket_id) )
		{
		show_msg(NULL,'Не указан тикет',MSG_WARNING);
		return;
		}
	//проверка прав на работу с тикетом отзывов
	if( $get_ticket_id==0 && $_SESSION['user_id']!=0 )
		{
		check_admin_access();
		show_msg(NULL,'Этот тикет не доступен',MSG_WARNING);
		return;
		}
?>
<p class="about_page">
	<?php echo url('Ознакомиться с этой страницей', 'TICKET', 'info/how_dialog');?>
</p>
<p align='center'>
<?php
		if(isset($mail)) $args = "mail=".$mail;
			else $args = NULL;
		echo url('<< К таблице запросов', 'TICKET', 'tickets', $args);
	?>
</p>
<?php
//обновляем тикет
call_user_func($ticket->update_ticket, $get_ticket_id, $mail, $password);
//сообщения тикета
$ret = call_user_func($ticket->show_tickets, $get_ticket_id, $mail, $password); 
if($ret !== true){
	if(!empty($ret))show_msg(NULL, $ret, MSG_WARNING);
	}
else
	// вывод формы ввода вопроса
	call_user_func($ticket->add_question_form, $get_ticket_id, $mail, $password);
	
?>
<style type="text/css">
.table_tickets {
			width:1000px;
			}
.table_tickets td  {
			border:1px solid #888;
			}
.table_dialog {
			width:100%;
			width=96%;
			padding: 0 100px;
			border-spacing: 0 20px; border-collapse: separate;
			}
table .td_who{
			margin:30px 0 0 0;
			padding: 10px;
			border: 1px solid #888;
			text-align:left;
			width: 250px;;
			}
.table_dialog .td_content {
			margin:0;
			padding: 10px;
			border: 1px solid #888;
			border-left: none;
			text-align:left;
			vertical-align:top;
			font-size:0.9em;
			line-height:1em;
			}
.table_dialog td  {
			border:1px solid grey;
			}
.attached	{
			color:blue;
			border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; font-size:10px; font-weight:bolder; padding:2px ;background-color:#EEE;
			border:1px solid white;
			margin:10px 1px 1px 50px;
			width:140px;
			text-align:center;
			}
</style>

