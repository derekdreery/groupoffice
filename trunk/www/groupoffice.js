GroupOffice = function(){
	var layout;
	var mainPanel;
	var navPanel;
	var innerLayout;
	var mainmenutb;
	var search_ds;
	var dialog;
	var linksDialog;
	var search_links_ds;
	var fromlinks;

	return {

		init : function(){
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				north: {
					split:false,
					initialSize: 25,
					titlebar: false
				},
				west: {
					split:true,
					initialSize: 200,
					minSize: 175,
					maxSize: 400,
					titlebar: false,
					collapsible: true,
					animate: true
				},
				east: {
					split:true,
					initialSize: 400,
					minSize: 200,
					maxSize: 600,
					titlebar: true,
					collapsible: true,
					closable: true,
					animate: true
				},
				center: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true
				}
			});

			layout.beginUpdate();
			layout.add('north', new Ext.ContentPanel('north'));





			search_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'links_json.php'
				}),

				baseParams: {"query": ''},

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'link_id', mapping: 'link_id'},
				{name: 'name', mapping: 'name'},
				{name: 'type', mapping: 'type'},
				{name: 'url', mapping: 'url'},
				{name: 'mtime', mapping: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			search_ds.setDefaultSort('mtime', 'desc');



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var search_cm = new Ext.grid.ColumnModel([{
				header: "Name",
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: "Type",
				dataIndex: 'type'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			search_cm.defaultSortable = true;

			// create the editor grid
			search_grid = new Ext.grid.Grid('searchgrid', {
				ds: search_ds,
				cm: search_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true

			});

			//grid.addListener("rowclick", this.rowClicked, this);
			search_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


			// trigger the data store load
			//search_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

			// render it
			search_grid.render();

			var searchGridFoot = search_grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var search_paging = new Ext.PagingToolbar(searchGridFoot, search_ds, {
				pageSize: GOsettings['max_rows_list'],
				displayInfo: true,
				displayMsg: 'Displaying notes {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			});
			
			var searchtb = new Ext.Toolbar('searchtoolbar');

			var save_button =searchtb.addButton({
				id: 'close',
				icon: GOimages['close'],
				text: GOlang['cmdClose'],
				cls: 'x-btn-text-icon',
				handler: this.closeSearchPanel
			}
			);

			var gridPanel = new Ext.GridPanel(search_grid, { title: 'Search results', toolbar: searchtb });



			layout.add('east', gridPanel);

			innerLayout = new Ext.BorderLayout('west', {
				south: {
					split:true,
					initialSize: 250,
					minSize: 100,
					maxSize: 400,
					autoScroll:true,
					titlebar: false
				},
				center: {
					autoScroll:true,
					titlebar:false
				}
			});
			// add the nested layout
			navLayout = new Ext.NestedLayoutPanel(innerLayout, 'Group-Office');
			layout.add('west', navLayout);

			innerLayout.beginUpdate();
			innerLayout.add('south', new Ext.ContentPanel('southwest'));

			navPanel = new Ext.ContentPanel('northwest');

			innerLayout.add('center', navPanel);



			// restore innerLayout state
			//innerLayout.restoreState();
			innerLayout.endUpdate(true);

			mainPanel = new Ext.ContentPanel('center');

			layout.add('center', mainPanel);

			layout.getRegion('east').hide();

			layout.endUpdate();

			Ext.QuickTips.init();
			//Ext.QuickTips.register({title: 'Play', qtip: 'The summary displays relevant info', target: 'summary', autoHide:true});

		},
		closeSearchPanel : function()
		{
			layout.getRegion('east').hide();
		},

		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = search_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.mainframe.document.location=record.data['url'];
			//this.showDialog({ url: record.data['url'], iframe: true });
			//layout.getRegion('east').collapse();
			
			Ext.get('dialog').load({url: record.data['url'], scripts: true });
		},

		search : function(query){
			var east = layout.getRegion('east');
			east.show();
			//east.getPanel('east').load('search.php?query='+escape(query), { scripts: true, nocache: true });

			search_ds.baseParams = {"query": query};

			search_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

			return false;
		},

		showLinks : function(config){


			fromlinks = config['fromlinks'];

			if(config['records'])
			{
				var records=config['records'];
				fromlinks = [];
	
				for (var i = 0;i<records.length;i++)
				{
					fromlinks.push({ 'link_id' : records[i].data['link_id'], 'link_type' : records[i].data['link_type'] });
				}
			}else
			{
				fromlinks = config['fromlinks'];
			}
			
			if(!linksDialog)
			{
				linksDialog = new Ext.BasicDialog("search_links_dialog", {
					shadow:false,
					draggable: true,
					modal:true,
					title: 'Title',
					resizable:false,
					style: 'width:'+config['width']+';height:'+config['height'],
					width: 600,
					height:420
				});



				search_links_ds = new Ext.data.Store({

					proxy: new Ext.data.HttpProxy({
						url: 'links_json.php'
					}),

					baseParams: {"query": ''},

					reader: new Ext.data.JsonReader({
						root: 'results',
						totalProperty: 'total',
						id: 'link_id'
					}, [
					{name: 'link_id', mapping: 'link_id'},
					{name: 'link_type', mapping: 'link_type'},
					{name: 'name', mapping: 'name'},
					{name: 'type', mapping: 'type'},
					{name: 'url', mapping: 'url'},
					{name: 'mtime', mapping: 'mtime'}
					]),

					// turn on remote sorting
					remoteSort: true
				});
				search_links_ds.setDefaultSort('mtime', 'desc');



				// the column model has information about grid columns
				// dataIndex maps the column to the specific data field in
				// the data store
				var search_links_cm = new Ext.grid.ColumnModel([{
					header: "Name",
					dataIndex: 'name',
					css: 'white-space:normal;'
				},{
					header: "Type",
					dataIndex: 'type'
				},{
					header: "Modified at",
					dataIndex: 'mtime'
				}]);

				// by default columns are sortable
				search_links_cm.defaultSortable = true;

				// create the editor grid
				search_links_grid = new Ext.grid.Grid('search_links_grid', {
					ds: search_links_ds,
					cm: search_links_cm,
					selModel: new Ext.grid.RowSelectionModel(),
					enableColLock:false,
					loadMask: true

				});

				//grid.addListener("rowclick", this.rowClicked, this);
				search_links_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


				// trigger the data store load
				//search_links_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

				// render it
				search_links_grid.render();

				var search_linksGridFoot = search_links_grid.getView().getFooterPanel(true);

				// add a paging toolbar to the grid's footer
				var search_links_paging = new Ext.PagingToolbar(search_linksGridFoot, search_links_ds, {
					pageSize: GOsettings['max_rows_list'],
					displayInfo: true,
					displayMsg: 'Displaying notes {0} - {1} of {2}',
					emptyMsg: "No topics to display"
				});



				linksDialog.addKeyListener(27, linksDialog.hide, linksDialog);
				linksDialog.addButton('Submit', this.linkItems, this);
				linksDialog.addButton('Close', linksDialog.hide, linksDialog);

			}
			linksDialog.show();
		},
		searchLinks : function(query){
			search_links_ds.baseParams = {"query": query};

			search_links_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});
		},
		searchLinksKeyEvent : function(e){

			var keynum;
			var input;
			input = Ext.get("query");

			if(window.event) // IE
			{
				keynum = e.keyCode
			}else if(e.which) // Netscape/Firefox/Opera
			{
				keynum = e.which
			}

			if(keynum==13)
			{
				this.searchLinks(input.getValue());
			}
			return true;
		},
		linkItems : function()	{
			var selectionModel = search_links_grid.getSelectionModel();
			var records = selectionModel.getSelections();

			var tolinks = [];

			for (var i = 0;i<records.length;i++)
			{
				tolinks.push({ 'link_id' : records[i].data['link_id'], 'link_type' : records[i].data['link_type'] });
			}

			var conn = new Ext.data.Connection();
			conn.request({
				url: 'action.php',
				params: {task: 'link', fromLinks: Ext.encode(fromlinks), toLinks: Ext.encode(tolinks)},
				callback: function(options, success, response)
				{
					if(!success)
					{
						Ext.MessageBox.alert('Failed', response.result.errors);
					}else
					{
						linksDialog.hide();
					}
				}
			});
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
						Ext.MessageBox.alert('Failed', response.result.errors);
					}else
					{
						return true;
					}
				}
			});
		}


	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
