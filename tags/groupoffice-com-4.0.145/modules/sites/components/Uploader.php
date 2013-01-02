<?php
class GO_Sites_Components_Uploader extends GO_Sites_Components_Component {
	
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
		
	public function __construct($id, GO_Sites_Controller_Site $controller,$params,$formname="form",$uploadTarget=false) {
		$this->formname = $formname;
		$this->uploadTarget = !$uploadTarget?GO::url('core/plupload'):$uploadTarget;
		
		parent::__construct($id, $controller,$params);
	}

	protected function _init(){
		
		$this->_silverLightUrl = GO::config()->host.'modules/sites/components/Uploader/plupload/js/plupload.silverlight.xap';
		$this->_swfUrl = GO::config()->host.'modules/sites/components/Uploader/plupload/js/plupload.flash.swf';
		
		$this->_page->attachHeaderInclude('js',GO::config()->host.'modules/sites/components/Uploader/jquery-1.7.1.min.js');
		$this->_page->attachHeaderInclude('js',GO::config()->host.'modules/sites/components/Uploader/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js');
		$this->_page->attachHeaderInclude('js',GO::config()->host.'modules/sites/components/Uploader/plupload/js/plupload.full.js');
		$this->_page->attachHeaderInclude('css',GO::config()->host.'modules/sites/components/Uploader/uploader_style.css');
		$this->_page->attachCustomHeaderScript($this->createjs());
	}
	
	public function render() {
		echo '<div id="'.$this->_id.'">'.$this->_controller->t('uploader_noFlash').'</div>';
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
		
		return "<script>".$script."</script>";
	} 	
}