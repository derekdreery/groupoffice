GO.files.SelectFile = Ext.extend(Ext.form.TriggerField,{

	triggerClass : 'fs-form-file-select',

	filesFilter : '',

	root_folder_id : 0,

	files_folder_id : 0,


	onTriggerClick : function(){


		if(!this.fb)
		{
			this.fb = new GO.files.FileBrowser({
				border:false,
				treeCollapsed:false
			});

			this.fb.setFileClickHandler(function(r){
					this.setValue(r.data.path);
					this.fileBrowserWindow.hide();
				}, this);

			this.fileBrowserWindow = new Ext.Window({
				title: GO.lang.strSelectFiles,
				height:500,
				width:750,
				modal:true,
				layout:'fit',
				border:false,
				collapsible:true,
				maximizable:true,
				closeAction:'hide',
				items: this.fb,
				buttons:[
					{
						text: GO.lang.cmdOk,
						handler: function(){this.fb.fileClickHandler();},
						scope: this
					},{
						text: GO.lang.cmdClose,
						handler: function(){
							this.fileBrowserWindow.hide();
						},
						scope:this
					}
				]

			});
		}

		this.fb.setFilesFilter(this.filesFilter);
		this.fb.setRootID(this.root_folder_id, this.files_folder_id);
		this.fileBrowserWindow.show();
	}

});