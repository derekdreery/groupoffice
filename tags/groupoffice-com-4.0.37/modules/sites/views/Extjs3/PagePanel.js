GO.sites.PagePanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	Ext.applyIf(config, {

	});
		
	GO.sites.PagePanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sites.PagePanel, Ext.Panel,{
	
});