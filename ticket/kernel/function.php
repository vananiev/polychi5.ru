<?php
	/***************************************************************************

						Используемые Функции 

	**************************************************************************/
	
	// Если пользователь не зарегистрировался, то доступ к тикету осуществляется по паролю и адресу-почты

	//------------------------ Получение информации о тикете --------------------------------------------
	function &ticket($id, $fields='*')
	{
		if(!is_numeric($id)) return false;
		$id = (int)$id;
		global $ticket, $table_tickets;
		$res = $ticket->db->query("SELECT $fields FROM `$table_tickets` WHERE id = '%u' LIMIT 1", $id) or die($ticket->db->error());
		$row = $res->fetch_assoc();
		return $row;
	}
	
	//------------------------- Проверка прав доступа к тикету ------------------------------------------
	function check_access_to_ticket($ticket, $mail=NULL, $hashed_password=NULL)
	{
		//проверка переменных
		if(!is_numeric($ticket['id'])) 		return false;
		if(!is_numeric($ticket['from_id'])) 	return false;
		if($hashed_password==NULL) $hashed_password='';
		return (
				(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ticket['from_id'] && $ticket['password'] == $hashed_password) ||
				($mail!=NULL && $ticket['from_mail']==$mail && $ticket['password'] == $hashed_password) ||
				(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ticket['to_id'] && $ticket['password'] == $hashed_password) ||
				$ticket['to_id'] == -1 || // для всех
				$ticket['id'] == 0 ||
				check_right('TKT_SUPPORT_ANS')
				);
	}
	//--------------------- операции по добавлению, удалению и редактированию вопросов в тикете ---------
	function update_ticket( $ticket_id, $mail=NULL, $hashed_password=NULL )
	{
		if(!is_numeric($ticket_id)) return false;
		global $ticket;
		/*
		параметры POST:
		text_question 		- текст вопроса
		new_status 			- изменить статус тикета с ticket_id
		edit_question 		- редактируемое сообщение
		del_question		- удаляемое сообщение
		loadfile_question 	- прикрепляемый файл
		*/
		if(isset($_POST['text_question']) && !empty($_POST['text_question'])) $post_text = $_POST['text_question']; else $post_text = NULL;
		if(isset($_POST['edit_question']) && is_numeric($_POST['edit_question'])) $edit_question = (int)$_POST['edit_question']; else $edit_question = NULL;
		if(!isset($_FILES['loadfile_question'])) $_FILES['loadfile_question']=NULL;
		//меняем статус
		if(isset($_POST['new_status']))
			{
			if( !call_user_func($ticket->change_ticket_status, $ticket_id, $_POST['new_status'], $mail, $hashed_password))
				show_msg(NULL,'Сменить статус тикета не удалось',MSG_WARNING, MSG_NO_BACK);
			}
		//добавляем, редактируем вопрос
		if( $post_text !== NULL )
			{
			$ret = call_user_func($ticket->add_question, $ticket_id, $edit_question, $post_text, $_FILES['loadfile_question'], $mail, $hashed_password);
			if(!is_int($ret)) show_msg(NULL, $ret, MSG_WARNING, MSG_NO_BACK);
			unset($_POST['edit_question']);
			}
		//удаление вопроса
		if(isset($_POST['del_question']) && is_numeric($_POST['del_question']))
			{
			if(call_user_func($ticket->del_question, $_POST['del_question']))
				;//show_msg("Выполнено","Сообщение успешно удалено", MSG_INFO, MSG_OK);
			else
				show_msg(NULL,"Сообщение не удалено", MSG_WARNING, MSG_NO_BACK);
			}
		return true;
	}
	
	//-------------------- Вывод формы добавления/редактирования вопроса --------------------------------
	function add_question_form( $ticket_id, $mail=NULL, $hashed_password=NULL)
	{
		if(!is_numeric($ticket_id)) return false;
		$ticket_id = (int)$ticket_id;
		global $table_tickets, $ticket, $table_questions;
		//обработка переменных
		if(!is_numeric($ticket_id)) return false;
		$mail = htmlspecialchars($mail,ENT_QUOTES);		// в запросах в БД не учавстнует, а только выводится на экран
		
		//от кого
		$row = &ticket($ticket_id, 'id,from_id,to_id,from_mail,password');
		if( check_access_to_ticket($row, $mail, $hashed_password) )
			{ 
			?>
			<form method="POST" enctype="multipart/form-data" name="send_ticket" style="text-align: left">
				<input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
				<?php if(isset($_POST['edit_question']) && is_numeric($_POST['edit_question'])){
								//проверка прав
								if( !check_right('TKT_DEL_MODF_MSG',R_MSG) ) //$ques['from_id']==$_SESSION['user_id'] || 
									return false;	
								// получаем текст обновляемого вопроса
								$res2 = $ticket->db->query("SELECT text FROM `$table_questions` WHERE id = '%d'",$_POST['edit_question']) or die($ticket->db->error());
								$ques = $res2->fetch_assoc();
								?>
								<input type="hidden" name="edit_question" value="<?php echo $_POST['edit_question']; ?>" />
								<?php } ?>
				<p align='center'>
				<textarea id='question_box' rows="5" name="text_question" cols="72"	><?php if(isset($ques['text']))
							echo str_replace("<br>","\r\n",htmlspecialchars($ques['text'],ENT_QUOTES));
				?></textarea></p>
				<p align='center'><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo 1000000*MAX_FILE_SIZE;?>" />
				<input type="file" name="loadfile_question" size="46" />&nbsp;
				<span class='info_text'>Файлы до <?php echo MAX_FILE_SIZE;?> МБ</span>
				</p><br><p align='center'><input class='mybutton' type="submit" value="Отправить" name="B1" /></p>
			</form>
			<?php 
			return true;
			}
		else
			{
			show_msg(NULL, "У вас нет доступа.",MSG_CRITICAL);
			return false;
			}
	}
	//-------------------- Вывод формы добавления тикета ------------------------------------------------
	function add_ticket_form( $mail=NULL )
	{ ?>
		<div align="center">
		<form method="POST" action="<?php echo url(NULL, 'TICKET', 'add_ticket_act');?>" enctype="multipart/form-data" name="send_ticket" style="text-align: left">
			<table  id="table1" style="border-width: 0px" align="center">
				<input type="hidden" name="thematic" value="HOW" />
				<!--tr>
					<td style="border-style: none; border-width: medium" align="right">
						Категория:
					</td>
					<td style="border-style: none; border-width: medium" align="left">
								<select size="1" name="thematic">
								<option selected value="HOW">Вопросы по работе системы</option>
								<option value="MONEY">Вопросы связанные с оплатой</option>
								<option value="OTHER">Без категории</option></select>
					</td>
				</tr-->
				<tr>
					<td style="border-style: none; border-width: medium;" align="left">
					 <input type="text" name="headline" style="color:#888;" size="45" value="Тема" 
					 onfocus="this.value='';this.style.color='black';"
					 onblur="this.style.color='#888'; if(this.value=='')this.value='Тема'"
					 /></td>
				</tr>
				<?php if(!isset($_SESSION['user_id']))
								{ ?>
				<tr>
					<td style="border-style: none; border-width: medium" align="left">
					<font color="#006600">
					<input type="text" style="color:#888;" name="mail" size="45" value="<?php if($mail!=NULL) echo $mail; else "e-mail"; ?>" 
					 onfocus="this.value='';this.style.color='black';"
					 onblur="this.style.color='#888'; if(this.value=='')this.value='e-mail'"
					/></font>
					</td>
				
				</tr>
				<tr>
					<td style="border-style: none; border-width: medium" align="left">
					<font color="#006600">
					<input type="text" style="color:#888;" name="password" size="45" value="Задать пароль доступа" 
					 onfocus="this.value='';this.style.color='black';this.type='password';"
					 onblur="this.style.color='#888'; if(this.value==''){this.value='Задать пароль доступа';this.type='text';}"
					/></font>
					</td>
				</tr>
				<?php }?>
			</table>
							<p style="text-align: center">
							<textarea rows="5" name="text" cols="72" style="color:#888;"
							onfocus="this.value='';this.style.color='black';"
							onblur="this.style.color='#888'; if(this.value=='')this.value='Ваш вопрос'"
							>Ваш вопрос</textarea></p>
			<p style="text-align: center">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo 1000000*MAX_FILE_SIZE;?>" />
			<input type="file" name="loadfile" size="46">&nbsp;
			<span class='info_text'>Файлы до 10 МБ</span>
			</p>
			<br>
			<p align='center'><input class='mybutton' type="submit" value="Отправить" name="B1" style="float: center" /></p>
		</form>
		</div>
	<?php
	}
	
	//---------------------------- Добавление тикета --------------------------------------------------------------------
	// при успешном выполнении возвращает  id созданного тикета
	function add_ticket($thematic, $headline, $text='', $loadfile=NULL, $mail=NULL, $password=NULL, $to_id=0, $from_id = NULL)
	{
		global $_, $ticket, $table_tickets;
		if(!is_int($to_id)) return $_('Не верный адресат');
		//кто обратился?
		if(isset($from_id) && is_int($from_id)){
			$from = $from_id;
			$from_mail = '';
			$password = '';
		}
		else if(isset($_SESSION['user_id']))
			{
			$from = (int)$_SESSION['user_id'];
			$from_mail = '';
			$password = '';
			}
		else if($mail!=NULL  && ereg("^[a-zA-Z_0-9\-]{1,}@[a-zA-Z_0-9\-]{1,}\.",$mail) && $password!=NULL)
			{
			$from = -1;
			$from_mail = $mail;
			$password = md5($password);
			}
		else
			return $_('Вы не указали правильную почту для обратной связи и/или пароль доступа к запросу');

		//формируем запрос в систему тикетов
		$query = "INSERT INTO `$table_tickets`
				(headline,thematic,from_id,from_mail,password,to_id,reg_date,last_visit,status)
				VALUES('%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				CURRENT_TIMESTAMP(),
				CURRENT_TIMESTAMP(),
				'NEW')";
		$ticket->db->query($query,array($headline, $thematic, $from, $from_mail, $password, $to_id)) or die($ticket->db->error());
		$ticket_id = $ticket->db->insert_id;
		$return = $ticket_id; // вернем, если не будет ошибок
		
		//добавляем вопрос
		if($text!='') {
			$ret = call_user_func($ticket->add_question, $ticket_id, NULL, $text, $loadfile, $mail, $password); 
			if(!is_int($ret)) $return = $ret;
			$ticket->db->query("UPDATE `$table_tickets` SET  status = 'NEW' WHERE id = '%u'", $ticket_id) or die($ticket->db->error());
			}
		return $return;
	}
	
	//--------------------------------- Удаление сообщения ---------------------------------------------------------------
	function del_question($id)
	{
		global $ticket, $table_questions;
		if(!is_numeric($id)) return false;
		if ( !check_right('TKT_DEL_MODF_MSG',R_MSG)) return false; //проверка прав
		$ticket->db->query("DELETE FROM `$table_questions` WHERE id = '%d'",$id) or die($ticket->db->error);
		$file_name = TICKET_FILE.'/'.$id.'.rar';
		@unlink($file_name);
		return true;
	}
	
	//-------------------------------- смена статуса тикета ------------------------------------------------------------------
	function change_ticket_status($ticket_id, $new_status, $mail=NULL, $hashed_password=NULL)
	{
		// проверка переменных
		if(!is_numeric($ticket_id)) 		return false;

		$ticket_id = (int)$ticket_id;
		global $ticket, $table_tickets;	
		if($hashed_password == NULL) $hashed_password='';
		$row = &ticket($ticket_id, "from_id, from_mail, password");
		if ( 
			(isset($_SESSION['user_id']) && $row['password']==$hashed_password && $_SESSION['user_id'] == $row['from_id'] && ($new_status=='OPENED' || $new_status=='CLOSED')) || 	// владелец тикета
			($mail!=NULL && $row['password']==$hashed_password && $row['from_mail']==$mail && ($new_status=='OPENED' || $new_status=='CLOSED'))	||									// владелец тикета
			check_right('TKT_CNG_STATUS')
			)
			{
			$ticket->db->query("UPDATE `$table_tickets` SET  status = '%s' WHERE id = '%u'", array($new_status, $ticket_id) ) or die($ticket->db->error());
			return true;
			}
		return false;
	}
	
	
	//------------------------------- добавление/замена вопроса -------------------------------------------------------
	// при успешном выполении возвращает номер добавленного\измененного сообщения
	function add_question($ticket_id, $question_number=NULL, $text='', $loadfile=NULL, $mail=NULL, $hashed_password=NULL )
	{
		global $ticket, $table_tickets, $table_questions, $_;
		//обработка переменных
		if(!is_numeric($ticket_id)) 		return $_('Не верный номер тикета');
		if(!is_numeric($question_number) && $question_number!=NULL)	return $_('Не верный номер вопроса');
		if($text=='')						return $_('Не верно составлен запрос. Заполните корректно поля.');
		
		$ticket_id = (int)$ticket_id;
		$question_number = (int)$question_number;
		$row = &ticket($ticket_id, 'id,from_id,to_id, from_mail, password');
		if(  check_access_to_ticket($row, $mail, $hashed_password) )
			{
			//кто обратился?
			if(isset($_SESSION['user_id']))
				$from = $_SESSION['user_id'];
			else if($mail!=NULL && ereg("^[a-zA-Z_0-9\-]{1,}@[a-zA-Z_0-9\-]{1,}\.",$mail))
				$from = -1;
			else if($ticket_id == 0)	//это отзыв
				$from = -1;
			else
				return $_('Вы не указали e-mail для обратной связи.');

			//загрузка файла
			$ext ="";
			$file_temp = "";
			if($loadfile!=NULL && $loadfile['name']!='')
				{
				$point_pos = strpos($loadfile['name'], '.', strlen($loadfile['name'])-8);
				$ext =substr($loadfile['name'],$point_pos, strlen($loadfile['name'])-$point_pos);
				//if ($ext=='.rar')
					{
					if($loadfile['size'] > MAX_FILE_SIZE*1024*1024)
							return $_('Размер файла превышает').' '.MAX_FILE_SIZE. ' MB';
					$file_temp = TICKET_FILE."/_temp_".(int)($ticket_id.rand()).$ext;
					if(copy($loadfile['tmp_name'],$file_temp))   //загружаем временный файл
						$issetFile = 'YES'; //echo "Файл загружен<br>";
					else
						return $_('Возникла ошибка при загрузке файла. Проверьте интернет соединение.');
					}
				/*else
					return $_('Ошибка. Разрешены только файлы Win-Rar (.rar).');*/
				
				}
			else
				$issetFile = 'NO'; //файла нет
			
			// к кому обращаемся в сообщении
			if(preg_match('/^([A-Za-z0-9\-_]+),/', $text, $name)) {
				$to_id = get_user_id($name[1]); 
				$text = str_replace($name[0], '', $text); 
				}
			else
				$to_id = NULL;
				
			// Проверяем все ли цитируем сообщения только из этих тикетов?
			if(preg_match_all('/\[Quote#([0-9]+)\]/', $text, $quotes))
				foreach($quotes[1] as $quote){
					$question  = $ticket->db->row($table_questions, (int)$quote, 'ticket_id');
					if($question){
						if($ticket_id != $question['ticket_id'])
							return $_('Цитата не на сообщение этого тикета');
						}
					else
						return $_("Цитата на несуществующее сообщение");
					}
			
			//изменяем статус - формируем запрос в систему тикетов
			$query = "UPDATE `$table_tickets`
					SET `last_visit` = CURRENT_TIMESTAMP(), ";
			if ( check_right('TKT_SUPPORT_ANS') )
				$query .= " status = 'ANSWERED' ";
			else
				$query .= " status = 'OPENED' ";
			$query.= " WHERE id = '%u'";
			$ticket->db->query($query,$ticket_id) or die($ticket->db->error());
			//добавляем/редактируем сообщение
			if($question_number != NULL)
				{
				if ( !check_right('TKT_DEL_MODF_MSG',R_MSG)) return $_('У вас нет прав на редактирование'); //проверка прав на редактирование
				//date =	CURRENT_TIMESTAMP()
				$query = "UPDATE `$table_questions`
					SET
					text = '%s',
					file = '%s'
					WHERE id='%u'";
				$ticket->db->query($query,array($text, $issetFile, $question_number)) or die($ticket->db->error());
				$id = $question_number;
				}
			else
				{
				$fields = '';
				$value = '';
				$values = array($ticket_id, $from, $text, $issetFile);
				if($to_id!==NULL){
					$fields = ',to_id';
					$value = ",'%u'";
					$values[] = $to_id;}
				$query = "INSERT INTO `$table_questions`
					(ticket_id,from_id,text,file,date{$fields})
					VALUES('%u', 
					'%d',
					'%s',
					'%s',
					CURRENT_TIMESTAMP(){$value})";
				$ticket->db->query($query, $values) or die($ticket->db->error());
				$id = $ticket->db->insert_id;
				}
			//переименовываем временный файл
			if($loadfile!=NULL && $loadfile['name']!='')
				{
				$file_name = TICKET_FILE.'/'.$id.$ext;
				@unlink($file_name);
				rename($file_temp,$file_name);
				}
			//отсылаем ответ службы поддержки(администратора) по почте
			if( check_right('TKT_SUPPORT_ANS')  )
				{
				if( $row['from_mail']!='' )
					exec_script('http://'.$_SERVER['SERVER_NAME'].url(NULL, 'TICKET', 'admin/mailer'), array('name'=>'ans_for_ticket', 'from_mail'=>$row['from_mail'],'ticket_id'=>$ticket_id));
				if($row['from_id']!=-1){ // зарегистрированный пользователь
					$usr = get_user((int)$row['from_id'], 'mail');
					if($usr != NULL)	// нет такого пользователя
						if($usr['mail'] != '') 
							exec_script('http://'.$_SERVER['SERVER_NAME'].url(NULL, 'TICKET', 'admin/mailer'), array('name'=>'ans_for_ticket', 'user_mail'=>$usr['mail'],'ticket_id'=>$ticket_id));
					}
				}
			else //отсылаем письмо службе поддержки о новом сообщении
				exec_script('http://'.$_SERVER['SERVER_NAME'].url(NULL, 'TICKET', 'admin/mailer'), array('name'=>'new_question', 'ticket_id'=>$ticket_id));

			// отсылаем письмо тому, к кому обращались в сообщении
			if($to_id != NULL)
				exec_script('http://'.$_SERVER['SERVER_NAME'].url(NULL, 'TICKET', 'admin/mailer'), array('name'=>'write_to_user', 'to_id'=>$to_id, 'ticket_id'=>$ticket_id));
			
			return $id;
			}
		else
			return $_('У вас нет доступа');
		return $_('Не определенная ошибка');
	}
	
	//--------------------------------- Вывод тикета в строку -------------------------------------------------------------------
	/* $ticket - номер тикета, в противном случае выводим все
		$from 	- с какой страницы отображать ($tkt = NULL)
		
		GET:
		sort	- как сортировать ('asc', 'desc')
		sortby	- по какому полю сортировать
		status - отобразить тикеты с данным статусом
		
		POST:
		status - отобразить тикеты с данным статусом
	*/
	function show_tickets($tkt = NULL, $mail=NULL, $password=NULL)
	{
		global $_, $table_tickets, $ticket, $URL;
		$showMes = false;
		// проверка переменных
		if(is_numeric($tkt)) {$tkt = &ticket((int)$tkt); $showMes = true;}
		else if($tkt==NULL);
		else return $_('Тикет не найден');
		
		if(isset($_GET['sort']) && ($_GET['sort']=='asc' || $_GET['sort']=='desc')) $sort = $_GET['sort']; else $sort = 'desc';
		if(isset($_GET['sortby'])) $sortby = $_GET['sortby']; else $sortby = 'id';
		if(isset($_POST['status'])) { $_GET['status'] = $_POST['status']; $URL['ARGS']=add_arg('status='.$_POST['status']);}
		if(isset($_GET['status'])) $status = $_GET['status']; else $status = NULL;

			//ввод почты для нерегистрированных пользователей
		if(!isset($_SESSION['user_id']) && isset($tkt['from_id']) && $tkt['from_id']==-1 && ( $mail==NULL || !ereg("^[a-zA-Z_0-9\-]{1,}@[a-zA-Z_0-9\-]{1,}\.",$mail))){ ?>
			<script lang="Java">
				var res = prompt('Для связи с Вами введите корректно Ваш e-mail. Например: example@mail.ru');
				if(res!=null)
					location.href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], $URL['ARGS'].'&mail=');?>"+res;
			</script>
			<?php 
			return;
			}
		elseif (!isset($_SESSION['user_id']) && isset($tkt['from_id']) && $tkt['from_id']!=-1 ){ // не залогинен, но запрос шел от зарегистрированого 
			$_SESSION['QUERY_STRING'] = url(NULL, $URL['MODULE'],$URL['FILE'],$URL['ARGS'],$URL['PAGE']);
			$INFO = "<div class='info_text'>Для просмотра сообщений введите логин-пароль</div>";
			require(get_request_file(url(NULL,'USERS','in')));
			return '';	// произошла ошибка, но сообщение об шибке не выдавать, т.к. оно уже выдано
			//return 'Для просмотра сообщений '.url('Войдите в систему','USERS','in').'<br>После входа Вы автоматически попадете на эту станицу';
			}
			
		if( $tkt!=NULL && $tkt['password']!=$password && !check_right('TKT_SUPPORT_ANS') ) // если выводим конкретный тикет
			{
			?>
			<script type="text/JavaScript" src="<?php echo JS_ROOT_REL;?>/md5.js"></script>
			<script lang="Java">
				var pass = prompt('Введите пароль доступа');
				if(pass != null)
					{
					location.href="<?php 
										$args = 'ticket_id='.$tkt['id'];
										if($mail!=NULL) $args .= "&mail=".$mail;
										echo url(NULL, $URL['MODULE'], $URL['FILE'], $args);
										?>&password="+MD5(pass);
					}
				else
					location.href="<?php
										$args = 'ticket_id='.$tkt['id'];
										if(isset($mail)) $args .= "&mail=".$mail;
										echo url(NULL, 'TICKET', 'tickets', $args); ?>";
			</script>
			<?php
			return true;
			}
		
		//выводим список тикетов
		if( $tkt == NULL)
			{
			if ($status!=NULL && $status != -1) $query = " AND  status = '%s' ";
				else $query = '';
			$ticket->on_page = (int)$ticket->on_page;
			if(empty($URL['PAGE']))
					$p=1;
				else
					$p=$URL['PAGE'];
			$from = ($p-1)*$ticket->on_page;
			if ( check_right('TKT_SUPPORT_ANS'))	// служба поддержки
				{
				$vars=array();
				if($query!='') $vars[] = $status;				
				$res = $ticket->db->query("SELECT * FROM `$table_tickets` WHERE `thematic` != 'HIDDEN'".$query."  ORDER BY `%s` $sort LIMIT {$from},{$ticket->on_page} ", array_merge($vars,array($sortby))) or die($ticket->db->error());	
				$res2 = $ticket->db->query("SELECT count(id) FROM `$table_tickets` WHERE `thematic` != 'HIDDEN'".$query, $vars) or die($ticket->db->error());
				$row = $res2->fetch_assoc();
				$max = $row['count(id)'];
				}
			else if(isset($_SESSION['user_id']))	// зарегистрированный пользователь
				{
				$vars = array($_SESSION['user_id']);
				if($query!='') $vars[] = $status;
				$res = $ticket->db->query("SELECT * FROM `$table_tickets` WHERE `thematic` != 'HIDDEN' AND `from_id` = '%d'".$query."  ORDER BY  `%s` $sort  LIMIT {$from},{$ticket->on_page} ", array_merge($vars,array($sortby))) or die($ticket->db->error());
				$res2 = $ticket->db->query("SELECT count(id) FROM `$table_tickets` WHERE `thematic` != 'HIDDEN' AND `from_id` = '%d'".$query, $vars) or die($ticket->db->error());
				$row = $res2->fetch_assoc();
				$max = $row['count(id)'];
				}
			else if($mail!=NULL && preg_match("/^[a-zA-Z_0-9\-]+@[a-zA-Z_0-9\-]+\./",$mail))  // пользователь идентифицируется по мылу
				{
				$vars = array($mail);
				if($query!='') $vars[] = $status;
				$res = $ticket->db->query("SELECT * FROM `$table_tickets` WHERE `thematic` != 'HIDDEN' AND `from_mail` = '%s'".$query."  ORDER BY  `%s` $sort  LIMIT {$from},{$ticket->on_page} ", array_merge($vars,array($sortby)))  or die($ticket->db->error());
				$res2 = $ticket->db->query("SELECT count(id) FROM `$table_tickets` WHERE `thematic` != 'HIDDEN' AND `from_mail` = '%s'".$query, $vars)  or die($ticket->db->error());
				$row = $res2->fetch_assoc();
				$max = $row['count(id)'];
				}
			else
				return $_('У Вас нет прав на просмотр тикетов');
			}

		if( isset($res) || check_access_to_ticket($tkt, $mail, $password)	) //выводим тикеты конкретного человека или проверяем права на выбранный тикет
			{
			if($sort=='desc') $sort2='asc';else $sort2='desc';
			?>
			<div align="center">
				<table class='table_tickets styled_table'>
					<tr class='table_header'>
						<th align="center">
							<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=id') );?>">Номер</a></span>
							<?php if( $sortby == 'id'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>
						</th>
						<th align="center">
							<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=headline') );?>">Тема</a></span>
							<?php if( $sortby == 'headline'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>					
						</th>
						<th align="center">
							<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=thematic') );?>">Категория</a></span>
							<?php if( $sortby == 'thematic'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>						
						</th>
						<th align="center">
							<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=reg_date') );?>">Добавлен</a></span>
							<?php if( $sortby == 'reg_date'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>
						</th>
						<th align="center">
							<span id="head_id4"><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=status') );?>">Статус</a></span>
							<span id="head_id4_chg" style="display:none;width:50px;height:30px;">
							<?php
							if(isset($res)) 
								{
								?>
								<form id="show_ticket_by_status1" method="POST" action="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], $URL['ARGS']);?>" name="see_ticket" style="text-align: left;display:inline;">
									<select name="status" 
										onchange="javascript: document.getElementById('show_ticket_by_status1').submit()"
										onBlur="javascript: document.getElementById('head_id4_chg').style.display='none'; document.getElementById('head_id4').style.display = 'inline'"
									>
										<option <?php if($status=='-1')echo "selected";?> value='-1'>Все</option>
										<option <?php if($status=='NEW')echo "selected";?> value='NEW'>Новые</option>
										<option <?php if($status=='OPENED')echo "selected";?> value='OPENED'>Открытые</option>
										<option <?php if($status=='ANSWERED')echo "selected";?> value='ANSWERED'>Ответ дан</option>
										<option <?php if($status=='CLOSED')echo "selected";?> value='CLOSED'>Закрытые</option>
									</select>
								</form>
								<?php } ?>
							</span>
							<span style="cursor:pointer;" onClick="javascript: var obj = document.getElementById('head_id4'); 
															var stl = obj.style.display;
															obj.style.display = document.getElementById('head_id4_chg').style.display;
															document.getElementById('head_id4_chg').style.display = stl">+
							</span>
							<?php if( $sortby == 'status'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>
						</th>
						<th align="center">
							<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=last_visit') );?>">Изменен</a></span>
							<?php if( $sortby == 'last_visit'){ ?>
							<span style="cursor:pointer;"
							onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }" 
							onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
							><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
							<?php } ?>
						</th>
					</tr>
					<?php
					if(isset($res)) $tkt = $res->fetch_array();
					// Если тикетов нет
					if(!$tkt){
						?>
						<script lang="javascript">
							RUN_AFTER_LOAD += "submenu('new_ticket');";
						</script>
						<?php
					}
					$flag = true;
					while($tkt && $flag)
						{
						?>
						<tr class='tr_ticket_other'>
							<td align="center" style="text-align:center;"><?php 
													$args = 'ticket_id='.$tkt['id'];
													if($mail!=NULL) 
														$args .= "&mail=".$mail;
													echo url($tkt['id'], 'TICKET', 'dialog', $args); 
												?>
							</td>
							<td align="center"><?php echo htmlspecialchars($tkt['headline'],ENT_QUOTES); ?></td>
							<td align="center">
								<?php
									if($tkt['thematic']=='HOW')
										echo "<span style='color:#0000AA;'>Работа системы</font>";
									else if($tkt['thematic']=='MONEY')
										echo "<span style='color:#AA0000;'>Оплата</font>";
									else if($tkt['thematic']=='OTHER')
										echo "<span style='color:#00AA00;'>Без категории</font>";
									else
										echo $tkt['thematic'];
									?>
							</td>
							<td align="center"><?php echo date(DATE_TIME_FORMAT,strtotime($tkt['reg_date'])); ?></td>
							<td align="center">
							<?php if (check_right('TKT_CNG_STATUS')) 
								{ // имеет право изменять статус 
								?>
								<form id="ticket_status_id<?php echo (int)$tkt['id']; ?>" method="POST"  name="see_ticket" style="text-align: center;margin:0px;">
									<?php if(isset($_POST['status'])){ ?><input type='hidden' name='status' value='<?php echo htmlspecialchars($_POST['status'],ENT_QUOTES); ?>'><?php } ?>
									<input type='hidden' name='ticket_id' value='<?php echo (int)$tkt['id']; ?>'>
									<select align='center' name="new_status" onchange="javascript: document.getElementById('ticket_status_id<?php echo (int)$tkt['id']; ?>').submit()">
										<option <?php if($tkt['status']=='NEW')echo "selected";?> value='NEW'>Новый</option>
										<option <?php if($tkt['status']=='OPENED')echo "selected";?> value='OPENED'>Открыт</option>
										<option <?php if($tkt['status']=='ANSWERED')echo "selected";?> value='ANSWERED'>Ответ дан</option>
										<option <?php if($tkt['status']=='CLOSED')echo "selected";?> value='CLOSED'>Закрыт</option>
									</select>
								</form>
								<?php
								} 
							// владелец меняет статус
							elseif(  
										(isset($_SESSION['user_id']) && $tkt['password']==$password && $_SESSION['user_id'] == $tkt['from_id']) ||
										($mail!=NULL && $tkt['password']==$password && $tkt['from_mail']==$mail)
										)
								{
								/*
								action="<?php 
									if($mail!=NULL) 
										$args = "mail=".$mail.'&password='.$password;
									else
										$args = NULL;
									echo url(NULL, $URL['MODULE'], $URL['FILE'], $args); 
								?>"
								*/
								?>
								<form id="ticket_status_id<?php echo (int)$tkt['id']; ?>" method="POST" name="see_ticket" style="text-align: center;margin:0px;">
									<input type='hidden' name='ticket_id' value='<?php echo $tkt['id']; ?>'>
									<select align='center' name="new_status" onchange="javascript: document.getElementById('ticket_status_id<?php echo (int)$tkt['id']; ?>').submit()">
										<option <?php if($tkt['status']!='OPENED')echo "selected";?> value='OPENED'>Открыт</option>
										<option <?php if($tkt['status']=='CLOSED')echo "selected";?> value='CLOSED'>Закрыт</option>
									</select>
								</form>					
								<?php }
							else // по умолчанию
								{
								if($tkt['status'] == "OPENED")
									echo "<span style='color:blue;'>Ожидается ответ</font>";
								else if($tkt['status'] == "CLOSED")
									echo "<span style='color:#800080;'>Закрыт</font>";
								else if($tkt['status'] == "ANSWERED")
									echo "Ответ дан";
								else if($tkt['status'] == "NEW")
									echo "<span style='color:red;'>Новый</font>";
								else
									echo $tkt['status'];
								} ?>
							</td>
							<td align="center"><?php echo date(DATE_TIME_FORMAT,strtotime($tkt['last_visit'])); ?></td>
						</tr>
						<?php 
						if(isset($res))$tkt = $res->fetch_array();	//следующий тикет
							else $flag = false;
						} ?>
				</table>
				<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
					if(isset($res)) get_table_nav($URL['MODULE'], $URL['FILE'], 'status='.$status, $max, $ticket->on_page); ?>
			</div>
			<p></p>
			<?php
			if( $showMes ) show_messages($tkt, $mail, $password); // показываем сообщения
			return true;
			}
		else
			return $_('У вас нет доступа');
	}
	
	//--------------------------------- Страница c сообщениями ------------------------------------------------------------------
	// $row - передаем строку из базы икета или номер тикета
	function show_messages($row, $mail = NULL, $password=NULL)
	{	
		global $ticket, $_, $table_tickets, $table_questions, $users, $table_users;
		// проверка переменных
		if(is_array($row));
		else if(is_numeric($row)) $row = &ticket((int)$row);
		else return $_('Тикет не найден');

		$ticket_id = $row['id'];
		if(	check_access_to_ticket($row, $mail, $password)	)
			{
			//вывод сообщений
			$res2 = $ticket->db->query("SELECT * FROM `$table_questions` WHERE ticket_id = '%d' ORDER BY date",$ticket_id) or die($ticket->db->error());
			?>
			<table class='table_dialog'>
				<?php
					while($row2 = $res2->fetch_assoc()) // перебор сообщений в тикете
						{
						?>
						<tr class='tr_dialog' id='msg_<?php echo $row2['id'];?>'>
							<td class='td_who'>
							<?php
								echo "<div class='avatar_box'>"; 
									output_avatar($row2['from_id']);
								echo "</div>";
								$user_login="";
								$user_link = "Незнакомец";
								if($row2['from_id']!=-1){
									$tmp = get_user($row2['from_id'],'login');
									$user_login = $tmp['login'];
									$user_link = get_user_link($row2['from_id']);
								}
								?>
								<div class='user_name_box'>
									<span class='msg_user_name'>
										<?php echo $user_link; ?>
									</span>
								</div>
								<div class='date_box'><?php echo date(DATE_TIME_FORMAT,strtotime($row2['date']));?></div>
							</td>
							<td class='td_content'>
								<?php
									// выводим сообщение
									echo process_msg_text($row2);
									// удаление, редактирование сообщения
									if(check_right('TKT_DEL_MODF_MSG')) //$row2['from_id']==$_SESSION['user_id'] || 
										{
										?>
										<div class='admin_buttons'>
												<form method="POST" enctype="multipart/form-data">
													<input type="hidden" name="edit_question" value="<?php echo $row2['id']; ?>" />
													<input type="submit" value=" [v] " name="send" title='Редактировать сообщение' style='position:absolute;right:30px;top:-30px;font-size:8px;cursor:pointer;'>
												</form>
												<form method="POST" enctype="multipart/form-data">
													<input type="hidden" name="del_question" value="<?php echo $row2['id']; ?>" />
													<input type="submit" value=" [x] " name="send" title='Удалить сообщение' style='position:absolute;right:0px;top:-30px;font-size:8px;cursor:pointer;'>
												</form>
										</div>
									<?php  } ?>
									<div class='user_buttons' id='user_button_id_<?php echo $row2['id'];?>'>
										<a id='a1' onClick="answer_to_msg('<?php echo $user_login;?>');">[Ответить]</a>
										&nbsp;&nbsp;&nbsp;
										<a onClick='quote_msg(this);'>[Цитировать]</a>
									</div>
							</td>
						</tr>
						<?php } ?>
			</table>
			<script type="text/javascript">
				// вставляем имя того, к кому обращаемся
				function answer_to_msg(login){
					if(login == '') return;
					var qestionBox = document.getElementById('question_box');
					qestionBox.value = qestionBox.value + login + ', ';
					qestionBox.focus();
				}
				// цитируем
				function quote_msg(Obj){
					var msg_num = Obj.parentNode.id.substring(15);
					var qestionBox = document.getElementById('question_box');
					qestionBox.value = qestionBox.value + '\n' + '[Quote#' + msg_num  + ']\n';
					qestionBox.focus();
				}
				// хук - абсолютное позиционирование в ячейках таблицы
				$.fn.absolute_positioning_for_td = function() {
					var $el;
					return this.each(function() {
						$el = $(this);
						var newDiv = $("<div />", {
							"class": "innerWrapper",
							"css"  : {
								"height"  : $el.height(),
								"width"   : "100%",
								"position": "relative",
								"padding" : 0,
								"margin"  : 0
							}
						});
						$el.wrapInner(newDiv);    
					});
				};
				$(".table_dialog .td_content").absolute_positioning_for_td();
			</script>
			<?php
			return true;
			}
		else
			return $_("У вас нет доступа");
	}
//----------------------- Обработка текста сообщения -----------------------------------------
// message - строка из таблицы $table_questions
function process_msg_text($message){
	global $ticket, $table_questions, $_;
	// заменяем все нехорошие символы введенные пользователем
	$text = $message['text'];
	// заменим "\n[" и "]\n" на [ и ]
	$text = preg_replace('/[\r\n]+\[/', '[', $text);
	$text = preg_replace('/\][\r\n]+/', ']', $text);
	// заменим переводы строк на <br>
	$text = str_replace("\r\n","<br>",htmlspecialchars($text,ENT_QUOTES));
	// Обращаемся к пользователю
	$to_id = $message['to_id'];
	if($to_id != NULL) $text = get_user_link($to_id). ', ' . $text;
	// обрабатываем цитаты
	// Проверяем все ли цитируем сообщения только из этих тикетов?
	if(preg_match_all('/\[Quote#([0-9]+)\]/', $text, $quotes))
		foreach($quotes[1] as $key=>$quote_id){
			$quote  = $ticket->db->row($table_questions, (int)$quote_id, 'id, ticket_id, to_id, text, file');
			if($quote){
				if($message['ticket_id'] != $quote['ticket_id']) return '<blockquote class="press">'.$_('Цитата не на сообщение этого тикета').'</blockquote>';
				// выводим цитаты
				$quote_text = process_msg_text($quote);
				$text = str_replace($quotes[0][$key], '<blockquote class="press">'.$quote_text.'</blockquote>', $text);
				}
			else
				return '<blockquote class="press">'.$_("Цитата на несуществующее сообщение").'</blockquote>';
			}
	//если есть файл
	if($message['file']=='YES'){
		$files = glob( TICKET_FILE . '/' . $message['id']. '.*');
		if(isset($files[0])){
			$point_pos = strpos($files[0], '.', strlen($files[0])-8);
			$ext = substr($files[0],$point_pos, strlen($files[0])-$point_pos);
			$text .= "
				<div class='attached'>
					Прикрепленный &nbsp;
					<a href='" . TICKET_FILE_RELATIVE . '/' . $message['id']. $ext . "'>файл</a>
				</div>";
			}
		}
	return $text;
}
//----------------------- возвращаем статус icq -----------------------------------------
// 0 - если человек  (  UIN )  не в сети ICQ, 
// 1 - если человек  (  UIN )   в сети ICQ,  
// 2 - неопределенное состояние 
function get_ICQ_status($uin) { 
	$path = 'http://status.icq.com/online.gif?icq=' . $uin . '&img=27'; 
	$page = _get_page($path); // если была ошибка подключения вернет false
	if($page===false) return 0;
	preg_match('|online([d]{1,2}).gif|si', $page, $matchs); 
    $return = ($matchs[1]) ? $matchs[1] : 0; 
    return $return; 
}	
//------------------------ Удаление тикета --------------------------------------------
function delete_ticket($id)
{
	if(!is_numeric($id)) return $_("Номер тикета должен быть числом");;
	$id = (int)$id;
	global $ticket, $table_tickets, $table_questions, $_;
	//проверка прав
	$res = $ticket->db->query("SELECT `from_id` FROM $table_tickets WHERE id='%u'", $id) or die($ticket->db->error());
	if(!($row = $res->fetch_assoc())) return $_("Тикет не найден");
	if($row['from_id'] != $_SESSION['user_id'] && !check_right('TKT_DEL_OTHERS_TICKET')) return $_("У вас нет прав на удаление тикета");

	// удалим файлы связанные с заданием
	$res = $ticket->db->query("SELECT `id` FROM `$table_questions` WHERE ticket_id='%u' AND `file`='YES'", $id) or die($ticket->db->error());
	while($row = $res->fetch_assoc()){
		$files = glob( TICKET_FILE.'/'.$row['id'].'.*'); 
		foreach($files as $file) unlink($file);
	}
	
	// удаляем вопросы и сам тикет из БД
	$ticket->db->query("DELETE FROM `$table_tickets` WHERE id='%u'", $id);
	$ticket->db->query("DELETE FROM `$table_questions` WHERE ticket_id='%u'", $id);	
	return true;
}
//---------------Вывод переписки, добавление сообщений, поле ввода сообщения --------------
function show_dialog($ticket_id){
	global $ticket;
	//обновляем тикет (удаление/добавление отзыва в $_POST переменной) 
	call_user_func($ticket->update_ticket, $ticket_id);
	//сообщения тикета
	$ret = call_user_func($ticket->show_messages, $ticket_id);
	if($ret !== true)
		show_msg(NULL, $ret, MSG_WARNING);
	else
		// вывод формы ввода вопроса
		call_user_func($ticket->add_question_form, $ticket_id);
}
?>
