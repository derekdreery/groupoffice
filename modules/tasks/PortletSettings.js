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
 * @author Danny Wijffelaars <dwijffelaars@intermesh.nl>
 */
GO.tasks.PortletSettings = function(config){
	if(!config)
	{
		config = {};
	}
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;

	var CheckColumn = new GO.grid.CheckColumn({
        header: GO.tasks.lang.visible,
        dataIndex: 'visible',
        width: 55,
        disabled_field:'',
	sortable:false
    });

	var fields ={
		fields:['name', 'tasklist_id'],
		columns:[{
			header: GO.lang.strTitle,
			dataIndex: 'name'
		},
		CheckColumn
	]
	};
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.tasks.url+ 'json.php',
		baseParams: {
			task: 'settings'
		},
		root: 'results',
		id: 'tasklist_id',
		totalProperty:'total',
		fields:['tasklist_id', 'name', 'visible'],
		remoteSort: true
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	config.plugins = [CheckColumn];

	GO.tasks.PortletSettings.superclass.constructor.call(this, config);
};
Ext.extend(GO.tasks.PortletSettings, Ext.grid.GridPanel,{

	getGridData : function(){
		var data = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}
		return data;
	}
});