

/**
 * Menuitem contextmenu
 */
GO.site.MenuitemContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	this.actionProperties = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.site.lang.properties,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function(){
			
			if(!GO.site.menuitemDialog){
				GO.site.menuitemDialog = new GO.site.MenuitemDialog();
			}
			GO.site.menuitemDialog.setMenuId(this.selected.attributes.menu_id);
			GO.site.menuitemDialog.show(this.selected.attributes.menu_item_id);
		}
	});
	
	this.actionAdd = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.site.lang.addMenuItem,
		cls: 'x-btn-text-icon',
		handler:function(){
			if(!GO.site.menuitemDialog){
				GO.site.menuitemDialog = new GO.site.MenuitemDialog();
			}
			GO.site.menuitemDialog.setMenuId(this.selected.attributes.menu_id);
			GO.site.menuitemDialog.show();
		},
		scope:this
	});
	
	this.actionDelete = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.site.lang['delete'],
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.deleteMenu();
		}
	});

	config.items.push(this.actionProperties);
	config.items.push(this.actionAdd);
	config.items.push(this.actionDelete);
		
	GO.site.MenuitemContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.MenuitemContextMenu, Ext.menu.Menu, {
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
			return false;
		else
			return this.selected;
	},
	deleteMenu : function() {
		if(this.selected.attributes.hasChildren){
			if(!this.errorDialog){
				this.errorDialog = new GO.ErrorDialog();
			}
			this.errorDialog.show(GO.site.lang.deleteMenuHasChildren, GO.site.lang.deleteMenu);
		} else {
			
			console.log(this.selected.attributes);
			
			var menuId = this.selected.attributes.menu_id;
			var menuItemId = this.selected.attributes.menu_item_id;

			Ext.MessageBox.confirm(GO.site.lang.deleteMenu, GO.site.lang.deleteMenuConfirm, function(btn){
				if(btn == 'yes'){
					GO.request({
						url: 'site/menuItem/delete',
						params: {
							id: menuItemId,
							menu_id:menuId
						},
						success: function(){
							GO.mainLayout.getModulePanel('site').rebuildTree();
						},
						failure: function(){

						},
						scope: this
					});
				}
			});
		}
	}
});


/**
 * MenuRootContextMenu contextmenu
 */
GO.site.MenuRootContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	this.actionAdd = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.site.lang.addMenu,
		cls: 'x-btn-text-icon',
		handler:function(){
			if(!GO.site.menuDialog){
				GO.site.menuDialog = new GO.site.MenuDialog();
			}
			GO.site.menuDialog.setSiteId(this.selected.attributes.site_id);
			GO.site.menuDialog.show();
		},
		scope:this
	});
	
	config.items.push(this.actionAdd);
		
	GO.site.MenuRootContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.MenuRootContextMenu, Ext.menu.Menu, {
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
			return false;
		else
			return this.selected;
	}
});

/**
 * ContentContextMenu contextmenu
 */
GO.site.ContentContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
//	this.actionView = new Ext.menu.Item({
//		iconCls: 'btn-view',
//		text: GO.lang.strView,
//		cls: 'x-btn-text-icon',
//		handler:function(){
////			console.log("View");
//			window.open(GO.url('site/content/redirectToFront', {id: this.selected[0].id}));			
//		},
//		scope:this
//	});

	
	this.actionAdvanced = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.site.lang.advanced,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{			
			this.treePanel.contentPanel.showContentDialog(this.selected.attributes.content_id);
		}
	});
	
	this.actionAdd = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.site.lang.addContent,
		cls: 'x-btn-text-icon',
		handler:function(){
			// Load an empty contentPanel and set the parent id
			this.treePanel.contentPanel.create(this.selected.attributes.site_id,this.selected.attributes.content_id);
		},
		scope:this
	});
	
	this.actionDelete = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.site.lang.deleteContent,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.deleteContent();
		}
	});
	
//	config.items.push(this.actionView);
	config.items.push(this.actionAdvanced);
	config.items.push(this.actionAdd);
	config.items.push(this.actionDelete);
		
	GO.site.ContentContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.ContentContextMenu, Ext.menu.Menu, {
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
			return false;
		else
			return this.selected;
	},
	deleteContent : function() {
		
		if(this.selected.attributes.hasChildren){
			if(!this.errorDialog){
				this.errorDialog = new GO.ErrorDialog();
			}
			this.errorDialog.show(GO.site.lang.deleteContentHasChildren, GO.site.lang.deleteContent);
		} else {
			var contentId = this.selected.attributes.content_id;

			Ext.MessageBox.confirm(GO.site.lang.deleteContent, GO.site.lang.deleteContentConfirm, function(btn){
				if(btn == 'yes'){
					GO.request({
						url: 'site/content/delete',
						params: {
							id: contentId
						},
						success: function(){
							GO.mainLayout.getModulePanel('site').rebuildTree();
						},
						failure: function(){

						},
						scope: this
					});
				}
			});
		}
	}
});

/**
 * ContentRootContextMenu contextmenu
 */
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

/**
 * Site contextmenu
 */
GO.site.SiteContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	config.items.push({
		iconCls: 'btn-view',
		text: GO.lang.strView,
		cls: 'x-btn-text-icon',
		handler:function(){
			window.open(GO.settings.config.host+'modules/site/index.php?site_id='+this.selected.attributes.site_id);			
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