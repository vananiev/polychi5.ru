<?php
	/***************************************************************************

					Модуль работы с заданиями

	**************************************************************************/
?>
<?php
	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "TASK: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		exit;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "TASK: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		exit;
		}

	// конфигурация модуля
	require('config.php');

	//описание модуля
	$INCLUDE_MODULES['TASK']['INFO']['DESCRIPTION'] = 'Работа с заданиями';

	//класс модуля
	class ClassTask{
		public $db;											// база данных
		public $show_task_dialog	= 'show_task_dialog';	// вывод формы диалога для задания
		}
	$task = new ClassTask();

	// подключаем БД заданий
	require_once("DB_task.php");

	// запрещаем доступ до папки с решениями
	$CLOSED_array[]=SOLVING_ROOT_RELATIVE;

	//----------------------------------------------------------------------------------------------------------------------------------
	// добавляем пункты меню
	require('files.php');

	// подключаем функции
	require_once("function.php");

	if(TASK_DIALOG && !defined('MODULE_TICKET'))
		{
		echo "TASK: Установлена функция TASK_DIALOG (kernel/medule.php) - 'Обсуждение заданий'. Для ее использования необходима установка модуля ".MODULE_TICKET."<br>";
		exit;
		}

	// страница отдачи решения
	if($URL['MODULE']=='TASK' && $URL['FILE']=='get_solving_link'){
		$event->add('init', 'get_solving_file');
		//exit;
		}
	function get_solving_file(){
		global $INCLUDE_MODULES;
		require(SCRIPT_ROOT.'/'.$INCLUDE_MODULES['TASK']['PATH'].'/kernel/get_solving_link.php');
		exit;
	}

	// страницы загрузки решения
	if($URL['MODULE']=='TASK' && $URL['FILE']=='add_task_act'){
		if(!isset($_SESSION['user_id'])){
			if($_POST['mode']!=1) $URL['RESULT'] = $_('Доступен только режим \'Игра по моим правилам\'');
			else{
				$event->add('init', 'reg_and_login_user_for_new_task',1);
				$event->add('init', 'create_new_task',2);
				}
			}
		else
			$event->add('init', 'create_new_task');
		}

	//Добавляем файл для настройки пользователя к странице USERS/users_update
	//$LINKED_FILE['USERS']['update_user'][] = array('MODULE'=>'TASK', 'FILE'=>'update_user');
?>
