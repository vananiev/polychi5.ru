<?php
		/*********************************************************************
						Выдача формы оплаты, просмотр истории
		*********************************************************************
		$_GET
		id - номер оплачиваемого задания
		*/
		if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id'];
?>
<?php
//вывод формы способов оплат
function pay_form()
{
 global $INCLUDE_MODULES;
?>
		<div class='box_standart' id='pay_box'>
			<?php if(isset($_SESSION['user_id'])){ ?><h2>Пополнить</h2><?php } ?>
				<?php
					// выводим информацию о способах оплаты
					global $event;
					$event->create('show_paymant_table');
				?>
				<script type="text/javascript">
					$(".payment-icon").bind('mouseenter', function(){ $(this).find('.commission').removeClass('fadeOut').addClass('fadeIn'); });
					$(".payment-icon").bind('mouseleave', function(){ $(this).find('.commission').removeClass('fadeIn').addClass('fadeOut');});
					<?php if(isset($_GET['sum'])) { ?>$("input[name='Symma']").val(<?php echo $_GET['sum']; ?>); <?php } ?>
				</script>
				<table class='oplata_table' width="100%" id="table2" style="border-width: 0px" align='center'>
				<tr>
					<td style="border: none; font-size:0.8em;" valign="top">
						<p align="right" class='info_text'><font color="#666666">Наш WMID:&nbsp;&nbsp;&nbsp; <br>
						Кошелек:&nbsp;&nbsp;&nbsp; </font></p>
					</td>
					<td style="width:250px;border-left-style:none; border-right-style:none; border-top-style:dotted; border-top-width:1px; border-bottom-style:none;font-size:0.8em;" valign="top" width="371">
						<p align="left" class='info_text' style="text-indent:0;"><a style="text-decoration:none;color: #888;" href="https://passport.webmoney.ru/asp/certview.asp?wmid=202566958179">202566958179</a><br>	
						R595280354376</p>
						<p align="left" class='info_text'>
						<a href="#infooWM" onmouseup ="javascript:submenu('oWM',0);" class='info_text'>Уведомление о рисках</a>
					</td>
				</tr>
		</table>
	</div>
	<style type="text/css">
	.oplata_table tr td {font-size:13px; line-height:18px;}
	.oplata_table .td_oplata_logo {border-left-style:none; border-right-style:none; border-top-style:dotted; border-top-width:1px; border-bottom-style:none;}
	.oplata_table .td_oplata_info {border-left-style:none; border-right-style:none; border-top-style:dotted; border-top-width:1px; border-bottom-style:none;}
	.oplata_table .td_oplata_send {border-left-style:none; border-right-style:none; border-top-style:dotted; border-top-width:1px; border-bottom-style:none;}
	</style>
<?php
}//end:pay_form()

//Вывод истории счета
function money_history()
{
?>
<div class='info_text' align="center">
<a href="javascript:submenu('history_box',0);" title='Покажет когда, как и на сколько Вы пополняли баланс и расходовали средства'>История счета</a></div>	
<div class='box_standart' id='history_box' style='width:90%;width=100%;padding:50px;background-color:#FAFAFA; display:none;'>
	<?php
		//формируем запрос в историю счетов
		global $table_money,$msconnect_money,$URL;
		$on_page =100;	//число сток в таблице
		$query = "SELECT count(*) FROM `$table_money`
				WHERE ot = '{$_SESSION['user_id']}' or komy = '{$_SESSION['user_id']}' ";
		$res = mysql_query($query,$msconnect_money) or die(mysql_error());
		$row = mysql_fetch_assoc($res);
		$max = $row['count(*)'];
		if($max == 0)
			{
			echo "<div style='text-align:center;font-size:2em; font-family: Arial; font-style:italic; font-weight:bolder; color: grey'>Вы не выполнили еще ни одной операции</div>";
			echo "</div>";
			return;
			}
		if(empty($URL['PAGE']))
			$p=1;
		else
			$p=$URL['PAGE'];
		$from = ($p-1)*$on_page;
		$query = "SELECT * FROM `$table_money`
				WHERE ot = '{$_SESSION['user_id']}' or komy = '{$_SESSION['user_id']}'
				ORDER BY id DESC LIMIT $from,$on_page ";
		$res = mysql_query($query,$msconnect_money) or die(mysql_error());
		$cnt=$from+1;
	?>
	<h2>История счета</h2>
	<div align="center">
		<table class='table_history styled_table'>
			<tr class="table_header">
				<th align="center">Номер</th>
				<th align="center">Статус</th>
				<th align="center">Сумма</th>
				<th align="center">Дата</th>
				<th align="center">Информация</th>
			</tr>
			<?php
			while($row = mysql_fetch_array($res))
					{
					?>
					<tr class='tr_ticket_other'>
						<td align="center"><?php echo $row['id']; ?></td>
						<td align="center">
							<?php
							if($row['ot'] == $_SESSION['user_id'])
								echo "<span style='color:red;'>расход</span>";
							else if($row['komy'] == $_SESSION['user_id'])
								echo "доход";
							else
								echo "ошибка";
							?>
						</td>
						<td align="left" style='padding:3px 3px 3px 10px'>
							<?php
							if($row['ot'] == $_SESSION['user_id'])
								echo $row['give'];
							else if($row['komy'] == $_SESSION['user_id'])
								echo $row['get'];
							else
								echo "ошибка";
							?>
						</td>
						<td align="center">
							<?php echo date(DATE_TIME_FORMAT,strtotime($row['date']));?>
						</td>
						<td align="center">
							<?php echo $row['description'];?>
						</td>
					</tr>
					<?php
					$cnt++;
					} ?>
		</table>
		<?php 	//вывод под таблицей ссылок перехода на страницы 'переидти'
			get_table_nav('TASK', 'get_balance', NULL, $max, $on_page); 
		?>
	</div>
</div>
<?php
} //end: money_history() .Вывод истории счета
?>