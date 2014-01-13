<?php
	/***************************************************************************

						Конфигурационный файл

	***************************************************************************/
	
	//папка с прикрепляемыми к ответам файлами
	define('TICKET_FILE_RELATIVE', '/m/'.MODULES_MEDIA_RELATIVE.'/'.$INCLUDE_MODULES['TICKET']['PATH'].'/files');
	define('TICKET_FILE', DOCUMENT_ROOT.TICKET_FILE_RELATIVE);
?>