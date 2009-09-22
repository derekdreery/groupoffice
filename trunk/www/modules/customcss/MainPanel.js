GO.customcss.MainPanel = function(config){
	config = config || {};

	config.items={
		xtype:'textarea',
		hideLabel:true,
		name:'css',
		anchor:'100% 100%',
		plugins: new GO.plugins.InsertAtCursorTextareaPlugin()
	}


	config.waitMsgTarget=true;
	
	config.border= false;

	config.tbar=[{
			iconCls:'btn-save',
			text:GO.lang.cmdSave,
			handler:function(){
				this.form.submit({
					url: GO.settings.modules.customcss.url+'action.php',
					waitMsg:GO.lang['waitMsgSave'],
					callback:function(){
						
					}
				});
			},
			scope:this
	},{
		iconCls: 'btn-files',
		text:GO.customcss.lang.selectFile,
		handler:function(){
			if(!this.selectFileBrowser)
			{
				this.selectFileBrowser= new GO.files.FileBrowser({
					border:false,
					treeCollapsed:false
				});

				this.selectFileBrowserWindow = new Ext.Window({
					title: GO.lang.strSelectFiles,
					height:500,
					width:750,
					modal:true,
					layout:'fit',
					border:false,
					collapsible:true,
					maximizable:true,
					closeAction:'hide',
					items: this.selectFileBrowser,
					buttons:[
						{
							text: GO.lang.cmdOk,
							handler: function(){
								var records = GO.selectFileBrowser.getSelectedGridRecords();
								this.selectFileBrowser.fileClickHandler.call(this, records[0]);
							},
							scope: this
						},{
							text: GO.lang.cmdClose,
							handler: function(){
								this.selectFileBrowserWindow.hide();
							},
							scope:this
						}
					]

				});
				this.selectFileBrowser.setRootID(GO.customcss.filesFolderId, GO.customcss.filesFolderId);

				this.selectFileBrowser.setFileClickHandler(function(r){
					this.form.findField('css').insertAtCursor(GO.settings.modules.files.url+'download.php?id='+r.data.id);
					this.selectFileBrowserWindow.hide();
				}, this);
			}

			this.selectFileBrowserWindow.show();
		},
		scope:this
	}];

	GO.customcss.MainPanel.superclass.constructor.call(this,config);
}

Ext.extend(GO.customcss.MainPanel, Ext.form.FormPanel, {
	afterRender : function(){
		GO.customcss.MainPanel.superclass.afterRender.call(this);
		this.form.load({
			url: GO.settings.modules.customcss.url+'json.php',
			waitMsg:GO.lang['waitMsgLoad']
		});
	}
	
});


GO.moduleManager.addModule('customcss', GO.customcss.MainPanel, {
	title : GO.customcss.lang.customcss,
	iconCls : 'go-tab-icon-customcss',
	admin:true
});