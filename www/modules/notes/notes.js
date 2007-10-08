Notes = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;
	var note_id;
	var link_id;
	var note_form;
	var save_button;
	var linksPanel;
var linksGrid;

	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


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
			    store: ds,
			    columns: [		        
			        {header: GOlang['strName'], width: 200, sortable: true, dataIndex: 'name'},
			        {header: GOlang['strMtime'], width: 120, sortable: true, dataIndex: 'mtime'}
			    ],
			    sm: new Ext.grid.RowSelectionModel(),
			    height:'100%',
			    iconCls:'icon-grid',
				tbar:[{
		           	id: 'delete',
					icon: GOimages['delete'],
					text: GOlang['cmdDelete'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
		        },
		        {
					id: 'add',
					icon: GOimages['add'],
					text: GOlang['cmdAdd'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
				},
				{
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
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
					
					parent.GroupOffice.showLinks({ 'url': '../../search.html', 'records': records});
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
								Ext.MessageBox.alert('Failed', response.result.errors);
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
				/*var conn = new Ext.data.Connection();
				conn.request({
					url: 'action.php',
					params: {task: 'add'},
					callback: function(options, success, response)
					{
						if(!success)
						{
							Ext.MessageBox.alert('Failed', response.result.errors);
						}else
						{
							var reponseParams = Ext.util.JSON.decode(response.responseText);
							//note_form.load({url : 'notes_json.php?note_id='+reponseParams['note_id']});
							note_id=reponseParams['note_id'];
							note_form.findField('name').focus(true);							
							this.toggleForm(true);
							ds.reload();
						}
					},
					scope: Notes
				});*/
				
				var conn = new Ext.data.Connection();
				conn.request({
					url: 'action.php',
					params: {task: 'add', selectedRows: Ext.encode(selectedRows)},
					callback: function(options, success, response)
					{
						if(!success)
						{
							Ext.MessageBox.alert('Failed', response.result.errors);
						}else
						{
							var reponseParams = Ext.util.JSON.decode(response.responseText);
							Note.showDialog(reponseParams['note_id']);
						}
					},
					scope: Notes
				});
					
					
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





Note = function(){

	var win;
	var formPanel;
	var layout;


	return {

		init : function(){


			if(!win){
			
				formPanel = new Ext.form.FormPanel({
					title: 'Properties',
					labelWidth: 75, // label settings here cascade unless overridden
					defaultType: 'textfield',
        			bodyStyle:'padding:5px;',
					reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
					}, [
					{name: 'name'},
					{name: 'content'}
					]),
					
					items: [{
						fieldLabel: GOlang['strName'],
						name: 'name',
						allowBlank:false,
						style:'width:100%'
					},{
						fieldLabel: GOlang['strText'],
						name: 'content',
						xtype: 'textarea',
						style:'width:100%;height:200px'
					}					
					],
					tbar: [{
						id: 'link',
						icon: GOimages['link'],
						text: GOlang['cmdLink'],
						cls: 'x-btn-text-icon',
						handler: this.onButtonClick
					}]
				});
				
				var tabs = new Ext.TabPanel({
			        //renderTo: 'tabs1',
			        activeTab: 0,
			        frame:true,
			        defaults:{autoHeight: true},
			        items:[
			           formPanel,
			           {
			                title: 'Test Tab',
			                html: "My content was added during construction."
			           }
			        ]
			    });
			
			
				win = new Ext.Window( {
					el: 'notedialog',
					layout: 'fit',
					modal:true,
					shadow:false,
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,
					plain:true,

        			
					items: [
						tabs
					],
					
					buttons: [
						{
							id: 'ok',
							text: GOlang['cmdOk'],
							handler: function(){
								formPanel.form.submit({
								url:'./action.php',
								params: {'task' : 'save','note_id' : loaded_note_id},
			
								success:function(form, action){
									//reload grid
									Notes.getDataSource().reload();
								},
			
								failure: function(form, action) {
									Ext.MessageBox.alert(GOlang['strError'], action.result.errors);
								}
							});
							win.hide();
							},
							scope:this
						},
						{
							id: 'close',
							text: GOlang['cmdClose'],
							handler: function(){win.hide();},
							scope: this
						}
					]
				});
			}
		},
		showDialog : function (note_id)
		{
			loaded_note_id=note_id;
			formPanel.form.load({url: 'notes_json.php?note_id='+note_id, waitMsg:GOlang['waitMsgLoad']});
			
			//layout.getRegion('center').showPanel('properties');
			
			win.show();
		},

		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = links_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.Ext.get('dialog').load({url: record.data['url'], scripts: true });
			parent.GroupOffice.showDialog({url: record.data['url'], scripts: true });
		},
		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : note_form.reader.jsonData.note[0].link_id, 'link_type' : 4 });

				parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});
				break;

				case 'unlink':

				var unlinks = [];

				var selectionModel = links_grid.getSelectionModel();
				var records = selectionModel.getSelections();

				for (var i = 0;i<records.length;i++)
				{
					unlinks.push(records[i].data['link_id']);
				}



				if(parent.GroupOffice.unlink(note_form.reader.jsonData.note[0].link_id, unlinks))
				{
					links_ds.load();
				}
				break;

		
			}
		}
	}
}();


Ext.EventManager.onDocumentReady(Notes.init, Notes, true);
Ext.EventManager.onDocumentReady(Note.init, Note, true);

//for the Group-Office search function
function showSearchResult(record)
{
	Note.showDialog(record.data['id']);
}