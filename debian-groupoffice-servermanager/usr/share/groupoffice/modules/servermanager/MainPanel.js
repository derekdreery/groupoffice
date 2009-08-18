/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: MainPanel.tpl 1913 2008-05-07 12:41:17Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.servermanager.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	this.installationsGrid = new GO.servermanager.InstallationsGrid();
	this.reportGrid = new GO.servermanager.ReportGrid();
	
	//dirty but it works
	this.installationsGrid.store.on('load', function(){		
		this.navMenu.select(0);	
	}, this);
	
	
	var navData = [
    		['sm_installations', GO.servermanager.lang.installations],
    		['sm_report', GO.servermanager.lang.report]    		
    	];
    	
  var cardPanelItems = [
		this.installationsGrid,		
		this.reportGrid		
		]
 	
 	
	var navStore = new Ext.data.SimpleStore({
			fields: ['dom_id', 'name'],
    	data : navData
			});
	
	this.navMenu= new GO.grid.SimpleSelectList({
		store: navStore		
		});
	
	
	this.navMenu.on('click', function(dataview, index){		
			this.cardPanel.getLayout().setActiveItem(index);
			
		}, this);
	
	this.navPanel = new Ext.Panel({
          region:'west',
          title:GO.lang.menu,
					autoScroll:true,					
					width: 150,
					split:true,
					resizable:true,							
					items:this.navMenu
	});

	this.cardPanel = new Ext.Panel({
		region:'center',
		layout:'card',
		border:false,
		activeItem: 0,
		layoutConfig: {
		  deferredRender: true
		},
		items:cardPanelItems
	
	});

	config.items=[
		this.navPanel,
		this.cardPanel
	];	
	
	config.title=GO.servermanager.lang.servermanager;
	
	config.layout='border';
	GO.servermanager.MainPanel.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.servermanager.MainPanel, Ext.Panel, {
	afterRender : function(){
		GO.servermanager.MainPanel.superclass.afterRender.call(this);
	}
});



GO.mainLayout.onReady(function(){
		GO.servermanager.installationDialog = new GO.servermanager.InstallationDialog();
	/* {LINKDIALOGS} */
});
/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('servermanager', GO.servermanager.MainPanel, {
	title : GO.servermanager.lang.servermanager,
	iconCls : 'go-tab-icon-servermanager'
});
/*
 * If your module has a linkable item, you should add a link handler like this. 
 * The index (no. 1 in this case) should be a unique identifier of your item.
 * See classes/base/links.class.inc for an overview.
 * 
 * Basically this function opens a project window when a user clicks on it from a 
 * panel with links. 
 */
GO.linkHandlers[13]=function(id){
	//	GO.servermanager.installationDialog.show(id);
	
	var installationPanel = new GO.servermanager.InstallationPanel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.billing.lang.installation,
		items: installationPanel
	});
	 installationPanel.loadInstallation(id);
	linkWindow.show();
}
/* {LINKHANDLERS} */


/* {NEWMENUITEMS} */

