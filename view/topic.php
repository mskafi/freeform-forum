<?php
class TopicView{
	public static function render_add($data, $errors=array()){
		ob_start();
		?>
<form action='add_topic.php' method='post'>
	<input name="action" type="hidden" value="process" />
	<input name="category_id" type="hidden" value="<?php echo $data['category_id']; ?>" />
	<div>
		<label>
			<strong>Title</strong><br/>
			<input name="title" type="text" size="80" value="<?php echo html_escape($data['title']); ?>"/>
			<?php
			if(isset($errors['title'])){
				if(in_array('required', $errors['title']))
					echo '<div class="error">Title is required</div>';
				if(in_array('too long', $errors['title']))
					echo '<div class="error">Title text too long</div>';
			}
			?>
		</label>
	</div>
	<div>
		<label>
			<strong>Message</strong><br/>
			<textarea name="content" rows="12" cols="60"><?php echo html_escape($data['content']); ?></textarea>
			<?php
			if(isset($errors['content'])){
				if(in_array('required', $errors['content']))
					echo '<div class="error">Message is required</div>';
			}
			?>
		</label>
	</div>
	<div><input type="submit" value="Submit"/></div>
</form>
		<?php
		return ob_get_clean();
	}
	
	public static function request(){
		return array(
			'category_id'	=> $_REQUEST['category_id'],
			'title'			=> $_REQUEST['title'],
			'content'		=> $_REQUEST['content'],
			'created_at'	=> sql_time()
		);
	}
	
	public static function render_list($array){
		$result = "";
		foreach($array as $topic){
			$result .= TopicView::render_list_item($topic);
		}
		if(strlen($result)>0)
			return "$result";
		else
			return "";
	}
	
	public static function render_list_item($topic){
		$topic = html_escape_array($topic);
		$last_page = floor(($topic['post_count']-1)/PAGE_SIZE);
		$result =
			'<div class="topic">' .
			'<div>' . render_link("topic.php?id=$topic[id]", $topic['title']) .
			" by " .
			render_link("user.php?id=$topic[creator_id]", $topic['creator_name']) .
			//" at $topic[created_at] " .
			'</div>' .
			'<div class="topic_details">' .
			render_link("topic.php?id=$topic[id]&page=$last_page#p$topic[post_id]", 'Last post') .
			" by " .
			render_link("user.php?id=$topic[poster_id]", $topic['poster_name']) .
			" at $topic[posted_at]" .
			'</div>' .
			'</div>';
			
		
		return $result;
	}
}
/*END OF FILE*/