/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */


 
GO.gnupg.SecurityDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
		
	this.keysGrid = new GO.gnupg.KeysGrid();

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=700;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.gnupg.lang.encryptionSettings;					
	config.items=this.keysGrid;
	config.buttons=[{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();				
			},
			scope:this
		}					
	];
	
	GO.gnupg.SecurityDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.gnupg.SecurityDialog, Ext.Window,{
	
	show : function(){
		
		//if(!this.keysGrid.store.loaded)
		//{
			this.keysGrid.store.load();
		//}
		GO.gnupg.SecurityDialog.superclass.show.call(this);
	
	}

});