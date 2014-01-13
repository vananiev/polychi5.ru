<?php if(user_in_group('SOLVER') && (isset($_SESSION['user_id']) && $_SESSION['user_id']!=0 ) ) { echo "Вы не можете выполнить заказ"; return;} ?>
<script type="text/javascript" language="JavaScript" src="<?php echo PLUGING_ROOT_RELATIVE."/calendar/calendar_us.js";?>"></script>
<link href="<?php echo PLUGING_ROOT_RELATIVE."/calendar/calendar.css";?>" type="text/css" rel="stylesheet">

<?php if(isset($_SESSION['user_id'])){ ?>
	<h1><?php echo $URL['TITLE']; ?></h1>
	<p class="about_page" style='margin-bottom:100px;'>
		<?php echo url('Ознакомиться с этой страницей', 'TASK', 'info/for_user');?>
	</p>
<?php } else { ?>
	<p>&nbsp;</p>
<?php } ?>

<form  method="POST" action="<?php echo url(NULL, 'TASK', 'add_task_act');?>" enctype="multipart/form-data" name="send_task">

<div class='box_standart order_box'>
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo 1000000*MAX_FILE_SIZE;?>">
	<table class='table_order'>
		<tr>
			<td class='head_text'>Предмет:</td>
			<td colspan='2'>
				<input id='section' class='field' name="section" placeholder="Предмет">
			</td>
		</tr>
		<tr>
			<td class='head_text' style="display:none;">Раздел:	</td>
			<td style='text-align:center;display:none;' colspan='2'>
				<input id='sub_section' class='field' value="-" name="subsection">
			</td>
		</tr>
		<tr>
			<td class='head_text'>Стоимость:</td>
			<td  colspan="2">
				<input type="number" class='field' name="price" id="price" value="40">
				<span class='rub-text'>руб<span>
			</td>
		</tr>
		<tr>
			<td class='head_text'>Решить до:</td>
			<td colspan="2">
				<input type="text" class='field' name="data" value='<?php
					$month_ru = Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
					$month_en = Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
					$d = date("d F Y",time()+3600*24*7);
					for($i=0;$i<12;$i++) $d = str_replace($month_en[$i],$month_ru[$i],$d);
					echo $d;?>'/>
				<script language="JavaScript">
					new tcal ({
						// form name
						'formname': 'send_task',
						// input name
						'controlname': 'data'
						});
					tcal.year_scroll = true;
					tcal.time_comp = true;
				</script>
				<input class='field' type="hidden" name="r_hour" value="0"><!---span id='hour' class='show_info'>часов</span-->
			</td>
		</tr>
		<?php if(false/*отключен режим быстрый старт - в kernel/function.php isset($_SESSION['user_id'])*/) { ?>
		<tr>
			<td class='head_text'>Порядок решения:</td>
			<td class="solving-mode">
				<div class="submenu">
					<input type="radio" value="0" name="mode" >
					<a href="javascript:submenu('fast_start')">&quot;Быстрый старт&quot;</a>
				</div>
				<div id='fast_start' class='box_standart about'>
					<div class='line'>решение начнется после того как первый Автор предложит свои услуги;</div>
					<div class='line' style ='border:none;'>стоимость решения - та которую вы указали;</div>
				</div>
			</td>
			<td class="solving-mode">
				<div class="submenu" style="<?php if(!isset($_SESSION['user_id']))echo "display: none;" ?>">
					<input type="radio" value="1" name="mode" checked>
					<a href="javascript:submenu('my_rools')">&quot;Игра по моим правилам&quot;</a>
				</div>
				<div id='my_rools' class='box_standart about' style="<?php if(!isset($_SESSION['user_id']))echo "display: block;margin:0 25%;" ?>">
					<div class='line'>Авторы предлагают решить задание - Вы выбираете Автора;</div>
					<div class='line' style ='border:none;'>стоимость решения - стоимость, которую предложил выбранный Автор;</div>
				</ul>
				</div>
			</td>
		</tr>
		<?php } else echo "<input type='hidden' name='mode' value='1'>"; 	?>
		<tr>
			<td class='head_text'>Указать задание:</td>
			<td colspan='2'>
				<textarea name="task_info" class='field' id="taskdesc" onClick="if(this.value=='Что нужно сделать:')this.value=''">Что нужно сделать:</textarea>
				<br>
				<input id="filefield" class='field' type="file" name="loadfile" size="13">
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td class='info_text'>Собери файлы задания в архив &nbsp;&nbsp;&nbsp;</td>
			<td class='info_text'>Ознакомьтесь с <?php echo url('рекомендуемыми ценами', 'INFO', 'price',NULL,NULL,"target='_blank'");?>&nbsp;&nbsp;&nbsp;</td>
			<td class='info_text'>Размер файлов до <?php echo MAX_FILE_SIZE;?> МБ</td>
		</tr>
	</table>
	<input id="send-button" type="button" class='mybutton'  value="Решить">
</div>

<div class="box_standart taskLoaded">
  <h2>Оповещения</h2>
  	<span>Как вас оповестить о решении:</span>
    <select name="notificationMethod" id="selectMethod" >
      <option value="phoneNotificate" <?php if(isset($USER)&&$USER['notification']=='phone')echo "selected";?>>смс</option>
      <option value="mailNotificate" <?php if(isset($USER)&&$USER['notification']=='mail')echo "selected";?>>e-mail</option>
      <option value="noNotificate" <?php if(isset($USER)&&$USER['notification']==NULL) echo "selected";?> >не оповещать</option>
    </select>
    <input type="submit" style="display:inline;" value="ok">
    <div id="phoneNotificate">
      <label for="userPhone:">Ваш номер телефона: </label>
      <input id="userPhone" type="text" name="phone" placeholder="79001002030" value="<?php echo isset($USER)?$USER['phone']:""; ?>">
    </div>
    <div id="mailNotificate">
      <label for="userMail">Ваш e-mail: </label>
      <input id="userMail" type="text" name="mail"  placeholder="e-mail" value="<?php echo isset($USER)?$USER['mail']:""; ?>">
    </div>
    <div id="noNotificate">
      Оповещения присылаться не будут. Смотрите статус задания в главном меню по ссылке <?php echo url('Статус заказа','TASK','check');?>
    </div>
</div>

</form>

<script type="text/javascript">
$('#selectMethod').change( function() { showNotificateItem();} );
showNotificateItem();
function showNotificateItem(){
  obj = document.getElementById( document.getElementById("selectMethod").value );
  $('.taskLoaded').children("div").css("display","none");
  obj.style.display = "block";
}


<?php
	if(isset($_GET['section'])) echo "\$('#section').val('".htmlspecialchars($_GET['section'],ENT_QUOTES)."');";
	if(isset($_GET['price'])) echo "\$('#price').val('".htmlspecialchars($_GET['price'],ENT_QUOTES)."');";
?>
var sections=["БЖД", "Биология", "Бухгалтерский учет", "Геодезия", "Гидравлика", "Детали машин", "Информатика", "Логика", "Логистика", "Математика",
"Материаловедение", "Менеджмент", "Металлургия", "Метрология", "Политология", "Правоведение", "Сопромат", "Статистика", "Теория ДВС", "Теория авт. управ.", "Теплотехника", "ТОЭ", "Физика", "Химия", "Черчение", "Электроснабжение", "Энергетика", "Экономика", "Языки" ];
$("#section").autocomplete({
	source: sections,
	autoFocus: true
});
$("#send-button").click(function(){
	$(".order_box").addClass('animated bounceOut order_box_animate');
	$(".order_box").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
		$(this).css('display','none')});
		setTimeout(function(){ $(".taskLoaded").css('display','block').addClass('animated bounceIn')},700);
});
</script>

