<?php
class HtmlView{
	public static function render($head, $body, $user=false){
		ob_start();
		if(isset($user))
			$user = html_escape_array($user);
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>FreeForm Forum</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
		<?php echo $head; ?>
	</head>
	<body>
		<div>
			<h1 id="name">Freeform Forum</h1>
			<div id="nav">
			<?php
			if(!isset($user)){
				echo "[".render_link('login.php', 'Log in')."] ";
				echo "[".render_link('register.php', 'Register')."] ";
			}
			else{
				echo "<div>";
				echo "[".render_link('logout.php', 'Log out')."] ";
				echo "</div>";
				echo "<div>You are signed in as <strong>$user[name]</strong></div>";
			}
			?>
			</div>
		</div>
		<div id="container">
		<?php
		echo $body;
		?>
		</div>
	</body>
</html>
		<?php
		return ob_get_clean();
	}
}
/*END OF FILE*/
?>