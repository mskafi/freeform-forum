<?php
require_once('boot.php');
require_once('model/post.php');
require_once('model/topic.php');
require_once('model/category.php');
require_once('view/topic.php');
require_once('view/category.php');
require_once('view/post.php');

if(!isset($_SESSION['user']))
	die("you need to be logged in to post a reply.");

$user = $_SESSION['user'];

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$view = 'default';

$topic_id = $_REQUEST['topic_id'];
if($action == 'process'){
	$errors = array();
	$post = PostView::request();
	$post['user_id'] = $user['id'];

	if(Post::save($post, $errors)){
		$view = 'redirect';
	}
}
else{
	$post = array('content' => '', 'topic_id' => $topic_id);
}

if($view == 'redirect'){
	$count = Post::count(array('topic_id' => $topic_id));
	$page = floor(($count-1) / PAGE_SIZE);
	redirect("topic.php?id=$topic_id&page=$page#p$post[id]");
}
else{
	if($action != 'process')
		$errors = array();
	$topic =
		array_first(
			Topic::select(array('id' => $topic_id))
		);
	$posts =
		Post::select(
			array('topic_id' => $topic_id),
			'DESC',
			PAGE_SIZE
		);
	
	$first_post = 
		array_first(
			Post::select(
				array('topic_id' => $topic_id),
				'ASC',
				1
			)
		);

	$cat =
		array_first(
			Category::select(array('id' => $topic['category_id']))
		);

	$path = Category::select_path($cat['tree_id']);

	$head	 = "";
	$body	 = "";
	$body	.= "<h2>$topic[title]</h2>";
	$body	.= PostView::render_add($post, $errors);
	$body	.= render_link("topic.php?id=$topic[id]", 'Back');
	$body	.= "<h3>Original Post</h3>";
	$body	.= PostView::render_item($first_post);
	$body	.= "<h3>Latest posts (latest first)</h3>";
	$body	.= PostView::render_item_list($posts);

	echo CategoryView::render($path, $cat, $user, null, $head, $body, true);
}

/*END OF FILE*/
