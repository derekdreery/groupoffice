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
//$uniqid = uniqid();
?>
<div id="linkstoolbar_<?php echo $uniqid; ?>"></div>
<div id="links_grid_div_<?php echo $uniqid; ?>"></div>

<script type="text/javascript">



links = function(){

	var linksPanel;
	var dialog;
	var links_grid;
	var links_ds;
	var links_loaded;
	var user_form;
	var reader;

	return {

		getGridPanel : function(link_id, uniqid){
			var linkstb = new Ext.Toolbar('linkstoolbar_'+uniqid);


			linkstb.addButton({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);

			linkstb.addButton({
				id: 'unlink',
				icon: GOimages['unlink'],
				text: GOlang['cmdUnlink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);

			links_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'links_json.php?link_id='+link_id
				}),


				// turn on remote sorting
				remoteSort: true
			});
			links_ds.setDefaultSort('mtime', 'desc');


			function IconRenderer(src){
				return '<img src=\"' + src +' \" />';
			}

			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var links_cm = new Ext.grid.ColumnModel([
			{
				header:"",
				width:28,
				dataIndex: 'icon',
				renderer: IconRenderer
			},{
				header: GOlang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: GOlang['strType'],
				dataIndex: 'type'
			},{
				header: GOlang['strMtime'],
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			links_cm.defaultSortable = true;

			// create the editor grid
			links_grid = new Ext.grid.Grid('links_grid_div_'+uniqid, {
				ds: links_ds,
				cm: links_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true,
				displayInfo: true,
				displayMsg: GOlang['displayingItems'],
				emptyMsg: GOlang['strNoItems']

			});

			//grid.addListener("rowclick", this.rowClicked, this);
			links_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);

			
			linksPanel = new Ext.ContentPanel('links_tab_'+uniqid, { title: 'Links'});
			
			

		},
		loadLinks : function()
		{
			if(!links_loaded)
			{
				links_loaded=true;
				links_ds.load();
				links_grid.render();
			}
		},
		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = links_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.Ext.get('dialog').load({url: record.data['url'], scripts: true });
			parent.GroupOffice.showDialog({url: record.data['url'], scripts: true });
		}
	}
}();

</script>