</head>
<body>
  <?php require('upper_button.php'); ?>
	<div id="shower" href="#"></div>
	<a id="move_up" href="#" name="up-mark" style="padding-top: 40px;z-index:1;">&#9650; Вверх</a>

	<!--Main-->
	<div class='main'>
		<!--upper-->
		<div id="upper_shower" style='position:fixed;top:0;left:0;width:10%;height:30px;z-index:98;' title='Щелкните, чтобы отобразить'></div>
		<div id='upper' class='upper'>
			<div style="position:absolute;width:auto;top:2px;right:20px;">
				<?php 	if(defined('PLUGIN_YANDEX_SHARE')) get_yandex_share(); ?>
			</div>
			<?php include_once(THEME_ROOT.'upper_menu.php') ?>
			<?php if( isset($_COOKIE['how_show_page']) && $_COOKIE['how_show_page'] == "body" ) { ?>
					<img class="logo" src="/images/logo.png" title="Сделайте click, чтобы скрыть/открыть меню" />
			<?php } ?>
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
		</div>
		<!--/upper-->

		<!--Content-->
		<div class='content'>
			<?php
			// Выводим контент
			require (GET_CONTENT());
			//поддон с рекламными и продвигаемыми ссылками
			require(THEME_ROOT.'/poddon.php');
			//Вывод настройки часов
			$rel = url('часовой пояс', 'USERS', 'update_user');
			show_msg("Настройка часов", 'Настройте '.$rel, MSG_CLASSIC, MSG_NO_BACK, MSG_HIDDEN, "id='clock_tune'");
			?>
		</div>
		<!--/Content-->

		<!--bottom_info_pic-->
		<div class="bottom_info_pic">
			<?php
				//фоновый рисунок внизу сайта
				$img = "/images/".str_replace('/', '-', $URL['FILE']).".png";
				if(file_exists(DOCUMENT_ROOT.$img)) { ?>
					<img   border='0' src='<?php echo $img; ?>'>
			<?php }	?>
		</div>
		<!--/bottom_info_pic-->
	</div>
	<!--/Main-->

	<!--footer-->
	<?php require_once(THEME_ROOT."/footer.php");	?>
	<!--/foter-->

	<!-- NETROXSC CODE. Theme No. 1 -->
	<script async type="text/javascript" src="http://code.netroxsc.ru/408D7AFF-6FD4-9254-557C-A263E3FA1F30/c.js?tmpl=1"></script>
</body>
</html>
