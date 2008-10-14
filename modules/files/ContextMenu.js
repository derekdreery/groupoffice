

GO.files.FilesContextMenu = function(config)
{
	if(!config)
	{
		config = {};
	}
	config['shadow']='frame';
	config['minWidth']=180;
	
	
	
	this.downloadButton = new Ext.menu.Item({
					iconCls: 'btn-download',
					text: GO.lang.download,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('download', this, this.clickedAt);
					},
					scope: this
				});
				
	this.gotaButton = new Ext.menu.Item({
					iconCls: 'btn-download-gota',
					text: GO.files.lang.downloadGOTA,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('gota', this, this.clickedAt);
					},
					scope: this
				});
	
	/*this.pasteButton = new Ext.menu.Item({
					iconCls: 'btn-paste',
					text: 'Paste',
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('paste', this);
					},
					scope: this
				});*/
				

	this.deleteButton = new Ext.menu.Item({
					iconCls: 'btn-delete',
					text: GO.lang['cmdDelete'],
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('delete', this, this.clickedAt);
					},
					scope: this
				});

	this.cutButton= new Ext.menu.Item({
					iconCls: 'btn-cut',
					text: GO.lang.cut,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('cut', this, this.clickedAt);
					},
					scope: this
				});
	this.copyButton = new Ext.menu.Item({
					iconCls: 'btn-copy',
					text: GO.lang.copy,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('copy', this, this.clickedAt);
					},
					scope: this
				});
	
	
	this.compressButton = new Ext.menu.Item({
					iconCls: 'btn-compress',
					text: GO.lang.compress,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('compress', this, this.clickedAt);
					},
					scope: this
				});
	this.decompressButton = new Ext.menu.Item({
				iconCls: 'btn-decompress',
				text: GO.lang.decompress,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.fireEvent('decompress', this, this.clickedAt);
				},
				scope: this
			});
	
	
	config['items']=[
				this.downloadButton];
				
	if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
	{
		config['items'].push(this.gotaButton);
	}
				

	config['items'].push({ 
		iconCls: 'btn-properties',
		text: GO.lang['strProperties'], 
		handler: function(){
				this.fireEvent('properties', this, this.clickedAt);
		},
		scope:this					
	});
	
	config['items'].push(new Ext.menu.Separator());
	config['items'].push(this.cutButton);
	config['items'].push(this.copyButton);
	//this.pasteButton,				
	config['items'].push(new Ext.menu.Separator());
	config['items'].push(this.deleteButton);
	config['items'].push(this.compressSeparator = new Ext.menu.Separator());
	config['items'].push(this.compressButton);
	config['items'].push(this.decompressButton);
				
	GO.files.FilesContextMenu.superclass.constructor.call(this, config);
	
	this.addEvents({
		
		'properties' : true,
		'paste' : true,
		'cut' : true,
		'copy' : true,
		'delete' : true,
		'compress' : true,
		'decompress' : true
		
	});
	
}

Ext.extend(GO.files.FilesContextMenu, Ext.menu.Menu,{
	/*tree or grid */
	clickedAt : 'grid',
	
	showAt : function(xy, extension, clickedAt)
	{ 	
		this.clickedAt = clickedAt;
 	
		switch(extension)
	 	{
	 		case 'zip':
	 		case 'tar':
	 		case 'tgz':
	 		case 'gz':
	 			this.downloadButton.show();
	 			this.gotaButton.show();
	 			this.decompressButton.show();
	 			this.compressButton.hide();
	 		break;
	 		
	 		case '':
			case 'folder':
	 			this.downloadButton.hide();
	 			this.gotaButton.hide();
	 			this.decompressButton.hide();
	 			this.compressButton.show();
	 			
	 		break;
	 		
	 		default:
	 			this.downloadButton.show();
	 			this.gotaButton.show();
	 			this.compressButton.show();	
	 			this.decompressButton.hide();
	 		break;	 		
	 	}	
 	
		GO.files.FilesContextMenu.superclass.showAt.call(this, xy);
	}
	
});
