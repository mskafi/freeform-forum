<?php
class UserView{
	public static function render_register($row, $errors=array()){
		ob_start();
		?>
		<form action='register.php' method='post'>
			<input name="action" type="hidden" value="process" />
			<div>
				<label>
					<strong>Name</strong><br/>
					<input type="text" name="name" value="<?php echo html_escape($row['name']); ?>"/>
				</label>
				<?php
				if(isset($errors['name'])){
					if(in_array('too long', $errors['name']))
						echo '<div class="error">Chosen name too long</div>';
					if(in_array('format', $errors['name']))
						echo '<div class="error">Name must start with an alphabet, and only contain alphabets, numbers, or underscores</div>';
					if(in_array('duplicate', $errors['name']))
						echo '<div class="error">This name is already in use</div>';
				}
				?>
			</div>
			<div>
				<label>
					<strong>Password</strong><br/>
					<input type="password" name="password" />
				</label>
				<?php
				if(isset($errors['password'])){
					if(in_array('too short', $errors['password']))
						echo '<div class="error">Password must be at least 6 characters</div>';
					if(in_array('too long', $errors['password']))
						echo '<div class="error">Password is too long</div>';
				}
				?>
			</div>
			<div>
				<label>
					<strong>Email</strong><br/>
					<input type="text" name="email" value="<?php echo html_escape($row['email']); ?>"/>
				</label>
				<?php
				if(isset($errors['email'])){
					if(in_array('format', $errors['email']))
						echo '<div class="error">A correctly formatted email is required</div>';
				}
				?>
			</div>
			<div>
				<input type="submit" value="Register"/>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}
	
	public static function render_login(){
		ob_start();
		?>
		<form action='login.php' method='post'>
			<input name="action" type="hidden" value="process" />
			<div>
				<label>
					<strong>Name</strong> <br/>
					<input type="text" name="name" />
				</label>
			</div>
			<div>
				<label>
					<strong>Password</strong> <br/>
					<input type="password" name="password" />
				</label>
			</div>
			<div>
				<input type="submit" value="Login"/>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}
	
	public static function request_register(){
		return array(
			'name' => $_REQUEST['name'],
			'password' => $_REQUEST['password'],
			'email' => $_REQUEST['email'],
			'registered_at' => sql_time(),
			'ip' => $_SERVER['REMOTE_ADDR']
		);
	}
	
	public static function request_login(){
		return array(
			'name' => $_REQUEST['name'],
			'password' => $_REQUEST['password']
		);
	}
}
/*END OF FILE*/