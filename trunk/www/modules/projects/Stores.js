Ext.namespace('GO.projects.stores');

GO.projects.stores.bookableProjects = new GO.data.JsonStore({

	url: GO.settings.modules.projects.url+ 'json.php',		
	baseParams:{
			task:'projects',
			auth_type: 'book'
		},
	root: 'results',
	totalProperty: 'total',
	id: 'id',
	fields:['id', 'name']
});
GO.projects.stores.bookableProjects.on('load', function(){
	GO.projects.stores.bookableProjects.loaded=true;
	
	GO.projects.enableHoursPanel();
}, this);


GO.projects.stores.readableFees = new GO.data.JsonStore({

		url: GO.settings.modules.projects.url+ 'json.php',		
		baseParams:{
				task:'fees',
				auth_type: 'read'
			},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id', 'name']
	});

GO.projects.stores.readableFees.on('load', function(){
	GO.projects.stores.readableFees.loaded=true;
	
	GO.projects.enableHoursPanel();
}, this);

GO.projects.enableHoursPanel=function(){
	
	var enabled =GO.projects.stores.readableFees.getCount()>0 && GO.projects.stores.bookableProjects.getCount()>0;   
	GO.projects.addHoursPanel.setDisabled(!enabled);
}
