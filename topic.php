<?php
require_once('boot.php');
require_once('model/topic.php');
require_once('model/category.php');
require_once('model/post.php');
require_once('view/topic.php');
require_once('view/category.php');
require_once('view/post.php');

$user = null;
if(isset($_SESSION['user']))
	$user = $_SESSION['user'];

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;

$topic_id = $_REQUEST['id'];
$topic =
	array_first(
		Topic::select(array('id' => $topic_id))
	);
	
$place = Topic::place($topic);
$page_in_category = floor($place / PAGE_SIZE);

$posts =
	Post::select(
		array('topic_id' => $topic_id),
		'ASC',
		array(
			'offset' => $page * PAGE_SIZE,
			'count' => PAGE_SIZE
		)
	);

$category =
	array_first(
		Category::select( array( 'id' => $topic['category_id'] ) )
	);

$path = Category::select_path($category['tree_id']);

$count = Post::count(array('topic_id' => $topic_id));
$page_count = ceil($count/PAGE_SIZE);

$head = "";
$body = "";

$body .= "<h2>$topic[title]</h2>";
$body .= "<div class='paging'>Pages: " .
	render_paging(
		$page_count,
		$page,
		5,
		"topic.php?id=$topic_id&page="
	) .
	"</div>";
$body .= PostView::render_item_list($posts);
if(isset($user)){
	$body .= "<div>".render_link('add_post.php?topic_id='.$topic_id, 'Add new post')."</div>";
}
$body .= render_link("category.php?id=$topic[category_id]&page=$page_in_category", 'Back');

echo CategoryView::render($path, $category, $user, null, $head, $body, true);

/*END OF FILE*/