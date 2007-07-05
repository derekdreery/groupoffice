GroupOffice = function(){
	var layout;
	var mainPanel;
	var navPanel;
	var innerLayout;
	var mainmenutb;
	var search_ds;
	var dialog;
	var linksDialog;
	var search_links_ds;
	var fromlinks;
	var search_grid_rendered;
	var links_callback;

	return {

		init : function(){
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				north: {
					split:false,
					initialSize: 28,
					titlebar: false
				},
				east: {
					split:true,
					initialSize: 400,
					minSize: 200,
					maxSize: 600,
					titlebar: true,
					collapsible: true,
					closable: true,
					animate: true
				},
				center: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true,
					tabPosition: 'top'
				}

			});

			layout.beginUpdate();
			layout.add('north', new Ext.ContentPanel('header'));





			search_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'links_json.php'
				}),

				baseParams: {"query": ''},

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'icon'},
				{name: 'link_id'},
				{name: 'name'},
				{name: 'type'},
				{name: 'url', mapping: 'url'},
				{name: 'mtime'},
				{name: 'module'},
				{name: 'id'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			search_ds.setDefaultSort('mtime', 'desc');


			function IconRenderer(src){
				return '<img src=\"' + src +' \" />';
			}

			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var search_cm = new Ext.grid.ColumnModel([
			{
				header:"",
				width:28,
				dataIndex: 'icon',
				renderer: IconRenderer
			},{
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
			search_grid = new Ext.grid.Grid('searchgrid', {
				ds: search_ds,
				cm: search_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true

			});

			//grid.addListener("rowclick", this.rowClicked, this);
			search_grid.addListener("rowdblclick",
			function(search_grid, rowClicked, e) {

				var selectionModel = search_grid.getSelectionModel();
				var record = selectionModel.getSelected();

				//parent.mainframe.document.location=record.data['url'];
				//this.showDialog({ url: record.data['url'], iframe: true });
				//layout.getRegion('east').collapse();
				
		
				
				if(window.frames[record.data['module']].showSearchResult)
				{
					window.frames[record.data['module']].showSearchResult(record);
					layout.showPanel(record.data['module']);
				}else{
				
					this.showPanel(record.data['module'], record.data['url']);
				}

				//Ext.get('dialog').load({url: record.data['url'], scripts: true });
			}
			, this);


			// trigger the data store load
			//search_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

			// render it




			var searchtb = new Ext.Toolbar('searchtoolbar');

			var save_button =searchtb.addButton({
				id: 'close',
				icon: GOimages['close'],
				text: GOlang['cmdClose'],
				cls: 'x-btn-text-icon',
				handler: this.closeSearchPanel
			}
			);

			var gridPanel = new Ext.GridPanel(search_grid, { title: GOlang['strSearchResults'], toolbar: searchtb });



			layout.add('east', gridPanel);



			//mainPanel = new Ext.ContentPanel('center');

			//layout.add('center', mainPanel);

			layout.getRegion('east').hide();

			layout.endUpdate();

			//Ext.QuickTips.init();
			//Ext.QuickTips.register({title: 'Play', qtip: 'The summary displays relevant info', target: 'summary', autoHide:true});
			
			
			var loading = Ext.get('loading');
			var mask = Ext.get('loading-mask');
			mask.setOpacity(.8);
			mask.shift({
				xy:loading.getXY(),
				width:loading.getWidth(),
				height:loading.getHeight(), 
				remove:true,
				duration:1,
				opacity:.3,
				easing:'bounceOut',
				callback : function(){
					loading.fadeOut({duration:.2,remove:true});
				}
			});

		},
		closeSearchPanel : function()
		{
			layout.getRegion('east').hide();
		},
		addCenterPanel : function(id, title, url)
		{
			var iframe = Ext.DomHelper.append(document.body,
			{
				'id': id, 
				name: id, 
				tag: 'iframe', 
				frameBorder: 0 
			});
			var panel = new Ext.ContentPanel(iframe,
			{title: title, fitToFrame:true, closable:false, background:true});


			panel.on('activate', function()
			{
				var frame = panel.getEl();
				if(frame.dom.src=='')
				{
					frame.set({'src': url});
				}
			}
			);

			layout.add('center', panel);

		},
		showPanel :  function (panelID, url){
			if(typeof(url)!='undefined')
			{
				var frame = Ext.get(panelID);
				frame.set({'src': url});
			}
			//don't know why but I get an error when trying to open a linked
			//item that links to the active panel if I don't check if layout
			//is defined
			if(typeof(layout) != 'undefined')
			{
				layout.showPanel(panelID);
			}


		},


		search : function(query){
			var east = layout.getRegion('east');
			east.show();
			//east.getPanel('east').load('search.php?query='+escape(query), { scripts: true, nocache: true });

			search_ds.baseParams = {"query": query};

			search_ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});

			if(!search_grid_rendered)
			{
				search_grid.render();

				var searchGridFoot = search_grid.getView().getFooterPanel(true);

				// add a paging toolbar to the grid's footer
				var search_paging = new Ext.PagingToolbar(searchGridFoot, search_ds, {
					pageSize: parseInt(GOsettings['max_rows_list']),
					displayInfo: true,
					displayMsg: GOlang['displayingItems'],
					emptyMsg: GOlang['strNoItems']
				});

				search_grid_rendered=true;
			}

			return false;
		},

		showLinks : function(config){



			links_callback = config['callback'];

			fromlinks = config['fromlinks'];

			if(config['records'])
			{
				var records=config['records'];
				fromlinks = [];

				for (var i = 0;i<records.length;i++)
				{
					fromlinks.push({ 'link_id' : records[i].data['link_id'], 'link_type' : records[i].data['link_type'] });
				}
			}else
			{
				fromlinks = config['fromlinks'];
			}

			if(!linksDialog)
			{
				linksDialog = new Ext.BasicDialog("search_links_dialog", {
					shadow:false,
					draggable: true,
					collapsible: false,
					modal:true,
					title: GOlang['strLinkItems'],
					resizable:false,
					style: 'width:'+config['width']+';height:'+config['height'],
					width: 600,
					height:420
				});



				search_links_ds = new Ext.data.Store({

					proxy: new Ext.data.HttpProxy({
						url: 'links_json.php'
					}),

					baseParams: {"query": ''},

					reader: new Ext.data.JsonReader({
						root: 'results',
						totalProperty: 'total',
						id: 'link_id'
					}, [
					{name: 'link_id', mapping: 'link_id'},
					{name: 'link_type', mapping: 'link_type'},
					{name: 'name', mapping: 'name'},
					{name: 'type', mapping: 'type'},
					{name: 'url', mapping: 'url'},
					{name: 'mtime', mapping: 'mtime'}
					]),

					// turn on remote sorting
					remoteSort: true
				});
				search_links_ds.setDefaultSort('mtime', 'desc');



				// the column model has information about grid columns
				// dataIndex maps the column to the specific data field in
				// the data store
				var search_links_cm = new Ext.grid.ColumnModel([{
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
				search_links_cm.defaultSortable = true;

				// create the editor grid
				search_links_grid = new Ext.grid.Grid('search_links_grid', {
					ds: search_links_ds,
					cm: search_links_cm,
					selModel: new Ext.grid.RowSelectionModel(),
					enableColLock:false,
					loadMask: true

				});

				//grid.addListener("rowclick", this.rowClicked, this);
				search_links_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


				// trigger the data store load
				//search_links_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});

				// render it
				search_links_grid.render();

				var search_linksGridFoot = search_links_grid.getView().getFooterPanel(true);

				// add a paging toolbar to the grid's footer
				var search_links_paging = new Ext.PagingToolbar(search_linksGridFoot, search_links_ds, {
					pageSize: parseInt(GOsettings['max_rows_list']),
					displayInfo: true,
					displayMsg: GOlang['displayingItems'],
					emptyMsg: GOlang['strNoItems']
				});



				linksDialog.addKeyListener(27, linksDialog.hide, linksDialog);
				linksDialog.addButton(GOlang['cmdOk'], this.linkItems, this);
				linksDialog.addButton(GOlang['cmdClose'], linksDialog.hide, linksDialog);

			}

			linksDialog.show();

			var links_query = Ext.get('links_query');

			links_query.focus(true);

		},
		searchLinks : function(query){
			search_links_ds.baseParams = {"query": query};

			search_links_ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});

		},
		searchLinksKeyEvent : function(e){

			var keynum;
			var input;
			input = Ext.get("links_query");

			if(window.event) // IE
			{
				keynum = e.keyCode
			}else if(e.which) // Netscape/Firefox/Opera
			{
				keynum = e.which
			}

			if(keynum==13)
			{
				this.searchLinks(input.getValue());
			}
			return true;
		},
		linkItems : function()	{
			var selectionModel = search_links_grid.getSelectionModel();
			var records = selectionModel.getSelections();

			var tolinks = [];

			for (var i = 0;i<records.length;i++)
			{
				tolinks.push({ 'link_id' : records[i].data['link_id'], 'link_type' : records[i].data['link_type'] });
			}

			var conn = new Ext.data.Connection();
			conn.request({
				url: 'action.php',
				params: {task: 'link', fromLinks: Ext.encode(fromlinks), toLinks: Ext.encode(tolinks)},
				callback: function(options, success, response)
				{
					if(!success)
					{
						Ext.MessageBox.alert('Failed', response.result.errors);
					}else
					{
						links_callback.call();
						linksDialog.hide();
					}
				}
			});
		},
		unlink : function(link_id, unlinks)	{

			var conn = new Ext.data.Connection();
			conn.request({
				url: BaseHref+'action.php',
				params: {task: 'unlink', link_id: link_id, unlinks: Ext.encode(unlinks)},
				callback: function(options, success, response)
				{
					if(!success)
					{
						Ext.MessageBox.alert('Failed', response.result.errors);
					}else
					{
						return true;
					}
				}
			});
			return true;
		},
		showDialog : function (config){

			if(!config['width'])
			{
				config['width']='100%';
			}
			if(!config['height'])
			{
				config['height']='100%';
			}

			var id = Ext.id();
			Ext.DomHelper.append(document.body, {tag: 'div', id: id});

			dialogdiv = Ext.get(id);

			dialogdiv.load({url: config['url'], scripts: true, nocache: true});
		}
	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
