

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
		GO.linkTypesStore= new GO.data.JsonStore({				
				fields: ['id','name','model', 'checked'],
				url:GO.url('search/modelTypes'),
				autoLoad:true
			});
	}

	config.store = config.store || GO.linkTypesStore;

	GO.LinkTypeFilterPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.LinkTypeFilterPanel, GO.grid.MultiSelectGrid,{

});

