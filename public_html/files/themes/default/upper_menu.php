			<ul id='nav' style='display:block;white-space: nowrap;'>
				<li><a href="/" >Главная</a></li>
				<?php
				global $USER, $MENU;
				if($USER!=NULL) {
					echo $MENU->out('MENU');
				}else{ ?>
				<li><a href='/wp/info/about/'>О нас</a></li>
				<li><a href='<?php echo url(NULL,'INFO','price')?>'>Стоимость</a></li>
				<li><a href='<?php echo url(NULL,'MONEY','info/oplata')?>'>Как оплатить</a></li>
				<li><a href='<?php echo url(NULL,'INFO','faq');?>'>Частые вопросы</a></li>
				<li><a href='<?php echo url(NULL,'INFO','guarantee')?>'>Гарантии</a></li>
				<li><a href='<?php echo url(NULL,'TASK','add_task');?>'>Заказать</a></li>
				<li><a href='<?php echo url(NULL,'TASK','check');?>'>Статус заказа</a></li>
				<?php } ?>
				<li><a href="/wp/news/">Новости</a></li>
				<?php if($USER!=NULL){ ?>
					<?php echo $MENU->out('SUBMENU') ?>
					<li><a href='<?php echo url(NULL,'USERS','admin/update_user')?>'>Настройки</a></li>
          <li><?php echo url('Выйти','USERS','exit_user') ?></li>
				<?php } else { ?>
					<li><?php echo url('Войти','USERS','in') ?></li>
				<?php } ?>
			</ul>
    <script src="<?php echo JS_ROOT_REL?>/flexMenu/flexmenu.min.js"></script>
    <script src="<?php echo JS_ROOT_REL?>/modernizer.standart.js"></script>
	<script type="text/javascript" >
        $('#nav').flexMenu({linkText:"&#8628"});
		setSelectItemToActiveForMenu("nav");
		setSelectItemToActiveForMenu("subNav");
        var more = $('.flexMenu-popup');
        if(more[0] != null){
          more[0].id = 'navMore';
          setSelectItemToActiveForMenu("navMore");
        }
	</script>
