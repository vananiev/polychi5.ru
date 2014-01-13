<h1><?php echo $URL['TITLE']; ?></h1>
<p>&nbsp;</p>
<p align="center">
			<img src="<?php echo media_dir();?>/how_dialog.jpg" ></p>
<ol>
	<li><font color="#0000FF">Номер</font>:
	это номер, был получен 
	вами при <?php echo url('создании запроса в службу поддержки', 'TICKET', 'info/how_tickets', '#add_tick_help'); ?>
	<span lang="en-us">;</li>
	<li><font color="#0000FF">Тема</font>: тема вашего запроса, 
	указанная при
	<?php echo url('создании запроса в службу поддержки', 'TICKET', 'info/how_tickets', '#add_tick_help'); ?>;</li>
	<li><font color="#0000FF">Категория</font>: указанная при
	<?php echo url('создании запроса', 'TICKET', 'info/how_tickets', '#add_tick_help');?>, принимает одно из значений:<ul>
		<li>
		<font color="#0000FF">
		Работа системы </font>- вопросы связанные с порядком работы системы<font color="#0000FF">;</font></li>
		<li>
		<font color="#0000FF">Оплата</font> - вопросы связанные 
		с оплатой;</li>
		<li>
		<font color="#0000FF">Без категории</font> - остальные&nbsp; 
		вопросы;</li>
	</ul>
	</li>
	<li> <font color="#0000FF">Добавлен</font>: 
	дата и время 
	<?php echo url('создания запроса', 'TICKET', 'info/how_tickets', '#add_tick_help'); ?>;</li>
	<li>&nbsp;<font color="#0000FF">Статус</font>: 
	вы можете изменив статус задания, тем самым:</li>
	<ul>
		<li><font color="#0000FF">Открыт</font> - 
		указывает на то, что Вы ожидаете ответа на заданный Вами вопрос;</li>
		<li><font color="#0000FF">Закрыт </font>- устанавливается Вами, когда Вы 
		считаете, что окончательный ответ на Ваш вопрос получен<span lang="en-us">;</li>
	</ul></li>
	<li> <font color="#0000FF">Изменен</font>: 
	указывает на дату и время последнего сообщения. </li>
</ol>
