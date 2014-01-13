<div class="help_body">
	<div class="help_text">
		</div>
	<div class="help_cons_box">	
		<div class="help_cons_text">
			
		</div>
		<div class="help_cons">
			<a <?php echo link_for_consultant(0); ?> >
				<img src="<?php echo MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_1.png'?>" />
				<span class="cons_name">Ирина</span>
				<span class="cons_info">консультант отдела поддержки клиентов</span>
			</a>
		</div>
		<div class="help_cons">
			<a <?php echo link_for_consultant(1); ?> >
				<img src="<?php echo MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_2.png'?>" />
				<span class="cons_name">Наталия</span>
				<span class="cons_info">консультант отдела качества</span>
			</a>
		</div>
		<div class="help_cons">
			<a <?php echo link_for_consultant(2); ?> >
				<img src="<?php echo MODULES_MEDIA_RELATIVE.'/ticket/images/consultant_3.png'?>" />
				<span class="cons_name">Сергей</span>
				<span class="cons_info">консельтант отдела технической поддержки</span>
			</a>
		</div>
	</div>
</div>
<style>
	.help_body {position:relative; margin:0 auto; top:30px; text-align:center;left:20%;height:600px;}
	.help_text {position:absolute; width:100%; color:grey; padding:0 auto 20px auto;}
	.help_cons_text {position:relative; color:grey;}
	.help_cons_box {position:absolute; width:400px; left:0; top:30px; text-align:left;}
	.help_cons {position:relative; border:1px solid #EEE; width:100%; height:120px; left:0; margin:20px 0; border-radius: 10px; box-shadow: 0 0 10px rgba(55, 55, 55, 0.5); background-color: #FAFAFA; }
	.help_cons img {left:0; height:100%;}
	.help_cons .cons_name { position: absolute; left:130px; top: 35%; font-size:20px; color:Blue;}
	.help_cons .cons_info { position: absolute; left:150px; top: 50%; font-size:12px; color:grey; }
</style>

