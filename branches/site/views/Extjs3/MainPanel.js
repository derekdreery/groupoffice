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
	
	treeNodeClick: function(node){
		
		console.log(node.id);
		var arr = node.id.split('_');
		
		var siteId = arr[0];
		var type = arr[1];
		var itemId = arr[2];
		
		// Load the content edit panel in the centerpanel
		if(type == 'content' && !GO.util.empty(itemId)){
			var panel = this.centerPanel.getComponent('site-'+type);
				
			this.centerPanel.getLayout().setActiveItem(panel);
			panel.load(itemId);
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