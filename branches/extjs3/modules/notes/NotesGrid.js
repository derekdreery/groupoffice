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
 
 GO.notes.NotesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.notes.lang.notes;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.notes.url+ 'json.php',
	    baseParams: {
	    	task: 'notes',
	    	category_id: 0	    	
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','category_id','user_name','ctime','mtime','name','content'],
	    remoteSort: true
	});

	
	config.paging=true;

	var columnModel =  new Ext.grid.ColumnModel([
		{
			header: GO.lang.strName, 
			dataIndex: 'name'
		},
		{
			header: GO.lang.strOwner, 
			dataIndex: 'user_name',
		  sortable: false,
			hidden:true
		},		{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime',
			hidden:true
		},		{
			header: GO.lang.strMtime, 
			dataIndex: 'mtime'
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
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
  });	
		    	
  config.tbar = [GO.lang['strSearch'] + ':', this.searchField];
	
	GO.notes.NotesGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		GO.notes.noteDialog.show(record.data.id);
		}, this);
	
};


Ext.extend(GO.notes.NotesGrid, GO.grid.GridPanel,{
	
	afterRender : function()
	{
		if(!GO.notes.noteDialog.hasListener('save'))
		{
			GO.notes.noteDialog.on('save', function(){   
					this.store.reload();	    			    			
			}, this);
		}
		GO.notes.NotesGrid.superclass.afterRender.call(this);
	}
});