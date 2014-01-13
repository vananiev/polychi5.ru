<h1><?php echo $URL['TITLE']; ?></h1>
<font color="#333333">При оплате через терминал Вы переводите деньги на Ваш электронный счет Webmoney. Номер этого счета - это номер вашего телефона.
<br>Электронный счет Webmoney универсален - с помощью него Вы можете оплачивать все товары в сети интернет, в том числе и пополнять баланс на нашем сайте.
<br>В последующем, если вы захотите, Вы можете не пользоваться счетом Webmoney. Никакой комиссии с Ваш за существование счета не взымается!
При желании Вы можете завести столько счетов, сколько захотите. </font>
<p></p>
<style>
	.paymentImage { border:none; border-bottom:1px dotted grey; text-align:left;}
	.paymentText  { border:none; border-bottom:1px dotted grey; text-align:left;}
</style>
<table style='width:100%; border-spacing: 0 0; border:none;'>
	<tr>
		<td class="paymentImage" >
		<p align="center">
		<img border="0" src="<?php echo media_dir();?>/webmoney_3.png" width="219" height="344" align="right">
		</td>
		<td class="paymentText"  valign="top" style='font-size:14px;line-height:26px;'>
		<br>1. В меню платежного
		<a target='_blank'  href="https://transfer.guarantee.ru/ATMLists.aspx">терминала</a> 
		найдите WebMoney,в форме ввода R или U кошелька введите 
		07 (для России) или 380 (для Украины) 
		и номер вашего мобильного <font color=grey size=0.7em>(например R079161112233 или U380501112233)</font>.
		<br><br><br>2. Внесите сумму с учетом комиссии терминала; 
		вам придет SMS с паролем.
		<br><br><br>3. На странице пополнения <?php echo url('баланса', 'TASK', 'get_balance',NULL,NULL, "target='_blank'");?> введите 
		номер телефона и пароль,нажмите переидти к оплате.

		<br><br><br>4. Дождитесь SMS с кодом подтверждения и подтвердите платеж.  
	</td>
	</tr>
	<tr>
		<td class="paymentImage" colspan="2">
		&nbsp;<p>&nbsp;</p>
		<a href="javascript:submenu('webmoney_terminal_full',0)">Подробнее...</a></td>
	</tr>
</table>	
<table id='webmoney_terminal_full' style='width:100%; border-spacing: 0 0; border:none; display: none; '>
	<tr>
		<td class="paymentImage">
		<img border="0" src="<?php echo media_dir();?>/Check_Ins_1.jpg" width="247" height="76" >
		</td>
		<td class="paymentText" >
		<p style="line-height: 150%">Найдите ближайший к вам терминал оплаты 
			(терминалы в 
		<a target="_blank" href="https://transfer.guarantee.ru/ATMLists.aspx">России</a> или в
			<a target="_blank" href="http://webmoney.ua/russian/sitepayment/webmoney_check">Украине</a>).<br>В меню 
		платежного терминала выберите WebMoney.
		</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<img border="0" src="<?php echo media_dir();?>/Check_Ins_2.jpg" width="247" height="76"></td>
		<td class="paymentText"   >
		<p style="line-height: 150%">В форме ввода R или U кошелька<br>
		введите 07 (для России)&nbsp;или 380 (для Украины),&nbsp;и далее номер вашего 
		мобильного телефона.<br>
		<font color=grey size=0.7em>Например: если номер телефона (916) 222-33-44, ввести нужно 079162223344</font></td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<img border="0" src="<?php echo media_dir();?>/Check_Ins_3.jpg" width="247" height="76"></td>
		<td class="paymentText"   >
		<p style="line-height: 150%">Если терминал запросит, введите номер мобильного 
		телефона.<br>
		<font color=grey size=0.7em>Например: если номер телефона (900) 222-33-44, ввести нужно 9002223344</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<img border="0" src="<?php echo media_dir();?>/Check_Ins_4.jpg" width="247" height="76"></td>
		<td class="paymentText"   >
		<p style="line-height: 150%">Внесите сумму. <strong>Внимание</strong>: 
		учитывайте комиссию терминала.<br>
		Она не всегда известна заранее, поэтому средства лучше вносить с запасом. 
		Не использованный остаток останется на Вашем счету. Вы можете 
		воспользоваться им в любой момент, при оплате любого товара и услуги в 
		интернет.<br>
		<font color=grey size=0.7em>Максимальная сумма 15 000 рублей, минимальная сумма 10 рублей.</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<img border="0" src="<?php echo media_dir();?>/Check_Ins_5.jpg" width="247" height="76"></td>
		<td class="paymentText"   >
		<p style="line-height: 150%">Терминал распечатает чек.</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<blockquote>
			<img border="0" src="<?php echo media_dir();?>/sms_1.png"></blockquote>
		</td>
		<td class="paymentText"   >
		<p style="line-height: 150%">&nbsp;Вам придет SMS-сообщение с паролем (от абонента
		<a target='_blank'  href="https://check.webmoney.ru/">WM Check</a>).<br>
		Данное сообщение приходит, если вы не зарегистрированы в системе
		<a target='_blank'  href="https://check.webmoney.ru/">WebMoney Check</a>.<br>
		Не удаляйте SMS, пока не сделаете покупку!</p>
		<p style="line-height: 150%">Поздравляем! На этом Вы завели счет
		<span lang="en-us">Webmoney.</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<blockquote>
			<p align="center">
			<img border="0" src="<?php echo media_dir();?>/vvod_tel.png" width="268" height="123" ></blockquote>
		</td>
		<td class="paymentText"   >
		<p style="line-height: 150%">На странице пополнения
		<?php echo url('баланса', 'TASK', 'get_balance',NULL,NULL, "target='_blank'");?>
		 введите Ваш номер телефона и 
		пароль, полученный в <span lang="en-us">SMS после оплаты в 
		терминале (смотрите предыдущее действие) и сумму пополнения. Нажмите 'Переидти к оплате'</td>
	</tr>
	<tr>
		<td class="paymentImage"   >
		<blockquote>
			<p align="center">
			<img border="0" src="<?php echo media_dir();?>/check_12.png" width="216" height="202" ></blockquote>
		<p align="center">
		<p>
		<p>
		</td>
		<td class="paymentText"   >
		<p style="line-height: 150%">На ваш мобильный телефон будет отправлено 
		sms-сообщение с одноразовым платежным кодом – набором из 7 цифр.</p>
		<p style="line-height: 150%">Этот пароль будет известен только Вам. С 
		помощью этого пароля подтверждается, что оплата производится именно 
		Вами.</td>
	</tr>
	<tr>
		<td class="paymentImage"   valign="bottom" >
			<img border="0" src="<?php echo media_dir();?>/check_13.png" width="334" height="323"></td>
		<td class="paymentText"   >
		<ul>
			<li>
			<p style="line-height: 150%">дождитесь СМС с кодом (не обновляя 
			страницу Webmoney Transfer!);</li>
			<li>
			<p style="line-height: 150%">введите полученный код;</li>
			<li>
			<p style="line-height: 150%">нажмите кнопку Платеж 
		подтверждаю.</li>
		</ul>
&nbsp;<p style="line-height: 150%">&nbsp;</td>
	</tr>
	<tr>
		<td class="paymentImage"   valign="top" >
			&nbsp;<img border="0" src="<?php echo media_dir();?>/check_14.png" width="331" height="339"></td>
		<td class="paymentText"   >
			<p style="line-height: 150%">&nbsp;Оплата проведена 
		успешно.</td>
	</tr>
	<tr>
		<td class="paymentImage"  >
		<p align="center">
		<img border="0" src="<?php echo media_dir();?>/Check_9.jpg" width="247" height="76" ></td>
		<td class="paymentText"   >
		<p style="line-height: 150%">Если Вы потратили 
		меньше, чем зачислили через терминал, банкомат или кассу банка, то Вы 
		можете:</p>
		<ul>
			<li>
			<p style="line-height: 150%">оплачивать таким же образом последующие покупки во всей сети интернет;</li>
			<li>
			<p style="line-height: 150%">просмотреть баланс;</li>
		</ul>
		<p style="line-height: 150%"><br>
		Всё это Вы можете делать в Вашем личном кабинете
		<a target='_blank'  href="http://check.webmoney.ru/">WebMoney Check</a>.<br>
&nbsp;</td>
	</tr>
	<tr>
		<td class="paymentImage"  style="border-left-style:none; border-left-width:medium; border-right-style:none; border-right-width:medium; border-bottom-style:none; border-bottom-width:medium">
		&nbsp;</td>
		<td class="paymentText"  valign="top"  style="border-left-style:none; border-left-width:medium; border-right-style:none; border-right-width:medium; border-bottom-style:none; border-bottom-width:medium">
		
		Узнайте больше на Википедия: 
		<a target='_blank'  href="http://wiki.webmoney.ru/wiki/show/Сервис+WebMoney+Check">http://wiki.webmoney.ru/wiki/show/Сервис+WebMoney+Check</a></td>
	</tr>
</table>
<p><br>
&nbsp;</p>