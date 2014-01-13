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

// добавлено новое задание	
if(isset($_POST['name']) && $_POST['name'] == 'new_task')	
	{
	if(isset($_POST['task_id']) && is_numeric($_POST['task_id']))
		{
		$id = $_POST['task_id'];
		global $task, $table_task;
		$task_info = $task->db->row($table_task, (int)$id, 'section');
		// получаем список пользователей с правом решения
		$users = users_whith_right('TSK_SOLVING','login,mail');
		foreach ($users as $user)
			{
			if($user['mail']!="")
				{
				$sub ="Новое задание";
				$rel = url(NULL, 'TASK', 'task', 'id='.$id);
				$mes = "Уважаемый, {$user['login']}.\n";
				$mes.= "Добавлено новое задание.\n";
				$mes.= "Предмет: {$task_info['section']}.\n";
				$mes.= "http://".MAIL_DOMAIN.$rel."\n";
				$mes.= "С уважением, http://".MAIL_DOMAIN;
				sendmail ($user['mail'],$sub ,$mes);
				}
			}
		}
	}