<?php
require_once('boot.php');
require_once('model/user.php');
require_once('view/user.php');
require_once('view/html.php');

//session_destroy();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$view = 'default';

if($action == 'process'){
	$creds = UserView::request_login();
	$user = array_first(
		User::select(
			array('name' => $creds['name'])
		)
	);
	
	if(isset($user) && $creds['password'] == $user['password']){
		$_SESSION['user'] = $user;
		$view = 'redirect';
	}
}

if($view == 'redirect'){
	redirect('index.php');
}
else{
	$head = "";
	$body = "";
	$body .= UserView::render_login();

	echo HtmlView::render($head, $body, null);
}

/*END OF FILE*/