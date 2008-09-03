GO.projects.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	this.summaryPanel = new GO.projects.SummaryPanel({
		layout:'fit',
		border: false
	});
	
	
	//dirty but it works
	this.summaryPanel.store.on('load', function(){
		
		this.navMenu.select(0);	
	}, this);
	
	this.hoursPanel = new GO.projects.HoursPanel({
		border:false
	});
	
	this.hoursPanel.on('show', function(){
		this.hoursPanel.hoursGrid.store.baseParams.user_id=GO.settings.user_id;
		this.hoursPanel.hoursGrid.store.load();
	}, this);
	
	
	
	
	
	
	
	var navData = [
    		['pm_summary', GO.projects.lang.summary],
    		['pm_nav_time', GO.projects.lang.timeTracking]    		
    	];
    	
  var cardPanelItems = [
		this.summaryPanel,		
		this.hoursPanel		
		]
    
  navData.push(['pm_nav_reports', GO.projects.lang.reports]);
  this.reportPanel = new GO.projects.ReportPanel({
			border: false,
			layout:'fit'		
		});
    cardPanelItems.push(this.reportPanel);
   	
  if(GO.settings.modules.projects.write_permission)
  {
  	navData.push(['pm_nav_projects', GO.projects.lang.projectAdministration]);
    navData.push(['pm_nav_fees', GO.projects.lang.feeAdministration]);
    
  
  	this.projectsPanel = new GO.projects.ProjectsPanel({
			title:GO.projects.lang.projectAdministration,
			layout:'fit',
			border: true
		});
		cardPanelItems.push(this.projectsPanel);  
    
    this.feesPanel = new GO.projects.FeesGrid({
			title:GO.projects.lang.feeAdministration,
			layout:'fit'			
		});
		cardPanelItems.push(this.feesPanel);
		
		
  }
	
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
	
	config.layout='border';
	GO.projects.MainPanel.superclass.constructor.call(this, config);
	
};


Ext.extend(GO.projects.MainPanel, Ext.Panel, {
	afterRender : function(){
		GO.projects.MainPanel.superclass.afterRender.call(this);
	}
});

GO.mainLayout.onReady(function(){
	GO.projects.projectDialog = new GO.projects.ProjectDialog();
});

/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('projects', GO.projects.MainPanel, {
	title : GO.projects.lang.projects,
	iconCls : 'go-tab-icon-projects'
});
/*
 * If your module has a linkable item, you should add a link handler like this. 
 * The index (no. 1 in this case) should be a unique identifier of your item.
 * See classes/base/links.class.inc for an overview.
 * 
 * Basically this function opens a project window when a user clicks on it from a 
 * panel with links. 
 */
GO.linkHandlers[5]=function(id){
	var projectPanel = new GO.projects.ProjectPanel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.projects.lang.project,
		items: projectPanel
	});
	projectPanel.loadProject(id);
	linkWindow.show();
}


