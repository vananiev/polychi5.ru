<?php
	/***************************************************************************

						Êîíôèãóðàöèîííûé ôàéë

	***************************************************************************/

	//Ïàïêà ñ àâàòàðêàìè
	define('AVATAR_ROOT_RELATIVE' , MODULES_MEDIA_RELATIVE.$INCLUDE_MODULES['USERS']['PATH']."/avatars/");
	define('AVATAR_ROOT' , DOCUMENT_ROOT.'/'.AVATAR_ROOT_RELATIVE);

	// ãðóïïà ïî óìîë÷àíèþ ïðè ðåãèñòðàöèè
	define('DEFAULT_GROUP',',USER,');	//îáÿçàòåëüíî íà÷èíàåòñÿ è çàêàí÷èâàåòñÿ çàïÿòîé

  // Login and pass hash for sms notification senter smsc.ru
  define("SMSC_LOGIN", "---");
  define("SMSC_PASSWORD", "---");

  // Включить Отзывы о пользователе (должен быть доступен модуль TICKET)
  define('USERS_DIALOG', true);
?>
