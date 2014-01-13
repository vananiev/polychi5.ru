<?php
	/*
	 * Функции работы с яднекс.деньги
	 *
	 *
	 */

/*
 * Вывод таблицы способа оплаты, вызывается из модуля MONEY
 */
function ym_paymant_table(){ 
	global $INCLUDE_MODULES;
	if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id']; // оплата задания
	?>
	<div class="payment-icon">
		<img src="<?php echo media_dir('YANDEX_MONEY').'/images/yandex.png'; ?>"
		onclick="$('#yandex-pay').css('display','block'); $('#yandex-pay input[name=\'paymentType\']').attr('value','PC');"
		/>
		<div class="commission animated">0,5%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('YANDEX_MONEY').'/images/mastercard.png'; ?>"
		onclick="$('#yandex-pay').css('display','block'); $('#yandex-pay input[name=\'paymentType\']').attr('value','AC');"
		/>
		<div class="commission animated">2%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('YANDEX_MONEY').'/images/visa.png'; ?>"
		onclick="$('#yandex-pay').css('display','block'); $('#yandex-pay input[name=\'paymentType\']').attr('value','AC');"
		/>
		<div class="commission animated">2%</div>
	</div>
	<div class="box_standart pay-form" id="yandex-pay">
		<form method="POST" action="<?php echo url(NULL,'YANDEX_MONEY', 'pay'); ?>">
			<input type="text" name="Symma" placeholder="Сумма"><span class="rub">руб</span>
			<input class="pay-ok" type="submit" value="ОК">
			<a onclick="$('#yandex-pay').css('display','none')">Отмена</a>
			<input type="hidden" name="Naznachenie" value="<?php
					global $USER;
					if(isset($id))
						echo "Получи [5]. Оплата за решение задания № ".$id;
					  else if(isset($_SESSION['user_id']))
						echo "Получи [5]. Пополнение баланса пользователя: ".$USER['login'];
				?>">
			<?php if(isset($id)) { ?><input name="id"  type="hidden" value="<?php echo $id; ?>"> <?php } ?>
			<input type='hidden' name='paymentType' value='PC'>
		</form>
	</div>
<?php 
}
?>