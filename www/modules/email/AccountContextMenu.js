GO.email.AccountContextMenu = Ext.extend(Ext.menu.Menu,{

	
	setNode : function(node){
		
	},
	initComponent : function(){
		
		this.items=[
		this.propertiesBtn = new Ext.menu.Item({
			iconCls: 'btn-edit',
			text: GO.lang['strProperties'],
			handler:function(a,b){
				var sm = this.treePanel.getSelectionModel();
				var node = sm.getSelectedNode();
				
				if(!this.accountDialog){
					this.accountDialog = new GO.email.AccountDialog();
					this.accountDialog.on('save', function(){
					this.store.reload();
						if(GO.email.aliasesStore.loaded)
						{
							GO.email.aliasesStore.reload();
						}
					}, this);
				}
				
				this.accountDialog.show(node.attributes.account_id);

			},
			scope:this
		})];


		GO.email.AccountContextMenu.superclass.initComponent.call(this);
		

	}
}
);
