<?php
	/* Выдает аватарки для пользователей из службы поддержки.
	Для использования скрипта необходимо билиотека GD: yum install php-gd */
	
	$id = (int)$_GET["id"];

	// впишите код получения данных пользователя из базы по $id
	if($id>=MIN_CONSULTANT_ID){
		$cons_id = $id - MIN_CONSULTANT_ID; 
		$foto = DOCUMENT_ROOT.'/'.$TD_CONSULTANT[$cons_id]['FOTO'];
		}
	else if($id>=MIN_USER_ID && $id<=MIN_USER_ID)
		$foto = AVATAR_ROOT."/default.png";
	else{
		$files = glob( AVATAR_ROOT.$id.'.*');
		if(!isset($files[0])) $foto = AVATAR_ROOT.'/default.png';
		else				  $foto =  $files[0];
		}
	$ext = end(explode('.', $foto));
	switch($ext){
		case 'jpg':
		case 'jpeg': $img = imagecreatefromjpeg($foto); break;
		case 'gif':  $img = imagecreatefromgif($foto); break;
		default:  $img = imagecreatefrompng($foto);
	}
	$new_img = imagecreatetruecolor(28, 28);

	// вырезаем квадрат из картинки и уменьшаем до 28x28
	$width = min(imagesx($img), imagesy($img));
	imagecopyresampled($new_img, $img, 0, 0, 0, 0, 28, 28, $width, $width);

	header("Content-Type: image/png");

	// кэшировать 10 дней, для уменьшения количества запросов и трафика
	header("Cache-Control: cache");
	header("Expires: ".gmdate("D, d M Y H:i:s", time() + 3600*24*10)." GMT");

	imagejpeg($new_img);
?> 