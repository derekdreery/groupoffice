Notes = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;


	return {

		init : function(){
			layout = new Ext.BorderLayout(document.body, {
				south: {
					split:true,
					initialSize: 250,
					minSize: 100,
					maxSize: 400,
					autoScroll:true,
					collapsible:true,
					titlebar: true,
					animate: true
				},
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true
				}
			});

			layout.beginUpdate();
			previewPanel = new Ext.ContentPanel('no-south', 'Preview');
			layout.add('south', previewPanel);


			Ext.QuickTips.init();



			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'notes_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'id'
				}, [
				{name: 'id', mapping: 'id'},
				{name: 'name', mapping: 'name'},
				{name: 'mtime', mapping: 'mtime'}
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
			grid = new Ext.grid.Grid('no-center', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});

			grid.addListener("rowclick", this.rowClicked, this);



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


			var tb = new Ext.Toolbar('toolbar');
			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				text: 'Delete',
				tooltip: {text:'Delete the selected items', title:'Tip Title'},
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
				})
			);
			
			tb.add(new Ext.Toolbar.Button({
				id: 'add',
				text: 'Add',
				tooltip: {text:'Add a new note', title:'Tip Title'},
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
				})
			);




			layout.add('center', new Ext.GridPanel(grid, {title: 'Notes', toolbar: tb}));

			layout.getRegion('south').collapse();
			layout.endUpdate();
		},

		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'delete':
					var selectedRows = grid.selModel.selections.keys;
									
					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {task: 'delete', selectedRows: Ext.encode(selectedRows)},
						callback: Notes.handleDeleteResponse,
						scope: Notes
					});
				break;
				
				case 'add':
					document.location='note.php?return_to='+document.location;
				break;
			}
		},
		
		handleDeleteResponse : function(options, success, response)
		{
	
			if(!success)
			{
				alert('Failed to delete the items');
			}else
			{
				//alert(response['responseText']);
				ds.reload();
			}
		},

		rowClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();

			var south = layout.getRegion('south');

			previewPanel.load({url: 'note.php?note_id='+record.data['id'], callback: south.expand()});
			


			
		}
	};

}();
Ext.EventManager.onDocumentReady(Notes.init, Notes, true);