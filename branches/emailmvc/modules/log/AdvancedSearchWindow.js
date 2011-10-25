GO.log.AdvancedSearchWindow = function(config){

	config = config || {};

	this.queryPanel = new GO.advancedquery.AdvancedQueryPanel({
		type:'log',
		fieldsUrl:GO.settings.modules.log.url+'json.php'
	});

	config.layout='fit';
	config.items=this.queryPanel;
	config.title=GO.lang.advancedSearch;
	config.width=600;
	config.height=500;


	GO.log.AdvancedSearchWindow.superclass.constructor.call(this, config);
}

Ext.extend(GO.log.AdvancedSearchWindow, GO.Window);

