GO.sites.SitesContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[];
	
	this.actionSiteProperties = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.sites.lang.siteProperties,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.showSitePropertiesDialog();
		}
	});
	config.items.push(this.actionSiteProperties);

	GO.sites.SitesContextMenu.superclass.constructor.call(this,config);

}

Ext.extend(GO.sites.SitesContextMenu, Ext.menu.Menu, {
	model_name : false,
	selected  : [],
	treePanel : false,

	setSelected : function (treePanel, model_name) {
		this.selected = treePanel.getSelectionModel().getSelectedNodes();
		this.model_name=model_name;
		this.treePanel = treePanel;
	},

	getSelected : function () {
		if (typeof(this.selected)=='undefined')
			return [];
		else
			return this.selected;
	},

	showSitePropertiesDialog : function() {
		var site_id = this.selected[0].id.substring(5,this.selected[0].id.length);
		GO.mainLayout.getModulePanel('sites').showSiteDialog(site_id);
	}
	
});