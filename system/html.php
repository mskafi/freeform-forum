<?php
function html_escape($in){
	return htmlspecialchars($in);
}

function html_escape_array($array){
	return array_map("html_escape", $array);
}

function render_link($href, $content){
	$href = str_replace('&', '&amp;', $href);
	$content = $content;
	return "<a href='$href'>$content</a>";
}

function render_paging($n, $page, $width, $base){
	if($n <= 1)
		return '';
	ob_start();
	
	$min = ceil($page - $width/2);
	$max = floor($page + $width/2);
	if($min < 0){
		$max += (0-$min);
		$min = 0;
	}
	if($max >= $n){
		$min -= $max-($n-1);
		$max = $n-1;
	}
	if($min < 0)
		$min = 0;
	
	if($min != 0){
		echo render_link($base.'0', '1')." ";
	}
	for($i = $min; $i <= $max; $i++){
		$p = $i + 1;
		if($i == $page)
			echo "<span class='selected'>$p</span> ";
		else{
			echo render_link($base.$i, $p)." ";
		}
	}
	if($max != $n-1){
		$top = $n - 1;
		$p = $top+1;
		echo render_link($base.$top, $p)." ";
	}
	
	$output = ob_get_contents();
	ob_end_clean();
	
	return $output;
}