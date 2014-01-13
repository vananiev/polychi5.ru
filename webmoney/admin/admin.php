<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['WEBMONEY']['admin/see_webmoney']['ANCHOR'], 'WEBMONEY', 'admin/see_webmoney'),
						'IMAGE'=>media_dir().'/images/module.png',
						'DESCRIPTION'=>'Ввод средств посредством webmoney'
					);
	CABINET::show_block('Разделы модуля '.$_('WEBMONEY'), $icons);
	unset($icons);
?>
