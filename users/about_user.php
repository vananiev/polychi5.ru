<?php
		/*********************************************************************
						Сведения об ученике/решающем
		*********************************************************************
		Параметры GET:
		user_id	- id пользователя
		*/
		if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) $user_id = (int)$_GET['user_id'];
?>
<?php
	if(!isset($user_id))
		{
		show_msg(NULL,"Пользователь не найден.",MSG_WARNING,MSG_RETURN);
		return;
		}
	$usr = get_user($user_id);
	if(!$usr){
		show_msg(NULL,"Пользователь не найден.",MSG_WARNING,MSG_RETURN);
		return;
	}
	$user_attr = array(
		'name' => 'Имя', 
		'age' => 'Возраст',
		'occupation' => 'Род занятий',
		'groups' => 'Группа',
		'balance' => 'Баланс',
		);
	$user_attr_admin = array(
		'mail' => 'e-mail',
		'phone' => 'Тел.',
		'notification' => 'Оповещение',
		'connect' => 'Как связаться',
		'koshelek' => 'Кошелек',
		);
	foreach($user_attr as $key => $value) {
		if($usr[$key]=='' or $usr=='-')
			unset($user_attr[$key]);
	}
	foreach($user_attr_admin as $key => $value) {
		if($usr[$key]=='' or $usr[$key]=='-')
			unset($user_attr_admin[$key]);
	}
	if(isset($usr['reg_date']))
		$usr['reg_date'] = date("d M Y",strtotime($usr['reg_date']));
	if(isset($usr['last_visit'])){
		$last_visit_time = strtotime($usr['last_visit']);
		$delta_sec = time() - $last_visit_time;
		$delta_day = date('d',time()) - date('d', $last_visit_time);
		if( $delta_sec < 600 )
			$usr['last_visit'] = 'Онлайн';
		else if( $delta_sec < 3600 )
			$usr['last_visit'] = 'заходил '.round($delta_sec/60).' минут назад';
		else if( $delta_day == 0 )
			$usr['last_visit'] = 'заходил сегодня в '. date('H:i',strtotime($usr['last_visit']));
		else if( $delta_day == 1 )
			$usr['last_visit'] = 'заходил вчера в '. date('H:i',strtotime($usr['last_visit']));
		else
			$usr['last_visit'] = 'заходил '. date('d M',strtotime($usr['last_visit']));
	}
	if(isset($usr['groups']))
		$usr['groups'] = str_replace(',', ' ', $usr['groups']);
	if(isset($usr['balance']))
		$usr['balance'] = $usr['balance'].' руб';
	if(strstr($usr['groups'], 'SOLVER'))
		$usr['rating'] .= '/5';
	else
		$usr['rating'] = ((int)$usr['rating']).' задач';
?>
<div class='passport'>
	<div id='last_visit'><span><?php echo $usr['last_visit'];?></span></div>
	
	<div id='left'>
		<?php output_avatar($user_id);?>
		<div id='main_info'>
			<?php if($user_id == $_SESSION['user_id']) { ?>
			<p><?php echo url('редактировать страницу','USERS','admin/update_user');?></p>
			<?php } ?>
			<p><?php echo (strstr($usr['groups'], 'SOLVER'))?'Рейтинг':'Заказал';?>: <?php echo $usr['rating'];?></p>
			<p>На сайте с <?php echo $usr['reg_date'];?></p>
		</div>
	</div>
	<div id='right'>
		<div id='login'><?php echo htmlspecialchars($usr['login'],ENT_QUOTES);?></div>
		<table>
			<?php foreach ($user_attr as $key => $value) { ?>
			<tr id='<?php echo $key;?>'>
				<td class='user_key'><?php echo $value;?>:</td>
				<td class='user_value'><?php echo htmlspecialchars($usr[$key],ENT_QUOTES);?></td>
			<tr>
			<?php } ?>
			<?php if(check_right('USR_SEE_ALL_INFO')) {
			foreach ($user_attr_admin as $key => $value) { ?>
			<tr id='<?php echo $key;?>'>
				<td class='user_key'><?php echo $value;?>:</td>
				<td class='user_value'><?php echo htmlspecialchars($usr[$key],ENT_QUOTES);?></td>
			<tr>
			<?php } ?>
			<tr id='edit'>
				<td>&nbsp;</td>
				<td><?php echo url('[редактиравать пользователя]','USERS','admin/edit_user',"user_id=".$user_id);?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
<div style="clear:both;"></div>
<?php show_user_dialog($user_id); ?>
