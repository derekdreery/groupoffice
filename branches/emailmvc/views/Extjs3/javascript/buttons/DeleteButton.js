Ext.ns('GO.buttons');

GO.buttons.DeleteButton = Ext.extend(Ext.Button,{
	
	buttonParams : null,
	
	initComponent : function(){
		
		Ext.applyIf(this,{
			iconCls: 'btn-delete',
			itemId:'delete',
			disabled:true,
			text: GO.lang.cmdDelete,
			cls: 'x-btn-text-icon',
			handler:function(){
				this.grid.deleteSelected();
			}
		});
		
		if(this.grid){
			this.grid.store.on('load', function(){
				this.buttonParams = this.grid.store.reader.jsonData.buttonParams;

				this.setDisabled(!this.buttonParams);

			}, this);
		}
		
		GO.buttons.DeleteButton.superclass.initComponent.call(this);
	}
});

Ext.reg('deletebutton', GO.buttons.DeleteButton);