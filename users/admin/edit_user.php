<?php
		/*********************************************************************
						Редактирование пользователей
		*********************************************************************
		Параметры GET:
		user_id			- id пользователя
		sudo			- просмотр от имени пользователя

		POST:
		user_id			- id пользователя
		groups 			- группы пользователя, изменяемого администратором
		count_of_groups	- общее число групп
		*/
		if(isset($_POST['user_id'])) $_GET['user_id'] = $_POST['user_id'];
		if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) $user_id = (int)$_GET['user_id'];
		//$_POST['groups'] не используется в запросах
		if(isset($_POST['count_of_groups']) && is_numeric($_POST['count_of_groups'])) $count_of_groups = (int)$_POST['count_of_groups'];
?>
<?php 
	if(!check_right('USR_SEE_ALL_INFO') && !check_right('CNG_USER_GROUP')) 
		{
		show_msg(NULL,'У вас не достаточно прав',MSG_CRITICAL);
		return; //проверка права просматривать полную информацию
		}

	// просмотр от лица пользователя
	if(isset($_GET['sudo'])){
		if(!check_right('SUDO',R_MSG)) return;
		$res = $users->db->query("SELECT login FROM `$table_users` WHERE `id` = '%d'", $_GET['sudo']) or die($users->db->error());
		$row = $res->fetch_assoc();
		$ret = call_user_func($users->login, $row['login'], '111');	//т.к. имеем права SUDO, то можно и без пароля 
		if($ret !== true) show_msg(NULL,'Ошибка просмотра от лица другого пользователя: '.$ret, MSG_WARNING);
		?>
		<script lang="Java"> location.href="/";</script>
		<?php
		return;
		}
?>
<h1><?php echo $URL['TITLE'];?></h1>
<div align="center">	
<table id="table1" style="border-width: 0px">
	<tr>
		<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Пользователь:</td>
		<td style="border-style: none; border-width: medium">
			<form method="POST">
				<select size="1" name="user_id">
					<?php
						// Список пользователей
						$query="SELECT id,login
								FROM `$table_users`";
						$use_res = mysql_query($query,$msconnect_users) or die(mysql_error());
						//$users = explode(',',$row['users']); - $users - зарезервированная переменная
						$cnt=0;
						while ($usr = mysql_fetch_array($use_res))
							{?>
							<option <?php if(isset($user_id) && $user_id==$usr['id']) echo "selected";?> value="<?php echo $usr['id'];?>" > <?php echo $usr['login'];?></option>
							<?php
							}
							?>
				</select>
				<input type="submit" value="Выбрать пользователя" name="set"></p>
			</form>
		</td>
	</tr>	

<?php
	if(!isset($user_id))
		{
		echo '</table></div>';
		//show_msg(NULL,"Пользователь не найден.",MSG_WARNING,MSG_RETURN);
		return;
		}
	//изменяем группы пользователя
	if(isset($_POST['set']) && $_POST['set']=='Изменить группы')
		{
		if(!check_right('CNG_USER_GROUP',R_MSG)) return;
		// формируем список разрешенных действий
		$groups = ',';
		for($i=0; $i < $count_of_groups; $i++)
			if(isset($_POST['groups'][$i]))
				{	
				$_POST['groups'][$i] = mysql_real_escape_string($_POST['groups'][$i], $msconnect_users);
				if(group_exists($_POST['groups'][$i]))
					$groups .= $_POST['groups'][$i].",";
				}

		$query = "UPDATE `$table_users`
				SET groups = '%s'
				WHERE id = '%d'";
		echo $query;
		$users->db->query($query,array($groups, $user_id)) or die($users->db->error());
		}

	// Выбираем пользователя пользователя
	$res = $users->db->query("SELECT * FROM `$table_users` WHERE `id` = '%d'", $user_id) or die($users->db->error());
	$row = $res->fetch_assoc();
	if($res->num_rows ==0 )
		{
		echo "Такой пользователь не существует.";
		return;
		}
?>
	<tr>
		<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Группы:</td>
		<td style="border-style: none; border-width: medium">
			<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_user', 'user_id='.$user_id);?>">
				<?php
					// Список групп
					$query="SELECT id,name,description
							FROM `$table_groups`";
					$grp_res = mysql_query($query,$msconnect_users) or die(mysql_error());
					$groups = explode(',',$row['groups']);
					$cnt=0;
					while($grp = mysql_fetch_array($grp_res))
						{?>
						<input type="checkbox" name="groups[<?php echo $cnt;?>]" <?php if(in_array($grp['name'],$groups) ) echo "checked";?> value="<?php echo $grp['name'];?>" >
						<?php
						echo $grp['name'].":        ".$grp['description']."<br>";
						$cnt++;
						}
						?>
				<input type="hidden" name="count_of_groups" value="<?php echo $cnt;?>">
				<input type="submit" value="Изменить группы" name="set"></p>
			</form>
		</td>
	</tr>
	<?php
	if(check_right('USR_SEE_ALL_INFO'))
		{
		if(user_in_group('SOLVER')){ ?>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Род занятий:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo $row['occupation']; ?>
			</td>
		</tr>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Возраст:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo $row['age']; ?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Дата посещения:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo date(DATE_TIME_FORMAT, strtotime($row['last_visit'])); ?>
			</td>
		</tr>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			E-Mail:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo $row['mail']; ?>
			</td>
		</tr>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Как связаться:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo $row['connect']; ?>
			</td>
		</tr>
		<tr>
			<td class="table_left" style="border-style: none; border-width: medium" width="161">
			Баланс:</td>
			<td style="border-style: none; border-width: medium">
			<?php echo $row['balance']; ?> руб.
			</td>
		</tr>
<?php } 
	if(check_right('SUDO')){ ?>
		<tr>
			<td colspan='2'>
			<?php echo url('Просмотр сайта от лица данного пользователя', NULL, NULL, 'sudo='.$row['id']); ?>
			</td>
		</tr>
<?php }?>
</table>
</div>
