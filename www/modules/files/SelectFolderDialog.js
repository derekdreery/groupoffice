GO.files.SelectFolderDialog = Ext.extend(GO.Window, {
	
	initComponent : function(){
		
		if(!this.scope)
			this.scope=this;
		
		this.layout='border';
		this.title=GO.files.lang.selectFolder;
		this.height=400;
		this.width=200;
		this.border=false;
		this.collapsible=true;
		this.maximizable=true;
		this.closeAction='hide';
			
		this.buttons=[
			{
				text: GO.lang.cmdOk,				        						
				handler: function(){
					var sm = this.foldersTree.getSelectionModel();
					var selectedFolderNode = sm.getSelectedNode();
					if(!selectedFolderNode)
						alert('Fout');
					this.handler.call(this.scope, this, selectedFolderNode.attributes.path);
					this.hide();
				}, 
				scope: this 
			},{
				text: GO.lang.cmdClose,				        						
				handler: function(){
					this.hide();
				},
				scope:this
			}				
		];
		
		this.foldersTree = new GO.files.TreePanel({
			region:'center',
			border:false,
			loadDelayed:true,
			hideActionButtons:true,
			treeCollapsed:false,
			scope: this,
			selModel: new Ext.tree.DefaultSelectionModel()
		});
		
		this.items=[this.foldersTree];
		
		GO.files.SelectFolderDialog.superclass.initComponent.call(this);
	},
	
	show : function(){
		
		GO.files.SelectFolderDialog.superclass.show.call(this);
	}
	
});