<?php
	/***************************************************************************

					Редактирование блоков ссылок

	*************************************************************************
	GET:
	linkblock_id	- id блока ссылок
	
	POST:
	linkblock_id	- id блока ссылок
	link 			- ссылки, которые были установлены ранее
	count_of_link - общее число существующих ссылок
	newlink 		- новые ссылки
	count_of_newlink - общее число новых ссылок
	block_name 		- имя создаваемого блока ссылок
	description - описание создаваемого блока ссылок
	optional_link	- произвольная ссылка (можно указать любую внешнюю)
	*/
	if(isset($_GET['linkblock_id'])) $_POST['linkblock_id'] = $_GET['linkblock_id'];
	if(isset($_POST['linkblock_id']) && is_numeric($_POST['linkblock_id'])) $linkblock_id = (int)$_POST['linkblock_id'];
	if(isset($_POST['count_of_link']) && is_numeric($_POST['count_of_link'])) $count_of_link = (int)$_POST['count_of_link'];
	if(isset($_POST['count_of_newlink']) && is_numeric($_POST['count_of_newlink'])) $count_of_newlink = (int)$_POST['count_of_newlink'];
	// проверка остальных переменных обрабатывается в $db->query()
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php
	if(!check_right('EDIT_LINKBLOCKS',R_MSG)) return;	// проверка прав
	//удаляем блок
	if(isset($_POST['set']) && $_POST['set']=='Удалить блок ссылок')
		{
		$MENU->delete($linkblock_id);
		unset($linkblock_id);
		}

	//создаем новый блок
	if(isset($_POST['set']) && $_POST['set']=='Создать новый блок ссылок')
		$MENU->add($_POST['block_name']);

	//сохраняем изменения
	if(isset($_POST['set']) && $_POST['set']=='Принять изменения' && isset($linkblock_id))
		{
		$MENU->read($linkblock_id, $old_links, $old_description);
		// удаляем старые не отмеченные ссылки
		unset($links);
		for($i=0; $i < $count_of_link; $i++)
			if(isset($_POST['link'][$i]))
				$links[] = $old_links[$_POST['link'][$i]];
		// запись новых отмеченных ссылок
		for($i=0; $i < $count_of_newlink; $i++)
			if(isset($_POST['newlink'][$i]))
				{
				$l = explode(',', $_POST['newlink'][$i]);
				if(count($l) == 2)
					{
					if(!isset($_POST['args'][$i])) $_POST['args'][$i]='';
					if(!isset($_POST['page'][$i]) || !is_numeric($_POST['page'][$i])) $_POST['page'][$i]='';
					if(!isset($_POST['tegs'][$i])) $_POST['tegs'][$i]='';
					if(ereg('[<>]',$_POST['tegs'][$i])) $_POST['tegs'][$i] = ''; // если имеются запрещенные символы
					// добавляем ссылку
					$links[] = array($l[0], $l[1], $_POST['args'][$i], $_POST['page'][$i], $_POST['tegs'][$i]);
					}
				}
		// новой произвольной ссылки
		if(isset($_POST['optional']))
			$links[] = array('OPTIONAL', $_POST['optional_link'], $_POST['optional_ankor'], NULL, $_POST['optional_tegs']);
		
		$MENU->update($linkblock_id, $links, $_POST['description']);
		}
	// Список блоков ссылок
	$res2 = $db->query("SELECT `id`,`name` FROM `$table_linkblocks`") or die($db->error);
?>
<div align="center">	
<table id="table1" style="border:none;">
	<tr>
		<td class="table_left" style="border-style: none; border-width: medium" >
			Блок ссылок:
		</td>
		<td>
			<form method="POST" action="<?php echo url(NULL, 'ADMIN', 'edit_linkblocks');?>">
				<select size="1" name="linkblock_id">
					<?php
					while ($row2 = mysqli_fetch_assoc($res2))
						{?>
						<option <?php if(isset($linkblock_id) && $linkblock_id==$row2['id']) echo "selected";?> value="<?php echo $row2['id'];?>" > <?php echo htmlspecialchars($row2['name'],ENT_QUOTES);?></option>
						<?php
						}
					?>
				</select>
				<input type="submit" value="Выбрать" name="set">
				<input style='position:relative;left:200px;' type="submit" value="Удалить блок ссылок" name="set">
			</form>
		</td>
	</tr>

	<?php  if(isset($linkblock_id))
		{ 
		// Выбираем блок ссылок
		if(!$MENU->read($linkblock_id, $links, $description))
			{
			echo "Такого блока ссылок не существует.";
			return;
			}
		// меняем состав ссылок
			?>
			<form method="POST" action="<?php echo url(NULL, 'ADMIN', 'edit_linkblocks');?>">
				<table>
					<tr>
						<td class="table_left" style="border-style: none; border-width: medium">
							Описание:</td>
						<td style="border-style: none; border-width: medium">
							<textarea rows="2" name="description" style='width:400px;height:30px;'><?php echo htmlspecialchars($description,ENT_QUOTES); ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<?php
							// Список имеющихся ссылок
							$cnt = count($links);
							for($i=0; $i < $cnt; $i++)		// перебор ссылок
								if(isset($links[$i][0]) && $links[$i][0]!='')
									{
									if(isset($FILE[$links[$i][0]][$links[$i][1]]['ANCHOR'])) 
										{
										$ankor = $FILE[$links[$i][0]][$links[$i][1]]['ANCHOR'];		// получаем анкор
										echo "<input type='checkbox' name='link[".$i."]'
												value='",$i,"'
												checked >",
												url($ankor,$links[$i][0],$links[$i][1],$links[$i][2],$links[$i][3],$links[$i][4]),'<br>';
										}
									elseif($links[$i][0] == 'OPTIONAL')	//произвольная ссылка
										{
										$ankor = $links[$i][2];	// анкор хранится в БД в столбце `args`
										echo "<input type='checkbox' name='link[".$i."]'
												value='",$i,"'
												checked >",
												url($ankor,$links[$i][0],$links[$i][1],NULL,NULL,$links[$i][4]),'<br>';
										}
									}
							?>
							<input type="hidden" name="count_of_link" value="<?php echo $cnt;?>">
							<table class='table_links'>
							<tr class='table_header'>
							<td>Ссылка</td>
							<td>Добавить в строку запроса</td>
							<td>Обратиться к странице</td>
							<td>html-теги ссылки</td>
							</tr>
							<tr>
							<td class='left_header'><input type="checkbox" name="optional" value="" ><input type='text' name='optional_ankor' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:100px;'></td>
							<td colspan='2'><input type='text' name='optional_link' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:300px;'></td>
							<td><input type='text' name='optional_tegs' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:100px;'></td>
							<tr>
							<?php
							// добавление новых ссылок
							foreach($INCLUDE_MODULES as $MODULE=>$tmp)
								if(isset($FILE[$MODULE]))
									{
									echo '<tr><td colspan=\'4\'>';
									if(isset($FILE[$MODULE]['TITLE']))
										secho ($FILE[$MODULE]['TITLE']);
									else
										secho($MODULE);
									echo ':</td></tr>';
									foreach($FILE[$MODULE] as $file_name => $file)
										if(isset($file['ANCHOR']) && is_array($file)) 
											{
											$ankor = $file['ANCHOR'];		// получаем анкор
											?>
											<tr>
											<td class='left_header'><input type="checkbox" name="newlink[<?php echo $cnt;?>]" value="<?php echo $MODULE,',',$file_name; ?>" >
											<?php 
											echo url($ankor,$MODULE,$file_name);
											?>
											</td>
											<td><input type='text' name='args[<?php echo $cnt;?>]' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:100px;'></td>
											<td><input type='text' name='page[<?php echo $cnt;?>]' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:30px;'></td>
											<td><input type='text' name='tegs[<?php echo $cnt;?>]' value='' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888';" style='font-size: 9px;color:#888;width:100px;'></td>
											</tr>
											<?php
											$cnt++;
											}
									}
								?>
							</table>
							<input type="hidden" name="count_of_newlink" value="<?php echo $cnt;?>">
							<input type="hidden" name="linkblock_id" value="<?php echo $linkblock_id;?>">
							<input type="submit" value="Принять изменения" name="set">
						</td>
					</tr>
				</table>
			</form>
	<?php }?>
	<form method="POST" action="<?php echo url(NULL, 'ADMIN', 'edit_linkblocks');?>">
	<table><tr>
			<td class="table_left" style="border-style: none; border-width: medium">
				Создать блок ссылок:</td>
			<td style="border-style: none; border-width: medium">
				<input type='text' name="block_name" value=''>
				<input type="submit" value="Создать новый блок ссылок" name="set">
			</td>
	</tr></table>
	</form>
</table>
</div>
<style type="text/css">
.table_links{
	font-size:11px;
	}
.table_links td {
	border:1px solid #BBB;
	text-align:center;
	}
.table_links .table_header {
	font-style:bold;
	}
.table_links .left_header {
	text-align:left;
	}
</style>