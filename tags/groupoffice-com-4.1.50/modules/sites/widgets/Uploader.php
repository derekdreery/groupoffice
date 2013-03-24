<?php
class GO_Sites_Widgets_Uploader extends GO_Sites_Widgets_Component {
	
	public $max_file_size = '100mb';
	public $chunk_size = '10mb';
	public $unique_names = true;
	public $runtimes = 'html5,gears,silverlight,flash,browserplus,html4';
	public $uploadTarget = 'upload.php';
	
	public $resizeImages = true;
	public $resizeWidth = 320;
	public $resizeHeight = 240;
	public $resizequality = 90;
	public $formname = "form";
	
	private $_swfUrl = '/plupload/js/plupload.flash.swf';
	private $_silverLightUrl = '/plupload/js/plupload.silverlight.xap';
		
	public function __construct($id,$params,$formname="form",$uploadTarget=false) {
		$this->formname = $formname;
		$this->uploadTarget = !$uploadTarget?GO::url('sites/site/plupload'):$uploadTarget;

		parent::__construct($id, $params);
	}

	protected function _init()
	{
		$assetUrl = GOS::site()->assets->publish(GO::config()->root_path.'modules/sites/widgets/Uploader');
		
		$this->_silverLightUrl = $assetUrl.'/Uploader/plupload/js/plupload.silverlight.xap';
		$this->_swfUrl = $assetUrl.'/Uploader/plupload/js/plupload.flash.swf';
		
		
	}
	
	public function render() {
		GOS::site()->scripts->registerGapiScript('jquery');
		
		$assetUrl = GOS::site()->assets->publish(GO::config()->root_path.'modules/sites/widgets/Uploader');
		
		GOS::site()->scripts->registerScriptFile($assetUrl.'/Uploader/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js'); 
		GOS::site()->scripts->registerScriptFile($assetUrl.'/Uploader/plupload/js/plupload.full.js'); 
		GOS::site()->scripts->registerCssFile($assetUrl.'/Uploader/uploader_style.css'); 
		GOS::site()->scripts->registerScript('UploadComponent', $this->createjs(), GO_Sites_Components_Scripts::POS_READY);
		
		$langFile = 'Uploader/plupload/js/i18n/'.GO::language()->getLanguage().'.js';
		if(file_exists(GO::config()->root_path.'modules/sites/widgets/'.$langFile)){
			GOS::site()->scripts->registerScriptFile($assetUrl.'/'.$langFile); 
		}
		
		echo '<div id="'.$this->_id.'">Loading upload widget...</div>';
	}
	
	private function createjs(){	
		$script = <<<EOD

$(function() {
	// Setup flash version
	$("#$this->_id").pluploadQueue({
		// General settings
		runtimes : '$this->runtimes',
		url : '$this->uploadTarget',
		max_file_size : '$this->max_file_size',
		chunk_size : '$this->chunk_size',
		// unique_names : $this->unique_names,
		multiple_queues : true,
		dragdrop : false,
		// Flash settings
		flash_swf_url : '$this->_swfUrl',
		// Silverlight settings
		silverlight_xap_url : '$this->_silverLightUrl'
	});
	
	var uploader = $("#$this->_id").pluploadQueue();
		
	uploader.bind('FilesAdded', function(up, files) {
			uploader.start();
	});
});

EOD;
		
		return $script;
	} 	
}