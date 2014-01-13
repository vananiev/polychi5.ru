<?php
/*
			Функции для рнаботы с мессенджером стороннего производителя TalkDriver
*/
	require('config.php');

	// URL запросы к файлам TalkDriver
	//то что должно выводиться без оформления
	if($URL['MODULE'] == 'TICKET' && $URL['FILE'] == "talkdriver/user_iframe"){
		require_once(SCRIPT_ROOT."/ticket/kernel/talkdriver/user_iframe.php");
		exit;	
		}
	else if($URL['MODULE'] == 'TICKET' && $URL['FILE'] == "talkdriver/user_avatar"){
		require_once(SCRIPT_ROOT."/ticket/kernel/talkdriver/user_avatar.php");
		exit;	
		}
	else if($URL['MODULE'] == 'TICKET' && $URL['FILE'] == "talkdriver/mailer"){
		require_once(SCRIPT_ROOT."/ticket/kernel/talkdriver/mailer.php");
		exit;	
		}	
	
//$user_id = ...; // id текущего залогиненного пользователя на вашем сайте
//$to_id = ...; // id пользователя, которому необходимо послать сообщение ==false (открыть окно не выбирая человека из сл. поддержки)
function td_link($user, $to_id=false ){
		
	if(EXTERNAL_IP=='')		$ip = $_SERVER["REMOTE_ADDR"]; // IP-адрес текущего пользователя
	else					$ip = EXTERNAL_IP;
	global $TD_CONSULTANT;
	if($user>=MIN_CONSULTANT_ID){
		$cons_id = (int)$user - MIN_CONSULTANT_ID;
		$user .= ";".$TD_CONSULTANT[$cons_id]['NAME'].";0";
		}
	$to = false;
	if($to_id!==false){
		global 	$TD_CONSULTANT;
		$to = MIN_CONSULTANT_ID+$to_id;														// данные пользователя $to_id
		if(isset($TD_CONSULTANT[$to_id]))$to.=    ";".$TD_CONSULTANT[$to_id]['NAME'].";0";
		}
	if( $to!==false ){
		$key = md5(TD_PASS.$ip.TD_SID.$user.$to); // ключ для контроля правильности ссылки
		$link = "http://".TD_SITENAME."/?sid=".TD_SID."&user=".rawurlencode($user)."&to=".rawurlencode($to)."&key=".$key;
		}
	else{
		$key = md5(TD_PASS.$ip.TD_SID.$user);
		$link = "http://".TD_SITENAME."/?sid=".TD_SID."&user=".rawurlencode($user)."&key=".$key;	
		}
	
	return " href='".$link."' target='msg".TD_SID."'";
}


//---------------------- обратиться к указанному консультанту --------------------------------
function link_for_consultant( $to_id=false ){
	/*
	http://talkdriver.ru/code/?SE=c96605f2#h8.1
	
	http://talkdriver.ru/?sid=<sid>&user=<user>&to=<to>&key=<key>
	где:
	<sid>	ID мессенджера, зарегистрированного на этом сайте, доступно на странице "Вход".
	<user>	данные текущего залогиненного пользователя на вашем сайте в виде: id;ник;пол. Обязательным полем является только id. Пол: 0 - без пола, 1 - мужской, 2 - женский.
	<to>	данные пользователя, кому необходимо послать сообщение, также в виде: id;ник;пол.
	<key>	ключ для защиты от подмены ссылки в формате md5("<psw><ip><sid><user><to>").
	<psw>	пароль, указанный в настройках мессенджера.
	<ip>	IP-адрес текущего пользователя, чтобы нельзя было перехватить ссылку и зайти в окно переписки с другого IP. 
	*/
	
	// если этот пользователь не зарегистрирован и этому пользователю все еще не присвоен ID, то присвиваем
	// уникальный ID из окна
	global $USER;
	if(isset($_SESSION['user_id']))
		$user = $USER['id'];
	else if(isset($_SESSION['td_user']))
		$user = $_SESSION['td_user'];
	else{
		$fname = dirname(__FILE__).'/td_non_reg_user.txt';
		$user = '';  					// данные пользователя $user
		if (file_exists($fname)){
			$fp = fopen($fname, 'r');
			if($fp){
				flock($fp, LOCK_SH); 	// блокирование на чтение
				$rd = fgets($fp, 999);
				flock($fp, LOCK_UN); // Снятие блокировки
				}
			fclose($fp);
			if(is_numeric($rd)) $user = (int)$rd;
			}
		if($user=='')    $user = MAX_USER_ID;
		if($user<MIN_USER_ID) $user = MAX_USER_ID;
		// сохраняем уменьшенное значение SID
		$fp = fopen($fname, 'w');
		flock($fp, LOCK_EX); // Блокирование файла для записи
		$txt = ($user-1);
		fwrite( $fp, $txt );
		flock($fp, LOCK_UN); // Снятие блокировки
		fclose($fp);
		}
	$_SESSION['td_user'] = $user;
	if(isset($_SESSION['user_id'])){ 
		if($USER['name']!='')$user .= ";".$USER['name'].";0";	// ник; пол
		else				 $user .= ";".$USER['login'].";0";
		}
	else							$user .= ";Незнакомец;0";
	return td_link($user, $to_id );
}

//------------------ Выдает число новых сообщений -------------------------------
function td_cnt_new_mes($user){
	if(EXTERNAL_IP=='')		$ip = $_SERVER["REMOTE_ADDR"]; // IP-адрес текущего пользователя
	else					$ip = EXTERNAL_IP;
	return "http://".TD_SITENAME."/newmes.php?sid=".TD_SID."&user=".$user."&key=".md5(TD_PASS.$ip.TD_SID.$user);
}

?>