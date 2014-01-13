<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php
	bloginfo('name');
	$title = '';
	if ( (is_single() || is_page()) && function_exists('get_post_meta'))
		$title = get_post_meta($post->ID, 'title', true);
	if($title != '')
		echo ' &raquo; '.$title;
	else
		wp_title();
?>
</title>
<meta name="description" content="<?php
	if ( is_single() || is_page() )
		if ( function_exists('get_post_meta'))
			echo get_post_meta($post->ID, 'description', true);
?>"/>
<meta name="keywords" content="<?php
	if ( is_single() || is_page() )
		$posttags = get_the_tags($post->ID);
		if ($posttags) {
  			foreach($posttags as $tag) {
    			echo $tag->name . ',';
  			}
		}
?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed'), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php printf(__('%s Atom Feed'), get_bloginfo('name')); ?>" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/index.css">
<link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/menu.css">
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<?php	require(PLUGING_ROOT."/plugins.php"); ?>

<?php wp_head(); ?>
<script type="text/javascript" src="<?php echo JS_ROOT_REL?>/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?php echo JS_ROOT_REL?>/jquery.easing.1.3.js"></script><?php /* ускорение в анимации */ ?>
<?php /* Список библиотек для эффектов http://mattweb.ru/component/k2/item/104-5-bibliotek-dlya-sozdaniya-yarkih-css-effektov */ ?>
<link href="/files/css/animate.min.css" type="text/css" rel="stylesheet">	<?php /* http://daneden.github.io/animate.css/ */ ?>
<link href="/files/css/hover-min.css" type="text/css" rel="stylesheet"> 	<?php /* http://ianlunn.github.io/Hover/ */ ?>
<script type="text/javascript" src="/files/js/wow.min.js"></script> 		<?php /* https://github.com/matthieua/WOW */ ?>
<link href="/files/css/jquery-ui.min.css" type="text/css" rel="stylesheet"> 	<?php /* autocomplete / */ ?>
<script type="text/javascript" src="/files/js/jquery-ui.min.js"></script> 		<?php /* autocomplete */ ?>
</head>
<body>
  <?php require(THEME_ROOT.'/upper_button.php'); ?>
	<a name="up-mark"></a>
	<!--Main-->
	<div class='main'>
		<?php if( !isset($_COOKIE['how_show_page']) || $_COOKIE['how_show_page']=="" ||  false!==strpos($_COOKIE['how_show_page'], 'header') ) { ?>
		<div class='head'>
			<img src='<?php echo THEME_MEDIA_RELATIVE;?>/images/head.png' height='100%' width='100%'>
			<img class='img_logo' src="<?php echo THEME_MEDIA_RELATIVE;?>/images/logo.png" alt="Получи[5]">
			<?php if($USER==NULL) {?>
				<div class='b_login_text head_a'><a href="<?php echo url(NULL,'USERS','in');?>">Войти</a></div>
				<div class='b_login'><a title='Войти' href="<?php echo url(NULL,'USERS','in');?>"></a></div>
			<?php }else{ ?>
				<div class='b_login_text head_a'><a href="<?php echo url(NULL,'USERS','exit_user');?>">Выйти</a></div>
				<div class='b_logout'><a title='Выйти' href="<?php echo url(NULL,'USERS','exit_user');?>"></a></div>
			<?php } ?>
			<div class='b_order'><a title='Заказать' href="<?php echo url(NULL,'TASK','add_task');?>"></a></div>
			<div class='b_order_text head_a'><a href="<?php echo url(NULL,'TASK','add_task');?>">Заказать</a></div>
			<div class='b_guarantee'><a title='Гарантии'href="<?php echo url(NULL,'INFO','guarantee');?>"></a></div>
			<div class='b_guarantee_text head_a'><a href="<?php echo url(NULL,'INFO','guarantee');?>">Гарантии</a></div>
			<!--div id="icq" class='b_icq b_icq<?php echo 1;//get_ICQ_status('624810629');?>'><a  title='Задать вопрос по ICQ' href="<?php echo url(NULL,'TICKET','icqhelp');?>"></a></div>
			<div class='b_icq_text head_a2'><a href="<?php echo url(NULL,'TICKET','icqhelp');?>">ICQ</a></div-->
			<div class='b_support'><a  title='Служба поддержки' href="<?php echo url(NULL,'TICKET','tickets');?>"></a></div>
			<div class='b_support_text head_a2' ><a href="<?php echo url(NULL,'TICKET','tickets');?>">HELP</a></div>
			<!--menu-->
			<?php require_once(THEME_ROOT."/menu.php");	?>
			<!--/menu-->
		</div>
		<?php } ?>
		<!--upper-->
		<div id="upper_shower" style='position:fixed;top:0;left:0;width:10%;height:30px;z-index:98;' title='Щелкните, чтобы отобразить'></div>
		<div id='upper' class='upper'>
			<div style="position:absolute;width:auto;top:2px;right:20px;">
				<?php 	if(defined('PLUGIN_YANDEX_SHARE')) get_yandex_share(); ?>
			</div>
			<?php include_once(THEME_ROOT.'upper_menu.php') ?>
			<?php if( isset($_COOKIE['how_show_page']) && $_COOKIE['how_show_page'] == "body" ) { ?>
					<img class="logo" src="/images/logo.png"
					title="Сделайте клик, чтобы скрыть/открыть меню" />
			<?php } ?>
		</div>
		<script type="text/javascript">
			function animate_upper(){
				if(parseInt($("#upper").css('top').replace('px',''))<-10)
					$("#upper").animate({ top: "0px"}, 300);
				else
					$("#upper").animate({ top: "-55px"}, 300);
			}
			$(".logo").click(function(){ animate_upper() });
			$('#upper_shower').mouseenter(function(){ animate_upper() });
		</script>
		<!--/upper-->
