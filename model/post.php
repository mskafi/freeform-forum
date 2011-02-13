<?php
class Post {
	static public function save(&$row, &$errors=null){
		Post::check($row, $errors);
		if(count($errors)>0) return false;
		
		if(!isset($row['id']))
			Post::insert($row);
		else
			Post::update($row);
		return true;
	}
	
	static private function check(&$row, &$errors){
		$errors = array();
		if(strlen($row['content'])==0){
			$errors['content'][] = 'required';
		}
	}
	
	static private function insert(&$row){
		$xrow = sql_escape_array($row);
		$sql = "INSERT INTO `post` (`content`, `created_at`, `topic_id`, `user_id`) VALUES
			('$xrow[content]', '$xrow[created_at]', '$xrow[topic_id]', '$xrow[user_id]')";
		
		mysql_query($sql);
		$row['id'] = mysql_insert_id();
	}
	
	static private function update($row){
		$row = sql_escape_array($row);
		$sql = "UPDATE `post` SET `content`='$row[content]', `created_at`='$row[created_at]',
		`topic_id`='$row[topic_id]', `user_id`='$row[user_id]'
		WHERE `id`=$row[id]";
	}
	
	public static function count($filter){
		$sql = "SELECT COUNT(*) as `count` FROM `post` as `p`\n";
		if(isset($filter))
			$sql .= Post::filter($filter);
		
		$row = array_first(query_assoc($sql));
		return $row['count'];
	}
	
	public static function select($filter, $order='ASC', $limit=null){
		$sql = "SELECT `p`.`id`, `p`.`content`, `p`.`created_at`, `p`.`topic_id`, `u`.`name`, `u`.`id` as `user_id`
		FROM `post` as `p` LEFT JOIN `user` as `u` ON `u`.`id`=`p`.`user_id`";
		if(isset($filter))
			$sql .= Post::filter($filter);
		
		$sql .= "ORDER BY `p`.`created_at` $order\n";
		
		if(isset($limit)){
			if(is_array($limit))
				$sql .= "LIMIT $limit[offset], $limit[count]\n";
			else
				$sql .= "LIMIT $limit\n";
		}
		
		return query_assoc($sql);
	}
	
	private static function filter($filter){
		if(isset($filter['id'])){
			$sql = "WHERE `id`=$filter[id]\n";
		}
		else if(isset($filter['topic_id'])){
			$sql = "WHERE `topic_id`=$filter[topic_id]\n";
		}
		else if(isset($filter['user_id'])){
			$sql = "WHERE `user_id`=$filter[user_id]\n";
		}
		
		return $sql;
	}
}

/*END OF FILE*/