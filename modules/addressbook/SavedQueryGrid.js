/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.addressbook.SavedQueryGrid = function(config)
	{
		if(!config)
		{
			config = {};
		}

		config.title = GO.addressbook.lang.savedQueries;
		config.paging=true;
		config.border=false;

		config.store = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+'json.php',
			baseParams: {
				task: "sqls",
				companies:0
			},
			root: 'results',
			id: 'id',
			fields: ['id','name','sql'],
			remoteSort: true
		});

		var cm =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header: GO.lang.strName,
			dataIndex: 'name'
		}]
		});
		
		config.cm=cm;

		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang.strNoItems
		}),
		config.sm=new Ext.grid.RowSelectionModel();
		config.loadMask=true;

		config.tbar = [{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];

		GO.addressbook.SavedQueryGrid.superclass.constructor.call(this, config);

		this.on("rowdblclick",function(grid,row,e) {
			GO.addressbook.searchQueryPanel.queryField.setValue(grid.store.data.items[row].data.sql);
			GO.addressbook.advancedSearchWindow.fireEvent('ok', GO.addressbook.advancedSearchWindow);
			GO.addressbook.advancedSearchWindow.hide();
		});

		this.on("beforeshow",function(gridpanel){
			this.store.load();
		});
	}

Ext.extend(GO.addressbook.SavedQueryGrid, GO.grid.GridPanel, {

/*	,
	executeSQL : function(sql) {
		Ext.Ajax.request({
			url: GO.settings.modules.addressbook.url + "action.php",
			params: {'task' : 'sql',
		});
	}
*/
});