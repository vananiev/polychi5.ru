<?php
	if(!isset($_SESSION['user_id']))
		{
		show_msg(NULL,"Действие запрещено",MSG_CRITICAL);
		return;
		}
?>
<h1><?php echo $URL['TITLE']; ?></h1>
<form style='position:relative;'  action="<?php echo url(NULL, 'USERS', 'admin/update_user_act');?>" method="post" enctype="multipart/form-data" >
	<?php
		/*<div class='user_update_menu'>

			echo url('Основные').'<br>';
			foreach($LINKED_FILE['USERS']['update_user'] as $file)
				echo url($FILE[$file['MODULE']][$file['FILE']]['ANCHOR'], $file['MODULE'], $file['FILE']).'<br>';
		*/
	?>
	</div>
    <table align="center">
    	<tr>
    		<td>Логин:</td>
    		<td><input type="text" name="login" size="20" value="<?php echo $USER['login']; ?>" <?php if(is_ulogin_user()) echo "disabled";?> /></td>
    	</tr>
    	<?php if(!is_ulogin_user()) { ?>
        <tr><td colspan="2"><font color="grey">Если хотите изменить пароль, заполните поля:</font></td>
		</tr>
		<tr>
            <td>Новый пароль:</td>
            <td><input type="password" name="new_password" size="20" /></td>
        </tr>
        <tr>
            <td>Подтвердите новый пароль:&nbsp;&nbsp;&nbsp; </td>
            <td><input type="password" name="new_password_check" size="20" /></td>
            <td></td>
        </tr>
        <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <?php if(user_in_group('USER')){ ?>
        <tr>
            <td>Оповещение с помощью:</td>
            <td>
              <select name="notificationMethod" id="selectMethod">
                <option value="phone" <?php if($USER['notification']=='phone')echo 'selected';?>>смс</option>
                <option value="mail" <?php if($USER['notification']=='mail')echo 'selected';?>>e-mail</option>
                <option value="NULL" <?php if($USER['notification']==NULL) echo 'selected';?> >не оповещать</option>
              </select>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Телефон:</td>
            <td><input type="text" name="phone" size="20" value="<?php echo htmlspecialchars($USER['phone'],ENT_QUOTES); ?>"></td>
            <td>формат: 79001002030</td>
        </tr>
        <?php } ?>
        <tr>
            <td>E-Mail:<?php if(!user_in_group('USER')) echo "<font size='2' color='#FF0000'>*</font>"; ?></td>
            <td><input type="text" name="mail" size="20" value="<?php echo htmlspecialchars($USER['mail'],ENT_QUOTES); ?>"></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Имя Отчество:
			<?php if(!user_in_group('USER')) echo "<font size='2' color='#FF0000'>*</font>"; ?>
			</td>
            <td><input type="text" name="name" size="20" value="<?php echo htmlspecialchars($USER['name'],ENT_QUOTES); ?>"></td>
        </tr>
        <tr>
            <td>Фамилия: <?php if(!user_in_group('USER')) echo "<font size='2' color='#FF0000'>*</font>"; ?></td>
            <td><input type="text" name="surname" size="20"  value="<?php echo htmlspecialchars($USER['surname'],ENT_QUOTES); ?>"></td>
        </tr>
        <?php
        	if(!user_in_group('USER') || $_SESSION['user_id']==0)
        		{ ?>
		        <tr>
		            <td>Город : <font size="2" color="#FF0000">*</font></td>
		            <td><input type="text" name="city" size="20" value="<?php echo htmlspecialchars($USER['city'],ENT_QUOTES); ?>"></td>
		        </tr>
		        <tr>
		            <td>Род занятий: <font size="2" color="#FF0000">*</font></td>
		            <td><input type="text" name="occupation" size="20"  value="<?php echo htmlspecialchars($USER['occupation'],ENT_QUOTES); ?>"></td>
		        </tr>
		        <tr>
		            <td>Возраст: <font size="2" color="#FF0000">*</font></td>
		            <td><input type="text" name="age" size="20" value="<?php  echo htmlspecialchars($USER['age'],ENT_QUOTES); ?>"></td>
		        </tr>
		        <tr>
		            <td>Кошелек<span lang="en-us">:
					<font size="2" color="#FF0000">*</font></td>
		            <td><input type="text" name="koshelek" value="<?php echo htmlspecialchars($USER['koshelek'],ENT_QUOTES); ?>" size="20" /></td>
		            <td><span class='info_text'>Необходимо указать кошелек <span lang="en-us">
					Webmoney (R) или Яндекс.Денег</span></td>
		        </tr>
		        <tr>
		            <td>Как связаться: <font size="2" color="#FF0000">*</font></td>
		            <td><input type="text" name="connect" size="20" value="<?php echo htmlspecialchars($USER['connect'],ENT_QUOTES); ?>"></td>
		            <td><span class='info_text'>необходимо указать аську или скайп</span></td>
		        </tr>
		        <?php } ?>
		        <tr>
		            <td>Ваше текущее время:</td>
		            <td>
		            	<select name="time_zone">
						<optgroup label="Выбрано">
		            			<option value="<?php echo htmlspecialchars($USER['time_zone'],ENT_QUOTES); ?>" ><?php /*echo htmlspecialchars(substr($USER['time_zone'],4,100),ENT_QUOTES)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".*/ echo date('H:i',time()); ?></option>
						</optgroup>

						<optgroup label="Часовые пояса ">
						<?php
							for($i=12;$i>=-12;$i--){
								$v="GMT".($i<0?"":"+").$i;
								//echo "<option value='Etc/$v'>$v";
								//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "<option value='Etc/$v'>";
								if(abs($i)<10) echo "&nbsp;";
								echo gmdate('H:i',time()-$i*3600)."</option>";
								}
						?>
						</optgroup></select>
		            </td>
		            <td><span class='info_text'>необходимо для
					правильного отображения времени</span></td>
		        </tr>
		   <tr>
            <td>Аватар:</td>
            <td><input type="file" name="avatarfile" size="6"></td>
            <td><span class='info_text'>до 100 кБ</span></td>
      </tr>
		<tr><td>&nbsp;</td></tr>
		<!--tr style="display:none;">
				<td style='font-size: 0.8em;'>Введите текущий логин-пароль:</td>
				<td><input type='text' name='login' style='font-size: 9px;color:grey;' value='Логин' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888'; if(this.value=='')this.value='Логин'">
				<input type='password' name='password' style='font-size:9px;color:grey;' value='Пароль' onfocus="this.value='';this.style.color='black';" onblur="this.style.color='#888'; if(this.value=='')this.value='Пароль'"></td>
		</tr-->
        <tr>
            <td height="29"></td>
            <td height="29"><input type="submit" value="Изменить" /></td>
            <?php if(!user_in_group('USER')) {?>
			<td><font size="2" color="#FF0000">*
				поля для обязательны для заполнения
				</font>
			</td>
			<?php } ?>
        </tr>
    </table>
</form>
<style type="text/css">
.user_update_menu{
	position:absolute;top:0;left:0;
	width:15%;
	}
</style>
