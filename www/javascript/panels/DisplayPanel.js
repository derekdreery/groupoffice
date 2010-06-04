/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.DisplayPanel=Ext.extend(Ext.Panel,{
	link_id: 0,
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

	noFileBrowser : false,

	
	
	addSaveHandler : function(win, eventName)
	{
		if(!eventName)
			eventName='save';

		if(!this.saveHandlerAdded)
		{
			win.on(eventName, this.onSave, this);
			this.saveHandlerAdded=true;
		}
	},
	
	
	createTopToolbar : function(){
		
		this.newMenuButton = new GO.NewMenuButton({
			panel:this
		});
		
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
				if(!GO.linkBrowser){
					GO.linkBrowser = new GO.LinkBrowser();
				}
				GO.linkBrowser.show({link_id: this.data.id,link_type: this.link_type,folder_id: "0"});
				GO.linkBrowser.on('hide', this.reload, this,{single:true});
			},
			scope: this
		}));
		
		if(GO.files && !this.noFileBrowser)
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

	initTemplate : function(){

	},
	
	initComponent : function(){
		this.autoScroll=true;
		this.split=true;
		var tbar = this.createTopToolbar();
		if(tbar)
			this.tbar = tbar;

		this.initTemplate();

		this.xtemplate = new Ext.XTemplate(this.template, this.templateConfig);
		this.xtemplate.compile();
		
		GO.DisplayPanel.superclass.initComponent.call(this);

		this.on('expand', function(){
			if(this.collapsedLinkId){
				this.load(this.collapsedLinkId);
				delete this.collapsedLinkId;
			}
		}, this);
	},
	
	afterRender : function(){		
		
		GO.DisplayPanel.superclass.afterRender.call(this);

		var tbar = this.getTopToolbar();
		if(tbar)
			tbar.setDisabled(true);

		this.body.on('click', this.onBodyClick, this);
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
		//this.body.removeAllListeners();
		this.body.update("");
		this.data={};
		this.link_id=this.collapsedLinkId=0;
		var tbar = this.getTopToolbar();
		if(tbar)
			tbar.setDisabled(true);
	},
	
	setData : function(data)
	{
		//this.body.removeAllListeners();
		
		data.link_type=this.link_type;
		this.data=data;
		
		var tbar = this.getTopToolbar();
		if(tbar)
			tbar.setDisabled(false);
	
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
		
		if(this.fileBrowseButton)
		{
			this.fileBrowseButton.setDisabled(data.files_folder_id<1);
		}
		
		this.xtemplate.overwrite(this.body, data);

		this.body.scrollTo('top', 0);
		
		//this.body.on('click', this.onBodyClick, this);
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
			e.preventDefault();
			
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

				//e.preventDefault();
				
				GO.email.addressContextMenu.showAt(e.getXY(), email);					
				//this.fireEvent('emailClicked', email);			
			}else 
			{			
				var pos = href.indexOf('#link_');
				if(pos>-1)
				{
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
							GO.files.openFolder(this.data.files_folder_id, file.id);							
						}else
						{
							if(GO.files){
								var record = new GO.files.FileRecord(file);
								GO.files.showFilePropertiesDialog(record.get('id'));
							}else
							{
								window.open(GO.settings.modules.files.url+'download.php?id='+file.id);
							}

						}
					}else if(href!='#')
					{
						if(href.substr(0,6)=='callto')
							document.location.href=href;
						else
							window.open(href);
					}
				}
			}
		}		
	},
	
	load : function(id, reload)
	{
		if(this.collapsed){

			//link_id is needed for editHandlers
			this.collapsedLinkId=this.link_id=id;
		}else if(this.link_id!=id || reload)
		{
			this.loadParams[this.idParam]=this.link_id=id;
			
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

	setTitle : function(title){
		if(typeof(this.title)!='undefined'){
			GO.DisplayPanel.superclass.setTitle.call(this, title);
		}else if(this.ownerCt)
		{
			//we are in a window
			this.ownerCt.setTitle(title);
		}
	},
	
	reload : function(){
		if(this.data.id)
			this.load(this.data.id, true);
	},
	
	editHandler : function(){
		
	}
});