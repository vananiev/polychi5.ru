<?php
	require_once(MOBILE_ROOT."/files.php");
	// for Htaccess contorl then on mobile
	@setcookie('mobile','true', time()+300, "/", DOMAIN);
?>
<!DOCTYPE html>
<html>
<head>
	<?php get_title(); ?>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,  maximum-scale=1.0, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<link rel="shortcut icon" href="http://polychi5.ru/images/logo_fav.ico">
	<link rel="canonical" href="http://polychi5.ru/">
	<link rel="stylesheet" type="text/css" media="all" href="/css/default.css">
	<script type="text/javascript" src="/js/addressbar.js"></script>
	<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/js/jquery.touchslider-1.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery.fontresize.js"></script>
	<script type="text/javascript" src="/js/jquery.tabs.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.min.js">
	</script><script type="text/javascript" src="/js/default.js"></script>
	<link rel="stylesheet" href="/css/colors.css" type="text/css" media="all">
	<link rel="stylesheet" href="/css/custom.css" type="text/css" media="all" >
	<script type="text/javascript" >var RUN_AFTER_LOAD="";</script>
</head>
<body>
