<h2><?php echo $URL['TITLE'];?></h2>
<?php
	if(!user_in_group('ADMIN',R_MSG)) return;
	// по модулю MONEY
	$icons[] = array(	'TITLE'=>url($FILE['MONEY']['admin/get_system_balance']['ANCHOR'], 'MONEY', 'admin/get_system_balance'),
						'IMAGE'=>media_dir().'/images/balance.png',
						'DESCRIPTION'=>'Итог по системе'
					);
	$icons[] = array(	'TITLE'=>url($FILE['MONEY']['admin/money_move']['ANCHOR'], 'MONEY', 'admin/money_move'),
					'IMAGE'=>media_dir().'/images/money_move.png',
					'DESCRIPTION'=>'Переводы средств с счета на счет'
				);
	$icons[] = array(	'TITLE'=>url($FILE['MONEY']['admin/money_pay_by_administrator']['ANCHOR'], 'MONEY', 'admin/money_pay_by_administrator'),
					'IMAGE'=>media_dir().'/images/money_pay_by_administrator.jpg',
					'DESCRIPTION'=>'Вывод средств (оплата авторам)'
				);
	CABINET::show_block('Разделы модуля '.$_('MONEY'), $icons);
	unset($icons);
	//связанные модули
	$searh[] = 'WEBMONEY';
	$searh[] = 'YANDEX_MONEY';
	$searh[] = 'ROBOKASSA';
	foreach ($INCLUDE_MODULES as $name=>$module){
		if ( in_array($name, $searh))
			if( file_exists(SCRIPT_ROOT.$module['PATH'].'/admin/admin.php') ){
				$image = media_dir($name).'/images/module.png';
				if( !file_exists( DOCUMENT_ROOT . $image )) $image = ENGINE_MEDIA_RELATIVE.'/images/module_unknown.png';
				$icons[] = array('TITLE'=>url($name, $name, 'admin/admin'), 'IMAGE'=>$image, 'DESCRIPTION'=>$module['INFO']['DESCRIPTION']);
				}
			}
	CABINET::show_block('Найдены модули для ввода и вывода средств', $icons);
	unset($icons);
?>
