GO.files.FilesGrid = function(config) {
	
	config = config || {};
	config.layout = 'fit';
	config.split  = true;
	config.paging  = true;
	config.autoExpandColumn = 'name';
	config.sm = new Ext.grid.RowSelectionModel();
	config.loadMask = true;
	config.enableDragDrop = true;
	config.ddGroup = 'FilesDD';
//	config.viewConfig = {'forceFit':true};

	GO.files.FilesGrid.superclass.constructor.call(this,config);
};

Ext.extend(GO.files.FilesGrid, GO.grid.GridPanel, {
	applyStoredState : function(saved_state){
		var state = Ext.decode(saved_state);
		GO.files.FilesGrid.superclass.applyState.call(this, state);
		if (this.rendered){
			this.reconfigure(this.store,this.getColumnModel());
			this.getColumnModel().setColumnWidth(0,this.getColumnModel().getColumnWidth(0));
		}
	}

//	getEncodedState : function() {
//		var state = this.getState();
//		console.log(state);
//		var columns = this.getColumnModel().columns;
//		var stateArray = new Array();
//		for (var i=0; i<state.columns.length; i++) {
//			if (!state.columns[i].hidden) {
//				stateArray.push({
//					'dataIndex' : columns[i].dataIndex,
//					'width' : columns[i].width,
//					'sortable' : columns[i].sortable
//				});
//			}
//		}
//		return Ext.encode(stateArray);
//	},
//
//	decodeState: function(encoded_state) {
//		var db_state = Ext.decode(encoded_state);
//		var cur_columns = this.getColumnModel().columns;
//
//		var col_names = new Array();
//		for (var i=0; i<db_state.length; i++)
//			col_names.push(db_state[i].dataIndex);
//
//		var stateArray = new Array();
//		for (var i=0; i<cur_columns.length; i++) {
//			if (this.in_array(cur_columns[i].dataIndex,col_names)) {
//				var col_specs = this.get_column_specs(cur_columns[i].dataIndex,db_state);
//				if (i==0)	var id = 'name';
//				else var id = i;
//				stateArray.push({
//					'id': id,
//					'sortable': col_specs.sortable,
//					'width': col_specs.width
//				});
//			} else {
//				var col_specs = this.get_column_specs(cur_columns[i].dataIndex,cur_columns);
//				if (col_specs!=false) {
//					if (i==0)	var id = 'name';
//					else var id = i;
//					stateArray.push({
//						'id': id,
//						'sortable': col_specs.sortable,
//						'width': col_specs.width,
//						'hidden': true
//					});
//				}
//			}
//		}
//
//		return {'columns': stateArray};
//	},
//
//	in_array: function (needle, haystack) {
//		for (var i=0; i<haystack.length; i++) {
//			if (haystack[i]==needle)
//				return true;
//		}
//		return false;
//	},
//
//	get_column_specs: function (dataIndex, col_specs) {
//		for (var i=0; i<col_specs.length; i++) {
//			if (col_specs[i].dataIndex==dataIndex)
//				return col_specs[i];
//		}
//		return false;
//	}
});