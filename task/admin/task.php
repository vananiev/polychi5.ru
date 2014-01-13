<h1 id="page_title"><?php echo $URL['TITLE']; ?></h1>

<p class="about_page" id="info_how_task">
	<?php echo url('Ознакомиться с этой страницей', 'TASK', 'info/how_task');?>
</p>

<?php
	$show_task_page = require(SCRIPT_ROOT.'/'.$INCLUDE_MODULES['TASK']['PATH'].'/kernel/task_act.php');
	if(!$show_task_page) return;
	//получаем обновленные сведения о задании
	$query="SELECT * FROM `%s` WHERE id = '%d'";
	$vars = array($table_task,$id);
	$res = $task->db->query($query,$vars) or die($task->db->error());
	$row = $res->fetch_array();
?>

<div class='box_standart'>
<table class='table_task'>
	<div class='task_id'>
		<span class="task_id_simbol">Номер:</span>
		<span class="task_id_number"><?php echo $id; ?></span>
	</div>
	<div id="task_spacer"><div>
	<tr>
		<td class="task_header" id="task_status">Состояние:</td>
		<td class='task_content' id="task_status_value">
		<?php
			if(check_right('TSK_CNG_STATUS'))
				{
	/*--------------------------------------- Административная функция -----------------------------------------------------------------------------*/
				?>
				<form id="status_form" method="POST" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$id);?>">
					<select size="1" name="status" onchange="javascript: document.getElementById('status_form').submit()">
						<option <?php if($row['status']=='NEW')echo "selected";?> value="NEW">Новый</option>
						<option <?php if($row['status']=='GETS')echo "selected";?> value="GETS">Мои правила</option>
						<option <?php if($row['status']=='WAIT')echo "selected";?> value="WAIT">Идет решение</option>
						<option <?php if($row['status']=='SOLV')echo "selected";?> value="SOLV">Решен</option>
						<option <?php if($row['status']=='REMK')echo "selected";?> value="REMK">Перерешать</option>
						<option <?php if($row['status']=='OK')echo "selected";?> value="OK">Выполнен</option>
						<option <?php if($row['status']=='NSOL')echo "selected";?> value="NSOL">Не решен</option>
						<option <?php if($row['status']=='LOCK')echo "selected";?> value="LOCK">Заблокирован</option>
					</select>
				</form>
				<?php
	/*--------------------------------------- Административная функция -----------------------------------------------------------------------------*/
				}
			else
				{
				if($row['status'] =="REMK")
					echo "<span style='color:#CC0000;'><b>Перерешать</b></span>";
				else if($row['status'] == "NEW")
					echo "<span style='color:red;'>Новый</span>";
				else if($row['status'] == "GETS")
					echo "<span style='color:#FF0066;'>Мои правила</b></span>";
				else if($row['status'] == "WAIT")
					echo "<span style='color:blue;'>Идет решение</span>";
				else if($row['status'] == "SOLV")
					echo "Решен";
				else if($row['status'] == "OK")
					echo "<span style='color:#800080;'>Выполнен</span>";
				else if($row['status'] == "NSOL")
					echo "<span style='color:#800080;'>Не решен</span>";
				else if($row['status'] == "LOCK")
					echo "<span style='color:#800080;'>Ошибка заказа</span>";
		        else
		            echo $row['status'];
				}
		?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_section">Предмет:</td>
		<td class='task_content' id="task_section_value">
		<?php
			$section = $row['section'];
			$section = explode (' ', $section);
			foreach($section as &$word)
				$word = mb_substr($word,0, 11, SITE_CHARSET);
			$section = join(' ', $section);
			echo htmlspecialchars($section,ENT_QUOTES);
		?></td>
	</tr>
	<tr>
		<td class="task_header" id="task_subsection">Раздел:</td>
		<td class='task_content' id="task_subsection_value">
		<?php
			$subsection = $row['subsection'];
			$subsection = explode (' ', $subsection);
			foreach($subsection as &$word)
				$word = mb_substr($word,0, 36, SITE_CHARSET);
			$subsection = join(' ', $subsection);
			echo htmlspecialchars($subsection,ENT_QUOTES);
		?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_resolv_until">Решить до:</td>
		<td class='task_content' id="task_resolv_until_value">
		<?php
			//if($row['status']!='GETS')
				echo date(DATE_TIME_FORMAT,strtotime($row['resolve_until']));
			/*else
				{
				$r_until = strtotime($row['resolve_until']) - strtotime($row['add_date']);	//абстрагируемся от часового пояса
				$days = (int)($r_until/3600/24);
				$hours =  (int)(($r_until - $days*3600*24)/3600);
				echo $days. " дн., ".$hours." часов";
				echo "<div class='info_text' >на решение ч. с момента выбора решающего</div>" ;
				}*/
		?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_task">Задание:</td>
		<td class='task_content' id="task_task_value">

  		<?php
		if(!defined('TASK_LOCKED'))
			echo get_task_link($row['id']);
		else
			echo "<span class='info_text'>открыть</span>";
		 ?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_price">Стоимость:</td>
		<td class='task_content' id="task_price_value">
		<?php
		/***********************************************
        	Комиссия системы 9% с учеников и 9% с решающих.
        	Поэтому стимость для решающих на 18% ниже, чем для учеников.*/
		if(isset($_SESSION['user_id'])){
			if(($row['status']=='NEW' || $row['status']=='GETS')&&
					( $row['user']==$_SESSION['user_id'] || check_right('TSK_PRICING_OTHERS') )   )
				{
				//ученик, владелец задания, или администратор изменяют цену, если статус задания NEW или GETS
				?>
				<form method="POST" action="<?php echo url(NULL, $URL['MODULE'], 'task', 'id='.$row['id']); ?>">
					<input type="text" name="price" style='font-size:14px;width:52px;' value="<?php echo $row['price'];?>">
					<span style='position:relative; top:-5px;left:-25px;font-size:8px;'>руб</span>
					<input type="submit" value="Изменить цену" name="set">
				</form>
				<?php }
			elseif($row['status']=="GETS" && check_right('TSK_SOLVING'))
				{
				//подать заявку на решение
				$solvers = unserialize($row['select_solver']);
				for($i=0;isset($solvers[$i][0]);$i++)
					{
					if($solvers[$i][0] == $_SESSION['user_id'])
						{$price = price_after_all_comm($solvers[$i][1]);
						echo "<div class='predlozhenie'>Ваша заявка: цена - {$price} руб";
						//show_msg(NULL,"Вами была отослана заявка на решения: цена - {$price}",MSG_INFO,MSG_NO_BACK);
						?>
							<form method="POST" action="<?php echo url(NULL, $URL['MODULE'], 'task', 'id='.$row['id']); ?>" style='position:relative;width:100%;'>
								<input type="hidden" name="solver_price" value="-1">
								<input type="submit" value="[x]" name="send" title='Отказаться от решения' style='position:absolute;right:0px;top:-30px;font-size:8px;cursor:pointer;'>
							</form>
						</div>
						<?php
						}
					}
				?>
					<form method="POST" action="<?php echo url(NULL, $URL['MODULE'], 'task', 'id='.$row['id']); ?>">
						<input type="text" size="5" name="solver_price" value="<?php echo price_after_all_comm($row['price']);?>">
						<input type="submit" value="Подать заявку на решение." name="send">
						<br>
						<span class='info_text'> Начайте решение, после того как ВАС выберут решающим
							(на e-mal Вам придет сообщение.
							Или узнайте об этом на этой странице - появится грава 'Решающий' с вашим логином )
						</span>
					</form>
			<?php }
			else if( $_SESSION['user_id'] == 0)
				echo "<div class='price'>".$row['price']."<span class='price_rub'>руб</span></div>";
			else if( user_in_group('SOLVER') )
				echo "<div class='price'>".price_after_all_comm($row['price'])."<span class='price_rub'>руб</span></div>";
			else
				echo "<div class='price'>".$row['price']."<span class='price_rub'>руб</span></div>";   //все остальные случаи
			}
		else
			{echo "<div class='price'>".$row['price']."<span class='price_rub'>руб</span></div>";}
		?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_user">От:</td>
		<td class='task_content' id="task_user_value"><?php echo get_user_link($row['user']);	?></td>
	</tr>
	<tr>
		<td class="task_header" id="task_add_date">Добавлен:</td>
		<td class='task_content' id="task_add_date_value">

		<?php echo date(DATE_TIME_FORMAT,strtotime($row['add_date'])); ?>
		</td>
	</tr>
	<?php
	if($row['status']=="WAIT" ||
		$row['status']=="SOLV" ||
		$row['status']=="REMK" ||
		$row['status']=="OK" ||
		$row['status']=="NSOL" )
		{?>
		<tr>
			<td class="task_header" id="task_solver">Решающий:</td>
			<td class='task_content' id="task_solver_value"><?php echo get_user_link($row['solver']); ?>
			</td>
		</tr>
		<tr>
			<td class="task_header" id="task_agree_date">Решение начато:</td>
			<td class='task_content' id="task_agree_date_value">
				<?php echo date(DATE_TIME_FORMAT,strtotime($row['agree_date'])); ?>
			</td>
		</tr>
		<?php
		}
	else if($row['status']=="GETS" &&		//выбор решающего
		isset($_SESSION['user_id']) &&
		($row['user']==$_SESSION['user_id'] || check_right('TSK_SELECT_SOLVER_OTHERS') ))
		{
		?>
		<tr>
			<td class="task_header" id="task_solvers">Решающий:</td>
			<td class='task_content' id="task_solvers_value">
					<?php
					$solvers = unserialize($row['select_solver']);
					if(isset($solvers[0][0]))
						{ ?>
						<form method="POST" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$id); ?>">
							<select id="solvers" name="solver">
							<?php
							for($i=0;isset($solvers[$i][0]);$i++)
								{
								$usrid = $solvers[$i][0];
								$row2 = get_user($usrid, 'login');
								?>
								<option  value="<?php echo serialize($solvers[$i]); ?>" userid="<?php echo $usrid; ?>" >
									<?php echo "{$row2['login']} : Цена - {$solvers[$i][1]} руб"?>
								</option>
								<?php } ?>
							</select>
							<input type="button" value="Инфо" id="about_user">
							<input type="submit" value="Выбрать" name="set">
						</form>
						<script type="text/javascript">
							$('#about_user').click( function () {
    							var usrid = $('#solvers').find('option:selected').attr('userid');
    							window.open(
  									'<?php  echo url(NULL, 'USERS', 'about_user'); ?>?user_id=' + usrid,
  									'_blank'
								);
  							});
						</script>
						<?php }
						else
							{
							echo "<span class='predlozhenie'>Нет заявок на решение</span>";
							//show_msg(NULL,"Нет заявок на решение<br><a href='#' onmouseup='javascript:submenu(this.parentNode);'>[ok]</a> ",MSG_INFO,MSG_RETURN);
							}
						?>
			</td>
		</tr>
		<?php }?>
	<?php if($row['status']=="OK" || $row['status']=="REMK" || $row['status']=="SOLV"){?>
	<tr>
		<td class="task_header" id="task_solv_date">Решен:</td>
		<td class='task_content'id="task_solv_date_value">
			<?php echo date(DATE_TIME_FORMAT,strtotime($row['solv_date'])); ?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_solv">Решение:</td>
		<td class='task_content' id="task_solv_link">

  		<?php
		if(!defined('TASK_LOCKED'))
			{
			$files = glob( SOLVING_ROOT.'/'.$row['id'].'.*');
			if(isset($files[0])){
				echo url('скачать', 'TASK', 'get_solving', 'id='.$id);
			}else{
				echo "<span class='info_text' style='color:red;font-size:12px;'>Ошибка! Решение не найдено. Если Вы Решающий попробуйте загрузить его еще раз. В случае повторной ошибки обратитесь в службу поддержки.</span>";
				show_msg("Критическая ошибка","Решение не найдено<br><a href='#' onmouseup='javascript:submenu(this.parentNode,0);'>[ok]</a>",MSG_CRITICAL,MSG_RETURN);
				}
			}
		else
			echo "<span class='info_text'>открыть</span>";
  		 ?>
		</td>
	</tr>
	<tr>
		<td class="task_header" id="task_rating">Оценка:</td>
		<td class='task_content' id="task_rating_value">
		<?php
		//решение можно менять в течении 7 дней, учеником, который задал задание или администратором
		if(strtotime($row['solv_date']) > (time()-7*24*3600)&&
			$row['status']!='OK' &&
			isset($_SESSION['user_id']) && ($row['user']==$_SESSION['user_id'] || check_right('TSK_RATING_OTHERS') ))
			{
			?>
			<form method="POST" id="rating_form" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$id); ?>">
		<select name="rating" onchange="javascript: document.getElementById('rating_form').submit()" style='width:auto;text-align:center;margin:1px 0;'>
				<option <?php if($row['rating']==0)echo "selected";?> value='0'>Я, не удовлетворен решением. Перерешать.</option>
				<option <?php if($row['rating']==1)echo "selected";?>>1</option>
				<option <?php if($row['rating']==2)echo "selected";?>>2</option>
				<option <?php if($row['rating']==3)echo "selected";?>>3</option>
				<option <?php if($row['rating']==4)echo "selected";?>>4</option>
				<option <?php if($row['rating']==5)echo "selected";?>>5</option>
				</select><!--input type="submit" value="Оценить" name="set"-->
			</form>
			<?php }
			else
			{
			if($row['rating']!=0)
				echo 0+$row['rating'];
			else
				echo "Я, не удовлетворен решением. Перерешать.";
			}
			 ?>
		</td>
	</tr>
	<?php } ?>
	<?php
	if(isset($_SESSION['user_id']) && $row['status']!="OK"){
		//согласиться на решения
		if( $row['status']=="NEW" && (check_right('TSK_SOLVING')|| check_right('TSK_SOLVING_OTHERS')) )
			{
			?>
			<tr>
				<td class='task_header'></td>
				<td class='task_content'>
					<form method="POST" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$row['id']); ?>">
				<input type="hidden" name="agree" value="<?php echo (int)$_SESSION['user_id'];?>"><input type="submit" value="Солгашаюсь решить задание и приступаю к решению" name="send"></p>
					</form>
				</td>
			</tr>
		<?php }
		//загрузка решения
		else if
			( ( $row['solver']==$_SESSION['user_id'] || check_right('TSK_SOLVING_OTHERS') ) &&
			($row['status']=="WAIT" || $row['status']=="REMK" || $row['status']=="SOLV")
			)
			{
			?>
			<tr>
				<td class='task_header'>Загрузить решение:</td>
				<td class='task_content'>
					<form method="POST" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$row['id']); ?>" enctype="multipart/form-data">
						<input type="file" name="filename" size="40" />
						<span class='info_text' style='font-size:8pt;'>
						Для отправки нескольких файлов используйте архивы (.rar, .zip)
						Размер файлов не более <?php echo MAX_FILE_SIZE;?> МБ.</span>
						<br>
						<input type="submit" value="Отправить решение" name="B1" />
					</form>
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
</table>
<?php
	if(($row['status']=='NEW' || $row['status']=='GETS') &&
		isset($_SESSION['user_id']) &&
		($row['user']==$_SESSION['user_id'] || check_right('TSK_DEL_OTHERS_TASK') ))
		{
		//удаление задания пользователем
		?>
		<form method="POST" action="<?php echo url(NULL,$URL['MODULE'], 'task', 'id='.$row['id']); ?>">
			    <p align = 'left'>
				<input type="hidden" name="delete_by_user" value="<?php echo $id;?>">
				<input type="submit" value="Удалить задание" name="send"></p>
		</form>
		<?php
		}
?>
<p align='center' id="back_to_tasks" >
	<?php
		if(user_in_group('USER')) $args = "user=".$_SESSION['user_id'];
		else if(user_in_group('SOLVER')) $args = "solver=".$_SESSION['user_id'];
		else $args = NULL;
		echo url('<< К таблице заданий', 'TASK', 'tasks', $args);
	?>
</p>
</div>
<?php
	// вывод диалога
	if( TASK_DIALOG )
		{
		$ret = call_user_func($task->show_task_dialog, $row);
		if( $ret !== true) show_msg(NULL, $ret, MSG_WARNING);
		}
?>
<style type="text/css">
.box_standart{
	width:600px;border:1px solid #FFA71F;margin:0 0 20px -300px; left:50%;
	}
.table_task	{
		margin:0 auto;
		font-family: "Lucida Sans", "Lucida Grande", "Lucida Sans Unicode", sans-serif;
		font-size:14px;
		}
.task_id{
		position:absolute;top:0;left:0;
		border-radius:20px; -moz-border-radius:20px; -webkit-border-radius:20px;
		background-color:#FFA71F;
		width:100%;
		width=104%;
		padding:30px 0;
		text-align:center;
		border:1px solid #B86F00;
		box-shadow: 0 0 2px black; /* Параметры тени */
		box-shadow: 0 0 5px rgba(0,0,0,0.5); /* Параметры тени */
		-moz-box-shadow: 0 0 5px rgba(0,0,0,0.5); /* Для Firefox */
		-webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5); /* Для Safari и Chrome */
		color:#222;

		}
.task_id_number{position:relative;top:8px; font-size:5em;text-shadow: grey 3px 1px 3px;}
.task_id_simbol{position:relative;top:-32px;color:#EEE;font-size:0.9em;}
#task_spacer{height:120px;}
.table_task td		{ border:0px solid #888;}
.task_content	{
		text-align:center;
		width:auto;
		padding:10px 70px;
		color:#333;
		}
.task_header	{
		 width:auto;
		 font-size:12px;
		 padding:0 30px;
		 color:#888;
		}
.price	{
		font-size:3.2em;color:#333;
		}
.price .price_rub	{
		position:relative; top:-27px;font-size:10px;
		}
.predlozhenie{
		border-radius:12px; -moz-border-radius:12px; -webkit-border-radius:12px; color:#EEE; font-weight:bolder;padding:8px ;background-color:#671C46;
		margin:1px;
		}
#task_section_value{
	font-size:4em;line-height:100%;text-shadow: grey 2px 1px 5px;color:#497CD0;font-weight:bold;
}
#task_subsection_value{ color:#4B8C24;font-size:1.2em;}
</style>
