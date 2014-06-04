<?php
class GO_Base_Util_TagParser{
	
	/**
	 * Parse as string for tags. Tags must be closed.
	 * 
	 * @param string $tagName eg. img
	 * @param string $text The text to parse
	 * 
	 * @return array eg. array(3) {
				["xml"]=>
				string(82) "<site:img id="1" lightbox="1" path="testing">
				<img src="blabla" />
				</site:img>"
				["params"]=>
				array(3) {
					["id"]=>
					string(1) "1"
					["lightbox"]=>
					string(1) "1"
					["path"]=>
					string(7) "testing"
				}
				["innerXml"]=>
				string(26) "
				<img src="blabla" />
				"
			}
	 */
	public static function getTags($tagName, $text){
		
		$pattern = '/<'.$tagName.'([^>]*)>(.*?)<\/'.$tagName.'>/s';		
		
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
			
			$tag = array(
					'xml'=>$matched_tags[$n][0],
					'params'=>$params_array,
					'innerXml'=>isset($matched_tags[$n][2]) ? $matched_tags[$n][2]	: null
					);
			
			$tags[] = $tag;
		}
		
		return $tags;
	}
}