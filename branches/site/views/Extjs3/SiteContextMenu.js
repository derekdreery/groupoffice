GO.site.SiteContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	config.items.push({
		iconCls: 'btn-view',
		text: GO.lang.strView,
		cls: 'x-btn-text-icon',
		handler:function(){
//			window.open(GO.url('site/site/redirectToFront', {id: this.selected[0].attributes.site_id}));			
		},
		scope:this
	});
	
	this.actionSiteProperties = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.site.lang.options,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.treePanel.mainPanel.showSiteDialog(this.selected.attributes.site_id);
		}
	});
	config.items.push(this.actionSiteProperties);
	
	GO.site.SiteContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.SiteContextMenu, Ext.menu.Menu, {
	model_name : false,
	selected  : false,
	treePanel : false,
	
	setSelected : function (treePanel, node, model_name) {
		this.selected = node;
		this.model_name=model_name;
		this.treePanel = treePanel;
	},

	getSelected : function () {
		if (typeof(this.selected)=='undefined')
			return [];
		else
			return this.selected;
	}
});