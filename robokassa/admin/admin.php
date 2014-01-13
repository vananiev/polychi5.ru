<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['ROBOKASSA']['admin/see_robokassa_money']['ANCHOR'], 'ROBOKASSA', 'admin/see_robokassa_money'),
						'IMAGE'=>media_dir().'/images/module.png',
						'DESCRIPTION'=>'Ввод средств посредством Робокассы'
					);
	CABINET::show_block('Разделы модуля '.$_('ROBOKASSA'), $icons);
	unset($icons);
	exit();
?>
