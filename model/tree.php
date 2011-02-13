<?php
class Tree{
	public static function insert($parent_id=null){
		if($parent_id!=null){
			$parent_id = sql_escape($parent_id);
			$query = "SELECT `rgt` FROM `tree` where `tree_id`=$parent_id";
			$result = mysql_query($query);
			list($right) = mysql_fetch_row($result);
			
			$query = "UPDATE `tree` SET `rgt`=`rgt`+2 WHERE `rgt`>=$right";
			mysql_query($query);
			
			$query = "UPDATE `tree` SET `lft`=`lft`+2 WHERE `lft`>=$right";
			mysql_query($query);
			
			$query = "INSERT INTO `tree` (`lft`, `rgt`) VALUES ($right, $right+1)";
			mysql_query($query);
			
			return mysql_insert_id();
		}
		else{
			$query = "SELECT MAX(`rgt`) FROM `tree`";
			$result = mysql_query($query);
			list($right) = mysql_fetch_row($result);
			if($right==null){
				$left = 1;
				$right = 2;
			}
			else{
				$left = $right+1;
				$right = $left+1;
			}
			$query = "INSERT INTO `tree` (`lft`, `rgt`) VALUES ($left, $right)";
			mysql_query($query);
			return mysql_insert_id();
		}
	}

	private static function construct_tree(&$rows, &$parent_index=0){
		$id = $rows[$parent_index]['tree_id'];
		$tree = array();

		$left = $rows[$parent_index]['lft'];
		$right = $rows[$parent_index]['rgt'];

		$index = $parent_index+1;

		while($index<count($rows) && $rows[$index]['lft']<=$right){
			$tree[] = $this->construct_tree($rows, $index);
			$index++;
		}
		$parent_index = $index-1;
		return array('id'=>$id, 'children'=>$tree);
	}

	public static function select_tree($id=null, $join="", $select=""){
		if(strlen($select)>0)
			$select = ", ".$select;
		if($id==null){
			$query =
				"SELECT `tree_id`, `lft`, `rgt`"
				.$select
				." FROM `tree` "
				.$join
				." ORDER BY `lft`";
			$result = mysql_query($query);
		}
		else{
			$query = "SELECT `lft`, `rgt` FROM `tree` WHERE `tree_id`=$id";
			$result = mysql_query($query);
			list($left, $right) = mysql_fetch_row($result);
			$query = "SELECT `tree_id`, `lft`, `rgt` FROM `tree` $join WHERE `lft`>=$left AND `rgt` <=$right ORDER BY `lft`";
			$result = mysql_query($query);
		}
		$rows = array();
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
		}
		$return = array();
		$parent = 0;
		while($parent<count($rows)){
			$return[] = $this->construct_tree($rows, $parent);
			$parent++;
		}
		return $return;
	}
	
	public static function select_children($id=null, $join="", $select=""){
		if(!isset($id))
			return Tree::select_top($join, $select);
		
		if(strlen($select)>0)
			$select = ", ".$select;

		$query = "SELECT `lft`, `rgt` FROM `tree` WHERE `tree_id`=$id";
		$result = mysql_query($query);
		list($left, $right) = mysql_fetch_row($result);
		
		$query = "SELECT `C`.`tree_id`, `C`.`lft`, `C`.`rgt`\n"
			.$select."\n"
			."FROM `tree` AS `C`\n"
			.$join."\n"
			."WHERE `lft` > $left AND  `lft` < $right\n"
			."	AND $left=(SELECT MAX(`P`.`lft`) FROM `tree` AS `P` WHERE `C`.`lft` > `P`.`lft` AND `C`.`lft` < `P`.`rgt`)"
			."ORDER BY `C`.`lft`";
		
		$result = mysql_query($query);
		$rows = array();
		while($row = mysql_fetch_assoc($result))
			$rows[] = $row;
		return $rows;
	}
	
	public static function select_top($join="", $select=""){
		if(strlen($select)>0)
			$select = ", ".$select;
		
		$query = 
		"SELECT `C`.`tree_id`, `C`.`lft`, `C`.`rgt`\n"
		.$select."\n"
		."FROM `tree` AS `C`\n"
		."$join\n"
		."WHERE NOT EXISTS (SELECT * FROM `tree` AS `P` WHERE `C`.`lft` > `P`.`lft` AND `C`.`lft` < `P`.`rgt`)\n"
		."ORDER BY `C`.`lft`";
		
		$result = mysql_query($query);
		$rows = array();
		while($row = mysql_fetch_assoc($result))
			$rows[] = $row;
		return $rows;
	}
	
	public static function select_path($id, $join="", $select=""){
		if(strlen($select)>0)
			$select = ", ".$select;
		$query = "SELECT `lft`, `rgt` FROM `tree` WHERE `tree_id`=$id";
		$result = mysql_query($query);
		list($left, $right) = mysql_fetch_row($result);
		$query = "SELECT `C`.`tree_id`, `C`.`lft`, `C`.`rgt` $select FROM `tree` as `C` $join WHERE `C`.`lft`<$left AND `C`.`rgt`>$right ORDER BY `C`.`lft` ASC";
		$result = mysql_query($query);
		$rows = array();
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
		}
		
		return $rows;
	}

	public static function delete_tree($id=null){
		if($id==null){
			$query = "DELETE FROM `tree`";
			mysql_query($query);
		}
		else{
			$query = "SELECT `lft`, `rgt` FROM `tree` where `tree_id`=$id";
			$result = mysql_query($query);
			list($left, $right) = mysql_fetch_row($result);
			$query = "DELETE FROM `tree` where `lft`>=$left AND `rgt`<=$right";
			mysql_query($query);
			$offset= $right+1-$left;
			$query = "UPDATE `tree` SET `lft`=`lft`-$offset WHERE `lft`>$right";
			mysql_query($query);
			$query = "UPDATE `tree` SET `rgt`=`rgt`-$offset WHERE `rgt`>$right";
			mysql_query($query);
		}
	}

	public static function move_tree($id, $parent_id){
		$query = "SELECT `lft`, `rgt` FROM `tree` where `tree_id`=$id";
		$result = mysql_query($query);
		list($left, $right) = mysql_fetch_row($result);

		$query = "SELECT `lft`, `rgt` FROM `tree` where `tree_id`=$parent_id";
		$result = mysql_query($query);
		list($parent_left, $parent_right) = mysql_fetch_row($result);

		if($parent_left>$left && $parent_right<$right)
			throw(new Exception('Invalid operation'));

		$query = "SELECT MAX(`rgt`) FROM `tree`";
		$result = mysql_query($query);
		list($max_right) = mysql_fetch_row($result);

		$offset = $max_right + 1 - $left;
		$query = "UPDATE `tree` SET `lft`=`lft`+$offset, `rgt`=`rgt`+$offset WHERE `rgt`>=$left AND `rgt`<=$right";
		mysql_query($query);

		$delta = $right + 1 - $left;
		if($parent_right>$right){
			$query = "UPDATE `tree` SET `lft`=`lft`-$delta WHERE `lft`>$right AND `lft`<$parent_right";
			mysql_query($query);

			$query = "UPDATE `tree` SET `rgt`=`rgt`-$delta WHERE `rgt`>$right AND `rgt`<$parent_right";
			mysql_query($query);
		}
		else{
			$query = "UPDATE `tree` SET `lft`=`lft`+$delta WHERE `lft`>=$parent_right AND `lft`<$left";
			mysql_query($query);

			$query = "UPDATE `tree` SET `rgt`=`rgt`+$delta WHERE `rgt`>=$parent_right AND `rgt`<$left";
			mysql_query($query);

			$parent_right+=$delta;
		}

		$query = "UPDATE `tree` SET `rgt`=`rgt`-$offset-$right+$parent_right-1 WHERE `lft`>$max_right";
		mysql_query($query);

		$query = "UPDATE `tree` SET `lft`=`lft`-$offset-$right+$parent_right-1 WHERE `lft`>$max_right";
		mysql_query($query);
	}
}
/*END OF FILE*/