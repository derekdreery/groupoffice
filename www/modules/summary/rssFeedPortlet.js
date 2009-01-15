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

GO.portlets.rssFeedPortlet = function(config) {
    Ext.apply(this, config);

		if(!this.feed)
		{
			this.feed = 'http://www.nu.nl/deeplink_rss2/index.jsp?r=Algemeen';
		}
    this.store = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({        		
            url: GO.settings.modules.summary.url+'feed_proxy.php'
        }),
        baseParams: {feed: this.feed},

        reader: new Ext.data.XmlReader(
            {record: 'item'},
            ['title', 'author', {name:'pubDate', type:'date'}, 'link', 'description', 'content']
        )
    });
    this.store.setDefaultSort('pubDate', "DESC");

    this.columns = [{
        id: 'title',
        header: GO.lang.strTitle,
        dataIndex: 'title',
        sortable:true,
        width: 420,
        renderer: this.formatTitle
      },{
        header: GO.lang.author,
        dataIndex: 'author',
        width: 100,
        hidden: true,
        sortable:true
      },{
        id: 'last',
        header: GO.lang.strDate,
        dataIndex: 'pubDate',
        width: 150,
        renderer:  this.formatDate,
        sortable:true
    }];

    GO.portlets.rssFeedPortlet.superclass.constructor.call(this, {
        loadMask: {msg:GO.summary.lang.loadingFeed},
        sm: new Ext.grid.RowSelectionModel({
            singleSelect:true
        }),
        viewConfig: {
            forceFit:true,
            enableRowBody:true,
            showPreview:true,
            getRowClass : this.applyRowClass
        }
    });

    this.on('rowcontextmenu', this.onContextClick, this);
};

Ext.extend(GO.portlets.rssFeedPortlet, Ext.grid.GridPanel, {

		afterRender : function(){
			GO.portlets.rssFeedPortlet.superclass.afterRender.call(this);
			
			this.on('rowDblClick', this.rowDoubleClick, this);
			//this.store.load();
			
			
		},
		
		rowDoubleClick : function(grid, index, e) {
			var record = this.store.getAt(index);
			
			window.open(record.data.link);
			
		},
    onContextClick : function(grid, index, e){
        if(!this.menu){ // create context menu on first right click
            this.menu = new Ext.menu.Menu({
                id:'grid-ctx',
                items: [
                {
                    iconCls: 'new-win',
                    text: GO.summary.lang.goToPost,
                    scope:this,
                    handler: function(){
                        window.open(this.ctxRecord.data.link);
                    }
                },'-',{
                    iconCls: 'refresh-icon',
                    text:GO.lang.cmdRefresh,
                    scope:this,
                    handler: function(){
                        this.ctxRow = null;
                        this.store.reload();
                    }
                }]
            });
            this.menu.on('hide', this.onContextHide, this);
        }
        e.stopEvent();
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
        this.ctxRow = this.view.getRow(index);
        this.ctxRecord = this.store.getAt(index);
        Ext.fly(this.ctxRow).addClass('x-node-ctx');
        this.menu.showAt(e.getXY());
    },

    onContextHide : function(){
        if(this.ctxRow){
            Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
    },

    loadFeed : function(url) {
        this.store.baseParams = {
            feed: url
        };
        
        Ext.TaskMgr.start({
				    run: this.store.load,
				    scope:this.store,
				    interval:1800000
				});       
    },

    // within this function "this" is actually the GridView
    applyRowClass: function(record, rowIndex, p, ds) {
        if (this.showPreview) {
            p.body = '<p class="description">' +record.data.description + '</p>';
            return 'x-grid3-row-expanded';
        }
        return 'x-grid3-row-collapsed';
    },

    formatDate : function(date) {
        if (!date) {
            return '';
        }
        var now = new Date();
        var d = now.clearTime(true);
        var notime = date.clearTime(true).getTime();
        if (notime == d.getTime()) {
            return GO.summary.lang.today + date.dateFormat('g:i a');
        }
        d = d.add('d', -6);
        if (d.getTime() <= notime) {
            return date.dateFormat('D g:i a');
        }
        return date.dateFormat('n/j g:i a');
    },

    formatTitle: function(value, p, record) {
        return '<div class="topic"><b>'+value+'</b></div>';
               
    }
});