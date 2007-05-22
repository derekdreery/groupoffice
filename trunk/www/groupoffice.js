GroupOffice = function(){
	var layout;
	var mainPanel;
	var navPanel;
	var innerLayout;
	var mainmenutb;
	var search_ds;
	var dialog;

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
			search_grid = new Ext.grid.Grid('east', {
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

			var gridPanel = new Ext.GridPanel(search_grid, { title: 'Search results' });



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

			layout.getRegion('east').collapse();

			layout.endUpdate();

			Ext.QuickTips.init();
			//Ext.QuickTips.register({title: 'Play', qtip: 'The summary displays relevant info', target: 'summary', autoHide:true});

		},

		setCenterUrl : function(url){
			mainPanel.load({
				url: url, scripts: true});
		},

		setNavUrl : function(url){
			navPanel.load({
				url: url});
		},

		getNavPanel : function(){
			return navPanel;
		},

		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = search_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.mainframe.document.location=record.data['url'];
			this.showDialog(record.data['url']);
			//layout.getRegion('east').collapse();
		},

		search : function(query){
			var east = layout.getRegion('east');
			east.expand();
			//east.getPanel('east').load('search.php?query='+escape(query), { scripts: true, nocache: true });
			
			search_ds.baseParams = {"query": query};
			
			search_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});
			
			return false;
		},
		
		showDialog : function(url){
			
			var dialogdiv = Ext.get('dialog');
			dialogdiv.update('');
			dialogdiv.setStyle('width:100%;height:100%');
			
            //if(!dialog){ // lazy initialize the dialog and only create it once
                dialog = new Ext.BasicDialog("dialog", { 
                        shadow:false,                        
                        draggable: true,
                        modal:true,
                        title: 'Title',                        
                        resizable:false,
                        style: 'height:100%;width:100%'
                });
                
                var iframe= Ext.DomHelper.append(dialog.body, {tag: 'iframe', id: 'dialogFrame', frameBorder: 0, src: url, style:'width:100%;height:100%'});
                
                dialog.addKeyListener(27, dialog.hide, dialog);
                dialog.addButton('Submit', dialog.hide, dialog).disable();
                dialog.addButton('Close', dialog.hide, dialog);
            //}
            //parent.dialogframe.document.location=url;
            dialog.show();
        }


	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
