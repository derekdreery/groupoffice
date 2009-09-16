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
 
GO.grid.SearchPanel = function(config){

	config = config || {};	
	
	if(!this.query)
	{
		this.query='';
	}	
	
	config.border=false;
	if(!config.noTitle)
  	config.title=GO.lang['strSearch']+': "'+Ext.util.Format.htmlEncode(this.query)+'"';
  	
	config.closable=true;
	config.iconCls='go-search-icon-tab';
	config.layout='border';

	this.filterPanel = new GO.LinkTypeFilterPanel({
		region:'west',
		collapsible:true,
		split:true,
		border:true,
		width:120
	});
	
	this.filterPanel.on('change', function(grid, types){		
		this.searchGrid.store.baseParams.types = Ext.encode(types);
		this.searchGrid.store.load();
		delete this.searchGrid.store.baseParams.types;
	}, this);
	
	
	this.store = new GO.data.JsonStore({
		url: BaseHref+'json.php',			
		baseParams: {task: "links", link_id: this.link_id, link_type: this.link_type, folder_id: this.folder_id, type_filter:'true'},
		root: 'results',
		totalProperty: 'total',
		id: 'link_and_type',
		fields: ['icon','link_and_type', 'link_type','name','type','url','mtime','id','module', 'description', 'iconCls'],
		remoteSort: true
	});
	
	this.searchField = new GO.form.SearchField({
		store: this.store,
		width:320
  });
	
  var gridConfig = {
		border:true,
		region:'center',
		tbar:[
	    GO.lang['strSearch']+': ', ' ',this.searchField,
	    '-',{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.searchGrid.deleteSelected();
				},
				scope: this
			}],
		store:this.store,
		columns:[{
				id:'name',
	      header: GO.lang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;',
				sortable: true,
				renderer:function(v, meta, record){
					return '<div class="go-grid-icon '+record.data.iconCls+'">'+v+'</div>';
				}
	    },{
		    header: GO.lang['strType'],
				dataIndex: 'type',
		    sortable:true,
		    width:100
	   	},{
	      header: GO.lang['strMtime'],
				dataIndex: 'mtime',
	      sortable:true,
	      width:100
	    }],
	 	autoExpandMax:2500,
		autoExpandColumn:'name',
		paging:true,
		layout:'fit',
		view:new Ext.grid.GridView({
			enableRowBody:true,
			showPreview:true,			
			emptyText:GO.lang.strNoItems,	
			getRowClass : function(record, rowIndex, p, store){
		    if(this.showPreview && record.data.description.length){
		        p.body = '<div class="go-links-panel-description">'+record.data.description+'</div>';
		        return 'x-grid3-row-expanded';
		    }
		    return 'x-grid3-row-collapsed';
			}
		}),
		loadMask:{msg: GO.lang['waitMsgLoad']},
		sm:new Ext.grid.RowSelectionModel({})
	};
	
		this.searchGrid = new GO.grid.GridPanel(gridConfig);
	
	this.searchGrid.store.setDefaultSort('mtime', 'desc');
	if(!config.noTitle)
	{
		this.searchGrid.store.on('load', function(){
	  	this.setTitle(GO.lang['strSearch']+': "'+Ext.util.Format.htmlEncode(this.searchGrid.store.baseParams.query)+'"');
	  	}, this);
	}
 
	if(!config.noOpenLinks)
	{
	  this.searchGrid.on('rowdblclick', function(grid, rowClicked, e) {
	
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			if(GO.linkHandlers[record.data.link_type])
			{
				GO.linkHandlers[record.data.link_type].call(this, record.data.id, record);
			}else
			{
				Ext.Msg.alert(GO.lang['strError'], 'No handler definded for link type: '+record.data.link_type);
			}
		}, this);
	}
  config.items=[this.filterPanel, this.searchGrid];

	if(config.noOpenLinks)
	{
		config.items.push({
			region:'south',
			height:34,
			layout:'form',
			cls:'go-form-panel',
			split:true,
			items:this.linkDescriptionField = new GO.form.LinkDescriptionField({
				name:'description',
				fieldLabel:GO.lang.strDescription,
				anchor:'100%'
			})
		});
	}


		
  GO.grid.SearchPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.grid.SearchPanel, Ext.Panel, {

	
	afterRender : function()
	{
		GO.grid.SearchPanel.superclass.afterRender.call(this);	
		this.load();
	},
	
	load : function(){
		if(!GO.linkTypeStore.loaded)
		{
			GO.linkTypeStore.load({
				scope:this,
				callback:function(){
					this.load();
				}
			});
		}else
		{		
			this.searchField.setValue(this.query);
	  	this.searchGrid.store.baseParams.query=this.query;
			this.searchGrid.store.load();
		}
	},	
	
	iconRenderer : function(src,cell,record){
		return '<div class=\"go-icon ' + record.data.iconCls +' \"></div>';
	}
});