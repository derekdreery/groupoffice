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
GO.summary.AnnouncementsViewGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.cls='go-grid3-hide-headers go-html-formatted';
	config.border=false;
	//config.layout='fit';
	config.autoHeight=true;
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.summary.url+ 'json.php',
	    baseParams: {
	    	task: 'announcements',
	    	active:'true'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','user_name','due_time','ctime','mtime','title', 'content'],
	    remoteSort: true
	});
	var columnModel =  new Ext.grid.ColumnModel([
	  {
			header: '', 
			dataIndex: 'title',
		  sortable: false,
		  renderer: function(value, p, record) {
        return '<b>'+value+'</b>';               
    	}
	  }
	]);

	config.cls='go-colored-table';
	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		enableRowBody:true,
    showPreview:true,
    forceFit:true,
    autoFill: true,
    getRowClass : function(record, rowIndex, p, ds) {

				var cls = rowIndex%2 == 0 ? 'odd' : 'even';

        if (this.showPreview) {
            p.body = '<div class="description">' +record.data.content + '</div>';
            return 'x-grid3-row-expanded '+cls;
        }
        return 'x-grid3-row-collapsed';
    },
		emptyText: GO.lang['strNoItems']		
	});
	//config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.disableSelection=true;
	
	GO.summary.AnnouncementsViewGrid.superclass.constructor.call(this, config);
};
Ext.extend(GO.summary.AnnouncementsViewGrid, GO.grid.GridPanel,{
	afterRender : function(){
		GO.summary.AnnouncementsViewGrid.superclass.afterRender.call(this);
		
		Ext.TaskMgr.start({
		    run: function(){this.store.load();},
		    scope:this,
		    interval:180000
		});  
	}
	
});
