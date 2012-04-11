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
	
	this.actionAddPage = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.sites.lang.addPage,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.addPage();
		}
	});
	config.items.push(this.actionAddPage);
	
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
	addPage : function(){

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
		
		Ext.MessageBox.confirm(GO.sites.lang.deleteSite, GO.sites.lang.deleteSiteText, function(btn){
			if(btn == 'yes'){
				Ext.Ajax.request({
					url: GO.url('sites/siteBackend/delete'),
					params: {
						id: site_id
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