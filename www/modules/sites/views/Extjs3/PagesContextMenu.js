GO.sites.PagesContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[];
	
	this.actionPageProperties = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.sites.lang.pageProperties,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.showPagePropertiesDialog();
		}
	});
	config.items.push(this.actionPageProperties);

	GO.sites.PagesContextMenu.superclass.constructor.call(this,config);

}

Ext.extend(GO.sites.PagesContextMenu, Ext.menu.Menu, {
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

	showPagePropertiesDialog : function() {
		//console.log(this.selected[0].id);
		GO.mainLayout.getModulePanel('sites').showPageDialog(this.selected[0].id);
	}
	
});