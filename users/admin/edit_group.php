<?php
		/*********************************************************************
						Редактирование групп
		*********************************************************************
		POST:
		group_id	- id группы
		description - описание группы
		
		group_name	- имя создаваемой группы
		*/
		if(isset($_POST['group_id']) && is_numeric($_POST['group_id'])) $group_id = (int)$_POST['group_id'];
		//$_POST['action'] явно не используется для вывода и передачи в БД
		if(isset($_POST['count_of_actions']) && is_numeric($_POST['count_of_actions'])) $count_of_actions = (int)$_POST['count_of_actions'];
		if(isset($_POST['description'])) $description = mysql_real_escape_string($_POST['description'],$msconnect_users);
		if(isset($_POST['group_name'])) $group_name = mysql_real_escape_string($_POST['group_name'],$msconnect_users);
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	//удаляем группу
	if(isset($_POST['set']) && $_POST['set']=='Удалить группу')
		{
		if(!check_right('ADD_DEL_GROUP',R_MSG)) return;	// проверка прав
		$query = "DELETE FROM `$table_groups`
				WHERE id = '{$group_id}'";
		echo $query;
		mysql_query($query,$msconnect_users) or die(mysql_error());
		unset($group_id);
		}

	//создаем новую группу
	if(isset($_POST['set']) && $_POST['set']=='Создать новую')
		{
		if(!check_right('ADD_DEL_GROUP',R_MSG)) return;	// проверка прав
		$query = "INSERT INTO `$table_groups`
				(name,action,description)
				VALUES('{$group_name}','','')";
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
		<td class="table_left"  >
			Выбрать группу:
		</td>
		<td>
			<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_group');?>">
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
				<?php if(check_right('ADD_DEL_GROUP'))
					{ ?>
					<input style='position:relative;left:200px;' type="submit" value="Удалить группу" name="set">
				<?php } ?>
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
		?>
		<tr>
			<td class="table_left" >
				Описание:
			</td>
			<td >
				<textarea rows="2" name="description"><?php echo htmlspecialchars($row['description'],ENT_QUOTES); ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="table_left">
				Права:
			</td>
			<td>
				<?php echo url(_('редактировать'),'USERS','admin/group_access', 'group_id='.$group_id);?>
			</td>
		</tr>
		<tr>
			<td class="table_left" >
				Меню:
			</td>
			<td >
				<?php echo url(_('редактировать'),'USERS','admin/edit_menu', 'group_id='.$group_id);?>
			</td>
		</tr>
	<?php } 
	if(check_right('ADD_DEL_GROUP'))
		{ ?>
		<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_group');?>">
		<tr>
			<td class="table_left" >
				Имя новой группы:</td>
			<td >
				<input type='text' name="group_name" value=''>
				<input type="submit" value="Создать новую" name="set">
			</td>
		</tr>
		</form>
		<?php } ?>
</table>
</div>