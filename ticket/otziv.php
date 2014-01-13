<?php /*
		**************************************************************
				Написание отзыва
		**************************************************************
*/
		$_GET['ticket_id'] = 0;
?>
<h1><?php echo $URL['TITLE']; ?></h1>
	<?php
	//обновляем тикет
	call_user_func($ticket->update_ticket, 0);
	// вывод сообщений тикета
	$ret = call_user_func($ticket->show_messages, 0); 
	if($ret !== true)
		show_msg(NULL, $ret, MSG_WARNING);
	else
		//добавить новый вопрос
		call_user_func($ticket->add_question_form, 0);

	/*/переговоры
	$query = "SELECT * FROM `$table_questions`
				WHERE ticket_id = 0
				ORDER BY date";
	$res2 = mysql_query($query,$msconnect_ticket) or die(mysql_error());
	?>
	<table  class='table_dialog'>
	    <?php
	    	while($row2 = mysql_fetch_array($res2))
	    		{
				?>
				<tr class='tr_dialog'>
					<td class='td_who'>
					<?php
						//require_once(SCRIPT_ROOT."/users/DB.php");
						$query = "SELECT name,surname,login
					        FROM `$table_users`
					        WHERE id = '{$row2['from_id']}'";
							$sql = mysql_query($query,$msconnect_users) or die(mysql_error());
							$row3 = mysql_fetch_array($sql);
						echo "<div class='avatar_box'>"; 
							output_avatar($row2['from_id']);
						echo "</div>";
						echo '<div class=\'user_name_box\'>';
							if(mysql_num_rows($sql)!=0)
								echo htmlspecialchars($row3['login'],ENT_QUOTES)." :  ".htmlspecialchars($row3['surname'],ENT_QUOTES).'  '.htmlspecialchars($row3['name'],ENT_QUOTES);
							else
								{
								if(isset($row['from_mail'])) echo htmlspecialchars($row['from_mail'],ENT_QUOTES);
								else echo 'Незнакомец';
								}
						echo '</div>';
						echo '<div class=\'date_box\'>'.date(DATE_TIME_FORMAT,strtotime($row2['date'])).'</div>';
					?>
					</td>
					<td class='td_content'>
	               		<?php
							echo $row2['text'];
							//echo str_replace('\r\n','<br>',htmlspecialchars($row2['text'],ENT_QUOTES));
	               			//если есть файл
	               			if($row2['file']=='YES')
	               				{
	               				?>
	               				<div class='attached'>
									Прикрепленный &nbsp;
									<a href="<?php echo TICKET_FILE_RELATIVE."/{$row2['id']}.rar"; ?>">файл</a>
								</div>
	               				<?php
	               				}
	               		?>
					</td>
				</tr>
				<?php } ?>
	</table>
	<?php
	*/
?>
<style type="text/css">
.table_dialog {
			width:100%;
			width=96%;
			padding: 0 100px;
			border-spacing: 0 20px; border-collapse: separate;
			}
table .td_who{
			margin:30px 0 0 0;
			padding: 10px;
			border: 1px solid grey;
			text-align:left;
			width: 250px;;
			}
.table_dialog .td_content {
			margin:0;
			padding: 10px;
			border: 1px solid grey;
			border-left: none;
			text-align:left;
			vertical-align:top;
			font-size:0.9em;
			line-height:1.5em;
			}
.table_dialog td  {
			border:1px solid grey;
			}
.attached	{
			color:blue;
			border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; font-size:10px; font-weight:bolder; padding:2px ;background-color:#EEE;
			border:1px solid white;
			margin:10px 1px 1px 50px;
			width:140px;
			text-align:center;
			}
</style>

