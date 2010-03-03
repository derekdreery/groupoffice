GO.shipping.AccordionMenu = function(config) {
	if(!config)
	{
		config = {};
	}

	config.layout = 'accordion';
	config.items = [new GO.grid.GridPanel({
		title : GO.shipping.lang.shipping,
		store : new Ext.data.SimpleStore({
			fields : ['dataType']
		}),
		cm : new Ext.grid.ColumnModel({
			defaults:{
				sortable:false
			},
			columns : [{
				id : 'dataType',
				dataIndex: 'dataType'
			}]
		}),
		sm : new Ext.grid.RowSelectionModel(),
		view : new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang['strNoItems']
		})
	})];

	GO.shipping.AccordionMenu.superclass.constructor.call(this, config);

	this.items.items[0].on("rowclick", function(grid,rowIndex,e) {
		GO.shipping.gridsContainer.layout.setActiveItem(rowIndex);
	})
}

Ext.extend(GO.shipping.AccordionMenu, Ext.Panel, {

	addItem : function(menuType,record) {
		if (menuType=='Shipping') {
			this.items.items[0].store.add(record);
		}
	}
});