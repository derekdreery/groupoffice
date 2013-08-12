GO.site.MainPanel = function(config){
	
	if(!config)
		config = {};
	
	this.centerPanel = new Ext.Panel({
		region:'center',
		border:true,
		layout:'card',
		layoutConfig:{ 
			layoutOnCardChange:true
		},
		items:[
			this.contentPanel = new GO.site.ContentPanel({
				parentPanel:this
			})
			]
	}); 
	
	this.treePanel = new GO.site.SiteTreePanel({
		region:'west',
		width:300,
		border:true,
		contentPanel:this.contentPanel,
		mainPanel:this
	});

	config.layout='border';
	
	config.items=[
		this.treePanel,
		this.centerPanel
	];
	
	this.reloadButton = new Ext.Button({
		iconCls: 'btn-refresh',
		itemId:'refresh',
		//text: GO.site.lang.refresh,
		cls: 'x-btn-text-icon'
	});
	
	this.reloadButton.on("click", function(){
		this.rebuildTree();
	},this);
	
	config.tbar=new Ext.Toolbar({
			cls:'go-head-tb',
			items: [
				this.reloadButton,
				'-'
			]
	});
	
	GO.site.MainPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.site.MainPanel, Ext.Panel,{
	
	showSiteDialog: function(site_id){
		if(!this.siteDialog){
			this.siteDialog = new GO.site.SiteDialog();
			this.siteDialog.on('hide', function(){
				this.rebuildTree();
			},this);
		}
		
		this.siteDialog.show(site_id);
	},

	rebuildTree: function(select){
		
		var selectedNode = this.treePanel.getSelectionModel().getSelectedNode();
		this.treePanel.getLoader().load(this.treePanel.getRootNode());
		
		if(select)
			this.treePanel.getSelectionModel().select(selectedNode); 
	}
});

GO.moduleManager.addModule('site', GO.site.MainPanel, {
	title : GO.site.lang.name,
	iconCls : 'go-tab-icon-site'
});