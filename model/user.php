<?php
class User {
	static public function save(&$row, &$errors=null){
		User::check($row, $errors);
		if(count($errors)>0) return false;
		
		if(!isset($row['id']))
			User::insert($row);
		else
			User::update($row);
		return true;
	}
	
	static private function check(&$row, &$errors){
		if(strlen($row['name'])>20){
			$errors['name'][] = 'too long';
		}
		else if(preg_match('/^[_a-zA-Z0-9]+$/', $row['name']) == 0){
			$errors['name'][] = 'format';
		}
		else {
			$count = User::count(array('name' => $row['name']));
			if($count > 0){
				$errors['name'][] = 'duplicate';
			}
		}
		
		if(strlen($row['password'])<6){
			$errors['password'][] = 'too short';
		}
		if(strlen($row['password'])>40){
			$errors['password'][] = 'too long';
		}
		
		if(preg_match('/^[\w\.]+@\w+.\w+/', $row['email'])==0){
			$errors['email'][] = 'format';
		}
	}
	
	static private function insert(&$row){
		$xrow = sql_escape_array($row);
		$sql = "INSERT INTO `user`
		(`name`, `password`, `email`, `registered_at`, `ip`) VALUES
		('$xrow[name]', '$xrow[password]', '$xrow[email]', '$xrow[registered_at]', '$xrow[ip]')";
		mysql_query($sql);
		$row['id'] = mysql_insert_id();
	}
	
	static private function update($row){
		$row = sql_escape_array($row);
		$sql =
			"UPDATE `user` SET `name`='$row[name]', `password`='$row[password]', `email`='$row[email]',
			`registered_at`='$row[registered_at]', `ip`='$row[ip]
			WHERE `id`=$row[id]";
		mysql_query($sql);
	}
	
	static public function select($filter = array()){
		$sql = "SELECT * FROM `user`\n";
		if(count($filter)>0){
			$sql .= User::filter($filter);
		}
		
		return query_assoc($sql);
	}
	
	static public function count($filter = array()){
		$sql = "SELECT COUNT(*) FROM `user`\n";
		if(count($filter)>0){
			$sql .= User::filter($filter);
		}
		
		$row = array_first(query_assoc($sql));
		return $row['count'];
	}
	
	static private function filter($filter = array()){
		if(isset($filter['id'])){
			$id = $filter['id'];
			return "WHERE `id`=$id\n";
		}
		else if(isset($filter['name'])){
			$name = sql_escape($filter['name']);
			return "WHERE `name`='$name'\n";
		}
	}
}

/*END OF FILE*/