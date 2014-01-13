<?php
	/****************************************************************

		Скрипт выдающий справку по порядку работы, разной
		для пользователей и решающих

	*****************************************************************/
?>

<?php
	if(!isset($_SESSION['user_id']))
		require(SCRIPT_ROOT."/".$INCLUDE_MODULES['TASK']['PATH']."/info/how_non_reg_user.php");
	else if(!user_in_group('SOLVER')) 				//если ученик или не регистрированный и другие
		require(SCRIPT_ROOT."/".$INCLUDE_MODULES['TASK']['PATH']."/info/how_user.php");
	else if($_SESSION['user_id']==0)
		{ 
		echo url('Для заказчиков', 'TASK','info/how_user');
		echo "<br>";
		echo url('Для решающих', 'TASK','info/how_solver');
		}
	else 			 			//если решающий
		require(SCRIPT_ROOT."/".$INCLUDE_MODULES['TASK']['PATH']."/info/how_solver.php");

?>