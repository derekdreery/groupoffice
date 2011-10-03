GO.files.FileBrowserButton = Ext.extend(Ext.Button, {
	
	model_name : "",
	id: 0,
	
	setId : function(id){
		this.id=id;
		this.setDisabled(!id);
	},
	
	initComponent : function(){
		Ext.apply(this, {
				iconCls: 'btn-files',
				cls: 'x-btn-text-icon', 
				text: GO.files.lang.files,
				handler: function(){			
					

					GO.request({
						url:'files/folder/checkModelFolder',
						maskEl:this.ownerCt.getEl(),
						params:{								
							mustExist:true,
							model:this.model_name,
							id:this.id
						},
						success:function(response, options, result){														
							GO.files.openFolder(result.files_folder_id);
							GO.files.fileBrowserWin.on('hide', this.reload, this, {single:true});
						},
						scope:this

					});
					
					
				},
				scope: this,
				disabled:true
			});
		
		GO.files.FileBrowserButton.superclass.initComponent.call(this);
	}
	
});
