/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */
 
Ext.grid.LinksPanel = function(config){
	
	var linksDialog;
	
	Ext.apply(this, config);
	
	if(!this.link_id)
	{
		this.link_id=0;
	}
	
	if(!this.link_type)
	{
		this.link_type=0;
	}
	
	
	
	
	this.store = new Ext.data.Store({

			proxy: new Ext.data.HttpProxy({
				url: BaseHref+'json.php',
				baseParams: {task: "links", "link_id": this.link_id}
			}),
			reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'icon'},
				{name: 'link_id'},
				{name: 'name'},
				{name: 'type'},
				{name: 'url'},
				{name: 'mtime'},
				{name: 'id'},
				{name: 'module'}
				]),

			// turn on remote sorting
			remoteSort: true
		});
	this.store.setDefaultSort('mtime', 'desc');
			
	
	this.linksDialog = new Ext.LinksDialog({linksStore: this.store});

	
	
	this.columns = [{
		       	header: "",
		       	width:28,
				dataIndex: 'icon',
				renderer: this.IconRenderer
		    },{
		       	header: GOlang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;',
				sortable: true
		    },{
			    header: GOlang['strType'],
				dataIndex: 'type',
			    sortable:true
		   	},{
		        header: GOlang['strMtime'],
				dataIndex: 'mtime',
		        sortable:true
		    }];
		    
		    
	this.tbar = [
			{
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: function(){				
					this.linksDialog.show();					
				},
				scope: this
				
			},{
				id: 'unlink',
				icon: GOimages['unlink'],
				text: GOlang['cmdUnlink'],
				cls: 'x-btn-text-icon',
				handler: function() {
					
					var unlinks = [];
	
					var selectionModel = this.getSelectionModel();
					var records = selectionModel.getSelections();
					
					if(records.length>0)
					{
	
						for (var i = 0;i<records.length;i++)
						{
							unlinks.push(records[i].data['link_id']);
						}
						if(this.unlink(this.link_id, unlinks))
						{
							this.store.reload();
						}
					}
				},
				scope: this
			}
		];

    Ext.grid.GridPanel.superclass.constructor.call(this, {
    	region: 'center',
        id: 'topic-grid',
        title: GOlang['strLinks'],
        disabled: true,
        loadMask: {msg: GOlang['waitMsgLoad']},
        sm: new Ext.grid.RowSelectionModel({})
    });
	
}

Ext.extend(Ext.grid.LinksPanel, Ext.grid.GridPanel, {
	
	IconRenderer : function(src){
		return '<img src=\"' + src +' \" />';
	},
	
	loadLinks : function (link_id, link_type)
	{
		if(link_id>0)
		{
			this.setDisabled(false);
		}else
		{
			this.setDisabled(true);
		}
		if(this.link_id!=link_id)
		{	
			this.link_type=link_type;
			this.link_id=link_id;
			this.store.baseParams = {"link_id": link_id};				
			this.store.load();
			
			this.linksDialog.setSingleLink(this.link_id, this.link_type);
		}
	},
	unlink : function(link_id, unlinks)	{

		var conn = new Ext.data.Connection();
		conn.request({
			url: BaseHref+'action.php',
			params: {task: 'unlink', link_id: link_id, unlinks: Ext.encode(unlinks)},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
				}else
				{
					return true;
				}
			}
		});
		return true;
	}

});