<?php
		/*********************************************************************
						Выдача данных о задании
						+установка оценка решения учеником
						+установка согласия на решение
						+загрузка решения
		*********************************************************************
		Параметры GET:
		id	- номер задания

		POST:
		rating - ученик выставляет оценку
		agree - содержит id пользователя, который дал согласие на решение 'быстрый старт'
		status -  администратор изменяет статус
		price - новая устанавливаемая цена (администратор или тот кто задал задание)
		delete_by_user - удаляемое задание
		$_FILES["filename"] - файл для загрузки
		solver	-  ученик выбирает решающего и цену задания serialize(solver,price)
		solver_price - решающий дает заявку на решение по 'игра по моим правилам'
		*/
		if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id'];
		if(isset($_POST['rating']) && is_numeric($_POST['rating'])) $rating = 0+$_POST['rating'];
		if(isset($_POST['agree']) && is_numeric($_POST['agree'])) $agree = (int)$_POST['agree'];
		if(isset($_POST['status'])) $status = mysql_real_escape_string($_POST['status'],$msconnect_task);
		if(isset($_POST['price']) && is_numeric($_POST['price'])) $post_price = 0+$_POST['price'];
		if(isset($_POST['delete_by_user']) && is_numeric($_POST['delete_by_user'])) $delete_by_user = (int)$_POST['delete_by_user'];
		// solver - это сериализованные данные не требуют обработки
		if(isset($_POST['solver_price']) && is_numeric($_POST['solver_price'])) $solver_price = 0+$_POST['solver_price'];
?>
<?php
	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");

	if(!isset($id))
		{
		if(isset($_SESSION['user']) && user_in_group('USER')) $args =  "user=".$_SESSION['user'];
		else if(isset($_SESSION['user']) && user_in_group('SOLVER'))$args =  "solver=".$_SESSION['user'];
		else $args = NULL;
		$print = "Выберите задание в ";
		$print .= url('таблице заданий', 'TASK', 'tasks', $args);
		$print .= ", щелкнув по его номеру.";
		show_msg("Ошибка",$print,MSG_INFO,MSG_NO_BACK);
		return false;
		}
	// получаем информацию о задании
	//существует ли данное задание
	$query="SELECT * FROM `%s` WHERE id = '%d'";
	$vars = array($table_task,$id);
	$res = $task->db->query($query,$vars);
	$row = $res->fetch_array();
	if(!$row)
		{
		show_msg(NULL,"Задание не найдено ",MSG_WARNING,MSG_RETURN);
		return false;
		}
?>
<?php
if(!isset($_SESSION['user_id']))
{
	$user_login = get_user((int)$row['user'],'login');
	// А не анонимный ли пользователь выполнял заказ
	// если да, то логинимся под ним без запроса логина-пароля, чтобы работать с заданием
	if( login($user_login) !== true ){
		$_SESSION['QUERY_STRING'] = url(NULL, 'TASK','task','id='.$id);
		$INFO = "<div class='info_text'>Для просмотра задания введите логин-пароль</div>";
		if(!is_mobile_site()) {
			require(get_request_file(url(NULL,'USERS','in')));
		}else {
			require(MOBILE_ROOT.'/users/in.html');
			return false;
		}
		//show_msg(NULL,'Для просмотра задания '.url('Войдите в систему','USERS','in').'<br>После входа Вы автоматически попадете на эту станицу',MSG_INFO, MSG_NO_BACK);
		return;
	}
}
?>
<?php
	if( 		! (
				$row['user']==$_SESSION['user_id'] || $row['solver']==$_SESSION['user_id'] || check_right('TSK_SEE_OTHERS_TASK') ||
				( check_right('TSK_SOLVING') && ($row['status']=='NEW' || $row['status']=='GETS') )
				)
		)
		{
		define('TASK_LOCKED',true);
		show_msg('Доступ запрещен','У Вас нет права просматривать чужие задания или решать их,
						также Вы не являетесь ни владельцем, ни решающим этого задания
						',MSG_WARNING);
		return false;
		}
    //пользователь или администратор удаляет задание
    if(isset($delete_by_user) && isset($_SESSION['user_id']))
    	{
		//если действительно владелец задания
		if($row['user']==$_SESSION['user_id']  || check_right('TSK_DEL_OTHERS_TASK') )
    		{
    		//удаление задания
			$query="DELETE FROM `%s` WHERE `id` = '%u'";
			$vars = array($table_task, $delete_by_user);
    		$task->db->query($query,$vars) or die("Произошла ошибка в ходе удаления задания. Попытайтесь еще раз.<br>".$task->db->error());
    		//удаление диалогов
			if(is_numeric($row['dialog_id']))
				{
				$res = delete_ticket((int)$row['dialog_id']);
				if($res !== true) show_msg(NULL, $res, MSG_WARNING, MSG_NO_BACK);
				/*/ удаление записей из БД
				$ticket->db->query("DELETE FROM $table_tickets WHERE id='%u'", $row['dialog_id']);
				$ticket->db->query("DELETE FROM $table_questions WHERE ticket_id='%u'", $row['dialog_id']);*/
				}
			//удаление файлов
			//даление решения
			$files = glob( SOLVING_ROOT.'/'.$id.'.*');
			if(isset($files[0])){ unlink($files[0]); }
			//$files = glob( OPENED_SOLVING_ROOT.'/'.$id.'.*');
			//if(isset($files[0])){ unlink($files[0]); }
			//удаление задания
			$files = glob( TASK_ROOT.'/'.$id.'.*');
			if(isset($files[0])){ unlink($files[0]); }
			show_msg("Выполнено","Задание удалено ".url('[ok]', 'TASK','tasks'),MSG_INFO,MSG_NO_BACK);
			return false;
    		}
    	else
    		{
    		show_msg("Ошибка","У вас не достаточно прав.",MSG_CRITICAL);
    		return true;
    		}
    	}
	/*--------------------------------------- Административная функция -----------------------------------------------------------------------------*/
	//администратор изменяет статус
	if( isset($status) )
		{
	    if(!check_right('TSK_CNG_STATUS',R_MSG)) return false;
	    $query="UPDATE `%s`
   				SET status = '%s'
           		WHERE id = '%d'";
		$vars = array($table_task, $status, $id);
		$task->db->query($query,$vars) or die("Не удалось изменить статус<br>".$task->db->error());
		}
	/*--------------------------------------- Административная функция -----------------------------------------------------------------------------*/

	//согласие на решение
    if(isset($agree) && isset($_SESSION['user_id']))
    	{
		if(!check_right('TSK_SOLVING',R_MSG)) return false; //проверка прав решающего
		if($agree==$_SESSION['user_id'] )
    		{
		    $query="UPDATE `%s`
		    		SET solver = '%d',
		    		agree_date = CURRENT_TIMESTAMP(),
		    		status = 'WAIT'
		            WHERE id = '%d'";
			$vars = array($table_task, $agree, $id);
			$task->db->query($query,$vars) or die($task->db->error());
			// диалог для Автора
			if(TASK_DIALOG)
				{
				global $ticket, $table_tickets;
				$ticket->db->query("UPDATE `$table_tickets` SET `to_id`='%d' WHERE id = '%d'", array($agree, $row['dialog_id']))  or die($ticket->db->error());
				}
			}
		else
			show_msg("Ошибка","Такой пользователь не зарегистрирован или не является решающим.",MSG_CRITICAL);
		}
	//загружаем решение
	if(isset($_FILES["filename"]) && isset($_SESSION['user_id']) )
		{
		$ext = '.'.end (explode (".", $_FILES["filename"]["name"])); // расширение файла
		/*$ext=substr($_FILES["filename"]["name"],strlen($_FILES["filename"]["name"])-4,4);
		if($ext=="jpeg")
			   $ext = ".jpg";
		if ($ext==".jpg"  || $ext==".txt" || $ext==".rar")
			{*/
			/*if($_FILES["filename"]["size"] > MAX_FILE_SIZE*1024*1024)
	   		{
	     			show_msg("Ошибка","Размер файла превышает ".MAX_FILE_SIZE." мегабайт",MSG_WARNING);
	     			return;
	   		}*/
	  		//
			if($row['solver']!=$_SESSION['user_id'] && !check_right('TSK_SOLVING_OTHERS') )
				{
				show_msg("Ошибка","Решением задачи занимаетесь не вы.",MSG_CRITICAL);
				return true;
				}
			//удаляем старое решение
			$files = glob( SOLVING_ROOT.'/'.$id.'.*');
			if(count($files))
				foreach($files as $name)
					unlink($name);

			//копируем новое решение
	   		if(copy($_FILES["filename"]["tmp_name"],SOLVING_ROOT.'/'.$id.$ext))
	   			{
				$query="UPDATE `%s`
	    				SET solv_date = CURRENT_TIMESTAMP(),
	    				status = 'SOLV',
	    				rating = 5
	            		WHERE id = '%d'";
				$vars = array($table_task, $id);
				$task->db->query($query,$vars) or die($task->db->error());

				//отправляем уведомление ученику в виде письма
        $usr  = get_user((int)$row['user'], 'mail,phone,notification,login');
        $rel = url(NULL,'TASK', 'task', 'id='.$id);
        $mail_mes = "Уважаемый, {$usr['login']}.\nВаше задание решено.\nhttp://".DOMAIN.$rel."\nС уважением, http://".DOMAIN."/.";
		$phone_mes = $usr['login'].", заказ ".$id." готов http://".DOMAIN.$rel;
        switch($usr['notification']){
        case 'mail':
          if($usr['mail']!="") sendmail ($usr['mail'],"Ваше задание решено!",$mail_mes);
          break;
        case 'phone':
          //list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "Ваш пароль: 123");
          if($usr['phone']!="") send_sms($usr['phone'], $phone_mes);
          break;
        }
				}
			else
				{
				show_msg(NULL,"Произошла ошибка при загрузке файла. Пожалуйста, попробуйте еще раз.",MSG_WARNING);
				return true;
				}
			/*}
		else
			{
			show_msg(NULL,"Данный формат файла не разрешен.",MSG_WARNING);
			return true;
			}*/
		}

    //оцениваем
    if(isset($rating) && isset($_SESSION['user_id']))
    	{
		if($row['user']==$_SESSION['user_id'] || check_right('TSK_RATING_OTHERS'))
			{
			if($rating>5)
				$rating=5;
			if($rating<0)
				$rating=0;
			if($rating!=0)
				{//удовлетворительная оценка
				$query="UPDATE `%s`
						SET rating = '%d',
						status = 'SOLV'
			            WHERE id = '%d'";
				$vars = array($table_task, $rating, $id);
				$task->db->query($query,$vars) or die($task->db->error());
				}
			else
				{//не удовлетворительная оценка
				//обновляем таблицу task
				$r_date = time() + 7*24*3600;  //7 дня на перерешение
	            $r_date = date('Y-m-d H:i:s',$r_date);
	            $query="UPDATE `%s`
						SET `rating` = 0,
						`status` = 'REMK',
						`resolve_until` = '%s'
			            WHERE `id` = '%d'";
				$vars = array($table_task, $r_date, $id);
				$task->db->query($query,$vars) or die("Попробуйте еще раз изменить оценку<br>".$task->db->error());
				//если решающий не перерешает он не сможет вывести деньги со счета
				//отправляем уведомление решающему в виде письма
				$query="SELECT `mail`, `login` FROM `%s` WHERE `id` = '%d'";
				$vars = array($table_users, $row['solver']);
				$res2 = $task->db->query($query,$vars) or die($task->db->error());
				$row2 = $res2->fetch_assoc();
				if($row2['mail']!="")
					{
					$sub = "Перерешать задание";
					$rel = url(NULL, 'TASK', 'task', 'id='.$id);
					$mes = "Уважаемый, {$row2['login']}.
							Решенное вами задание было оценено НЕудовлетворительной оценкой.
							Чтобы вывести средства из системы, Вам необходимо перерешать задание.
							Вам необходимо прислать решение задания в течение недели. Для уточнения срока решения переидите по ссылке
							http://".DOMAIN.$rel."
							В противном случае на Вас будет наложен штраф в размере ".get_strav_sum($row['price'])." рублей.
							http://".DOMAIN.$rel."
							С уважением, http://".DOMAIN."/.";
					sendmail ($row2['mail'],$sub,$mes);
					}
				}
			}
		}

	//администратор или ученик,владелец задания, меняет цену
	if(isset($post_price) && is_int((int)$post_price))
		{
		if($row['user']==$_SESSION['user_id'] || check_right('TSK_PRICING_OTHERS')) //проверка прав пользователя
			{
			/* Глупо требовать положительного баланса, чтобы поменять цену задания
			$query = "SELECT balance
						FROM `$table_users`
						WHERE id='{$_SESSION['user_id']}'
						LIMIT 1";
			$res2 = mysql_query($query,$msconnect_users) or die(mysql_error());
			$row2=mysql_fetch_array($res2);
			if((int)$row2['balance'] < 1) //(int)$post_price)
				{
				$rel = url('[пополните баланс]', 'TASK', 'get_balance');
				show_msg(NULL,"Стоимость не изменена!<br><span style='color:red;'>На вашем балансе должен быть хотя бы 1 рубль.</span>
					<br>Пожалуйста ".$rel. '<br>' , MSG_INFO,MSG_RETURN);
				return true;
				}
			*/
			global $task;
			$task->db->query("UPDATE `$table_task` SET price = '%u' WHERE id = '%u'" ,array((int)$post_price, $id)) or die("Не удалось изменить цену<br>".$task->error());
			}
		}
	//ученик выбирает решающего
    if(isset($_POST['solver']) && $_POST['solver']!="")
    	{
		if( $row['user']==$_SESSION['user_id'] || check_right('TSK_SELECT_SOLVER_OTHERS') )  //если это ученик - владелец задания  или имеет соответствующее право
			{
			$array=unserialize($_POST['solver']);
			if(!is_array ($array))
				{
				show_msg(NULL,"Ошибка не удалось определить решающего",MSG_CRITICAL);
				return true;
				}
			//хватит ли денег расплатиться за решение
			//узнаем баланс
			if(isset($USER))
				{
				$min_balance = 0.5*$array[1];
				if($USER['balance'] < $min_balance) // (int)$array[1])
					{
					$rel = url('[Пополнить баланс]', 'TASK', 'get_balance',"sum=".$min_balance);
					show_msg(NULL,"Для выбора автора и начала решения задания на вашем счету должно быть не менее 50% суммы заказа (".$min_balance." руб). Пополните счет и выберите автора снова.<br>".$rel."&nbsp;",MSG_INFO,MSG_OK);
					return true;
					}
				}
			else
				{
				show_msg(NULL,"Войдите в ситему под своим именем.",MSG_CRITICAL);
				return false;
				}

			$array[0] = (int)$array[0]; //преобразуем тип
			$array[0] = 0+$array[0];	//преобразуем тип
            $query="UPDATE `$table_task`
		  				SET solver = '{$array[0]}',
		  				price = '{$array[1]}',
		  				status = 'WAIT',
		  				agree_date = CURRENT_TIMESTAMP()
		          		WHERE id = '{$id}'"; //resolve_until = '$resolve_until'
			$res = mysql_query($query,$msconnect_task) or die("Не удалось выбрать решающего<br>".mysql_error());
			//отправляем письмо решающему
			$query = "SELECT mail, login
	           		FROM $table_users
	           		WHERE id='{$array[0]}'
	           		LIMIT 1";
		   	$res3 = mysql_query($query,$msconnect_users) or die(mysql_error());
			$row3=mysql_fetch_array($res3);
			if($row3['mail']!="")
				{
				$resolve_until = date('Y-m-d H:i:s',(strtotime($row['resolve_until'])));  //время задано относительно в днях и часах
				$sub ="Вы назначены Решающим задания {$id}.";
				$rel = url(NULL, 'TASK', 'task', 'id='.$id);
				$mes = "Уважаемый, {$row3['login']}\n";
				$mes.= "Вы назначены Решающим задания №{$id}.\n";
				$mes.= "Задание необходимо решить до {$resolve_until}.\n";
				$mes.= "http://".DOMAIN.$rel."\n";
				$mes.= "С уважением, http://".DOMAIN."/";
				sendmail ($row3['mail'],$sub,$mes);
				}
			// диалог для Автора
			if(TASK_DIALOG)
				{
				global $ticket, $table_tickets;
				$ticket->db->query("UPDATE `$table_tickets` SET to_id='%d' WHERE id = '%d'", array($array[0], $row['dialog_id'])) or die($ticket->db->error());
				}
			}
    	}
    //решающий дает заявку на решение
    if(isset($solver_price))
    	{
    	if(!check_right('TSK_SOLVING',R_MSG)) return false; //проверка прав решающего

		$query = "SELECT select_solver
				FROM `$table_task`
				WHERE id='{$id}'
				LIMIT 1";
		$res3 = mysql_query($query,$msconnect_task) or die(mysql_error());
		$row3=mysql_fetch_array($res3);
		$n=0;
		if(isset($row3['select_solver']) && $row3['select_solver']!='')
			{
			$array= array_values(unserialize($row3['select_solver'])); // array_values - нумеруем с нулевого
			$cnt = count($array);
			$n=$cnt;
			for($i=0; $i<$cnt;$i++)
				if($array[$i][0] == (int)$_SESSION['user_id'])
					{ $n=$i;break;}
			}
		if($solver_price>0)
			{
			$array[$n][0] = (int)$_SESSION['user_id'];
			$array[$n][1] = price_before_all_comm($solver_price);     //учет комисии и округления
			}
		else //отказ от решения
			{
			//echo $n;
			//print_r($array);
			unset($array[$n]);
			//print_r($array);
			}
		$select_solver=serialize($array);
		$query="UPDATE `$table_task`
					SET select_solver = '{$select_solver}'
					WHERE id = '{$id}'";
		$res = mysql_query($query,$msconnect_task) or die("Не удалось изменить цену<br>".mysql_error());
		if($solver_price>0){
			;//show_msg(NULL,"Ваша предложение принято: цена задания - {$solver_price} руб.",MSG_INFO,MSG_NO_BACK);
        //отправляем уведомление ученику в виде письма
        $usr  = get_user((int)$row['user'], 'mail,phone,notification,login');
        $rel = url(NULL,'TASK', 'task', 'id='.$id);
        $mail_mes = "Уважаемый, {$usr['login']}.\nАвтор подал заявку на решение Вашего задания.\nСтоимость ".price_before_all_comm($solver_price)." руб.\nhttp://".DOMAIN.$rel."\nС уважением, http://".DOMAIN."/.";
				$phone_mes = $usr['login'].", выберите решающего заказа ".$id." http://".DOMAIN.$rel;
        switch($usr['notification']){
        case 'mail':
          if($usr['mail']!="") sendmail ($usr['mail'],"Ваше задание готовы решить",$mail_mes);
          break;
        case 'phone':
          //list($sms_id, $sms_cnt, $cost, $balance) = send_sms("79999999999", "Ваш пароль: 123");
          // Отсылаем смс только 1 раз
          if($usr['phone']!="" && $n==0) send_sms($usr['phone'], $phone_mes);
          break;
        }
			}
		else
			show_msg(NULL,"Вы отказались от решения задания",MSG_INFO,MSG_NO_BACK);
    	}
?>
