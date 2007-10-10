Notes = function(){
	var ds;
	var note_id;
	var save_button;
	var grid;
	var linksDialog;

	return {

		init : function(){

			// initialize state manager, we will use cookies
			//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


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
				{name: 'link_id', mapping: 'link_id'},				
				{name: 'link_type', mapping: 'link_type'},	
				{name: 'name', mapping: 'name'},
				{name: 'mtime', mapping: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			ds.setDefaultSort('name', 'asc');

			grid = new Ext.grid.GridPanel({
				//el:document.body,
				autoSizeColumns: true,
			    store: ds,
			    columns: [		        
			        {header: GOlang['strName'], sortable: true, dataIndex: 'name'},
			        {header: GOlang['strMtime'], sortable: true, dataIndex: 'mtime'}
			    ],
			    sm: new Ext.grid.RowSelectionModel(),
			    height:'100%',
			    iconCls:'icon-grid',
				tbar:[{
		           	id: 'delete',
					icon: GOimages['delete'],
					text: GOlang['cmdDelete'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick,
					scope: this
		        },
		        {
					id: 'add',
					icon: GOimages['add'],
					text: GOlang['cmdAdd'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick,
					scope: this
				},
				{
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick,
					scope: this
				}]
				
			});

	
			grid.addListener("rowdblclick", this.rowDoubleClicked, this);


			
			//ds.on('load', function (){grid.getView().autoSizeColumns();}, false, { single: true });
			
			var viewport = new Ext.Viewport({
	        layout:'fit',
	        items:[
	        	grid
	        	]
	        });
	        
	        ds.load();

		},
		
		getDataSource : function()
		{
			return ds;
		},
		

		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':
					var selectionModel = grid.getSelectionModel();
					var records = selectionModel.getSelections();						
					if(!linksDialog)
					{
						linksDialog= new Ext.LinksDialog({"gridRecords":records, linksStore: ds});
					}
					linksDialog.setLinkRecords(records);
					linksDialog.show();
				break;
				
				case 'delete':
					var selectedRows = grid.selModel.selections.keys;
	
					if(selectedRows.length)
					{
	
						var conn = new Ext.data.Connection();
						conn.request({
							url: 'action.php',
							params: {task: 'delete', selectedRows: Ext.encode(selectedRows)},
							callback: function(options, success, response)
							{
								if(!success)
								{
									Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
								}else
								{
									ds.reload();
								}
							},
							scope: Notes
						});
					}
				break;

				case 'add':

					Note.showDialog(0);
					
				break;

			}
		},
		


		
		rowDoubleClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//showDialog('dialog', {url: 'note.php?note_id='+record.data['id']});
			//Ext.get('dialog').load({url: 'note.php?note_id='+record.data['id'], scripts: true });
			Note.showDialog(record.data['id']);
		}
	};

}();





Ext.EventManager.onDocumentReady(Notes.init, Notes, true);




//for the Group-Office search function
function showSearchResult(record)
{
	Note.showDialog(record.data['id']);
}