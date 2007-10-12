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
 
Ext.app.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
    initComponent : function(){
        Ext.app.SearchField.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e){
            if(e.getKey() == e.ENTER){
                this.onTrigger2Click();
            }
        }, this);
    },

    validationEvent:false,
    validateOnBlur:false,
    trigger1Class:'x-form-clear-trigger',
    trigger2Class:'x-form-search-trigger',
    hideTrigger1:true,
    width:180,
    hasSearch : false,
    paramName : 'query',

    onTrigger1Click : function(){
        if(this.hasSearch){
            var o = {start: 0};
            o[this.paramName] = '';
            this.store.reload({params:o});
            this.el.dom.value = '';
            this.triggers[0].hide();
            this.hasSearch = false;
        }
    },

    onTrigger2Click : function(){
        var v = this.getRawValue();
        if(v.length < 1){
            this.onTrigger1Click();
            return;
        }
        var o = {start: 0};
        o[this.paramName] = v;
        this.store.reload({params:o});
        this.hasSearch = true;
        this.triggers[0].show();
    }
});

/*
 * 
 * Params:
 * 
 * linksStore: store to reload after items are linked
 * gridRecords: records from grid to link. They must have a link_id and link_type
 * fromLinks: array with link_id and link_type to link
 */
Ext.LinksDialog = function(config){
	
	Ext.apply(this, config);
	

			

	this.store = new Ext.data.Store({
       	proxy: new Ext.data.HttpProxy({
			url: BaseHref+'links_json.php'
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
		remoteSort: true,
        baseParams: {limit:20}
    });

	
	this.grid = new Ext.grid.GridPanel({
		    store: this.store,
		    columns: [{
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
	    }],
		    sm: new Ext.grid.RowSelectionModel(),
			
		});
		
	this.SearchField = new Ext.app.SearchField({
                store: this.store,
                width:320
            });


    var panel = new Ext.Panel({
        autoScroll:true,
        layout: 'fit',

        items: this.grid,

        tbar: [
            GOlang['strSearch']+': ', ' ',
            this.SearchField
        ],

        bbar: new Ext.PagingToolbar({
            store: this.store,
            pageSize: 20,
            displayInfo: true,
            displayMsg: GOlang['displayingItems'],
            emptyMsg: GOlang['strNoItems']
        })
    });

    //this.store.load({params:{start:0, limit:20}});
			
			
			
	
	
	Ext.Window.superclass.constructor.call(this, {
    	layout: 'fit',
		modal:false,
		shadow:false,
		minWidth:300,
		minHeight:300,
		height:400,
		width:600,
		plain:true,
		closeAction:'hide',
		title:GOlang['strLinkItems'],
		

		
		items: [
			panel
		],
		
		buttons: [
			{
				id: 'ok',
				text: GOlang['cmdOk'],
				handler: function(){							
					this.linkItems();
				},
				scope:this
			},
			{
				id: 'close',
				text: GOlang['cmdClose'],
				handler: function(){this.hide();},
				scope: this
			}
		]
    });
};

Ext.extend(Ext.LinksDialog, Ext.Window, {
	
	IconRenderer : function(src){
		return '<img src=\"' + src +' \" />';
	},
	
	setLinkRecords : function(gridRecords)
	{
		this.fromLinks = [];
		for (var i = 0;i<gridRecords.length;i++)
		{
			this.fromLinks.push({ 'link_id' : gridRecords[i].data['link_id'], 'link_type' : gridRecords[i].data['link_type'] });
		}
	},
	setSingleLink : function(link_id, link_type)
	{
		this.fromLinks=[{"link_id":link_id,"link_type":link_type}];
	},
	
	show : function()
	{
		
		Ext.LinksDialog.superclass.show.call(this);
		//If I don't put a 100ms delay it doesn't work in Firefox 2.0 on Linux
		this.SearchField.focus.defer(100, this.SearchField,[true]);
	},
	
	linkItems : function()	{
		var selectionModel = this.grid.getSelectionModel();
		var records = selectionModel.getSelections();

		var tolinks = [];

		for (var i = 0;i<records.length;i++)
		{
			tolinks.push({ 'link_id' : records[i].data['link_id'], 'link_type' : records[i].data['link_type'] });
		}

		var conn = new Ext.data.Connection();
		conn.request({
			url: BaseHref+'action.php',
			params: {task: 'link', fromLinks: Ext.encode(this.fromLinks), toLinks: Ext.encode(tolinks)},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
				}else
				{
					if(this.linksStore)
					{
						this.linksStore.reload();
					}
					this.hide();
				}
			},
			scope: this
		});
	}
});


