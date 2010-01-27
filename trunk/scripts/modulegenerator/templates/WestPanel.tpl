/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: WestPanel.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.{module}.{friendly_multiple_ucfirst}Grid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.{module}.lang.{friendly_multiple};
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.{module}.url+ 'json.php',
	    baseParams: {task: '{friendly_multiple}'},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: [{STOREFIELDS}],
	    remoteSort: true
	});
	
	config.store.on('load', function(){
		this.selModel.selectFirstRow();
	}, this);
	
	config.paging=true;

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
	   {COLUMNS}
		]
	});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	GO.{module}.{friendly_multiple_ucfirst}Grid.superclass.constructor.call(this, config);
};


Ext.extend(GO.{module}.{friendly_multiple_ucfirst}Grid, GO.grid.GridPanel,{
	
	loaded : false,
	
	afterRender : function()
	{
		GO.{module}.{friendly_multiple_ucfirst}Grid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	
	onGridShow : function(){
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
});