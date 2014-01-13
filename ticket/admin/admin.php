<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['TICKET']['tickets']['ANCHOR'], 'TICKET', 'tickets'),
						'IMAGE'=>media_dir().'/images/question.png',
						'DESCRIPTION'=>'Просмотр запросов в систему'
					);
	$icons[] = array(	'TITLE'=>url($FILE['TICKET']['otziv']['ANCHOR'], 'TICKET', 'otziv'),
					'IMAGE'=>media_dir().'/images/otziv.png',
					'DESCRIPTION'=>'Добавление пользователя в группу и просмотр сведений'
				);
	CABINET::show_block('Разделы модуля '.$_('TIKET'), $icons);
	unset($icons);
?>
