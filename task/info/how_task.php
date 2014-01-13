<h1><?php echo $URL['TITLE']; ?></h1>

<p>В зависимости от
<a title="Это статус задания (Новый, Мои правила, Идет решение, Решен, Выполнен)">
Состояния</a> задания Вы можете получить следующую информацию о задании (нажмите на статус Вашего задания ниже):</p>
<ul>
	<li><div class="submenu"><a href="javascript:submenu('my_rools')">Мои правила</a></div>
		<div id='my_rools' style='display:none'>
			<p align='center'>
			<img src="<?php echo media_dir();?>/task_my_rools.png" style="border:none; padding: 1px"></p>
		<div>
	</li>
	<li><div class="submenu"><a href="javascript:submenu('new')">
		Новый</a></div>
		<div id='new' style='display:none'>
			<p align='center'><img style="border:none; padding: 1px" src="<?php echo media_dir();?>/task_new.png"></p>
		<div>
	</li>
	<li><div class="submenu"><a href="javascript:submenu('wait')">
		Идет решение</a></div>
		<div id='wait' style='display:none'>
			<p align='center'><img style="border:none; padding: 1px" src="<?php echo media_dir();?>/task_wait.png"></p>
		<div>
	</li>
	<li><div class="submenu"><a href="javascript:submenu('solv')">
		Решен</a></div>
		<div id='solv' style='display:none'>
			<p align='center'><img style="border:none; padding: 1px" src="<?php echo media_dir();?>/task_solv.png"></p>
		<div>
	</li>
	<li><div class="submenu"><a href="javascript:submenu('remk')">
		Перерешать</a></div>
		<div id='remk' style='display:none'>
			<p align='center'><img style="border:none; padding: 1px" src="<?php echo media_dir();?>/task_remk.png"></p>
		<div>
	</li>
	<li><div class="submenu"><a href="javascript:submenu('ok')">
		Выполнен</a></div>
		<div id='ok' style='display:none'>
			<p align='center'><img style="border:none; padding: 1px" src="<?php echo media_dir();?>/task_ok.png"></p>
		<div>
	</li>
</ul>

<p>Информация может содержать данные:</p>
<ul>
	<li><font color="#0000FF">Номер</font> - это номер, был получен вами при
	<?php echo url('Загрузке задания', 'TASK', 'add_task', "status=NEW' or status='GETS");?>;
	</li>
	<li><font color="#0000FF">Состояние</font> - отображает ход решения задания.
	И принимает одно из <a href="javascript:submenu('values')">значений:</a>
		<div id='values' style='display:none'>
			<ul>
			<li><div class="submenu"><a href="javascript:submenu('my_rools0')">Мой правила</a> - 
				<?php echo url('задание было задано в режиме &quot;Игра по моим правилом&quot;', 'TASK', 'add_task');?>
				</div>
				<div id='my_rools0' style='display:none'>
					<ul style="font-style: italic">
						<li>
						<p style="word-spacing: 2px">
						<font color="#006600">
						указанная вами стоимость является
						ориентиром для решающих;</font></p></li>
						<li>
						<p style="word-spacing: 2px">
						<font color="#006600">заявку на решение задания могут подать не
						ограниченное количество решающих, при этом они предлагают решение по
						своим ценам; </font></p></li>
						<li>
						<p style="word-spacing: 2px">
						<font color="#0000FF"><u>вы выбираете решающего</u></font><font color="#006600">,
						чьи условия (цена, рейтинг решающего) вас удовлетворяют;</font></p></li>
						<li>
						<p style="word-spacing: 2px">
						<font color="#006600">за </font>
						<font color="#0000FF"><u>стоимость</u></font><font color="#006600">
						задания принимается цена, указанная решающим, которого вы выбрали;</font></p>
						</li>
						<li>
						<p style="word-spacing: 2px">
						<u>
						<font color="#0000FF">время на решение задания</font></u><font color="#006600"> 
						(7 дней и 0 часов в этой таблице)
						отсчитывается <u>с момента выбора решающего</u>.</font></p>
						</li>
					</ul>
				</div>
			</li>
			<li><div class="submenu"><a href="javascript:submenu('fast_start0')">Новый</a> - 
				<?php echo url('задание было задано в режиме &quot;Быстрый старт&quot;', 'TASK', 'add_task');?>
				</div>
				<div id='fast_start0' style='display:none'>
					<ul>
						<li>
						<p style="word-spacing: 2px"><i>
						<font color="#006600">заданная вами </font><u><font color="#0000FF">
						стоимость</font></u></i><font color="#006600"><i> не изменна;</i></font></p>
						</li>
						<li>
						<p style="word-spacing: 2px"><i>
						<u><font color="#0000FF">время на решение задания</font></u><font color="#006600">
						отсчитывается <u>с момента отправки задания в систему</u>;</font></i></p>
						</li>
						<li>
						<p style="word-spacing: 2px"><i>
						<font color="#006600">задание решает тот </font>
						<u>
						<font color="#0000FF">решающий</u></font><font color="#006600">,
						который первый соглашается на решение.</font></i></p></li>
					</ul>
				</div>
			</li>
			<li>
			<font color="#0000FF">
			Идет решение;</font></li>
			<li>
			<font color="#0000FF">Решен</font> - задание готово к скачиванию решения;</li>
			<li>
			<font color="#0000FF">Перерешать</font> - пользователь, Владелец задания (в данном 
			случае &quot;user&quot;), <?php echo url('оценил решение', 'TASK', 'info/how_task');?>
			 в 0 баллов. Задание необходимо перерешать, на это решающему 
			дается 7 дней с момента оценки задания;</li>
			<li>
			<font color="#0000FF">Выполнен</font> - с момента решения задания прошло 7 дней. В 
			течении этого времени Владелец задания (в данном случае &quot;user&quot;) 
			не предъявил претензий к качеству решения задания. Задание считается 
			решенным и Решающий получает деньги за выполненное задание;</li>
		</ul>
		</div>
	</li>
	<li>
	
	<font color="#0000FF">От</font> - логин
	<a title="Пользователь приславший задание">Владельца задания</a><a title="Пользователь приславший задание">;</a></li>
	<li>
	<font color="#0000FF">Решить до</font> - <a href="javascript:submenu('values1')">время на решение задания</a>
		<div id='values1' style='display:none'>
		<ul>
			<li>при указании в формате 'День 
				Месяц Час : Минуты - Год' - задание должно 
				быть решено до указанной даты
			;</li>
			<li>если задание имеет статус <span lang="en-us">'Мои правила<span lang="en-us">', 
			указывается в формате '<span lang="en-us">X 
			дн., <span lang="en-us">Y ч.<span lang="en-us">'.Это 
			значит, что Решающий не назначен, но с момента назначения Решающего 
			(задание переходит в статус <span lang="en-us">'Идет решение<span lang="en-us">'), 
			Решающему дается указанное время на&nbsp; выполнение задание;</li>
		</ul>
		</div>
	
	</li>
	<li>
	<font color="#0000FF">Предмет</font>;</li>
	<li>
	<font color="#0000FF">Раздел</font>;</li>
	<li>
	<font color="#0000FF">Задание</font> - чтобы посмотреть задание кликните по ссылке;</li>
	<li>
	<font color="#0000FF">Стоимость</font> -
	<a title="Пользователь приславший задание">Владелец задания</a> имеет право 
	изменять стоимость пока задание находится в состоянии 
	
	'Новый'
	или 'Мои правила';</li>
	<li>
	<font color="#0000FF">Добавлен</font> - дата загрузки задания в систему;</li>
	<li><font color="#0000FF"><a href="javascript:submenu('values2')">Решающий</a></font></li>
		<div id='values2' style='display:none'>	
		<ul>
		<li>Логин отображается, когда задание находится в состоянии
		'Идет решение', 'Решен','Перерешать' 
		или 'Выполнен';</li>
		<li>Если задание в состоянии 'Мои 
		правила', здесь представлен список Решающих, готовых 
		выполнить задание, и цен за решение задания, которые запросили Решающие.
		<a title="Пользователь приславший задание">Владелец задания</a> может выбрать 
		любого Решающего в любое время</a><a title="Пользователь приславший задание"></a>;</li>
		</ul>
		</div>
	<li><font color="#0000FF"><a href="javascript:submenu('values3')">Решение начато</a></font>
		<div id='values3' style='display:none'>
			<ul>
			<li>Для режима 
						<?php echo url('&quot;Быстрый старт&quot;', 'TASK', 'add_task');?>
						 - указывает время, когда Решающий 
			согласился на решение;</li>
			<li>Для режима 
						<?php echo url('&quot;Игра по моим правилом&quot;', 'TASK', 'add_task');?>
						 - указывает время, когда
			<a title="Пользователь приславший задание">
			Владелец задания</a> выбрал Решающего;</li>
			</ul>
		</div>
	</li>
	<li><font color="#0000FF">Решен</font> - дата решения<span lang="en-us">;</li>
	<li><font color="#0000FF">Решение</font> -
	&nbsp;чтобы скачать решение кликните по ссылке. При 
	скачивании с баланса снимается сумма равная стоимости решения. 
	Владелец задания оплачивает лишь один раз, при повторном получении ссылки 
	деньги с баланса не снимаются.
	Решающий скачивает задание бесплатно;</li>
	<li><font color="#0000FF">Оценка</font> - балл, выставленный
	<a title="Пользователь приславший задание">
	Владельцем задания</a>. Если Вы считаете, что задание 
	выполнено не в полном объеме, и с момента решения прошло менее 7 суток, Вы 
	можете заявить о некачественном решении, установив оценку <span lang="en-us">
	'Я не удовлетворен решением. Перерешать.<span lang="en-us">' 
	Задание Будет перерешано в течении 7 суток или мы вернем Вам деньги. 
	Ознакомьтесь с <?php echo url('Нашими гарантиями', 'INFO', 'guarantee');?>.</li>
</ul>
<p>
	Вы имеете право удалить задание,&nbsp; если оно находится в 
состоянии <span lang="en-us">'Новый<span lang="en-us">' 
или <span lang="en-us">'Мои правила<span lang="en-us">'.</p>
<p>&nbsp;</p>