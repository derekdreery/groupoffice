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
		url: GO.url('notes/note/store'),		
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: ['id','category_id','user_name','ctime','mtime','name','content'],
		remoteSort: true,
		model:"GO_Notes_Model_Note"
	});

	config.store.on('load', function()
	{
		if(config.store.reader.jsonData.feedback)
		{
			alert(config.store.reader.jsonData.feedback);
		}
	},this)

	config.paging=true;

	
	config.columns=[
		{
			header: GO.lang.strName,
			dataIndex: 'name',
			sortable: true
		},
		{
			header: GO.lang.strOwner,
			dataIndex: 'user_name',
			sortable: false,
			hidden:true
		},		{
			header: GO.lang.strCtime,
			dataIndex: 'ctime',
			hidden:true,
			sortable: true
		},		{
			header: GO.lang.strMtime,
			dataIndex: 'mtime',
			sortable: true
		}
		];
	
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


//GO.notes.NotesGrid = Ext.extend(GO.grid.LiveGridPanel,{
//	
//	initComponent : function() {
//
//		var store = new GO.data.LiveStore({
//			url: GO.url('notes/note/store'),		
//			fields: ['id','category_id','user_name','ctime','mtime','name','content'],
//			model:"GO_Notes_Model_Note"
//			
//		});
//		
//		this.searchField = new GO.form.SearchField({
//			store: store,
//			width:320
//		})
//		
//		Ext.apply(this,{
//			title : GO.notes.lang.notes,
//			tbar : [GO.lang['strSearch'] + ':', this.searchField],
//			store : store,
//			//paging : true,
//			columns : [
//			{
//				header: GO.lang.strName,
//				dataIndex: 'name',
//				sortable: true
//			},{
//				header: GO.lang.strOwner,
//				dataIndex: 'user_name',
//				sortable: false,
//				hidden:true
//			},{
//				header: GO.lang.strCtime,
//				dataIndex: 'ctime',
//				hidden:true,
//				sortable: true
//			},{
//				header: GO.lang.strMtime,
//				dataIndex: 'mtime',
//				sortable: true
//			}]
//		});
//		
//		GO.notes.NotesGrid.superclass.initComponent.call(this);
//	}
//
//});
