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
	
	if(!config.root)
	{
		config.root='root';
	}

	this.treePanel = new Ext.tree.TreePanel({
		region:'west',
		title:GO.lang.locations,
		layout:'fit',
    split:true,
		autoScroll:true,
		width: 200,
		
		animate:true,
		loader: new Ext.tree.TreeLoader(
		{
			dataUrl:GO.settings.modules.files.url+'json.php',
			baseParams:{task: 'tree'},
			preloadChildren:true
		}),
		collapsed: config.treeCollapsed,
		containerScroll: true,
		rootVisible: config.treeRootVisible,
		collapsible:true,
		ddAppendOnly: true,
		containerScroll: true,
		ddGroup : 'FilesDD',
		enableDD:true,
		selModel:new Ext.tree.MultiSelectionModel()
		
	});
	


	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		text: GO.lang.folders,
		draggable:false,
		id: config.root,
		iconCls : 'folder-default'
	});
	this.treePanel.setRootNode(this.rootNode);
	
	
	this.treePanel.on('click', function(node)	{
		this.setPath(node.id, true);
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
		  record.data['path']=e.data.node.id;
		  var selections = [record];		  
		}
		
		this.paste('cut', e.target.id, selections);
	},
	this);
	
	
	
	this.gridStore = new GO.data.JsonStore({
		url: GO.settings.modules.files.url+'json.php',
		baseParams: {'task': 'grid'},
		root: 'results',
		totalProperty: 'total',
		id: 'path',
		fields:['path','name','type', 'size', 'mtime', 'grid_display', 'extension', 'timestamp', 'thumb_url'],
		remoteSort:true
	});
	
	this.gridStore.on('load', this.onStoreLoad, this);
	
	if(config.filesFilter)
	{		
		this.setFilesFilter(config.filesFilter);
	}	
	
	this.gridPanel = new GO.grid.GridPanel( {
			layout:'fit',
			split:true,
			store: this.gridStore,
			deleteConfig: {
				scope:this,
				success:function(){
				  var activeNode = this.treePanel.getNodeById(this.path);
				  if(activeNode)
				  {
				  	activeNode.reload();
				  }
				}
			},
			columns:[{
					header:GO.lang['strName'],
					dataIndex: 'grid_display',
					sortable:true
				},{
					header:GO.lang.strType,
					dataIndex: 'type',
					sortable:true
				},{
					header:GO.lang.strSize,
					dataIndex: 'size',
					sortable:true
					
				},{
					header:GO.lang.strMtime,
					dataIndex: 'mtime',
					sortable:true
				}],						
			view:new  Ext.grid.GridView({
				autoFill:true,
				forceFit:true
			}),
			sm: new Ext.grid.RowSelectionModel(),
			loadMask: true,
			enableDragDrop: true,
			ddGroup : 'FilesDD'		
		});
		
	
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
						this.setPath(this.parentPath);
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
					handler: function(){ this.showUploadDialog(); },
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
	
	config['layout']='border';
	config['tbar']=new Ext.Toolbar({		
			cls:'go-head-tb',
			items: [
				
				this.newButton,
				this.uploadButton,	
				new Ext.Toolbar.Separator(),
				this.upButton,		
				new Ext.Toolbar.Separator(),				
				this.copyButton,
				this.cutButton,
				this.pasteButton,
				new Ext.Toolbar.Separator(),				
				this.deleteButton,
				'-',
				this.thumbsToggle = new Ext.Button({
					text: GO.files.lang.thumbnails,
	        enableToggle: true,
	        toggleHandler: function(item, pressed){
	        	if(pressed)
				  	{
				  		this.thumbsPanel.setStore(this.gridStore);	        		    		
				  		this.cardPanel.getLayout().setActiveItem(1);
				  	}else
				  	{
				  		this.thumbsPanel.setStore(false);	        		
				  		this.cardPanel.getLayout().setActiveItem(0);
				  	}        	
	        	
	        	var thumbs = this.gridStore.reader.jsonData.thumbs=='1';
	        	if(thumbs!=pressed)
		        	Ext.Ajax.request({
		        		url:GO.settings.modules.files.url+'action.php',
		        		params: {
		        			task:'set_view',
		        			path: this.path,
		        			thumbs: pressed ? '1' : '0'
		        		}
	        	});
	        	
	        	//this.getActiveGridStore().load();
	        },
	        scope:this
				})				
				
			]});

	this.thumbsPanel = new GO.files.ThumbsPanel();
	
	this.thumbsPanel.view.on('dblclick', function(view, index, node, e){
		
		var record = view.store.getAt(index);
		
		if(record.data.extension=='folder')
		{
			this.setPath(record.data.path, true);	
		}else
		{
			if(this.fileClickHandler)
			{
				this.fileClickHandler.call(this.scope);
			}else
			{
				GO.files.openFile(record.data.path, this.getActiveGridStore());
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
		this.contextTreePath = node.id;
		
		var coords = e.getXY();
		this.filesContextMenu.showAt(coords, records);
		
	}, this);
	
	this.thumbsPanel.on('drop', function(targetPath, dragRecords){
		this.paste('cut', targetPath, dragRecords);
	}, this);
	
	this.cardPanel =new Ext.Panel({
			region:'center',
			layout:'card',
			activeItem:0,
			deferredRender:false,
		  border:false,
		  anchor:'100% 100%',
			items:[this.gridPanel, this.thumbsPanel]		
		});
			
	config['items']=[this.locationPanel, this.treePanel,this.cardPanel];
	
	GO.files.FileBrowser.superclass.constructor.call(this, config);
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
		this.setWritePermission(store.reader.jsonData.write_permission);
		
		this.thumbsToggle.toggle(store.reader.jsonData.thumbs=='1');
		
		
		
		var lastIndexOf = this.path.lastIndexOf('/');
		this.parentPath = this.path.substr(0, lastIndexOf);
		if(this.parentPath=='users' || this.path==this.rootNode.id)
		{		
			this.upButton.setDisabled(true);			
		}else
		{
			this.upButton.setDisabled(false);
		}		
	},
	
	onShow : function(){
		
		GO.files.FileBrowser.superclass.onShow.call(this);
		
		if(!this.loaded)
		{
			this.loadFiles();
		}
				
	},
	
	setFileClickHandler : function(handler, scope)
	{
		this.fileClickHandler = handler;
		this.scope = scope;
	},
	
	setFilesFilter : function(filter)
	{
		this.gridStore.baseParams['files_filter']=filter;
		//this.thumbsStore.baseParams['files_filter']=filter;
	},

	
	afterRender : function(){
		
		GO.files.FileBrowser.superclass.afterRender.call(this);
		
		if(!this.loadDelayed)
		{			
			this.loadFiles();
		}
		
		
	},
	
	loadFiles : function(path){
		
		this.buildNewMenu();		
		this.setRootNode(this.root, path);
		this.loaded=true;
	},
	
	setRootPath : function(rootPath, loadNow)
	{
		this.root = rootPath;
		this.loaded=false;		
		
		if(loadNow)
		{
			this.loadFiles();	
		}
	},
	
	setRootNode : function(id, path)
	{
		
		this.rootNode.id=id;
		this.rootNode.attributes.id=id;
		//delete this.rootNode.children;
		//this.rootNode.expanded=false;
		//this.rootNode.childrenRendered=false;
		
		if(id=='root' && !path)
		{
			this.rootNode.on('load', function(node)
			{
				if(node.childNodes[0])
				{
					var firstAccountNode = node.childNodes[0];
					this.setPath(firstAccountNode.id);
				}				
			}, this, {single:true});
		}else
		{
			if(!path)
				path = id;
				
			this.setPath(path, true, true);
		}
		
		//this.setPath(id);
	
		this.rootNode.reload();
		
	},
	
	
	showFolderPropertiesDialog : function (path)
	{
		if(!this.folderPropertiesDialog)
		{
			this.folderPropertiesDialog = new GO.files.FolderPropertiesDialog();
				this.folderPropertiesDialog.on('rename', function(){
				this.reload();
			}, this);
		}
		this.folderPropertiesDialog.show(path);
	},
	
	showFilePropertiesDialog : function(path)
	{
		this.filePropertiesDialog = new GO.files.FilePropertiesDialog();
		this.filePropertiesDialog.on('rename', function(){
			this.getActiveGridStore().load();	
		}, this);
		this.filePropertiesDialog.show(path);
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
					this.createFileFromTemplate(template_id, filename);
				},this);
		}else
		{
			var store = this.getActiveGridStore();
			
			store.baseParams['template_id']=template_id;
			store.baseParams['template_name']=filename;
			
			store.load({
				callback: function(){
					
					if(store.reader.jsonData.new_path)
					{
						GO.files.openFile(store.reader.jsonData.new_path);
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
		for(var i=0;i<records.length;i++)
		{
			compress_sources.push(records[i].data.path);
		}		
		
		if(compress_sources.length)
		{		
			if(!filename || filename == '')
			{
				Ext.Msg.prompt(GO.files.lang.enterName, GO.files.lang.pleaseEnterNameArchive, 
					function(id, filename){ 
						this.onCompress(records, filename);
					},this);
			}else
			{
				var store = this.getActiveGridStore();
				
				store.baseParams['compress_sources']=Ext.encode(compress_sources);
				store.baseParams['archive_name']=filename;
				
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
			}		
		}
	},
	
	getSelectedTreeRecords : function(){
		 var sm = this.treePanel.getSelectionModel();		 
		 var nodes = sm.getSelectedNodes();
		 
		 var records=[];
		 
		 for(var i=0;i<nodes.length;i++)
		 {
		 	records.push({data: {path: nodes[i].id, extension:'folder'}});
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
		
		/*if(this.cardPanel.getLayout().activeItem.id=='files-grid')
		{
			return this.gridStore;
		}else
		{
			return this.thumbsStore;
		}*/
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
			this.paste(this.pasteMode, this.path, this.pasteSelections);
	},
	
	onDelete : function(clickedAt){		
				
		if(clickedAt=='tree')
		{
			var records = this.getSelectedTreeRecords();
			GO.deleteItems({
				url:GO.settings.modules.files.url+'action.php',
				params:{
					task:'delete',
					path: records[0]	
				},
				count:1,
				callback:function(responseParams){
					
					if(responseParams.success)
					{
						var treeNode = this.treePanel.getNodeById(this.contextTreePath);
						if(treeNode)
						{
							if(this.path.indexOf(this.contextTreePath)>-1 || (treeNode.parentNode && treeNode.parentNode.id==this.path))
							{
								this.setPath(treeNode.parentNode.id);
							}
							treeNode.remove();
						}
					}else
					{
						Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
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
						var treeNode = this.treePanel.getNodeById(this.path);
						if(treeNode)
						{
							while(treeNode.attributes.notreloadable)
							{
								treeNode=treeNode.parentNode;
							}
							treeNode.reload();
						}		
					},
					scope:this
				});
			}else
			{
				this.thumbsPanel.deleteSelected({
					callback:function(){				
						var treeNode = this.treePanel.getNodeById(this.path);
						if(treeNode)
						{
							while(treeNode.attributes.notreloadable)
							{
								treeNode=treeNode.parentNode;
							}
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
				this.paste('cut', dropRecord.data.path, data.selections);
	    }
		}else
		{
		  return false;
		}
	},
	
	onGridRowContextMenu : function(grid, rowIndex, e) {
			e.stopEvent();				
		
			var selModel = grid.getSelectionModel();
			
      if(selModel.isSelected(rowIndex) !== true) {
          selModel.clearSelections();
          selModel.selectRow(rowIndex);
      }
			var selections = selModel.getSelections();	
			
			var coords = e.getXY();
			this.filesContextMenu.showAt(coords, selections);
	},
	
	paste : function(pasteMode, destination, records)
	{
		var paste_sources = Array();
		var folderSelected = false;
		for(var i=0;i<records.length;i++)
		{
			paste_sources.push(records[i].data['path']);
			if(records[i].data['extension']=='folder')
			{
				folderSelected = true;
			}
		}
		
		var params = {			
			task : 'paste',
			paste_sources : Ext.encode(paste_sources),
			paste_destination : destination,
			paste_mode : pasteMode,
			path : this.path			
		};
		
		this.sendOverwrite(params);
		
	},
	
	showUploadDialog : function(){
	  
	  if(!this.uploadDialog)
	  {
		  this.uploadFile = new GO.form.UploadFile({
	    			inputName : 'attachments',
	    			addText: GO.lang.smallUpload
	    		});

    	this.upForm = new Ext.form.FormPanel({
    			fileUpload:true,
    			waitMsgTarget:true,
    			items: [this.uploadFile, new Ext.Button({
    				text:GO.lang.largeUpload,
    				handler: function(){
    					if(!deployJava.isWebStartInstalled('1.6.0'))
							{		
    						Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
							}else
							{ 					
	    					GO.util.popup({
	    						url: GO.settings.modules.files.url+'jupload/index.php?path='+encodeURIComponent(this.path), 
	    						width : 640,
	    						height: 500,
	    						target: 'jupload'
	    					});
	    							
	    					this.uploadDialog.hide();
	    					//for refreshing by popup
	    					GO.currentFilesStore = this.getActiveGridStore();
							}
    				},
    				scope:this
    			})   				
    			],
    			cls: 'go-form-panel'
    		});
				
			this.uploadDialog = new Ext.Window({
					title: GO.lang.uploadFiles,
					layout:'fit',					
					modal:false,
					height:300,
					width:300,		
					items: this.upForm,
					buttons:[
						{
							text:GO.files.lang.startTransfer,
							handler: this.uploadHandler, 
							scope: this
						},
						{
							text:GO.lang['cmdClose'],
							handler: function(){this.uploadDialog.hide()}, 
							scope: this
						}]
				});
	  }
	  this.uploadDialog.show();
	  
	},
	uploadHandler : function(){
		this.upForm.container.mask(GO.lang.waitMsgUpload,'x-mask-loading');
		this.upForm.form.submit({
			url:GO.settings.modules.files.url+'action.php',
			params: {
			  task: 'upload',
			  path: this.path
			},
			success:function(form, action){
				this.uploadFile.clearQueue();						
				this.uploadDialog.hide();		
				this.sendOverwrite({
					path : this.path,
					task: 'overwrite'
				});
				
				this.upForm.container.unmask();
				
			},
			failure:function(form, action)
			{
				this.upForm.container.unmask();
			},
			scope: this
		});
			
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
					
					delete params.paste_sources;
					delete params.paste_destination;
					
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{				
						
						var responseParams = Ext.decode(response.responseText);
						
						if(!responseParams.success && !responseParams.file_exists)
						{
							Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
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
								this.getActiveGridStore().reload();
								this.treePanel.getRootNode().reload();						
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
			this.newFolderNameField = new Ext.form.TextField({	
						id:'new-folder-input',	                	
	          fieldLabel: GO.lang['strName'],
	          name: 'name',
	          value: 'New folder',
	          allowBlank:false,
	          anchor:'100%'   
	      });
			this.newFolderFormPanel = new Ext.form.FormPanel({
					url: GO.settings.modules.files.url+'action.php',
					defaultType: 'textfield',
					labelWidth:75,
					cls:'go-form-panel',waitMsgTarget:true,
					items:this.newFolderNameField			
				});
			
			
			this.newFolderWindow = new Ext.Window({
				title:GO.files.lang.addFolder,
				width:500,
				autoHeight:true,
				modal:false,
				closeAction:'hide',
				items: this.newFolderFormPanel,
				focus:function(){
					Ext.getCmp('new-folder-input').focus(true);
				},
				scope:this,
				buttons: [
				{
					text: GO.lang['cmdOk'],
					handler: function(){	
						
						this.newFolderFormPanel.form.submit({
										
							url:GO.settings.modules.files.url+'action.php',
							params: {'task' : 'new_folder', 'path': this.path},
							waitMsg:GO.lang['waitMsgSave'],
							success:function(form, action){
								this.getActiveGridStore().reload();	
								
								//problem if folder didn't have a subfolder yet
								//fixed by reloading parent
								
								var activeNode = this.treePanel.getNodeById(this.path);
								if(activeNode)
								{
								  var callback = function(){
								    var newNode = this.treePanel.getNodeById(this.path);
								  	if(newNode)
								  	{
								  	  newNode.expand();
								  	}									    
								  }
								  var callbackDelegate = callback.createDelegate(this);
								  
								  if(activeNode.parentNode)
								  {
										activeNode.parentNode.reload(callbackDelegate);
								  }else
								  {
								  	activeNode.reload(callbackDelegate);
								  }			
								}	
															
								this.newFolderWindow.hide();
							},
					
							failure: function(form, action) {
								var error = '';
								if(action.failureType=='client')
								{
									error = GO.lang['strErrorsInForm'];
								}else
								{
									error = action.result.feedback;
								}
								
								Ext.MessageBox.alert(GO.lang['strError'], error);
							},
							scope:this
							
						});
						
					},
					scope:this
				},
				{
					text: GO.lang['cmdClose'],
					handler: function(){this.newFolderWindow.hide();},
					scope: this
				}]				
			});
			
		
		}else
		{
			this.newFolderNameField.reset();
		}
		this.newFolderWindow.show();
		
		
	},
	
	onGridDoubleClick : function(grid, rowClicked, e){
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();
		
		if(record.data.extension=='folder')
		{
			this.setPath(record.data.path, true);	
		}else
		{
			if(this.fileClickHandler)
			{
				this.fileClickHandler.call(this.scope);
			}else
			{
				GO.files.openFile(record.data.path, this.getActiveGridStore());
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
	},
	
	setPath : function(path, expand, createPath)
	{
		this.path = path;
		
		//this.gridStore.baseParams['path']=this.thumbsStore.baseParams['path']=path;
		this.gridStore.baseParams['path']=path;
		//this.gridStore.baseParams['create_path']=this.thumbsStore.baseParams['create_path']=createPath;
		this.gridStore.baseParams['create_path']=createPath;
		
		this.getActiveGridStore().load({
			callback:function(){
				delete this.gridStore.baseParams['create_path'];
				
				if(expand)
				{
					var activeNode = this.treePanel.getNodeById(path);
					if(activeNode)
					{
						activeNode.expand();			
					}
				}	
			},
			scope:this
		});	
		
		this.locationTextField.setValue(this.path);			
	},
	
	reload : function()
	{
		this.getActiveStore.load();	
		var activeNode = this.treePanel.getNodeById(this.path);
		if(activeNode)
		{
			activeNode.reload();			
		}	
	},
	
	
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
			this.showFolderPropertiesDialog(record.data.path);
		}else
		{
			this.showFilePropertiesDialog(record.data.path);			
		}
	}
});





GO.files.openFile = function(path, store)
{
	var extension = GO.util.getFileExtension(path);	
	
	switch(extension)
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
		
		var index = 0;
		var images = Array();
		if(store)
		{
			for (var i = 0; i < store.data.items.length;  i++)
			{
				var r = store.data.items[i].data;
				var ext = GO.util.getFileExtension(r.path);
				
				if(ext=='jpg' || ext=='png' || ext=='gif' || ext=='bmp' || ext=='jpeg')
				{
					images.push({name: r.name, src: GO.settings.modules.files.url+'download.php?mode=download&path='+r.path})
				}
				if(r.path==path)
				{
					index=images.length-1;
				}
			}
		}
		
		this.imageViewer.show(images, index);
			
		break;
		
		case 'doc':
		case 'odt':
		case 'ods':
		case 'xls':
		case 'ppt':
		case 'odp':
		case 'txt':				
			if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
			{
				if(!GO.files.noJavaNotified && !deployJava.isWebStartInstalled('1.6.0'))
				{
					GO.files.noJavaNotified=true;
					Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);					
					window.location.href=GO.settings.modules.files.url+'download.php?mode=download&path='+path;
				}else
				{
					window.location.href=GO.settings.modules.gota.url+'jnlp.php?path='+path;
				}
			}else
			{
				window.location.href=GO.settings.modules.files.url+'download.php?mode=download&path='+path;
			}
		break;
		
		default:
			window.location.href=GO.settings.modules.files.url+'download.php?mode=download&path='+path;
		break;	
	}	
}


GO.files.openFolder = function(path)
{
	if(!GO.files.fileBrowser)
	{	
		GO.files.fileBrowser=new GO.files.FileBrowser({
				border:false,
				treeRootVisible:true,
				treeCollapsed:true
			});			        			
		GO.files.fileBrowserWin = new Ext.Window({
		
			title: GO.files.lang.fileBrowser,
			height:500,
			width:700,
			layout:'fit',
			border:false,
			maximizable:true,
			collapsible:true,
			closeAction:'hide',
			items: GO.files.fileBrowser,
			buttons:[
				{
					text: GO.lang['cmdClose'],				        						
					handler: function(){
						GO.files.fileBrowserWin.hide();
					},
					scope:this
				}				
			]							        				
		});		
	}
	GO.files.fileBrowser.setRootPath(path, true);
	GO.files.fileBrowserWin.show();
	
}


GO.linkHandlers[6]=function(id, record){
	GO.files.openFile(record.data.description);
}


GO.moduleManager.addModule('files', GO.files.FileBrowser, {
	title : GO.files.lang.files,
	iconCls : 'go-tab-icon-files'
});

