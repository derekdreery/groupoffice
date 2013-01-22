<?php
class GO_Base_Util_TagParser{
	
	public static function getTags($tagName, $text){
		
		$pattern = '/<'.$tagName.' ([^>]*)>/sU';		
		$matched_tags=array();		
		preg_match_all($pattern,$text,$matched_tags, PREG_SET_ORDER);
		
		$tags = array();	
		for($n=0;$n<count($matched_tags);$n++) {			
			// parse params
			$params_array = array();
			$params=array();
			preg_match_all('/\s*([^=]+)="([^"]*)"/',$matched_tags[$n][1],$params, PREG_SET_ORDER);
			for ($i=0; $i<count($params);$i++) {
				$right = $params[$i][2];
				$left = $params[$i][1];
				$params_array[$left]= $right;
			}
			$tags[] = $params_array;
		}
		
		return $tags;
	}
}