GO.DisplayPanel = Ext.extend(Ext.Panel,{
	
	link_type : 0,
	
	newMenuButton : false,
	
	template : '',
	
	templateConfig : {
		
		notEmpty : function(v){
			if(v && v.length)
			{
				return true;
			}
		}
	},
	
	loadParams : {},
	
	idParam : '',
	
	loadUrl : '',
	
	data : {},
	
	saveHandlerAdded : false,
	
	
	addSaveHandler : function(win)
	{
		if(!this.saveHandlerAdded)
		{
			win.on('save', this.onSave, this);
			this.saveHandlerAdded=true;
		}
	},
	
	
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
				GO.linkBrowser.on('hide', this.reload, this,{single:true});
			},
			scope: this
		}));
		
		if(GO.files)
		{
			tbar.push(this.fileBrowseButton = new Ext.Button({
				iconCls: 'btn-files',
				cls: 'x-btn-text-icon', 
				text: GO.files.lang.files,
				handler: function(){					
					GO.files.openFolder(this.data.files_folder_id);
					GO.files.fileBrowserWin.on('hide', this.reload, this, {single:true});
				},
				scope: this,
				disabled:true
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
	  tbar.push({            
	      iconCls: "btn-print",
	      tooltip:GO.lang.cmdPrint,
	 			handler: function(){
					this.body.print();		
				},
				scope:this
	  });
	  
	  return tbar;
	},	
	
	initComponent : function(){
		this.autoScroll=true;
		this.split=true;
		this.tbar = this.createTopToolbar();	
		this.xtemplate = new Ext.XTemplate(this.template, this.templateConfig);
		this.xtemplate.compile();
		
		GO.DisplayPanel.superclass.initComponent.call(this);		
	},
	
	afterRender : function(){		
		this.getTopToolbar().setDisabled(true);
		GO.DisplayPanel.superclass.afterRender.call(this);
	},
	
	getLinkName : function(){
		return this.data.name;
	},
	
	onSave : function(panel, saved_id)
	{
		if(saved_id > 0 && this.data.id == saved_id)
		{
			this.reload();
		}
	},
	
	gridDeleteCallback : function(config){
		if(this.data)
		{
			var keys = Ext.decode(config.params.delete_keys);				
			for(var i=0;i<keys.length;i++)
			{
				if(this.data.id==keys[i])
				{
					this.reset();
				}
			}
		}
	},
	
	reset : function(){
		this.body.removeAllListeners();
		this.body.update("");
		this.data={};
		
		this.getTopToolbar().setDisabled(true);
	},
	
	setData : function(data)
	{
		this.body.removeAllListeners();
		
		data.link_type=this.link_type;
		this.data=data;
		
		this.getTopToolbar().setDisabled(false);
	
		this.editButton.setDisabled(!data.write_permission);
		
		if(data.write_permission)
		{
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:this.link_type,
				text: this.getLinkName(),
				callback:this.reload,
				scope:this
			});
		}else
		{
			this.newMenuButton.setDisabled(true);
		}		
		
		if(GO.files)
		{
			this.fileBrowseButton.setDisabled(data.files_folder_id<1);
		}
		
		this.xtemplate.overwrite(this.body, data);			
		
		this.body.on('click', this.onBodyClick, this);		
	},
	
	onBodyClick :  function(e, target){
		if(target.tagName!='A')
		{
			target = Ext.get(target).findParent('A', 10);
			if(!target)
				return false;
		}
		
		if(target.tagName=='A')
		{	
			var href=target.attributes['href'].value;
			if(GO.email && href.substr(0,6)=='mailto')
			{
				var indexOf = href.indexOf('?');
				if(indexOf>0)
				{
					var email = href.substr(7, indexOf-8);
				}else
				{
					var email = href.substr(7);
				}				

				e.preventDefault();
				
				GO.email.addressContextMenu.showAt(e.getXY(), email);					
				//this.fireEvent('emailClicked', email);			
			}else 
			{			
				var pos = href.indexOf('#link_');
				if(pos>-1)
				{
					e.preventDefault();
					
					var index = href.substr(pos+6, href.length);		
					var link = this.data.links[index];			
					if(link.link_type=='folder')
					{
						GO.linkBrowser.show({link_id: link.parent_link_id,link_type: link.parent_link_type,folder_id: link.id});
					}else
					{
						GO.linkHandlers[link.link_type].call(this, link.id, {data: link});
					}
				}else
				{
					pos = href.indexOf('#files_');
					if(pos>-1)
					{
						var index = href.substr(pos+7, href.length);		
						var file = this.data.files[index];			
						if(file.extension=='folder')
						{
							var fb = GO.files.openFolder(this.data.files_folder_id, file.id);
							
						}else
						{
							var record = new GO.files.FileRecord(file);
							GO.files.filePropertiesDialog.show(record.get('id'));
						}
					}
				}
			}
		}		
	},
	
	load : function(id, reload)
	{
		if(this.data.id!=id || reload)
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
		if(this.data.id)
			this.load(this.data.id, true);
	},
	
	editHandler : function(){
		
	}
});