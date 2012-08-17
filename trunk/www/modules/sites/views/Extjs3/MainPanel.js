GO.sites.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	this.centerPanel = new GO.sites.ContentPanel({
		region:'center',
		border:true
	}); 
	
	this.treePanel = new GO.sites.SitesTreePanel({
		region:'west',
		width:300,
		border:true
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
	
//	this.settingsButton = new Ext.Button({
//		iconCls: 'btn-settings',
//		itemId:'settings',
//		text: GO.sites.lang.moduleSettings,
//		cls: 'x-btn-text-icon'
//	});
//	
//	this.settingsButton.on("click", function(){
//		this.showModuleSettingsDialog();
//	},this);
	
	config.layout='border';
	
	config.items=[
		this.treePanel,
		this.centerPanel
	];
	
	config.tbar=new Ext.Toolbar({
			cls:'go-head-tb',
			items: [
				this.newSiteButton
//				"-",
//				this.settingsButton
			]
	});
	
	GO.sites.MainPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sites.MainPanel, Ext.Panel,{

	showSiteDialog: function(site_id){
		if(!this.siteDialog){
			this.siteDialog = new GO.sites.SiteDialog();
			this.siteDialog.on('hide', function(){
				this.rebuildTree();
			},this);
		}
		
		this.siteDialog.show(site_id);
	},
	showContentDialog: function(page_id){
		if(!this.contentDialog){
			this.contentDialog = new GO.sites.ContentDialog();
			this.contentDialog.on('hide', function(){
				this.rebuildTree();
			},this);
		}
		
		this.contentDialog.show(page_id);
	},
//	showModuleSettingsDialog: function(){
//		if(!this.moduleSettingsDialog)
//			this.moduleSettingsDialog = new GO.sites.ModuleSettingsDialog();
//		
//		this.moduleSettingsDialog.show();
//	},
	rebuildTree: function(){
		this.treePanel.getLoader().load(this.treePanel.getRootNode());
	}
});

GO.moduleManager.addModule('sites', GO.sites.MainPanel, {
	title : GO.sites.lang.name,
	iconCls : 'go-tab-icon-sites'
});