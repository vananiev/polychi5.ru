<?php
	/*************************************************************

			Подвал сайта

	**************************************************************/
?>
<div class="footer">
	<div class='footer-content' style='position:absolute; top:43px;'>
		<div class='footer-block' style='margin-left:15%'>	
			<div class='headline'>Контакты</div>
			<table style='border:none; background:none;'>
				<tr>
					<td style='border:none;'>e-mail:</td>
					<td style='border:none;'><a href="mailto:support@polychi5.ru?subject=">support@polychi5.ru</a></td>
				</tr>
			</table>
		</div>
		<div class='footer-block'>
			<div class='headline'>Студентам</div>
			<?php 	
					echo url('Заказать','TASK','add_task');
					echo "<br>";
					echo url('Стоимость заказа','INFO','price');
					echo "<br>";
					echo url('Способы оплаты','MONEY','info/oplata');
					echo "<br>";
					echo url('Вопрос-ответ','INFO','faq');
			?>
		</div>
		<div class='footer-block'>
			<div class='headline'>Информация</div>
			<?php 	echo url('Гаранитии','INFO','guarantee');
					echo "<br>";
					echo url('Наши преимущества','INFO','why');
					echo "<br>";
					echo url('Отзывы','TICKET','otziv');
					echo "<br>";
					/*echo url('Презентация','INFO','how');
					echo "<br><a href='/info/about.html?how_show_page=body'>Флеш-версия сайта</a><br>";*/
			?>
			<a href="<?php echo "http://".MOBILE_DOMAIN; ?>" >Мобильная версия</a>
		</div>
		<div style='clear:both;'></div>
		<div style="position:relative; width:99%; text-align:right; font-family: Trebuchet MS, sans-serif; margin-bottom: 10px;">
			<font size='2' color="#39f"> 
			Copyright&nbsp;©&nbsp;
			<?php 
				echo date("Y",time());
				echo " Заказ контрольных работ, ПОЛУЧИ5";
			?>
			</font>
		</div>
		<div style='position:absolute;left:150px;bottom:4px;color:#888;font-size:10px;<?php if(!isset($_SESSION['user_id']) || $_SESSION['user_id']!=0) echo 'opacity:0;';?>'>
		<?php include(DOCUMENT_ROOT.'/yandex_metrika.php'); ?>
		</div>
	</div>
	<!--div class='footer-up-img'><a href="#up-mark" onclick="return anchorScroller(this, 1000)"></a></div-->
</div>