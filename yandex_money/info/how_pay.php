<h1><?php echo $URL['TITLE']; ?></h1>

<p>1) перейдите по ссылке <?php echo url('Пополнения счета', 'MONEY', 'info/oplata');?>;</p>
<p>2) выберите Яндекс.Деньги пополнения, укажите 'Cумму оплаты' и нажмите 'Переидти к оплате';</p>
<p>3) ознакомьтесь с параметрами платежа и с коммиссией со стороны Яндекс (0,5%) и нажмите 'Оплатить';<br>
	<span class="info_text">Внимание! Комисия всегда округляется до рубля. К примеру если комиссия составляет 0,8 руб, то она будет Округлена до 1 руб.</span>
</p>
<p align="center"><img src="<?php echo media_dir();?>/how_pay_0.png"></p>
<p>4) Яндекс предложит авторизоваться, после этого Вы получите окно для подтверждения оплаты ;</p>
<p align="center"><img src="<?php echo media_dir();?>/how_pay_1.png"></p>
<p>5) подтвердите согласие и Ваш счет будет пополнен;</p>
<p align="center"><img src="<?php echo media_dir();?>/how_pay_2.png"></p>