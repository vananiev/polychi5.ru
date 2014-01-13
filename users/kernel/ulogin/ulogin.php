<?php
/*
Plugin Name: uLogin - виджет авторизации через социальные сети
Plugin URI: http://ulogin.ru/
Description: uLogin
Version: 1.8
Author: uLogin
Author URI: http://ulogin.ru/
License: GPL2
*/

require_once 'settings.ulogin.php';

/*
 * Установка настроек плагина
 */



/* 
 * Возвращает код JavaScript-функции, устанавливающей параметры uLogin
 */
function users_ulogin_js_setparams($display_parameters) {
    $ulPluginSettings = new usersuLoginPluginSettings();
    $ulPluginSettings->init($display_parameters);
    $ulOptions = $ulPluginSettings->getOptions();
    if(is_array($ulOptions)) {
            $x_ulogin_params = '';
            foreach ($ulOptions as $key=>$value){
                $x_ulogin_params.= $key.'='.$value.';';
            }
            return 	'<script type=text/javascript>ulogin_addr=function(id,comment) {'.
			'document.getElementById(id).setAttribute("x-ulogin-params","'.$x_ulogin_params.'redirect_uri="+encodeURIComponent((location.href.indexOf(\'#\') != -1 ? location.href.substr(0, location.href.indexOf(\'#\')) : location.href)+ (comment?\'#commentform\':\'\')));'.
			'}</script>';
	}
	return '';
}

/* 
 * Возвращает код div-а с кнопками uLogin
 */
function users_ulogin_div($display_parameters,$id) {
    $ulPluginSettings = new usersuLoginPluginSettings();
    $ulPluginSettings->init($display_parameters);
    $ulOptions = $ulPluginSettings->getOptions();
    $panel = '';
    if (is_array($ulOptions)){
        if ($ulOptions['display'] != 'window')
            $panel = '<div style="float:left;line-height:24px">'.$ulOptions['label'].'&nbsp;</div><div id="'.$id.'" style="float:left"></div><div style="clear:both"></div>';
        else
            $panel = '<div style="float:left;line-height:24px">'.$ulOptions['label'].'&nbsp;</div><a href="#" id="'.$id.'" style="float:left"><img src="http://ulogin.ru/img/button.png" width=187 height=30 alt="МультиВход"/></a><div style="clear:both"></div>';
    }
    return $panel ;
}

/*
 * Возвращает код uLogin для отображения в произвольном месте
 */
function users_ulogin_panel($display_parameters=array(), $id='') {
	global $USER;
	if (!$USER) {
		global $ulogin_counter;
		$ulogin_counter ++;
		$id=($id==''?'uLogin'.$ulogin_counter:$id);
       $panel ='<div id="ulogin"><script src="http://ulogin.ru/js/ulogin.js" type="text/javascript"></script>'.users_ulogin_js_setparams($display_parameters).users_ulogin_div($display_parameters,$id).'</div><script type="text/javascript">ulogin_addr("'.$id.'");uLogin.initWidget("'.$id.'");</script>';
                
	}
	return $panel;
}

/*
 * Обработка ответа сервера авторизации
 */
if($URL['MODULE']=='USERS' && $URL['FILE']=='in')
	$event->add('init', 'users_ulogin_parse_request');
function users_ulogin_parse_request() {
	if (isset($_POST['token'])) {
		$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
		$user = json_decode($s, true);
		if (isset($user['uid'])) {
			$login = 'ulogin_' . $user['network'] . '_' . $user['uid'];
			$user_id = get_user_id($login);
			if ($user_id==NULL) {
				$pass = preg_replace('/W/',null,crypt(microtime));
				$reg_result = reg_user($login, $pass,
					array('mail' => $user['email'], 'name' => $user['first_name'], 'surname' => $user['last_name'], 'photo' => $user['photo']));
				$i = 0;
				$email = explode('@', $user['email']);
				while (!$reg_result && $i<10) {
					$i++;
					$reg_result = reg_user($login, $pass,
					 array('mail' => $email[0] . '+' . $i . '@' . $email[1], 'name' => $user['first_name'], 'surname' => $user['last_name'], 'photo' => $user['photo']));				
				}
				$user_id = get_user_id($login);
			}
			$usr = get_user( $user_id, 'login, password' );
			soft_login($usr['login'], $usr['password']);
			// Редиректим куда надо
			$r_url = '/';
			if(isset($_SESSION['QUERY_STRING'])){
				$r_url=$_SESSION['QUERY_STRING'];
				unset($_SESSION['QUERY_STRING']);
			}
			header("HTTP/1.1 301 Moved Temporarily");
			header("Location: ".$r_url);
			exit();
		}
	}
}

/*
 * Возвращает url аватарки пользователя
 */
function users_ulogin_get_avatar($avatar, $id_or_email) {
        if (is_numeric($id_or_email)) {
                $user_id = get_user_by('id', (int) $id_or_email)->ID;
        } elseif (is_object($id_or_email)) {
                if (!empty($id_or_email->user_id)) {
                        $user_id = $id_or_email->user_id;
                } elseif (!empty($id_or_email->comment_author_email)) {
                        $user_id = get_user_by('email', $id_or_email->comment_author_email)->ID;
                }
        } else {
                $user_id = get_user_by('email', $id_or_email)->ID;
        }
        $photo = get_user_meta($user_id, 'ulogin_photo', 1);
        if ($photo)
                return preg_replace('/src=([^\s]+)/i', 'src="' . $photo . '"', $avatar);

        return $avatar;
}

/*
 * Возвращает текущий url
 */
function users_current_page_url() {
	$pageURL = 'http';
	if( isset($_SERVER["HTTPS"]) ) {
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/*
* Возвращает true/false - является ли пользователь внутренним или залогинен через социальные сети
*/
function is_ulogin_user($user_id=NULL){
	if($user_id == NULL) $user_id = $USER['id'];
	$usr = get_user($user_id,'login');
	return preg_match('/^ulogin_/u', $usr['login']);
}
?>
