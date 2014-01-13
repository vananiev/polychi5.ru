<?php
	/*******************************************************
		Меню сайта
	*******************************************************/
?>
<ul class='sub-menu'>
	Меню
	<?php
		if(isset($_SESSION['user_id']))
			echo $MENU->out('SUBMENU');	// вывод различного меню в зависимости от группы
		else
			{//не зарегестрированный пользователь
			?>
			<li class='<?php if($URL['FILE']=='why') 	echo 'active'?>' ><a href='<?php echo url(NULL,'INFO','why')?>'>Наши преимущества</a></li>
			<li class='<?php if($URL['FILE']=='otziv') 		echo 'active'?>' ><a href='<?php echo url(NULL,'TICKET','otziv')?>'>Отзывы</a></li>
			<li class='<?php if($URL['FILE']=='contacts') 	echo 'active'?>' ><a href='<?php echo url(NULL,'INFO','contacts')?>'>Контакты</a></li>
			<?php 
			}
	?>
</ul>