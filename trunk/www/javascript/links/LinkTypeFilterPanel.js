

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
	config.header=false;
	config.collapseMode='mini';
	config.allowNoSelection=true;
	

	//config.title=GO.lang.strType;

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

	getSelectedTypes : function(type_id){

		if(typeof(type_id)=='undefined')
			type_id=-1;

		var types = [], checked, current_type_id;

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			current_type_id = this.filterGrid.store.data.items[i].get('id');
			if(type_id>-1 && type_id != current_type_id){
				checked=false;
				this.store.data.items[i].set('checked', "0");
			}else
			{
				if(type_id ==current_type_id){
					checked="1";
					this.store.data.items[i].set('checked', "1");
				}else
				{
					checked = this.filterGrid.store.data.items[i].get('checked');
				}
			}
			if(checked=="1")
			{
				types.push(this.filterGrid.store.data.items[i].get('id'));
			}
		}
		return types;
	}
});

