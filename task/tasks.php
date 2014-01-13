<?php
		/********************************************************************
						Выдача таблицы заданий

		Параметры GET:
		user  - номер пользователя
		solver - номер решающего
		status == "NEW\" or status=\"GETS" - выдача результатов по статусу
		status - выдача результатов по статусу
		sortby - сортировать по

		POST:
		status - выдача результатов по статусу
		********************************************************************/
		if(isset($_GET['user']) && is_numeric($_GET['user'])) $user = (int)$_GET['user'];
		if(isset($_GET['solver']) && is_numeric($_GET['solver'])) $solver = (int)$_GET['solver'];
		if(isset($_POST['status'])) { $_GET['status'] = $_POST['status']; $URL['ARGS']=add_arg('status='.$_POST['status']);}
		if(isset($_GET['status'])) $post_status = $_GET['status']; else $status = NULL;
?>
<?php
	if(!isset($_SESSION['user_id']))
		{
		$rel = url('войдите','USERS','in');
		$_SESSION['QUERY_STRING']=url(NULL,'TASK','tasks');
		show_msg(NULL,'Пожалуйса, '.$rel.' в систему', MSG_INFO,MSG_NO_BACK);
		return;
		}
?>
<h1><?php echo $URL['TITLE']; ?></h1>

<?php
	//Если это решающий просматривает какие задания решать
	if( user_in_group('SOLVER')  && isset($_GET['status']) && ($_GET['status']=="NEW' or status='GETS" || $_GET['status']=="NEW\" or status=\"GETS"))
			{
			$post_status='new_for_solver';
			unset($solver);	//запрещаем просмотр от имени других решающих
			}
	else if( !check_right('TSK_SEE_OTHERS_TASK')) //можно просматривать только свои задания
		{
		if(
			(isset($user) && $user!=$_SESSION['user_id']) ||
			(isset($solver) && $solver!=$_SESSION['user_id'])
	        	)
			{
			show_msg(NULL,"Можно просматривать только свои задания",MSG_CRITICAL,MSG_RETURN,MSG_SHOW);
			return;
			}
		//если не указан ни user ни solver и это
		if(!(isset($user) && !isset($solver)))
			if(user_in_group('SOLVER'))
				$solver = $_SESSION['user_id'];
			else if(isset($_SESSION['user_id']))
				$user = $_SESSION['user_id'];
		}
	// сортировка
	$sort_col = array('id', 'status', 'user', 'solver', 'resolve_until', 'price', 'section', 'subsection');
	if(isset($_GET['sortby']) && isset($sort_col[$_GET['sortby']])) $sortby = $sort_col[$_GET['sortby']]; else $sortby = 'id';
	if(isset($_GET['sort']) && ($_GET['sort']=='asc' || $_GET['sort']=='desc')) $sort = $_GET['sort']; else $sort = 'desc';
	if($sort=='desc') $sort2='asc';else $sort2='desc';
?>
<?php
	$on_page = 20;	//число сток в таблице
	require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");   //тут указан % комиссии
	$query="select count(id) FROM `%s`";
	$vars = array($table_task);
	if(isset($user) || isset($solver) || (isset($post_status) && $post_status!="-1"))
    	$query.=" WHERE ";
	if(isset($user))
            {$query.=" user = '%u' "; $vars[] = $user; }
    	if(isset($user) && isset($solver))
            	$query.=" and ";
 	if(isset($solver))
            {$query.=" solver = '%u' ";$vars[] = $solver; }
  	//выдача по статусу
	if(isset($post_status) && $post_status == 'new_for_solver')
	   		{
   			if(isset($user) || isset($solver))
   		    	$query.=" AND ";
   		  	$query.=" ( status = 'NEW' or status='GETS' ) ";
   		  	}
   	else if(isset($post_status) && $post_status!="-1" && $post_status!="")
   			{
   			if(isset($user) || isset($solver))
   		    	$query.=" AND ";
   		  	$query.=" status = '%s' "; $vars[] = $post_status;
   		  	}
	$res = $task->db->query($query,$vars) or die($task->db->error());
	$row = $res->fetch_assoc();
	$max = $row['count(id)'];

	if(empty($URL['PAGE']))
		$p=1;
	else
		$p=$URL['PAGE'];
	$from = ($p-1)*$on_page;
	$query="SELECT `id`, `status`, `user`, `solver`, `resolve_until`, `price`, `section`, `subsection`, `user_pay`, `add_date`  FROM `%s`";
	$vars = array($table_task);
    if(isset($user) || isset($solver) || (isset($post_status) && $post_status!="-1"))
    	$query.=" WHERE ";
	if(isset($user))
            {$query.=" user = '%u' "; $vars[] = $user; }
    	if(isset($user) && isset($solver))
            	$query.=" and ";
 	if(isset($solver))
            {$query.=" solver = '%u' ";$vars[] = $solver; }
  	//выдача по статусу
	if(isset($post_status) && $post_status == 'new_for_solver')
	   		{
   			if(isset($user) || isset($solver))
   		    	$query.=" AND ";
   		  	$query.=" ( status = 'NEW' or status='GETS' ) ";
   		  	}
   	else if(isset($post_status) && $post_status!="-1" && $post_status!="")
   			{
   			if(isset($user) || isset($solver))
   		    	$query.=" AND ";
   		  	$query.=" status = '%s' "; $vars[] = $post_status;
   		  	}
	$query.= " ORDER BY `%s` $sort LIMIT $from,  $on_page";
	$vars[] = $sortby;
	$res = $task->db->query($query,$vars) or die($task->db->error());
?>
<p class="about_page">
	<?php echo url('Ознакомиться с этой страницей', 'TASK', 'info/how_tasks');?>
</p>
<?php if( !user_in_group('SOLVER') ) {?>
	<p align='left'>
		<img src='<?php echo media_dir();?>/i_add_task_2.png' style='margin:0 0 -6px 0px;'>
		<span style="font-size:0.9em;">
		<?php echo url('Новый заказ', 'TASK', 'add_task');?>
		</span>
	</p>
<?php } ?>
<table class='table_tasks styled_table' id="tasks" align="center">
	<tr class='table_header'>
		<th  width="10%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=0') );?>"><?php if(isset($_SESSION['user_id']))echo "Номер"; else echo "Задание";?></a></span>
			<?php if( $sortby == $sort_col[0]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="10%" align="center" >
			<span id="head_status" style="display:inline;"><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=1') );?>">Состояние</a></span>
			<?php if($URL['FILE']!='new_tasks') { ?>
				<form id="show_task_by_status" method="POST" style="display:none;"
												action="<?php
													if(isset($user)) $args = 'user='.$user;
													else if(isset($solver)) $args ='solver='.$solver;
													else $args =NULL;
													echo url(NULL, 'TASK', $URL['FILE'], $args);
											?>">
					<select name="status"
						onchange="javascript: document.getElementById('show_task_by_status').submit()"
						onBlur="javascript: document.getElementById('show_task_by_status').style.display='none'; document.getElementById('head_status').style.display = 'inline'"
					>
						<option <?php if(!isset($post_status) || $post_status==-1)echo "selected"; echo " value='-1'";?>>Все</option>
						<option <?php if(isset($post_status) && ($post_status=='NEW'))echo "selected"; echo " value='NEW'";?>>Новые</option>
						<option <?php if(isset($post_status) && ($post_status=='GETS'))echo "selected"; echo " value='GETS'";?>>Мои правила</option>
						<option <?php if(isset($post_status) && $post_status=='WAIT')echo "selected";echo " value='WAIT'";?>>Идет решение</option>
						<option <?php if(isset($post_status) && $post_status=='SOLV')echo "selected";echo " value='SOLV'";?>>Решен</option>
						<option <?php if(isset($post_status) && $post_status=='REMK')echo "selected";echo " value='REMK'";?>>Перерешать</option>
						<option <?php if(isset($post_status) && $post_status=='OK')echo "selected";  echo " value='OK'";?>>Выполнен</option>
						<option <?php if(isset($post_status) && $post_status=='NSOL')echo "selected";?> value="NSOL">Не решен</option>
					</select>
				</form>
			<?php } ?>
			<span style="cursor:pointer;" onClick="javascript: var obj = document.getElementById('head_status');
											var stl = obj.style.display;
											obj.style.display = document.getElementById('show_task_by_status').style.display;
											document.getElementById('show_task_by_status').style.display = stl">+
			</span>
			<?php if( $sortby == $sort_col[1]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="10%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=2') );?>">От</a></span>
			<?php if( $sortby == $sort_col[2]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="10%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=3') );?>">Решающий</a></span>
			<?php if( $sortby == $sort_col[3]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="15%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=4') );?>">Решить до</a></span>
			<?php if( $sortby == $sort_col[4]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="10%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=5') );?>">Стоимость, руб</a></span>
			<?php if( $sortby == $sort_col[5]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="15%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=6') );?>">Предмет</a></span>
			<?php if( $sortby == $sort_col[6]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
		<th  width="20%" align="center" >
			<span><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby=7') );?>">Раздел</a></span>
			<?php if( $sortby == $sort_col[7]){ ?>
			<span style="cursor:pointer;"
			onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
			onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
			><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
			<?php } ?>
		</th>
	</tr>

	<?php
	$row_number = 0;
	while($row = $res->fetch_assoc()){
		$row_number++;
	?>
	<tr class="<?php
	            if($row['status'] =='REMK')
					echo 'tr_class_remake';
				else if($row['status'] == 'NEW')
					echo 'tr_class_new';
				else if($row['status'] == 'GETS')
					echo 'tr_class_getSolver';
				else if($row['status'] == 'WAIT')
					echo 'tr_class_wait';
				else if($row['status'] == 'SOLV')
					echo 'tr_class_solv';
				else if($row['status'] == 'OK')
					echo 'tr_class_ok';
               	else
               		echo 'tr_class_other';
				if($row_number%2==0) echo ' odd ';
				?>"

        title="<?php echo $row['id']; ?>"
  >
		<td align="center" style="text-align:center;">
			<?php
				if(isset($_SESSION['user_id']))
					echo url($row['id'], 'TASK', 'task','id='.$row['id']);
				else
					echo get_task_link($row['id'],"title='Просмотреть задание'",$row['id']);
			?>
		</td>
		<td style='padding:0 0 0 10px'>
				<?php
				if($row['status'] =="REMK")
					$status = "<span style='color:#CC0000;'><b>Перерешать</b></span>";
				else if($row['status'] == "NEW")
					$status = "<span style='color:red;'>Новый</span>";
				else if($row['status'] == "GETS")
					$status = "<span style='color:#FF0066;'>Мои правила</b></span>";
				else if($row['status'] == "WAIT")
					$status = "<span style='color:blue;'>Идет решение</span>";
				else if($row['status'] == "SOLV")
					$status = "Решен";
				else if($row['status'] == "OK")
					$status = "<span style='color:#800080;'>Выполнен</span>";
				else if($row['status'] == "NSOL")
					$status = "<span style='color:#800080;'>Не решен</span>";
				else if($row['status'] == "LOCK")
					$status = "<span style='color:#800080;'>Ошибка заказа</span>";
	           		else
	           			$status = $row['status'];
	          	/*/для оплаченных решений появляется ссылка на решение, доступная всем
				if($row['user_pay']==1)
					echo get_opened_solving_link($row['id'],"style='padding:0px;line-height:1em;margin:0px;text-decoration:none;'",$status."&nbsp;&nbsp;&nbsp;&nbsp;<img border='0' src='".media_dir()."/img_get_solv.png' width=12px height=12px alt='Скачать' title='Скачать решение'>");
				else*/
					echo $status;
				?>
		</td>
		<td align="center"><?php echo get_user_link($row['user']); ?></td>
		<?php
		if(isset($row['solver']) && $row['status'] != "GETS" && $row['status'] != "NEW"){ ?>
			<td align="center"><?php if(isset($row['solver'])) echo get_user_link($row['solver']); ?></td>
		<?php	}
		else if($row['status'] == "GETS" && isset($_SESSION['user_id']) && $row['user']==$_SESSION['user_id']){
		?>
			<td align="center">
				<?php echo url("<span style='font-size:0.8em'>Выбрать решающего</span>", 'TASK', 'task','id='.$row['id']);?>
			</td>
		<?php  	}
		else
			{ ?>
			<td align="center">&nbsp;</td>
			<?php } ?>

		<td style='padding:0 0 0 30px'><?php
			//if($row['status']!='GETS')
				echo date(DATE_TIME_FORMAT,strtotime($row['resolve_until']));
			/*else
				{
				$r_until = strtotime($row['resolve_until']) - strtotime($row['add_date']);	//абстрагируемся от часового пояса
				$days = (int)($r_until/3600/24);
				$hours =  (int)(($r_until - $days*3600*24)/3600);
				echo $days. " дн., ".$hours." ч." ;
				}*/
		 ?>
		</td>
		<td style='padding:0 0 0 50px'>
		<?php
		if(isset($_SESSION['user_id'])){
			if(user_in_group('SOLVER') && $_SESSION['user_id']!==0)
				echo price_after_all_comm($row['price']);
			else
				echo 0+$row['price'];
			}
		else
			{echo 0+$row['price'];}
		?>
		</td>
		<td style='padding:0 0 0 10px'><?php echo htmlspecialchars($row['section'],ENT_QUOTES); ?></td>
		<td style='padding:0 0 0 10px'><?php echo htmlspecialchars($row['subsection'],ENT_QUOTES); ?></td>
	</tr>
	<?php } ?>
</table>
<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
	$var ="";
	if(isset($user))$var .= "&user=".$user;
	if(isset($solver))$var .= "&solver=".$solver;
	if(isset($post_status)) $var .= "&status=".$post_status;
	get_table_nav('TASK', $URL['FILE'], NULL, $max, $on_page)
?>

<?php if(isset($_SESSION['user_id'])){ ?>
<script type="text/javascript" >
  // переход при клике на строчку таблицы
  $('#tasks tr').click(function ()
  {
      location.href = "<?php echo url(null, 'TASK', 'task','id='); ?>" + $(this).attr('title');
  });
</script>
<?php } ?>
<style type='text/css'>
.table_tasks
			{
			width:100%;
			width=95%;
			}
.table_tasks tr{
    cursor:pointer;
}
</style>
