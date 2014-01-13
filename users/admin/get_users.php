<?php
		/********************************************************************

					Список пользователей он-лайн

		*******************************************************************
		GET:
		see_all - смотреть всех
*/
		if(! isset($_GET['see_all'])) $_GET['see_all']='true';
?>

<?php
	if(!check_right('USR_SEE_USR_LIST', R_MSG)) return; 	//проверка прав
?>
<h1><?php echo $URL['TITLE'];?></h1>
<?php if(isset($_GET['see_all']) && $_GET['see_all']=='true')
	{ ?>
	<p align='center'><?php echo url('Все пользователи', 'USERS', 'admin/get_users', 'see_all=false');?></p>
<?php }else{ ?>
	<p align='center'><?php echo url('Он-лайн пользователи', 'USERS', 'admin/get_users', 'see_all=true');?></p>
<?php } ?>
<table width=100% align='center' style='line-height:200%'>
<tr>
<?php
	$query = "SELECT id,login
			FROM `$table_users`";
	if(isset($_GET['see_all']) && $_GET['see_all']=='true')
		{}
	else
		$query .=" WHERE authorize != ''";
    $res = mysql_query($query.' ORDER BY `id`',$msconnect_users) or die(mysql_error());
    if(mysql_num_rows($res)!=0)
    	echo "Всего пользователей: ".mysql_num_rows($res)."<br>";
    $cnt=0;
     while($row = mysql_fetch_array($res))
    	{
        echo "<td>";
		echo url($row['id'], 'USERS', 'about_user', 'user_id='.$row['id']);
		echo ': '.htmlspecialchars($row['login'],ENT_QUOTES).'</td>';
		$cnt++;
		if($cnt%4 == 0)
			echo '</tr><tr>';
    	}
    if(mysql_num_rows($res)==0)
    	echo '<br><br>нет';
?>
</table>