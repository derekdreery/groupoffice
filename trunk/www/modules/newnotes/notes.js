Notes = function(){
	var layout;
	var mainPanel;
	var navPanel;
	var innerLayout;
	var mainmenutb;

	return {

		init : function(){
			layout = new Ext.BorderLayout(document.body, {
				south: {
					split:true,
					initialSize: 250,
					minSize: 100,
					maxSize: 400,
					autoScroll:false,
					collapsible:true,
					titlebar: true,
					animate: true,
					cmargins: {top:2,bottom:0,right:0,left:0}
				},
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true
				}
			});

			layout.beginUpdate();
			layout.add('south', new Ext.ContentPanel('no-south', 'Preview'));
			layout.add('center', new Ext.ContentPanel('no-center', 'Notes'));
			layout.endUpdate();

			Ext.QuickTips.init();



			var ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'notes_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'id'
				}, [
				{name: 'name', mapping: 'name'},
				{name: 'mtime', mapping: 'Modified at'}
				]),

				// turn on remote sorting
				//remoteSort: true
			});


			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([{
				header: "Name",
				dataIndex: 'name',
				width: 420,
				css: 'white-space:normal;'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			var grid = new Ext.grid.Grid('notes-grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
				enableColLock:false,
				loadMask: true
			});


			// render it
			grid.render();

			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: 10,
				displayInfo: true,
				displayMsg: 'Displaying notes {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			});

			// trigger the data store load
			ds.load({params:{start:0, limit:10}});
		},

		setCenterUrl : function(url){
			mainPanel.load({
				url: url});
		},

		setNavUrl : function(url){
			navPanel.load({
				url: url});
		},

		getNavPanel : function(){
			return navPanel;
		}
	};

}();
Ext.EventManager.onDocumentReady(Notes.init, Notes, true);