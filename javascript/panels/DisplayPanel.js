GO.DisplayPanel = Ext.extend(Ext.Panel,{
	
	link_type : 0,
	
	newMenuButton : false,
	
	template : '',
	
	templateConfig : {},
	
	loadParams : {},
	
	idParam : '',
	
	loadUrl : '',
	
	
	createTopToolbar : function(){
		
		this.newMenuButton = new GO.NewMenuButton();		
		
		var tbar=[];
		tbar.push(this.editButton = new Ext.Button({
				iconCls: 'btn-edit', 
				text: GO.lang['cmdEdit'], 
				cls: 'x-btn-text-icon', 
				handler:this.editHandler, 
				scope: this,
				disabled : true
			}));
		
		tbar.push(this.linkBrowseButton = new Ext.Button({
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: this.link_type,folder_id: "0"});				
			},
			disabled: true,
			scope: this
		}));
		
		if(GO.files)
		{
			tbar.push(this.fileBrowseButton = new Ext.Button({
				iconCls: 'go-menu-icon-files', 
				cls: 'x-btn-text-icon', 
				text: GO.files.lang.files,
				handler: function(){
					GO.files.openFolder(this.data.files_path);				
				},
				scope: this,
				disabled: true
			}));
		}
		
		tbar.push(this.newMenuButton);
		
		tbar.push('-');
		tbar.push({            
	      iconCls: "btn-refresh",
	      tooltip:GO.lang.cmdRefresh,      
	      handler: this.reload,
	      scope:this
	  });
	  
	  return tbar;
	},	
	
	initComponent : function(){
		this.autoScroll=true;
		this.split=true;
		this.tbar = this.createTopToolbar();	
		this.xtemplate = new Ext.XTemplate(this.template, this.templateConfig);
		
		GO.DisplayPanel.superclass.initComponent.call(this);		
	},
	
	getLinkName : function(){
		return this.data.name;
	},
	
	setData : function(data)
	{
		data.link_type=this.link_type;
		this.data=data;
		this.editButton.setDisabled(!data.write_permission);
		this.linkBrowseButton.setDisabled(false);
		if(GO.files)
		{
			this.fileBrowseButton.setDisabled(false);
		}
		
		if(data.write_permission)
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:this.link_type,
				text: this.getLinkName(),
				callback:this.reload,
				scope:this
			});
		
		
		this.xtemplate.overwrite(this.body, data);	
	},
	
	load : function(id, reload)
	{
		if(!this.data || this.data.id!=id || reload)
		{
			this.loadParams[this.idParam]=id;
			
			this.body.mask(GO.lang.waitMsgLoad);
			Ext.Ajax.request({
				url: this.loadUrl,
				params:this.loadParams,
				callback: function(options, success, response)
				{
					this.body.unmask();
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{
						var responseParams = Ext.decode(response.responseText);
						this.setData(responseParams.data);
					}				
				},
				scope: this			
			});
		}
	},
	
	reload : function(){
		if(this.data)
			this.load(this.data.id, true);
	},
	
	editHandler : function(){
		
	}
});