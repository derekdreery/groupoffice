GO.email.UploadFlashDialog = function(config){
	
	if(!config)
	{
		config={};
	}

	this.uploadPanel = config.uploadPanel;

	config.items=[this.uploadPanel];
	config.layout='fit';
	config.title=GO.email.lang.message;
	config.maximizable=true;
	config.modal=false;
	config.width=500;
	config.height=400;
	config.resizable=false;	
	config.minizable=true;
	config.closeAction='hide';	
	config.html='items';
	config.buttons=[{	
		text: GO.lang['cmdClose'],
		handler: function()
		{
			this.hide();
		},
		scope:this
	}];
	
	GO.email.UploadFlashDialog.superclass.constructor.call(this, config);

	this.addEvents({
		'fileUploadSuccess' : true
	});

	this.uploadPanel.on('fileUploadSuccess', function(obj, file, data)
	{
		this.fireEvent('fileUploadSuccess', obj, file, data);
	},this)


	this.uploadPanel.on('fileUploadComplete', function()
	{
		this.removeAllFiles();
	});
	this.uploadPanel.on('fileUploadComplete', function()
	{
		this.hide();
	},this);

	this.uploadPanel.on('swfUploadLoaded', function()
	{
	}, this)
	
}

Ext.extend(GO.email.UploadFlashDialog, Ext.Window,{
		
	show : function()
	{
		if(!this.rendered)
			this.render(Ext.getBody());
		
		GO.email.UploadFlashDialog.superclass.show.call(this);
	}
	
});