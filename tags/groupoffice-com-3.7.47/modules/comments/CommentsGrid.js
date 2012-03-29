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
GO.comments.CommentsGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.border=false;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.comments.url+ 'json.php',
	    baseParams: {
	    	task: 'comments'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','link_id','link_type','user_name','ctime','mtime','comments'],
	    remoteSort: true
	});
	
	
	config.store.on('load', function(){		
		this.setWritePermission(this.store.reader.jsonData.write_permission);
		
	}, this);
	
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[{
			header: GO.lang.strOwner, 
			dataIndex: 'user_name',
		  sortable: false,
		  renderer: function(v){
		  	return '<i>'+v+'</i>';
		  }
		},{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime',
			align:'right',
		  renderer: function(v){
		  	return '<b>'+v+'</b>';
		  }
		}]
	});
		
	
	config.cm=columnModel;
	config.viewConfig={
      forceFit:true,
      enableRowBody:true,
      showPreview:true,
      getRowClass : this.applyRowClass
  };
	
	config.disabled=true;
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
		
		
		
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.comments.showCommentDialog();
				GO.comments.commentDialog.formPanel.baseParams.link_id=this.store.baseParams.link_id;
 		 		GO.comments.commentDialog.formPanel.baseParams.link_type=this.store.baseParams.link_type;
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
		
	GO.comments.CommentsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){		
		if(this.writePermission)
		{
			var record = grid.getStore().getAt(rowIndex);
			
			if(!GO.comments.commentDialog)
				GO.comments.commentDialog = new GO.comments.CommentDialog();
			
			GO.comments.commentDialog.show(record.data.id);
		}
	}, this);
};
Ext.extend(GO.comments.CommentsGrid, GO.grid.GridPanel,{
	writePermission : false,
	
	setWritePermission : function(writePermission){
		this.writePermission=writePermission;
		this.getTopToolbar().setDisabled(!writePermission);
	},
	
	afterRender : function(){
		
		GO.comments.commentDialogListeners={
			save:function(){
				this.store.reload();
			},
			scope:this
		};

		
		GO.comments.CommentsGrid.superclass.afterRender.call(this);
	},
	
	applyRowClass: function(record, rowIndex, p, ds) {
      if (this.showPreview) {
          p.body = '<p class="description">' +record.data.comments + '</p>';
          return 'x-grid3-row-expanded';
      }
      return 'x-grid3-row-collapsed';
  },
  setLinkId :  function(link_id, link_type){
  	this.store.baseParams.link_id=link_id;
  	this.store.baseParams.link_type=link_type;
  	
  	
  	this.store.loaded=false;
  	
  	this.setDisabled(link_id<1);
  },
  onShow : function(){
		GO.grid.LinksPanel.superclass.onShow.call(this);
		
		if(!this.store.loaded)
			this.store.load();
  }
});
