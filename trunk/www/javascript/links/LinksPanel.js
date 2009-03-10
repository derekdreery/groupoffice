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
 
GO.grid.LinksPanel = function(config){
	
	var linksDialog;
	
	//Ext.apply(this, config);
	
	if(!config)
	{
		config={};
	}
	
	if(!this.link_id)
	{
		this.link_id=0;
	}
	
	if(!this.link_type)
	{
		this.link_type=0;
	}
	
	if(!this.folder_id)
	{
		this.folder_id=0;
	}

	this.linksDialog = new GO.dialog.LinksDialog({linksStore: config['store']});
	this.linksDialog.on('link', function(){this.linksGrid.store.reload();}, this);
	
	this.linksTree = new GO.LinksTree({
		region:'west',
		split:true,
		title:GO.lang.folders
	});
	
	this.linksTree.on('click', function(node)	{
		this.setFolder(node.id.substr(10));
	}, this);
	
	this.linksTree.on('contextmenu', function(node, e){
		e.stopEvent();
		var folder_id = node.id.substr(10);
		
		if(folder_id!='')
		{
			var coords = e.getXY();
			this.linksContextMenu.showAt([coords[0], coords[1]], ['folder:'+folder_id], 'folder');	
		}		
	}, this);
	
	this.linksTree.on('beforenodedrop', function(e){
		
		if(!this.write_permission)
		{
			return false;
		}
		
		var target = {
			folder_id: e.target.id.substr(10),
			link_id: this.link_id,
			link_type: this.link_type
		};
		
		var selections = [];		
		if(e.data.selections)
		{
			//dropped from grid
		  for(var i=0;i<e.data.selections.length;i++)
			{
				if(e.data.selections[i].data.link_and_type.substr(0,6)=='folder')
				{					
					var id = e.data.selections[i].data.link_and_type.substr(7);
					var movedNode = this.linksTree.getNodeById('lt-folder-'+id);
					var targetNode = this.linksTree.getNodeById('lt-folder-'+target.folder_id);
					targetNode.appendChild(movedNode);
				}
				selections.push(e.data.selections[i].data.link_and_type);
			}
		}else
		{
			//dropped from tree		  
		  var selections = ['folder:'+e.data.node.id.substr(10)];
		}
		
		this.moveSelections(selections, target);
		
	},
	this);
	
	
	this.linksGrid = new GO.grid.LinksGrid({
		region:'center',
		deleteConfig: {
				scope:this,
				success:function(){
				  var activeNode = this.linksTree.getNodeById('lt-folder-'+this.folder_id);
				  if(activeNode)
				  {
				  	activeNode.reload();
				  }else
				  {
				  	this.linksTree.getRootNode().reload();
				  }
				}
			}
	});
	
	this.linksGrid.on('folderDrop', function(grid, selections, dropRecord){
		var target = {
			folder_id: dropRecord.data.id,
			link_id: this.link_id,
			link_type: this.link_type
		};
		var selectedKeys=[]
		for(var i=0;i<selections.length;i++)
		{
			selectedKeys.push(selections[i].data.link_and_type);
		}
		
		this.moveSelections(selectedKeys, target);
		
	}, this);

	
	this.linksGrid.on('rowcontextmenu', function(grid, rowIndex,e){
		
		var type = '';
		var selections = selModel.getSelections();
		if(selections.length=='1')
		{				
  		type = selections[0].data.link_type;				
		}
		
		var coords = e.getXY();
		this.linksContextMenu.showAt([coords[0], coords[1]], selModel.selections.keys, type);
	}, this)
	
	
	this.linksGrid.store.on('load', function(){
		
		this.setWritePermission(this.linksGrid.store.reader.jsonData.write_permission);
		
	}, this);
	
	this.folderWindow = new GO.LinkFolderWindow();
	this.folderWindow.on('save', function(){
		this.linksGrid.store.reload();
		
		var activeNode = this.linksTree.getNodeById('lt-folder-'+this.folder_id);
		
		if(activeNode)
		{
			//delete preloaded children otherwise no request will be sent
			delete activeNode.attributes.children;
			activeNode.reload();
		}else
		{
			this.linksTree.rootNode.reload();
		}

	}, this);	
	
	config.items=[this.linksTree, this.linksGrid];		
	
	this.linksContextMenu = new GO.LinksContextMenu();
	
	this.linksContextMenu.on('properties', function(menu,selections){
		
		var colonPos = selections[0].indexOf(':');
		var folder_id = selections[0].substr(colonPos+1);		
		
		this.folderWindow.show({
			folder_id: folder_id
		});
	
	}, this);
	
	this.linksContextMenu.on('delete', function(menu,selections){
		
	}, this);
	
	this.linksContextMenu.on('unlink', function(menu,selections){
		this.linksGrid.store.baseParams['unlinks']=Ext.encode(selections);
		this.linksGrid.store.reload();
		delete this.linksGrid.store.baseParams['unlinks'];
	}, this);
	
	config['layout']='border';
	config.border=false;
	
	//was required to show the search field in the tbar
	config.hideMode='offsets';
		
	config['tbar'] = [
			this.linkButton = new Ext.Button({
				iconCls: 'btn-link',
				text: GO.lang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: function(){				
					this.linksDialog.show();					
				},
				scope: this
				
			}),this.unlinkButton = new Ext.Button({
				iconCls: 'btn-unlink',
				text: GO.lang['cmdUnlink'],
				cls: 'x-btn-text-icon',
				handler: function() {
					
					var unlinks = [];
	
					var selectionModel = this.linksGrid.getSelectionModel();
					var records = selectionModel.getSelections();
					
					if(records.length>0)
					{
						this.linksGrid.store.baseParams['unlinks']=Ext.encode(selectionModel.selections.keys);
						this.linksGrid.store.reload();
						delete this.linksGrid.store.baseParams['unlinks'];
					}
				},
				scope: this
			}),this.newFolderButton = new Ext.Button({
				id: 'unlink',
				iconCls: 'btn-add',
				text: GO.lang.newFolder,
				cls: 'x-btn-text-icon',
				handler: function() {
					
					this.folderWindow.show({
						link_id : this.link_id,
						link_type : this.link_type,
						parent_id : this.folder_id
					});
				},
				scope: this
			}),this.deleteButton = new Ext.Button({
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.linksGrid.deleteSelected();
				},
				scope: this
			})
		];
		
	if(GO.links && GO.links.LinkDescriptionsGrid)
	{
		config.tbar.push('-');
		
		config.tbar.push({
			text: GO.links.lang.linkDescriptions,
			scope:this,
			iconCls:'btn-settings',
			handler:function(){
				if(!this.settingsWindow)
				{
					this.settingsWindow = new GO.Window({
						height:400,
						width:300,
						layout:'fit',
						title:GO.links.lang.linkDescriptions,
						closeAction:'hide',
						items:new GO.links.LinkDescriptionsGrid(),
						buttons:[
							{
								text: GO.lang['cmdClose'],				        						
								handler: function(){
									this.settingsWindow.hide();
								},
								scope:this
							}]
					});
				}
				this.settingsWindow.show();				
			}
		});
	}
		
		
	this.linksGrid.on("rowdblclick", this.rowDoulbleClicked, this);
	
	
  GO.grid.LinksPanel.superclass.constructor.call(this, config);
	
}

Ext.extend(GO.grid.LinksPanel, Ext.Panel, {
	
	afterRender : function(){
		
		GO.grid.LinksPanel.superclass.afterRender.call(this);
		
		this.on("rowdblclick", this.rowDoulbleClicked, this);
		
		if(this.isVisible())
		{
			this.onShow();
		}
	},
	
	
	moveSelections : function(selections, target)
	{
		Ext.Ajax.request({
			url: BaseHref+'action.php',
			params: {
				'task' : 'move_links',
				selections : Ext.encode(selections),
				target : Ext.encode(target)
				},
			callback: function(options, success, response){				
				
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					var responseParams = Ext.decode(response.responseText);
					
					if(responseParams.moved_links)
					{
						for(var i=0;i<responseParams.moved_links.length;i++)
						{
							var record = this.linksGrid.store.getById(responseParams.moved_links[i]);
							if(record)
							{
								this.linksGrid.store.remove(record);
							}
						}
					}					
				}
			},
			scope:this								
			
		});
		
		
	},
	
	
	rowDoulbleClicked : function(grid, rowClicked, e) {
			
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();
		
		if(record.data.link_type=='folder')
		{
			this.setFolder(record.data.id);
			
		}else	if(GO.linkHandlers[record.data.link_type])
		{
			GO.linkHandlers[record.data.link_type].call(this, record.data.id);
		}else
		{
			Ext.Msg.alert(GO.lang['strError'], 'No handler definded for link type: '+record.data.link_type);
		}
	},
	
	
	onShow : function(){
		GO.grid.LinksPanel.superclass.onShow.call(this);
		
		if(!this.loaded && this.link_id>0)
		{
			this.linksGrid.store.load();
			var rootNode = this.linksTree.getRootNode();
			
			if(rootNode.isExpanded()){
				rootNode.reload();
			}else
			{
				rootNode.expand();
			}
			this.loaded=true;
		}
	},
	
	setWritePermission : function(writePermission){
		this.linkButton.setDisabled(!writePermission);
		this.unlinkButton.setDisabled(!writePermission);
		this.newFolderButton.setDisabled(!writePermission);
		this.deleteButton.setDisabled(!writePermission);		
		
		this.write_permission=writePermission;
		this.linksGrid.write_permission=writePermission;
	},
	
	setFolder : function(folder_id)
	{
		var activeNode = this.linksTree.getNodeById('lt-folder-'+folder_id);
		if(activeNode)
		{
			activeNode.expand();			
		}
		
		this.linksDialog.folder_id=folder_id;
		
		this.folder_id=folder_id;
		this.linksGrid.store.baseParams["folder_id"]=folder_id;
		this.linksGrid.store.load();
	},
	
	loadLinks : function (link_id, link_type, folder_id)
	{
		if(link_id>0)
		{
			this.setDisabled(false);
		}else
		{
			this.setDisabled(true);
		}
		
		if(this.link_id!=link_id || this.link_type!=link_type)
		{	
			this.link_id=this.linksGrid.store.baseParams["link_id"]=link_id;
			this.link_type=this.linksGrid.store.baseParams["link_type"]=link_type;			
			this.linksGrid.store.baseParams["folder_id"]=folder_id;
			
			this.linksTree.loadLinks(link_id, link_type);

			this.linksDialog.setSingleLink(this.link_id, this.link_type);
			this.loaded=false;
		}
	}

});

