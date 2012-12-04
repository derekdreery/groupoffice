GO.sites.ContentPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	Ext.applyIf(config, {

	});
		
	GO.sites.ContentPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sites.ContentPanel, Ext.Panel,{
	
});