<?php
require_once('html.php');

function markup($text){
	$text = str_replace("\r", "", $text);
	if(strrpos($text, "\n")!==strlen($text)-1)
		$text .= "\n";
	$blocks_return = parse_blocks($text);
	if(is_null($blocks_return))
		return null;
	return $blocks_return['result'];
}

function parse_blocks($text){
	$parsed = 0;
	$result = "";
	while(!is_null($block_return = parse_block($text))){
		$block_parsed = $block_return['parsed'];
		$block_result = $block_return['result'];
		
		$text = substr($text, $block_parsed);
		
		$parsed += $block_parsed;
		$result .= $block_result;
	}
	
	if($parsed == 0)
		return null;
	
	return array('result' => $result, 'parsed' => $parsed);
}

function parse_block($text){
	$quote_return = parse_quote($text);
	if(!is_null($quote_return)){
		return $quote_return;
	}
	
	$header_return = parse_header($text);
	if(!is_null($header_return)){
		return $header_return;
	}
	
	return parse_paragraph($text);
}

function parse_paragraph($text){
	$result = "<p>";
	$parsed = 0;
	
	$continue = true;
	while($continue){
		$continue = false;
		
		$inline_many_return = parse_inline_many($text);
		if(!is_null($inline_many_return)){
			$parsed += $inline_many_return['parsed'];
			$result .= $inline_many_return['result'];
			
			$text = substr($text, $inline_many_return['parsed']);
		}
		
		if($text[0]=='['){
			$result .= $text[0];
			$parsed++;
			
			$text = substr($text, 1);
			
			$continue = true;
		}
	}
	
	if(strpos($text, "\n")!==0){
		return null;
	}
	
	$parsed++;
	$result .= "</p>";
	
	return array('result' => $result, 'parsed' => $parsed);
}

function parse_inline($text){
	$parsed = 0;
	$result = "";

	do{
		$bold_return = parse_bold($text);
		if(!is_null($bold_return)){
			$parsed += $bold_return['parsed'];
			$result .= $bold_return['result'];
			$text = substr($text, $bold_return['parsed']);
			break;
		}
		
		$text_return = parse_text($text);
		if(!is_null($text_return)){
			$parsed += $text_return['parsed'];
			$result .= $text_return['result'];
			$text = substr($text, $text_return['parsed']);
			break;
		}
	} while (false);
	
	if($parsed == 0) return null;
	return array(
		'result' => $result,
		'parsed' => $parsed
	);
}

function parse_inline_many($text){
	$parsed = 0;
	$result = "";
	
	while(true){
		$inline_return = parse_inline($text);
		if(is_null($inline_return))
			break;
		
		$parsed += $inline_return['parsed'];
		$result .= $inline_return['result'];
		$text = substr($text, $inline_return['parsed']);
	}
	
	if($parsed == 0)
		return null;
	
	return array('result' => $result, 'parsed' => $parsed);
}

function parse_bold($text){
	$parsed = 0;
	$result = "<strong>";
	
	if(strpos($text, '[b]') !== 0)
		return null;
		
	// [b]
	// 123
	$parsed += 3;
	$text = substr($text, 3);
	
	$text_return = parse_text($text);
	if(is_null($text_return))
		return null;

	$parsed += $text_return['parsed'];
	$result .= $text_return['result'];
	
	$text = substr($text, $text_return['parsed']);
	
	if(strpos($text, '[/b]') !== 0)
		return null;
	
	// [/b]
	// 1234
	$parsed += 4;
	$result .= "</strong>";
	return array('result' => $result, 'parsed' => $parsed);
}

function parse_header($text){
	$parsed = 0;
	$result = "";
	
	if(strpos($text, '[h') !== 0)
		return null;
	
	// [h
	// 12
	$parsed += 2;
	$text = substr($text, 2);
	
	$level = $text[0];
	if(!in_array($level, array('1', '2', '3')))
		return null;
		
	$parsed += 1;
	$text = substr($text, 1);
	
	if($text[0] != ']')
		return null;
	
	$parsed += 1;
	$text = substr($text, 1);
	
	$result = "<h$level>";
	
	while(true){
		$inline_many_return = parse_inline_many($text);
		if(is_null($inline_many_return))
			return null;

		$parsed += $inline_many_return['parsed'];
		$result .= $inline_many_return['result'];
		
		$text = substr($text, $inline_many_return['parsed']);
		
		if(strpos($text, "[/h$level]\n") === 0)
			break;
		
		if(strpos($text, '[')===0){
			$result .= '[';
			$parsed++;
			
			$text = substr($text, 1);
			
			continue;
		}
	}
	
	if(strpos($text, "[/h$level]\n") !== 0)
		return null;
	
	// [/h#]\n
	// 12345 6
	$parsed += 6;
	$result .= "</h$level>";
	
	$text = substr($text, 6);
	
	return array(
		'result' => $result,
		'parsed' => $parsed);
}

function parse_text($text){
	$nl_pos = strpos($text, "\n");
	$bracket_pos = strpos($text, '[');
	
	if($nl_pos === false)
		$nl_pos = strlen($text);
	
	if($bracket_pos === false || $nl_pos < $bracket_pos){
		$parsed = $nl_pos;
	}
	else{
		$parsed = $bracket_pos;
	}
	$result = html_escape(substr($text, 0, $parsed));
	
	if($parsed == 0)
		return null;
	
	return array('result' => $result, 'parsed' => $parsed);
}

function parse_quote($text){
	$parsed = 0;
	if(strpos('[quote]', $text)!==0)
		return null;
	
	// [quote]
	// 1234567
	$parsed += 7;
	
	$text = substr($text, 7);
	$blocks_return = parse_blocks($text);
	
	if(is_null($blocks_return))
		return null;
	
	$blocks_parsed = $blocks_return['parsed'];
	$blocks_result = $blocks_return['result'];
	
	$parsed += $blocks_parsed;
	$text = substr($text, $blocks_parsed);
	if(strpos("[/quote]\n", $text)!=0)
		return null;
	
	// [/quote]\n
	// 12345678 9
	$parsed += 9;
	
	$result = "<div class='quote'>".$blocks_result."</div>";
	return array('result' => $result, 'parsed' => $parsed);
}
/*END OF FILE*/