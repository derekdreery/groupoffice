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


Ext.namespace("GO.files");

GO.files.FileRecord = Ext.data.Record.create([
				 {name: 'type_id', type: 'string'},
         {name: 'id', type: 'string'},
         {name: 'name', type: 'string'},
         {name: 'type', type: 'string'},
         {name: 'mtime'},
         {name: 'extension'},
         {name: 'timestamp'},
         {name: 'thumb_url', type: 'string'} 
    ]);

/*
 * 
 * if config.treeRootVisible == false (default) then the tree will load automatically!
 * 
 */

GO.files.FileBrowser = function(config){
	
	if(!config)
	{
		config = {};
	}
	if(!config.id)
		config.id=Ext.id();
	
	this.treeLoader = new Ext.tree.TreeLoader(
		{
			dataUrl:GO.settings.modules.files.url+'json.php',
			baseParams:{task: 'tree',root_folder_id:0, expand_folder_id:0},
			preloadChildren:true
		});

	this.treeLoader.on('beforeload', function(){
		var el =this.treePanel.getEl();
		if(el){
			el.mask(GO.lang.waitMsgLoad);
		}
	}, this);
	this.treeLoader.on('load', function(){
		var el =this.treePanel.getEl();
		if(el){
			el.unmask();
		}
	}, this);

	this.treePanel = new Ext.tree.TreePanel({
		region:'west',
		//title:GO.lang.locations,
		layout:'fit',
    split:true,
		autoScroll:true,
		width: 200,
		animate:true,
		loader: this.treeLoader,
		collapsed: config.treeCollapsed,
		rootVisible:false,
		containerScroll: true,
		collapsible:true,
		collapseMode:'mini',
		header:false,
		ddAppendOnly: true,
		ddGroup : 'FilesDD',
		enableDD:true,
		selModel:new Ext.tree.MultiSelectionModel()		
	});


	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		text: '',
		draggable:false,
		id: 'root',
		iconCls : 'folder-default'
	});
	
	//select the first inbox to be displayed in the messages grid
	this.rootNode.on('load', function(node)
	{	
		//var grid_id = !this.treePanel.rootVisible && node.childNodes[0] ? node.childNodes[0].id : node.id;
		if(!this.folder_id)
		{
			this.folder_id=node.childNodes[0].id;
		}
		this.setFolderID(this.folder_id);
		
	}, this);
	
	this.treePanel.setRootNode(this.rootNode);
	
	this.treePanel.on('click', function(node)	{
		this.setFolderID(node.id, true);
	}, this);

	this.treePanel.on('contextmenu', function(node, e){
		e.stopEvent();
		
		var selModel = this.treePanel.getSelectionModel();
		
		if(!selModel.isSelected(node))
		{
			selModel.clearSelections();
			selModel.select(node);
		}
		
		var records = this.getSelectedTreeRecords();
		
		var coords = e.getXY();
		this.filesContextMenu.showAt(coords, records, 'tree');
	}, this);
	
	this.treePanel.on('beforenodedrop', function(e){
		
		if(e.data.selections)
		{
		  var selections = e.data.selections;
		}else
		{
		  var record = {};
		  record.data={};
		  record.data['extension']='folder';
		  record.data['id']=e.data.node.id;
		  record.data['type_id']='d:'+e.data.node.id;
		  var selections = [record];		  
		}
		
		this.paste('cut', e.target.id, selections);
	},
	this);
	
	this.treePanel.on('nodedragover', function(dragEvent){
		
		if(!dragEvent.dropNode)
		{
				
			//comes from grid, don't allow it to paste it into a child
			for(var i=0;i<dragEvent.data.selections.length;i++)
			{
				if(dragEvent.data.selections[i].data.extension=='folder')
				{
					var moveid = dragEvent.data.selections[i].data.id;
					var parentid = dragEvent.data.selections[i].data.parent_id;
					var targetid = dragEvent.target.id;
					
					if(moveid==targetid || parentid==targetid)
					{
						return false;
					}
				
					var dragNode = this.treePanel.getNodeById(moveid);
					if(dragNode.parentNode.id == targetid || dragEvent.target.isAncestor(dragNode))
					{
						return false;
					}
					return true;
				}
			}			
		}else
		{
			var parentId = this.treePanel.getNodeById(dragEvent.dropNode.id).parentNode.id;
			if(parentId == dragEvent.target.id)
			{
				return false
			}
			return true;
		}
	}, this);


	var fields ={
		fields:['type_id', 'id','name','type', 'size', 'mtime', 'extension', 'timestamp', 'thumb_url','path','acl_id'],
		columns:[{
					id:'name',
					header:GO.lang['strName'],
					dataIndex: 'name',
					renderer:function(v, meta, r){
						var cls = r.get('acl_id')>0 ? 'folder-shared' : 'filetype filetype-'+r.get('extension');
						return '<div class="go-grid-icon '+cls+'">'+v+'</div>';
					}
				},{
					header:GO.lang.strType,
					dataIndex: 'type',
					sortable:false,
					hidden:true,
					width:100
				},{
					header:GO.lang.strSize,
					dataIndex: 'size',
					renderer: function(v){
						return  v=='-' ? v : Ext.util.Format.fileSize(v);
					},
					hidden:true,
					width:100
				},{
					header:GO.lang.strMtime,
					dataIndex: 'mtime',
					width:120
				}]
	};

	if(GO.customfields)
	{
		GO.customfields.addColumns(6, fields);
	}
	
	this.gridStore = new GO.data.JsonStore({
		url: GO.settings.modules.files.url+'json.php',
		baseParams: {'task': 'grid'},
		root: 'results',
		totalProperty: 'total',
		id: 'type_id',
		fields:fields.fields,
		remoteSort:true
	});
	
	this.gridStore.on('load', this.onStoreLoad, this);
	
	if(config.filesFilter)
	{		
		this.setFilesFilter(config.filesFilter);
	}

	var cm =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
	
	this.gridPanel = new GO.grid.GridPanel( {
			layout:'fit',
			id:config.id+'-fs-grid',
			split:true,
			store: this.gridStore,
			paging:true,
			deleteConfig: {
				scope:this,
				success:function(){
				  var activeNode = this.treePanel.getNodeById(this.folder_id);
				  if(activeNode)
				  {
				  	activeNode.reload();
				  }
				}
			},
			cm:cm,
			autoExpandColumn:'name',
			sm: new Ext.grid.RowSelectionModel(),
			loadMask: true,
			enableDragDrop: true,
			ddGroup : 'FilesDD'
		});
		
	this.gridPanel.on('delayedrowselect', function (grid, rowIndex, r){
		this.fireEvent('fileselected', this, r);
	}, this);
		
	
	this.gridPanel.on('render', function(){
		//enable row sorting
		var DDtarget = new Ext.dd.DropTarget(this.gridPanel.getView().mainBody, 
		{
			ddGroup : 'FilesDD',
			copy:false,
			notifyOver : this.onGridNotifyOver,
			notifyDrop : this.onGridNotifyDrop.createDelegate(this)
		});
	}, this);	
	
	this.gridPanel.on('rowdblclick', this.onGridDoubleClick, this);
	

	
	this.filesContextMenu = new GO.files.FilesContextMenu();
	
	this.filesContextMenu.on('properties', function(menu, records){		
		this.showPropertiesDialog(records[0]);
	}, this);
	
	this.filesContextMenu.on('cut', function(menu, records){		
		this.onCutCopy('cut', records);
	}, this);

	this.filesContextMenu.on('copy', function(menu, records){		
		this.onCutCopy('copy', records);
	}, this);
	
	this.filesContextMenu.on('delete', function(menu, records, clickedAt){		
		this.onDelete(clickedAt);
	}, this);
	
	this.filesContextMenu.on('compress', function(menu, records, clickedAt){		
		this.onCompress(records);
	}, this);
	
	this.filesContextMenu.on('decompress', function(menu, records){		
		this.onDecompress(records);
	}, this);
	

	this.gridPanel.on('rowcontextmenu', this.onGridRowContextMenu, this);
	
	
	this.newMenu = new Ext.menu.Menu({
					//id: 'new-menu',
	        items: []
			});
	
	this.newButton = new Ext.Button({
			text:GO.lang.cmdNew,
      iconCls: 'btn-add',
      menu: this.newMenu  
		});
	
	this.locationTextField = new Ext.form.TextField({
	   	fieldLabel:GO.lang.strLocation,
	   	name:'files-location',
	   	anchor:'100%'
	  });
	
	this.locationPanel = new Ext.Panel({
	  region:'north',
	  layout:'form',
	  border:false,	  
	  baseCls:'x-plain',
	  height:32,
	  labelWidth:75,
	  plain:true,
	  cls:'go-files-location-panel',
	  items:this.locationTextField	  
	});
	
	this.upButton = new Ext.Button({
					iconCls: 'btn-up',
					text: GO.lang.up,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.setFolderID(this.parentID);
					},
					scope: this,
					disabled:true
				});
	
	this.pasteButton = new Ext.Button({
					iconCls: 'btn-paste',
					text: GO.lang.paste,
					cls: 'x-btn-text-icon',
					handler: this.onPaste,
					scope: this,
					disabled:true
				});
				
	this.uploadButton = new Ext.Button({
					iconCls: 'btn-upload',
					text: GO.lang.upload,
					cls: 'x-btn-text-icon',
					handler: function(){ 
						if(!this.uploadDialog)
					  {			
							this.uploadDialog = new GO.files.UploadDialog();
							this.uploadDialog.on('upload', function(){
								this.sendOverwrite({
									folder_id : this.folder_id,
									task: 'overwrite'
								});				
							},this);
					  }
					  GO.currentFilesStore=this.gridStore;
					  this.uploadDialog.show(this.folder_id);	
					},
					scope: this
				});
	this.deleteButton = new Ext.Button({
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.onDelete('grid');
		},
		scope: this
	});
	
	this.cutButton= new Ext.Button({
		iconCls: 'btn-cut',
		text: GO.lang.cut,
		cls: 'x-btn-text-icon',
		handler: function(){
			var records = this.getSelectedGridRecords();
			this.onCutCopy('cut', records);
		},
		scope: this
	});
	this.copyButton = new Ext.Button({
		iconCls: 'btn-copy',
		text: GO.lang.copy,
		cls: 'x-btn-text-icon',
		handler: function(){
			var records = this.getSelectedGridRecords();
			this.onCutCopy('copy', records);
		},
		scope: this
	});
	this.emptyListButton = new Ext.Button({
		iconCls: 'btn-refresh',
		text: GO.files.lang.emptyList,
		cls: 'x-btn-text-icon',
		hidden:true,
		handler: function(){
			this.gridStore.baseParams.empty_new_files=true;
			this.gridStore.load();
			delete this.gridStore.baseParams.empty_new_files;
		},
		scope: this
	});
				
	var tbar = [];

	tbar.push(this.newButton);
	
	if(!config.hideActionButtons)
	{		
		tbar.push(this.uploadButton);
		/*tbar.push({

			text: 'SWF upload',
			handler:function(){
				var window = new Ext.Window({
					title: 'SWF Upload (Max Filesize 1 MB)',
					height:400,
					width:600,
					layout:'fit',
					items:[
			
								new Ext.ux.SwfUploadPanel({
								post_params : {
									"task" : 'upload'
								},
								upload_url : GO.settings.modules.files.url+'swfupload.php',
								labelWidth: 110,
								file_size_limit:"100MB"
								})
					]
						
				});

				window.show();
			}
		});*/
		tbar.push('-');
	}
	tbar.push(this.upButton);
	tbar.push({            
      iconCls: "btn-refresh",
      text:GO.lang.cmdRefresh,      
      handler: function(){
      	this.refresh(true);
      },
      scope:this
  });
  
  if(!config.hideActionButtons)
	{
		tbar.push('-');			
		tbar.push(this.copyButton);
		tbar.push(this.cutButton);
		tbar.push(this.pasteButton);
		tbar.push('-');
		tbar.push(this.deleteButton);
		tbar.push('-');
	}				
	
	tbar.push(this.thumbsToggle = new Ext.Button({
			text: GO.files.lang.thumbnails,
			iconCls: 'btn-thumbnails',
      enableToggle: true,
      toggleHandler: function(item, pressed){
      	if(pressed)
		  	{
		  		//this.thumbsPanel.setStore(this.gridStore);
		  		this.cardPanel.getLayout().setActiveItem(1);
		  	}else
		  	{
		  		//this.thumbsPanel.setStore(false);
		  		this.cardPanel.getLayout().setActiveItem(0);
		  	}        	
      	
      	var thumbs = this.gridStore.reader.jsonData.thumbs=='1';
      	if(thumbs!=pressed)
        	Ext.Ajax.request({
        		url:GO.settings.modules.files.url+'action.php',
        		params: {
        			task:'set_view',
        			id: this.folder_id,
        			thumbs: pressed ? '1' : '0'
        		}
      	});
      },
      scope:this
		}));
		
	if(!config.hideActionButtons)
	{
		tbar.push('-');
		tbar.push(this.emptyListButton);
			
	}

	config.keys=[{
					ctrl:true,
					key: Ext.EventObject.C,
					fn:function(){
						var records = this.getSelectedGridRecords();
						this.onCutCopy('copy', records);
					},
					scope:this
			},{
					ctrl:true,
					key: Ext.EventObject.X,
					fn:function(){
						var records = this.getSelectedGridRecords();
						this.onCutCopy('cut', records);
					},
					scope:this
			},{
					ctrl:true,
					key: Ext.EventObject.V,
					fn:function(){
						this.onPaste();
					},
					scope:this
			}];
	
	
	config['layout']='border';
	config['tbar']=new Ext.Toolbar({		
			cls:'go-head-tb',
			items: tbar});

	this.thumbsPanel = new GO.files.ThumbsPanel({store:this.gridStore});
	
	this.thumbsPanel.view.on('click', function(view, index,node,e){
		var record = view.store.getAt(index);
		this.fireEvent('fileselected', this, record);
	}, this);
	
	this.thumbsPanel.view.on('dblclick', function(view, index, node, e){
		
		var record = view.store.getAt(index);
		
		this.fireEvent('filedblclicked', this, record);
		
		if(record.data.extension=='folder')
		{
			this.setFolderID(record.data.id, true);	
		}else
		{
			if(this.fileClickHandler)
			{
				this.fileClickHandler.call(this.scope, record);
			}else
			{
				GO.files.openFile(record, this.getActiveGridStore());
			}			
		}
	}, this);
	
	this.thumbsPanel.view.on('contextmenu', function(view, index, node, e){		 
		
		if(!view.isSelected(index))
		{
			view.clearSelections();			
			view.selectRange(index, index);			
		}
		var records = view.getSelectedRecords();
		
		e.stopEvent();
		this.contextTreeID = node.id;
		
		var coords = e.getXY();
		this.filesContextMenu.showAt(coords, records);
		
	}, this);
	
	this.thumbsPanel.on('drop', function(targetID, dragRecords){
		this.paste('cut', targetID, dragRecords);
	}, this);
	
	this.cardPanel =new Ext.Panel({
			region:'center',
			layout:'card',
			id:config.id+'-card-panel',
			activeItem:0,
			deferredRender:false,
		  border:false,
		  anchor:'100% 100%',
			items:[this.gridPanel, this.thumbsPanel]		
		});


	this.filePanel = new GO.files.FilePanel({
				region:'east',
				id:config.id+'-file-panel',
				//collapsed:true,
				width:450,
				collapseMode:'mini',
				collapsible:true,
				split:true
	});

	
			
	config['items']=[this.locationPanel, this.treePanel,this.cardPanel,this.filePanel];
	
	GO.files.FileBrowser.superclass.constructor.call(this, config);

	
	this.addEvents({
		fileselected : true,
		filedblclicked : true
	});

	this.on('fileselected',function(grid, r){
		if(r.data.extension!='folder'){
			this.filePanel.load(r.id.substr(2));
		}
	}, this);
}

Ext.extend(GO.files.FileBrowser, Ext.Panel,{
		
	fileClickHandler : false,
	scope : this,
	pasteSelections : Array(),
	/*
	 * cut or copy
	 */
	pasteMode : 'cut',


	
	onStoreLoad : function(store){

		if(!GO.util.empty(store.reader.jsonData.feedback))
		{
			alert(store.reader.jsonData.feedback);
		}

		this.setWritePermission(store.reader.jsonData.write_permission);
		
		this.thumbsToggle.toggle(store.reader.jsonData.thumbs=='1');
		
		if(this.folder_id=='new')
		{
			var num_files = store.reader.jsonData.num_files;
			var activeNode = this.treePanel.getNodeById('new');
			if(activeNode)
				activeNode.setText(GO.files.lang.newFiles + " (" + num_files + ")");
		}
		
		this.emptyListButton.setVisible(this.folder_id=='new' && num_files > 0);

		if(store.reader.jsonData.refreshed)
		{
			var activeNode = this.treePanel.getNodeById(this.folder_id);
			if(activeNode)
			{
				delete activeNode.attributes.children;
				activeNode.reload();
			}
		}
		
		this.parentID = store.reader.jsonData.parent_id;
		if(this.parentID==0 || !this.treePanel.getNodeById(this.parentID))
		{		
			this.upButton.setDisabled(true);			
		}else
		{
			this.upButton.setDisabled(false);
		}		
	},
	
	/*onShow : function(){
		
		GO.files.FileBrowser.superclass.onShow.call(this);
		
		if(!this.loaded)
		{
			this.loadFiles();
		}
				
	},*/
	
	setFileClickHandler : function(handler, scope)
	{
		this.fileClickHandler = handler;
		this.scope = scope;
	},
	
	setFilesFilter : function(filter)
	{
		this.gridStore.baseParams['files_filter']=filter;
	},

	
	afterRender : function(){		
		GO.files.FileBrowser.superclass.afterRender.call(this);

		GO.files.filePropertiesDialogListeners={
			scope:this,
			rename:function(dlg, folder_id){
				if(this.folder_id==folder_id)
				{
					this.getActiveGridStore().load();
				}
			}
		}

		GO.files.folderPropertiesDialogListeners={
			scope:this,
			rename:function(dlg, parent_id){
				if(parent_id==this.folder_id)
				{
					this.setFolderID(parent_id);
				}
				var node = this.treePanel.getNodeById(parent_id);
				if(node)
				{
					delete node.attributes.children;
					node.reload();
				}
			}
		}
		
		this.buildNewMenu();	
	},

	
	setRootID : function(rootID, folder_id)
	{
		if(this.treeLoader.baseParams.root_folder_id!=rootID || (folder_id>0 && this.folder_id!=folder_id)){
			this.folder_id=folder_id;
			this.treeLoader.baseParams.root_folder_id=rootID;
			this.treeLoader.baseParams.expand_folder_id=folder_id;
			this.rootNode.reload({
				callback:function(){
					delete this.treeLoader.baseParams.expand_folder_id;
				},
				scope:this
			});
		}
	},
	
	buildNewMenu : function(){		
	
		this.newMenu.removeAll();
		
		Ext.Ajax.request({
			url: GO.settings.modules.files.url+'json.php',
			params: {
				task: 'templates'
			},
			callback: function(options, success, response)
			{

				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					this.newMenu.add( {
								iconCls: 'btn-add-folder',
								text: GO.lang.folder,
								cls: 'x-btn-text-icon',
								handler: this.promptNewFolder,									
								scope: this
							});
					
					var responseParams = Ext.decode(response.responseText);
					
					if(responseParams.results.length)
					{
						this.newMenu.add('-');
						for(var i=0;i<responseParams.results.length;i++)
						{
							var template = responseParams.results[i];
							
							var menuItem = new Ext.menu.Item({
								iconCls:'filetype filetype-'+template.extension,
								text: template.name,
								template_id:template.id,
								handler: function(item){
									
									this.createFileFromTemplate(item.template_id);
								},
								scope:this	
							});
							
							this.newMenu.add(menuItem);						
						}						
					}
					
					if(GO.settings.modules.files.write_permission)
					{
						this.newMenu.add('-');
						
						this.newMenu.add({
							iconCls: 'btn-templates',
							text: GO.files.lang.manageTemplates,
							cls: 'x-btn-text-icon',
							handler: function(){
								if(!this.templatesWindow)
								{
									this.templatesWindow = new GO.files.TemplateWindow();
									this.templatesWindow.gridStore.on('datachanged', function(){
										if(!this.templatesWindow.firstLoad)
										{
											this.buildNewMenu();
										}
									}, this);
								}
								this.templatesWindow.show();
							},
							scope: this					
						});
					}
				}
			},
			scope: this		
		});		
	},
	
	createFileFromTemplate : function(template_id, filename){
		
		if(!filename || filename == '')
		{
			Ext.Msg.prompt(GO.files.lang.enterName, GO.files.lang.pleaseEnterName, 
				function(id, filename){ 
					if(id=='cancel')
						return false;
					else
						this.createFileFromTemplate(template_id, filename);
				},this);
		}else
		{
			var store = this.getActiveGridStore();
			
			store.baseParams['template_id']=template_id;
			store.baseParams['template_name']=filename;
			
			store.load({
				callback: function(){
					if(store.reader.jsonData.new_id)
					{
						var record = store.getById('f:'+store.reader.jsonData.new_id);
						GO.files.openFile(record);
					}
				},
				scope: this
			});
			delete store.baseParams['template_id'];
			delete store.baseParams['template_name'];
		}		
	},
	
	onDecompress : function(records){
		
		var decompress_sources = [];		
		for(var i=0;i<records.length;i++)
		{
			decompress_sources.push(records[i].data.path);
		}
				
		if(decompress_sources.length)
		{		
			var store = this.getActiveGridStore();
			store.baseParams['decompress_sources']=Ext.encode(decompress_sources);
			
			store.load({
				callback: function(){
					
					if(!store.reader.jsonData.decompress_success)
					{
						Ext.Msg.alert(GO.lang['strError'], store.reader.jsonData.decompress_feedback);
					}
				},
				scope: this
			});
			delete store.baseParams['decompress_sources'];
		}		
	},
	
	onCompress : function(records, filename)
	{		
		var compress_sources = [];
		var compress_id=0;
		if(records.length==1 && !records[0].data.path && records[0].data.id>0){
			compress_id=records[0].data.id;
		}else
		{
			for(var i=0;i<records.length;i++)
			{
				compress_sources.push(records[i].data.path);
			}
		}
			
		
		if(compress_sources.length || compress_id>0)
		{		
			if(!filename || filename == '')
			{
				Ext.Msg.prompt(GO.files.lang.enterName, GO.files.lang.pleaseEnterNameArchive, 
					function(id, filename){
						if(id=='ok'){
							this.onCompress(records, filename);
						}
					},this);
			}else
			{
				var store = this.getActiveGridStore();
				
				store.baseParams['compress_sources']=Ext.encode(compress_sources);
				store.baseParams['archive_name']=filename;
				store.baseParams['compress_id']=compress_id;
				
				store.load({
					callback: function(){
						
						if(!store.reader.jsonData.compress_success)
						{
							Ext.Msg.alert(GO.lang['strError'], store.reader.jsonData.compress_feedback);
						}
					},
					scope: this
				});
				delete store.baseParams['compress_sources'];
				delete store.baseParams['archive_name'];
				delete store.baseParams['compress_id'];
			}		
		}else
		{
			alert('You can\'t compress that folder');
		}
	},
	
	getSelectedTreeRecords : function(){
		 var sm = this.treePanel.getSelectionModel();		 
		 var nodes = sm.getSelectedNodes();
		 
		 var records=[];
		 
		 for(var i=0;i<nodes.length;i++)
		 {
		 	records.push({data: {
		 		type_id:'d:'+nodes[i].id, 
		 		id: nodes[i].id,
		 		extension:'folder'}});
		 }
		 return records;		
	},
	
	getSelectedGridRecords : function(){
		//detect grid on selModel. thumbs doesn't have that
		if(this.cardPanel.getLayout().activeItem.selModel)
		{
			var selModel = this.gridPanel.getSelectionModel();
			return selModel.getSelections();
		}else
		{
			return this.thumbsPanel.view.getSelectedRecords();
		}
	},
	
	getActiveGridStore : function(){		
		return this.gridStore;
	},
	
	onCutCopy : function(pasteMode, records){		
		this.pasteSelections=records;
		this.pasteMode=pasteMode;
		if(this.pasteSelections.length)
		{
			this.pasteButton.setDisabled(false);
		}
	},

	onPaste : function(){
			this.paste(this.pasteMode, this.folder_id, this.pasteSelections);
	},
	
	onDelete : function(clickedAt){		

		if(clickedAt=='tree')
		{
			var records = this.getSelectedTreeRecords();
			GO.deleteItems({
				url:GO.settings.modules.files.url+'action.php',
				params:{
					task:'delete',
					id: records[0].data.id	
				},
				count:1,
				callback:function(responseParams){
					
					if(responseParams.success)
					{
						var treeNode = this.treePanel.getNodeById(records[0].data.id);
						if(treeNode)
						{
							//parentNode is destroyed after remove so keep it for later use
							var parentNodeId = treeNode.parentNode.id;
							treeNode.remove();
							
							var activeTreenode = this.treePanel.getNodeById(this.folder_id);
							if(!activeTreenode){
								//current folder must have been removed. Let's go up.
								this.setFolderID(parentNodeId);
							}
						}
					}
				},
				scope:this			
			});
		}else
		{
			//detect grid on selModel. thumbs doesn't have that
			if(this.cardPanel.getLayout().activeItem.selModel)
			{
				this.gridPanel.deleteSelected({
					callback:function(){				
						var treeNode = this.treePanel.getNodeById(this.folder_id);
						if(treeNode)
						{
							delete treeNode.attributes.children;
							treeNode.reload();
						}		
					},
					scope:this
				});
			}else
			{
				this.thumbsPanel.deleteSelected({
					callback:function(){				
						var treeNode = this.treePanel.getNodeById(this.folder_id);
						if(treeNode)
						{
							delete treeNode.attributes.children;
							treeNode.reload();
						}		
					},
					scope:this
				});
			}			
		}
	},
	
	onGridNotifyOver : function(dd, e, data){
			var dragData = dd.getDragData(e);
			if(data.grid)
			{
  			var dropRecord = data.grid.store.data.items[dragData.rowIndex];
  			if(dropRecord)
    		{
  				if(dropRecord.data.extension=='folder')
  				{
  					for(var i=0;i<data.selections.length;i++)
  					{
  						if(data.selections[i].data.id==dropRecord.data.id)
  						{
  							return false;
  						}
  					}
  					return this.dropAllowed;
  				}
				}
			}
			return false;
	},

	onGridNotifyDrop : function(dd, e, data)
	{
	  if(data.grid)
		{
			var sm=data.grid.getSelectionModel();
			var rows=sm.getSelections();
			var dragData = dd.getDragData(e);
			
			var dropRecord = data.grid.store.data.items[dragData.rowIndex];
			
			if(dropRecord.data.extension=='folder')
	    {
	    	for(var i=0;i<data.selections.length;i++)
				{
					if(data.selections[i].data.id==dropRecord.data.id)
					{
						return false;
					}
				}
				this.paste('cut', dropRecord.data.id, data.selections);
	    }
		}else
		{
		  return false;
		}
	},
	
	onGridRowContextMenu : function(grid, rowIndex, e) {
			var selections = grid.getSelectionModel().getSelections();	
			
			var coords = e.getXY();
			this.filesContextMenu.showAt(coords, selections, 'grid');
	},
	
	paste : function(pasteMode, destination, records)
	{
		var paste_sources = Array();
		//var folderSelected = false;
		for(var i=0;i<records.length;i++)
		{
			paste_sources.push(records[i].data['type_id']);
			/*if(records[i].data['extension']=='folder')
			{
				folderSelected = true;
			}*/
		}
		
		var params = {			
			task : 'paste',
			paste_sources : Ext.encode(paste_sources),
			paste_destination : destination,
			paste_mode : pasteMode,
			id : this.folder_id			
		};
		
		this.sendOverwrite(params);
		
	},
	
	
	refresh : function(syncFilesystemWithDatabase){
	
		var activeNode = this.treePanel.getNodeById(this.folder_id);

		this.treeLoader.baseParams.expand_folder_id=this.folder_id;
		if(syncFilesystemWithDatabase)
			this.treeLoader.baseParams.sync_folder_id=this.folder_id;

		this.expandPath=false;
		if(activeNode)
		{
			this.expandPath = activeNode.getPath();
		}		
		this.rootNode.reload((function(){

				this.treeLoader.baseParams.expand_folder_id=0;

				if(this.expandPath)
					this.treePanel.expandPath(this.expandPath);
			}).createDelegate(this));

		if(syncFilesystemWithDatabase)
			delete this.treeLoader.baseParams.sync_folder_id;
	},
	
	sendOverwrite : function(params){
		
		if(!params.command)
		{
			params.command='ask';
		}		
		
		this.overwriteParams = params;
		
		Ext.Ajax.request({
				url: GO.settings.modules.files.url+'action.php',
				params:this.overwriteParams,
				callback: function(options, success, response){				
					
					var pasteSources = Ext.decode(this.overwriteParams.paste_sources);
					var pasteDestination = this.overwriteParams.paste_destination;
					
					
					//delete params.paste_sources;
					//delete params.paste_destination;
					
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{				
						
						var responseParams = Ext.decode(response.responseText);
						
						if(!responseParams.success && !responseParams.file_exists)
						{
							if(this.overwriteDialog)
							{
								this.overwriteDialog.hide();
							}	
							Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
							this.refresh();							
						}else
						{
						
							if(responseParams.file_exists)
							{
								if(!this.overwriteDialog)
								{
									
									this.overwriteDialog = new Ext.Window({				
										width:500,
										autoHeight:true,
										closeable:false,
										closeAction:'hide',
										plain:true,
										border: false,
										title:GO.lang.fileExists,
										modal:false,
										buttons: [
												{
													text: GO.lang.cmdYes,
													handler: function(){			
															this.overwriteParams.command='yes';		
															this.sendOverwrite(this.overwriteParams);												
														},
													scope: this
								   			},{
													text: GO.lang.cmdYesToAll,
													handler: function(){		
															this.overwriteParams.command='yestoall';					
															this.sendOverwrite(this.overwriteParams);												
														},
													scope: this
								   			},{
													text: GO.lang.cmdNo,
													handler: function(){
															this.overwriteParams.command='no';		
															this.sendOverwrite(this.overwriteParams);
														},
													scope: this
								   			},{
													text: GO.lang.cmdNoToAll,
													handler: function(){
															this.overwriteParams.command='notoall';		
															this.sendOverwrite(this.overwriteParams);
														},
													scope: this
								   			},{
													text: GO.lang.cmdCancel,
													handler: function(){
															this.getActiveGridStore().reload();						
															this.overwriteDialog.hide();
														},
													scope: this
								   			}]
										
									});
									this.overwriteDialog.render(Ext.getBody());
								}
								
								var t = new Ext.Template(GO.lang.overwriteFile);								
								t.overwrite(this.overwriteDialog.body, {file: responseParams.file_exists});								
								this.overwriteDialog.show();
							}else
							{
								//this.getActiveGridStore().reload();
								var store = this.getActiveGridStore();
								if(!pasteDestination || pasteDestination==this.folder_id)
								{
									store.reload();
								}else if(pasteSources)
								{
									for(var i=0;i<pasteSources.length;i++)
									{										
										var record = store.getById(pasteSources[i]);
										if(record)
										{
											store.reload();
											break;
										}										
									}
								}
								
								
								var destinationNode = this.treePanel.getNodeById(pasteDestination);
								if(destinationNode)
								{
									//delete destinationNode.attributes.children;
									destinationNode.attributes.children=[];
									destinationNode.attributes.childrenRendered=false;
									destinationNode.reload();
								}
								
								if(pasteSources)
								{
									for(var i=0;i<pasteSources.length;i++)
									{
										var node = this.treePanel.getNodeById(pasteSources[i]);
										if(node)
										{
											node.remove();
										}
									}	
								}
								
								if(this.overwriteDialog)
								{
									this.overwriteDialog.hide();
								}			
							}
						}						
					}											
				},
				scope: this		
			});
	
	},
	
	promptNewFolder : function(){
		
		if(!this.newFolderWindow)
		{
			this.newFolderWindow = new GO.files.NewFolderDialog();
			this.newFolderWindow.on('save', function(){
				this.getActiveGridStore().reload();	
								
					// problem if folder didn't have a subfolder yet
					// fixed by reloading parent					
					var activeNode = this.treePanel.getNodeById(this.folder_id);
					if(activeNode)
					{
					  // delete preloaded children otherwise no
						// request will be sent
					  delete activeNode.attributes.children;
					  activeNode.reload();	
					}	
			},this);
		}
		this.newFolderWindow.show(this.folder_id);
	},
	
	onGridDoubleClick : function(grid, rowClicked, e){
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();

		this.fireEvent('filedblclicked', this, record);
		
		if(record.data.extension=='folder')
		{
			this.setFolderID(record.data.id, true);	
		}else
		{
			if(this.fileClickHandler)
			{
				this.fileClickHandler.call(this.scope, record);
			}else
			{
				GO.files.openFile(record, this.getActiveGridStore());
			}			
		}
	},
	
	setWritePermission : function(writePermission)
	{
		this.newButton.setDisabled(!writePermission);		
		this.deleteButton.setDisabled(!writePermission);	
		this.uploadButton.setDisabled(!writePermission);	
		this.cutButton.setDisabled(!writePermission);	
		this.pasteButton.setDisabled(!writePermission || !this.pasteSelections.length);
					
		//this.filesContextMenu.deleteButton.setDisabled(!writePermission);
	},
	
	setFolderID : function(id, expand)
	{
		this.folder_id = id;
		
		//this.gridStore.baseParams['id']=this.thumbsStore.baseParams['id']=id;
		this.gridStore.baseParams['id']=id;
		
		this.getActiveGridStore().load({
			callback:function(){
				var activeNode = this.treePanel.getNodeById(id);
				if(activeNode)
				{
					this.treePanel.getSelectionModel().select(activeNode);
					var path = new String();				
					path = activeNode.getPath('text');
					path = path.substring(2);
					this.locationTextField.setValue(path);	
				}

				if(expand)
				{					
					if(activeNode)
					{
						activeNode.expand();
					}
				}

				this.focus();
			},
			scope:this
		});	
		
				
	},
	
	/*expandID : function(id){
		var folders = split('/', id);
		
		var curID = folders[0];
		
		var node = this.treePanel.getNodeById(curID);
		if(node)
		{
			node.expand();
		}
		
		for(var i=1;i<folders.length;i++)
		{
			curID = curid+'/'+folders[i];
			var node = this.treePanel.getNodeById(curID);
			if(node)
			{
				node.expand();
			}
		}		
	},*/

	
	showGridPropertiesDialog  : function(){		
		var selModel = this.gridPanel.getSelectionModel();
		var selections = selModel.getSelections();
		
		if(selections.length==0)
		{
			Ext.Msg.alert(GO.lang['strError'], GO.lang['noItemSelected']);
		}else if(selections.length>1)
		{
			Ext.Msg.alert(GO.lang['strError'], GO.files.lang.errorOneItem);
		}else
		{
			this.showPropertiesDialog(selections[0]);
			
		}
	},
	
	showPropertiesDialog : function(record)
	{
		if(record.data.extension=='folder')
		{
			GO.files.showFolderPropertiesDialog(record.data.id);
		}else
		{
			GO.files.showFilePropertiesDialog(record.data.id);
		}
	}
});


GO.files.showFilePropertiesDialog = function(file_id){

	if(!GO.files.filePropertiesDialog)
		GO.files.filePropertiesDialog = new GO.files.FilePropertiesDialog();

	if(GO.files.filePropertiesDialogListeners){
		GO.files.filePropertiesDialog.on(GO.files.filePropertiesDialogListeners);
		delete GO.files.filePropertiesDialogListeners;
	}

	GO.files.filePropertiesDialog.show(file_id);
}

GO.files.showFolderPropertiesDialog = function(folder_id){

	if(!GO.files.folderPropertiesDialog)
		GO.files.folderPropertiesDialog = new GO.files.FolderPropertiesDialog();

	if(GO.files.folderPropertiesDialogListeners){
		GO.files.folderPropertiesDialog.on(GO.files.folderPropertiesDialogListeners);
		delete GO.files.folderPropertiesDialogListeners;
	}

	GO.files.folderPropertiesDialog.show(folder_id);
}



GO.mainLayout.onReady(function(){
	
	if(GO.workflowLinkHandlers)
	{
		GO.workflowLinkHandlers[6]=function(id, process_file_id){
			GO.files.showFilePropertiesDialog(id+"", {loadParams:{process_file_id:process_file_id}});
			GO.files.filePropertiesDialog.tabPanel.setActiveTab(3);
		}
	}
});

GO.files.openFile = function(record, store)
{
	var index = record.data.id ? 'id' : 'path';

	switch(record.data.extension)
	{
		case 'bmp':
		case 'png':
		case 'gif':
		case 'jpg':
		case 'jpeg':
		
		if(!this.imageViewer)
		{
			this.imageViewer = new GO.files.ImageViewer({
				closeAction:'hide'
			});
		}
		
		var imgindex = 0;
		var images = Array();
		if(store)
		{
			for (var i = 0; i < store.data.items.length;  i++)
			{
				var r = store.data.items[i].data;
				
				if(r.extension=='jpg' || r.extension=='png' || r.extension=='gif' || r.extension=='bmp' || r.extension=='jpeg')
				{
					images.push({name: r.name, src: GO.settings.modules.files.url+'download.php?mode=download&'+index+'='+r[index]})
				}
				if(r[index]==record.data[index])
				{
					imgindex=images.length-1;
				}
			}
		}else
		{
			images.push({name: record.data['name'], src: GO.settings.modules.files.url+'download.php?mode=download&'+index+'='+record.data[index]})
		}
		
		this.imageViewer.show(images, imgindex);
			
		break;
		
		case 'dwg':
		case 'doc':
		case 'odt':
		case 'ods':
		case 'xls':
		case 'ppt':
		case 'odp':
		case 'txt':				
			if(index == 'id' && GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
			{
				if(!GO.files.noJavaNotified && !deployJava.isWebStartInstalled('1.6.0'))
				{
					GO.files.noJavaNotified=true;
					Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);					
					window.location.href=GO.settings.modules.files.url+'download.php?mode=download&'+index+'='+record.data[index];
				}else
				{
					window.location.href=GO.settings.modules.gota.url+'jnlp.php?'+index+'='+record.data['id'];
				}
			}else
			{
				window.location.href=GO.settings.modules.files.url+'download.php?mode=download&'+index+'='+record.data[index];
			}
		break;
		
		case 'eml':
			if(GO.mailings)
			{
				GO.linkHandlers[9].call(this, 0, {
						path: record.data.path
					});
				break;
			}
		
		default:
			window.location.href=GO.settings.modules.files.url+'download.php?mode=download&'+index+'='+record.data[index];
		break;	
	}	
}


GO.files.openFolder = function(id, folder_id)
{
	if(!GO.files.fileBrowser)
	{	
		GO.files.fileBrowser=new GO.files.FileBrowser({
				id:'popupfb',
				border:false,
				treeRootVisible:true/*,
				treeCollapsed:true*/
			});			        			
		GO.files.fileBrowserWin = new GO.Window({
		
			title: GO.files.lang.fileBrowser,
			height:500,
			width:900,
			layout:'fit',
			border:false,
			maximizable:true,
			collapsible:true,
			closeAction:'hide',
			items: GO.files.fileBrowser/*,
			buttons:[
				{
					text: GO.lang['cmdClose'],				        						
					handler: function(){
						GO.files.fileBrowserWin.hide();
					},
					scope:this
				}				
			]*/
		});		
	}
	GO.files.fileBrowser.setRootID(id, folder_id);
	GO.files.fileBrowserWin.show();
	
	return GO.files.fileBrowser;
}

GO.files.createSelectFileBrowser = function(){
	if(!GO.selectFileBrowser)
	{
		GO.selectFileBrowser= new GO.files.FileBrowser({
			border:false,
			treeCollapsed:false
		});

		GO.selectFileBrowserWindow = new Ext.Window({
			title: GO.lang.strSelectFiles,
			height:500,
			width:750,
			modal:true,
			layout:'fit',
			border:false,
			collapsible:true,
			maximizable:true,
			closeAction:'hide',
			items: GO.selectFileBrowser,
			buttons:[
				{
					text: GO.lang.cmdOk,
					handler: function(){
						var records = GO.selectFileBrowser.getSelectedGridRecords();
						GO.selectFileBrowser.fileClickHandler.call(GO.selectFileBrowser.scope, records[0]);
					},
					scope: this
				},{
					text: GO.lang.cmdClose,
					handler: function(){
						GO.selectFileBrowserWindow.hide();
					},
					scope:this
				}
			]

		});
	}
}


GO.linkHandlers[6]=function(id, record){
	GO.files.showFilePropertiesDialog(id+"");
}
GO.linkPreviewPanels[6]=function(config){
	config = config || {};
	return new GO.files.FilePanel(config);
}





GO.moduleManager.addModule('files', GO.files.FileBrowser, {
	title : GO.files.lang.files,
	iconCls : 'go-tab-icon-files'
});
