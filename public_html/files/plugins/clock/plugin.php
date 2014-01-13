<?php
	/*******************************************************
					Плагин вывода часов
	*******************************************************/

	
// Вывод часов. принимает параметр Часового пояса И атрибуты настройки div`а
function show_clock($time_zone, $tune)
	{ 
	$timeofs = $time_zone - 1;
	?>
		<div <?php echo $tune;?>>
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="50" height="50">
		<param name="movie" value="<?php echo PLUGING_ROOT_RELATIVE;?>/clock/clock.swf?par=4&moffset=<?php echo $timeofs;?>&arrowcol=EF3030&digitcol=000000&bgnum=6105&opacity=100&mytext=" />
		<param name="quality" value="high" />
		<embed src="<?php echo PLUGING_ROOT_RELATIVE;?>/clock/clock.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="50" height="50" flashvars="par=4&moffset=<?php echo $timeofs;?>&arrowcol=EF3030&digitcol=000000&bgnum=6105&opacity=100&mytext="></embed>
		</object>
		</div>
	<?php
	}
?>