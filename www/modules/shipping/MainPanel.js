Ext.namespace('GO.shipping');

GO.shipping.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}

	GO.shipping.accordionMenu = new GO.shipping.AccordionMenu({region: 'center'});

	GO.shipping.gridsContainer = new GO.shipping.GridsContainer( {
		title:GO.shipping.lang.shippings,
		id:'sh-shipping-grid',
		region:'center',
		menu : GO.shipping.accordionMenu
	});


	GO.shipping.westPanel = new Ext.Panel({
		region:'west',
		titlebar: false,
		autoScroll:false,
		closeOnTab: true,
		width: 210,
		split:true,
		resizable:true,
		layout:'border',
		baseCls: 'x-plain',
		items:[
		GO.shipping.accordionMenu
		//,filterPanel
		]
	});

config.layout='border';
//config.tbar=;
config.items=[
	new Ext.Panel({
		region:'north',
		height:32,
		baseCls:'x-plain',
		tbar:new Ext.Toolbar({
			cls:'go-head-tb',
			items: [this.addButton = new Ext.Button({
					iconCls: 'btn-add',
					text: GO.lang['cmdAdd'],
					cls: 'x-btn-text-icon',
					handler: function(){

						GO.shipping.taskDialog.show({
							tasklist_id: this.tasklist_id,
							tasklist_name: this.tasklist_name
						});
									
					},
					scope: this
				}),this.deleteButton = new Ext.Button({
					iconCls: 'btn-delete',
					text: GO.lang['cmdDelete'],
					cls: 'x-btn-text-icon',
					handler: function(){
						this.gridsContainer.deleteSelected({
							callback : this.taskPanel.gridDeleteCallback,
							scope: this.taskPanel
						});
					},
					scope: this
				}),{
					iconCls: 'btn-settings',
					text: GO.lang.administration,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.showAdminDialog();
					},
					scope: this
				},{
					iconCls: 'btn-export',
					text: GO.lang.cmdExport,
					cls: 'x-btn-text-icon',
					handler:function(){
						var config = {};
						config.colModel = this.gridsContainer.getColumnModel();
						config.title = GO.shipping.lang.shippings;

						var query = this.gridsContainer.searchField.getValue();
						if(!GO.util.empty(query))
						{
							config.subtitle= GO.lang.searchQuery+': '+query;
						}else
						{
							config.subtitle='';
						}

						if(!this.exportDialog)
						{
							this.exportDialog = new GO.ExportQueryDialog({
								query:'get_tasks'
							});
						}
						this.exportDialog.show(config);

					},
					scope: this
				},{
					iconCls: 'btn-refresh',
					text: GO.lang['cmdRefresh'],
					cls: 'x-btn-text-icon',
					handler: function(){
						this.taskListsStore.load();
					},
					scope: this
				}
			]
		})
				
	}),
	GO.shipping.westPanel,
	GO.shipping.gridsContainer
];
	
GO.shipping.MainPanel.superclass.constructor.call(this, config);

}
 
Ext.extend(GO.shipping.MainPanel, Ext.Panel,{
afterRender : function()
{
	GO.shipping.MainPanel.superclass.afterRender.call(this);
		
}	
});


GO.mainLayout.onReady(function(){

});


GO.shipping.writableTasklistsStore = new GO.data.JsonStore({
url: GO.settings.modules.tasks.url+'json.php',
baseParams: {
	'task': 'tasklists',
	'auth_type':'write'
},
root: 'results',
totalProperty: 'total',
id: 'id',
fields:['id','name','user_name'],
remoteSort:true,
sortInfo: {
	field: 'name',
	direction: 'ASC'
}
});


/*
* This will add the module to the main tabpanel filled with all the modules
*/
 
GO.moduleManager.addModule('shipping', GO.shipping.MainPanel, {
title : GO.shipping.lang.shipping,
iconCls : 'go-tab-icon-tasks'
});
/*
* If your module has a linkable item, you should add a link handler like this. 
* The index (no. 1 in this case) should be a unique identifier of your item.
* See classes/base/links.class.inc for an overview.
* 
* Basically this function opens a task window when a user clicks on it from a 
* panel with links. 
*/
GO.linkHandlers[12]=function(id, link_config){
/*if(!GO.shipping.taskDialog)
		{
			GO.shipping.taskDialog = new GO.shipping.TaskDialog();
		}
		GO.shipping.taskDialog.show({task_id: id, link_config: link_config});*/
		
var taskPanel = new GO.shipping.TaskPanel();
var linkWindow = new GO.LinkViewWindow({
	title: GO.shipping.lang.shipping,
	items: taskPanel
});
taskPanel.load(id);
linkWindow.show();
}

GO.linkPreviewPanels[12]=function(config){
config = config || {};
return new GO.shipping.TaskPanel(config);
}


GO.newMenuItems.push({
text: GO.shipping.lang.shipping,
iconCls: 'go-link-icon-12',
handler:function(item, e){

}
});
