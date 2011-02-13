<?php
require_once('boot.php');
require_once('model/user.php');
require_once('view/user.php');
require_once('view/html.php');

unset($_SESSION['user']);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$view = 'default';

if($action == 'process'){
	$user = UserView::request_register();
	$errors = array();
	if(User::save($user, $errors)){
		$_SESSION['user'] = $user;
		$view = 'redirect';
	}
}
else{
	$user = array('name' => '', 'email' => '', 'password' => '');
}

if($view == 'redirect'){
	redirect('index.php');
}
else{
	if($action != 'process'){
		$errors = array();
	}
	
	$header = '';
	$body = '';
	$body .= UserView::render_register($user, $errors);

	echo HtmlView::render($header, $body, null);
}
/*END OF FILE*/
