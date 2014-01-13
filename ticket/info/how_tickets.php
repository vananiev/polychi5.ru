<h1><?php echo $URL['TITLE']; ?></h1>
<div style='margin:20px 0 50px 100px;'>
	<p class='info_text'><a href='#see' onclick="return anchorScroller(this, 1000)" >Просмотр уже заданных  вопросов</a></p>
	<p class='info_text'><a href='#add_tick_help' onclick="return anchorScroller(this, 1000)" >Задать новый вопроc</a></p>
</div>
<a name='see'></a><h3>Просмотр уже заданных вопросов</h3>
<p>Заданные Вами вопросы представлены в виде таблицы. Каждая строка таблицы 
соответствует разным темам Ваших переговоров с нашими специалистами.</p>
<p>Разберем пример таблицы запросов в службу поддержки. <br>Она представляется в формате. В столбцах указываются:</p>
<p align="center"><img border="0" src="<?php echo media_dir();?>/how_tickets.jpg"></p>
<ul>
	<li>1 - <font color="#0000FF">Номер</font>: это номер, был получен 
	вами при <a href="#add_tick_help" onclick="return anchorScroller(this, 1000)"">создании запроса в службу поддержки</a>. Кликните по номеру, для того чтобы 
	<?php echo url('переидти к сообщению', 'TICKET', 'info/how_dialog');?>
	;</li>
	<li>2 - <font color="#0000FF">Категория</font>: принимает 
	следующие значения:<ul>
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
	<li>3 - <font color="#0000FF">Добавлен</font>: 
	дата и время 
	задания вопроса;</li>
	<li>4 - <font color="#0000FF">Статус</font>: 
	принимает следующие значения:</li>
	<ul>
		<li><font color="#0000FF">Новый</font><font color="#0000FF"> </font>- 
		устанавливается, когда вы задаете первый вопрос;</li>
		<li><font color="#0000FF">Открыт</font> - 
		устанавливается, когда вы задаете следующие вопросы;</li>
		<li><font color="#0000FF">Ответ дан</font> - 
		устанавливается, когда наш специалист ответит на Ваш вопрос;</li>
		<li><font color="#0000FF">Закрыт </font>- устанавливается Вами, когда Вы 
		считаете, что ответ на Ваш вопрос получен<span lang="en-us">;</li>
	</ul>
	<li>5 - <font color="#0000FF">Изменен</font>: 
	указывает на дату и время последнего сообщения.
	</ul>
<p>&nbsp;</p>
<a name="add_tick_help"></a><h3>Задать новый вопрос</h3>
<p>Для того чтобы задать 
вопрос на новую тему, заполните следующие поля:</p>
<p align="center"><img border="0" src="<?php echo media_dir();?>/how_tickets_new.jpg">

<ol>
	<li>
	<p align="left"> <font color="#0000FF">Категория: </font>
	выберите категорию вопроса:<ul>
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
	<li><font color="#0000FF">Тема вопроса:</font> введите тему 
	Вашего вопроса;</li>
	<li><font color="#0000FF">Ваш вопрос:</font> задайте вопрос;</li>
	<li><font color="#0000FF">Прикрепить файл: </font>запакуйте 
	файлы, которые вы хотите отослать, архиватором Win-Rar. 
	Укажите файл-архив, нажав по кнопке обзор;</li>
	<li>Нажмите отправить.</li>
</ol>
