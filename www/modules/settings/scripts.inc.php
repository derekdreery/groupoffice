<?php

$t = GO::config()->get_setting('login_screen_text_enabled');
		
if(!empty($t)){
	$login_screen_text = GO::config()->get_setting('login_screen_text');
	$login_screen_text_title = GO::config()->get_setting('login_screen_text_title');
	$GO_SCRIPTS .=  'GO.mainLayout.on("login", function(mainLayout){mainLayout.msg("'.String::escape_javascript ($login_screen_text_title).'", "'.String::escape_javascript ($login_screen_text).'", 3600, 400);});';
}
	