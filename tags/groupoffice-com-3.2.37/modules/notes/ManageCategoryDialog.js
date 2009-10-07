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
 
GO.notes.ManageCategoriesDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
		
	this.categoriesGrid = new GO.notes.ManageCategoriesGrid();

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=500;
	config.height=300;
	config.closeAction='hide';
	config.title= GO.notes.lang.manageCategories;					
	config.items= this.categoriesGrid;
	config.buttons=[{
			text: GO.lang['cmdClose'],
			handler: function(){
				if(this.categoriesGrid.changed)
				{
					this.fireEvent('change');
					this.categoriesGrid.changed=false;
				}
				this.hide();
				
			},
			scope:this
		}					
	];
	

	
	GO.notes.ManageCategoriesDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'change':true});
}

Ext.extend(GO.notes.ManageCategoriesDialog, Ext.Window,{

});