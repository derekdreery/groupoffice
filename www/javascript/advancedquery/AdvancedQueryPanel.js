Ext.ns('GO.advancedquery');

GO.advancedquery.AdvancedQueryPanel = function (config){
	if(!config)
	{
		config={};
	}

	this.searchQueryPanel = new GO.advancedquery.SearchQueryPanel({
		region:'north',
		height:250,
		fieldsUrl:config.fieldsUrl,
		type:config.type
	});
	
	this.savedQueryGrid = new GO.advancedquery.SavedQueriesGrid({
		region:'center',
		type:config.type
	});


	config.items = [this.searchQueryPanel, this.savedQueryGrid];

	config.layout='border';
	config.modal=false;
	config.resizable=true;
	config.closeAction='hide';
	//config.title= GO.filesearch.lang.advancedSearch;

	GO.advancedquery.AdvancedQueryPanel.superclass.constructor.call(this, config);

	this.addEvents({
		'search':true
	});

}

Ext.extend(GO.advancedquery.AdvancedQueryPanel, Ext.Panel);