<?php
	if(!user_in_group('SOLVER',R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE']; ?></h1>

<p>1. В главном меню перейдите по ссылке <?php echo url('Решать', 'TASK', 'new_tasks');?>;</p>
<p>2. Преходите по заданию, щелкнув по его номеру. Ознакамливаетесь с заданием (нажать ссылку 'открыть');</p>
<p>3. Если вы готовы взяться за задание, указываете цену которую хотите за решение и нажимаете 'Подать заявку на решение';</p>
<p>4. Когда заказчик вас выберет задание переместится в пункт меню <?php echo url('\'Таблица заданий\'', 'TASK', 'tasks');?>,
	с этого момента нужно начать решать. Его нужно решить до срока указанного в поле 'Решить до';</p>
<p>5. Чтобы отослать решение:</p>
<p>
	- перейдите по ссылке '<?php echo url('Таблица заданий', 'TASK', 'tasks', 'solver='.$_SESSION['user_id']);?>' в главном меню;
</p>
<p>
	- выберите задание, которое решили, нажав по кнопке 'Обзор', укажите файл с решением (если файлов много предварительно упакуйте их в один архив);
</p>
<p>
	- нажмите по кнопке 'Отправить решение';
</p>
<p>
	- состояние задания сменится c 'Идет решение' на 'Решен';
</p>
<p>6. Оплата:</p>
<p>- В течении 7 суток заказчик скачивает решение и, если задание решено 
верно, его состояние сменится с 'Решен' на 'Выполнен', 
при этом на <?php echo url('Ваш баланс', 'TASK', 'get_balance');?>
 зачислится сумма за решение задания;</p>
<p>- Если задание решено не верно, в течении 7 суток состояние задание поменяется 
с 'Решен' на 'Перерешать'. Вы должны в течении 7 дней отослать верное решение. Дата, до 
которой необходимо перерешать задание будет указана в столбце 'Решить до';</p>
<br>
<p>Форс-мажорные ситуации:</p>
<p>
	- Если заказчик не оплачивает решение, то и вам оплачивается 50% предоплаты, которую внес заказчик;</p>
</p>
<p>
	- В случае, если вы не решите задание в указанный срок, на вас будет наложен штраф в размере <?php echo 100*STRAV;?>% стоимости решения задания. Будьте внимательны!
</p>
<p>
	- Если, Вы назначены Решающим (т.е. задание находится в '<?php echo url('Таблица заданий', 'TASK', 'tasks');?>), но Вы не можете по каким-либо обстоятельствам решить задание, немедленно обратитесь в службу поддержки (внизу справа на любой странице кнопка "Задать вопрос")!
</p>
