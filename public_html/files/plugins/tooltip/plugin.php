<?php
	/*******************************************************
					Плагин вывода сообщений
	*******************************************************/
?>
	<link rel="stylesheet" type="text/css" href="<?php echo PLUGING_ROOT_RELATIVE.$INCLUDE_PLUGINS['TOOLTIP']."/tooltip.css";?>">
	<script type="text/javascript" src="<?php echo PLUGING_ROOT_RELATIVE.$INCLUDE_PLUGINS['TOOLTIP']."/functions.js";?>"></script>
<?php
//-----------------------------вывести сообщение------------------------------
function msg_box($title, $text, $class=MSG_INFO, $history_back=MSG_BACK, $show=MSG_SHOW, $attributes='',$style='')
{
	$res = "<span class='tooltips ";
	if($class == MSG_INFO)
		$res .= " custom info";
	else if($class == MSG_HELP)
		$res .= " custom help";
	else if($class == MSG_CRITICAL)
		$res .= " custom critical";
	else if($class == MSG_WARNING)
		$res .= " custom warning";
	else if($class == MSG_CLASSIC)
		$res .= " classic";
	else
		$res .= $class;
	$res .= "'  style='text-align:left;display:";
	if($show==MSG_SHOW)
		$res .= "block";
	else if($show==MSG_HIDDEN)
		$res .= "none";
	else
		$res .= $show;
	$res .= ";{$style};' {$attributes}>";
	//изображение
	if($class != MSG_CLASSIC)
		{
		$res .= "<img src='".PLUGING_ROOT_RELATIVE."/tooltip/";
		if($class == MSG_INFO)
			$res .= "Info.png";
		else if($class == MSG_HELP)
			$res .= "Help.png";
		else if($class == MSG_CRITICAL)
			$res .= "Critical.png";
		else if($class == MSG_WARNING)
			$res .= "Warning.png";
		$res .= "' alt='Помощь' height='48' width='48' />";
		}
	//заголовок
	if($title == NULL)
		{
		if($class == MSG_INFO)
			$res .= "<em>Информация</em>";
		else if($class == MSG_HELP)
			$res .= "<em>Помощь</em>";
		else if($class == MSG_CRITICAL)
			$res .= "<em>Ошибка</em>";
		else if($class == MSG_WARNING)
			$res .= "<em>Ошибка</em>";
		else if($class == MSG_CLASSIC)
			$res .= "";
		else
			$res .= "<em>К сведению</em>";
		}
	else
		$res .= "<em>{$title}</em>";
	//текст
	$res .= $text;
	if($history_back==MSG_BACK)
		$res .= "<a class='tooltip_button' href='javascript:history.go(-1)'>[ok]</a>";
	else if($history_back==MSG_RETURN)
		$res .= "<a class='tooltip_button' href='javascript:history.go(-1)'>[Назад]</a>";
	else if($history_back==MSG_NO_BACK)
		$res .= "";
	else if($history_back==MSG_OK)
		$res .= "<a class='tooltip_button' onmouseup='javascript:submenu(this.parentNode, 0);'>[ok]</a>";
	else
		$res .= $history_back;
	//кнопка закрытия
	$res .="<span
		style='color:red;
		text-decoration:none;
		font-style: normal;
		text-float:right;
		position:absolute;
		right:10px;
		top:3px;
		cursor: pointer;'
		onmouseup='javascript:submenu(this.parentNode, 0);'
		>[x]</span>";
	$res .= "</span>";
	echo $res;	
}
//-----------------------------вывести скрывающийся блок ---------------------
define('BOX_HEAD', 0);
define('BOX_FOOTER', 1);
function show_box( $box_theme='standart', $box_part=BOX_HEAD )
{
global $INCLUDE_PLUGINS;
$agent = get_user_agent();
if($agent != 'MSIE 6' )
	{
	if($box_part == BOX_HEAD)
		echo "
			<img class='box-top' src='".PLUGING_ROOT_RELATIVE."/".$INCLUDE_PLUGINS['TOOLTIP']."/".$box_theme."-up.png'>
				<div class='box-middle'>
					<img class='box-img-content' src='".PLUGING_ROOT_RELATIVE."/".$INCLUDE_PLUGINS['TOOLTIP']."/".$box_theme."-middle.png'>
					<div class='box-content'>";
	else if($box_part == BOX_FOOTER)
		echo "
					</div>
				</div>
			<img class='box-bottom' src='".PLUGING_ROOT_RELATIVE."/".$INCLUDE_PLUGINS['TOOLTIP']."/".$box_theme."-down.png'>";
	}
else
	{
	if($box_part == BOX_HEAD)
		echo "<div class=\"box-content-ie\">";
	else if($box_part == BOX_FOOTER)
		echo '</div>';
	}
}
?>