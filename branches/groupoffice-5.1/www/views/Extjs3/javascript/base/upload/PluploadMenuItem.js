Ext.namespace('GO.base.upload');

GO.base.upload.PluploadMenuItem = Ext.extend(Ext.menu.Item, {
	constructor: function(config) {
			
		Ext.applyIf(config, {
			iconCls: 'btn-upload',
			text: GO.lang.upload,
			window_width: 640,
			window_height: 480,
			window_title: GO.lang.upload,
			clearOnClose: false, //clear queue after window is closed (actually window is hidden )		
			upload_config: {}
		});

		this.uploadpanel = new GO.base.upload.PluploadPanel(config.upload_config);
		
		var title = config.window_title || config.text || 'Upload files';
		
		title += " ("+GO.lang.strMax+": "+this.uploadpanel.max_file_size+")";
		

		this.window = new GO.Window({ 
			title: title,
			width: config.window_width || 640, 
			height: config.window_height || 380, 
			layout: 'fit', 
			items: this.uploadpanel, 
			closeAction: 'hide',
			listeners: {
				hide: function (window) {
					if ( this.clearOnClose ) {
						this.uploadpanel.onDeleteAll();
					}
				},
				scope: this
			}
		});

		this.handler = function () { 
			this.window.show(); 
			this.uploadpanel.doLayout();
		};
        
		GO.base.upload.PluploadMenuItem.superclass.constructor.apply(this, arguments);
	},
	lowerMaxFileSize: function(new_max_filesize) {
		if(new_max_filesize < 0)
				new_max_filesize=0;
		var go_max_filesize = Math.ceil(GO.settings.config.max_file_size/1024/1024);
		if(new_max_filesize < go_max_filesize) {
			this.uploadpanel.max_file_size = new_max_filesize+'mb';
		} else {
			this.uploadpanel.max_file_size = go_max_filesize+'mb';
		}
		this.window.setTitle(" ("+GO.lang.strMax+": "+this.uploadpanel.max_file_size+")");
		if(this.uploadpanel.uploader){
			this.uploadpanel.uploader.settings.max_file_size = this.uploadpanel.max_file_size;
		}
		//this.render();
		//this.uploader.max_file_size = this.max_file_size;
	}
});