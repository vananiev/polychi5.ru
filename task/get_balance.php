<?php
		/*********************************************************************
						Сведения о балансе пользователя
		**********************************************************************/		
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<?php
	require_once(SCRIPT_ROOT.$INCLUDE_MODULES['MONEY']['PATH']."/kernel/function.php");
	if(isset($_SESSION['user_id']))
		{
		//получаем сведения о балансе
		$query = "SELECT balance
	            FROM `$table_users`
	            WHERE id='{$_SESSION['user_id']}'";
	    $res = mysql_query($query,$msconnect_users) or die(mysql_error());
	    $row = mysql_fetch_array($res);
        ?>

        <p style="font-family:Arial; color:#333; font-size:26px; font-style:bolder;text-align:center;">Ваш баланс: <?php echo $row['balance']; ?> руб</p>
		<?php

		if( user_in_group('SOLVER') )
			//решающий
			{
			//проверяем не должен ли перерешать решающий
			$query = "SELECT status
		            FROM `$table_task`
		            WHERE solver='{$_SESSION['user_id']}'";
		    	$res2 = mysql_query($query,$msconnect_task) or die(mysql_error());
		    	$all_OK=true;
		    	while($row2 = mysql_fetch_array($res2))
			    	{
			    	if($row2['status'] == 'REMK')
			    		{
			    		echo "<p align='center'><span style='color:red;'>Вывод средств не возможен.</span><br>
						Необходимо провести работу над ошибками для задач, имеющих статус <span style='color:#CC0000;'><b><u>'Перерешать'</u></b></span>.<br></p>";
			    		$all_OK=false;
			    		break;
			    		}
			    	}
	            	if($all_OK)
	            		{
					?>
		            <form method="POST" action="<?php echo url(NULL, 'USERS', 'money_out');?>">
		            	<p align="center">Послать запрос на получение денег:</p>
						<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'];?>">
						<p align="center"><input type="text" name="Symma" >
						<input type="submit" value="Вывести" name="go">
						</p>
					</form>
	            		<?php
	            		}
			
			}
		// это анонимный пользователь с балансом больше нуля
		if( is_password_null() && $USER['balance']>0){
			$msg = "Рекомендуем сменить логин и установить ".url('пароль', 'USERS','admin/update_user').
			", чтобы защитить свои деньги на ".url('балансе', 'TASK', 'get_balance',NULL,NULL,'target="_blank"')." ";
			show_msg("Совет", $msg, MSG_INFO, MSG_OK);

		}
			
		pay_form(); //вывод формы способов оплат	
		money_history();
    	}
    else
    	{
		if(!isset($_SESSION['get_balance_0']))//не регистрированные пользователи
			{
			$rel1 = url('войдите', 'USERS', 'in');
			$rel2 = url('зарегистрируйтесь', 'USERS', 'reg_user');
			show_msg(NULL,"Пожалуйста ".$rel1." в систему или ".$rel2.", чтобы завести личный<a href='javascript:submenu(\"vschet\")'>электронный счет</a>",MSG_INFO,MSG_NO_BACK);
			$_SESSION['get_balance_0']=true;
			}
		pay_form();//вывод формы способов оплат
		}			
			
	show_msg("Уведомление о рисках","<a name='infooWM'></a>Предлагаемые товары и услуги предоставляются не по заказу лица 
			либо предприятия, эксплуатирующего систему WebMoney Transfer. 
			Мы являемся независимым предприятием, оказывающим услуги, 
			и самостоятельно принимаем решения о ценах и предложениях. 
			Предприятия, эксплуатирующие систему WebMoney Transfer, 
			не получают комиссионных вознаграждений или иных вознаграждений за 
			участие в предоставлении услуг и 
			не несут никакой ответственности за нашу деятельность.<br>
			Аттестация, произведенная со стороны WebMoney Transfer, лишь 
			подтверждает наши реквизиты для связи и удостоверяет личность. 
			Она осуществляется по нашему желанию и 
			не означает, что мы каким-либо образом связаны с продажами операторов 
			системы WebMoney.",MSG_HELP,MSG_NO_BACK,MSG_HIDDEN,"id='oWM'","left:auto;top:30%;right:5%;width:50%;");
?>