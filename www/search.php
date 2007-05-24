<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once("Group-Office.php");
$GO_SECURITY->authenticate();
load_basic_controls();

$query = isset($_REQUEST['query']) ? smart_addslashes($_REQUEST['query']) : '';

$div = new html_element('div');
$div->set_attribute('id','searchgrid');

echo $div->get_html();
?>
<script type="text/javascript">

alert('ja');

Search = function(element, config) {
	var search_grid;
	

	return {

		render : function(){

			var search_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'links_json.php'
				}),
				
				baseParams: {"query": config['query']},

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
			search_grid = new Ext.grid.Grid(element, {
				ds: search_ds,
				cm: search_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true				
				
			});

			//grid.addListener("rowclick", this.rowClicked, this);
			search_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


			// trigger the data store load
			search_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

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
		},
		
		rowDoulbleClicked : function(search_grid, rowClicked, e) {
			
			var selectionModel = search_grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			document.location=record.data['url'];
		}
	}
};

var search = new Search('searchgrid',{ query: '%' });
search.render();

</script>