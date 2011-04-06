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

GO.DisplayPanel=function(config){
	config = config || {};

	this.templateConfig = {};
	this.collapsibleSections = {};

	GO.DisplayPanel.superclass.constructor.call(this, config);

	this.addEvents({bodyclick:true,afterbodyclick:true});
}

Ext.extend(GO.DisplayPanel, Ext.Panel,{
	link_id: 0,
	link_type : 0,
	
	newMenuButton : false,
	
	template : '',
	
	templateConfig : {},
	
	loadParams : {},
	
	idParam : '',
	
	loadUrl : '',
	
	data : {},
	
	saveHandlerAdded : false,

	noFileBrowser : false,

	collapsibleSections : {},

	hiddenSections : [],

	expandListenObject : false,

	editGoDialogId : false,

	loading:false,
	
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

		tbar.push(this.newMenuButton);
		
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
					this.body.print({title:this.getTitle()});
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

		this.templateConfig.panel=this;

		/*
		 * id is the dom id of the element that needs to hidden or showed
		 * dataKey is a reference name for the data that needs to be fetched from
		 * the server. The hidden sections will be sent as an array of dataKeys.
		 * The server can use this array to check if particular data needs be
		 * returned to the client.
		 */
		
		this.templateConfig.collapsibleSectionHeader = function(title, id, dataKey){
			this.panel.collapsibleSections[id]=dataKey;
			return '<div class="collapsible-display-panel-header"><div style="float:left">'+title+'</div><div class="x-tool x-tool-toggle" style="float:right;margin:0px;padding:0px;cursor:pointer" id="toggle-'+id+'">&nbsp;</div></div>';
		}
		
		this.xtemplate = new Ext.XTemplate(this.template, this.templateConfig);
		this.xtemplate.compile();
		
		GO.DisplayPanel.superclass.initComponent.call(this);

		if(!this.expandListenObject){
			this.expandListenObject=this;
		}

		this.expandListenObject.on('expand', function(){
			if(this.collapsedLinkId){
				this.load(this.collapsedLinkId, true);
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

		if(this.editGoDialogId){
			GO.dialogListeners.add(this.editGoDialogId,{
				scope:this,
				save:this.onSave
			});
		}
	},
	
	getLinkName : function(){
		return this.data.name;
	},
	
	onSave : function(panel, saved_id)
	{
		/*if(saved_id > 0 && this.data.id == saved_id)
		{
			this.reload();
		}*/
		if(saved_id > 0 && (this.link_id == saved_id || this.link_id==0)){
			this.load(saved_id, true);
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
		if(this.body)
			this.body.update("");		

		this.data={};
		this.link_id=this.collapsedLinkId=0;
		var tbar = this.getTopToolbar();
		if(tbar)
			tbar.setDisabled(true);
	},

	getState : function(){
		return Ext.apply(GO.DisplayPanel.superclass.getState.call(this) || {}, {hiddenSections:this.hiddenSections});
	},
	
	setData : function(data)
	{
		//this.body.removeAllListeners();
		
		data.link_type=this.link_type;
		data.panelId=this.getId();
		this.data=data;
		
		var tbar = this.getTopToolbar();
		if(tbar)
			tbar.setDisabled(false);

		if(this.editButton)
			this.editButton.setDisabled(!data.write_permission);
		
		if(this.newMenuButton){
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
		}
		
		if(this.fileBrowseButton)
		{
			this.fileBrowseButton.setDisabled(data.files_folder_id<1);
		}
		
		this.xtemplate.overwrite(this.body, data);

		for(var id in this.collapsibleSections){
			if(this.hiddenSections.indexOf(this.collapsibleSections[id])>-1){
				this.toggleSection(id, true);
			}
		}

		//
		
		//this.body.on('click', this.onBodyClick, this);
	},

	toggleSection : function(toggleId, collapse){

		var el = Ext.get(toggleId);
		var toggleBtn = Ext.get('toggle-'+toggleId);

		if(!toggleBtn)
			return false;
		
		var saveState=false;
		if(typeof(collapse)=='undefined'){
			collapse = !toggleBtn.hasClass('go-tool-toggle-collapsed');// toggleBtn.dom.innerHTML=='-';
			saveState=true;
		}

		
		if(collapse){
			//data not loaded yet			

			if(this.hiddenSections.indexOf(this.collapsibleSections[toggleId])==-1)
				this.hiddenSections.push(this.collapsibleSections[toggleId]);
		}else
		{
			var index = this.hiddenSections.indexOf(this.collapsibleSections[toggleId]);
			if(index>-1)
				this.hiddenSections.splice(index,1);
		}

		if(!el && !collapse){
			this.reload();
		}else
		{
			if(el)
				el.setDisplayed(!collapse);

			if(collapse){
				toggleBtn.addClass('go-tool-toggle-collapsed');
			}else
			{
				toggleBtn.removeClass('go-tool-toggle-collapsed');
			}
			//dom.innerHTML = collapse ? '+' : '-';
		}
		if(saveState)
			this.saveState();
	},

	/*collapsibleSectionHeader : function(title, id, dataKey){

		this.collapsibleSections[id]=dataKey;

		return '<div class="collapsible-display-panel-header">'+title+'<div class="x-tool x-tool-toggle" style="float:right;cursor:pointer" id="toggle-'+id+'" title="'+title+'">&nbsp;</div></div>';
	},*/

	
	onBodyClick :  function(e, target){

		this.fireEvent('bodyclick', this, target, e);

		if(target.id.substring(0,6)=='toggle'){
			var toggleId = target.id.substring(7,target.id.length);

			this.toggleSection(toggleId);
		}

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
					var index = href.substr(pos+6, href.length);		
					var link = this.data.links[index];			
					if(link.link_type=='folder')
					{
						GO.linkBrowser.show({link_id: link.parent_link_id,link_type: link.parent_link_type,folder_id: link.id});
					}else
					{
						GO.linkHandlers[link.link_type].call(this, link.id, {data: link});
					}
					e.preventDefault();
					return;
				}

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
							//GO.files.showFilePropertiesDialog(record.get('id'));
							GO.files.openFile(record);
						}else
						{
							window.open(GO.settings.modules.files.url+'download.php?id='+file.id);
						}

					}
					e.preventDefault();
					return;
				}

				if(href.indexOf('#browselinks')>-1){

					if(!GO.linkBrowser){
						GO.linkBrowser = new GO.LinkBrowser();
					}
					GO.linkBrowser.show({link_id: this.data.id,link_type: this.link_type,folder_id: "0"});
					GO.linkBrowser.on('hide', this.reload, this,{single:true});
					e.preventDefault();

					return;
				}

				if(href.indexOf('#browsefiles')>-1){

					GO.files.openFolder(this.data.files_folder_id);
					GO.files.fileBrowserWin.on('hide', this.reload, this, {single:true});
					e.preventDefault();

					return;
				}

				this.fireEvent('afterbodyclick', this, target, e, href);


				/*if(href!='#')
				{
					if(href.substr(0,6)=='callto')
						document.location.href=href;
					else
						window.open(href);
				}*/
				
			}
		}		
	},
	
	load : function(id, reload)
	{
		if(this.expandListenObject.collapsed || !this.rendered){
			//link_id is needed for editHandlers
			this.collapsedLinkId=this.link_id=id;
		}else if(this.link_id!=id || reload)
		{
			this.loading=true;

			this.loadParams[this.idParam]=this.link_id=id;
			this.loadParams['hidden_sections']=Ext.encode(this.hiddenSections)
			
			this.body.mask(GO.lang.waitMsgLoad);
			Ext.Ajax.request({
				url: this.loadUrl,
				params:this.loadParams,
				callback: function(options, success, response)
				{
					this.loading=false;
					this.body.unmask();
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{
						var responseParams = Ext.decode(response.responseText);
						if(!responseParams.success){
							Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
						}else
						{
							this.setData(responseParams.data);
							if(!reload)
								this.body.scrollTo('top', 0);
						}
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

	getTitle : function(){
		if(typeof(this.title)!='undefined'){
			return this.title;
		}else if(this.ownerCt)
		{
			//we are in a window
			this.ownerCt.title;
		}
		return false;
	},
	
	reload : function(){
		if(this.data.id)
			this.load(this.data.id, true);
	},
	
	editHandler : function(){
		
	}
});