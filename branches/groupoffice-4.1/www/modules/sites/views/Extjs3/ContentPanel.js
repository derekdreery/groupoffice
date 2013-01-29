/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: NotesGrid.js 10767 2012-06-12 13:31:03Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.sites.ContentPanel = Ext.extend(GO.grid.GridPanel,{
	constructor : function(config){
		config = config || {};
		
		config.title = GO.sites.lang.content;
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


		GO.sites.ContentPanel.constructor.call(this, config);
	}
});

