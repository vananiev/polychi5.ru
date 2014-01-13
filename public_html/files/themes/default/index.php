<link href="<?php echo THEME_ROOT_RELATIVE; ?>/home.css" type="text/css" rel="stylesheet">
</head>
<body>
<?php require('upper_button.php'); ?>
<div id="shower" href="#"></div>
<a id="move_up" href="#">&#9650; Вверх</a>

<div id="main">
			<ul class='indexpagemenu'>
				<li><a href="/" >Главная</a></li>
				<?php
				global $USER, $MENU;
				if($USER!=NULL) {
					echo $MENU->out('MENU');
				}else{ ?>
				<li><a href='/wp/info/about'>О нас</a></li>
				<li><a href='<?php echo url(NULL,'INFO','price')?>'>Стоимость</a></li>
				<li><a href='<?php echo url(NULL,'MONEY','info/oplata')?>'>Как оплатить</a></li>
				<li><a href='<?php echo url(NULL,'INFO','faq')?>'>Частые вопросы</a></li>
				<li><a href='<?php echo url(NULL,'TASK','check')?>'>Статус заказа</a></li>
				<?php } ?>
				<li><a href="/wp/news/">Новости</a></li>
				<?php if($USER!=NULL){ ?>
					<li><?php echo url('Выйти','USERS','exit_user') ?></li>
				<?php } else { ?>
					<li><?php echo url('Войти','USERS','in') ?></li>
				<?php } ?>
			</ul>
<div class="whiter"></div>
<div class="whiter" style="margin:60px 60%;"></div>
<p id="sitename">Получи 5</p>
<p id="description">Выполним контрольные, курсовые, дипломные работы</p>

<div class="aboutas">Нам доверяют более тысячи клиентов по всему СНГ</div>
<div class="aboutas_desc">Тысячи успешных работ, сотни авторов. Ваш заказ в надежных руках.</div>
<a id="show_video" class="button_video play wow fadeIn" data-wow-delay="0.5s">Посмотреть обучающий видео ролик</a>

<div class="whiter" style="right:10%;top:300px; margin:0;"></div>
<div id="buy_button" class="great_btn_around wow fadeIn" data-wow-delay="0.8s"><input class="great_btn" type="button" value="Заказать сейчас" onclick="location.href='<?php echo url(NULL,'TASK','add_task');?>'"/><div class="howbay">Бесплатно. Без регистрации.</div></div>

<div style="text-align:center; margin: 90px 0 40px 0;" >
<div class="benefit wow fadeIn" id="benefit_one">
<div class="grow">
<img src="/images/one.jpg">
<div class="information">Конкурентное формирование цен - вы получаете решение по минимальной рыночной стоимости <a href="<?php echo url(NULL,'INFO','price');?>" class="buttonMore">more</a></div>
</div>
</div>

<div class="benefit wow fadeIn"id="benefit_two">
<div class="grow">
<img src="/images/two.jpg">
<div class="information">Зачем вам тратить лишние деньги? Закажите у нас. Выполним качественно, а вы сэкономите <a href="<?php echo url(NULL,'INFO','why');?>" class="buttonMore">more</a></div>
</div>
</div>

<div class="benefit wow fadeIn" id="benefit_three">
<div class="grow">
<img src="/images/three.jpg">
<div class="information">Решим заказы в любое время суток. Контрольные за час, курсовые за день, диплом за неделю <a href="<?php echo url(NULL,'INFO','guarantee');?>" class="buttonMore">more</a></div>
</div>
</div>
</div>

<div id='how_to_video'>
<div id="close_video" class="pulse">[&#x2718;]</div>
<div class="video">
<iframe width="630" height="462" src="http://youtube.com/embed/Xd0iL84WdXE" frameborder="0" allowfullscreen></iframe>
</div>
</div>


</div>
<?php require_once(THEME_ROOT."/footer.php");	?>
<script type="text/javascript" >
	new WOW().init();
	$("#close_video").click(function () {
	      $("#how_to_video").fadeOut("slow");
	    });
	$("#show_video").click(function () {
	      $("#how_to_video").fadeIn("slow");
	      $('#close_video').addClass('animated bounceInLeft');
	      setTimeout(function(){$('#close_video').removeClass('animated bounceInLeft');}, 1000);
	    });
</script>
<!-- NETROXSC CODE. Theme No. 1 --><script async type="text/javascript" src="http://code.netroxsc.ru/408D7AFF-6FD4-9254-557C-A263E3FA1F30/c.js?tmpl=1"></script>

</body>
</html>