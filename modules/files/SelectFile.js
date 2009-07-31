GO.files.SelectFile = Ext.extend(Ext.form.TriggerField,{

	triggerClass : 'fs-form-file-select',

	filesFilter : '',

	root_folder_id : 0,

	files_folder_id : 0,


	onTriggerClick : function(){


		if(!GO.selectFileBrowser)
		{
			GO.selectFileBrowser= new GO.files.FileBrowser({
				border:false,
				treeCollapsed:false
			});

			GO.selectFileBrowser.setFileClickHandler(function(r){
					this.setValue(r.data.path);
					GO.selectFileBrowserWindow.hide();
				}, this);

			GO.selectFileBrowserWindow = new Ext.Window({
				title: GO.lang.strSelectFiles,
				height:500,
				width:750,
				modal:true,
				layout:'fit',
				border:false,
				collapsible:true,
				maximizable:true,
				closeAction:'hide',
				items: GO.selectFileBrowser,
				buttons:[
					{
						text: GO.lang.cmdOk,
						handler: function(){
							var records = GO.selectFileBrowser.getSelectedGridRecords();
							GO.selectFileBrowser.fileClickHandler.call(this, records[0]);
						},
						scope: this
					},{
						text: GO.lang.cmdClose,
						handler: function(){
							GO.selectFileBrowserWindow.hide();
						},
						scope:this
					}
				]

			});
		}

		GO.selectFileBrowser.setFilesFilter(this.filesFilter);
		GO.selectFileBrowser.setRootID(this.root_folder_id, this.files_folder_id);
		GO.selectFileBrowserWindow.show();
	}

});

Ext.ComponentMgr.registerType('selectfile', GO.files.SelectFile);