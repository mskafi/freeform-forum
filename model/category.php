<?php
require_once('tree.php');
class Category{
	public static function insert(&$row, $parent_tree_id=null){
		$tree_id = Tree::insert($parent_tree_id);
		$xrow = sql_escape_array($row);
		$sql =
			"INSERT INTO `category` (`title`, `desc`, `tree_id`) VALUES ('$xrow[title]', '$xrow[desc]', $tree_id)";
		mysql_query($sql);
		$row['id'] = mysql_insert_id();
	}
	
	public static function update($row){
		$row = sql_escape_array($row);
		$sql =
			"UPDATE `category` SET `title`='$row[title]', `desc`='$row[desc]'
			WHERE `id`=$row[id]";
		mysql_query($sql);
	}
	
	public static function select_children($parent_tree_id=null){
		$select = "`Ctg`.`title`, `Ctg`.`desc`, `Ctg`.`id` as `id`";
		$join = "LEFT JOIN `category` as `Ctg` ON `Ctg`.`tree_id` = `C`.`tree_id`";
		
		return Tree::select_children($parent_tree_id, $join, $select);
	}
	
	public static function select_path($tree_id){
		$select = "`Ctg`.`title`, `Ctg`.`desc`, `Ctg`.`id` as `id`";
		$join = "LEFT JOIN `category` as `Ctg` ON `Ctg`.`tree_id` = `C`.`tree_id`";
		
		return Tree::select_path($tree_id, $join, $select);
	}
	
	public static function select($filter=null){
		$sql = "SELECT * FROM `category`\n";
		if(isset($filter['id'])){
			$sql .= "WHERE `id`=$filter[id]";
		}
		return query_assoc($sql);
	}
}
/*END OF FILE*/
