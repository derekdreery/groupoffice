GO.sites.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	this.centerPanel = new GO.sites.PagePanel({
		region:'center',
		border:true
	}); 
	
	this.treePanel = new GO.sites.SitesTreePanel({
		region:'west',
		width:300,
		border:true
	});
	
	config.layout='border';
	
	config.items=[
		this.treePanel,
		this.centerPanel
	];
	
	config.tbar=new Ext.Toolbar({
			cls:'go-head-tb',
			items: []
	});
	
	GO.sites.MainPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sites.MainPanel, Ext.Panel,{

	showSiteDialog: function(site_id){
		if(!this.siteDialog)
			this.siteDialog = new GO.sites.SiteDialog();
		
		this.siteDialog.show(site_id);
	},
	showPageDialog: function(page_id){
		if(!this.pageDialog)
			this.pageDialog = new GO.sites.PageDialog();
		
		this.pageDialog.show(page_id);
	}
});

GO.moduleManager.addModule('sites', GO.sites.MainPanel, {
	title : GO.sites.lang.name,
	iconCls : 'go-tab-icon-sites'
});