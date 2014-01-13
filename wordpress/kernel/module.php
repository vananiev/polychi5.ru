<?php
	/***************************************************************************

						Модуль для работы с WordPress

	**************************************************************************/

	if(defined('ABSPATH'))	return;				// работаем из вордпресс, не выполняем код, во избежание зацикливания
	define('IN_ENGINE', true);					// сигнализируем плагин WP, что работаем в движке
	define('WP_ROOT_REL', '/wp');							// путь до вордпресс
	define('WP_ROOT', DOCUMENT_ROOT.'/wp');				// путь до вордпресс
	$event->add('user_registered', 'register_in_wp');
	$event->add('user_deleted', 'delete_in_wp');
	$event->add('logout', 'logout_in_wp');
	$event->add('login', 'login_in_wp');
	$event->add('user_updated', 'user_update_in_wp');
	$event->add('restore_password', 'restore_password_in_wp');

	//------ регистрация пользователя в вордпресс - обработчик события ---------
	function register_in_wp(){
		include_once(WP_ROOT. '/wp-load.php');
		if( func_num_args()<3 ) return;
		//$id = func_get_arg(0);
		$user_name = func_get_arg(1);
		$wp_user_id = username_exists( $user_name );
		if ( !$wp_user_id ){
			$password = func_get_arg(2);
			$usr = get_user(get_user_id($user_name));
			$wp_user_id = wp_create_user( $user_name, $password, $usr['mail'] );
			// Set ulogin avatar in wordpress
			if( $usr['photo']!=NULL )	update_usermeta($wp_user_id, 'ulogin_photo', $usr['photo']);
			if( $usr['name']!=NULL ){
				update_usermeta($wp_user_id, 'first_name', $usr['name']);
				update_usermeta($wp_user_id, 'display_name', $usr['name']);
				wp_update_user( array ('ID' => $wp_user_id, 'display_name' => $usr['name'] ));
			}
			if( $usr['surname']!=NULL ) update_usermeta($wp_user_id, 'last_name', $usr['surname']);
		}
	}
	
	//------ обновление пароля в вордпресс - обработчик события ---------
	function restore_password_in_wp(){
		include_once(WP_ROOT. '/wp-load.php');
		if( func_num_args()<2 ) return;
		$user_name = func_get_arg(0);
		$user_id = username_exists( $user_name );
		$password = func_get_arg(1);
		wp_set_password( $password, $user_id );
	}
	
	//------ удаление пользователя в вордпресс - обработчик события ---------
	function delete_in_wp(){
		//include_once(WP_ROOT. '/wp-load.php');
		include_once(WP_ROOT. '/wp-admin/includes/user.php');
		if( func_num_args()<2 ) return;
		//$id = func_get_arg(0);
		$user_name = func_get_arg(1);
		$user_id = username_exists( $user_name );
		if ( $user_id ){//and email_exists($user_email) == false ) {
			wp_delete_user( $user_id,  1);	// назначаем все посты пользователя администратору (второй параметр=1)
		} else {
			//global $_;
			//echo $_('User not exists.');
		}
	}
	
	//--------------------- возникает при выходе из системы -----------------
	function logout_in_wp(){
		include_once(WP_ROOT. '/wp-load.php');
		wp_logout();
	}

	//--------------------- возникает при входе в систему -----------------
	function login_in_wp(){
		if( func_num_args()<2 ) return;
		include_once(WP_ROOT. '/wp-load.php');

		// user logged in engine
		if(isset($_SESSION['user_id'])){
			//determine WordPress user account to impersonate
			$user_login = func_get_arg(1); 

			//get users password
			$usr = new WP_User(0, $user_login);
			$user_pass = md5($usr->user_pass); 

			//login, set cookies, and set current user
			wp_login($user_login, $user_pass, true);
			wp_setcookie($user_login, $user_pass, true);
			wp_set_current_user($usr->ID, $user_login);
			}
	}
	
	//---------------------- обновляем профиль пользователя в WP -------------
	/*function user_update_in_wp($user_id){
		if(defined('ABSPATH'))	return;				// работаем из вордпресс, не выполняем код, во избежание зацикливания
		$usr = get_user($user_id);
		include_once(WP_ROOT. '/wp-load.php');
		$wp_id = username_exists( $usr['login'] );
		if ( $wp_id ) wp_insert_user( array('first_name'=>$usr['name'], 'last_name'=>$usr['surname'], 'user_email'=>$usr['mail'], 'user_login'=>$usr['login'], 'ID'=>$wp_id) ) ;
		else		  wp_insert_user( array('first_name'=>$usr['name'], 'last_name'=>$usr['surname'], 'user_email'=>$usr['mail'], 'user_login'=>$usr['login'], 'user_pass'=>wp_generate_password()) );

	}*/
	function user_update_in_wp(){
		if(defined('ABSPATH'))	return;				// работаем из вордпресс, не выполняем код, во избежание зацикливания
		if( func_num_args()<2 ) return;	
		$user_id = 	func_get_arg(0); 
		$data = func_get_arg(1); 
		$usr = get_user($user_id);
		include_once(WP_ROOT. '/wp-load.php');
		$wp_id = username_exists( $usr['login'] );
		if ( $wp_id!=NULL ){
			wp_insert_user( array('first_name'=>$usr['name'], 'last_name'=>$usr['surname'], 'user_email'=>$usr['mail'], 'user_login'=>$usr['login'], 'ID'=>$wp_id) ) ;
			if(isset($data['password'])) wp_set_password( $data['password'], $wp_id );;
		}else
			wp_insert_user( array('first_name'=>$usr['name'], 'last_name'=>$usr['surname'], 'user_email'=>$usr['mail'], 'user_login'=>$usr['login'], 'user_pass'=>$data['password']) );
	}
?>
