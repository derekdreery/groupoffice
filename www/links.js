
// namespace object
var GroupOffice = GroupOffice || {};


GroupOffice.linksGrid = function(element, config) {
	var links_grid;
	

	return {

		render : function(){

			var links_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: '../../links_json.php?link_id='+config['link_id']+'&link_type='+config['link_type']
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'link_id', mapping: 'link_id'},
				{name: 'name', mapping: 'name'},
				{name: 'url', mapping: 'url'},
				{name: 'mtime', mapping: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			links_ds.setDefaultSort('mtime', 'desc');



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var links_cm = new Ext.grid.ColumnModel([{
				header: "Name",
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			links_cm.defaultSortable = true;

			// create the editor grid
			links_grid = new Ext.grid.Grid(element, {
				ds: links_ds,
				cm: links_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true				
				
			});

			//grid.addListener("rowclick", this.rowClicked, this);
			links_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


			// trigger the data store load
			links_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

			// render it
			links_grid.render();

			var linksGridFoot = links_grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var links_paging = new Ext.PagingToolbar(linksGridFoot, links_ds, {
				pageSize: GOsettings['max_rows_list'],
				displayInfo: true,
				displayMsg: 'Displaying notes {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			});
		},
		
		rowDoulbleClicked : function(links_grid, rowClicked, e) {
			
			var selectionModel = links_grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			document.location=record.data['url'];
		}
	}
};





