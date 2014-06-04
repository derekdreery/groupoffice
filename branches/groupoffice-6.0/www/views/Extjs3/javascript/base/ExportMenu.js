GO.base.ExportMenu = Ext.extend(Ext.Button,{
	
	className : null,
	
	constructor : function(config){
		
		this.className = config.className;
		
		GO.base.ExportMenu.superclass.constructor.call(this);	
	},
	
	initComponent : function(){

		Ext.apply(this, {
			iconCls: 'btn-export',
			text: GO.lang.cmdExport,
			menu: new Ext.menu.Menu()
		});

		GO.base.ExportMenu.superclass.initComponent.call(this);	

		this._createDefaultMenu();
	},
	
	// Add an item at the end of the menu
	addItem : function(menuItem){
		this.menu.addItem(menuItem);
	},
	
	// Insert an item at the given position. When 0, then add it at the top.
	insertItem : function(position,menuItem){
		this.menu.insert(position,menuItem);
	},
	
	_createDefaultMenu : function(){

		this.savedExportMenu = new GO.menu.JsonMenu({
			store: new GO.data.JsonStore({
				url: GO.url('core/export/savedExportsStore'),
				baseParams : {
					className : this.className
				},
				root: 'results',
				id: 'id',
				totalProperty:'total',
				fields: ['id','name'],
				remoteSort: true,
				model:"GO\\Base\\Model\\SavedExport"
			}),
			listeners:{
				scope:this,
				itemclick : function(item, e ) {
					if(!item.isManageButton && !item.isSeparator){
						this.doExport(item);
					}
				},
				load : function(menu,records){
					this.savedExportMenu.addItem(new Ext.menu.Separator({isSeparator:true}));
					this.savedExportMenu.addItem(this.getManageExportButton());
				}
			}
		});

		this.savedExportsButton = new Ext.menu.Item({
			text: GO.lang.savedExports,
			menu: this.savedExportMenu,
			scope: this
		});
		
		this.gridExportButton = new Ext.menu.Item({
			text: GO.lang.exportScreen,
			handler: function(item,event){

				if(!GO.base.currentGridExportDialog){
					GO.base.currentGridExportDialog = new GO.base.CurrentGridExportDialog();
				}
				
				GO.base.currentGridExportDialog.setClass(this.className);
				GO.base.currentGridExportDialog.show(0,{
					loadParams:{
						className:this.className
					}
				});
			},
			scope: this
		});
		
		this.menu.addItem(this.gridExportButton);
		this.menu.addSeparator();
		this.menu.addItem(this.savedExportsButton);
	},

	getManageExportButton : function(){
		
		this.manageExportsButton = new Ext.menu.Item({
			isManageButton: true,
			text: GO.lang.manageSavedExports,
			handler:function(){

				if(!GO.base.savedExportGridDialog){
					GO.base.savedExportGridDialog = new GO.base.SavedExportGridDialog();

					GO.base.savedExportGridDialog.on('hide', function(){
						this.savedExportMenu.store.load();
					}, this);

				}

				GO.base.savedExportGridDialog.setClass(this.className);
				GO.base.savedExportGridDialog.show();
			},
			scope: this
		});
		
		return this.manageExportsButton;
	},
	
	doExport : function(item){
		
		var data = {
			class_name:item.class_name,
			export_columns:item.export_columns,
			include_column_names:item.include_column_names,
			orientation:item.orientation,
			use_db_column_names:item.use_db_column_names,
			view:item.view,
			id: item.id			
		};

		window.open(GO.url("core/export/export",data));
	}
	
});