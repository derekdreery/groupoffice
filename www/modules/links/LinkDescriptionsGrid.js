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
GO.links.LinkDescriptionsGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.title=GO.links.lang.linkDescriptions;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.closable=false;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.config.host+ 'json.php',
	    baseParams: {
	    	task: 'link_descriptions'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','description'],
	    remoteSort: true
	});
	//config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
	   		{
			header: GO.lang.strDescription, 
			dataIndex: 'description'
		}]
	});
	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	this.linkDescriptionDialog = new GO.links.LinkDescriptionDialog();
		this.linkDescriptionDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
	    	this.linkDescriptionDialog.show();
			},
			scope: this
		},{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];
	GO.links.LinkDescriptionsGrid.superclass.constructor.call(this, config);
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		this.linkDescriptionDialog.show(record.data.id);
		}, this);
};
Ext.extend(GO.links.LinkDescriptionsGrid, GO.grid.GridPanel,{
	afterRender : function()
	{
		
		GO.links.LinkDescriptionsGrid.superclass.afterRender.call(this);
		this.store.load();
		
	}
});
