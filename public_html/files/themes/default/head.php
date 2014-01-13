<?php
	/*******************************************************
		Шапка сайта
	*******************************************************/
?>
<!DOCTYPE html>
<html>
<head>
	<?php get_title(); ?>
	<!--[IF lte IE 7 ]><link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/index_ie7.css"><![endif]-->
	<!--[IF gte IE 8 ]><link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/index.css"><![endif]-->
	<!--[if IE]><![if !IE]><![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo THEME_MEDIA_RELATIVE;?>/menu.css">
	<!--[if IE]><![endif]><![endif]-->
	<link rel="shortcut icon" href="/images/logo_fav.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo SITE_CHARSET; ?>">
	