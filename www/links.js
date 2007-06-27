
links = function(){

	var linksPanel;
	var dialog;
	var links_grid;
	var links_ds;
	var user_form;
	var reader;
	var link_id=0;
	var link_type=0;

	return {

		getGridPanel : function(toolbarEl, gridEl){
			var linkstb = new Ext.Toolbar(toolbarEl);


			linkstb.addButton({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: function(){
					var fromlinks = [];
					fromlinks.push({ 'link_id' : link_id, 'link_type' : link_type });
					parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){ links_ds.load();}});
				}
			}
			);

			linkstb.addButton({
				id: 'unlink',
				icon: GOimages['unlink'],
				text: GOlang['cmdUnlink'],
				cls: 'x-btn-text-icon',
				handler: function() {
					
					var unlinks = [];
	
					var selectionModel = links_grid.getSelectionModel();
					var records = selectionModel.getSelections();
	
					for (var i = 0;i<records.length;i++)
					{
						unlinks.push(records[i].data['link_id']);
					}
					if(parent.GroupOffice.unlink(link_id, unlinks))
					{
						links_ds.load();
					}
				}
			}
			);

			links_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'links_json.php',
					baseParams: {"link_id": link_id}
				}),
				reader: new Ext.data.JsonReader({
						root: 'results',
						totalProperty: 'total',
						id: 'link_id'
					}, [
					{name: 'icon'},
					{name: 'link_id'},
					{name: 'name'},
					{name: 'type'},
					{name: 'url'},
					{name: 'mtime'},
					{name: 'id'},
					{name: 'module'}
					]),

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
			links_grid = new Ext.grid.Grid(gridEl, {
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
			links_grid.addListener("rowdblclick",
			function(search_grid, rowClicked, e) {

				var selectionModel = search_grid.getSelectionModel();
				var record = selectionModel.getSelected();

				//parent.mainframe.document.location=record.data['url'];
				//this.showDialog({ url: record.data['url'], iframe: true });
				//layout.getRegion('east').collapse();
				
				/*for (var i = 0;i<parent.window.frames.length;i++)
				{
					alert(parent.window.frames[i].name);
				}*/
				if(parent.window.frames[record.data['module']].showSearchResult)
				{
					parent.window.frames[record.data['module']].showSearchResult(record);
					parent.GroupOffice.showPanel(record.data['module']);
				}else{
				
					parent.GroupOffice.showPanel(record.data['module'], record.data['url']);
				}

				//Ext.get('dialog').load({url: record.data['url'], scripts: true });
			}
			, this);

			
			return new Ext.GridPanel(links_grid, { title: 'Links', toolbar: linkstb});
			
			

		},
		loadLinks : function(new_link_id, new_link_type)
		{
			if(link_id!=new_link_id)
			{
				link_type=new_link_type;
				link_id=new_link_id;
				links_ds.baseParams = {"link_id": link_id};				
				links_ds.load();
				links_grid.render();
			}
		}
	}
}();
