Ext.namespace('GO.base.upload');

GO.base.upload.PluploadButton = function(config){
	
	config = config || {};
	
	var maxFileSize = Math.floor(GO.settings.config.max_file_size/1048576)+'mb';
	
	
	Ext.applyIf(config, {
		iconCls: 'btn-upload',
		text: GO.lang.upload,
		window_width: 640,
		window_height: 480,
		window_title: GO.lang.upload,
		clearOnClose: false, //clear queue after window is closed (actually window is hidden )		
		upload_config: {}
	});
	
	Ext.applyIf(config.upload_config, {
		url: GO.url('core/plupload'),
		//the only required parameter

		//runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
		runtimes: 'html5,gears,silverlight,flash,html4',
		//runtimes: 'html4',
		// first available runtime will be used

		multipart: true,
		multipart_params: {
			param1: 1, 
			param2: 2
		},
		// works as baseParams for store. 
		// Accessible via this.uploader.settings.multipart_params after init
		// multipart must be true
		chunk_size:'2mb',
			
		max_file_size: maxFileSize,

		resize: {
			width: 640, 
			height: 480, 
			quality: 60
		},

		flash_swf_url: BaseHref+'views/Extjs3/javascript/plupload/plupload/js/plupload.flash.swf',
		silverlight_xap_url: BaseHref+'views/Extjs3/javascript/plupload/plupload/js/plupload.silverlight.xap',
		// urls must be set properly or absent, otherwise uploader fail to initialize

		//			filters: [  {
		//				title : "Image files", 
		//				extensions : "jpg,JPG,gif,GIF,png,PNG"
		//			},
		//
		//			{
		//				title : "Zip files", 
		//				extensions : "zip,ZIP"
		//			},
		//
		//			{
		//				title : "Text files", 
		//				extensions : "txt,TXT"
		//			}
		//			],

		runtime_visible: true, // show current runtime in statusbar

		// icon classes for toolbar buttons
		addButtonCls: 'btn-add',
		uploadButtonCls: 'btn-up',
		cancelButtonCls: 'btn-cancel',
		deleteButtonCls: 'btn-delete',

		// localization
		addButtonText: 'Add files',
		uploadButtonText: 'Upload',
		cancelButtonText: 'Cancel upload',
		deleteButtonText: 'Remove',
		deleteSelectedText: '<b>Remove selected</b>',
		deleteUploadedText: 'Remove uploaded',
		deleteAllText: 'Remove ALL',
 
		statusQueuedText: 'Queued',
		statusUploadingText: 'Uploading ({0}%)',
		statusFailedText: '<span style="color: red">FAILED</span>',
		statusDoneText: '<span style="color: green">DONE</span>',
 
		statusInvalidSizeText: 'Too big',
		statusInvalidExtensionText: 'Invalid file type',
 
		emptyText: '<div class="plupload_emptytext"><span>Upload queue is empty</span></div>',
		emptyDropText: '<div class="plupload_emptytext"><span>Drop files here</span></div>',
 
		progressText: '{0}/{1} ({3} failed) ({5}/s)'
	// params are number of
	// {0} files sent
	// {1} total files
	// {2} files successfully uploaded
	// {3} failed files
	// {4} files left in queue
	// {5} current upload speed 

			
	});
		
	GO.base.upload.PluploadButton.superclass.constructor.call(this, config);
}

Ext.extend(GO.base.upload.PluploadButton , Ext.ux.PluploadButton);