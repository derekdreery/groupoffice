<?php
class GO_Comments_CommentsModule extends GO_Base_Module{
	public function autoInstall() {
		return true;
	}
	
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		
		GO::config()->save_setting('comments_enable_read_more', isset($params['comments_enable_read_more']) ? $params['comments_enable_read_more'] : '0', GO::user()->id);
		
		return parent::submitSettings($settingsController, $params, $response, $user);
	}
	
	public static function loadReadMore(){
		
		$readMore = GO::config()->get_setting("comments_enable_read_more",GO::user()->id);
		
		if($readMore === false)
			return 1; // By default (when the setting is not set) return 1;
		else
			return $readMore;
	}
	
	public static function commentsRequired(){
		return isset(GO::config()->comments_category_required)?GO::config()->comments_category_required:false;
	} 
	
	
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		
		$response['data']['comments_enable_read_more'] = self::loadReadMore();
		
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
	
}