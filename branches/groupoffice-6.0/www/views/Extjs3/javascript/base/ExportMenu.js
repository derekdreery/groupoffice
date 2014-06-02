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
	
	addItem : function(menuItem){
		this.menu.addItem(menuItem);
	},
	
	_createDefaultMenu : function(){

		this.manageExportsButton = new Ext.menu.Item({
			text: GO.lang.manageSavedExports,
			handler:function(){
				
				if(!GO.base.savedExportGridDialog){
					GO.base.savedExportGridDialog = new GO.base.SavedExportGridDialog();
				}
				
				GO.base.savedExportGridDialog.setClass(this.className);
				GO.base.savedExportGridDialog.show();
			},
			scope: this
		});

		this.savedExportsButton = new Ext.menu.Item({
			text: GO.lang.savedExports,
			menu: new Ext.menu.Menu({
				items: [
					new Ext.menu.Separator(),
					this.manageExportsButton
				]
			}),
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
		
		this._insertSavedMenus();
	},
	
	_insertSavedMenus : function(){
		
		GO.request({
			url: "core/export/SavedExportsStore",
			params:{
				className : this.className
			},
			success: function(options, response, result)
			{
				if(result.total > 0){
					Ext.each(result.results, function(data){
						this.savedExportsButton.menu.insert(0,new Ext.menu.Item({
							text : data.name,
							handler:function(){
								this.doExport(data);
							},scope: this
						}));
					},this);
				} else {
					this.savedExportsButton.menu.insert(0,new Ext.menu.Item({
						text : GO.lang.noSavedExports,
						disabled: true
					}));
				}
			},
			scope:this
		});  
		
	},
	
	doExport : function(data){
		window.open(GO.url("core/export/export",data));
	}
	
});