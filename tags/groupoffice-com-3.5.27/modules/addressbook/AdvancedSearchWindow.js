GO.addressbook.AdvancedSearchWindow = function(config){	
	if(!config)
	{
		config={};
	}	

	GO.addressbook.searchQueryPanel = new GO.addressbook.SearchQueryPanel();
	GO.addressbook.savedQueryGrid = new GO.addressbook.SavedQueryGrid();

	config.grid = GO.addressbook.savedQueryGrid;

	config.items = new Ext.TabPanel({
		activeTab: 0,
		border: false,
		items: [GO.addressbook.searchQueryPanel, GO.addressbook.savedQueryGrid]
	});

	//config.iconCls='go-link-icon-4';
	config.collapsible=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=600;
	config.height=350;
	config.closeAction='hide';
	config.title= GO.addressbook.lang.advancedSearch;		
	config.buttons=[{
		text: GO.addressbook.lang.executeQuery,
		handler: function(){
			this.fireEvent('ok', this);
			this.hide();
		},
		scope: this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}
	];
	
	
	GO.addressbook.AdvancedSearchWindow.superclass.constructor.call(this, config);	
}

Ext.extend(GO.addressbook.AdvancedSearchWindow, GO.Window,{
	
	editor : new Ext.form.TextField(),
	
	getCellEditor : function(colIndex, rowIndex){
		return this.editor;
	},
	show : function(type){
		if(type!=GO.addressbook.searchQueryPanel.typesStore.baseParams.type)
		{
			GO.addressbook.searchQueryPanel.typesStore.baseParams.type=type;
			GO.addressbook.searchQueryPanel.typesStore.load();

			GO.addressbook.savedQueryGrid.store.baseParams.companies=type=='companies' ? '1' : '0';
		}
		GO.addressbook.AdvancedSearchWindow.superclass.show.call(this);
	}
	/*
	,
	getGridData : function(){
		
		var data = {};
		
		for (var i = 0; i < GO.addressbook.savedQueryGrid.store.data.items.length;  i++)
		{
			var r = GO.addressbook.savedQueryGrid.store.data.items[i];
			
						
			if(!GO.util.empty(r.get('value')))
			{
				data[r.get('field')]=r.get('value');
			}
		}
		
		return data;		
	}
	*/
});