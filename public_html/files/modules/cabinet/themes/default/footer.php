<?php
	/*************************************************************

						Подвал кабинета

	**************************************************************/
?>
<div class="footer">
	<div class='footer-content' style='position:absolute; top:43px;'>
		<div class='footer-block' style='width:40%;'>	
			<div class='headline'>Контакты</div>
			<table style='border:none;'>
				<tr>
					<td style='border:none;'>e-mail:</td>
					<td style='border:none;'><a href="mailto:an-vitek@yandex.ru?subject=Engine">an-vitek@yandex.ru</a></td>
				</tr>
			</table>
		</div>
		<div class='footer-block' style='width:25%;'>
			<div class='headline'>Ссылки</div>
			<!--
			<?php 	
					echo url('Админка','ADMIN','admin');
					echo "<br>";
			?> -->
		</div>
		<div class='footer-block' style='width:25%;'>
			<div class='headline'>Информация</div>
			<!--
			<?php 	echo url('Пользователь','USERS','admin/edit_users');
					echo "<br>";
			?> -->
		</div>
		<div style='clear:both;'></div>
		<div style="position:relative; width:99%; text-align:right; font-family: Trebuchet MS, sans-serif;">
			<font size='2' color="#7FAEFF"> 
			Copyright&nbsp;©&nbsp;
			<?php 
				echo date("Y",time());
				echo " Свободный движок сайта, Engine";
			?>
			</font>
		</div>
		<div style='position:absolute;left:0;bottom:0;color:#888;font-size:10px;'>
			Сгенерирована за &nbsp;
			<?php printf('%.2f',1000*(microtime(true) - $start_time)); ?>
			&nbsp;мс<br>
			Запросов к БД: &nbsp;
			<?php
			$cnt = 0;
			foreach($MODULES as $key=>$mod)
				if(isset($mod['DB'])){
					$cnt += count($mod['DB']->last_query); 
					//echo $key;var_dump($mod['DB']->last_query);
					}
			echo $cnt; 
			?>
		</div>
		<div style='position:absolute;left:150px;bottom:4px;color:#888;font-size:10px;<?php if(!isset($_SESSION['user_id']) || $_SESSION['user_id']!=0) echo 'opacity:0;';?>'>
			<!--LiveInternet counter--><script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img src='//counter.yadro.ru/hit?t25.5;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";h"+escape(document.title.substring(0,80))+";"+Math.random()+
			"' alt='' title='LiveInternet: показано число посетителей за"+
			" сегодня' "+
			"border='0' width='88' height='15'><\/a>")
			//--></script><!--/LiveInternet-->
		</div>
	</div>
	<div class='footer-up-img'><a href="#up-mark" onclick="return anchorScroller(this, 1000)"></a></div>
</div>