GO.sites.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	this.centerPanel = new Ext.Panel({
		region:'center',
		border:true,
		layout:'card',
		items:[new GO.sites.ContentPanel(),
			new Ext.Panel({
				id:'sites-menus',
				html:'menus'
		})]
	}); 
	
	this.treePanel = new GO.sites.SitesTreePanel({
		region:'west',
		width:300,
		border:true,
		listeners:{
			click:this.treeNodeClick,
			scope:this
		}
	});
	
	this.newSiteButton = new Ext.Button({
		iconCls: 'btn-add',
		itemId:'add',
		text: GO.sites.lang.newSite,
		cls: 'x-btn-text-icon'
	});
	
	this.newSiteButton.on("click", function(){
		this.showSiteDialog(0); // The parameter 0 will generate a new site object.
	},this);
	
	
	config.layout='border';
	
	config.items=[
		this.treePanel,
		this.centerPanel
	];
	
	config.tbar=new Ext.Toolbar({
			cls:'go-head-tb',
			items: [
				this.newSiteButton
			]
	});
	
	GO.sites.MainPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sites.MainPanel, Ext.Panel,{
	
	treeNodeClick: function(node){
		var arr = node.id.split('_');
		if(arr[0]!='site'){
			var centerPanelId = 'sites-'+arr[0];
			var item = this.centerPanel.getComponent(centerPanelId);
				
			this.centerPanel.getLayout().setActiveItem(item);
			item.load(arr[1]);
		}
	},

	showSiteDialog: function(site_id){
		if(!this.siteDialog){
			this.siteDialog = new GO.sites.SiteDialog();
			this.siteDialog.on('hide', function(){
				this.rebuildTree();
			},this);
		}
		
		this.siteDialog.show(site_id);
	},
//	showContentDialog: function(page_id){
//		if(!this.contentDialog){
//			this.contentDialog = new GO.sites.ContentDialog();
//			this.contentDialog.on('hide', function(){
//				this.rebuildTree();
//			},this);
//		}
//		
//		this.contentDialog.show(page_id);
//	},

	rebuildTree: function(){
		this.treePanel.getLoader().load(this.treePanel.getRootNode());
	}
});

GO.moduleManager.addModule('sites', GO.sites.MainPanel, {
	title : GO.sites.lang.name,
	iconCls : 'go-tab-icon-sites'
});