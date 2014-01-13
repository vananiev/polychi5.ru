<?php
		/*********************************************************************
						Установка прав на группы
		*********************************************************************
		GET:
		group_id 	- id группы
		
		POST:
		group_id 	- id группы
		selectmenu		- имя меню
		count_of_blocks - общее число существующих блоков ссылок
		
		menu_name	- имя создаваемой группы
		*/
		if(isset($_POST['group_id'])) $_GET['group_id'] = $_POST['group_id'];
		if(isset($_GET['group_id']) && is_numeric($_GET['group_id'])) $group_id = (int)$_GET['group_id'];
		if(isset($_POST['count_of_blocks']) && is_numeric($_POST['count_of_blocks'])) $count_of_blocks = (int)$_POST['count_of_blocks'];
		if(isset($_POST['selectmenu'])) $selectmenu = $_POST['selectmenu'];
		//остальные переменные обрабатываются на месте

?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	if(!check_right('EDIT_MENU',R_MSG)) return;	// проверка прав
?>	
<div align="center" style='padding:0 0 30px 0;'>	
	<table id="table1" style="border:none;">
		<tr>
			<td class="table_left"  >
				Выбрать группу:
			</td>
			<td>
				<form method="POST" action='<?php echo url(NULL); ?> '>
					<select size="1" name="group_id">
						<?php
						// Список групп
						$query="SELECT id,name
								FROM `$table_groups`";
						$res2 = mysql_query($query,$msconnect_users) or die(mysql_error());
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
	</table>
</div>	
<?php	
	if(!isset($group_id)) {
		//show_msg(NULL, 'Не указана группа, для который необходимо отобразить меню', MSG_WARNING); 
		return;
		}
	
	//удаляем меню
	if(isset($_POST['set']) && $_POST['set']=='Удалить меню' && isset($selectmenu))
		{
		// Список имеющихся меню
		$query="SELECT `menus` FROM `$table_groups` WHERE id='{$group_id}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		if(!$res) { echo "Такой группы не существует."; return; }
		$row = mysql_fetch_assoc($res);
		$menus = unserialize($row['menus']);
		unset($menus[$selectmenu]);	// удаляем
		$menus = serialize($menus);
		$menus = mysql_real_escape_string($menus, $msconnect_users);
		$query = "UPDATE `$table_groups`
				SET `menus`= '{$menus}'
				WHERE id = '{$group_id}'";
		mysql_query($query,$msconnect_users) or die(mysql_error());
		unset($selectmenu);
		}

	//создаем новое меню
	if(isset($_POST['set']) && $_POST['set']=='Создать новое меню' && $_POST['menu_name']!='')
		{
		// Список имеющихся меню
		$query="SELECT `menus` FROM `$table_groups` WHERE id='{$group_id}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		if(!$res) { echo "Такой группы не существует."; return; }
		$row = mysql_fetch_assoc($res);
		$menus = unserialize($row['menus']);
		$menus[$_POST['menu_name']] = array();	// добавляем
		$menus = serialize($menus);
		$menus = mysql_real_escape_string($menus, $msconnect_users);
		$query = "UPDATE `$table_groups`
				SET `menus`= '{$menus}'
				WHERE id = '{$group_id}'";
		mysql_query($query,$msconnect_users) or die(mysql_error());
		}

	//сохраняем изменения
	if(isset($_POST['set']) && $_POST['set']=='Принять изменения')
		{
		// Список имеющихся меню
		$query="SELECT `menus` FROM `$table_groups` WHERE id='{$group_id}'";
		$res = mysql_query($query,$msconnect_users) or die(mysql_error());
		if(!$res) { echo "Такой группы не существует."; return; }
		$row = mysql_fetch_assoc($res);
		$menus = unserialize($row['menus']);
		// редактируем
		unset($menus[$selectmenu]);
		for($i=0; $i < $count_of_blocks; $i++)
			if(isset($_POST['block'][$i]))
				$menus[$selectmenu][] = (int)$_POST['block'][$i];
		$menus = serialize($menus);
		$menus = mysql_real_escape_string($menus, $msconnect_users);
		$query = "UPDATE `$table_groups`
				SET `menus`= '{$menus}'
				WHERE id = '{$group_id}'";
		mysql_query($query,$msconnect_users) or die(mysql_error());
		}
	
	// Список меню
	$query="SELECT `id`,`name`,`menus` FROM `$table_groups` WHERE id='{$group_id}'";
	$res2 = mysql_query($query,$msconnect_users) or die(mysql_error());
	if(!$res2)
		{
		echo "Такой группы не существует.";
		return;
		}
	$group = mysql_fetch_assoc($res2);
?>
<div align="center">	
<table id="table2" style="border:none;">
	<tr>
		<td class="table_left" >
			Редактирование меню:
		</td>
		<td>
			<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_menu', 'group_id='.$group_id);?>">
				<select size="1" name="selectmenu">
					<?php
					$menus = unserialize($group['menus']);
					foreach($menus as $name => $menu)
						{?>
						<option <?php if(isset($selectmenu) && $selectmenu==$name) 	echo "selected"; ?> value="<?php echo $name;?>" > <?php echo htmlspecialchars($name,ENT_QUOTES);?></option>
						<?php
						}
					?>
				</select>
				<input type="submit" value="Выбрать меню" name="set">
				<input style='position:relative;left:200px;' type="submit" value="Удалить меню" name="set">
			</form>
		</td>
	</tr>
	<?php  if(isset($selectmenu))
		{ 
		// вывод блоков ссылок
		?>
		<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_menu', 'group_id='.$group_id);?>" >
			<tr>
				<td colspan='2' style="border-style: none; border-width: medium">
					<?php
					echo url('Создать блок ссылок', 'ADMIN', 'edit_linkblocks').'<br>';
					// Список блоков ссылок
					$blocks = $db->query("SELECT `id`, `description`,`name` FROM `$table_linkblocks`") or die($db->error);
					$cnt=0;	
					while($block = $blocks->fetch_assoc())
						{
						?>
						<input type="checkbox" name="block[<?php echo $cnt;?>]" value="<?php echo $block['id']; ?>" <?php if(isset($menus[$selectmenu]) && in_array($block['id'], $menus[$selectmenu])) echo "checked";?> >
						<?php 
						echo url(htmlspecialchars($block['name'],ENT_QUOTES), 'ADMIN', 'edit_linkblocks','linkblock_id='.$block['id'], NULL, "title='".htmlspecialchars($block['description'],ENT_QUOTES)."'").'<br>';
						$cnt++;
						} ?>
					<input type="hidden" name="count_of_blocks" value="<?php echo $cnt;?>">
					<input type="hidden" name="selectmenu" value="<?php echo $selectmenu;?>">
					<input type="submit" value="Принять изменения" name="set">
				</td>
			</tr>
		</form>
	<?php } 
	if(check_right('ADD_DEL_GROUP'))
		{ ?>
		<form method="POST" action="<?php echo url(NULL, 'USERS', 'admin/edit_menu', 'group_id='.$group_id);?>">
		<tr>
			<td colspan='2' style="border-style: none; border-width: medium">
				<input type='text' name="menu_name" value=''>
				<input type="submit" value="Создать новое меню" name="set">
			</td>
		</tr>
		</form>
		<?php } ?>
</table>
</div>