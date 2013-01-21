GO.files.SelectFilesDialog = function(config){


	if(!config)
	{
		config={};
	}

	config.layout='border';
	config.modal=false;
	config.resizable=true;
	config.maximizable=true;
	config.width=600;
	config.height=400;
	config.closeAction='hide';
	config.title=GO.files.lang.selectFiles;        

        this.filesGrid = new GO.files.SelectFilesGrid({
		region:'center',
                id:'fs_select_files_grid'
	});

        this.treeLoader = new GO.base.tree.TreeLoader(
	{
		dataUrl:GO.settings.modules.files.url+'json.php',
		baseParams:{
			task: 'tree',
			root_folder_id:0,                        
			expand_folder_id:0
		},
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
		layout:'fit',
		split:true,
		autoScroll:true,
		width: 200,
		animate:true,
                collapsible:true,
		collapseMode:'mini',
		loader: this.treeLoader,
		rootVisible:false,
		containerScroll: true,
		header:false,
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


	config.items=[this.treePanel, this.filesGrid];


        config.tbar=[
	{
		text: GO.lang.selectAll,
		handler: function()
                {
                        this.filesGrid.selectAllFiles(this.root_id);

		},
		scope: this
	}];

        config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.submitForm();
		},
		scope: this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}];


	GO.files.SelectFilesDialog.superclass.constructor.call(this, config);

        this.addEvents({'save' : true});
        
}

Ext.extend(GO.files.SelectFilesDialog, Ext.Window,{

        folder_id: 0,

        submitForm : function(hide)
        {
                var selectedFiles = this.filesGrid.getSelectedFiles(true);

                this.filesGrid.removeSelectedFiles();

                this.fireEvent('save', this, selectedFiles);
                
                this.hide();
        },

        setRootID : function(rootID, folder_id)
	{
                this.folder_id=folder_id;
                this.root_id = rootID;
                
                this.treeLoader.baseParams.root_folder_id=rootID;
                this.treeLoader.baseParams.expand_folder_id=folder_id;
                this.rootNode.reload({
                        callback:function(){
                                delete this.treeLoader.baseParams.expand_folder_id;
                        },
                        scope:this
                });
	},

        setFolderID : function(folder_id, expand)
	{
		this.folder_id = folder_id;                
		this.filesGrid.store.baseParams['id']=folder_id;

		this.filesGrid.store.load({
			callback:function(){
				var activeNode = this.treePanel.getNodeById(folder_id);
				if(activeNode)
				{
					this.treePanel.getSelectionModel().select(activeNode);                                        				
				}

				if(expand && activeNode)
				{					
                                        activeNode.expand();
				}

				this.focus();
			},
			scope:this
		});
	},

        show : function(folder_id)
        {
                this.setRootID(folder_id);

                GO.files.SelectFilesDialog.superclass.show.call(this);
        }
        
});
