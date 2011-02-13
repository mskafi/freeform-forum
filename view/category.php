<?php
require_once('html.php');

class CategoryView{
	public static function render($path, $category, $user, $children, $head, $body, $link=false){
		$templ	 = "<div class='path'>";
		$templ	.= render_link('index.php', 'All');
		$templ	.= " / ";
		$templ	.= CategoryView::render_path($path);
		if(count($path)>0)
			$templ .= " / ";
		$templ	.= $link ? CategoryView::render_path_item($category) : html_escape($category['title']);
		$templ	.= "</div>";
		//$templ	.= "<p>".$category['desc']."</p>";
		if(isset($children))
			$templ .= CategoryView::render_list($children);
		$templ	.= "<hr />";
		
		return HtmlView::render($head, $templ.$body, $user);
	}
	
	public static function render_list($array){
		$result = "";
		foreach($array as $category){
			$result .= "<li>".CategoryView::render_list_item($category)."</li>";
		}
		if(strlen($result)>0)
			return "<ul>$result</ul>";
		else
			return "";
	}
	
	public static function render_path($path){
		return implode(
				" / ",
				array_map(
					"CategoryView::render_path_item",
					$path)
				);
	}
	
	public static function render_list_item($category){
		$category = html_escape_array($category);
		$result = "<a href='category.php?id=$category[id]'>$category[title]</a>: $category[desc]";
		return $result;
	}
	
	public static function render_path_item($category){
		$category = html_escape_array($category);
		$result = "<a href='category.php?id=$category[id]'>$category[title]</a>";
		return $result;
	}
}
/*END OF FILE*/