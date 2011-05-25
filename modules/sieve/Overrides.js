/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Overrides.js 0000 2010-12-29 08:59:17 wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.AccountDialog, {
		sieveCheckedAccountId : 0,
		
		initComponent : GO.email.AccountDialog.prototype.initComponent.createSequence(function(){

			this.sieveGrid = new GO.sieve.SieveGrid();
			var inPos = this.tabPanel.items.indexOf(this.filtersTab);
			this.tabPanel.insert(inPos,this.sieveGrid);

			this.tabPanel.on('beforetabchange', function(tabPanel, newPanel, oldPanel){
				if(newPanel==this.sieveGrid && this.sieveCheckedAccountId!=this.account_id){
					this.sieveCheck();
					return false;
				}
			}, this)

			this.on('show', function(){
				this.tabPanel.hideTabStripItem(this.filtersTab);
				this.tabPanel.unhideTabStripItem(this.sieveGrid.getId());
				this.sieveCheckedAccountId=0;
			}, this);

			this.tabPanel.hideTabStripItem(this.filtersTab);
		}),

		sieveCheck :function(){
			if(this.account_id > 0 && this.sieveCheckedAccountId!=this.account_id)
			{
				this.getEl().mask(GO.lang.waitMsgLoad);
				Ext.Ajax.request({
					url: GO.settings.modules.sieve.url+ 'fileIO.php',
					success: function(response){
						var sieve_supported = Ext.decode(response.responseText);

						if(sieve_supported.sieve_supported)
						{
							// Hide the 'normal' panel and show this panel
							this.tabPanel.hideTabStripItem(this.filtersTab);
							this.tabPanel.unhideTabStripItem(this.sieveGrid);
							this.sieveGrid.show();
						}
						else
						{
							// Hide this panel and show the 'normal' panel
							this.tabPanel.hideTabStripItem(this.sieveGrid);
							this.tabPanel.unhideTabStripItem(this.filtersTab);
							this.filtersTab.show();
						}
						this.getEl().unmask();
					},
					failure: function(response){
						alert(GO.sieve.lang.checksieveerror);
						this.getEl().unmask();
					},
					params: {
						task: 'check_is_supported',
						account_id: this.account_id
					},
					scope:this
				});
			}
			this.sieveCheckedAccountId=this.account_id;
		},
		setAccountId : GO.email.AccountDialog.prototype.setAccountId.createSequence(function(account_id){
			this.sieveGrid.setAccountId(account_id);
		})
	})
});