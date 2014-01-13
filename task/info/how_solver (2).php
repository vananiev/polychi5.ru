<?php
	if(!user_in_group('SOLVER',R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<p><font color="#0000FF">Если Вам что-то не понятно, на каждой 
странице сверху-справа под меню представлена справка, которую можно получить 
кликнув по ссылке '<i>Ознакомиться с этой страницей</i>'.</font></p>
<p></p>
<p style='font-style:bold;color:red;font-size:1.5em;'>Внимание!! Начинайте решать задание, только когда оно будет находиться в списке <?php echo url('\'Мои решения\'', 'TASK', 'tasks');?></p>
<p style="line-height: 150%">1. В главном меню 
перейдите по ссылке <?php echo url('Решать', 'TASK', 'new_tasks');?>. 
Вы попадете на страницу  <?php echo url('таблицы заданий', 'TASK', 'new_tasks');?>;</p>
<p style="line-height: 150%">2. Задания, которые Вы можете 
решать могут иметь состояния: &quot;<span>
<a href="javascript:submenu('fast_start')">Новый</a>
&quot; и &quot;<span>
	<a href="javascript:submenu('my_rools')">Мои правила</a>&quot;<span lang="en-us">;</p>

</p>
<p></p>
<p></p>
<p></p>
	<div id='fast_start' style='text-indent:0pt;border: blue 1px solid; display:none'>
	<p style="line-height: 150%"><font color="#FF0000">&quot;Новый&quot;</font>: <i>обеспечивает <u>наименьшие затраты</u> 
	времени</i></p>
	<ul>
		<li>
		<p style="line-height: 150%">в столбце <span lang="en-us">'Решить 
		до<span lang="en-us">' <?php echo url('таблицы заданий', 'TASK', 'new_tasks', 'status=NEW');?>, 
		указана дата, до истечения которой Вы должны решить задание<span lang="en-us">;</p>
		<li>
		<p style="line-height: 150%">цена решения указывается в 
		столбце <span lang="en-us">'<i>Стоимость</i>
		<?php echo url('таблицы заданий', 'TASK', 'new_tasks', 'status=NEW');?>, 
		она не изменна;</p>
		<li>
		<p style="line-height: 150%">чтобы начать решение кликните по номеру 
		задания в <?php echo url('таблицы заданий', 'TASK', 'new_tasks', 'status=NEW');?>;</p>
		</li>
		<li>
		<p style="line-height: 150%">нажмите по кнопке '<i>Соглашаюсь 
		решить задание и приступаю к решению</i>';</p>
		</li>
		<li>
		<p style="line-height: 150%">состояние задания сменится	с <font color="#FF0000"><span lang="en-us">'</font><span lang="en-us"><font color="red"><u>Новый</u>'</font>&nbsp;на<span lang="en-us"> <u> <font color="blue">'Идет решение'</font></u>;</p>
		</li>
		<li>
		<p style="line-height: 150%">начинайте решать задание;</p>
		</li>
		
		<li>
		<p style="line-height: 150%">задание автоматически добавляется в пункт 
		'<?php echo url('Мои решения', 'TASK', 'tasks', "solver=".$_SESSION['user_id']);?>'
		в главном меню;</p>
		</li>
	</ul>
	</div>
	
	<div id='my_rools' style='text-indent:0pt;border: blue 1px solid;display:none'>
	<p style="line-height: 150%"><font color="#FF0000">&quot;М</font><font color="#FF0000">ои 
	правила&quot;</font>: <i>обеспечивает наибольшую <u>
	функциональность</u></i></p>
	<ul>
		<li>
		<p style="line-height: 150%">в столбце 
		<span lang="en-us">'Решить до<span lang="en-us">'  
		<?php echo url('таблицы заданий', 'TASK', 'new_tasks', 'status=GETS');?>, 
		указано время в днях и часах<span lang="en-us">, 
		за которое Вы должны решить задание. Отсчет времени начнется с момента 
		выбора Решающего (читайте дальше)<span lang="en-us">;</p>
		</li>
		<li>
		<p style="line-height: 150%">цена за решение указывается в столбце
		<span lang="en-us">'<i>Стоимость</i><span lang="en-us">'  
		<?php echo url('таблицы заданий', 'TASK', 'new_tasks', 'status=GETS');?>, 
		она является ориентировочной;</p>
		</li>
		<li>
		<p style="line-height: 150%">чтобы предложить свою цену за решение 
		кликните по номеру задания в <?php echo url('таблице заданий', 'TASK', 'new_tasks', 'status=GETS');?>;</p>
		</li>
		<li>
		<p style="line-height: 150%">в строке стоимость укажите 
		свою цену;</p>
		</li>
		<li>
		<p style="line-height: 150%">нажмите по кнопке '<i>Подать 
		заявку на решение. Начать решение после того, как меня выберут решающим</i>';</p>
		</li>
		<li>
		<p style="line-height: 150%">если, через некоторое время, 
		<a title="Тот пользователь, которому Вы решаете задание">
		
		Владелец задания</a> выберет Вас Решающим, Вы должны начать 
		решение (Вы узнаете об этом, когда это задание перейдет 
		в группу '
		<?php echo url('Мои решения', 'TASK', 'tasks', 'solver='.$_SESSION['user_id']);?>
		')<span lang="en-us">;</p>
		<li>
		<p style="line-height: 150%">с этого момента начнется отсчет времени, 
		который указывается в столбце &nbsp;<span lang="en-us">'Решить 
		до<span lang="en-us">' 
		<?php echo url('таблицы заданий', 'TASK', 'tasks', 'status=GETS');?><span lang="en-us">;</p>
		<li>
		<p style="line-height: 150%">Вы должны решить задание за это время;</p>
		</li>
	</ul>
	</div>
<p style="line-height: 150%">3. <a name="Время решения"></a>&#39;<i>Время на решение задания</i>' 
- для заданий с состоянием:</p>
<ul>
	<li>
	<p style="line-height: 150%"><font color="#FF0000"> 
	&quot;Новый&quot;</font> указана дата, до истечения которой Вы должны решить 
	задание<span lang="en-us">;</p>
	</li>
	<li>
	<p style="line-height: 150%"><font color="#FF0000">&quot;Мои правила&quot;</font> 
	указано время в днях и часах, за которое Вы должны 
	решить задание. Отсчет этого времени начнется <u><b>с момента выбора 
	Решающего </b></u>(Вы узнаете об этом, когда это задание перейдет в группу
	'<?php echo url('Мои решения', 'TASK', 'tasks', 'solver='.$_SESSION['user_id']);?>');</p>
	</li>
</ul>

<p style="line-height: 150%">4. Чтобы отослать решение:</p>
<ul>
	<li>
	<p style="line-height: 150%">&nbsp;перейдите по ссылке '<?php echo url('Мои решения', 'TASK', 'tasks', 'solver='.$_SESSION['user_id']);?>' в главном меню<span lang="en-us">;</p></li>
	<li>
	<p style="line-height: 150%">кликните по
	<?php echo url('номеру задания', 'TASK', 'tasks', 'solver='.$_SESSION['user_id'].'&status=WAIT');?>, 
	который находится в состоянии <span lang="en-us">'<i>Идет 
	решение</i><span lang="en-us">';</p></li>
	<li>
	<p style="line-height: 150%">нажав по кнопке <span lang="en-us">'<i>Обзор</i><span lang="en-us">', 
	укажите файл с решением<span lang="en-us">;</p></li>
	<li>
	<p style="line-height: 150%">нажмите по кнопке <span lang="en-us">'<i>Отправить 
	решение</i><span lang="en-us">';</p></li>
	<li>
	<p style="line-height: 150%">состояние задания сменится<span lang="en-us"> c <font color="blue"><u>'Идет решение'</u></font>
	&nbsp;на&nbsp;<u>'Решен'<span lang="en-us">;</u> </p></li>
</ul>
<p style="line-height: 150%">5<span lang="en-us">. В течении 7 суток&nbsp; 
		<a title="Тот пользователь, которому Вы решаете задание">
		
		Владелец задания</a> скачивает решение и, если задание решено 
верно, его состояние сменится с&nbsp;<u>'Решен'</u> на <u> <font color="#800080">'Выполнен'</font></u>, 
при этом на <?php echo url('Ваш баланс', 'TASK', 'get_balance');?>
 зачислится сумма за решение задания<span lang="en-us">;</p>
<p style="line-height: 150%">6.<span lang="en-us"> Если 
задание решено не верно, в течении 7 суток состояние задание поменяется 
с&nbsp;<u>'Решен'</u> на <font color="#cc0000"><b><u>'Перерешать'</u></b></font>.
 Вы должны в течении 7 дней отослать верное решение. Дата, до 
которой необходимо перерешать задание будет указана в столбце 
		<span lang="en-us">'Решить до<span lang="en-us">' в группе  
<span lang="en-us">'
<?php echo url('Мои решения', 'TASK', 'tasks', 'solver='.$_SESSION['user_id'].'&status=REMK');?>
<span lang="en-us">';</p>
<p style="line-height: 150%"><font color="#FF0000">7. <u>Форс-мажорные ситуации:</u></font></p>
<ul>
	<li>
	<p style="line-height: 150%">Если&nbsp; 
		<a title="Тот пользователь, которому Вы решаете задание">
		Владелец задания</a> не скачивает решение (т.е. с баланса 
		<a title="Тот пользователь, которому Вы решаете задание">
		Владельца задания</a> не снимается сумма за решение), то и на <?php echo url('Ваш баланс', 'TASK', 'get_balance');?> сумма за решения задания не&nbsp; начисляется (такого не было ни разу, при добавлении задания в систему заказчик вносит часть суммы, поэтому в его интересах задание получить);</p>
	</li>
	<li>
	<p style="line-height: 150%">В случае, если задание имеющее 
	состояние <font color="blue"><span lang="en-us"> <u>'Идет решение'</u></font> 
	или  <font color="#cc0000"><b><u>'Перерешать'</u></b></font> 
	не будет решено в указанный срок, на вас будет наложен штраф в размере 
	<?php echo 100*STRAV;?>% стоимости решения задания. Будьте внимательны!</p>
	</li>
	<li>
	<p style="line-height: 150%">Если, Вы назначены Решающим 
	(т.е. задание находится в группе <span lang="en-us">'
	<?php echo url('Мои решения', 'TASK', 'tasks', 'solver='.$_SESSION['user_id']);?>
	<span lang="en-us">') и задание 
	находится в состоянии  <font color="blue"><span lang="en-us"> <u>'Идет решение'</u></font>, 
	но Вы не можете по каким-либо обстоятельствам решить задание, немедленно
	<?php echo url('обратитесь в службу поддержки', 'TICKET', 'tickets');?>
	!</p>
	</li>
</ul>
