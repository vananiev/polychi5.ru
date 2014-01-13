<?php
	/*******************************************************
					Плагин ICQ менеждера через Web
	*******************************************************/
	
	// настройки
	class IcqOnSite{
		
		var $icq = '624810629';															// ICQ техподдержки
		var $pages = array(array('MODULE' => 'TICKET', 'FILE' => 'icqhelp'),			// на каких страницах отображать ICQ менеждер (при пустом значении отображается везде)
						);
	};
	$IcqOnSite = new IcqOnSite();
	// если отображение плагина разрешено на данной странице то подключим файлы
	if($IcqOnSite->pages == NULL)  $icq_show = true;	// если не указано ни одной страницы показываем на всех страницах
	else							$icq_show = false;
	foreach($IcqOnSite->pages as $page) if($page['MODULE']==$URL['MODULE'] && $page['FILE']==$URL['FILE']) {$icq_show=true; break;}
	if($icq_show==true){
?>
	<script>window.ICQ = {siteOwner:'<?php echo $IcqOnSite->icq;?>'};</script><script src="//c.icq.com/siteim/icqbar/js/partners/initbar_ru.js" language="javascript" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="<?php echo PLUGING_ROOT_RELATIVE.$INCLUDE_PLUGINS['ICQONSITE']."/functions.js";?>"></script>
<?php
	}
?>

