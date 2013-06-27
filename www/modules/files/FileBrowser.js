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

//GO.files.FileRecord = Ext.data.Record.create([
//{
//	name: 'type_id',
//	type: 'string'
//},
//{
//	name: 'id',
//	type: 'string'
//},
//{
//	name: 'name',
//	type: 'string'
//},
//{
//	name: 'type',
//	type: 'string'
//},
//{
//	name: 'mtime'
//},
//{
//	name: 'extension'
//},
//{
//	name: 'timestamp'
//},
//{
//	name: 'thumb_url',
//	type: 'string'
//}
//]);

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

	this.westPanel = {
		region: 'west',
		layout: 'border',
		width: 200,
		split: true,
		id: 'fs-tree-bookmarks-panel-'+config.id,
		items : [
			this.treePanel = new GO.files.TreePanel({
				region:'center',
				split:true,
				width: 200,
				collapsed: config.treeCollapsed,
				collapsible:true,
				collapseMode:'mini',
				header:false,
				ddAppendOnly: true,
				ddGroup : 'FilesDD',
				enableDD:true
			}),
			this.bookmarksGrid = new GO.files.BookmarksGrid({
				region: 'south'
			})
		]
	};
	
	//select the first inbox to be displayed in the messages grid
	this.treePanel.getRootNode().on('load', function(node)
	{
		//var grid_id = !this.treePanel.rootVisible && node.childNodes[0] ? node.childNodes[0].id : node.id;
		if(!this.folder_id)
		{
			this.folder_id=node.childNodes[0].id;
		}
		this.setFolderID(this.folder_id);
	}, this, {single:true});
	
	
	this.treePanel.getLoader().on('load', function()
	{		
		this.updateLocation();
	}, this);
	

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
		fields:['type_id', 'id','name','type', 'size', 'mtime', 'extension', 'timestamp', 'thumb_url','path','acl_id','locked_user_id','locked','folder_id','permission_level','readonly','unlock_allowed','handler'],
		columns:[{
			id:'name',
			header:GO.lang['strName'],
			dataIndex: 'name',
			renderer:function(v, meta, r){
				var cls = r.get('acl_id')>0 && r.get('readonly')==0 ? 'folder-shared' : 'filetype filetype-'+r.get('extension');
				if(r.get('locked_user_id')>0)
					v = '<div class="fs-grid-locked">'+v+'</div>';

				return '<div class="go-grid-icon '+cls+'" style="float:left;">'+v+'</div>';
			}
		},{
			id:'type',
			header:GO.lang.strType,
			dataIndex: 'type',
			sortable:true,
			hidden:true,
			width:100
		},{
			id:'size',
			header:GO.lang.strSize,
			dataIndex: 'size',
			renderer: function(v){
				return  v=='-' ? v : Ext.util.Format.fileSize(v);
			},
			hidden:true,
			width:100
		},{
			id:'mtime',
			header:GO.lang.strMtime,
			dataIndex: 'mtime',
			width:120
		}]
	};

	if(GO.customfields)
	{
		GO.customfields.addColumns("GO_Files_Model_File", fields);
	}

	this.gridStore = new GO.data.JsonStore({
//		url: GO.settings.modules.files.url+'json.php',
//		baseParams: {
//			'task': 'grid'
//		},
//		root: 'results',
//		totalProperty: 'total',
		url:GO.url("files/folder/list"),
		baseParams: {
			'query' : ''
		},
		id: 'type_id',
		fields:fields.fields,
		remoteSort:true
	});

	this.gridStore.on('load', this.onStoreLoad, this);

	if(config.filesFilter)
	{
		this.setFilesFilter(config.filesFilter);
	}

	this.gridPanel = new GO.files.FilesGrid({
		id:config.id+'-fs-grid',
		store: this.gridStore,
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
		cm:new Ext.grid.ColumnModel({
			defaults:{
				sortable:true
			},
			columns:fields.columns
		})
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

	/*
	 * Handles saving of locked state by the admin of the folder.
	 **/
	this.gridPanel.on('beforestatesave',function(grid, state){
		if(this.gridStore.reader.jsonData.lock_state){

			if (this.gridStore.reader.jsonData.may_apply_state)
				this.saveCMState(state);

			//cancel regular state save
			return false;
		}
	},this);


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

	this.filesContextMenu.on('download_link', function(menu, records, clickedAt, email){
		this.onDownloadLink(records,email);
	}, this);


	this.filesContextMenu.on('email_files', function(menu, records){
		this.emailFiles(records);
	}, this);
	
	this.filesContextMenu.on('addBookmark', function(menu, folderId){
		this.bookmarksGrid.store.load();
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

	this.locationPanel = new Ext.Panel({
		region:'north',
		border:false,
		baseCls:'x-plain',
		height:32,
		labelWidth:75,
		plain:true,
                layout: 'form',
//		cls:'go-files-location-panel',
		resizable:false,
                split:true,
		minSize:40,
		maxSize:40,
//		height:40,
		forceLayout:true,
                padding: '5px',
		items: [{
                      xtype: 'compositefield',
                      border: false,
                      anchor: '100%',
                      fieldLabel:GO.lang.strLocation,
                      items: [
                        this.locationTextField = new Ext.form.TextField({
                                name:'files-location',
                                flex : 1
                        }),
                        this.searchField = new GO.form.SearchField({
                            store: this.gridStore,
                            width: 230,
                            listeners: {
                              scope : this,
                              search : function() {
                                this.fireEvent('search');
                              },
                              reset : function() {
                                this.fireEvent('refresh');
                              }
                            }
                          })
                      ]
                    }]
	});

	this.upButton = new Ext.Button({
		iconCls: 'btn-up',
		text: GO.lang.up,
		cls: 'x-btn-text-icon',
		handler: function(){
                    if (GO.util.empty(this.gridStore.baseParams['query']))
                        this.setFolderID(this.parentID);
                    else
                        Ext.MessageBox.alert('',GO.files.lang['notInSearchMode']);
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

	var tbar = [{
	      	 	xtype:'htmlcomponent',
			html:GO.files.lang.name,
			cls:'go-module-title-tbar'
		}];

	tbar.push(this.newButton);

	this.uploadItem = new GO.base.upload.PluploadMenuItem({
		text: GO.lang.smallUpload,
		upload_config: {
			listeners: {
				scope:this,
				beforestart: function(uploadpanel) {
					//uploadpanel.uploader.settings.url = '/path/to/upload/handler?_runtime=' + uploadpanel.runtime;
				},
				uploadstarted: function(uploadpanel) {

				},
				uploadcomplete: function(uploadpanel, success, failures) {
					if ( success.length ) {
						this.sendOverwrite({
							upload:true

						});
						if(!failures.length){
							uploadpanel.onDeleteAll();
							uploadpanel.ownerCt.hide();
						}
					}
				}
			}
		}
	});

	this.jUploadItem = new Ext.menu.Item({
		iconCls: 'btn-upload',
		text : GO.lang.largeUpload,
		handler : function() {
                    if ( GO.util.empty(this.gridStore.baseParams['query']) ) {
			GO.currentFilesStore=this.gridStore;

			if (!deployJava.isWebStartInstalled('1.5.0')) {
				Ext.MessageBox.alert(GO.lang.strError,
				GO.lang.noJava);
			} else {
				GO.files.juploadFileBrowser=this; //for handling after upload
				GO.util.popup({
					url: GO.url('files/jupload/renderJupload'),
					//GO.settings.modules.files.url+'jupload/index.php?id='+encodeURIComponent(this.folder_id),
					width : 660,
					height: 500,
					target: 'jupload',
					allwaysOnTop:true // Not working!!
				});
			}
                    } else {
                        Ext.MessageBox.alert('',GO.files.lang['notInSearchMode']);
                    }
		},
		scope : this
	});

	this.uploadMenu = new Ext.menu.Menu({
		items: [
			this.uploadItem,
			this.jUploadItem
		]
	});

	this.uploadButton = new Ext.Button({
		text:GO.lang.upload,
		iconCls: 'btn-upload',
		menu: this.uploadMenu
	});

	if(!config.hideActionButtons)
	{
		tbar.push(this.uploadButton);
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
				GO.request({
					url:'files/folderPreference/submit',
					params: {
						folder_id: this.folder_id,
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

	tbar.push(this.stateLockedButton = new Ext.Button({
		iconCls: 'btn-settings',
		text: GO.files.lang.stateLocked,
		cls: 'x-btn-text-icon',
		hidden: true,
		disabled: true,
		scope: this
	}));

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
		items: tbar
	});

	this.thumbsPanel = new GO.files.ThumbsPanel({
		store:this.gridStore
	});

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
				//GO.files.openFile({id:record.data.id});
				record.data.handler.call(this);
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




	this.eastPanel = new Ext.Panel({
		region:'east',
		layout:'card',
		activeItem: 0,
		//items:[this.filePanel, this.folderPanel],
		collapsed:config.filePanelCollapsed,
		width:450,
		collapseMode:'mini',
		collapsible:true,
		hideCollapseTool:true,
		split:true,
		border:false,
		id: config.id+'fs-east-panel'
	});


	this.filePanel = new GO.files.FilePanel({
		id:config.id+'-file-panel',
		expandListenObject:this.eastPanel
	});
	this.eastPanel.add(this.filePanel);

	this.folderPanel = new GO.files.FolderPanel({
		id:config.id+'-folder-panel',
		hidden:true,
		expandListenObject:this.eastPanel
	});
	this.eastPanel.add(this.folderPanel);


	config['items']=[this.locationPanel, this.westPanel,this.cardPanel,this.eastPanel];

	GO.files.FileBrowser.superclass.constructor.call(this, config);

	

	this.addEvents({
		fileselected : true,
		filedblclicked : true,
                refresh : true,
                folderIdSet : true,
                rootIdSet : true,
                search : true
	});

	this.on('fileselected',function(grid, r){
		if(r.data.extension!='folder'){
//			this.folderPanel.setVisible(false);
//			this.filePanel.show()
			this.eastPanel.getLayout().setActiveItem(this.filePanel);

			this.filePanel.load(r.id.substr(2));
		}else
		{
//			this.filePanel.setVisible(false);
//			this.folderPanel.show();
			this.eastPanel.getLayout().setActiveItem(this.folderPanel);

			this.folderPanel.load(r.id.substr(2));
		}

	}, this);

	this.bookmarksGrid.on('bookmarkClicked', function(bookmarksGrid,bookmarkRecord){
		this.setFolderID(bookmarkRecord.data['folder_id']);
	},this);
        
    this.on('beforeFolderIdSet',function(){

      this.searchField.setValue('');
      delete this.gridStore.baseParams['query'];

      // turn on buttons
      if (!GO.util.empty(this.gridStore.reader.jsonData))
        this.setWritePermission(this.gridStore.reader.jsonData.permission_level);
      this._enableFilesContextMenuButtons(true);
    },this);

    this.on('folderIdSet',function(){

      this.searchField.setValue('');
      delete this.gridStore.baseParams['query'];

      // turn on buttons
      if (!GO.util.empty(this.gridStore.reader.jsonData))
        this.setWritePermission(this.gridStore.reader.jsonData.permission_level);
      this._enableFilesContextMenuButtons(true);
    },this);

    this.on('refresh',function(){

      this.searchField.setValue('');
      delete this.gridStore.baseParams['query'];

      // turn on buttons
      if (!GO.util.empty(this.gridStore.reader.jsonData))
        this.setWritePermission(this.gridStore.reader.jsonData.permission_level);
      this._enableFilesContextMenuButtons(true);
    },this);

    this.on('search',function(){

      // turn off buttons
      this._enableFilesContextMenuButtons(false);
      this.setWritePermission(0);

    },this);

}

Ext.extend(GO.files.FileBrowser, Ext.Panel,{

	fileClickHandler : false,
	scope : this,
	pasteSelections : Array(),
	/*
	 * cut or copy
	 */
	pasteMode : 'cut',

	path : '',

        _enableFilesContextMenuButtons : function(enable) {
            this.filesContextMenu.cutButton.setDisabled(!enable);
            this.filesContextMenu.copyButton.setDisabled(!enable);
            this.filesContextMenu.compressButton.setDisabled(!enable);
            this.filesContextMenu.decompressButton.setDisabled(!enable);
            this.filesContextMenu.createDownloadLinkButton.setDisabled(!enable);
            
            if (!GO.util.empty(this.filesContextMenu.gotaButton))
                this.filesContextMenu.gotaButton.setDisabled(!enable);
            
            if (!GO.util.empty(this.filesContextMenu.downloadLinkButton))
                this.filesContextMenu.downloadLinkButton.setDisabled(!enable);
            
            if (!GO.util.empty(this.filesContextMenu.emailFilesButton))
                this.filesContextMenu.emailFilesButton.setDisabled(!enable);
        },

	saveCMState: function(state) {
		GO.request({
			url: "files/folder/submit",
			params : {
				'id' : this.folder_id,
				'cm_state' : Ext.encode(state)
			},
			scope: this
		})
	},

	onStoreLoad : function(store){
		var state;

		if (store.reader.jsonData.lock_state && store.reader.jsonData.cm_state!='') {
			state = Ext.decode(store.reader.jsonData.cm_state);
		}else
		{
			state = Ext.state.Manager.get(this.gridPanel.id);
		}

		//state.sort=store.sortInfo;

		if(state){
			this.gridPanel.applyStoredState(state);

			if(store.reader.jsonData.lock_state && store.reader.jsonData.cm_state==''){
				//locked state is not stored yet do it now
				this.saveCMState(state);
			}
		}


		this.stateLockedButton.setVisible(store.reader.jsonData.lock_state);

		if(!GO.util.empty(store.reader.jsonData.feedback))
		{
			alert(store.reader.jsonData.feedback);
		}

		this.path = store.reader.jsonData.path;

		this.setWritePermission(store.reader.jsonData.permission_level);

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
		if(!this.parentID || !this.treePanel.getNodeById(this.parentID))
		{
			this.upButton.setDisabled(true);
		}else
		{
			this.upButton.setDisabled(false);
		}

		if(this.filePanel.model_id>0 && !store.getById('f:'+this.filePanel.model_id)){
			this.filePanel.reset();
		}

		if(this.folderPanel.model_id>0 && !store.getById('d:'+this.folderPanel.model_id)){
			this.folderPanel.reset();
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

	/**
	 * The filter parameter needs to be a comma separated string of file extensions.
	 * Example: 'jpg,png,xls,xlsx,pdf'
	 * 
	 */
	setFilesFilter : function(filter)
	{
		var old_filter = this.gridStore.baseParams['files_filter'];
		this.gridStore.baseParams['files_filter']=filter;

		if((old_filter != undefined) && old_filter != filter)
		{
			this.gridStore.reload();
		}
	},


	afterRender : function(){
		GO.files.FileBrowser.superclass.afterRender.call(this);

		GO.files.filePropertiesDialogListeners={
			scope:this,
			save:function(dlg, file_id, folder_id){
				if(this.folder_id==folder_id)
				{
					this.getActiveGridStore().load();
				}
			}
		}

		GO.files.folderPropertiesDialogListeners={
			scope:this,
//			save:function(dlg, folder_id){
//				this.setFolderID(folder_id, true);
//			},
			save:function(dlg, folder_id, parent_id){
				if(parent_id==this.folder_id)
				{
					this.setFolderID(parent_id);
				}
				//console.log(parent_id);
				var node = this.treePanel.getNodeById(parent_id);
				if(node)
				{
					delete node.attributes.children;
					node.reload();
				}
			}
		}
		
		if(!this.treePanel.getLoader().baseParams.root_folder_id)
			this.bookmarksGrid.store.load();
		
		this.buildNewMenu();
	},


	setRootID : function(rootID, folder_id)
	{
		
		rootID ? this.searchField.hide() : this.searchField.show();
		rootID ? this.bookmarksGrid.hide() : this.bookmarksGrid.show();
		
		
				
		if(this.treePanel.getLoader().baseParams.root_folder_id!=rootID || (folder_id>0 && this.folder_id!=folder_id)){
		
				this.folder_id=folder_id;
				this.treePanel.getLoader().baseParams.root_folder_id=rootID;
				this.treePanel.setExpandFolderId(folder_id);
				if(folder_id)
					this.refresh();
		}
                
    this.fireEvent('folderIdSet');
	},

	buildNewMenu : function(){

		this.newMenu.removeAll();

		GO.request({
			url: 'files/template/store',
			success: function(response, options, result)
			{

				this.newMenu.add( {
					iconCls: 'btn-add-folder',
					text: GO.lang.folder,
					cls: 'x-btn-text-icon',
					handler: this.promptNewFolder,
					scope: this
				});

				if(result.results.length)
				{
					this.newMenu.add('-');
					for(var i=0;i<result.results.length;i++)
					{
						var template = result.results[i];

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

			GO.request({
				url: 'files/template/createFile',
				params:{
					template_id:template_id,
					folder_id:this.folder_id,
					filename: filename
				},
				success: function(response, options, result)
				{
					store.load({
						callback: function(){
							if(result.id)
							{
								GO.files.openFile({id: result.id});
							}
						},
						scope: this
					});
				},
				scope:this
			});
		}
	},

	onDecompress : function(records){

            if (GO.util.empty(this.gridStore.baseParams['query'])) {

		var decompress_sources = [];
		for(var i=0;i<records.length;i++)
		{
			decompress_sources.push(records[i].data.path);
		}

		if(decompress_sources.length)
		{
			var store = this.getActiveGridStore();
			var params = {};
			params['decompress_sources']=Ext.encode(decompress_sources);
			params.working_folder_id=this.folder_id;

			GO.request({
				timeout:300000,
				maskEl:this.getEl(),
				url:'files/folder/decompress',
				params:params,
				success:function(){
					store.load();
				}
			});
		}
               
            } else {
                Ext.MessageBox.alert('', GO.files.lang['notInSearchMode']);
            }
	},

	onCompress : function(records, filename, utf8)
	{

            if (GO.util.empty(this.gridStore.baseParams['query'])) {

		var params = {
			compress_sources: [],
			working_folder_id:this.folder_id,
			destination_folder_id:this.folder_id
		};

		for(var i=0;i<records.length;i++)
		{
			if(records[i].data.parent_id)//for tree
				params.working_folder_id=records[i].data.parent_id;

			params.compress_sources.push(records[i].data.path);
		}


		if(!filename || filename == '')
		{
			if(!this.compressDialog){
				this.compressDialog = new GO.files.CompressDialog ({
					scope:this,
					handler:function(win, filename, utf8){
						this.onCompress(records, filename, utf8);
					}
				});
			}
			
			this.compressDialog.show();
			
		}else
		{
			params.archive_name=filename;
			params.utf8=utf8 ? '1' : '0';
			params.compress_sources=Ext.encode(params.compress_sources);
			var store = this.getActiveGridStore();

			GO.request({
				timeout:300000,
				maskEl:this.getEl(),
				url:'files/folder/compress',
				params:params,
				success:function(){
					store.load();
				}
			});
		}

            } else {
                Ext.MessageBox.alert('', GO.files.lang['notInSearchMode']);
            }

	},

	getSelectedTreeRecords : function(){
		var sm = this.treePanel.getSelectionModel();
		var nodes = sm.getSelectedNodes();

		var records=[];

		for(var i=0;i<nodes.length;i++)
		{
			records.push({
				data: {
					type_id:'d:'+nodes[i].id,
					id: nodes[i].id,
					extension:'folder'
				}
			});
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
            if (GO.util.empty(this.gridStore.baseParams['query']))
		this.paste(this.pasteMode, this.folder_id, this.pasteSelections);
            else
                Ext.MessageBox.alert('', GO.files.lang['notInSearchMode']);
	},

	onDelete : function(clickedAt){
		if(clickedAt=='tree')
		{
			var records = this.getSelectedTreeRecords();
			GO.deleteItems({
				url:GO.url('files/folder/delete'),
				params:{
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

	emailFiles: function(records) {
		var files = new Array();
		Ext.each(records, function(record) {
			var folderId = record.data.folder_id;
			var id = record.data.id;

			if (!Ext.isEmpty(folderId)) {
				files.push(record.data.path);
			} else {
				GO.email.openFolderTree(id);
			}
		});
		GO.email.emailFiles(files);
	},

	onDownloadLink : function(records,email){

		this.emailDownloadLink=email;
		this.file_data = records[0].data;

		if (!this.expireDateWindow) {
			this.expireForm = new Ext.form.FormPanel({
				items: [new Ext.DatePicker({
					itemId: 'expire_time',
					name : 'expire_time',
					format: GO.settings.date_format,
					hideLabel: true
				})]
			});
			this.expireDateWindow = new GO.Window({
				title: GO.files.lang.expireTime,
				height:218,
				width:224,
				layout:'fit',
				border:false,
				maximizable:true,
				collapsible:true,
				closeAction:'hide',
				items: [this.expireForm]
			});
			this.expireForm.items.get('expire_time').on('select', function(field,date){
				if(this.emailDownloadLink){

					GO.email.showComposer({
						loadUrl:GO.url('files/file/emailDownloadLink'),
						loadParams:{
							id:this.file_data.id,
							expire_time: parseInt(date.setDate(date.getDate())/1000)
						}
					});
				} else {
					GO.request({
						maskEl: this.getEl(),
						url: 'files/file/createDownloadLink',
						params: {
							id:this.file_data.id,
							expire_time: parseInt(date.setDate(date.getDate())/1000)
						},
						success: function(options, response, result)
						{
							this.filePanel.reload();
						},
						scope:this
					});
				}

				this.expireDateWindow.hide();
			}, this);
		}
/*
		this.expireDateWindow.on('show', function(){
			var myDate = new Date;
			var unixtime_ms = myDate.setDate(myDate.getDate()+7);
			var unixtime = parseInt(unixtime_ms/1000);
			this.expireForm.items.get('expire_time').setValue(myDate.format(GO.settings.date_format));
			//this.expireForm.items.get('expire_unixtime').setValue(unixtime);
		}, this);
*/

		this.expireDateWindow.show();
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
			ids : Ext.encode(paste_sources),
			destination_folder_id : destination,
			paste_mode : pasteMode,
			id : this.folder_id
		};

		this.sendOverwrite(params);

	},


	refresh : function(syncFilesystemWithDatabase){
		
		this.treePanel.setExpandFolderId(this.folder_id);
		
		if(syncFilesystemWithDatabase)
			this.treePanel.getLoader().baseParams.sync_folder_id=this.folder_id;

		
		this.treePanel.getRootNode().reload();
		
		this.setFolderID(this.folder_id);

		if(syncFilesystemWithDatabase)
			delete this.treePanel.getLoader().baseParams.sync_folder_id;

		this.searchField.setValue('');
		delete this.gridStore.baseParams['query'];

		this.filePanel.reload();
                
		this.fireEvent('refresh');
	},

	sendOverwrite : function(params){

		if(!params.command)
			params.command='ask';

		if(!params.destination_folder_id)
			params.destination_folder_id=this.folder_id;

		this.overwriteParams = params;

		this.getEl().mask(GO.lang.waitMsgSave);

		var url = params.upload ? GO.url('files/folder/processUploadQueue') : GO.url('files/folder/paste');

		Ext.Ajax.request({
			url: url,
			params:this.overwriteParams,
			callback: function(options, success, response){

				this.getEl().unmask();

				var pasteSources = Ext.decode(this.overwriteParams.ids);
				var pasteDestination = this.overwriteParams.destination_folder_id;


				//delete params.paste_sources;
				//delete params.paste_destination;

				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{

					var responseParams = Ext.decode(response.responseText);

					if(!responseParams.success && !responseParams.fileExists)
					{
						if(this.overwriteDialog)
						{
							this.overwriteDialog.hide();
						}
						Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
						this.refresh();
					}else
					{
						if(responseParams.fileExists)
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
											this.overwriteParams.overwrite='yes';
											this.sendOverwrite(this.overwriteParams);
										},
										scope: this
									},{
										text: GO.lang.cmdYesToAll,
										handler: function(){
											this.overwriteParams.overwrite='yestoall';
											this.sendOverwrite(this.overwriteParams);
										},
										scope: this
									},{
										text: GO.lang.cmdNo,
										handler: function(){
											this.overwriteParams.overwrite='no';
											this.sendOverwrite(this.overwriteParams);
										},
										scope: this
									},{
										text: GO.lang.cmdNoToAll,
										handler: function(){
											this.overwriteParams.overwrite='notoall';
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
							t.overwrite(this.overwriteDialog.body, {
								file: responseParams.fileExists
							});
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
								delete destinationNode.attributes.children;
								destinationNode.reload();
							}

							if(pasteSources && params.paste_mode=="cut")
							{
								//remove moved nodes if we cut and paste
								for(var i=0;i<pasteSources.length;i++)
								{
									var arr = pasteSources[i].split(':');
									var node = this.treePanel.getNodeById(arr[1]);
									if(node)
										node.remove();
								}
							}

							if(this.overwriteDialog)
								this.overwriteDialog.hide();
						}
					}
				}
			},
			scope: this
		});

	},

	promptNewFolder : function(){

		if (GO.util.empty(this.gridStore.baseParams['query'])) {
	
			if(!this.newFolderWindow)
			{
				this.newFolderWindow = new GO.files.NewFolderDialog();
				this.newFolderWindow.on('save', function(){
					this.getActiveGridStore().load();

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
            	
		} else {
			Ext.MessageBox.alert('', GO.files.lang['notInSearchMode']);
		}
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
				//browsers don't like loading a json request and download dialog at the same time.'
//				if(this.filePanel.loading)
//				{
//					this.onGridDoubleClick.defer(200, this, [grid, rowClicked, e]);
//				}else
//				{
//					GO.files.openFile({id:record.data.id});
					record.data.handler.call(this);
//				}
			}
		}
	},

	setWritePermission : function(permissionLevel)
	{
		var writePermission=permissionLevel>=GO.permissionLevels.write;
		var deletePermission=permissionLevel>=GO.permissionLevels.writeAndDelete;
		var createPermission=permissionLevel>=GO.permissionLevels.create;

		this.newButton.setDisabled(!createPermission);
		this.deleteButton.setDisabled(!deletePermission);
		this.uploadButton.setDisabled(!createPermission);
		this.cutButton.setDisabled(!deletePermission);
                
                this.copyButton.setDisabled(permissionLevel<=0);
                
		this.pasteButton.setDisabled(!writePermission || !this.pasteSelections.length);

	//this.filesContextMenu.deleteButton.setDisabled(!writePermission);
	},

	setFolderID : function(id, expand)
	{
      
    this.fireEvent('beforeFolderIdSet');
      
		this.folder_id = id;
		//this.gridStore.baseParams['id']=this.thumbsStore.baseParams['id']=id;
		this.gridStore.baseParams['folder_id']=id;

		this.getActiveGridStore().load({
			callback:function(){
			
				if(expand)
				{
					var activeNode = this.treePanel.getNodeById(id);
						
					if(activeNode){
						activeNode.expand();
						this.updateLocation();
					}else{
						this.treePanel.setExpandFolderId(id);
						this.treePanel.getRootNode().reload();	
					}
				}

				this.focus();
			},
			scope:this
		});

	},
	
	updateLocation : function(){
		var activeNode = this.treePanel.getNodeById(this.folder_id);
		
		if(activeNode)
		{			
			var selectedNode = this.treePanel.getSelectionModel().getSelectedNodes();
			if((!selectedNode.length || activeNode.id!=selectedNode[0].id))
				this.treePanel.getSelectionModel().select(activeNode);
			
			var path = new String();
			path = activeNode.getPath('text');
			path = path.substring(2);
			this.locationTextField.setValue(path);
		}
	},

	showGridPropertiesDialog  : function(){
		var selModel = this.gridPanel.getSelectionModel();
		var selections = selModel.getSelections();

		if(selections.length==0)
		{
			GO.errorDialog.show(GO.lang['noItemSelected']);
		}else if(selections.length>1)
		{
			GO.errorDialog.show(GO.files.lang.errorOneItem);
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
			GO.files.showFilePropertiesDialog(id+"", {
				loadParams:{
					process_file_id:process_file_id
				}
			});
			GO.files.filePropertiesDialog.tabPanel.setActiveTab(3);
		}
	}

	GO.checker.registerRequest("files/notification/unsent",{},function(checker, data){});
});


GO.files.FilesObservable = function(){
	GO.files.FilesObservable.superclass.constructor.call(this);

	this.addEvents({
		'beforeopenfile':true
	})
}
Ext.extend(GO.files.FilesObservable, Ext.util.Observable);

GO.files.filesObservable = new GO.files.FilesObservable();

GO.files.showImageViewer = function(imagesParams){
	if(!this.imageViewer)
	{
		this.imageViewer = new GO.files.ImageViewer({
			closeAction:'hide'
		});
	}
	
	imagesParams["thumbParams"]=Ext.encode({lw:this.imageViewer.width-20,ph:this.imageViewer.height-100});

GO.request({
		url:"files/folder/images",
		params:imagesParams,
		maskEl:Ext.getBody(),
		success:function(response, options, result){
			this.imageViewer.show(result.images, result.index);
		},
		scope:this
	});
}

GO.files.openFile = function(config)
{		
	if(!GO.files.openFileWindow){
		GO.files.openFileWindow =  new GO.files.OpenFileWindow();
	}
	GO.files.openFileWindow.show(config);
}


GO.files.downloadFile = function (fileId){
	window.open(GO.url("files/file/download",{id:fileId,inline:false}));
}

//GO.files.editFile = function (fileId){
//
//	if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission && !GO.util.isAndroid())
//	{
//		if(!deployJava.isWebStartInstalled('1.6.0'))
//		{
//			Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
//		}else
//		{
//			document.location.href=GO.url('gota/file/edit&id='+fileId);
//			return;
//		}
//	}
//	GO.files.downloadFile(fileId);
//}

//for external links
GO.files.showFolder = function(folder_id){

	var fb = GO.mainLayout.openModule("files");
	
	fb.setFolderID(folder_id, true);
		
}

GO.files.openFolder = function(id, folder_id)
{
	if(!GO.files.fileBrowser)
	{
		GO.files.fileBrowser=new GO.files.FileBrowser({
			id:'popupfb',
			border:false,
			filePanelCollapsed:true
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
			items: GO.files.fileBrowser
		});
	}
	
	if(!folder_id)
		folder_id=id;
	
	GO.files.fileBrowser.setRootID(id, folder_id);
	GO.files.fileBrowserWin.show();

	return GO.files.fileBrowser;
}

GO.files.createSelectFileBrowser = function(){
	if(!GO.selectFileBrowser)
	{
		GO.selectFileBrowser= new GO.files.FileBrowser({
			border:false,
			filePanelCollapsed:true,
			treeCollapsed:false
		});

		GO.selectFileBrowserWindow = new GO.Window({
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


GO.linkHandlers["GO_Files_Model_File"]=function(id, record){
	//GO.files.showFilePropertiesDialog(id+"");
	if(!GO.files.linkFileWindow){
		var filePanel = new GO.files.FilePanel();
		GO.files.linkFileWindow= new GO.LinkViewWindow({
			title: GO.files.lang.file,
			items: filePanel,
			filePanel: filePanel,
			closeAction:"hide"
		});
	}
	GO.files.linkFileWindow.filePanel.load(id);
	GO.files.linkFileWindow.show();
	return GO.files.linkFileWindow;
}
GO.linkPreviewPanels["GO_Files_Model_File"]=function(config){
	config = config || {};
	return new GO.files.FilePanel(config);
}


GO.linkHandlers["GO_Files_Model_Folder"]=function(id, record){
	//GO.files.showFilePropertiesDialog(id+"");
	if(!GO.files.linkFolderWindow){
		var filePanel = new GO.files.FolderPanel();
		GO.files.linkFolderWindow= new GO.LinkViewWindow({
			title: GO.files.lang.folder,
			items: filePanel,
			filePanel: filePanel,
			closeAction:"hide"
		});
	}
	GO.files.linkFolderWindow.filePanel.load(id);
	GO.files.linkFolderWindow.show();
	return GO.files.linkFolderWindow;
}
GO.linkPreviewPanels["GO_Files_Model_Folder"]=function(config){
	config = config || {};
	return new GO.files.FolderPanel(config);
}



GO.moduleManager.addModule('files', GO.files.FileBrowser, {
	title : GO.files.lang.files,
	iconCls : 'go-tab-icon-files'
});
