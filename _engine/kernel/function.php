<?php
	/*************************************************
		Функции
	*************************************************/

	/*$cnt=1000000;
	$t1=microtime(true);
		for($i=0;$i<$cnt;$i++);
	$t2=microtime(true);
		for($i=0;$i<$cnt;$i++)
			$a = users_whith_right('CHANGE_ACCESS');
	$t3=microtime(true);
	echo '<br>all: ',($t3-$t2-($t2-$t1))/$cnt;
	echo '<br>';
	var_dump($a);*/
?>
<?php
//----------------------------- получаем переменные из строки запроса ------------------------------------------------
function &array_from_args($str)
{
	$return = array();
	while(strlen($str))
		{
		$eq = strpos($str,'=');
		$key = substr($str, 0, $eq);
		$amp = strpos($str,'&',$eq);
		if($amp == 0) $amp = strlen($str);
		$value = substr($str, $eq+1, $amp-$eq-1);
		$return[$key]=$value;
		if(isset($str[$amp+1]))
			$str = substr($str,$amp+1);
		else
			$str='';
		}
	return $return;
}
//----------------------------- становка переменных в строки запроса -------------------------------------------------
function add_arg($args)
{
	global $URL;
	$a = &array_from_args($URL['ARGS']);
	$b = &array_from_args($args);
	$ARG = array_merge($a, $b); // перезаписываем одинаковые
	return args_from_array($ARG);
}
//----------------------------- удаление переменных в строки запроса -------------------------------------------------
//args	- массив имен, удаляемых переменных
function del_arg($args)
{
	global $URL;
	$a = &array_from_args($URL['ARGS']);
	foreach($args as $val)
		if(isset($a[$val])) unset($a[$val]);
	return args_from_array($a);
}
//----------------------------- получаем переменные из строки запроса ------------------------------------------------
function args_from_array(&$values)
{
	$return = '';
	foreach($values as $key => $val)
		$return .= $key . '=' . $val . '&';
	return substr($return,0,-1);
}
//-----------------------------навигация по таблицам (вывод ссылок на страницы перехода)------------------------------
function show_paging($rows_amount, $rows_on_page){get_table_nav(NULL, NULL, NULL, $rows_amount, $rows_on_page);}
function get_table_nav($module, $file, $args, $rows_amount, $rows_on_page)
{
	global $URL;
	if($module == NULL) $module = $URL['MODULE'];
	if($file == NULL) $file = $URL['FILE'];
	$ARG=NULL;
	if($args[0] == '&' or $args[0]=='?') $args = substr($args,1);
	if(!empty($URL['ARGS']))
		{
		if(!empty($args))
				{
				$a = &array_from_args($URL['ARGS']);
				$b = &array_from_args($args);
				$ARG = array_merge($a, $b); // перезаписываем одинаковые
				$ARG = args_from_array($ARG);
				}
		else
				$ARG = $URL['ARGS'];
		}
	else
		$ARG = $args;
	echo '<div class=\"page_nav\">';
			$m=(int)($rows_amount/$rows_on_page);
			if($rows_amount%$rows_on_page != 0)
				$m++;
			if($m > 1)
				{
				echo "<a title='Щелкните по номеру страницы, которую надо отобразить'>Переидти:</a>";
				for($i=1;$i <= $m; $i++)
					{
				       		echo url($i, $module, $file, $ARG, $i, "title='".$i." страница'");
							echo "&nbsp";
					}

				}
	echo "</div> <!--END: page_nav-->";
}
//-------------------- Поиск папок ----------------------------
function search_dir($path, $pattern = '*', $flags = 0, $depth = 0)
{
	$files = array();
	$dirs = array();
	$folders = array(rtrim($path, DIRECTORY_SEPARATOR));

	$dir=$folders;
	while($folder = array_shift($folders)) {
		$files = array_merge($files, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
		if($depth != 0) {
			$moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
			$depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
			$folders = array_merge($folders, $moreFolders);
			$dirs = array_merge($dirs, $moreFolders);
		}
	}
	return array('dir'=>$dirs, 'file'=>$files);
}
//--------------- Получаем файл и переменные запроса из URL ---
$URL_MODEL_VARS = array ('MODULE', 'FILE', 'PAGE', 'ARGS');
$URL_MODEL_VAR_REG = array ('[a-zA-Z0-9_\-]+', '[a-zA-Z0-9_\/\-]+', '[0-9]*', ".*"); //не должно встречаться круглых скобок
$URL_MODEL_REG = str_replace('(ARGS)','[\?]?(ARGS)', URL_MODEL);									//заменяем ? на [\-]?
$URL_MODEL_REG = str_replace(')(',')'.URL_SPLITTER_REG.'(', $URL_MODEL_REG);									//заменяем )( на )[\.]?(
$URL_MODEL_REG = "^".str_replace($URL_MODEL_VARS, $URL_MODEL_VAR_REG, $URL_MODEL_REG)."$";
function url_parse($REQUEST_URI)
{
	global $URL_MODEL_REG;
	if(preg_match('#'.$URL_MODEL_REG.'#', $REQUEST_URI, $res))
		{
		$i=1;
		$ret=array();
		global $URL_MODEL_VARS;
		foreach($URL_MODEL_VARS as $VAR)
			if(isset($res[$i]))
				$ret[$VAR] = $res[$i++];
		//if( isset($ret['PAGE']) ) //это номер страницы
		//	$_GET['page'] = $ret['PAGE']; // не устанавливаем, т.к. строка $REQUEST_URI м.б. и не запрашиваемой страницой
		//if($ret['ARGS'][0] == '?') $ret['ARGS']=substr($ret['ARGS'],1);	// убираем первый ?
		//$URL['MODULE'] хранит ключ, а не $INCLUDE_MODULES[X]['PAGE']
		global $INCLUDE_MODULES;
		$flag = false;
		foreach($INCLUDE_MODULES as $KEY => $MODULE)
			if($ret['MODULE'] == $MODULE['PAGE'])
				{
				$ret['MODULE']= $KEY;
				$flag = true;
				break;
				}
		if(!$flag) $ret=false;
		return $ret;
		}
	else
		return false;
}
//-------------------- Вывод URL ------------------------------
/*
url()			- ссылка на текущую страницу с анкором из $FILE (kernel/files.php) и $page=$URL['PAGE'],  но $args=''
url(NULL)		- получение адреса текущей страницы $page=$URL['PAGE'],  но $args='': вида /эта_страница.php?a=1&b=2
url('go')		- ссылка на текущую страницу с анкором 'go',но   $args=''

url($ankor, $module, $file) - ссылка на указанную страницу на $page=''
url(NULL, $module, $file)	- получение адреса страницы с $args='' и $page='': вида /какая-то_страниц.php?a=1&b=2
url($ankor, 'OPTIONAL', www.google.ru/123', "class='my'")	- получение произвольной ссылки
*/
url();
function url($ankor=NULL, $module=NULL, $file=NULL, $args=NULL, $page=NULL, $html_tegs=NULL)
{
	global $URL, $FILE;
	//if($ankor!=NULL) $ankor = htmlspecialchars($ankor, ENT_QUOTES);
	if($module!=NULL) $module = htmlspecialchars($module, ENT_QUOTES);
	if($file!=NULL) $file = htmlspecialchars($file, ENT_QUOTES);
	//if($args!=NULL) $args = htmlspecialchars($args, ENT_QUOTES); '&' не должен заменяться на '&amp;'
	if($page!=NULL) $page = htmlspecialchars($page, ENT_QUOTES);
	if($html_tegs!=NULL) $html_tegs = htmlspecialchars($html_tegs, ENT_NOQUOTES);	//' и " сохраняем

	if(func_num_args()==0 && isset($FILE)) {
		// указываем на тот же самый файл с теми же аргументами, той же страницей
		$module=$URL['MODULE'];
		$file=$URL['FILE'];
		$page=$URL['PAGE'];
		$ankor=$FILE[$module][$file]['ANCHOR'];
		}
	if ($ankor!=NULL && $module == 'OPTIONAL' && $file!=NULL){
		// в $args при $module == 'OPTIONAL' передаются html-теги
		return '<a href=\''.$file.'\' '.$args.'>'.$ankor.'</a>';
		}
	if($module==NULL) $module=$URL['MODULE'];
	if($file==NULL)   $file=$URL['FILE'];
	//$page=$URL['PAGE']; <-- иначе не работает переход по страницам

	global $INCLUDE_MODULES;
	if(!isset($INCLUDE_MODULES[$module]))
		return "/";
	if($page==NULL) $page ="";
	if($args==NULL) $args ="";
	if($html_tegs==NULL) $html_tegs ="";
	// В какой форме представляются ссылки в системе
	if(USE_FRIENDLY_URL == true)
		{
		// ЧПУ
		if(!empty($args) and $args[0]=='&')
			$args[0] = '?';
		if(!empty($args) and $args[0]!='?')
			$args = '?'.$args;
		//Ставляем переменные согласно шаблону
		global $URL_MODEL_VARS;
		$URL_MODEL_VAR_HUMAN = array ($INCLUDE_MODULES[$module]['PAGE'], $file, $page, $args); //не должно встречаться круглых скобок
		$return = str_replace($URL_MODEL_VARS, $URL_MODEL_VAR_HUMAN, URL_MODEL);
		//вставляем символ делиметора
		global $URL_SPLITTER;
		$return = str_replace('()','',$return);
		$return = str_replace(')(',URL_SPLITTER,$return);	//заменяем )( на символ сплиттера
		$return =str_replace(array('(',')','\\'), '', $return);
		}
	else
		{
		//Обычные
		if(!empty($args) and $args[0]=='?')
			$args[0] = '&';
		if(!empty($args) and $args[0]!='&')
			$args = '&'.$args;
		//из аргументов выкусываем page=...
		if(ereg('(.*)page=[0-9]*(.*)', $args, $res))
			$args = $res[1].$res[2];
		$args = str_replace('&&', '&', $args);
		$return = "/?".$INCLUDE_MODULES[$module]['PAGE']."=".$file.$args;
		if($page != "")
			$return .= "&page=".$page;
		}
	//добавляем код html
	if($ankor !="" and $ankor!= NULL)
		$return = "<a href=\"".$return."\" ".$html_tegs.">".$ankor."</a>";
	return $return;
}
//-------------------- Ищем файл по ЧПУ ссылкам -----------
function friendly_url_to_file($url)
{
	global $URL;
	if($url !== false && $URL !== false)
		{
		$URL = array_merge($URL,$url); //переписываем значения ячеек в $URL ячейками из $url
		// выбираем запрашиваемый модуль
		global $INCLUDE_MODULES;
		if(!isset($INCLUDE_MODULES[$url['MODULE']]))
			return false; //set $REQUEST_FILE
		$MODULE = $INCLUDE_MODULES[$url['MODULE']];
		//чтобы не смогли сменить папку, в которой ищется файл введя ../ (--ИЛИ admin/get_users--)
		//исключаем наличие . (--и \--) в переменной
		//запрещаем доступ в папку kernel
		if(preg_match("#\.+|kernel#",$URL['FILE']))  //(--"\.+|/+"--)
			return false; //set $REQUEST_FILE
		//ищем запрашиваемый файл
		if(file_exists(SCRIPT_ROOT."/".$MODULE['PATH']."/".$url['FILE'].".php"))
			return (SCRIPT_ROOT."/".$MODULE['PATH']."/".$url['FILE'].".php"); //set $REQUEST_FILE

		/*/ ищем в других модулях
		if($MODULE['INTRODUCE']==TRUE) // файлы модуля могуть быть в папках других модулей
			foreach($INCLUDE_MODULES as $MODULE_2)
				if(file_exists(SCRIPT_ROOT."/".$MODULE_2['PATH']."/".$MODULE['PATH']."/".$url['FILE'].".php"))
					return (SCRIPT_ROOT."/".$MODULE_2['PATH']."/".$MODULE['PATH']."/".$url['FILE'].".php"); //set $REQUEST_FILE
		*/
		}
	return false; //set $REQUEST_FILE
}
//-------------------- Ищем файл по GET запросу (не ЧПУ) --
function url_to_file(&$get)
{
	global $INCLUDE_MODULES;
	//блокируем возможность одновременного запроса 2х страниц (разрешенной и запрещенной)
	$cnt=0;
	foreach($INCLUDE_MODULES as $MODULE)
		if(isset($get[$MODULE['PAGE']])) $cnt++;		//количество запрашиваемых страниц должно быть 1!!, иначе вазможно ложное срабатывание защиты
	if($cnt>1)
		{
		//show_msg("Нарушение безопасности", "Отказ доступа", MSG_CRITICAL,MSG_RETURN);
		echo "Отказ доступа";
		return  false; //set $REQUEST_FILE
		}

	foreach($INCLUDE_MODULES as $KEY => $MODULE)
			{
			//чтобы не смогли сменить папку, в которой ищется файл введя ../ (--ИЛИ admin/get_users--)
			//исключаем наличие . (--и \--) в переменной
			//запрещаем доступ в папку kernel
			if(isset($get[$MODULE['PAGE']]) && !ereg("\.+|kernel",$get[$MODULE['PAGE']]))  //(--"\.+|/+"--)
				{
				if(file_exists(SCRIPT_ROOT."/".$MODULE['PATH']."/".$get[$MODULE['PAGE']].".php"))
					{
					global $URL;
					if(!isset($get['page'])) $get['page'] = '';

					$URL = array_merge($URL, array('MODULE'=>$KEY, 'FILE'=>$get[$MODULE['PAGE']],'PAGE'=>$get['page'], 'ARGS'=>'?'.$_SERVER['QUERY_STRING']));

					//из $URL['ARGS'] выкусываем модуль=file и page=...
					if(ereg('(.*)page=[0-9]*(.*)', $URL['ARGS'], $res))
						$URL['ARGS'] = $res[1].$res[2];
					$URL['ARGS'] = str_replace($MODULE['PAGE']."=".$get[$MODULE['PAGE']], '', $URL['ARGS']);
					$URL['ARGS'] = str_replace('&&', '&', $URL['ARGS']);
					if($URL['ARGS'] == '&' ) $URL['ARGS'] = '';
					//убираем первый и последний символы &
					if(!empty($URL['ARGS']) and $URL['ARGS'][0] == '?' ) $URL['ARGS'] = substr($URL['ARGS'],1);
					if(!empty($URL['ARGS']) and $URL['ARGS'][0] == '&' ) $URL['ARGS'] = substr($URL['ARGS'],1);
					if(!empty($URL['ARGS']) and $URL['ARGS'][strlen($URL['ARGS'])-1] == '&' ) $URL['ARGS'] = substr($URL['ARGS'],0,strlen($URL['ARGS'])-1);
					// возврат значения
					return (SCRIPT_ROOT."/".$MODULE['PATH']."/".$get[$MODULE['PAGE']].".php"); //set $REQUEST_FILE
					}
				/*
				if($MODULE['INTRODUCE']==TRUE) // файлы модуля могуть быть в папках других модулей
					foreach($INCLUDE_MODULES as $KEY => $MODULE_2)
						if(file_exists(SCRIPT_ROOT."/".$MODULE_2['PATH']."/".$MODULE['PATH']."/".$get[$MODULE['PAGE']].".php"))
							{
							global $URL;
							if(!isset($get['page'])) $get['page'] = '';

							$URL = array_merge($URL, array('MODULE'=>$KEY, 'FILE'=>$get[$MODULE['PAGE']],'PAGE'=>$get['page'], 'ARGS'=>$_SERVER['QUERY_STRING']));

							//из $URL['ARGS'] выкусываем модуль=file и page=...
							if(ereg('(.*)page=[0-9]*(.*)', $URL['ARGS'], $res))
								$URL['ARGS'] = $res[1].$res[2];
							$URL['ARGS'] = str_replace($MODULE['PAGE']."=".$get[$MODULE['PAGE']], '', $URL['ARGS']);
							$URL['ARGS'] = str_replace('&&', '&', $URL['ARGS']);
							if($URL['ARGS'] == '&' ) $URL['ARGS'] = '';
							//убираем первый и последний символы &
							if(!empty($URL['ARGS']) and $URL['ARGS'][0] == '?' ) $URL['ARGS'] = substr($URL['ARGS'],1);
							if(!empty($URL['ARGS']) and $URL['ARGS'][0] == '&' ) $URL['ARGS'] = substr($URL['ARGS'],1);
							if(!empty($URL['ARGS']) and $URL['ARGS'][strlen($URL['ARGS'])-1] == '&' ) $URL['ARGS'] = substr($URL['ARGS'],0,strlen($URL['ARGS'])-1);
							// возврат значения
							return (SCRIPT_ROOT."/".$MODULE_2['PATH']."/".$MODULE['PATH']."/".$get[$MODULE['PAGE']].".php"); //set $REQUEST_FILE
							}
				*/
				}
			}
	return false; //set $REQUEST_FILE
}
//-------- Преобразуем строку запроса в файл ----------------
$REQUEST_FILE = "";	//файл который запрашивали
$URL=array('MODULE'=>'', 'FILE'=>'', 'PAGE'=>'', 'ARGS'=>'', 'TITLE'=>'');		//данные о запрашиваемом url (MODULE, FILE, PAGE, ARGS) или =false если файл не найден

$tmp=TEMP_ROOT_RELATIVE;
if($tmp[0]=='/') $tmp=substr($tmp,1);
if($tmp[strlen($tmp)-1]=='/') $tmp=substr($tmp,0,strlen($tmp)-1);

$CLOSED_URL=array();
$CLOSED_URL[]=$tmp;		// файлы и пути, которые не должны быть доступны через броузер

// костыль
$_SERVER['REQUEST_URI']=str_replace("http://".$_SERVER["HTTP_HOST"],"",$_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI']=str_replace("https://".$_SERVER["HTTP_HOST"],"",$_SERVER['REQUEST_URI']);

function get_request_file( $REQUEST_STR=NULL )
{
	global $REQUEST_FILE;
	global $URL;
	global $CLOSED_URL;
	if($REQUEST_STR == NULL) $REQUEST_STR = $_SERVER['REQUEST_URI'];
	//если ничего не запрашивали, то выводим страницу по умолчанию
	if($REQUEST_STR == '' || $REQUEST_STR == '/' || $REQUEST_STR == DEFAULT_PAGE)
		{
		$REQUEST_URI = DEFAULT_PAGE;
		if($pos = strpos(DEFAULT_PAGE, '?'))
			{
			$get = &array_from_args(substr(DEFAULT_PAGE,$pos+1));
			$_GET = &$get;
			}
		else
			$get = array();
		}
	else
		{
		$REQUEST_URI = $REQUEST_STR;
		$get = &$_GET;
		}
	/*$REQUEST_URI = &$_SERVER['REQUEST_URI'];
	$get = &$_GET;*/

	//пути закрытые через броузер
	if(is_array($CLOSED_URL))
		foreach($CLOSED_URL as $CL_URL )
			if(strpos($REQUEST_URI,$CL_URL))
				{
				echo "Доступ закрыт";
				exit;
				}
	if(USE_FRIENDLY_URL == true)
		{
		// ЧПУ
		$url = url_parse($REQUEST_URI);
		if($url == false)
			{
			if($REQUEST_URI == DEFAULT_PAGE)
				{echo 'Не верный параметр DEFAULT_PAGE в /config.php';exit;}
			//$URL = false;
			//return get_request_file(DEFAULT_PAGE );
			$REQUEST_FILE =  ENGINE_MEDIA . '/err_pages/404.php';
			$URL = false;
			}
		else
			$REQUEST_FILE = friendly_url_to_file($url);
		}
	else
		{
		//Обычные ссылки
		//в вызванной функции заполняется массив $URL
		$REQUEST_FILE = url_to_file($get);
		if($URL['MODULE']=='')
			{
			if($REQUEST_URI == DEFAULT_PAGE)
				{echo 'Не верный параметр DEFAULT_PAGE в /config.php';exit;}
			//$URL=false;
			return get_request_file(DEFAULT_PAGE);
			}
		}
	return $REQUEST_FILE;
}
//-------------- Получаем контент ---------------------------
function GET_CONTENT()
{
	global $REQUEST_FILE;
	if($REQUEST_FILE === false or $REQUEST_FILE=='')
		return ENGINE_MEDIA.'err_pages/404.php';
	else
		return $REQUEST_FILE;

}
//------- Команды PHP, выполняемые в файле after.php---------
$RUN_AT_THE_END = "";
//- это строка, в которую по мере выполнения php добавляются сктроки типа
// RUN_AFTER_LOAD += "function();". Данные команды будут выполнены самыми последними
function run_at_the_end($commands = '')
{
	global $RUN_AT_THE_END;
	$RUN_AT_THE_END = $RUN_AT_THE_END.$commands.";";
}
function run_ending_cmd()
{
	global $RUN_AT_THE_END;
	eval ($RUN_AT_THE_END);
}
/*----------- Добавить метатег в <head> ---------------------
Массив содержит данные для вывода тега
<link rel="stylesheet" type="text/css" href="/files/plugins/tooltip/tooltip.css">
*/
$LINK_TEGS = array();
function add_link_teg($href, $rel='stylesheet', $type='text/css' )
{
	global $LINK_TEGS;
	$LINK_TEGS[] = array ('HREF'=>$href, 'REL'=>$rel, 'TYPE'=>$type);
}
//-------------- Получаем заголовок страницы и др метатеги --
function get_title()
 {
	global $URL;
	global $FILE;
	global $INCLUDE_MODULES;
	$window_title = COMPANY_NAME;
	$mata_keys='';
	$mata_desc='';

	if($URL!==false && file_exists(SCRIPT_ROOT.'/'.$INCLUDE_MODULES[$URL['MODULE']]['PATH'].'/kernel/files.php'))
		{
		if(isset($FILE[$URL['MODULE']][$URL['FILE']]))
			{
			$page = $FILE[$URL['MODULE']][$URL['FILE']];
			if(isset($FILE[$URL['MODULE']]['TITLE']) && $FILE[$URL['MODULE']]['TITLE']!='') $window_title .= ' > '.$FILE[$URL['MODULE']][$URL['FILE']]['TITLE'] ;
			if(isset($page['TITLE']) && $page['TITLE']!='') { $URL['TITLE'] = $page['TITLE']; /*$window_title .= ' > '.$URL['TITLE']*/;}
			if(isset($page['KEYWORDS']) && $page['KEYWORDS']!='') $mata_keys = $page['KEYWORDS'];
				else if(isset($FILE[$URL['MODULE']]['KEYWORDS'])) $mata_keys = $FILE[$URL['MODULE']]['KEYWORDS'];
				else $mata_keys = META_KEYWORDS;
			if(isset($page['DESCRIPTION']) && $page['DESCRIPTION']!='') $mata_desc = $page['DESCRIPTION'];
				else if(isset($FILE[$URL['MODULE']]['DESCRIPTION'])) $mata_desc = $FILE[$URL['MODULE']]['DESCRIPTION'];
				else $mata_desc = META_DESCRIPTION;
			}
		}

	echo '<title>',$window_title,'</title>';
	echo '<meta name=\'Description\' content=\'',$mata_desc,'\'>';
	echo '<meta name=\'Keywords\' content=\'',$mata_keys,'\'>';
 }
//--------- Получаем ссылку на медиа-дерикт для модуля ------
	//для модуля просматриваемой страницы
function media_dir($module = NULL)
{
	global $URL;
	global $INCLUDE_MODULES;
	if($module == NULL && $URL != false)
		return MODULES_MEDIA_RELATIVE."/".$INCLUDE_MODULES[$URL['MODULE']]['PATH'];
	elseif( is_array($module) && isset($module['PATH']) )
		return MODULES_MEDIA_RELATIVE."/".$module['PATH'];
	elseif( is_string($module) && isset($INCLUDE_MODULES[$module]['PATH']) )
		return  MODULES_MEDIA_RELATIVE."/".$INCLUDE_MODULES[$module]['PATH'];
	else
		return 'not_found';
}
	//для любого модуля
function media_dir_for($module){ return media_dir($module);}
//------------- Кеширование переменных,если доступен memChaced ----------
//------------- если не доступен используются стандартные ф-ии-----------
if(class_exists('Memcache'))
	{
	//$memcache_obj = new Memcache;
	define('SET_VAR','');	//функция установки
	define('GET_VAR','');	//получение переменной
	define('DEL_VAR','');	//удаление переменной
	}
else
	{
	define('SET_VAR','set_var');	//функция установки
	define('GET_VAR','get_var');	//получение переменной
	define('DEL_VAR','del_var');	//удаление переменной
	}
//--------- Установка переменной ----------------------------
// после выполнения set($key, $a), изменив $a, Вы измените и сохраненную к кеше переменную !!!
// поэтому желательно выполнять set($key, $a);unset($a);
function &set_var($KEY, &$VALUE)
{
	if(isset($_SESSION['user_id']))
		{
		$_SESSION[$KEY] = &$VALUE;
		return $_SESSION[$KEY];		//возврат значения $a=set_var() или ссылки $a=&set_var()
		}
	else
		{
		$GLOBALS[$KEY] = &$VALUE;
		return $GLOBALS[$KEY];
		}
}
//--------- Получение переменной ----------------------------
function &get_var($KEY)
{
	if(isset($_SESSION['user_id'])&& isset($_SESSION[$KEY]))
		return $_SESSION[$KEY];
	if (isset($GLOBALS[$KEY]))
		return $GLOBALS[$KEY];
	else
		{$ret=NULL; return $ret;}	//возвращаем ссылку, если был вызов $a=&get_var() <-- Внимание!! после выхода из ф-ии значение переменной $a все еще доступно, хотя $ret уже не существует
}
//--------- Удаление  переменной ----------------------------
function del_var($KEY)
{
	if(isset($_SESSION['user_id'])&& isset($_SESSION[$KEY]))
		unset($_SESSION[$KEY]);
	else if (isset($GLOBALS[$KEY]))
		unset($GLOBALS[$KEY]);
}
//--------- Определение броузера ----------------------------
function get_user_agent()
{
	$browser = false;
	if(isset($_SERVER['HTTP_USER_AGENT']))
		{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if ( stristr($user_agent, 'MSIE 6') ) 	$browser = 'MSIE 6'; // IE
		if ( stristr($user_agent, 'MSIE 7') ) 	$browser = 'MSIE 7'; // IE
		if ( stristr($user_agent, 'MSIE 8') ) 	$browser = 'MSIE 8'; // IE
		if ( stristr($user_agent, 'MSIE 9') ) 	$browser = 'MSIE 9'; // IE
		if ( stristr($user_agent, 'Firefox') ) 	$browser = 'Firefox';
		if ( stristr($user_agent, 'Chrome') ) 	$browser = 'Chrome';
		}
	return $browser;
}
//--------- Избавляемся от лишних кавычек (gри php_flag magic_quotes_gpc=on) ---------
function strip_quotes(&$el) {
  if (is_array($el))
    foreach($el as $k=>$v)
      strip_quotes($el[$k]);
  else $el = stripslashes($el);
}
//--------- Избавляемся от лишних кавычек ----------------------------
function delete_magic_quotes()
{
	if (get_magic_quotes_gpc()) {
		strip_quotes($_GET);
		strip_quotes($_POST);
		strip_quotes($_COOKIE);
		strip_quotes($_REQUEST);
		if (isset($_SERVER['PHP_AUTH_USER'])) strip_quotes($_SERVER['PHP_AUTH_USER']);
		if (isset($_SERVER['PHP_AUTH_PW']))   strip_quotes($_SERVER['PHP_AUTH_PW']);
		}
}
//--------- Вывод сообщения во время тестирования --------------------
function debug_msg($TEXT = '', $FILE=NULL, $LINE = NULL)
{
	if(DEBUG_MODE === 'NONE') return;
	$return = '<font color=\'red\'><b>'.$TEXT.'</b></font>';
	if($LINE !== NULL) $return .= '<br>line: '.$LINE;
	if($FILE !== NULL) $return .= '<br>file: '.$FILE;
	if(function_exists('show_msg'))
		show_msg('DEBUG',$return, MSG_WARNING, MSG_NO_BACK);
	else
		echo $return;
}
//--------- Безопасный вывод в броузер c перекодировкой для искл. html-инъекций ------
function secho($text)
{
	if(CONVERT_CHARSET)
		{
		$out = &ob_iconv_handler($text,NULL);
		if($out == '') $out = &$text;			// не удалось перекодировать
		echo htmlspecialchars($out, ENT_QUOTES);
		}
	else
		echo htmlspecialchars($text, ENT_QUOTES);
}
//--------- Вывод в броузер c перекодировкой в SITE_CHARSET ---------
function cecho($in)
{
	if(CONVERT_CHARSET)
		{
		$text = &ob_iconv_handler($in,NULL);
		if($text == '') $text = &$in;
		}
	echo $text;
}
//----------- Перевод и преобразование кодировки --------------------
$_='_conv_charset';		// функция преобразования кодировки
function _conv_charset($text)
{
	$return=_($text);		// попытка вернуть перевод (возвращается в SITE_CHARSET)
	if( CONVERT_CHARSET && $return === $text) // если не вернули перевод, изменяем кодировку
		{
		$return = &ob_iconv_handler($text,NULL);
		if($return === '') $return = &$text;
		}
	return $return;
}

//------------------------------ Кодирование ----------------------------------
function mime_header_encode($str, $data_charset, $send_charset=false)
{
	# Если задана кодировка передачи и она не совпадает
	# с кодировкой данных, конвертируем строку
	if($send_charset AND $data_charset!=$send_charset){
	$str = iconv($data_charset, $send_charset, $str);
	} else {
	$send_charset = $data_charset;
	}
	return '=?'.$send_charset.'?B?'.base64_encode($str).'?=';
}
//------------------ Отправка письма -------------------------------------------
function sendmail($to, $subj, $msg, $from = COMPANY_NAME, $attach=false)
	{
	$charset = 'utf-8';//кодировка письма
	$subj = mime_header_encode($subj, $charset);//кодируем поле "Тема"
	$from = mime_header_encode($from, $charset).' <info@'.MAIL_DOMAIN.'>';//кодируем поле "Тема"

	$head = "MIME-Version: 1.0\r\n";
	$head .= "X-Priority: 3\r\n";//приоритет
	$head .= "From: $from\r\n";
	# Если есть вложение, присоединяем его
	if($attach AND is_file($attach))
		{
		$fp = fopen($attach, 'rb');
		if($fp)
			{
			$file = fread($fp, filesize($attach));
			fclose($fp);
			$filename = basename($attach);
			$boundary = '--'.md5(uniqid(time()));
			$msg .= "\r\n\r\n--$boundary\r\n";
			$msg .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
			$msg .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
			$msg .= "Content-Transfer-Encoding: base64\r\n\r\n";
			$msg .= chunk_split(base64_encode($file));
			$msg .= "\r\n--$boundary--";
			$head .= "Content-Type: multipart/mixed;\r\n";
			$head .= " boundary=$boundary";
			$head .= "\r\n\r\n--$boundary\r\n";
			}
		}
	$head .= "Content-Type: text/plain; charset=$charset\r\n";
	$head .= "Content-Transfer-Encoding: 8bit\r\n";
	return @mail($to, $subj, $msg, $head);// '-f'.$from);
}
//----------------- Асинхронное выполнение скрипта -----------------------
function exec_script($url, $params = array())
{
    $parts = parse_url($url);

    if (!$fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80))
    {
        return false;
    }

	$params['secret'] = md5(SECRET);
    $data = http_build_query($params, '', '&');

    fwrite($fp, "POST " . (!empty($parts['path']) ? $parts['path'] : '/') . " HTTP/1.1\r\n");
    fwrite($fp, "Host: " . $parts['host'] . "\r\n");
    fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
    fwrite($fp, "Content-Length: " . strlen($data) . "\r\n");
    fwrite($fp, "Connection: Close\r\n\r\n");
    fwrite($fp, $data);
    fclose($fp);

    return true;
}
/*/------- Базовый класс для создания расширяемых классов -------------------
class DynamicClass
{
	private $imported;				// содержим массив [имя класса]= объект
	private $imported_functions;	// массив с функциями [имя функции]=ссылка_на_класс | NULL(если функция не часть класса)
	public function __construct()
		{
		$this->imported	= array();
		$this->imported_functions = array();
		}
	//импортирум новую функцию
	protected function imports($name)
		{
		if(class_exists($name))		// передан класс
			{
			$new_import 		= new $name(); 				// the new object to import
			$import_name		= get_class($new_import);	// the name of the new object (class name)
			$import_functions 	= get_class_methods($new_import); // the new functions to import
			$this->imported[$import_name] = $new_import;	// add the object to the registry
			// add teh methods to the registry
			foreach($import_functions as $key => $function_name)
				$this->imported_functions[$function_name] = &$new_import;
			return true;
			}
		else if (function_exists($name)) //передана функция
			{
			$this->imported_functions[$name] = NULL;
			}
		}
	public function __call($name, $args)
	{
		// make sure the function exists
		if(array_key_exists($name, $this->imported_functions) )
		{
			if($this->imported_functions[$name] === NULL)	// вызываем функцию
				{
				if(is_callable($name)) return call_user_func_array($name, $args);
				}
			else	// вызываем метод класса
				{
				if(is_callable(array($this->imported_functions[$name], $name)))
					{
					$args[] = $this;
					return call_user_func_array(array($this->imported_functions[$name], $name), $args);
					}
				}
		}
		throw new Exception ('Call to undefined name/class function: ' . $name);
	}
}
		/* Пример использования
		class UserFunctions
			{
			public function lastThenFirst(&$that)
				{
				return $that->last_name . ', ' . $that->first_name;
				}
			}
		class User extends DynamicClass
			{
			public	$first_name;
			public $last_name;

			public function __construct()
				{
				parent::__construct();
				$this->first_name = 'Ian';
				$this->last_name = 'Selby';
				$this->imports('UserFunctions');
				}

			public function getFullName()
				{
				return $this->first_name . ' ' . $this->last_name;
				}
			}
		$user = new User();
		echo $user->getFullName() . '<br />';
		echo $user->lastThenFirst() . '<br />';
		*/
//----------------- Сборка и вывод таблицы -------------------------------
/*
$db		- из какой БД производим выборку
$query	- запрос, в котором переменные замены на %s, %u ... и вынесены в $vars
$vars	- переменные в запросе
$field_names 	- вывод в шапке вместо названия полей из БД стоки из этого массива array('id'=>'Номер', 'status'=>'Состояние')
$data_replacement	- замена данных в теле таблицы 	array(	'thematic'=>array(0=>array('TEST'=>'OTHER', 'RULE'=>'<i>%v</i>')),
															'id'=>		array(0=>array('TEST'=>true, 'RULE'=>url('%v', 'TICKET', 'dialog', 'ticket_id=%v&mail='.$mail))))
					если 'TEST'===true или 'TEST' равен значению ячейки из указанного столбца ('thematic', 'id'), то производится замена на 'RULE', с подстановкой
					вместо %v значения поля
					%id на id поля
					%key на имя столбца
$table_id		- id тег таблицы
$table_class	- тег class таблицы
$on_page		- число строк на одной странице
*/
function show_table( $db, $query, $vars, $field_names , $data_replacement, $allow_where ,$table_id, $table_class ,$on_page = 4)
{
		global $URL;
		// Условия сортировки
		if(isset($_GET['sort']) && ($_GET['sort']=='asc' || $_GET['sort']=='desc')) $sort = $_GET['sort']; else $sort = 'desc';
		if(isset($_GET['sortby'])) $sortby = $_GET['sortby']; else $sortby = 'id';

		if(empty($URL['PAGE']))
				$p=1;
			else
				$p=$URL['PAGE'];
		$from = ($p-1)*$on_page;

		// выполним запрос
		$where = '';
		$where_vars = array();
		foreach($allow_where as $field){
			if(isset($_POST['where_'.$field]))
				if($_POST['where_'.$field] != 'All')
					{ $_GET['where_'.$field] = $_POST['where_'.$field]; $URL['ARGS']=add_arg('where_'.$field.'='.$_POST['where_'.$field]);}
				else
					{
					unset($_GET['where_'.$field]);
					unset($_POST['where_'.$field]);
					$URL['ARGS']=del_arg(array('where_'.$field));
					}
			if(isset($_GET['where_'.$field])){
				if($where != '') $where .= ' AND ';
				$where .= "`$field`='%s'";
				$where_vars[] = $_GET['where_'.$field];
				}
			}
		if($where != ''){
			$start = strpos($query, 'WHERE');
			if($start === false) $start = strpos($query, 'where');
			if($start === false)
				$query = $query.' WHERE '.$where;
			else
				$query = substr($query, 0, $start+6). $where . ' AND '. substr($query, $start+6);
			$vars = array_merge($vars, $where_vars);
			}
		$res = $db->query($query."  ORDER BY `%s` $sort LIMIT {$from},{$on_page} ", array_merge($vars,array($sortby))) or die($db->error());

		// информация о полях
		$fieldsinfo = $res->fetch_fields();
		foreach($fieldsinfo as $obj)
			$finfo[$obj->name] = $obj;
		unset($fieldsinfo);

		// число строк
		$res2 = $db->query("SELECT count(id) ".stristr($query,'FROM'), $vars) or die($db->error());
		$row2 = $res2->fetch_assoc();
		$max = $row2['count(id)'];

		if($sort=='desc') $sort2='asc';else $sort2='desc';

		// вывод таблицы
		// шапка таблицы
		foreach($finfo as $key=>$val) $titles[$key] = $key;
		$titles = array_merge($titles, $field_names);
		?>
		<table id="<?php echo $table_id?>" class="<?php echo $table_class?>">
			<tr class='table_header'>
			<?php foreach ( $titles as $key=>$name ) { ?>
				<td>
					<span id="head_<?php echo $key?>"><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sortby='.$key) );?>"><?php echo $name;?></a></span>
					<?php if($finfo[$key]->type === 254 && in_array($key, $allow_where)){ // если тип столбца enum и разрешен показ в $allow_where
						// определяем список возможных значений
						$start = strpos($query, 'FROM');
						$end = strpos($query, ' ', $start+5);
						$table = substr($query, $start+5, $end - ($start+5)); // имя таблицы
						$enum = $db->query("SHOW COLUMNS FROM {$table} LIKE '$key'") or die($db->error());
						$r = $enum->fetch_assoc();
						$v = str_replace(array("enum('", "')"), array('', ''), $r['Type']);
						$v = split("','", $v);
						array_unshift($v, 'All');
						?>
						<span id="head_<?php echo $key?>_chg" style="display:none;width:50px;height:30px;">
							<form id="show_where_<?php echo $key?>" method="POST" action="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], $URL['ARGS']);?>" style="text-align: left;display:inline;">
								<select name="<?php echo 'where_'.$key?>"  id="<?php echo 'where_'.$key?>"
									onchange="javascript: document.getElementById('show_where_<?php echo $key?>').submit()"
									onBlur="javascript: document.getElementById('head_<?php echo $key?>_chg').style.display='none'; document.getElementById('head_<?php echo $key?>').style.display = 'inline'"
								>
									<?php foreach($v as $line)
										echo "<option value='$line'>$line</option>" ?>
								</select>
								<script>
									var objSel = document.getElementById("<?php echo 'where_'.$key?>");
									for (var i=0; i < objSel.options.length; i++)
										if (objSel.options[i].value == '<?php echo $_GET['where_'.$key]?>') objSel.selectedIndex = i;
								</script>
							</form>
						</span>
						<span style="cursor:pointer;" onClick="javascript: var obj = document.getElementById('head_<?php echo $key?>');
														var stl = obj.style.display;
														obj.style.display = document.getElementById('head_<?php echo $key?>_chg').style.display;
														document.getElementById('head_<?php echo $key?>_chg').style.display = stl">+
						</span>
					<?php } ?>
					<?php if( $sortby == $key){ ?>
					<span style="cursor:pointer;"
					onmouseout="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>'; }"
					onmouseover="if(document.getElementById('sort_img')){ document.getElementById('sort_img').src='<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort2.png"; ?>'; }"
					><a href="<?php echo url(NULL, $URL['MODULE'], $URL['FILE'], add_arg('sort='.$sort2) );?>"><img id='sort_img' src="<?php echo ENGINE_MEDIA_RELATIVE."/images/s_$sort.png"; ?>"></a></span>
					<?php } ?>
				</td>
				<?php
				} ?>
			</tr>
		<?php

		// тело таблицы
		$keys = array();	// имена всех полей для осуществления замены
		foreach($titles as $k=>$v) $keys[] = '%'.$k;
		while($row = $res->fetch_assoc()){
			echo '<tr>';
			foreach ( $row as $key=>$val ){
				echo '<td>';
				//замена данных
				if(isset($data_replacement[$key])){
					foreach($data_replacement[$key] as $n){
						if($val == $n['TEST'] || $n['TEST']===true){
							$vals = array();	//все значения
							foreach($row as $v) $vals[] = $v;
							$val = str_replace($keys, $vals, $n['RULE']);
							$val = str_replace(array('%v', '%key'), array($val, $key), $n['RULE']);
							}
						}
					}
				//вывод данных
				if($finfo[$key]->type == 7)	//datetime
					echo date(DATE_TIME_FORMAT, strtotime($val));
				else
					echo $val;
				echo '</td>';
				}
			echo '</tr>';
		}; ?>
		</table>
		<?php
		echo get_table_nav($URL['MODULE'], $URL['FILE'], ' ', $max, $on_page);
}
//-----------------------------вывести сообщение------------------------------
define('MSG_INFO',"info");
define('MSG_HELP',"help");
define('MSG_WARNING',"warning");
define('MSG_CRITICAL',"critical");
define('MSG_CLASSIC',"classic");
define('MSG_NO_BACK',0);
define('MSG_BACK',1);
define('MSG_RETURN',2);
define('MSG_OK',3);
define('MSG_SHOW',true);
define('MSG_HIDDEN',false);
function show_msg($title, $text, $class=MSG_INFO, $history_back=MSG_BACK, $show=MSG_SHOW, $attributes='',$style='')
{
	if(defined( 'PLUGIN_TOOLTIP' ))
		{
		msg_box($title, $text, $class, $history_back, $show, $attributes,$style);
		return;
		}
	echo <<<END1
	<h2 class="title">{$title}</h2>
	<div class="message {$class}" style="{$style}" {$attributes}>{$text}</div>
END1;
	$res="";
	if($history_back==MSG_BACK)
		$res = "<a class='tooltip_button' href='javascript:history.go(-1)'>[ok]</a>";
	else if($history_back==MSG_RETURN)
		$res = "<a class='tooltip_button' href='javascript:history.go(-1)'>[Назад]</a>";
	else if($history_back==MSG_NO_BACK)
		$res = "";
	else if($history_back==MSG_OK )
		$res = is_mobile_site()?"":"<a class='tooltip_button' onmouseup='javascript:submenu(this.parentNode, 0);'>[ok]</a>";
	else
		$res = $history_back;
	echo <<<END2
	<div class="message_link">{$res}</div>
END2;
}
//------- Возвращает страницу по заданному URL  URL  вместе с http://  ----------------
function _get_page ($url) {
    $cells = parse_url($url);
    $host = $cells['host'];
    $path = $cells['path'] . '?' . $cells['query'];

    $fp = @fsockopen ("${host}", 80, $errno, $errstr, 1);
	if(!$fp) return false;
    $headers = "GET ${path} HTTP/1.0rn"
              ."Host: ${host}rn"
              ."Referer: http://${host}"
              ."User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.7) Gecko/20050414 Firefox/1.0.3rn"
              ."Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5rn"
              ."Accept-Language: ru,en-us;q=0.7,en;q=0.3rn"
              ."Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7rn"
              //."Keep-Alive: 300rn"
              //."Proxy-Connection: keep-alivernrn"
				;
    fwrite ($fp, $headers);
    while (!feof ($fp)) {
        $str .= fgets($fp, 1024);
    }
    fclose($fp);
    return $str;
};
//--------- Определяем мобильный броузер или нет ----------------------------------------
function mobile_detect()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $ipod = strpos($user_agent,"iPod");
    $iphone = strpos($user_agent,"iPhone");
    $android = strpos($user_agent,"Android");
    $symb = strpos($user_agent,"Symbian");
    $winphone = strpos($user_agent,"WindowsPhone");
    $wp7 = strpos($user_agent,"WP7");
    $wp8 = strpos($user_agent,"WP8");
    $operam = strpos($user_agent,"Opera M");
    $palm = strpos($user_agent,"webOS");
    $berry = strpos($user_agent,"BlackBerry");
    $mobile = strpos($user_agent,"Mobile");
    $htc = strpos($user_agent,"HTC_");
    $fennec = strpos($user_agent,"Fennec/");

    if ($ipod || $iphone || $android || $symb || $winphone || $wp7 || $wp8 || $operam || $palm || $berry || $mobile || $htc || $fennec)
    {
        return true;
    }
    else
    {
        return false;
    }
}
//---------------------------------------------------------------------------------------------
function is_mobile(){
	return mobile_detect();
}
//---------------------------------------------------------------------------------------------
function is_mobile_site(){
	return MOBILE_DOMAIN == $_SERVER['HTTP_HOST'];
}
?>
