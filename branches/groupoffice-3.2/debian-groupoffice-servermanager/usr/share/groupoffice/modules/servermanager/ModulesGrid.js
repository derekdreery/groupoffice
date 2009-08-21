/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GridPanel.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.servermanager.ModulesGrid = function(config){

	if(!config)
	{
		config = {};
	}

	config.title = GO.servermanager.lang.modules;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.servermanager.url+ 'json.php',
	    baseParams: {
	    	task: 'modules',
				installation_id:0
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','name','installed','allowed']
	});


	/*var installedColumn = new GO.grid.CheckColumn({
		header: GO.servermanager.lang.installed,
		dataIndex: 'installed',
		width: 55,		
		menuDisabled:true
	});*/

	var allowedColumn = new GO.grid.CheckColumn({
		header: GO.servermanager.lang.allowed,
		dataIndex: 'allowed',
		width: 55,
		menuDisabled:true
	});

	config.plugins=[allowedColumn/*, installedColumn*/];

	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.lang.strName,
			dataIndex: 'name',
			sortable:true
		},		
		/*installedColumn,*/
		allowedColumn
	]);

	config.cm=columnModel;

	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;


	GO.servermanager.ModulesGrid.superclass.constructor.call(this, config);

};

Ext.extend(GO.servermanager.ModulesGrid, GO.grid.GridPanel,{
	afterRender : function(){
		GO.servermanager.ModulesGrid.superclass.afterRender.call(this);
		//this.store.load();
	},
	
	onShow : function(){
		GO.servermanager.ModulesGrid.superclass.onShow.call(this);
		if(!this.store.loaded)
			this.store.load();
	}
});