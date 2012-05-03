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


	var fields ={
		fields:['id','category_id','user_name','ctime','mtime','name','content'],
		columns:[
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
		]
	};

	if(GO.customfields)
	{
		GO.customfields.addColumns("GO_Notes_Model_Note", fields);
	}
	
	config.title = GO.notes.lang.notes;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
		url: GO.url('notes/note/store'),		
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: fields.fields,
		remoteSort: true
	});

	config.store.on('load', function()
	{
		if(config.store.reader.jsonData.feedback)
		{
			alert(config.store.reader.jsonData.feedback);
		}
	},this)

	config.paging=true;

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
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
	});
		    	
	config.tbar = [GO.lang['strSearch'] + ':', this.searchField];
	
	GO.notes.NotesGrid.superclass.constructor.call(this, config);
};


Ext.extend(GO.notes.NotesGrid, GO.grid.GridPanel,{
	

	});