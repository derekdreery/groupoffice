GO.site.MainPanel = function(config){
	
	if(!config)
		config = {};
	
	this.centerPanel = new Ext.Panel({
		region:'center',
		border:true,
		layout:'card',
		items:[
			new GO.site.ContentPanel(),
			new Ext.Panel({
				id:'site-menus',
				html:'menus'
		})]
	}); 
	
	this.treePanel = new GO.site.SiteTreePanel({
		region:'west',
		width:300,
		border:true,
		listeners:{
			click:this.treeNodeClick,
			scope:this
		}
	});

	config.layout='border';
	
	config.items=[
		this.treePanel,
		this.centerPanel
	];
	
	config.tbar=new Ext.Toolbar({
			cls:'go-head-tb',
			items: ['-']
	});
	
	GO.site.MainPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.site.MainPanel, Ext.Panel,{
	
	treeNodeClick: function(node){
		var arr = node.id.split('_');
		if(arr[0]!='site'){
			var centerPanelId = 'site-'+arr[0];
			var item = this.centerPanel.getComponent(centerPanelId);
				
			this.centerPanel.getLayout().setActiveItem(item);
			item.load(arr[1]);
		}
	},

	showSiteDialog: function(site_id){
		if(!this.siteDialog){
			this.siteDialog = new GO.site.SiteDialog();
			this.siteDialog.on('hide', function(){
				this.rebuildTree();
			},this);
		}
		
		this.siteDialog.show(site_id);
	},

	rebuildTree: function(){
		this.treePanel.getLoader().load(this.treePanel.getRootNode());
	}
});

GO.moduleManager.addModule('site', GO.site.MainPanel, {
	title : GO.site.lang.name,
	iconCls : 'go-tab-icon-site'
});