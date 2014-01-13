<?php
	/****************************************************************************************
		
		Выдача рекламных ссылок внизу контента перед подвалом сайта

	*****************************************************************************************/
?>

<?php 

	return;

	if(!isset($_SESSION['user_id']) && $URL['MODULE'] == 'TASK' && $URL['FILE'] =='info/how_user_full') {?>
		<div style="bottom:0; float:left;">
			<h5><?php echo url('Заказать контрольную в Минске', 'INFO', 'kontrolnie_minsk', NULL,NULL, "title='Заказать контрольную в Минске'");?></h5>
		</div>
		<?php 
	}
	else if(!isset($_SESSION['user_id']) && $URL['MODULE'] == 'MONEY' && $URL['FILE'] =='info/oplata') {?>
		<div style='position: relative; top: -40px; left:50px;opacity:0.3;'>
			<a href="http://www.megastock.ru/" target="_blank"><img src="<?php echo THEME_MEDIA_RELATIVE;?>/images/acc_blue_on_transp_ru.png" alt="www.megastock.ru" border="0"></a>
			<a href="https://passport.webmoney.ru/asp/certview.asp?wmid=202566958179" target="_blank"><img src="<?php echo THEME_MEDIA_RELATIVE;?>/images/v_blue_on_transp_ru.png" alt="Здесь находится аттестат нашего WM идентификатора" border="0" /></a>
		</div>
	<?php } ?>