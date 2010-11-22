

GO.LinkTypeFilterPanel = function(config)
{
	if(!config)
	{
		config = {};
	}

	config.split=true;
	config.resizable=true;
	config.autoScroll=true;
	config.collapsible=true;
	//config.header=false;
	config.collapseMode='mini';
	config.allowNoSelection=true;
	
	if(!config.title)
		config.title=GO.lang.strType;

	if(!GO.linkTypesStore){
		GO.linkTypesStore= new Ext.data.JsonStore({
				root: 'results',
				data: {"results":GO.linkTypes}, //defined in /default_scripts.inc.php
				fields: ['id','name', 'checked'],
				id:'id'
			});
	}

	config.store = config.store || GO.linkTypesStore;

	GO.LinkTypeFilterPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.LinkTypeFilterPanel, GO.grid.MultiSelectGrid,{

});

