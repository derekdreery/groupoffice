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

			layout = new Ext.BorderLayout(document.body, {
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true
				}
			});
			
			
			
			layout.beginUpdate();
			
			


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



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([{
				header: GOlang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: GOlang['strMtime'],
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('notes-grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});

	
			grid.addListener("rowdblclick", this.rowDoubleClicked, this);


			// render it
			grid.render();

			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: parseInt(GOsettings['max_rows_list']),
				displayInfo: true,
				displayMsg: GOlang['displayingItems'],
				emptyMsg: GOlang['strNoItems']
			});

			// trigger the data store load
			ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});


			var tb = new Ext.Toolbar('notestb');
			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'add',
				icon: GOimages['add'],
				text: GOlang['cmdAdd'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);
			
			tb.add(new Ext.Toolbar.Button({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);




			layout.add('center', new Ext.GridPanel(grid, {title: NotesLang['notes'], toolbar: tb}));
			

			layout.endUpdate();
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
					Ext.get('dialog').load({url: 'note.php?note_id=0', scripts: true });
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

	var dialog;
	var note_form;
	var layout;


	return {

		init : function(){

			if(!dialog){
				dialog = new Ext.LayoutDialog("notedialog", {
					modal:true,
					shadow:false,
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,
					proxyDrag: true,
					collapsible: false,
					center: {
						autoScroll:true,
						tabPosition: 'top',
						closeOnTab: true,
						alwaysShowTabs: true
					}
				});
				dialog.addKeyListener(27, this.hide, this);
				dialog.addButton({
					id: 'ok',
					text: GOlang['cmdOk'],
					handler: function(){
						note_form.submit({
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
					dialog.hide();
					}
				}, this);
				
				dialog.addButton({
					id: 'apply',
					text: GOlang['cmdApply'],
					handler: function(){
						note_form.submit({
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
					}
				}, this);
				
				dialog.addButton(GOlang['cmdClose'], dialog.hide, dialog);

				layout = dialog.getLayout();
				layout.beginUpdate();






				note_form = new Ext.form.Form({
					labelWidth: 75, // label settings here cascade unless overridden


					reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
					}, [
					{name: 'name'},
					{name: 'content'}
					])
				});

				var name_field = new Ext.form.TextField({
					fieldLabel: GOlang['strName'],
					name: 'name',
					allowBlank:false,
					style:'width:100%'
				});


				note_form.add(name_field
				,

				new Ext.form.TextArea({
					fieldLabel: GOlang['strText'],
					name: 'content',
					style:'width:100%;height:200px'
				})

				);

				note_form.render('form');


				var notetb = new Ext.Toolbar('toolbar');


				notetb.addButton({
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
				}
				);



				notePanel = new Ext.ContentPanel('properties',{
					title: NotesLang['note'],
					//toolbar: notetb,
					autoScroll:true,
				});

				layout.add('center', notePanel);
				
				
				
				
				
				linksPanel = links.getGridPanel('linkstoolbar','links_grid_div');
				layout.add('center', linksPanel);
				linksPanel.on('activate',function() {

					links.loadLinks(note_form.reader.jsonData.note[0].link_id, 4);

				});
				
				




				

				layout.getRegion('center').showPanel('properties');

				layout.endUpdate();
			}
			name_field.focus(true);
		},
		showDialog : function (note_id)
		{
			loaded_note_id=note_id;
			note_form.load({url: 'notes_json.php?note_id='+note_id, waitMsg:GOlang['waitMsgLoad']});
			
			layout.getRegion('center').showPanel('properties');
			
			dialog.show();
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