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
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.email.AccountsDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	this.accountsGrid = new GO.email.AccountsGrid();
	
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=500;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.email.lang.accounts;					
	config.items=this.accountsGrid;
	config.ddGroup="EmailAccountsDD";
	config.buttons=[			
			{				
				text: GO.lang.cmdClose,
				handler: function(){this.hide();},
				scope: this
			}
		]
		
	GO.email.AccountsDialog.superclass.constructor.call(this, config);
}
Ext.extend(GO.email.AccountsDialog, Ext.Window,{
	
	

});