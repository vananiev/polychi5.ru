<?php
	/*
	 * Функции работы с яднекс.деньги
	 *
	 *
	 */

/*
 * Вывод таблицы способа оплаты, вызывается из модуля MONEY
 */
function robokassa_paymant_table(){ 
	global $INCLUDE_MODULES;
	if(isset($_GET['id']) && is_numeric($_GET['id'])) $id = (int)$_GET['id']; // оплата задания
	?>
	<div class="box_standart pay-form" id="robokassa-pay">
		<form method="POST" action="<?php echo url(NULL,'ROBOKASSA', 'pay'); ?>">
			<input type="text" name="Symma" placeholder="Сумма"><span class="rub">руб</span>
			<input class="pay-ok" type="submit" value="ОК">
			<a onclick="$('#robokassa-pay').css('display','none')">Отмена</a>
			<input type="hidden" name="Naznachenie" value="<?php
					global $USER;
					if(isset($id))
						echo "Получи [5]. Оплата за решение задания № ".$id;
					  else if(isset($_SESSION['user_id']))
						echo "Получи [5]. Пополнение баланса пользователя: ".$USER['login'];
				?>">
			<?php if(isset($id)) { ?><input name="id" type="hidden" value="<?php echo $id; ?>"> <?php } ?>
		</form>
	</div>

	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/robokassa.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7-11%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/qiwi.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7,3%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/svaznoi.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">8%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/euroset.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">8%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/mts.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/russtand.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/vtb.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7%</div>
	</div>
	<div class="payment-icon ">
		<img src="<?php echo media_dir('ROBOKASSA').'/images/alfabank.png'; ?>"
		onclick="$('#robokassa-pay').css('display','block')"
		/>
		<div class="commission animated">7%</div>
	</div>
<?php 
}
?>
