/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.AccountDialog, {	
		initComponent : GO.email.AccountDialog.prototype.initComponent.createSequence(function(){

			this.sieveGrid = new GO.sieve.SieveGrid();

//			this.tabPanel.add(this.sieveGrid);
			
			var inPos = this.tabPanel.items.indexOf(this.filterGrid);
			this.tabPanel.insert(inPos,this.sieveGrid);

			this.tabPanel.on('beforetabchange', function(tabPanel, newPanel, oldPanel){
				if(newPanel==this.sieveGrid && this.sieveCheckedAccountId!=this.account_id){
					this.sieveCheck();
					return false;
				}
			}, this)

			this.on('show', function(){
				this.tabPanel.hideTabStripItem(this.filterGrid);
				this.tabPanel.unhideTabStripItem(this.sieveGrid.getId());
				this.sieveCheckedAccountId=0;
			}, this);

			this.tabPanel.hideTabStripItem(this.filterGrid);

		}),

		sieveCheck :function(){
			if(this.account_id > 0)// && this.sieveCheckedAccountId!=this.account_id)
			{
				
				GO.request({
					maskEl:this.getEl(),
					url: "sieve/sieve/isSupported",
					success: function(response, options, result){
						
						if(result.supported)
						{
							// Hide the 'normal' panel and show this panel
							this.tabPanel.hideTabStripItem(this.filterGrid);
							this.tabPanel.unhideTabStripItem(this.sieveGrid);
							this.sieveGrid.show();							
						}
						else
						{
							// Hide this panel and show the 'normal' panel
							this.tabPanel.hideTabStripItem(this.sieveGrid);
							this.tabPanel.unhideTabStripItem(this.filterGrid);
							this.filterGrid.show();
						}						
					},
					fail: function(response){
						alert(GO.sieve.lang.checksieveerror);						
					},
					params: {
						account_id: this.account_id
					},
					scope:this
				});
			}
			this.sieveCheckedAccountId=this.account_id;
		},
		setAccountId : GO.email.AccountDialog.prototype.setAccountId.createSequence(function(account_id){
			this.tabPanel.unhideTabStripItem(this.sieveGrid);
			this.tabPanel.hideTabStripItem(this.filterGrid);
			
			this.sieveCheckedAccountId=0;
			
			this.sieveGrid.setAccountId(account_id);
		})
	})
});