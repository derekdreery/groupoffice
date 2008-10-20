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
	
	
	if(config.local_path)
		this.local_path= config.local_path;
	else
		this.local_path='';

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
			baseParams:{task: 'tree', local_path: this.local_path},
			preloadChildren:true
		}),
		collapsed: config.treeCollapsed,
		containerScroll: true,
		rootVisible: config.treeRootVisible,
		collapsible:true,
		ddAppendOnly: true,
		containerScroll: true,
		ddGroup : 'FilesDD',
		enableDD:true
		
	});
	


	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		text: GO.lang.folders,
		draggable:false,
		id: config.root,
		iconCls : 'folder-default'
	});
	this.treePanel.setRootNode(this.rootNode);
	
	
	/*if(!config.treeRootVisible)
	{
		this.rootNode.on('load', function(node)
		{
			if(node.childNodes[0])
			{
				var firstAccountNode = node.childNodes[0];
				this.setPath(firstAccountNode.id);
			}				
		}, this, {single:true});
	}*/
	
	
	
	this.treePanel.on('click', function(node)	{
		this.setPath(node.id, true);
	}, this);

	this.treePanel.on('contextmenu', function(node, e){
		e.stopEvent();
		this.contextTreePath = node.id;
		
		var coords = e.getXY();
		this.filesContextMenu.showAt([coords[0], coords[1]], 'folder', 'tree');
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
		baseParams: {'task': 'grid', local_path: this.local_path},
		root: 'results',
		totalProperty: 'total',
		id: 'path',
		fields:['path','name','type', 'size', 'mtime', 'grid_display', 'extension'],
		remoteSort:true
	});
	
	if(config.filesFilter)
	{		
		this.setFilesFilter(config.filesFilter);
	}
	
	
	this.gridStore.on('load', function(store){
		this.setWritePermission(store.reader.jsonData.write_permission);
		
		var lastIndexOf = this.path.lastIndexOf('/');
		this.parentPath = this.path.substr(0, lastIndexOf);
		if(this.parentPath=='users' || this.path==this.rootNode.id)
		{		
			this.upButton.setDisabled(true);			
		}else
		{
			this.upButton.setDisabled(false);
		}
		
	}, this);
	
	
	this.gridPanel = new GO.grid.GridPanel( {
			region:'center',
			layout:'fit',
			split:true,
			//paging:true,
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
	
	this.filesContextMenu.on('properties', function(menu, clickedAt){
		
		if(clickedAt=='tree')
		{			
			this.showFolderPropertiesDialog(this.contextTreePath);
		}else
		{
			this.showGridPropertiesDialog();
		}	
	}, this);
	
	this.filesContextMenu.on('cut', function(menu, clickedAt){		
		this.onCutCopy('cut', clickedAt);
	}, this);

	this.filesContextMenu.on('copy', function(menu, clickedAt){		
		this.onCutCopy('copy', clickedAt);
	}, this);
	
	this.filesContextMenu.on('delete', function(menu, clickedAt){		
		this.onDelete(clickedAt);
	}, this);
	
	this.filesContextMenu.on('compress', function(menu, clickedAt){		
		this.onCompress(clickedAt);
	}, this);
	
	this.filesContextMenu.on('decompress', function(menu, clickedAt){		
		this.onDecompress(clickedAt);
	}, this);
	
	
	this.filesContextMenu.on('download', function(){
		var selectionModel = this.gridPanel.getSelectionModel();
		var record = selectionModel.getSelected();
		
		window.location.href=GO.settings.modules.files.url+'download.php?mode=download&path='+record.data.path;
	}, this);
	
	this.filesContextMenu.on('gota', function(){
		var selectionModel = this.gridPanel.getSelectionModel();
		var record = selectionModel.getSelected();
		
		if(!deployJava.isWebStartInstalled('1.6.0'))
		{
			Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
		}else
		{		
			window.location.href=GO.settings.modules.gota.url+'jnlp.php?path='+record.data.path;
		}
	}, this);
		
	
	this.gridPanel.on('rowcontextmenu', this.onGridRowContextMenu, this);
	
	
	this.newMenu = new Ext.menu.Menu({
					id: 'new-menu',
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
						this.onCutCopy('cut', 'grid');
					},
					scope: this
				});
	this.copyButton = new Ext.Button({
					iconCls: 'btn-copy',
					text: GO.lang.copy,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.onCutCopy('copy', 'grid');
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
				this.deleteButton
				
			]});
	config['items']=[this.locationPanel, this.treePanel,this.gridPanel];
//config['items']=[this.treePanel,this.gridPanel];
	
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
			this.folderPropertiesDialog = new GO.files.FolderPropertiesDialog({local_path:this.local_path});
				this.folderPropertiesDialog.on('rename', function(){
				this.reload();
			}, this);
		}
		this.folderPropertiesDialog.show(path);
	},
	
	showFilePropertiesDialog : function(path)
	{
		this.filePropertiesDialog = new GO.files.FilePropertiesDialog({local_path:this.local_path});
		this.filePropertiesDialog.on('rename', function(){
			this.gridStore.load();	
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
			
			this.gridStore.baseParams['template_id']=template_id;
			this.gridStore.baseParams['template_name']=filename;
			
			this.gridStore.load({
				callback: function(){
					
					if(this.gridStore.reader.jsonData.new_path)
					{
						GO.files.openFile(this.gridStore.reader.jsonData.new_path);
					}
				},
				scope: this
			});
			delete this.gridStore.baseParams['template_id'];
			delete this.gridStore.baseParams['template_name'];
		}		
	},
	
	onDecompress : function(clickedAt){
		

		var decompress_sources = this.gridPanel.selModel.selections.keys;
		
		if(decompress_sources.length)
		{		
			this.gridStore.baseParams['decompress_sources']=Ext.encode(decompress_sources);
			
			this.gridStore.load({
				callback: function(){
					
					if(!this.gridStore.reader.jsonData.decompress_success)
					{
						Ext.Msg.alert(GO.lang['strError'], this.gridStore.reader.jsonData.decompress_feedback);
					}
				},
				scope: this
			});
			delete this.gridStore.baseParams['decompress_sources'];
		}		
	},
	
	onCompress : function(clickedAt, filename)
	{
		var compress_sources = this.gridPanel.selModel.selections.keys;
		
		if(compress_sources.length)
		{		
			if(!filename || filename == '')
			{
				Ext.Msg.prompt(GO.files.lang.enterName, GO.files.lang.pleaseEnterNameArchive, 
					function(id, filename){ 
						this.onCompress(clickedAt, filename);
					},this);
			}else
			{
				
				this.gridStore.baseParams['compress_sources']=Ext.encode(compress_sources);
				this.gridStore.baseParams['archive_name']=filename;
				
				this.gridStore.load({
					callback: function(){
						
						if(!this.gridStore.reader.jsonData.compress_success)
						{
							Ext.Msg.alert(GO.lang['strError'], this.gridStore.reader.jsonData.compress_feedback);
						}
					},
					scope: this
				});
				delete this.gridStore.baseParams['archive_name'];
			}		
		}
	},
	
	onCutCopy : function(pasteMode, clickedAt){
		if(clickedAt=='tree')
		{
			var record = {};
		  record.data={};
		  record.data['extension']='folder';
		  record.data['path']=this.contextTreePath;
		  this.pasteSelections= [record];
		}else
		{
			var selModel = this.gridPanel.getSelectionModel();
			this.pasteSelections=selModel.getSelections();			
		}
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
			GO.deleteItems({
				url:GO.settings.modules.files.url+'action.php',
				params:{
					task:'delete',
					local_path: this.local_path,
					path: this.contextTreePath	
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
			
			var extension = '';
			var selections = selModel.getSelections();
			if(selections.length=='1')
			{				
    		extension = selections[0].data.extension;				
			}
			
			var coords = e.getXY();
			this.filesContextMenu.showAt([coords[0], coords[1]], extension, 'grid');
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
	    					var local_path = this.local_path ? 'true' : false;
	    					
	    					GO.util.popup({
	    						url: GO.settings.modules.files.url+'jupload/index.php?path='+encodeURIComponent(this.path)+'&local_path='+local_path, 
	    						width : 640,
	    						height: 500,
	    						target: 'jupload'
	    					});
	    					
	    					this.uploadDialog.hide();
	    					//for refreshing by popup
	    					GO.currentFilesStore = this.gridStore;
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
			  path: this.path,
			  local_path: this.local_path
			},
			success:function(form, action){
				this.uploadFile.clearQueue();						
				this.uploadDialog.hide();		
				this.sendOverwrite({
					path : this.path,
					task: 'overwrite',
					local_path: this.local_path
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
															this.gridStore.reload();						
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
								this.gridStore.reload();
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
							params: {'task' : 'new_folder', 'path': this.path, local_path: this.local_path},
							waitMsg:GO.lang['waitMsgSave'],
							success:function(form, action){
								this.gridStore.reload();	
								
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
				GO.files.openFile(record.data.path);
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
		this.gridStore.baseParams['path']=path;
		this.gridStore.baseParams['create_path']=createPath;
		this.gridStore.load({
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
		this.gridStore.load();	
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
			
			if(selections[0].data.extension=='folder')
			{
				this.showFolderPropertiesDialog(selections[0].data.path);
			}else
			{
				this.showFilePropertiesDialog(selections[0].data.path);
				
			}
		}
	}
});





GO.files.openFile = function(path)
{
	var lastIndex = path.lastIndexOf('.');
	var extension = '';
	if(lastIndex)
	{
		extension = path.substr(lastIndex+1);
	}
	
	
	switch(extension)
	{
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

