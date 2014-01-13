<?php
	/***************************************************************************

							Модуль работы с данными MySql

	**************************************************************************/	
	class dbMySql extends MySQLi{
		
		var $link = NULL;
		var $host = NULL;
		var $user = NULL;
		var $password = NULL;
		var $db_name = NULL;
		var $db_time_zone = NULL;
		var $charset = NULL;
		
		//----------------------------------------------------------------------------------------------------------------------
		
		//------------------------- Коннект к БД -------------------------------------------------------------------------------
		# Возвращает false или линк к БД
		# Ошибка при соединении или информация о хосте(при успешном подкл.) помещается в $msg_stack
		var $host_info = NULL; // информация о хосте
		function __construct ($host, $user, $password, $db_name, $charset, $db_time_zone, $db_port, $socket = NULL )
			{
			$this->host = $host;
			$this->user = $user;
			$this->password = $password;
			$this->db_name = $db_name;
			$this->db_port = $db_port;
			$this->charset = $charset;
			$this->db_time_zone = $db_time_zone;
			if($socket==NULL) $socket = ini_get("mysqli.default_socket");
			parent::__construct($host, $user, $password, $db_name, $db_port, $socket);
			if(mysqli_connect_errno())
				{
				parent::__construct($host, $user, $password, NULL, $db_port, $socket);
				if(mysqli_connect_errno())
					{
					$this->push_msg( mysqli_connect_error() );
					return false;
					}
				}
			//Устанавливаем часовой пояс
			$this->query("SET @@session.time_zone = '$db_time_zone'") or die($this->error);

			//установка набора символов сайта
			$this->set_charset($charset) or $this->query('SET NAMES '.$charset) or die($this->error);	//изменяет кодировку и для mysql_real_escape_string() в отличие от: mysql_query("SET NAMES ".SITE_CHARSET);
			return $this;
			}
			
			
		
		//--------------------------------- Добавление сообщения в стек сообщений ----------------------------------------------	
		private $msg_stack = array();		// cтек сообщений
		private $msg_max_stack = 10;		# максимальное число сообщений в стеке
		function push_msg($message)
			{
			//очистка старых сообщений
			while(count($this->msg_stack) >= $this->msg_max_stack)
				array_shift($this->msg_stack);
			// запись
			$this->msg_stack[]=$message;
			}
			
			
			
		//--------------------------------- Извлекаем сообщение с стека сообщений ----------------------------------------------	
		function get_msg($num = -1)
			{
			if($num == -1)
				return end($this->msg_stack);
			else
				return $this->msg_stack[$num];
			}
		
		//----------------------------------- Получение безопасн. переменных для выполнения запроса в БД ----------------------------
		function prepare_vars( $query, &$values )
			{
			// проверка соответствия передаваемых аргументов
			$cnt=0;
			reset ($values); // внутренний указатель массива на первый элемент
			$pos = 0;
			while( $pos = strpos($query, '%', $pos) )
				{ 
				list($key,$val) = each($values);
				$pos++;
				if($val===false) return false; 	//переменных не достаточно
				switch($query[$pos])
					{
					case 'd':;	// десятичное со знаком
					case 'u':;	// десятичное беззнаковое
					case 'o':;	// целое -> ост
					case 'x':;	// целое -> hex
					case 'X':;	// целое -> HEX
					case 'b':;	// целое -> bin
					case 'c':;	// целое -> char
							$old = $val;
							$val = (int)$old;	// для правильного разбора строки '1.23e2'
							if( (string)$val !== (string)$old ) return false;
							$values[$key] = $val;
							break;
					case 'e':;	// 1.3e7
					case 'f':	// float
							if(!is_numeric($val)) return false;
							break;
					case 's':
							if(!is_string($val)) return false;		
							break;
					default:
							return false;break;
					}
				$cnt++;
				};
			if( count($values) != $cnt ) return false; 			// передали параметров больше, чем ожидалось
			reset ($values); // внутренний указатель массива на первый элемент
		
			foreach($values as &$value)
				{
				if(is_string($value))
					{
					$value = $this->real_escape_string($value);
					//$value=addCslashes($value, '_%');
					//$value=str_replace('\\','\\\\',$value);
					}
				}
			}
		

		//------------------------- Закавычиваем в запросе %_ -------------------------------------------------------------------
		// Закавычивание происходит только если переменная %x не находится внутри кавычек `string`
		// часть строки ABC%u%sABC будет заменена на ABC'0string'ABC, а не на выражение ABC'0''string'ABC
		// Пример: "%s FROM `table_%s` %s WHERE id=%d AND name=%s" заменится на "'%s' FROM `table_%s` WHERE id='%s' AND name='%s'" 
		function add_quotes(&$query)
			{
			$pos = 0;
			 while( $pos = strpos($query, '%', $pos) )
				{
				for($table_name_quotes_count=0, $i=0;$i<$pos;$i++) if($query[$i]=='`') $table_name_quotes_count++; // внутри имени таблиц и полей замены не добавляем кавычек
				if($table_name_quotes_count%2 == 0 )
					{
					if(isset($query[$pos-1]) && $query[$pos-2] !='%' )
						{
						if($query[$pos-1]!="'") // добавляем кавычку в начало '%_
							{
							$query = substr($query, 0, $pos)."'".substr($query, $pos);
							$pos++;
							}
						}
					else if( !isset($query[$pos-1]) )	// добавляем в начало
						{
						$query = "'".$query;
						$pos++;
						}
					if(isset($query[$pos+2]) && $query[$pos+2] !='%')
						{
						if($query[$pos+2]!="'")	//добавляем кавычку в конец %_'
							{
							$query = substr($query, 0, $pos+2)."'".substr($query, $pos+2);
							$pos++;
							}
						}
					else if( !isset($query[$pos+2]) )	// добавляем в конец
						$query .= "'";
					}
				$pos++;
				}
			}

		//--------------------------------- Добавление запроса в стек запросов ----------------------------------------------	
		var $last_query = array();		// cтек сообщений
		private $query_max_stack = 128;		# максимальное число сообщений в стеке
		function push_query($query)
			{
			//очистка старых сообщений
			while(count($this->last_query) >= $this->query_max_stack)
				array_shift($this->last_query);
			// запись
			$this->last_query[] = $query;
			}
		//--------------------------------- Извлекаем запроса в стек запросов -----------------------------------------------	
		function get_query($num = -1)
			{
			if($num == -1)
				return end($this->last_query);
			else
				return $this->last_query[$num];
			}
		function last_query()
			{
				return end($this->last_query);
			}
			
			
		//------------------------- Безопасный запрос к БД ----------------------------------------------------------------------
		// Пример ('SELECT FROM `user` WHERE `id`=%u',array(0)), см.svprintf()
		// Для передачи переменной в LIKE необходимо $value = str_replace('\\','\\\\',$value);
		// Возвращает указатель на результат, либо false - если запрос завершился неудачей или число или типы передаваемых параметров не соответствуют $query
		function query($query, $values = NULL)
			{
			$pos = strpos($query, '%');
			if( $values !== NULL && $pos !== false ) //переданы переменные
				{
				$values = (array)$values;
				// подготовим переменные и проверим соответствие типов
 				if( false === $this->prepare_vars($query, $values)) {$this->push_msg('Переданы не верные переменные'); return false;}
				// закавычиваем переменные в запросе, где необходимо
				$this->add_quotes($query);
				// подготовим запрос
				$query = vsprintf($query, $values);
				return $this->_query($query);
				}
			else if(($values === NULL || $values===array()) && $pos === false) // переменные не передавались
				{
				// выполним запрос
				return $this->_query($query);
				}
			else
				{$this->push_msg('Не верный формат вызова'); return false;}
			}
			
		//------------------------------------ Запрос к БД ----------------------------------------------------------------------
		function _query($query)
			{
				// сохраняем запрос
				$this->push_query($query);
				// выполним запрос
				$res = parent::query($query);
				if(!$res) $this->push_msg($this->error)	;
				return  $res;
			}	
			
		//--------------------------------- Запоминаем  результат в переменной ------------------------------------------------
		// запихивает результат в массив и очищает ресурс
		// если массив двумерный, надо указать уникальный ключ $id
		// id	- сделать столбец идентификатором строки array(0=>array(), 1=>array()), если в результате несколько строк
		// result - результат, который вернула функция query()
		function &fetch($result, $id = NULL)
		{
			$cnt = mysqli_num_rows($result);
			if(!$result) 
				return false;				// нечего добавлять
			else if($cnt >= 1)
				{
				if($id == NULL)
					{
					$n = 0;
					while( $row = $result->fetch_assoc() )
						{
						$save[$n] = $row;	// запись в кеш
						$n++;
						}
					}
				else
					while( $row = $result->fetch_assoc() )
						$save[$row[$id]] = $row;	// запись в кеш
				}
			else
				return false;
			$result->close();
			return $save;	// возврат по ссылке ($save) или по значению
		}
		
		//--------------------------------- Возврат ошибки ----------------------------------------------------------------------
		// parent::error - содержит ошибку последнего запроса
		function error()
		{
			return $this->get_msg();
		}
		
		//------------------------------- получение строки из БД -----------------------------------------------------------------
		function row($table, $row_id, $fields='*')
		{
			global $_;
			if (!is_int($row_id)) {$this->push_msg('Номер строки должен быть числом');return false;}
			$fields = $this->prepare_field_name($fields);
			return $this->_query('SELECT '.$fields.' FROM `'.$table.'` WHERE `id`='.$row_id)->fetch_assoc();
		}
		
		//------------------------------- получение значений столбца -------------------------------------------------------------
		function field($table, $fields='*', $from=NULL, $count=NULL)
		{
			global $_;
			if (!is_int($where)) {$this->push_msg($_('3-ий параметр должен быть массивом'));return false;}
			$fields = $this->prepare_field_name($fields);
			if($from != NULL && $count == NULL) {$count = $from; $from = NULL;}
			if($from != NULL && $count != NULL)
				return $this->_query('SELECT '.$fields.' FROM `'.$table.'` LIMIT '.$from.','.$count);
			elseif($from == NULL && $count != NULL)
				return $this->_query('SELECT '.$fields.' FROM `'.$table.'` LIMIT '.$count);
			elseif($from == NULL && $count ==NULL)
				return $this->_query('SELECT '.$fields.' FROM `'.$table.'`');
		}
		
		//----------------------- подготовка имен таблиц для вставки в запрос -----------------------------------------------------
		function prepare_field_name($fields)
		{
			if($fields != '*'){
				$flds = explode(',', $fields);
				foreach($flds as &$field){
					$field = trim($field);
					if($field[0] != '`') $field = '`'. $field;
					if($field[strlen($field)-1] != '`') $field .= '`';
				}
				return implode($flds, ',');
			}else
				return $fields;
		}
	}
?>
