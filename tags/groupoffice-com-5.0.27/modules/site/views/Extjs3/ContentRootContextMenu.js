GO.site.ContentRootContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	this.actionAdd = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.site.lang.addContent,
		cls: 'x-btn-text-icon',
		handler:function(){
			// Create, only send the siteId because it need to be created in the root
			this.treePanel.contentPanel.create(this.selected.attributes.site_id);
		},
		scope:this
	});

	config.items.push(this.actionAdd);
		
	GO.site.ContentRootContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.ContentRootContextMenu, Ext.menu.Menu, {
	model_name : false,
	selected  : [],
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