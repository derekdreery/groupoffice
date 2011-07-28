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
 
GO.LogoComponent = Ext.extend(Ext.BoxComponent, {
	onRender : function(ct, position){
		this.el = ct.createChild({tag: 'div', cls: "go-app-logo"});
	}
});

 /**
 * @class GO.dialog.AboutDialog
 * @extends Ext.Window
 * The Group-Office login dialog window.
 * 
 * @cfg {Function} callback A function called when the login was successfull
 * @cfg {Object} scope The scope of the callback
 * 
 * @constructor
 * @param {Object} config The config object
 */
 
GO.dialog.AboutDialog = function(config){
	
	Ext.apply(this, config);

	GO.dialog.AboutDialog.superclass.constructor.call(this, {
		modal:false,
		layout:'fit',
		height:500,
		width:520,
		resizable: false,
		closeAction:'hide',
		title:GO.lang.strAbout.replace('{product_name}', GO.settings.config.product_name),
		items: new Ext.Panel({
			border:false,
			layout:'fit',
			autoLoad:'about.php',
			autoScroll:true
			}),		
		buttons: [
			{				
				text: GO.lang['cmdClose'],
				handler: function(){this.hide()},
				scope:this
			}
		]
    });
};

Ext.extend(GO.dialog.AboutDialog, Ext.Window, {
	
});

