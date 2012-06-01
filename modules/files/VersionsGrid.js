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

GO.files.VersionsGrid = function(config) {

	if (!config) {
		config = {};
	}

	config.title = GO.files.lang.olderVersions;
	config.layout = 'fit';
	config.autoScroll = true;
	config.split = true;
	config.store = new GO.data.JsonStore({
				url : GO.settings.modules.files.url + 'json.php',
				baseParams : {
					'task' : 'versions'
				},
				root : 'results',
				totalProperty : 'total',
				id : 'path',
				fields : ['path', 'name', 'type', 'size', 'mtime',
						'grid_display', 'extension', 'timestamp', 'thumb_url'],
				remoteSort : true
			});
			
	var nameId = Ext.id();

	config.paging = true;
	var columnModel = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[{
				header : GO.lang['strName'],
				dataIndex : 'grid_display',
				sortable : true,
				id: nameId
			}, {
				header : GO.lang.strSize,
				dataIndex : 'size',
				width:80,
				sortable : true
			}, {
				header : GO.lang.strMtime,
				dataIndex : 'mtime',
				sortable : true,
				width:100
			}]
});
	
	config.cm = columnModel;
	
	config.autoExpandColumn=nameId;

	config.view = new Ext.grid.GridView({
				emptyText : GO.lang['strNoItems']
			});
	config.sm = new Ext.grid.RowSelectionModel();
	config.loadMask = true;

	GO.files.VersionsGrid.superclass.constructor.call(this, config);

	this.on('rowdblclick', function(grid, rowIndex) {
				var record = grid.getStore().getAt(rowIndex);
				GO.files.openFile(record);
			}, this);

};

Ext.extend(GO.files.VersionsGrid, GO.grid.GridPanel, {

			onShow : function() {
				GO.files.VersionsGrid.superclass.onShow.call(this);
				this.store.load();
			},

			setFileID : function(file_id) {
				this.store.baseParams.file_id = file_id
				this.store.loaded = false;
			}

		});