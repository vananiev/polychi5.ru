<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	//ссылки по модулю
	$icons[] = array(	'TITLE'=>url($FILE['USERS']['admin/edit_group']['ANCHOR'], 'USERS', 'admin/edit_group'),
						'IMAGE'=>media_dir().'/images/user_group.png',
						'DESCRIPTION'=>'Добавление и удаление групп. А также редактирование '.url('прав', 'USERS', 'admin/group_access').' и '.url('меню', 'USERS', 'admin/edit_menu').' для группы'
					);
	$icons[] = array(	'TITLE'=>url($FILE['USERS']['admin/edit_user']['ANCHOR'], 'USERS', 'admin/edit_user'),
						'IMAGE'=>media_dir().'/images/module.png',
						'DESCRIPTION'=>'Добавление пользователя в группу и просмотр сведений'
				);
	$icons[] = array(	'TITLE'=>url($FILE['USERS']['admin/get_users']['TITLE'], 'USERS', 'admin/get_users'),
						'IMAGE'=>media_dir().'/images/user_list.png',
						'DESCRIPTION'=>'Список всех пользователей'
			);
	$icons[] = array(	'TITLE'=>url($FILE['USERS']['admin/recalculate_rating']['TITLE'], 'USERS', 'admin/recalculate_rating'),
						'IMAGE'=>media_dir().'/images/rating.png',
						'DESCRIPTION'=>'Пересчитать рейтинги пользователей'
			);
	CABINET::show_block('Разделы модуля '.$_('USERS'), $icons);
	unset($icons);
?>
