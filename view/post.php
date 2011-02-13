<?php
require_once 'PEAR.php';

class PostView{
	public static function render_add($data, $errors=array()){
		ob_start();
		?>
<form action='add_post.php' method='post'>
	<input name="topic_id" type="hidden" value="<?php echo $data['topic_id']; ?>" />
	<input name="action" type="hidden" value="process" />
	<label>
		<strong>Message</strong><br/>
		<textarea name="content" rows="12" cols="60"></textarea>
		<?php
		if(isset($errors['content'])){
			if(in_array('required', $errors['content']))
				echo '<div class="error">Message is required</div>';
		}
		?>
	</label>
	<div><input type="submit" value="Submit"/></div>
</form>
		<?php
		return ob_get_clean();
	}
	
	public static function request(){
		return array(
			'content' => $_REQUEST['content'],
			'topic_id' => $_REQUEST['topic_id'],
			'created_at' => sql_time());
	}

	public static function render_item($post){	
		$text = markup($post['content']);
		
		$result  = "<div id='p$post[id]' class='post'>";
			$result .= "<div class='post_details'>";
				$result .= render_link("topic.php?id=$post[topic_id]#p$post[id]", html_escape($post['created_at']));
				$result .= " by ";
				$result .= render_link("user.php?id=$post[user_id]", html_escape($post['name']));
			$result .= "</div>";
			//$result .= "<p>".nl2br(html_escape($post['content']))."</p>";
			$result .= "$text";
		$result .= "</div>";
		
		return $result;
	}
	
	public static function render_item_list($array){
		return implode(array_map("PostView::render_item", $array));
	}
}
/*END OF FILE*/