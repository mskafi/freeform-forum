<?php
function query_assoc($sql){
	$set = mysql_query($sql);
	
	if($set === false) return false;
	
	$result = array();
	while($row = mysql_fetch_assoc($set)){
		$result[] = $row;
	}
	
	return $result;
}

function sql_escape($in){
	return mysql_real_escape_string($in);
}

function sql_escape_array($in){
	return array_map("mysql_real_escape_string", $in);
}

function sql_insert($table, &$in){
	if(empty($in)) return;
	
	$keys =
		join(
			", ",
			array_map(
				"sql_tick",
				sql_escape_array(array_keys($in))
			)
		);
	$values =
		join(
			", ",
			array_map(
				"sql_quote",
				sql_escape_array(array_values($in))
			)
		);
	
	$sql = "INSERT INTO `$table` ($keys) VALUES ($values)";
	mysql_query($query);
	$in['id'] = mysql_insert_id();
}

function sql_update($from, $row, $condition){
	$sql = "UPDATE $from SET ";
	
	$assigns = array();
	foreach($row as $key => $val){
		$key = sql_tick(sql_escape($key));
		$val = sql_quote(sql_escape($val));
		$assigns[] = "$key = $val";
	}
	
	$sql .= implode(", ", $assigns);
	$sql .= $condition;
}

function sql_tick($str){
	return "`$str`";
}

function sql_quote($str){
	return "'$str'";
}

function sql_time(){
	return gmdate('Y-m-d H:i:s');
}
/*END OF FILE*/