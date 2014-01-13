<div align="center" >
<div style='width:1200px;'>
	<div class='price_box grow' id='toe'>
		<div class='price_price'>
		100<span class='price_rub'>руб<span>
		</div>
		<div class='price_name'>ТОЭ</div>
		<div class='price_info'>
		<p>Цепи постоянного тока</p>
		<p>Цепи переменного тока</p>
		Переходные процессы и др.
		</div>
	</div>
	<div class='price_box grow' id='phizika'>
		<div class='price_price'>
		50<span class='price_rub'>руб<span>
		</div>
		<div class='price_name'>Физика</div>
		<div class='price_info'>
		<p>Механика</p>
		<p>Колебания и волны</p>
		Электричество и магнетизм и др.
		</div>
	</div>
	<div class='price_box selct grow' id='math'>
		<div class='price_price'>
		40<span class='price_rub'>руб<span>
		</div>
		<div class='price_name'>Математика</div>
		<div class='price_info'>
		<p>Алгебра</p>
		<p>Ананлитическая геометрия</p>
		<p>Теория вероятности</p>
		Пределы и интегралы и др.
		</div>
		<img src="<?php echo MODULES_MEDIA_RELATIVE; ?>/info/best-price.png" style='border:none;position:absolute; top:-35px; left:-30px; opacity:0.8; -moz-opacity:0.8;'>
	</div>
	<div class='price_box grow' id='informatika'>
		<div class='price_price'>
		50<span class='price_rub'>руб<span>
		</div>
		<div class='price_name'>Информатика</div>
		<div class='price_info'>
		<p>С/С++/С#</p>
		<p>Pascal</p>
		<p>Basic</p>
		PHP и др.
		</div>
	</div>
	<div class='price_box grow' style='z-index:5;' id='economica'>
		<div class='price_price'>
		70<span class='price_rub'>руб<span>
		</div>
		<div class='price_name'>Экономика</div>
		<div class='price_info'>
		<p>Бухгалтерский учет</p>
		<p>Банковское дело</p>
		Экономика предприятия и др.
		</div>
	</div>
	<div style='clear:both;'></div>
</div>
</div>

<style type="text/css">
.price_box	{
			float:left;
			display=inline;
			float=none;
			position:relative;
			height:350px;
			width:220px;
			margin: 100px -10px 32px 0;
			border-radius:10px; -moz-border-radius:10px; -webkit-border-radius:10px;
			border:1px #9FDAEE groove;
			background-color:#F4FAFC;
			font-family: "Lucida Sans", "Lucida Grande", "Lucida Sans Unicode", sans-serif;
			color:black;
			z-index:6;
			box-shadow: 0 0 5px black; /* Параметры тени */
			box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Параметры тени */
			-moz-box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Для Firefox */
			-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Для Safari и Chrome */
			cursor:pointer;
			}
.price_box:hover	{
			/*height:400px;width:250px;
			margin: 60px -25px 0 -15px;*/
			z-index:8 !important;
			box-shadow: 0 0 20px #FB0; /* Параметры тени */
			box-shadow: 0 0 20px rgba(255,200,0,0.8); /* Параметры тени */
			-moz-box-shadow: 0 0 20px rgba(255,200,0,0.8); /* Для Firefox */
			-webkit-box-shadow: 0 0 20px rgba(255,200,0,0.8); /* Для Safari и Chrome */
			background-color:#FFFFFC;
			}
.selct	{
			height:400px;width:250px;
			margin: 50px -25px 0 -15px;
			z-index:7;
			}
.selct:hover	{
			height:402px;width:252px;
			margin: 50px -26px 0 -16px;
			}
.price_price{
			position:absolute;
			width:100%; height:50%;
			top:0; left:0; margin:0;
			background-color: #FFA71F;
			border-radius:10px; -moz-border-radius:10px; -webkit-border-top-radius:10px;
			color:#333;
			font-size:80px;
			line-height:140px;
			text-align:center;
			font-weight:bolder;
			text-shadow: grey 7px 3px 10px;
			}
.price_rub	{
			top:-50px;
			font-size:16px;
			}
.price_name{
			position:absolute;
			width:100%; height:20%;
			top:40%; left:0; margin:0;
			background-color: #9FDAEE;
			color: yellow;
			font-size:28px;
			line-height:70px;
			text-align:center;
			font-weight:bolder;
			text-shadow: grey 1px 1px 3px;
			}
.price_info	{
			position:absolute;
			width:80%; 
			top:60%;
			left:0;
			padding:10%;
			font-family: Arial, sans-serif;
			color: black;
			font-size:11px;
			line-height:8px;
			text-align:left;
			font-weight:normal;
			}
.price_info p	{ border-bottom:1px groove grey; width:100%; padding-bottom:8px; } 
</style>

