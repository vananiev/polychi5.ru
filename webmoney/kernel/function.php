<?php
	/*
	 * Функции работы с вебмани
	 *
	 *
	 */

function wm_paymant_table(){ 
	global $INCLUDE_MODULES;
	if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id']; // оплата задания
	?>
	<div class="payment-icon">
		<img src="<?php echo media_dir('WEBMONEY').'/images/wm.png'; ?>"
		onclick="$('#wm-pay').css('display','block')"
		/>
		<div class="commission animated">0,8%</div>
	</div>
	<div class="box_standart pay-form" id="wm-pay">
		<form method="POST" action="<?php echo url(NULL,'WEBMONEY', 'pay'); ?>">
			<input type="text" name="Symma" placeholder="Сумма"><span class="rub">руб</span>
			<input class="pay-ok" type="submit" value="ОК">
			<a onclick="$('#wm-pay').css('display','none')">Отмена</a>
			<input type="hidden" name="Naznachenie" value="<?php
					global $USER;
					if(isset($id))
						echo "Получи [5]. Оплата за решение задания № ".$id;
					  else if(isset($_SESSION['user_id']))
						echo "Получи [5]. Пополнение баланса пользователя ".$USER['login'];
				?>">
			<input type="hidden" name="Koshelek" value="<?php echo KOSHELEK; ?>">
			<?php if(isset($id)) { ?><input name="id" type="hidden" value="<?php echo $id; ?>"> <?php } ?>
		</form>
	</div>
<?php 
}
?>