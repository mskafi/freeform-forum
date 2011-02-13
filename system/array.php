<?php

function array_first($arr){
	if(!is_array($arr) || count($arr) == 0) return null;
	return $arr[0];
}

function array_sub($array, $subkey){
	$result = array();
	foreach($array as $key => $value){
		$result[$key] = $value[$subkey];
	}
	
	return $result;
}
/*END OF FILE*/