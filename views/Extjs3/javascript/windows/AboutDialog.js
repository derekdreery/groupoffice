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

	if (GO.settings.config.product_name=='Group-Office')
		var aboutText = GO.lang['about'].replace('{company_name}', 'Intermesh B.V.');
	else
		var aboutText = GO.lang['about'].replace('{company_name}', '{product_name}');

	GO.dialog.AboutDialog.superclass.constructor.call(this, {
		modal:false,
		layout:'fit',
		height: 230,
		width: 480,
		resizable: false,
		closeAction:'hide',
		title:GO.lang.strAbout.replace('{product_name}', GO.settings.config.product_name),
		items: new Ext.Panel({
			border:false,
			padding: '10px',
			items: [
				new GO.LogoComponent(),
				new GO.form.PlainField({
				hideLabel: true,
				value: aboutText
					.replace('{version}', GO.settings.config.product_version)
					.replace('{current_year}', new Date().getFullYear())
					.replace('{product_name}', GO.settings.config.product_name)
					.replace('{product_name}', GO.settings.config.product_name)
				})
			],
//			autoLoad:'about.php',
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

