<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['TASK']['admin/make_OK']['ANCHOR'], 'TASK', 'admin/make_OK'),
						'IMAGE'=>media_dir().'/images/make_ok.png',
						'DESCRIPTION'=>'Присвоить заданиям статус выполено и выплатить вознаграждение Автору'
					);
	$icons[] = array(	'TITLE'=>url($FILE['TASK']['admin/shtrav']['ANCHOR'], 'TASK', 'admin/shtrav'),
					'IMAGE'=>media_dir().'/images/strav.png',
					'DESCRIPTION'=>'Применить штрафные санкции к Авторам, не выполнившим задания в срок'
				);
	CABINET::show_block('Разделы модуля '.$_('TASK'), $icons);
	unset($icons);
?>