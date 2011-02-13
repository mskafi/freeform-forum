<?php
require_once('boot.php');
require_once('model/category.php');
require_once('view/category.php');
require_once('view/html.php');

$user = null;
if(isset($_SESSION['user']))
	$user = $_SESSION['user'];


$cats = Category::select_children();
$head = "";
$body = CategoryView::render_list($cats);

echo HtmlView::render($head, $body, $user);
/*END OF FILE*/