<?php
require_once('boot.php');
require_once('model/category.php');
require_once('model/topic.php');
require_once('view/category.php');
require_once('view/topic.php');
require_once('view/html.php');

$user = null;
if(isset($_SESSION['user']))
	$user = $_SESSION['user'];

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;

$category_id = $_REQUEST['id'];
$category =
	array_first(
		Category::select( array( 'id' => $category_id ) )
	);

$path		= Category::select_path($category['tree_id']);
$children	= Category::select_children($category['tree_id']);
$topics		=
	Topic::select(
		array('category_id' => $category_id ),
		"DESC",
		array(
			'offset' => PAGE_SIZE * $page,
			'count' => PAGE_SIZE
		)
	);

$count = Topic::count(array('category_id' => $category_id));
$page_count = ceil($count/PAGE_SIZE);

$head	 = "";
$body	 = "<h2>$category[title]</h2>";
$body	.= "<p>$category[desc]</p>";
if(isset($user)){
	$body	.= render_link("add_topic.php?category_id=$category_id", 'Add new topic');
}
$body	.= "<div class='paging'>Pages: " . render_paging($page_count, $page, 5, "category.php?id=$category_id&page=") . "</div>";
$body	.= TopicView::render_list($topics);

echo CategoryView::render($path, $category, $user, $children, $head, $body);
/*END OF FILE*/
