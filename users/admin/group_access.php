<?php
		/*********************************************************************
						Установка прав на группы
		*********************************************************************
		GET:
		group_id	- id группы
		
		POST:
		group_id	- id группы
		action 		- разрешения
		count_of_actions - общее число существующих действий
		description - описание группы
		
		group_name	- имя создаваемой группы
		*/
		if(isset($_GET['group_id'])) $_POST['group_id'] = $_GET['group_id'];
		if(isset($_POST['group_id']) && is_numeric($_POST['group_id'])) $group_id = (int)$_POST['group_id'];
		//$_POST['action'] явно не используется для вывода и передачи в БД
		if(isset($_POST['count_of_actions']) && is_numeric($_POST['count_of_actions'])) $count_of_actions = (int)$_POST['count_of_actions'];
		if(isset($_POST['description'])) $description = mysql_real_escape_string($_POST['description'],$msconnect_users);
		if(isset($_POST['group_name'])) $group_name = mysql_real_escape_string($_POST['group_name'],$msconnect_users);
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	//сохраняем изменения
	if(isset($_POST['set']) && $_POST['set']=='Принять изменения')
		{
		if(!check_right('CHANGE_ACCESS',R_MSG)) return;	// проверка прав
		// формируем список разрешенных действий
		$action = ',';
		for($i=0; $i < $count_of_actions; $i++)
			if(isset($_POST['action'][$i]) && is_numeric($_POST['action'][$i]))
				$action .= $_POST['action'][$i].",";

		$action = mysql_real_escape_string($action,$msconnect_users);
		$query = "UPDATE `$table_groups`
				SET action = '{$action}',
				description = '{$description}'
				WHERE id = '{$group_id}'";
		echo $query;
		mysql_query($query,$msconnect_users) or die(mysql_error());
		}
	// Список групп
	$query="SELECT id,name
            FROM `$table_groups`";
	$res2 = mysql_query($query,$msconnect_users) or die(mysql_error());
?>
<div align="center">	
<table id="table1" style="border:none;">
	<tr>
		<td class="table_left" style="border-style: none; border-width: medium" >
			Выбрать группу:
		</td>
		<td>
			<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/group_access');?>">
				<select size="1" name="group_id">
					<?php
					while ($row2 = mysql_fetch_array($res2))
						{?>
						<option <?php if(isset($group_id) && $group_id==$row2['id']) echo "selected";?> value="<?php echo $row2['id'];?>" > <?php echo htmlspecialchars($row2['name'],ENT_QUOTES);?></option>
						<?php
						}
					?>
				</select>
				<input type="submit" value="Выбрать группу" name="set">
			</form>
		</td>
	</tr>
	<?php  if(isset($group_id))
		{ 
		// Выбираем группу
		$query="SELECT *
				FROM `$table_groups`
				WHERE id = '{$group_id}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		$row = mysql_fetch_array($res);
		if(mysql_num_rows($res)==0)
			{
			echo "Такой группы не существует.";
			return;
			}
		// меняем права
		//print_r($_SESSION);
		if(check_right('CHANGE_ACCESS'))
			{
			?>
			<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/group_access');?>">
				<tr>
					<td class="table_left" style="border-style: none; border-width: medium">
						Описание:</td>
					<td style="border-style: none; border-width: medium">
						<textarea rows="2" name="description"><?php echo htmlspecialchars($row['description'],ENT_QUOTES); ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="table_left" style="border-style: none; border-width: medium" >
						Права:</td>
					<td style="border-style: none; border-width: medium">
						<?php
						// Список действий
						$query="SELECT *
								FROM $table_actions";
						$res3 = mysql_query($query,$msconnect_users) or die(mysql_error());
						$rights = explode(',',$row['action']);
						$cnt=0;	
						while($rule = mysql_fetch_array($res3))
							{
							?>
							<input type="checkbox" name="action[<?php echo $cnt;?>]" value="<?php echo $rule['id']; ?>" <?php if(in_array($rule['id'], $rights)) echo "checked";?> >
							<?php 
							echo htmlspecialchars($rule['description'],ENT_QUOTES).'  ('.htmlspecialchars($rule['name'],ENT_QUOTES).')<br>';
							$cnt++;
							} ?>
						<input type="hidden" name="count_of_actions" value="<?php echo $cnt;?>">
						<input type="hidden" name="group_id" value="<?php echo $group_id;?>">
						<input type="submit" value="Принять изменения" name="set">
					</td>
				</tr>
			</form>
		<?php } ?>
	<?php } ?> 
</table>
</div>