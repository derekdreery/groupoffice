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
 
GO.notes.CategoriesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.notes.lang.categories;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.notes.url+ 'json.php',
	    baseParams: {
	    	task: 'categories',
	    	auth_type: 'read'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','user_name','acl_id','name'],
	    remoteSort: true
	});

	var columnModel =  new Ext.grid.ColumnModel([
	  {
			header: GO.lang.strName, 
			dataIndex: 'name'
		}
	]);
	columnModel.defaultSortable = true;
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	
	this.categoryDialog = new GO.notes.CategoryDialog();
	    			    		
		this.categoryDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	
	
	GO.notes.CategoriesGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		this.categoryDialog.show(record.data.id);
		
		}, this);
		
	
	
};


Ext.extend(GO.notes.CategoriesGrid, GO.grid.GridPanel,{
	
	loaded : false,
	
	afterRender : function()
	{
		GO.notes.CategoriesGrid.superclass.afterRender.call(this);
		
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