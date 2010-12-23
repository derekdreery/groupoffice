GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.AccountDialog, {
		initComponent : GO.email.AccountDialog.prototype.initComponent.createSequence(function(){

			this.sieveGrid = new GO.sieve.SieveGrid();
			this.tabPanel.add(this.sieveGrid);
		}),

		setAccountId : GO.email.AccountDialog.prototype.setAccountId.createSequence(function(account_id){
			this.sieveGrid.setAccountId(account_id);
		})
	})
});