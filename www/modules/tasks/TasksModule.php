<?php
class GO_Tasks_TasksModule extends GO_Base_Module{
	public static function initListeners() {
		//GO_Core_Controller_Settings::addListener('actionLoad', 'GO_Tasks_TasksModule', 'onLoadSettings');		
		//todo create default tasklist on user create and delete. See notes.
	}
	
	public static function loadSettings(&$settingsController, &$params, &$response) {
		//$response['jaja']='het werkt';
	}
}