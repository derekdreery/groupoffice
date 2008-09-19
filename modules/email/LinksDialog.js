/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: LinksDialog.js 2955 2008-09-03 11:37:22Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * 
 * Params:
 * 
 * linksStore: store to reload after items are linked
 * gridRecords: records from grid to link. They must have a link_id and link_type
 * fromLinks: array with link_id and link_type to link
 */
 
GO.email.LinksDialog = function(config){
	
	Ext.apply(this, config);

	this.store = new GO.data.JsonStore({
   	url: BaseHref+'json.php',
    root: 'results',
		totalProperty: 'total',
		//id: 'link_id',
		fields: ['icon','link_type','name','type','url','mtime','id','module'],
		remoteSort: true,
    baseParams: {task: 'links'}
    });
    
  this.SearchField = new GO.form.SearchField({
		store: this.store,
		width:320,
		id: 'email-links-search'
    });

	
	this.grid = new GO.grid.GridPanel({
			paging:true,
	    store: this.store,
	    view: new Ext.grid.GridView({
    		autoFill: true,
    		forceFit: true}),
	    columns: [{
       	header: "",
       	width:28,
				dataIndex: 'icon',
				renderer: this.IconRenderer
	    },{
	       	header: GO.lang.strName,
				dataIndex: 'name',
				css: 'white-space:normal;',
				sortable: true
	    },{
		    header: GO.lang.strType,
				dataIndex: 'type',
		    sortable:true
	   	},{
	      header: GO.lang.strMtime,
				dataIndex: 'mtime',
	      sortable:true
    	}],
	    sm: new Ext.grid.RowSelectionModel(),
		 	tbar: [
	            GO.lang.strSearch+': ', ' ',
	            this.SearchField
	        ]
			
		});
		
	

	
	Ext.Window.superclass.constructor.call(this, {
    layout: 'fit',
		modal:false,
		minWidth:300,
		minHeight:300,
		height:400,
		width:600,
		plain:true,
		closeAction:'hide',
		title:GO.lang.strLinkItems,
		items: this.grid,
		focus: function(){
			Ext.getCmp('email-links-search').focus(true);
		},
		buttons: [
			{
				id: 'ok',
				text: GO.lang.cmdOk,
				handler: function(){							
					this.linkItems();
				},
				scope:this
			},
			{
				id: 'close',
				text: GO.lang.cmdClose,
				handler: function(){this.hide();},
				scope: this
			}
		]
    });
};

Ext.extend(GO.email.LinksDialog, Ext.Window, {
	
	IconRenderer : function(src,cell,record){
		return '<div class=\"go-link-icon-' + record.data.link_type +' \"></div>';
	},
	
	
	linkItems : function()	{
		var selectionModel = this.grid.getSelectionModel();
		var records = selectionModel.getSelections();

		var links = [];

		for (var i = 0;i<records.length;i++)
		{
			links.push({ 'link_id' : records[i].data['id'], 'link_type' : records[i].data['link_type'] });
		}
		
	
		var uids = this.messagesGrid.selModel.selections.keys;		
		var mailbox = this.messagesGrid.store.baseParams.mailbox;
		var account_id = this.messagesGrid.store.baseParams.account_id;
		
		

		Ext.Ajax.request({
			url: GO.settings.modules.mailings.url+'action.php',
			params: {
				task: 'link_messages', 
				links: Ext.encode(links), 
				uids: Ext.encode(uids),
				mailbox: mailbox,
				account_id: account_id
				},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang.strError, response.result.errors);
				}else
				{					
					this.hide();
				}
			},
			scope: this
		});
	}
});


