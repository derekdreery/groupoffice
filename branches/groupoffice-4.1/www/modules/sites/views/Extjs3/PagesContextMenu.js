GO.sites.PagesContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[];
	
	config.items.push({
		iconCls: 'btn-view',
		text: GO.lang.strView,
		cls: 'x-btn-text-icon',
		handler:function(){
			window.open(GO.url('sites/contentBackend/redirectToFront', {id: this.selected[0].id}));			
		},
		scope:this
	});

	
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
	
	this.actionDeletePage = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.sites.lang.deletePage,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.deletePage();
		}
	});
	config.items.push(this.actionDeletePage);
		

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
		GO.mainLayout.getModulePanel('sites').showContentDialog(this.selected[0].id);
	},
	deletePage : function() {
		var page_id = this.selected[0].id;
		
		Ext.MessageBox.confirm(GO.sites.lang.deletePage, GO.sites.lang.deletePageText, function(btn){
			if(btn == 'yes'){
				Ext.Ajax.request({
					url: GO.url('sites/contentBackend/delete'),
					params: {
						id: page_id
					},
					success: function(){
						GO.mainLayout.getModulePanel('sites').rebuildTree();
					},
					failure: function(){
						
					},
					scope: this
				});
			}
		});
	}
	
});