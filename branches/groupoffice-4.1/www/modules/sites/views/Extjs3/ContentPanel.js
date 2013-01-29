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

	
	load : function(site_id){
		this.store.baseParams.site_id=site_id;
		this.store.load();
	},
	constructor : function(config){
		config = config || {};
		
		config.id='sites-content';
		config.title = GO.sites.lang.content;
		config.layout='fit';
		config.autoScroll=true;
		config.split=true;
		config.store = new GO.data.JsonStore({
			url: GO.url('sites/content/store'),		
			fields: ['id','title','slug'],
			baseParams:{
				site_id:0
			},
			remoteSort: true,
			model:"GO_Sites_Model_Content"
		});
	
		config.columns=[
		{
			header: GO.sites.lang.contentTitle,
			dataIndex: 'title',
			sortable: true
		},
		{
			header: GO.sites.lang.contentSlug,
			dataIndex: 'slug',
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
		
		config.standardTbar=true;


		GO.sites.ContentPanel.superclass.constructor.call(this, config);
	},
	editDialogClass:GO.sites.ContentDialog,
	relatedGridParamName:'site_id'
});

