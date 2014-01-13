<a name="slide"></a>
<a href="#slide" id='for_script'></a>
<div class='box_standart slider'>
	<div class='slide_window' id='no1'>
		<img src='<?php echo media_dir_for('INFO');?>/chess.png'>
		<div class='slider_title' >Мы работаем - Вы наслаждаетесь</div>
		<div class='slider_about'>
			Сервис Получи[5] специализируется на решении контрольных работ, домашних заданий и курсовых работ по широкому спектру специальностей.
		</div>
	</div>
	<div class='slide_window' id='no2'>
		<img src='<?php echo media_dir_for('INFO');?>/thebest.png'>
		<div class='slider_title'>Мы лучшие в своем деле</div>
		<div class='slider_about' >
			<ul>
				<li>Вы оплачиваете решение только после того, как Ваш заказ будет выполнен</li>
				<li>C нами работают профессионалы своего дела, много лет занимающиеся в сфере образования</li>
				<li>Вы сами определяете стоимость и сроки решения задания</li>
			<ul>
		</div>
	</div>
	<div class='slide_window' id='no3'>
		<img style='height:100%;' src='<?php echo media_dir_for('INFO');?>/crayons.jpg'>
		<div class='slider_title'>Скорость и качество</div>
		<div class='slider_about'>
			Вы получаете решение в самые короткие сроки. Решение контрольных от 6 часов!
		</div>
	</div>
	<div class='slide_window' id='no4'>
		<img  style='height:100%;' src='<?php echo media_dir_for('INFO');?>/time.jpg'>
		<div class='slider_title'>Получить решение просто</div>
		<div class='slider_about'>
			Мы ценим свое и Ваше время. Загрузив задание, Вы получите e-mail или смс со ссылкой на решение.
		</div>
	</div>
	<div class='slide_window' id='no5'>
		<img style="height:100%;" src='<?php echo media_dir_for('INFO');?>/mouse.jpg'>
		<div class='slider_title'>Отлаженный автоматизированный интерфейс</div>
		<div class='slider_about'>
			Добавление задания в один клик. Оповещение на e-mail о готовности решения к скачиванию. 
		</div>
	</div>
	<div class='slide_window' id='no6'>
		<img style="height:100%;" src='<?php echo media_dir_for('INFO');?>/monets.jpg'>
		<div class='slider_title'>Отсутствие предоплаты</div>
		<div class='slider_about'>
			Только у нас при заказе задания Вы не вносите предоплату. Вы оплачиваете решение только перед его скачиванием.<br>
			Мы говорим: "Нет предоплате!"
		</div>
	</div>
	<div class='slide_window' id='no7'>
		<img style="height:100%;" src='<?php echo media_dir_for('INFO');?>/cards.jpg'>
		<div class='slider_title'>Оплата через терминал</div>
		<div class='slider_about'>
			Вы можете оплатить решение в любом доступном Вам терминале. Это не сложнее, чем пополнение баланса на телефоне.
		</div>
	</div>
	
	<div class='pager'>
		<ul>
			<li id="menu1"><a onclick="show_window(1);">О нас</a></li>
			<li id="menu2"><a onclick="show_window(2);">Лучшие</a></li>
			<li id="menu3"><a onclick="show_window(3);">Качество</a></li>
			<li id="menu4"><a onclick="show_window(4);">Просто</a></li>
			<li id="menu5"><a onclick="show_window(5);">Удобство</a></li>
			<li id="menu6"><a onclick="show_window(6);">Без предоплаты</a></li>
			<li id="menu7"><a onclick="show_window(7);">Терминалы</a></li>
		</ul>
	</div>
</div>

<style type="text/css">
.slider	{
		margin-top:100px; margin-bottom:20px;
		width:80%;max-width:90%; min-width:90%;width=100%;
		height:600px; 
		}
.slide_window{
		position:absolute; top:0; left:0;
		height:100%; width:100%;
		display:none;
		border-radius:20px; -moz-border-radius:20px; -webkit-border-radius:20px;
		}
.slider_title{
		position:absolute;
		bottom:20%; right:5%;
		font-size:40px;line-height:60px; height:60px; font-style:italic; font-weight:bolder;
		font-family: "Lucida Sans", "Lucida Grande", "Lucida Sans Unicode", sans-serif;
		text-shadow: grey 3px 2px 8px;
		color:#FFF;
		opacity:0.9; -moz-opacity:0.9; filter:alpha(opacity=90);
		font-family: sans-serif;
		padding-left: 2%;
		}
.slider_about{
		position:absolute;
		bottom:40%; left:10%;
		width:300px;
		padding:10px;
		font-size:12px;
		font-family:Arial;
		text-align:justify;
		line-height:18px;
		background-color:black; color:white;
		opacity:0.5; -moz-opacity:0.5; filter:alpha(opacity=50);
		}
.slide_window img{
		position:absolute;
		bottom:0;left:0;
		width:100%;
		width=104%;
		height:120%;
		border-radius:20px; -moz-border-radius:20px; -webkit-border-radius:20px;
		}
.pager	{
		position:absolute;
		bottom:2%; right:5%;
		}
.pager li{
		display:inline;
		background:none;
		}
.pager li a{
		color:#999;
		border-radius:20px; -moz-border-radius:20px; -webkit-border-radius:20px;
		padding:2px 5px;
		margin:2px;
		
		border:1px groove #CCC;
		opacity:0.5; -moz-opacity:0.5; filter:alpha(opacity=50);
		}
.pager li a:hover{
		opacity:1; -moz-opacity:1; filter:alpha(opacity=100);
		cursor:pointer;
		}
.menu_selected a{
		background-color:#D9C1FB;
		color:#FFF;
		border:1px groove #D7C9E7;
		}
</style>
<script type="text/javascript" language="JavaScript">
var winW = 0.8*$(window).height();
//Set height and width of size div
$('.slider').css({'height':winW});
$('.pager li a').css({'font-size':winW/40})

var window_no = 1;
var time = 100;
// Показываем начальное окно
<?php
	$agent = get_user_agent();
	if($agent != 'MSIE 6' && $agent != 'MSIE 7')
		echo "opacity(\"no\"+window_no, 100, time);";
	else
		echo "document.getElementById(\"no\"+window_no).style.display='block';";
?>
document.getElementById("menu"+window_no).className += " menu_selected";		//добавляем новый класс к элементу меню

//Проматываем слайд
var cnt=window_no;
var sliding_wnd = false;
sliding_wnd = clearInterval(sliding_wnd);
sliding_wnd = setInterval(function() {
	cnt++;
	if(document.getElementById('no'+cnt) == null) cnt = 1;
	show_window(cnt);
	}, 5000 );
//anchorScroller(document.getElementById('for_script'), 1000);
function show_window(id)
{
	if(id == window_no)
		return;
	obj = document.getElementById("menu"+window_no);		//получаем элемент меню;
	if (obj.className.match(/(^| )menu_selected($| )/)) 	//проверяем назначен ли класс элементу
		obj.className=str_replace(obj.className,"menu_selected"," ");									//удаляем класс из списка классов, назначенных элементу
	obj = document.getElementById("menu"+id);				//получаем элемент меню
	obj.className = " menu_selected";						//добавляем новый класс к элементу
	//opacity("no"+window_no, 0, time); //скрываем старое
	<?php
		if($agent != 'MSIE 6' && $agent != 'MSIE 7')
			echo "opacity(\"no\"+window_no, 0, time);";
		else
			echo "document.getElementById(\"no\"+window_no).style.display='none';";
	?>
	window_no = id;
	cnt = window_no;					// для автопрокрутки
	//opacity("no"+window_no, 100, time);	//показываем новое
	<?php
		if($agent != 'MSIE 6' && $agent != 'MSIE 7')
			echo "opacity(\"no\"+window_no, 100, time);	";
		else
			echo "document.getElementById(\"no\"+window_no).style.display='block';";
	?>
}
</script>
<script type="text/javascript" language="JavaScript">
	// следующая страница будет открыта в нормальном окне
	document.cookie = "how_show_page=''; path='/'; expires=<?php echo time()-36000;?>";
</script>