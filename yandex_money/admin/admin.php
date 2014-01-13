<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['YANDEX_MONEY']['admin/see_yandex_money']['ANCHOR'], 'YANDEX_MONEY', 'admin/see_yandex_money'),
						'IMAGE'=>media_dir().'/images/module.png',
						'DESCRIPTION'=>'Ввод средств посредством Яндекс.Деньги'
					);
	CABINET::show_block('Разделы модуля '.$_('YANDEX_MONEY'), $icons);
	unset($icons);
?>