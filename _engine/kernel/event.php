<?php
	/***************************************************************************

							��������� ������� � �������� ��������

	**************************************************************************/

// -------------------- ����� ������� -----------------------------------------
class cEvent{
	var $handler = array();	// ����������� �������
	
	//---------------- ��� ������ � ������� ��������� ������ ������� -----------
	// ��� ���� ���������� ����� ������������ �������
	function create( $event_name, $array_of_vars=null ){
		if(isset($this->handler[$event_name]) && is_array($this->handler[$event_name])){
			ksort($this->handler[$event_name]);
			foreach($this->handler[$event_name] as $hnds_on_set_priority){ // <- �������� ������ ������� ��������������� �� ����������
				if(is_array($hnds_on_set_priority))
					foreach($hnds_on_set_priority as $hnd){	//<- �������� ������� � ������ �����������
						if($array_of_vars==NULL) $array_of_vars=array();
						call_user_func_array($hnd, $array_of_vars);
					}
				}
			}
	}
	
	//---------------- ��������� ����������� ������� --------------------------
	function add($event_name, $handler_function, $priority=10){
		if(!isset($handler[$event_name][$priority])) $handler[$event_name][$priority] = array();
		$this->handler[$event_name][$priority][] = $handler_function;
	}

}
$event = new cEvent;






// -------------------- ����� �������� -----------------------------------------
class cFilter{
	var $handler = array();	// ����������� �������
	
	//---------------- ��� ������ � ������� ��������� ������ ������� -----------
	// ��� ���� ���������� ����� ������������ �������
	function applay( $filter_name, $value ){
		if(isset($this->handler[$filter_name]) && is_array($this->handler[$filter_name])){
			foreach($this->handler[$filter_name] as $hnds_on_set_priority){ // <- �������� ������ ������� ��������������� �� ����������
				if(is_array($hnds_on_set_priority))
					foreach($hnds_on_set_priority as $hnd)	//<- �������� ������� � ������ �����������
						$value = call_user_func_array($hnd, $value);
				}
			}
		return $value;
	}
	
	//---------------- ��������� ����������� ������� --------------------------
	function add($filter_name, $handler_function, $priority=10){
		if(!isset($handler[$filter_name][$priority])) $handler[$filter_name][$priority] = array();
		$this->handler[$filter_name][$priority][] = $handler_function;
	}

}
	$filter = new cFilter;
?>
