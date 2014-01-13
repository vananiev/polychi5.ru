<h2><?php echo $URL['TITLE'];?></h2>
<link rel="stylesheet" type="text/css" href="/files/themes/cabinet/css/module.css">
<?php
	if(user_in_group('ADMIN',R_MSG)){
		$icons = array();
		foreach ($INCLUDE_MODULES as $name=>$module){
			if( file_exists(SCRIPT_ROOT.$module['PATH'].'/admin/admin.php') ){
				$image = media_dir($name).'/images/module.png';
				if( !file_exists( DOCUMENT_ROOT . $image )) $image = ENGINE_MEDIA_RELATIVE.'/images/module_unknown.png';
				$title = url($name, $name, 'admin/admin');
				$icons[] = array('TITLE'=>url($name, $name, 'admin/admin'), 'IMAGE'=>$image, 'DESCRIPTION'=>$module['INFO']['DESCRIPTION']);
				}
			}
		CABINET::show_block('Быстрый доступ к модулям сайта', $icons);
		unset($icons);
		}
?>
