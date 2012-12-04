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
	/*
	this.actionAddPage = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.sites.lang.addContent,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.addContent();
		}
	});
	config.items.push(this.actionAddPage);
	*/
	this.actionDeleteSite = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.sites.lang.deleteSite,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.deleteSite();
		}
	});
	
	config.items.push(this.actionDeleteSite);
	
	config.items.push({
		iconCls: 'btn-view',
		text: GO.lang.strView,
		cls: 'x-btn-text-icon',
		handler:function(){
			window.open(GO.url('sites/siteBackend/redirectToFront', {id: this.selected[0].attributes.site_id}));			
		},
		scope:this
	});

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
	},
	addContent : function(){

		var site_id = this.selected[0].id.substring(5,this.selected[0].id.length);
		
		if(!GO.sites.pageDialog){
			GO.sites.pageDialog = new GO.sites.PageDialog();

			GO.sites.pageDialog.on("hide",function(){
				GO.mainLayout.getModulePanel('sites').rebuildTree();
			},this);
		}
		GO.sites.pageDialog.addBaseParam('site_id',site_id);
		GO.sites.pageDialog.show();
	},	
	deleteSite : function() {
		var site_id = this.selected[0].id.substring(5,this.selected[0].id.length);
		
		Ext.MessageBox.confirm(GO.sites.lang.deleteSite, GO.sites.lang.deleteSiteConfirm, function(btn){
			if(btn == 'yes'){
				GO.request({
					url: 'sites/siteBackend/delete',
					params: {
						id: site_id
					},
					success: function(){
						GO.mainLayout.getModulePanel('sites').rebuildTree();
					},
					scope: this
				});
			}
		});
	}
	
});