<?php
	/*******************************************************
		Меню сайта
	*******************************************************/
?>
<ul class='menu'>
	<?php
		$temp = $FILE;
		if(isset($_SESSION['user_id']))
			{
			if( user_in_group('ADMIN') )
				{
				//администратор
				$FILE['TASK']['tasks']['ANCHOR'] 				= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/tasks.png'><div class='menu-text'>Задания</div>";
				$FILE['TICKET']['tickets']['ANCHOR'] 			= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/support_2.png'><div class='menu-text'>Поддержка</div>";
				$FILE['TASK']['admin/make_OK']['ANCHOR'] 		= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/menu-guarantee.png'><div class='menu-text'>Выполнено</div>";
				$FILE['USERS']['admin/get_users']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/users.png'><div class='menu-text'>Участники</div>";
				echo $MENU->out('MENU');
				}
			else if( user_in_group('SUPPORT') )
				{
				//техническая поддержка
				$FILE['TASK']['tasks']['ANCHOR'] 		= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/tasks.png'><div class='menu-text'>Задания</div>";
				$FILE['TICKET']['tickets']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/support_2.png'><div class='menu-text'>Поддержка</div>";
				//$FILE['TASK']['admin/make_ok']['ANCHOR'] 		= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/menu-guarantee.png'><div class='menu-text'>Выполнено</div>";
				$FILE['USERS']['admin/get_users']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/users.png'><div class='menu-text'>Участники</div>";
				echo $MENU->out('MENU');
				}
			else if(user_in_group('USER'))
				{
				//ученик
				$FILE['TASK']['tasks']['ANCHOR'] 		= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/folder_yellow.png'><div class='menu-text'>Мои задания</div>";
				$FILE['TASK']['add_task']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/order_2.png'><div class='menu-text'>Заказать</div>";
				$FILE['TASK']['info/for_user']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/training.png'><div class='menu-text'>Обучение</div>";
				$FILE['TASK']['get_balance']['ANCHOR']= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/menu-price.png'><div class='menu-text'>Баланс</div>";
				echo $MENU->out('MENU');
				}
			else if(user_in_group('SOLVER'))
				{
				//решающий
				$FILE['TASK']['tasks']['ANCHOR'] 		= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/folder_green.png'><div class='menu-text'>Мои решения</div>";
				$FILE['TASK']['new_tasks']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/menu-solv.png'><div class='menu-text'>Решать</div>";
				$FILE['TASK']['info/for_user']['ANCHOR'] 	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/training.png'><div class='menu-text'>Обучение</div>";
				$FILE['TASK']['get_balance']['ANCHOR']	= "<img class='img-menu' src='".THEME_MEDIA_RELATIVE."/images/menu-price.png'><div class='menu-text'>Баланс</div>";
				echo $MENU->out('MENU');
				}
			else if(user_in_group('BANED'))
				{
				//заблокированный
				show_msg(NULL, "Вы заблокированы",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW,"","top:auto;left:auto;bottom:5%,right:5%");
				require(SCRIPT_ROOT."/users/exit_user.php");
				exit;
				}
			else
				{
				//подделка статуса, не существующий статус
				show_msg(NULL, "Вы не состоите ни в одной группе",MSG_CRITICAL,MSG_NO_BACK,MSG_SHOW,"","top:auto;left:auto;bottom:5%,right:5%");
				require(SCRIPT_ROOT."/users/exit_user.php");
				exit;
				}
			}
		else
			{//не зарегестрированный пользователь
			?>
			<li class='<?php if($URL['FILE']=='') 			echo 'active'?>' 		>		<a href='/'><img class='img-menu' src='<?php echo THEME_MEDIA_RELATIVE;?>/images/menu-home.png'><div class='menu-text'>Главная</div></a></li>
			<li class='<?php if($URL['FILE']=='price') 		echo 'active'?>' 		>		<a href='<?php echo url(NULL,'INFO','price')?>'>	<img class='img-menu' src='<?php echo THEME_MEDIA_RELATIVE;?>/images/menu-price.png'><div class='menu-text'>Стоимость</div></a></li>
			<li class='<?php if($URL['FILE']=='info/oplata') echo 'active'?>' 		>		<a href='<?php echo url(NULL,'MONEY','info/oplata')?>'>	<img class='img-menu' src='<?php echo THEME_MEDIA_RELATIVE;?>/images/menu-oplata.png'><div class='menu-text'>Оплата</div></a></li>
			<li class='<?php if($URL['FILE']=='how_user_full') 	echo 'active'?>' 		>		<a href='<?php echo url(NULL,'TASK','info/how_user_full')?>'><img class='img-menu' src='<?php echo THEME_MEDIA_RELATIVE;?>/images/menu-guarantee.png'><div class='menu-text'>Как заказать</div></a></li>
			<?php
			}
	$FILE = $temp;
	unset($temp);
	?>
</ul>
