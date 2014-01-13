<?php
/*----------------------------------------------------------------------------------------
	
					Функции для работы с кабинетом
	
------------------------------------------------------------------------------------------*/

class CABINET{

/*-----------------------------------------------------------------------------------------
$blockname	- имя блока
$icon = array(array('TITLE'=>'', 'IMAGE'=>'', 'DESCRIPTION'=>'' ), ...) - массив с иконками
*/
function show_block($blockname, &$icons)
{
	echo "<div class='blockcontent'>
	<div class='blockname'>{$blockname}</div>";
	$cnt=0;
	foreach($icons as $icon){
		echo <<< END
		<div class='incon'>
			<img class='iconimg' src="{$icon['IMAGE']}" />
			<div class='iconinfo' >
				<div class='iconhead'>{$icon['TITLE']}</div>
				{$icon['DESCRIPTION']}
			</div>
		</div>
END;
		$cnt++;
		if($cnt%2 == 0) echo "<div style='clear:both;'></div>";
		}
	if($cnt%2 == 1) echo "<div style='clear:both;'></div>";
	echo '</div>';
}	
	

}
?>