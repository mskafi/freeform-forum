<?php
require_once('boot.php');
require_once('model/post.php');
require_once('model/topic.php');
require_once('model/category.php');
require_once('view/topic.php');
require_once('view/category.php');

if(!isset($_SESSION['user']))
	die("you need to be logged in to post a topic.");

$user = $_SESSION['user'];

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$view = 'default';

$category_id = $_REQUEST['category_id'];

if($action == 'process'){
	$topic = TopicView::request();
	$topic['user_id'] = $user['id'];
	
	$errors = array();
	if(Topic::save($topic, $errors)){
		$post =
			array(
				'content' => $topic['content'],
				'topic_id' => $topic['id'],
				'user_id' => $topic['user_id'],
				'created_at' => $topic['created_at']
			);

		Post::save($post);
		$view = 'redirect';
	}
}
else{
	$topic = array('title' => '', 'content' => '', 'category_id' => $category_id);
}

if($view == 'redirect'){
	redirect("topic.php?id=$topic[id]");
}
else{
	if($action != 'process')
		$errors = array();
	
	$cat =
		array_first(
			Category::select(array('id' => $category_id))
		);

	$path = Category::select_path($cat['tree_id']);

	$head	 = "";
	$body	 = "";
	$body	.= "<h2>$cat[title]</h2>";
	$body	.= "<p>$cat[desc]</p>";
	$body	.= TopicView::render_add($topic, $errors);
	$body	.= render_link("category.php?id=$category_id", 'Back');
}

echo CategoryView::render($path, $cat, $user, null, $head, $body, true);
/*END OF FILE*/
