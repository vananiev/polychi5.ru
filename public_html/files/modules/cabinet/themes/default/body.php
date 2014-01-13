</head>
<body>
	<!--Main-->	
	<div class='main'>
		<a name="up-mark"></a>
		<div class='head'">
			<!--span class="button_white"><a href='#'>123</a></span-->
			<?php include('menu.php'); ?>
		</div>

		<!--Content-->
		<div class='content'>
			<?php require (GET_CONTENT()); ?>
		</div>
	</div>
	<!--/Main-->	

	<!--footer-->
	<?php require(CABINET_THEME_ROOT."/footer.php");?>
	<!--foter-->
</body>
</html>