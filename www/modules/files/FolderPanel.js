GO.files.FolderPanel = Ext.extend(GO.DisplayPanel,{
	link_type : 17,

	loadParams : {task: 'folder_with_items'},

	idParam : 'folder_id',

	loadUrl : GO.settings.modules.files.url+'json.php',

	noFileBrowser:true,

	editHandler : function(){	
	},

	createTopToolbar : function(){
		var tbar = GO.files.FilePanel.superclass.createTopToolbar.call(this);

		tbar.splice(1,0,{
			iconCls: 'btn-settings',
			text: GO.lang.strProperties,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.showFolderPropertiesDialog(this.link_id+"");
				this.addSaveHandler(GO.files.folderPropertiesDialog);
			},
			scope: this
		});

		return tbar;
	},

	setData : function(data)
	{
		this.setTitle(data.name);
	
		this.topToolbar.items.items[0].setVisible(false);

		GO.files.FolderPanel.superclass.setData.call(this, data);
	},

	initTemplate : function(){
		this.template =

				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td>'+GO.lang.strLocation+':</td>'+
						'<td>{location}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.lang.strType+':</td>'+
						'<td>{type}</td>'+
					'</tr>'+					

					'<tr>'+
						'<td>'+GO.lang.strCtime+':</td>'+
						'<td>{ctime}</td>'+
					'</tr>'+

					'<tr>'+
						'<td>'+GO.lang.strMtime+':</td>'+
						'<td>{mtime}</td>'+
					'</tr>'+

					'<tpl if="!GO.util.empty(comment)">'+
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">'+GO.files.lang.comments+'</td>'+
						'</tr>'+
						'<tr>'+
							'<td colspan="2">{comment}</td>'+
						'</tr>'+
					'</tpl>'+
				'</table>';

		this.template += GO.linksTemplate;	
		
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);

		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}

		GO.files.FolderPanel.superclass.initTemplate.call(this);
	}
});