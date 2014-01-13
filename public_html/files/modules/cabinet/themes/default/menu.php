<!--!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"-->
<link href="<?php echo CABINET_THEME_ROOT_RELATIVE; ?>/css/menu.css" type="text/css" rel="stylesheet">
<ul id="nav">
	<li ><a href="/">Сайт</a></li>
	<?php if(user_in_group('ADMIN',R_NO_MSG)) echo '<li>'.url('Главная', 'ADMIN', 'admin').'</li>'; ?>
	<li><?php echo url('Профиль', 'USERS', 'admin/update_user') ?> </li>
	<?php if(isset($INCLUDE_MODULES['WORDPRESS']) && defined('WP_ROOT_REL') && $_SESSION['user_id']==0 ) echo "<li><a href='".WP_ROOT_REL."/wp-admin/'>Вордпресс</a></li>"; ?>
	<?php
		if(user_in_group('ADMIN',R_NO_MSG)){ ?>
	<li><a href="#">Модули</a>
		<ul id="subNav">
			<?php foreach ($INCLUDE_MODULES as $name=>$module){
					if( file_exists(SCRIPT_ROOT.$module['PATH'].'/admin/admin.php') ){
						echo '<li>'. url($name, $name, 'admin/admin') .'</li>';
						}
					} ?>
		</ul>
	</li>
	<?php } ?>
	<li><?php echo url('Паспорт', 'USERS', 'about_user', 'user_id='.$USER['id']);?></li>
	<li><?php echo url('Выйти', 'USERS', 'exit_user');?></li>
</ul>
<script type="text/javascript" >
	setSelectItemToActiveForMenu("nav");
	setSelectItemToActiveForMenu("subNav");
</script>
