<?php /*
	����� ������������� ��������� ��������� ������� � ���� ��� ����������.
	������ �������� YES ��� ����������� �������.
	��� ���������� ��� ����������� ������� ������ � �������.
*/
	//���������� ������
	ini_set("display_errors","0");
	ini_set("display_startup_errors","0");
	ini_set('error_reporting', E_NONE);
?>
<?php
	require_once(SCRIPT_ROOT."/users/kernel/DB.php");
	require_once(SCRIPT_ROOT."/webmoney/kernel/DB_pay.php");
	header("http/1.0 200 Ok");
	header("Content-Type: text/html;charset=windows-1251");
	
	//��� ��������������� ������
	if(isset($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST']==1)
		{
			//�������� ������
			if(!isset($_POST['LMI_MODE']) || $_POST['LMI_MODE']==1)
				{
				echo "��������������� ������: �������� ������� �� ���������.";
				exit;
				}
			//������ ������ � ������� � ��
			
			$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];

			//�������� �������� �������
				$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
				$row = mysql_fetch_array($r);
				if ( mysql_num_rows($r) == 1 )
					{
					if($row['Koshelek_prodavcha']!=$_POST['LMI_PAYEE_PURSE'] || 
						$row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT'])
						{	
						echo "��������������� ������: ������� ������� ��� �����.";
						exit;
						}
					/* ������ �� ��������� ����� �������� � ���� (�������� ������ ��������, ���� ������ ����� webMoneyCheck)
					if(isset($_POST['LMI_WMCHECK_NUMBER']) && $row['fone']!=$_POST['LMI_WMCHECK_NUMBER'])
						{	
						echo "��������������� ������: ������� ����� ��������.";
						exit;
						}*/
					//�������� id ����������
					if(!isset($_POST['user_id']) || $row['id_pokypatelya']!=$_POST['user_id'])
						{	
						echo "��������������� ������: ������� ����� ����������.";
						exit;
						}		
					}
				else
					{
					echo "��������������� ������: � �� �� ������� ������ � �������.";
					exit;
					}
			$LMI_MODE = mysql_real_escape_string($_POST['LMI_MODE'],$msconnect_pay);
			$LMI_PAYER_PURSE = mysql_real_escape_string($_POST['LMI_PAYER_PURSE'],$msconnect_pay);
			$LMI_PAYER_WM = mysql_real_escape_string($_POST['LMI_PAYER_WM'],$msconnect_pay);
			mysql_query("UPDATE `$table_pay`
				SET testovii_platezh='{$LMI_MODE}',
				Koshelek_pokypatelya='{$LMI_PAYER_PURSE}',
				wmid_pokypatelya='{$LMI_PAYER_WM}',
				nomer_scheta_vm='-',
				nomer_platezha_vm='-',
				status='NOFINISH'
				WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
				or die("��������������� ������: �� ������� �������� ���������� � ������� � ���� ������.".mysql_error());

		//����������� �������. ������� ����������
		echo "YES";
		exit(0);
		}
	//��� ���������� � �������
	else if(isset($_POST['LMI_SYS_INVS_NO']))
		{
			//�������� ������
			if(!isset($_POST['LMI_MODE']) || $_POST['LMI_MODE']==1)
				{
				echo "��������������� ������: �������� ������� �� ���������.";
				exit;
				}
			//������ ������ � ������� � ��
			
			$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];

			//�������� �������� �������
				$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
				$row = mysql_fetch_array($r);
				if ( mysql_num_rows($r) == 1 )
					{
					if($row['Koshelek_prodavcha']!=$_POST['LMI_PAYEE_PURSE'] ||
						$row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT'] ||
						$row['testovii_platezh']!=$_POST['LMI_MODE'] ||
						$row['Koshelek_pokypatelya']!=$_POST['LMI_PAYER_PURSE'] ||
						$row['wmid_pokypatelya']!=$_POST['LMI_PAYER_WM'])
						{echo "���������� � �������: ������� �������, �����, ���� ��������� �������, ������� ����������, ������� ��� wmid ����������";
						exit;}
					/* ������ �� ��������� ����� �������� � ���� (�������� ������ ��������, ���� ������ ����� webMoneyCheck)
					if(isset($_POST['LMI_WMCHECK_NUMBER']) && $row['fone']!=$_POST['LMI_WMCHECK_NUMBER'])
						{	
						echo "���������� � �������: ������� ����� ��������.";
						exit;
						}*/
					//�������� id ����������
					if(!isset($_POST['user_id']) || $row['id_pokypatelya']!=$_POST['user_id'])
						{	
						echo "���������� � �������: ������� ����� ����������.";
						exit;
						}
					}else
					{echo "���������� � �������: � �� �� ������� ������ � �������.";
					exit;}
			$LMI_SYS_INVS_NO = mysql_real_escape_string($_POST['LMI_SYS_INVS_NO'],$msconnect_pay);
			$LMI_SYS_TRANS_NO = mysql_real_escape_string($_POST['LMI_SYS_TRANS_NO'],$msconnect_pay);
			$LMI_SYS_TRANS_DATE = mysql_real_escape_string($_POST['LMI_SYS_TRANS_DATE'],$msconnect_pay);
			mysql_query("UPDATE `$table_pay`
				SET nomer_scheta_vm='{$LMI_SYS_INVS_NO}',
				nomer_platezha_vm='{$LMI_SYS_TRANS_NO}',
				data_platezha='{$LMI_SYS_TRANS_DATE}'
				WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
				or die("���������� � �������: �� ������� �������� ���������� � ������� � ���� ������.".mysql_error());

		//�������� �������
		if( (float)$row['Symma']==(float)$_POST['LMI_PAYMENT_AMOUNT'] )	// $_POST['LMI_PAYMENT_AMOUNT']  ���������� � ������� ������ 1.00, a $row['Symma'] - ��� �����
			{												// ��� ���������� ���� ���������� $_POST['LMI_PAYMENT_AMOUNT'], ��� �������, ��� �� ����� $row['Symma']
			$str =  KOSHELEK.$_POST['LMI_PAYMENT_AMOUNT'].$Nomer_paltezha.$_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].
					Secret_Key.$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'];
			$Hash =strtoupper(hash('sha256',$str));
			//$r=$str.":".$Hash.":".$_POST['LMI_HASH'];
			}
		else
			$Hash = NULL;		// $row['Symma']!=$_POST['LMI_PAYMENT_AMOUNT']
		if($Hash === $_POST['LMI_HASH'])
			$status="OK";
			else
			$status="ERROR";
		mysql_query("UPDATE `$table_pay` SET status='{$status}' WHERE id='{$Nomer_paltezha}'", $msconnect_pay)
			or die("���������� � �������: �� ������� �������� ���������� � ������� �������.".mysql_error());
		
		//���������� �����
		$Nomer_paltezha  = (int)$_POST['LMI_PAYMENT_NO'];
		$r=mysql_query("SELECT * FROM `$table_pay` WHERE id='{$Nomer_paltezha}'",$msconnect_pay);
		$row = mysql_fetch_array($r);
		if(isset($_POST['user_id']) && $row['status']=='OK' && $_POST['LMI_MODE']==0)
			{
			require_once(SCRIPT_ROOT."/money/kernel/money_circulation.php");
			$perev = (int)$row['Symma'];
			$money_row_id = add_record(
				WEBMONEY_IN,				 //�� �������
				(int)$_POST['user_id'],      //������������
				(int)$perev,         	 //��������
				(int)$perev,         	 //����������
				"Webmoney payment #{$row['id']}",
				-1,        //�������� ����� ������
				0);        //��������
			if($money_row_id ==0)
				{
				show_msg(NULL,"������ ���������� ������� money. ������ webmoney �{$row['id']}. ���������� � �������������� � ���� ����������",MSG_CRITICAL,MSG_NOBACK);
				}
			//����������� ������
			//�� ������������
			$add = (int)$row['Symma'];
			$user_id = (int)$_POST['user_id'];
			$query="UPDATE `$table_users` SET balance = `balance` + '{$add}' WHERE id = '{$user_id}'";
			mysql_query($query,$msconnect_users);
			}
		else
			{
			//������ �������� ����������� ���������� ��� �� ����� LMI_HASH ��� �������� ������
			exit;
			}

		}
	else
		{
		//������ ���������������
		echo "������. ������ ��������.";
		} ?>