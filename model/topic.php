<?php
class Topic {
	public static function save(&$row, &$error){
		Topic::check($row, $error);
		if(count($error)>0) return false;
		
		if(!isset($row['id']))
			Topic::insert($row);
		else
			Topic::update($row);
		return true;
	}
	
	private static function check(&$row, &$error){
		$error = array();
		if(strlen($row['title'])==0){
			$error['title'][] = 'required';
		}
		if(strlen($row['title'])>100){
			$error['title'][] = 'too long';
		}
		if(strlen($row['content'])==0){
			$error['content'][] = 'required';
		}
	}
	
	private static function insert(&$row){
		$xrow = sql_escape_array($row);
		
		$sql = "INSERT INTO `topic` (`title`, `created_at`, `user_id`, `category_id`) VALUES
			('$row[title]', '$row[created_at]', '$row[user_id]', '$row[category_id]')";
		
		mysql_query($sql);
		$row['id'] = mysql_insert_id();
	}
	
	private static function update($row){
		$row = sql_escape_array($row);
		$sql = "UPDATE `topic` SET `title`='$row[title]', `created_at`='$row[created_at],
			`user_id`='$row[user_id]', `category_id`='$row[category_id]'
			WHERE `id`=$row[id]";
		mysql_query($sql);
	}
	
	public static function select($filter=null, $order='ASC', $limit=null){
		$sql =
		"SELECT
			`t`.`id`, `t`.`title`, `t`.`created_at`, `t`.`category_id`,
			`u`.`name` as `creator_name`, `u`.`id` AS `creator_id`,
			`p`.`created_at` AS `posted_at`, `p`.`user_id` as `poster_id`, `p`.`id` AS `post_id`,
			`pu`.`name` AS `poster_name`,
			(SELECT COUNT(*) FROM `post` as `count_p` WHERE `count_p`.`topic_id`=`t`.`id`) AS `post_count`
		FROM
			`topic` AS `t`
			LEFT JOIN `user` AS `u` ON `u`.`id`=`t`.`user_id`
			LEFT JOIN `post` AS `p` ON `p`.`topic_id`=`t`.`id`
			LEFT JOIN `user` AS `pu` ON `p`.`user_id`=`pu`.`id`
		WHERE
			`p`.`id` = (
				SELECT `sub_p`.`id`
				FROM `post` AS `sub_p`
				WHERE `sub_p`.`topic_id`=`t`.`id`
				ORDER BY `sub_p`.`created_at` DESC
				LIMIT 1
			)
		\n";
		
		if(isset($filter))
			$sql .= "AND\n".Topic::filter($filter);
		
		$sql .= "ORDER BY `posted_at` $order\n";
		
		if(isset($limit)){
			if(is_array($limit))
				$sql .= "LIMIT $limit[offset], $limit[count]\n";
			else
				$sql .= "LIMIT $limit\n";
		}
		
		return query_assoc($sql);
	}
	
	public static function count($filter){
		$sql = "SELECT COUNT(*) as `count` FROM `topic` as `t`\n";
		if(isset($filter))
			$sql .= "WHERE ".Topic::filter($filter);
		
		$row = array_first(query_assoc($sql));
		return $row['count'];
	}
	
	private function filter($filter){
		if(isset($filter['id'])){
			$sql = "`t`.`id`=$filter[id]\n";
		}
		else if(isset($filter['category_id'])){
			$sql = "`t`.`category_id`=$filter[category_id]\n";
		}
		else if(isset($filter['user_id'])){
			$sql = "`t`.`user_id`=$filter[user_id]\n";
		}
		
		return $sql;
	}
	
	public function place($topic){
		$id = $topic['id'];
		$cat = $topic['category_id'];
		
		$sql =
		"SELECT COUNT(*) as `count`
		FROM
			`topic` AS `t1`
			LEFT JOIN `user` AS `u1` ON `u1`.`id`=`t1`.`user_id`
			LEFT JOIN `post` AS `p1` ON `p1`.`topic_id`=`t1`.`id`,
			`topic` AS `t2`
			LEFT JOIN `user` AS `u2` ON `u2`.`id`=`t2`.`user_id`
			LEFT JOIN `post` AS `p2` ON `p2`.`topic_id`=`t2`.`id`
		WHERE
			`p1`.`id` = (
				SELECT `sub_p`.`id`
				FROM `post` AS `sub_p`
				WHERE `sub_p`.`topic_id`=`t1`.`id`
				ORDER BY `sub_p`.`created_at` DESC
				LIMIT 1
			) AND
			`p2`.`id` = (
				SELECT `sub_p`.`id`
				FROM `post` AS `sub_p`
				WHERE `sub_p`.`topic_id`=`t2`.`id`
				ORDER BY `sub_p`.`created_at` DESC
				LIMIT 1
			) AND
			`t1`.`id`=$id AND
			`t1`.`category_id`=$cat AND
			`t2`.`category_id`=$cat AND
			`p1`.`created_at` < `p2`.`created_at`";
		
		$row = array_first(query_assoc($sql));
		echo mysql_error();
		return $row['count'];
	}
}
/*END OF FILE*/