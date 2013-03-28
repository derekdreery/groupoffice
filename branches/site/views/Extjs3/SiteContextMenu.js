GO.site.SiteContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[];
	
	this.actionSiteProperties = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.site.lang.siteProperties,
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
		text: GO.site.lang.addContent,
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
		text: GO.site.lang.deleteSite,
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
			window.open(GO.url('site/site/redirectToFront', {id: this.selected[0].attributes.site_id}));			
		},
		scope:this
	});

	GO.site.SiteContextMenu.superclass.constructor.call(this,config);

}

Ext.extend(GO.site.SiteContextMenu, Ext.menu.Menu, {
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
		GO.mainLayout.getModulePanel('site').showSiteDialog(site_id);
	},
	addContent : function(){

		var site_id = this.selected[0].id.substring(5,this.selected[0].id.length);
		
		if(!GO.site.pageDialog){
			GO.site.pageDialog = new GO.site.PageDialog();

			GO.site.pageDialog.on("hide",function(){
				GO.mainLayout.getModulePanel('site').rebuildTree();
			},this);
		}
		GO.site.pageDialog.addBaseParam('site_id',site_id);
		GO.site.pageDialog.show();
	},	
	deleteSite : function() {
		var site_id = this.selected[0].id.substring(5,this.selected[0].id.length);
		
		Ext.MessageBox.confirm(GO.site.lang.deleteSite, GO.site.lang.deleteSiteConfirm, function(btn){
			if(btn == 'yes'){
				GO.request({
					url: 'site/site/delete',
					params: {
						id: site_id
					},
					success: function(){
						GO.mainLayout.getModulePanel('site').rebuildTree();
					},
					scope: this
				});
			}
		});
	}
	
});