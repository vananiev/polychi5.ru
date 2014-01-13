<?php if($_SERVER['REQUEST_URI']!='/') { ?>
<header class="mt-main-header" style="text-align:center;">
<a class="mt-link-home" href="/"></a>
<h1><?php echo $FILE[$URL['MODULE']][$URL['FILE']]['TITLE'];?></h1>
</header>
<?php } ?>
<div class="mt-navigation">
<?php if($_SERVER['REQUEST_URI']!='/') { ?>
<a href="#" class="mt-menu-btn">Меню</a>
<?php } ?>
<nav>
<?php if(!isset($USER)) { ?>
<ul class="mt-menu mt-menu-accordion">
	<li><a href="/wp/info/about.html">О нас</a></li>
	<li><a href="/task/add_task.html">Заказать</a></li>
	<li><a href="/task/check.html">Проверить заказ</a></li>
	<li><a href="/info/price.html">Стоимость</a></li>
	<li><a href="/money/info/oplata.html">Как оплатить</a></li>
	<li><a href="/task/info/how_user_full.html">Как это работает</a></li>
	<li><a href="/info/faq.html">Частые вопросы</a></li>
	<li><a href="/users/in.html">Войти</a></li>
</ul>
<?php }elseif(user_in_group('USER') ) { ?>
<ul class="mt-menu mt-menu-accordion">
	<li><?php echo url('Мои заказы', 'TASK','tasks'); ?></li>
	<li><a href="/task/check.html">Проверить заказ</a></li>
	<li><a href="/task/add_task.html">Заказать</a></li>
	<li><?php echo url('Мой баланс', 'TASK','get_balance'); ?></li>
	<li><?php echo url('Выйти', 'USERS','exit_user'); ?></li>
</ul>
<?php }elseif(user_in_group('SOLVER') ) { ?>
<ul class="mt-menu mt-menu-accordion">
	<li><?php echo url('Мои заказы', 'TASK','tasks'); ?></li>
	<li><?php echo url('Решать', 'TASK','new_tasks'); ?></li>
	<li><?php echo url('Мой баланс', 'TASK','get_balance'); ?></li>
	<li><?php echo url('Выйти', 'USERS','exit_user'); ?></li>
</ul>
<?php }elseif(user_in_group('ADMIN') ) { ?>
<ul class="mt-menu mt-menu-accordion">
	<li><?php echo url('Заказы', 'TASK','tasks'); ?></li>
	<li><a href="/task/check.html">Проверить заказ</a></li>
	<li><a href="/task/add_task.html">Заказать</a></li>
	<li><?php echo url('Решать', 'TASK','new_tasks'); ?></li>
	<li><?php echo url('Мой баланс', 'TASK','get_balance'); ?></li>
	<li><?php echo url('Выйти', 'USERS','exit_user'); ?></li>
</ul>
<?php } ?>


</nav></div>
